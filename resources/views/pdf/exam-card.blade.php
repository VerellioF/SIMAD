<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu Peserta Ujian Semester</title>
    <style>
        @page {
            margin: 1cm 1.2cm 1cm 1.2cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.3;
            color: #1e293b;
        }
        .card-wrapper {
            border: 1.5px solid #0d3b2e;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 20px;
            background-color: #fafaf9;
            page-break-inside: avoid;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0d3b2e;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h2 {
            margin: 0;
            font-size: 11pt;
            color: #0d3b2e;
            text-transform: uppercase;
        }
        .header-title p {
            margin: 1px 0 0 0;
            font-size: 7.5pt;
            color: #64748b;
        }
        .doc-badge {
            text-align: center;
            margin: 4px 0 8px 0;
        }
        .doc-badge span {
            background-color: #0d3b2e;
            color: #ffffff;
            padding: 3px 12px;
            border-radius: 4px;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 8px;
        }
        table.meta-table td {
            padding: 2px 3px;
            vertical-align: top;
        }
        .photo-box {
            width: 55px;
            height: 70px;
            border: 1px solid #cbd5e0;
            background: #e2e8f0;
            text-align: center;
            line-height: 70px;
            font-size: 7.5pt;
            color: #64748b;
        }
        table.exam-schedule {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-top: 6px;
        }
        table.exam-schedule th, table.exam-schedule td {
            border: 1px solid #cbd5e0;
            padding: 3px 5px;
            text-align: center;
        }
        table.exam-schedule th {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        .rules-box {
            font-size: 7pt;
            color: #475569;
            margin-top: 6px;
            border-top: 1px dashed #cbd5e0;
            padding-top: 4px;
        }
        .signatures {
            width: 100%;
            margin-top: 8px;
            font-size: 8pt;
        }
        .cut-line {
            border-top: 1px dashed #94a3b8;
            margin: 15px 0;
            text-align: center;
            position: relative;
        }
        .cut-line span {
            background: #ffffff;
            padding: 0 8px;
            font-size: 7pt;
            color: #94a3b8;
            position: relative;
            top: -7px;
        }
    </style>
</head>
<body>

    @foreach($students as $index => $student)
        <div class="card-wrapper">
            <!-- Header Kop -->
            <table class="header-table">
                <tr>
                    <td style="width: 12%; text-align: center; vertical-align: middle;">
                        <img src="{{ public_path('images/logo-madrasah.svg') }}" style="width: 45px; height: auto;" alt="Logo">
                    </td>
                    <td class="header-title" style="width: 88%;">
                        <h2>{{ $school['name'] }}</h2>
                        <p>KARTU PESERTA PENILAIAN / ASESMEN SEMESTER</p>
                        <p>{{ $school['address'] }} | NSM: {{ $school['nsm'] }}</p>
                    </td>
                </tr>
            </table>

            <div class="doc-badge">
                <span>{{ $examTitle }}</span>
            </div>

            <!-- Identitas Siswa -->
            <table class="meta-table">
                <tr>
                    <td style="width: 18%; vertical-align: middle;">
                        <div class="photo-box">FOTO 3x4</div>
                    </td>
                    <td style="width: 82%; vertical-align: top; padding-left: 10px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 30%; color: #64748b;">No. Peserta Ujian</td>
                                <td style="width: 3%;">:</td>
                                <td style="font-weight: bold; font-family: monospace; color: #0d3b2e; font-size: 9pt;">
                                    UJN-{{ $student->classroom?->grade_level ?? '1' }}-{{ $student->nis }}
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #64748b;">Nama Peserta</td>
                                <td>:</td>
                                <td style="font-weight: bold; font-size: 9.5pt;">{{ $student->full_name }}</td>
                            </tr>
                            <tr>
                                <td style="color: #64748b;">NIS / NISN</td>
                                <td>:</td>
                                <td style="font-family: monospace;">{{ $student->nis }} / {{ $student->nisn }}</td>
                            </tr>
                            <tr>
                                <td style="color: #64748b;">Kelas / Ruang</td>
                                <td>:</td>
                                <td style="font-weight: bold;">{{ $student->classroom?->name ?? '-' }} (Ruang {{ $student->classroom?->grade_level ?? '1' }})</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Tabel Verifikasi Kehadiran Ujian / Paraf Pengawas -->
            <table class="exam-schedule">
                <thead>
                    <tr>
                        <th style="width: 15%;">Hari 1</th>
                        <th style="width: 15%;">Hari 2</th>
                        <th style="width: 15%;">Hari 3</th>
                        <th style="width: 15%;">Hari 4</th>
                        <th style="width: 15%;">Hari 5</th>
                        <th style="width: 25%;">Tanda Tangan Pengawas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Al-Qur'an Hadits / B. Indo</td>
                        <td>Akidah Akhlak / Matematika</td>
                        <td>Fikih / IPA</td>
                        <td>SKI / IPS</td>
                        <td>B. Arab / B. Jawa</td>
                        <td style="height: 22px;"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Tata Tertib Singkat & Tanda Tangan -->
            <table class="signatures">
                <tr>
                    <td style="width: 65%; vertical-align: top;">
                        <div class="rules-box">
                            <strong>Tata Tertib Peserta Ujian:</strong><br>
                            1. Hadir 15 menit sebelum ujian dimulai dengan seragam rapi lengkap.<br>
                            2. Wajib membawa kartu ujian ini selama pelaksanaan asesmen semester.<br>
                            3. Mengerjakan soal dengan jujur, tertib, dan mandiri.
                        </div>
                    </td>
                    <td style="width: 35%; text-align: center; vertical-align: top;">
                        Blitar, {{ date('d F Y') }}<br>
                        Kepala Madrasah,
                        <div style="height: 35px;"></div>
                        <strong style="text-decoration: underline;">{{ $school['principal'] }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        @if(! $loop->last && ($index % 2 == 1))
            <div style="page-break-after: always;"></div>
        @elseif(! $loop->last)
            <div class="cut-line">
                <span>✂ Potong di sini (Batas Lembar Kartu Ujian)</span>
            </div>
        @endif
    @endforeach

</body>
</html>
