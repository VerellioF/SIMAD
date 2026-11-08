<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\CharacterRule;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentConductRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeacherConductController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        // Get homeroom or all classrooms
        $classrooms = Classroom::orderBy('grade_level')->orderBy('name')->get();
        $selectedClassroomId = $request->input('classroom_id', $classrooms->first()?->id);

        $students = Student::query()
            ->when($selectedClassroomId, fn ($q) => $q->where('classroom_id', $selectedClassroomId))
            ->where('status', 'active')
            ->orderBy('full_name')
            ->with(['conductRecords' => fn ($q) => $q->latest('incident_date')->limit(5)])
            ->get()
            ->map(function ($st) {
                $st->total_score = $st->calculateTotalConductScore();
                return $st;
            });

        $recentRecords = StudentConductRecord::query()
            ->with(['student.classroom', 'characterRule', 'recorder'])
            ->where('recorded_by', $teacher->id)
            ->latest('incident_date')
            ->limit(10)
            ->get();

        $rules = CharacterRule::where('is_active', true)->orderBy('type')->orderBy('points')->get();

        return view('teacher.conduct.index', compact(
            'classrooms',
            'selectedClassroomId',
            'students',
            'recentRecords',
            'rules'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();
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

        $validated['recorded_by'] = $teacher->id;

        StudentConductRecord::create($validated);

        return back()->with('success', 'Catatan pembiasaan karakter santri berhasil disimpan.');
    }
}
