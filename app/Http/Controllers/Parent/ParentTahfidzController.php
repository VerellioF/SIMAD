<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\TahfidzRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentTahfidzController extends Controller
{
    public function index(Request $request): View
    {
        $parent = Auth::user();
        $students = Student::where('parent_id', $parent->id)->get();

        $selectedStudentId = $request->query('student_id', $students->first()?->id);
        $selectedStudent = $students->firstWhere('id', (int) $selectedStudentId) ?? $students->first();

        $records = $selectedStudent
            ? TahfidzRecord::where('student_id', $selectedStudent->id)
                ->with('teacher')
                ->latest('date')
                ->paginate(15)
                ->withQueryString()
            : collect();

        $totalSetoran = $selectedStudent ? TahfidzRecord::where('student_id', $selectedStudent->id)->count() : 0;
        $lancarCount = $selectedStudent ? TahfidzRecord::where('student_id', $selectedStudent->id)->where('fluency_grade', 'lancar')->count() : 0;

        return view('parent.tahfidz.index', compact('students', 'selectedStudent', 'records', 'totalSetoran', 'lancarCount'));
    }
}
