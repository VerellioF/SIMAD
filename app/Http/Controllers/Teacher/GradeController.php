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
use App\Services\ReportCardPdfService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        // All grades for this classroom and subject across all grade types
        $allExistingGrades = $classroom && $subject
            ? AcademicGrade::where('classroom_id', $classroom->id)
                ->where('subject_id', $subject->id)
                ->when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))
                ->get()
                ->groupBy('student_id')
            : collect();

        // Backward compatibility: existingGrades filtered by current grade_type
        $existingGrades = $classroom && $subject
            ? AcademicGrade::where('classroom_id', $classroom->id)
                ->where('subject_id', $subject->id)
                ->where('grade_type', $gradeType)
                ->when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))
                ->get()
                ->keyBy('student_id')
            : collect();

        // Build structured student grades map for spreadsheet mode
        $studentGradeMap = [];
        foreach ($students as $st) {
            $stGrades = $allExistingGrades->get($st->id, collect());
            $harian = $stGrades->firstWhere('grade_type.value', 'harian')?->score;
            $tugas = $stGrades->firstWhere('grade_type.value', 'tugas')?->score;
            $pts = $stGrades->firstWhere('grade_type.value', 'pts')?->score;
            $pas = $stGrades->firstWhere('grade_type.value', 'pas')?->score;
            $notes = $stGrades->first(fn ($g) => !empty($g->notes))?->notes ?? '';

            $scores = array_filter([(float)$harian, (float)$pts, (float)$pas]);
            $finalScore = !empty($scores) ? round(array_sum($scores) / count($scores), 1) : null;

            $predicate = match (true) {
                $finalScore === null => '-',
                $finalScore >= 88 => 'A',
                $finalScore >= 76 => 'B',
                $finalScore >= 65 => 'C',
                default => 'D',
            };

            $studentGradeMap[$st->id] = [
                'harian' => $harian !== null ? (float)$harian : '',
                'tugas' => $tugas !== null ? (float)$tugas : '',
                'pts' => $pts !== null ? (float)$pts : '',
                'pas' => $pas !== null ? (float)$pas : '',
                'final' => $finalScore !== null ? $finalScore : '',
                'predicate' => $predicate,
                'notes' => $notes,
            ];
        }

        // Mode: spreadsheet (default) or single
        $viewMode = $request->query('mode', 'spreadsheet');

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
            'viewMode',
            'students',
            'studentGradeMap',
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
            'grade_type' => ['nullable', 'string'],
            'mode' => ['nullable', 'string', 'in:spreadsheet,single'],
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.notes' => ['nullable', 'string', 'max:255'],
            'grades.*.harian' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.tugas' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.pts' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.pas' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $classroomId = $validated['classroom_id'] ?? $homeroomClassroom?->id ?? Classroom::first()?->id;
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $savedCount = 0;
        $mode = $validated['mode'] ?? ($request->has('grade_type') && in_array($request->input('grade_type'), ['harian', 'tugas', 'pts', 'pas']) && !isset($validated['grades'][0]['harian']) ? 'single' : 'spreadsheet');

        foreach ($validated['grades'] as $item) {
            $studentId = $item['student_id'];
            $notes = $item['notes'] ?? null;

            // If spreadsheet mode or multi-column fields present
            if ($mode === 'spreadsheet' || isset($item['harian']) || isset($item['tugas']) || isset($item['pts']) || isset($item['pas'])) {
                $assessmentTypes = ['harian', 'tugas', 'pts', 'pas'];
                foreach ($assessmentTypes as $type) {
                    if (array_key_exists($type, $item) && $item[$type] !== null && $item[$type] !== '') {
                        AcademicGrade::updateOrCreate(
                            [
                                'student_id' => $studentId,
                                'classroom_id' => $classroomId,
                                'subject_id' => $validated['subject_id'],
                                'academic_year_id' => $activeYear->id,
                                'grade_type' => $type,
                            ],
                            [
                                'teacher_id' => $teacher->id,
                                'score' => $item[$type],
                                'notes' => $notes,
                            ]
                        );
                        $savedCount++;
                    }
                }
            } elseif (!empty($validated['grade_type']) && isset($item['score']) && $item['score'] !== '' && $item['score'] !== null) {
                // Backward-compatible single assessment type
                AcademicGrade::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'classroom_id' => $classroomId,
                        'subject_id' => $validated['subject_id'],
                        'academic_year_id' => $activeYear->id,
                        'grade_type' => $validated['grade_type'],
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'score' => $item['score'],
                        'notes' => $notes,
                    ]
                );
                $savedCount++;
            }
        }

        $subjectName = Subject::find($validated['subject_id'])?->name ?? 'Mata Pelajaran';

        return redirect()->route('teacher.grades.index', [
            'classroom_id' => $classroomId,
            'subject_id' => $validated['subject_id'],
            'grade_type' => $validated['grade_type'] ?? 'harian',
            'mode' => $mode,
        ])->with('success', "Penilaian mapel {$subjectName} berhasil disimpan ({$savedCount} data diperbarui).");
    }

    public function downloadReportCard(Student $student, ReportCardPdfService $service): Response
    {
        $pdf = $service->generate($student);
        return $pdf->stream("Rapor_{$student->nis}_{$student->full_name}.pdf");
    }
}
