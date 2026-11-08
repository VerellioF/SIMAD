@extends('layouts.public')

@section('title', $announcement->title . ' - Pengumuman Resmi')

@section('content')
<div class="bg-slate-50 py-10" x-data="{ copied: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
            <a href="/" class="hover:text-emerald-900 transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('public.news') }}" class="hover:text-emerald-900 transition">Kabar & Galeri</a>
            <span>/</span>
            <a href="{{ route('public.news', ['tab' => 'pengumuman']) }}" class="hover:text-emerald-900 transition">Pengumuman</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-xs sm:max-w-md">{{ $announcement->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- MAIN ANNOUNCEMENT CONTAINER (8 COLS) -->
            <div class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200/80 space-y-8">
                
                <!-- Official Header Letterhead Style -->
                <div class="p-6 rounded-2xl bg-gradient-to-r from-emerald-950 to-emerald-900 text-white space-y-3 border border-gold-500/30">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="px-3 py-1 rounded-full bg-gold-500 text-emerald-950 font-bold uppercase text-[10px] tracking-wider">
                            Pengumuman Resmi
                        </span>
                        <span class="text-xs font-mono text-emerald-200">
                            {{ $announcement->created_at->translatedFormat('d F Y') }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white leading-snug">
                        {{ $announcement->title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-emerald-100 pt-2 border-t border-emerald-800/80">
                        <div>
                            <span class="text-emerald-400 block text-[10px] uppercase font-bold">Sasaran Informasi:</span>
                            <span class="font-bold text-white">{{ $announcement->target_audience->label() }}</span>
                        </div>
                        <div>
                            <span class="text-emerald-400 block text-[10px] uppercase font-bold">Masa Berlaku:</span>
                            <span class="font-bold text-white">
                                {{ $announcement->valid_until ? $announcement->valid_until->translatedFormat('d F Y') : 'Berlaku Seterusnya' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Announcement Body -->
                <div class="text-slate-800 text-sm sm:text-base leading-relaxed space-y-4 whitespace-pre-line p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    {{ $announcement->content }}
                </div>

                <!-- Action Bar & Hotline -->
                <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="space-y-1 text-center sm:text-left">
                        <span class="text-xs font-bold text-emerald-950 block">Ada Pertanyaan Mengenai Pengumuman Ini?</span>
                        <p class="text-xs text-emerald-800">Hubungi petugas Tata Usaha atau nomor hotline resmi madrasah.</p>
                    </div>

                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('simad.contact_phone')) }}?text={{ urlencode('Assalamu\'alaikum, saya ingin bertanya terkait pengumuman: ' . $announcement->title) }}"
                        target="_blank"
                        class="px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs flex items-center gap-2 transition shadow-sm shrink-0">
                        <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        <span>Hubungi Hotline TU</span>
                    </a>
                </div>

                <!-- Share Box -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100 text-xs">
                    <a href="{{ route('public.news', ['tab' => 'pengumuman']) }}" class="font-bold text-emerald-900 hover:underline flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Daftar Seluruh Pengumuman</span>
                    </a>

                    <div class="flex items-center gap-2">
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($announcement->title . ' - ' . url()->current()) }}" target="_blank"
                            class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            <span>Bagikan</span>
                        </a>

                        <button type="button" @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2500)"
                            class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                            <span x-show="!copied">Salin Tautan</span>
                            <span x-show="copied" x-cloak class="text-emerald-700 font-bold">✓ Tersalin!</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR (4 COLS) -->
            <aside class="lg:col-span-4 space-y-6">

                <!-- Other Announcements Widget -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pengumuman Lainnya</h3>
                        <a href="{{ route('public.news', ['tab' => 'pengumuman']) }}" class="text-[11px] font-bold text-emerald-800 hover:underline">Semua</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentAnnouncements as $ann)
                            <a href="{{ route('public.news.announcement', $ann) }}" class="block p-3.5 rounded-2xl bg-slate-50 hover:bg-emerald-50/50 transition border border-slate-100">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block mb-1">
                                    {{ $ann->target_audience->label() }}
                                </span>
                                <h4 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug">
                                    {{ $ann->title }}
                                </h4>
                                <span class="text-[10px] font-mono text-slate-400 mt-1 block">
                                    {{ $ann->created_at->translatedFormat('d M Y') }}
                                </span>
                            </a>
                        @empty
                            <p class="text-xs text-slate-400">Tidak ada pengumuman lainnya.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Articles Widget -->
                @if($recentArticles->isNotEmpty())
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Warta Madrasah</h3>
                            <a href="{{ route('public.news', ['tab' => 'berita']) }}" class="text-[11px] font-bold text-emerald-800 hover:underline">Semua</a>
                        </div>

                        <div class="space-y-3">
                            @foreach($recentArticles as $art)
                                <a href="{{ route('public.news.article', $art->slug) }}" class="flex items-start gap-3 group">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-950 overflow-hidden shrink-0 flex items-center justify-center text-gold-400">
                                        <svg class="w-5 h-5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 transition line-clamp-2 leading-snug">
                                            {{ $art->title }}
                                        </h4>
                                        <span class="text-[10px] font-mono text-slate-400 mt-0.5 block">
                                            {{ $art->published_at ? $art->published_at->translatedFormat('d M Y') : $art->created_at->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </aside>

        </div>

    </div>
</div>
@endsection
