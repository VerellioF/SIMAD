@extends('layouts.portal')

@section('title', 'Manajemen Pengumuman')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, activeAnnounce: {} }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pengumuman & Surat Edaran</h1>
            <p class="text-xs text-slate-500 mt-0.5">Siarkan pengumuman resmi madrasah untuk publik maupun khusus orang tua / wali murid</p>
        </div>

        <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow flex items-center justify-center gap-2 transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Buat Pengumuman Baru</span>
        </button>
    </div>

    <!-- Table of Announcements -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Judul Pengumuman</th>
                        <th class="py-3.5 px-6">Sasaran Pembaca</th>
                        <th class="py-3.5 px-6">Berlaku Sampai</th>
                        <th class="py-3.5 px-6">Tanggal Rilis</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($announcements as $ann)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 max-w-md">
                                <span class="font-bold text-slate-900 block text-sm">{{ $ann->title }}</span>
                                <span class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">{{ Str::limit(strip_tags($ann->content), 100) }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border
                                    {{ $ann->target_audience->value === 'all' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($ann->target_audience->value === 'parent' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-blue-50 text-blue-800 border-blue-200') }}">
                                    {{ $ann->target_audience->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-600">
                                {{ $ann->valid_until ? $ann->valid_until->format('d/m/Y') : 'Selamanya' }}
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-400">
                                {{ $ann->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6 text-right space-x-1">
                                <button type="button" @click="activeAnnounce = {{ json_encode($ann) }}; editModal = true"
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition">
                                    Edit
                                </button>
                                <form action="{{ route('admin.announcements.destroy', $ann) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-bold text-[11px] transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <span class="text-3xl block">📢</span>
                                    <h4 class="text-xs font-extrabold text-slate-800">Belum Ada Pengumuman</h4>
                                    <p class="text-[11px] text-slate-400">Siarkan pengumuman atau surat edaran resmi madrasah untuk orang tua atau publik.</p>
                                    <button type="button" @click="addModal = true" class="mt-2 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-900 text-white font-bold text-xs shadow-sm hover:bg-emerald-800 transition">
                                        <span>Buat Pengumuman Baru</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Pengumuman -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addModal = false" class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Buat Pengumuman Baru</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pengumuman *</label>
                    <input type="text" name="title" required placeholder="Contoh: Libur Awal Ramadhan 1448 H" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sasaran Pembaca *</label>
                        <select name="target_audience" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            @foreach($targets as $tgt)
                                <option value="{{ $tgt->value }}">{{ $tgt->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Berlaku Sampai (Opsional)</label>
                        <input type="date" name="valid_until" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pengumuman Lengkap *</label>
                    <textarea name="content" rows="5" required placeholder="Tuliskan isi pengumuman atau surat edaran..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Simpan & Publikasikan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Pengumuman -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Edit Pengumuman</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="'/admin/pengumuman/' + activeAnnounce.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pengumuman *</label>
                    <input type="text" name="title" :value="activeAnnounce.title" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sasaran Pembaca *</label>
                        <select name="target_audience" x-model="activeAnnounce.target_audience" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            @foreach($targets as $tgt)
                                <option value="{{ $tgt->value }}">{{ $tgt->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Berlaku Sampai</label>
                        <input type="date" name="valid_until" :value="activeAnnounce.valid_until ? activeAnnounce.valid_until.substring(0, 10) : ''" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pengumuman Lengkap *</label>
                    <textarea name="content" rows="5" x-model="activeAnnounce.content" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Perbarui Pengumuman</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
