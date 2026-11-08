<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use App\Models\SchoolGallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_loads_successfully_with_school_profile(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText("MI Plus Raudlatul Muta'allimin");
        $response->assertSeeText('PPDB Online');
        $response->assertSeeText('Cek Status');
    }

    public function test_landing_page_renders_blitar_prayer_times(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('Jadwal Sholat');
        $response->assertSeeText('Blitar');
        $response->assertSeeText('Subuh');
        $response->assertSeeText('Dzuhur');
        $response->assertSeeText('Ashar');
        $response->assertSeeText('Maghrib');
        $response->assertSeeText('Isya');
    }

    public function test_landing_page_displays_articles_and_galleries(): void
    {
        Article::create([
            'title' => 'Peringatan Maulid Nabi di MI Plus',
            'slug' => 'peringatan-maulid-nabi',
            'content' => 'Acara hadrah dan tausiyah berlangsung khidmat...',
            'is_published' => true,
        ]);

        SchoolGallery::create([
            'title' => 'Grup Hadrah Rebana Santri',
            'category' => 'hadrah_rebana',
            'image_path' => 'galleries/hadrah.jpg',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('Peringatan Maulid Nabi di MI Plus');
        $response->assertSeeText('Grup Hadrah Rebana Santri');
    }
}
