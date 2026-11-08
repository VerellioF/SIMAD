@extends('layouts.public')

@section('title', 'Beranda')

@section('content')

    <!-- HERO SECTION (Islamic Arch Aesthetic) -->
    <section class="relative bg-emerald-900 text-white overflow-hidden pt-12 pb-24 pattern-overlay">
        <!-- Subtle Decorative Arch Lights -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-800/60 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 -right-24 w-96 h-96 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Text Intro -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-800/80 border border-gold-500/40 text-xs font-semibold text-gold-400">
                        <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
                        <span>Penerimaan Peserta Didik Baru (PPDB) 2026/2027 Telah Dibuka</span>
                    </div>

                    <p class="font-arabic text-2xl lg:text-3xl text-gold-400 font-bold tracking-wide">
                        بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                    </p>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        Membina Generasi <span class="text-gold-400 underline decoration-gold-500/60 decoration-wavy underline-offset-8">Qur'ani</span>, Berakhlak Mulia & Berprestasi
                    </h1>

                    <p class="text-emerald-100/90 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Selamat datang di portal resmi <strong>MI Plus Raudlatul Muta'allimin Kalipucung, Blitar</strong>. Madrasah ibtidaiyah terpadu yang memadukan keunggulan akademik, tahfidz Juz 'Amma, dan keteladanan salafus sholih.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="/ppdb/daftar" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-base shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            <span>Daftar PPDB Online</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="/ppdb/cek-status" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700/80 text-white font-semibold text-base border border-emerald-600/80 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Cek Status Pendaftaran</span>
                        </a>
                    </div>
                </div>

                <!-- Hero Visual (Islamic Arch Frame) -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-md">
                        <div class="absolute inset-0 bg-gold-500/20 islamic-arch transform rotate-3 scale-95 -z-10"></div>
                        <div class="bg-gradient-to-b from-emerald-800 to-emerald-950 p-3 islamic-arch border-2 border-gold-500/50 shadow-2xl">
                            <div class="islamic-arch overflow-hidden bg-emerald-950/80 aspect-[4/5] flex flex-col justify-end p-6 text-white relative">
                                <div class="absolute inset-0 bg-gradient-to-t from-emerald-950 via-emerald-950/40 to-transparent"></div>
                                <div class="relative z-10 space-y-2">
                                    <span class="inline-block px-3 py-1 rounded-md bg-gold-500 text-emerald-950 text-xs font-bold uppercase tracking-wider">Tahfidz & Karakter</span>
                                    <h3 class="text-xl font-bold text-white">Lingkungan Belajar Asri & Islami</h3>
                                    <p class="text-xs text-emerald-200">Mencetak santri cerdas, santun, dan hafal Juz 30.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PRAYER TIMES WIDGET RIBBON -->
    <section class="px-4 sm:px-6 lg:px-8 mb-16">
        <x-prayer-times :schedule="$prayerSchedule" />
    </section>

    <!-- STATS COUNTER -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-y md:divide-y-0 md:divide-x divide-slate-100">
            <div class="p-4">
                <span class="block text-3xl lg:text-4xl font-extrabold text-emerald-900 font-mono">350+</span>
                <span class="block text-xs sm:text-sm font-semibold text-slate-500 mt-1 uppercase tracking-wider">Santri Aktif</span>
            </div>
            <div class="p-4">
                <span class="block text-3xl lg:text-4xl font-extrabold text-emerald-900 font-mono">100%</span>
                <span class="block text-xs sm:text-sm font-semibold text-slate-500 mt-1 uppercase tracking-wider">Lulus Ujian Kemenag</span>
            </div>
            <div class="p-4">
                <span class="block text-3xl lg:text-4xl font-extrabold text-emerald-900 font-mono">25+</span>
                <span class="block text-xs sm:text-sm font-semibold text-slate-500 mt-1 uppercase tracking-wider">Guru & Asatidz</span>
            </div>
            <div class="p-4">
                <span class="block text-3xl lg:text-4xl font-extrabold text-emerald-900 font-mono">Juz 30</span>
                <span class="block text-xs sm:text-sm font-semibold text-slate-500 mt-1 uppercase tracking-wider">Target Tahfidz Lulus</span>
            </div>
        </div>
    </section>

    <!-- PROGRAM UNGGULAN -->
    <section id="program" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-24">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-gold-600 bg-gold-500/10 px-3 py-1 rounded-full border border-gold-500/20">Kurikulum Unggulan</span>
            <h2 class="text-3xl font-extrabold text-emerald-950">Empat Pilar Pendidikan MI Plus</h2>
            <p class="text-slate-600 text-sm sm:text-base">Perpaduan seimbang antara kurikulum standar Kemenag dengan pembiasaan adab dan tradisi pesantren.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Card 1 -->
            <div class="bg-white rounded-2xl p-7 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-gold-500/50 transition-all group">
                <div class="w-14 h-14 rounded-xl bg-emerald-50 text-emerald-900 flex items-center justify-center mb-6 group-hover:bg-emerald-900 group-hover:text-gold-400 transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-800 mb-2">Tahfidz & Tartil Qur'an</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Bimbingan hafalan Juz 30 berjenjang dengan mutaba'ah harian, hukum tajwid, makharijul huruf, dan wisuda tahfidz.</p>
            </div>

            <!-- Card 2 -->
            <div class="bg-white rounded-2xl p-7 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-gold-500/50 transition-all group">
                <div class="w-14 h-14 rounded-xl bg-emerald-50 text-emerald-900 flex items-center justify-center mb-6 group-hover:bg-emerald-900 group-hover:text-gold-400 transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-800 mb-2">Pembiasaan Ibadah Fardhu & Dhuha</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Sholat dhuha berjamaah setiap pagi, pembacaan Asmaul Husna, sholat dzuhur berjamaah, serta doa harian terstruktur.</p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-2xl p-7 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-gold-500/50 transition-all group">
                <div class="w-14 h-14 rounded-xl bg-emerald-50 text-emerald-900 flex items-center justify-center mb-6 group-hover:bg-emerald-900 group-hover:text-gold-400 transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-800 mb-2">Hadrah & Sholawat Rebana</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Pengembangan bakat seni hadrah rebana, seni suara islami, dan pentas kreasi santri di berbagai event Kemenag dan umum.</p>
            </div>

            <!-- Card 4 -->
            <div class="bg-white rounded-2xl p-7 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-gold-500/50 transition-all group">
                <div class="w-14 h-14 rounded-xl bg-emerald-50 text-emerald-900 flex items-center justify-center mb-6 group-hover:bg-emerald-900 group-hover:text-gold-400 transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-800 mb-2">Sains & Kepramukaan</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Eksplorasi matematika & sains madrasah, kegiatan kepramukaan, outbound santri, dan kemandirian sejak dini.</p>
            </div>
        </div>
    </section>

    <!-- GALERI KEGIATAN & BERITA -->
    <section id="galeri" class="bg-slate-100/70 py-20 border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full">Dokumentasi & Kabar</span>
                    <h2 class="text-3xl font-extrabold text-emerald-950 mt-2">Kilas Kegiatan Madrasah</h2>
                </div>
            </div>

            <!-- Galleries Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
                @forelse($galleries as $gallery)
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-slate-200 hover:shadow-md transition group">
                        <div class="h-48 bg-emerald-950 flex items-center justify-center text-emerald-300 text-xs font-mono p-4 text-center">
                            <span>Foto: {{ $gallery->title }}</span>
                        </div>
                        <div class="p-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                                {{ $gallery->category->label() ?? 'Kegiatan' }}
                            </span>
                            <h4 class="font-bold text-slate-800 text-sm mt-2">{{ $gallery->title }}</h4>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-500 text-sm">
                        Belum ada dokumentasi foto galeri yang diunggah.
                    </div>
                @endforelse
            </div>

            <!-- Articles / Berita -->
            @if($articles->isNotEmpty())
                <div class="border-t border-slate-200/80 pt-12">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Warta & Berita Terkini</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($articles as $article)
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-3">
                                <span class="text-xs text-slate-400 font-mono">{{ $article->created_at->translatedFormat('d M Y') }}</span>
                                <h4 class="text-base font-bold text-emerald-950 hover:text-emerald-700 transition line-clamp-2">
                                    {{ $article->title }}
                                </h4>
                                <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ Str::limit(strip_tags($article->content), 120) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- CALL TO ACTION BANNER PPDB -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-24">
        <div class="bg-gradient-to-r from-emerald-950 via-emerald-900 to-emerald-950 rounded-3xl p-8 sm:p-14 text-white relative overflow-hidden shadow-2xl border border-gold-500/40">
            <div class="max-w-2xl space-y-6 relative z-10 text-center sm:text-left">
                <span class="px-3.5 py-1.5 rounded-full bg-gold-500 text-emerald-950 text-xs font-bold uppercase tracking-wider inline-block">
                    Penerimaan Siswa Baru
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                    Daftarkan Putra-Putri Anda di MI Plus Raudlatul Muta'allimin
                </h2>
                <p class="text-emerald-200 text-sm sm:text-base leading-relaxed">
                    Pendaftaran dapat dilakukan secara mandiri dari rumah melalui formulir PPDB online. Unduh bukti formulir pendaftaran resmi berformat PDF dan pantau status seleksi secara berkala.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-4 pt-2">
                    <a href="/ppdb/daftar" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-base shadow-lg transition-all text-center">
                        Daftar PPDB Sekarang
                    </a>
                    <a href="/ppdb/cek-status" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-emerald-900 hover:bg-emerald-850 text-emerald-100 font-semibold text-base border border-emerald-700 transition-all text-center">
                        Cek Status Registrasi
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
