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
            font-size: 11pt;
            color: #1a1a2e;
            background: #fff;
        }
        .page { padding: 28px 36px; }

        /* ── HEADER ────────────────────────────────────────────── */
        .header { display: flex; align-items: flex-start; justify-content: space-between; border-bottom: 3px solid #1e3a8a; padding-bottom: 16px; margin-bottom: 20px; }
        .logo-area .company-name { font-size: 18pt; font-weight: 700; color: #1e3a8a; }
        .logo-area .company-tagline { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .invoice-meta { text-align: right; }
        .invoice-meta .inv-label { font-size: 8pt; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
        .invoice-meta .inv-number { font-size: 13pt; font-weight: 700; color: #1e3a8a; }
        .invoice-meta .inv-status { display: inline-block; background: #dcfce7; color: #166534; padding: 2px 10px; border-radius: 20px; font-size: 8pt; font-weight: 600; margin-top: 4px; }

        /* ── INFO SECTION ──────────────────────────────────────── */
        .info-grid { display: flex; gap: 20px; margin-bottom: 20px; }
        .info-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; }
        .info-box .box-title { font-size: 8pt; color: #64748b; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; margin-bottom: 6px; }
        .info-box .box-value { font-size: 10pt; font-weight: 600; color: #1e3a8a; line-height: 1.5; }
        .info-box .box-sub { font-size: 8.5pt; color: #475569; }

        /* ── BILLING SUMMARY ───────────────────────────────────── */
        .summary-box { background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; }
        .summary-grid { display: flex; gap: 10px; }
        .summary-item { flex: 1; text-align: center; }
        .summary-item .s-val { font-size: 22pt; font-weight: 700; color: #1e3a8a; }
        .summary-item .s-label { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .summary-item .s-note { font-size: 7pt; color: #94a3b8; }
        .divider-v { width: 1px; background: #bfdbfe; margin: 0 5px; }

        /* ── SKEMA BADGE ───────────────────────────────────────── */
        .skema-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 8.5pt; font-weight: 600; }
        .skema-bulanan { background: #dbeafe; color: #1e40af; }
        .skema-semester { background: #ede9fe; color: #5b21b6; }
        .skema-tahunan { background: #e2e8f0; color: #334155; }
        .skema-per4 { background: #f1f5f9; color: #475569; }

        /* ── KOREKSI NOTE ──────────────────────────────────────── */
        .koreksi-note { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 6px; padding: 8px 12px; margin-bottom: 16px; font-size: 8.5pt; color: #92400e; }

        /* ── APPROVAL TABLE ────────────────────────────────────── */
        .approval-section { margin-bottom: 20px; }
        .section-title { font-size: 10pt; font-weight: 700; color: #1e3a8a; border-bottom: 1.5px solid #bfdbfe; padding-bottom: 4px; margin-bottom: 12px; }
        .approval-grid { display: flex; gap: 16px; }
        .approval-box { flex: 1; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; }
        .approval-box.approved { border-color: #bbf7d0; background: #f0fdf4; }
        .approval-box .ap-role { font-size: 8pt; color: #64748b; text-transform: uppercase; font-weight: 600; }
        .approval-box .ap-name { font-size: 10pt; font-weight: 600; color: #1e3a8a; margin-top: 4px; }
        .approval-box .ap-date { font-size: 8.5pt; color: #64748b; margin-top: 2px; }
        .approval-box .ap-status { font-size: 8pt; font-weight: 700; color: #166534; margin-top: 6px; }
        .ttd-area { text-align: center; margin-top: 10px; border-top: 1px dashed #94a3b8; padding-top: 4px; }
        .ttd-area .ttd-label { font-size: 7.5pt; color: #94a3b8; }

        /* ── KONTRAK WARNING ────────────────────────────────────── */
        .kontrak-box { border: 2px solid #fca5a5; border-radius: 8px; padding: 14px 16px; background: #fff7f7; margin-bottom: 12px; }
        .kontrak-box .kontrak-title { font-size: 9pt; font-weight: 700; color: #b91c1c; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
        .kontrak-box .kontrak-text { font-size: 8.5pt; color: #374151; line-height: 1.6; }
        .kontrak-box .kontrak-text strong { color: #b91c1c; }

        /* ── FOOTER ────────────────────────────────────────────── */
        .footer { border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 8px; display: flex; justify-content: space-between; align-items: center; }
        .footer-left { font-size: 7.5pt; color: #94a3b8; }
        .footer-right { font-size: 7.5pt; color: #94a3b8; text-align: right; }

        /* ── PAGE BREAK ─────────────────────────────────────────── */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
<div class="page">

    {{-- ─── HEADER ─────────────────────────────────────────────────────── --}}
    <div class="header">
        <div class="logo-area">
            <div class="company-name">ERLASS</div>
            <div class="company-tagline">PT. Erlass Prokreatif Indonesia</div>
            <div style="font-size:8pt; color:#64748b; margin-top:4px;">
                Bali, Indonesia
            </div>
        </div>
        <div class="invoice-meta">
            <div class="inv-label">Nomor Invoice</div>
            <div class="inv-number">{{ $invoice->nomor_invoice }}</div>
            <div class="inv-status">✅ APPROVED</div>
            <div style="font-size:7.5pt; color:#64748b; margin-top:4px;">
                Diterbitkan: {{ $invoice->akunting_approved_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
            </div>
        </div>
    </div>

    {{-- ─── INFO GRID ──────────────────────────────────────────────────── --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="box-title">Ditujukan Kepada</div>
            <div class="box-value">{{ $invoice->sekolah?->namasekolah ?? $invoice->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? '-' }}</div>
            <div class="box-sub">Kode: {{ $invoice->sekolah_kodlan ?? $invoice->rombel?->ekstrakurikuler?->sekolah?->kodlan ?? '-' }}</div>
            <div class="box-sub">{{ $invoice->sekolah?->alamat ?? $invoice->rombel?->ekstrakurikuler?->sekolah?->alamat ?? '' }}</div>
        </div>
        <div class="info-box">
            <div class="box-title">Identitas Tagihan</div>
            <div class="box-value">{{ $invoice->total_rombel ?: ($invoice->items->count() ?: 1) }} Rombel</div>
            <div class="box-sub">Tahun Ajaran: {{ $invoice->tahun_ajaran }}</div>
            <div class="box-sub">
                Periode: <strong>{{ $invoice->periode_label }}</strong>
            </div>
            @if($invoice->sesi_dari && $invoice->sesi_sampai)
            <div class="box-sub">Pertemuan: {{ $invoice->sesi_dari }} – {{ $invoice->sesi_sampai }}</div>
            @endif
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
            <div class="box-sub" style="margin-top:6px;">Total Sesi: {{ $invoice->jumlah_sesi }}</div>
        </div>
    </div>

    {{-- ─── TABEL RINCIAN ITEM PER ROMBEL ──────────────────────────────── --}}
    @if($invoice->items->isNotEmpty())
    <div style="margin-bottom: 20px;">
        <div class="section-title">Rincian Tagihan per Rombel</div>
        <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt;">
            <thead>
                <tr style="background: #1e3a8a; color: #fff; text-align: left;">
                    <th style="padding: 6px 10px; width: 25px; text-align: center;">#</th>
                    <th style="padding: 6px 10px;">Program / Kategori</th>
                    <th style="padding: 6px 10px;">Rombel</th>
                    <th style="padding: 6px 10px; text-align: center;">Sesi</th>
                    <th style="padding: 6px 10px; text-align: right;">Siswa Billable</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $it)
                <tr style="border-bottom: 1px solid #e2e8f0; {{ $idx % 2 == 1 ? 'background: #f8fafc;' : '' }}">
                    <td style="padding: 6px 10px; text-align: center; color: #64748b;">{{ $idx + 1 }}</td>
                    <td style="padding: 6px 10px; font-weight: 600; color: #1e3a8a;">
                        {{ $it->rombel?->ekstrakurikuler?->kategori_program ?? '-' }}
                    </td>
                    <td style="padding: 6px 10px;">{{ $it->rombel?->nama_rombel ?? '-' }}</td>
                    <td style="padding: 6px 10px; text-align: center;">
                        @if($it->sesi_dari && $it->sesi_sampai)
                            Sesi {{ $it->sesi_dari }}–{{ $it->sesi_sampai }}
                        @else
                            {{ $it->jumlah_sesi }} Sesi
                        @endif
                    </td>
                    <td style="padding: 6px 10px; text-align: right; font-weight: 600;">
                        {{ $it->billable_efektif }} siswa
                        @if($it->hasKoreksi())
                            <span style="font-size: 7pt; color: #b45309;">(koreksi)</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #eff6ff; border-top: 2px solid #bfdbfe; font-weight: 700;">
                    <td colspan="4" style="padding: 8px 10px; color: #1e3a8a;">TOTAL SISWA BILLABLE:</td>
                    <td style="padding: 8px 10px; text-align: right; color: #1e3a8a; font-size: 9.5pt;">
                        {{ $invoice->billable_efektif }} siswa
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    {{-- Single Rombel Summary Box --}}
    <div class="summary-box">
        <div style="font-size:8pt; color:#1e40af; font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px;">
            Ringkasan Tagihan
        </div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="s-val">{{ $invoice->jumlah_sesi }}</div>
                <div class="s-label">Total Sesi</div>
                <div class="s-note">Pertemuan terlaksana</div>
            </div>
            <div class="divider-v"></div>
            <div class="summary-item">
                <div class="s-val">{{ $invoice->billable_efektif }}</div>
                <div class="s-label">Siswa Billable</div>
                <div class="s-note">Hadir ≥2 dari 4 sesi</div>
            </div>
            <div class="divider-v"></div>
            <div class="summary-item">
                <div class="s-val">{{ $invoice->periode_label }}</div>
                <div class="s-label">Periode</div>
                <div class="s-note">{{ $invoice->tahun_ajaran }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- ─── CATATAN KOREKSI (jika ada) ────────────────────────────────── --}}
    @if($invoice->hasKoreksi())
    <div class="koreksi-note">
        ⚠️ <strong>Catatan Koreksi Data:</strong>
        Jumlah siswa billable telah dikoreksi dari <strong>{{ $invoice->jumlah_siswa_billable }}</strong>
        menjadi <strong>{{ $invoice->koreksi_siswa_billable }}</strong> oleh {{ $invoice->koreksiByUser?->name ?? 'Admin' }}
        pada {{ $invoice->koreksi_at?->translatedFormat('d F Y') }}.
        Alasan: {{ $invoice->koreksi_catatan }}
    </div>
    @endif

    {{-- ─── APPROVAL ───────────────────────────────────────────────────── --}}
    <div class="approval-section">
        <div class="section-title">Status Persetujuan</div>
        <div class="approval-grid">
            <div class="approval-box approved">
                <div class="ap-role">Operasional / Akademik</div>
                <div class="ap-name">{{ $invoice->operasionalUser?->name ?? '-' }}</div>
                <div class="ap-date">{{ $invoice->operasional_approved_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</div>
                <div class="ap-status">✅ Disetujui</div>
                @if($invoice->operasional_catatan)
                <div style="font-size:7.5pt; color:#64748b; margin-top:4px;">Catatan: {{ $invoice->operasional_catatan }}</div>
                @endif
                <div class="ttd-area">
                    <div class="ttd-label">Tanda Tangan Digital</div>
                </div>
            </div>
            <div class="approval-box approved">
                <div class="ap-role">Akunting / Finance</div>
                <div class="ap-name">{{ $invoice->akuntingUser?->name ?? '-' }}</div>
                <div class="ap-date">{{ $invoice->akunting_approved_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</div>
                <div class="ap-status">✅ Disetujui</div>
                @if($invoice->akunting_catatan)
                <div style="font-size:7.5pt; color:#64748b; margin-top:4px;">Catatan: {{ $invoice->akunting_catatan }}</div>
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
            Digenerate: {{ now()->translatedFormat('d F Y, H:i') }} WIB<br>
            No. Invoice: {{ $invoice->nomor_invoice }}
        </div>
        <div class="footer-right">
            <strong>PT. Erlass Prokreatif Indonesia</strong><br>
            erlass.institute
        </div>
    </div>

</div>
</body>
</html>
