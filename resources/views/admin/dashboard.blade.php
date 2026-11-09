@extends('layouts.portal')

@section('title', 'Dashboard Backoffice TU')

@section('content')
<div class="space-y-8">
    
    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-emerald-950 via-emerald-900 to-emerald-950 p-6 sm:p-8 rounded-3xl text-white shadow-lg border border-gold-500/30">
        <div class="space-y-1">
            <span class="inline-block px-3 py-1 rounded-full bg-gold-500 text-emerald-950 text-xs font-bold uppercase tracking-wider">
                Backoffice Tata Usaha
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                Selamat Datang, {{ auth()->user()->name }}
            </h1>
            <p class="text-xs sm:text-sm text-emerald-200">
                Kelola seleksi pendaftaran siswa baru, verifikasi berkas, dan pelaporan EMIS Kemenag.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ppdb.export_emis') }}" class="px-5 py-2.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-xs sm:text-sm shadow flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export EMIS (Excel)</span>
            </a>
            <a href="{{ route('admin.ppdb.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-800 hover:bg-emerald-700 text-white font-semibold text-xs sm:text-sm border border-emerald-600 transition">
                <span>Kelola PPDB</span>
            </a>
        </div>
    </div>

    <!-- Action Needed Queue Card (Productivity Booster) -->
    @if($stats['pending_ppdb'] > 0)
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-lg shrink-0">
                    !
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-extrabold text-amber-950">
                        Perhatian: Ada {{ $stats['pending_ppdb'] }} Berkas Calon Siswa Menunggu Verifikasi PPDB
                    </h4>
                    <p class="text-xs text-amber-800 mt-0.5">
                        Mohon periksa data akta, KK, dan bukti pembayaran pendaftar untuk penetapan status kelulusan.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.ppdb.index') }}"
               class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm transition whitespace-nowrap text-center">
                Buka Verifikasi PPDB →
            </a>
        </div>
    @endif

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total PPDB -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pendaftar</span>
                <div class="p-2 rounded-xl bg-emerald-50 text-emerald-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <span class="block text-3xl font-extrabold text-slate-900 font-mono">{{ $stats['total_ppdb'] }}</span>
            <span class="block text-[11px] text-slate-400">Total berkas masuk</span>
        </div>

        <!-- Menunggu Verifikasi -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Perlu Verifikasi</span>
                <div class="p-2 rounded-xl bg-amber-50 text-amber-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <span class="block text-3xl font-extrabold text-amber-600 font-mono">{{ $stats['pending_ppdb'] }}</span>
            <span class="block text-[11px] text-amber-600/80">Menunggu tindakan TU</span>
        </div>

        <!-- Terverifikasi -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Berkas Valid</span>
                <div class="p-2 rounded-xl bg-blue-50 text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <span class="block text-3xl font-extrabold text-blue-600 font-mono">{{ $stats['verified_ppdb'] }}</span>
            <span class="block text-[11px] text-slate-400">Dokumen telah diperiksa</span>
        </div>

        <!-- Siswa Aktif -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Siswa Aktif</span>
                <div class="p-2 rounded-xl bg-emerald-50 text-emerald-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <span class="block text-3xl font-extrabold text-slate-900 font-mono">{{ $stats['total_students'] }}</span>
            <span class="block text-[11px] text-slate-400">{{ $stats['total_classrooms'] }} Rombel Aktif</span>
        </div>
    </div>

    <!-- Recent PPDB Submissions Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Pendaftaran PPDB Terbaru</h3>
                <p class="text-xs text-slate-500">5 calon siswa pendaftar terakhir di sistem</p>
            </div>
            <a href="{{ route('admin.ppdb.index') }}" class="text-xs font-bold text-emerald-800 hover:underline">
                Lihat Semua Data →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">No. Registrasi</th>
                        <th class="py-3.5 px-6">Nama Calon Siswa</th>
                        <th class="py-3.5 px-6">L/P</th>
                        <th class="py-3.5 px-6">Orang Tua / WA</th>
                        <th class="py-3.5 px-6">Asal Sekolah</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($latestRegistrations as $reg)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 font-mono font-bold text-slate-800">
                                {{ $reg->registration_number }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                {{ $reg->child_name }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $reg->gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $reg->gender }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="block text-slate-800">{{ $reg->parent_name }}</span>
                                <a href="https://wa.me/{{ $reg->parent_whatsapp }}" target="_blank" class="text-[10px] text-emerald-700 hover:underline font-mono">
                                    {{ $reg->parent_whatsapp }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $reg->previous_school }}
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $badge = match($reg->status->value) {
                                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'verified' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'accepted' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $badge }}">
                                    {{ $reg->status->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.ppdb.show', $reg) }}" class="inline-block px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-bold text-xs transition">
                                    Detail / Verifikasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada data pendaftaran PPDB.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
