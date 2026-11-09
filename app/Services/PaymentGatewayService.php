<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\PpdbRegistration;
use App\Models\TuitionBill;
use App\Models\TuitionPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    public function __construct(
        protected WhatsAppNotificationService $waService
    ) {}

    /**
     * Get active provider from configuration ('simulator', 'tripay', 'midtrans').
     */
    public function getProvider(): string
    {
        return (string) config('simad.payment.provider', 'simulator');
    }

    /**
     * Determine if sandbox / simulator is active.
     */
    public function isSandbox(): bool
    {
        return ! (bool) config('simad.payment.is_production', false);
    }

    /**
     * Calculate admin fee (if not absorbed by school).
     */
    public function calculateFee(float $amount, string $method = 'qris'): float
    {
        $absorb = (bool) config('simad.payment.absorb_fee', true);

        if ($absorb) {
            return 0.00;
        }

        // Standard 0.7% QRIS Bank Indonesia fee rounded up
        if ($method === 'qris') {
            $feePercent = (float) config('simad.payment.qris_fee_percent', 0.7);
            return (float) ceil($amount * ($feePercent / 100));
        }

        // Standard VA fee
        return 2500.00;
    }

    /**
     * Create or retrieve an existing pending transaction for a Tuition Bill.
     */
    public function createForTuitionBill(TuitionBill $bill, ?User $user = null, string $paymentMethod = 'qris'): PaymentTransaction
    {
        // Check for existing pending transaction within 24 hours
        $existing = PaymentTransaction::where('payable_type', TuitionBill::class)
            ->where('payable_id', $bill->id)
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if ($existing) {
            return $existing;
        }

        $amount = (float) $bill->amount;
        $fee = $this->calculateFee($amount, $paymentMethod);
        $total = $amount + $fee;

        $student = $bill->student;
        $parent = $user ?? $student->parent;

        $payerName = $parent?->name ?? $student->parent_name ?? 'Wali Murid ' . $student->full_name;
        $payerPhone = $parent?->phone_number ?? $student->parent_phone ?? '081234567890';
        $payerEmail = $parent?->email ?? null;

        $reference = 'SPP-' . $bill->id . '-' . strtoupper(Str::random(6));

        $provider = $this->getProvider();
        $gatewayResult = $this->requestGatewayPayment([
            'reference' => $reference,
            'amount' => (int) $amount,
            'total' => (int) $total,
            'method' => $paymentMethod,
            'title' => 'SPP: ' . $bill->bill_title . ' - ' . $student->full_name,
            'payer_name' => $payerName,
            'payer_phone' => $payerPhone,
            'payer_email' => $payerEmail,
        ]);

        return PaymentTransaction::create([
            'reference' => $reference,
            'payable_type' => TuitionBill::class,
            'payable_id' => $bill->id,
            'user_id' => $parent?->id,
            'payer_name' => $payerName,
            'payer_email' => $payerEmail,
            'payer_phone' => $payerPhone,
            'amount' => $amount,
            'fee_amount' => $fee,
            'total_amount' => $total,
            'payment_method' => $paymentMethod,
            'payment_provider' => $provider,
            'provider_reference' => $gatewayResult['provider_reference'] ?? null,
            'qr_string' => $gatewayResult['qr_string'] ?? null,
            'qr_url' => $gatewayResult['qr_url'] ?? null,
            'checkout_url' => $gatewayResult['checkout_url'] ?? null,
            'status' => 'pending',
            'expired_at' => now()->addHours(24),
            'raw_payload' => $gatewayResult['raw_response'] ?? null,
        ]);
    }

    /**
     * Create or retrieve an existing pending transaction for PPDB Registration fee.
     */
    public function createForPpdbRegistration(PpdbRegistration $registration, string $paymentMethod = 'qris'): PaymentTransaction
    {
        $existing = PaymentTransaction::where('payable_type', PpdbRegistration::class)
            ->where('payable_id', $registration->id)
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if ($existing) {
            return $existing;
        }

        $amount = (float) $registration->registration_fee;
        $fee = $this->calculateFee($amount, $paymentMethod);
        $total = $amount + $fee;

        $reference = 'PPDB-' . $registration->registration_number . '-' . strtoupper(Str::random(4));

        $provider = $this->getProvider();
        $gatewayResult = $this->requestGatewayPayment([
            'reference' => $reference,
            'amount' => (int) $amount,
            'total' => (int) $total,
            'method' => $paymentMethod,
            'title' => 'PPDB MI Plus - ' . $registration->child_name . ' (' . $registration->registration_number . ')',
            'payer_name' => $registration->parent_name,
            'payer_phone' => $registration->parent_whatsapp,
            'payer_email' => null,
        ]);

        return PaymentTransaction::create([
            'reference' => $reference,
            'payable_type' => PpdbRegistration::class,
            'payable_id' => $registration->id,
            'user_id' => null,
            'payer_name' => $registration->parent_name,
            'payer_email' => null,
            'payer_phone' => $registration->parent_whatsapp,
            'amount' => $amount,
            'fee_amount' => $fee,
            'total_amount' => $total,
            'payment_method' => $paymentMethod,
            'payment_provider' => $provider,
            'provider_reference' => $gatewayResult['provider_reference'] ?? null,
            'qr_string' => $gatewayResult['qr_string'] ?? null,
            'qr_url' => $gatewayResult['qr_url'] ?? null,
            'checkout_url' => $gatewayResult['checkout_url'] ?? null,
            'status' => 'pending',
            'expired_at' => now()->addHours(24),
            'raw_payload' => $gatewayResult['raw_response'] ?? null,
        ]);
    }

    /**
     * Dispatch request to configured payment gateway provider.
     */
    protected function requestGatewayPayment(array $data): array
    {
        $provider = $this->getProvider();

        if ($provider === 'tripay') {
            return $this->requestTripayPayment($data);
        }

        if ($provider === 'midtrans') {
            return $this->requestMidtransPayment($data);
        }

        // Default: Simulator / Sandbox Provider
        return $this->requestSimulatorPayment($data);
    }

    /**
     * Built-in Simulator provider for instant testing and sandbox demos.
     */
    protected function requestSimulatorPayment(array $data): array
    {
        $ref = $data['reference'];
        $amount = $data['total'];
        $qrPayload = '00020101021226680016ID.CO.MIPLUS.WWW011893600999' . $ref . '520458125303360540' . $amount . '5802ID5917MI PLUS BLITAR6006BLITAR6304';
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . rawurlencode($qrPayload);

        return [
            'provider_reference' => 'SIM-' . $ref,
            'qr_string' => $qrPayload,
            'qr_url' => $qrUrl,
            'checkout_url' => url('/payment/checkout/' . $ref),
            'raw_response' => [
                'provider' => 'simulator',
                'mode' => 'sandbox_simulator',
                'created_at' => now()->toIso8601String(),
            ],
        ];
    }

    /**
     * Tripay payment gateway integration.
     */
    protected function requestTripayPayment(array $data): array
    {
        $apiKey = config('simad.payment.tripay.api_key');
        $privateKey = config('simad.payment.tripay.private_key');
        $merchantCode = config('simad.payment.tripay.merchant_code');
        $apiUrl = rtrim((string) config('simad.payment.tripay.api_url', 'https://tripay.co.id/api-sandbox/'), '/') . '/';

        if (empty($apiKey) || empty($privateKey) || empty($merchantCode)) {
            Log::warning('Tripay credentials missing, falling back to simulator mode.');
            return $this->requestSimulatorPayment($data);
        }

        $signature = hash_hmac('sha256', $merchantCode . $data['reference'] . $data['total'], $privateKey);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post($apiUrl . 'transaction/create', [
                'method' => strtoupper($data['method']),
                'merchant_ref' => $data['reference'],
                'amount' => (int) $data['total'],
                'customer_name' => $data['payer_name'],
                'customer_email' => $data['payer_email'] ?? 'wali@miplus.sch.id',
                'customer_phone' => $data['payer_phone'],
                'order_items' => [
                    [
                        'sku' => $data['reference'],
                        'name' => $data['title'],
                        'price' => (int) $data['total'],
                        'quantity' => 1,
                    ],
                ],
                'callback_url' => route('payment.webhook'),
                'return_url' => route('payment.checkout', $data['reference']),
                'expired_time' => time() + (24 * 3600),
                'signature' => $signature,
            ]);

            if ($response->successful() && $response->json('success')) {
                $payload = $response->json('data');
                return [
                    'provider_reference' => $payload['reference'] ?? null,
                    'qr_string' => $payload['qr_string'] ?? null,
                    'qr_url' => $payload['qr_url'] ?? null,
                    'checkout_url' => $payload['checkout_url'] ?? null,
                    'raw_response' => $payload,
                ];
            }

            Log::error('Tripay transaction creation failed', ['response' => $response->body()]);
        } catch (\Throwable $e) {
            Log::error('Tripay request exception: ' . $e->getMessage());
        }

        return $this->requestSimulatorPayment($data);
    }

    /**
     * Midtrans payment gateway integration.
     */
    protected function requestMidtransPayment(array $data): array
    {
        $serverKey = config('simad.payment.midtrans.server_key');
        $isProduction = (bool) config('simad.payment.midtrans.is_production', false);

        if (empty($serverKey)) {
            Log::warning('Midtrans server key missing, falling back to simulator mode.');
            return $this->requestSimulatorPayment($data);
        }

        $baseUrl = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($baseUrl, [
                    'transaction_details' => [
                        'order_id' => $data['reference'],
                        'gross_amount' => (int) $data['total'],
                    ],
                    'customer_details' => [
                        'first_name' => $data['payer_name'],
                        'phone' => $data['payer_phone'],
                        'email' => $data['payer_email'] ?? 'wali@miplus.sch.id',
                    ],
                    'item_details' => [
                        [
                            'id' => $data['reference'],
                            'price' => (int) $data['total'],
                            'quantity' => 1,
                            'name' => Str::limit($data['title'], 45),
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $payload = $response->json();
                return [
                    'provider_reference' => $payload['token'] ?? null,
                    'qr_string' => null,
                    'qr_url' => null,
                    'checkout_url' => $payload['redirect_url'] ?? null,
                    'raw_response' => $payload,
                ];
            }

            Log::error('Midtrans Snap creation failed', ['response' => $response->body()]);
        } catch (\Throwable $e) {
            Log::error('Midtrans request exception: ' . $e->getMessage());
        }

        return $this->requestSimulatorPayment($data);
    }

    /**
     * Mark a transaction as paid, update the related bill/registration, and trigger WhatsApp alert.
     */
    public function markTransactionAsPaid(PaymentTransaction $transaction, array $extraData = []): bool
    {
        if ($transaction->status === 'paid') {
            return true;
        }

        $transaction->update([
            'status' => 'paid',
            'paid_at' => now(),
            'raw_payload' => array_merge((array) $transaction->raw_payload, $extraData),
        ]);

        $payable = $transaction->payable;

        if ($payable instanceof TuitionBill) {
            $payable->update(['status' => 'paid']);

            $receiptNum = 'KWT-PG-' . date('Ym') . '-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT);

            $payment = TuitionPayment::updateOrCreate(
                ['tuition_bill_id' => $payable->id],
                [
                    'verified_by' => null, // verified automatically via gateway
                    'receipt_number' => $receiptNum,
                    'amount_paid' => $transaction->amount,
                    'payment_method' => str_contains($transaction->payment_method, 'va') ? 'virtual_account' : 'qris',
                    'bank_sender_name' => $transaction->payer_name . ' (Payment Gateway QRIS)',
                    'proof_attachment' => null,
                    'payment_date' => now()->toDateString(),
                    'verified_at' => now(),
                ]
            );

            // Send automated WhatsApp receipt to parent
            try {
                $this->waService->sendTuitionPaymentAlert($payment, $payable, $transaction->payer_phone);
            } catch (\Throwable $e) {
                Log::error('Failed sending WA payment notification: ' . $e->getMessage());
            }
        } elseif ($payable instanceof PpdbRegistration) {
            $payable->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'payment_method' => 'transfer',
            ]);

            // Send automated WhatsApp receipt to parent
            try {
                $this->waService->sendPpdbPaymentAlert($payable);
            } catch (\Throwable $e) {
                Log::error('Failed sending WA PPDB payment notification: ' . $e->getMessage());
            }
        }

        return true;
    }

    /**
     * Simulate successful payment in sandbox / simulator mode.
     */
    public function simulateSuccess(PaymentTransaction $transaction): bool
    {
        return $this->markTransactionAsPaid($transaction, [
            'simulated_at' => now()->toIso8601String(),
            'simulated_by' => 'sandbox_tester',
        ]);
    }

    /**
     * Verify incoming webhook authenticity based on provider.
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $provider = $this->getProvider();

        if ($provider === 'simulator') {
            return true;
        }

        if ($provider === 'tripay') {
            $privateKey = (string) config('simad.payment.tripay.private_key');
            $callbackSignature = $request->header('X-Callback-Signature');
            $rawContent = $request->getContent();

            if (! $callbackSignature || empty($privateKey)) {
                return false;
            }

            return hash_equals(hash_hmac('sha256', $rawContent, $privateKey), $callbackSignature);
        }

        if ($provider === 'midtrans') {
            $serverKey = (string) config('simad.payment.midtrans.server_key');
            $orderId = (string) $request->input('order_id');
            $statusCode = (string) $request->input('status_code');
            $grossAmount = (string) $request->input('gross_amount');
            $signatureKey = (string) $request->input('signature_key');

            if (! $signatureKey || empty($serverKey)) {
                return false;
            }

            $computed = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            return hash_equals($computed, $signatureKey);
        }

        return true;
    }

    /**
     * Process incoming webhook callback.
     */
    public function handleWebhook(Request $request): array
    {
        if (! $this->verifyWebhookSignature($request)) {
            Log::warning('Payment gateway webhook signature mismatch', ['ip' => $request->ip()]);
            return ['success' => false, 'message' => 'Invalid signature', 'code' => 403];
        }

        $reference = $request->input('merchant_ref')
            ?? $request->input('order_id')
            ?? $request->input('reference');

        if (! $reference) {
            return ['success' => false, 'message' => 'Missing reference', 'code' => 400];
        }

        $transaction = PaymentTransaction::where('reference', $reference)->first();

        if (! $transaction) {
            return ['success' => false, 'message' => 'Transaction not found', 'code' => 404];
        }

        // Determine status from payload
        $status = strtolower((string) (
            $request->input('status')
            ?? $request->input('transaction_status')
            ?? ''
        ));

        $isPaid = in_array($status, ['paid', 'settlement', 'capture', 'success'], true);

        if ($isPaid) {
            $this->markTransactionAsPaid($transaction, $request->all());
            return ['success' => true, 'message' => 'Payment marked as paid', 'code' => 200];
        }

        if (in_array($status, ['expired', 'expire'], true)) {
            $transaction->update(['status' => 'expired']);
            return ['success' => true, 'message' => 'Transaction marked as expired', 'code' => 200];
        }

        if (in_array($status, ['failed', 'cancel', 'deny'], true)) {
            $transaction->update(['status' => 'failed']);
            return ['success' => true, 'message' => 'Transaction marked as failed', 'code' => 200];
        }

        return ['success' => true, 'message' => 'Webhook received', 'code' => 200];
    }
}
