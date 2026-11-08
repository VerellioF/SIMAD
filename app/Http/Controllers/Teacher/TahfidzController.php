<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\TahfidzRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TahfidzController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = Auth::user();
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->first();

        $students = $classroom ? Student::where('classroom_id', $classroom->id)->get() : collect();

        $records = $classroom
            ? TahfidzRecord::whereIn('student_id', $students->pluck('id'))
                ->with(['student', 'teacher'])
                ->latest('date')
                ->paginate(15)
            : collect();

        return view('teacher.tahfidz.index', compact('classroom', 'students', 'records'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'date' => ['required', 'date'],
            'surah_name' => ['required', 'string', 'max:100'],
            'verse_range' => ['required', 'string', 'max:50'],
            'fluency_grade' => ['required', 'in:lancar,kurang_lancar,mengulang'],
            'tajweed_grade' => ['required', 'in:mumtaz,jayyid,maqbul'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        TahfidzRecord::create([
            'student_id' => $validated['student_id'],
            'teacher_id' => $teacher->id,
            'date' => $validated['date'],
            'surah_name' => $validated['surah_name'],
            'verse_range' => $validated['verse_range'],
            'fluency_grade' => $validated['fluency_grade'],
            'tajweed_grade' => $validated['tajweed_grade'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('teacher.tahfidz.index')
            ->with('success', 'Setoran hafalan Qur\'an berhasil dicatat.');
    }
}
