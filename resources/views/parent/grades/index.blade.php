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
            <div class="bg-amber-50 px-4 py-2.5 rounded-2xl border border-amber-200 text-xs text-amber-900 font-semibold">
                Tahun Ajaran 2026/2027 Ganjil
            </div>
        </div>

        <!-- Grades Table -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
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
        </div>
    @endif

</div>
@endsection
