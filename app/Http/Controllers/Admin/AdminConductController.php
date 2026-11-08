<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CharacterRule;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentConductRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminConductController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'records'); // records, rules, leaderboard

        // 1. Records Query
        $recordsQuery = StudentConductRecord::query()
            ->with(['student.classroom', 'recorder', 'characterRule'])
            ->latest('incident_date');

        if ($request->filled('type') && $request->type !== 'all') {
            $recordsQuery->where('type', $request->type);
        }

        $records = $recordsQuery->paginate(15)->withQueryString();

        // 2. Rules List
        $rules = CharacterRule::orderBy('type')->orderBy('points')->get();

        // 3. Students Leaderboard / Overview
        $students = Student::with('classroom')->where('status', 'active')->get()->map(function ($st) {
            $st->total_score = $st->calculateTotalConductScore();
            return $st;
        })->sortByDesc('total_score');

        $classrooms = Classroom::orderBy('grade_level')->orderBy('name')->get();

        return view('admin.conduct.index', compact('records', 'rules', 'students', 'classrooms', 'tab'));
    }

    public function storeRecord(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'character_rule_id' => ['nullable', 'exists:character_rules,id'],
            'type' => ['required', 'in:apresiasi,pelanggaran'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'incident_date' => ['required', 'date'],
            'follow_up_action' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['recorded_by'] = $request->user()->id;

        StudentConductRecord::create($validated);

        return redirect()->route('admin.conduct.index', ['tab' => 'records'])
            ->with('success', 'Catatan pembiasaan karakter santri berhasil disimpan.');
    }

    public function destroyRecord(StudentConductRecord $record): RedirectResponse
    {
        $record->delete();

        return redirect()->route('admin.conduct.index', ['tab' => 'records'])
            ->with('success', 'Catatan karakter santri berhasil dihapus.');
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:apresiasi,pelanggaran'],
            'category' => ['required', 'in:akhlak_adab,kedisiplinan,prestasi,kebersihan_kerapian,ibadah'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        CharacterRule::create($validated);

        return redirect()->route('admin.conduct.index', ['tab' => 'rules'])
            ->with('success', 'Aturan karakter baru berhasil ditambahkan.');
    }

    public function destroyRule(CharacterRule $rule): RedirectResponse
    {
        $rule->delete();

        return redirect()->route('admin.conduct.index', ['tab' => 'rules'])
            ->with('success', 'Aturan tata tertib berhasil dihapus.');
    }
}
