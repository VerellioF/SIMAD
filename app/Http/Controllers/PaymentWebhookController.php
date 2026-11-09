<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentService
    ) {}

    /**
     * Handle incoming gateway webhook notifications.
     */
    public function handle(Request $request): JsonResponse
    {
        $result = $this->paymentService->handleWebhook($request);

        return response()->json([
            'success' => $result['success'] ?? false,
            'message' => $result['message'] ?? 'Webhook processed',
        ], $result['code'] ?? 200);
    }
}
