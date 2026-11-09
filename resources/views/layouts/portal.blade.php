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
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col"
      x-data="{
          sidebarOpen: false,
          confirmModal: {
              open: false,
              title: 'Konfirmasi Tindakan',
              message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
              actionCallback: null
          },
          openConfirm(title, message, callback) {
              this.confirmModal.title = title;
              this.confirmModal.message = message;
              this.confirmModal.actionCallback = callback;
              this.confirmModal.open = true;
          },
          executeConfirm() {
              if (typeof this.confirmModal.actionCallback === 'function') {
                  this.confirmModal.actionCallback();
              }
              this.confirmModal.open = false;
          }
      }">

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
                        <img src="{{ asset('images/logo-madrasah.svg') }}" alt="Logo MI Plus" class="w-9 h-9 object-contain">
                        <div>
                            <span class="block text-sm font-extrabold text-white leading-tight">MI Plus Raudlatul Muta'allimin</span>
                            <span class="block text-[11px] font-semibold text-gold-400">
                                @if(auth()->user()->isAdmin())
                                    Backoffice Tata Usaha & Admin
                                @elseif(auth()->user()->isTeacher())
                                    Portal Guru & Wali Kelas
                                @else
                                    Portal Orang Tua / Wali Murid
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
    <div class="flex-1 flex min-h-[calc(100vh-4rem)]">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

        <!-- Sidebar Navigation -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transform transition-transform duration-200 ease-in-out lg:sticky lg:top-16 lg:h-[calc(100vh-4rem)] lg:z-30 lg:flex-shrink-0 lg:translate-x-0 lg:transform-none overflow-y-auto"
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
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Dashboard Ringkasan</span>
                            </a>
                            <a href="{{ route('admin.ppdb.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.ppdb.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.ppdb.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Seleksi PPDB Online</span>
                            </a>
                            <a href="{{ route('admin.tuition.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.tuition.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.tuition.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Tata Kelola SPP & Kas</span>
                            </a>
                            <a href="{{ route('admin.payment.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.payment.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.payment.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span>Pembayaran Online</span>
                            </a>
                            <a href="{{ route('admin.ppdb.export_emis') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Export EMIS Excel</span>
                            </a>

                            <span class="block px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-4 mb-2">Akademik & KBM</span>
                            <a href="{{ route('admin.agenda.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.agenda.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.agenda.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Kalender & Agenda</span>
                            </a>
                            <a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.schedules.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.schedules.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Jadwal KBM Mingguan</span>
                            </a>
                            <a href="{{ route('admin.ekskul.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.ekskul.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.ekskul.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                <span>Ekstrakurikuler</span>
                            </a>
                            <a href="{{ route('admin.conduct.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.conduct.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.conduct.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Disiplin & Poin Karakter</span>
                            </a>
                            <a href="{{ route('admin.curriculum.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.curriculum.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.curriculum.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Struktur Kurikulum</span>
                            </a>

                            <span class="block px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-4 mb-2">Master Data</span>
                            <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.students.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.students.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span>Data Siswa (30)</span>
                            </a>
                            <a href="{{ route('admin.teachers.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.teachers.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.teachers.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                <span>Guru & Tenaga Pendidik</span>
                            </a>
                            <a href="{{ route('admin.classrooms.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.classrooms.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.classrooms.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Kelas & Rombel</span>
                            </a>

                            <span class="block px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-4 mb-2">Publikasi & CMS</span>
                            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.articles.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.articles.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                <span>Warta & Berita</span>
                            </a>
                            <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.announcements.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.announcements.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                <span>Pengumuman</span>
                            </a>
                            <a href="{{ route('admin.galleries.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.galleries.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.galleries.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Galeri Kegiatan</span>
                            </a>

                            <span class="block px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-4 mb-2">Komunikasi & Notifikasi</span>
                            <a href="{{ route('admin.whatsapp.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold {{ request()->routeIs('admin.whatsapp.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>Notifikasi WhatsApp</span>
                            </a>
                        </div>
                    @elseif(auth()->user()->isTeacher())
                        <div class="space-y-1">
                            <span class="block px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Portal Guru & Wali Kelas</span>
                            <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.dashboard') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.dashboard') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Beranda Guru</span>
                            </a>
                            <a href="{{ route('teacher.attendance.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.attendance.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.attendance.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                <span>Presensi Siswa</span>
                            </a>
                            <a href="{{ route('teacher.leave.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.leave.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.leave.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Izin Kehadiran Siswa</span>
                            </a>
                            <a href="{{ route('teacher.tahfidz.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.tahfidz.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.tahfidz.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Mutaba'ah Tahfidz</span>
                            </a>
                            <a href="{{ route('teacher.grades.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.grades.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.grades.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Buku Nilai Raport</span>
                            </a>
                            <a href="{{ route('teacher.schedules.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.schedules.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.schedules.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Jadwal Mengajar</span>
                            </a>
                            <a href="{{ route('teacher.worship.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.worship.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.worship.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Mutaba'ah Ibadah Siswa</span>
                            </a>
                            <a href="{{ route('teacher.conduct.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.conduct.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.conduct.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Disiplin & Poin Karakter</span>
                            </a>
                            <a href="{{ route('teacher.curriculum.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.curriculum.*') ? 'bg-teal-50 text-teal-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('teacher.curriculum.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Panduan Kurikulum</span>
                            </a>
                        </div>
                    @else
                        <div class="space-y-1">
                            <span class="block px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Portal Orang Tua / Wali Murid</span>
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
                                <span>Surat Izin Kehadiran</span>
                            </a>
                            <a href="{{ route('parent.schedules.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.schedules.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.schedules.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Jadwal Pelajaran</span>
                            </a>
                            <a href="{{ route('parent.worship.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.worship.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.worship.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Mutaba'ah Ibadah</span>
                            </a>
                            <a href="{{ route('parent.tahfidz.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.tahfidz.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.tahfidz.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Mutaba'ah Tahfidz</span>
                            </a>
                            <a href="{{ route('parent.ekskul.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.ekskul.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.ekskul.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                <span>Ekstrakurikuler</span>
                            </a>
                            <a href="{{ route('parent.grades.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.grades.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.grades.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Nilai & Raport</span>
                            </a>
                            <a href="{{ route('parent.conduct.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.conduct.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.conduct.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Raport Karakter Ananda</span>
                            </a>
                            <a href="{{ route('parent.curriculum.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('parent.curriculum.*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 {{ request()->routeIs('parent.curriculum.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Target & Kurikulum Kelas</span>
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
        <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
            <div class="max-w-7xl mx-auto">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-semibold flex items-center gap-2 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm font-semibold flex items-center gap-2 shadow-sm">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm font-semibold shadow-sm space-y-1.5">
                        <div class="flex items-center gap-2 font-bold text-rose-950">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Periksa kembali input formulir Anda:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs text-rose-800 space-y-0.5 ml-6 font-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>

    <!-- Mobile Sticky Bottom Navigation (Mobile-First for Teachers & Parents) -->
    @if(auth()->check() && !auth()->user()->isAdmin())
        <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-xl px-2 py-1.5 flex items-center justify-around text-[10px] font-bold">
            @if(auth()->user()->isTeacher())
                <a href="{{ route('teacher.dashboard') }}"
                   class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition {{ request()->routeIs('teacher.dashboard') ? 'text-teal-900 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('teacher.dashboard') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Beranda</span>
                </a>
                <a href="{{ route('teacher.attendance.index') }}"
                   class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition {{ request()->routeIs('teacher.attendance.*') ? 'text-teal-900 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('teacher.attendance.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Presensi</span>
                </a>
                <a href="{{ route('teacher.tahfidz.index') }}"
                   class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition {{ request()->routeIs('teacher.tahfidz.*') ? 'text-teal-900 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('teacher.tahfidz.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Tahfidz</span>
                </a>
                <a href="{{ route('teacher.grades.index') }}"
                   class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition {{ request()->routeIs('teacher.grades.*') ? 'text-teal-900 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('teacher.grades.*') ? 'text-teal-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Buku Nilai</span>
                </a>
            @else
                <a href="{{ route('parent.dashboard') }}"
                   class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition {{ request()->routeIs('parent.dashboard') ? 'text-amber-900 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('parent.dashboard') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Beranda</span>
                </a>
                <a href="{{ route('parent.attendance.index') }}"
                   class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition {{ request()->routeIs('parent.attendance.*') ? 'text-amber-900 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('parent.attendance.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Kehadiran</span>
                </a>
                <a href="{{ route('parent.grades.index') }}"
                   class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition {{ request()->routeIs('parent.grades.*') ? 'text-amber-900 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('parent.grades.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Nilai Rapor</span>
                </a>
                <a href="{{ route('parent.tuition.index') }}"
                   class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition {{ request()->routeIs('parent.tuition.*') ? 'text-amber-900 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('parent.tuition.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Bayar SPP</span>
                </a>
            @endif
        </nav>
    @endif

    <!-- Global Confirmation Modal Dialog (Custom Replacement for window.confirm) -->
    <div x-show="confirmModal.open" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="confirmModal.open = false"
             class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-150">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-900 flex items-center justify-center font-black text-lg shrink-0">
                    ⚠️
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900" x-text="confirmModal.title"></h3>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed" x-text="confirmModal.message"></p>
                </div>
            </div>

            <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" @click="confirmModal.open = false"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </button>
                <button type="button" @click="executeConfirm()"
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow transition">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>

</body>
</html>
