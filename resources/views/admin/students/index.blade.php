@extends('layouts.portal')

@section('title', 'Manajemen Data Siswa')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, activeStudent: {} }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Master Data Siswa<span class="sr-only">Master Data Santri</span></h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data induk seluruh siswa aktif MI Plus Raudlatul Muta'allimin</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($classroomId)
                @php $currentClass = $classrooms->firstWhere('id', (int) $classroomId); @endphp
                @if($currentClass)
                    <a href="{{ route('admin.classrooms.exam_cards', $currentClass) }}" target="_blank"
                       class="px-4 py-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-900 border border-purple-200 font-bold text-xs shadow-sm flex items-center gap-2 transition">
                        <span>🎟️ Cetak Kartu Ujian Rombel</span>
                    </a>
                @endif
            @endif
            <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow flex items-center justify-center gap-2 transition">
                <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Tambah Siswa Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <form action="{{ route('admin.students.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3 items-center">
            <div class="relative w-full sm:w-72">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIS, atau NISN..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-emerald-800">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="classroom_id" onchange="this.form.submit()" class="w-full sm:w-48 py-2 px-3 rounded-xl border border-slate-200 text-xs font-semibold bg-white">
                <option value="">Semua Rombel</option>
                @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ $classroomId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="w-full sm:w-40 py-2 px-3 rounded-xl border border-slate-200 text-xs font-semibold bg-white">
                <option value="">Semua Status</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="graduated" {{ $status === 'graduated' ? 'selected' : '' }}>Lulus</option>
                <option value="transferred" {{ $status === 'transferred' ? 'selected' : '' }}>Pindah</option>
                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                Filter
            </button>
            @if($search || $classroomId || $status)
                <a href="{{ route('admin.students.index') }}" class="text-xs text-red-600 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table of Students -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">NIS / NISN</th>
                        <th class="py-3.5 px-6">Nama Lengkap & Panggilan</th>
                        <th class="py-3.5 px-6">L/P</th>
                        <th class="py-3.5 px-6">Kelas / Rombel</th>
                        <th class="py-3.5 px-6">Wali Murid</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $st)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 font-mono">
                                <span class="font-bold text-slate-800 block">{{ $st->nis ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $st->nisn ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block text-sm">{{ $st->full_name }}</span>
                                <span class="text-[11px] text-slate-500">Panggilan: {{ $st->nickname ?? '-' }} • {{ $st->birth_place }}, {{ $st->birth_date?->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $st->gender === 'L' ? 'bg-blue-50 text-blue-800 border border-blue-200' : 'bg-pink-50 text-pink-800 border border-pink-200' }}">
                                    {{ $st->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-700">
                                {{ $st->classroom?->name ?? 'Belum Rombel' }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800 block">{{ $st->parent?->name ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $st->parent?->phone_number ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border
                                    {{ $st->status->value === 'active' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                    {{ $st->status->value }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('admin.students.report_card', $st) }}" target="_blank"
                                       class="px-2 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-[10px] transition border border-emerald-200" title="Cetak Rapor Digital Siswa">
                                        📄 Rapor
                                    </a>
                                    <a href="{{ route('admin.students.card', $st) }}" target="_blank"
                                       class="px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-[10px] transition border border-blue-200" title="Cetak Kartu Tanda Siswa (KTS)">
                                        🪪 KTS
                                    </a>
                                    <a href="{{ route('admin.students.exam_card', $st) }}" target="_blank"
                                       class="px-2 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-800 font-bold text-[10px] transition border border-purple-200" title="Cetak Kartu Peserta Ujian">
                                        🎟️ Ujian
                                    </a>
                                    <button type="button" @click="activeStudent = {{ json_encode($st) }}; editModal = true"
                                            class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] transition">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.students.destroy', $st) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-bold text-[10px] transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Tidak ada data siswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Siswa -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Tambah Siswa Baru</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="full_name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Panggilan</label>
                        <input type="text" name="nickname" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kelamin *</label>
                        <select name="gender" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">NIS *</label>
                        <input type="text" name="nis" required placeholder="Contoh: 262701031" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">NISN *</label>
                        <input type="text" name="nisn" required placeholder="10 digit nomor NISN" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tempat Lahir *</label>
                        <input type="text" name="birth_place" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Lahir *</label>
                        <input type="date" name="birth_date" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Rombel Kelas *</label>
                        <select name="classroom_id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Akun Wali Murid *</label>
                        <select name="parent_id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Siswa *</label>
                        <select name="status" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="active">Aktif</option>
                            <option value="graduated">Lulus</option>
                            <option value="transferred">Pindah</option>
                            <option value="inactive">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Domisili Lengkap *</label>
                    <textarea name="address" rows="2" required placeholder="Dusun/Jalan, RT/RW, Desa, Kecamatan, Kabupaten" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Simpan Siswa</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Siswa -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Edit Data Siswa</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="'/admin/santri/' + activeStudent.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="full_name" :value="activeStudent.full_name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Panggilan</label>
                        <input type="text" name="nickname" :value="activeStudent.nickname" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kelamin *</label>
                        <select name="gender" x-model="activeStudent.gender" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">NIS *</label>
                        <input type="text" name="nis" :value="activeStudent.nis" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">NISN *</label>
                        <input type="text" name="nisn" :value="activeStudent.nisn" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tempat Lahir *</label>
                        <input type="text" name="birth_place" :value="activeStudent.birth_place" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Lahir *</label>
                        <input type="date" name="birth_date" :value="activeStudent.birth_date ? activeStudent.birth_date.substring(0, 10) : ''" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Rombel Kelas *</label>
                        <select name="classroom_id" x-model="activeStudent.classroom_id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Akun Wali Murid *</label>
                        <select name="parent_id" x-model="activeStudent.parent_id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Siswa *</label>
                        <select name="status" x-model="activeStudent.status" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="active">Aktif</option>
                            <option value="graduated">Lulus</option>
                            <option value="transferred">Pindah</option>
                            <option value="inactive">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Domisili Lengkap *</label>
                    <textarea name="address" rows="2" x-model="activeStudent.address" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 text-white text-xs font-bold shadow hover:bg-emerald-800">Perbarui Siswa</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
