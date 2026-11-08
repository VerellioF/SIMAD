<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Pendaftaran PPDB - {{ $registration->registration_number }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #1a202c;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #0d3b2e;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h2 {
            margin: 0;
            font-size: 15pt;
            color: #0d3b2e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h3 {
            margin: 3px 0;
            font-size: 11pt;
            font-weight: normal;
            color: #4a5568;
        }
        .header-title p {
            margin: 0;
            font-size: 8.5pt;
            color: #718096;
        }
        .doc-title {
            text-align: center;
            margin: 15px 0 20px 0;
        }
        .doc-title h1 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            text-decoration: underline;
            color: #1a202c;
        }
        .badge-box {
            border: 2px solid #0d3b2e;
            background-color: #f7fafc;
            padding: 8px 15px;
            display: inline-block;
            margin-top: 8px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13pt;
            font-weight: bold;
            color: #0d3b2e;
            letter-spacing: 1px;
        }
        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #e2e8f0;
            padding: 4px 8px;
            margin-top: 15px;
            margin-bottom: 8px;
            border-left: 4px solid #0d3b2e;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table td {
            padding: 4px 6px;
            font-size: 10pt;
            vertical-align: top;
        }
        table.data-table td.label {
            width: 32%;
            color: #4a5568;
        }
        table.data-table td.colon {
            width: 3%;
            text-align: center;
        }
        table.data-table td.value {
            width: 65%;
            font-weight: 500;
        }
        .checklist-box {
            border: 1px dashed #cbd5e0;
            padding: 10px;
            margin-top: 10px;
            font-size: 9pt;
            background-color: #f8fafc;
        }
        .checklist-box p {
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        .signatures {
            width: 100%;
            margin-top: 35px;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            font-size: 10pt;
        }
        .sign-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI -->
    <table class="header-table">
        <tr>
            <td class="header-title">
                <h2>{{ $school['name'] }}</h2>
                <h3>{{ $school['tagline'] }}</h3>
                <p>{{ $school['address'] }} | Telp/WA: {{ $school['phone'] }}</p>
                <p>NSM: {{ $school['nsm'] }} • NPSN: {{ $school['npsn'] }} • Status: Terakreditasi</p>
            </td>
        </tr>
    </table>

    <!-- JUDUL DOKUMEN & NOMOR REGISTRASI -->
    <div class="doc-title">
        <h1>Lembar Bukti Pendaftaran Santri Baru (PPDB Online)</h1>
        <div class="badge-box">
            {{ $registration->registration_number }}
        </div>
        <p style="font-size: 9pt; color: #718096; margin-top: 5px;">
            Waktu Pendaftaran: {{ $registration->created_at->translatedFormat('d F Y, H:i') }} WIB
        </p>
    </div>

    <!-- DATA CALON SANTRI -->
    <div class="section-title">A. Biodata Calon Santri</div>
    <table class="data-table">
        <tr>
            <td class="label">Nama Lengkap Santri</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $registration->child_name }}</strong></td>
        </tr>
        <tr>
            <td class="label">NIK Santri</td>
            <td class="colon">:</td>
            <td class="value">{{ $registration->nik }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td class="colon">:</td>
            <td class="value">{{ $registration->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td class="label">Tempat, Tanggal Lahir</td>
            <td class="colon">:</td>
            <td class="value">{{ $registration->birth_place }}, {{ $registration->birth_date->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Asal Lembaga (RA/TK)</td>
            <td class="colon">:</td>
            <td class="value">{{ $registration->previous_school }}</td>
        </tr>
    </table>

    <!-- DATA ORANG TUA / WALI -->
    <div class="section-title">B. Data Orang Tua / Wali</div>
    <table class="data-table">
        <tr>
            <td class="label">Nama Orang Tua / Wali</td>
            <td class="colon">:</td>
            <td class="value">{{ $registration->parent_name }}</td>
        </tr>
        <tr>
            <td class="label">Nomor WhatsApp Aktif</td>
            <td class="colon">:</td>
            <td class="value">{{ $registration->parent_whatsapp }}</td>
        </tr>
        <tr>
            <td class="label">Pekerjaan Orang Tua</td>
            <td class="colon">:</td>
            <td class="value">{{ $registration->parent_job ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Domisili</td>
            <td class="colon">:</td>
            <td class="value">{{ $registration->address }}</td>
        </tr>
    </table>

    <!-- PETUNJUK BERKAS OBSERVASI -->
    <div class="checklist-box">
        <p>Kelengkapan Berkas Fisik yang Wajib Dibawa Saat Jadwal Observasi:</p>
        <table style="width: 100%; font-size: 8.5pt;">
            <tr>
                <td style="width: 50%;">[  ] Fotokopi Akta Kelahiran (2 lembar)</td>
                <td style="width: 50%;">[  ] Fotokopi KTP Kedua Orang Tua</td>
            </tr>
            <tr>
                <td style="width: 50%;">[  ] Fotokopi Kartu Keluarga (2 lembar)</td>
                <td style="width: 50%;">[  ] Pasfoto Berwarna 3x4 (3 lembar)</td>
            </tr>
        </table>
    </div>

    <!-- TANDA TANGAN -->
    <table class="signatures">
        <tr>
            <td>
                Orang Tua / Wali Santri,
                <div class="sign-space"></div>
                ( <strong>{{ $registration->parent_name }}</strong> )
            </td>
            <td>
                Blitar, {{ date('d F Y') }}<br>
                Panitia PPDB MI Plus,
                <div class="sign-space"></div>
                ( _________________________ )
            </td>
        </tr>
    </table>

</body>
</html>
