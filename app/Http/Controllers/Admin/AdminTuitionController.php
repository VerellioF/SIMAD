<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\TuitionBill;
use App\Models\TuitionPayment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTuitionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $query = TuitionBill::with(['student.classroom', 'payment'])->latest();

        if ($status && in_array($status, ['unpaid', 'pending_verification', 'paid'], true)) {
            $query->where('status', $status);
        }

        $bills = $query->paginate(15)->withQueryString();

        $stats = [
            'total_bills' => TuitionBill::count(),
            'unpaid_count' => TuitionBill::where('status', 'unpaid')->count(),
            'pending_count' => TuitionBill::where('status', 'pending_verification')->count(),
            'paid_count' => TuitionBill::where('status', 'paid')->count(),
            'unpaid_sum' => TuitionBill::where('status', 'unpaid')->sum('amount'),
            'paid_sum' => TuitionBill::where('status', 'paid')->sum('amount'),
        ];

        return view('admin.tuition.index', compact('bills', 'stats', 'status'));
    }

    public function generateMonthlyBills(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2025', 'max:2030'],
            'amount' => ['required', 'numeric', 'min:10000'],
            'bill_title' => ['required', 'string', 'max:150'],
            'due_date' => ['required', 'date'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();
        $students = Student::where('status', 'active')->get();

        $count = 0;
        foreach ($students as $student) {
            $exists = TuitionBill::where('student_id', $student->id)
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->exists();

            if (! $exists) {
                TuitionBill::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $activeYear->id,
                    'bill_title' => $validated['bill_title'],
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'amount' => $validated['amount'],
                    'status' => 'unpaid',
                    'due_date' => $validated['due_date'],
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.tuition.index')
            ->with('success', "Berhasil menerbitkan {$count} tagihan SPP untuk siswa aktif.");
    }

    public function verifyPayment(Request $request, TuitionBill $bill): RedirectResponse
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
        ]);

        if ($validated['action'] === 'approve') {
            $receiptNum = 'KWT-' . date('Ym') . '-' . str_pad((string) $bill->id, 4, '0', STR_PAD_LEFT);

            $bill->update(['status' => 'paid']);

            TuitionPayment::updateOrCreate(
                ['tuition_bill_id' => $bill->id],
                [
                    'verified_by' => $admin->id,
                    'receipt_number' => $receiptNum,
                    'amount_paid' => $bill->amount,
                    'payment_method' => 'transfer_bank',
                    'payment_date' => now()->toDateString(),
                    'verified_at' => now(),
                ]
            );

            return back()->with('success', "Pembayaran tagihan {$bill->bill_title} disetujui. Kwitansi: {$receiptNum}.");
        } else {
            $bill->update(['status' => 'unpaid']);

            return back()->with('success', "Bukti pembayaran tagihan {$bill->bill_title} ditolak.");
        }
    }

    public function recordCashPayment(Request $request, TuitionBill $bill): RedirectResponse
    {
        $admin = Auth::user();
        $receiptNum = 'KWT-CASH-' . date('Ym') . '-' . str_pad((string) $bill->id, 4, '0', STR_PAD_LEFT);

        $bill->update(['status' => 'paid']);

        TuitionPayment::create([
            'tuition_bill_id' => $bill->id,
            'verified_by' => $admin->id,
            'receipt_number' => $receiptNum,
            'amount_paid' => $bill->amount,
            'payment_method' => 'tunai_madrasah',
            'payment_date' => now()->toDateString(),
            'verified_at' => now(),
        ]);

        return back()->with('success', "Pembayaran tunai sebesar Rp " . number_format((float) $bill->amount, 0, ',', '.') . " berhasil dicatat.");
    }
}
