@extends('layouts.portal')

@section('title', 'Verifikasi Berkas - ' . $registration->registration_number)

@section('content')
<div class="space-y-6">

    <!-- Back Button & Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ppdb.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Verifikasi Pendaftar PPDB</h1>
                <p class="text-xs text-slate-500 font-mono">{{ $registration->registration_number }} • Masuk {{ $registration->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('ppdb.print_proof', $registration->registration_number) }}" target="_blank"
               class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center gap-2 transition shadow-sm">
                <svg class="w-4 h-4 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Lembar Bukti (PDF)</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Data Siswa & Dokumen (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Biodata Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    <span>Identitas Calon Siswa</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Nama Lengkap</span>
                        <span class="font-bold text-slate-900 text-sm block mt-0.5">{{ $registration->child_name }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">NIK Calon Siswa</span>
                        <span class="font-mono font-bold text-slate-800 text-sm block mt-0.5">{{ $registration->nik }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Jenis Kelamin</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $registration->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Tempat & Tanggal Lahir</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $registration->birth_place }}, {{ $registration->birth_date->format('d F Y') }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Asal Sekolah Sebelumnya</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $registration->previous_school }}</span>
                    </div>
                </div>

                <h3 class="text-base font-bold text-slate-900 pt-3 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold-500"></span>
                    <span>Data Orang Tua / Wali</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Nama Orang Tua / Wali</span>
                        <span class="font-bold text-slate-900 block mt-0.5">{{ $registration->parent_name }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Kontak WhatsApp</span>
                        <a href="https://wa.me/{{ $registration->parent_whatsapp }}" target="_blank" class="font-mono font-bold text-emerald-800 hover:underline block mt-0.5">
                            {{ $registration->parent_whatsapp }} ↗
                        </a>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Pekerjaan</span>
                        <span class="font-medium text-slate-800 block mt-0.5">{{ $registration->parent_job ?? '-' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Alamat Lengkap</span>
                        <span class="font-medium text-slate-800 block mt-0.5">{{ $registration->address }}</span>
                    </div>
                </div>
            </div>

            <!-- Uploaded Documents Preview Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-4">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <span>Dokumen Berkas Unggahan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Akta Kelahiran -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div>
                            <span class="font-bold text-slate-800 block">Akta Kelahiran</span>
                            <span class="text-[10px] text-slate-400 font-mono truncate block">{{ $registration->file_birth_certificate }}</span>
                        </div>
                        <a href="{{ asset('storage/' . $registration->file_birth_certificate) }}" target="_blank"
                           class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-emerald-50 text-emerald-900 hover:bg-emerald-100 font-bold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Buka / Unduh Akta</span>
                        </a>
                    </div>

                    <!-- Kartu Keluarga -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div>
                            <span class="font-bold text-slate-800 block">Kartu Keluarga (KK)</span>
                            <span class="text-[10px] text-slate-400 font-mono truncate block">{{ $registration->file_family_card }}</span>
                        </div>
                        <a href="{{ asset('storage/' . $registration->file_family_card) }}" target="_blank"
                           class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-emerald-50 text-emerald-900 hover:bg-emerald-100 font-bold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Buka / Unduh KK</span>
                        </a>
                    </div>

                    @if($registration->file_photo)
                        <!-- Pasfoto -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                            <div>
                                <span class="font-bold text-slate-800 block">Pasfoto Calon Siswa</span>
                                <span class="text-[10px] text-slate-400 font-mono truncate block">{{ $registration->file_photo }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $registration->file_photo) }}" target="_blank"
                               class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-100 text-slate-800 hover:bg-slate-200 font-bold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Lihat Pasfoto</span>
                            </a>
                        </div>
                    @endif

                    @if($registration->file_payment_proof)
                        <!-- Bukti Pembayaran -->
                        <div class="p-4 rounded-2xl border border-emerald-200 bg-emerald-50/40 space-y-3">
                            <div>
                                <span class="font-bold text-emerald-950 block">Bukti Pembayaran / Transfer</span>
                                <span class="text-[10px] text-emerald-700 font-mono truncate block">{{ $registration->file_payment_proof }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $registration->file_payment_proof) }}" target="_blank"
                               class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Lihat Bukti Transfer</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column: Verification Panel (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md p-6 space-y-4 sticky top-24">
                <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                    Tindakan Verifikasi
                </h3>

                <form action="{{ route('admin.ppdb.update_status', $registration) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Biaya Pendaftaran Info Box -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Biaya Formulir</span>
                            <span class="font-mono font-extrabold text-sm text-slate-900">{{ $registration->formattedFee() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Metode:</span>
                            <span class="font-bold text-slate-800">{{ $registration->payment_method === 'cash' ? 'Tunai (Loket TU)' : 'Transfer BSI' }}</span>
                        </div>
                        @if($registration->paid_at)
                            <div class="text-[10px] text-emerald-700 font-mono">
                                Terverifikasi: {{ $registration->paid_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Status Pembayaran
                        </label>
                        <select name="payment_status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent">
                            <option value="unpaid" {{ $registration->payment_status === 'unpaid' ? 'selected' : '' }}>Belum Bayar (Unpaid)</option>
                            <option value="paid" {{ $registration->payment_status === 'paid' ? 'selected' : '' }}>Lunas / Terverifikasi (Paid)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Status Seleksi Calon Siswa
                        </label>
                        <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent">
                            <option value="pending" {{ $registration->status->value === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi (Pending)</option>
                            <option value="verified" {{ $registration->status->value === 'verified' ? 'selected' : '' }}>Berkas Valid (Verified)</option>
                            <option value="accepted" {{ $registration->status->value === 'accepted' ? 'selected' : '' }}>Diterima (Accepted)</option>
                            <option value="rejected" {{ $registration->status->value === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Catatan Panitia Seleksi
                        </label>
                        <textarea name="admin_notes" rows="4"
                            placeholder="Catatan ini dapat dilihat oleh orang tua / wali murid saat memeriksa status seleksi..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-600 focus:border-transparent">{{ old('admin_notes', $registration->admin_notes) }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow transition flex items-center justify-center gap-2">
                        <span>Simpan Perubahan Status</span>
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </form>

                <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-400 space-y-1">
                    <p>Status terakhir diperbarui:</p>
                    <p class="font-mono text-slate-600">{{ $registration->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>

            <!-- Enrollment Card for Accepted Students -->
            @if($registration->status->value === 'accepted')
                <div class="bg-gradient-to-br from-emerald-900 to-emerald-950 text-white rounded-3xl shadow-lg p-6 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-gold-400 animate-ping"></span>
                        <h4 class="text-sm font-extrabold uppercase tracking-wider text-gold-300">Pendaftaran Ulang Siswa</h4>
                    </div>
                    <p class="text-xs text-emerald-100">
                        Calon siswa telah <strong>Diterima</strong>. Daftarkan langsung ke rombel dan aktifkan akun portal wali murid secara otomatis.
                    </p>

                    @if($isAlreadyEnrolled)
                        <div class="p-3 bg-white/10 rounded-xl text-center text-xs font-bold text-gold-300 border border-gold-400/30">
                            ✓ Siswa ini telah terdaftar aktif di sistem.
                        </div>
                    @else
                        <form action="{{ route('admin.ppdb.enroll', $registration) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-emerald-200 mb-1">Pilih Kelas / Rombel *</label>
                                <select name="classroom_id" required class="w-full px-3 py-2 rounded-xl border border-white/20 text-xs font-semibold bg-white text-slate-900">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($classrooms as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} (Tingkat {{ $c->grade_level }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                                <span>Daftarkan sebagai Siswa Aktif →</span>
                            </button>
                        </form>
                    @endif
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
