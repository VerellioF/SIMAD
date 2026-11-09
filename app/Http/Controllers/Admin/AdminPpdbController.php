<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PpdbStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\PpdbRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $classrooms = Classroom::orderBy('name')->get();
        $isAlreadyEnrolled = Student::where('nisn', $registration->nik)->exists();

        return view('admin.ppdb.show', compact('registration', 'classrooms', 'isAlreadyEnrolled'));
    }

    public function enroll(Request $request, PpdbRegistration $registration): RedirectResponse
    {
        abort_unless($registration->status === PpdbStatus::Accepted, 400, 'Hanya calon siswa berstatus Diterima yang dapat didaftarkan.');

        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'nis' => ['nullable', 'string', 'max:30', 'unique:students,nis'],
            'nisn' => ['nullable', 'string', 'max:30', 'unique:students,nisn'],
        ]);

        // 1. Ensure Parent user exists or create one
        $parent = User::firstOrCreate(
            ['phone_number' => $registration->parent_whatsapp],
            [
                'name' => $registration->parent_name,
                'email' => 'wali.' . preg_replace('/[^0-9]/', '', $registration->parent_whatsapp) . '@miplus.sch.id',
                'password' => Hash::make('password123'),
                'role' => UserRole::Parent,
                'is_active' => true,
            ]
        );

        // 2. Generate NIS if not given
        $year = date('y');
        $nis = $validated['nis'] ?? ($year . date('m') . str_pad((string) $registration->id, 4, '0', STR_PAD_LEFT));
        $nisn = $validated['nisn'] ?? $registration->nik;

        // 3. Create or update Student
        $student = Student::firstOrCreate(
            ['nisn' => $nisn],
            [
                'full_name' => $registration->child_name,
                'gender' => $registration->gender,
                'nis' => $nis,
                'birth_place' => $registration->birth_place,
                'birth_date' => $registration->birth_date,
                'classroom_id' => $validated['classroom_id'],
                'parent_id' => $parent->id,
                'address' => $registration->address,
                'photo_path' => $registration->file_photo,
                'status' => 'active',
            ]
        );

        return redirect()->route('admin.students.index', ['classroom_id' => $student->classroom_id])
            ->with('success', "Alhamdulillah! Calon santri {$student->full_name} berhasil didaftarkan aktif di kelas. Akun portal wali murid telah dibuat.");
    }

    public function updateStatus(Request $request, PpdbRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(PpdbStatus::class)],
            'payment_status' => ['nullable', 'in:unpaid,paid'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
        ];

        if (isset($validated['payment_status'])) {
            $updateData['payment_status'] = $validated['payment_status'];
            if ($validated['payment_status'] === 'paid' && ! $registration->paid_at) {
                $updateData['paid_at'] = now();
            } elseif ($validated['payment_status'] === 'unpaid') {
                $updateData['paid_at'] = null;
            }
        }

        $registration->update($updateData);

        return back()->with('success', "Status pendaftaran & pembayaran {$registration->registration_number} berhasil diperbarui.");
    }
}
