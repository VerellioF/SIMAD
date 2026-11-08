<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Extracurricular;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicExtracurricularController extends Controller
{
    public function index(Request $request): View
    {
        $query = Extracurricular::query()
            ->withCount('enrollments')
            ->where('is_active', true);

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $extracurriculars = $query->orderBy('name')->get();

        return view('public.extracurriculars', compact('extracurriculars'));
    }
}
