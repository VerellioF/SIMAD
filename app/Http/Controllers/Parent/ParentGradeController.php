<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AcademicGrade;
use App\Models\Student;
use App\Models\Subject;
use App\Services\ReportCardPdfService;
use App\Services\StudentCardPdfService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ParentGradeController extends Controller
{
    public function index(Request $request): View
    {
        $parent = Auth::user();
        $students = Student::where('parent_id', $parent->id)->get();

        $selectedStudentId = $request->query('student_id', $students->first()?->id);
        $selectedStudent = $students->firstWhere('id', (int) $selectedStudentId) ?? $students->first();

        $subjects = Subject::orderBy('category')->orderBy('name')->get();

        $grades = $selectedStudent
            ? AcademicGrade::where('student_id', $selectedStudent->id)
                ->with('subject')
                ->get()
                ->groupBy('subject_id')
            : collect();

        return view('parent.grades.index', compact('students', 'selectedStudent', 'subjects', 'grades'));
    }

    public function downloadReportCard(Student $student, ReportCardPdfService $service): Response
    {
        $parent = Auth::user();
        abort_unless($student->parent_id === $parent->id, 403);

        $pdf = $service->generate($student);
        return $pdf->stream("Rapor_{$student->nis}_{$student->full_name}.pdf");
    }

    public function downloadStudentCard(Student $student, StudentCardPdfService $service): Response
    {
        $parent = Auth::user();
        abort_unless($student->parent_id === $parent->id, 403);

        $pdf = $service->generateStudentCard($student);
        return $pdf->stream("KTS_{$student->nis}_{$student->full_name}.pdf");
    }
}
