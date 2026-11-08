<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\DailyWorshipRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ParentWorshipController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $request->user();
        $students = $parent->students()->with('classroom')->where('status', 'active')->get();

        $selectedStudentId = $request->input('student_id', $students->first()?->id);
        $selectedStudent = $students->firstWhere('id', (int) $selectedStudentId);

        $selectedDate = $request->input('date', now()->toDateString());

        $currentRecord = null;
        $recentRecords = collect();

        if ($selectedStudent) {
            $currentRecord = DailyWorshipRecord::firstOrNew([
                'student_id' => $selectedStudent->id,
                'date' => $selectedDate,
            ]);

            $recentRecords = DailyWorshipRecord::where('student_id', $selectedStudent->id)
                ->latest('date')
                ->limit(7)
                ->get();
        }

        return view('parent.worship.index', compact(
            'students',
            'selectedStudent',
            'selectedDate',
            'currentRecord',
            'recentRecords'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $parent = $request->user();
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'date' => ['required', 'date'],
            'parent_notes' => ['nullable', 'string', 'max:255'],
        ]);

        // Security check: ensure student belongs to parent
        $student = $parent->students()->findOrFail($validated['student_id']);

        DailyWorshipRecord::updateOrCreate(
            [
                'student_id' => $student->id,
                'date' => $validated['date'],
            ],
            [
                'subuh' => $request->boolean('subuh'),
                'dzuhur' => $request->boolean('dzuhur'),
                'ashar' => $request->boolean('ashar'),
                'maghrib' => $request->boolean('maghrib'),
                'isya' => $request->boolean('isya'),
                'dhuha' => $request->boolean('dhuha'),
                'tadarus_quran' => $request->boolean('tadarus_quran'),
                'birrul_walidain' => $request->boolean('birrul_walidain'),
                'parent_notes' => $validated['parent_notes'],
            ]
        );

        return redirect()->route('parent.worship.index', [
            'student_id' => $student->id,
            'date' => $validated['date'],
        ])->with('success', 'Jurnal Mutaba\'ah Ibadah Yaumiyah berhasil diperbarui!');
    }
}
