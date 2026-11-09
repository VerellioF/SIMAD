<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Services\PaymentGatewayService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $type = $request->query('type');
        $search = $request->query('search');

        $query = PaymentTransaction::with(['payable', 'user'])->latest();

        if ($status && in_array($status, ['pending', 'paid', 'expired', 'failed'], true)) {
            $query->where('status', $status);
        }

        if ($type === 'spp') {
            $query->where('payable_type', \App\Models\TuitionBill::class);
        } elseif ($type === 'ppdb') {
            $query->where('payable_type', \App\Models\PpdbRegistration::class);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('payer_name', 'like', "%{$search}%")
                    ->orWhere('payer_phone', 'like', "%{$search}%");
            });
        }

        $transactions = $query->paginate(15)->withQueryString();

        $stats = [
            'total_count' => PaymentTransaction::count(),
            'paid_count' => PaymentTransaction::where('status', 'paid')->count(),
            'pending_count' => PaymentTransaction::where('status', 'pending')->count(),
            'paid_volume' => PaymentTransaction::where('status', 'paid')->sum('amount'),
            'fee_volume' => PaymentTransaction::where('status', 'paid')->sum('fee_amount'),
        ];

        $currentProvider = $this->paymentService->getProvider();
        $isSandbox = $this->paymentService->isSandbox();

        return view('admin.payment.index', compact('transactions', 'stats', 'status', 'type', 'search', 'currentProvider', 'isSandbox'));
    }

    public function simulate(PaymentTransaction $transaction): RedirectResponse
    {
        abort_unless($this->paymentService->isSandbox(), 403, 'Simulator hanya tersedia di mode sandbox.');

        $this->paymentService->simulateSuccess($transaction);

        return back()->with('success', "Transaksi {$transaction->reference} berhasil disimulasikan lunas & notifikasi WhatsApp otomatis dikirim.");
    }
}
