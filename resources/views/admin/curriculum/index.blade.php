@extends('layouts.portal')

@section('title', 'Kelola Struktur Kurikulum Madrasah')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, selectedItem: {} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Struktur Kurikulum Madrasah</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola beban jam tatap muka (JP), kelompok mata pelajaran, dan capaian pembelajaran per fase jenjang.</p>
        </div>
        <button @click="addModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Mapel Kurikulum</span>
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter Fase -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-2 overflow-x-auto">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap mr-2">Filter Fase:</span>
        <a href="{{ route('admin.curriculum.index', ['phase' => 'all']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $selectedPhase === 'all' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua Fase</a>
        <a href="{{ route('admin.curriculum.index', ['phase' => 'fase_a']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $selectedPhase === 'fase_a' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Fase A (Kelas 1-2)</a>
        <a href="{{ route('admin.curriculum.index', ['phase' => 'fase_b']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $selectedPhase === 'fase_b' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Fase B (Kelas 3-4)</a>
        <a href="{{ route('admin.curriculum.index', ['phase' => 'fase_c']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $selectedPhase === 'fase_c' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Fase C (Kelas 5-6)</a>
    </div>

    <!-- Table List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Mata Pelajaran</th>
                        <th class="px-5 py-3.5">Fase & Jenjang</th>
                        <th class="px-5 py-3.5">Kelompok Kurikulum</th>
                        <th class="px-5 py-3.5 text-center">Beban Jam (JP)</th>
                        <th class="px-5 py-3.5">Capaian Pembelajaran (CP)</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($structures as $st)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-bold text-slate-900 block">{{ $st->name }}</span>
                            @if($st->code)<span class="text-[10px] text-slate-400 font-mono">{{ $st->code }}</span>@endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $st->phaseLabel() }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold
                                @if($st->curriculum_type === 'kemenag_pai') bg-emerald-100 text-emerald-850
                                @elseif($st->curriculum_type === 'nasional_merdeka') bg-teal-100 text-teal-850
                                @else bg-amber-100 text-amber-850 @endif">
                                {{ $st->curriculumTypeLabel() }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center whitespace-nowrap font-mono font-bold text-emerald-850">
                            {{ $st->hours_per_week }} JP
                        </td>
                        <td class="px-5 py-4 max-w-xs truncate text-xs text-slate-500">
                            {{ $st->competency_goals ?? '-' }}
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap space-x-2">
                            <button @click="selectedItem = {{ $st->toJson() }}; editModal = true" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                                Edit
                            </button>
                            <form action="{{ route('admin.curriculum.destroy', $st) }}" method="POST" class="inline-block" @submit.prevent="openConfirm('Hapus Mata Pelajaran', 'Apakah Anda yakin ingin menghapus mata pelajaran ini dari struktur kurikulum?', () => $el.submit())">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400 text-xs">Belum ada struktur kurikulum yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $structures->links() }}
        </div>
    </div>

    <!-- Modal Tambah Mapel Kurikulum -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="addModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Tambah Mata Pelajaran Kurikulum</h3>
            <form action="{{ route('admin.curriculum.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Mata Pelajaran</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="e.g. Al-Qur'an Hadits">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kode Mapel</label>
                        <input type="text" name="code" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="QH-1">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Fase / Jenjang</label>
                        <select name="phase" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option value="fase_a">Fase A (Kelas 1 - 2)</option>
                            <option value="fase_b">Fase B (Kelas 3 - 4)</option>
                            <option value="fase_c">Fase C (Kelas 5 - 6)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kelompok Kurikulum</label>
                        <select name="curriculum_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option value="kemenag_pai">Khas Kemenag (PAI & B. Arab)</option>
                            <option value="nasional_merdeka">Nasional / Merdeka</option>
                            <option value="muatan_lokal_pesantren">Muatan Lokal & Pesantren Plus</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Beban Jam (JP / Minggu)</label>
                        <input type="number" name="hours_per_week" min="1" max="10" value="2" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Urutan Tampilan</label>
                        <input type="number" name="display_order" value="1" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Target Capaian Pembelajaran (CP)</label>
                    <textarea name="competency_goals" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="Capaian kompetensi siswa..."></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Ringkasan Materi Pokok / Silabus</label>
                    <textarea name="syllabus_summary" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="Materi bahasan utama..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-bold shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Mapel Kurikulum -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="editModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Edit Mata Pelajaran Kurikulum</h3>
            <form :action="'/admin/kurikulum/' + selectedItem.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Mata Pelajaran</label>
                        <input type="text" name="name" :value="selectedItem.name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kode Mapel</label>
                        <input type="text" name="code" :value="selectedItem.code" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Fase / Jenjang</label>
                        <select name="phase" x-model="selectedItem.phase" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option value="fase_a">Fase A (Kelas 1 - 2)</option>
                            <option value="fase_b">Fase B (Kelas 3 - 4)</option>
                            <option value="fase_c">Fase C (Kelas 5 - 6)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kelompok Kurikulum</label>
                        <select name="curriculum_type" x-model="selectedItem.curriculum_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option value="kemenag_pai">Khas Kemenag (PAI & B. Arab)</option>
                            <option value="nasional_merdeka">Nasional / Merdeka</option>
                            <option value="muatan_lokal_pesantren">Muatan Lokal & Pesantren Plus</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Beban Jam (JP / Minggu)</label>
                        <input type="number" name="hours_per_week" x-model="selectedItem.hours_per_week" min="1" max="10" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Urutan Tampilan</label>
                        <input type="number" name="display_order" x-model="selectedItem.display_order" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Target Capaian Pembelajaran (CP)</label>
                    <textarea name="competency_goals" x-model="selectedItem.competency_goals" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Ringkasan Materi Pokok / Silabus</label>
                    <textarea name="syllabus_summary" x-model="selectedItem.syllabus_summary" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-bold shadow">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
