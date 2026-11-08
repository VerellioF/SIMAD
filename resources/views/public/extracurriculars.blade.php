@extends('layouts.public')

@section('title', 'Ekstrakurikuler Unggulan')

@section('content')
<!-- Header Hero -->
<section class="bg-gradient-to-b from-emerald-950 via-emerald-900 to-emerald-850 text-white py-16 relative overflow-hidden">
    <div class="absolute inset-0 pattern-overlay opacity-10"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gold-500/20 text-gold-400 border border-gold-500/30 text-xs font-semibold uppercase tracking-wider mb-3">
            Minat & Bakat Siswa-Siswi
        </span>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight">Ekstrakurikuler Unggulan</h1>
        <p class="mt-4 text-emerald-200 text-sm sm:text-base max-w-2xl mx-auto font-light">
            Wadah pengembangan potensi spiritual, kepemimpinan, olahraga, seni Islam, dan kreativitas ilmiah bagi siswa-siswi MI Plus Raudlatul Muta'allimin.
        </p>
    </div>
</section>

<!-- Ekstrakurikuler Grid -->
<section class="py-14 bg-ivory">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Filter Kategori -->
        <div class="flex items-center justify-center gap-2 flex-wrap">
            <a href="{{ route('public.ekskul', ['category' => 'all']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('category') || request('category') == 'all' ? 'bg-emerald-900 text-white shadow' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">Semua Kategori</a>
            <a href="{{ route('public.ekskul', ['category' => 'keagamaan']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('category') == 'keagamaan' ? 'bg-emerald-900 text-white shadow' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">Keagamaan & Tahfidz</a>
            <a href="{{ route('public.ekskul', ['category' => 'seni_budaya']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('category') == 'seni_budaya' ? 'bg-emerald-900 text-white shadow' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">Seni & Budaya Islam</a>
            <a href="{{ route('public.ekskul', ['category' => 'olahraga']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('category') == 'olahraga' ? 'bg-emerald-900 text-white shadow' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">Olahraga</a>
            <a href="{{ route('public.ekskul', ['category' => 'kepanduan']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('category') == 'kepanduan' ? 'bg-emerald-900 text-white shadow' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">Pramuka & Kepanduan</a>
            <a href="{{ route('public.ekskul', ['category' => 'sains']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('category') == 'sains' ? 'bg-emerald-900 text-white shadow' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">Klub Sains & Riset</a>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($extracurriculars as $ekskul)
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-100 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <!-- Image or Decorative Header -->
                    <div class="h-44 bg-gradient-to-tr from-emerald-900 to-emerald-700 p-6 flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur text-white text-[10px] font-bold uppercase tracking-wider">
                                {{ str_replace('_', ' ', ucfirst($ekskul->category)) }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-gold-500 text-emerald-950 font-extrabold text-[10px]">
                                {{ $ekskul->enrollments_count }} Siswa Aktif
                            </span>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold text-white group-hover:text-gold-400 transition-colors">{{ $ekskul->name }}</h3>
                            <p class="text-xs text-emerald-200 mt-0.5">Pembina: {{ $ekskul->instructor_name }}</p>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-6 space-y-4">
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                            {{ $ekskul->description ?? 'Kegiatan ekstrakurikuler terpadu untuk membentuk karakter siswa-siswi yang berprestasi dan berakhlak mulia.' }}
                        </p>

                        <div class="pt-3 border-t border-slate-100 space-y-2 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ $ekskul->schedule_day }}, {{ $ekskul->schedule_time }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Lokasi: {{ $ekskul->location }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 pb-6 pt-2">
                    <a href="/wali/login" class="block w-full text-center py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-bold text-xs transition border border-emerald-200">
                        Daftarkan Ananda via Portal Wali
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-16 bg-white rounded-3xl border border-slate-100 text-slate-500 text-sm">
                Belum ada kegiatan ekstrakurikuler yang terdaftar pada kategori ini.
            </div>
            @endforelse
        </div>

    </div>
</section>
@endsection
