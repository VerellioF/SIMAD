<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PpdbRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PpdbFeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_displays_fee_amount(): void
    {
        $response = $this->get('/ppdb/daftar');

        $response->assertOk();
        $response->assertSeeText('Biaya Pendaftaran & Observasi');
        $response->assertSeeText('Rp 50.000');
        $response->assertSeeText('Transfer Bank (BSI Syariah)');
        $response->assertSeeText('Bayar Tunai di Loket TU');
    }

    public function test_registration_persists_payment_method_and_proof(): void
    {
        Storage::fake('public');

        $birthCert = UploadedFile::fake()->create('akta.pdf', 300, 'application/pdf');
        $familyCard = UploadedFile::fake()->create('kk.jpg', 300, 'image/jpeg');
        $paymentProof = UploadedFile::fake()->create('bukti_transfer.jpg', 400, 'image/jpeg');

        $payload = [
            'child_name' => 'Ahmad Faiz',
            'nik' => '3505011105190001',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-05-11',
            'previous_school' => 'RA Mambaul Ulum',
            'parent_name' => 'H. Ridwan',
            'parent_whatsapp' => '081234567899',
            'parent_job' => 'PNS',
            'address' => 'Desa Kalipucung RT 03 RW 01',
            'payment_method' => 'transfer',
            'file_birth_certificate' => $birthCert,
            'file_family_card' => $familyCard,
            'file_payment_proof' => $paymentProof,
        ];

        $response = $this->post('/ppdb/daftar', $payload);

        $registration = PpdbRegistration::first();
        $this->assertNotNull($registration);
        $this->assertEquals(50000, $registration->registration_fee);
        $this->assertEquals('transfer', $registration->payment_method);
        $this->assertEquals('unpaid', $registration->payment_status);
        $this->assertNotNull($registration->file_payment_proof);

        Storage::disk('public')->assertExists($registration->file_payment_proof);
        $response->assertRedirect("/ppdb/sukses/{$registration->registration_number}");
    }

    public function test_cash_payment_selection_persisted(): void
    {
        Storage::fake('public');

        $payload = [
            'child_name' => 'Siti Nurhaliza',
            'nik' => '3505015506190002',
            'gender' => 'P',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-06-15',
            'previous_school' => 'TK Dharma Wanita',
            'parent_name' => 'Bambang',
            'parent_whatsapp' => '082233445566',
            'address' => 'Kalipucung Wetan',
            'payment_method' => 'cash',
            'file_birth_certificate' => UploadedFile::fake()->create('akta.pdf', 300, 'application/pdf'),
            'file_family_card' => UploadedFile::fake()->create('kk.pdf', 300, 'application/pdf'),
        ];

        $this->post('/ppdb/daftar', $payload);

        $registration = PpdbRegistration::where('nik', '3505015506190002')->first();
        $this->assertNotNull($registration);
        $this->assertEquals('cash', $registration->payment_method);
        $this->assertEquals('unpaid', $registration->payment_status);
        $this->assertEquals('Rp 50.000', $registration->formattedFee());
    }

    public function test_admin_can_verify_and_update_payment_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'phone_number' => '081234567890',
        ]);

        $registration = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-0099',
            'child_name' => 'Muhammad Zaki',
            'nik' => '3505011212190003',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-12-12',
            'previous_school' => 'RA Baiturrahman',
            'parent_name' => 'Zainuddin',
            'parent_whatsapp' => '085678901234',
            'address' => 'Blitar',
            'file_birth_certificate' => 'ppdb/akta.pdf',
            'file_family_card' => 'ppdb/kk.pdf',
            'registration_fee' => 50000,
            'payment_method' => 'transfer',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/ppdb/{$registration->id}/status", [
            'status' => 'verified',
            'payment_status' => 'paid',
            'admin_notes' => 'Pembayaran lunas via transfer BSI.',
        ]);

        $response->assertRedirect();
        $registration->refresh();
        $this->assertEquals('paid', $registration->payment_status);
        $this->assertNotNull($registration->paid_at);
        $this->assertTrue($registration->isPaid());
        $this->assertEquals('verified', $registration->status->value);
    }

    public function test_public_status_check_and_pdf_show_payment_info(): void
    {
        $registration = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-0088',
            'child_name' => 'Dewi Sartika',
            'nik' => '3505014407190004',
            'gender' => 'P',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-07-04',
            'previous_school' => 'TK Pertiwi',
            'parent_name' => 'Subagyo',
            'parent_whatsapp' => '087711223344',
            'address' => 'Kalipucung',
            'file_birth_certificate' => 'ppdb/akta.pdf',
            'file_family_card' => 'ppdb/kk.pdf',
            'registration_fee' => 50000,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        // Cek Status Page
        $statusResponse = $this->get("/ppdb/cek-status?q={$registration->registration_number}");
        $statusResponse->assertOk();
        $statusResponse->assertSeeText('Biaya Formulir & Observasi');
        $statusResponse->assertSeeText('Rp 50.000');
        $statusResponse->assertSeeText('Bayar Tunai di Loket TU');

        // Cetak Bukti PDF Endpoint with DOB verification
        $pdfResponse = $this->get("/ppdb/cetak/{$registration->registration_number}?dob={$registration->birth_date->format('Y-m-d')}");
        $pdfResponse->assertOk();
        $pdfResponse->assertHeader('content-type', 'application/pdf');

        // Render PDF template directly to assert HTML content
        $html = view('pdf.ppdb-proof', [
            'registration' => $registration,
            'school' => [
                'name' => config('simad.school_name'),
                'tagline' => config('simad.school_tagline'),
                'address' => config('simad.school_address'),
                'nsm' => config('simad.nsm'),
                'npsn' => config('simad.npsn'),
                'phone' => config('simad.contact_phone'),
            ],
        ])->render();

        $this->assertStringContainsString('Administrasi Biaya Pendaftaran', $html);
        $this->assertStringContainsString('Rp 50.000', $html);
        $this->assertStringContainsString('Bayar Tunai di Loket TU', $html);
    }
}
