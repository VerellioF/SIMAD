@extends('layouts.portal')

@section('title', 'Mutaba\'ah Ibadah Yaumiyah Ananda')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800">Mutaba'ah Ibadah Yaumiyah Ananda</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Jurnal pembiasaan ibadah di rumah: sholat 5 waktu, dhuha, tadarus Al-Qur'an, dan birrul walidain.</p>
        </div>

        @if($students->count() > 1)
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400">Pilih Ananda:</span>
            <div class="flex gap-1.5">
                @foreach($students as $st)
                <a href="{{ route('parent.worship.index', ['student_id' => $st->id, 'date' => $selectedDate]) }}"
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Checklist Form (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pb-4 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Form Pengisian Harian</span>
                    <h3 class="text-lg font-bold text-slate-900 mt-0.5">Jurnal Ibadah Ananda {{ $selectedStudent->full_name }}</h3>
                </div>
                <!-- Pilih Tanggal -->
                <form method="GET" action="{{ route('parent.worship.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                    <label class="text-xs font-semibold text-slate-500">Tanggal:</label>
                    <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white">
                </form>
            </div>

            <form action="{{ route('parent.worship.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">

                <!-- Sholat Fardhu 5 Waktu -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        Sholat Fardhu 5 Waktu
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        @foreach([
                            'subuh' => 'Subuh',
                            'dzuhur' => 'Dzuhur',
                            'ashar' => 'Ashar',
                            'maghrib' => 'Maghrib',
                            'isya' => 'Isya'
                        ] as $key => $label)
                        <label class="cursor-pointer border border-slate-200 rounded-xl p-3 flex flex-col items-center justify-center text-center hover:bg-emerald-50/50 transition has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-600 has-[:checked]:text-emerald-900">
                            <input type="checkbox" name="{{ $key }}" value="1" {{ $currentRecord && $currentRecord->$key ? 'checked' : '' }} class="w-5 h-5 rounded text-emerald-900 focus:ring-emerald-900 border-slate-300">
                            <span class="text-xs font-bold mt-2">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Ibadah Sunnah & Akhlak -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-gold-500"></span>
                        Ibadah Sunnah & Adab Terpuji
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="cursor-pointer border border-slate-200 rounded-xl p-3 flex items-center gap-3 hover:bg-amber-50/50 transition has-[:checked]:bg-amber-50 has-[:checked]:border-amber-500 has-[:checked]:text-amber-950">
                            <input type="checkbox" name="dhuha" value="1" {{ $currentRecord && $currentRecord->dhuha ? 'checked' : '' }} class="w-5 h-5 rounded text-amber-600 focus:ring-amber-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold block">Sholat Dhuha</span>
                                <span class="text-[10px] text-slate-400">Minimal 2 rakaat</span>
                            </div>
                        </label>

                        <label class="cursor-pointer border border-slate-200 rounded-xl p-3 flex items-center gap-3 hover:bg-amber-50/50 transition has-[:checked]:bg-amber-50 has-[:checked]:border-amber-500 has-[:checked]:text-amber-950">
                            <input type="checkbox" name="tadarus_quran" value="1" {{ $currentRecord && $currentRecord->tadarus_quran ? 'checked' : '' }} class="w-5 h-5 rounded text-amber-600 focus:ring-amber-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold block">Tadarus Al-Qur'an</span>
                                <span class="text-[10px] text-slate-400">Mengaji / Muroja'ah</span>
                            </div>
                        </label>

                        <label class="cursor-pointer border border-slate-200 rounded-xl p-3 flex items-center gap-3 hover:bg-amber-50/50 transition has-[:checked]:bg-amber-50 has-[:checked]:border-amber-500 has-[:checked]:text-amber-950">
                            <input type="checkbox" name="birrul_walidain" value="1" {{ $currentRecord && $currentRecord->birrul_walidain ? 'checked' : '' }} class="w-5 h-5 rounded text-amber-600 focus:ring-amber-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold block">Birrul Walidain</span>
                                <span class="text-[10px] text-slate-400">Membantu Orang Tua</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Catatan Orang Tua -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Catatan Orang Tua / Wali (Opsional)</label>
                    <input type="text" name="parent_notes" value="{{ $currentRecord->parent_notes ?? '' }}" placeholder="e.g. Ananda sholat subuh berjamaah di musholla..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-900">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition">
                        Simpan Mutaba'ah Hari Ini
                    </button>
                </div>
            </form>
        </div>

        <!-- Feedback & History (1 col) -->
        <div class="space-y-6">
            <!-- Catatan Asatidz -->
            <div class="bg-teal-50 border border-teal-200 rounded-2xl p-5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800">Umpan Balik Asatidz</span>
                <h4 class="text-sm font-extrabold text-teal-950 mt-1">Apresiasi & Catatan Guru</h4>
                <p class="text-xs text-teal-900 mt-2 leading-relaxed bg-white/70 p-3 rounded-xl border border-teal-100">
                    {{ $currentRecord->teacher_notes ?? 'Belum ada catatan khusus dari asatidz untuk tanggal ini. Terus dampingi ananda dengan penuh kasih sayang.' }}
                </p>
            </div>

            <!-- Recent 7 Days -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Riwayat 7 Hari Terakhir</h4>
                <div class="divide-y divide-slate-100">
                    @forelse($recentRecords as $rec)
                        <a href="{{ route('parent.worship.index', ['student_id' => $selectedStudent->id, 'date' => $rec->date->toDateString()]) }}" class="py-2.5 flex items-center justify-between text-xs hover:bg-slate-50 px-2 rounded-lg transition block">
                            <div>
                                <span class="font-bold text-slate-800">{{ $rec->date->isoFormat('dddd, D MMM') }}</span>
                                <span class="text-[10px] text-slate-400 block">
                                    {{ ($rec->subuh + $rec->dzuhur + $rec->ashar + $rec->maghrib + $rec->isya) }}/5 Waktu
                                </span>
                            </div>
                            <div class="flex items-center gap-1">
                                @if($rec->dhuha)<span class="w-1.5 h-1.5 rounded-full bg-gold-500" title="Dhuha"></span>@endif
                                @if($rec->tadarus_quran)<span class="w-1.5 h-1.5 rounded-full bg-emerald-600" title="Tadarus"></span>@endif
                                @if($rec->birrul_walidain)<span class="w-1.5 h-1.5 rounded-full bg-blue-500" title="Birrul Walidain"></span>@endif
                                <svg class="w-3.5 h-3.5 text-slate-300 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center italic">Belum ada riwayat tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
    @endif
</div>
@endsection
