<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\CurriculumStructure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ParentCurriculumController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $request->user();
        $students = $parent->students()->with('classroom')->where('status', 'active')->get();

        $selectedStudentId = $request->input('student_id', $students->first()?->id);
        $selectedStudent = $students->firstWhere('id', (int) $selectedStudentId);

        // Determine phase based on grade_level (1-2: fase_a, 3-4: fase_b, 5-6: fase_c)
        $phase = 'fase_a';
        if ($selectedStudent && $selectedStudent->classroom) {
            $grade = $selectedStudent->classroom->grade_level;
            if ($grade >= 5) {
                $phase = 'fase_c';
            } elseif ($grade >= 3) {
                $phase = 'fase_b';
            }
        }

        $curriculums = CurriculumStructure::query()
            ->where('phase', $phase)
            ->where('is_active', true)
            ->orderBy('curriculum_type')
            ->orderBy('display_order')
            ->get()
            ->groupBy('curriculum_type');

        $totalJp = $curriculums->flatten()->sum('hours_per_week');

        return view('parent.curriculum.index', compact('students', 'selectedStudent', 'curriculums', 'phase', 'totalJp'));
    }
}
