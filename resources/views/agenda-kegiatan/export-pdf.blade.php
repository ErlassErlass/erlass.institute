<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Agenda Kegiatan — Erlass Institute</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1a202c; margin: 0; padding: 16px; }
    h1   { font-size: 14pt; color: #1E3A5F; margin: 0 0 4px; }
    h2   { font-size: 10pt; color: #4a5568; margin: 0 0 12px; font-weight: normal; }
    .header { border-bottom: 2px solid #1E3A5F; padding-bottom: 10px; margin-bottom: 16px; }
    table  { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
    th     { background: #1E3A5F; color: #fff; padding: 6px 8px; text-align: left; font-weight: 600; }
    td     { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
    tr:nth-child(even) td { background: #f0f6ff; }
    .badge-hadir { background: #dcfce7; color: #166534; padding: 1px 6px; border-radius: 10px; font-size: 8pt; }
    .page-break  { page-break-after: always; }
    .footer      { margin-top: 20px; font-size: 8pt; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
</style>
</head>
<body>

<div class="header">
    <h1>Agenda Kegiatan Ekstrakurikuler</h1>
    <h2>Erlass Institute &mdash; Digenerate pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</h2>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nama Sekolah</th>
            <th>Kategori Pengajaran</th>
            <th>Rombel</th>
            <th>Tanggal Mengajar</th>
            <th>Pertemuan</th>
            <th style="text-align:center;">Hadir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sessions as $index => $session)
            @php
                $rombel  = $session->rombel;
                $ekskul  = $rombel?->ekstrakurikuler;
                $sekolah = $ekskul?->sekolah;
                $laporan = $session->laporanMengajar;
                $tanggal = $session->tanggal_pelaksanaan ?? $session->tanggal_terjadwal;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $sekolah?->namasekolah ?? '—' }}</strong></td>
                <td>{{ $ekskul?->kategori_program ?? '—' }}</td>
                <td>{{ $rombel?->nama_rombel ?? '—' }}</td>
                <td>{{ $tanggal ? $tanggal->translatedFormat('d M Y') : '—' }}</td>
                <td style="text-align:center;">{{ $session->nomor_pertemuan ?? '—' }}</td>
                <td style="text-align:center;">
                    <span class="badge-hadir">{{ $laporan?->jumlah_siswa_hadir ?? 0 }}</span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Total: {{ $sessions->count() }} sesi &mdash; Erlass Institute Academic Operations &amp; Quality Control System (AOQCS)
</div>

</body>
</html>
