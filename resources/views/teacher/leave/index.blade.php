@extends('layouts.portal')

@section('title', 'Validasi Izin Siswa')

@section('content')
<div class="space-y-6" x-data="{ rejectModal: false, activeAction: '', activeStudent: '', rejectNotes: '' }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Validasi Surat Izin Siswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelas: <strong>{{ $classroom->name ?? 'Belum Ada Rombel' }}</strong> • Tinjau pengajuan surat izin dan sakit dari wali murid
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            @if(session('wa_direct_url'))
                <a href="{{ session('wa_direct_url') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition shrink-0">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    <span>Kirim Konfirmasi WA ke Wali {{ session('wa_student_name') }} &rarr;</span>
                </a>
            @endif
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Nama Siswa</th>
                        <th class="py-3.5 px-6">Alasan</th>
                        <th class="py-3.5 px-6">Rentang Tanggal</th>
                        <th class="py-3.5 px-6">Keterangan & Lampiran</th>
                        <th class="py-3.5 px-6">Status Validasi</th>
                        <th class="py-3.5 px-6 text-right">Tindakan Guru</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leaveRequests as $req)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $req->student->full_name }}</span>
                                <span class="text-[10px] text-slate-400">Wali: {{ $req->parent->name }} ({{ $req->parent->phone_number }})</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $req->reason_type->value === 'sakit' ? 'bg-blue-50 text-blue-800 border border-blue-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                    {{ $req->reason_type->value === 'sakit' ? 'Sakit' : 'Izin' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-700">
                                {{ $req->start_date->format('d/m/Y') }} s/d {{ $req->end_date->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6 max-w-xs">
                                <p class="text-slate-700 line-clamp-2">{{ $req->description }}</p>
                                @if($req->attachment_path)
                                    <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank"
                                       class="text-[11px] text-emerald-700 hover:underline inline-flex items-center gap-1 mt-1 font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span>Lihat Lampiran Surat / Dokter</span>
                                    </a>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $badge = match($req->status->value) {
                                        'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                        default => 'bg-amber-100 text-amber-800 border-amber-200',
                                    };
                                    $label = match($req->status->value) {
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default => 'Menunggu',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $badge }}">
                                    {{ $label }}
                                </span>
                                @if($req->review_notes)
                                    <span class="block text-[10px] text-slate-400 mt-1">Catatan: {{ $req->review_notes }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($req->status->value === 'pending')
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form action="{{ route('teacher.leave.update', $req) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs transition shadow-sm">
                                                Setujui
                                            </button>
                                        </form>

                                        <button type="button"
                                                @click="rejectModal = true; activeAction = '{{ route('teacher.leave.update', $req) }}'; activeStudent = '{{ $req->student->full_name }}'; rejectNotes = ''"
                                                class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs border border-red-200 transition">
                                            Tolak...
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400">Selesai Diverifikasi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-1.5">
                                    <span class="text-3xl block">📋</span>
                                    <h4 class="text-xs font-extrabold text-slate-800">Tidak Ada Pengajuan Izin</h4>
                                    <p class="text-[11px] text-slate-400">Seluruh siswa di rombel aktif dan tidak ada permohonan izin/sakit yang menunggu validasi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tolak Izin Siswa -->
    <div x-show="rejectModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="rejectModal = false"
             class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-150">
            <div class="flex items-start gap-3 border-b border-slate-100 pb-3">
                <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center font-black text-lg shrink-0">
                    ✕
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Tolak Pengajuan Izin</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Siswa: <strong x-text="activeStudent"></strong></p>
                </div>
            </div>

            <form :action="activeAction" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="status" value="rejected">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Alasan / Catatan Guru untuk Wali Murid <span class="text-slate-400 font-normal">(opsional)</span>:
                    </label>
                    <textarea name="review_notes" x-model="rejectNotes" rows="3"
                              placeholder="Misal: Mohon lampirkan surat dokter asli, atau hari ini ada jadwal ujian penting..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-red-600 focus:outline-none"></textarea>
                    <p class="text-[10px] text-slate-400 mt-1">Catatan ini akan tersimpan dan dapat dibaca oleh wali murid pada portal.</p>
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="rejectModal = false"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow transition">
                        Konfirmasi Tolak Izin
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
