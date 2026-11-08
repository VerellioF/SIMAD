@extends('layouts.portal')

@section('title', 'Pengajuan Izin Siswa')

@section('content')
<div class="space-y-6" x-data="{ showForm: false }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Surat Izin & Sakit Siswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">Ajukan surat izin ketidakhadiran ananda langsung ke wali kelas</p>
        </div>

        <button type="button" @click="showForm = !showForm"
                class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs sm:text-sm shadow flex items-center justify-center gap-2 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span x-text="showForm ? 'Tutup Formulir' : 'Buat Pengajuan Izin Baru'"></span>
        </button>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Submission Accordion/Card -->
    <div x-show="showForm" x-cloak class="bg-white rounded-3xl border border-amber-200 shadow-md p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Formulir Pengajuan Izin / Sakit</h3>
            <p class="text-xs text-slate-500">Pemberitahuan akan langsung masuk ke portal wali kelas yang bersangkutan</p>
        </div>

        <form action="{{ route('parent.leave.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Pilih Ananda <span class="text-red-500">*</span>
                    </label>
                    <select name="student_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-white">
                        @foreach($students as $st)
                            <option value="{{ $st->id }}" {{ old('student_id') == $st->id ? 'selected' : '' }}>
                                {{ $st->full_name }} ({{ $st->classroom->name ?? 'Kelas Belum Ditentukan' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Jenis Izin <span class="text-red-500">*</span>
                    </label>
                    <select name="reason_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-white">
                        <option value="sakit" {{ old('reason_type') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="izin" {{ old('reason_type') === 'izin' ? 'selected' : '' }}>Izin Keperluan Keluarga / Mendesak</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Tanggal Berakhir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="end_date" value="{{ old('end_date', now()->toDateString()) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Keterangan Alasan <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="3" required
                          placeholder="Jelaskan kondisi sakit / urusan keluarga secara singkat dan jelas..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Lampiran Foto Surat Dokter / Surat Orang Tua (Opsional)
                </label>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-900 hover:file:bg-amber-200">
                <span class="text-[10px] text-slate-400 block mt-1">Format: JPG, PNG, atau PDF (maksimal 2MB).</span>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" @click="showForm = false" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow transition">
                    Kirim Surat Izin
                </button>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Riwayat Pengajuan Surat Izin</h3>
            <p class="text-xs text-slate-500">Status verifikasi surat izin oleh wali kelas</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Nama Ananda</th>
                        <th class="py-3.5 px-6">Jenis</th>
                        <th class="py-3.5 px-6">Rentang Tanggal</th>
                        <th class="py-3.5 px-6">Keterangan</th>
                        <th class="py-3.5 px-6">Status Validasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leaveRequests as $req)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $req->student->full_name }}
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
                                <p class="text-slate-700">{{ $req->description }}</p>
                                @if($req->attachment_path)
                                    <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" class="text-[11px] text-amber-700 hover:underline font-semibold block mt-1">
                                        Lihat Lampiran Foto ↗
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
                                        default => 'Menunggu Validasi',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $badge }}">
                                    {{ $label }}
                                </span>
                                @if($req->review_notes)
                                    <span class="block text-[10px] text-slate-400 mt-1">Catatan Wali Kelas: {{ $req->review_notes }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                Belum ada riwayat pengajuan surat izin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
