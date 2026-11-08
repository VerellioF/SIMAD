<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\TahfidzRecord;
use App\Models\TuitionBill;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    public function index(): View
    {
        $parent = Auth::user();

        $students = Student::where('parent_id', $parent->id)
            ->with(['classroom.homeroomTeacher'])
            ->get();

        $studentIds = $students->pluck('id');

        $unpaidBills = TuitionBill::whereIn('student_id', $studentIds)
            ->where('status', 'unpaid')
            ->get();

        $recentAttendances = Attendance::whereIn('student_id', $studentIds)
            ->latest('date')
            ->limit(10)
            ->get();

        $recentTahfidz = TahfidzRecord::whereIn('student_id', $studentIds)
            ->latest()
            ->limit(5)
            ->get();

        return view('parent.dashboard', compact('students', 'unpaidBills', 'recentAttendances', 'recentTahfidz'));
    }
}
