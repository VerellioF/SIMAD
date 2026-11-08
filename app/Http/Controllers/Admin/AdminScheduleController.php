<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $classrooms = Classroom::with('academicYear')->orderBy('grade_level')->orderBy('name')->get();
        $selectedClassroomId = $request->input('classroom_id', $classrooms->first()?->id);

        $selectedClassroom = $classrooms->firstWhere('id', (int) $selectedClassroomId);

        $schedules = ClassSchedule::query()
            ->when($selectedClassroomId, fn ($q) => $q->where('classroom_id', $selectedClassroomId))
            ->with(['subject', 'teacher', 'classroom'])
            ->orderBy('period_order')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $teachers = User::where('role', UserRole::Teacher)->where('is_active', true)->orderBy('name')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

        return view('admin.schedules.index', compact(
            'classrooms',
            'selectedClassroom',
            'schedules',
            'subjects',
            'teachers',
            'activeYear',
            'days'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'day_of_week' => ['required', 'in:senin,selasa,rabu,kamis,jumat,sabtu'],
            'start_time' => ['required', 'string', 'max:10'],
            'end_time' => ['required', 'string', 'max:10'],
            'period_order' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        ClassSchedule::create($validated);

        return redirect()->route('admin.schedules.index', ['classroom_id' => $validated['classroom_id']])
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, ClassSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'day_of_week' => ['required', 'in:senin,selasa,rabu,kamis,jumat,sabtu'],
            'start_time' => ['required', 'string', 'max:10'],
            'end_time' => ['required', 'string', 'max:10'],
            'period_order' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index', ['classroom_id' => $validated['classroom_id']])
            ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(ClassSchedule $schedule): RedirectResponse
    {
        $classroomId = $schedule->classroom_id;
        $schedule->delete();

        return redirect()->route('admin.schedules.index', ['classroom_id' => $classroomId])
            ->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
