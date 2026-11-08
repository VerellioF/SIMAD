<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TeacherScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $schedules = ClassSchedule::query()
            ->where('teacher_id', $teacher->id)
            ->with(['classroom', 'subject'])
            ->orderBy('period_order')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

        $totalTeachingHours = ClassSchedule::query()
            ->where('teacher_id', $teacher->id)
            ->count();

        return view('teacher.schedules.index', compact('schedules', 'days', 'activeYear', 'totalTeachingHours'));
    }
}
