@extends('layouts.portal')

@section('title', 'Jadwal Pelajaran KBM Ananda')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Jadwal Pelajaran Ananda</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Roster kegiatan belajar mengajar mingguan di kelas ananda.</p>
        </div>

        @if($students->count() > 1)
        <!-- Switch Student if multiple -->
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400">Pilih Ananda:</span>
            <div class="flex gap-1.5">
                @foreach($students as $st)
                <a href="{{ route('parent.schedules.index', ['student_id' => $st->id]) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $selectedStudent && $selectedStudent->id == $st->id ? 'bg-amber-500 text-emerald-950 shadow-sm' : 'bg-slate-100 text-slate-600' }}">
                    {{ $st->nickname ?? $st->full_name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @if($selectedStudent)
        <!-- Info Banner Ananda -->
        <div class="bg-amber-50 border border-amber-200/80 p-4 rounded-2xl flex items-center justify-between text-xs text-amber-900">
            <div class="flex items-center gap-2 font-bold">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Ananda {{ $selectedStudent->full_name }} (Kelas {{ $selectedStudent->classroom->name ?? '-' }})</span>
            </div>
            <span class="text-[11px] font-semibold text-amber-800">T.A. {{ $activeYear->name ?? '2026/2027' }}</span>
        </div>

        <!-- Weekly Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($days as $day)
                @php
                    $daySlots = $schedules->get($day, collect());
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="px-5 py-3.5 bg-gradient-to-r from-emerald-950 to-emerald-900 text-white flex justify-between items-center">
                            <span class="font-extrabold text-sm uppercase tracking-wider">{{ ucfirst($day) }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/20 font-bold">{{ $daySlots->count() }} Mapel</span>
                        </div>

                        <div class="p-4 divide-y divide-slate-100 space-y-3">
                            @forelse($daySlots as $slot)
                                <div class="pt-3 first:pt-0">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-bold font-mono">Jam ke-{{ $slot->period_order }}</span>
                                        <span class="text-xs font-mono font-bold text-emerald-800">{{ $slot->start_time }} - {{ $slot->end_time }}</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-sm mt-1.5">{{ $slot->subject->name ?? 'Mata Pelajaran' }}</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Guru: {{ $slot->teacher->name ?? 'Guru Pengampu' }}</p>
                                </div>
                            @empty
                                <div class="py-6 text-center text-slate-400 text-xs italic">
                                    Tidak ada KBM terjadwal hari {{ $day }}.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white p-12 text-center rounded-2xl border border-slate-200/80 text-slate-400 text-xs">
            Data ananda tidak ditemukan.
        </div>
    @endif
</div>
@endsection
