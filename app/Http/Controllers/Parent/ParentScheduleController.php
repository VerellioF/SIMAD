<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ParentScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $request->user();
        $students = $parent->students()->with('classroom')->where('status', 'active')->get();

        $selectedStudentId = $request->input('student_id', $students->first()?->id);
        $selectedStudent = $students->firstWhere('id', (int) $selectedStudentId);

        $schedules = collect();
        if ($selectedStudent && $selectedStudent->classroom_id) {
            $schedules = ClassSchedule::query()
                ->where('classroom_id', $selectedStudent->classroom_id)
                ->with(['subject', 'teacher'])
                ->orderBy('period_order')
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_of_week');
        }

        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $activeYear = AcademicYear::where('is_active', true)->first();

        return view('parent.schedules.index', compact(
            'students',
            'selectedStudent',
            'schedules',
            'days',
            'activeYear'
        ));
    }
}
