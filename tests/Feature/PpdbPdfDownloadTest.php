<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PpdbRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PpdbPdfDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_download_route_returns_pdf_stream_for_valid_registration(): void
    {
        $registration = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-0001',
            'child_name' => 'Ahmad Santri',
            'nik' => '3505012304190001',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-03-15',
            'previous_school' => 'RA Perwanida',
            'parent_name' => 'Bapak Ahmad',
            'parent_whatsapp' => '081234567890',
            'address' => 'Kalipucung, Blitar',
            'file_birth_certificate' => 'ppdb/akta.pdf',
            'file_family_card' => 'ppdb/kk.pdf',
            'status' => 'pending',
        ]);

        $response = $this->get("/ppdb/cetak/{$registration->registration_number}");

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_pdf_download_returns_404_for_invalid_registration(): void
    {
        $response = $this->get('/ppdb/cetak/INVALID-NON-EXISTENT');

        $response->assertNotFound();
    }
}
