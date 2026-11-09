<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\Student;
use App\Models\StudentConductRecord;
use App\Models\WhatsAppLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Format any Indonesian phone number to international WhatsApp format (e.g. 628123456789).
     */
    public function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (empty($clean)) {
            return null;
        }

        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        return $clean;
    }

    /**
     * Generate direct WhatsApp Click-to-Chat URL.
     */
    public function generateClickToChatUrl(?string $phone, string $message): ?string
    {
        $formatted = $this->formatPhoneNumber($phone);

        if (! $formatted) {
            return null;
        }

        return 'https://wa.me/' . $formatted . '?text=' . rawurlencode($message);
    }

    /**
     * Check if automatic API sending is configured and enabled.
     */
    public function isAutoSendEnabled(): bool
    {
        $enabled = (bool) config('simad.whatsapp.enabled', true);
        $autoSend = (bool) config('simad.whatsapp.auto_send', false);
        $token = (string) config('simad.whatsapp.token', '');

        return $enabled && $autoSend && ! empty($token);
    }

    /**
     * Send message via API if configured, otherwise create queued/manual log with click-to-chat url.
     *
     * @return array{success: bool, status: string, wa_url: ?string, phone: ?string, message: string}
     */
    public function sendMessage(
        string $phone,
        string $message,
        ?Student $student = null,
        string $type = 'general',
        ?int $senderId = null,
        ?string $recipientName = null
    ): array {
        $formattedPhone = $this->formatPhoneNumber($phone);

        if (! $formattedPhone) {
            return [
                'success' => false,
                'status' => 'invalid_phone',
                'wa_url' => null,
                'phone' => $phone,
                'message' => $message,
            ];
        }

        $waUrl = $this->generateClickToChatUrl($formattedPhone, $message);
        $name = $recipientName ?? $student?->parent?->name ?? 'Orang Tua / Wali';

        if ($this->isAutoSendEnabled()) {
            return $this->dispatchToGateway($formattedPhone, $name, $message, $student, $type, $senderId, $waUrl);
        }

        // Log as manual_ready so user can use the direct URL
        WhatsAppLog::create([
            'student_id' => $student?->id,
            'sent_by' => $senderId,
            'recipient_phone' => $formattedPhone,
            'recipient_name' => $name,
            'type' => $type,
            'status' => 'manual_click',
            'message' => $message,
            'response_payload' => json_encode(['info' => 'Siap dikirim via direct WhatsApp link (tanpa gateway berbayar).']),
        ]);

        return [
            'success' => true,
            'status' => 'manual_click',
            'wa_url' => $waUrl,
            'phone' => $formattedPhone,
            'message' => $message,
        ];
    }

    /**
     * Dispatch HTTP POST to the configured WhatsApp Gateway.
     */
    protected function dispatchToGateway(
        string $phone,
        string $name,
        string $message,
        ?Student $student,
        string $type,
        ?int $senderId,
        ?string $waUrl
    ): array {
        $provider = config('simad.whatsapp.provider', 'fonnte');
        $token = config('simad.whatsapp.token', '');
        $endpoint = config('simad.whatsapp.endpoint', 'https://api.fonnte.com/send');

        try {
            $response = match ($provider) {
                'fonnte' => Http::withHeaders([
                    'Authorization' => $token,
                ])->asForm()->post($endpoint, [
                    'target' => $phone,
                    'message' => $message,
                    'countryCode' => '62',
                ]),
                'wablas' => Http::withHeaders([
                    'Authorization' => $token,
                ])->asForm()->post($endpoint, [
                    'phone' => $phone,
                    'message' => $message,
                ]),
                default => Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ])->post($endpoint, [
                    'to' => $phone,
                    'message' => $message,
                ]),
            };

            $isSuccess = $response->successful();

            WhatsAppLog::create([
                'student_id' => $student?->id,
                'sent_by' => $senderId,
                'recipient_phone' => $phone,
                'recipient_name' => $name,
                'type' => $type,
                'status' => $isSuccess ? 'sent' : 'failed',
                'message' => $message,
                'response_payload' => $response->body(),
            ]);

            return [
                'success' => $isSuccess,
                'status' => $isSuccess ? 'sent' : 'failed',
                'wa_url' => $waUrl,
                'phone' => $phone,
                'message' => $message,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp Gateway Error: ' . $e->getMessage(), ['phone' => $phone]);

            WhatsAppLog::create([
                'student_id' => $student?->id,
                'sent_by' => $senderId,
                'recipient_phone' => $phone,
                'recipient_name' => $name,
                'type' => $type,
                'status' => 'failed',
                'message' => $message,
                'response_payload' => json_encode(['error' => $e->getMessage()]),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'wa_url' => $waUrl,
                'phone' => $phone,
                'message' => $message,
            ];
        }
    }

    /**
     * Build attendance notification message for absent (alpa), sick, or permitted student.
     */
    public function buildAttendanceAlert(
        Student $student,
        string $status,
        string $date,
        ?string $subjectName = null,
        ?string $notes = null
    ): string {
        $parentName = $student->parent?->name ?? 'Bapak/Ibu Wali Murid';
        $statusLabels = [
            'alpa' => 'Tidak Hadir / Alpa (Tanpa Keterangan)',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'hadir' => 'Hadir di Madrasah',
        ];

        $statusText = $statusLabels[strtolower($status)] ?? strtoupper($status);
        $subjectText = $subjectName ? "Mata Pelajaran: *{$subjectName}*" : "Presensi Harian / Wali Kelas";
        $formattedDate = date('d F Y', strtotime($date));

        $msg = "Assalamu'alaikum Wr. Wb.\n";
        $msg .= "Yth. {$parentName},\n\n";
        $msg .= "Kami dari *MI Plus Raudlatul Muta'allimin Kalipucung* menginformasikan rekaman kehadiran ananda:\n\n";
        $msg .= "👤 *Nama Siswa:* *{$student->full_name}*\n";
        $msg .= "🏫 *Kelas:* " . ($student->classroom?->name ?? '-') . "\n";
        $msg .= "📅 *Tanggal:* {$formattedDate}\n";
        $msg .= "📖 {$subjectText}\n";
        $msg .= "📌 *Status Kehadiran:* *{$statusText}*\n";

        if (! empty($notes)) {
            $msg .= "📝 *Catatan:* {$notes}\n";
        }

        if (strtolower($status) === 'alpa') {
            $msg .= "\n⚠️ *Penting:* Hingga saat ini madrasah belum menerima surat izin atau konfirmasi dari pihak keluarga. Mohon segera membalas pesan ini atau menghubungi wali kelas untuk konfirmasi keselamatan dan keberadaan ananda.\n";
        }

        $msg .= "\nTerima kasih atas perhatian dan kerjasamanya.\nWassalamu'alaikum Wr. Wb.\n";
        $msg .= "_Bidang Kesiswaan & Wali Kelas MI Plus Raudlatul Muta'allimin_";

        return $msg;
    }

    /**
     * Send attendance alert to student's parent.
     */
    public function sendAttendanceAlert(
        Student $student,
        string $status,
        string $date,
        ?string $subjectName = null,
        ?string $notes = null,
        ?int $senderId = null
    ): ?array {
        $parentPhone = $student->parent?->phone_number;

        if (! $parentPhone) {
            return null;
        }

        $message = $this->buildAttendanceAlert($student, $status, $date, $subjectName, $notes);

        return $this->sendMessage($parentPhone, $message, $student, 'attendance', $senderId);
    }

    /**
     * Build student conduct / character incident alert message.
     */
    public function buildConductAlert(Student $student, StudentConductRecord $record): string
    {
        $parentName = $student->parent?->name ?? 'Bapak/Ibu Wali Murid';
        $isPositive = $record->type === 'apresiasi';

        $icon = $isPositive ? '⭐ [APRESIASI KARAKTER]' : '⚠️ [CATATAN PEMBINAAN DISIPLIN]';
        $pointSign = $isPositive ? '+' : '-';
        $formattedDate = date('d F Y', strtotime($record->incident_date->toDateString()));

        $msg = "Assalamu'alaikum Wr. Wb.\n";
        $msg .= "Yth. {$parentName},\n\n";
        $msg .= "Berikut informasi catatan perkembangan pembiasaan karakter ananda:\n\n";
        $msg .= "👤 *Nama Siswa:* *{$student->full_name}*\n";
        $msg .= "🏫 *Kelas:* " . ($student->classroom?->name ?? '-') . "\n";
        $msg .= "📅 *Tanggal:* {$formattedDate}\n";
        $msg .= "🔖 *Kategori:* {$icon}\n";
        $msg .= "📌 *Perihal:* *{$record->title}*\n";
        $msg .= "🎯 *Poin:* {$pointSign}{$record->points} poin\n";

        if (! empty($record->description)) {
            $msg .= "📝 *Keterangan:* {$record->description}\n";
        }

        if (! empty($record->follow_up_action)) {
            $msg .= "🤝 *Tindak Lanjut / Bimbingan:* {$record->follow_up_action}\n";
        }

        if ($isPositive) {
            $msg .= "\nAlhamdulillah, kami turut bangga dan mengapresiasi teladan baik yang ditunjukkan ananda. Semoga senantiasa istiqomah.\n";
        } else {
            $msg .= "\nMohon bimbingan dan pendampingan bersama di rumah agar ananda dapat terus memperbaiki sikap dan kedisiplinannya.\n";
        }

        $msg .= "\nTerima kasih atas sinergi yang baik antara madrasah dan keluarga.\nWassalamu'alaikum Wr. Wb.\n";
        $msg .= "_Tim Bimbingan Karakter & Wali Kelas MI Plus Raudlatul Muta'allimin_";

        return $msg;
    }

    /**
     * Send conduct alert to parent.
     */
    public function sendConductAlert(Student $student, StudentConductRecord $record, ?int $senderId = null): ?array
    {
        $parentPhone = $student->parent?->phone_number;

        if (! $parentPhone) {
            return null;
        }

        $message = $this->buildConductAlert($student, $record);

        return $this->sendMessage($parentPhone, $message, $student, 'conduct', $senderId);
    }

    /**
     * Build leave request review status alert.
     */
    public function buildLeaveAlert(LeaveRequest $leaveRequest): string
    {
        $student = $leaveRequest->student;
        $parentName = $leaveRequest->parent?->name ?? 'Bapak/Ibu Wali Murid';
        $statusText = $leaveRequest->status->value === 'approved' ? 'DISETUJUI (Izin Diterima)' : 'DITOLAK / PERLU KONFIRMASI';

        $startDate = date('d/m/Y', strtotime($leaveRequest->start_date->toDateString()));
        $endDate = date('d/m/Y', strtotime($leaveRequest->end_date->toDateString()));

        $msg = "Assalamu'alaikum Wr. Wb.\n";
        $msg .= "Yth. {$parentName},\n\n";
        $msg .= "Pemberitahuan resmi mengenai pengajuan surat izin madrasah ananda:\n\n";
        $msg .= "👤 *Nama Siswa:* *{$student->full_name}*\n";
        $msg .= "📅 *Periode Izin:* {$startDate} s.d {$endDate}\n";
        $msg .= "📋 *Keperluan:* " . ucfirst($leaveRequest->reason_type->value) . " - {$leaveRequest->description}\n";
        $msg .= "📌 *Status Pengajuan:* *{$statusText}*\n";

        if (! empty($leaveRequest->review_notes)) {
            $msg .= "💬 *Catatan Wali Kelas:* {$leaveRequest->review_notes}\n";
        }

        $msg .= "\nTerima kasih telah memberitahukan ketidakhadiran ananda secara tertib.\nWassalamu'alaikum Wr. Wb.\n";
        $msg .= "_Wali Kelas MI Plus Raudlatul Muta'allimin_";

        return $msg;
    }

    /**
     * Send leave status alert to parent.
     */
    public function sendLeaveAlert(LeaveRequest $leaveRequest, ?int $senderId = null): ?array
    {
        $parentPhone = $leaveRequest->parent?->phone_number;

        if (! $parentPhone) {
            return null;
        }

        $message = $this->buildLeaveAlert($leaveRequest);

        return $this->sendMessage($parentPhone, $message, $leaveRequest->student, 'leave', $senderId);
    }

    /**
     * Build SPP payment confirmation receipt message.
     */
    public function buildTuitionPaymentAlert(\App\Models\TuitionPayment $payment, \App\Models\TuitionBill $bill): string
    {
        $student = $bill->student;
        $parentName = $student?->parent?->name ?? 'Bapak/Ibu Wali Murid';
        $nominal = 'Rp ' . number_format((float) $payment->amount_paid, 0, ',', '.');
        $receipt = $payment->receipt_number;
        $payDate = date('d/m/Y', strtotime($payment->payment_date->toDateString() ?? date('Y-m-d')));

        $methodName = $payment->payment_method instanceof \App\Enums\PaymentMethod
            ? $payment->payment_method->label()
            : strtoupper(str_replace('_', ' ', (string) $payment->payment_method));

        $msg = "Assalamu'alaikum Wr. Wb.\n";
        $msg .= "Yth. {$parentName},\n\n";
        $msg .= "Alhamdulillah, pembayaran SPP/Madrasah ananda telah *BERHASIL DITERIMA & DIVERIFIKASI*:\n\n";
        $msg .= "🧾 *No. Kwitansi Sah:* `{$receipt}`\n";
        $msg .= "👤 *Nama Siswa:* *{$student->full_name}*\n";
        $msg .= "🏫 *Kelas:* " . ($student->classroom?->name ?? '-') . "\n";
        $msg .= "📋 *Uraian:* {$bill->bill_title}\n";
        $msg .= "💰 *Nominal Dibayar:* *{$nominal}*\n";
        $msg .= "📅 *Tanggal Verifikasi:* {$payDate}\n";
        $msg .= "💳 *Metode:* {$methodName}\n\n";
        $msg .= "Kwitansi digital resmi dapat diunduh kapan saja melalui portal wali murid.\n\n";
        $msg .= "Jazakumullahu khairan katsiran atas partisipasi Bapak/Ibu dalam mendukung pendidikan ananda.\nWassalamu'alaikum Wr. Wb.\n";
        $msg .= "_Bendahara & Tata Usaha MI Plus Raudlatul Muta'allimin_";

        return $msg;
    }

    /**
     * Send SPP payment confirmation alert to parent.
     */
    public function sendTuitionPaymentAlert(\App\Models\TuitionPayment $payment, \App\Models\TuitionBill $bill, ?string $targetPhone = null, ?int $senderId = null): ?array
    {
        $phone = $targetPhone ?? $bill->student?->parent?->phone_number;

        if (! $phone) {
            return null;
        }

        $message = $this->buildTuitionPaymentAlert($payment, $bill);

        return $this->sendMessage($phone, $message, $bill->student, 'tuition_payment', $senderId);
    }

    /**
     * Build PPDB registration fee confirmation message.
     */
    public function buildPpdbPaymentAlert(\App\Models\PpdbRegistration $registration): string
    {
        $nominal = $registration->formattedFee();
        $regNo = $registration->registration_number;

        $msg = "Assalamu'alaikum Wr. Wb.\n";
        $msg .= "Yth. Bapak/Ibu {$registration->parent_name},\n\n";
        $msg .= "Alhamdulillah, pembayaran biaya pendaftaran PPDB ananda telah *BERHASIL TERVERIFIKASI OTOMATIS*:\n\n";
        $msg .= "📌 *No. Registrasi:* `{$regNo}`\n";
        $msg .= "👤 *Calon Siswa:* *{$registration->child_name}*\n";
        $msg .= "💰 *Biaya Formulir:* *{$nominal}*\n";
        $msg .= "✅ *Status:* Lunas & Berkas Siap Diobservasi\n\n";
        $msg .= "Bapak/Ibu dapat memantau status seleksi atau mengunduh ulang bukti pendaftaran di website madrasah melalui menu Cek Status PPDB.\n\n";
        $msg .= "Wassalamu'alaikum Wr. Wb.\n";
        $msg .= "_Panitia PPDB MI Plus Raudlatul Muta'allimin_";

        return $msg;
    }

    /**
     * Send PPDB payment confirmation alert.
     */
    public function sendPpdbPaymentAlert(\App\Models\PpdbRegistration $registration, ?int $senderId = null): ?array
    {
        $phone = $registration->parent_whatsapp;

        if (! $phone) {
            return null;
        }

        $message = $this->buildPpdbPaymentAlert($registration);

        return $this->sendMessage($phone, $message, null, 'ppdb_payment', $senderId);
    }
}
