@extends('layouts.portal')

@section('title', 'Panduan Kurikulum & Capaian Pembelajaran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Panduan Kurikulum & Capaian Pembelajaran</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pedoman struktur kurikulum, alokasi jam tatap muka (JP), dan target capaian kompetensi santri per fase.</p>
        </div>

        <!-- Filter Fase -->
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400">Pilih Fase:</span>
            <div class="flex gap-1.5">
                <a href="{{ route('teacher.curriculum.index', ['phase' => 'fase_a']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $phase === 'fase_a' ? 'bg-teal-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600' }}">Fase A (1-2)</a>
                <a href="{{ route('teacher.curriculum.index', ['phase' => 'fase_b']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $phase === 'fase_b' ? 'bg-teal-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600' }}">Fase B (3-4)</a>
                <a href="{{ route('teacher.curriculum.index', ['phase' => 'fase_c']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $phase === 'fase_c' ? 'bg-teal-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600' }}">Fase C (5-6)</a>
            </div>
        </div>
    </div>

    <!-- Groups -->
    <div class="space-y-6">
        @foreach([
            'kemenag_pai' => 'Kelompok PAI & Bahasa Arab (Kemenag)',
            'nasional_merdeka' => 'Kelompok Kurikulum Nasional / Merdeka',
            'muatan_lokal_pesantren' => 'Kelompok Muatan Lokal & Kepesantrenan Plus'
        ] as $typeKey => $title)
            @php
                $items = $curriculums->get($typeKey, collect());
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-4 bg-teal-50/50 border-b border-teal-100 flex justify-between items-center">
                    <h3 class="font-bold text-sm text-teal-950 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span>
                        {{ $title }}
                    </h3>
                    <span class="text-xs font-mono font-bold text-teal-800">{{ $items->sum('hours_per_week') }} JP / Minggu</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($items as $item)
                    <div class="p-5 space-y-2 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h4 class="font-extrabold text-sm text-slate-900">{{ $item->name }}</h4>
                                @if($item->code)<span class="text-[10px] text-slate-400 font-mono font-bold">[{{ $item->code }}]</span>@endif
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-800 font-mono font-bold text-xs">
                                {{ $item->hours_per_week }} JP
                            </span>
                        </div>
                        @if($item->competency_goals)
                            <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <strong class="text-slate-800 block mb-0.5">Capaian Pembelajaran (CP):</strong>
                                {{ $item->competency_goals }}
                            </div>
                        @endif
                        @if($item->syllabus_summary)
                            <p class="text-xs text-slate-500">
                                <strong>Materi Pokok:</strong> {{ $item->syllabus_summary }}
                            </p>
                        @endif
                    </div>
                    @empty
                    <p class="p-5 text-center text-slate-400 text-xs italic">Belum ada mata pelajaran pada kelompok ini untuk fase ini.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
