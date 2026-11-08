@props(['schedule'])

<div class="bg-gradient-to-r from-emerald-950 via-emerald-900 to-emerald-950 text-white rounded-3xl shadow-2xl border-2 border-gold-500/40 p-6 sm:p-7 -mt-14 relative z-30 max-w-6xl mx-auto backdrop-blur pattern-islamic-dark overflow-hidden">
    <!-- Corner Decorative Accents -->
    <div class="absolute -top-3 -right-3 w-16 h-16 pointer-events-none opacity-40">
        <img src="{{ asset('images/ornaments/corner-arabesque.svg') }}" class="w-full h-full transform rotate-90" alt="">
    </div>
    <div class="absolute -bottom-3 -left-3 w-16 h-16 pointer-events-none opacity-40">
        <img src="{{ asset('images/ornaments/corner-arabesque.svg') }}" class="w-full h-full transform -rotate-90" alt="">
    </div>

    <div class="flex flex-col md:flex-row items-center justify-between pb-4 mb-4 border-b border-emerald-800/80 gap-3 relative z-10">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-emerald-800/80 text-gold-400 border border-gold-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    Jadwal Sholat Fardhu Wilayah Blitar
                    <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-gold-500/20 text-gold-400 border border-gold-500/40 font-medium">Hari Ini</span>
                </h3>
                <p class="text-xs text-emerald-300/80">{{ $schedule['date_formatted'] ?? date('d F Y') }} • Waktu Indonesia Barat (WIB)</p>
            </div>
        </div>

        <div class="flex items-center gap-2 text-xs bg-emerald-850 px-4 py-2 rounded-xl border border-emerald-700/60">
            <span class="text-emerald-300">Sholat Selanjutnya:</span>
            <span class="font-bold text-gold-400 text-sm uppercase tracking-wide">{{ $schedule['next_prayer'] ?? 'Subuh' }}</span>
            <span class="px-2 py-0.5 rounded bg-gold-500 text-emerald-950 font-bold font-mono">{{ $schedule['next_prayer_time'] ?? '04:00' }}</span>
        </div>
    </div>

    <!-- Times Grid -->
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
        @foreach($schedule['times'] ?? [] as $name => $time)
            @php
                $isNext = ($schedule['next_prayer'] ?? '') === $name;
            @endphp
            <div class="rounded-xl p-3 text-center transition-all duration-300 {{ $isNext ? 'bg-gold-500 text-emerald-950 ring-2 ring-gold-400 shadow-lg scale-105 font-bold' : 'bg-emerald-900/60 hover:bg-emerald-800/80 text-emerald-100 border border-emerald-800/50' }}">
                <span class="block text-xs uppercase tracking-wider {{ $isNext ? 'text-emerald-900 font-extrabold' : 'text-emerald-300/90' }}">{{ $name }}</span>
                <span class="block text-lg font-mono font-bold mt-1 {{ $isNext ? 'text-emerald-950' : 'text-white' }}">{{ $time }}</span>
            </div>
        @endforeach
    </div>
</div>
