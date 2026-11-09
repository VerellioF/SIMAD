@extends('layouts.public')

@section('title', $article->title . ' - Kabar Madrasah')

@section('content')
<div class="bg-slate-50 py-10" x-data="{ copied: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
            <a href="/" class="hover:text-emerald-900 transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('public.news') }}" class="hover:text-emerald-900 transition">Kabar & Galeri</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-xs sm:max-w-md">{{ $article->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- MAIN ARTICLE BODY (8 COLS) -->
            <article class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200/80 space-y-8">
                
                <!-- Article Header -->
                <div class="space-y-4 pb-6 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-950 font-bold uppercase text-[10px] tracking-wider">
                            Warta Madrasah
                        </span>
                        <span class="text-slate-400">•</span>
                        <span class="font-mono text-slate-500">
                            {{ $article->published_at ? $article->published_at->translatedFormat('l, d F Y') : $article->created_at->translatedFormat('l, d F Y') }}
                        </span>
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ max(1, (int) ceil(str_word_count(strip_tags($article->content)) / 150)) }} menit baca</span>
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-snug">
                        {{ $article->title }}
                    </h1>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-900 text-gold-400 font-black text-sm flex items-center justify-center shadow-inner">
                                {{ strtoupper(substr($article->author->name ?? 'M', 0, 1)) }}
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">{{ $article->author->name ?? 'Redaksi MI Plus' }}</span>
                                <span class="text-[11px] text-slate-500 block">Humas & Dokumentasi MI Plus</span>
                            </div>
                        </div>

                        <!-- Share Buttons Top -->
                        <div class="flex items-center gap-2">
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' - ' . url()->current()) }}" target="_blank"
                                class="p-2 rounded-xl bg-emerald-50 text-emerald-800 hover:bg-emerald-100 transition shadow-sm" title="Bagikan ke WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            </a>
                            <button type="button" @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2500)"
                                class="p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition" title="Salin Tautan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                @if($article->thumbnail)
                    <div class="rounded-2xl overflow-hidden shadow-sm border border-slate-200">
                        <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}" class="w-full max-h-[420px] object-cover">
                    </div>
                @endif

                <!-- Article Content -->
                <div class="prose prose-slate prose-emerald max-w-none text-slate-700 text-sm sm:text-base leading-relaxed space-y-4">
                    {!! nl2br(e($article->content)) !!}
                </div>

                <!-- Share Box Bottom -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="space-y-0.5 text-center sm:text-left">
                        <span class="text-xs font-bold text-slate-900 block">Bagikan Kabar Ini</span>
                        <p class="text-xs text-slate-500">Bantu sebarkan informasi bermanfaat kepada wali murid dan masyarakat.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' - ' . url()->current()) }}" target="_blank"
                            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 transition shadow-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            <span>Bagikan via WhatsApp</span>
                        </a>

                        <button type="button" @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2500)"
                            class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center gap-2 transition">
                            <span x-show="!copied">Salin Link</span>
                            <span x-show="copied" x-cloak class="text-emerald-700 font-bold">✓ Tersalin!</span>
                        </button>
                    </div>
                </div>

                <!-- Back to News -->
                <div class="pt-4">
                    <a href="{{ route('public.news') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-900 hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Kembali ke Pusat Kabar & Galeri</span>
                    </a>
                </div>

            </article>

            <!-- SIDEBAR (4 COLS) -->
            <aside class="lg:col-span-4 space-y-6">

                <!-- Recent News Widget -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Kabar Terbaru Lainnya</h3>
                        <a href="{{ route('public.news', ['tab' => 'berita']) }}" class="text-[11px] font-bold text-emerald-800 hover:underline">Semua</a>
                    </div>

                    <div class="space-y-4">
                        @forelse($recentArticles as $recent)
                            <a href="{{ route('public.news.article', $recent->slug) }}" class="flex items-start gap-3 group">
                                <div class="w-16 h-16 rounded-xl bg-emerald-950 overflow-hidden shrink-0">
                                    @if($recent->thumbnail)
                                        <img src="{{ asset('storage/' . $recent->thumbnail) }}" alt="{{ $recent->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-emerald-900 to-emerald-950 flex items-center justify-center text-gold-400">
                                            <svg class="w-6 h-6 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-mono text-slate-400">{{ $recent->published_at ? $recent->published_at->translatedFormat('d M Y') : $recent->created_at->translatedFormat('d M Y') }}</span>
                                    <h4 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 transition line-clamp-2 leading-snug">
                                        {{ $recent->title }}
                                    </h4>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs text-slate-400">Tidak ada kabar lainnya.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Announcements Widget -->
                @if($recentAnnouncements->isNotEmpty())
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pengumuman Penting</h3>
                            <a href="{{ route('public.news', ['tab' => 'pengumuman']) }}" class="text-[11px] font-bold text-emerald-800 hover:underline">Semua</a>
                        </div>

                        <div class="space-y-3">
                            @foreach($recentAnnouncements as $ann)
                                <a href="{{ route('public.news.announcement', $ann) }}" class="block p-3 rounded-2xl bg-slate-50 hover:bg-emerald-50/50 transition border border-slate-100">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block mb-1">
                                        {{ $ann->target_audience->label() }}
                                    </span>
                                    <h4 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug">
                                        {{ $ann->title }}
                                    </h4>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- PPDB Online Card CTA -->
                <div class="rounded-3xl bg-gradient-to-br from-emerald-950 to-emerald-900 p-6 text-white text-center border border-gold-500/30 space-y-3 shadow-md">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gold-400 block">Penerimaan Siswa Baru (PPDB)</span>
                    <h3 class="text-lg font-extrabold text-white">Daftarkan Ananda ke MI Plus Sekarang</h3>
                    <p class="text-xs text-emerald-200">Kurikulum terpadu Kemenag, Kurikulum Merdeka, dan Tahfidz Yanbu'a.</p>
                    <div class="pt-2">
                        <a href="/ppdb/daftar" class="inline-block w-full py-2.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-xs shadow transition">
                            Formulir PPDB Online →
                        </a>
                    </div>
                </div>

            </aside>

        </div>

    </div>
</div>
@endsection
