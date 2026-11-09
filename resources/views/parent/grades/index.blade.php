@extends('layouts.portal')

@section('title', 'Nilai & Raport Digital')

@section('content')
<div class="space-y-6">

    <!-- Header & Student Switcher -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Lembar Hasil Belajar (Raport)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau nilai capaian akademik ananda per mata pelajaran</p>
        </div>

        @if($students->count() > 1)
            <div>
                <form action="{{ route('parent.grades.index') }}" method="GET">
                    <select name="student_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white">
                        @foreach($students as $st)
                            <option value="{{ $st->id }}" {{ $selectedStudent?->id === $st->id ? 'selected' : '' }}>
                                Siswa: {{ $st->full_name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        @endif
    </div>

    @if(!$selectedStudent)
        <div class="bg-white rounded-3xl p-8 border border-slate-200 text-center text-slate-400">
            Belum ada siswa yang ditautkan ke akun ini.
        </div>
    @else
        <!-- Student Header Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="space-y-1">
                <span class="text-xs uppercase font-bold text-slate-400">Nama Siswa:</span>
                <h2 class="text-xl font-extrabold text-slate-900">{{ $selectedStudent->full_name }}</h2>
                <p class="text-xs text-slate-500">NISN: {{ $selectedStudent->nisn }} • Kelas: {{ $selectedStudent->classroom->name ?? '-' }}</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('parent.grades.report_card', $selectedStudent) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh Rapor Semester (PDF)</span>
                </a>
                <a href="{{ route('parent.students.card', $selectedStudent) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    <span>🪪 Kartu Siswa (KTS)</span>
                </a>
            </div>
        </div>

        <!-- Grades Table (Desktop) & Cards (Mobile) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Desktop Table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-6">Mata Pelajaran</th>
                            <th class="py-3.5 px-6">Kategori</th>
                            <th class="py-3.5 px-6 text-center">Harian (UH)</th>
                            <th class="py-3.5 px-6 text-center">Tugas</th>
                            <th class="py-3.5 px-6 text-center">PTS</th>
                            <th class="py-3.5 px-6 text-center">PAS</th>
                            <th class="py-3.5 px-6 text-center">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($subjects as $sub)
                            @php
                                $subGrades = $grades->get($sub->id, collect());
                                $harian = $subGrades->firstWhere('grade_type.value', 'harian')?->score;
                                $tugas = $subGrades->firstWhere('grade_type.value', 'tugas')?->score;
                                $pts = $subGrades->firstWhere('grade_type.value', 'pts')?->score;
                                $pas = $subGrades->firstWhere('grade_type.value', 'pas')?->score;

                                $scores = array_filter([(float)$harian, (float)$tugas, (float)$pts, (float)$pas], fn($s) => $s > 0);
                                $avg = count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : null;
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $sub->name }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $sub->category->value === 'pai' ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $sub->category->value ?? 'Umum' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center font-mono font-bold text-slate-800">
                                    {{ $harian !== null ? (float)$harian : '-' }}
                                </td>
                                <td class="py-4 px-6 text-center font-mono font-bold text-slate-800">
                                    {{ $tugas !== null ? (float)$tugas : '-' }}
                                </td>
                                <td class="py-4 px-6 text-center font-mono font-bold text-slate-800">
                                    {{ $pts !== null ? (float)$pts : '-' }}
                                </td>
                                <td class="py-4 px-6 text-center font-mono font-bold text-slate-800">
                                    {{ $pas !== null ? (float)$pas : '-' }}
                                </td>
                                <td class="py-4 px-6 text-center font-mono font-extrabold {{ $avg !== null ? ($avg >= 75 ? 'text-emerald-700' : 'text-amber-700') : 'text-slate-300' }}">
                                    {{ $avg !== null ? $avg : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    Belum ada mata pelajaran terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Responsive Card View -->
            <div class="sm:hidden p-4 space-y-3">
                @forelse($subjects as $sub)
                    @php
                        $subGrades = $grades->get($sub->id, collect());
                        $harian = $subGrades->firstWhere('grade_type.value', 'harian')?->score;
                        $tugas = $subGrades->firstWhere('grade_type.value', 'tugas')?->score;
                        $pts = $subGrades->firstWhere('grade_type.value', 'pts')?->score;
                        $pas = $subGrades->firstWhere('grade_type.value', 'pas')?->score;

                        $scores = array_filter([(float)$harian, (float)$tugas, (float)$pts, (float)$pas], fn($s) => $s > 0);
                        $avg = count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : null;
                    @endphp
                    <div class="p-3.5 rounded-2xl border border-slate-200/80 bg-slate-50/50 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-xs text-slate-900">{{ $sub->name }}</span>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $sub->category->value === 'pai' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                {{ $sub->category->value ?? 'Umum' }}
                            </span>
                        </div>
                        <div class="grid grid-cols-4 gap-1.5 text-center font-mono">
                            <div class="p-1.5 rounded-xl bg-white border border-slate-100">
                                <span class="text-[9px] text-slate-400 block">UH</span>
                                <span class="text-xs font-bold text-slate-800">{{ $harian !== null ? (float)$harian : '-' }}</span>
                            </div>
                            <div class="p-1.5 rounded-xl bg-white border border-slate-100">
                                <span class="text-[9px] text-slate-400 block">Tugas</span>
                                <span class="text-xs font-bold text-slate-800">{{ $tugas !== null ? (float)$tugas : '-' }}</span>
                            </div>
                            <div class="p-1.5 rounded-xl bg-white border border-slate-100">
                                <span class="text-[9px] text-slate-400 block">PTS</span>
                                <span class="text-xs font-bold text-slate-800">{{ $pts !== null ? (float)$pts : '-' }}</span>
                            </div>
                            <div class="p-1.5 rounded-xl bg-white border border-slate-100">
                                <span class="text-[9px] text-slate-400 block">PAS</span>
                                <span class="text-xs font-bold text-slate-800">{{ $pas !== null ? (float)$pas : '-' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-xs">
                            <span class="text-[11px] text-slate-500 font-semibold">Rata-rata Semester:</span>
                            <span class="font-mono font-black text-sm {{ $avg !== null ? ($avg >= 75 ? 'text-emerald-700' : 'text-amber-700') : 'text-slate-400' }}">
                                {{ $avg !== null ? $avg : 'Belum Ada' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        Belum ada mata pelajaran terdaftar.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>
@endsection
