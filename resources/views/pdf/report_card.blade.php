<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor Ekstrakurikuler - {{ $siswa->nama_lengkap }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #334155;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 3px double #3b82f6;
            padding-bottom: 10px;
        }
        .logo-cell {
            width: 70px;
            vertical-align: middle;
        }
        .logo-cell img {
            height: 55px;
            width: auto;
        }
        .title-cell {
            text-align: center;
            vertical-align: middle;
        }
        .title-cell h2 {
            margin: 0;
            color: #0f172a;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-cell p {
            margin: 3px 0 0 0;
            font-size: 9pt;
            color: #64748b;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-label {
            width: 18%;
            color: #64748b;
            font-weight: 600;
        }
        .info-colon {
            width: 2%;
            color: #64748b;
        }
        .info-value {
            width: 30%;
            color: #0f172a;
            font-weight: bold;
        }
        .competency-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .competency-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px;
            font-size: 8.5pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }
        .competency-table td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            font-size: 8.5pt;
            vertical-align: top;
        }
        .competency-name {
            font-weight: bold;
            color: #0f172a;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .grade-badge {
            font-weight: bold;
            color: #3b82f6;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .results-table td {
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }
        .results-table td:last-child {
            padding-right: 0;
            padding-left: 10px;
        }
        .card-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            background-color: #f8fafc;
            min-height: 120px;
        }
        .card-title {
            font-size: 9pt;
            font-weight: bold;
            color: #3b82f6;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .scratch-title {
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .scratch-desc {
            font-size: 8pt;
            color: #64748b;
        }
        .scale-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 20px;
        }
        .scale-table th, .scale-table td {
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            text-align: center;
        }
        .scale-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 9pt;
        }
        .signature-table td {
            width: 33%;
            text-align: center;
            vertical-align: top;
        }
        .signature-title {
            margin-bottom: 45px;
            color: #64748b;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('images/logo-erlass.png') }}" alt="Logo Erlass">
            </td>
            <td class="title-cell">
                <h2>Rapor Kegiatan Ekstrakurikuler</h2>
                <p>Erlass Institute — Education & Training Center</p>
            </td>
        </tr>
    </table>

    <!-- Student Info -->
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Siswa</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $siswa->nama_lengkap }}</td>
            
            <td class="info-label">Program Ekskul</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $ekskul->kategori_program }}</td>
        </tr>
        <tr>
            <td class="info-label">NISN</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $siswa->nisn }}</td>
            
            <td class="info-label">Rombel / Kelas</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $rombel->nama_rombel }}</td>
        </tr>
        <tr>
            <td class="info-label">Periode Rapor</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $score->periode }}</td>
            
            <td class="info-label">Status Kelulusan</td>
            <td class="info-colon">:</td>
            <td class="info-value" style="color: {{ $score->nilai_kehadiran >= 75 ? '#10b981' : '#f43f5e' }}">
                {{ $score->nilai_kehadiran >= 75 ? 'Lulus / Eligible' : 'Perlu Pendampingan' }}
            </td>
        </tr>
    </table>

    <!-- Competency Table -->
    <table class="competency-table">
        <thead>
            <tr>
                <th style="width: 25%; text-align: left;">Kompetensi Utama</th>
                <th style="width: 10%;">Nilai</th>
                <th style="width: 10%;">Predikat</th>
                <th style="width: 55%; text-align: left;">Capaian Kompetensi / Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($competencies as $comp)
                <tr>
                    <td class="competency-name">{{ $comp['kompetensi'] }}</td>
                    <td class="text-center fw-bold">{{ number_format($comp['nilai'], 0) }}</td>
                    <td class="text-center grade-badge">{{ $comp['pred'] }}</td>
                    <td>{{ $comp['deskripsi'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary & Project Scratch -->
    <table class="results-table">
        <tr>
            <td>
                <!-- Final Result -->
                <div class="card-box">
                    <div class="card-title">Hasil Akhir Penilaian</div>
                    <table style="width: 100%; font-size: 8.5pt;">
                        <tr>
                            <td style="width: 45%; padding: 2px 0;">Nilai Kehadiran (30%)</td>
                            <td style="width: 5%; padding: 2px 0;">:</td>
                            <td style="width: 50%; padding: 2px 0; font-weight: bold;">{{ number_format($score->nilai_kehadiran, 1) }}%</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Nilai Tugas (30%)</td>
                            <td>:</td>
                            <td style="padding: 2px 0; font-weight: bold;">{{ number_format($score->nilai_tugas, 1) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Nilai Sikap (20%)</td>
                            <td>:</td>
                            <td style="padding: 2px 0; font-weight: bold;">{{ number_format($score->nilai_sikap, 1) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Nilai Proyek (20%)</td>
                            <td>:</td>
                            <td style="padding: 2px 0; font-weight: bold;">{{ number_format($score->nilai_proyek, 1) }}</td>
                        </tr>
                        <tr style="font-size: 9.5pt; color: #3b82f6;">
                            <td style="padding: 6px 0 0 0; font-weight: bold;">NILAI AKHIR (NA)</td>
                            <td style="padding: 6px 0 0 0; font-weight: bold;">:</td>
                            <td style="padding: 6px 0 0 0; font-weight: bold;">{{ number_format($score->nilai_akhir, 1) }} ({{ $score->getPredikat() }} - {{ $score->getKeterangan() }})</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                <!-- Scratch Project Box -->
                <div class="card-box">
                    <div class="card-title">Proyek Scratch / Kuis Akhir</div>
                    @if($score->projek_scratch)
                        <div class="scratch-title">{{ $score->projek_scratch }}</div>
                        <div class="scratch-desc">
                            Siswa telah mempublikasikan portofolio karya Scratch / karya akhir di platform pembelajaran sebagai bukti kelayakan program.
                        </div>
                    @else
                        <div class="text-muted small italic">Tidak ada nama proyek Scratch terdaftar.</div>
                    @endif
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top: 10px;">
                <!-- Catatan Guru -->
                <div class="card-box" style="min-height: 70px;">
                    <div class="card-title">Catatan Guru / Instruktur</div>
                    <div class="small" style="font-style: italic;">
                        {{ $score->catatan_guru ?? 'Siswa menunjukkan antusiasme yang baik selama proses pembelajaran dan dapat mengikuti semua arahan instruktur dengan tertib.' }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Scale Guide -->
    <table class="scale-table">
        <thead>
            <tr>
                <th colspan="5">Kategori Predikat Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="background-color: #f8fafc; font-weight: bold; width: 20%;">90 - 100 (A+)</td>
                <td style="background-color: #f8fafc; font-weight: bold; width: 20%;">85 - 89 (A)</td>
                <td style="background-color: #f8fafc; font-weight: bold; width: 20%;">80 - 84 (B+)</td>
                <td style="background-color: #f8fafc; font-weight: bold; width: 20%;">75 - 79 (B)</td>
                <td style="background-color: #f8fafc; font-weight: bold; width: 20%;">0 - 74 (C)</td>
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

    <!-- Signature Section -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-title">Mengetahui,<br>Kepala Sekolah / PIC Mitra</div>
                <div class="signature-space"></div>
                <div class="signature-name">...................................................</div>
            </td>
            <td>
                <div class="signature-title">Jakarta, {{ now()->translatedFormat('d F Y') }}<br>Instruktur Pengajar</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $rombel->instruktur->nama_lengkap ?? 'Instruktur Erlass' }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
