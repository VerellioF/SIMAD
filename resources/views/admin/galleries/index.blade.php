@extends('layouts.portal')

@section('title', 'Manajemen Galeri Madrasah')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Galeri Foto & Dokumentasi</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola foto kegiatan ekstrakurikuler, hadrah, pramuka, dan fasilitas untuk galeri publik</p>
        </div>

        <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow flex items-center justify-center gap-2 transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Unggah Foto Baru</span>
        </button>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($galleries as $g)
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-md transition group">
                <div class="h-48 bg-emerald-950 overflow-hidden relative">
                    <img src="{{ asset($g->image_path) }}" alt="{{ $g->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute top-3 left-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/90 text-emerald-900 shadow">
                            {{ $g->category->label() }}
                        </span>
                    </div>
                </div>

                <div class="p-5 flex items-start justify-between gap-3">
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm leading-snug">{{ $g->title }}</h4>
                        <span class="text-[10px] text-slate-400 font-mono mt-1 block">{{ $g->created_at->format('d/m/Y') }}</span>
                    </div>

                    <form action="{{ route('admin.galleries.destroy', $g) }}" method="POST" onsubmit="return confirm('Hapus foto kegiatan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus Foto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-12 text-center text-slate-400 rounded-3xl border border-slate-200">
                Belum ada foto kegiatan di galeri madrasah.
            </div>
        @endforelse
    </div>

    @if($galleries->hasPages())
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            {{ $galleries->links() }}
        </div>
    @endif

    <!-- Modal Unggah Foto -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Unggah Foto Galeri Baru</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul / Keterangan Foto *</label>
                    <input type="text" name="title" required placeholder="Contoh: Latihan Drumband Santri" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Kegiatan *</label>
                    <select name="category" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Berkas Foto (JPG/PNG/WEBP/SVG, Maks 3MB) *</label>
                    <input type="file" name="image" required accept="image/*" class="w-full text-xs file:mr-2 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:text-emerald-800 file:font-semibold">
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Unggah Foto</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
