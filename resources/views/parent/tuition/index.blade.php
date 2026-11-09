@extends('layouts.portal')

@section('title', 'Tagihan SPP & Infaq')

@section('content')
<div class="space-y-6" x-data="{
    activeBill: null,
    showConfirmModal: false,
    openModal(bill) {
        this.activeBill = bill;
        this.showConfirmModal = true;
    }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tagihan SPP & Infaq Madrasah</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar tagihan bulanan dan konfirmasi pembayaran ananda</p>
        </div>
    </div>

    <!-- Bank Info & Outstanding Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Rekening BSI Madrasah -->
        <div class="lg:col-span-7 bg-gradient-to-r from-emerald-950 via-emerald-900 to-emerald-950 p-6 sm:p-8 rounded-3xl text-white border border-gold-500/40 shadow-lg space-y-3"
             x-data="{ copied: false }">
            <div class="flex items-center justify-between">
                <span class="inline-block px-3 py-1 rounded-full bg-gold-500 text-emerald-950 text-xs font-bold uppercase tracking-wider">
                    Rekening Resmi Madrasah
                </span>
                <button type="button"
                        @click="navigator.clipboard.writeText('7123456789'); copied = true; setTimeout(() => copied = false, 2500)"
                        class="px-3 py-1 rounded-lg bg-emerald-800/80 hover:bg-emerald-700 text-gold-300 font-bold text-xs transition flex items-center gap-1.5 border border-emerald-700">
                    <span x-text="copied ? '✓ Tersalin!' : '📋 Salin No. Rekening'"></span>
                </button>
            </div>
            <div class="space-y-1">
                <span class="text-xs text-emerald-300">Bank Syariah Indonesia (BSI)</span>
                <p class="text-2xl sm:text-3xl font-mono font-extrabold text-white tracking-widest">7123456789</p>
                <p class="text-xs text-emerald-200">a.n. YAYASAN RAUDLATUL MUTA'ALLIMIN</p>
            </div>
            <p class="text-[11px] text-emerald-300/80 pt-1">
                Setelah transfer ATM / Mobile Banking, silakan upload bukti transfer pada tagihan terkait di bawah ini atau bayar langsung via QRIS/VA otomatis.
            </p>
        </div>

        <!-- Total Tunggakan Summary -->
        <div class="lg:col-span-5 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-4">
            <div>
                <span class="text-xs uppercase font-bold text-slate-400 block">Total Tagihan Belum Lunas</span>
                <span class="text-3xl font-extrabold font-mono block mt-1 {{ $unpaidTotal > 0 ? 'text-red-600' : 'text-emerald-700' }}">
                    Rp {{ number_format((float)$unpaidTotal, 0, ',', '.') }}
                </span>
                <span class="text-xs text-slate-500 block mt-1">
                    {{ $unpaidTotal > 0 ? 'Mohon selesaikan sebelum jatuh tempo' : 'Alhamdulillah, seluruh tagihan lunas' }}
                </span>
            </div>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('simad.contact_phone')) }}?text=Assalamu'alaikum%20Admin%20TU,%20saya%20ingin%20konsultasi%20pembayaran%20SPP"
               target="_blank" class="w-full text-center py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Konsultasi Pembayaran via WA →
            </a>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200" @click.away="showConfirmModal = false">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Konfirmasi Pembayaran Transfer</h3>
                <p class="text-xs text-slate-500" x-text="activeBill ? 'Tagihan: ' + activeBill.bill_title : ''"></p>
            </div>

            <form :action="'/wali/spp/' + (activeBill ? activeBill.id : '') + '/konfirmasi'" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Nama Pemilik Rekening Pengirim <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bank_sender_name" required placeholder="Contoh: Ahmad Dahlan"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Tanggal Transfer <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Unggah Bukti Struk / Screenshot Transfer <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="proof_attachment" required accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-900 hover:file:bg-amber-200">
                    <span class="text-[10px] text-slate-400 block mt-1">Format: JPG, PNG, atau PDF (maksimal 2MB).</span>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="showConfirmModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow transition">
                        Kirim Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bills Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Riwayat Tagihan & Pembayaran SPP</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Nama Ananda</th>
                        <th class="py-3.5 px-6">Uraian Tagihan</th>
                        <th class="py-3.5 px-6">Nominal</th>
                        <th class="py-3.5 px-6">Jatuh Tempo</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bills as $bill)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $bill->student->full_name }}
                            </td>
                            <td class="py-4 px-6 text-slate-800 font-medium">
                                {{ $bill->bill_title }}
                            </td>
                            <td class="py-4 px-6 font-mono font-bold text-slate-900 text-sm">
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
                                        'pending_verification' => 'Menunggu Verifikasi TU',
                                        default => 'Belum Lunas',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $badge }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($bill->status->value === 'unpaid')
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('parent.tuition.pay_online', $bill) }}"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                            <span>Bayar QRIS</span>
                                        </a>
                                        <button type="button" @click="openModal({{ json_encode($bill) }})"
                                                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
                                                title="Transfer Manual ATM/Bank & Unggah Struk">
                                            Transfer Manual
                                        </button>
                                    </div>
                                @elseif($bill->status->value === 'pending_verification')
                                    <span class="text-xs text-amber-700 font-semibold">Sedang Ditinjau TU</span>
                                @else
                                    <a href="{{ route('parent.tuition.receipt', $bill) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-bold text-xs transition border border-emerald-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Kwitansi Sah</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Belum ada daftar tagihan SPP untuk ananda.
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
