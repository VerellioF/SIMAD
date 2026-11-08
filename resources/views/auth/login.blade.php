<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk SIMAD - Sistem Informasi Madrasah Terpadu</title>
    
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
        .pattern-overlay {
            background-image: radial-gradient(#d4af37 0.75px, transparent 0.75px);
            background-size: 16px 16px;
        }
    </style>
</head>
<body class="bg-ivory text-slate-800 font-sans antialiased min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 selection:bg-gold-500 selection:text-white"
      x-data="{
          portal: '{{ $portal ?? 'admin' }}',
          loginInput: '{{ old('login', '') }}',
          passwordInput: '',
          fillDemo(email, pwd, targetPortal) {
              this.loginInput = email;
              this.passwordInput = pwd;
              this.portal = targetPortal;
          }
      }">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <!-- Logo & Brand Header -->
        <a href="/" class="inline-flex items-center gap-3 group mb-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-900 flex items-center justify-center text-gold-400 font-bold text-xl shadow-md border border-gold-500/40 group-hover:scale-105 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="text-left">
                <span class="block text-lg font-extrabold text-emerald-950 leading-tight">MI Plus Raudlatul Muta'allimin</span>
                <span class="block text-xs font-semibold text-emerald-700">SIMAD Terpadu • Blitar</span>
            </div>
        </a>

        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Masuk Portal Sistem
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Pilih portal akses sesuai peran Anda di madrasah
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white py-8 px-6 sm:px-10 rounded-3xl shadow-xl border border-slate-200/80">
            
            <!-- Portal Selection Tabs -->
            <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 rounded-2xl mb-6 text-center text-xs font-semibold">
                <button type="button" @click="portal = 'admin'"
                    :class="portal === 'admin' ? 'bg-emerald-900 text-white shadow font-bold' : 'text-slate-600 hover:text-slate-900'"
                    class="py-2 px-2 rounded-xl transition">
                    Admin / TU
                </button>
                <button type="button" @click="portal = 'teacher'"
                    :class="portal === 'teacher' ? 'bg-teal-700 text-white shadow font-bold' : 'text-slate-600 hover:text-slate-900'"
                    class="py-2 px-2 rounded-xl transition">
                    Guru Kelas
                </button>
                <button type="button" @click="portal = 'parent'"
                    :class="portal === 'parent' ? 'bg-amber-600 text-white shadow font-bold' : 'text-slate-600 hover:text-slate-900'"
                    class="py-2 px-2 rounded-xl transition">
                    Wali Murid
                </button>
            </div>

            <!-- Portal Badge Indicator -->
            <div class="mb-5 text-center">
                <span x-show="portal === 'admin'" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <span>Backoffice Tata Usaha & Administrator</span>
                </span>
                <span x-show="portal === 'teacher'" x-cloak class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 text-teal-800 border border-teal-200 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                    <span>Portal Asatidz & Wali Kelas</span>
                </span>
                <span x-show="portal === 'parent'" x-cloak class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                    <span>Portal Wali Murid & Pantau Santri</span>
                </span>
            </div>

            @if(session('status'))
                <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-medium">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Email atau Nomor WhatsApp
                    </label>
                    <input type="text" name="login" x-model="loginInput" required autofocus
                        placeholder="admin@simad.id atau 0812xxxxxxx"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Password
                    </label>
                    <input type="password" name="password" x-model="passwordInput" required
                        placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-emerald-800 focus:ring-emerald-600 border-slate-300">
                        <span class="ml-2">Ingat Sesi Saya</span>
                    </label>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('simad.contact_phone')) }}?text=Assalamu'alaikum%20Admin%20TU,%20saya%20lupa%20password%20SIMAD" target="_blank" class="text-emerald-800 hover:underline font-semibold">
                        Lupa Password?
                    </a>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3.5 px-4 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-sm shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                        <span>Masuk ke Portal</span>
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>

            <!-- 1-Click Demo Fill Box -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-xs space-y-2">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center">
                    Uji Coba Cepat (Akun Demo)
                </span>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="fillDemo('admin@simad.id', 'password', 'admin')"
                        class="p-2 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 text-center transition">
                        <span class="block font-bold text-slate-800 text-[11px]">Admin TU</span>
                        <span class="block text-[9px] text-slate-400">admin@simad.id</span>
                    </button>
                    <button type="button" @click="fillDemo('guru@simad.id', 'password', 'teacher')"
                        class="p-2 rounded-xl bg-slate-50 hover:bg-teal-50 border border-slate-200 hover:border-teal-300 text-center transition">
                        <span class="block font-bold text-slate-800 text-[11px]">Guru Kelas</span>
                        <span class="block text-[9px] text-slate-400">guru@simad.id</span>
                    </button>
                    <button type="button" @click="fillDemo('wali@simad.id', 'password', 'parent')"
                        class="p-2 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-300 text-center transition">
                        <span class="block font-bold text-slate-800 text-[11px]">Wali Santri</span>
                        <span class="block text-[9px] text-slate-400">wali@simad.id</span>
                    </button>
                </div>
            </div>

        </div>

        <div class="text-center mt-6">
            <a href="/" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Website Utama</span>
            </a>
        </div>
    </div>

</body>
</html>
