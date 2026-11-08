<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\ExtracurricularEnrollment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ParentExtracurricularController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $request->user();
        $students = $parent->students()->with('classroom')->where('status', 'active')->get();

        $selectedStudentId = $request->input('student_id', $students->first()?->id);
        $selectedStudent = $students->firstWhere('id', (int) $selectedStudentId);

        $activeYear = AcademicYear::where('is_active', true)->first();

        $enrolledIds = [];
        $enrollments = collect();

        if ($selectedStudent && $activeYear) {
            $enrollments = ExtracurricularEnrollment::with('extracurricular')
                ->where('student_id', $selectedStudent->id)
                ->where('academic_year_id', $activeYear->id)
                ->get();

            $enrolledIds = $enrollments->pluck('extracurricular_id')->toArray();
        }

        $availableEkskuls = Extracurricular::where('is_active', true)->orderBy('name')->get();

        return view('parent.extracurriculars.index', compact(
            'students',
            'selectedStudent',
            'enrollments',
            'availableEkskuls',
            'enrolledIds',
            'activeYear'
        ));
    }

    public function enroll(Request $request): RedirectResponse
    {
        $parent = $request->user();
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'extracurricular_id' => ['required', 'exists:extracurriculars,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $student = $parent->students()->findOrFail($validated['student_id']);

        ExtracurricularEnrollment::updateOrCreate(
            [
                'student_id' => $student->id,
                'extracurricular_id' => $validated['extracurricular_id'],
                'academic_year_id' => $validated['academic_year_id'],
            ],
            [
                'status' => 'active',
            ]
        );

        return redirect()->route('parent.ekskul.index', ['student_id' => $student->id])
            ->with('success', 'Ananda berhasil didaftarkan pada kegiatan ekstrakurikuler.');
    }

    public function cancel(Request $request, ExtracurricularEnrollment $enrollment): RedirectResponse
    {
        $parent = $request->user();

        // Check if student belongs to parent
        if ($enrollment->student->parent_id !== $parent->id) {
            abort(403);
        }

        $studentId = $enrollment->student_id;
        $enrollment->delete();

        return redirect()->route('parent.ekskul.index', ['student_id' => $studentId])
            ->with('success', 'Pendaftaran ekstrakurikuler ananda telah dibatalkan.');
    }
}
