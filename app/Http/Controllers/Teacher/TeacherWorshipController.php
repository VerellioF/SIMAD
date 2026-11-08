<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\DailyWorshipRecord;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeacherWorshipController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $date = $request->input('date', now()->toDateString());

        // Get homeroom classroom or first available classroom
        $homeroomClassrooms = $teacher->homeroomClassrooms()->with('academicYear')->get();
        $allClassrooms = Classroom::with('academicYear')->orderBy('grade_level')->orderBy('name')->get();

        $selectedClassroomId = $request->input(
            'classroom_id',
            $homeroomClassrooms->first()?->id ?? $allClassrooms->first()?->id
        );

        $classroom = $allClassrooms->firstWhere('id', (int) $selectedClassroomId) ?? $allClassrooms->first();

        $students = Student::query()
            ->when($classroom, fn ($q) => $q->where('classroom_id', $classroom->id))
            ->where('status', 'active')
            ->orderBy('full_name')
            ->with(['dailyWorshipRecords' => fn ($q) => $q->where('date', $date)])
            ->get();

        // Calculate statistics
        $records = $students->map(fn ($st) => $st->dailyWorshipRecords->first())->filter();
        $totalStudents = $students->count();

        $stats = [
            'total' => $totalStudents,
            'subuh' => $records->where('subuh', true)->count(),
            'dzuhur' => $records->where('dzuhur', true)->count(),
            'ashar' => $records->where('ashar', true)->count(),
            'maghrib' => $records->where('maghrib', true)->count(),
            'isya' => $records->where('isya', true)->count(),
            'dhuha' => $records->where('dhuha', true)->count(),
            'tadarus' => $records->where('tadarus_quran', true)->count(),
            'birrul' => $records->where('birrul_walidain', true)->count(),
        ];

        return view('teacher.worship.index', compact(
            'classroom',
            'students',
            'allClassrooms',
            'selectedClassroomId',
            'date',
            'stats'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'date' => ['required', 'date'],
            'worship' => ['required', 'array'],
            'worship.*.student_id' => ['required', 'exists:students,id'],
            'worship.*.subuh' => ['nullable'],
            'worship.*.dzuhur' => ['nullable'],
            'worship.*.ashar' => ['nullable'],
            'worship.*.maghrib' => ['nullable'],
            'worship.*.isya' => ['nullable'],
            'worship.*.dhuha' => ['nullable'],
            'worship.*.tadarus_quran' => ['nullable'],
            'worship.*.birrul_walidain' => ['nullable'],
            'worship.*.teacher_notes' => ['nullable', 'string', 'max:255'],
        ]);

        $date = $validated['date'];
        $classroomId = $validated['classroom_id'];

        foreach ($validated['worship'] as $item) {
            DailyWorshipRecord::updateOrCreate(
                [
                    'student_id' => $item['student_id'],
                    'date' => $date,
                ],
                [
                    'subuh' => isset($item['subuh']) && (bool) $item['subuh'],
                    'dzuhur' => isset($item['dzuhur']) && (bool) $item['dzuhur'],
                    'ashar' => isset($item['ashar']) && (bool) $item['ashar'],
                    'maghrib' => isset($item['maghrib']) && (bool) $item['maghrib'],
                    'isya' => isset($item['isya']) && (bool) $item['isya'],
                    'dhuha' => isset($item['dhuha']) && (bool) $item['dhuha'],
                    'tadarus_quran' => isset($item['tadarus_quran']) && (bool) $item['tadarus_quran'],
                    'birrul_walidain' => isset($item['birrul_walidain']) && (bool) $item['birrul_walidain'],
                    'teacher_notes' => $item['teacher_notes'] ?? null,
                ]
            );
        }

        $classroomName = Classroom::find($classroomId)?->name ?? 'Kelas';

        return redirect()->route('teacher.worship.index', [
            'classroom_id' => $classroomId,
            'date' => $date,
        ])->with('success', "Mutaba'ah ibadah yaumiyah {$classroomName} tanggal " . date('d/m/Y', strtotime($date)) . ' berhasil disimpan.');
    }

    public function updateNotes(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'date' => ['required', 'date'],
            'teacher_notes' => ['nullable', 'string', 'max:255'],
        ]);

        DailyWorshipRecord::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'date' => $validated['date'],
            ],
            [
                'teacher_notes' => $validated['teacher_notes'],
            ]
        );

        return back()->with('success', 'Catatan evaluasi ibadah berhasil disimpan.');
    }
}
