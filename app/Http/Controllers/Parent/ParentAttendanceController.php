<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $parent = Auth::user();
        $students = Student::where('parent_id', $parent->id)->with('classroom')->get();

        $selectedStudentId = $request->query('student_id', $students->first()?->id);
        $selectedStudent = $students->firstWhere('id', (int) $selectedStudentId) ?? $students->first();

        $attendances = $selectedStudent
            ? Attendance::where('student_id', $selectedStudent->id)
                ->latest('date')
                ->paginate(20)
                ->withQueryString()
            : collect();

        $counts = [
            'hadir' => $selectedStudent ? Attendance::where('student_id', $selectedStudent->id)->where('status', 'hadir')->count() : 0,
            'sakit' => $selectedStudent ? Attendance::where('student_id', $selectedStudent->id)->where('status', 'sakit')->count() : 0,
            'izin' => $selectedStudent ? Attendance::where('student_id', $selectedStudent->id)->where('status', 'izin')->count() : 0,
            'alpa' => $selectedStudent ? Attendance::where('student_id', $selectedStudent->id)->where('status', 'alpa')->count() : 0,
        ];

        $total = array_sum($counts);
        $percentage = $total > 0 ? round(($counts['hadir'] / $total) * 100, 1) : 100;

        return view('parent.attendance.index', compact('students', 'selectedStudent', 'attendances', 'counts', 'percentage'));
    }
}
