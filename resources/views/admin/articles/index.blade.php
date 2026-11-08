@extends('layouts.portal')

@section('title', 'Manajemen Warta & Berita')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, activeArticle: {} }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Warta & Berita Madrasah</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola publikasi kabar kegiatan, prestasi santri, dan berita untuk website publik</p>
        </div>

        <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow flex items-center justify-center gap-2 transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Tulis Berita Baru</span>
        </button>
    </div>

    <!-- Table of Articles -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Judul Artikel</th>
                        <th class="py-3.5 px-6">Penulis</th>
                        <th class="py-3.5 px-6">Tanggal Rilis</th>
                        <th class="py-3.5 px-6">Status Publikasi</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($articles as $art)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 max-w-md">
                                <span class="font-bold text-slate-900 block text-sm">{{ $art->title }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">/berita/{{ $art->slug }}</span>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-700">
                                {{ $art->author?->name ?? 'Admin TU' }}
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-600">
                                {{ $art->published_at ? $art->published_at->format('d M Y') : $art->created_at->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border
                                    {{ $art->is_published ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                    {{ $art->is_published ? 'Tayang (Publik)' : 'Draf' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-1">
                                <button type="button" @click="activeArticle = {{ json_encode($art) }}; editModal = true"
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition">
                                    Edit
                                </button>
                                <form action="{{ route('admin.articles.destroy', $art) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
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
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                Belum ada artikel warta yang dipublikasikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Artikel -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Tulis Berita Baru</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Artikel / Berita *</label>
                    <input type="text" name="title" required placeholder="Contoh: Semarak Maulid Nabi di MI Plus" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Publikasi *</label>
                        <select name="is_published" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="1">Langsung Terbitkan (Publik)</option>
                            <option value="0">Simpan Sebagai Draf</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Foto Sampul (Opsional)</label>
                        <input type="file" name="thumbnail" accept="image/*" class="w-full text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-800 file:font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Berita Lengkap *</label>
                    <textarea name="content" rows="6" required placeholder="Tulis isi warta berita madrasah secara lengkap..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-sans"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Terbitkan Berita</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Artikel -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Edit Berita</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="'/admin/berita/' + activeArticle.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Artikel / Berita *</label>
                    <input type="text" name="title" :value="activeArticle.title" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Publikasi *</label>
                        <select name="is_published" x-model="activeArticle.is_published" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option :value="1">Langsung Terbitkan (Publik)</option>
                            <option :value="0">Simpan Sebagai Draf</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Foto Sampul</label>
                        <input type="file" name="thumbnail" accept="image/*" class="w-full text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-800 file:font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Berita Lengkap *</label>
                    <textarea name="content" rows="6" x-model="activeArticle.content" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-sans"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Perbarui Berita</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
