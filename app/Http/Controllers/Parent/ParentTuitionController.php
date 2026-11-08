<?php

declare(strict_types=1);

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\TuitionBill;
use App\Models\TuitionPayment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentTuitionController extends Controller
{
    public function index(): View
    {
        $parent = Auth::user();
        $students = Student::where('parent_id', $parent->id)->get();
        $studentIds = $students->pluck('id');

        $bills = TuitionBill::whereIn('student_id', $studentIds)
            ->with(['student', 'payment'])
            ->latest('due_date')
            ->paginate(15);

        $unpaidTotal = TuitionBill::whereIn('student_id', $studentIds)
            ->where('status', 'unpaid')
            ->sum('amount');

        return view('parent.tuition.index', compact('students', 'bills', 'unpaidTotal'));
    }

    public function confirmPayment(Request $request, TuitionBill $bill): RedirectResponse
    {
        $parent = Auth::user();

        // Ensure bill belongs to parent's student
        abort_unless($bill->student->parent_id === $parent->id, 403);

        $validated = $request->validate([
            'bank_sender_name' => ['required', 'string', 'max:100'],
            'payment_date' => ['required', 'date'],
            'proof_attachment' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $proofPath = $request->file('proof_attachment')->store('spp_bukti', 'public');

        $bill->update(['status' => 'pending_verification']);

        TuitionPayment::updateOrCreate(
            ['tuition_bill_id' => $bill->id],
            [
                'receipt_number' => 'PENDING-' . $bill->id,
                'amount_paid' => $bill->amount,
                'payment_method' => 'transfer_bank',
                'bank_sender_name' => $validated['bank_sender_name'],
                'proof_attachment' => $proofPath,
                'payment_date' => $validated['payment_date'],
            ]
        );

        return redirect()->route('parent.tuition.index')
            ->with('success', 'Konfirmasi pembayaran SPP berhasil dikirim. Staf TU akan segera memverifikasi.');
    }

    public function receipt(TuitionBill $bill): View
    {
        $parent = Auth::user();

        // Ensure bill belongs to parent's student or admin
        abort_unless($bill->student->parent_id === $parent->id || $parent->isAdmin(), 403);
        abort_unless($bill->status->value === 'paid' && $bill->payment, 404);

        return view('parent.tuition.receipt', compact('bill'));
    }
}
