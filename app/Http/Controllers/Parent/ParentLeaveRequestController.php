<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentLeaveRequestController extends Controller
{
    public function index(): View
    {
        $parent = Auth::user();
        $students = Student::where('parent_id', $parent->id)->get();

        $leaveRequests = LeaveRequest::where('parent_id', $parent->id)
            ->with(['student', 'reviewer'])
            ->latest()
            ->paginate(15);

        return view('parent.leave.index', compact('students', 'leaveRequests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $parent = Auth::user();

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'reason_type' => ['required', 'in:sakit,izin'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['required', 'string', 'max:500'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'end_date.after_or_equal' => 'Tanggal akhir izin tidak boleh sebelum tanggal mulai.',
            'description.required' => 'Keterangan izin wajib diisi.',
        ]);

        // Security check: ensure student belongs to authenticated parent
        $student = Student::where('id', $validated['student_id'])
            ->where('parent_id', $parent->id)
            ->firstOrFail();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('izin', 'public');
        }

        LeaveRequest::create([
            'student_id' => $student->id,
            'parent_id' => $parent->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason_type' => $validated['reason_type'],
            'description' => $validated['description'],
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('parent.leave.index')
            ->with('success', "Surat izin untuk santri {$student->full_name} berhasil dikirim ke wali kelas.");
    }
}
