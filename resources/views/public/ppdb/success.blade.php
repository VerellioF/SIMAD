@extends('layouts.public')

@section('title', 'Pendaftaran Berhasil')

@section('content')
<div class="bg-slate-50 py-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-8 sm:p-12 text-center space-y-6">
            
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto text-emerald-800 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>

            <div class="space-y-2">
                <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 text-xs font-bold uppercase tracking-wider">
                    Registrasi Diterima
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-emerald-950">
                    Pendaftaran Berhasil Dikirim!
                </h1>
                <p class="text-slate-600 text-sm max-w-md mx-auto">
                    Terima kasih, data calon santri atas nama <strong>{{ $registration->child_name }}</strong> telah tersimpan di sistem SIMAD MI Plus Raudlatul Muta'allimin.
                </p>
            </div>

            <!-- Registration Number Card -->
            <div class="bg-gradient-to-r from-emerald-950 to-emerald-900 rounded-2xl p-6 text-white text-center border border-gold-500/40 shadow-md">
                <span class="text-xs uppercase tracking-wider text-emerald-300 font-medium">Nomor Registrasi Resmi</span>
                <div class="text-2xl sm:text-3xl font-extrabold font-mono text-gold-400 tracking-wider my-1">
                    {{ $registration->registration_number }}
                </div>
                <p class="text-[11px] text-emerald-200/80">Simpan nomor registrasi ini untuk melacak status seleksi atau mengunduh ulang bukti pendaftaran.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a href="/ppdb/cetak/{{ $registration->registration_number }}" target="_blank"
                    class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-emerald-950 font-bold text-sm shadow-md transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Bukti Pendaftaran (PDF)</span>
                </a>

                <a href="/ppdb/cek-status?q={{ $registration->registration_number }}"
                    class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-sm transition flex items-center justify-center gap-2">
                    <span>Pantau Status Seleksi</span>
                </a>
            </div>

            <!-- Information Callout -->
            <div class="text-left bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs text-slate-600 space-y-1.5">
                <p class="font-bold text-slate-800">Petunjuk Selanjutnya:</p>
                <p>1. Panitia seleksi PPDB madrasah akan memverifikasi berkas Anda dalam 1-3 hari kerja.</p>
                <p>2. Konfirmasi jadwal observasi/wawancara santri akan dikirimkan melalui WhatsApp ke nomor <strong>{{ $registration->parent_whatsapp }}</strong>.</p>
            </div>

        </div>

    </div>
</div>
@endsection
