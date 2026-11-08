@extends('layouts.portal')

@section('title', 'Buku Nilai Raport')

@section('content')
<div class="space-y-6">

    <!-- Header & Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Buku Penilaian Akademik</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelas: <strong>{{ $classroom->name ?? 'Belum Ada Rombel' }}</strong> • Input nilai harian, tugas, PTS, dan PAS santri
            </p>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('teacher.grades.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <select name="subject_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white">
                @foreach($subjects as $sub)
                    <option value="{{ $sub->id }}" {{ $selectedSubjectId == $sub->id ? 'selected' : '' }}>
                        {{ $sub->name }} ({{ strtoupper($sub->category->value ?? 'Umum') }})
                    </option>
                @endforeach
            </select>

            <select name="grade_type" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white">
                <option value="harian" {{ $gradeType === 'harian' ? 'selected' : '' }}>Nilai Harian (UH)</option>
                <option value="tugas" {{ $gradeType === 'tugas' ? 'selected' : '' }}>Nilai Tugas / PR</option>
                <option value="pts" {{ $gradeType === 'pts' ? 'selected' : '' }}>Penilaian Tengah Semester (PTS)</option>
                <option value="pas" {{ $gradeType === 'pas' ? 'selected' : '' }}>Penilaian Akhir Semester (PAS)</option>
            </select>
        </form>
    </div>

    @if(!$classroom || $subjects->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-amber-900 text-xs">
            Belum ada mata pelajaran terdaftar atau akun Anda belum ditugaskan ke rombel aktif.
        </div>
    @else
        <form action="{{ route('teacher.grades.store') }}" method="POST">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
            <input type="hidden" name="grade_type" value="{{ $gradeType }}">

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            Entri Nilai: {{ $subjects->firstWhere('id', $selectedSubjectId)?->name }}
                        </h3>
                        <span class="text-xs text-slate-500 uppercase font-semibold">Tipe: {{ strtoupper($gradeType) }} • Skala 0 - 100</span>
                    </div>

                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Semua Nilai</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-6 w-12 text-center">No</th>
                                <th class="py-3 px-6">NISN / Nama Santri</th>
                                <th class="py-3 px-6 w-32">Skor Nilai (0-100)</th>
                                <th class="py-3 px-6">Catatan / Evaluasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $idx => $st)
                                @php
                                    $existing = $existingGrades->get($st->id);
                                    $score = old("grades.{$idx}.score", $existing?->score);
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3.5 px-6 text-center font-mono text-slate-400">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <input type="hidden" name="grades[{{ $idx }}][student_id]" value="{{ $st->id }}">
                                        <span class="font-bold text-slate-900 block">{{ $st->full_name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $st->nisn }}</span>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <input type="number" step="0.5" min="0" max="100"
                                               name="grades[{{ $idx }}][score]"
                                               value="{{ $score !== null ? (float)$score : '' }}"
                                               placeholder="0 - 100"
                                               class="w-28 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-mono font-bold focus:ring-1 focus:ring-teal-600">
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <input type="text" name="grades[{{ $idx }}][notes]"
                                               value="{{ old("grades.{$idx}.notes", $existing?->notes) }}"
                                               placeholder="Opsional (cth: remidi, sangat baik)"
                                               class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-teal-600">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400">
                                        Belum ada santri terdaftar di rombel ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->isNotEmpty())
                    <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-100 flex justify-end">
                        <button type="submit"
                                class="px-8 py-3 rounded-xl bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs shadow transition flex items-center gap-2">
                            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Nilai Santri</span>
                        </button>
                    </div>
                @endif
            </div>
        </form>
    @endif

</div>
@endsection
