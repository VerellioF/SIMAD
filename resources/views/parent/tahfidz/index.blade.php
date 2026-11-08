@extends('layouts.portal')

@section('title', 'Mutaba\'ah Tahfidz Qur\'an')

@section('content')
<div class="space-y-6">

    <!-- Header & Student Switcher -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Mutaba'ah Tahfidz Al-Qur'an</h1>
            <p class="text-xs text-slate-500 mt-0.5">Catatan perkembangan setoran hafalan Juz 30 ananda tercinta</p>
        </div>

        @if($students->count() > 1)
            <div>
                <form action="{{ route('parent.tahfidz.index') }}" method="GET">
                    <select name="student_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white">
                        @foreach($students as $st)
                            <option value="{{ $st->id }}" {{ $selectedStudent?->id === $st->id ? 'selected' : '' }}>
                                Santri: {{ $st->full_name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        @endif
    </div>

    @if(!$selectedStudent)
        <div class="bg-white rounded-3xl p-8 border border-slate-200 text-center text-slate-400">
            Belum ada santri yang ditautkan ke akun ini.
        </div>
    @else
        <!-- Tahfidz Target & Achievement Banner -->
        <div class="bg-gradient-to-r from-emerald-950 via-emerald-900 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white border border-gold-500/40 shadow-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
            <div class="space-y-2">
                <span class="inline-block px-3 py-1 rounded-full bg-gold-500 text-emerald-950 text-xs font-bold uppercase tracking-wider">
                    Program Unggulan MI Plus
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white">Target Capaian: Juz 30 (Juz 'Amma)</h2>
                <p class="text-xs sm:text-sm text-emerald-200 max-w-xl">
                    Santri dibimbing menghafal secara berurutan mulai dari An-Naas hingga An-Naba' dengan memperhatikan makharijul huruf dan hukum tajwid.
                </p>
            </div>

            <div class="flex items-center gap-4 bg-emerald-900/60 p-4 rounded-2xl border border-emerald-800">
                <div class="text-center">
                    <span class="block text-3xl font-extrabold text-gold-400 font-mono">{{ $totalSetoran }}</span>
                    <span class="block text-[10px] text-emerald-200 uppercase font-semibold">Total Setoran</span>
                </div>
                <div class="w-px h-10 bg-emerald-800"></div>
                <div class="text-center">
                    <span class="block text-3xl font-extrabold text-emerald-300 font-mono">{{ $lancarCount }}</span>
                    <span class="block text-[10px] text-emerald-200 uppercase font-semibold">Predikat Lancar</span>
                </div>
            </div>
        </div>

        <!-- History Records Table -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Jurnal Hafalan: {{ $selectedStudent->full_name }}</h3>
                    <p class="text-xs text-slate-500">Evaluasi dan mutaba'ah langsung oleh Asatidz pembimbing</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-6">Tanggal</th>
                            <th class="py-3.5 px-6">Surat & Rentang Ayat</th>
                            <th class="py-3.5 px-6">Kelancaran</th>
                            <th class="py-3.5 px-6">Tajwid</th>
                            <th class="py-3.5 px-6">Catatan Asatidz</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($records as $rec)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6 font-mono font-bold text-slate-800">
                                    {{ $rec->date->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-bold text-emerald-950 block text-sm">QS. {{ $rec->surah_name }}</span>
                                    <span class="text-[11px] text-slate-500 font-mono">{{ $rec->verse_range }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $rec->fluency_grade->value === 'lancar' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                        {{ $rec->fluency_grade->value === 'lancar' ? 'Lancar & Fasih' : ($rec->fluency_grade->value === 'kurang_lancar' ? 'Kurang Lancar' : 'Mengulang') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $rec->tajweed_grade->value === 'mumtaz' ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($rec->tajweed_grade->value) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-600 max-w-sm">
                                    {{ $rec->notes ?? '-' }}
                                    @if($rec->teacher)
                                        <span class="block text-[10px] text-slate-400 mt-0.5">Penguji: {{ $rec->teacher->name }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    Belum ada catatan setoran hafalan untuk ananda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($records instanceof \Illuminate\Pagination\LengthAwarePaginator && $records->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
