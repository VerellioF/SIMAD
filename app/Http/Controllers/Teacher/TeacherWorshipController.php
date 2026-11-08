<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\DailyWorshipRecord;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeacherWorshipController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $date = $request->input('date', now()->toDateString());

        // Get homeroom classroom or first available classroom
        $homeroomClassrooms = $teacher->homeroomClassrooms()->with('academicYear')->get();
        $allClassrooms = Classroom::with('academicYear')->orderBy('grade_level')->orderBy('name')->get();

        $selectedClassroomId = $request->input(
            'classroom_id',
            $homeroomClassrooms->first()?->id ?? $allClassrooms->first()?->id
        );

        $students = Student::query()
            ->when($selectedClassroomId, fn ($q) => $q->where('classroom_id', $selectedClassroomId))
            ->where('status', 'active')
            ->orderBy('full_name')
            ->with(['dailyWorshipRecords' => fn ($q) => $q->where('date', $date)])
            ->get();

        return view('teacher.worship.index', compact(
            'students',
            'allClassrooms',
            'selectedClassroomId',
            'date'
        ));
    }

    public function updateNotes(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'date' => ['required', 'date'],
            'teacher_notes' => ['nullable', 'string', 'max:255'],
        ]);

        DailyWorshipRecord::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'date' => $validated['date'],
            ],
            [
                'teacher_notes' => $validated['teacher_notes'],
            ]
        );

        return back()->with('success', 'Catatan evaluasi ibadah berhasil disimpan.');
    }
}
