@props([
    'title' => 'Portal Madrasah',
    'subtitle' => 'Silakan masuk dengan kredensial Anda',
    'badge' => 'SIMAD',
    'color' => 'emerald',
])

<div class="mb-6 text-center space-y-3">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-800 dark:text-[#D4AF37]">
        <span class="w-2 h-2 rounded-full bg-[#D4AF37]"></span>
        <span>{{ $badge }}</span>
    </div>

    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
        {{ $title }}
    </h2>
    <p class="text-xs text-slate-500 dark:text-slate-400">
        {{ $subtitle }}
    </p>

    <!-- Return to Website Link -->
    <div class="pt-1">
        <a href="/" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 dark:text-[#D4AF37] hover:underline">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Website Utama</span>
        </a>
    </div>
</div>
