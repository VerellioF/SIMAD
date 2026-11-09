<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContactAndNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_navbar_has_academic_dropdown_with_curriculum_teachers_and_calendar(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('Akademik & Profil');
        $response->assertSeeText('Kurikulum Terpadu');
        $response->assertSeeText('Dewan Guru');
        $response->assertSeeText('Kalender Akademik');
        $response->assertSeeText('Kontak & Lokasi');
    }

    public function test_home_page_contains_contact_and_location_section(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="kontak"', false);
        $response->assertSeeText('Kontak & Lokasi Madrasah');
        $response->assertSeeText('Jl. KH. Muta\'allim No. 09');
        $response->assertSeeText('Jam Operasional Tata Usaha');
    }

    public function test_public_contact_page_is_accessible(): void
    {
        $response = $this->get(route('public.contact'));

        $response->assertOk();
        $response->assertSeeText('Kontak & Lokasi Madrasah');
        $response->assertSeeText('Peta Lokasi & Rute Kampus');
        $response->assertSeeText('Kirim Pesan ke Tata Usaha');
        $response->assertSeeText('Jl. KH. Muta\'allim No. 09');
        $response->assertSeeText('Google Maps');
    }

    public function test_user_can_submit_contact_consultation_form(): void
    {
        $payload = [
            'name' => 'Bapak Muhammad Hidayat',
            'phone' => '081298765432',
            'category' => 'ppdb',
            'message' => 'Assalamu\'alaikum, mohon info rincian biaya seragam dan kitab untuk santri baru tahun ajaran 2026/2027.',
        ];

        $response = $this->post(route('public.contact.send'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->post(route('public.contact.send'), []);

        $response->assertSessionHasErrors(['name', 'phone', 'category', 'message']);
    }
}
