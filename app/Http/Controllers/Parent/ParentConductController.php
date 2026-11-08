<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\StudentConductRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ParentConductController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $request->user();
        $students = $parent->students()->with('classroom')->where('status', 'active')->get();

        $selectedStudentId = $request->input('student_id', $students->first()?->id);
        $selectedStudent = $students->firstWhere('id', (int) $selectedStudentId);

        $records = collect();
        $totalScore = 100;
        $totalApresiasi = 0;
        $totalPelanggaran = 0;

        if ($selectedStudent) {
            $records = StudentConductRecord::query()
                ->where('student_id', $selectedStudent->id)
                ->with(['characterRule', 'recorder'])
                ->latest('incident_date')
                ->get();

            $totalApresiasi = $records->where('type', 'apresiasi')->sum('points');
            $totalPelanggaran = $records->where('type', 'pelanggaran')->sum('points');
            $totalScore = 100 + $totalApresiasi - $totalPelanggaran;
        }

        return view('parent.conduct.index', compact(
            'students',
            'selectedStudent',
            'records',
            'totalScore',
            'totalApresiasi',
            'totalPelanggaran'
        ));
    }
}
