@extends('layouts.portal')

@section('title', 'Kelola Kalender & Agenda Madrasah')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, selectedEvent: {} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Kalender & Agenda Madrasah</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola agenda kegiatan, jadwal ujian, hari libur, dan peringatan hari besar Islam.</p>
        </div>
        <button @click="addModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Agenda Baru</span>
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table of Events -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Daftar Agenda Terjadwal</span>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.agenda.index') }}" class="px-3 py-1 rounded-lg text-xs font-semibold {{ !request('type') ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
                <a href="{{ route('admin.agenda.index', ['type' => 'akademik']) }}" class="px-3 py-1 rounded-lg text-xs font-semibold {{ request('type') == 'akademik' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Akademik</a>
                <a href="{{ route('admin.agenda.index', ['type' => 'ujian']) }}" class="px-3 py-1 rounded-lg text-xs font-semibold {{ request('type') == 'ujian' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Ujian</a>
                <a href="{{ route('admin.agenda.index', ['type' => 'libur']) }}" class="px-3 py-1 rounded-lg text-xs font-semibold {{ request('type') == 'libur' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Libur</a>
                <a href="{{ route('admin.agenda.index', ['type' => 'phbi']) }}" class="px-3 py-1 rounded-lg text-xs font-semibold {{ request('type') == 'phbi' ? 'bg-emerald-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">PHBI</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Agenda / Kegiatan</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Status Libur</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($events as $event)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800">{{ $event->start_date->format('d M Y') }}</span>
                            @if($event->end_date && $event->end_date->gt($event->start_date))
                                <span class="text-slate-400 block text-xs">s.d. {{ $event->end_date->format('d M Y') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-900">{{ $event->title }}</span>
                            @if($event->description)
                                <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $event->description }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                @if($event->event_type == 'ujian') bg-amber-100 text-amber-800
                                @elseif($event->event_type == 'libur') bg-red-100 text-red-800
                                @elseif($event->event_type == 'keagamaan') bg-purple-100 text-purple-800
                                @else bg-teal-100 text-teal-800 @endif">
                                {{ ucfirst($event->event_type) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($event->is_holiday)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">Libur KBM</span>
                            @else
                                <span class="text-xs text-slate-400">Hari Efektif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap space-x-2">
                            <button @click="selectedEvent = {{ $event->toJson() }}; editModal = true" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                                Edit
                            </button>
                            <form action="{{ route('admin.agenda.destroy', $event) }}" method="POST" class="inline-block" @submit.prevent="openConfirm('Hapus Agenda', 'Apakah Anda yakin ingin menghapus agenda madrasah ini?', () => $el.submit())">
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
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="max-w-xs mx-auto space-y-2">
                                <span class="text-3xl block">📅</span>
                                <h4 class="text-xs font-extrabold text-slate-800">Belum Ada Agenda Terdaftar</h4>
                                <p class="text-[11px] text-slate-400">Jadwal kegiatan semester, libur sekolah, atau ujian belum dibuat.</p>
                                <button type="button" @click="addModal = true" class="mt-2 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-900 text-white font-bold text-xs shadow-sm hover:bg-emerald-800 transition">
                                    <span>Tambah Agenda Sekarang</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $events->links() }}
        </div>
    </div>

    <!-- Modal Tambah Agenda -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="addModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Tambah Agenda Madrasah</h3>
            <form action="{{ route('admin.agenda.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $activeYear->id ?? 1 }}">

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Judul Agenda</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="e.g. Asesmen Sumatif Akhir Semester (ASAS)">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kategori</label>
                        <select name="event_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option value="akademik">Akademik</option>
                            <option value="ujian">Ujian / Asesmen</option>
                            <option value="libur">Libur Madrasah</option>
                            <option value="phbi">Keagamaan / PHBI</option>
                            <option value="kegiatan">Kegiatan / Ekstra</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Status Libur</label>
                        <select name="is_holiday" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option value="0">Tidak Libur (Efektif)</option>
                            <option value="1">Libur Resmi KBM</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal Selesai (Opsional)</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Keterangan / Catatan</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900" placeholder="Keterangan tambahan..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-bold shadow">Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Agenda -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="editModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Edit Agenda Madrasah</h3>
            <form :action="'/admin/agenda/' + selectedEvent.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="academic_year_id" :value="selectedEvent.academic_year_id || {{ $activeYear->id ?? 1 }}">

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Judul Agenda</label>
                    <input type="text" name="title" :value="selectedEvent.title" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kategori</label>
                        <select name="event_type" x-model="selectedEvent.event_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option value="akademik">Akademik</option>
                            <option value="ujian">Ujian / Asesmen</option>
                            <option value="libur">Libur Madrasah</option>
                            <option value="phbi">Keagamaan / PHBI</option>
                            <option value="kegiatan">Kegiatan / Ekstra</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Status Libur</label>
                        <select name="is_holiday" x-model="selectedEvent.is_holiday" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            <option :value="false">Tidak Libur (Efektif)</option>
                            <option :value="true">Libur Resmi KBM</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" :value="selectedEvent.start_date ? selectedEvent.start_date.substring(0, 10) : ''" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" :value="selectedEvent.end_date ? selectedEvent.end_date.substring(0, 10) : ''" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Keterangan / Catatan</label>
                    <textarea name="description" x-model="selectedEvent.description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-bold shadow">Perbarui Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
