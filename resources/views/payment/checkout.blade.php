@extends('layouts.public')

@section('title', 'Pembayaran Online QRIS - SIMAD')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 sm:py-16" x-data="{
    status: '{{ $transaction->status }}',
    isPaid: {{ $transaction->isPaid() ? 'true' : 'false' }},
    redirectUrl: null,
    pollInterval: null,
    checkStatus() {
        if (this.isPaid) return;
        fetch('{{ route('payment.status', $transaction->reference) }}')
            .then(res => res.json())
            .then(data => {
                this.status = data.status;
                if (data.is_paid) {
                    this.isPaid = true;
                    this.redirectUrl = data.redirect_url;
                    clearInterval(this.pollInterval);
                }
            })
            .catch(() => {});
    },
    init() {
        if (!this.isPaid) {
            this.pollInterval = setInterval(() => this.checkStatus(), 4000);
        }
    }
}">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top Breadcrumb / Navigation -->
        <div class="flex items-center justify-between text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <a href="{{ url('/') }}" class="hover:text-emerald-800 font-medium">Beranda</a>
                <span>/</span>
                <span class="text-slate-700 font-semibold">Pembayaran Digital QRIS</span>
            </div>
            <span class="font-mono bg-slate-200/80 px-2.5 py-1 rounded-lg text-slate-700 font-bold">
                REF: {{ $transaction->reference }}
            </span>
        </div>

        @if($isSandbox)
            <!-- Sandbox Alert Banner -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-sm shrink-0">
                        ⚡
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-900">Mode Sandbox / Uji Coba Payment Gateway Aktif</h4>
                        <p class="text-[11px] text-amber-700">Transaksi ini terhubung ke simulator gateway. Anda dapat menguji proses pembayaran otomatis tanpa uang sungguhan.</p>
                    </div>
                </div>
                <form action="{{ route('payment.simulate', $transaction->reference) }}" method="POST" class="shrink-0 w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simulasikan Bayar Berhasil</span>
                    </button>
                </form>
            </div>
        @endif

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
            
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-emerald-950 via-emerald-900 to-emerald-950 p-6 sm:p-8 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-gold-500 text-emerald-950 text-[10px] font-extrabold uppercase tracking-wider">
                            Resmi Bank Indonesia
                        </span>
                        <span class="text-xs text-emerald-300">QRIS Standar Nasional</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
                        Pembayaran Biaya Pendidikan
                    </h1>
                    <p class="text-xs text-emerald-200">MI Plus Raudlatul Muta'allimin Kalipucung</p>
                </div>

                <div class="sm:text-right bg-white/10 px-4 py-2.5 rounded-2xl backdrop-blur-sm border border-white/10">
                    <span class="text-[10px] uppercase font-bold text-emerald-300 block">Total Tagihan</span>
                    <span class="text-2xl sm:text-3xl font-mono font-black text-gold-400">
                        {{ $transaction->formattedTotal() }}
                    </span>
                </div>
            </div>

            <!-- Dynamic Status Banner (When Paid) -->
            <div x-show="isPaid" x-cloak class="p-6 bg-emerald-50 border-b border-emerald-200 text-center space-y-3">
                <div class="w-14 h-14 bg-emerald-600 text-white rounded-full flex items-center justify-center mx-auto shadow-md">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-emerald-950">Alhamdulillah! Pembayaran Telah Diterima</h3>
                    <p class="text-xs text-emerald-700 mt-0.5">Sistem telah memverifikasi pembayaran secara otomatis. Notifikasi konfirmasi dan kwitansi telah dicatat.</p>
                </div>
                <div class="pt-2 flex justify-center gap-3">
                    <template x-if="redirectUrl">
                        <a :href="redirectUrl" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow transition inline-flex items-center gap-2">
                            <span>Buka Bukti / Kwitansi Sah →</span>
                        </a>
                    </template>
                    @if($transaction->payable instanceof \App\Models\TuitionBill)
                        <a href="{{ route('parent.tuition.receipt', $transaction->payable_id) }}" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow transition inline-flex items-center gap-2">
                            <span>Lihat Kwitansi SPP →</span>
                        </a>
                    @elseif($transaction->payable instanceof \App\Models\PpdbRegistration)
                        <a href="{{ route('ppdb.check-status', ['q' => $transaction->payable->registration_number]) }}" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow transition inline-flex items-center gap-2">
                            <span>Cek Status PPDB →</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-12 gap-8 items-start" x-show="!isPaid">
                
                <!-- QRIS Box Section -->
                <div class="md:col-span-6 flex flex-col items-center justify-center space-y-4 bg-slate-50 p-6 rounded-3xl border border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-slate-800 text-sm tracking-widest font-mono">QRIS</span>
                        <span class="text-[10px] text-slate-400">Pembayaran Cepat</span>
                    </div>

                    <!-- QR Code Frame -->
                    <div class="bg-white p-4 rounded-2xl shadow-md border-2 border-slate-200/80 relative group">
                        <img src="{{ $transaction->qr_url ?? 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=' . rawurlencode($transaction->qr_string ?? $transaction->reference) }}"
                             alt="QRIS Pembayaran SIMAD"
                             class="w-60 h-60 object-contain mx-auto rounded-lg">
                        
                        <div class="absolute inset-0 bg-white/90 backdrop-blur-sm rounded-2xl flex flex-col items-center justify-center p-4 text-center opacity-0 group-hover:opacity-100 transition duration-200">
                            <span class="text-xs font-bold text-slate-800">Scan Menggunakan:</span>
                            <span class="text-[11px] text-slate-600 mt-1">BSI Mobile, BCA, BRImo, Mandiri Livin', GoPay, OVO, ShopeePay, DANA</span>
                        </div>
                    </div>

                    <div class="text-center space-y-1">
                        <span class="text-[11px] text-slate-500 block">NMID: ID1020030040050</span>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Menunggu Scan dari m-Banking / E-Wallet</span>
                        </div>
                    </div>

                    <!-- Expiry countdown notice -->
                    <div class="text-[11px] text-slate-400 text-center">
                        Berlaku hingga: <strong class="text-slate-700">{{ $transaction->expired_at?->format('d/m/Y H:i') ?? '24 Jam' }} WIB</strong>
                    </div>
                </div>

                <!-- Transaction Details & Instructions -->
                <div class="md:col-span-6 space-y-6">
                    
                    <!-- Breakdown Tagihan -->
                    <div class="space-y-3">
                        <h3 class="text-xs uppercase font-extrabold tracking-wider text-slate-400">
                            Rincian Pembayaran
                        </h3>

                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-2.5 text-xs">
                            <div class="flex justify-between pb-2 border-b border-slate-200/60">
                                <span class="text-slate-500">Uraian Tagihan:</span>
                                <span class="font-bold text-slate-800 text-right">
                                    @if($transaction->payable instanceof \App\Models\TuitionBill)
                                        {{ $transaction->payable->bill_title }}
                                    @elseif($transaction->payable instanceof \App\Models\PpdbRegistration)
                                        Formulir PPDB ({{ $transaction->payable->child_name }})
                                    @else
                                        Pembayaran Pendidikan
                                    @endif
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-slate-500">Atas Nama:</span>
                                <span class="font-bold text-slate-800">{{ $transaction->payer_name }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-slate-500">Nomor WhatsApp:</span>
                                <span class="font-mono text-slate-700">{{ $transaction->payer_phone }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-slate-500">Nominal Pokok:</span>
                                <span class="font-mono text-slate-800">{{ $transaction->formattedAmount() }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-slate-500">Biaya Layanan Gateway:</span>
                                <span class="font-mono {{ $transaction->fee_amount > 0 ? 'text-slate-800' : 'text-emerald-700 font-bold' }}">
                                    {{ $transaction->fee_amount > 0 ? $transaction->formattedFee() : 'Gratis (Disubsidi Madrasah)' }}
                                </span>
                            </div>

                            <div class="flex justify-between pt-2 border-t border-slate-200 text-sm font-extrabold text-slate-900">
                                <span>Total Tagihan:</span>
                                <span class="font-mono text-emerald-800">{{ $transaction->formattedTotal() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Langkah Pembayaran -->
                    <div class="space-y-3">
                        <h3 class="text-xs uppercase font-extrabold tracking-wider text-slate-400">
                            Panduan Mudah Pembayaran (3 Langkah)
                        </h3>

                        <ol class="space-y-2.5 text-xs text-slate-600">
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                                <div>
                                    <strong>Buka Aplikasi m-Banking / E-Wallet</strong> pilihan Anda (BSI Mobile, BRImo, Livin Mandiri, BCA Mobile, GoPay, OVO, ShopeePay, DANA).
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                                <div>
                                    Pilih menu <strong>Scan QRIS / Bayar</strong>, lalu arahkan kamera ke barcode di samping.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                                <div>
                                    Periksa nama tujuan <strong>MI Plus Raudlatul Muta'allimin</strong> dan nominal pembayaran, lalu masukkan PIN Anda. Selesai!
                                </div>
                            </li>
                        </ol>
                    </div>

                    <!-- Auto-Detection Callout -->
                    <div class="p-3.5 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl text-[11px] text-emerald-900 flex items-start gap-2">
                        <span class="text-base">✨</span>
                        <p>
                            <strong>Verifikasi Otomatis Detik Itu Juga:</strong> Anda tidak perlu repot mengirim atau mengunggah bukti struk. Halaman ini akan mendeteksi pembayaran secara otomatis dan kwitansi sah langsung diterbitkan.
                        </p>
                    </div>

                </div>

            </div>

            <!-- Card Footer -->
            <div class="bg-slate-50 px-6 sm:px-8 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Transaksi dijamin aman dengan enkripsi standar Bank Indonesia & PCI-DSS.
                </span>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('simad.contact_phone')) }}?text=Assalamu'alaikum%20Admin%20TU,%20saya%20butuh%20bantuan%20pembayaran%20online%20dengan%20ref%20{{ $transaction->reference }}"
                   target="_blank" class="hover:text-emerald-800 font-semibold underline">
                    Butuh Bantuan? Hubungi TU Madrasah
                </a>
            </div>

        </div>

    </div>
</div>
@endsection
