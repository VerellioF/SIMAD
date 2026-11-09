@extends('layouts.portal')

@section('title', 'Rekap Kehadiran Siswa')

@section('content')
<div class="space-y-6">

    <!-- Header & Student Switcher -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Presensi & Kehadiran Siswa <span class="sr-only">Presensi & Kehadiran Santri</span></h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau rekaman presensi harian ananda di kelas</p>
        </div>

        @if($students->count() > 1)
            <div>
                <form action="{{ route('parent.attendance.index') }}" method="GET">
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
        <!-- Stats Indicators -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm col-span-2 lg:col-span-1 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Persentase Hadir</span>
                <span class="text-3xl font-extrabold text-emerald-800 font-mono block">{{ $percentage }}%</span>
                <span class="text-[10px] text-slate-400 block">Semester Berjalan</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
                <span class="text-[10px] uppercase font-bold text-emerald-800 block">Total Hadir</span>
                <span class="text-3xl font-extrabold text-emerald-900 font-mono block">{{ $counts['hadir'] }}</span>
                <span class="text-[10px] text-slate-400 block">Hari</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
                <span class="text-[10px] uppercase font-bold text-blue-700 block">Sakit</span>
                <span class="text-3xl font-extrabold text-blue-600 font-mono block">{{ $counts['sakit'] }}</span>
                <span class="text-[10px] text-slate-400 block">Hari</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
                <span class="text-[10px] uppercase font-bold text-amber-700 block">Izin</span>
                <span class="text-3xl font-extrabold text-amber-600 font-mono block">{{ $counts['izin'] }}</span>
                <span class="text-[10px] text-slate-400 block">Hari</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
                <span class="text-[10px] uppercase font-bold text-red-700 block">Alpa</span>
                <span class="text-3xl font-extrabold text-red-600 font-mono block">{{ $counts['alpa'] }}</span>
                <span class="text-[10px] text-slate-400 block">Hari</span>
            </div>
        </div>

        <!-- Attendance History Table -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Riwayat Kehadiran: {{ $selectedStudent->full_name }}</h3>
                <a href="{{ route('parent.leave.index') }}" class="px-3.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs border border-amber-200 transition">
                    Ajukan Izin / Sakit
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-6">Tanggal</th>
                            <th class="py-3.5 px-6">Mata Pelajaran / Sesi</th>
                            <th class="py-3.5 px-6">Status Kehadiran</th>
                            <th class="py-3.5 px-6">Catatan Guru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($attendances as $att)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6 font-mono font-bold text-slate-800">
                                    {{ $att->date->format('d/m/Y') }} ({{ $att->date->translatedFormat('l') }})
                                </td>
                                <td class="py-4 px-6">
                                    @if($att->subject)
                                        <span class="font-bold text-slate-800 block">{{ $att->subject->name }}</span>
                                        @if($att->classSchedule)
                                            <span class="text-[10px] text-emerald-800 font-mono">{{ $att->classSchedule->start_time }} - {{ $att->classSchedule->end_time }}</span>
                                        @endif
                                    @else
                                        <span class="text-slate-500 italic text-[11px]">Presensi Harian Rombel</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $badge = match($att->status->value) {
                                            'hadir' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'sakit' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'izin' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            'alpa' => 'bg-red-100 text-red-800 border-red-200',
                                        };
                                        $label = match($att->status->value) {
                                            'hadir' => 'Hadir',
                                            'sakit' => 'Sakit',
                                            'izin' => 'Izin',
                                            'alpa' => 'Alpa (Tanpa Keterangan)',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $badge }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-600">
                                    {{ $att->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    Belum ada data presensi tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attendances->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
