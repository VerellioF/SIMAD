@extends('layouts.portal')

@section('title', 'Mutaba\'ah Ibadah Yaumiyah Santri')

@section('content')
<div class="space-y-6" x-data="{ noteModal: false, activeStudent: {}, activeNotes: '' }">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Mutaba'ah Ibadah Yaumiyah</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pemantauan kedisiplinan sholat fardhu 5 waktu, sholat dhuha, tadarus Al-Qur'an, dan birrul walidain santri.</p>
        </div>
        <!-- Form Filter Kelas & Tanggal -->
        <form method="GET" action="{{ route('teacher.worship.index') }}" class="flex items-center gap-2 flex-wrap">
            <select name="classroom_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white">
                @foreach($allClassrooms as $cls)
                    <option value="{{ $cls->id }}" {{ $selectedClassroomId == $cls->id ? 'selected' : '' }}>Kelas {{ $cls->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white">
        </form>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table of Student Worship -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rekapitulasi Ibadah Santri (Tanggal: {{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y') }})</span>
            <span class="text-xs text-slate-400">Total: {{ $students->count() }} Santri</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3">Nama Santri</th>
                        <th class="px-2 py-3 text-center">Subuh</th>
                        <th class="px-2 py-3 text-center">Dzuhur</th>
                        <th class="px-2 py-3 text-center">Ashar</th>
                        <th class="px-2 py-3 text-center">Maghrib</th>
                        <th class="px-2 py-3 text-center">Isya</th>
                        <th class="px-2 py-3 text-center">Dhuha</th>
                        <th class="px-2 py-3 text-center">Tadarus</th>
                        <th class="px-2 py-3 text-center">Birrul W.</th>
                        <th class="px-4 py-3">Catatan Wali</th>
                        <th class="px-4 py-3 text-right">Apresiasi Guru</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($students as $st)
                        @php
                            $record = $st->dailyWorshipRecords->first();
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-800">{{ $st->full_name }}</span>
                                <span class="block text-[10px] text-slate-400 font-mono">NIS: {{ $st->nis }}</span>
                            </td>
                            <!-- Sholat 5 Waktu & Sunnah -->
                            @foreach(['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya', 'dhuha', 'tadarus_quran', 'birrul_walidain'] as $prayer)
                                <td class="px-2 py-3.5 text-center">
                                    @if($record && $record->$prayer)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold" title="Dilaksanakan">✓</span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-300 text-xs font-bold" title="Belum / Tidak">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-4 py-3.5 max-w-xs truncate text-xs text-slate-500 italic">
                                {{ $record->parent_notes ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <button @click="activeStudent = {{ $st->toJson() }}; activeNotes = '{{ addslashes($record->teacher_notes ?? '') }}'; noteModal = true" class="px-2.5 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold transition">
                                    {{ $record && $record->teacher_notes ? 'Edit Catatan' : '+ Beri Catatan' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada santri di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Catatan Evaluasi Guru -->
    <div x-show="noteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="noteModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-800">Catatan & Motivasi untuk <span x-text="activeStudent.full_name"></span></h3>
            <form action="{{ route('teacher.worship.notes') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="student_id" :value="activeStudent.id">
                <input type="hidden" name="date" value="{{ $date }}">

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Catatan Asatidz / Wali Kelas</label>
                    <textarea name="teacher_notes" x-model="activeNotes" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-900" placeholder="e.g. Masya Allah, tingkatkan istiqomah sholat berjamaah ya ananda..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="noteModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-teal-900 hover:bg-teal-800 text-white text-xs font-bold shadow">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
