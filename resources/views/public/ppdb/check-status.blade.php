@extends('layouts.public')

@section('title', 'Cek Status Seleksi PPDB')

@section('content')
<div class="bg-slate-50 py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Title -->
        <div class="text-center space-y-3 mb-10">
            <span class="inline-block px-3.5 py-1 rounded-full bg-emerald-100 text-emerald-900 text-xs font-bold uppercase tracking-wider">
                Layanan Mandiri PPDB
            </span>
            <h1 class="text-3xl font-extrabold text-emerald-950">
                Cek Status Seleksi PPDB Online
            </h1>
            <p class="text-sm text-slate-600 max-w-lg mx-auto">
                Masukkan <strong>Nomor Registrasi</strong> (contoh: <em>PPDB-2026-0001</em>) atau <strong>NIK 16 digit</strong> calon siswa untuk melihat progres verifikasi berkas dan hasil seleksi.
            </p>
        </div>

        <!-- Search Box Form -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 mb-10">
            <form action="/ppdb/cek-status" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="q" value="{{ $query }}" required autocomplete="off"
                        placeholder="Ketik Nomor Registrasi atau NIK Calon Siswa..."
                        class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                </div>
                <button type="submit"
                    class="px-8 py-3.5 rounded-2xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2">
                    <span>Lacak Status</span>
                    <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
        </div>

        <!-- SEARCH RESULTS -->
        @if($searched)
            @if($result)
                <div class="bg-white rounded-3xl shadow-lg border border-slate-200/80 overflow-hidden">
                    <!-- Status Header Banner -->
                    @php
                        $badgeBg = match($result->status->value) {
                            'verified' => 'bg-blue-600',
                            'accepted' => 'bg-emerald-600',
                            'rejected' => 'bg-red-600',
                            default => 'bg-amber-500',
                        };

                        $stepActive = match($result->status->value) {
                            'pending' => 1,
                            'verified' => 2,
                            'accepted' => 3,
                            'rejected' => 2,
                            default => 1,
                        };
                    @endphp

                    <div class="{{ $badgeBg }} text-white px-6 py-4 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs uppercase font-bold tracking-wider">Status Terkini:</span>
                            <span class="px-3 py-1 rounded-full bg-white/20 text-white text-xs font-extrabold uppercase">
                                {{ $result->status->label() }}
                            </span>
                        </div>
                        <span class="text-xs font-mono font-bold">{{ $result->registration_number }}</span>
                    </div>

                    <!-- Progress Stepper Visualization -->
                    <div class="px-6 py-6 bg-slate-50 border-b border-slate-200">
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="space-y-1">
                                <div class="w-7 h-7 mx-auto rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow">✓</div>
                                <span class="font-bold text-slate-800 block text-[11px]">1. Pendaftaran</span>
                                <span class="text-[10px] text-slate-400 block">Terkirim</span>
                            </div>
                            <div class="space-y-1">
                                <div class="w-7 h-7 mx-auto rounded-full {{ $stepActive >= 2 ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center font-bold text-xs shadow">
                                    {{ $stepActive >= 2 ? '✓' : '2' }}
                                </div>
                                <span class="font-bold text-slate-800 block text-[11px]">2. Verifikasi Dokumen</span>
                                <span class="text-[10px] text-slate-400 block">Panitia TU</span>
                            </div>
                            <div class="space-y-1">
                                <div class="w-7 h-7 mx-auto rounded-full {{ $stepActive >= 3 ? 'bg-emerald-600 text-white' : ($result->status->value === 'rejected' ? 'bg-red-500 text-white' : 'bg-slate-200 text-slate-600') }} flex items-center justify-center font-bold text-xs shadow">
                                    {{ $stepActive >= 3 ? '✓' : ($result->status->value === 'rejected' ? '✕' : '3') }}
                                </div>
                                <span class="font-bold text-slate-800 block text-[11px]">3. Pengumuman Kelulusan</span>
                                <span class="text-[10px] text-slate-400 block">Hasil Seleksi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Details Body -->
                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100 text-sm">
                            <div>
                                <span class="block text-xs uppercase tracking-wider text-slate-400">Nama Calon Siswa</span>
                                <span class="font-bold text-slate-800 text-base">{{ $result->child_name }}</span>
                            </div>
                            <div>
                                <span class="block text-xs uppercase tracking-wider text-slate-400">NIK Calon Siswa</span>
                                <span class="font-mono text-slate-700">{{ strlen($result->nik) === 16 ? substr($result->nik, 0, 6) . '******' . substr($result->nik, -4) : $result->nik }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">(Disamarkan demi keamanan data pribadi / UU PDP)</span>
                            </div>
                            <div>
                                <span class="block text-xs uppercase tracking-wider text-slate-400">Asal Lembaga (TK/RA/PAUD)</span>
                                <span class="text-slate-700">{{ $result->previous_school }}</span>
                            </div>
                            <div>
                                <span class="block text-xs uppercase tracking-wider text-slate-400">Orang Tua / Wali Murid</span>
                                <span class="text-slate-700">{{ $result->parent_name }}</span>
                            </div>
                        </div>

                        <!-- Biaya & Status Pembayaran -->
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <span class="block text-xs uppercase tracking-wider text-slate-400">Biaya Formulir & Observasi</span>
                                <span class="font-extrabold text-base text-slate-900 font-mono">{{ $result->formattedFee() }}</span>
                                <span class="text-xs text-slate-500 block">Metode: {{ $result->payment_method === 'cash' ? 'Bayar Tunai di Loket TU' : 'Transfer Bank / QRIS Online' }}</span>
                            </div>
                            <div class="flex flex-col sm:items-end gap-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $result->paymentStatusBadge() }}">
                                    {{ $result->paymentStatusLabel() }}
                                </span>
                                @if(! $result->isPaid())
                                    <a href="{{ route('ppdb.pay', $result->registration_number) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        <span>Bayar via QRIS (Otomatis)</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Notes from Panitia -->
                        @if($result->admin_notes)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-sm space-y-1">
                                <span class="block text-xs font-bold uppercase tracking-wider text-emerald-900">Catatan Panitia PPDB:</span>
                                <p class="text-slate-700">{{ $result->admin_notes }}</p>
                            </div>
                        @endif

                        <!-- Action Button -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                            <span class="text-xs text-slate-500">
                                Butuh perubahan berkas? Hubungi Panitia TU via WhatsApp.
                            </span>
                            <a href="{{ route('ppdb.print_proof', ['registration_number' => $result->registration_number, 'dob' => $result->birth_date?->format('Y-m-d')]) }}" target="_blank"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-sm shadow-md transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Unduh Bukti Pendaftaran (PDF)</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 rounded-3xl bg-white border border-slate-200 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Data pendaftaran tidak ditemukan</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Nomor registrasi atau NIK <strong>"{{ $query }}"</strong> belum terdaftar. Mohon pastikan nomor yang Anda ketik sudah benar atau daftar melalui formulir online.
                    </p>
                    <div class="pt-2">
                        <a href="/ppdb/daftar" class="inline-block px-5 py-2.5 rounded-xl bg-emerald-900 text-white text-xs font-bold hover:bg-emerald-800 transition">
                            Buka Formulir PPDB Online
                        </a>
                    </div>
                </div>
            @endif
        @endif

    </div>
</div>
@endsection
