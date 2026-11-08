@extends('layouts.portal')

@section('title', 'Mutaba\'ah Ibadah Harian Siswa')

@section('content')
<div class="space-y-6" x-data="{
    noteModal: false,
    activeStudent: {},
    activeNotes: '',
    searchStudent: '',
    checkSchoolPrayers() {
        // Centang Dhuha, Dzuhur, dan Tadarus Qur'an (ibadah rutin selama di madrasah)
        document.querySelectorAll('input.check-dzuhur, input.check-dhuha, input.check-tadarus').forEach(el => el.checked = true);
    },
    checkAll() {
        document.querySelectorAll('input.worship-checkbox').forEach(el => el.checked = true);
    },
    uncheckAll() {
        if (confirm('Yakin ingin mengosongkan semua centang ibadah di kelas ini?')) {
            document.querySelectorAll('input.worship-checkbox').forEach(el => el.checked = false);
        }
    }
}">

    <!-- Header & Filter Form -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Buku Mutaba'ah Digital
                </span>
                <span class="text-xs text-slate-400">Pembiasaan Ibadah Fardhu & Sunnah Siswa</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ "Mutaba'ah Ibadah Yaumiyah" }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Monitoring kedisiplinan sholat fardhu 5 waktu, sholat dhuha, tadarus Al-Qur'an, dan birrul walidain siswa.
            </p>
        </div>

        <!-- Filter Kelas & Tanggal -->
        <form method="GET" action="{{ route('teacher.worship.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <select name="classroom_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-emerald-800">
                @foreach($allClassrooms as $cls)
                    <option value="{{ $cls->id }}" {{ $selectedClassroomId == $cls->id ? 'selected' : '' }}>
                        {{ $cls->name }} (Kelas {{ $cls->grade_level }})
                    </option>
                @endforeach
            </select>

            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                   class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-emerald-800">
        </form>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Statistik & Fitur Cepat Input Mutaba'ah -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Banner Info Kelas -->
        <div class="bg-gradient-to-tr from-emerald-950 via-emerald-900 to-emerald-950 text-white rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Rombel Terpilih</span>
                <h3 class="text-xl font-black mt-1 text-white">{{ $classroom?->name }}</h3>
                <p class="text-xs text-emerald-200 mt-1">
                    {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-emerald-800 flex items-center justify-between text-xs text-emerald-200">
                <span>Total Siswa:</span>
                <strong class="text-amber-400 font-mono text-sm">{{ $stats['total'] }} Siswa</strong>
            </div>
        </div>

        <!-- Tingkat Pelaksanaan Sholat Berjamaah -->
        <div class="md:col-span-2 bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Capaian Ibadah Rombel Hari Ini</span>
            <div class="grid grid-cols-4 sm:grid-cols-8 gap-1.5 mt-2 text-center">
                <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                    <span class="text-xs font-bold text-slate-500 block">Subuh</span>
                    <span class="text-sm font-black font-mono text-emerald-850">{{ $stats['subuh'] }}</span>
                </div>
                <div class="bg-emerald-50 rounded-xl p-2 border border-emerald-200">
                    <span class="text-xs font-bold text-emerald-800 block">Dzuhur</span>
                    <span class="text-sm font-black font-mono text-emerald-900">{{ $stats['dzuhur'] }}</span>
                </div>
                <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                    <span class="text-xs font-bold text-slate-500 block">Ashar</span>
                    <span class="text-sm font-black font-mono text-emerald-850">{{ $stats['ashar'] }}</span>
                </div>
                <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                    <span class="text-xs font-bold text-slate-500 block">Maghrib</span>
                    <span class="text-sm font-black font-mono text-emerald-850">{{ $stats['maghrib'] }}</span>
                </div>
                <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                    <span class="text-xs font-bold text-slate-500 block">Isya</span>
                    <span class="text-sm font-black font-mono text-emerald-850">{{ $stats['isya'] }}</span>
                </div>
                <div class="bg-amber-50 rounded-xl p-2 border border-amber-200">
                    <span class="text-xs font-bold text-amber-800 block">Dhuha</span>
                    <span class="text-sm font-black font-mono text-amber-900">{{ $stats['dhuha'] }}</span>
                </div>
                <div class="bg-teal-50 rounded-xl p-2 border border-teal-200">
                    <span class="text-xs font-bold text-teal-800 block">Tadarus</span>
                    <span class="text-sm font-black font-mono text-teal-900">{{ $stats['tadarus'] }}</span>
                </div>
                <div class="bg-blue-50 rounded-xl p-2 border border-blue-200">
                    <span class="text-xs font-bold text-blue-800 block">Birrul W.</span>
                    <span class="text-sm font-black font-mono text-blue-900">{{ $stats['birrul'] }}</span>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Data terintegrasi real-time dari buku mutaba'ah orang tua / wali murid dan guru.</p>
        </div>

        <!-- Fitur Cepat Centang Massal -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-center gap-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi Cepat Guru</span>
            <button type="button" @click="checkSchoolPrayers()"
                    class="w-full py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-950 font-bold text-xs border border-emerald-300 transition text-left flex items-center justify-between">
                <span>⚡ Centang Dhuha, Dzuhur & Tadarus</span>
                <span class="text-[10px]">Madrasah</span>
            </button>
            <div class="flex gap-2">
                <button type="button" @click="checkAll()"
                        class="flex-1 py-1.5 px-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                    Centang Semua
                </button>
                <button type="button" @click="uncheckAll()"
                        class="flex-1 py-1.5 px-2 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-900 text-[11px] font-bold transition">
                    Kosongkan
                </button>
            </div>
        </div>
    </div>

    <!-- Form Checklist Mutaba'ah Massal Rombel -->
    <form action="{{ route('teacher.worship.store') }}" method="POST">
        @csrf
        <input type="hidden" name="classroom_id" value="{{ $classroom?->id }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Checklist Ibadah Siswa: {{ $classroom?->name }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Centang ibadah yang telah dilaksanakan siswa. Kolom berlatar hijau adalah ibadah utama di madrasah (Dhuha, Dzuhur, Tadarus).
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <input type="text" x-model="searchStudent" placeholder="Cari siswa..."
                           class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800">

                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-emerald-950 hover:bg-emerald-900 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Mutaba'ah</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Nama Siswa</th>
                            <th class="px-2 py-3 text-center">Subuh</th>
                            <th class="px-2 py-3 text-center bg-emerald-50/60 text-emerald-900">Dzuhur</th>
                            <th class="px-2 py-3 text-center">Ashar</th>
                            <th class="px-2 py-3 text-center">Maghrib</th>
                            <th class="px-2 py-3 text-center">Isya</th>
                            <th class="px-2 py-3 text-center bg-amber-50/60 text-amber-900">Dhuha</th>
                            <th class="px-2 py-3 text-center bg-teal-50/60 text-teal-900">Tadarus</th>
                            <th class="px-2 py-3 text-center">Birrul W.</th>
                            <th class="px-4 py-3">Catatan Wali</th>
                            <th class="px-4 py-3">Apresiasi & Catatan Guru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($students as $idx => $st)
                            @php
                                $record = $st->dailyWorshipRecords->first();
                            @endphp
                            <tr class="hover:bg-slate-50 transition"
                                x-show="!searchStudent || '{{ strtolower($st->full_name) }}'.includes(searchStudent.toLowerCase())">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <input type="hidden" name="worship[{{ $idx }}][student_id]" value="{{ $st->id }}">
                                    <span class="font-bold text-slate-900 block">{{ $st->full_name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $st->nisn ?? $st->nis }}</span>
                                </td>

                                <!-- Subuh -->
                                <td class="px-2 py-3.5 text-center">
                                    <input type="checkbox" name="worship[{{ $idx }}][subuh]" value="1"
                                           class="worship-checkbox check-subuh w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                                           {{ old("worship.{$idx}.subuh", $record?->subuh) ? 'checked' : '' }}>
                                </td>

                                <!-- Dzuhur (Madrasah) -->
                                <td class="px-2 py-3.5 text-center bg-emerald-50/30">
                                    <input type="checkbox" name="worship[{{ $idx }}][dzuhur]" value="1"
                                           class="worship-checkbox check-dzuhur w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-emerald-400"
                                           {{ old("worship.{$idx}.dzuhur", $record?->dzuhur) ? 'checked' : '' }}>
                                </td>

                                <!-- Ashar -->
                                <td class="px-2 py-3.5 text-center">
                                    <input type="checkbox" name="worship[{{ $idx }}][ashar]" value="1"
                                           class="worship-checkbox check-ashar w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                                           {{ old("worship.{$idx}.ashar", $record?->ashar) ? 'checked' : '' }}>
                                </td>

                                <!-- Maghrib -->
                                <td class="px-2 py-3.5 text-center">
                                    <input type="checkbox" name="worship[{{ $idx }}][maghrib]" value="1"
                                           class="worship-checkbox check-maghrib w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                                           {{ old("worship.{$idx}.maghrib", $record?->maghrib) ? 'checked' : '' }}>
                                </td>

                                <!-- Isya -->
                                <td class="px-2 py-3.5 text-center">
                                    <input type="checkbox" name="worship[{{ $idx }}][isya]" value="1"
                                           class="worship-checkbox check-isya w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                                           {{ old("worship.{$idx}.isya", $record?->isya) ? 'checked' : '' }}>
                                </td>

                                <!-- Dhuha (Madrasah) -->
                                <td class="px-2 py-3.5 text-center bg-amber-50/30">
                                    <input type="checkbox" name="worship[{{ $idx }}][dhuha]" value="1"
                                           class="worship-checkbox check-dhuha w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-amber-400"
                                           {{ old("worship.{$idx}.dhuha", $record?->dhuha) ? 'checked' : '' }}>
                                </td>

                                <!-- Tadarus Quran (Madrasah) -->
                                <td class="px-2 py-3.5 text-center bg-teal-50/30">
                                    <input type="checkbox" name="worship[{{ $idx }}][tadarus_quran]" value="1"
                                           class="worship-checkbox check-tadarus w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-teal-400"
                                           {{ old("worship.{$idx}.tadarus_quran", $record?->tadarus_quran) ? 'checked' : '' }}>
                                </td>

                                <!-- Birrul Walidain -->
                                <td class="px-2 py-3.5 text-center">
                                    <input type="checkbox" name="worship[{{ $idx }}][birrul_walidain]" value="1"
                                           class="worship-checkbox check-birrul w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                                           {{ old("worship.{$idx}.birrul_walidain", $record?->birrul_walidain) ? 'checked' : '' }}>
                                </td>

                                <!-- Catatan Wali (Read-only) -->
                                <td class="px-4 py-3.5 max-w-xs truncate text-xs text-slate-500 italic">
                                    {{ $record?->parent_notes ?? '-' }}
                                </td>

                                <!-- Catatan Guru (Inline input + Quick modal button) -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" name="worship[{{ $idx }}][teacher_notes]"
                                               value="{{ old("worship.{$idx}.teacher_notes", $record?->teacher_notes) }}"
                                               placeholder="Beri motivasi / catatan..."
                                               class="w-full px-2.5 py-1 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800">
                                        <button type="button" @click="activeStudent = {{ $st->toJson() }}; activeNotes = '{{ addslashes($record->teacher_notes ?? '') }}'; noteModal = true"
                                                class="px-2 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-800 text-[11px] font-bold whitespace-nowrap transition" title="Buka Detail Modal">
                                            Modal
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-slate-400 text-xs">
                                    Belum ada siswa terdaftar di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500">
                    Perubahan checklist dan apresiasi langsung tampil di akun portal orang tua / wali murid.
                </span>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-emerald-950 hover:bg-emerald-900 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Mutaba'ah</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Modal Catatan Guru (Dedicated Modal) -->
    <div x-show="noteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="noteModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Apresiasi & Bimbingan Ibadah</span>
                    <h3 class="text-lg font-black text-slate-900" x-text="activeStudent.full_name"></h3>
                    <p class="text-xs text-slate-400" x-text="'Tanggal: {{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y') }}'"></p>
                </div>
                <button type="button" @click="noteModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('teacher.worship.notes') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="student_id" :value="activeStudent.id">
                <input type="hidden" name="date" value="{{ $date }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan / Apresiasi Guru</label>
                    <textarea name="teacher_notes" x-model="activeNotes" rows="3"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800"
                              placeholder="Tuliskan motivasi, bimbingan, atau apresiasi ibadah untuk siswa..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="noteModal = false"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-emerald-950 hover:bg-emerald-900 text-white text-xs font-bold shadow">
                        Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
