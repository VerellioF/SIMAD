<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePpdbRegistrationRequest;
use App\Models\PpdbRegistration;
use App\Services\PpdbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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

        return redirect()->route('ppdb.success', ['registration_number' => $registration->registration_number])
            ->with('status', 'Pendaftaran berhasil dikirim!');
    }

    public function success(string $registration_number): View
    {
        $registration = PpdbRegistration::where('registration_number', $registration_number)->firstOrFail();

        return view('public.ppdb.success', compact('registration'));
    }
}
