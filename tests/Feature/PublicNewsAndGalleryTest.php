<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AnnouncementTarget;
use App\Enums\GalleryCategory;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\SchoolGallery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNewsAndGalleryTest extends TestCase
{
    use RefreshDatabase;

    protected User $author;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->create([
            'role' => 'admin',
            'phone_number' => '081234567890',
            'name' => 'Ustadz Ahmad Fauzi',
        ]);
    }

    public function test_news_and_gallery_hub_page_is_accessible(): void
    {
        Article::create([
            'author_id' => $this->author->id,
            'title' => 'Santri MI Plus Raih Medali Emas Olimpiade Sains',
            'slug' => 'santri-mi-plus-raih-medali-emas',
            'content' => '<p>Alhamdulillah santri kami berhasil mengharumkan nama madrasah.</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Announcement::create([
            'title' => 'Libur Awal Ramadhan 1448 H',
            'content' => 'Madrasah diliburkan menyambut bulan suci Ramadhan.',
            'target_audience' => AnnouncementTarget::All,
            'valid_until' => now()->addDays(10),
        ]);

        SchoolGallery::create([
            'title' => 'Latihan Rutin Hadrah Rebana',
            'category' => GalleryCategory::HadrahRebana,
            'image_path' => 'images/gallery/hadrah.svg',
        ]);

        $response = $this->get('/kabar');

        $response->assertOk();
        $response->assertSeeText('Kabar, Pengumuman & Galeri Madrasah');
        $response->assertSeeText('Santri MI Plus Raih Medali Emas Olimpiade Sains');
        $response->assertSeeText('Libur Awal Ramadhan 1448 H');
        $response->assertSeeText('Latihan Rutin Hadrah Rebana');
    }

    public function test_news_filtering_by_tab_and_search(): void
    {
        Article::create([
            'author_id' => $this->author->id,
            'title' => 'Pelatihan Robotika dan Coding Santri Cilik',
            'slug' => 'pelatihan-robotika-santri-cilik',
            'content' => '<p>Kegiatan pengenalan teknologi modern berbasis Islam.</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $searchResponse = $this->get('/kabar?q=Robotika');
        $searchResponse->assertOk();
        $searchResponse->assertSeeText('Pelatihan Robotika dan Coding Santri Cilik');

        $tabResponse = $this->get('/kabar?tab=berita');
        $tabResponse->assertOk();
        $tabResponse->assertSeeText('Berita & Warta Madrasah');
    }

    public function test_article_detail_page_is_accessible_and_shows_full_content(): void
    {
        $article = Article::create([
            'author_id' => $this->author->id,
            'title' => 'Peringatan Hari Santri Nasional Berlangsung Khidmat',
            'slug' => 'peringatan-hari-santri-nasional-berlangsung-khidmat',
            'content' => '<p>Upacara peringatan Hari Santri Nasional di MI Plus berlangsung khidmat dipimpin oleh Pengasuh Yayasan.</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get("/berita/{$article->slug}");

        $response->assertOk();
        $response->assertSeeText('Peringatan Hari Santri Nasional Berlangsung Khidmat');
        $response->assertSeeText('Ustadz Ahmad Fauzi');
        $response->assertSeeText('Upacara peringatan Hari Santri Nasional');
        $response->assertSeeText('Bagikan via WhatsApp');
        $response->assertSeeText('Salin Link');
    }

    public function test_unpublished_article_returns_404(): void
    {
        $article = Article::create([
            'author_id' => $this->author->id,
            'title' => 'Draft Berita Internal',
            'slug' => 'draft-berita-internal',
            'content' => '<p>Masih dalam peninjauan dewan guru.</p>',
            'is_published' => false,
        ]);

        $response = $this->get("/berita/{$article->slug}");
        $response->assertNotFound();
    }

    public function test_announcement_detail_page_is_accessible(): void
    {
        $announcement = Announcement::create([
            'title' => 'Edaran Pembagian Rapor Semester Ganjil',
            'content' => 'Pengambilan rapor santri wajib dihadiri orang tua/wali santri.',
            'target_audience' => AnnouncementTarget::Parent,
            'valid_until' => now()->addDays(5),
        ]);

        $response = $this->get("/pengumuman/{$announcement->id}");

        $response->assertOk();
        $response->assertSeeText('Edaran Pembagian Rapor Semester Ganjil');
        $response->assertSeeText('Pengambilan rapor santri wajib dihadiri');
        $response->assertSeeText('Wali Murid');
        $response->assertSeeText('Hubungi Hotline TU');
    }

    public function test_legacy_galeri_url_redirects_to_dedicated_hub(): void
    {
        $response = $this->get('/galeri');

        $response->assertRedirect('/kabar?tab=galeri');
    }

    public function test_homepage_links_to_news_detail_pages(): void
    {
        $article = Article::create([
            'author_id' => $this->author->id,
            'title' => 'PPDB MI Plus Resmi Membuka Gelombang 1',
            'slug' => 'ppdb-mi-plus-resmi-membuka-gelombang-1',
            'content' => '<p>Pendaftaran gelombang pertama telah dibuka.</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('PPDB MI Plus Resmi Membuka Gelombang 1');
        $response->assertSee('/berita/ppdb-mi-plus-resmi-membuka-gelombang-1');
        $response->assertSee('/kabar');
    }
}
