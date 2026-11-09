@extends('layouts.portal')

@section('title', 'Kurikulum & Capaian Belajar Ananda')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Kurikulum & Capaian Belajar Ananda</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Struktur pembelajaran integratif: Kemenag PAI, Kurikulum Merdeka Nasional, dan Muatan Lokal Pesantren Plus.</p>
        </div>

        @if($students->count() > 1)
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400">Pilih Ananda:</span>
            <div class="flex gap-1.5">
                @foreach($students as $st)
                <a href="{{ route('parent.curriculum.index', ['student_id' => $st->id]) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $selectedStudent && $selectedStudent->id == $st->id ? 'bg-amber-500 text-emerald-950 shadow-sm' : 'bg-slate-100 text-slate-600' }}">
                    {{ $st->nickname ?? $st->full_name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @if($selectedStudent)
    <!-- Info Ringkasan Fase Belajar Siswa -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Fase Aktif -->
        <div class="bg-gradient-to-tr from-emerald-950 to-emerald-900 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Fase Pembelajaran Siswa</span>
                <span class="block text-2xl font-black mt-1 text-white">
                    @if($phase === 'fase_a') Fase A (Kelas 1-2)
                    @elseif($phase === 'fase_b') Fase B (Kelas 3-4)
                    @else Fase C (Kelas 5-6) @endif
                </span>
            </div>
            <div class="mt-4 pt-3 border-t border-emerald-800/80 flex items-center justify-between text-xs text-emerald-200">
                <span>Kelas:</span>
                <strong class="text-amber-400">{{ $selectedStudent->classroom?->name ?? 'Belum ditentukan' }}</strong>
            </div>
        </div>

        <!-- Beban Belajar Perminggu -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Jam Pelajaran</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-3xl font-black text-slate-800 font-mono">{{ $totalJp }}</span>
                <span class="text-xs font-bold text-slate-500">JP / pekan</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">1 Jam Pelajaran = 35 menit tatap muka efektif.</p>
        </div>

        <!-- 3 Pilar Kurikulum -->
        <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Model Pembelajaran</span>
            <span class="block text-base font-extrabold text-emerald-900 mt-1">Integrasi 3 Pilar</span>
            <p class="text-[11px] text-slate-500 mt-2">Kemenag, Kurikulum Merdeka, dan Karakter Madrasah Berakhlak Mulia.</p>
        </div>

        <!-- Target Capaian -->
        <div class="bg-white border border-amber-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Target Tahfidz Utama</span>
            <span class="block text-base font-extrabold text-amber-900 mt-1">
                @if($phase === 'fase_a') Juz 30 (An-Naas - An-Naba')
                @elseif($phase === 'fase_b') Juz 29 & Muroja'ah Juz 30
                @else Juz 28 & Tahsin Tartil Matan @endif
            </span>
            <p class="text-[11px] text-slate-500 mt-2">Dikuatkan setoran harian di madrasah & binaan wali.</p>
        </div>
    </div>

    <!-- Rincian Kurikulum Per Pilar -->
    <div class="space-y-6">
        @php
            $pillarMeta = [
                'kemenag_pai' => [
                    'title' => '1. Pilar PAI & Bahasa Arab (Kemenag RI)',
                    'desc' => 'Membangun pondasi aqidah shohihah, ibadah praktis, sejarah Islam, dan kefasihan Bahasa Al-Qur\'an.',
                    'badge' => 'Kekhasan Madrasah',
                    'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'header_bg' => 'bg-emerald-50/60',
                ],
                'nasional_merdeka' => [
                    'title' => '2. Pilar Kurikulum Merdeka Nasional (Kemendikbudristek)',
                    'desc' => 'Literasi mendalam, numerasi aplikatif, wawasan sains-lingkungan, dan penguatan karakter Profil Pelajar Pancasila.',
                    'badge' => 'Standar Nasional',
                    'badge_class' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'header_bg' => 'bg-blue-50/60',
                ],
                'muatan_lokal_pesantren' => [
                    'title' => '3. Pilar Muatan Lokal & Keagamaan Plus',
                    'desc' => 'Kaderisasi siswa beradab melalui Tahfidz talaqqi bersanad, aksara Pegon, akhlakul karimah, dan kemandirian.',
                    'badge' => 'Keunggulan Plus MIRM',
                    'badge_class' => 'bg-amber-100 text-amber-900 border-amber-200',
                    'header_bg' => 'bg-amber-50/60',
                ],
            ];
        @endphp

        @foreach($pillarMeta as $typeKey => $meta)
            @php
                $items = $curriculums->get($typeKey, collect());
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200/80 {{ $meta['header_bg'] }} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-extrabold text-slate-800">{{ $meta['title'] }}</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $meta['badge_class'] }}">
                                {{ $meta['badge'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">{{ $meta['desc'] }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-slate-500">Subtotal JP:</span>
                        <span class="text-sm font-black text-slate-800 font-mono ml-1">{{ $items->sum('hours_per_week') }} JP</span>
                    </div>
                </div>

                @if($items->isEmpty())
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada mata pelajaran yang didaftarkan pada pilar ini untuk {{ $phase === 'fase_a' ? 'Fase A' : ($phase === 'fase_b' ? 'Fase B' : 'Fase C') }}.
                </div>
                @else
                <div class="divide-y divide-slate-100">
                    @foreach($items as $curr)
                    <div class="p-4 sm:p-5 hover:bg-slate-50/60 transition">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-3">
                            <div class="space-y-1 md:w-2/3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-mono text-[11px] font-bold">
                                        {{ $curr->code ?? $curr->subject?->code ?? 'MAPEL' }}
                                    </span>
                                    <h3 class="text-sm font-bold text-slate-800">{{ $curr->name }}</h3>
                                    <span class="text-[11px] text-slate-400">({{ $curr->hours_per_week }} JP/pekan)</span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed pt-1">
                                    <strong class="text-slate-700">Fokus Capaian Belajar:</strong>
                                    {{ $curr->competency_goals ?? 'Pembelajaran mengacu silabus nasional madrasah ibtidaiyah terakreditasi.' }}
                                </p>
                            </div>
                            <div class="md:w-1/3 flex flex-col md:items-end justify-center bg-slate-50 md:bg-transparent p-3 md:p-0 rounded-xl">
                                <span class="text-[11px] font-bold text-slate-500">Pendampingan Orang Tua:</span>
                                <p class="text-[11px] text-slate-600 text-left md:text-right mt-0.5">
                                    Memeriksa buku mutaba'ah & memastikan ananda mengulang muraja'ah 15 menit ba'da maghrib.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Tips Sinergi Wali & Madrasah -->
    <div class="bg-emerald-950 text-white p-6 rounded-2xl shadow-sm relative overflow-hidden">
        <div class="relative z-10 max-w-3xl">
            <h3 class="text-base font-black text-amber-400 mb-2">Sinergi Pembelajaran: Kunci Keberkahan Belajar Siswa</h3>
            <p class="text-xs text-emerald-100 leading-relaxed">
                Madrasah memadukan ilmu fardhu 'ain (aqidah, ibadah, tahfidz) dengan fardhu kifayah (sains, logika matematika, bahasa, teknologi). Dukungan terbaik Ayah/Bunda adalah menciptakan suasana rumah yang meneladani adab belajar, membatasi gawai hiburan, serta rutin mendoakan putra-putri di sepertiga malam.
            </p>
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/80 shadow-sm">
        <p class="text-sm text-slate-500">Data siswa belum dikaitkan dengan akun orang tua / wali murid ini.</p>
    </div>
    @endif
</div>
@endsection
