@extends('layouts.public')

@section('title', 'Pendaftaran PPDB Online')

@section('content')
<div class="bg-slate-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Banner -->
        <div class="bg-emerald-900 rounded-3xl p-8 text-white shadow-xl mb-8 relative overflow-hidden border border-gold-500/30">
            <div class="relative z-10 space-y-2">
                <span class="inline-block px-3 py-1 rounded-full bg-gold-500 text-emerald-950 text-xs font-bold uppercase tracking-wider">
                    PPDB Online {{ date('Y') }}/{{ date('Y') + 1 }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                    Formulir Pendaftaran Santri Baru
                </h1>
                <p class="text-sm text-emerald-200">
                    Lengkapi data calon santri dan orang tua dengan teliti. Siapkan berkas Akta Kelahiran dan Kartu Keluarga (format JPG/PNG/PDF, maks 2MB).
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                <p class="font-bold flex items-center gap-1.5 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Mohon perbaiki kesalahan berikut sebelum melanjutkan:
                </p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Multi-Step Form with Alpine -->
        <div x-data="{ step: 1 }" class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-10">
            
            <!-- Step Navigation Tabs -->
            <div class="grid grid-cols-3 gap-2 pb-8 border-b border-slate-100 mb-8">
                <button type="button" @click="step = 1" class="text-left py-2 px-3 rounded-xl transition" :class="step === 1 ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200' : 'text-slate-400 hover:text-slate-600'">
                    <span class="block text-xs uppercase tracking-wider text-emerald-700 font-bold">Langkah 1</span>
                    <span class="text-sm hidden sm:inline">Data Calon Santri</span>
                </button>
                <button type="button" @click="step = 2" class="text-left py-2 px-3 rounded-xl transition" :class="step === 2 ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200' : 'text-slate-400 hover:text-slate-600'">
                    <span class="block text-xs uppercase tracking-wider text-emerald-700 font-bold">Langkah 2</span>
                    <span class="text-sm hidden sm:inline">Data Orang Tua</span>
                </button>
                <button type="button" @click="step = 3" class="text-left py-2 px-3 rounded-xl transition" :class="step === 3 ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200' : 'text-slate-400 hover:text-slate-600'">
                    <span class="block text-xs uppercase tracking-wider text-emerald-700 font-bold">Langkah 3</span>
                    <span class="text-sm hidden sm:inline">Unggah Berkas</span>
                </button>
            </div>

            <form action="/ppdb/daftar" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- STEP 1: BIODATA CALON SANTRI -->
                <div x-show="step === 1" class="space-y-6">
                    <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100">
                        1. Identitas Calon Santri
                    </h3>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Nama Lengkap Santri <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="child_name" value="{{ old('child_name') }}" required
                            placeholder="Sesuai Akta Kelahiran"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                NIK Santri (16 Digit) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" required
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
                                <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Tempat Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="birth_place" value="{{ old('birth_place') }}" required
                                placeholder="Kota / Kabupaten Lahir"
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
                            Asal Sekolah / Lembaga Sebelumnya <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="previous_school" value="{{ old('previous_school') }}" required
                            placeholder="Contoh: RA Perwanida Kalipucung / TK Dharma Wanita"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="button" @click="step = 2"
                            class="px-6 py-3 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-sm flex items-center gap-2 transition">
                            <span>Lanjut: Data Orang Tua</span>
                            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: DATA ORANG TUA / WALI -->
                <div x-show="step === 2" x-cloak class="space-y-6">
                    <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100">
                        2. Identitas Orang Tua / Wali
                    </h3>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Nama Orang Tua / Wali <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="parent_name" value="{{ old('parent_name') }}" required
                            placeholder="Nama Ayah / Ibu / Wali Santri"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Nomor WhatsApp Aktif <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="parent_whatsapp" value="{{ old('parent_whatsapp') }}" required
                                placeholder="Contoh: 081234567890"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm font-mono">
                            <span class="text-[11px] text-slate-500 mt-1 block">Digunakan untuk notifikasi status pendaftaran dan akun portal wali murid.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Pekerjaan Orang Tua
                            </label>
                            <input type="text" name="parent_job" value="{{ old('parent_job') }}"
                                placeholder="Contoh: PNS / Wiraswasta / Petani"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Alamat Domisili Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" rows="3" required
                            placeholder="Nama Dusun, RT/RW, Desa Kalipucung, Kecamatan Sanankulon, Blitar"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 focus:border-transparent text-sm">{{ old('address') }}</textarea>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 1"
                            class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition">
                            Kembali
                        </button>
                        <button type="button" @click="step = 3"
                            class="px-6 py-3 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-sm flex items-center gap-2 transition">
                            <span>Lanjut: Unggah Berkas</span>
                            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: UNGGAH DOKUMEN -->
                <div x-show="step === 3" x-cloak class="space-y-6">
                    <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100">
                        3. Unggah Dokumen Kelengkapan
                    </h3>

                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/50">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Foto / Scan Akta Kelahiran <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="file_birth_certificate" required accept=".jpg,.jpeg,.png,.pdf"
                                class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-900 hover:file:bg-emerald-200">
                            <span class="text-[11px] text-slate-500 block mt-1">Format: JPG, PNG, atau PDF (Maksimal 2MB).</span>
                        </div>

                        <div class="p-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/50">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Foto / Scan Kartu Keluarga (KK) <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="file_family_card" required accept=".jpg,.jpeg,.png,.pdf"
                                class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-900 hover:file:bg-emerald-200">
                            <span class="text-[11px] text-slate-500 block mt-1">Format: JPG, PNG, atau PDF (Maksimal 2MB).</span>
                        </div>

                        <div class="p-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/50">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Pasfoto Calon Santri (Opsional)
                            </label>
                            <input type="file" name="file_photo" accept=".jpg,.jpeg,.png"
                                class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300">
                            <span class="text-[11px] text-slate-500 block mt-1">Format: JPG atau PNG (Maksimal 2MB).</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                        <p class="font-bold mb-1">Pernyataan Wali Santri:</p>
                        <p>Dengan mengirim formulir ini, saya menyatakan data yang diisikan adalah benar dan bersedia mematuhi seluruh tata tertib MI Plus Raudlatul Muta'allimin Kalipucung.</p>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 2"
                            class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition">
                            Kembali
                        </button>
                        <button type="submit"
                            class="px-8 py-3.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-sm shadow-lg hover:shadow-xl transition flex items-center gap-2">
                            <span>Kirim Pendaftaran Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
