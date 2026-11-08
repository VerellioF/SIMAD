@extends('layouts.portal')

@section('title', 'Manajemen Data Guru & Asatidz')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, activeTeacher: {} }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Data Dewan Guru & Asatidz</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola akun ustadz/ustadzah pengajar dan penugasan wali kelas</p>
        </div>

        <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow flex items-center justify-center gap-2 transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Tambah Guru / Asatidz</span>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <form action="{{ route('admin.teachers.index') }}" method="GET" class="w-full sm:w-96 flex gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau no WhatsApp..."
                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-emerald-800">
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">Cari</button>
            @if($search)
                <a href="{{ route('admin.teachers.index') }}" class="px-2 py-2 text-xs text-red-600 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table of Teachers -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Nama Ustadz / Guru</th>
                        <th class="py-3.5 px-6">Kontak & Email</th>
                        <th class="py-3.5 px-6">Penugasan Wali Kelas</th>
                        <th class="py-3.5 px-6">Status Akun</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teachers as $t)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block text-sm">{{ $t->name }}</span>
                                <span class="text-[11px] text-slate-400">Pendidik MI Plus</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-mono text-slate-800 block">{{ $t->email }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">{{ $t->phone_number ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                @if($t->homeroomClassrooms->isNotEmpty())
                                    @foreach($t->homeroomClassrooms as $hc)
                                        <span class="inline-block px-2.5 py-1 rounded-lg bg-teal-50 text-teal-900 border border-teal-200 font-bold text-xs">
                                            {{ $hc->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-slate-400 italic">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border
                                    {{ $t->is_active ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-800 border-red-200' }}">
                                    {{ $t->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-1">
                                <button type="button" @click="activeTeacher = {{ json_encode($t) }}; editModal = true"
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition">
                                    Edit
                                </button>
                                <form action="{{ route('admin.teachers.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun guru ini?')">
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
                                Tidak ada data guru / asatidz.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Guru -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Tambah Akun Guru Baru</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.teachers.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap & Gelar *</label>
                    <input type="text" name="name" required placeholder="Contoh: Ustadz Ahmad, S.Pd.I." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Login *</label>
                        <input type="email" name="email" required placeholder="guru@simad.id" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No WhatsApp *</label>
                        <input type="text" name="phone_number" required placeholder="081234567890" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru *</label>
                        <input type="password" name="password" required placeholder="Min. 6 karakter" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tugaskan Wali Kelas</label>
                        <select name="homeroom_classroom_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="">-- Bukan Wali Kelas --</option>
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Simpan Guru</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Guru -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Edit Akun Guru</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="'/admin/guru/' + activeTeacher.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap & Gelar *</label>
                    <input type="text" name="name" :value="activeTeacher.name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Login *</label>
                        <input type="email" name="email" :value="activeTeacher.email" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No WhatsApp *</label>
                        <input type="text" name="phone_number" :value="activeTeacher.phone_number" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Password (Kosongkan jika tetap)</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tetap" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Akun *</label>
                        <select name="is_active" x-model="activeTeacher.is_active" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option :value="1">Aktif</option>
                            <option :value="0">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tugaskan Wali Kelas</label>
                    <select name="homeroom_classroom_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                        <option value="">-- Bukan Wali Kelas --</option>
                        @foreach($classrooms as $c)
                            <option value="{{ $c->id }}" :selected="activeTeacher.homeroom_classrooms && activeTeacher.homeroom_classrooms.some(r => r.id === {{ $c->id }})">
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Perbarui Guru</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
