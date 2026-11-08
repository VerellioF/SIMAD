@extends('layouts.portal')

@section('title', 'Portal Guru & Wali Kelas')

@section('content')
<div class="space-y-8">
    
    <!-- Welcome Header Banner -->
    <div class="bg-gradient-to-r from-teal-950 via-teal-900 to-teal-950 p-6 sm:p-8 rounded-3xl text-white shadow-lg border border-teal-500/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <span class="inline-block px-3 py-1 rounded-full bg-teal-500 text-teal-950 text-xs font-bold uppercase tracking-wider">
                Portal Guru & Wali Kelas
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                Selamat Datang, {{ auth()->user()->name }}
            </h1>
            <p class="text-xs sm:text-sm text-teal-200">
                Wali Kelas: <strong>{{ $classroom->name ?? 'Belum Ditugaskan' }}</strong> • T.A. 2026/2027 Ganjil
            </p>
        </div>

        <div class="text-right bg-teal-900/60 p-3 rounded-2xl border border-teal-800 text-xs">
            <span class="text-teal-300 block text-[10px] uppercase font-bold">Hari Ini</span>
            <span class="font-bold text-white text-sm">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kelas Binaan</span>
            <span class="block text-xl font-extrabold text-teal-950">{{ $classroom->name ?? '-' }}</span>
            <span class="text-[11px] text-slate-500">Tingkat Kelas {{ $classroom->grade_level ?? '-' }}</span>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Siswa Binaan</span>
            <span class="block text-3xl font-extrabold text-teal-800 font-mono">{{ $students->count() }}</span>
            <span class="text-[11px] text-slate-500">Siswa terdaftar aktif di kelas</span>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Presensi Hari Ini</span>
            <span class="block text-xl font-bold text-slate-800">
                {{ $recentAttendances->count() > 0 ? $recentAttendances->count() . ' Terabsen' : 'Belum Ada Input' }}
            </span>
            <span class="text-[11px] text-teal-700 font-semibold">Buka modul presensi harian</span>
        </div>
    </div>

    <!-- Student Roster in Classroom -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar Siswa Kelas {{ $classroom->name ?? '' }}</h3>
                <p class="text-xs text-slate-500">Kelola kehadiran siswa dan perkembangan hafalan Al-Qur'an</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">NISN / NIS</th>
                        <th class="py-3.5 px-6">Nama Lengkap Siswa</th>
                        <th class="py-3.5 px-6">L/P</th>
                        <th class="py-3.5 px-6">Orang Tua / Wali Murid</th>
                        <th class="py-3.5 px-6">Status Siswa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $st)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 font-mono text-slate-600">
                                <span class="font-bold text-slate-800 block">{{ $st->nisn }}</span>
                                <span class="text-[10px] text-slate-400">NIS: {{ $st->nis ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                {{ $st->full_name }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $st->gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $st->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $st->parent->name ?? '-' }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 uppercase">
                                    Aktif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                Belum ada siswa yang dimasukkan ke kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
