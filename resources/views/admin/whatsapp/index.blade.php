@extends('layouts.portal')

@section('title', 'Notifikasi WhatsApp & Gateway Hub')

@section('content')
<div class="space-y-6" x-data="{ targetType: 'single', testModal: false }">
    <!-- Header -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Layanan Komunikasi Madrasah
                </span>
                <span class="text-xs text-slate-400">Hybrid Mode: Gateway API & Direct wa.me</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">Notifikasi WhatsApp ke Wali Murid</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Monitoring pesan otomatis presensi (alpa/bolos), pembinaan karakter, surat izin, dan pengumuman madrasah.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="testModal = true" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                <span>Uji Coba Kirim WA</span>
            </button>
        </div>
    </div>

    <!-- Alert Success / Direct Link -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            @if(session('wa_direct_url'))
                <a href="{{ session('wa_direct_url') }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition shrink-0">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    <span>Buka WhatsApp Sekarang &rarr;</span>
                </a>
            @endif
        </div>
    @endif

    <!-- Status Cards: Mode & Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Card 1: Mode Status -->
        <div class="bg-gradient-to-tr from-emerald-950 via-emerald-900 to-emerald-950 text-white rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Status Integrasi</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $gatewayConfig['auto_send'] ? 'bg-emerald-500 text-white' : 'bg-gold-500/20 text-gold-400 border border-gold-500/30' }}">
                        {{ $gatewayConfig['auto_send'] ? 'Otomatis Gateway API' : 'Mode Siap Kirim (Direct wa.me)' }}
                    </span>
                </div>
                <h3 class="text-xl font-black mt-2 text-white capitalize">
                    Provider: {{ $gatewayConfig['provider'] }}
                </h3>
                <p class="text-xs text-emerald-200 mt-1">
                    {{ $gatewayConfig['has_token'] ? 'Token gateway aktif terkonfigurasi di server.' : 'Token API kosong (menggunakan direct click wa.me gratis tanpa biaya).' }}
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-emerald-800 text-[11px] text-emerald-300">
                <span>Pengirim: <strong>{{ config('simad.school_name') }}</strong></span>
            </div>
        </div>

        <!-- Card 2: Pengiriman Otomatis Presensi & Disiplin -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Trigger Otomatis Aktif</span>
                <div class="mt-2 space-y-1.5 text-xs text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span><strong>Presensi Alpa / Bolos:</strong> Langsung disiapkan notifikasi ke wali.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span><strong>Catatan Karakter / Disiplin:</strong> Poin bintang & teguran santun.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span><strong>Surat Izin Madrasah:</strong> Konfirmasi persetujuan dari wali kelas.</span>
                    </div>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-3 pt-3 border-t border-slate-100">
                Template disusun sopan, rapi, dan mencantumkan identitas madrasah.
            </p>
        </div>

        <!-- Card 3: Rekap Log Pesan -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Statistik Pengiriman Pesan</span>
            <div class="grid grid-cols-3 gap-2 text-center mt-2">
                <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <span class="text-xl font-black text-emerald-900 block font-mono">{{ $logs->total() }}</span>
                    <span class="text-[10px] font-bold text-emerald-700 uppercase">Total Log</span>
                </div>
                <div class="p-3 bg-teal-50 rounded-2xl border border-teal-100">
                    <span class="text-xl font-black text-teal-900 block font-mono">{{ \App\Models\WhatsAppLog::where('type', 'attendance')->count() }}</span>
                    <span class="text-[10px] font-bold text-teal-700 uppercase">Presensi</span>
                </div>
                <div class="p-3 bg-amber-50 rounded-2xl border border-amber-100">
                    <span class="text-xl font-black text-amber-900 block font-mono">{{ \App\Models\WhatsAppLog::where('type', 'conduct')->count() }}</span>
                    <span class="text-[10px] font-bold text-amber-700 uppercase">Disiplin</span>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-2 text-center">Rekaman tersimpan aman untuk audit TU.</p>
        </div>
    </div>

    <!-- Form Kirim Pesan Khusus / Broadcast -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Kirim Pesan WhatsApp Manual / Siaran Rombel</h2>
                <p class="text-xs text-slate-500">Kirim pemberitahuan khusus kepada satu wali murid atau siarkan ke seluruh wali murid satu rombel kelas.</p>
            </div>
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                <button type="button" @click="targetType = 'single'" :class="targetType === 'single' ? 'bg-white shadow text-slate-900' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg transition">
                    Per Siswa (Personal)
                </button>
                <button type="button" @click="targetType = 'broadcast'" :class="targetType === 'broadcast' ? 'bg-white shadow text-slate-900' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg transition">
                    Siaran Satu Rombel
                </button>
            </div>
        </div>

        <form action="{{ route('admin.whatsapp.send') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="target_type" :value="targetType">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Single Target -->
                <div x-show="targetType === 'single'">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Siswa & Orang Tua / Wali *</label>
                    <select name="student_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-800">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">
                                {{ $st->full_name }} (Kelas {{ $st->classroom?->name ?? '-' }}) - Wali: {{ $st->parent?->name ?? 'Belum ada akun wali' }} [{{ $st->parent?->phone_number ?? 'No HP Kosong' }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Broadcast Target -->
                <div x-show="targetType === 'broadcast'" x-cloak>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Rombongan Belajar (Kelas) *</label>
                    <select name="classroom_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-800">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}">
                                {{ $cls->name }} (Kelas {{ $cls->grade_level }}) - {{ $cls->students()->count() }} siswa
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Custom Phone Fallback (optional) -->
                <div x-show="targetType === 'single'">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp Manual (Opsional)</label>
                    <input type="text" name="custom_phone" placeholder="Contoh: 081234567890 (Jika nomor berbeda dari profil)"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Isi Pesan WhatsApp *</label>
                <textarea name="message" rows="4" required
                          placeholder="Tulis pesan resmi madrasah di sini... Untuk siaran rombel, Anda dapat menggunakan tag {nama_siswa} dan {nama_wali} untuk personalisasi otomatis."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">
                    Variabel dinamis yang didukung: <code class="text-emerald-700 font-bold">{nama_siswa}</code>, <code class="text-emerald-700 font-bold">{nama_wali}</code>.
                </p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-950 hover:bg-emerald-900 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    <span>Kirim Notifikasi WhatsApp</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Log Pesan WhatsApp -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Riwayat Pengiriman Notifikasi WhatsApp</h3>
                <p class="text-xs text-slate-500 mt-0.5">Catatan pengiriman pesan otomatis dan pesan manual yang tercatat di sistem.</p>
            </div>

            <!-- Filter Tipe -->
            <form method="GET" action="{{ route('admin.whatsapp.index') }}" class="flex flex-wrap items-center gap-2">
                <select name="type" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 bg-white">
                    <option value="">Semua Kategori</option>
                    <option value="attendance" {{ request('type') === 'attendance' ? 'selected' : '' }}>Presensi / Alpa</option>
                    <option value="conduct" {{ request('type') === 'conduct' ? 'selected' : '' }}>Disiplin / Karakter</option>
                    <option value="leave" {{ request('type') === 'leave' ? 'selected' : '' }}>Surat Izin</option>
                    <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>Umum & Siaran</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 bg-white">
                    <option value="">Semua Status</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Otomatis Terkirim</option>
                    <option value="manual_click" {{ request('status') === 'manual_click' ? 'selected' : '' }}>Direct wa.me</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Waktu</th>
                        <th class="py-3.5 px-6">Penerima & Siswa</th>
                        <th class="py-3.5 px-6">Kategori</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Isi Pesan Singkat</th>
                        <th class="py-3.5 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        @php
                            $waUrl = 'https://wa.me/' . $log->recipient_phone . '?text=' . rawurlencode($log->message);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-6 text-slate-500 font-mono text-[11px] whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="font-bold text-slate-900 block">{{ $log->recipient_name ?? 'Wali Murid' }}</span>
                                <span class="text-[11px] text-slate-500 font-mono block">{{ $log->recipient_phone }}</span>
                                @if($log->student)
                                    <span class="inline-block mt-0.5 text-[10px] text-emerald-800 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md">
                                        Ananda: {{ $log->student->full_name }} ({{ $log->student->classroom?->name ?? '-' }})
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6">
                                @if($log->type === 'attendance')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Presensi / Alpa
                                    </span>
                                @elseif($log->type === 'conduct')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                        Disiplin & Karakter
                                    </span>
                                @elseif($log->type === 'leave')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                        Surat Izin
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                        Pemberitahuan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6">
                                @if($log->status === 'sent')
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-bold text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Terkirim API
                                    </span>
                                @elseif($log->status === 'manual_click')
                                    <span class="inline-flex items-center gap-1 text-blue-700 font-bold text-xs">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                        Direct wa.me
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-rose-600 font-bold text-xs">
                                        Gagal
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 max-w-xs truncate text-slate-600 font-mono text-[11px]" title="{{ $log->message }}">
                                {{ Str::limit($log->message, 70) }}
                            </td>
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                <a href="{{ $waUrl }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-[11px] border border-emerald-200 transition">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                    <span>Buka Chat</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                Belum ada riwayat pengiriman notifikasi WhatsApp. Notifikasi akan otomatis dicatat ketika presensi disimpan atau catatan karakter diinput.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Uji Coba Gateway -->
    <div x-show="testModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="testModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="text-base font-black text-slate-900">Uji Coba Kirim WhatsApp</h3>
                <button @click="testModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <p class="text-xs text-slate-500">
                Masukkan nomor WhatsApp Anda untuk menguji apakah format pesan dan tautan kirim berfungsi dengan baik.
            </p>

            <form action="{{ route('admin.whatsapp.test') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp Penguji *</label>
                    <input type="text" name="test_phone" placeholder="Contoh: 081234567890" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800">
                </div>

                <div class="p-3 bg-slate-50 rounded-2xl text-[11px] text-slate-500 space-y-1">
                    <p class="font-bold text-slate-700">Catatan Integrasi:</p>
                    <p>• Jika <code>WA_AUTO_SEND=true</code> diatur, sistem mencoba HTTP POST ke gateway API.</p>
                    <p>• Jika belum aktif, sistem akan langsung membuka WhatsApp Web/Aplikasi dengan template pesan siap kirim.</p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="testModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-950 hover:bg-emerald-900 text-white text-xs font-bold shadow">
                        Kirim Pesan Uji
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
