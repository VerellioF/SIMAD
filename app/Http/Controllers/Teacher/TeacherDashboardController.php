<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\TahfidzRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function index(): View
    {
        $teacher = Auth::user();

        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)
            ->with(['academicYear'])
            ->first();

        $students = $classroom ? Student::where('classroom_id', $classroom->id)->get() : collect();

        $recentAttendances = $classroom
            ? Attendance::where('classroom_id', $classroom->id)->whereDate('date', now()->toDateString())->get()
            : collect();

        $recentTahfidz = $classroom
            ? TahfidzRecord::whereIn('student_id', $students->pluck('id'))->latest()->limit(5)->get()
            : collect();

        return view('teacher.dashboard', compact('classroom', 'students', 'recentAttendances', 'recentTahfidz'));
    }
}
