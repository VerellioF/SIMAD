@extends('layouts.portal')

@section('title', 'Validasi Izin Siswa')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Validasi Surat Izin Siswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelas: <strong>{{ $classroom->name ?? 'Belum Ada Rombel' }}</strong> • Tinjau pengajuan surat izin dan sakit dari wali murid
            </p>
        </div>
    </div>

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
                                    <div class="flex items-center justify-end gap-1.5" x-data="{ openNotes: false }">
                                        <form action="{{ route('teacher.leave.update', $req) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs transition">
                                                Setujui
                                            </button>
                                        </form>

                                        <form action="{{ route('teacher.leave.update', $req) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs border border-red-200 transition">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400">Selesai Diverifikasi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Belum ada pengajuan surat izin dari wali murid.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
