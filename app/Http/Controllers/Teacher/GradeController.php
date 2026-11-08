<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\Classroom;
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
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->first();

        $subjects = Subject::orderBy('name')->get();
        $selectedSubjectId = $request->query('subject_id', $subjects->first()?->id);
        $gradeType = $request->query('grade_type', 'harian');

        $students = $classroom ? Student::where('classroom_id', $classroom->id)->get() : collect();

        $existingGrades = $classroom && $selectedSubjectId
            ? AcademicGrade::where('classroom_id', $classroom->id)
                ->where('subject_id', $selectedSubjectId)
                ->where('grade_type', $gradeType)
                ->get()
                ->keyBy('student_id')
            : collect();

        return view('teacher.grades.index', compact('classroom', 'subjects', 'selectedSubjectId', 'gradeType', 'students', 'existingGrades'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user();
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->firstOrFail();
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'grade_type' => ['required', 'in:harian,tugas,pts,pas'],
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated['grades'] as $item) {
            if ($item['score'] === null || $item['score'] === '') {
                continue;
            }

            AcademicGrade::updateOrCreate(
                [
                    'student_id' => $item['student_id'],
                    'classroom_id' => $classroom->id,
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
        }

        return redirect()->route('teacher.grades.index', [
            'subject_id' => $validated['subject_id'],
            'grade_type' => $validated['grade_type'],
        ])->with('success', 'Buku penilaian akademik berhasil disimpan.');
    }
}
