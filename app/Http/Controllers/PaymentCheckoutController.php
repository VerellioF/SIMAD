<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\PpdbRegistration;
use App\Models\TuitionBill;
use App\Services\PaymentGatewayService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentCheckoutController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentService
    ) {}

    /**
     * Initiate online payment for a Tuition Bill.
     */
    public function payTuition(TuitionBill $bill): RedirectResponse
    {
        $user = Auth::user();

        // Ensure bill belongs to parent's student or admin
        if ($user && ! $user->isAdmin()) {
            abort_unless($bill->student->parent_id === $user->id, 403);
        }

        if ($bill->status->value === 'paid') {
            return redirect()->route('parent.tuition.receipt', $bill)
                ->with('info', 'Tagihan ini sudah lunas.');
        }

        $transaction = $this->paymentService->createForTuitionBill($bill, $user);

        return redirect()->route('payment.checkout', $transaction->reference);
    }

    /**
     * Initiate online payment for PPDB Registration Fee.
     */
    public function payPpdb(string $registration_number): RedirectResponse
    {
        $registration = PpdbRegistration::where('registration_number', $registration_number)->firstOrFail();

        if ($registration->isPaid()) {
            return redirect()->route('ppdb.check-status', ['q' => $registration->registration_number])
                ->with('info', 'Biaya pendaftaran untuk calon siswa ini sudah lunas.');
        }

        $transaction = $this->paymentService->createForPpdbRegistration($registration);

        return redirect()->route('payment.checkout', $transaction->reference);
    }

    /**
     * Display the payment checkout page (QRIS & Instructions).
     */
    public function show(string $reference): View|RedirectResponse
    {
        $transaction = PaymentTransaction::where('reference', $reference)
            ->with(['payable', 'user'])
            ->firstOrFail();

        $isSandbox = $this->paymentService->isSandbox();

        return view('payment.checkout', compact('transaction', 'isSandbox'));
    }

    /**
     * Poll real-time status of transaction via JSON.
     */
    public function status(string $reference): JsonResponse
    {
        $transaction = PaymentTransaction::where('reference', $reference)->firstOrFail();

        $redirectUrl = null;
        if ($transaction->isPaid()) {
            if ($transaction->payable instanceof TuitionBill) {
                $redirectUrl = route('parent.tuition.receipt', $transaction->payable_id);
            } elseif ($transaction->payable instanceof PpdbRegistration) {
                $redirectUrl = route('ppdb.check-status', ['q' => $transaction->payable->registration_number]);
            }
        }

        return response()->json([
            'status' => $transaction->status,
            'is_paid' => $transaction->isPaid(),
            'paid_at' => $transaction->paid_at?->format('d/m/Y H:i:s'),
            'redirect_url' => $redirectUrl,
        ]);
    }

    /**
     * Sandbox simulation endpoint to simulate instant payment success.
     */
    public function simulate(Request $request, string $reference): RedirectResponse|JsonResponse
    {
        abort_unless($this->paymentService->isSandbox(), 403, 'Simulator hanya tersedia di mode sandbox / pengujian.');

        $transaction = PaymentTransaction::where('reference', $reference)->firstOrFail();

        $this->paymentService->simulateSuccess($transaction);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil disimulasikan.',
                'status' => 'paid',
            ]);
        }

        return redirect()->route('payment.checkout', $transaction->reference)
            ->with('success', 'Alhamdulillah! Pembayaran berhasil disimulasikan dan diverifikasi otomatis.');
    }
}
