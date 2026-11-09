@extends('layouts.portal')

@section('title', 'Pendaftaran Ekstrakurikuler Ananda')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Ekstrakurikuler Ananda</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftarkan dan pantau kegiatan pengembangan minat bakat ananda di madrasah.</p>
        </div>

        @if($students->count() > 1)
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400">Pilih Ananda:</span>
            <div class="flex gap-1.5">
                @foreach($students as $st)
                <a href="{{ route('parent.ekskul.index', ['student_id' => $st->id]) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $selectedStudent && $selectedStudent->id == $st->id ? 'bg-amber-500 text-emerald-950 shadow-sm' : 'bg-slate-100 text-slate-600' }}">
                    {{ $st->nickname ?? $st->full_name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($selectedStudent)
    <!-- Section 1: Ekskul yang Sedang Diikuti Ananda -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-emerald-850 uppercase tracking-wider">Status Partisipasi</span>
                <h3 class="text-base font-extrabold text-slate-900 mt-0.5">Ekstrakurikuler yang Diikuti Ananda {{ $selectedStudent->full_name }}</h3>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-900 font-bold text-xs">{{ $enrollments->count() }} Kegiatan Aktif</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($enrollments as $enr)
                <div class="border border-emerald-200 rounded-2xl p-4 bg-emerald-50/30 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-850">
                                {{ str_replace('_', ' ', ucfirst($enr->extracurricular->category)) }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800">Aktif</span>
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm">{{ $enr->extracurricular->name }}</h4>
                        <p class="text-xs text-slate-500 mt-1">Pembina: {{ $enr->extracurricular->instructor_name }}</p>
                        <p class="text-xs text-emerald-800 font-medium mt-1">
                            {{ $enr->extracurricular->schedule_day }}, {{ $enr->extracurricular->schedule_time }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-emerald-100 flex justify-end">
                        <form action="{{ route('parent.ekskul.cancel', $enr) }}" method="POST" @submit.prevent="openConfirm('Batalkan Ekskul', 'Apakah Anda yakin ingin membatalkan keikutsertaan ananda pada ekskul ini?', () => $el.submit())">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-semibold hover:underline">
                                Batalkan Keikutsertaan
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-8 text-center text-slate-400 text-xs italic">
                    Ananda belum terdaftar pada kegiatan ekstrakurikuler manapun semester ini. Silakan pilih kegiatan di bawah.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 2: Katalog Ekskul Tersedia -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="pb-3 border-b border-slate-100">
            <span class="text-xs font-bold text-gold-600 uppercase tracking-wider">Katalog Madrasah</span>
            <h3 class="text-base font-extrabold text-slate-900 mt-0.5">Pilihan Ekstrakurikuler Tersedia</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableEkskuls as $ekskul)
                @php
                    $isEnrolled = in_array($ekskul->id, $enrolledIds);
                @endphp
                <div class="border border-slate-200 rounded-2xl p-5 hover:border-emerald-300 transition flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                @if($ekskul->category == 'keagamaan') bg-emerald-100 text-emerald-800
                                @elseif($ekskul->category == 'olahraga') bg-blue-100 text-blue-800
                                @elseif($ekskul->category == 'seni_budaya') bg-purple-100 text-purple-800
                                @elseif($ekskul->category == 'kepanduan') bg-amber-100 text-amber-800
                                @else bg-cyan-100 text-cyan-800 @endif">
                                {{ str_replace('_', ' ', ucfirst($ekskul->category)) }}
                            </span>
                            @if($isEnrolled)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Terdaftar</span>
                            @endif
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm">{{ $ekskul->name }}</h4>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $ekskul->description }}</p>
                        
                        <div class="mt-3 text-xs text-slate-600 space-y-0.5">
                            <p>Pembina: <span class="font-semibold">{{ $ekskul->instructor_name }}</span></p>
                            <p>Jadwal: <span class="text-emerald-850 font-semibold">{{ $ekskul->schedule_day }}, {{ $ekskul->schedule_time }}</span></p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100">
                        @if($isEnrolled)
                            <button disabled class="w-full py-2 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs cursor-not-allowed">
                                Sudah Terdaftar
                            </button>
                        @else
                            <form action="{{ route('parent.ekskul.enroll') }}" method="POST">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                                <input type="hidden" name="extracurricular_id" value="{{ $ekskul->id }}">
                                <input type="hidden" name="academic_year_id" value="{{ $activeYear->id ?? 1 }}">
                                <button type="submit" class="w-full py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow-sm transition">
                                    Daftarkan Ananda
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
