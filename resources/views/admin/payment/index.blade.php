@extends('layouts.portal')

@section('title', 'Transaksi Pembayaran Online (Gateway)')

@section('content')
<div class="space-y-6">

    <!-- Header & Gateway Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Transaksi Pembayaran Online</h1>
            <p class="text-xs text-slate-500 mt-0.5">Audit log dan rekonsiliasi pembayaran otomatis via QRIS & Payment Gateway</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Gateway: {{ strtoupper($currentProvider) }} ({{ $isSandbox ? 'Sandbox' : 'Production' }})</span>
            </span>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-[11px] uppercase font-bold text-slate-400 block tracking-wider">Total Transaksi</span>
            <span class="text-2xl font-extrabold font-mono text-slate-900">{{ number_format($stats['total_count']) }}</span>
            <span class="text-[10px] text-slate-400 block">Seluruh riwayat pembayaran</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-[11px] uppercase font-bold text-emerald-600 block tracking-wider">Lunas Terverifikasi</span>
            <span class="text-2xl font-extrabold font-mono text-emerald-800">{{ number_format($stats['paid_count']) }}</span>
            <span class="text-[10px] text-emerald-600/80 block">Otomatis via Webhook</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-[11px] uppercase font-bold text-amber-600 block tracking-wider">Menunggu Pembayaran</span>
            <span class="text-2xl font-extrabold font-mono text-amber-800">{{ number_format($stats['pending_count']) }}</span>
            <span class="text-[10px] text-amber-600/80 block">QR aktif / belum discan</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-[11px] uppercase font-bold text-slate-400 block tracking-wider">Total Dana Masuk</span>
            <span class="text-2xl font-extrabold font-mono text-emerald-900">
                Rp {{ number_format((float)$stats['paid_volume'], 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-slate-400 block">Masuk ke kas madrasah</span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <!-- Status Filter -->
            <a href="{{ route('admin.payment.index', array_merge(request()->query(), ['status' => null])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Status
            </a>
            <a href="{{ route('admin.payment.index', array_merge(request()->query(), ['status' => 'paid'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'paid' ? 'bg-emerald-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Lunas
            </a>
            <a href="{{ route('admin.payment.index', array_merge(request()->query(), ['status' => 'pending'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'pending' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Menunggu
            </a>
            <a href="{{ route('admin.payment.index', array_merge(request()->query(), ['status' => 'expired'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'expired' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Kadaluarsa
            </a>

            <!-- Type Divider -->
            <span class="text-slate-300 hidden sm:inline">|</span>

            <a href="{{ route('admin.payment.index', array_merge(request()->query(), ['type' => 'spp'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('type') === 'spp' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                SPP Saja
            </a>
            <a href="{{ route('admin.payment.index', array_merge(request()->query(), ['type' => 'ppdb'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('type') === 'ppdb' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                PPDB Saja
            </a>
        </div>

        <form method="GET" action="{{ route('admin.payment.index') }}" class="flex items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari ref / nama / no HP..."
                   class="px-3.5 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-600 w-full sm:w-60">
            <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">No. Referensi</th>
                        <th class="py-3.5 px-6">Pembayar / Wali</th>
                        <th class="py-3.5 px-6">Tipe & Uraian</th>
                        <th class="py-3.5 px-6">Nominal Pokok</th>
                        <th class="py-3.5 px-6">Metode</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">
                                <a href="{{ route('payment.checkout', $trx->reference) }}" target="_blank"
                                   class="text-emerald-700 hover:underline inline-flex items-center gap-1" title="Lihat Halaman QRIS">
                                    <span>{{ $trx->reference }}</span>
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                <span class="block text-[10px] text-slate-400 font-sans mt-0.5">
                                    {{ $trx->created_at->format('d/m/Y H:i') }} WIB
                                </span>
                            </td>

                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $trx->payer_name }}</div>
                                <div class="font-mono text-slate-500 text-[11px]">{{ $trx->payer_phone }}</div>
                            </td>

                            <td class="py-4 px-6 text-slate-700">
                                @if($trx->payable instanceof \App\Models\TuitionBill)
                                    <span class="inline-block px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold uppercase mb-0.5">
                                        SPP Madrasah
                                    </span>
                                    <div class="text-xs text-slate-900 font-medium">{{ $trx->payable->bill_title }}</div>
                                    <div class="text-[10px] text-slate-500">Siswa: {{ $trx->payable->student->full_name }}</div>
                                @elseif($trx->payable instanceof \App\Models\PpdbRegistration)
                                    <span class="inline-block px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-[10px] font-bold uppercase mb-0.5">
                                        PPDB Online
                                    </span>
                                    <div class="text-xs text-slate-900 font-medium">Calon Siswa: {{ $trx->payable->child_name }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $trx->payable->registration_number }}</div>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>

                            <td class="py-4 px-6 font-mono">
                                <span class="font-bold text-slate-900 text-sm">{{ $trx->formattedAmount() }}</span>
                                @if($trx->fee_amount > 0)
                                    <span class="block text-[10px] text-slate-400">+ Fee: {{ $trx->formattedFee() }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-6">
                                <span class="uppercase font-mono font-bold text-xs text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg">
                                    {{ strtoupper($trx->payment_method) }}
                                </span>
                                <span class="block text-[10px] text-slate-400 mt-1 capitalize">{{ $trx->payment_provider }}</span>
                            </td>

                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $trx->statusBadge() }}">
                                    {{ $trx->statusLabel() }}
                                </span>
                                @if($trx->isPaid() && $trx->paid_at)
                                    <span class="block text-[10px] text-emerald-700 mt-1 font-mono">
                                        {{ $trx->paid_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-right">
                                @if($trx->isPending() && $isSandbox)
                                    <form action="{{ route('admin.payment.simulate', $trx) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Simulasikan pembayaran lunas untuk referensi {{ $trx->reference }}?')"
                                                class="px-3 py-1 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] shadow transition">
                                            ⚡ Simulasikan Lunas
                                        </button>
                                    </form>
                                @elseif($trx->isPaid())
                                    <span class="text-xs text-emerald-700 font-bold inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Terverifikasi</span>
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Belum ada riwayat transaksi pembayaran online tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
