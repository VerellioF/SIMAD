@extends('layouts.public')

@section('title', 'Kalender Akademik & Agenda Madrasah')

@section('content')
<!-- Header Hero -->
<section class="bg-gradient-to-b from-emerald-950 via-emerald-900 to-emerald-850 text-white py-16 relative overflow-hidden">
    <div class="absolute inset-0 pattern-overlay opacity-10"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gold-500/20 text-gold-400 border border-gold-500/30 text-xs font-semibold uppercase tracking-wider mb-3">
            Tahun Ajaran {{ $activeYear->name ?? '2026/2027' }}
        </span>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight">Kalender Akademik & Agenda</h1>
        <p class="mt-4 text-emerald-200 text-sm sm:text-base max-w-2xl mx-auto font-light">
            Informasi lengkap jadwal kegiatan belajar mengajar, ujian, peringatan hari besar Islam, dan hari libur madrasah.
        </p>
    </div>
</section>

<!-- Main Content -->
<section class="py-14 bg-ivory">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left / Main: List Agenda & Filter -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Filter Tabs -->
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <a href="{{ route('public.calendar', ['type' => 'all']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ !request('type') || request('type') == 'all' ? 'bg-emerald-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
                        <a href="{{ route('public.calendar', ['type' => 'akademik']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ request('type') == 'akademik' ? 'bg-emerald-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Akademik</a>
                        <a href="{{ route('public.calendar', ['type' => 'ujian']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ request('type') == 'ujian' ? 'bg-emerald-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Ujian / Asesmen</a>
                        <a href="{{ route('public.calendar', ['type' => 'libur']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ request('type') == 'libur' ? 'bg-emerald-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Libur Madrasah</a>
                        <a href="{{ route('public.calendar', ['type' => 'phbi']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ request('type') == 'phbi' ? 'bg-emerald-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">PHBI & Religi</a>
                    </div>
                </div>

                <!-- Events List -->
                <div class="space-y-4">
                    @forelse($events as $event)
                    <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md border border-slate-100 transition flex items-start gap-4">
                        <div class="w-16 h-16 rounded-2xl flex-shrink-0 flex flex-col items-center justify-center text-center {{ $event->is_holiday ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-900 border border-emerald-100' }}">
                            <span class="text-xs uppercase font-extrabold">{{ $event->start_date->isoFormat('MMM') }}</span>
                            <span class="text-xl font-black leading-none">{{ $event->start_date->format('d') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                    @if($event->event_type == 'ujian') bg-amber-100 text-amber-800
                                    @elseif($event->event_type == 'libur') bg-red-100 text-red-800
                                    @elseif($event->event_type == 'keagamaan') bg-purple-100 text-purple-800
                                    @else bg-teal-100 text-teal-800 @endif">
                                    {{ ucfirst($event->event_type) }}
                                </span>
                                @if($event->is_holiday)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">Libur Resmi</span>
                                @endif
                            </div>
                            <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $event->title }}</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $event->start_date->isoFormat('D MMMM Y') }}
                                @if($event->end_date && $event->end_date->gt($event->start_date))
                                    s.d. {{ $event->end_date->isoFormat('D MMMM Y') }}
                                @endif
                            </p>
                            @if($event->description)
                                <p class="text-xs text-slate-600 mt-2">{{ $event->description }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-2xl p-10 text-center border border-slate-100 text-slate-500 text-sm">
                        Tidak ada agenda madrasah yang ditemukan untuk kategori ini.
                    </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Agenda Mendatang & Download -->
            <div class="space-y-6">
                <!-- Upcoming Widget -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <h4 class="font-extrabold text-sm text-emerald-950 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Agenda Mendatang
                    </h4>
                    <div class="divide-y divide-slate-100">
                        @forelse($upcomingEvents as $up)
                        <div class="py-3 first:pt-0 last:pb-0">
                            <span class="text-[10px] font-bold text-emerald-700 uppercase">{{ $up->start_date->isoFormat('dddd, D MMM Y') }}</span>
                            <h5 class="text-xs font-bold text-slate-800 mt-0.5">{{ $up->title }}</h5>
                        </div>
                        @empty
                        <p class="text-xs text-slate-400 py-2">Belum ada agenda terdekat.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-emerald-900 text-white rounded-2xl p-6 shadow-md relative overflow-hidden">
                    <div class="relative z-10">
                        <h4 class="font-bold text-base text-gold-400 mb-2">Informasi Jadwal</h4>
                        <p class="text-xs text-emerald-100 leading-relaxed">
                            Jadwal dapat berubah sewaktu-waktu menyesuaikan surat edaran Kementerian Agama Kabupaten Blitar dan keputusan Rapat Dewan Guru.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
