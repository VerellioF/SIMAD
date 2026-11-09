@extends('layouts.portal')

@section('title', 'Presensi Siswa Berbasis Jadwal Mengajar')

@section('content')
<div class="space-y-6" x-data="{
    filterQuery: '',
    setAll(status) {
        document.querySelectorAll('input[type=radio][value=' + status + ']').forEach(el => {
            el.checked = true;
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }
}">

    <!-- Header & Schedule Switcher -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Jadwal KBM Aktif
                </span>
                <span class="text-xs text-slate-400 capitalize">Hari {{ $dayName }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">Presensi Harian Siswa & Jadwal Mengajar <span class="sr-only">Presensi Harian Santri</span></h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pencatatan presensi spesifik per mata pelajaran, rombel kelas, dan jam tatap muka sesuai jadwal mengajar Anda.
            </p>
        </div>

        <!-- Date & Schedule Form Selector -->
        <form action="{{ route('teacher.attendance.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <!-- Pilih Tanggal -->
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                   class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white focus:ring-2 focus:ring-emerald-800">

            <!-- Pilih Jadwal Mengajar -->
            <select name="schedule_id" onchange="this.form.submit()"
                    class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-emerald-800">
                @if($todaySchedules->isNotEmpty())
                    <optgroup label="Jadwal Hari Ini ({{ ucfirst($dayName) }})">
                        @foreach($todaySchedules as $sch)
                            <option value="{{ $sch->id }}" {{ $selectedScheduleId == (string)$sch->id ? 'selected' : '' }}>
                                [{{ $sch->start_time }}] {{ $sch->classroom?->name }} • {{ $sch->subject?->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endif

                @if($mySchedules->isNotEmpty())
                    <optgroup label="Semua Jadwal Mengajar Guru">
                        @foreach($mySchedules as $sch)
                            <option value="{{ $sch->id }}" {{ $selectedScheduleId == (string)$sch->id ? 'selected' : '' }}>
                                {{ ucfirst($sch->day_of_week) }} {{ $sch->start_time }} • {{ $sch->classroom?->name }} - {{ $sch->subject?->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endif

                @if($homeroomClassroom)
                    <optgroup label="Wali Kelas">
                        <option value="homeroom" {{ $selectedScheduleId === 'homeroom' ? 'selected' : '' }}>
                            Presensi Harian Wali Kelas ({{ $homeroomClassroom->name }})
                        </option>
                    </optgroup>
                @endif
            </select>
        </form>
    </div>

    <!-- Info Sesi & Quick Tools -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Banner Sesi Mengajar Aktif -->
        <div class="md:col-span-2 bg-gradient-to-tr from-emerald-950 via-emerald-900 to-emerald-950 text-white rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">
                    @if($activeSchedule)
                        Sesi Pembelajaran • {{ ucfirst($activeSchedule->day_of_week) }} ({{ $activeSchedule->start_time }} - {{ $activeSchedule->end_time }})
                    @else
                        Presensi Harian Rombel
                    @endif
                </span>
                <h2 class="text-xl font-black text-white">
                    {{ $classroom?->name ?? 'Belum ada kelas terpilih' }}
                </h2>
                <p class="text-xs text-emerald-200">
                    Mata Pelajaran: <strong class="text-white">{{ $subject?->name ?? 'Presensi Umum Rombel' }}</strong>
                    @if($subject?->code) <span class="text-amber-400 font-mono">({{ $subject->code }})</span> @endif
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-emerald-800/80 flex items-center justify-between text-xs text-emerald-200">
                <span>Tanggal Presensi:</span>
                <strong class="text-amber-400">{{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}</strong>
            </div>
        </div>

        <!-- Ringkasan Kehadiran Sesi -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rekapitulasi Sesi</span>
            <div class="grid grid-cols-4 gap-2 text-center mt-2">
                <div class="bg-emerald-50 text-emerald-900 rounded-xl p-2 border border-emerald-200">
                    <span class="text-lg font-black font-mono block">{{ $summary['hadir'] }}</span>
                    <span class="text-[10px] font-bold uppercase">Hadir</span>
                </div>
                <div class="bg-amber-50 text-amber-900 rounded-xl p-2 border border-amber-200">
                    <span class="text-lg font-black font-mono block">{{ $summary['sakit'] }}</span>
                    <span class="text-[10px] font-bold uppercase">Sakit</span>
                </div>
                <div class="bg-blue-50 text-blue-900 rounded-xl p-2 border border-blue-200">
                    <span class="text-lg font-black font-mono block">{{ $summary['izin'] }}</span>
                    <span class="text-[10px] font-bold uppercase">Izin</span>
                </div>
                <div class="bg-rose-50 text-rose-900 rounded-xl p-2 border border-rose-200">
                    <span class="text-lg font-black font-mono block">{{ $summary['alpa'] }}</span>
                    <span class="text-[10px] font-bold uppercase">Alpa</span>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 text-center">Total: {{ $summary['total'] }} siswa terdaftar</p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-center gap-2.5">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fitur Cepat Presensi</span>
            <button type="button" @click="setAll('hadir')"
                    class="w-full py-2.5 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-bold text-xs border border-emerald-300 transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Tandai Semua Hadir</span>
            </button>
            <div class="flex gap-2">
                <button type="button" @click="setAll('sakit')" class="flex-1 py-1.5 rounded-xl bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-900 text-[11px] font-bold transition">Set Sakit</button>
                <button type="button" @click="setAll('izin')" class="flex-1 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-900 text-[11px] font-bold transition">Set Izin</button>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Alert Notifikasi WhatsApp Ketidakhadiran Siswa -->
    @if(session('wa_alerts') && count(session('wa_alerts')) > 0)
        <div class="p-5 rounded-3xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-2xl bg-amber-100 text-amber-700">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    </span>
                    <div>
                        <h4 class="font-black text-sm text-slate-900">Notifikasi WhatsApp Ketidakhadiran Siswa</h4>
                        <p class="text-xs text-amber-800">Terdapat {{ count(session('wa_alerts')) }} siswa berstatus Alpa / Sakit / Izin. Anda dapat langsung mengabari orang tua/wali murid dengan 1 klik:</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 pt-2 border-t border-amber-200/80">
                @foreach(session('wa_alerts') as $waAlert)
                    <div class="bg-white p-3 rounded-2xl border border-amber-200/70 shadow-2xs flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <span class="block text-xs font-bold text-slate-900 truncate">{{ $waAlert['student_name'] }}</span>
                            <span class="block text-[10px] uppercase font-bold {{ $waAlert['status'] === 'alpa' ? 'text-rose-600' : 'text-amber-600' }}">
                                Status: {{ $waAlert['status'] }}
                            </span>
                        </div>
                        @if($waAlert['is_sent'])
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-[10px] font-bold">Otomatis Terkirim</span>
                        @elseif($waAlert['wa_url'])
                            <a href="{{ $waAlert['wa_url'] }}" target="_blank"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow transition shrink-0">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>Kirim WA</span>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(!$classroom)
        <div class="bg-amber-50 border border-amber-200 rounded-3xl p-8 text-amber-900 text-xs text-center">
            <p class="font-bold text-base mb-1">Jadwal Mengajar Belum Ditemukan</p>
            <p>Akun Anda belum memiliki plotting jadwal mengajar aktif di rombel madrasah. Silakan hubungi bagian Kurikulum atau Staf TU.</p>
        </div>
    @else
        <!-- Form Presensi Roster -->
        <form action="{{ route('teacher.attendance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
            <input type="hidden" name="subject_id" value="{{ $subject?->id }}">
            <input type="hidden" name="class_schedule_id" value="{{ $activeSchedule?->id }}">

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">
                            Roster Siswa: {{ $classroom->name }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Mapel: <strong>{{ $subject?->name ?? 'Presensi Umum' }}</strong> • Klik status kehadiran tiap siswa lalu klik simpan.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="text" x-model="filterQuery" placeholder="Cari nama siswa..."
                               class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800">

                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-emerald-950 hover:bg-emerald-900 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 whitespace-nowrap">
                            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Presensi</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-6 w-12 text-center">No</th>
                                <th class="py-3 px-6">NISN / Nama Siswa</th>
                                <th class="py-3 px-6 text-center">Status Kehadiran</th>
                                <th class="py-3 px-6">Catatan Sesi</th>
                                <th class="py-3 px-4 text-center">WA Wali</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $index => $st)
                                @php
                                    $existing = $existingAttendances->get($st->id);
                                    $currentStatus = old("attendances.{$index}.status", $existing?->status->value ?? 'hadir');
                                    $notes = old("attendances.{$index}.notes", $existing?->notes ?? '');
                                    $waMsg = app(\App\Services\WhatsAppNotificationService::class)->buildAttendanceAlert($st, $currentStatus, $date, $subject?->name, $notes);
                                    $directWaUrl = $st->parent?->phone_number ? app(\App\Services\WhatsAppNotificationService::class)->generateClickToChatUrl($st->parent->phone_number, $waMsg) : null;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition"
                                    x-show="!filterQuery || '{{ strtolower($st->full_name) }}'.includes(filterQuery.toLowerCase())">
                                    <td class="py-3.5 px-6 text-center font-mono text-slate-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $st->id }}">
                                        <span class="font-bold text-slate-900 block">{{ $st->full_name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $st->nisn ?? $st->nis }} ({{ $st->gender == 'L' ? 'Laki-laki' : 'Perempuan' }})</span>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Hadir (H) -->
                                            <label class="cursor-pointer">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="hadir"
                                                       class="peer sr-only" {{ $currentStatus === 'hadir' ? 'checked' : '' }}>
                                                <span class="px-3 py-1.5 rounded-xl border text-xs font-bold transition peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-700 bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                                    Hadir
                                                </span>
                                            </label>

                                            <!-- Sakit (S) -->
                                            <label class="cursor-pointer">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="sakit"
                                                       class="peer sr-only" {{ $currentStatus === 'sakit' ? 'checked' : '' }}>
                                                <span class="px-3 py-1.5 rounded-xl border text-xs font-bold transition peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-600 bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                                    Sakit
                                                </span>
                                            </label>

                                            <!-- Izin (I) -->
                                            <label class="cursor-pointer">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="izin"
                                                       class="peer sr-only" {{ $currentStatus === 'izin' ? 'checked' : '' }}>
                                                <span class="px-3 py-1.5 rounded-xl border text-xs font-bold transition peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-700 bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                                    Izin
                                                </span>
                                            </label>

                                            <!-- Alpa (A) -->
                                            <label class="cursor-pointer">
                                                <input type="radio" name="attendances[{{ $index }}][status]" value="alpa"
                                                       class="peer sr-only" {{ $currentStatus === 'alpa' ? 'checked' : '' }}>
                                                <span class="px-3 py-1.5 rounded-xl border text-xs font-bold transition peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-700 bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                                    Alpa
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <input type="text" name="attendances[{{ $index }}][notes]"
                                               value="{{ $notes }}"
                                               placeholder="Catatan / keterangan (opsional)..."
                                               class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800">
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($directWaUrl)
                                            <a href="{{ $directWaUrl }}" target="_blank"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-[11px] border border-emerald-200 transition"
                                               title="Kirim Catatan Presensi ke WhatsApp Wali: {{ $st->parent?->name }} ({{ $st->parent?->phone_number }})">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                <span>WA Wali</span>
                                            </a>
                                        @else
                                            <span class="text-[10px] text-slate-300 font-mono">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-400 text-xs">
                                        Belum ada siswa terdaftar di rombel kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        Pastikan memeriksa kembali presensi sebelum menyimpan rekap data.
                    </span>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-emerald-950 hover:bg-emerald-900 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Presensi</span>
                    </button>
                </div>
            </div>
        </form>
    @endif

</div>
@endsection
