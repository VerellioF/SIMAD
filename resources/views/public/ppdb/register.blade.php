@extends('layouts.public')

@section('title', 'Pendaftaran PPDB Online')

@section('content')
<div class="bg-slate-50 py-12" x-data="{
    step: 1,
    nik: '{{ old('nik', '') }}',
    wa: '{{ old('parent_whatsapp', '') }}',
    fileAktaName: '',
    fileKkName: '',
    fileFotoName: '',
    filePaymentName: '',
    paymentMethod: '{{ old('payment_method', 'transfer') }}',
}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-emerald-950 via-[#043327] to-emerald-950 rounded-3xl p-8 text-white shadow-xl mb-8 relative overflow-hidden border-2 border-gold-500/40 pattern-islamic-dark">
            <div class="hidden sm:block absolute top-0 right-10 w-16 pointer-events-none opacity-80">
                <img src="{{ asset('images/ornaments/hanging-lantern.svg') }}" class="w-full h-auto drop-shadow-[0_8px_16px_rgba(251,191,36,0.3)]" alt="">
            </div>
            <div class="relative z-10 space-y-2 max-w-2xl">
                <span class="inline-block px-3 py-1 rounded-full bg-gold-500 text-emerald-950 text-xs font-bold uppercase tracking-wider">
                    PPDB Online {{ date('Y') }}/{{ date('Y') + 1 }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                    Formulir Pendaftaran Siswa Baru (PPDB Online)
                </h1>
                <p class="text-xs text-gold-400 font-semibold tracking-wide">
                    Formulir Pendaftaran Santri Baru Jenjang Sekolah Dasar / Madrasah Ibtidaiyah (SD/MI)
                </p>
                <p class="text-sm text-emerald-200">
                    Lengkapi data calon siswa dan orang tua dengan teliti. Cukup isi data pokok terlebih dahulu, berkas fisik dapat menyusul saat datang ke sekolah.
                </p>
            </div>
        </div>

        <!-- Panduan Ramah Orang Tua / Awam -->
        <div class="mb-6 p-4 sm:p-5 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm flex items-start gap-3.5 shadow-sm">
            <span class="text-2xl shrink-0">💡</span>
            <div class="space-y-1">
                <p class="font-bold text-emerald-950 text-sm">Petunjuk Pengisian untuk Bapak/Ibu Wali Murid:</p>
                <p class="text-xs text-emerald-800 leading-relaxed">
                    Pengisian formulir sangat mudah dan hanya butuh waktu ± 3 menit. Jika ada berkas foto (Akta/KK) atau bukti transfer yang belum sempat disiapkan di HP saat ini, <strong>Bapak/Ibu tetap bisa mengirimkan pendaftaran terlebih dahulu</strong>. Berkas fisik dan administrasi pendaftaran dapat diserahkan langsung saat berkunjung ke loket Tata Usaha sekolah.
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm shadow-sm">
                <p class="font-bold flex items-center gap-1.5 mb-2">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Mohon periksa dan perbaiki kesalahan pengisian berikut:
                </p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card Container -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-10">
            
            <!-- Step Navigation Tabs / Stepper Bar -->
            <div class="grid grid-cols-3 gap-2 pb-6 border-b border-slate-100 mb-8">
                <button type="button" @click="step = 1" class="text-left py-2.5 px-3 rounded-2xl transition border" :class="step === 1 ? 'bg-emerald-50 text-emerald-950 font-bold border-emerald-300 ring-2 ring-emerald-500/20' : 'border-slate-100 text-slate-400 hover:text-slate-600'">
                    <span class="block text-[11px] uppercase tracking-wider font-bold" :class="step === 1 ? 'text-emerald-700' : 'text-slate-400'">Langkah 1</span>
                    <span class="text-xs sm:text-sm font-semibold truncate block">Data Calon Siswa</span>
                    <span class="sr-only">Data Calon Santri</span>
                </button>
                <button type="button" @click="step = 2" class="text-left py-2.5 px-3 rounded-2xl transition border" :class="step === 2 ? 'bg-emerald-50 text-emerald-950 font-bold border-emerald-300 ring-2 ring-emerald-500/20' : 'border-slate-100 text-slate-400 hover:text-slate-600'">
                    <span class="block text-[11px] uppercase tracking-wider font-bold" :class="step === 2 ? 'text-emerald-700' : 'text-slate-400'">Langkah 2</span>
                    <span class="text-xs sm:text-sm font-semibold truncate block">Data Orang Tua / Wali</span>
                </button>
                <button type="button" @click="step = 3" class="text-left py-2.5 px-3 rounded-2xl transition border" :class="step === 3 ? 'bg-emerald-50 text-emerald-950 font-bold border-emerald-300 ring-2 ring-emerald-500/20' : 'border-slate-100 text-slate-400 hover:text-slate-600'">
                    <span class="block text-[11px] uppercase tracking-wider font-bold" :class="step === 3 ? 'text-emerald-700' : 'text-slate-400'">Langkah 3</span>
                    <span class="text-xs sm:text-sm font-semibold truncate block">Unggah Berkas & Biaya</span>
                </button>
            </div>

            <form action="/ppdb/daftar" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- STEP 1: BIODATA CALON SISWA -->
                <div x-show="step === 1" class="space-y-6">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <h3 class="text-base sm:text-lg font-bold text-slate-800">
                            1. Identitas Calon Siswa / Siswi
                        </h3>
                        <span class="text-xs text-slate-400">Tahap 1 dari 3</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Nama Lengkap Calon Siswa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="child_name" value="{{ old('child_name') }}" required
                            placeholder="Sesuai Akta Kelahiran (huruf kapital rapi)"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    NIK Calon Siswa (16 Digit Sesuai KK) <span class="text-red-500">*</span>
                                </label>
                                <span class="text-[10px] font-mono" :class="nik.length === 16 ? 'text-emerald-600 font-bold' : 'text-slate-400'" x-text="nik.length + ' / 16'"></span>
                            </div>
                            <input type="text" name="nik" x-model="nik" value="{{ old('nik') }}" maxlength="16" required
                                placeholder="Contoh: 350501xxxxxxxxxx"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <select name="gender" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm bg-white">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki (Putra)</option>
                                <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan (Putri)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Tempat Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="birth_place" value="{{ old('birth_place') }}" required
                                placeholder="Kota / Kabupaten Tempat Lahir"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Asal Lembaga Sebelumnya (TK / RA / PAUD) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="previous_school" value="{{ old('previous_school') }}" required
                            placeholder="Contoh: RA Perwanida Kalipucung / TK Dharma Wanita"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="button" @click="step = 2"
                            class="px-6 py-3 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-sm flex items-center gap-2 transition shadow">
                            <span>Lanjut: Data Orang Tua</span>
                            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: DATA ORANG TUA / WALI -->
                <div x-show="step === 2" x-cloak class="space-y-6">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <h3 class="text-base sm:text-lg font-bold text-slate-800">
                            2. Identitas Orang Tua / Wali Murid
                        </h3>
                        <span class="text-xs text-slate-400">Tahap 2 dari 3</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Nama Lengkap Orang Tua / Wali (Bapak / Ibu) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="parent_name" value="{{ old('parent_name') }}" required
                            placeholder="Nama Ayah / Ibu / Wali Murid"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Nomor WhatsApp Aktif Bapak / Ibu <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="parent_whatsapp" x-model="wa" value="{{ old('parent_whatsapp') }}" required
                                placeholder="Contoh: 081234567890"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm font-mono">
                            <span class="text-[11px] text-slate-500 mt-1 block">Digunakan untuk informasi jadwal observasi santai dan akses akun portal orang tua siswa.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Pekerjaan Orang Tua / Wali
                            </label>
                            <input type="text" name="parent_job" value="{{ old('parent_job') }}"
                                placeholder="Contoh: Wiraswasta / Karyawan / PNS / Petani / Ibu Rumah Tangga"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Alamat Rumah / Domisili Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" rows="3" required
                            placeholder="Nama Jalan / Dusun, RT/RW, Desa/Kelurahan, Kecamatan, Kabupaten"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">{{ old('address') }}</textarea>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 1"
                            class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition">
                            ← Kembali
                        </button>
                        <button type="button" @click="step = 3"
                            class="px-6 py-3 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-sm flex items-center gap-2 transition shadow">
                            <span>Lanjut: Unggah Berkas & Biaya</span>
                            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: UNGGAH DOKUMEN -->
                <div x-show="step === 3" x-cloak class="space-y-6">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <h3 class="text-base sm:text-lg font-bold text-slate-800">
                            3. Unggah Berkas & Biaya Pendaftaran
                        </h3>
                        <span class="text-xs text-slate-400">Tahap 3 dari 3</span>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">A. Dokumen Persyaratan</h4>

                        <!-- Akta Kelahiran -->
                        <div class="p-5 rounded-2xl border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50/50 transition">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Scan / Foto Akta Kelahiran <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="file_birth_certificate" required accept=".jpg,.jpeg,.png,.pdf"
                                @change="fileAktaName = $event.target.files[0]?.name || ''"
                                class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-900 hover:file:bg-emerald-200">
                            <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                                <span>Format: PDF / JPG / PNG (Maksimal 2MB)</span>
                                <span x-show="fileAktaName" class="text-emerald-700 font-semibold" x-text="'File terpilih: ' + fileAktaName"></span>
                            </div>
                        </div>

                        <!-- Kartu Keluarga -->
                        <div class="p-5 rounded-2xl border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50/50 transition">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Scan / Foto Kartu Keluarga (KK) <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="file_family_card" required accept=".jpg,.jpeg,.png,.pdf"
                                @change="fileKkName = $event.target.files[0]?.name || ''"
                                class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-900 hover:file:bg-emerald-200">
                            <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                                <span>Format: PDF / JPG / PNG (Maksimal 2MB)</span>
                                <span x-show="fileKkName" class="text-emerald-700 font-semibold" x-text="'File terpilih: ' + fileKkName"></span>
                            </div>
                        </div>

                        <!-- Pasfoto -->
                        <div class="p-5 rounded-2xl border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50/50 transition">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Pasfoto Calon Siswa (Opsional - Foto seragam/bebas rapi)
                            </label>
                            <input type="file" name="file_photo" accept=".jpg,.jpeg,.png"
                                @change="fileFotoName = $event.target.files[0]?.name || ''"
                                class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300">
                            <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                                <span>Format: JPG / PNG (Maksimal 2MB)</span>
                                <span x-show="fileFotoName" class="text-emerald-700 font-semibold" x-text="'File terpilih: ' + fileFotoName"></span>
                            </div>
                        </div>
                    </div>

                    <!-- B. Biaya & Metode Pembayaran -->
                    <div class="pt-4 border-t border-slate-100 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">B. Biaya Pendaftaran & Observasi</h4>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-900 font-extrabold text-xs">
                                {{ config('simad.ppdb_fee_formatted', 'Rp 50.000') }}
                            </span>
                        </div>

                        <!-- Info Card Nominal -->
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-900 to-emerald-950 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-gold-500/30">
                            <div>
                                <span class="text-[11px] uppercase tracking-wider text-gold-400 font-bold block">Biaya Formulir & Infaq Observasi</span>
                                <p class="text-xs text-emerald-100 mt-1 max-w-md">
                                    Mencakup map fisik portofolio siswa, kartu peserta, instrumen observasi kesiapan belajar anak yang ramah dan menyenangkan, serta souvenir peserta.
                                </p>
                            </div>
                            <div class="text-right sm:text-right shrink-0">
                                <span class="text-2xl font-black text-gold-400 font-mono">{{ config('simad.ppdb_fee_formatted', 'Rp 50.000') }}</span>
                            </div>
                        </div>

                        <!-- Pilihan Metode Pembayaran -->
                        <div class="space-y-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Pilih Cara Pembayaran <span class="text-red-500">*</span>
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Transfer Bank -->
                                <label class="relative flex items-start p-4 rounded-2xl border-2 cursor-pointer transition"
                                    :class="paymentMethod === 'transfer' ? 'border-emerald-600 bg-emerald-50/50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'">
                                    <input type="radio" name="payment_method" value="transfer" x-model="paymentMethod" class="mt-1 text-emerald-800 focus:ring-emerald-600">
                                    <div class="ml-3">
                                        <span class="block text-xs font-bold text-slate-900">Transfer Bank (BSI Syariah)</span>
                                        <span class="block text-[11px] text-slate-500 mt-0.5">Transfer via ATM / Mobile Banking, lalu unggah bukti bayar.</span>
                                    </div>
                                </label>

                                <!-- Bayar Tunai -->
                                <label class="relative flex items-start p-4 rounded-2xl border-2 cursor-pointer transition"
                                    :class="paymentMethod === 'cash' ? 'border-emerald-600 bg-emerald-50/50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'">
                                    <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="mt-1 text-emerald-800 focus:ring-emerald-600">
                                    <div class="ml-3">
                                        <span class="block text-xs font-bold text-slate-900">Bayar Tunai di Loket TU</span>
                                        <span class="block text-[11px] text-slate-500 mt-0.5">Bisa dibayarkan saat datang menyerahkan berkas fisik atau saat observasi.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Transfer Instruction & Proof Upload (Show if transfer) -->
                        <div x-show="paymentMethod === 'transfer'" x-transition class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 bg-white rounded-xl border border-slate-200/80">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Bank Tujuan</span>
                                    <span class="font-bold text-xs text-slate-800">Bank Syariah Indonesia (BSI)</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Nomor Rekening</span>
                                    <span class="font-mono font-extrabold text-sm text-emerald-900 select-all">7123456789</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Atas Nama</span>
                                    <span class="font-semibold text-xs text-slate-800">YAYASAN RAUDLATUL MUTA'ALLIMIN</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Unggah Bukti Transfer <span class="text-xs font-normal text-slate-400">(Opsional jika bayar kemudian)</span>
                                </label>
                                <input type="file" name="file_payment_proof" accept=".jpg,.jpeg,.png,.pdf"
                                    @change="filePaymentName = $event.target.files[0]?.name || ''"
                                    class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-900 hover:file:bg-emerald-200">
                                <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                                    <span>Format: JPG / PNG / PDF (Maksimal 2MB)</span>
                                    <span x-show="filePaymentName" class="text-emerald-700 font-semibold" x-text="'File terpilih: ' + filePaymentName"></span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Syarat & Ketentuan Accordion / Callout -->
                    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-amber-900 text-xs space-y-1">
                        <p class="font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pernyataan Orang Tua / Wali Murid:
                        </p>
                        <p class="leading-relaxed">
                            Dengan mengklik tombol <strong>"Kirim Pendaftaran Sekarang"</strong>, saya menyatakan data yang diisikan adalah benar dan bersedia mengikuti alur seleksi observasi serta tata tertib di MI Plus Raudlatul Muta'allimin.
                        </p>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 2"
                            class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition">
                            ← Kembali
                        </button>
                        <button type="submit"
                            class="px-8 py-3.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-sm shadow-lg hover:shadow-xl transition flex items-center gap-2">
                            <span>Kirim Pendaftaran Siswa Baru Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Quick Footer Link -->
        <div class="text-center mt-6">
            <p class="text-xs text-slate-500">
                Sudah pernah mendaftar sebelumnya?
                <a href="/ppdb/cek-status" class="text-emerald-800 font-bold hover:underline ml-1">
                    Cek Status Pendaftaran di Sini →
                </a>
            </p>
        </div>

    </div>
</div>
@endsection
