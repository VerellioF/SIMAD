@extends('layouts.portal')

@section('title', 'Manajemen Rombel & Kelas')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, activeClass: {} }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Rombongan Belajar & Kelas</h1>
            <p class="text-xs text-slate-500 mt-0.5">Tahun Ajaran: <strong>{{ $activeYear?->name ?? '2026/2027' }}</strong> • Kelola rombel dan penetapan wali kelas</p>
        </div>

        <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow flex items-center justify-center gap-2 transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Buat Rombel Baru</span>
        </button>
    </div>

    <!-- Classroom Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($classrooms as $cls)
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Tingkat Kelas {{ $cls->grade_level }}
                        </span>
                        <h3 class="text-lg font-extrabold text-slate-900 mt-2">{{ $cls->name }}</h3>
                    </div>

                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-800 flex items-center justify-center font-extrabold text-sm border border-emerald-200 font-mono">
                        {{ $cls->students_count }}
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-3 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-400">Wali Kelas:</span>
                        <span class="font-bold text-slate-900">{{ $cls->homeroomTeacher?->name ?? 'Belum Ditentukan' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-400">Total Siswa:</span>
                        <span class="font-bold font-mono text-emerald-900">{{ $cls->students_count }} Siswa</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="activeClass = {{ json_encode($cls) }}; editModal = true"
                            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Edit
                    </button>
                    <form action="{{ route('admin.classrooms.destroy', $cls) }}" method="POST" onsubmit="return confirm('Hapus rombel ini? Hanya bisa jika tidak ada siswa di dalamnya.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-12 text-center text-slate-400 rounded-3xl border border-slate-200">
                Belum ada rombel kelas yang terdaftar.
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah Kelas -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Buat Rombel Baru</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.classrooms.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Rombel *</label>
                    <input type="text" name="name" required placeholder="Contoh: Kelas 1-A (Abu Bakar)" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tingkat Kelas (1-6) *</label>
                        <select name="grade_level" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}">Kelas {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Wali Kelas</label>
                        <select name="homeroom_teacher_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Simpan Rombel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kelas -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Edit Rombel</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="'/admin/kelas/' + activeClass.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Rombel *</label>
                    <input type="text" name="name" :value="activeClass.name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tingkat Kelas (1-6) *</label>
                        <select name="grade_level" x-model="activeClass.grade_level" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}">Kelas {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Wali Kelas</label>
                        <select name="homeroom_teacher_id" x-model="activeClass.homeroom_teacher_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Perbarui Rombel</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
