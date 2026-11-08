@extends('layouts.portal')

@section('title', 'Disiplin & Poin Karakter Santri')

@section('content')
<div class="space-y-6" x-data="{ recordModal: false, activeStudent: {}, activeType: 'apresiasi' }">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Disiplin & Karakter Santri</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Berikan apresiasi poin karakter terpuji atau catat pembinaan kedisiplinan santri secara edukatif.</p>
        </div>
        
        <!-- Filter Kelas -->
        <form method="GET" action="{{ route('teacher.conduct.index') }}" class="flex items-center gap-2">
            <label class="text-xs font-semibold text-slate-500">Rombel:</label>
            <select name="classroom_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white">
                @foreach($classrooms as $cls)
                    <option value="{{ $cls->id }}" {{ $selectedClassroomId == $cls->id ? 'selected' : '' }}>Kelas {{ $cls->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Grid: Daftar Santri di Kelas -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Daftar Santri & Skor Karakter Terkini</span>
            <span class="text-xs text-slate-400">Total: {{ $students->count() }} Santri</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Nama Santri</th>
                        <th class="px-5 py-3 text-center">Skor Karakter</th>
                        <th class="px-5 py-3 text-center">Status Predikat</th>
                        <th class="px-5 py-3 text-right">Aksi Pembinaan / Apresiasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($students as $st)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="font-bold text-slate-900 block">{{ $st->full_name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">NIS: {{ $st->nis }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-center font-mono font-extrabold text-base">
                            <span class="{{ $st->total_score >= 100 ? 'text-emerald-800' : 'text-amber-700' }}">
                                {{ $st->total_score }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($st->total_score >= 115)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-850">Teladan (Mumtaz)</span>
                            @elseif($st->total_score >= 95)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-teal-100 text-teal-850">Baik (Jayyid)</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Perlu Bimbingan</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-2">
                            <button @click="activeStudent = {{ $st->toJson() }}; activeType = 'apresiasi'; recordModal = true" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-850 text-xs font-bold transition">
                                + Beri Poin Apresiasi
                            </button>
                            <button @click="activeStudent = {{ $st->toJson() }}; activeType = 'pelanggaran'; recordModal = true" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition">
                                - Catat Bimbingan
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-xs">Belum ada santri di kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Catat Poin Karakter -->
    <div x-show="recordModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="recordModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-800">
                <span x-text="activeType === 'apresiasi' ? 'Beri Poin Apresiasi: ' : 'Catat Bimbingan Disiplin: '"></span>
                <span class="text-emerald-900 font-extrabold" x-text="activeStudent.full_name"></span>
            </h3>
            
            <form action="{{ route('teacher.conduct.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="student_id" :value="activeStudent.id">
                <input type="hidden" name="type" :value="activeType">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Bobot Poin</label>
                        <input type="number" name="points" min="1" max="100" value="10" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal</label>
                        <input type="date" name="incident_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Katalog Tata Tertib (Opsional)</label>
                    <select name="character_rule_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-900">
                        <option value="">-- Kustom / Tulis Sendiri --</option>
                        @foreach($rules as $r)
                            <option value="{{ $r->id }}">[{{ ucfirst($r->type) }}] {{ $r->name }} ({{ $r->points }} poin)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Keterangan Singkat</label>
                    <input type="text" name="title" placeholder="e.g. Membantu teman merapikan buku / Terlambat sholat dhuha" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tindak Lanjut / Apresiasi Edukatif</label>
                    <input type="text" name="follow_up_action" placeholder="e.g. Diapresiasi di depan kelas / Dinasehati dengan kasih sayang" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-900"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="recordModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-teal-900 hover:bg-teal-800 text-white text-xs font-bold shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
