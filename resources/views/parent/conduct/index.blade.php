@extends('layouts.portal')

@section('title', 'Raport Karakter & Disiplin Ananda')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Raport Karakter & Disiplin Ananda</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pemantauan rekam jejak akhlak terpuji, apresiasi prestasi, dan pembinaan kedisiplinan ananda di madrasah.</p>
        </div>

        @if($students->count() > 1)
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400">Pilih Ananda:</span>
            <div class="flex gap-1.5">
                @foreach($students as $st)
                <a href="{{ route('parent.conduct.index', ['student_id' => $st->id]) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $selectedStudent && $selectedStudent->id == $st->id ? 'bg-amber-500 text-emerald-950 shadow-sm' : 'bg-slate-100 text-slate-600' }}">
                    {{ $st->nickname ?? $st->full_name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @if($selectedStudent)
    <!-- Skor Karakter Banner -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Skor Utama -->
        <div class="bg-gradient-to-tr from-emerald-950 to-emerald-900 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-gold-400">Total Akumulasi Skor</span>
                <span class="block text-4xl font-black font-mono mt-1 text-white">{{ $totalScore }}</span>
            </div>
            <div class="mt-4 pt-3 border-t border-emerald-800/80 flex items-center justify-between text-xs text-emerald-200">
                <span>Predikat Karakter:</span>
                <strong class="text-gold-400">
                    @if($totalScore >= 115) Mumtaz (Teladan)
                    @elseif($totalScore >= 95) Jayyid (Baik)
                    @else Bimbingan Edukatif @endif
                </strong>
            </div>
        </div>

        <!-- Standar Poin Awal -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Poin Dasar Awal</span>
            <span class="block text-2xl font-extrabold text-slate-800 font-mono mt-1">100</span>
            <p class="text-[11px] text-slate-500 mt-2">Diberikan secara penuh di awal tahun ajaran.</p>
        </div>

        <!-- Apresiasi Positif -->
        <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Poin Apresiasi (+)</span>
            <span class="block text-2xl font-extrabold text-emerald-850 font-mono mt-1">+{{ $totalApresiasi }}</span>
            <p class="text-[11px] text-slate-500 mt-2">Dari kebaikan akhlak, prestasi & keteladanan.</p>
        </div>

        <!-- Pembinaan Pelanggaran -->
        <div class="bg-white border border-rose-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">Poin Pembinaan (-)</span>
            <span class="block text-2xl font-extrabold text-rose-700 font-mono mt-1">-{{ $totalPelanggaran }}</span>
            <p class="text-[11px] text-slate-500 mt-2">Catatan kedisiplinan yang perlu dibimbing.</p>
        </div>
    </div>

    <!-- Riwayat Catatan Karakter Santri -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Buku Penghubung Karakter</span>
                <h3 class="text-base font-extrabold text-slate-900 mt-0.5">Catatan Penghargaan & Pembinaan dari Asatidz</h3>
            </div>
            <span class="text-xs text-slate-400">{{ $records->count() }} Catatan</span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($records as $rec)
                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 first:pt-0">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            @if($rec->type === 'apresiasi')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-850">
                                    Apresiasi (+{{ $rec->points }})
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">
                                    Bimbingan (-{{ $rec->points }})
                                </span>
                            @endif
                            <span class="text-xs text-slate-400 font-mono">{{ $rec->incident_date->isoFormat('D MMMM Y') }}</span>
                            <span class="text-xs text-slate-500">• Oleh: <strong>{{ $rec->recorder->name ?? 'Asatidz' }}</strong></span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">{{ $rec->title }}</h4>
                        @if($rec->description)
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $rec->description }}</p>
                        @endif
                        @if($rec->follow_up_action)
                            <div class="pt-1">
                                <span class="text-[11px] text-emerald-800 font-semibold bg-emerald-50 px-2.5 py-1 rounded-lg inline-block">
                                    Tindakan / Apresiasi: {{ $rec->follow_up_action }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-400 text-xs italic">
                    Belum ada catatan pembiasaan karakter atau pelanggaran untuk ananda. Terus dukung kebiasaan baik ananda di rumah!
                </div>
            @endforelse
        </div>
    </div>
    @endif
</div>
@endsection
