<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Belajar (Rapor) - {{ $student->full_name }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.2cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #1a202c;
        }
        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #0d3b2e;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h2 {
            margin: 0;
            font-size: 13pt;
            color: #0d3b2e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h3 {
            margin: 2px 0;
            font-size: 10pt;
            font-weight: normal;
            color: #2d3748;
        }
        .header-title p {
            margin: 0;
            font-size: 8pt;
            color: #718096;
        }
        .doc-title {
            text-align: center;
            margin: 10px 0 12px 0;
        }
        .doc-title h1 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0d3b2e;
        }
        .doc-title p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            color: #4a5568;
        }
        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        table.meta-table td {
            padding: 2.5px 4px;
            vertical-align: top;
        }
        .section-header {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #edf2f7;
            padding: 4px 6px;
            margin-top: 10px;
            margin-bottom: 6px;
            border-left: 3px solid #0d3b2e;
        }
        table.grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8.5pt;
        }
        table.grid-table th, table.grid-table td {
            border: 1px solid #cbd5e0;
            padding: 4px 5px;
            vertical-align: middle;
        }
        table.grid-table th {
            background-color: #f7fafc;
            color: #2d3748;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .category-row {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            padding: 4px 6px;
        }

        .summary-box {
            border: 1px solid #cbd5e0;
            padding: 6px 8px;
            margin-bottom: 8px;
            font-size: 8.5pt;
            background-color: #fafaf9;
        }

        .signatures-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .signatures-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            padding: 0 5px;
        }
        .sig-space {
            height: 55px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Header Kop Surat -->
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: center; vertical-align: middle;">
                <img src="{{ public_path('images/logo-madrasah-pdf.svg') }}" style="width: 65px; height: auto;" alt="Logo">
            </td>
            <td class="header-title" style="width: 85%;">
                <p style="font-size: 8.5pt; letter-spacing: 1px;">KEMENTERIAN AGAMA REPUBLIK INDONESIA</p>
                <h2>{{ $school['name'] }}</h2>
                <h3>STATUS TERAKREDITASI | NSM: {{ $school['nsm'] }} | NPSN: {{ $school['npsn'] }}</h3>
                <p>{{ $school['address'] }}</p>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h1>Laporan Hasil Belajar Siswa (Rapor Semester)</h1>
        <p>Tahun Ajaran {{ $year?->name ?? '2026/2027' }} - Semester {{ ucfirst($year?->semester?->value ?? (string)($year?->semester ?? 'Ganjil')) }}</p>
    </div>

    <!-- Identitas Siswa -->
    <table class="meta-table">
        <tr>
            <td style="width: 18%; color: #4a5568;">Nama Peserta Didik</td>
            <td style="width: 2%;">:</td>
            <td style="width: 45%; font-weight: bold;">{{ $student->full_name }}</td>
            <td style="width: 15%; color: #4a5568;">Kelas / Rombel</td>
            <td style="width: 2%;">:</td>
            <td style="width: 18%; font-weight: bold;">{{ $student->classroom?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="color: #4a5568;">NIS / NISN</td>
            <td>:</td>
            <td style="font-family: monospace;">{{ $student->nis }} / {{ $student->nisn }}</td>
            <td style="color: #4a5568;">Wali Kelas</td>
            <td>:</td>
            <td>{{ $student->classroom?->homeroomTeacher?->name ?? 'Bapak/Ibu Guru Kelas' }}</td>
        </tr>
        <tr>
            <td style="color: #4a5568;">Tempat, Tgl Lahir</td>
            <td>:</td>
            <td>{{ $student->birth_place }}, {{ $student->birth_date?->format('d/m/Y') }}</td>
            <td style="color: #4a5568;">Fase / Tingkat</td>
            <td>:</td>
            <td>Fase {{ $student->classroom?->grade_level <= 2 ? 'A (Kelas 1-2)' : ($student->classroom?->grade_level <= 4 ? 'B (Kelas 3-4)' : 'C (Kelas 5-6)') }}</td>
        </tr>
    </table>

    <!-- A. Capaian Nilai Akademik -->
    <div class="section-header">A. Capaian Kompetensi Mata Pelajaran</div>
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 32%;">Mata Pelajaran</th>
                <th style="width: 9%;">Tugas/UH</th>
                <th style="width: 9%;">PTS</th>
                <th style="width: 9%;">PAS</th>
                <th style="width: 10%;">Nilai Akhir</th>
                <th style="width: 6%;">Predikat</th>
                <th style="width: 20%;">Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach(['agama' => 'Kelompok A: Pendidikan Agama Islam & Budi Pekerti', 'umum' => 'Kelompok B: Mata Pelajaran Umum', 'muatan_lokal' => 'Kelompok C: Muatan Lokal Madrasah'] as $catKey => $catLabel)
                @if(isset($subjectReport[$catKey]) && count($subjectReport[$catKey]) > 0)
                    <tr>
                        <td colspan="8" class="category-row">{{ $catLabel }}</td>
                    </tr>
                    @foreach($subjectReport[$catKey] as $item)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td class="font-bold">{{ $item['subject']->name }}</td>
                            <td class="text-center">{{ $item['harian'] }}</td>
                            <td class="text-center">{{ $item['pts'] }}</td>
                            <td class="text-center">{{ $item['pas'] }}</td>
                            <td class="text-center font-bold" style="background-color: #f8fafc;">
                                {{ $item['final'] }}
                            </td>
                            <td class="text-center font-bold">{{ $item['predicate'] }}</td>
                            <td style="font-size: 8pt; color: #334155;">{{ $item['description'] }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- B. Capaian Khusus Tahfidz & Al-Qur'an -->
    <div class="section-header">B. Capaian Tahfidz Al-Qur'an & Pembiasaan Ibadah</div>
    <div class="summary-box">
        <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                    <strong>Hafalan Al-Qur'an (Juz 30):</strong>
                    <ul style="margin: 3px 0 0 15px; padding: 0;">
                        @forelse($tahfidzRecords as $tahfidz)
                            <li>
                                <strong>{{ $tahfidz->surah_name }}</strong> (Ayat: {{ $tahfidz->verse_range ?? 'Lengkap' }})
                                — Kelancaran: <em>{{ $tahfidz->fluency_grade->label() ?? 'Baik' }}</em>,
                                Tajwid: <em>{{ $tahfidz->tajweed_grade->label() ?? 'Baik' }}</em>
                            </li>
                        @empty
                            <li style="color: #64748b;">Sedang dalam pembiasaan hafalan surat pendek Juz 30.</li>
                        @endforelse
                    </ul>
                </td>
                <td style="width: 50%; vertical-align: top; border-left: 1px dashed #cbd5e0; padding-left: 10px;">
                    <strong>Poin Karakter & Akhlakul Karimah:</strong>
                    <p style="margin: 3px 0 0 0;">
                        Skor Karakter Terkini: <strong>{{ $conductScore }} Poin</strong><br>
                        Predikat Disiplin & Adab: <strong>{{ $conductPredicate }}</strong><br>
                        <span style="font-size: 8pt; color: #475569;">
                            Aktif menjalankan shalat dhuha bersama, sopan santun kepada bapak/ibu guru, dan rukun dengan sesama teman.
                        </span>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <!-- C. Rekap Kehadiran (Presensi) -->
    <div class="section-header">C. Rekap Ketidakhadiran Semester</div>
    <table style="width: 60%; border-collapse: collapse; margin-bottom: 12px; font-size: 8.5pt;">
        <tr>
            <td style="border: 1px solid #cbd5e0; padding: 3px 6px; width: 60%;">Sakit (S)</td>
            <td style="border: 1px solid #cbd5e0; padding: 3px 6px; width: 40%; text-align: center; font-weight: bold;">
                {{ $attendanceStats['sakit'] }} Hari
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e0; padding: 3px 6px;">Izin Resmi (I)</td>
            <td style="border: 1px solid #cbd5e0; padding: 3px 6px; text-align: center; font-weight: bold;">
                {{ $attendanceStats['izin'] }} Hari
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e0; padding: 3px 6px;">Tanpa Keterangan (Alpa)</td>
            <td style="border: 1px solid #cbd5e0; padding: 3px 6px; text-align: center; font-weight: bold;">
                {{ $attendanceStats['alpa'] }} Hari
            </td>
        </tr>
    </table>

    <!-- D. Catatan Wali Kelas -->
    <div class="section-header">D. Catatan & Motivasi Wali Kelas</div>
    <div class="summary-box" style="font-style: italic; min-height: 35px;">
        "Alhamdulillah ananda menunjukkan perkembangan belajar dan hafalan Al-Qur'an yang positif. Pertahankan semangat belajar, shalat lima waktu, dan adab yang baik di rumah maupun di sekolah."
    </div>

    <!-- Tanda Tangan -->
    <table class="signatures-table">
        <tr>
            <td>
                Mengetahui,<br>
                Orang Tua / Wali Murid
                <div class="sig-space"></div>
                <div class="sig-name">{{ $student->parent?->name ?? '..................................' }}</div>
            </td>
            <td>
                Blitar, {{ date('d F Y') }}<br>
                Wali Kelas
                <div class="sig-space"></div>
                <div class="sig-name">{{ $student->classroom?->homeroomTeacher?->name ?? 'Bapak/Ibu Guru' }}</div>
                <span style="font-size: 8pt; color: #64748b;">NIP. -</span>
            </td>
            <td>
                Mengetahui,<br>
                Kepala Madrasah
                <div class="sig-space"></div>
                <div class="sig-name">{{ $school['principal'] }}</div>
                <span style="font-size: 8pt; color: #64748b;">NIP. {{ $school['principal_nip'] }}</span>
            </td>
        </tr>
    </table>

</body>
</html>
