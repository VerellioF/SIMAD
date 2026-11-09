@extends('layouts.portal')

@section('title', 'Portal Wali Murid')

@section('content')
<div class="space-y-6" x-data="{ activeStudentId: {{ $students->first()?->id ?? 'null' }} }">
    
    <!-- Welcome Header Banner -->
    <div class="bg-gradient-to-r from-amber-950 via-amber-900 to-amber-950 p-6 sm:p-8 rounded-3xl text-white shadow-lg border border-amber-500/30">
        <div class="space-y-1">
            <span class="inline-block px-3 py-1 rounded-full bg-amber-500 text-amber-950 text-xs font-bold uppercase tracking-wider">
                Portal Orang Tua / Wali Murid
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                Ahlan wa Sahlan, Bapak/Ibu {{ auth()->user()->name }}
            </h1>
            <p class="text-xs sm:text-sm text-amber-200">
                Pantau perkembangan kehadiran, nilai, ibadah harian, dan administrasi pendidikan putra-putri tercinta.
            </p>
        </div>
    </div>

    <!-- Multi-child Switcher Tabs (If parent has >1 children enrolled) -->
    @if($students->count() > 1)
        <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-2 overflow-x-auto">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider px-3 shrink-0">Pilih Ananda:</span>
            @foreach($students as $stTab)
                <button type="button" @click="activeStudentId = {{ $stTab->id }}"
                        :class="activeStudentId === {{ $stTab->id }} ? 'bg-amber-500 text-amber-950 font-extrabold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 shrink-0">
                    <span>{{ $stTab->gender === 'L' ? '👦' : '👧' }} {{ $stTab->nickname ?? $stTab->full_name }}</span>
                    <span class="text-[10px] opacity-80">({{ $stTab->classroom?->name ?? 'Rombel' }})</span>
                </button>
            @endforeach
        </div>
    @endif

    <!-- Student Cards Loop (for parents with 1 or multiple children) -->
    <div class="space-y-6">
        @forelse($students as $student)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6"
                 x-show="activeStudentId === {{ $student->id }}" x-cloak>
                
                <!-- Student Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xl border border-amber-200">
                            {{ substr($student->full_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">{{ $student->full_name }}</h2>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $student->gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $student->gender === 'L' ? 'Putra' : 'Putri' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">
                                NISN: {{ $student->nisn }} • NIS: {{ $student->nis ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="text-left sm:text-right">
                        <span class="text-xs text-slate-400 block">Kelas & Rombel:</span>
                        <span class="text-sm font-bold text-slate-800 block">{{ $student->classroom->name ?? 'Belum Ditentukan' }}</span>
                        @if($student->classroom?->homeroomTeacher)
                            <a href="https://wa.me/{{ $student->classroom->homeroomTeacher->phone_number }}" target="_blank" class="text-xs text-emerald-700 hover:underline inline-flex items-center gap-1 mt-0.5">
                                <span>Wali Kelas: {{ $student->classroom->homeroomTeacher->name }}</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
                    </div>
                </div>

                @php
                    $studentAttendances = $recentAttendances->where('student_id', $student->id);
                    $totalRec = $studentAttendances->count();
                    $hadirCount = $studentAttendances->where('status.value', 'hadir')->count();
                    $presenceRate = $totalRec > 0 ? round(($hadirCount / $totalRec) * 100) : 100;
                    
                    $latestTahfidz = $recentTahfidz->where('student_id', $student->id)->first();
                    $studentUnpaid = $unpaidBills->where('student_id', $student->id);
                @endphp

                <!-- 3 Pillars Dashboard Indicators -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Presensi Kehadiran -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Presensi Terakhir</span>
                        @if($totalRec > 0)
                            <span class="text-xl font-extrabold text-emerald-800 font-mono block">{{ $presenceRate }}% Hadir</span>
                            <span class="text-[11px] text-slate-500 block">{{ $hadirCount }} dari {{ $totalRec }} rekaman tatap muka</span>
                        @else
                            <span class="text-xl font-extrabold text-emerald-800 font-mono block">100% Hadir</span>
                            <span class="text-[11px] text-slate-500 block">Disiplin dan aktif mengikuti pembelajaran</span>
                        @endif
                    </div>

                    <!-- Mutaba'ah Tahfidz -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Setoran Hafalan Terakhir</span>
                        @if($latestTahfidz)
                            <span class="text-base font-extrabold text-amber-700 block truncate">
                                {{ $latestTahfidz->surah_name }} ({{ $latestTahfidz->grade->label() ?? 'Mumtaz' }})
                            </span>
                            <span class="text-[11px] text-slate-500 block">
                                Ayat {{ $latestTahfidz->verse_start }} - {{ $latestTahfidz->verse_end }} • {{ $latestTahfidz->created_at->diffForHumans() }}
                            </span>
                        @else
                            <span class="text-xl font-extrabold text-amber-700 block">Juz 30 (Juz 'Amma)</span>
                            <span class="text-[11px] text-slate-500 block">Mutaba'ah harian hafalan Al-Qur'an</span>
                        @endif
                    </div>

                    <!-- Status Administrasi SPP -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Status SPP Madrasah</span>
                        @if($studentUnpaid->isEmpty())
                            <span class="text-xl font-extrabold text-emerald-600 block">LUNAS</span>
                            <span class="text-[11px] text-slate-500 block">Alhamdulillah, tidak ada tunggakan</span>
                        @else
                            <span class="text-xl font-extrabold text-red-600 block">{{ $studentUnpaid->count() }} Tagihan Belum Lunas</span>
                            <span class="text-[11px] text-red-500 block">Harap konfirmasi pembayaran</span>
                        @endif
                    </div>
                </div>

                <!-- Aksi Cepat Wali Murid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                    <a href="{{ route('parent.attendance.index', ['student_id' => $student->id]) }}"
                       class="p-3.5 rounded-2xl bg-slate-50 hover:bg-amber-50/80 border border-slate-200/70 text-slate-800 transition flex flex-col gap-1 group">
                        <span class="text-lg">📅</span>
                        <span class="font-extrabold text-xs group-hover:text-amber-900">Rekap Presensi</span>
                        <span class="text-[10px] text-slate-400">Pantau kehadiran ananda</span>
                    </a>

                    <a href="{{ route('parent.grades.index', ['student_id' => $student->id]) }}"
                       class="p-3.5 rounded-2xl bg-slate-50 hover:bg-amber-50/80 border border-slate-200/70 text-slate-800 transition flex flex-col gap-1 group">
                        <span class="text-lg">📊</span>
                        <span class="font-extrabold text-xs group-hover:text-amber-900">Nilai & Rapor</span>
                        <span class="text-[10px] text-slate-400">Hasil asesmen belajar</span>
                    </a>

                    <a href="{{ route('parent.tuition.index') }}"
                       class="p-3.5 rounded-2xl bg-slate-50 hover:bg-amber-50/80 border border-slate-200/70 text-slate-800 transition flex flex-col gap-1 group">
                        <span class="text-lg">💳</span>
                        <span class="font-extrabold text-xs group-hover:text-amber-900">Tagihan SPP</span>
                        <span class="text-[10px] text-slate-400">Konfirmasi bukti transfer</span>
                    </a>

                    <a href="{{ route('parent.leave.index', ['student_id' => $student->id]) }}"
                       class="p-3.5 rounded-2xl bg-slate-50 hover:bg-amber-50/80 border border-slate-200/70 text-slate-800 transition flex flex-col gap-1 group">
                        <span class="text-lg">✉️</span>
                        <span class="font-extrabold text-xs group-hover:text-amber-900">Ajukan Izin</span>
                        <span class="text-[10px] text-slate-400">Kirim surat sakit/izin</span>
                    </a>
                </div>

                <!-- Rekening Pembayaran Infaq & SPP Info -->
                <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-200/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs">
                    <div class="space-y-0.5">
                        <span class="font-bold text-amber-950 block">Rekening Resmi Pembayaran SPP / Infaq:</span>
                        <p class="text-amber-900">Bank Syariah Indonesia (BSI) • No. Rek: <strong class="font-mono text-sm">7123456789</strong> (a.n. YAYASAN RAUDLATUL MUTA'ALLIMIN)</p>
                    </div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('simad.contact_phone')) }}?text=Assalamu'alaikum%20Admin%20TU,%20konfirmasi%20pembayaran%20SPP%20ananda%20{{ urlencode($student->full_name) }}"
                       target="_blank" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold transition whitespace-nowrap">
                        Konfirmasi Pembayaran (WA)
                    </a>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center space-y-3">
                <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Akun Belum Terhubung dengan Data Siswa</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Akun wali murid ini belum ditautkan ke data siswa aktif di buku induk madrasah. Silakan hubungi Staf Tata Usaha untuk verifikasi data kependudukan.
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
