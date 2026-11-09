<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu Tanda Siswa - {{ $student->full_name }}</title>
    <style>
        @page {
            margin: 0;
            size: 85.6mm 54mm landscape;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 4mm 5mm;
            background: #ffffff;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
        }
        .card-container {
            width: 100%;
            height: 100%;
            border: 1.5px solid #0d3b2e;
            border-radius: 4mm;
            padding: 3mm 4mm;
            box-sizing: border-box;
            background-color: #fafaf9;
            position: relative;
        }
        .header {
            display: table;
            width: 100%;
            border-bottom: 1.5px solid #0d3b2e;
            padding-bottom: 1.5mm;
            margin-bottom: 2mm;
        }
        .header-logo {
            display: table-cell;
            width: 12mm;
            vertical-align: middle;
        }
        .header-logo img {
            width: 10mm;
            height: auto;
        }
        .header-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .header-text h1 {
            margin: 0;
            font-size: 7.5pt;
            color: #0d3b2e;
            text-transform: uppercase;
            font-weight: 800;
        }
        .header-text p {
            margin: 0.5mm 0 0 0;
            font-size: 5.5pt;
            color: #475569;
        }
        .title-badge {
            text-align: center;
            margin-bottom: 2mm;
        }
        .title-badge span {
            background-color: #0d3b2e;
            color: #ffffff;
            font-size: 6pt;
            font-weight: bold;
            padding: 1mm 4mm;
            border-radius: 2mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .content {
            display: table;
            width: 100%;
        }
        .photo-box {
            display: table-cell;
            width: 18mm;
            vertical-align: top;
        }
        .photo-frame {
            width: 16mm;
            height: 20mm;
            border: 1px solid #cbd5e0;
            border-radius: 2mm;
            background-color: #e2e8f0;
            text-align: center;
            line-height: 20mm;
            font-size: 6pt;
            color: #64748b;
        }
        .data-box {
            display: table-cell;
            vertical-align: top;
            padding-left: 2mm;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.5pt;
        }
        table.info-table td {
            padding: 0.8mm 0;
            vertical-align: top;
        }
        .label {
            width: 16mm;
            color: #475569;
        }
        .colon {
            width: 2mm;
            text-align: center;
        }
        .val {
            font-weight: bold;
            color: #0f172a;
        }
        .footer {
            margin-top: 1.5mm;
            display: table;
            width: 100%;
            font-size: 5pt;
        }
        .barcode-side {
            display: table-cell;
            vertical-align: bottom;
            font-family: monospace;
            font-weight: bold;
            color: #0d3b2e;
        }
        .sig-side {
            display: table-cell;
            text-align: right;
            vertical-align: bottom;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div class="card-container">
        <!-- Header -->
        <div class="header">
            <div class="header-logo">
                <img src="{{ public_path('images/logo-madrasah.svg') }}" alt="Logo">
            </div>
            <div class="header-text">
                <h1>{{ $school['name'] }}</h1>
                <p>NSM: {{ $school['nsm'] }} | NPSN: {{ $school['npsn'] }} | {{ $school['address'] }}</p>
            </div>
        </div>

        <div class="title-badge">
            <span>Kartu Tanda Siswa (KTS)</span>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="photo-box">
                <div class="photo-frame">
                    FOTO 2x3
                </div>
            </div>
            <div class="data-box">
                <table class="info-table">
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="colon">:</td>
                        <td class="val">{{ $student->full_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIS / NISN</td>
                        <td class="colon">:</td>
                        <td class="val">{{ $student->nis }} / {{ $student->nisn }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kelas / Rombel</td>
                        <td class="colon">:</td>
                        <td class="val">{{ $student->classroom?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">TTL</td>
                        <td class="colon">:</td>
                        <td class="val">{{ $student->birth_place }}, {{ $student->birth_date?->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat</td>
                        <td class="colon">:</td>
                        <td class="val">{{ \Illuminate\Support\Str::limit($student->address, 38) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer / Signature -->
        <div class="footer">
            <div class="barcode-side">
                *{{ $student->nis }}*
            </div>
            <div class="sig-side">
                Kepala Madrasah<br>
                <strong>{{ $school['principal'] }}</strong>
            </div>
        </div>
    </div>
</body>
</html>
