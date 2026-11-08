@extends('layouts.portal')

@section('title', 'Presensi Santri Harian')

@section('content')
<div class="space-y-6" x-data="{
    date: '{{ $date }}',
    setAllPresent() {
        document.querySelectorAll('input[type=radio][value=hadir]').forEach(el => el.checked = true);
    }
}">

    <!-- Header & Date Switcher -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Presensi Harian Santri</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelas: <strong>{{ $classroom->name ?? 'Belum Ada Rombel' }}</strong> • Pencatatan kehadiran harian madrasah
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form action="{{ route('teacher.attendance.index') }}" method="GET" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                       class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent">
            </form>

            <button type="button" @click="setAllPresent()"
                    class="px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-bold text-xs border border-emerald-200 transition">
                ✓ Set Semua Hadir
            </button>
        </div>
    </div>

    @if(!$classroom)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-amber-900 text-xs">
            <p class="font-bold">Perhatian:</p>
            <p>Akun guru Anda belum ditugaskan sebagai wali kelas di tahun ajaran aktif. Hubungi Staf TU untuk plotting rombel.</p>
        </div>
    @else
        <!-- Attendance Form -->
        <form action="{{ route('teacher.attendance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Daftar Kehadiran: {{ date('l, d F Y', strtotime($date)) }}</h3>
                        <span class="text-xs text-slate-400">Total {{ $students->count() }} Santri</span>
                    </div>

                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Presensi</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-6 w-12 text-center">No</th>
                                <th class="py-3 px-6">NISN / Nama Santri</th>
                                <th class="py-3 px-6 text-center">Status Kehadiran</th>
                                <th class="py-3 px-6">Catatan Khusus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $index => $st)
                                @php
                                    $existing = $existingAttendances->get($st->id);
                                    $currentStatus = old("attendances.{$index}.status", $existing?->status->value ?? 'hadir');
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-6 text-center font-mono text-slate-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $st->id }}">
                                        <span class="font-bold text-slate-900 block">{{ $st->full_name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $st->nisn }} ({{ $st->gender }})</span>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <div class="flex items-center justify-center gap-3">
                                            <!-- Hadir -->
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg border hover:bg-emerald-50 transition"
                                                   :class="{'bg-emerald-50 border-emerald-300 font-bold text-emerald-900': true}">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="hadir"
                                                       class="text-emerald-700 focus:ring-emerald-600"
                                                       {{ $currentStatus === 'hadir' ? 'checked' : '' }}>
                                                <span class="text-xs">Hadir</span>
                                            </label>

                                            <!-- Sakit -->
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg border hover:bg-blue-50 transition">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="sakit"
                                                       class="text-blue-700 focus:ring-blue-600"
                                                       {{ $currentStatus === 'sakit' ? 'checked' : '' }}>
                                                <span class="text-xs">Sakit</span>
                                            </label>

                                            <!-- Izin -->
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg border hover:bg-amber-50 transition">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="izin"
                                                       class="text-amber-700 focus:ring-amber-600"
                                                       {{ $currentStatus === 'izin' ? 'checked' : '' }}>
                                                <span class="text-xs">Izin</span>
                                            </label>

                                            <!-- Alpa -->
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg border hover:bg-red-50 transition">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="alpa"
                                                       class="text-red-700 focus:ring-red-600"
                                                       {{ $currentStatus === 'alpa' ? 'checked' : '' }}>
                                                <span class="text-xs">Alpa</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <input type="text" name="attendances[{{ $index }}][notes]"
                                               value="{{ old("attendances.{$index}.notes", $existing?->notes) }}"
                                               placeholder="Opsional (cth: terlambat, flu)"
                                               class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-emerald-600">
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
                                class="px-8 py-3 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow transition flex items-center gap-2">
                            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Presensi Santri</span>
                        </button>
                    </div>
                @endif
            </div>
        </form>
    @endif

</div>
@endsection
