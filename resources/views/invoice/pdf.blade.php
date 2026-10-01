<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->nomor_invoice }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #1a1a2e;
            background: #fff;
        }
        .page { padding: 26px 34px; }

        /* ── HEADER ────────────────────────────────────────────── */
        .header { display: flex; align-items: flex-start; justify-content: space-between; border-bottom: 3px solid #1e3a8a; padding-bottom: 14px; margin-bottom: 16px; }
        .logo-area .company-name { font-size: 18pt; font-weight: 700; color: #1e3a8a; }
        .logo-area .company-tagline { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .invoice-meta { text-align: right; }
        .invoice-meta .inv-label { font-size: 8pt; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
        .invoice-meta .inv-number { font-size: 13pt; font-weight: 700; color: #1e3a8a; }
        .invoice-meta .inv-status { display: inline-block; background: #dcfce7; color: #166534; padding: 2px 10px; border-radius: 20px; font-size: 8pt; font-weight: 600; margin-top: 4px; }

        /* ── INFO SECTION ──────────────────────────────────────── */
        .info-grid { display: flex; gap: 16px; margin-bottom: 16px; }
        .info-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; }
        .info-box .box-title { font-size: 7.5pt; color: #64748b; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; margin-bottom: 4px; }
        .info-box .box-value { font-size: 9.5pt; font-weight: 600; color: #1e3a8a; line-height: 1.4; }
        .info-box .box-sub { font-size: 8pt; color: #475569; }

        /* ── BILLING SUMMARY ───────────────────────────────────── */
        .summary-box { background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1.5px solid #bfdbfe; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; }
        .summary-grid { display: flex; gap: 10px; }
        .summary-item { flex: 1; text-align: center; }
        .summary-item .s-val { font-size: 20pt; font-weight: 700; color: #1e3a8a; }
        .summary-item .s-label { font-size: 7.5pt; color: #64748b; margin-top: 2px; }
        .summary-item .s-note { font-size: 7pt; color: #94a3b8; }
        .divider-v { width: 1px; background: #bfdbfe; margin: 0 5px; }

        /* ── SKEMA BADGE ───────────────────────────────────────── */
        .skema-badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 8pt; font-weight: 600; }
        .skema-bulanan { background: #dbeafe; color: #1e40af; }
        .skema-semester { background: #ede9fe; color: #5b21b6; }
        .skema-tahunan { background: #e2e8f0; color: #334155; }
        .skema-per4 { background: #f1f5f9; color: #475569; }

        /* ── KOREKSI NOTE ──────────────────────────────────────── */
        .koreksi-note { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 6px; padding: 7px 10px; margin-bottom: 14px; font-size: 8pt; color: #92400e; }

        /* ── APPROVAL TABLE ────────────────────────────────────── */
        .approval-section { margin-bottom: 16px; }
        .section-title { font-size: 9.5pt; font-weight: 700; color: #1e3a8a; border-bottom: 1.5px solid #bfdbfe; padding-bottom: 4px; margin-bottom: 10px; }
        .approval-grid { display: flex; gap: 14px; }
        .approval-box { flex: 1; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; }
        .approval-box.approved { border-color: #bbf7d0; background: #f0fdf4; }
        .approval-box.pending { border-color: #fde68a; background: #fffbeb; }
        .approval-box .ap-role { font-size: 7.5pt; color: #64748b; text-transform: uppercase; font-weight: 600; }
        .approval-box .ap-name { font-size: 9.5pt; font-weight: 600; color: #1e3a8a; margin-top: 3px; }
        .approval-box .ap-date { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .approval-box .ap-status { font-size: 8pt; font-weight: 700; color: #166534; margin-top: 5px; }
        .approval-box.pending .ap-status { color: #b45309; }
        .ttd-area { text-align: center; margin-top: 8px; border-top: 1px dashed #94a3b8; padding-top: 3px; }
        .ttd-area .ttd-label { font-size: 7pt; color: #94a3b8; }

        /* ── KONTRAK WARNING ────────────────────────────────────── */
        .kontrak-box { border: 1.5px solid #fca5a5; border-radius: 8px; padding: 10px 14px; background: #fff7f7; margin-bottom: 10px; }
        .kontrak-box .kontrak-title { font-size: 8pt; font-weight: 700; color: #b91c1c; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .kontrak-box .kontrak-text { font-size: 7.8pt; color: #374151; line-height: 1.5; }
        .kontrak-box .kontrak-text strong { color: #b91c1c; }

        /* ── FOOTER ────────────────────────────────────────────── */
        .footer { border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 8px; display: flex; justify-content: space-between; align-items: center; }
        .footer-left { font-size: 7pt; color: #94a3b8; }
        .footer-right { font-size: 7pt; color: #94a3b8; text-align: right; }

        /* ── PAGE BREAK ─────────────────────────────────────────── */
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- ═════════════════════════════════════════════════════════════════════════ --}}
{{-- HALAMAN 1: LEMBAR UTAMA TAGIHAN (INVOICE SHEET)                           --}}
{{-- ═════════════════════════════════════════════════════════════════════════ --}}
<div class="page">

    {{-- ─── HEADER ─────────────────────────────────────────────────────── --}}
    <div class="header">
        <div class="logo-area">
            <div class="company-name">ERLASS</div>
            <div class="company-tagline">PT. Erlass Prokreatif Indonesia</div>
            <div style="font-size:7.5pt; color:#64748b; margin-top:2px;">
                Jakarta & Bali, Indonesia · erlass.institute
            </div>
        </div>
        <div class="invoice-meta">
            <div class="inv-label">Nomor Invoice</div>
            <div class="inv-number">{{ $invoice->nomor_invoice }}</div>
            @if($isDraft ?? false)
                <div class="inv-status" style="background: #fef3c7; color: #92400e; border: 1px solid #fcd34d;">⚠️ DRAFT — KONFIRMASI PIC</div>
                <div style="font-size:7pt; color:#b45309; margin-top:3px; font-weight:600;">
                    Dokumen Pra-Tagihan (Konfirmasi PIC)
                </div>
            @else
                <div class="inv-status">✅ APPROVED</div>
                <div style="font-size:7pt; color:#64748b; margin-top:3px;">
                    Diterbitkan: {{ $invoice->akunting_approved_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Banner Keterangan Khusus Draft Invoice --}}
    @if($isDraft ?? false)
    <div style="background: #fffbeb; border: 1.5px dashed #f59e0b; border-radius: 8px; padding: 8px 14px; margin-bottom: 14px; text-align: center;">
        <div style="font-size: 8.5pt; font-weight: bold; color: #b45309;">
            📄 DRAFT INVOICE — KHUSUS UNTUK KONFIRMASI DATA DENGAN PIC SEKOLAH
        </div>
        <div style="font-size: 7.2pt; color: #78350f; margin-top: 2px;">
            Dokumen ini merupakan rincian pra-tagihan sementara untuk validasi kehadiran & absensi siswa. Nomor resmi diterbitkan setelah persetujuan Operasional & Akunting.
        </div>
    </div>
    @endif

    {{-- ─── INFO GRID ──────────────────────────────────────────────────── --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="box-title">Ditujukan Kepada</div>
            <div class="box-value">{{ $invoice->sekolah?->namasekolah ?? '-' }}</div>
            <div class="box-sub">Kode: {{ $invoice->sekolah_kodlan ?? '-' }}</div>
            <div class="box-sub">{{ $invoice->sekolah?->alamat ?? '' }}</div>
        </div>
        <div class="info-box">
            <div class="box-title">Identitas Tagihan</div>
            <div class="box-value">{{ $invoice->program_nama }}</div>
            <div class="box-sub">{{ $invoice->total_rombel ?: ($invoice->items->count() ?: 1) }} Rombel · TA: {{ $invoice->tahun_ajaran }}</div>
            <div class="box-sub">
                Periode: <strong>{{ $invoice->periode_label }}</strong>
                @if($invoice->sesi_dari && $invoice->sesi_sampai)
                    (Sesi {{ $invoice->sesi_dari }}–{{ $invoice->sesi_sampai }})
                @endif
            </div>
        </div>
        <div class="info-box">
            <div class="box-title">Skema Tagihan</div>
            @php
                $skemaClass = match($invoice->skema_tagihan) {
                    'bulanan'         => 'skema-bulanan',
                    'semester'        => 'skema-semester',
                    'tahunan'         => 'skema-tahunan',
                    'per_4_pertemuan' => 'skema-per4',
                    default           => 'skema-per4',
                };
                $skemaText = match($invoice->skema_tagihan) {
                    'bulanan'         => 'Bulanan (Kalender)',
                    'semester'        => 'Per Semester (~16 sesi)',
                    'tahunan'         => 'Per Tahun (~32 sesi)',
                    'per_4_pertemuan' => 'Per 4 Pertemuan (Rolling)',
                    default           => '-',
                };
            @endphp
            <div class="box-value">
                <span class="skema-badge {{ $skemaClass }}">{{ $skemaText }}</span>
            </div>
            <div class="box-sub" style="margin-top:4px;">Total Sesi Selesai: {{ $invoice->jumlah_sesi }} Sesi</div>
        </div>
    </div>

    {{-- ─── TABEL RINCIAN ITEM PER ROMBEL ──────────────────────────────── --}}
    @if($invoice->items->isNotEmpty())
    <div style="margin-bottom: 16px;">
        <div class="section-title">Rincian Rombel yang Ditagihkan</div>
        <table style="width: 100%; border-collapse: collapse; font-size: 8pt;">
            <thead>
                <tr style="background: #1e3a8a; color: #fff; text-align: left;">
                    <th style="padding: 5px 8px; width: 25px; text-align: center;">#</th>
                    <th style="padding: 5px 8px;">Program</th>
                    <th style="padding: 5px 8px;">Rombel</th>
                    <th style="padding: 5px 8px; text-align: center;">Sesi</th>
                    <th style="padding: 5px 8px; text-align: right;">Siswa Billable</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $item)
                <tr style="border-bottom: 1px solid #e2e8f0; background: {{ $idx % 2 === 0 ? '#fff' : '#f8fafc' }};">
                    <td style="padding: 5px 8px; text-align: center; color: #64748b;">{{ $idx + 1 }}</td>
                    <td style="padding: 5px 8px; font-weight: 600; color: #1e3a8a;">
                        {{ $item->rombel?->ekstrakurikuler?->kategori_program ?? $invoice->program_nama }}
                    </td>
                    <td style="padding: 5px 8px;">
                        {{ $item->rombel?->nama_rombel ?? '-' }}
                    </td>
                    <td style="padding: 5px 8px; text-align: center;">
                        @if($item->sesi_dari && $item->sesi_sampai)
                            Sesi {{ $item->sesi_dari }}–{{ $item->sesi_sampai }} ({{ $item->jumlah_sesi }}x)
                        @else
                            {{ $item->jumlah_sesi }} Sesi
                        @endif
                    </td>
                    <td style="padding: 5px 8px; text-align: right; font-weight: 700; color: #1e3a8a;">
                        {{ $item->billable_efektif }} Siswa
                        @if($item->hasKoreksi())
                            <span style="font-size: 6.5pt; color: #b45309; display: block;">(koreksi manual)</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- ─── BILLING SUMMARY ────────────────────────────────────────────── --}}
    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="s-val">{{ $invoice->total_rombel ?: ($invoice->items->count() ?: 1) }}</div>
                <div class="s-label">Total Rombel</div>
                <div class="s-note">Kelas rombongan belajar</div>
            </div>
            <div class="divider-v"></div>
            <div class="summary-item">
                <div class="s-val">{{ $invoice->billable_efektif }}</div>
                <div class="s-label">Total Siswa Billable</div>
                <div class="s-note">
                    @if($invoice->hasKoreksi())
                        ⚠️ Disesuaikan manual
                    @else
                        Presensi sistem terverifikasi
                    @endif
                </div>
            </div>
            <div class="divider-v"></div>
            <div class="summary-item">
                <div class="s-val">{{ $invoice->jumlah_sesi }}</div>
                <div class="s-label">Jumlah Sesi Selesai</div>
                <div class="s-note">Sesuai rentang tagihan</div>
            </div>
        </div>
    </div>

    {{-- ─── CATATAN KOREKSI (jika ada) ────────────────────────────────── --}}
    @if($invoice->hasKoreksi())
    <div class="koreksi-note">
        ⚠️ <strong>Catatan Koreksi Data:</strong>
        Jumlah siswa billable telah dikoreksi menjadi <strong>{{ $invoice->billable_efektif }}</strong> oleh {{ $invoice->koreksiByUser?->nama_lengkap ?? $invoice->koreksiByUser?->name ?? 'Admin' }}
        pada {{ $invoice->koreksi_at?->translatedFormat('d F Y') }}.
        Alasan: {{ $invoice->koreksi_catatan }}
    </div>
    @endif

    {{-- ─── APPROVAL ───────────────────────────────────────────────────── --}}
    <div class="approval-section">
        <div class="section-title">Status Persetujuan</div>
        <div class="approval-grid">
            <div class="approval-box {{ $invoice->operasional_status === 'approved' ? 'approved' : 'pending' }}">
                <div class="ap-role">Operasional / Akademik (Konfirmasi PIC)</div>
                <div class="ap-name">{{ $invoice->operasionalUser?->nama_lengkap ?? $invoice->operasionalUser?->name ?? '-' }}</div>
                <div class="ap-date">{{ $invoice->operasional_approved_at?->translatedFormat('d F Y, H:i') ? $invoice->operasional_approved_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Menunggu konfirmasi' }}</div>
                <div class="ap-status">
                    @if($invoice->operasional_status === 'approved')
                        ✅ Disetujui (Konfirmasi PIC OK)
                    @else
                        ⏳ Menunggu Konfirmasi PIC
                    @endif
                </div>
                @if($invoice->pic_konfirmasi_nama)
                <div style="font-size:7pt; color:#475569; margin-top:3px;">
                    PIC Dihubungi: <strong>{{ $invoice->pic_konfirmasi_nama }}</strong>
                </div>
                @endif
                <div class="ttd-area">
                    <div class="ttd-label">Tanda Tangan Digital</div>
                </div>
            </div>
            <div class="approval-box {{ $invoice->akunting_status === 'approved' ? 'approved' : 'pending' }}">
                <div class="ap-role">Akunting / Finance (Cetak & Tagih)</div>
                <div class="ap-name">{{ $invoice->akuntingUser?->nama_lengkap ?? $invoice->akuntingUser?->name ?? '-' }}</div>
                <div class="ap-date">{{ $invoice->akunting_approved_at?->translatedFormat('d F Y, H:i') ? $invoice->akunting_approved_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Menunggu approval' }}</div>
                <div class="ap-status">
                    @if($invoice->akunting_status === 'approved')
                        ✅ Disetujui & Siap Ditagihkan
                    @else
                        ⏳ Menunggu Cetak & Approval Akunting
                    @endif
                </div>
                @if($invoice->akunting_catatan)
                <div style="font-size:7pt; color:#64748b; margin-top:3px;">Catatan: {{ $invoice->akunting_catatan }}</div>
                @endif
                <div class="ttd-area">
                    <div class="ttd-label">Tanda Tangan Digital</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── CATATAN KONTRAK WAJIB ──────────────────────────────────────── --}}
    <div class="kontrak-box">
        <div class="kontrak-title">⚠️ CATATAN KOMITMEN KONTRAK (TIDAK BOLEH ADA PEMBATALAN)</div>
        <div class="kontrak-text">
            Seluruh sesi pembelajaran yang telah dijadwalkan mengikat alokasi penugasan instruktur
            dan sarana belajar Erlass Prokreatif Indonesia. Sesi pembelajaran
            <strong>TIDAK DAPAT DIBATALKAN</strong> secara sepihak untuk pengurangan biaya tagihan.
            Apabila terdapat kendala operasional internal sekolah (seperti kegiatan porseni, ujian sekolah,
            atau libur insidental), pertemuan wajib dialihkan ke tanggal pengganti melalui prosedur
            <strong>Reschedule resmi</strong>.
        </div>
    </div>

    {{-- ─── FOOTER ─────────────────────────────────────────────────────── --}}
    <div class="footer">
        <div class="footer-left">
            Digenerate: {{ now()->translatedFormat('d F Y, H:i') }} WIB · No. Invoice: {{ $invoice->nomor_invoice }}
        </div>
        <div class="footer-right">
            <strong>PT. Erlass Prokreatif Indonesia</strong> · Halaman 1 (Lembar Tagihan)
        </div>
    </div>

</div>

{{-- ═════════════════════════════════════════════════════════════════════════ --}}
{{-- HALAMAN 2 dst: LAMPIRAN RINCIAN PRESENSI & LAPORAN MENGAJAR               --}}
{{-- (Mengadopsi format cetak absensi ekstrakurikuler-session/print)           --}}
{{-- ═════════════════════════════════════════════════════════════════════════ --}}
@if(!empty($attendanceData))
@foreach($attendanceData as $attIndex => $att)
<div class="page page-break" style="padding: 22px 30px;">

    {{-- Header Lampiran --}}
    <table style="width: 100%; border-collapse: collapse; border-bottom: 2px solid #1e3a8a; padding-bottom: 6px; margin-bottom: 10px;">
        <tr>
            <td style="vertical-align: bottom;">
                <div style="font-size: 11pt; font-weight: 700; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.5px;">
                    LAMPIRAN: REKAPITULASI PRESENSI & LAPORAN MENGAJAR
                </div>
                <div style="font-size: 7.5pt; color: #475569; margin-top: 1px;">
                    Lampiran Pendukung Invoice: <strong>{{ $invoice->nomor_invoice }}</strong>
                </div>
            </td>
            <td style="vertical-align: bottom; text-align: right; width: 220px;">
                <div style="font-size: 7pt; color: #64748b;">Program / Rombel</div>
                <div style="font-size: 8.5pt; font-weight: 700; color: #1e3a8a;">
                    {{ $att['rombel_nama'] }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Meta Grid Sekolah & Program --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 7.5pt; background: #f8fafc; border: 1px solid #cbd5e1;">
        <tr>
            <td style="padding: 5px 8px; width: 50%; vertical-align: top; border-right: 1px solid #e2e8f0;">
                <div><span style="color:#64748b; width: 95px; display:inline-block;">Nama Sekolah</span>: <strong>{{ $att['school_name'] }}</strong></div>
                <div style="margin-top: 2px;"><span style="color:#64748b; width: 95px; display:inline-block;">Program</span>: <strong>{{ $att['program_nama'] }}</strong></div>
                <div style="margin-top: 2px;"><span style="color:#64748b; width: 95px; display:inline-block;">Rombel</span>: <strong>{{ $att['rombel_nama'] }}</strong></div>
            </td>
            <td style="padding: 5px 8px; width: 50%; vertical-align: top;">
                <div><span style="color:#64748b; width: 95px; display:inline-block;">Instruktur</span>: <strong>{{ $att['instructor_name'] }}</strong></div>
                <div style="margin-top: 2px;"><span style="color:#64748b; width: 95px; display:inline-block;">PIC Sekolah</span>: <strong>{{ $att['pic_name'] }}</strong></div>
                <div style="margin-top: 2px;"><span style="color:#64748b; width: 95px; display:inline-block;">Periode / Sesi</span>: <strong>{{ $invoice->periode_label }} @if($invoice->sesi_dari && $invoice->sesi_sampai)(Sesi {{ $invoice->sesi_dari }}–{{ $invoice->sesi_sampai }})@endif</strong></div>
            </td>
        </tr>
    </table>

    @php
        $sessions = $att['sessions'];
        $students = $att['students'];
        $attendanceMap = $att['attendanceMap'];
        $colCount = max(1, $sessions->count());
        $colWidth = round(36 / $colCount, 1);
    @endphp

    {{-- Tabel Presensi Siswa --}}
    <div style="margin-bottom: 12px;">
        <div style="font-size: 8.5pt; font-weight: 700; color: #1e3a8a; margin-bottom: 4px;">
            Daftar Kehadiran Siswa (Presensi Sesi)
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 7pt; table-layout: fixed;">
            <thead>
                <tr style="background: #1e3a8a; color: white; text-align: center;">
                    <th rowspan="2" style="border: 1px solid #475569; padding: 3px 2px; width: 4%;">No</th>
                    <th rowspan="2" style="border: 1px solid #475569; padding: 3px 5px; text-align: left; width: 38%;">Nama Lengkap Siswa</th>
                    <th rowspan="2" style="border: 1px solid #475569; padding: 3px 2px; width: 10%;">Kelas</th>
                    @foreach($sessions as $sess)
                        <th style="border: 1px solid #475569; padding: 2px; width: {{ $colWidth }}%;">
                            Pert. {{ $sess->nomor_pertemuan }}
                        </th>
                    @endforeach
                    <th rowspan="2" style="border: 1px solid #475569; padding: 3px 2px; width: 8%;">Hadir</th>
                    <th rowspan="2" style="border: 1px solid #475569; padding: 3px 2px; width: 6%;">Status</th>
                </tr>
                <tr style="background: #e2e8f0; color: #1e293b; font-size: 6.5pt; text-align: center;">
                    @foreach($sessions as $sess)
                        @php
                            $tgl = $sess->tanggal_pelaksanaan ?? $sess->tanggal_terjadwal;
                            $tglShort = $tgl ? (\Carbon\Carbon::parse($tgl)->format('d/m')) : '-';
                        @endphp
                        <th style="border: 1px solid #cbd5e1; padding: 2px;">
                            {{ $tglShort }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($students as $stIdx => $student)
                @php
                    $hadirCount = 0;
                @endphp
                <tr style="background: {{ $stIdx % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                    <td style="border: 1px solid #cbd5e1; text-align: center; padding: 2.5px 2px;">{{ $stIdx + 1 }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 2.5px 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $student->nama_lengkap }}
                    </td>
                    <td style="border: 1px solid #cbd5e1; text-align: center; padding: 2.5px 2px;">
                        {{ $student->kelas ?? $student->rombel ?? '-' }}
                    </td>
                    @foreach($sessions as $sess)
                        @php
                            $stId = $student->id;
                            $stHadir = $attendanceMap[$sess->id][$stId] ?? null;
                            if ($stHadir === 1) $hadirCount++;
                        @endphp
                        <td style="border: 1px solid #cbd5e1; text-align: center; padding: 1.5px;">
                            @if($stHadir === 1)
                                <span style="font-weight: bold; color: #166534; font-size: 7.5pt;">&#10003;</span>
                            @elseif($stHadir === 0)
                                <span style="color: #dc2626; font-weight: bold; font-size: 7pt;">&times;</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                    @endforeach
                    <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #1e3a8a; padding: 2.5px 2px;">
                        {{ $hadirCount }}
                    </td>
                    <td style="border: 1px solid #cbd5e1; text-align: center; padding: 2.5px 2px; font-size: 6.5pt;">
                        @if(isset($student->pivot) && $student->pivot->status === 'keluar')
                            <span style="color: #dc2626; font-weight: 600;">Keluar</span>
                        @else
                            <span style="color: #166534;">Aktif</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 4 + $sessions->count() }}" style="border: 1px solid #cbd5e1; text-align: center; padding: 8px; color: #94a3b8;">
                        Tidak ada data siswa terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Rincian Laporan Mengajar Tiap Pertemuan --}}
    <div style="margin-bottom: 12px;">
        <div style="font-size: 8.5pt; font-weight: 700; color: #1e3a8a; margin-bottom: 4px;">
            Rincian Pelaksanaan Materi Mengajar Tiap Sesi
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 7pt;">
            <thead>
                <tr style="background: #f1f5f9; color: #1e3a8a; border-bottom: 1.5px solid #cbd5e1; text-align: left;">
                    <th style="border: 1px solid #cbd5e1; padding: 3px 5px; width: 14%;">Pertemuan</th>
                    <th style="border: 1px solid #cbd5e1; padding: 3px 5px; width: 14%;">Tanggal</th>
                    <th style="border: 1px solid #cbd5e1; padding: 3px 5px; width: 20%;">Instruktur</th>
                    <th style="border: 1px solid #cbd5e1; padding: 3px 5px; width: 40%;">Materi Pokok Bahasan</th>
                    <th style="border: 1px solid #cbd5e1; padding: 3px 5px; width: 12%; text-align: center;">Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($att['sessionReports'] as $report)
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 3px 5px; font-weight: bold; color: #1e3a8a;">
                        Pertemuan {{ $report['nomor_pertemuan'] }}
                    </td>
                    <td style="border: 1px solid #cbd5e1; padding: 3px 5px;">
                        {{ $report['tanggal'] }}
                    </td>
                    <td style="border: 1px solid #cbd5e1; padding: 3px 5px;">
                        {{ $report['instruktur'] }}
                    </td>
                    <td style="border: 1px solid #cbd5e1; padding: 3px 5px;">
                        {{ $report['materi'] }}
                    </td>
                    <td style="border: 1px solid #cbd5e1; padding: 3px 5px; text-align: center; font-weight: bold; color: #166534;">
                        {{ $report['total_hadir'] }} Hadir
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tanda Tangan Konfirmasi Lampiran --}}
    <table style="width: 100%; border-collapse: collapse; border: none; margin-top: 8px;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top; border: none; padding-right: 40px;">
                <div style="font-size: 7.5pt; color: #64748b; margin-bottom: 30px;">
                    Mengetahui & Memvalidasi,<br><strong>PIC Sekolah / Koordinator</strong>
                </div>
                <div style="border-top: 1px solid #334155; padding-top: 2px; font-size: 7.5pt; font-weight: bold; color: #0f172a;">
                    {{ $att['pic_name'] ?: '( ........................................ )' }}
                </div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top; border: none; padding-left: 40px;">
                <div style="font-size: 7.5pt; color: #64748b; margin-bottom: 30px;">
                    Diverifikasi Oleh,<br><strong>Instruktur Pengajar</strong>
                </div>
                <div style="border-top: 1px solid #334155; padding-top: 2px; font-size: 7.5pt; font-weight: bold; color: #0f172a;">
                    {{ $att['instructor_name'] ?: '( ........................................ )' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Footer Lampiran --}}
    <div class="footer" style="margin-top: 10px;">
        <div class="footer-left">
            Lampiran Invoice: {{ $invoice->nomor_invoice }} · Rombel: {{ $att['rombel_nama'] }}
        </div>
        <div class="footer-right">
            Halaman {{ $attIndex + 2 }} (Lampiran Presensi & Laporan)
        </div>
    </div>

</div>
@endforeach
@endif

</body>
</html>
