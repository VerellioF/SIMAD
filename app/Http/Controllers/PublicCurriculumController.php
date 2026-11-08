<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CurriculumStructure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicCurriculumController extends Controller
{
    public function index(Request $request): View
    {
        $selectedPhase = $request->input('phase', 'fase_a'); // fase_a, fase_b, fase_c, all

        $query = CurriculumStructure::query()->where('is_active', true);

        if ($selectedPhase !== 'all') {
            $query->where('phase', $selectedPhase);
        }

        $curriculums = $query->orderBy('display_order')->orderBy('name')->get()->groupBy('curriculum_type');

        $totalJp = $curriculums->flatten()->sum('hours_per_week');

        return view('public.curriculum', compact('curriculums', 'selectedPhase', 'totalJp'));
    }
}
