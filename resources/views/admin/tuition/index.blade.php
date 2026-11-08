@extends('layouts.portal')

@section('title', 'Tata Kelola SPP & Keuangan')

@section('content')
<div class="space-y-6" x-data="{ showGenerateModal: false }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tata Kelola SPP & Kas Madrasah</h1>
            <p class="text-xs text-slate-500 mt-0.5">Penerbitan tagihan bulanan, pencatatan kas, dan verifikasi transfer wali santri</p>
        </div>

        <button type="button" @click="showGenerateModal = true"
                class="px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow flex items-center justify-center gap-2 transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Terbitkan Tagihan Bulanan</span>
        </button>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Tagihan</span>
            <span class="text-3xl font-extrabold text-slate-900 font-mono block">{{ $stats['total_bills'] }}</span>
            <span class="text-[11px] text-slate-500">Lembar tagihan terbit</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-700 block">Menunggu Verifikasi</span>
            <span class="text-3xl font-extrabold text-amber-600 font-mono block">{{ $stats['pending_count'] }}</span>
            <span class="text-[11px] text-amber-600/80">Bukti transfer masuk</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-red-600 block">Tunggakan Belum Lunas</span>
            <span class="text-xl sm:text-2xl font-extrabold text-red-600 font-mono block">
                Rp {{ number_format((float)$stats['unpaid_sum'], 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-slate-400">{{ $stats['unpaid_count'] }} santri belum bayar</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">Kas Masuk (Lunas)</span>
            <span class="text-xl sm:text-2xl font-extrabold text-emerald-800 font-mono block">
                Rp {{ number_format((float)$stats['paid_sum'], 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-slate-400">{{ $stats['paid_count'] }} tagihan terbayar</span>
        </div>
    </div>

    <!-- Modal Generate Tagihan Bulanan -->
    <div x-show="showGenerateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Terbitkan Tagihan SPP Massal</h3>
                <p class="text-xs text-slate-500">Sistem akan otomatis membuat tagihan untuk semua santri aktif</p>
            </div>

            <form action="{{ route('admin.tuition.generate') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Judul Tagihan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bill_title" value="SPP Bulan {{ date('F Y') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Bulan (1 - 12) <span class="text-red-500">*</span>
                        </label>
                        <select name="month" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-white">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                    Bulan {{ $m }} ({{ DateTime::createFromFormat('!m', (string)$m)->format('F') }})
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Tahun <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="year" value="{{ date('Y') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Nominal SPP (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="amount" value="100000" step="5000" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Jatuh Tempo <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="due_date" value="{{ date('Y-m-10', strtotime('+1 month')) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="showGenerateModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow transition">
                        Terbitkan Tagihan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.tuition.index') }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ empty($status) ? 'bg-emerald-900 text-white border-emerald-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Semua Tagihan ({{ $stats['total_bills'] }})
        </a>
        <a href="{{ route('admin.tuition.index', ['status' => 'pending_verification']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ $status === 'pending_verification' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-amber-700 border-slate-200 hover:bg-amber-50' }}">
            Perlu Verifikasi ({{ $stats['pending_count'] }})
        </a>
        <a href="{{ route('admin.tuition.index', ['status' => 'unpaid']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ $status === 'unpaid' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-red-700 border-slate-200 hover:bg-red-50' }}">
            Belum Lunas ({{ $stats['unpaid_count'] }})
        </a>
        <a href="{{ route('admin.tuition.index', ['status' => 'paid']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ $status === 'paid' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-emerald-700 border-slate-200 hover:bg-emerald-50' }}">
            Lunas ({{ $stats['paid_count'] }})
        </a>
    </div>

    <!-- Table of Bills -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Nama Santri / Rombel</th>
                        <th class="py-3.5 px-6">Uraian Tagihan</th>
                        <th class="py-3.5 px-6">Nominal</th>
                        <th class="py-3.5 px-6">Jatuh Tempo</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi Bendahara TU</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bills as $bill)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $bill->student->full_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $bill->student->classroom->name ?? 'Tanpa Rombel' }} • NISN: {{ $bill->student->nisn }}</span>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-800">
                                {{ $bill->bill_title }}
                            </td>
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">
                                Rp {{ number_format((float)$bill->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-500">
                                {{ $bill->due_date->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $badge = match($bill->status->value) {
                                        'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'pending_verification' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        default => 'bg-red-100 text-red-800 border-red-200',
                                    };
                                    $label = match($bill->status->value) {
                                        'paid' => 'Lunas',
                                        'pending_verification' => 'Verifikasi Bukti',
                                        default => 'Belum Lunas',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $badge }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($bill->status->value === 'pending_verification')
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($bill->payment?->proof_attachment)
                                            <a href="{{ asset('storage/' . $bill->payment->proof_attachment) }}" target="_blank"
                                               class="p-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition font-bold" title="Lihat Bukti Transfer">
                                                Lihat Bukti ↗
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.tuition.verify', $bill) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs transition">
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.tuition.verify', $bill) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 font-bold text-xs transition">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @elseif($bill->status->value === 'unpaid')
                                    <form action="{{ route('admin.tuition.cash', $bill) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                                            Bayar Tunai di Loket
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-mono font-bold text-emerald-800">
                                        {{ $bill->payment?->receipt_number ?? 'Lunas' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Belum ada data tagihan SPP.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bills->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $bills->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
