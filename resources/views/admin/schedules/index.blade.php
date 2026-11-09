@extends('layouts.portal')

@section('title', 'Jadwal Pelajaran KBM Mingguan')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Jadwal Pelajaran KBM Mingguan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola pembagian roster jadwal mengajar guru dan jam tatap muka per kelas.</p>
        </div>
        <button @click="addModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow transition">
            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Jadwal Slot</span>
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Classroom Selection Tabs -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-2 overflow-x-auto">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap mr-2">Pilih Rombel:</span>
        @foreach($classrooms as $cls)
            <a href="{{ route('admin.schedules.index', ['classroom_id' => $cls->id]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $selectedClassroom && $selectedClassroom->id == $cls->id ? 'bg-emerald-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Kelas {{ $cls->name }}
            </a>
        @endforeach
    </div>

    <!-- Schedule Weekly Grid -->
    @if($selectedClassroom)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($days as $day)
                @php
                    $daySchedules = $schedules->get($day, collect());
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Day Header -->
                        <div class="px-5 py-3.5 bg-gradient-to-r from-emerald-900 to-emerald-850 text-white flex justify-between items-center">
                            <span class="font-extrabold text-sm uppercase tracking-wider">{{ ucfirst($day) }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/20 font-bold">{{ $daySchedules->count() }} Jam</span>
                        </div>

                        <!-- Slots List -->
                        <div class="p-4 divide-y divide-slate-100 space-y-3">
                            @forelse($daySchedules as $slot)
                                <div class="pt-3 first:pt-0 flex items-start justify-between gap-2 group">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-mono font-bold">Jam ke-{{ $slot->period_order }}</span>
                                            <span class="text-[11px] text-emerald-800 font-bold font-mono">{{ $slot->start_time }} - {{ $slot->end_time }}</span>
                                        </div>
                                        <h4 class="font-bold text-slate-800 text-xs sm:text-sm mt-1 leading-snug">{{ $slot->subject->name ?? 'Mata Pelajaran' }}</h4>
                                        <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            {{ $slot->teacher->name ?? 'Guru Pengampu' }}
                                        </p>
                                    </div>
                                    <form action="{{ route('admin.schedules.destroy', $slot) }}" method="POST" @submit.prevent="openConfirm('Hapus Slot Jadwal', 'Apakah Anda yakin ingin menghapus slot jadwal mata pelajaran ini?', () => $el.submit())">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="opacity-0 group-hover:opacity-100 p-1 text-slate-400 hover:text-red-600 transition" title="Hapus Slot">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="py-6 text-center text-slate-400 text-xs italic">
                                    Belum ada slot pelajaran hari {{ $day }}.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white p-12 text-center rounded-2xl border border-slate-200/80 text-slate-400 text-xs">
            Belum ada kelas yang dibuat. Silakan tambahkan kelas terlebih dahulu di menu Master Data Kelas.
        </div>
    @endif

    <!-- Modal Tambah Slot Jadwal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="addModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Tambah Slot Jadwal Pelajaran</h3>
            <form action="{{ route('admin.schedules.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $selectedClassroom->id ?? 1 }}">
                <input type="hidden" name="academic_year_id" value="{{ $activeYear->id ?? 1 }}">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Hari</label>
                        <select name="day_of_week" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                            @foreach($days as $d)
                                <option value="{{ $d }}">{{ ucfirst($d) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jam Ke-</label>
                        <input type="number" name="period_order" min="1" max="12" value="1" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jam Mulai</label>
                        <input type="text" name="start_time" placeholder="07:30" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jam Selesai</label>
                        <input type="text" name="end_time" placeholder="08:45" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Mata Pelajaran</label>
                    <select name="subject_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Guru Pengampu</label>
                    <select name="teacher_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-900">
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->position ?? 'Guru' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-bold shadow">Simpan Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
