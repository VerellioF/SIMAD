<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PpdbRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PpdbRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_is_accessible(): void
    {
        $response = $this->get('/ppdb/daftar');

        $response->assertOk();
        $response->assertSeeText('Formulir Pendaftaran Santri Baru');
        $response->assertSeeText('Data Calon Santri');
        $response->assertSeeText('Unggah Berkas');
    }

    public function test_registration_validates_required_fields(): void
    {
        $response = $this->post('/ppdb/daftar', []);

        $response->assertSessionHasErrors([
            'child_name',
            'nik',
            'gender',
            'birth_place',
            'birth_date',
            'previous_school',
            'parent_name',
            'parent_whatsapp',
            'address',
            'file_birth_certificate',
            'file_family_card',
        ]);
    }

    public function test_registration_validates_nik_and_whatsapp_format(): void
    {
        $response = $this->post('/ppdb/daftar', [
            'nik' => '12345', // invalid length
            'parent_whatsapp' => 'abcdef', // invalid phone
        ]);

        $response->assertSessionHasErrors(['nik', 'parent_whatsapp']);
    }

    public function test_successful_registration_stores_data_and_redirects(): void
    {
        Storage::fake('public');

        $birthCert = UploadedFile::fake()->create('akta.pdf', 500, 'application/pdf');
        $familyCard = UploadedFile::fake()->create('kk.jpg', 500, 'image/jpeg');
        $photo = UploadedFile::fake()->create('pasfoto.jpg', 500, 'image/jpeg');

        $payload = [
            'child_name' => 'Fulan bin Fulan',
            'nik' => '3505012304190001',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-04-12',
            'previous_school' => 'RA Perwanida Kalipucung',
            'parent_name' => 'Fulan Senior',
            'parent_whatsapp' => '081234567890',
            'parent_job' => 'Wiraswasta',
            'address' => 'Dusun Krajan, RT 01 RW 02 Kalipucung',
            'file_birth_certificate' => $birthCert,
            'file_family_card' => $familyCard,
            'file_photo' => $photo,
        ];

        $response = $this->post('/ppdb/daftar', $payload);

        $this->assertDatabaseHas('ppdb_registrations', [
            'child_name' => 'Fulan bin Fulan',
            'nik' => '3505012304190001',
            'status' => 'pending',
        ]);

        $registration = PpdbRegistration::first();
        $this->assertNotNull($registration);
        $this->assertMatchesRegularExpression('/^PPDB-\d{4}-\d{4}$/', $registration->registration_number);

        Storage::disk('public')->assertExists($registration->file_birth_certificate);
        Storage::disk('public')->assertExists($registration->file_family_card);
        Storage::disk('public')->assertExists($registration->file_photo);

        $response->assertRedirect("/ppdb/sukses/{$registration->registration_number}");
    }

    public function test_success_page_displays_registration_number(): void
    {
        $registration = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-0001',
            'child_name' => 'Santri Baru',
            'nik' => '3505012304190002',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-01-01',
            'previous_school' => 'TK Al-Hidayah',
            'parent_name' => 'Orang Tua',
            'parent_whatsapp' => '0811111111',
            'address' => 'Blitar',
            'file_birth_certificate' => 'ppdb/akta.pdf',
            'file_family_card' => 'ppdb/kk.pdf',
            'status' => 'pending',
        ]);

        $response = $this->get("/ppdb/sukses/{$registration->registration_number}");

        $response->assertOk();
        $response->assertSeeText('PPDB-2026-0001');
        $response->assertSeeText('Pendaftaran Berhasil Dikirim');
    }
}
