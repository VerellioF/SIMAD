<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PpdbStatus;
use App\Http\Controllers\Controller;
use App\Models\PpdbRegistration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPpdbController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = PpdbRegistration::query()->latest();

        if ($status && in_array($status, array_column(PpdbStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                    ->orWhere('child_name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('parent_name', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => PpdbRegistration::count(),
            'pending' => PpdbRegistration::where('status', PpdbStatus::Pending)->count(),
            'verified' => PpdbRegistration::where('status', PpdbStatus::Verified)->count(),
            'accepted' => PpdbRegistration::where('status', PpdbStatus::Accepted)->count(),
            'rejected' => PpdbRegistration::where('status', PpdbStatus::Rejected)->count(),
        ];

        return view('admin.ppdb.index', compact('registrations', 'counts', 'status', 'search'));
    }

    public function show(PpdbRegistration $registration): View
    {
        return view('admin.ppdb.show', compact('registration'));
    }

    public function updateStatus(Request $request, PpdbRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(PpdbStatus::class)],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $registration->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return back()->with('success', "Status pendaftaran {$registration->registration_number} berhasil diperbarui.");
    }
}
