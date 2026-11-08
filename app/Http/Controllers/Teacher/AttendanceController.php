<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = Auth::user();
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->first();

        $date = $request->query('date', now()->toDateString());

        $students = $classroom
            ? Student::where('classroom_id', $classroom->id)->orderBy('full_name')->get()
            : collect();

        $existingAttendances = $classroom
            ? Attendance::where('classroom_id', $classroom->id)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id')
            : collect();

        return view('teacher.attendance.index', compact('classroom', 'students', 'date', 'existingAttendances'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user();
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->firstOrFail();

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'attendances' => ['required', 'array'],
            'attendances.*.student_id' => ['required', 'exists:students,id'],
            'attendances.*.status' => ['required', 'in:hadir,sakit,izin,alpa'],
            'attendances.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $date = $validated['date'];

        foreach ($validated['attendances'] as $item) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $item['student_id'],
                    'date' => $date,
                ],
                [
                    'classroom_id' => $classroom->id,
                    'recorded_by' => $teacher->id,
                    'status' => $item['status'],
                    'notes' => $item['notes'] ?? null,
                ]
            );
        }

        return redirect()->route('teacher.attendance.index', ['date' => $date])
            ->with('success', 'Presensi santri tanggal ' . date('d/m/Y', strtotime($date)) . ' berhasil disimpan.');
    }
}
