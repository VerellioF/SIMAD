<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\Extracurricular;
use App\Models\SchoolEvent;
use App\Models\SchoolGallery;
use App\Models\User;
use App\Services\PrayerTimeService;
use Illuminate\Contracts\View\View;

class PublicController extends Controller
{
    public function __construct(
        protected PrayerTimeService $prayerTimeService
    ) {}

    public function index(): View
    {
        $prayerSchedule = $this->prayerTimeService->getSchedule();

        $articles = Article::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->limit(3)
            ->get();

        $galleries = SchoolGallery::query()
            ->latest()
            ->limit(6)
            ->get();

        $announcements = Announcement::query()
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->latest()
            ->limit(3)
            ->get();

        $upcomingEvents = SchoolEvent::query()
            ->orderBy('start_date')
            ->limit(4)
            ->get();

        $featuredTeachers = User::query()
            ->whereIn('role', [\App\Enums\UserRole::Teacher, \App\Enums\UserRole::Admin])
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->limit(4)
            ->get();

        $featuredEkskuls = Extracurricular::query()
            ->where('is_active', true)
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(3)
            ->get();

        return view('public.home', compact(
            'prayerSchedule',
            'articles',
            'galleries',
            'announcements',
            'upcomingEvents',
            'featuredTeachers',
            'featuredEkskuls'
        ));
    }
}
