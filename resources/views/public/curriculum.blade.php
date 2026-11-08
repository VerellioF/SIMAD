@extends('layouts.public')

@section('title', 'Kurikulum Terpadu Madrasah')

@section('content')
<!-- Header Hero -->
<section class="bg-gradient-to-b from-emerald-950 via-emerald-900 to-emerald-850 text-white py-16 relative overflow-hidden">
    <div class="absolute inset-0 pattern-overlay opacity-10"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gold-500/20 text-gold-400 border border-gold-500/30 text-xs font-semibold uppercase tracking-wider mb-3">
            Integrasi 3 Pilar Kurikulum
        </span>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight">Kurikulum Terpadu Madrasah</h1>
        <p class="mt-4 text-emerald-200 text-sm sm:text-base max-w-2xl mx-auto font-light leading-relaxed">
            Memadukan Kurikulum Merdeka Nasional, Kurikulum Keagamaan Kemenag RI, serta Muatan Lokal dan Kepesantrenan Plus Salafus Sholih.
        </p>
    </div>
</section>

<!-- Main Overview Pillars -->
<section class="py-14 bg-ivory">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- 3 Pilar Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Pilar 1 -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-emerald-900/10 hover:shadow-lg transition">
                <div class="w-12 h-12 rounded-2xl bg-emerald-900 text-gold-400 flex items-center justify-center font-bold text-xl mb-4">
                    1
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-full">Kemenag RI</span>
                <h3 class="text-lg font-bold text-slate-900 mt-2">Pendidikan Agama Islam (PAI) Terpadu</h3>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Pembelajaran mendalam Al-Qur'an Hadits, Aqidah Akhlak, Fiqih Ibadah, Sejarah Kebudayaan Islam (SKI), dan Bahasa Arab Komunikatif.
                </p>
            </div>

            <!-- Pilar 2 -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-emerald-900/10 hover:shadow-lg transition">
                <div class="w-12 h-12 rounded-2xl bg-emerald-900 text-gold-400 flex items-center justify-center font-bold text-xl mb-4">
                    2
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 bg-teal-50 px-2.5 py-1 rounded-full">Kemendikbudristek</span>
                <h3 class="text-lg font-bold text-slate-900 mt-2">Kurikulum Merdeka Nasional</h3>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Fokus pada materi esensial, literasi, numerasi (Matematika Nalaria Realistik), IPAS saintifik, Bahasa Inggris dasar, dan Projek Penguatan Profil Pelajar Pancasila (P5-PPRA).
                </p>
            </div>

            <!-- Pilar 3 -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-emerald-900/10 hover:shadow-lg transition">
                <div class="w-12 h-12 rounded-2xl bg-emerald-900 text-gold-400 flex items-center justify-center font-bold text-xl mb-4">
                    3
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 bg-amber-50 px-2.5 py-1 rounded-full">Kekhasan MI Plus</span>
                <h3 class="text-lg font-bold text-slate-900 mt-2">Pesantren & Tahfidz Plus</h3>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Target hafalan Juz 30 dan Juz 29 mutqin, pengenalan aksara Pegon, dasar kitab kuning (Akhlaq & Tajwid), serta amaliah Aswaja An-Nahdliyyah.
                </p>
            </div>
        </div>

        <!-- Filter Fase Jenjang -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Fase Pembelajaran:</span>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('public.curriculum', ['phase' => 'fase_a']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $selectedPhase === 'fase_a' ? 'bg-emerald-900 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Fase A (Kelas 1 - 2)
                </a>
                <a href="{{ route('public.curriculum', ['phase' => 'fase_b']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $selectedPhase === 'fase_b' ? 'bg-emerald-900 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Fase B (Kelas 3 - 4)
                </a>
                <a href="{{ route('public.curriculum', ['phase' => 'fase_c']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $selectedPhase === 'fase_c' ? 'bg-emerald-900 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Fase C (Kelas 5 - 6)
                </a>
                <a href="{{ route('public.curriculum', ['phase' => 'all']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $selectedPhase === 'all' ? 'bg-emerald-900 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Jenjang
                </a>
            </div>
        </div>

        <!-- Structure Accordions / Groups -->
        <div class="space-y-8">
            @foreach([
                'kemenag_pai' => 'Kelompok Mata Pelajaran PAI & Bahasa Arab (Kemenag)',
                'nasional_merdeka' => 'Kelompok Mata Pelajaran Umum (Kurikulum Merdeka)',
                'muatan_lokal_pesantren' => 'Kelompok Muatan Lokal & Kepesantrenan Plus'
            ] as $typeKey => $typeTitle)
                @php
                    $groupItems = $curriculums->get($typeKey, collect());
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full
                                @if($typeKey === 'kemenag_pai') bg-emerald-600
                                @elseif($typeKey === 'nasional_merdeka') bg-teal-600
                                @else bg-amber-500 @endif"></span>
                            <h3 class="font-bold text-sm sm:text-base text-slate-800">{{ $typeTitle }}</h3>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-500">{{ $groupItems->sum('hours_per_week') }} JP / Minggu</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($groupItems as $item)
                        <div class="p-6 hover:bg-slate-50/50 transition space-y-2">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-extrabold text-base text-slate-900">{{ $item->name }}</h4>
                                    @if($item->code)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-mono text-slate-500 font-bold">{{ $item->code }}</span>
                                    @endif
                                </div>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-850 font-bold text-xs font-mono self-start sm:self-auto">
                                    {{ $item->hours_per_week }} Jam Pelajaran (JP)
                                </span>
                            </div>

                            @if($item->competency_goals)
                                <div class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                    <strong class="text-slate-800 block mb-0.5">Target Capaian Pembelajaran:</strong>
                                    {{ $item->competency_goals }}
                                </div>
                            @endif

                            @if($item->syllabus_summary)
                                <p class="text-xs text-slate-500 mt-1">
                                    <span class="font-semibold text-slate-700">Materi Pokok:</span> {{ $item->syllabus_summary }}
                                </p>
                            @endif
                        </div>
                        @empty
                        <p class="p-6 text-center text-slate-400 text-xs italic">Belum ada mata pelajaran yang terdaftar pada kelompok ini.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Download Info Banner -->
        <div class="bg-gradient-to-r from-emerald-950 via-emerald-900 to-emerald-950 text-white rounded-3xl p-8 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl border border-gold-500/30">
            <div class="space-y-2 text-center sm:text-left">
                <span class="text-xs font-bold uppercase tracking-wider text-gold-400">Pendaftaran Peserta Didik Baru</span>
                <h3 class="text-xl sm:text-2xl font-bold">Ingin Putra-Putri Anda Belajar di MI Plus?</h3>
                <p class="text-xs text-emerald-200">Daftarkan segera secara online atau konsultasi program kurikulum langsung ke kantor madrasah.</p>
            </div>
            <a href="/ppdb/daftar" class="px-7 py-3 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-xs sm:text-sm shadow-md transition whitespace-nowrap">
                Daftar PPDB Online Sekarang
            </a>
        </div>

    </div>
</section>
@endsection
