<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\GalleryCategory;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\SchoolGallery;
use App\Services\PrayerTimeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicNewsController extends Controller
{
    public function __construct(
        protected PrayerTimeService $prayerTimeService
    ) {}

    public function index(Request $request): View
    {
        $prayerSchedule = $this->prayerTimeService->getSchedule();
        $tab = $request->query('tab', 'all');
        $search = $request->query('q');
        $category = $request->query('kategori');

        // Articles Query
        $articlesQuery = Article::query()
            ->where('is_published', true)
            ->with('author')
            ->latest('published_at');

        if ($search) {
            $articlesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }
        $articles = $articlesQuery->get();

        // Announcements Query
        $announcementsQuery = Announcement::query()
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->latest();

        if ($search) {
            $announcementsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }
        $announcements = $announcementsQuery->get();

        // Galleries Query
        $galleriesQuery = SchoolGallery::query()->latest();

        if ($category && ($enumCat = GalleryCategory::tryFrom($category))) {
            $galleriesQuery->where('category', $enumCat);
        }

        if ($search) {
            $galleriesQuery->where('title', 'like', "%{$search}%");
        }
        $galleries = $galleriesQuery->get();

        // Featured highlight (first latest article)
        $featuredArticle = $articles->first();

        $galleryCategories = GalleryCategory::cases();

        return view('public.news.index', compact(
            'prayerSchedule',
            'tab',
            'search',
            'category',
            'articles',
            'announcements',
            'galleries',
            'featuredArticle',
            'galleryCategories'
        ));
    }

    public function showArticle(string $slug): View
    {
        $prayerSchedule = $this->prayerTimeService->getSchedule();

        $article = Article::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->with('author')
            ->firstOrFail();

        $recentArticles = Article::query()
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        $recentAnnouncements = Announcement::query()
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->latest()
            ->limit(3)
            ->get();

        return view('public.news.article', compact(
            'prayerSchedule',
            'article',
            'recentArticles',
            'recentAnnouncements'
        ));
    }

    public function showAnnouncement(Announcement $announcement): View
    {
        $prayerSchedule = $this->prayerTimeService->getSchedule();

        $recentAnnouncements = Announcement::query()
            ->where('id', '!=', $announcement->id)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->latest()
            ->limit(4)
            ->get();

        $recentArticles = Article::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.news.announcement', compact(
            'prayerSchedule',
            'announcement',
            'recentAnnouncements',
            'recentArticles'
        ));
    }
}
