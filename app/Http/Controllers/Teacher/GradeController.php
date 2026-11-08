<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Enums\GradeType;
use App\Http\Controllers\Controller;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = Auth::user();

        // 1. All classrooms accessible by this teacher (homeroom + teaching schedules + all)
        $homeroomClassrooms = $teacher->homeroomClassrooms()->with('academicYear')->get();
        $scheduledClassroomIds = ClassSchedule::where('teacher_id', $teacher->id)->pluck('classroom_id')->unique();
        $scheduledClassrooms = Classroom::whereIn('id', $scheduledClassroomIds)->with('academicYear')->get();

        $allClassrooms = Classroom::with('academicYear')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        // Preferred default classroom
        $defaultClassroomId = $homeroomClassrooms->first()?->id
            ?? $scheduledClassrooms->first()?->id
            ?? $allClassrooms->first()?->id;

        $selectedClassroomId = $request->query('classroom_id', (string) $defaultClassroomId);
        $classroom = $allClassrooms->firstWhere('id', (int) $selectedClassroomId) ?? $allClassrooms->first();

        // 2. Subjects (highlighting subjects taught by teacher)
        $taughtSubjectIds = ClassSchedule::where('teacher_id', $teacher->id)
            ->when($classroom, fn ($q) => $q->where('classroom_id', $classroom->id))
            ->pluck('subject_id')
            ->unique();

        $subjects = Subject::orderBy('name')->get();
        $defaultSubjectId = $taughtSubjectIds->first() ?? $subjects->first()?->id;
        $selectedSubjectId = $request->query('subject_id', (string) $defaultSubjectId);
        $subject = $subjects->firstWhere('id', (int) $selectedSubjectId) ?? $subjects->first();

        // 3. Assessment Type
        $gradeType = $request->query('grade_type', 'harian');
        $validGradeTypes = ['harian', 'tugas', 'pts', 'pas'];
        if (! in_array($gradeType, $validGradeTypes, true)) {
            $gradeType = 'harian';
        }

        // 4. Students in selected classroom
        $students = $classroom
            ? Student::where('classroom_id', $classroom->id)
                ->where('status', 'active')
                ->orderBy('full_name')
                ->get()
            : collect();

        // 5. Existing grades for this classroom, subject, and grade_type
        $activeYear = AcademicYear::where('is_active', true)->first();

        $existingGrades = $classroom && $subject
            ? AcademicGrade::where('classroom_id', $classroom->id)
                ->where('subject_id', $subject->id)
                ->where('grade_type', $gradeType)
                ->when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))
                ->get()
                ->keyBy('student_id')
            : collect();

        // Calculate stats
        $filledGrades = $existingGrades->filter(fn ($g) => $g->score !== null);
        $averageScore = $filledGrades->isNotEmpty() ? round((float) $filledGrades->avg('score'), 1) : 0;
        $maxScore = $filledGrades->isNotEmpty() ? (float) $filledGrades->max('score') : 0;
        $minScore = $filledGrades->isNotEmpty() ? (float) $filledGrades->min('score') : 0;
        $passedCount = $filledGrades->filter(fn ($g) => (float) $g->score >= 75)->count();
        $remedialCount = $filledGrades->filter(fn ($g) => (float) $g->score < 75)->count();

        return view('teacher.grades.index', compact(
            'classroom',
            'allClassrooms',
            'homeroomClassrooms',
            'scheduledClassrooms',
            'subjects',
            'subject',
            'selectedSubjectId',
            'gradeType',
            'students',
            'existingGrades',
            'averageScore',
            'maxScore',
            'minScore',
            'passedCount',
            'remedialCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user();

        // Support backward compatibility if classroom_id not supplied in request
        $homeroomClassroom = Classroom::where('homeroom_teacher_id', $teacher->id)->first();

        $validated = $request->validate([
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'grade_type' => ['required', 'in:harian,tugas,pts,pas'],
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $classroomId = $validated['classroom_id'] ?? $homeroomClassroom?->id ?? Classroom::first()?->id;
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $savedCount = 0;
        foreach ($validated['grades'] as $item) {
            if ($item['score'] === null || $item['score'] === '') {
                continue;
            }

            AcademicGrade::updateOrCreate(
                [
                    'student_id' => $item['student_id'],
                    'classroom_id' => $classroomId,
                    'subject_id' => $validated['subject_id'],
                    'academic_year_id' => $activeYear->id,
                    'grade_type' => $validated['grade_type'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'score' => $item['score'],
                    'notes' => $item['notes'] ?? null,
                ]
            );
            $savedCount++;
        }

        $subjectName = Subject::find($validated['subject_id'])?->name ?? 'Mata Pelajaran';
        $typeLabel = match ($validated['grade_type']) {
            'harian' => 'Ulangan Harian',
            'tugas' => 'Tugas Mandiri',
            'pts' => 'PTS',
            'pas' => 'PAS',
            default => 'Nilai',
        };

        return redirect()->route('teacher.grades.index', [
            'classroom_id' => $classroomId,
            'subject_id' => $validated['subject_id'],
            'grade_type' => $validated['grade_type'],
        ])->with('success', "Penilaian {$typeLabel} mapel {$subjectName} berhasil disimpan untuk {$savedCount} siswa.");
    }
}
