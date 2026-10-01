<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->nomor_invoice }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 8pt;
            line-height: 1.25;
            color: #1e293b;
            background: #ffffff;
        }

        /* ── TABLE BASE UTILITIES ───────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            vertical-align: top;
        }

        /* ── BADGES & STATUS ────────────────────────────────────── */
        .skema-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .skema-bulanan { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .skema-semester { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }
        .skema-tahunan { background: #e2e8f0; color: #334155; border: 1px solid #cbd5e1; }
        .skema-per4 { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .status-draft {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
        }
        .status-approved {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        /* ── SECTION TITLE ──────────────────────────────────────── */
        .section-heading {
            font-size: 7.8pt;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 2px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* ── AVOID BREAKS FOR CARDS ─────────────────────────────── */
        .keep-together {
            page-break-inside: avoid;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

{{-- ═════════════════════════════════════════════════════════════════════════ --}}
{{-- HALAMAN 1: LEMBAR UTAMA TAGIHAN (INVOICE SHEET)                           --}}
{{-- Dirancang pas 1 halaman A4 dengan pure-table layout                      --}}
{{-- ═════════════════════════════════════════════════════════════════════════ --}}
<div class="keep-together">

    {{-- ─── 1. HEADER PERUSAHAAN & IDENTITAS INVOICE ───────────────────── --}}
    <table style="border-bottom: 2.5px solid #1e3a8a; padding-bottom: 5px; margin-bottom: 7px;">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div style="font-size: 16pt; font-weight: bold; color: #1e3a8a; letter-spacing: -0.5px; line-height: 1;">ERLASS</div>
                <div style="font-size: 7.8pt; font-weight: bold; color: #334155; margin-top: 1px;">PT. Erlass Prokreatif Indonesia</div>
                <div style="font-size: 6.8pt; color: #64748b; margin-top: 1px;">
                    Erlass Institute &middot; Jakarta &amp; Bali, Indonesia &middot; erlass.institute
                </div>
            </td>
            <td style="width: 45%; vertical-align: top; text-align: right;">
                <div style="font-size: 6.5pt; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: bold;">
                    NOMOR INVOICE
                </div>
                <div style="font-size: 11.5pt; font-weight: bold; color: #1e3a8a; line-height: 1.2;">
                    {{ $invoice->nomor_invoice }}
                </div>
                <div style="margin-top: 3px;">
                    @if($isDraft ?? false)
                        <span class="status-badge status-draft">DRAFT &mdash; KONFIRMASI PIC</span>
                        <div style="font-size: 6.2pt; color: #b45309; margin-top: 1px; font-weight: bold;">
                            Dokumen Pra-Tagihan (Konfirmasi PIC)
                        </div>
                    @else
                        <span class="status-badge status-approved">APPROVED &mdash; SIAP DITAGIHKAN</span>
                        <div style="font-size: 6.2pt; color: #64748b; margin-top: 1px;">
                            Diterbitkan: {{ $invoice->akunting_approved_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- ─── 2. DRAFT BANNER NOTICE (khusus draft) ───────────────────────── --}}
    @if($isDraft ?? false)
    <table style="margin-bottom: 7px; background: #fffbeb; border: 1px dashed #d97706; border-radius: 4px;">
        <tr>
            <td style="padding: 4px 7px; text-align: center;">
                <div style="font-size: 7pt; font-weight: bold; color: #92400e; letter-spacing: 0.3px;">
                    [DRAFT INVOICE] KHUSUS UNTUK KONFIRMASI DATA DENGAN PIC SEKOLAH
                </div>
                <div style="font-size: 6.4pt; color: #78350f; margin-top: 1px; line-height: 1.25;">
                    Dokumen ini adalah rincian pra-tagihan sementara untuk validasi kehadiran siswa. Nomor resmi diterbitkan setelah persetujuan Operasional &amp; Akunting.
                </div>
            </td>
        </tr>
    </table>
    @endif

    {{-- ─── 3. INFO BOX 3-KOLOM (Table murni side-by-side) ──────────────── --}}
    <table style="margin-bottom: 7px;">
        <tr>
            {{-- Kolom 1: Ditujukan Kepada --}}
            <td style="width: 32%; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 5px 7px;">
                <div style="font-size: 6.3pt; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.4px;">
                    DITUJUKAN KEPADA
                </div>
                <div style="font-size: 8.2pt; font-weight: bold; color: #1e3a8a; margin-top: 2px; line-height: 1.2;">
                    {{ $invoice->sekolah?->namasekolah ?? '-' }}
                </div>
                <div style="font-size: 6.6pt; color: #475569; margin-top: 2px;">
                    Kode: <strong>{{ $invoice->sekolah_kodlan ?? '-' }}</strong>
                </div>
                <div style="font-size: 6.3pt; color: #64748b; margin-top: 1px; line-height: 1.2;">
                    {{ Str::limit($invoice->sekolah?->alamat ?? '', 80) }}
                </div>
            </td>

            <td style="width: 2%;"></td>

            {{-- Kolom 2: Identitas Tagihan --}}
            <td style="width: 32%; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 5px 7px;">
                <div style="font-size: 6.3pt; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.4px;">
                    IDENTITAS TAGIHAN
                </div>
                <div style="font-size: 8.2pt; font-weight: bold; color: #1e3a8a; margin-top: 2px; line-height: 1.2;">
                    {{ $invoice->program_nama }}
                </div>
                <div style="font-size: 6.6pt; color: #475569; margin-top: 2px;">
                    {{ $invoice->total_rombel ?: ($invoice->items->count() ?: 1) }} Rombel &middot; TA: {{ $invoice->tahun_ajaran }}
                </div>
                <div style="font-size: 6.6pt; color: #475569; margin-top: 1px;">
                    Periode: <strong>{{ $invoice->periode_label }}</strong>
                    @if($invoice->sesi_dari && $invoice->sesi_sampai)
                        <span style="color: #1e3a8a;">(Sesi {{ $invoice->sesi_dari }}&ndash;{{ $invoice->sesi_sampai }})</span>
                    @endif
                </div>
            </td>

            <td style="width: 2%;"></td>

            {{-- Kolom 3: Skema Tagihan --}}
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
            <td style="width: 32%; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 5px 7px;">
                <div style="font-size: 6.3pt; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.4px;">
                    SKEMA TAGIHAN
                </div>
                <div style="margin-top: 2px;">
                    <span class="skema-badge {{ $skemaClass }}">{{ $skemaText }}</span>
                </div>
                <div style="font-size: 6.6pt; color: #475569; margin-top: 3px;">
                    Total Sesi Ditagih: <strong>{{ $invoice->jumlah_sesi }} Sesi</strong>
                </div>
            </td>
        </tr>
    </table>

    {{-- ─── 4. TABEL RINCIAN ITEM PER ROMBEL ────────────────────────────── --}}
    @if($invoice->items->isNotEmpty())
    <div style="margin-bottom: 7px;">
        <div class="section-heading">Rincian Rombel yang Ditagihkan</div>
        <table style="font-size: 7.2pt; border: 1px solid #cbd5e1;">
            <thead>
                <tr style="background: #1e3a8a; color: #ffffff;">
                    <th style="padding: 3.5px 5px; width: 22px; text-align: center; border: 1px solid #1e3a8a;">#</th>
                    <th style="padding: 3.5px 6px; text-align: left; border: 1px solid #1e3a8a;">Program</th>
                    <th style="padding: 3.5px 6px; text-align: left; border: 1px solid #1e3a8a;">Rombel</th>
                    <th style="padding: 3.5px 6px; text-align: center; border: 1px solid #1e3a8a; width: 140px;">Rentang Sesi</th>
                    <th style="padding: 3.5px 6px; text-align: right; border: 1px solid #1e3a8a; width: 100px;">Siswa Billable</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $item)
                <tr style="background: {{ $idx % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                    <td style="padding: 3px 5px; text-align: center; border: 1px solid #e2e8f0; color: #64748b;">{{ $idx + 1 }}</td>
                    <td style="padding: 3px 6px; border: 1px solid #e2e8f0; font-weight: bold; color: #1e3a8a;">
                        {{ $item->rombel?->ekstrakurikuler?->kategori_program ?? $invoice->program_nama }}
                    </td>
                    <td style="padding: 3px 6px; border: 1px solid #e2e8f0;">
                        {{ $item->rombel?->nama_rombel ?? '-' }}
                    </td>
                    <td style="padding: 3px 6px; text-align: center; border: 1px solid #e2e8f0;">
                        @if($item->sesi_dari && $item->sesi_sampai)
                            Sesi {{ $item->sesi_dari }}&ndash;{{ $item->sesi_sampai }} ({{ $item->jumlah_sesi }}x)
                        @else
                            {{ $item->jumlah_sesi }} Sesi
                        @endif
                    </td>
                    <td style="padding: 3px 6px; text-align: right; border: 1px solid #e2e8f0; font-weight: bold; color: #1e3a8a;">
                        {{ $item->billable_efektif }} Siswa
                        @if($item->hasKoreksi())
                            <span style="font-size: 5.8pt; color: #b45309; font-weight: normal; display: block;">(koreksi manual)</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- ─── 5. BILLING SUMMARY (3 Kolom Bersebelahan Murni Table) ───────── --}}
    <table style="margin-bottom: 7px; background: #f0f7ff; border: 1.5px solid #bfdbfe; border-radius: 5px;">
        <tr>
            <td style="width: 33.3%; text-align: center; padding: 5px 6px; border-right: 1px solid #bfdbfe; vertical-align: middle;">
                <div style="font-size: 14pt; font-weight: bold; color: #1e3a8a; line-height: 1;">
                    {{ $invoice->total_rombel ?: ($invoice->items->count() ?: 1) }}
                </div>
                <div style="font-size: 6.6pt; font-weight: bold; color: #475569; text-transform: uppercase; margin-top: 1px;">
                    Total Rombel
                </div>
                <div style="font-size: 6pt; color: #94a3b8;">
                    Kelas rombongan belajar
                </div>
            </td>
            <td style="width: 33.4%; text-align: center; padding: 5px 6px; border-right: 1px solid #bfdbfe; vertical-align: middle;">
                <div style="font-size: 14pt; font-weight: bold; color: #1e3a8a; line-height: 1;">
                    {{ $invoice->billable_efektif }}
                </div>
                <div style="font-size: 6.6pt; font-weight: bold; color: #475569; text-transform: uppercase; margin-top: 1px;">
                    Total Siswa Billable
                </div>
                <div style="font-size: 6pt; color: #94a3b8;">
                    @if($invoice->hasKoreksi())
                        <span style="color: #b45309; font-weight: bold;">Disesuaikan manual</span>
                    @else
                        Presensi sistem terverifikasi
                    @endif
                </div>
            </td>
            <td style="width: 33.3%; text-align: center; padding: 5px 6px; vertical-align: middle;">
                <div style="font-size: 14pt; font-weight: bold; color: #1e3a8a; line-height: 1;">
                    {{ $invoice->jumlah_sesi }}
                </div>
                <div style="font-size: 6.6pt; font-weight: bold; color: #475569; text-transform: uppercase; margin-top: 1px;">
                    Jumlah Sesi Ditagih
                </div>
                <div style="font-size: 6pt; color: #94a3b8;">
                    Sesuai rentang tagihan
                </div>
            </td>
        </tr>
    </table>

    {{-- ─── 6. CATATAN KOREKSI (jika ada) ──────────────────────────────── --}}
    @if($invoice->hasKoreksi())
    <div style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 4px; padding: 4px 6px; margin-bottom: 7px; font-size: 6.6pt; color: #92400e; line-height: 1.25;">
        <strong>Catatan Koreksi Data:</strong> Jumlah siswa billable telah dikoreksi menjadi <strong>{{ $invoice->billable_efektif }}</strong> oleh {{ $invoice->koreksiByUser?->nama_lengkap ?? $invoice->koreksiByUser?->name ?? 'Admin' }} pada {{ $invoice->koreksi_at?->translatedFormat('d F Y') }}.
        Alasan: <em>{{ $invoice->koreksi_catatan }}</em>
    </div>
    @endif

    {{-- ─── 7. STATUS PERSETUJUAN (2 Kolom Bersebelahan Murni Table) ────── --}}
    <div style="margin-bottom: 7px;">
        <div class="section-heading">Status Persetujuan Dokumen</div>
        <table>
            <tr>
                {{-- Box Operasional --}}
                <td style="width: 49%; vertical-align: top; border: 1.5px solid {{ $invoice->operasional_status === 'approved' ? '#86efac' : '#fde68a' }}; background: {{ $invoice->operasional_status === 'approved' ? '#f0fdf4' : '#fffbeb' }}; border-radius: 5px; padding: 5px 7px;">
                    <div style="font-size: 6.3pt; color: #64748b; text-transform: uppercase; font-weight: bold;">
                        1. Operasional / Akademik (Konfirmasi PIC)
                    </div>
                    <div style="font-size: 7.8pt; font-weight: bold; color: #1e3a8a; margin-top: 1px;">
                        {{ $invoice->operasionalUser?->nama_lengkap ?? $invoice->operasionalUser?->name ?? '-' }}
                    </div>
                    <div style="font-size: 6.3pt; color: #64748b; margin-top: 1px;">
                        {{ $invoice->operasional_approved_at?->translatedFormat('d F Y, H:i') ? $invoice->operasional_approved_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Menunggu konfirmasi' }}
                    </div>
                    <div style="font-size: 6.6pt; font-weight: bold; margin-top: 2px; color: {{ $invoice->operasional_status === 'approved' ? '#166534' : '#b45309' }};">
                        @if($invoice->operasional_status === 'approved')
                            [OK] Disetujui (Konfirmasi PIC Selesai)
                        @else
                            [PROSES] Menunggu Konfirmasi PIC Sekolah
                        @endif
                    </div>
                    @if($invoice->pic_konfirmasi_nama)
                    <div style="font-size: 6.3pt; color: #475569; margin-top: 1px;">
                        PIC Dihubungi: <strong>{{ $invoice->pic_konfirmasi_nama }}</strong>
                    </div>
                    @endif
                    <div style="border-top: 1px dashed #cbd5e1; margin-top: 5px; padding-top: 1px; text-align: center; font-size: 5.8pt; color: #94a3b8;">
                        Tanda Tangan Digital Operasional
                    </div>
                </td>

                <td style="width: 2%;"></td>

                {{-- Box Akunting --}}
                <td style="width: 49%; vertical-align: top; border: 1.5px solid {{ $invoice->akunting_status === 'approved' ? '#86efac' : '#fde68a' }}; background: {{ $invoice->akunting_status === 'approved' ? '#f0fdf4' : '#fffbeb' }}; border-radius: 5px; padding: 5px 7px;">
                    <div style="font-size: 6.3pt; color: #64748b; text-transform: uppercase; font-weight: bold;">
                        2. Akunting / Finance (Cetak &amp; Tagih)
                    </div>
                    <div style="font-size: 7.8pt; font-weight: bold; color: #1e3a8a; margin-top: 1px;">
                        {{ $invoice->akuntingUser?->nama_lengkap ?? $invoice->akuntingUser?->name ?? '-' }}
                    </div>
                    <div style="font-size: 6.3pt; color: #64748b; margin-top: 1px;">
                        {{ $invoice->akunting_approved_at?->translatedFormat('d F Y, H:i') ? $invoice->akunting_approved_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Menunggu approval' }}
                    </div>
                    <div style="font-size: 6.6pt; font-weight: bold; margin-top: 2px; color: {{ $invoice->akunting_status === 'approved' ? '#166534' : '#b45309' }};">
                        @if($invoice->akunting_status === 'approved')
                            [OK] Disetujui &amp; Invoice Resmi Tercetak
                        @else
                            [PROSES] Menunggu Cetak &amp; Approval Akunting
                        @endif
                    </div>
                    @if($invoice->akunting_catatan)
                    <div style="font-size: 6.3pt; color: #64748b; margin-top: 1px;">
                        Catatan: {{ $invoice->akunting_catatan }}
                    </div>
                    @endif
                    <div style="border-top: 1px dashed #cbd5e1; margin-top: 5px; padding-top: 1px; text-align: center; font-size: 5.8pt; color: #94a3b8;">
                        Tanda Tangan Digital Akunting
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ─── 8. CATATAN KONTRAK WAJIB ───────────────────────────────────── --}}
    <div style="border: 1px solid #fca5a5; border-radius: 4px; background: #fff7f7; padding: 4px 7px; margin-bottom: 5px;">
        <div style="font-size: 6.6pt; font-weight: bold; color: #b91c1c; text-transform: uppercase; letter-spacing: 0.3px;">
            CATATAN KOMITMEN KONTRAK (TIDAK BOLEH ADA PEMBATALAN SEPIHAK)
        </div>
        <div style="font-size: 6.3pt; color: #374151; line-height: 1.25; margin-top: 1px;">
            Seluruh sesi pembelajaran yang telah dijadwalkan mengikat alokasi penugasan instruktur dan sarana belajar Erlass Prokreatif Indonesia. Sesi pembelajaran <strong style="color: #b91c1c;">TIDAK DAPAT DIBATALKAN</strong> secara sepihak untuk pengurangan biaya tagihan. Apabila terdapat kendala operasional internal sekolah (seperti kegiatan porseni, ujian sekolah, atau libur insidental), pertemuan wajib dialihkan ke tanggal pengganti melalui prosedur <strong style="color: #b91c1c;">Reschedule resmi</strong>.
        </div>
    </div>

    {{-- ─── 9. FOOTER LEMBAR TAGIHAN ───────────────────────────────────── --}}
    <table style="border-top: 1px solid #cbd5e1; padding-top: 3px; margin-top: 3px;">
        <tr>
            <td style="font-size: 6pt; color: #94a3b8; vertical-align: middle;">
                Digenerate: {{ now()->translatedFormat('d F Y, H:i') }} WIB &middot; No. Invoice: {{ $invoice->nomor_invoice }}
            </td>
            <td style="font-size: 6pt; color: #94a3b8; text-align: right; vertical-align: middle;">
                <strong>PT. Erlass Prokreatif Indonesia</strong> &middot; Halaman 1 (Lembar Tagihan)
            </td>
        </tr>
    </table>

</div>

{{-- ═════════════════════════════════════════════════════════════════════════ --}}
{{-- HALAMAN 2 dst: LAMPIRAN RINCIAN PRESENSI & LAPORAN MENGAJAR               --}}
{{-- (Mengadopsi format cetak absensi ekstrakurikuler-session/print)           --}}
{{-- ═════════════════════════════════════════════════════════════════════════ --}}
@if(!empty($attendanceData))
@foreach($attendanceData as $attIndex => $att)
<div class="page-break" style="padding-top: 2px;">

    {{-- Header Lampiran --}}
    <table style="border-bottom: 2px solid #1e3a8a; padding-bottom: 4px; margin-bottom: 4px;">
        <tr>
            <td style="vertical-align: bottom;">
                <div style="font-size: 9.5pt; font-weight: bold; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.3px;">
                    LAMPIRAN: REKAPITULASI PRESENSI &amp; LAPORAN MENGAJAR
                </div>
                <div style="font-size: 6.5pt; color: #475569; margin-top: 1px;">
                    Lampiran Pendukung Invoice: <strong>{{ $invoice->nomor_invoice }}</strong>
                </div>
            </td>
            <td style="vertical-align: bottom; text-align: right; width: 200px;">
                <div style="font-size: 6pt; color: #64748b;">Program / Rombel</div>
                <div style="font-size: 8pt; font-weight: bold; color: #1e3a8a;">
                    {{ $att['rombel_nama'] }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Meta Grid Sekolah & Program --}}
    <table style="margin-bottom: 5px; font-size: 6.8pt; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px;">
        <tr>
            <td style="padding: 3px 6px; width: 50%; vertical-align: top; border-right: 1px solid #e2e8f0;">
                <div><span style="color:#64748b; width: 80px; display:inline-block;">Nama Sekolah</span>: <strong>{{ $att['school_name'] }}</strong></div>
                <div style="margin-top: 1px;"><span style="color:#64748b; width: 80px; display:inline-block;">Program</span>: <strong>{{ $att['program_nama'] }}</strong></div>
                <div style="margin-top: 1px;"><span style="color:#64748b; width: 80px; display:inline-block;">Rombel</span>: <strong>{{ $att['rombel_nama'] }}</strong></div>
            </td>
            <td style="padding: 3px 6px; width: 50%; vertical-align: top;">
                <div><span style="color:#64748b; width: 80px; display:inline-block;">Instruktur</span>: <strong>{{ $att['instructor_name'] }}</strong></div>
                <div style="margin-top: 1px;"><span style="color:#64748b; width: 80px; display:inline-block;">PIC Sekolah</span>: <strong>{{ $att['pic_name'] }}</strong></div>
                <div style="margin-top: 1px;"><span style="color:#64748b; width: 80px; display:inline-block;">Periode / Sesi</span>: <strong>{{ $invoice->periode_label }} @if($invoice->sesi_dari && $invoice->sesi_sampai)(Sesi {{ $invoice->sesi_dari }}&ndash;{{ $invoice->sesi_sampai }})@endif</strong></div>
            </td>
        </tr>
    </table>

    @php
        $sessions = $att['sessions'];
        $students = $att['students'];
        $attendanceMap = $att['attendanceMap'];
        $colCount = max(1, $sessions->count());
        $colWidth = round(34 / $colCount, 1);
        $totalStudents = $students->count();

        // Dynamic styling so up to 42 students comfortably fit on 1 page along with the report and signatures
        if ($totalStudents > 35) {
            $rowPadding = '1px 2px';
            $rowFontSize = '6.2pt';
            $lineHeight = '1.05';
        } elseif ($totalStudents > 25) {
            $rowPadding = '1.5px 3px';
            $rowFontSize = '6.6pt';
            $lineHeight = '1.1';
        } else {
            $rowPadding = '2px 4px';
            $rowFontSize = '7pt';
            $lineHeight = '1.2';
        }
    @endphp

    {{-- Tabel Presensi Siswa --}}
    <div style="margin-bottom: 5px;">
        <div style="font-size: 7.2pt; font-weight: bold; color: #1e3a8a; margin-bottom: 2px;">
            Daftar Kehadiran Siswa (Presensi Sesi)
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: {{ $rowFontSize }}; line-height: {{ $lineHeight }}; table-layout: fixed;">
            <thead>
                <tr style="background: #1e3a8a; color: white; text-align: center;">
                    <th style="border: 1px solid #475569; padding: 2px 2px; width: 4%;">No</th>
                    <th style="border: 1px solid #475569; padding: 2px 4px; text-align: left; width: 38%;">Nama Lengkap Siswa</th>
                    <th style="border: 1px solid #475569; padding: 2px 2px; width: 9%;">Kelas</th>
                    @foreach($sessions as $sess)
                        @php
                            $tgl = $sess->tanggal_pelaksanaan ?? $sess->tanggal_terjadwal;
                            $tglShort = $tgl ? (\Carbon\Carbon::parse($tgl)->format('d/m')) : '-';
                        @endphp
                        <th style="border: 1px solid #475569; padding: 1.5px 1px; width: {{ $colWidth }}%;">
                            Pert. {{ $sess->nomor_pertemuan }}<br>
                            <span style="font-size: 5.5pt; font-weight: normal; color: #93c5fd;">{{ $tglShort }}</span>
                        </th>
                    @endforeach
                    <th style="border: 1px solid #475569; padding: 2px 2px; width: 7%;">Hadir</th>
                    <th style="border: 1px solid #475569; padding: 2px 2px; width: 6%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $stIdx => $student)
                @php
                    $hadirCount = 0;
                @endphp
                <tr style="background: {{ $stIdx % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                    <td style="border: 1px solid #cbd5e1; text-align: center; padding: {{ $rowPadding }};">{{ $stIdx + 1 }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: {{ $rowPadding }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $student->nama_lengkap }}
                    </td>
                    <td style="border: 1px solid #cbd5e1; text-align: center; padding: {{ $rowPadding }};">
                        {{ $student->kelas ?? $student->rombel ?? '-' }}
                    </td>
                    @foreach($sessions as $sess)
                        @php
                            $stId = $student->id;
                            $stHadir = $attendanceMap[$sess->id][$stId] ?? null;
                            if ($stHadir === 1) $hadirCount++;
                        @endphp
                        <td style="border: 1px solid #cbd5e1; text-align: center; padding: 1px;">
                            @if($stHadir === 1)
                                <span style="font-weight: bold; color: #166534; font-size: 7.5pt;">&#10003;</span>
                            @elseif($stHadir === 0)
                                <span style="color: #dc2626; font-weight: bold; font-size: 7pt;">&times;</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                    @endforeach
                    <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #1e3a8a; padding: {{ $rowPadding }};">
                        {{ $hadirCount }}
                    </td>
                    <td style="border: 1px solid #cbd5e1; text-align: center; padding: {{ $rowPadding }}; font-size: 5.8pt;">
                        @if(isset($student->pivot) && $student->pivot->status === 'keluar')
                            <span style="color: #dc2626; font-weight: bold;">Keluar</span>
                        @else
                            <span style="color: #166534; font-weight: bold;">Aktif</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 4 + $sessions->count() }}" style="border: 1px solid #cbd5e1; text-align: center; padding: 5px; color: #94a3b8;">
                        Tidak ada data siswa terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Rincian Pelaksanaan Materi Mengajar Tiap Sesi & Tanda Tangan --}}
    <div class="keep-together">
        <div style="margin-bottom: 5px;">
            <div style="font-size: 7.2pt; font-weight: bold; color: #1e3a8a; margin-bottom: 2px;">
                Rincian Pelaksanaan Materi Mengajar Tiap Sesi
            </div>
            <table style="font-size: 6.6pt; border: 1px solid #cbd5e1;">
                <thead>
                    <tr style="background: #f1f5f9; color: #1e3a8a; text-align: left;">
                        <th style="border: 1px solid #cbd5e1; padding: 2px 4px; width: 14%;">Pertemuan</th>
                        <th style="border: 1px solid #cbd5e1; padding: 2px 4px; width: 14%;">Tanggal</th>
                        <th style="border: 1px solid #cbd5e1; padding: 2px 4px; width: 20%;">Instruktur</th>
                        <th style="border: 1px solid #cbd5e1; padding: 2px 4px; width: 40%;">Materi Pokok Bahasan</th>
                        <th style="border: 1px solid #cbd5e1; padding: 2px 4px; width: 12%; text-align: center;">Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($att['sessionReports'] as $report)
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 2px 4px; font-weight: bold; color: #1e3a8a;">
                            Pertemuan {{ $report['nomor_pertemuan'] }}
                        </td>
                        <td style="border: 1px solid #cbd5e1; padding: 2px 4px;">
                            {{ $report['tanggal'] }}
                        </td>
                        <td style="border: 1px solid #cbd5e1; padding: 2px 4px;">
                            {{ $report['instruktur'] }}
                        </td>
                        <td style="border: 1px solid #cbd5e1; padding: 2px 4px;">
                            {{ $report['materi'] }}
                        </td>
                        <td style="border: 1px solid #cbd5e1; padding: 2px 4px; text-align: center; font-weight: bold; color: #166534;">
                            {{ $report['total_hadir'] }} Hadir
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Tanda Tangan Konfirmasi Lampiran --}}
        <table style="border: none; margin-top: 4px; margin-bottom: 2px;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top; border: none; padding-right: 30px;">
                    <div style="font-size: 6.8pt; color: #64748b; margin-bottom: 18px;">
                        Mengetahui &amp; Memvalidasi,<br><strong>PIC Sekolah / Koordinator</strong>
                    </div>
                    <div style="border-top: 1px solid #334155; padding-top: 2px; font-size: 6.8pt; font-weight: bold; color: #0f172a;">
                        {{ $att['pic_name'] ?: '( ........................................ )' }}
                    </div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top; border: none; padding-left: 30px;">
                    <div style="font-size: 6.8pt; color: #64748b; margin-bottom: 18px;">
                        Diverifikasi Oleh,<br><strong>Instruktur Pengajar</strong>
                    </div>
                    <div style="border-top: 1px solid #334155; padding-top: 2px; font-size: 6.8pt; font-weight: bold; color: #0f172a;">
                        {{ $att['instructor_name'] ?: '( ........................................ )' }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Footer Lampiran --}}
        <table style="border-top: 1px solid #cbd5e1; padding-top: 2px; margin-top: 2px;">
            <tr>
                <td style="font-size: 6pt; color: #94a3b8; vertical-align: middle;">
                    Lampiran Invoice: {{ $invoice->nomor_invoice }} &middot; Rombel: {{ $att['rombel_nama'] }}
                </td>
                <td style="font-size: 6pt; color: #94a3b8; text-align: right; vertical-align: middle;">
                    Halaman {{ $attIndex + 2 }} (Lampiran Presensi &amp; Laporan)
                </td>
            </tr>
        </table>
    </div>

</div>
@endforeach
@endif

</body>
</html>
