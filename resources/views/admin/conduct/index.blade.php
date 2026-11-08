@extends('layouts.portal')

@section('title', 'Buku Disiplin & Poin Karakter Santri')

@section('content')
<div class="space-y-6" x-data="{ addRecordModal: false, addRuleModal: false, selectedType: 'apresiasi' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Buku Disiplin & Karakter Santri</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pencatatan poin karakter positif, apresiasi keteladanan akhlak, dan pembinaan kedisiplinan madrasah.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="addRuleModal = true" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                + Aturan Baru
            </button>
            <button @click="addRecordModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow transition">
                <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Poin Santri</span>
            </button>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Nav Tabs: Log Catatan, Peringkat Santri, Katalog Tata Tertib -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('admin.conduct.index', ['tab' => 'records']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $tab === 'records' ? 'bg-emerald-900 text-white shadow' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            Riwayat Catatan Karakter
        </a>
        <a href="{{ route('admin.conduct.index', ['tab' => 'leaderboard']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $tab === 'leaderboard' ? 'bg-emerald-900 text-white shadow' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            Peringkat Karakter Santri
        </a>
        <a href="{{ route('admin.conduct.index', ['tab' => 'rules']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $tab === 'rules' ? 'bg-emerald-900 text-white shadow' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            Katalog Tata Tertib & Apresiasi
        </a>
    </div>

    @if($tab === 'records')
        <!-- Tab 1: Records Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Log Riwayat Pembiasaan Karakter</span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.conduct.index', ['tab' => 'records', 'type' => 'all']) }}" class="px-3 py-1 rounded-lg text-xs font-semibold {{ !request('type') || request('type') == 'all' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                    <a href="{{ route('admin.conduct.index', ['tab' => 'records', 'type' => 'apresiasi']) }}" class="px-3 py-1 rounded-lg text-xs font-semibold {{ request('type') == 'apresiasi' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600' }}">Apresiasi (+)</a>
                    <a href="{{ route('admin.conduct.index', ['tab' => 'records', 'type' => 'pelanggaran']) }}" class="px-3 py-1 rounded-lg text-xs font-semibold {{ request('type') == 'pelanggaran' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600' }}">Pembinaan (-)</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5">Santri & Rombel</th>
                            <th class="px-5 py-3.5">Tipe & Poin</th>
                            <th class="px-5 py-3.5">Kejadian / Penghargaan</th>
                            <th class="px-5 py-3.5">Tindak Lanjut / Apresiasi</th>
                            <th class="px-5 py-3.5">Pencatat</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($records as $rec)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-xs text-slate-500">
                                {{ $rec->incident_date->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900 block">{{ $rec->student->full_name }}</span>
                                <span class="text-[10px] text-slate-400">Kelas {{ $rec->student->classroom->name ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($rec->type === 'apresiasi')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-850">
                                        +{{ $rec->points }} Poin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800">
                                        -{{ $rec->points }} Poin
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-800 block">{{ $rec->title }}</span>
                                @if($rec->description)
                                    <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $rec->description }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-600">
                                {{ $rec->follow_up_action ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap">
                                {{ $rec->recorder->name ?? 'Asatidz' }}
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <form action="{{ route('admin.conduct.destroy_record', $rec) }}" method="POST" onsubmit="return confirm('Hapus catatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400 text-xs">Belum ada catatan karakter santri.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $records->links() }}
            </div>
        </div>

    @elseif($tab === 'leaderboard')
        <!-- Tab 2: Leaderboard / Student Scores -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
            <div>
                <span class="text-xs font-bold text-gold-600 uppercase tracking-wider">Evaluasi Karakter Santri</span>
                <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">Akumulasi Skor Karakter & Keteladanan Santri</h3>
                <p class="text-xs text-slate-500 mt-1">Skor standar awal setiap santri adalah 100 poin. Poin bertambah saat santri berprestasi/berakhlak mulia dan berkurang bila melanggar kedisiplinan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($students as $st)
                <div class="border border-slate-200 rounded-2xl p-4 flex items-center justify-between hover:border-emerald-300 transition">
                    <div>
                        <span class="font-bold text-slate-800 text-sm block">{{ $st->full_name }}</span>
                        <span class="text-xs text-slate-400">Kelas {{ $st->classroom->name ?? '-' }} • NIS: {{ $st->nis }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-black font-mono {{ $st->total_score >= 100 ? 'text-emerald-800' : 'text-amber-600' }}">
                            {{ $st->total_score }}
                        </span>
                        <span class="block text-[10px] font-bold uppercase tracking-wider
                            @if($st->total_score >= 115) text-emerald-800
                            @elseif($st->total_score >= 95) text-teal-800
                            @else text-amber-700 @endif">
                            @if($st->total_score >= 115) Teladan (Mumtaz)
                            @elseif($st->total_score >= 95) Baik (Jayyid)
                            @else Perlu Bimbingan @endif
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    @else
        <!-- Tab 3: Rules Catalog -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold text-emerald-850 uppercase tracking-wider">Tata Tertib & Panduan</span>
                    <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">Katalog Bobot Poin Karakter Madrasah</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Apresiasi Positif -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        Apresiasi Karakter Positif & Prestasi
                    </h4>
                    <div class="divide-y divide-slate-100 border border-emerald-100 rounded-2xl overflow-hidden bg-emerald-50/20">
                        @forelse($rules->where('type', 'apresiasi') as $rule)
                        <div class="p-3.5 flex items-center justify-between gap-2 hover:bg-emerald-50/50 transition">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">{{ $rule->name }}</span>
                                <span class="text-[10px] text-slate-400 capitalize">{{ str_replace('_', ' ', $rule->category) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-850 font-bold text-xs font-mono">+{{ $rule->points }}</span>
                                <form action="{{ route('admin.conduct.destroy_rule', $rule) }}" method="POST" onsubmit="return confirm('Hapus aturan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 text-xs">✕</button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <p class="p-4 text-xs text-slate-400 text-center italic">Belum ada aturan apresiasi.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Kolom Pelanggaran & Pembinaan -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-rose-700 flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                        Pelanggaran Tata Tertib & Bimbingan
                    </h4>
                    <div class="divide-y divide-slate-100 border border-rose-100 rounded-2xl overflow-hidden bg-rose-50/20">
                        @forelse($rules->where('type', 'pelanggaran') as $rule)
                        <div class="p-3.5 flex items-center justify-between gap-2 hover:bg-rose-50/50 transition">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">{{ $rule->name }}</span>
                                <span class="text-[10px] text-slate-400 capitalize">{{ str_replace('_', ' ', $rule->category) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-xs font-mono">-{{ $rule->points }}</span>
                                <form action="{{ route('admin.conduct.destroy_rule', $rule) }}" method="POST" onsubmit="return confirm('Hapus aturan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 text-xs">✕</button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <p class="p-4 text-xs text-slate-400 text-center italic">Belum ada aturan pelanggaran.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Catat Poin Santri -->
    <div x-show="addRecordModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="addRecordModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Catat Poin Karakter Santri</h3>
            <form action="{{ route('admin.conduct.store_record') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Pilih Santri</label>
                    <select name="student_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->full_name }} (Kelas {{ $st->classroom->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jenis Catatan</label>
                        <select name="type" x-model="selectedType" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option value="apresiasi">Apresiasi Positif (+)</option>
                            <option value="pelanggaran">Bimbingan / Pelanggaran (-)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Bobot Poin</label>
                        <input type="number" name="points" min="1" max="100" value="10" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Pilih Tata Tertib (Opsional)</label>
                    <select name="character_rule_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                        <option value="">-- Bebas / Kustom --</option>
                        @foreach($rules as $r)
                            <option value="{{ $r->id }}">[{{ ucfirst($r->type) }}] {{ $r->name }} ({{ $r->points }} poin)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Judul Kejadian / Penghargaan</label>
                        <input type="text" name="title" placeholder="e.g. Membantu membersihkan musholla" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal Kejadian</label>
                        <input type="date" name="incident_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tindak Lanjut / Apresiasi Edukatif</label>
                    <input type="text" name="follow_up_action" placeholder="e.g. Diberikan bintang teladan / Diberi nasehat santun" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Keterangan Tambahan</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addRecordModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-bold shadow">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Aturan Tata Tertib -->
    <div x-show="addRuleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="addRuleModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-800">Tambah Aturan Tata Tertib</h3>
            <form action="{{ route('admin.conduct.store_rule') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Butir Aturan</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="e.g. Terlambat hadir ke madrasah">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jenis</label>
                        <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option value="apresiasi">Apresiasi (+)</option>
                            <option value="pelanggaran">Pelanggaran (-)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Bobot Poin</label>
                        <input type="number" name="points" min="1" max="100" value="5" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kategori</label>
                    <select name="category" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                        <option value="akhlak_adab">Akhlak & Adab</option>
                        <option value="kedisiplinan">Kedisiplinan</option>
                        <option value="prestasi">Prestasi & Bakat</option>
                        <option value="kebersihan_kerapian">Kebersihan & Kerapian</option>
                        <option value="ibadah">Ibadah & Doa</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Deskripsi Singkat</label>
                    <input type="text" name="description" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addRuleModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-bold shadow">Simpan Aturan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
