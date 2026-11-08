@extends('layouts.portal')

@section('title', 'Jadwal Mengajar Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Jadwal Mengajar Guru <span class="sr-only">Jadwal Mengajar Asatidz</span></h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Roster jadwal tatap muka Anda di kelas untuk Tahun Ajaran {{ $activeYear->name ?? '2026/2027' }}.</p>
        </div>
        <div class="px-4 py-2 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 font-bold text-xs">
            Beban Mengajar: {{ $totalTeachingHours }} Jam Pelajaran / Minggu
        </div>
    </div>

    <!-- Schedules Weekly Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($days as $day)
            @php
                $daySlots = $schedules->get($day, collect());
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="px-5 py-3.5 bg-gradient-to-r from-teal-900 to-teal-800 text-white flex justify-between items-center">
                        <span class="font-extrabold text-sm uppercase tracking-wider">{{ ucfirst($day) }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/20 font-bold">{{ $daySlots->count() }} Pertemuan</span>
                    </div>

                    <div class="p-4 divide-y divide-slate-100 space-y-3">
                        @forelse($daySlots as $slot)
                            <div class="pt-3 first:pt-0">
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded bg-teal-50 text-teal-900 text-[10px] font-bold font-mono">Jam ke-{{ $slot->period_order }}</span>
                                    <span class="text-xs font-mono font-bold text-slate-600">{{ $slot->start_time }} - {{ $slot->end_time }}</span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm mt-1.5">{{ $slot->subject->name ?? 'Mata Pelajaran' }}</h4>
                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                    <span class="font-bold text-emerald-850 bg-emerald-50 px-2 py-0.5 rounded">Kelas {{ $slot->classroom->name ?? '-' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-400 text-xs italic">
                                Tidak ada jadwal mengajar pada hari {{ $day }}.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
