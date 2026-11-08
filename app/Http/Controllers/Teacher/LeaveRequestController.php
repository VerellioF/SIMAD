<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\LeaveRequest;
use App\Services\WhatsAppNotificationService;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index(): View
    {
        $teacher = Auth::user();
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->first();

        $leaveRequests = $classroom
            ? LeaveRequest::whereHas('student', fn ($q) => $q->where('classroom_id', $classroom->id))
                ->with(['student', 'parent'])
                ->latest()
                ->paginate(15)
            : collect();

        return view('teacher.leave.index', compact('classroom', 'leaveRequests'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'review_notes' => ['nullable', 'string', 'max:255'],
        ]);

        $leaveRequest->update([
            'status' => $validated['status'],
            'reviewed_by' => $teacher->id,
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        // If approved, automatically create attendance records for the leave period
        if ($validated['status'] === 'approved') {
            $period = CarbonPeriod::create($leaveRequest->start_date, $leaveRequest->end_date);
            $attendanceStatus = $leaveRequest->reason_type->value === 'sakit' ? 'sakit' : 'izin';

            foreach ($period as $date) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $leaveRequest->student_id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'classroom_id' => $leaveRequest->student->classroom_id,
                        'recorded_by' => $teacher->id,
                        'status' => $attendanceStatus,
                        'notes' => 'Disetujui dari pengajuan surat izin orang tua',
                    ]
                );
            }
        }

        $label = $validated['status'] === 'approved' ? 'disetujui' : 'ditolak';

        $waService = app(WhatsAppNotificationService::class);
        $leaveRequest->load(['student.classroom', 'parent']);
        $waRes = $waService->sendLeaveAlert($leaveRequest, $teacher->id);

        $redirect = back()->with('success', "Pengajuan izin siswa {$leaveRequest->student->full_name} berhasil {$label}.");

        if ($waRes) {
            $redirect->with('wa_direct_url', $waRes['wa_url'])
                     ->with('wa_student_name', $leaveRequest->student->full_name)
                     ->with('wa_auto_sent', $waRes['status'] === 'sent');
        }

        return $redirect;
    }
}
