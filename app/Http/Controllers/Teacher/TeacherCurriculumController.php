<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\CurriculumStructure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TeacherCurriculumController extends Controller
{
    public function index(Request $request): View
    {
        $phase = $request->input('phase', 'fase_a');

        $curriculums = CurriculumStructure::query()
            ->where('phase', $phase)
            ->where('is_active', true)
            ->orderBy('curriculum_type')
            ->orderBy('display_order')
            ->get()
            ->groupBy('curriculum_type');

        return view('teacher.curriculum.index', compact('curriculums', 'phase'));
    }
}
