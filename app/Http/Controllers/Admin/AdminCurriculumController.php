<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurriculumStructure;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminCurriculumController extends Controller
{
    public function index(Request $request): View
    {
        $selectedPhase = $request->input('phase', 'all');

        $query = CurriculumStructure::query()->with('subject');

        if ($selectedPhase !== 'all') {
            $query->where('phase', $selectedPhase);
        }

        $structures = $query->orderBy('phase')->orderBy('curriculum_type')->orderBy('display_order')->paginate(20)->withQueryString();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('admin.curriculum.index', compact('structures', 'subjects', 'selectedPhase'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:20'],
            'phase' => ['required', 'in:fase_a,fase_b,fase_c'],
            'curriculum_type' => ['required', 'in:kemenag_pai,nasional_merdeka,muatan_lokal_pesantren'],
            'hours_per_week' => ['required', 'integer', 'min:1', 'max:10'],
            'competency_goals' => ['nullable', 'string', 'max:1000'],
            'syllabus_summary' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        CurriculumStructure::create($validated);

        return redirect()->route('admin.curriculum.index', ['phase' => $validated['phase']])
            ->with('success', 'Mata pelajaran struktur kurikulum berhasil ditambahkan.');
    }

    public function update(Request $request, CurriculumStructure $curriculum): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:20'],
            'phase' => ['required', 'in:fase_a,fase_b,fase_c'],
            'curriculum_type' => ['required', 'in:kemenag_pai,nasional_merdeka,muatan_lokal_pesantren'],
            'hours_per_week' => ['required', 'integer', 'min:1', 'max:10'],
            'competency_goals' => ['nullable', 'string', 'max:1000'],
            'syllabus_summary' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['display_order'] = $validated['display_order'] ?? 0;

        $curriculum->update($validated);

        return redirect()->route('admin.curriculum.index', ['phase' => $validated['phase']])
            ->with('success', 'Struktur kurikulum berhasil diperbarui.');
    }

    public function destroy(CurriculumStructure $curriculum): RedirectResponse
    {
        $phase = $curriculum->phase;
        $curriculum->delete();

        return redirect()->route('admin.curriculum.index', ['phase' => $phase])
            ->with('success', 'Struktur kurikulum berhasil dihapus.');
    }
}
