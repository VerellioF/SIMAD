<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu Tanda Siswa - {{ $student->full_name }}</title>
    <style>
        @page {
            margin: 1cm 1.2cm;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Sheet Header */
        .sheet-header {
            text-align: center;
            border-bottom: 2px solid #0d3b2e;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .sheet-header h2 {
            margin: 0;
            font-size: 12pt;
            color: #0d3b2e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sheet-header p {
            margin: 2px 0 0 0;
            font-size: 8pt;
            color: #64748b;
        }

        /* Instructions */
        .instruction-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 7.5pt;
            color: #475569;
            margin-bottom: 20px;
            text-align: center;
            line-height: 1.35;
        }

        /* Card Container (Standard ID Card CR80: 85.6mm x 54mm) */
        .card-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid !important;
            margin-bottom: 20px;
        }

        .card-wrapper {
            width: 324px;
            border: 2px solid #0d3b2e;
            border-radius: 10px;
            background: #ffffff;
            page-break-inside: avoid !important;
            margin: 0 auto;
            border-collapse: collapse;
        }

        /* Front Card Header */
        .card-top {
            background: #0d3b2e;
            color: #ffffff;
            padding: 6px 8px;
            border-bottom: 2px solid #d4af37;
        }
        .card-top-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-cell {
            width: 32px;
            vertical-align: middle;
            text-align: left;
        }
        .logo-img {
            width: 28px;
            height: 28px;
        }
        .header-cell {
            vertical-align: middle;
            text-align: center;
        }
        .header-cell h3 {
            margin: 0;
            font-size: 7.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #ffffff;
        }
        .header-cell p {
            margin: 1px 0 0 0;
            font-size: 5pt;
            color: #f1f5f9;
            line-height: 1.15;
        }

        /* Card Subheader Badge */
        .badge-strip {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 3px 8px;
            text-align: center;
        }
        .badge-title {
            display: inline-block;
            background-color: #0d3b2e;
            color: #d4af37;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1.5px 8px;
            border-radius: 4px;
        }

        /* Card Body */
        .card-body-cell {
            padding: 6px 8px 4px 8px;
            vertical-align: top;
        }
        .body-table {
            width: 100%;
            border-collapse: collapse;
        }
        .photo-column {
            width: 58px;
            vertical-align: top;
            text-align: center;
        }
        .photo-holder {
            width: 52px;
            height: 64px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            background-color: #f1f5f9;
            text-align: center;
            vertical-align: middle;
            display: block;
            margin: 0 auto;
        }
        .photo-holder span {
            font-size: 6pt;
            color: #94a3b8;
            line-height: 64px;
            display: block;
        }

        /* Student Details Table */
        .info-column {
            vertical-align: top;
            padding-left: 6px;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.2pt;
            line-height: 1.25;
        }
        .student-table td {
            padding: 0.8px 0;
            vertical-align: top;
        }
        .st-lbl {
            width: 54px;
            color: #64748b;
        }
        .st-sep {
            width: 6px;
            text-align: center;
            color: #64748b;
        }
        .st-val {
            font-weight: bold;
            color: #0f172a;
        }

        /* Card Footer */
        .card-footer-cell {
            padding: 3px 8px 5px 8px;
            border-top: 1px solid #e2e8f0;
            background-color: #fafafa;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 5pt;
            line-height: 1.1;
        }
        .barcode-cell {
            vertical-align: middle;
            text-align: left;
        }
        .barcode-stripes {
            display: inline-block;
            height: 14px;
            letter-spacing: -1.5px;
            font-family: monospace;
            font-weight: 900;
            font-size: 11pt;
            color: #0d3b2e;
            transform: scaleY(1.3);
        }
        .barcode-text {
            font-family: monospace;
            font-weight: bold;
            font-size: 5.5pt;
            color: #0d3b2e;
            display: block;
            margin-top: 1px;
        }
        .signature-cell {
            text-align: right;
            vertical-align: middle;
        }
        .sig-title {
            color: #64748b;
        }
        .sig-name {
            font-weight: bold;
            color: #0d3b2e;
        }

        /* Card Back Styling */
        .back-top {
            background: #0d3b2e;
            color: #d4af37;
            padding: 6px 8px;
            text-align: center;
            border-bottom: 2px solid #d4af37;
        }
        .back-top h3 {
            margin: 0;
            font-size: 6.8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .back-body-cell {
            padding: 8px 10px;
            font-size: 5.8pt;
            line-height: 1.35;
            color: #334155;
            vertical-align: top;
        }
        .back-body-cell ol {
            margin: 3px 0 6px 14px;
            padding: 0;
        }
        .back-body-cell li {
            margin-bottom: 2px;
        }
        .return-note {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 4px 6px;
            font-size: 5.2pt;
            color: #475569;
            text-align: center;
            margin-top: 6px;
        }

        /* Cut Label */
        .cut-label {
            font-size: 7.5pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen Cetak -->
    <div class="sheet-header">
        <h2>{{ $school['name'] }}</h2>
        <p>LEMBAR CETAK KARTU TANDA SISWA (KTS) RESMI</p>
        <p>{{ $school['address'] }} | NSM: {{ $school['nsm'] }} | NPSN: {{ $school['npsn'] }}</p>
    </div>

    <!-- Petunjuk Cetak & Laminating -->
    <div class="instruction-box">
        <strong>💡 Petunjuk Pencetakan untuk Staf TU / Wali Murid:</strong><br>
        1. Cetak dokumen ini pada kertas tebal A4 (misal: kertas foto / karton 210-260 gsm) dengan skala cetak 100% (Actual Size).<br>
        2. Gunting kedua kartu (Sisi Depan & Sisi Belakang) mengikuti garis batas kartu ✂.<br>
        3. Rekatkan sisi depan dan belakang secara presisi, lalu laminating untuk hasil kartu yang kokoh dan tahan lama.
    </div>

    <!-- Layout Dua Sisi Kartu Berdampingan (1 Halaman Utuh) -->
    <table class="card-table">
        <tr>
            <!-- SISI DEPAN (FRONT) -->
            <td style="width: 50%; vertical-align: top; text-align: center; padding-right: 12px;">
                <div class="cut-label">▼ SISI DEPAN (FRONT) ▼</div>
                
                <table class="card-wrapper">
                    <!-- Top Header -->
                    <tr>
                        <td class="card-top">
                            <table class="card-top-table">
                                <tr>
                                    <td class="logo-cell">
                                        <img src="{{ public_path('images/logo-madrasah-pdf.svg') }}" class="logo-img" alt="Logo">
                                    </td>
                                    <td class="header-cell">
                                        <h3>{{ $school['name'] }}</h3>
                                        <p>NSM: {{ $school['nsm'] }} • NPSN: {{ $school['npsn'] }}</p>
                                        <p>{{ $school['address'] }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Subheader Badge -->
                    <tr>
                        <td class="badge-strip">
                            <span class="badge-title">KARTU TANDA SISWA (KTS)</span>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="card-body-cell">
                            <table class="body-table">
                                <tr>
                                    <!-- Photo Box -->
                                    <td class="photo-column">
                                        <div class="photo-holder">
                                            <span>FOTO 2x3</span>
                                        </div>
                                    </td>

                                    <!-- Student Details -->
                                    <td class="info-column">
                                        <table class="student-table">
                                            <tr>
                                                <td class="st-lbl">Nama Lengkap</td>
                                                <td class="st-sep">:</td>
                                                <td class="st-val">{{ $student->full_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="st-lbl">NIS / NISN</td>
                                                <td class="st-sep">:</td>
                                                <td class="st-val" style="font-family: monospace;">{{ $student->nis ?? '-' }} / {{ $student->nisn }}</td>
                                            </tr>
                                            <tr>
                                                <td class="st-lbl">Kelas / Rombel</td>
                                                <td class="st-sep">:</td>
                                                <td class="st-val">{{ $student->classroom?->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="st-lbl">Tempat, Tgl Lahir</td>
                                                <td class="st-sep">:</td>
                                                <td class="st-val">{{ $student->birth_place }}, {{ $student->birth_date?->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="st-lbl">Jenis Kelamin</td>
                                                <td class="st-sep">:</td>
                                                <td class="st-val">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="st-lbl">Alamat</td>
                                                <td class="st-sep">:</td>
                                                <td class="st-val">{{ \Illuminate\Support\Str::limit($student->address, 34) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Card Footer -->
                    <tr>
                        <td class="card-footer-cell">
                            <table class="footer-table">
                                <tr>
                                    <td class="barcode-cell">
                                        <span class="barcode-stripes">||| | |||| | || ||| ||</span>
                                        <span class="barcode-text">*{{ $student->nis ?? $student->nisn }}*</span>
                                    </td>
                                    <td class="signature-cell">
                                        <span class="sig-title">Kepala Madrasah,</span><br>
                                        <span class="sig-name">{{ $school['principal'] }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- SISI BELAKANG (BACK) -->
            <td style="width: 50%; vertical-align: top; text-align: center; padding-left: 12px;">
                <div class="cut-label">▼ SISI BELAKANG (BACK) ▼</div>

                <table class="card-wrapper">
                    <!-- Top Header -->
                    <tr>
                        <td class="back-top">
                            <h3>TATA TERTIB & KETENTUAN PEMEGANG KARTU</h3>
                        </td>
                    </tr>

                    <!-- Back Body -->
                    <tr>
                        <td class="back-body-cell">
                            <strong style="color: #0d3b2e;">Janji & Kewajiban Siswa Madrasah:</strong>
                            <ol>
                                <li>Taat kepada Allah SWT & Rasul-Nya, berbakti kepada orang tua dan bapak/ibu guru.</li>
                                <li>Rajin shalat fardhu 5 waktu, shalat dhuha, dan istiqomah menghafal Al-Qur'an.</li>
                                <li>Menjaga adab, kejujuran, kedisiplinan, dan nama baik madrasah di mana pun berada.</li>
                                <li>Kartu tanda siswa ini wajib dibawa saat kegiatan KBM dan asesmen semester.</li>
                            </ol>

                            <div class="return-note">
                                <strong>Pemberitahuan:</strong><br>
                                Kartu ini adalah identitas resmi siswa MI Plus Raudlatul Muta'allimin.<br>
                                Apabila menemukan kartu ini tercecer, mohon diserahkan ke alamat madrasah atau hubungi kantor TU.
                            </div>

                            <div style="text-align: right; margin-top: 8px; font-size: 5pt; color: #475569;">
                                Blitar, Jawa Timur<br>
                                <strong>MI Plus Raudlatul Muta'allimin</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>