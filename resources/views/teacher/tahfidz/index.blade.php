@extends('layouts.portal')

@section('title', 'Mutaba\'ah Tahfidz Qur\'an')

@section('content')
<div class="space-y-6" x-data="{ showForm: false }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Mutaba'ah Tahfidz Qur'an</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelas: <strong>{{ $classroom->name ?? 'Belum Ada Rombel' }}</strong> • Pencatatan setoran hafalan Juz 'Amma santri
            </p>
        </div>

        <button type="button" @click="showForm = !showForm"
                class="px-5 py-2.5 rounded-xl bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs sm:text-sm shadow flex items-center justify-center gap-2 transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span x-text="showForm ? 'Tutup Formulir' : '+ Catat Setoran Hafalan'"></span>
        </button>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Input Setoran Form -->
    <div x-show="showForm" x-cloak class="bg-white rounded-3xl border border-teal-200 shadow-md p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Formulir Setoran Hafalan Baru</h3>
            <p class="text-xs text-slate-500">Nilai kelancaran hafalan dan kaidah tajwid santri</p>
        </div>

        <form action="{{ route('teacher.tahfidz.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Pilih Santri <span class="text-red-500">*</span>
                    </label>
                    <select name="student_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-white">
                        <option value="">-- Pilih Santri di Kelas --</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}" {{ old('student_id') == $st->id ? 'selected' : '' }}>
                                {{ $st->full_name }} ({{ $st->gender }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Tanggal Setoran <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Nama Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="surah_name" value="{{ old('surah_name') }}" required
                           placeholder="Contoh: An-Naba', Al-Fajr, An-Naas"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Rentang Ayat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="verse_range" value="{{ old('verse_range') }}" required
                           placeholder="Contoh: Ayat 1-20 atau 1 Surat Penuh"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Predikat Kelancaran <span class="text-red-500">*</span>
                    </label>
                    <select name="fluency_grade" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-white">
                        <option value="lancar" {{ old('fluency_grade') === 'lancar' ? 'selected' : '' }}>Lancar & Fasih</option>
                        <option value="kurang_lancar" {{ old('fluency_grade') === 'kurang_lancar' ? 'selected' : '' }}>Kurang Lancar (Bimbingan)</option>
                        <option value="mengulang" {{ old('fluency_grade') === 'mengulang' ? 'selected' : '' }}>Mengulang (Ziyadah)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Kaidah Tajwid & Makhraj <span class="text-red-500">*</span>
                    </label>
                    <select name="tajweed_grade" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-white">
                        <option value="mumtaz" {{ old('tajweed_grade') === 'mumtaz' ? 'selected' : '' }}>Mumtaz (Sangat Baik / Sesuai Tajwid)</option>
                        <option value="jayyid" {{ old('tajweed_grade') === 'jayyid' ? 'selected' : '' }}>Jayyid (Cukup Baik)</option>
                        <option value="maqbul" {{ old('tajweed_grade') === 'maqbul' ? 'selected' : '' }}>Maqbul (Perlu Perbaikan Makhraj)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Catatan & Nasihat Ustadz (Opsional)
                </label>
                <textarea name="notes" rows="2"
                          placeholder="Perhatikan mad thabi'i, ghunnah di ayat 5, dll."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" @click="showForm = false" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs shadow transition">
                    Simpan Setoran Hafalan
                </button>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Buku Mutaba'ah Tahfidz Santri</h3>
                <p class="text-xs text-slate-500">Catatan setoran hafalan Juz 30 santri kelas {{ $classroom->name ?? '' }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Tanggal</th>
                        <th class="py-3.5 px-6">Nama Santri</th>
                        <th class="py-3.5 px-6">Surat & Ayat</th>
                        <th class="py-3.5 px-6">Kelancaran</th>
                        <th class="py-3.5 px-6">Tajwid</th>
                        <th class="py-3.5 px-6">Catatan Ustadz</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 font-mono font-bold text-slate-800">
                                {{ $rec->date->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $rec->student->full_name }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-teal-950 block">QS. {{ $rec->surah_name }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">{{ $rec->verse_range }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $rec->fluency_grade->value === 'lancar' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                    {{ $rec->fluency_grade->value === 'lancar' ? 'Lancar' : ($rec->fluency_grade->value === 'kurang_lancar' ? 'Kurang Lancar' : 'Mengulang') }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $rec->tajweed_grade->value === 'mumtaz' ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($rec->tajweed_grade->value) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 max-w-xs">
                                {{ $rec->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Belum ada catatan setoran tahfidz untuk kelas ini.
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

</div>
@endsection
