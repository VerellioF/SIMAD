@extends('layouts.portal')

@section('title', 'Buku Nilai Raport & Penilaian Siswa')

@section('content')
<div class="space-y-5" x-data="{
    viewMode: '{{ $viewMode }}',
    searchStudent: '',
    activeCol: 'harian',
    selectedStudentModal: null,

    // Raw initial student grades data from backend
    students: [
        @foreach($students as $idx => $st)
            @php
                $map = $studentGradeMap[$st->id] ?? [
                    'harian' => '',
                    'tugas' => '',
                    'pts' => '',
                    'pas' => '',
                    'notes' => '',
                ];
            @endphp
            {
                id: {{ $st->id }},
                nis: '{{ $st->nis }}',
                nisn: '{{ $st->nisn ?? $st->nis }}',
                name: '{{ addslashes($st->full_name) }}',
                harian: '{{ $map['harian'] }}',
                tugas: '{{ $map['tugas'] }}',
                pts: '{{ $map['pts'] }}',
                pas: '{{ $map['pas'] }}',
                notes: '{{ addslashes($map['notes']) }}',
                reportUrl: '{{ route('teacher.grades.report_card', $st) }}'
            },
        @endforeach
    ],

    // Calculate final score for a student (Formatif UH/Tugas + PTS + PAS)
    getFinalScore(st) {
        let scores = [];
        let h = parseFloat(st.harian);
        let t = parseFloat(st.tugas);
        let p = parseFloat(st.pts);
        let a = parseFloat(st.pas);

        // Formatif avg
        let formatives = [];
        if (!isNaN(h)) formatives.push(h);
        if (!isNaN(t)) formatives.push(t);
        let formAvg = formatives.length > 0 ? (formatives.reduce((acc, v) => acc + v, 0) / formatives.length) : null;

        if (formAvg !== null) scores.push(formAvg);
        if (!isNaN(p)) scores.push(p);
        if (!isNaN(a)) scores.push(a);

        if (scores.length === 0) return null;
        let avg = scores.reduce((acc, v) => acc + v, 0) / scores.length;
        return Math.round(avg * 10) / 10;
    },

    getPredicate(score) {
        if (score === null || isNaN(score)) return '-';
        if (score >= 88) return 'A';
        if (score >= 76) return 'B';
        if (score >= 65) return 'C';
        return 'D';
    },

    getPredicateColor(score) {
        if (score === null || isNaN(score)) return 'bg-slate-100 text-slate-500';
        if (score >= 88) return 'bg-emerald-100 text-emerald-800 border-emerald-300';
        if (score >= 75) return 'bg-teal-100 text-teal-800 border-teal-300';
        if (score >= 65) return 'bg-amber-100 text-amber-800 border-amber-300';
        return 'bg-rose-100 text-rose-800 border-rose-300';
    },

    // Keyboard navigation: Enter / Down / Up / Left / Right
    focusCell(col, row) {
        if (row < 0 || row >= this.students.length) return;
        let el = document.getElementById(`cell-${col}-${row}`);
        if (el) {
            el.focus();
            el.select();
        }
    },

    // Auto-fill KKM (75) on empty cells of target column
    fillKkm(targetCol) {
        let col = targetCol || this.activeCol || 'harian';
        let count = 0;
        this.students.forEach(st => {
            if (st[col] === '' || st[col] === null) {
                st[col] = 75;
                count++;
            }
        });
        alert(`Berhasil mengisi nilai KKM (75) pada ${count} siswa untuk kolom ${col.toUpperCase()}.`);
    },

    // Fill uniform score
    fillUniform(targetCol) {
        let col = targetCol || this.activeCol || 'harian';
        let val = prompt(`Masukkan skor seragam (0 - 100) untuk kolom ${col.toUpperCase()}:`, '80');
        if (val !== null && !isNaN(val) && val >= 0 && val <= 100) {
            this.students.forEach(st => {
                st[col] = parseFloat(val);
            });
        }
    },

    // Auto-generate standard Kemenag/Kurikulum Merdeka notes based on final score
    autoGenerateNotes() {
        this.students.forEach(st => {
            let final = this.getFinalScore(st);
            let pred = this.getPredicate(final);
            if (pred === 'A') {
                st.notes = 'Menunjukkan penguasaan kompetensi materi dengan sangat baik dan mandiri.';
            } else if (pred === 'B') {
                st.notes = 'Menunjukkan pemahaman materi yang baik dan tuntas.';
            } else if (pred === 'C') {
                st.notes = 'Cukup menguasai capaian pembelajaran pokok, perlu sedikit pengulangan.';
            } else if (pred === 'D') {
                st.notes = 'Perlu bimbingan dan pendampingan intensif pada materi pembelajaran.';
            }
        });
        alert('Catatan deskripsi capaian kompetensi berhasil di-generate otomatis untuk seluruh siswa!');
    },

    // Export current table to CSV
    exportCsv() {
        let csvContent = 'data:text/csv;charset=utf-8,';
        csvContent += 'NIS,Nama Siswa,UH,Tugas,PTS,PAS,Catatan\n';
        this.students.forEach(st => {
            let row = [
                `\'${st.nis}\'`,
                `\'${st.name.replace(/'/g, '')}\'`,
                st.harian || '',
                st.tugas || '',
                st.pts || '',
                st.pas || '',
                `\'${(st.notes || '').replace(/'/g, '')}\'`
            ];
            csvContent += row.join(',') + '\n';
        });
        let encodedUri = encodeURI(csvContent);
        let link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'Buku_Nilai_{{ $classroom?->name }}_{{ $subject?->name }}.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    },

    // Import CSV file
    importCsv(e) {
        let file = e.target.files[0];
        if (!file) return;
        let reader = new FileReader();
        reader.onload = (event) => {
            let lines = event.target.result.split('\n');
            let updated = 0;
            for (let i = 1; i < lines.length; i++) {
                let line = lines[i].trim();
                if (!line) continue;
                let cols = line.split(',');
                if (cols.length >= 2) {
                    let nis = cols[0].replace(/['\x22]/g, '').trim();
                    let st = this.students.find(s => s.nis === nis);
                    if (st) {
                        if (cols[2] !== undefined && cols[2].trim() !== '') st.harian = parseFloat(cols[2].trim());
                        if (cols[3] !== undefined && cols[3].trim() !== '') st.tugas = parseFloat(cols[3].trim());
                        if (cols[4] !== undefined && cols[4].trim() !== '') st.pts = parseFloat(cols[4].trim());
                        if (cols[5] !== undefined && cols[5].trim() !== '') st.pas = parseFloat(cols[5].trim());
                        if (cols[6] !== undefined) st.notes = cols[6].replace(/['\x22]/g, '').trim();
                        updated++;
                    }
                }
            }
            alert(`Berhasil mengimpor data nilai untuk ${updated} siswa dari file CSV.`);
        };
        reader.readAsText(file);
    },

    // Modal Focus per Student
    openFocusModal(st) {
        this.selectedStudentModal = JSON.parse(JSON.stringify(st));
    },

    saveFocusModal() {
        if (!this.selectedStudentModal) return;
        let idx = this.students.findIndex(s => s.id === this.selectedStudentModal.id);
        if (idx !== -1) {
            this.students[idx].harian = this.selectedStudentModal.harian;
            this.students[idx].tugas = this.selectedStudentModal.tugas;
            this.students[idx].pts = this.selectedStudentModal.pts;
            this.students[idx].pas = this.selectedStudentModal.pas;
            this.students[idx].notes = this.selectedStudentModal.notes;
        }
        this.selectedStudentModal = null;
    }
}">

    <!-- Top Header: Filter & Mode Toggle -->
    <div class="bg-white px-6 py-4 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-teal-100 text-teal-900 border border-teal-200">
                    Buku Raport Digital
                </span>
                <span class="text-xs text-slate-400">Standar Penilaian Kemenag & Kurikulum Merdeka</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">Buku Penilaian Akademik</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola nilai formatif & sumatif terpadu untuk rombel <strong>{{ $classroom?->name }}</strong> — Mata Pelajaran: <strong>{{ $subject?->name }}</strong>
            </p>
        </div>

        <!-- Filter Form & Mode Switcher -->
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <!-- Filter Rombel & Mapel -->
            <form action="{{ route('teacher.grades.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="mode" :value="viewMode">
                
                <!-- Rombel Kelas -->
                <select name="classroom_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-teal-700">
                    @foreach($allClassrooms as $cls)
                        <option value="{{ $cls->id }}" {{ $classroom && $classroom->id == $cls->id ? 'selected' : '' }}>
                            {{ $cls->name }} (Kelas {{ $cls->grade_level }})
                        </option>
                    @endforeach
                </select>

                <!-- Mata Pelajaran -->
                <select name="subject_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-teal-700">
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}" {{ $selectedSubjectId == $sub->id ? 'selected' : '' }}>
                            {{ $sub->name }} ({{ $sub->code ?? 'MAPEL' }})
                        </option>
                    @endforeach
                </select>
            </form>

            <!-- Mode Switcher Pill -->
            <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 text-xs font-bold">
                <button type="button" @click="viewMode = 'spreadsheet'"
                        :class="viewMode === 'spreadsheet' ? 'bg-white text-teal-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <span>📊 Smart Spreadsheet</span>
                </button>
                <button type="button" @click="viewMode = 'single'"
                        :class="viewMode === 'single' ? 'bg-white text-teal-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <span>🎯 Mode Per Asesmen</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(!$classroom || $subjects->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-3xl p-8 text-center text-amber-900 text-xs">
            Belum ada mata pelajaran terdaftar atau akun Anda belum memiliki rombel yang dapat dinilai.
        </div>
    @else

        <!-- SMART SPREADSHEET MODE (DEFAULT) -->
        <div x-show="viewMode === 'spreadsheet'" class="space-y-4">
            
            <!-- Compact Real-time Class KPI Bar (Doesn't take half the screen!) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 px-5 py-3 shadow-sm flex flex-wrap items-center justify-between gap-4 text-xs">
                <div class="flex items-center gap-6 divide-x divide-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Target:</span>
                        <strong class="text-slate-800 font-bold">{{ $subject?->name }}</strong>
                        <span class="px-2 py-0.5 rounded bg-teal-50 text-teal-900 font-bold text-[10px] border border-teal-200">KKM: 75.0</span>
                    </div>

                    <div class="pl-6 flex items-center gap-4">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Total Siswa:</span>
                            <span class="font-extrabold text-slate-800 font-mono">{{ $students->count() }} Orang</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Ketuntasan:</span>
                            <span class="font-extrabold text-emerald-700">Tuntas: {{ $passedCount }}</span>
                            <span class="text-slate-300">/</span>
                            <span class="font-extrabold text-rose-700">Remidi: {{ $remedialCount }}</span>
                        </div>
                    </div>
                </div>

                <!-- Keyboard Hint -->
                <div class="hidden md:flex items-center gap-2 text-slate-500 text-[11px]">
                    <span class="inline-flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded text-[10px] font-mono">Enter</kbd> atau
                        <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded text-[10px] font-mono">↓</kbd>
                        <span>Turun Siswa</span>
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded text-[10px] font-mono">↑</kbd>
                        <span>Naik Siswa</span>
                    </span>
                </div>
            </div>

            <!-- Toolbar: Quick Actions & CSV Import/Export -->
            <div class="bg-gradient-to-r from-teal-950 via-teal-900 to-teal-950 text-white rounded-2xl px-5 py-3 shadow flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-amber-300 font-extrabold text-xs mr-1">⚡ Aksi Cerdas:</span>
                    
                    <button type="button" @click="fillKkm(activeCol)"
                            class="px-2.5 py-1.5 rounded-lg bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs border border-teal-600 transition flex items-center gap-1">
                        <span>Isi KKM (75)</span>
                    </button>

                    <button type="button" @click="fillUniform(activeCol)"
                            class="px-2.5 py-1.5 rounded-lg bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs border border-teal-600 transition flex items-center gap-1">
                        <span>Isi Seragam...</span>
                    </button>

                    <button type="button" @click="autoGenerateNotes()"
                            class="px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-emerald-950 font-bold text-xs shadow-sm transition flex items-center gap-1">
                        <span>✨ Generate Narasi Capaian</span>
                    </button>

                    <button type="button" @click="exportCsv()"
                            class="px-2.5 py-1.5 rounded-lg bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs border border-teal-600 transition flex items-center gap-1">
                        <span>📥 Unduh CSV</span>
                    </button>

                    <label class="px-2.5 py-1.5 rounded-lg bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs border border-teal-600 transition flex items-center gap-1 cursor-pointer">
                        <span>📤 Upload CSV</span>
                        <input type="file" accept=".csv" @change="importCsv($event)" class="hidden">
                    </label>
                </div>

                <div class="w-full md:w-56">
                    <input type="text" x-model="searchStudent" placeholder="Cari nama / NISN..."
                           class="w-full px-3 py-1.5 rounded-xl bg-teal-900 border border-teal-700 text-white placeholder-teal-300 text-xs focus:ring-2 focus:ring-amber-400">
                </div>
            </div>

            <!-- Spreadsheet Form -->
            <form action="{{ route('teacher.grades.store') }}" method="POST">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                <input type="hidden" name="mode" value="spreadsheet">

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-slate-700">
                                Lembar Matriks Penilaian: Formatif (UH & Tugas) + Sumatif (PTS & PAS)
                            </span>
                        </div>
                        <button type="submit"
                                class="px-5 py-2 rounded-xl bg-teal-900 hover:bg-teal-800 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Nilai Semua Siswa</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto max-h-[620px] overflow-y-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-100 text-slate-600 font-extrabold text-[10px] uppercase tracking-wider sticky top-0 z-10 border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-3 w-10 text-center bg-slate-100">No</th>
                                    <th class="py-3 px-4 min-w-[200px] bg-slate-100">Nama Siswa / NISN</th>
                                    <th class="py-3 px-2 w-24 text-center bg-slate-100" @click="activeCol = 'harian'">
                                        <div class="flex items-center justify-center gap-1 cursor-pointer">
                                            <span>UH (0-100)</span>
                                            <span x-show="activeCol === 'harian'" class="text-teal-700">●</span>
                                        </div>
                                    </th>
                                    <th class="py-3 px-2 w-24 text-center bg-slate-100" @click="activeCol = 'tugas'">
                                        <div class="flex items-center justify-center gap-1 cursor-pointer">
                                            <span>Tugas</span>
                                            <span x-show="activeCol === 'tugas'" class="text-teal-700">●</span>
                                        </div>
                                    </th>
                                    <th class="py-3 px-2 w-24 text-center bg-slate-100" @click="activeCol = 'pts'">
                                        <div class="flex items-center justify-center gap-1 cursor-pointer">
                                            <span>PTS</span>
                                            <span x-show="activeCol === 'pts'" class="text-teal-700">●</span>
                                        </div>
                                    </th>
                                    <th class="py-3 px-2 w-24 text-center bg-slate-100" @click="activeCol = 'pas'">
                                        <div class="flex items-center justify-center gap-1 cursor-pointer">
                                            <span>PAS</span>
                                            <span x-show="activeCol === 'pas'" class="text-teal-700">●</span>
                                        </div>
                                    </th>
                                    <th class="py-3 px-3 w-20 text-center bg-slate-100">Nilai Akhir</th>
                                    <th class="py-3 px-3 w-24 text-center bg-slate-100">Predikat</th>
                                    <th class="py-3 px-4 min-w-[280px] bg-slate-100">Catatan Capaian Pembelajaran</th>
                                    <th class="py-3 px-3 w-16 text-center bg-slate-100">Fokus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(st, idx) in students" :key="st.id">
                                    <tr class="hover:bg-teal-50/50 transition group"
                                        x-show="!searchStudent || st.name.toLowerCase().includes(searchStudent.toLowerCase()) || st.nisn.includes(searchStudent)">
                                        
                                        <!-- No -->
                                        <td class="py-2.5 px-3 text-center font-mono text-slate-400" x-text="idx + 1"></td>

                                        <!-- Nama Siswa & Link Rapor -->
                                        <td class="py-2.5 px-4">
                                            <input type="hidden" :name="`grades[${idx}][student_id]`" :value="st.id">
                                            <div class="flex items-center justify-between gap-2">
                                                <div>
                                                    <span class="font-extrabold text-slate-900 block" x-text="st.name"></span>
                                                    <span class="font-mono text-[10px] text-slate-400" x-text="st.nisn"></span>
                                                </div>
                                                <a :href="st.reportUrl" target="_blank"
                                                   class="px-2 py-0.5 rounded text-[10px] font-bold text-teal-800 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition"
                                                   title="Cetak Lembar Rapor">
                                                    📄 Rapor
                                                </a>
                                            </div>
                                        </td>

                                        <!-- UH Input -->
                                        <td class="py-2 px-2 text-center">
                                            <input type="number" step="0.5" min="0" max="100"
                                                   :id="`cell-harian-${idx}`"
                                                   :name="`grades[${idx}][harian]`"
                                                   x-model="st.harian"
                                                   @focus="activeCol = 'harian'"
                                                   @keydown.enter.prevent="focusCell('harian', idx + 1)"
                                                   @keydown.arrow-down.prevent="focusCell('harian', idx + 1)"
                                                   @keydown.arrow-up.prevent="focusCell('harian', idx - 1)"
                                                   class="w-full text-center py-1.5 px-1 rounded-lg border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-700 focus:bg-amber-50"
                                                   placeholder="-">
                                        </td>

                                        <!-- Tugas Input -->
                                        <td class="py-2 px-2 text-center">
                                            <input type="number" step="0.5" min="0" max="100"
                                                   :id="`cell-tugas-${idx}`"
                                                   :name="`grades[${idx}][tugas]`"
                                                   x-model="st.tugas"
                                                   @focus="activeCol = 'tugas'"
                                                   @keydown.enter.prevent="focusCell('tugas', idx + 1)"
                                                   @keydown.arrow-down.prevent="focusCell('tugas', idx + 1)"
                                                   @keydown.arrow-up.prevent="focusCell('tugas', idx - 1)"
                                                   class="w-full text-center py-1.5 px-1 rounded-lg border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-700 focus:bg-amber-50"
                                                   placeholder="-">
                                        </td>

                                        <!-- PTS Input -->
                                        <td class="py-2 px-2 text-center">
                                            <input type="number" step="0.5" min="0" max="100"
                                                   :id="`cell-pts-${idx}`"
                                                   :name="`grades[${idx}][pts]`"
                                                   x-model="st.pts"
                                                   @focus="activeCol = 'pts'"
                                                   @keydown.enter.prevent="focusCell('pts', idx + 1)"
                                                   @keydown.arrow-down.prevent="focusCell('pts', idx + 1)"
                                                   @keydown.arrow-up.prevent="focusCell('pts', idx - 1)"
                                                   class="w-full text-center py-1.5 px-1 rounded-lg border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-700 focus:bg-amber-50"
                                                   placeholder="-">
                                        </td>

                                        <!-- PAS Input -->
                                        <td class="py-2 px-2 text-center">
                                            <input type="number" step="0.5" min="0" max="100"
                                                   :id="`cell-pas-${idx}`"
                                                   :name="`grades[${idx}][pas]`"
                                                   x-model="st.pas"
                                                   @focus="activeCol = 'pas'"
                                                   @keydown.enter.prevent="focusCell('pas', idx + 1)"
                                                   @keydown.arrow-down.prevent="focusCell('pas', idx + 1)"
                                                   @keydown.arrow-up.prevent="focusCell('pas', idx - 1)"
                                                   class="w-full text-center py-1.5 px-1 rounded-lg border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-700 focus:bg-amber-50"
                                                   placeholder="-">
                                        </td>

                                        <!-- Nilai Akhir (Live Real-Time) -->
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="font-mono font-black text-xs block"
                                                  :class="getFinalScore(st) >= 75 ? 'text-teal-900' : (getFinalScore(st) !== null ? 'text-rose-600' : 'text-slate-300')"
                                                  x-text="getFinalScore(st) !== null ? getFinalScore(st) : '-'"></span>
                                        </td>

                                        <!-- Predikat & Status -->
                                        <td class="py-2.5 px-3 text-center">
                                            <template x-if="getFinalScore(st) !== null">
                                                <span class="px-2 py-0.5 rounded-full font-bold text-[10px] border"
                                                      :class="getPredicateColor(getFinalScore(st))">
                                                    <span x-text="getPredicate(getFinalScore(st))"></span>
                                                    <span x-text="getFinalScore(st) >= 75 ? '• Tuntas' : '• Remidi'"></span>
                                                </span>
                                            </template>
                                            <template x-if="getFinalScore(st) === null">
                                                <span class="text-slate-400 text-[10px]">-</span>
                                            </template>
                                        </td>

                                        <!-- Catatan Capaian Pembelajaran -->
                                        <td class="py-2 px-3">
                                            <div class="relative">
                                                <input type="text"
                                                       :name="`grades[${idx}][notes]`"
                                                       x-model="st.notes"
                                                       placeholder="Catatan hasil belajar / capaian kompetensi..."
                                                       class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-teal-700">
                                            </div>
                                        </td>

                                        <!-- Tombol Fokus / Modal Drawer Siswa -->
                                        <td class="py-2.5 px-3 text-center">
                                            <button type="button" @click="openFocusModal(st)"
                                                    class="p-1.5 rounded-lg text-slate-400 hover:text-teal-800 hover:bg-teal-50 transition"
                                                    title="Formulir Detail Siswa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <span class="text-xs text-slate-500">
                            💡 Seluruh nilai yang disimpan otomatis masuk ke kalkulasi raport resmi Kemenag & laporan digital wali murid.
                        </span>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-teal-900 hover:bg-teal-800 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Nilai Semua Siswa</span>
                        </button>
                    </div>
                </div>
            </form>

        </div>

        <!-- SINGLE ASSESSMENT MODE (ALTERNATIVE) -->
        <div x-show="viewMode === 'single'" class="space-y-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-500">Pilih Jenis Asesmen Tunggal:</span>
                    <form action="{{ route('teacher.grades.index') }}" method="GET" class="inline-flex">
                        <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                        <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                        <input type="hidden" name="mode" value="single">
                        <select name="grade_type" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-teal-700">
                            <option value="harian" {{ $gradeType === 'harian' ? 'selected' : '' }}>Ulangan Harian (UH)</option>
                            <option value="tugas" {{ $gradeType === 'tugas' ? 'selected' : '' }}>Tugas Mandiri / PR</option>
                            <option value="pts" {{ $gradeType === 'pts' ? 'selected' : '' }}>Penilaian Tengah Semester (PTS)</option>
                            <option value="pas" {{ $gradeType === 'pas' ? 'selected' : '' }}>Penilaian Akhir Semester (PAS)</option>
                        </select>
                    </form>
                </div>
                <div class="text-xs text-slate-500">
                    Standar KKM: <strong class="text-amber-600 font-mono">75.0</strong>
                </div>
            </div>

            <!-- Single Mode Form -->
            <form action="{{ route('teacher.grades.store') }}" method="POST">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                <input type="hidden" name="grade_type" value="{{ $gradeType }}">
                <input type="hidden" name="mode" value="single">

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                            Lembar Nilai Tunggal: {{ strtoupper($gradeType) }} - {{ $subject?->name }}
                        </h3>
                        <button type="submit"
                                class="px-5 py-2 rounded-xl bg-teal-900 hover:bg-teal-800 text-white font-bold text-xs shadow transition">
                            Simpan Nilai {{ strtoupper($gradeType) }}
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-100 text-slate-600 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4 w-12 text-center">No</th>
                                    <th class="py-3 px-6">Nama Siswa</th>
                                    <th class="py-3 px-6 w-44">Skor Nilai (0 - 100)</th>
                                    <th class="py-3 px-6">Catatan Evaluasi Guru</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($students as $idx => $st)
                                    @php
                                        $existing = $existingGrades->get($st->id);
                                        $score = old("grades.{$idx}.score", $existing?->score);
                                        $notes = old("grades.{$idx}.notes", $existing?->notes ?? '');
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3 px-4 text-center font-mono text-slate-400">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-6">
                                            <input type="hidden" name="grades[{{ $idx }}][student_id]" value="{{ $st->id }}">
                                            <span class="font-bold text-slate-900 block">{{ $st->full_name }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $st->nisn ?? $st->nis }}</span>
                                        </td>
                                        <td class="py-3 px-6">
                                            <input type="number" step="0.5" min="0" max="100"
                                                   name="grades[{{ $idx }}][score]"
                                                   value="{{ $score !== null ? (float)$score : '' }}"
                                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-700"
                                                   placeholder="0 - 100">
                                        </td>
                                        <td class="py-3 px-6">
                                            <input type="text" name="grades[{{ $idx }}][notes]"
                                                   value="{{ $notes }}"
                                                   placeholder="Catatan hasil belajar siswa..."
                                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-700">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>

    @endif

    <!-- MODAL DRAWER: FOKUS INPUT PER SISWA -->
    <div x-show="selectedStudentModal !== null" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="selectedStudentModal = null"
             class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl">
            
            <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800">Formulir Detail Siswa</span>
                    <h3 class="text-base font-extrabold text-slate-900" x-text="selectedStudentModal ? selectedStudentModal.name : ''"></h3>
                    <p class="text-xs text-slate-400" x-text="selectedStudentModal ? 'NISN: ' + selectedStudentModal.nisn : ''"></p>
                </div>
                <button type="button" @click="selectedStudentModal = null" class="text-slate-400 hover:text-slate-600 font-bold text-sm">✕</button>
            </div>

            <!-- Form Nilai 4 Asesmen -->
            <template x-if="selectedStudentModal">
                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Ulangan Harian (UH)</label>
                            <input type="number" step="0.5" min="0" max="100"
                                   x-model="selectedStudentModal.harian"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-700">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tugas Mandiri</label>
                            <input type="number" step="0.5" min="0" max="100"
                                   x-model="selectedStudentModal.tugas"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-700">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">PTS (Tengah Semester)</label>
                            <input type="number" step="0.5" min="0" max="100"
                                   x-model="selectedStudentModal.pts"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-700">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">PAS (Akhir Semester)</label>
                            <input type="number" step="0.5" min="0" max="100"
                                   x-model="selectedStudentModal.pas"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-700">
                        </div>
                    </div>

                    <!-- Live Calculation Badge -->
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Kalkulasi Nilai Akhir:</span>
                            <span class="font-mono text-base font-black text-teal-900"
                                  x-text="getFinalScore(selectedStudentModal) !== null ? getFinalScore(selectedStudentModal) : 'Belum Lengkap'"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block">Predikat Capaian:</span>
                            <span class="px-2.5 py-0.5 rounded-full font-bold text-xs border"
                                  :class="getPredicateColor(getFinalScore(selectedStudentModal))"
                                  x-text="getPredicate(getFinalScore(selectedStudentModal))"></span>
                        </div>
                    </div>

                    <!-- Catatan / Deskripsi Capaian -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-bold text-slate-700">Deskripsi Capaian Pembelajaran</label>
                            <span class="text-[10px] text-slate-400">Pilih Preset Cepat:</span>
                        </div>
                        
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            <button type="button" @click="selectedStudentModal.notes = 'Menunjukkan penguasaan kompetensi materi dengan sangat baik dan mandiri.'"
                                    class="px-2 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[10px] font-bold border border-emerald-200 transition">
                                🌟 Sangat Baik
                            </button>
                            <button type="button" @click="selectedStudentModal.notes = 'Menunjukkan pemahaman materi yang baik dan tuntas.'"
                                    class="px-2 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-800 text-[10px] font-bold border border-teal-200 transition">
                                ✅ Baik / Tuntas
                            </button>
                            <button type="button" @click="selectedStudentModal.notes = 'Cukup menguasai capaian pembelajaran pokok, perlu sedikit pengulangan.'"
                                    class="px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 text-[10px] font-bold border border-amber-200 transition">
                                ⚠️ Cukup
                            </button>
                            <button type="button" @click="selectedStudentModal.notes = 'Perlu bimbingan dan pendampingan intensif pada materi pembelajaran.'"
                                    class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-800 text-[10px] font-bold border border-rose-200 transition">
                                🆘 Perlu Bimbingan
                            </button>
                        </div>

                        <textarea x-model="selectedStudentModal.notes" rows="3"
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-700"
                                  placeholder="Tuliskan catatan kemajuan belajar atau pilih preset di atas..."></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="selectedStudentModal = null"
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                            Batal
                        </button>
                        <button type="button" @click="saveFocusModal()"
                                class="px-5 py-2 rounded-xl bg-teal-900 hover:bg-teal-800 text-white font-bold transition">
                            Terapkan ke Tabel
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
