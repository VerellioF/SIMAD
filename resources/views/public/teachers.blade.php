@extends('layouts.public')

@section('title', 'Dewan Guru & Pimpinan Madrasah')

@section('content')
<!-- Header Hero -->
<section class="bg-gradient-to-b from-emerald-950 via-emerald-900 to-emerald-850 text-white py-16 relative overflow-hidden">
    <div class="absolute inset-0 pattern-overlay opacity-10"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gold-500/20 text-gold-400 border border-gold-500/30 text-xs font-semibold uppercase tracking-wider mb-3">
            Keluarga Besar Tenaga Pendidik
        </span>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight">Dewan Guru & Pimpinan Sekolah <span class="sr-only">Dewan Asatidz & Pimpinan</span></h1>
        <p class="text-xs text-gold-400 font-semibold tracking-wider uppercase mt-2">Dewan Guru & Pimpinan MI Plus Raudlatul Muta'allimin</p>
        <p class="mt-4 text-emerald-200 text-sm sm:text-base max-w-2xl mx-auto font-light">
            Mengenal bapak dan ibu guru pendidik yang berdedikasi tinggi, ramah, telaten, berakhlak mulia, dan berkompetensi di bidang ilmu umum serta keagamaan Islam.
        </p>
    </div>
</section>

<!-- Content Body -->
<section class="py-16 bg-ivory">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        
        <!-- Pimpinan & Direksi -->
        @if($leadership->isNotEmpty())
        <div>
            <div class="text-center max-w-xl mx-auto mb-10">
                <span class="text-xs font-bold text-emerald-850 uppercase tracking-widest">Kepemimpinan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-emerald-950 mt-1">Pimpinan & Kepala Madrasah</h2>
                <div class="w-16 h-1 bg-gold-500 mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-center">
                @foreach($leadership as $leader)
                <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl border border-emerald-900/10 transition-all duration-300 text-center group">
                    <div class="w-24 h-24 mx-auto rounded-2xl bg-gradient-to-tr from-emerald-900 to-emerald-700 flex items-center justify-center text-white text-3xl font-extrabold shadow-md mb-4 group-hover:scale-105 transition-transform">
                        {{ strtoupper(substr($leader->name, 0, 2)) }}
                    </div>
                    <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-850 font-bold text-xs mb-2">
                        {{ $leader->position ?? ($leader->isAdmin() ? 'Pimpinan Yayasan / Tata Usaha' : 'Kepala Madrasah') }}
                    </span>
                    <h3 class="text-lg font-bold text-slate-800">{{ $leader->name }}</h3>
                    @if($leader->education)
                        <p class="text-xs font-semibold text-gold-600 mt-1">{{ $leader->education }}</p>
                    @endif
                    <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                        {{ $leader->bio ?? 'Berdedikasi dalam mendidik dan membimbing siswa-siswi dengan keteladanan akhlaqul karimah dan wawasan keilmuan kontemporer.' }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Dewan Guru -->
        <div>
            <div class="text-center max-w-xl mx-auto mb-10">
                <span class="text-xs font-bold text-emerald-850 uppercase tracking-widest">Tenaga Pendidik</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-emerald-950 mt-1">Dewan Bapak & Ibu Guru Pengajar</h2>
                <div class="w-16 h-1 bg-gold-500 mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($teachers as $teacher)
                <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md border border-slate-100 transition text-center flex flex-col justify-between">
                    <div>
                        <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 text-emerald-900 flex items-center justify-center text-xl font-bold mb-3 shadow-inner">
                            {{ strtoupper(substr($teacher->name, 0, 2)) }}
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm leading-snug">{{ $teacher->name }}</h4>
                        <p class="text-xs text-emerald-700 font-medium mt-1">{{ $teacher->position ?? 'Guru Mata Pelajaran' }}</p>
                        @if($teacher->education)
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $teacher->education }}</p>
                        @endif
                    </div>
                    @if($teacher->bio)
                        <p class="text-[11px] text-slate-500 mt-3 italic line-clamp-2">"{{ $teacher->bio }}"</p>
                    @endif
                </div>
                @empty
                <div class="col-span-4 text-center py-10 bg-white rounded-2xl border border-slate-100 text-slate-500 text-sm">
                    Belum ada data dewan guru yang ditampilkan.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</section>
@endsection
