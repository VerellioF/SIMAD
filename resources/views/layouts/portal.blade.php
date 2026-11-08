<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal') - {{ config('simad.school_name') }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Zero-node standalone runtime) -->
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
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col" x-data="{ sidebarOpen: false }">

    <!-- Top Navigation Bar -->
    <header class="bg-emerald-950 text-white sticky top-0 z-40 border-b border-emerald-900 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                
                <!-- Left: Sidebar Toggle & Brand -->
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-xl text-emerald-200 hover:text-white hover:bg-emerald-900 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isTeacher() ? route('teacher.dashboard') : route('parent.dashboard')) }}" class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-900 flex items-center justify-center text-gold-400 font-bold border border-gold-500/40">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <span class="block text-sm font-extrabold text-white leading-tight">MI Plus Raudlatul Muta'allimin</span>
                            <span class="block text-[11px] font-semibold text-gold-400">
                                @if(auth()->user()->isAdmin())
                                    Backoffice Tata Usaha & Admin
                                @elseif(auth()->user()->isTeacher())
                                    Portal Guru & Asatidz
                                @else
                                    Portal Wali Murid
                                @endif
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Right: User Profile & Logout -->
                <div class="flex items-center gap-3">
                    <a href="/" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-900/60 hover:bg-emerald-900 text-xs font-semibold text-emerald-200 hover:text-white transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Lihat Website</span>
                    </a>

                    <div class="flex items-center gap-2 pl-2 sm:border-l border-emerald-900">
                        <div class="hidden sm:block text-right">
                            <span class="block text-xs font-bold text-white leading-tight">{{ auth()->user()->name }}</span>
                            <span class="block text-[10px] text-emerald-300 font-mono">{{ auth()->user()->email ?? auth()->user()->phone_number }}</span>
                        </div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 rounded-xl text-emerald-300 hover:text-red-400 hover:bg-emerald-900 transition" title="Keluar dari Akun">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <!-- Main Shell: Sidebar + Content -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

        <!-- Sidebar Navigation -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transform transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 overflow-y-auto"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="min-h-full flex flex-col justify-between p-4">
                <div class="space-y-6">
                    <!-- Close button on Mobile -->
                    <div class="flex items-center justify-between lg:hidden pb-3 border-b border-slate-100">
                        <span class="text-xs font-bold uppercase text-slate-400 tracking-wider">Menu Navigasi</span>
                        <button @click="sidebarOpen = false" class="p-1 rounded-lg text-slate-500 hover:bg-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Role-Specific Navigation Links -->
                    @if(auth()->user()->isAdmin())
                        <div class="space-y-1">
                            <span class="block px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Backoffice TU</span>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Dashboard Ringkasan</span>
                            </a>
                            <a href="{{ route('admin.ppdb.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.ppdb.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('admin.ppdb.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Seleksi PPDB Online</span>
                            </a>
                            <a href="{{ route('admin.tuition.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.tuition.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('admin.tuition.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Tata Kelola SPP & Kas</span>
                            </a>
                            <a href="{{ route('admin.ppdb.export_emis') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Export EMIS Excel</span>
                            </a>
                        </div>
                    @elseif(auth()->user()->isTeacher())
                        <div class="space-y-1">
                            <span class="block px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Portal Asatidz</span>
                            <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.dashboard') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.dashboard') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Beranda Guru</span>
                            </a>
                            <a href="{{ route('teacher.attendance.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.attendance.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.attendance.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                <span>Presensi Santri</span>
                            </a>
                            <a href="{{ route('teacher.leave.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.leave.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.leave.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Validasi Izin Santri</span>
                            </a>
                            <a href="{{ route('teacher.tahfidz.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.tahfidz.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.tahfidz.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Mutaba'ah Tahfidz</span>
                            </a>
                            <a href="{{ route('teacher.grades.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.grades.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.grades.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Buku Nilai Raport</span>
                            </a>
                        </div>
                    @else
                        <div class="space-y-1">
                            <span class="block px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Portal Wali Santri</span>
                            <a href="{{ route('parent.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.dashboard') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.dashboard') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Beranda Ananda</span>
                            </a>
                            <a href="{{ route('parent.attendance.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.attendance.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.attendance.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Rekap Kehadiran</span>
                            </a>
                            <a href="{{ route('parent.leave.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.leave.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.leave.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Surat Izin Santri</span>
                            </a>
                            <a href="{{ route('parent.tahfidz.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.tahfidz.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.tahfidz.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Mutaba'ah Tahfidz</span>
                            </a>
                            <a href="{{ route('parent.grades.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.grades.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.grades.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Nilai & Raport</span>
                            </a>
                            <a href="{{ route('parent.tuition.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.tuition.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.tuition.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Tagihan SPP & Infaq</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Bottom System Info -->
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 text-[11px] text-slate-500 space-y-1">
                    <p class="font-bold text-slate-700">SIMAD MI Plus Kalipucung</p>
                    <p>Versi Custom MVC v1.0</p>
                    <p class="text-[10px] text-emerald-800 font-semibold">T.A. 2026/2027 Ganjil</p>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-semibold flex items-center gap-2 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>

</body>
</html>
