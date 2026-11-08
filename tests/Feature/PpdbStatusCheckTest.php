<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PpdbRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PpdbStatusCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_check_page_is_accessible(): void
    {
        $response = $this->get('/ppdb/cek-status');

        $response->assertOk();
        $response->assertSeeText('Cek Status Seleksi PPDB Online');
        $response->assertSeeText('Nomor Registrasi');
        $response->assertSeeText('NIK');
    }

    public function test_search_by_nik_returns_registration_status(): void
    {
        $registration = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-0005',
            'child_name' => 'Fatimah Zahra',
            'nik' => '3505012304190005',
            'gender' => 'P',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-06-20',
            'previous_school' => 'TK Aisyiyah',
            'parent_name' => 'Wali Fatimah',
            'parent_whatsapp' => '081234567895',
            'address' => 'Sanankulon, Blitar',
            'file_birth_certificate' => 'ppdb/akta.pdf',
            'file_family_card' => 'ppdb/kk.pdf',
            'status' => 'verified',
            'admin_notes' => 'Berkas lengkap, silakan hadir observasi tanggal 20.',
        ]);

        $response = $this->get('/ppdb/cek-status?q=3505012304190005');

        $response->assertOk();
        $response->assertSeeText('Fatimah Zahra');
        $response->assertSeeText('PPDB-2026-0005');
        $response->assertSeeText('Berkas Terverifikasi');
        $response->assertSeeText('Berkas lengkap, silakan hadir observasi tanggal 20.');
    }

    public function test_search_by_registration_number_returns_status(): void
    {
        $registration = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-0010',
            'child_name' => 'Ibrahim Khalil',
            'nik' => '3505012304190010',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-07-10',
            'previous_school' => 'RA Miftahul Huda',
            'parent_name' => 'Wali Ibrahim',
            'parent_whatsapp' => '081234567810',
            'address' => 'Kalipucung',
            'file_birth_certificate' => 'ppdb/akta.pdf',
            'file_family_card' => 'ppdb/kk.pdf',
            'status' => 'accepted',
        ]);

        $response = $this->get('/ppdb/cek-status?q=PPDB-2026-0010');

        $response->assertOk();
        $response->assertSeeText('Ibrahim Khalil');
        $response->assertSeeText('Diterima');
    }

    public function test_search_with_non_existent_data_shows_friendly_message(): void
    {
        $response = $this->get('/ppdb/cek-status?q=9999999999999999');

        $response->assertOk();
        $response->assertSeeText('Data pendaftaran tidak ditemukan');
    }
}
