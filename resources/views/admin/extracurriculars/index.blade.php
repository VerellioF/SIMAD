@extends('layouts.portal')

@section('title', 'Kelola Ekstrakurikuler')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, selectedEkskul: {}, enrollModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Kelola Ekstrakurikuler</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar kegiatan minat bakat, jadwal mingguan, pembina, serta daftar peserta siswa.</p>
        </div>
        <button @click="addModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Ekskul Baru</span>
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Main Grid: List Ekskul & Detail Peserta -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- List Ekskul (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Katalog Ekstrakurikuler</span>
                <span class="text-xs text-slate-400">Total: {{ $extracurriculars->total() }} Kegiatan</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($extracurriculars as $ekskul)
                <div class="p-5 hover:bg-slate-50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $selectedEkskul && $selectedEkskul->id == $ekskul->id ? 'bg-emerald-50/50' : '' }}">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                @if($ekskul->category == 'keagamaan') bg-emerald-100 text-emerald-800
                                @elseif($ekskul->category == 'olahraga') bg-blue-100 text-blue-800
                                @elseif($ekskul->category == 'seni_budaya') bg-purple-100 text-purple-800
                                @elseif($ekskul->category == 'kepanduan') bg-amber-100 text-amber-800
                                @else bg-cyan-100 text-cyan-800 @endif">
                                {{ str_replace('_', ' ', ucfirst($ekskul->category)) }}
                            </span>
                            @if(!$ekskul->is_active)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Non-Aktif</span>
                            @endif
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-base leading-tight">{{ $ekskul->name }}</h4>
                        <p class="text-xs text-slate-500">
                            Pembina: <span class="font-semibold text-slate-700">{{ $ekskul->instructor_name }}</span> • 
                            Jadwal: <span class="text-emerald-800 font-medium">{{ $ekskul->schedule_day }}, {{ $ekskul->schedule_time }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.ekskul.index', ['ekskul_id' => $ekskul->id]) }}" class="px-3 py-1.5 rounded-xl bg-emerald-900 text-white text-xs font-bold hover:bg-emerald-800 shadow-sm transition flex items-center gap-1">
                            <span>{{ $ekskul->enrollments_count }} Siswa</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <button @click="selectedEkskul = {{ $ekskul->toJson() }}; editModal = true" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <form action="{{ route('admin.ekskul.destroy', $ekskul) }}" method="POST" @submit.prevent="openConfirm('Hapus Ekstrakurikuler', 'Apakah Anda yakin ingin menghapus data ekstrakurikuler ini?', () => $el.submit())">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="p-10 text-center text-slate-400 text-xs">Belum ada ekstrakurikuler yang dibuat.</div>
                @endforelse
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $extracurriculars->links() }}
            </div>
        </div>

        <!-- Detail Peserta (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            @if($selectedEkskul)
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">Daftar Siswa Peserta</span>
                        <h3 class="text-base font-extrabold text-slate-900">{{ $selectedEkskul->name }}</h3>
                    </div>
                    <button @click="enrollModal = true" class="px-3 py-1.5 rounded-xl bg-gold-500 hover:bg-gold-600 text-emerald-950 font-bold text-xs shadow-sm transition">
                        Daftarkan Siswa
                    </button>
                </div>

                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @forelse($enrollments as $enr)
                    <div class="py-2.5 flex items-center justify-between gap-2">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">{{ $enr->student->full_name }}</span>
                            <span class="text-[10px] text-slate-400">Kelas {{ $enr->student->classroom->name ?? '-' }} • NIS: {{ $enr->student->nis }}</span>
                        </div>
                        <form action="{{ route('admin.ekskul.unenroll', $enr) }}" method="POST" @submit.prevent="openConfirm('Keluarkan Siswa', 'Batalkan keikutsertaan siswa dari ekstrakurikuler ini?', () => $el.submit())">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[11px] text-red-500 hover:underline">Hapus</button>
                        </form>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-6 text-center italic">Belum ada siswa yang terdaftar di ekstrakurikuler ini.</p>
                    @endforelse
                </div>
            @else
                <div class="py-16 text-center text-slate-400 text-xs">
                    <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Klik tombol "Siswa" pada salah satu ekskul di sebelah kiri untuk melihat dan mengelola peserta.
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah Ekskul -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="addModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Tambah Ekstrakurikuler Baru</h3>
            <form action="{{ route('admin.ekskul.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Ekstrakurikuler</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="e.g. Seni Hadrah & Banjari">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kategori</label>
                        <select name="category" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option value="keagamaan">Keagamaan</option>
                            <option value="seni_budaya">Seni Budaya</option>
                            <option value="olahraga">Olahraga</option>
                            <option value="kepanduan">Kepanduan</option>
                            <option value="sains">Sains</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Pembina / Pelatih</label>
                        <input type="text" name="instructor_name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="e.g. Bpk. Ahmad Baihaqi, S.Pd.">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Hari Pelaksanaan</label>
                        <input type="text" name="schedule_day" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="e.g. Sabtu Sore">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jam Pelaksanaan</label>
                        <input type="text" name="schedule_time" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="e.g. 15:30 - 17:00">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Lokasi Latihan</label>
                    <input type="text" name="location" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="e.g. Aula Madrasah Lantai 2">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-bold shadow">Simpan Ekskul</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pendaftaran Siswa Manual oleh Admin -->
    @if($selectedEkskul)
    <div x-show="enrollModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="enrollModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-800">Daftarkan Siswa ke {{ $selectedEkskul->name }}</h3>
            <form action="{{ route('admin.ekskul.enroll') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="extracurricular_id" value="{{ $selectedEkskul->id }}">
                <input type="hidden" name="academic_year_id" value="{{ $activeYear->id ?? 1 }}">

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Pilih Siswa</label>
                    <select name="student_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->classroom->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="enrollModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-bold shadow">Daftarkan Siswa</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
