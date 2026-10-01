<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->nomor_invoice }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 18mm 12mm 18mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 7.8pt;
            line-height: 1.3;
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
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .skema-bulanan { background: #dbeafe; color: #1e40af; }
        .skema-semester { background: #ede9fe; color: #5b21b6; }
        .skema-tahunan { background: #e2e8f0; color: #334155; }
        .skema-per4 { background: #e0f2fe; color: #0369a1; }

        .status-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 6.8pt;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .status-draft {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #d97706;
        }
        .status-approved {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #16a34a;
        }

        /* ── PAGE LAYOUT ────────────────────────────────────────── */
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
{{-- HALAMAN 1: FAKTUR TAGIHAN RESMI (OFFICIAL CORPORATE INVOICE)              --}}
{{-- Didesain elegan, proporsional, dan tuntas pas dalam 1 lembar A4          --}}
{{-- ═════════════════════════════════════════════════════════════════════════ --}}
<div class="keep-together">

    {{-- ─── 1. KOP SURAT / HEADER RESMI PERUSAHAAN ─────────────────────── --}}
    <table style="border-bottom: 2px solid #0f172a; padding-bottom: 6px; margin-bottom: 7px;">
        <tr>
            {{-- Kiri: Logo & Info Perusahaan --}}
            <td style="width: 54%; vertical-align: top;">
                <div style="font-size: 17pt; font-weight: bold; color: #0f172a; letter-spacing: 1px; line-height: 1;">
                    ERLASS
                </div>
                <div style="font-size: 7.8pt; font-weight: bold; color: #334155; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px;">
                    PT. Erlass Prokreatif Indonesia
                </div>
                <div style="font-size: 6.3pt; color: #64748b; margin-top: 2px; line-height: 1.35;">
                    Pejaten Office Park Blok D, Jl. WarungBuncit Raya no. 79, RT.1/RW.7,<br>
                    Pejaten Bar., Ps. Minggu, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12790<br>
                    Website: erlass.institute &middot; Email: finance@erlass.institute
                </div>
            </td>

            {{-- Kanan: Judul Invoice & Nomor Resmi (Tanpa wrap patah) --}}
            <td style="width: 46%; vertical-align: top; text-align: right;">
                <div style="font-size: 13.5pt; font-weight: bold; color: #0f172a; letter-spacing: 0.5px; line-height: 1;">
                    FAKTUR TAGIHAN
                </div>
                <div style="font-size: 6.8pt; color: #64748b; text-transform: uppercase; margin-top: 3px;">
                    Nomor Invoice
                </div>
                <div style="font-size: 9pt; font-weight: bold; color: #0f172a; white-space: nowrap; margin-top: 1px;">
                    {{ $invoice->nomor_invoice }}
                </div>
                <div style="margin-top: 4px;">
                    @if($isDraft ?? false)
                        <span class="status-badge status-draft">DRAFT &mdash; KONFIRMASI PIC</span>
                        <div style="font-size: 6.5pt; color: #b45309; margin-top: 2px; font-weight: bold;">
                            Pra-Tagihan Sementara (Validasi Kehadiran)
                        </div>
                    @else
                        <span class="status-badge status-approved">APPROVED &mdash; SIAP DITAGIHKAN</span>
                        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">
                            Diterbitkan: {{ $invoice->akunting_approved_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Banner Notifikasi Khusus Draft Invoice --}}
    @if($isDraft ?? false)
    <table style="margin-bottom: 7px; background: #fffbeb; border: 1px dashed #d97706; border-radius: 4px;">
        <tr>
            <td style="padding: 4px 8px; text-align: center;">
                <div style="font-size: 7.2pt; font-weight: bold; color: #92400e; letter-spacing: 0.3px;">
                    DRAFT FAKTUR TAGIHAN &mdash; DOKUMEN VERIFIKASI KEHADIRAN SISWA DENGAN PIC SEKOLAH
                </div>
                <div style="font-size: 6.5pt; color: #78350f; margin-top: 1px;">
                    Rincian data presensi dan rombel ini digunakan untuk validasi sebelum penerbitan faktur tagihan resmi oleh Bagian Keuangan.
                </div>
            </td>
        </tr>
    </table>
    @endif

    {{-- ─── 2. INFORMASI PIHAK KEDUA (BILL TO) & METADATA TAGIHAN ──────── --}}
    @php
        $skemaText = match($invoice->skema_tagihan) {
            'bulanan'         => 'Bulanan (Kalender)',
            'semester'        => 'Per Semester (~16 sesi)',
            'tahunan'         => 'Per Tahun (~32 sesi)',
            'per_4_pertemuan' => 'Per 4 Pertemuan (Rolling)',
            default           => '-',
        };
        $totalRombel = $invoice->total_rombel ?: ($invoice->items->count() ?: 1);
    @endphp
    <table style="margin-bottom: 8px; font-size: 7.8pt;">
        <tr>
            {{-- Kolom Kiri: Tagihan Kepada (Bill To) --}}
            <td style="width: 52%; vertical-align: top; padding-right: 14px;">
                <div style="font-size: 6.8pt; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 2px; margin-bottom: 4px;">
                    TAGIHAN KEPADA (BILL TO):
                </div>
                <div style="font-size: 10pt; font-weight: bold; color: #0f172a; line-height: 1.2;">
                    {{ $invoice->sekolah?->namasekolah ?? '-' }}
                </div>
                <div style="font-size: 7.2pt; color: #475569; margin-top: 2px;">
                    Kode Langganan: <strong>{{ $invoice->sekolah_kodlan ?? '-' }}</strong>
                </div>
                <div style="font-size: 7.2pt; color: #475569; margin-top: 2px; line-height: 1.35;">
                    {{ $invoice->sekolah?->alamat ?? '-' }}
                </div>
                @if($invoice->sekolah?->penanggung_jawab)
                <div style="font-size: 7.2pt; color: #475569; margin-top: 2px;">
                    UP / PIC: <strong>{{ $invoice->sekolah->penanggung_jawab }}</strong>
                </div>
                @endif
            </td>

            {{-- Kolom Kanan: Rincian Program & Sesi --}}
            <td style="width: 48%; vertical-align: top; border-left: 1.5px solid #e2e8f0; padding-left: 14px;">
                <div style="font-size: 6.8pt; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 2px; margin-bottom: 4px;">
                    RINCIAN KONTRAK &amp; PERIODE:
                </div>
                <table style="width: 100%; font-size: 7.5pt;">
                    <tr>
                        <td style="width: 110px; color: #64748b; padding: 2px 0;">Program</td>
                        <td style="padding: 2px 0;">: <strong style="color: #0f172a;">{{ $invoice->program_nama }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2px 0;">Tahun Ajaran</td>
                        <td style="padding: 2px 0;">: {{ $invoice->tahun_ajaran }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2px 0;">Skema Tagihan</td>
                        <td style="padding: 2px 0;">: <strong style="color: #0f172a;">{{ $skemaText }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2px 0;">Periode Tagihan</td>
                        <td style="padding: 2px 0;">: <strong>{{ $invoice->periode_label }}</strong> @if($invoice->sesi_dari && $invoice->sesi_sampai) <span style="color:#475569;">(Sesi {{ $invoice->sesi_dari }}&ndash;{{ $invoice->sesi_sampai }})</span> @endif</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2px 0;">Total Rombel</td>
                        <td style="padding: 2px 0;">: <strong>{{ $totalRombel }} Rombel Belajar</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ─── 3. TABEL UTAMA TAGIHAN (ACCOUNTING INVOICE TABLE) ───────────── --}}
    <table style="margin-bottom: 8px; font-size: 7.5pt; border: 1px solid #334155;">
        <thead>
            <tr style="background: #0f172a; color: #ffffff;">
                <th style="padding: 5px 6px; width: 5%; text-align: center; border: 1px solid #334155;">NO</th>
                <th style="padding: 5px 8px; width: 45%; text-align: left; border: 1px solid #334155;">PROGRAM &amp; ROMBONGAN BELAJAR</th>
                <th style="padding: 5px 8px; width: 22%; text-align: center; border: 1px solid #334155;">RENTANG SESI</th>
                <th style="padding: 5px 8px; width: 13%; text-align: center; border: 1px solid #334155;">JUMLAH SESI</th>
                <th style="padding: 5px 8px; width: 15%; text-align: right; border: 1px solid #334155;">SISWA BILLABLE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $idx => $item)
            <tr style="background: {{ $idx % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                <td style="padding: 5px 6px; text-align: center; border: 1px solid #cbd5e1; color: #64748b;">
                    {{ $idx + 1 }}
                </td>
                <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">
                    <div style="font-weight: bold; color: #0f172a;">
                        {{ $item->rombel?->ekstrakurikuler?->kategori_program ?? $invoice->program_nama }}
                    </div>
                    <div style="font-size: 6.8pt; color: #475569; margin-top: 1px;">
                        Kelas / Rombel: <strong>{{ $item->rombel?->nama_rombel ?? 'Rombel ' . ($idx + 1) }}</strong>
                    </div>
                </td>
                <td style="padding: 5px 8px; text-align: center; border: 1px solid #cbd5e1;">
                    @if($item->sesi_dari && $item->sesi_sampai)
                        Sesi {{ $item->sesi_dari }} s/d {{ $item->sesi_sampai }}
                    @else
                        {{ $item->jumlah_sesi }} Sesi Selesai
                    @endif
                </td>
                <td style="padding: 5px 8px; text-align: center; border: 1px solid #cbd5e1;">
                    {{ $item->jumlah_sesi }} Pertemuan
                </td>
                <td style="padding: 5px 8px; text-align: right; border: 1px solid #cbd5e1; font-weight: bold; color: #0f172a;">
                    {{ $item->billable_efektif }} Siswa
                    @if($item->hasKoreksi())
                        <div style="font-size: 5.8pt; color: #b45309; font-weight: normal;">(penyesuaian data)</div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if($invoice->hasKoreksi())
            <tr style="background: #fffbeb; font-size: 7.2pt;">
                <td colspan="4" style="padding: 4px 8px; text-align: right; border: 1px solid #cbd5e1; color: #92400e;">
                    <em>Catatan Penyesuaian: {{ $invoice->koreksi_catatan }} (oleh {{ $invoice->koreksiByUser?->nama_lengkap ?? 'Admin' }})</em>
                </td>
                <td style="padding: 4px 8px; text-align: right; border: 1px solid #cbd5e1; color: #92400e; font-weight: bold;">
                    Disesuaikan
                </td>
            </tr>
            @endif
            <tr style="background: #f1f5f9; font-size: 8pt;">
                <td colspan="4" style="padding: 5px 8px; text-align: right; font-weight: bold; color: #0f172a; border: 1px solid #334155;">
                    TOTAL SISWA BILLABLE YANG DITAGIHKAN:
                </td>
                <td style="padding: 5px 8px; text-align: right; font-weight: bold; font-size: 9.5pt; color: #1e3a8a; border: 1px solid #334155;">
                    {{ $invoice->billable_efektif }} Siswa
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- Ringkasan Parameter Tagihan Baris Rapi --}}
    <table style="margin-bottom: 8px; font-size: 7.2pt; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px;">
        <tr>
            <td style="padding: 4px 8px; width: 33.3%; text-align: center; border-right: 1px solid #e2e8f0;">
                <span style="color: #64748b;">Total Rombel:</span>
                <strong style="color: #0f172a; font-size: 8pt;">{{ $totalRombel }} Rombel</strong>
            </td>
            <td style="padding: 4px 8px; width: 33.4%; text-align: center; border-right: 1px solid #e2e8f0;">
                <span style="color: #64748b;">Total Pertemuan:</span>
                <strong style="color: #0f172a; font-size: 8pt;">{{ $invoice->jumlah_sesi }} Sesi Terlaksana</strong>
            </td>
            <td style="padding: 4px 8px; width: 33.3%; text-align: center;">
                <span style="color: #64748b;">Presensi Terverifikasi:</span>
                <strong style="color: #166534; font-size: 8pt;">{{ $invoice->billable_efektif }} Siswa Aktif</strong>
            </td>
        </tr>
    </table>

    {{-- ─── 4. OTORISASI & PERSETUJUAN RESMI (CORPORATE SIGNATURE BLOCKS) ─ --}}
    <div style="font-size: 7.2pt; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 2px; margin-bottom: 5px;">
        LEMBAR PENGESAHAN &amp; PERSETUJUAN RESMI:
    </div>
    <table style="margin-bottom: 8px;">
        <tr>
            {{-- Kolom Kiri: Verifikasi Operasional / Akademik --}}
            <td style="width: 48%; vertical-align: top; border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px 9px; background: #ffffff;">
                <div style="font-size: 6.8pt; font-weight: bold; color: #64748b; text-transform: uppercase;">
                    1. OPERASIONAL / AKADEMIK (VERIFIKASI PIC)
                </div>
                <div style="font-size: 7.2pt; margin-top: 2px;">
                    Status:
                    @if($invoice->operasional_status === 'approved')
                        <strong style="color: #166534;">[SUDAH DIKONFIRMASI PIC SEKOLAH]</strong>
                    @else
                        <span style="color: #b45309; font-weight: bold;">[DRAFT &mdash; MENUNGGU KONFIRMASI]</span>
                    @endif
                </div>
                <div style="font-size: 6.8pt; color: #475569; margin-top: 1px;">
                    PIC Dihubungi: <strong>{{ $invoice->pic_konfirmasi_nama ?: ($invoice->sekolah?->penanggung_jawab ?: 'Koordinator Sekolah') }}</strong>
                </div>

                <div style="height: 26px;"></div>

                <div style="border-top: 1px solid #0f172a; padding-top: 2px; font-weight: bold; font-size: 7.8pt; color: #0f172a;">
                    {{ $invoice->operasionalUser?->nama_lengkap ?? $invoice->operasionalUser?->name ?? 'Tim Operasional Erlass' }}
                </div>
                <div style="font-size: 6.5pt; color: #64748b;">
                    {{ $invoice->operasional_approved_at?->translatedFormat('d F Y, H:i') ? 'Dikonfirmasi: ' . $invoice->operasional_approved_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Tgl: ........................................' }}
                </div>
            </td>

            <td style="width: 4%;"></td>

            {{-- Kolom Kanan: Approval Keuangan / Akunting --}}
            <td style="width: 48%; vertical-align: top; border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px 9px; background: #ffffff;">
                <div style="font-size: 6.8pt; font-weight: bold; color: #64748b; text-transform: uppercase;">
                    2. KEUANGAN / AKUNTING (PENERBITAN FAKTUR)
                </div>
                <div style="font-size: 7.2pt; margin-top: 2px;">
                    Status:
                    @if($invoice->akunting_status === 'approved')
                        <strong style="color: #166534;">[TELAH DISETUJUI &amp; DITERBITKAN]</strong>
                    @else
                        <span style="color: #b45309; font-weight: bold;">[MENUNGGU PERSETUJUAN AKUNTING]</span>
                    @endif
                </div>
                <div style="font-size: 6.8pt; color: #475569; margin-top: 1px;">
                    Catatan: {{ $invoice->akunting_catatan ?: '-' }}
                </div>

                <div style="height: 26px;"></div>

                <div style="border-top: 1px solid #0f172a; padding-top: 2px; font-weight: bold; font-size: 7.8pt; color: #0f172a;">
                    {{ $invoice->akuntingUser?->nama_lengkap ?? $invoice->akuntingUser?->name ?? 'Bagian Keuangan Erlass' }}
                </div>
                <div style="font-size: 6.5pt; color: #64748b;">
                    {{ $invoice->akunting_approved_at?->translatedFormat('d F Y, H:i') ? 'Disetujui: ' . $invoice->akunting_approved_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Tgl: ........................................' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ─── 5. KETENTUAN KHUSUS & CATATAN KONTRAK ───────────────────────── --}}
    <div style="border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 4px; padding: 5px 8px; margin-bottom: 6px;">
        <div style="font-size: 6.8pt; font-weight: bold; color: #334155; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">
            KETENTUAN &amp; CATATAN TAGIHAN:
        </div>
        <table style="font-size: 6.5pt; color: #475569; line-height: 1.35;">
            <tr>
                <td style="width: 14px; vertical-align: top;">&bull;</td>
                <td>Seluruh sesi pembelajaran yang terjadwal mengikat alokasi penugasan instruktur dan sarana belajar Erlass Prokreatif Indonesia.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">&bull;</td>
                <td>Sesi pembelajaran <strong style="color: #0f172a;">tidak dapat dibatalkan secara sepihak</strong> untuk pengurangan biaya tagihan. Apabila terdapat kendala internal sekolah (ujian/libur/acara sekolah), sesi wajib dialihkan melalui prosedur <strong style="color: #0f172a;">Reschedule resmi</strong>.</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">&bull;</td>
                <td>Pembayaran tagihan ditransfer ke rekening resmi <strong>PT. Erlass Prokreatif Indonesia</strong> sesuai invoice final yang diterbitkan.</td>
            </tr>
        </table>
    </div>

    {{-- ─── 6. FOOTER HALAMAN 1 ────────────────────────────────────────── --}}
    <table style="border-top: 1px solid #cbd5e1; padding-top: 3px; font-size: 6.2pt; color: #94a3b8;">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB &middot; No: {{ $invoice->nomor_invoice }}
            </td>
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                <strong>PT. Erlass Prokreatif Indonesia</strong> &middot; Halaman 1 (Faktur Tagihan)
            </td>
        </tr>
    </table>

</div>

{{-- ═════════════════════════════════════════════════════════════════════════ --}}
{{-- HALAMAN 2 dst: LAMPIRAN PRESENSI & LAPORAN MENGAJAR RESMI                 --}}
{{-- Format konsisten dengan dokumen resmi absensi sekolah                    --}}
{{-- ═════════════════════════════════════════════════════════════════════════ --}}
@if(!empty($attendanceData))
@foreach($attendanceData as $attIndex => $att)
<div class="page-break" style="padding-top: 2px;">

    {{-- Header Lampiran Formal --}}
    <div style="text-align: center; margin-bottom: 6px; border-bottom: 1.5px solid #0f172a; padding-bottom: 4px;">
        <div style="font-size: 10.5pt; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;">
            LAMPIRAN REKAPITULASI PRESENSI &amp; LAPORAN MENGAJAR
        </div>
        <div style="font-size: 8pt; font-weight: bold; color: #475569; text-transform: uppercase; margin-top: 1px;">
            {{ $att['school_name'] }} &mdash; {{ $att['program_nama'] }} &middot; T.A. {{ $invoice->tahun_ajaran }}
        </div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 1px;">
            Lampiran Faktur Tagihan: <strong>{{ $invoice->nomor_invoice }}</strong> &middot; Rombel: <strong>{{ $att['rombel_nama'] }}</strong>
        </div>
    </div>

    {{-- Meta Grid Rombel & Pengajar (Sesuai format resmi print-blank) --}}
    <table style="margin-bottom: 6px; font-size: 7.2pt; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 4px; padding: 4px 8px;">
        <tr>
            <td style="width: 15%; color: #64748b; padding: 1.5px 0;">Nama Sekolah</td>
            <td style="width: 35%; font-weight: bold; padding: 1.5px 0; color: #0f172a;">: {{ $att['school_name'] }}</td>
            <td style="width: 15%; color: #64748b; padding: 1.5px 0;">Instruktur</td>
            <td style="width: 35%; font-weight: bold; padding: 1.5px 0; color: #0f172a;">: {{ $att['instructor_name'] }}</td>
        </tr>
        <tr>
            <td style="color: #64748b; padding: 1.5px 0;">Program</td>
            <td style="font-weight: bold; padding: 1.5px 0; color: #0f172a;">: {{ $att['program_nama'] }}</td>
            <td style="color: #64748b; padding: 1.5px 0;">PIC Sekolah</td>
            <td style="font-weight: bold; padding: 1.5px 0; color: #0f172a;">: {{ $att['pic_name'] }}</td>
        </tr>
        <tr>
            <td style="color: #64748b; padding: 1.5px 0;">Rombel</td>
            <td style="font-weight: bold; padding: 1.5px 0; color: #0f172a;">: {{ $att['rombel_nama'] }}</td>
            <td style="color: #64748b; padding: 1.5px 0;">Periode / Sesi</td>
            <td style="font-weight: bold; padding: 1.5px 0; color: #0f172a;">: {{ $invoice->periode_label }} @if($invoice->sesi_dari && $invoice->sesi_sampai)(Sesi {{ $invoice->sesi_dari }}&ndash;{{ $invoice->sesi_sampai }})@endif</td>
        </tr>
    </table>

    @php
        $sessions = $att['sessions'];
        $students = $att['students'];
        $attendanceMap = $att['attendanceMap'];
        $colCount = max(1, $sessions->count());
        $colWidth = round(34 / $colCount, 1);
        $totalStudents = $students->count();

        // Responsive density for up to 42 students on 1 single page
        if ($totalStudents > 35) {
            $rowPadding = '0.5px 2px';
            $rowFontSize = '5.8pt';
            $lineHeight = '1.0';
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
    <div style="margin-bottom: 6px;">
        <div style="font-size: 7.2pt; font-weight: bold; color: #0f172a; margin-bottom: 2px; text-transform: uppercase;">
            Daftar Kehadiran Siswa (Presensi Sesi):
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: {{ $rowFontSize }}; line-height: {{ $lineHeight }}; table-layout: fixed;">
            <thead>
                <tr style="background: #0f172a; color: white; text-align: center;">
                    <th style="border: 1px solid #334155; padding: 2px 2px; width: 4%;">No</th>
                    <th style="border: 1px solid #334155; padding: 2px 4px; text-align: left; width: 38%;">Nama Lengkap Siswa</th>
                    <th style="border: 1px solid #334155; padding: 2px 2px; width: 9%;">Kelas</th>
                    @foreach($sessions as $sess)
                        @php
                            $tgl = $sess->tanggal_pelaksanaan ?? $sess->tanggal_terjadwal;
                            $tglShort = $tgl ? (\Carbon\Carbon::parse($tgl)->format('d/m')) : '-';
                        @endphp
                        <th style="border: 1px solid #334155; padding: 1.5px 1px; width: {{ $colWidth }}%;">
                            Pert. {{ $sess->nomor_pertemuan }}<br>
                            <span style="font-size: 5.5pt; font-weight: normal; color: #93c5fd;">{{ $tglShort }}</span>
                        </th>
                    @endforeach
                    <th style="border: 1px solid #334155; padding: 2px 2px; width: 7%;">Hadir</th>
                    <th style="border: 1px solid #334155; padding: 2px 2px; width: 6%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $stIdx => $student)
                @php
                    $hadirCount = 0;
                @endphp
                <tr style="background: {{ $stIdx % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                    <td style="border: 1px solid #cbd5e1; text-align: center; padding: {{ $rowPadding }}; color: #64748b;">
                        {{ $stIdx + 1 }}
                    </td>
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
                    <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #0f172a; padding: {{ $rowPadding }};">
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
            <div style="font-size: 7.2pt; font-weight: bold; color: #0f172a; margin-bottom: 2px; text-transform: uppercase;">
                Rincian Pelaksanaan Materi Mengajar Tiap Sesi:
            </div>
            <table style="font-size: 6pt; border: 1px solid #cbd5e1;">
                <thead>
                    <tr style="background: #f1f5f9; color: #0f172a; text-align: left;">
                        <th style="border: 1px solid #cbd5e1; padding: 1.5px 3px; width: 14%;">Pertemuan</th>
                        <th style="border: 1px solid #cbd5e1; padding: 1.5px 3px; width: 14%;">Tanggal</th>
                        <th style="border: 1px solid #cbd5e1; padding: 1.5px 3px; width: 20%;">Instruktur</th>
                        <th style="border: 1px solid #cbd5e1; padding: 1.5px 3px; width: 40%;">Materi Pokok Bahasan</th>
                        <th style="border: 1px solid #cbd5e1; padding: 1.5px 3px; width: 12%; text-align: center;">Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($att['sessionReports'] as $report)
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 1px 3px; font-weight: bold; color: #0f172a;">
                            Pertemuan {{ $report['nomor_pertemuan'] }}
                        </td>
                        <td style="border: 1px solid #cbd5e1; padding: 1px 3px;">
                            {{ $report['tanggal'] }}
                        </td>
                        <td style="border: 1px solid #cbd5e1; padding: 1px 3px;">
                            {{ $report['instruktur'] }}
                        </td>
                        <td style="border: 1px solid #cbd5e1; padding: 1px 3px;">
                            {{ $report['materi'] }}
                        </td>
                        <td style="border: 1px solid #cbd5e1; padding: 1px 3px; text-align: center; font-weight: bold; color: #166534;">
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
                    <div style="font-size: 6.8pt; color: #64748b; margin-bottom: 8px;">
                        Mengetahui &amp; Memvalidasi,<br><strong>PIC Sekolah / Koordinator</strong>
                    </div>
                    <div style="border-top: 1px solid #0f172a; padding-top: 2px; font-size: 6.8pt; font-weight: bold; color: #0f172a;">
                        {{ $att['pic_name'] ?: '( ........................................ )' }}
                    </div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top; border: none; padding-left: 30px;">
                    <div style="font-size: 6.8pt; color: #64748b; margin-bottom: 8px;">
                        Diverifikasi Oleh,<br><strong>Instruktur Pengajar</strong>
                    </div>
                    <div style="border-top: 1px solid #0f172a; padding-top: 2px; font-size: 6.8pt; font-weight: bold; color: #0f172a;">
                        {{ $att['instructor_name'] ?: '( ........................................ )' }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Footer Lampiran --}}
        <table style="border-top: 1px solid #cbd5e1; padding-top: 2px; margin-top: 2px;">
            <tr>
                <td style="font-size: 6pt; color: #94a3b8; vertical-align: middle;">
                    Lampiran Faktur Tagihan: {{ $invoice->nomor_invoice }} &middot; Rombel: {{ $att['rombel_nama'] }}
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
