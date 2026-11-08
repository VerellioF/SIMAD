@extends('layouts.portal')

@section('title', 'Seleksi PPDB Online')

@section('content')
<div class="space-y-6">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Data Seleksi PPDB Online</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola dan verifikasi berkas calon siswa baru MI Plus Raudlatul Muta'allimin</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ppdb.export_emis') }}"
               class="px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export EMIS Excel</span>
            </a>
        </div>
    </div>

    <!-- Status Filters Bar -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.ppdb.index') }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ empty($status) ? 'bg-emerald-900 text-white border-emerald-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Semua ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.ppdb.index', ['status' => 'pending']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ $status === 'pending' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-amber-700 border-slate-200 hover:bg-amber-50' }}">
            Menunggu Verifikasi ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.ppdb.index', ['status' => 'verified']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ $status === 'verified' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-blue-700 border-slate-200 hover:bg-blue-50' }}">
            Berkas Valid ({{ $counts['verified'] }})
        </a>
        <a href="{{ route('admin.ppdb.index', ['status' => 'accepted']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ $status === 'accepted' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-emerald-700 border-slate-200 hover:bg-emerald-50' }}">
            Diterima ({{ $counts['accepted'] }})
        </a>
        <a href="{{ route('admin.ppdb.index', ['status' => 'rejected']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ $status === 'rejected' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-red-700 border-slate-200 hover:bg-red-50' }}">
            Ditolak ({{ $counts['rejected'] }})
        </a>
    </div>

    <!-- Search Bar Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('admin.ppdb.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari berdasarkan Nama Siswa, NIK, No. Registrasi, atau Nama Wali..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-xs">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                Cari Data
            </button>
            @if($search)
                <a href="{{ route('admin.ppdb.index', array_filter(['status' => $status])) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 text-center transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">No. Registrasi</th>
                        <th class="py-3.5 px-6">Nama Calon Siswa</th>
                        <th class="py-3.5 px-6">NIK</th>
                        <th class="py-3.5 px-6">L/P</th>
                        <th class="py-3.5 px-6">Wali & Kontak</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Tgl Daftar</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($registrations as $reg)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 font-mono font-bold text-slate-800">
                                {{ $reg->registration_number }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $reg->child_name }}</span>
                                <span class="text-[11px] text-slate-400 block">{{ $reg->birth_place }}, {{ $reg->birth_date->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-600">
                                {{ $reg->nik }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $reg->gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $reg->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-medium text-slate-800 block">{{ $reg->parent_name }}</span>
                                <a href="https://wa.me/{{ $reg->parent_whatsapp }}" target="_blank" class="text-emerald-700 hover:underline font-mono text-[11px]">
                                    {{ $reg->parent_whatsapp }}
                                </a>
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
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $reg->paymentStatusBadge() }}">
                                        {{ $reg->paymentStatusLabel() }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-mono text-[11px]">
                                {{ $reg->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('ppdb.print_proof', $reg->registration_number) }}" target="_blank"
                                   class="inline-block p-1.5 rounded-lg text-slate-500 hover:text-emerald-800 hover:bg-slate-100 transition" title="Cetak PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                </a>
                                <a href="{{ route('admin.ppdb.show', $reg) }}"
                                   class="inline-block px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-bold text-xs transition">
                                    Verifikasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                Tidak ada data pendaftar yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
