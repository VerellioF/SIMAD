<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePpdbRegistrationRequest;
use App\Models\PpdbRegistration;
use App\Services\PpdbPdfService;
use App\Services\PpdbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PpdbController extends Controller
{
    public function __construct(
        protected PpdbService $ppdbService
    ) {}

    public function create(): View
    {
        return view('public.ppdb.register');
    }

    public function store(StorePpdbRegistrationRequest $request): RedirectResponse
    {
        $registration = $this->ppdbService->register($request->validated());

        if ($request->input('payment_method') === 'qris') {
            return redirect()->route('ppdb.pay', ['registration_number' => $registration->registration_number])
                ->with('status', 'Pendaftaran berhasil dikirim! Silakan selesaikan pembayaran biaya formulir melalui QRIS di bawah ini.');
        }

        return redirect()->route('ppdb.success', ['registration_number' => $registration->registration_number])
            ->with('status', 'Pendaftaran berhasil dikirim!');
    }

    public function success(string $registration_number): View
    {
        $registration = PpdbRegistration::where('registration_number', $registration_number)->firstOrFail();

        return view('public.ppdb.success', compact('registration'));
    }

    public function downloadProof(string $registration_number, PpdbPdfService $pdfService): Response
    {
        $registration = PpdbRegistration::where('registration_number', $registration_number)->firstOrFail();
        $pdf = $pdfService->generateProofPdf($registration);

        return $pdf->stream("Bukti_PPDB_{$registration->registration_number}.pdf");
    }

    public function checkStatus(Request $request): View
    {
        $query = trim((string) $request->input('q', ''));
        $result = null;
        $searched = false;

        if ($query !== '') {
            $searched = true;
            $result = PpdbRegistration::query()
                ->where('registration_number', $query)
                ->orWhere('nik', $query)
                ->first();
        }

        return view('public.ppdb.check-status', compact('query', 'result', 'searched'));
    }
}
