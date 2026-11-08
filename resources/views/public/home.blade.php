@extends('layouts.public')

@section('title', 'Beranda')

@section('content')

    <!-- HERO SECTION (Islamic Arch Aesthetic) -->
    <section class="relative bg-gradient-to-b from-[#02241b] via-[#043c2e] to-[#021f17] text-white overflow-hidden pt-12 pb-28 pattern-islamic-dark">
        <!-- Hanging Lantern Left -->
        <div class="absolute top-0 left-4 sm:left-10 w-16 sm:w-20 pointer-events-none z-20 opacity-85">
            <img src="{{ asset('images/ornaments/hanging-lantern.svg') }}" class="w-full h-auto drop-shadow-[0_12px_24px_rgba(251,191,36,0.3)]" alt="Lentera Islami">
        </div>

        <!-- Hanging Lantern Right -->
        <div class="hidden lg:block absolute top-0 right-14 w-16 pointer-events-none z-20 opacity-75">
            <img src="{{ asset('images/ornaments/hanging-lantern.svg') }}" class="w-full h-auto drop-shadow-[0_12px_24px_rgba(251,191,36,0.25)]" alt="Lentera Islami">
        </div>

        <!-- Subtle Decorative Arch Ambient Lights -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-700/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 -right-24 w-96 h-96 bg-gold-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Text Intro -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-800/80 border border-gold-500/40 text-xs font-semibold text-gold-400 backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
                        <span>Penerimaan Peserta Didik Baru (PPDB) 2026/2027 Telah Dibuka</span>
                    </div>

                    <div class="space-y-2">
                        <p class="font-arabic text-2xl lg:text-3xl text-gold-400 font-bold tracking-wide">
                            بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                        </p>
                        <div class="flex justify-center lg:justify-start">
                            <img src="{{ asset('images/ornaments/islamic-divider.svg') }}" class="h-4 opacity-80" alt="Pembatas Islami">
                        </div>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        Membina Generasi <span class="text-gold-400 underline decoration-gold-500/60 decoration-wavy underline-offset-8">Qur'ani</span>, Berakhlak Mulia & Berprestasi
                    </h1>

                    <p class="text-emerald-100/90 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Selamat datang di portal resmi <strong>MI Plus Raudlatul Muta'allimin Kalipucung, Blitar</strong>. Madrasah ibtidaiyah terpadu yang memadukan keunggulan akademik, tahfidz Juz 'Amma, dan keteladanan salafus sholih.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="/ppdb/daftar" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-base shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 border-2 border-gold-300">
                            <span>Daftar PPDB Online</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="/ppdb/cek-status" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700/80 text-white font-semibold text-base border border-emerald-600/80 transition-all flex items-center justify-center gap-2 backdrop-blur-sm">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Cek Status Pendaftaran</span>
                        </a>
                    </div>
                </div>

                <!-- Hero Visual (Islamic Arch Frame) -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-md">
                        <div class="absolute inset-0 bg-gold-500/20 islamic-arch transform rotate-3 scale-95 -z-10"></div>
                        <div class="bg-gradient-to-b from-emerald-800 to-emerald-950 p-3 islamic-arch border-2 border-gold-500/60 shadow-2xl">
                            <div class="islamic-arch overflow-hidden bg-emerald-950 aspect-[4/5] flex flex-col justify-end p-6 text-white relative">
                                <img src="{{ asset('images/hero-santri.svg') }}" alt="Siswa-Siswi MI Plus Raudlatul Muta'allimin" class="absolute inset-0 w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-emerald-950 via-emerald-950/30 to-transparent"></div>
                                <div class="relative z-10 space-y-2">
                                    <span class="inline-block px-3 py-1 rounded-md bg-gold-500 text-emerald-950 text-xs font-bold uppercase tracking-wider">Tahfidz & Karakter</span>
                                    <h3 class="text-xl font-bold text-white">Lingkungan Belajar Asri & Ramah Anak</h3>
                                    <p class="text-xs text-emerald-200">Membina siswa-siswi cerdas, santun, dan berkarakter Qur'ani.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mosque Skyline Silhouette Divider at Bottom of Hero -->
        <div class="absolute bottom-0 inset-x-0 overflow-hidden leading-none z-10 pointer-events-none opacity-40">
            <img src="{{ asset('images/ornaments/mosque-skyline.svg') }}" class="w-full h-16 sm:h-20 object-cover object-bottom" alt="">
        </div>
    </section>

    <!-- PRAYER TIMES WIDGET RIBBON -->
    <section class="px-4 sm:px-6 lg:px-8 mb-16">
        <x-prayer-times :schedule="$prayerSchedule" />
    </section>

    <!-- STATS COUNTER -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20 relative">
        <div class="bg-gradient-to-r from-white via-[#fbf8ef] to-white rounded-3xl shadow-lg border-2 border-gold-500/30 p-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-y md:divide-y-0 md:divide-x divide-slate-200/80 relative overflow-hidden pattern-islamic-light">
            <div class="p-4 relative z-10">
                <span class="block text-3xl lg:text-4xl font-extrabold text-emerald-900 font-mono">350+</span>
                <span class="block text-xs sm:text-sm font-semibold text-slate-600 mt-1 uppercase tracking-wider">Siswa & Siswi Aktif</span>
            </div>
            <div class="p-4 relative z-10">
                <span class="block text-3xl lg:text-4xl font-extrabold text-emerald-900 font-mono">100%</span>
                <span class="block text-xs sm:text-sm font-semibold text-slate-600 mt-1 uppercase tracking-wider">Lulus Ujian Kemenag</span>
            </div>
            <div class="p-4 relative z-10">
                <span class="block text-3xl lg:text-4xl font-extrabold text-emerald-900 font-mono">25+</span>
                <span class="block text-xs sm:text-sm font-semibold text-slate-600 mt-1 uppercase tracking-wider">Bapak & Ibu Guru</span>
            </div>
            <div class="p-4 relative z-10">
                <span class="block text-3xl lg:text-4xl font-extrabold text-gold-600 font-mono">Juz 30</span>
                <span class="block text-xs sm:text-sm font-semibold text-slate-600 mt-1 uppercase tracking-wider">Target Tahfidz Kelulusan</span>
            </div>
        </div>
    </section>

    <!-- PROFIL MADRASAH & VISI MISI -->
    <section id="profil" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-24" x-data="{ tab: 'visi' }">
        <div class="bg-white rounded-3xl border-2 border-gold-500/20 shadow-md p-8 sm:p-12 overflow-hidden relative pattern-islamic-light">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center relative z-10">
                <div class="lg:col-span-5 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-850 bg-emerald-100 px-3 py-1 rounded-full border border-emerald-200">
                        Profil Lembaga
                    </span>
                    <h2 class="text-3xl font-extrabold text-emerald-950 leading-tight">
                        Mengenal Lebih Dekat MI Plus Raudlatul Muta'allimin
                    </h2>
                    <div>
                        <img src="{{ asset('images/ornaments/islamic-divider.svg') }}" class="h-4 opacity-75" alt="">
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Terletak di Dusun Kalipucung, Sanankulon, Blitar, kami hadir sebagai wadah pendidikan dasar Islam yang mengedepankan pembiasaan ibadah praktis, adab keseharian, serta pencapaian akademik berstandar nasional.
                    </p>
                    
                    <!-- Tab Buttons -->
                    <div class="flex gap-2 pt-2 border-b border-slate-200">
                        <button @click="tab = 'visi'" :class="tab === 'visi' ? 'border-emerald-800 text-emerald-900 font-bold border-b-2' : 'text-slate-500 hover:text-slate-800'" class="pb-2 text-sm transition">
                            Visi & Misi
                        </button>
                        <button @click="tab = 'tujuan'" :class="tab === 'tujuan' ? 'border-emerald-800 text-emerald-900 font-bold border-b-2' : 'text-slate-500 hover:text-slate-800'" class="pb-2 text-sm transition ml-4">
                            Tujuan Lembaga
                        </button>
                        <button @click="tab = 'budaya'" :class="tab === 'budaya' ? 'border-emerald-800 text-emerald-900 font-bold border-b-2' : 'text-slate-500 hover:text-slate-800'" class="pb-2 text-sm transition ml-4">
                            Budaya Madrasah
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-7 bg-gradient-to-br from-emerald-950 via-[#032f23] to-emerald-950 text-white rounded-3xl p-6 sm:p-8 border-2 border-gold-500/40 pattern-islamic-dark relative overflow-hidden shadow-xl">
                    <!-- Corner Flourish -->
                    <div class="absolute -top-2 -right-2 w-16 h-16 pointer-events-none opacity-40">
                        <img src="{{ asset('images/ornaments/corner-arabesque.svg') }}" class="w-full h-full transform rotate-90" alt="">
                    </div>

                    <!-- Tab Visi -->
                    <div x-show="tab === 'visi'" class="space-y-4 relative z-10">
                        <h4 class="text-lg font-bold text-gold-400 flex items-center gap-2">
                            <span>Visi Madrasah</span>
                        </h4>
                        <p class="text-emerald-100 text-sm italic font-serif leading-relaxed pl-3 border-l-2 border-gold-400">
                            "Terwujudnya Siswa-Siswi yang Beriman, Bertaqwa, Berakhlaq Mulia, Unggul dalam Prestasi, dan Berkarakter Qur'ani."
                        </p>
                        <h4 class="text-base font-bold text-gold-400 pt-2">Misi Utama</h4>
                        <ul class="space-y-2 text-xs sm:text-sm text-emerald-200">
                            <li class="flex items-start gap-2">
                                <span class="text-gold-400 font-bold">1.</span>
                                <span>Menumbuhkembangkan pembiasaan ibadah fardhu dan sunnah sejak dini pada peserta didik.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-gold-400 font-bold">2.</span>
                                <span>Melaksanakan program tahfidz Al-Qur'an Juz 30 dengan tartil dan tajwid yang benar.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-gold-400 font-bold">3.</span>
                                <span>Menyelenggarakan pembelajaran saintifik yang memicu rasa ingin tahu, nalar kritis, dan kreativitas siswa.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Tujuan -->
                    <div x-show="tab === 'tujuan'" x-cloak class="space-y-4 relative z-10">
                        <h4 class="text-lg font-bold text-gold-400">Tujuan Pendidikan Madrasah</h4>
                        <ul class="space-y-2 text-xs sm:text-sm text-emerald-200">
                            <li class="flex items-start gap-2">
                                <span class="text-gold-400 font-bold">•</span>
                                <span>Lulusan hafal minimal Juz 30 (Juz 'Amma) dan terbiasa membaca Al-Qur'an setiap hari di rumah.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-gold-400 font-bold">•</span>
                                <span>Mampu mempraktikkan tata cara wudhu dan sholat fardhu secara mandiri, khusyuk, dan tertib.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-gold-400 font-bold">•</span>
                                <span>Memiliki kesiapan akademik dan akhlak untuk melanjutkan ke jenjang MTs/SMP unggulan maupun pondok pesantren.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Budaya -->
                    <div x-show="tab === 'budaya'" x-cloak class="space-y-4 relative z-10">
                        <h4 class="text-lg font-bold text-gold-400">5 Budaya Karakter Siswa Hebat</h4>
                        <div class="grid grid-cols-2 gap-3 text-xs text-emerald-200">
                            <div class="p-2.5 rounded-xl bg-emerald-900/80 border border-emerald-800">
                                <span class="font-bold text-gold-400 block mb-0.5">1. 5S</span>
                                Senyum, Sapa, Salam, Sopan, dan Santun kepada Bapak/Ibu Guru serta orang tua.
                            </div>
                            <div class="p-2.5 rounded-xl bg-emerald-900/80 border border-emerald-800">
                                <span class="font-bold text-gold-400 block mb-0.5">2. Dhuha Berjamaah</span>
                                Mengetuk pintu rezeki berkah dan ketenangan hati setiap pagi sebelum belajar.
                            </div>
                            <div class="p-2.5 rounded-xl bg-emerald-900/80 border border-emerald-800">
                                <span class="font-bold text-gold-400 block mb-0.5">3. Bersih & Rapi</span>
                                Menjaga kebersihan madrasah dan kerapian diri sebagai cerminan iman.
                            </div>
                            <div class="p-2.5 rounded-xl bg-emerald-900/80 border border-emerald-800">
                                <span class="font-bold text-gold-400 block mb-0.5">4. Gemar Berinfaq</span>
                                Melatih empati, kepedulian sosial, dan keikhlasan melalui kotak infaq harian.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PROGRAM UNGGULAN -->
    <section id="program" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-24">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-gold-600 bg-gold-500/10 px-3 py-1 rounded-full border border-gold-500/20">Kurikulum Unggulan</span>
            <h2 class="text-3xl font-extrabold text-emerald-950">Empat Pilar Pendidikan MI Plus</h2>
            <div>
                <img src="{{ asset('images/ornaments/islamic-divider.svg') }}" class="h-4 mx-auto opacity-75" alt="">
            </div>
            <p class="text-slate-600 text-sm sm:text-base">Perpaduan seimbang antara kurikulum standar Kemenag, Kurikulum Merdeka nasional, dan penguatan karakter islami.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Card 1 -->
            <div class="bg-white rounded-3xl p-7 shadow-sm border-2 border-slate-200/80 hover:shadow-xl hover:border-gold-500/60 transition-all group relative overflow-hidden border-t-4 hover:border-t-gold-500">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-900 flex items-center justify-center mb-6 group-hover:bg-emerald-900 group-hover:text-gold-400 transition-colors shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-emerald-900 transition-colors">Tahfidz & Tartil Qur'an</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Bimbingan hafalan Juz 30 berjenjang dengan mutaba'ah harian, hukum tajwid, makharijul huruf, dan wisuda tahfidz kelulusan.</p>
            </div>

            <!-- Card 2 -->
            <div class="bg-white rounded-3xl p-7 shadow-sm border-2 border-slate-200/80 hover:shadow-xl hover:border-gold-500/60 transition-all group relative overflow-hidden border-t-4 hover:border-t-gold-500">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-900 flex items-center justify-center mb-6 group-hover:bg-emerald-900 group-hover:text-gold-400 transition-colors shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-emerald-900 transition-colors">Pembiasaan Ibadah Fardhu & Dhuha</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Sholat dhuha berjamaah setiap pagi, pembacaan Asmaul Husna, sholat dzuhur berjamaah, serta hafalan doa harian terstruktur.</p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-3xl p-7 shadow-sm border-2 border-slate-200/80 hover:shadow-xl hover:border-gold-500/60 transition-all group relative overflow-hidden border-t-4 hover:border-t-gold-500">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-900 flex items-center justify-center mb-6 group-hover:bg-emerald-900 group-hover:text-gold-400 transition-colors shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-emerald-900 transition-colors">Hadrah & Sholawat Rebana</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Pengembangan bakat seni hadrah rebana, seni suara islami, dan pentas kreasi siswa-siswi di berbagai event Kemenag dan umum.</p>
            </div>

            <!-- Card 4 -->
            <div class="bg-white rounded-3xl p-7 shadow-sm border-2 border-slate-200/80 hover:shadow-xl hover:border-gold-500/60 transition-all group relative overflow-hidden border-t-4 hover:border-t-gold-500">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-900 flex items-center justify-center mb-6 group-hover:bg-emerald-900 group-hover:text-gold-400 transition-colors shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-emerald-900 transition-colors">Sains, Literasi & Pramuka</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Eksplorasi matematika nalaria & sains terapan, kepramukaan siaga/penggalang, literasi buku, dan kemandirian sejak dini.</p>
            </div>
        </div>
    </section>

    <!-- GALERI KEGIATAN & BERITA -->
    <section id="galeri" class="bg-parchment pattern-islamic-light py-24 border-y-2 border-gold-500/30 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-850 bg-emerald-100 px-3 py-1 rounded-full border border-emerald-200">Dokumentasi & Kabar</span>
                    <h2 class="text-3xl font-extrabold text-emerald-950 mt-2">Kilas Kegiatan Madrasah</h2>
                    <div class="mt-2">
                        <img src="{{ asset('images/ornaments/islamic-divider.svg') }}" class="h-4 opacity-75" alt="">
                    </div>
                </div>
                <div>
                    <a href="{{ route('public.news') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition border border-gold-500/40">
                        <span>Pusat Kabar & Galeri Lengkap</span>
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

            <!-- Galleries Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
                @forelse($galleries as $gallery)
                    <a href="{{ route('public.news', ['tab' => 'galeri']) }}" class="bg-white rounded-3xl overflow-hidden shadow-sm border-2 border-slate-200/80 hover:shadow-xl hover:border-gold-500/60 transition-all group block">
                        <div class="h-48 bg-emerald-950 overflow-hidden relative">
                            <img src="{{ asset($gallery->image_path) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <div class="p-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                                {{ $gallery->category->label() ?? 'Kegiatan' }}
                            </span>
                            <h4 class="font-bold text-slate-800 text-sm mt-2 group-hover:text-emerald-850 transition">{{ $gallery->title }}</h4>
                        </div>
                    </a>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-500 text-sm">
                        Belum ada dokumentasi foto galeri yang diunggah.
                    </div>
                @endforelse
            </div>

            <!-- Articles / Berita -->
            @if($articles->isNotEmpty())
                <div class="border-t border-slate-200/80 pt-12">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900">Warta & Berita Terkini</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Liputan agenda siswa-siswi, prestasi, dan kegiatan madrasah</p>
                        </div>
                        <a href="{{ route('public.news', ['tab' => 'berita']) }}" class="text-xs font-bold text-emerald-800 hover:underline">
                            Lihat Semua Berita →
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($articles as $article)
                            <div class="bg-white p-6 rounded-3xl shadow-sm border-2 border-slate-200/80 flex flex-col justify-between group hover:shadow-lg hover:border-gold-500/50 transition">
                                <div class="space-y-3">
                                    <span class="text-xs text-slate-400 font-mono">{{ $article->published_at ? $article->published_at->translatedFormat('d M Y') : $article->created_at->translatedFormat('d M Y') }}</span>
                                    <h4 class="text-base font-bold text-emerald-950 group-hover:text-emerald-700 transition line-clamp-2 leading-snug">
                                        <a href="{{ route('public.news.article', $article->slug) }}">
                                            {{ $article->title }}
                                        </a>
                                    </h4>
                                    <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                        {{ Str::limit(strip_tags($article->content), 120) }}
                                    </p>
                                </div>
                                <div class="pt-4 mt-2 border-t border-slate-100">
                                    <a href="{{ route('public.news.article', $article->slug) }}"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-850 group-hover:underline">
                                        <span>Baca Selengkapnya</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- KALENDER AGENDA & EKSTRAKURIKULER SHOWCASE -->
    <section class="py-24 bg-gradient-to-b from-[#fbf8ee] via-white to-[#fbf8ee] pattern-islamic-light relative border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
            
            <!-- Grid: Agenda & Ekskul -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                
                <!-- Left: Agenda Mendatang (5 cols) -->
                <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 flex flex-col justify-between space-y-6">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <span class="text-xs font-bold text-gold-600 uppercase tracking-wider">Kalender Akademik</span>
                                <h3 class="text-xl font-extrabold text-emerald-950 mt-1">Agenda Madrasah</h3>
                            </div>
                            <a href="{{ route('public.calendar') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950 transition flex items-center gap-1">
                                <span>Lihat Semua</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                        <div class="space-y-4 mt-6">
                            @forelse($upcomingEvents as $ev)
                                <div class="flex items-start gap-4 p-3 rounded-2xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100">
                                    <div class="w-12 h-12 rounded-xl flex-shrink-0 flex flex-col items-center justify-center text-center {{ $ev->is_holiday ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-900 border border-emerald-100' }}">
                                        <span class="text-[9px] uppercase font-bold">{{ $ev->start_date->isoFormat('MMM') }}</span>
                                        <span class="text-base font-black leading-none">{{ $ev->start_date->format('d') }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">
                                            {{ ucfirst($ev->event_type) }}
                                        </span>
                                        <h5 class="text-xs sm:text-sm font-bold text-slate-900 mt-1 leading-snug truncate">{{ $ev->title }}</h5>
                                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $ev->start_date->isoFormat('D MMMM Y') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-4 text-center">Belum ada agenda terdekat.</p>
                            @endforelse
                        </div>
                    </div>

                    <a href="{{ route('public.calendar') }}" class="block w-full text-center py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-emerald-950 font-bold text-xs border border-slate-200 transition">
                        Buka Kalender Akademik Lengkap
                    </a>
                </div>

                <!-- Right: Ekstrakurikuler Pilihan (7 cols) -->
                <div class="lg:col-span-7 flex flex-col justify-between space-y-6">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <span class="text-xs font-bold text-emerald-850 uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full">Minat & Bakat Siswa-Siswi</span>
                                <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-950 mt-2">Ekstrakurikuler Unggulan</h3>
                            </div>
                            <a href="{{ route('public.ekskul') }}" class="hidden sm:inline-flex items-center gap-1 text-xs font-bold text-emerald-850 hover:underline">
                                <span>Katalog Selengkapnya</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($featuredEkskuls as $fe)
                                <a href="{{ route('public.ekskul') }}" class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-emerald-300 transition flex flex-col justify-between group">
                                    <div>
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-850">
                                                {{ str_replace('_', ' ', ucfirst($fe->category)) }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-semibold">{{ $fe->enrollments_count }} Siswa Aktif</span>
                                        </div>
                                        <h4 class="font-bold text-slate-900 text-sm group-hover:text-emerald-800 transition">{{ $fe->name }}</h4>
                                        <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $fe->description }}</p>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                                        <span>Jadwal: <strong class="text-slate-700">{{ $fe->schedule_day }}</strong></span>
                                        <span class="text-emerald-800 font-bold group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">Detail <span>→</span></span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Bottom Quick Info / CTA Bar to fill space and align heights -->
                    <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 rounded-2xl p-4 sm:p-5 text-white shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 text-amber-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <div>
                                <h5 class="text-xs sm:text-sm font-bold text-white">Wadah Pengembangan Minat & Potensi Dini</h5>
                                <p class="text-[11px] text-emerald-100/90 mt-0.5">Seluruh kegiatan ekstrakurikuler dibimbing langsung oleh instruktur berpengalaman.</p>
                            </div>
                        </div>
                        <a href="{{ route('public.ekskul') }}" class="flex-shrink-0 px-4 py-2 bg-amber-400 hover:bg-amber-300 text-emerald-950 font-bold text-xs rounded-xl transition shadow">
                            Lihat Semua Ekskul →
                        </a>
                    </div>
                </div>

            </div>

            <!-- Showcase Dewan Guru & Pimpinan -->
            @if($featuredTeachers->isNotEmpty())
            <div class="pt-10 border-t border-slate-200/80">
                <div class="text-center max-w-xl mx-auto mb-10">
                    <span class="text-xs font-bold text-emerald-850 uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full">Pendidik Berakhlak & Kompeten</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-950 mt-2">Dewan Guru & Pimpinan Madrasah</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-2">Dididik dan dibimbing langsung oleh dewan guru dan tenaga pendidik berkompeten lulusan perguruan tinggi Islam dan umum terkemuka.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredTeachers as $ft)
                        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 text-center hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-tr from-emerald-900 to-emerald-700 text-white flex items-center justify-center text-xl font-black shadow-md mb-3">
                                    {{ strtoupper(substr($ft->name, 0, 2)) }}
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm leading-snug">{{ $ft->name }}</h4>
                                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-850">
                                    {{ $ft->position ?? 'Guru Mata Pelajaran' }}
                                </span>
                                @if($ft->education)
                                    <p class="text-[10px] text-slate-400 mt-1 line-clamp-1">{{ $ft->education }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-8">
                    <a href="{{ route('public.teachers') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow transition">
                        <span>Lihat Profil Lengkap Seluruh Dewan Guru</span>
                        <svg class="w-3.5 h-3.5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
            @endif

        </div>
    </section>

    <!-- SECTION KONTAK & LOKASI SEKOLAH -->
    <section id="kontak" class="bg-parchment pattern-islamic-light py-24 border-t-2 border-gold-500/30 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 relative z-10">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-850 bg-emerald-100 px-3 py-1 rounded-full border border-emerald-200">
                    Kunjungi Kampus Kami
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Kontak & Lokasi Madrasah
                </h2>
                <div>
                    <img src="{{ asset('images/ornaments/islamic-divider.svg') }}" class="h-4 mx-auto opacity-75" alt="">
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Informasi resmi pendaftaran, konsultasi program madrasah, dan alamat kampus MI Plus Raudlatul Muta'allimin Kalipucung Blitar.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <!-- Info Kontak Cepat (5 cols) -->
                <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-5">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-850 flex items-center justify-center shrink-0 font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase text-slate-400">Alamat Kampus</h4>
                                <p class="text-sm font-semibold text-slate-800 mt-0.5">
                                    Jl. KH. Muta'allim No. 09, Desa Kalipucung, Kec. Sanankulon, Kab. Blitar, Jawa Timur 66173
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-teal-100 text-teal-850 flex items-center justify-center shrink-0 font-bold">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase text-slate-400">Hotline & WhatsApp</h4>
                                <p class="text-sm font-semibold text-slate-800 font-mono mt-0.5">
                                    {{ config('simad.contact_phone') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-850 flex items-center justify-center shrink-0 font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase text-slate-400">Email Resmi</h4>
                                <p class="text-sm font-semibold text-slate-800 font-mono mt-0.5">
                                    {{ config('simad.contact_email') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-850 flex items-center justify-center shrink-0 font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase text-slate-400">Jam Operasional Tata Usaha</h4>
                                <p class="text-xs text-slate-700 mt-0.5">Senin - Kamis: 07.00 - 14.30 WIB • Jum'at: 07.00 - 11.00 WIB • Sabtu: 07.00 - 13.00 WIB</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('public.contact') }}" class="px-5 py-2.5 rounded-xl bg-emerald-950 text-white font-bold text-xs text-center hover:bg-emerald-900 transition flex-1">
                            Halaman Kontak Lengkap
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('simad.contact_phone')) }}" target="_blank"
                           class="px-5 py-2.5 rounded-xl bg-teal-800 text-white font-bold text-xs text-center hover:bg-teal-700 transition flex items-center justify-center gap-1.5">
                            <span>Chat WhatsApp</span>
                        </a>
                    </div>
                </div>

                <!-- Google Maps Embed (7 cols) -->
                <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                    <div class="w-full h-80 sm:h-full min-h-[300px] relative">
                        <iframe
                            src="https://maps.google.com/maps?q=-8.0983,112.1681&hl=id&z=15&output=embed"
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            title="Peta Lokasi MI Plus Raudlatul Muta'allimin">
                        </iframe>
                    </div>
                </div>
            </div>
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
