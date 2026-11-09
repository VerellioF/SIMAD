<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leger Nilai Kelas {{ $classroom->name }} - MI Plus Raudlatul Muta'allimin</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            @page { size: landscape; margin: 1cm; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans p-4 sm:p-8">

    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="no-print max-w-7xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.grades.index', ['classroom_id' => $classroom->id]) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                &larr; Kembali ke Buku Nilai
            </a>
            <div>
                <h1 class="text-sm font-extrabold text-slate-900">Buku Leger Nilai Hasil Belajar Siswa</h1>
                <p class="text-[11px] text-slate-500">Rombel: <strong>{{ $classroom->name }}</strong> • T.A. {{ $activeYear->name ?? '2026/2027' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}"
               class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-1.5 transition">
                <span>📥 Unduh CSV / Excel</span>
            </a>
            <button onclick="window.print()"
                    class="px-5 py-2 rounded-xl bg-emerald-900 hover:bg-emerald-800 text-white font-bold text-xs flex items-center gap-1.5 shadow transition">
                <span>🖨️ Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Document Sheet -->
    <div class="max-w-7xl mx-auto bg-white p-8 sm:p-10 rounded-3xl shadow-md border border-slate-200/80 print:shadow-none print:border-none print:p-0">
        
        <!-- Header Madrasah -->
        <div class="border-b-2 border-emerald-950 pb-4 mb-6 text-center">
            <div class="flex items-center justify-center gap-4 mb-2">
                <img src="{{ asset('images/logo-madrasah.svg') }}" alt="Logo" class="w-14 h-14 object-contain">
                <div>
                    <h2 class="text-sm font-bold text-emerald-950 uppercase tracking-widest">KEMENTERIAN AGAMA REPUBLIK INDONESIA</h2>
                    <h3 class="text-xl font-black text-emerald-950 tracking-tight">MI PLUS RAUDLATUL MUTA'ALLIMIN KALIPUCUNG</h3>
                    <p class="text-[11px] text-slate-600">NSM: 111235050000 • NPSN: 60700000 • Akreditasi Madrasah Kemenag RI</p>
                    <p class="text-[10px] text-slate-500">Jl. Kalipucung No. 45, Kec. Sanankulon, Kabupaten Blitar, Jawa Timur 66151</p>
                </div>
            </div>
            <div class="inline-block mt-2 px-6 py-1 rounded-full bg-emerald-900 text-amber-400 font-bold text-xs tracking-wider uppercase">
                LEGER NILAI HASIL BELAJAR SISWA (RAPORT SEMESTER)
            </div>
        </div>

        <!-- Metadata Rombel -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs mb-6 pb-4 border-b border-slate-100">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Rombel / Kelas</span>
                <span class="font-extrabold text-slate-900">{{ $classroom->name }} (Tingkat {{ $classroom->grade_level }})</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Wali Kelas</span>
                <span class="font-extrabold text-slate-900">{{ $classroom->homeroomTeacher->name ?? auth()->user()->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Tahun Pelajaran</span>
                <span class="font-extrabold text-slate-900">{{ $activeYear->name ?? '2026/2027' }} (Ganjil)</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Jumlah Siswa</span>
                <span class="font-extrabold text-slate-900 font-mono">{{ $students->count() }} Siswa</span>
            </div>
        </div>

        <!-- Table Matrix Leger -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-[11px]">
                <thead>
                    <tr class="bg-emerald-950 text-white text-center">
                        <th class="p-2 border border-emerald-900 w-8">No</th>
                        <th class="p-2 border border-emerald-900 w-20">NIS / NISN</th>
                        <th class="p-2 border border-emerald-900 text-left min-w-[140px]">Nama Lengkap Siswa</th>
                        <th class="p-2 border border-emerald-900 w-10">L/P</th>
                        @foreach($subjects as $sub)
                            <th class="p-2 border border-emerald-900 min-w-[65px]" title="{{ $sub->name }}">
                                <span class="block font-bold text-[10px] truncate max-w-[80px]">{{ $sub->code ?? $sub->name }}</span>
                            </th>
                        @endforeach
                        <th class="p-2 border border-emerald-900 w-16 bg-emerald-900 font-bold">Total</th>
                        <th class="p-2 border border-emerald-900 w-16 bg-emerald-900 font-bold">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $idx => $st)
                        @php
                            $row = $matrix[$st->id] ?? ['scores' => [], 'total' => 0, 'average' => null];
                        @endphp
                        <tr class="hover:bg-slate-50 {{ $idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/60' }}">
                            <td class="p-2 border border-slate-200 text-center font-mono">{{ $idx + 1 }}</td>
                            <td class="p-2 border border-slate-200 font-mono text-[10px] text-center">
                                <span class="block font-bold text-slate-800">{{ $st->nis ?? '-' }}</span>
                                <span class="block text-slate-400 text-[9px]">{{ $st->nisn }}</span>
                            </td>
                            <td class="p-2 border border-slate-200 font-bold text-slate-900">
                                {{ $st->full_name }}
                            </td>
                            <td class="p-2 border border-slate-200 text-center font-bold {{ $st->gender === 'L' ? 'text-blue-700' : 'text-pink-700' }}">
                                {{ $st->gender }}
                            </td>
                            @foreach($subjects as $sub)
                                @php
                                    $score = $row['scores'][$sub->id] ?? null;
                                @endphp
                                <td class="p-2 border border-slate-200 text-center font-mono font-bold {{ $score !== null ? ($score >= 75 ? 'text-slate-800' : 'text-amber-700') : 'text-slate-300' }}">
                                    {{ $score !== null ? $score : '-' }}
                                </td>
                            @endforeach
                            <td class="p-2 border border-slate-200 text-center font-mono font-black text-emerald-950 bg-emerald-50/50">
                                {{ $row['total'] > 0 ? $row['total'] : '-' }}
                            </td>
                            <td class="p-2 border border-slate-200 text-center font-mono font-black text-emerald-800 bg-emerald-50/80">
                                {{ $row['average'] !== null ? $row['average'] : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 6 + $subjects->count() }}" class="p-8 text-center text-slate-400">
                                Belum ada siswa aktif terdaftar di rombel kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan Resmi Pengesahan Leger -->
        <div class="mt-12 pt-6 grid grid-cols-2 text-xs">
            <div class="text-center">
                <p class="text-slate-500">Mengetahui,</p>
                <p class="font-bold text-slate-800">Kepala Madrasah</p>
                <div class="h-20"></div>
                <p class="font-extrabold text-slate-900 underline">K.H. Nur Hadi, M.Pd.I.</p>
                <p class="text-[10px] text-slate-500 font-mono">NIP. 197508152005011002</p>
            </div>
            <div class="text-center">
                <p class="text-slate-500">Blitar, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-800">Wali Kelas {{ $classroom->name }}</p>
                <div class="h-20"></div>
                <p class="font-extrabold text-slate-900 underline">{{ $classroom->homeroomTeacher->name ?? auth()->user()->name }}</p>
                <p class="text-[10px] text-slate-500 font-mono">NIP / NUPTK. -</p>
            </div>
        </div>

    </div>

</body>
</html>
