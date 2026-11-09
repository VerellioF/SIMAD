<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\Subject;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = Auth::user();
        $date = $request->query('date', now()->toDateString());

        // Map Carbon dayOfWeek to Indonesian day string
        $dayMap = [
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
            0 => 'ahad',
        ];
        $carbonDate = Carbon::parse($date);
        $dayName = $dayMap[$carbonDate->dayOfWeek] ?? 'senin';

        // 1. All schedules taught by this teacher
        $mySchedules = ClassSchedule::query()
            ->where('teacher_id', $teacher->id)
            ->with(['classroom', 'subject'])
            ->orderByRaw("CASE day_of_week
                WHEN 'senin' THEN 1
                WHEN 'selasa' THEN 2
                WHEN 'rabu' THEN 3
                WHEN 'kamis' THEN 4
                WHEN 'jumat' THEN 5
                WHEN 'sabtu' THEN 6
                ELSE 7 END")
            ->orderBy('start_time')
            ->get();

        // Today's teaching schedules
        $todaySchedules = $mySchedules->where('day_of_week', $dayName);

        // Teacher's homeroom classroom (if any)
        $homeroomClassroom = Classroom::where('homeroom_teacher_id', $teacher->id)->first();

        // 2. Determine selected schedule or classroom/subject
        $selectedScheduleId = $request->query('schedule_id');
        $activeSchedule = null;

        if ($selectedScheduleId && $selectedScheduleId !== 'homeroom') {
            $activeSchedule = $mySchedules->firstWhere('id', (int) $selectedScheduleId);
        } elseif (! $selectedScheduleId && $todaySchedules->isNotEmpty()) {
            $activeSchedule = $todaySchedules->first();
        } elseif (! $selectedScheduleId && $mySchedules->isNotEmpty()) {
            $activeSchedule = $mySchedules->first();
        }

        // Active classroom & subject
        if ($activeSchedule) {
            $classroom = $activeSchedule->classroom;
            $subject = $activeSchedule->subject;
            $selectedScheduleId = (string) $activeSchedule->id;
        } else {
            // Fallback to homeroom or first classroom available
            $classroom = $homeroomClassroom ?? Classroom::orderBy('grade_level')->orderBy('name')->first();
            $subjectId = $request->query('subject_id');
            $subject = $subjectId ? Subject::find($subjectId) : null;
            $selectedScheduleId = 'homeroom';
        }

        // 3. Students list for active classroom
        $students = $classroom
            ? Student::with(['parent', 'classroom'])
                ->where('classroom_id', $classroom->id)
                ->where('status', 'active')
                ->orderBy('full_name')
                ->get()
            : collect();

        // 4. Existing attendances for this classroom, subject, and date
        $attendanceQuery = Attendance::where('classroom_id', $classroom?->id)
            ->whereDate('date', $date);

        if ($subject) {
            $attendanceQuery->where('subject_id', $subject->id);
        }

        $existingAttendances = $attendanceQuery->get()->keyBy('student_id');

        // Quick summary counts
        $summary = [
            'hadir' => $existingAttendances->where('status', AttendanceStatus::Hadir)->count(),
            'sakit' => $existingAttendances->where('status', AttendanceStatus::Sakit)->count(),
            'izin' => $existingAttendances->where('status', AttendanceStatus::Izin)->count(),
            'alpa' => $existingAttendances->where('status', AttendanceStatus::Alpa)->count(),
            'total' => $students->count(),
        ];

        return view('teacher.attendance.index', compact(
            'classroom',
            'subject',
            'activeSchedule',
            'mySchedules',
            'todaySchedules',
            'homeroomClassroom',
            'selectedScheduleId',
            'students',
            'date',
            'dayName',
            'existingAttendances',
            'summary'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'class_schedule_id' => ['nullable', 'exists:class_schedules,id'],
            'attendances' => ['required', 'array'],
            'attendances.*.student_id' => ['required', 'exists:students,id'],
            'attendances.*.status' => ['required', 'in:hadir,sakit,izin,alpa'],
            'attendances.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $date = $validated['date'];
        $classroomId = $validated['classroom_id'] ?? Classroom::where('homeroom_teacher_id', $teacher->id)->first()?->id;

        if (! $classroomId) {
            return back()->with('error', 'Kelas tidak valid.');
        }

        // Security / Authorization check: Verify teacher is homeroom or assigned to teach in this classroom
        $isHomeroom = Classroom::where('id', $classroomId)->where('homeroom_teacher_id', $teacher->id)->exists();
        $teachesInClass = ClassSchedule::where('classroom_id', $classroomId)->where('teacher_id', $teacher->id)->exists();
        abort_unless($isHomeroom || $teachesInClass || $teacher->isAdmin(), 403, 'Anda tidak memiliki hak akses mencatat presensi untuk kelas ini.');

        $subjectId = $validated['subject_id'] ?? null;
        $scheduleId = $validated['class_schedule_id'] ?? null;
        $subjectName = $subjectId ? Subject::find($subjectId)?->name : 'Wali Kelas / Harian';

        $waService = app(WhatsAppNotificationService::class);
        $waAlerts = [];

        foreach ($validated['attendances'] as $item) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $item['student_id'],
                    'classroom_id' => $classroomId,
                    'subject_id' => $subjectId,
                    'date' => $date,
                ],
                [
                    'class_schedule_id' => $scheduleId,
                    'recorded_by' => $teacher->id,
                    'status' => $item['status'],
                    'notes' => $item['notes'] ?? null,
                ]
            );

            // Automatically notify or prepare direct WhatsApp link for absent students
            if (in_array($item['status'], ['alpa', 'sakit', 'izin'])) {
                $student = Student::with(['parent', 'classroom'])->find($item['student_id']);
                if ($student && $student->parent?->phone_number) {
                    $res = $waService->sendAttendanceAlert(
                        $student,
                        $item['status'],
                        $date,
                        $subjectName,
                        $item['notes'] ?? null,
                        $teacher->id
                    );

                    if ($res) {
                        $waAlerts[] = [
                            'student_name' => $student->full_name,
                            'status' => $item['status'],
                            'phone' => $student->parent->phone_number,
                            'wa_url' => $res['wa_url'],
                            'is_sent' => $res['status'] === 'sent',
                        ];
                    }
                }
            }
        }

        return redirect()->route('teacher.attendance.index', [
            'date' => $date,
            'schedule_id' => $scheduleId ?? ($subjectId ? null : 'homeroom'),
            'subject_id' => $subjectId,
        ])->with('success', "Presensi siswa mapel {$subjectName} tanggal " . date('d/m/Y', strtotime($date)) . ' berhasil disimpan.')
          ->with('wa_alerts', $waAlerts);
    }
}
