<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Kelulusan - {{ $siswa->nama_lengkap }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Georgia', serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        
        /* Page 1: Certificate Layout */
        .certificate-container {
            width: 297mm;
            height: 210mm;
            box-sizing: border-box;
            padding: 20mm;
            position: relative;
            background-color: #ffffff;
            /* Thin elegant gold-like border using existing light-ish color rules */
            border: 15px solid #f1f5f9;
        }
        .inner-border {
            border: 2px solid #cbd5e1;
            height: 100%;
            box-sizing: border-box;
            padding: 15mm;
            position: relative;
        }
        .cert-header {
            text-align: center;
            margin-bottom: 15px;
        }
        .cert-logo {
            height: 55px;
            margin-bottom: 10px;
        }
        .cert-title-org {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 11pt;
            font-weight: bold;
            color: #3b82f6;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
        }
        .cert-main-title {
            text-align: center;
            font-size: 26pt;
            font-weight: bold;
            color: #0f172a;
            margin: 15px 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .cert-subtitle {
            font-size: 11pt;
            font-style: italic;
            color: #64748b;
            text-align: center;
            margin-bottom: 25px;
        }
        .cert-recipient {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            color: #3b82f6;
            margin: 10px 0;
            border-bottom: 1px solid #cbd5e1;
            display: inline-block;
            padding-bottom: 5px;
            min-width: 50%;
        }
        .cert-text {
            text-align: center;
            font-size: 11pt;
            line-height: 1.6;
            color: #475569;
            margin: 20px auto;
            max-width: 80%;
        }
        .cert-footer-table {
            width: 100%;
            border-collapse: collapse;
            position: absolute;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
        }
        .cert-footer-table td {
            vertical-align: bottom;
            text-align: center;
            font-size: 10pt;
        }
        .cert-signature-space {
            height: 55px;
        }
        .cert-signature-line {
            width: 70%;
            border-bottom: 1px solid #94a3b8;
            margin: 0 auto 5px auto;
        }
        .cert-signature-name {
            font-weight: bold;
            color: #0f172a;
        }
        .cert-signature-title {
            font-size: 9pt;
            color: #64748b;
        }
        .qr-box {
            position: absolute;
            bottom: 10px;
            left: 10px;
            text-align: left;
        }
        .qr-box img {
            height: 70px;
            width: 70px;
        }
        .qr-text {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 7pt;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Page 2: Transcript Layout */
        .page-break {
            page-break-after: always;
        }
        .transcript-container {
            width: 297mm;
            height: 210mm;
            box-sizing: border-box;
            padding: 20mm;
            background-color: #ffffff;
            border: 15px solid #f1f5f9;
        }
        .transcript-title {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }
        .transcript-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        .transcript-info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .transcript-info-label {
            width: 15%;
            color: #64748b;
            font-weight: bold;
        }
        .transcript-info-value {
            width: 35%;
            color: #0f172a;
            font-weight: bold;
        }
        .transcript-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .transcript-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px;
            font-size: 8.5pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }
        .transcript-table td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            font-size: 8.5pt;
            vertical-align: top;
        }
        .transcript-comp-name {
            font-weight: bold;
            color: #0f172a;
        }
        .transcript-scale-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-top: 15px;
        }
        .transcript-scale-table th, .transcript-scale-table td {
            border: 1px solid #e2e8f0;
            padding: 4px;
            text-align: center;
        }
        .transcript-scale-table th {
            background-color: #f1f5f9;
            color: #475569;
        }
    </style>
</head>
<body>

    <!-- PAGE 1: CERTIFICATE -->
    <div class="certificate-container page-break">
        <div class="inner-border">
            <div class="cert-header">
                <img class="cert-logo" src="{{ public_path('images/logo-erlass.png') }}" alt="Logo Erlass">
                <h4 class="cert-title-org">Erlass Institute</h4>
            </div>

            <div class="cert-main-title">Sertifikat Kelulusan</div>
            <div class="cert-subtitle">Certificate of Completion</div>

            <div style="text-align: center; color: #475569; font-size: 11pt;">Diberikan Kepada (Awarded to):</div>
            <div style="text-align: center;">
                <span class="cert-recipient">{{ $siswa->nama_lengkap }}</span>
            </div>

            <div class="cert-text">
                Atas partisipasi dan kelulusannya pada program ekstrakurikuler <strong>{{ $ekskul->kategori_program }}</strong>
                yang diselenggarakan oleh Erlass Institute di {{ $ekskul->sekolah->namasekolah }} pada periode semester berjalan dengan predikat kelulusan <strong>{{ $score->getKeterangan() }} ({{ $score->getPredikat() }})</strong>.
            </div>

            <!-- QR Verification Corner -->
            <div class="qr-box">
                @if($certificate->qr_code_path)
                    <img src="{{ public_path($certificate->qr_code_path) }}" alt="QR Verification">
                @else
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('certificates.verify', $certificate->certificate_code)) }}" alt="QR Verification">
                @endif
                <div class="qr-text">Scan to Verify: {{ $certificate->certificate_code }}</div>
            </div>

            <!-- Signatures -->
            <table class="cert-footer-table">
                <tr>
                    <td style="width: 33%;">
                        <div class="cert-signature-space"></div>
                        <div class="cert-signature-line"></div>
                        <div class="cert-signature-name">Jakarta, {{ $certificate->issued_at->translatedFormat('d F Y') }}</div>
                        <div class="cert-signature-title">Tanggal Terbit</div>
                    </td>
                    <td style="width: 34%;">
                        <div class="cert-signature-space"></div>
                        <div class="cert-signature-line"></div>
                        <div class="cert-signature-name">{{ $rombel->instruktur->nama_lengkap ?? 'Instruktur Pengajar' }}</div>
                        <div class="cert-signature-title">Instruktur Pengajar</div>
                    </td>
                    <td style="width: 33%;">
                        <div class="cert-signature-space"></div>
                        <div class="cert-signature-line"></div>
                        <div class="cert-signature-name">Erlass Academy Executive</div>
                        <div class="cert-signature-title">Direktur Pendidikan</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- PAGE 2: TRANSCRIPT -->
    <div class="transcript-container">
        <div class="transcript-title">Transkrip Hasil Belajar</div>
        
        <table class="transcript-info-table">
            <tr>
                <td class="transcript-info-label">Nama Siswa</td>
                <td class="transcript-info-value">: {{ $siswa->nama_lengkap }}</td>
                
                <td class="transcript-info-label">Program</td>
                <td class="transcript-info-value">: {{ $ekskul->kategori_program }}</td>
            </tr>
            <tr>
                <td class="transcript-info-label">NISN</td>
                <td class="transcript-info-value">: {{ $siswa->nisn }}</td>
                
                <td class="transcript-info-label">Rombel / Kelas</td>
                <td class="transcript-info-value">: {{ $rombel->nama_rombel }}</td>
            </tr>
            <tr>
                <td class="transcript-info-label">Sesi Kehadiran</td>
                <td class="transcript-info-value">: {{ number_format($score->nilai_kehadiran, 1) }}%</td>
                
                <td class="transcript-info-label">Nilai Akhir (NA)</td>
                <td class="transcript-info-value">: {{ number_format($score->nilai_akhir, 1) }} ({{ $score->getPredikat() }})</td>
            </tr>
        </table>

        <table class="transcript-table">
            <thead>
                <tr>
                    <th style="width: 25%; text-align: left;">Kompetensi Utama</th>
                    <th style="width: 10%;">Nilai</th>
                    <th style="width: 10%;">Predikat</th>
                    <th style="width: 55%; text-align: left;">Keterangan Hasil Belajar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($competencies as $comp)
                    <tr>
                        <td class="transcript-comp-name">{{ $comp['kompetensi'] }}</td>
                        <td class="text-center" style="font-weight: bold;">{{ number_format($comp['nilai'], 0) }}</td>
                        <td class="text-center" style="font-weight: bold; color: #3b82f6;">{{ $comp['pred'] }}</td>
                        <td>{{ $comp['deskripsi'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Grading Scales -->
        <table class="transcript-scale-table">
            <thead>
                <tr>
                    <th colspan="5">Kriteria Penilaian & Predikat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold;">90 - 100 (A+)</td>
                    <td style="font-weight: bold;">85 - 89 (A)</td>
                    <td style="font-weight: bold;">80 - 84 (B+)</td>
                    <td style="font-weight: bold;">75 - 79 (B)</td>
                    <td style="font-weight: bold;">0 - 74 (C)</td>
                </tr>
                <tr>
                    <td>Luar Biasa</td>
                    <td>Sangat Baik</td>
                    <td>Baik</td>
                    <td>Cukup</td>
                    <td>Perlu Pendampingan</td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures -->
        <table class="signature-table" style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 8pt;">
            <tr>
                <td style="text-align: center; width: 50%;">
                    <div style="margin-bottom: 35px; color: #64748b;">Mengetahui,<br>Kepala Sekolah / PIC Mitra</div>
                    <div style="font-weight: bold; color: #0f172a;">...................................................</div>
                </td>
                <td style="text-align: center; width: 50%;">
                    <div style="margin-bottom: 35px; color: #64748b;">Jakarta, {{ $certificate->issued_at->translatedFormat('d F Y') }}<br>Instruktur Pengajar</div>
                    <div style="font-weight: bold; color: #0f172a;">{{ $rombel->instruktur->nama_lengkap ?? 'Instruktur Erlass' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
