@extends('layouts.portal')

@section('title', 'Buku Nilai Raport & Penilaian Santri')

@section('content')
<div class="space-y-6" x-data="{
    searchStudent: '',
    fillKkm(val) {
        document.querySelectorAll('input.grade-score-input').forEach(el => {
            if (!el.value || el.value === '') {
                el.value = val;
                el.dispatchEvent(new Event('input'));
            }
        });
    },
    fillPrompt() {
        let val = prompt('Masukkan skor nilai seragam (0 - 100):', '80');
        if (val !== null && !isNaN(val) && val >= 0 && val <= 100) {
            document.querySelectorAll('input.grade-score-input').forEach(el => {
                el.value = val;
                el.dispatchEvent(new Event('input'));
            });
        }
    },
    clearAll() {
        if (confirm('Yakin ingin mengosongkan semua isian nilai pada form ini?')) {
            document.querySelectorAll('input.grade-score-input').forEach(el => {
                el.value = '';
                el.dispatchEvent(new Event('input'));
            });
        }
    }
}">

    <!-- Header & Filter Form -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-teal-100 text-teal-800 border border-teal-200">
                    Buku Raport Digital
                </span>
                <span class="text-xs text-slate-400">Standar Penilaian Kemenag & Kurikulum Merdeka</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">Buku Penilaian Akademik</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola skor Ulangan Harian (UH), Tugas Mandiri, PTS, dan PAS santri untuk seluruh kelas dan mata pelajaran yang Anda ampu.
            </p>
        </div>

        <!-- Filter Form: Kelas, Mapel, Tipe Nilai -->
        <form action="{{ route('teacher.grades.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <!-- Filter Rombel Kelas -->
            <select name="classroom_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-teal-700">
                @foreach($allClassrooms as $cls)
                    <option value="{{ $cls->id }}" {{ $classroom && $classroom->id == $cls->id ? 'selected' : '' }}>
                        {{ $cls->name }} (Kelas {{ $cls->grade_level }})
                    </option>
                @endforeach
            </select>

            <!-- Filter Mata Pelajaran -->
            <select name="subject_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-teal-700">
                @foreach($subjects as $sub)
                    <option value="{{ $sub->id }}" {{ $selectedSubjectId == $sub->id ? 'selected' : '' }}>
                        {{ $sub->name }} ({{ $sub->code ?? 'MAPEL' }})
                    </option>
                @endforeach
            </select>

            <!-- Filter Jenis Penilaian -->
            <select name="grade_type" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-teal-700">
                <option value="harian" {{ $gradeType === 'harian' ? 'selected' : '' }}>Ulangan Harian (UH)</option>
                <option value="tugas" {{ $gradeType === 'tugas' ? 'selected' : '' }}>Tugas Mandiri / PR</option>
                <option value="pts" {{ $gradeType === 'pts' ? 'selected' : '' }}>Penilaian Tengah Semester (PTS)</option>
                <option value="pas" {{ $gradeType === 'pas' ? 'selected' : '' }}>Penilaian Akhir Semester (PAS)</option>
            </select>
        </form>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(!$classroom || $subjects->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-3xl p-8 text-center text-amber-900 text-xs">
            Belum ada mata pelajaran terdaftar atau akun Anda belum memiliki rombel yang dapat dinilai.
        </div>
    @else
        <!-- Statistik & Fitur Cepat Pengisian Nilai -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Active Subject Info -->
            <div class="bg-gradient-to-tr from-teal-950 via-teal-900 to-teal-950 text-white rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Target Asesmen</span>
                    <h3 class="text-xl font-black mt-1 text-white">{{ $subject?->name }}</h3>
                    <p class="text-xs text-teal-200 mt-1">
                        Rombel: <strong class="text-white">{{ $classroom->name }}</strong> • Tipe: <span class="uppercase font-bold text-amber-300">{{ $gradeType }}</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-teal-800 flex items-center justify-between text-xs text-teal-200">
                    <span>Standar KKM:</span>
                    <strong class="text-amber-400 font-mono text-sm">75.0</strong>
                </div>
            </div>

            <!-- Rata-rata & Ketuntasan -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rata-Rata Nilai</span>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-3xl font-black text-slate-800 font-mono">{{ $averageScore }}</span>
                    <span class="text-xs text-slate-400">/ 100</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-emerald-700 font-bold">Tuntas: {{ $passedCount }} santri</span>
                    <span class="text-amber-700 font-bold">Remidi: {{ $remedialCount }} santri</span>
                </div>
            </div>

            <!-- Rentang Skor Tertinggi & Terendah -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rentang Skor Kelas</span>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div class="bg-emerald-50 rounded-xl p-2.5 text-center border border-emerald-200">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase block">Tertinggi</span>
                        <span class="text-xl font-black font-mono text-emerald-900">{{ $maxScore }}</span>
                    </div>
                    <div class="bg-rose-50 rounded-xl p-2.5 text-center border border-rose-200">
                        <span class="text-[10px] font-bold text-rose-800 uppercase block">Terendah</span>
                        <span class="text-xl font-black font-mono text-rose-900">{{ $minScore }}</span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 text-center">Berdasarkan data tersimpan saat ini</p>
            </div>

            <!-- Fitur Cepat Pengisian Form -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fitur Pintar Input</span>
                <button type="button" @click="fillKkm(75)"
                        class="w-full py-2 px-3 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-900 font-bold text-xs border border-teal-200 transition text-left flex items-center justify-between">
                    <span>⚡ Isi KKM (75) yang kosong</span>
                    <span class="font-mono text-[10px]">75</span>
                </button>
                <div class="flex gap-2">
                    <button type="button" @click="fillPrompt()"
                            class="flex-1 py-1.5 px-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                        Isi Seragam...
                    </button>
                    <button type="button" @click="clearAll()"
                            class="flex-1 py-1.5 px-2 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-900 text-[11px] font-bold transition">
                        Reset Form
                    </button>
                </div>
            </div>
        </div>

        <!-- Grade Entry Table Form -->
        <form action="{{ route('teacher.grades.store') }}" method="POST">
            @csrf
            <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
            <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
            <input type="hidden" name="grade_type" value="{{ $gradeType }}">

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">
                            Lembar Penilaian: {{ $subject?->name }} ({{ strtoupper($gradeType) }})
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Tekan <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-200 rounded text-[10px] font-mono">Tab</kbd> untuk berpindah cepat ke santri berikutnya.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="text" x-model="searchStudent" placeholder="Cari santri..."
                               class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-700">

                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-teal-900 hover:bg-teal-800 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 whitespace-nowrap">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Semua Nilai</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-6 w-12 text-center">No</th>
                                <th class="py-3 px-6">NISN / Nama Santri</th>
                                <th class="py-3 px-6 w-44">Skor Nilai (0 - 100)</th>
                                <th class="py-3 px-6 w-32 text-center">Status</th>
                                <th class="py-3 px-6">Catatan Evaluasi / Rekomendasi Guru</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $idx => $st)
                                @php
                                    $existing = $existingGrades->get($st->id);
                                    $score = old("grades.{$idx}.score", $existing?->score);
                                    $notes = old("grades.{$idx}.notes", $existing?->notes ?? '');
                                @endphp
                                <tr class="hover:bg-slate-50 transition"
                                    x-data="{ currentScore: '{{ $score !== null ? (float)$score : '' }}' }"
                                    x-show="!searchStudent || '{{ strtolower($st->full_name) }}'.includes(searchStudent.toLowerCase())">
                                    <td class="py-3.5 px-6 text-center font-mono text-slate-400">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <input type="hidden" name="grades[{{ $idx }}][student_id]" value="{{ $st->id }}">
                                        <span class="font-bold text-slate-900 block">{{ $st->full_name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $st->nisn ?? $st->nis }}</span>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <div class="relative">
                                            <input type="number" step="0.5" min="0" max="100"
                                                   name="grades[{{ $idx }}][score]"
                                                   x-model="currentScore"
                                                   class="grade-score-input w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-700 bg-white"
                                                   placeholder="0 - 100">
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-6 text-center">
                                        <template x-if="currentScore === '' || currentScore === null">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-400">
                                                Belum Diisi
                                            </span>
                                        </template>
                                        <template x-if="currentScore !== '' && currentScore !== null && parseFloat(currentScore) >= 75">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                ✓ Tuntas
                                            </span>
                                        </template>
                                        <template x-if="currentScore !== '' && currentScore !== null && parseFloat(currentScore) < 75">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                Remedial
                                            </span>
                                        </template>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <input type="text" name="grades[{{ $idx }}][notes]"
                                               value="{{ $notes }}"
                                               placeholder="Catatan hasil belajar, kelebihan, atau tindak lanjut..."
                                               class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-700">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 text-xs">
                                        Belum ada santri terdaftar di rombel kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        Nilai tersimpan langsung terintegrasi dengan raport digital wali santri.
                    </span>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-teal-900 hover:bg-teal-800 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Semua Nilai</span>
                    </button>
                </div>
            </div>
        </form>
    @endif

</div>
@endsection
