<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('simad.school_name')) - {{ config('simad.school_tagline') }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Amiri -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN for zero-node standalone runtime fallback + local compiled assets) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            850: '#0B3326',
                            900: '#0D3B2E',
                            950: '#08251C',
                        },
                        gold: {
                            400: '#E5C158',
                            500: '#D4AF37',
                            600: '#B89428',
                        },
                        ivory: '#FAF8F5',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        arabic: ['Amiri', 'serif'],
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .islamic-arch {
            border-top-left-radius: 4.5rem;
            border-top-right-radius: 4.5rem;
        }
        .pattern-overlay {
            background-image: url('{{ asset('images/ornaments/islamic-star-pattern-dark.svg') }}');
            background-size: 80px 80px;
            background-repeat: repeat;
        }
        .pattern-islamic-light {
            background-image: url('{{ asset('images/ornaments/islamic-star-pattern.svg') }}');
            background-size: 80px 80px;
            background-repeat: repeat;
        }
        .pattern-islamic-dark {
            background-image: url('{{ asset('images/ornaments/islamic-star-pattern-dark.svg') }}');
            background-size: 80px 80px;
            background-repeat: repeat;
        }
        .bg-parchment {
            background-color: #faf7ee;
        }
    </style>
</head>
<body class="bg-ivory text-slate-800 font-sans antialiased selection:bg-gold-500 selection:text-white" x-data="{ mobileMenu: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- Top Bar Islami -->
    <div class="bg-emerald-950 text-emerald-100 text-xs py-2 border-b border-emerald-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center gap-1.5 font-medium text-gold-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $prayerSchedule['hijri_formatted'] ?? '1447 H' }}
                </span>
                <span class="hidden md:inline-block text-emerald-600">•</span>
                <span class="hidden md:inline-flex items-center gap-1 text-emerald-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Kalipucung, Sanankulon, Blitar
                </span>
            </div>

            <!-- Portal Login Dropdown & Direct Links -->
            <div class="flex items-center space-x-3" x-data="{ open: false }">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('simad.contact_phone')) }}" target="_blank" class="hover:text-gold-400 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    <span>Hotline TU</span>
                </a>
                <span>|</span>
                <div class="relative">
                    <button @click="open = !open" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-900 hover:bg-emerald-800 rounded font-semibold text-gold-400 transition">
                        <span>Masuk Portal</span>
                        <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2.5 z-50 text-slate-700">
                        <div class="px-3.5 py-1.5 border-b border-slate-100 text-[10px] font-bold uppercase text-slate-400">Pilih Portal Masuk</div>
                        <a href="/wali/login" class="flex items-center gap-2.5 px-4 py-2 hover:bg-emerald-50 hover:text-emerald-900 transition text-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <div>
                                <span class="font-bold block">Portal Wali Murid</span>
                                <span class="text-[10px] text-slate-400 block">Orang Tua / Wali Siswa</span>
                            </div>
                        </a>
                        <a href="/guru/login" class="flex items-center gap-2.5 px-4 py-2 hover:bg-emerald-50 hover:text-emerald-900 transition text-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                            <div>
                                <span class="font-bold block">Portal Guru & Wali Kelas</span>
                                <span class="text-[10px] text-slate-400 block">Presensi, Nilai & Ibadah</span>
                            </div>
                        </a>
                        <a href="/admin/login" class="flex items-center gap-2.5 px-4 py-2 hover:bg-emerald-50 hover:text-emerald-900 transition text-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <div>
                                <span class="font-bold block">Backoffice Staf TU</span>
                                <span class="text-[10px] text-slate-400 block">Operator & Administrasi</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white/95 backdrop-blur sticky top-0 z-40 border-b border-slate-200/80 shadow-sm transition-all duration-200" :class="scrolled ? 'py-2 shadow-md' : 'py-3.5'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Brand / Logo -->
                <a href="/" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo-madrasah.svg') }}" alt="Logo MI Plus Raudlatul Muta'allimin" class="w-12 h-12 object-contain group-hover:scale-105 transition-transform">
                    <div>
                        <span class="block text-base sm:text-lg font-extrabold text-emerald-950 leading-tight">MI Plus Raudlatul Muta'allimin</span>
                        <span class="block text-xs font-semibold text-emerald-700">Kalipucung, Sanankulon, Blitar</span>
                    </div>
                </a>

                <!-- Desktop Menu (Spacious & Clean) -->
                <nav class="hidden lg:flex items-center space-x-8 text-sm font-semibold text-slate-700">
                    <a href="/" class="hover:text-emerald-900 transition {{ request()->is('/') ? 'text-emerald-900 font-bold' : '' }}">
                        Beranda
                    </a>

                    <!-- Dropdown: Akademik & Profil (Kurikulum, Dewan Guru, Kalender, Ekskul) -->
                    <div class="relative" x-data="{ openAkademik: false }" @mouseleave="openAkademik = false">
                        <button @click="openAkademik = !openAkademik" @mouseenter="openAkademik = true"
                                class="inline-flex items-center gap-1.5 hover:text-emerald-900 transition py-1 {{ request()->routeIs('public.curriculum', 'public.teachers', 'public.calendar', 'public.ekskul') ? 'text-emerald-900 font-bold' : '' }}">
                            <span>Akademik & Profil</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{'rotate-180': openAkademik}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="openAkademik" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50">
                            
                            <a href="{{ route('public.curriculum') }}" class="flex items-start gap-3 px-4 py-2.5 hover:bg-emerald-50 text-slate-700 hover:text-emerald-950 transition">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div>
                                    <span class="block font-bold text-xs text-slate-900">Kurikulum Terpadu</span>
                                    <span class="block text-[11px] text-slate-500">Integrasi Kemenag, Merdeka & Tahfidz</span>
                                </div>
                            </a>

                            <a href="{{ route('public.teachers') }}" class="flex items-start gap-3 px-4 py-2.5 hover:bg-emerald-50 text-slate-700 hover:text-emerald-950 transition">
                                <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div>
                                    <span class="block font-bold text-xs text-slate-900">Dewan Guru & Pimpinan (Dewan Asatidz)</span>
                                    <span class="block text-[11px] text-slate-500">Profil Kepala Madrasah & Bapak/Ibu Guru</span>
                                </div>
                            </a>

                            <a href="{{ route('public.calendar') }}" class="flex items-start gap-3 px-4 py-2.5 hover:bg-emerald-50 text-slate-700 hover:text-emerald-950 transition">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <span class="block font-bold text-xs text-slate-900">Kalender Akademik</span>
                                    <span class="block text-[11px] text-slate-500">Agenda Ujian, Libur & Kegiatan PHBI</span>
                                </div>
                            </a>

                            <a href="{{ route('public.ekskul') }}" class="flex items-start gap-3 px-4 py-2.5 hover:bg-emerald-50 text-slate-700 hover:text-emerald-950 transition border-t border-slate-100">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-800 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                </div>
                                <div>
                                    <span class="block font-bold text-xs text-slate-900">Ekstrakurikuler Siswa</span>
                                    <span class="block text-[11px] text-slate-500">Minat, Bakat, Olahraga & Seni Rebana</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('public.news') }}" class="hover:text-emerald-900 transition {{ request()->routeIs('public.news*') ? 'text-emerald-900 font-bold' : '' }}">
                        Kabar & Galeri
                    </a>
                    
                    <a href="{{ route('public.contact') }}" class="hover:text-emerald-900 transition {{ request()->routeIs('public.contact') ? 'text-emerald-900 font-bold' : '' }}">
                        Kontak & Lokasi
                    </a>
                </nav>

                <!-- Actions: PPDB Dropdown Button & Mobile Hamburger -->
                <div class="flex items-center gap-3">
                    
                    <!-- PPDB Dropdown Action Widget -->
                    <div class="relative hidden sm:block" x-data="{ openPpdb: false }" @mouseleave="openPpdb = false">
                        <div class="inline-flex rounded-xl shadow-sm">
                            <a href="/ppdb/daftar" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-l-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm transition border-y border-l border-emerald-700">
                                <span>PPDB Online</span>
                            </a>
                            <button @click="openPpdb = !openPpdb" @mouseenter="openPpdb = true"
                                    class="px-2.5 py-2.5 rounded-r-xl bg-emerald-950 hover:bg-emerald-800 text-gold-400 transition border border-emerald-700 border-l-emerald-800"
                                    title="Pilihan Layanan PPDB">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{'rotate-180': openPpdb}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>

                        <!-- Dropdown PPDB Items -->
                        <div x-show="openPpdb" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            
                            <a href="/ppdb/daftar" class="flex items-start gap-2.5 px-4 py-2.5 hover:bg-emerald-50 text-slate-800 hover:text-emerald-950 transition text-xs font-semibold">
                                <span class="w-2 h-2 rounded-full bg-gold-500 mt-1.5 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-slate-900 block">Daftar Siswa Baru</span>
                                    <span class="text-[10px] text-slate-400 block font-normal">Formulir Pendaftaran Online</span>
                                </div>
                            </a>

                            <a href="/ppdb/cek-status" class="flex items-start gap-2.5 px-4 py-2.5 hover:bg-emerald-50 text-slate-800 hover:text-emerald-950 transition text-xs font-semibold border-t border-slate-100">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 mt-1.5 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-slate-900 block">Cek Status Seleksi</span>
                                    <span class="text-[10px] text-slate-400 block font-normal">Pantau verifikasi & hasil seleksi</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Hamburger Mobile Button -->
                    <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 transition" aria-label="Menu Navigasi">
                        <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div x-show="mobileMenu" x-cloak @click.away="mobileMenu = false" class="lg:hidden mt-4 pt-4 pb-6 border-t border-slate-100 space-y-3">
                <nav class="flex flex-col space-y-1 text-sm font-semibold text-slate-700" x-data="{ mobileAkademikOpen: false }">
                    <a href="/" @click="mobileMenu = false" class="px-3 py-2 rounded-xl hover:bg-emerald-50 hover:text-emerald-900">Beranda</a>

                    <!-- Mobile Submenu: Akademik & Profil -->
                    <div class="space-y-1">
                        <button type="button" @click="mobileAkademikOpen = !mobileAkademikOpen"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl hover:bg-emerald-50 hover:text-emerald-900 transition text-left">
                            <span>Akademik & Profil</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{'rotate-180': mobileAkademikOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="mobileAkademikOpen" x-cloak class="pl-4 space-y-1 border-l-2 border-emerald-100 ml-3">
                            <a href="{{ route('public.curriculum') }}" @click="mobileMenu = false" class="block px-3 py-1.5 rounded-lg text-xs hover:bg-emerald-50 hover:text-emerald-900">
                                ↳ Kurikulum Terpadu
                            </a>
                            <a href="{{ route('public.teachers') }}" @click="mobileMenu = false" class="block px-3 py-1.5 rounded-lg text-xs hover:bg-emerald-50 hover:text-emerald-900">
                                ↳ Dewan Guru & Pimpinan (Dewan Asatidz)
                            </a>
                            <a href="{{ route('public.calendar') }}" @click="mobileMenu = false" class="block px-3 py-1.5 rounded-lg text-xs hover:bg-emerald-50 hover:text-emerald-900">
                                ↳ Kalender Akademik
                            </a>
                            <a href="{{ route('public.ekskul') }}" @click="mobileMenu = false" class="block px-3 py-1.5 rounded-lg text-xs hover:bg-emerald-50 hover:text-emerald-900">
                                ↳ Ekstrakurikuler Siswa
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('public.news') }}" @click="mobileMenu = false" class="px-3 py-2 rounded-xl hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('public.news*') ? 'text-emerald-900 font-bold' : '' }}">Kabar & Galeri</a>
                    <a href="{{ route('public.contact') }}" @click="mobileMenu = false" class="px-3 py-2 rounded-xl hover:bg-emerald-50 hover:text-emerald-900">Kontak & Lokasi</a>
                </nav>

                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <a href="/ppdb/daftar" class="block w-full text-center py-2.5 rounded-xl bg-gold-500 text-emerald-950 font-bold text-sm shadow">
                        Daftar PPDB Online
                    </a>
                    <a href="/ppdb/cek-status" class="block w-full text-center py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs">
                        Cek Status Seleksi PPDB
                    </a>
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <a href="/wali/login" class="text-center py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700">
                            Portal Wali
                        </a>
                        <a href="/guru/login" class="text-center py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700">
                            Portal Guru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Floating WhatsApp Action -->
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('simad.contact_phone')) }}" target="_blank"
       class="fixed bottom-6 right-6 z-40 bg-emerald-600 hover:bg-emerald-500 text-white p-3.5 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-transform border-2 border-white"
       title="Konsultasi PPDB WhatsApp">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
    </a>

    <!-- Footer -->
    <footer class="bg-gradient-to-b from-emerald-950 via-[#032a20] to-emerald-950 text-white pt-8 pb-8 border-t-4 border-gold-500 relative pattern-islamic-dark overflow-hidden">
        <!-- Mosque Silhouette Header Ribbon -->
        <div class="w-full overflow-hidden leading-none mb-6 opacity-30 pointer-events-none">
            <img src="{{ asset('images/ornaments/mosque-skyline.svg') }}" class="w-full h-12 sm:h-16 object-cover object-bottom" alt="">
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-emerald-900">
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-madrasah.svg') }}" alt="Logo MI Plus" class="w-10 h-10 object-contain">
                        <h4 class="text-xl font-bold text-white">MI Plus Raudlatul Muta'allimin</h4>
                    </div>
                    <p class="text-emerald-200/80 text-sm leading-relaxed max-w-lg">
                        Lembaga pendidikan dasar Islam terpadu di Blitar yang memadukan kurikulum Kemenag dengan penguatan hafalan Al-Qur'an (Tahfidz Juz 30), pembiasaan akhlaqul karimah, dan kesenian hadrah rebana.
                    </p>
                    <div class="text-xs text-gold-400/90 font-medium space-y-1">
                        <p>NSM: {{ config('simad.nsm') }} • NPSN: {{ config('simad.npsn') }}</p>
                        <p>Akreditasi Madrasah Kemenag RI</p>
                    </div>
                </div>

                <div>
                    <h5 class="text-sm font-bold text-gold-400 uppercase tracking-wider mb-4">Navigasi Cepat</h5>
                    <ul class="space-y-2 text-sm text-emerald-200/80">
                        <li><a href="{{ route('public.news') }}" class="hover:text-white transition">Kabar, Pengumuman & Galeri</a></li>
                        <li><a href="{{ route('public.contact') }}" class="hover:text-white transition">Kontak & Lokasi Madrasah</a></li>
                        <li><a href="/ppdb/daftar" class="hover:text-white transition">Pendaftaran Siswa Baru (PPDB Online)</a></li>
                        <li><a href="/ppdb/cek-status" class="hover:text-white transition">Cek Status Pendaftaran Siswa</a></li>
                        <li><a href="/wali/login" class="hover:text-white transition">Portal Orang Tua / Wali Murid</a></li>
                        <li><a href="/guru/login" class="hover:text-white transition">Portal Guru & Wali Kelas</a></li>
                        <li><a href="/admin/login" class="hover:text-white transition">Backoffice Staf TU</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-sm font-bold text-gold-400 uppercase tracking-wider mb-4">Rekening Resmi & Infaq</h5>
                    <div class="bg-emerald-900/60 p-4 rounded-xl border border-emerald-800 text-xs space-y-2">
                        <p class="text-emerald-300 font-semibold">Bank Syariah Indonesia (BSI)</p>
                        <p class="text-lg font-mono font-bold text-white tracking-wider">7123456789</p>
                        <p class="text-emerald-200/70">a.n. YAYASAN RAUDLATUL MUTA'ALLIMIN</p>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-emerald-300/70 gap-4">
                <p>© {{ date('Y') }} MI Plus Raudlatul Muta'allimin Kalipucung Blitar. Hak Cipta Dilindungi.</p>
                <p>Sistem Informasi Madrasah Terpadu (SIMAD) v1.0</p>
            </div>
        </div>
    </footer>

</body>
</html>
