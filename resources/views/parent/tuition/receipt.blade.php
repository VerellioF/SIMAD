<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kwitansi Pembayaran - {{ $bill->payment->receipt_number }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Zero-node standalone runtime) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans p-4 sm:p-8 text-slate-800">

    <div class="max-w-xl mx-auto bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-200">
        
        <!-- Action Toolbar -->
        <div class="no-print flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
            <a href="{{ route('parent.tuition.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                ← Kembali ke Daftar Tagihan
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-emerald-900 text-white font-bold text-xs shadow flex items-center gap-1.5 hover:bg-emerald-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Kwitansi</span>
            </button>
        </div>

        <!-- School Kop -->
        <div class="text-center pb-6 border-b-2 border-emerald-900 space-y-1">
            <h2 class="text-lg font-extrabold text-emerald-950 uppercase tracking-wide">
                MI PLUS RAUDLATUL MUTA'ALLIMIN
            </h2>
            <p class="text-xs text-slate-500">
                Kalipucung, Sanankulon, Kabupaten Blitar • NSM: {{ config('simad.nsm') }} • NPSN: {{ config('simad.npsn') }}
            </p>
        </div>

        <div class="text-center my-6 space-y-1">
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 text-xs font-bold uppercase tracking-wider">
                Bukti Pembayaran Sah (Lunas)
            </span>
            <h1 class="text-xl font-extrabold text-slate-900 font-mono tracking-wider pt-1">
                {{ $bill->payment->receipt_number }}
            </h1>
        </div>

        <!-- Details -->
        <div class="space-y-4 text-xs border-y border-slate-100 py-6">
            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-400">Telah Terima Dari:</span>
                <span class="font-bold text-slate-900">{{ $bill->student->parent->name ?? 'Wali Santri' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-400">Untuk Pembayaran Santri:</span>
                <span class="font-bold text-slate-900">{{ $bill->student->full_name }} (NISN: {{ $bill->student->nisn }})</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-400">Kelas / Rombel:</span>
                <span class="font-bold text-slate-900">{{ $bill->student->classroom->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-400">Uraian Pembayaran:</span>
                <span class="font-bold text-slate-900">{{ $bill->bill_title }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-400">Metode Bayar:</span>
                <span class="font-bold text-slate-900 uppercase">{{ str_replace('_', ' ', $bill->payment->payment_method->value ?? 'transfer_bank') }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-400">Tanggal Transaksi:</span>
                <span class="font-mono text-slate-900">{{ $bill->payment->payment_date->format('d F Y') }}</span>
            </div>
            <div class="flex justify-between items-center pt-2">
                <span class="text-sm font-bold text-slate-900 uppercase">Jumlah Terbayar:</span>
                <span class="text-xl font-extrabold font-mono text-emerald-800">
                    Rp {{ number_format((float)$bill->payment->amount_paid, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="pt-8 flex justify-between items-end text-xs">
            <div class="space-y-1 text-slate-400 text-[10px]">
                <p>Status: LUNAS & DIVERIFIKASI</p>
                <p>SIMAD Online Payment System</p>
            </div>
            <div class="text-center space-y-1">
                <span class="text-slate-500 block">Blitar, {{ now()->format('d F Y') }}</span>
                <span class="text-slate-400 block text-[10px]">Bendahara / Tata Usaha</span>
                <div class="h-12 flex items-center justify-center">
                    <span class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold font-mono text-[10px]">TERVERIFIKASI SISTEM</span>
                </div>
                <span class="font-bold text-slate-900 block underline">Staf Administrasi TU</span>
            </div>
        </div>

    </div>

</body>
</html>
