<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\WhatsAppLog;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminWhatsAppController extends Controller
{
    public function index(Request $request): View
    {
        $query = WhatsAppLog::with(['student.classroom', 'sender'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $logs = $query->paginate(20)->withQueryString();
        $classrooms = Classroom::orderBy('grade_level')->orderBy('name')->get();
        $students = Student::with('parent', 'classroom')->where('status', 'active')->orderBy('full_name')->get();

        $gatewayConfig = [
            'enabled' => config('simad.whatsapp.enabled', true),
            'auto_send' => config('simad.whatsapp.auto_send', false),
            'provider' => config('simad.whatsapp.provider', 'fonnte'),
            'has_token' => ! empty(config('simad.whatsapp.token')),
            'endpoint' => config('simad.whatsapp.endpoint', 'https://api.fonnte.com/send'),
        ];

        return view('admin.whatsapp.index', compact('logs', 'classrooms', 'students', 'gatewayConfig'));
    }

    public function sendCustomMessage(Request $request, WhatsAppNotificationService $waService): RedirectResponse
    {
        $validated = $request->validate([
            'target_type' => ['required', 'in:single,broadcast'],
            'student_id' => ['nullable', 'required_if:target_type,single', 'exists:students,id'],
            'classroom_id' => ['nullable', 'required_if:target_type,broadcast', 'exists:classrooms,id'],
            'custom_phone' => ['nullable', 'string', 'max:20'],
            'message' => ['required', 'string', 'max:1500'],
        ]);

        $admin = $request->user();

        if ($validated['target_type'] === 'single') {
            $student = $validated['student_id'] ? Student::with('parent')->find($validated['student_id']) : null;
            $phone = ($validated['custom_phone'] ?? null) ?: $student?->parent?->phone_number;

            if (! $phone) {
                return back()->with('error', 'Nomor telepon tujuan tidak ditemukan.');
            }

            $res = $waService->sendMessage(
                $phone,
                $validated['message'],
                $student,
                'general',
                $admin->id,
                $student?->parent?->name
            );

            if ($res['status'] === 'sent') {
                return back()->with('success', "Pesan WhatsApp berhasil dikirim otomatis via gateway ke {$phone}.");
            }

            return back()->with('success', "Pesan siap dikirim. Klik tautan WhatsApp berikut untuk mengirim langsung:")
                ->with('wa_direct_url', $res['wa_url']);
        }

        // Broadcast to classroom
        $students = Student::with('parent')
            ->where('classroom_id', $validated['classroom_id'])
            ->where('status', 'active')
            ->get();

        $sentCount = 0;
        $queuedCount = 0;

        foreach ($students as $st) {
            $phone = $st->parent?->phone_number;
            if ($phone) {
                $personalized = str_replace(
                    ['{nama_siswa}', '{nama_wali}'],
                    [$st->full_name, $st->parent?->name ?? 'Bapak/Ibu'],
                    $validated['message']
                );

                $res = $waService->sendMessage($phone, $personalized, $st, 'general', $admin->id);
                if ($res['status'] === 'sent') {
                    $sentCount++;
                } else {
                    $queuedCount++;
                }
            }
        }

        return back()->with('success', "Siaran pesan diproses untuk rombel: {$sentCount} terkirim otomatis, {$queuedCount} tercatat dalam riwayat pesan.");
    }

    public function testGateway(Request $request, WhatsAppNotificationService $waService): RedirectResponse
    {
        $validated = $request->validate([
            'test_phone' => ['required', 'string', 'max:20'],
        ]);

        $msg = "🔔 *Uji Coba WhatsApp Gateway*\n\n"
             . "Assalamu'alaikum Wr. Wb.\n"
             . "Ini adalah pesan verifikasi koneksi dari sistem informasi *MI Plus Raudlatul Muta'allimin*.\n\n"
             . "Waktu kirim: " . date('d/m/Y H:i:s') . " WIB\n"
             . "Status integrasi: *Aktif & Berhasil terhubung*.\n\n"
             . "Wassalamu'alaikum Wr. Wb.";

        $res = $waService->sendMessage(
            $validated['test_phone'],
            $msg,
            null,
            'test',
            $request->user()->id,
            'Penguji Sistem'
        );

        if ($res['status'] === 'sent') {
            return back()->with('success', "Uji coba berhasil! Pesan WhatsApp telah terkirim ke {$validated['test_phone']}.");
        }

        return back()->with('success', "Gateway belum diaktifkan (WA_AUTO_SEND=false). Anda dapat menguji melalui tautan berikut:")
            ->with('wa_direct_url', $res['wa_url']);
    }
}
