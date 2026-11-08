@extends('layouts.public')

@section('title', 'Kabar, Pengumuman & Galeri Madrasah')

@section('content')
<div class="bg-slate-50 py-12" x-data="{
    activeTab: '{{ $tab }}',
    lightboxOpen: false,
    activeImage: '',
    activeTitle: '',
    activeCategory: '',
    openLightbox(src, title, category) {
        this.activeImage = src;
        this.activeTitle = title;
        this.activeCategory = category;
        this.lightboxOpen = true;
    }
}" @keydown.escape.window="lightboxOpen = false">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        <!-- Header Hero Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-950 via-[#043327] to-emerald-950 p-8 sm:p-12 text-white shadow-xl border-2 border-gold-500/40 pattern-islamic-dark">
            <!-- Hanging Lantern Accent -->
            <div class="hidden sm:block absolute top-0 right-10 w-16 pointer-events-none opacity-80">
                <img src="{{ asset('images/ornaments/hanging-lantern.svg') }}" class="w-full h-auto drop-shadow-[0_8px_16px_rgba(251,191,36,0.3)]" alt="">
            </div>

            <div class="relative z-10 max-w-3xl space-y-3">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-gold-500/20 text-gold-400 border border-gold-500/30 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <span>Pusat Informasi & Dokumentasi</span>
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                    Kabar, Pengumuman & Galeri Madrasah
                </h1>
                <p class="text-emerald-100/90 text-sm sm:text-base leading-relaxed">
                    Ikuti perkembangan terkini, prestasi siswa-siswi, pengumuman resmi madrasah, dan dokumentasi kegiatan pembelajaran di MI Plus Raudlatul Muta'allimin.
                </p>
            </div>
            <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-gold-500/10 blur-2xl pointer-events-none"></div>
        </div>

        <!-- Filter & Search Bar Container -->
        <div class="bg-white rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <!-- Tab Selector Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="activeTab = 'all'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5"
                        :class="activeTab === 'all' ? 'bg-emerald-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                        <span>Semua</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'all' ? 'bg-emerald-800 text-gold-300' : 'bg-slate-200 text-slate-600'">
                            {{ $articles->count() + $announcements->count() + $galleries->count() }}
                        </span>
                    </button>

                    <button type="button" @click="activeTab = 'berita'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5"
                        :class="activeTab === 'berita' ? 'bg-emerald-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                        <span>Berita & Warta</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'berita' ? 'bg-emerald-800 text-gold-300' : 'bg-slate-200 text-slate-600'">
                            {{ $articles->count() }}
                        </span>
                    </button>

                    <button type="button" @click="activeTab = 'pengumuman'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5"
                        :class="activeTab === 'pengumuman' ? 'bg-emerald-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                        <span>Pengumuman Resmi</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'pengumuman' ? 'bg-emerald-800 text-gold-300' : 'bg-slate-200 text-slate-600'">
                            {{ $announcements->count() }}
                        </span>
                    </button>

                    <button type="button" @click="activeTab = 'galeri'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5"
                        :class="activeTab === 'galeri' ? 'bg-emerald-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                        <span>Galeri Foto</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'galeri' ? 'bg-emerald-800 text-gold-300' : 'bg-slate-200 text-slate-600'">
                            {{ $galleries->count() }}
                        </span>
                    </button>
                </div>

                <!-- Search Form -->
                <form action="{{ route('public.news') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="tab" :value="activeTab">
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="q" value="{{ $search }}"
                            placeholder="Cari berita atau info..."
                            class="w-full pl-9 pr-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent">
                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                        Cari
                    </button>
                    @if($search)
                        <a href="{{ route('public.news', ['tab' => $tab]) }}" class="px-2 py-2 text-slate-400 hover:text-slate-600 text-xs">Reset</a>
                    @endif
                </form>

            </div>

            <!-- Optional Subcategory for Gallery -->
            <div x-show="activeTab === 'galeri' || activeTab === 'all'" x-transition class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-1.5">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-2">Kategori Foto:</span>
                <a href="{{ route('public.news', array_filter(['tab' => 'galeri', 'q' => $search])) }}"
                    class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ empty($category) ? 'bg-emerald-100 text-emerald-900' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Foto
                </a>
                @foreach($galleryCategories as $cat)
                    <a href="{{ route('public.news', array_filter(['tab' => 'galeri', 'kategori' => $cat->value, 'q' => $search])) }}"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $category === $cat->value ? 'bg-emerald-100 text-emerald-900 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $cat->label() }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- SECTION 1: BERITA & WARTA (Show if activeTab is 'all' or 'berita') -->
        <div x-show="activeTab === 'all' || activeTab === 'berita'" x-transition class="space-y-6">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-emerald-950">Berita & Warta Madrasah</h2>
                </div>
                <span class="text-xs text-slate-500 font-medium">{{ $articles->count() }} Kabar Diterbitkan</span>
            </div>

            @if($articles->isNotEmpty())
                <!-- Featured Article Hero Card (Only on 'all' or 'berita' if available) -->
                @if($featuredArticle && empty($search))
                    <div class="bg-white rounded-3xl overflow-hidden shadow-md border border-slate-200/80 grid grid-cols-1 lg:grid-cols-12 group hover:shadow-lg transition">
                        <div class="lg:col-span-5 bg-emerald-950 min-h-[220px] relative overflow-hidden flex items-center justify-center">
                            @if($featuredArticle->thumbnail)
                                <img src="{{ asset('storage/' . $featuredArticle->thumbnail) }}" alt="{{ $featuredArticle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-emerald-900 to-emerald-950 p-8 flex flex-col justify-between text-gold-400">
                                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-300">Sorotan Utama</span>
                                    <svg class="w-16 h-16 opacity-30 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                    <span class="text-xs text-emerald-200/80">MI Plus Raudlatul Muta'allimin</span>
                                </div>
                            @endif
                        </div>
                        <div class="lg:col-span-7 p-6 sm:p-8 flex flex-col justify-between space-y-4">
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 text-xs text-slate-400">
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 font-bold uppercase text-[10px]">Sorotan Kabar</span>
                                    <span class="font-mono">{{ $featuredArticle->published_at ? $featuredArticle->published_at->translatedFormat('d F Y') : $featuredArticle->created_at->translatedFormat('d F Y') }}</span>
                                    <span>•</span>
                                    <span>{{ $featuredArticle->author->name ?? 'Redaksi MI Plus' }}</span>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 group-hover:text-emerald-800 transition leading-snug">
                                    <a href="{{ route('public.news.article', $featuredArticle->slug) }}">
                                        {{ $featuredArticle->title }}
                                    </a>
                                </h3>
                                <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                                    {{ Str::limit(strip_tags($featuredArticle->content), 180) }}
                                </p>
                            </div>
                            <div class="pt-2">
                                <a href="{{ route('public.news.article', $featuredArticle->slug) }}"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs transition shadow-sm">
                                    <span>Baca Berita Selengkapnya</span>
                                    <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Articles Grid (Remaining articles) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($articles as $article)
                        @if(!$loop->first || $search || $tab === 'berita')
                            <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-200/80 hover:shadow-md transition flex flex-col justify-between group">
                                <div>
                                    <div class="h-44 bg-emerald-950 overflow-hidden relative">
                                        @if($article->thumbnail)
                                            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-tr from-emerald-900 to-emerald-950 p-4 flex flex-col justify-between text-white">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-gold-400">Warta Madrasah</span>
                                                <svg class="w-8 h-8 opacity-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-6 space-y-2.5">
                                        <div class="flex items-center gap-2 text-[11px] text-slate-400 font-mono">
                                            <span>{{ $article->published_at ? $article->published_at->translatedFormat('d M Y') : $article->created_at->translatedFormat('d M Y') }}</span>
                                            <span>•</span>
                                            <span class="truncate">{{ $article->author->name ?? 'Redaksi' }}</span>
                                        </div>
                                        <h4 class="font-bold text-slate-900 text-base group-hover:text-emerald-800 transition line-clamp-2 leading-snug">
                                            <a href="{{ route('public.news.article', $article->slug) }}">
                                                {{ $article->title }}
                                            </a>
                                        </h4>
                                        <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed">
                                            {{ Str::limit(strip_tags($article->content), 120) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="px-6 pb-6 pt-0">
                                    <a href="{{ route('public.news.article', $article->slug) }}"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-850 hover:text-emerald-950 group-hover:underline">
                                        <span>Baca Selengkapnya</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 text-slate-400">
                    <p class="text-sm">Belum ada warta berita yang sesuai dengan kriteria pencarian.</p>
                </div>
            @endif
        </div>

        <!-- SECTION 2: PENGUMUMAN RESMI (Show if activeTab is 'all' or 'pengumuman') -->
        <div x-show="activeTab === 'all' || activeTab === 'pengumuman'" x-transition class="space-y-6">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-emerald-950">Pengumuman & Edaran Resmi</h2>
                </div>
                <span class="text-xs text-slate-500 font-medium">{{ $announcements->count() }} Pengumuman Aktif</span>
            </div>

            @if($announcements->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($announcements as $announcement)
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 hover:shadow-md transition flex flex-col justify-between space-y-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
                                        Sasaran: {{ $announcement->target_audience->label() }}
                                    </span>
                                    <span class="text-[11px] font-mono text-slate-400">
                                        {{ $announcement->created_at->translatedFormat('d M Y') }}
                                    </span>
                                </div>

                                <h3 class="font-bold text-slate-900 text-base hover:text-emerald-800 transition leading-snug">
                                    <a href="{{ route('public.news.announcement', $announcement) }}">
                                        {{ $announcement->title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ Str::limit(strip_tags($announcement->content), 130) }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                @if($announcement->valid_until)
                                    <span class="text-[10px] text-slate-400">
                                        Berlaku s.d: <strong class="text-slate-600 font-mono">{{ $announcement->valid_until->translatedFormat('d M Y') }}</strong>
                                    </span>
                                @else
                                    <span class="text-[10px] text-emerald-700 font-semibold">Berlaku Seterusnya</span>
                                @endif

                                <a href="{{ route('public.news.announcement', $announcement) }}"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-emerald-850 hover:underline">
                                    <span>Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 text-slate-400">
                    <p class="text-sm">Tidak ada pengumuman resmi yang aktif saat ini.</p>
                </div>
            @endif
        </div>

        <!-- SECTION 3: GALERI FOTO KEGIATAN (Show if activeTab is 'all' or 'galeri') -->
        <div x-show="activeTab === 'all' || activeTab === 'galeri'" x-transition class="space-y-6">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-gold-500"></span>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-emerald-950">Dokumentasi & Galeri Kegiatan</h2>
                </div>
                <span class="text-xs text-slate-500 font-medium">{{ $galleries->count() }} Dokumentasi Foto</span>
            </div>

            @if($galleries->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($galleries as $gallery)
                        <div @click="openLightbox('{{ asset($gallery->image_path) }}', '{{ addslashes($gallery->title) }}', '{{ $gallery->category->label() }}')"
                            class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-200/80 hover:shadow-lg transition cursor-pointer group">
                            <div class="h-56 bg-slate-900 overflow-hidden relative">
                                <img src="{{ asset($gallery->image_path) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                                    <span class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur text-white text-xs font-bold flex items-center gap-1.5 shadow">
                                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                        <span>Perbesar Foto</span>
                                    </span>
                                </div>
                            </div>
                            <div class="p-5">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-900 border border-emerald-200">
                                        {{ $gallery->category->label() }}
                                    </span>
                                    <span class="text-[11px] font-mono text-slate-400">{{ $gallery->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                                <h4 class="font-bold text-slate-800 text-sm group-hover:text-emerald-800 transition line-clamp-2 leading-snug">
                                    {{ $gallery->title }}
                                </h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 text-slate-400">
                    <p class="text-sm">Belum ada foto galeri untuk kategori yang dipilih.</p>
                </div>
            @endif
        </div>

    </div>

    <!-- LIGHTBOX MODAL DIALOG (Alpine.js) -->
    <div x-show="lightboxOpen" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm"
        @click.self="lightboxOpen = false">

        <div class="relative max-w-4xl w-full bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-white/10" @click.stop>
            <!-- Close Button -->
            <button type="button" @click="lightboxOpen = false"
                class="absolute top-4 right-4 z-10 p-2.5 rounded-full bg-black/50 hover:bg-black/80 text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Image Container -->
            <div class="max-h-[70vh] flex items-center justify-center bg-black/40 overflow-hidden">
                <img :src="activeImage" :alt="activeTitle" class="max-h-[70vh] w-auto max-w-full object-contain">
            </div>

            <!-- Caption / Footer -->
            <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-t border-slate-800">
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-gold-500/20 text-gold-400 text-[10px] font-bold uppercase tracking-wider mb-1" x-text="activeCategory"></span>
                    <h3 class="text-base sm:text-lg font-bold text-white" x-text="activeTitle"></h3>
                </div>
                <button type="button" @click="lightboxOpen = false" class="px-5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
