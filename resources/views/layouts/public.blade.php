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
            border-top-left-radius: 4rem;
            border-top-right-radius: 4rem;
        }
        .pattern-overlay {
            background-image: radial-gradient(#d4af37 0.75px, transparent 0.75px);
            background-size: 16px 16px;
        }
    </style>
</head>
<body class="bg-ivory text-slate-800 font-sans antialiased selection:bg-gold-500 selection:text-white">

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

            <!-- Portal Login Dropdown -->
            <div class="flex items-center space-x-3" x-data="{ open: false }">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('simad.contact_phone')) }}" target="_blank" class="hover:text-gold-400 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    Hotline TU
                </a>
                <span>|</span>
                <div class="relative">
                    <button @click="open = !open" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-900 hover:bg-emerald-800 rounded font-semibold text-gold-400 transition">
                        <span>Masuk Portal</span>
                        <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-slate-100 py-1.5 z-50 text-slate-700">
                        <a href="/wali/login" class="flex items-center gap-2 px-4 py-2 hover:bg-emerald-50 hover:text-emerald-900 transition">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Portal Wali Murid</span>
                        </a>
                        <a href="/guru/login" class="flex items-center gap-2 px-4 py-2 hover:bg-emerald-50 hover:text-emerald-900 transition">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            <span>Portal Guru / Ustadz</span>
                        </a>
                        <a href="/admin/login" class="flex items-center gap-2 px-4 py-2 hover:bg-emerald-50 hover:text-emerald-900 transition">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            <span>Backoffice TU & Admin</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white/95 backdrop-blur sticky top-0 z-40 border-b border-slate-200/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Brand / Logo -->
                <a href="/" class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-900 flex items-center justify-center text-gold-400 font-bold text-xl shadow-md border border-gold-500/40">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <span class="block text-lg font-extrabold text-emerald-900 leading-tight">MI Plus Raudlatul Muta'allimin</span>
                        <span class="block text-xs font-medium text-slate-500">Kalipucung, Sanankulon, Blitar</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center space-x-7 text-sm font-semibold text-slate-700">
                    <a href="/" class="hover:text-emerald-900 transition">Beranda</a>
                    <a href="#profil" class="hover:text-emerald-900 transition">Profil</a>
                    <a href="#program" class="hover:text-emerald-900 transition">Program Unggulan</a>
                    <a href="#galeri" class="hover:text-emerald-900 transition">Galeri Kegiatan</a>
                    <a href="/ppdb/cek-status" class="hover:text-emerald-900 transition">Cek Status PPDB</a>
                </nav>

                <!-- CTA Action -->
                <div class="flex items-center gap-3">
                    <a href="/ppdb/daftar" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all border border-emerald-700">
                        <span>PPDB Online</span>
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-emerald-950 text-white pt-16 pb-8 border-t-4 border-gold-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-emerald-900">
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-900 flex items-center justify-center text-gold-400 font-bold border border-gold-500/40">
                            MI
                        </div>
                        <h4 class="text-xl font-bold text-white">MI Plus Raudlatul Muta'allimin</h4>
                    </div>
                    <p class="text-emerald-200/80 text-sm leading-relaxed max-w-lg">
                        Lembaga pendidikan dasar Islam berkarakter di Blitar yang memadukan kurikulum Kemenag dengan penguatan hafalan Al-Qur'an (Tahfidz Juz 30), pembiasaan akhlaqul karimah, dan kesenian hadrah rebana.
                    </p>
                    <div class="text-xs text-gold-400/90 font-medium space-y-1">
                        <p>NSM: {{ config('simad.nsm') }} • NPSN: {{ config('simad.npsn') }}</p>
                        <p>Akreditasi Madrasah Kemenag RI</p>
                    </div>
                </div>

                <div>
                    <h5 class="text-sm font-bold text-gold-400 uppercase tracking-wider mb-4">Navigasi Cepat</h5>
                    <ul class="space-y-2 text-sm text-emerald-200/80">
                        <li><a href="/ppdb/daftar" class="hover:text-white transition">Pendaftaran PPDB Online</a></li>
                        <li><a href="/ppdb/cek-status" class="hover:text-white transition">Cek Status Seleksi Santri</a></li>
                        <li><a href="/wali/login" class="hover:text-white transition">Portal Wali Murid</a></li>
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
