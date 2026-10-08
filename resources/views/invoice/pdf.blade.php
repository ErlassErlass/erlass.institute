<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->nomor_invoice }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 10mm 15mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 7.6pt;
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
        .skema-csr { background: #dcfce7; color: #166534; }

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
    <table style="border-bottom: 2px solid #0f172a; padding-bottom: 5px; margin-bottom: 6px;">
        <tr>
            {{-- Kiri: Logo & Info Perusahaan --}}
            <td style="width: 54%; vertical-align: top;">
                <div style="font-size: 16pt; font-weight: bold; color: #0f172a; letter-spacing: 1px; line-height: 1;">
                    ERLASS
                </div>
                <div style="font-size: 7.5pt; font-weight: bold; color: #334155; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px;">
                    PT. Erlass Prokreatif Indonesia
                </div>
                <div style="font-size: 6.2pt; color: #64748b; margin-top: 2px; line-height: 1.3;">
                    Pejaten Office Park Blok D, Jl. WarungBuncit Raya no. 79, RT.1/RW.7,<br>
                    Pejaten Bar., Ps. Minggu, Kota Jakarta Selatan, D.K.I. Jakarta 12790
                </div>
            </td>

            {{-- Kanan: Judul Invoice & Nomor Resmi --}}
            <td style="width: 46%; vertical-align: top; text-align: right;">
                <div style="font-size: 13pt; font-weight: bold; color: #0f172a; letter-spacing: 0.5px; line-height: 1;">
                    FAKTUR TAGIHAN
                </div>
                <div style="font-size: 6.5pt; color: #64748b; text-transform: uppercase; margin-top: 2px;">
                    Nomor Invoice
                </div>
                <div style="font-size: 8.8pt; font-weight: bold; color: #0f172a; white-space: nowrap; margin-top: 1px;">
                    {{ $invoice->nomor_invoice }}
                </div>
                <div style="margin-top: 3px;">
                    @if($isDraft ?? false)
                        <span class="status-badge status-draft">DRAFT &mdash; MENUNGGU KONFIRMASI PIC</span>
                    @else
                        <span class="status-badge status-approved">RESMI &mdash; DISETUJUI PIC SEKOLAH</span>
                        <div style="font-size: 6.2pt; color: #64748b; margin-top: 1px;">
                            Dikonfirmasi: {{ $invoice->pic_konfirmasi_tgl?->translatedFormat('d F Y') ?? ($invoice->operasional_approved_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y')) }}
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Banner Notifikasi Khusus Draft Invoice --}}
    @if($isDraft ?? false)
    <table style="margin-bottom: 6px; background: #fffbeb; border: 1px dashed #d97706; border-radius: 4px;">
        <tr>
            <td style="padding: 3px 6px; text-align: center;">
                <div style="font-size: 6.8pt; font-weight: bold; color: #92400e;">
                    DRAFT FAKTUR TAGIHAN &mdash; DOKUMEN VERIFIKASI KEHADIRAN SISWA DENGAN PIC SEKOLAH
                </div>
            </td>
        </tr>
    </table>
    @endif

    {{-- ─── 2. INFORMASI PIHAK KEDUA (BILL TO) & METADATA TAGIHAN ──────── --}}
    @php
        $skemaText = match($invoice->skema_tagihan) {
            'bulanan'          => 'Bulanan (Kalender)',
            'semester'         => 'Per Semester (~16 sesi)',
            'tahunan'          => 'Per Tahun (~32 sesi)',
            'per_4_pertemuan'  => 'Per 4 Pertemuan',
            'csr_reguler_soga' => 'CSR Reguler SOGA (Solidaritas Erlangga)',
            default            => '-',
        };
        $totalRombel = $invoice->total_rombel ?: ($invoice->items->count() ?: 1);
    @endphp
    <table style="margin-bottom: 6px; font-size: 7.5pt;">
        <tr>
            {{-- Kolom Kiri: Tagihan Kepada (Bill To) --}}
            <td style="width: 52%; vertical-align: top; padding-right: 12px;">
                <div style="font-size: 6.5pt; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 1px; margin-bottom: 3px;">
                    TAGIHAN KEPADA (BILL TO):
                </div>
                @if($invoice->skema_tagihan === \App\Models\Sekolah::SKEMA_CSR_REGULER_SOGA)
                <div style="font-size: 9.5pt; font-weight: bold; color: #0f172a; line-height: 1.2;">
                    CSR SOGA (Solidaritas Erlangga)
                </div>
                <div style="font-size: 7.2pt; font-weight: bold; color: #1e40af; margin-top: 2px;">
                    Program Kemitraan: {{ $invoice->sekolah?->namasekolah ?? '-' }}
                </div>
                <div style="font-size: 6.8pt; color: #475569; margin-top: 1px;">
                    Kode Langganan Sekolah: <strong>{{ $invoice->sekolah_kodlan ?? '-' }}</strong>
                </div>
                <div style="font-size: 6.8pt; color: #475569; margin-top: 1px; line-height: 1.3;">
                    Lokasi: {{ $invoice->sekolah?->alamat ?? '-' }}
                </div>
                <div style="font-size: 6.8pt; color: #475569; margin-top: 1px;">
                    Penanggung Jawab: <strong>{{ $invoice->pic_nama ?: 'CSR SOGA (Solidaritas Erlangga)' }}</strong> 
                    @if($invoice->pic_jabatan) ({{ $invoice->pic_jabatan }}) @endif
                </div>
                @else
                <div style="font-size: 9.5pt; font-weight: bold; color: #0f172a; line-height: 1.2;">
                    {{ $invoice->sekolah?->namasekolah ?? '-' }}
                </div>
                <div style="font-size: 6.8pt; color: #475569; margin-top: 1px;">
                    Kode Langganan: <strong>{{ $invoice->sekolah_kodlan ?? '-' }}</strong>
                </div>
                <div style="font-size: 6.8pt; color: #475569; margin-top: 1px; line-height: 1.3;">
                    {{ $invoice->sekolah?->alamat ?? '-' }}
                </div>
                @if($invoice->pic_nama)
                <div style="font-size: 6.8pt; color: #475569; margin-top: 1px;">
                    UP / PIC: <strong>{{ $invoice->pic_nama }}</strong> 
                    @if($invoice->pic_jabatan) ({{ $invoice->pic_jabatan }}) @endif
                </div>
                @endif
                @endif
            </td>

            {{-- Kolom Kanan: Rincian Program & Sesi --}}
            <td style="width: 48%; vertical-align: top; border-left: 1.5px solid #e2e8f0; padding-left: 12px;">
                <div style="font-size: 6.5pt; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 1px; margin-bottom: 3px;">
                    RINCIAN KONTRAK &amp; PERIODE:
                </div>
                <table style="width: 100%; font-size: 7.2pt;">
                    <tr>
                        <td style="width: 105px; color: #64748b; padding: 1.5px 0;">Program</td>
                        <td style="padding: 1.5px 0;">: <strong style="color: #0f172a;">{{ $invoice->program_nama }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 1.5px 0;">Tahun Ajaran</td>
                        <td style="padding: 1.5px 0;">: {{ $invoice->tahun_ajaran }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 1.5px 0;">Skema Tagihan</td>
                        <td style="padding: 1.5px 0;">: <strong style="color: #0f172a;">{{ $skemaText }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 1.5px 0;">Periode Tagihan</td>
                        <td style="padding: 1.5px 0;">: <strong>{{ $invoice->periode_label }}</strong> @if($invoice->sesi_dari && $invoice->sesi_sampai) <span style="color:#475569;">(Sesi {{ $invoice->sesi_dari }}&ndash;{{ $invoice->sesi_sampai }})</span> @endif</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 1.5px 0;">Total Rombel</td>
                        <td style="padding: 1.5px 0;">: <strong>{{ $totalRombel }} Rombel Belajar</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ─── 3. TABEL UTAMA TAGIHAN (ACCOUNTING INVOICE TABLE) ───────────── --}}
    <table style="margin-bottom: 6px; font-size: 7.2pt; border: 1px solid #334155;">
        <thead>
            <tr style="background: #0f172a; color: #ffffff;">
                <th style="padding: 4px 6px; width: 5%; text-align: center; border: 1px solid #334155;">NO</th>
                <th style="padding: 4px 8px; width: 45%; text-align: left; border: 1px solid #334155;">PROGRAM &amp; ROMBONGAN BELAJAR</th>
                <th style="padding: 4px 8px; width: 22%; text-align: center; border: 1px solid #334155;">RENTANG SESI</th>
                <th style="padding: 4px 8px; width: 13%; text-align: center; border: 1px solid #334155;">JUMLAH SESI</th>
                <th style="padding: 4px 8px; width: 15%; text-align: right; border: 1px solid #334155;">SISWA HADIR</th>
            </tr>
        </thead>
        <tbody>
            @if($invoice->items->isNotEmpty())
                @foreach($invoice->items as $idx => $item)
                <tr style="background: {{ $idx % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                    <td style="padding: 4px 6px; text-align: center; border: 1px solid #cbd5e1; color: #64748b;">
                        {{ $idx + 1 }}
                    </td>
                    <td style="padding: 4px 8px; border: 1px solid #cbd5e1;">
                        <div style="font-weight: bold; color: #0f172a;">
                            {{ $item->rombel?->ekstrakurikuler?->kategori_program ?? $invoice->program_nama }}
                        </div>
                        <div style="font-size: 6.5pt; color: #475569;">
                            Kelas / Rombel: <strong>{{ $item->rombel?->nama_rombel ?? 'Rombel ' . ($idx + 1) }}</strong>
                        </div>
                    </td>
                    <td style="padding: 4px 8px; text-align: center; border: 1px solid #cbd5e1;">
                        @if($item->sesi_dari && $item->sesi_sampai)
                            Sesi {{ $item->sesi_dari }} s/d {{ $item->sesi_sampai }}
                        @else
                            {{ $item->jumlah_sesi }} Sesi Selesai
                        @endif
                    </td>
                    <td style="padding: 4px 8px; text-align: center; border: 1px solid #cbd5e1;">
                        {{ $item->jumlah_sesi }} Pertemuan
                    </td>
                    <td style="padding: 4px 8px; text-align: right; border: 1px solid #cbd5e1; font-weight: bold; color: #0f172a;">
                        {{ $item->jumlah_siswa_billable }} Siswa
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td style="padding: 4px 6px; text-align: center; border: 1px solid #cbd5e1;">1</td>
                    <td style="padding: 4px 8px; border: 1px solid #cbd5e1;">
                        <div style="font-weight: bold;">{{ $invoice->program_nama }}</div>
                        <div style="font-size: 6.5pt; color: #475569;">Rombel: {{ $invoice->rombel?->nama_rombel ?? 'Utama' }}</div>
                    </td>
                    <td style="padding: 4px 8px; text-align: center; border: 1px solid #cbd5e1;">
                        Sesi {{ $invoice->sesi_dari ?? 1 }} s/d {{ $invoice->sesi_sampai ?? $invoice->jumlah_sesi }}
                    </td>
                    <td style="padding: 4px 8px; text-align: center; border: 1px solid #cbd5e1;">
                        {{ $invoice->jumlah_sesi }} Pertemuan
                    </td>
                    <td style="padding: 4px 8px; text-align: right; border: 1px solid #cbd5e1; font-weight: bold;">
                        {{ $invoice->jumlah_siswa_billable }} Siswa
                    </td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            {{-- Baris Potongan Siswa Gratis (Anak Guru/Kasek) --}}
            @if(($invoice->jumlah_siswa_gratis ?? 0) > 0)
            <tr style="background: #eff6ff; font-size: 7pt;">
                <td colspan="4" style="padding: 3px 8px; text-align: right; border: 1px solid #cbd5e1; color: #1e40af;">
                    <strong>Potongan Siswa Non-Billable (Anak Guru / Kasek / Kebijakan Khusus):</strong><br>
                    <span style="font-size: 6.2pt; color: #3b82f6;">
                        Nama Siswa: {{ collect($invoice->siswa_gratis_list ?? [])->pluck('nama')->implode(', ') }}
                    </span>
                </td>
                <td style="padding: 3px 8px; text-align: right; border: 1px solid #cbd5e1; color: #1e40af; font-weight: bold;">
                    -{{ $invoice->jumlah_siswa_gratis }} Siswa
                </td>
            </tr>
            @endif

            {{-- Baris Koreksi Manual Jika Ada --}}
            @if($invoice->hasKoreksi())
            <tr style="background: #fffbeb; font-size: 7pt;">
                <td colspan="4" style="padding: 3px 8px; text-align: right; border: 1px solid #cbd5e1; color: #92400e;">
                    <em>Catatan Koreksi Override: {{ $invoice->koreksi_catatan }}</em>
                </td>
                <td style="padding: 3px 8px; text-align: right; border: 1px solid #cbd5e1; color: #92400e; font-weight: bold;">
                    Disesuaikan
                </td>
            </tr>
            @endif

            {{-- Baris Total Billable Akhir --}}
            <tr style="background: #f1f5f9; font-size: 7.8pt;">
                <td colspan="4" style="padding: 4px 8px; text-align: right; font-weight: bold; color: #0f172a; border: 1px solid #334155;">
                    TOTAL SISWA BILLABLE YANG DITAGIHKAN PADA INVOICE:
                </td>
                <td style="padding: 4px 8px; text-align: right; font-weight: bold; font-size: 9.5pt; color: #1e3a8a; border: 1px solid #334155;">
                    {{ $invoice->billable_efektif }} Siswa
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- Ringkasan Parameter Tagihan Baris Rapi --}}
    <table style="margin-bottom: 6px; font-size: 7pt; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px;">
        <tr>
            <td style="padding: 3px 6px; width: 25%; text-align: center; border-right: 1px solid #e2e8f0;">
                <span style="color: #64748b;">Siswa Hadir:</span>
                <strong style="color: #0f172a; font-size: 7.8pt;">{{ $invoice->jumlah_siswa_billable }} Orang</strong>
            </td>
            <td style="padding: 3px 6px; width: 25%; text-align: center; border-right: 1px solid #e2e8f0;">
                <span style="color: #64748b;">Siswa Gratis:</span>
                <strong style="color: #ea580c; font-size: 7.8pt;">{{ $invoice->jumlah_siswa_gratis ?? 0 }} Orang</strong>
            </td>
            <td style="padding: 3px 6px; width: 25%; text-align: center; border-right: 1px solid #e2e8f0;">
                <span style="color: #64748b;">Total Pertemuan:</span>
                <strong style="color: #0f172a; font-size: 7.8pt;">{{ $invoice->jumlah_sesi }} Sesi Selesai</strong>
            </td>
            <td style="padding: 3px 6px; width: 25%; text-align: center;">
                <span style="color: #64748b;">Ditagihkan:</span>
                <strong style="color: #166534; font-size: 8.5pt;">{{ $invoice->billable_efektif }} Siswa</strong>
            </td>
        </tr>
    </table>

    {{-- ─── 4. LEMBAR PENGESAHAN & PERSETUJUAN RESMI (GATE 1) ─────────── --}}
    <div style="font-size: 7pt; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 1px; margin-bottom: 4px;">
        LEMBAR PENGESAHAN &amp; KOMITMEN PEMBUATAN INVOICE:
    </div>
    <table style="margin-bottom: 6px;">
        <tr>
            {{-- Kolom Kiri: Verifikasi PIC Sekolah --}}
            <td style="width: 50%; vertical-align: top; border: 1px solid #cbd5e1; border-radius: 4px; padding: 5px 8px; background: #ffffff;">
                <div style="font-size: 6.6pt; font-weight: bold; color: #1e3a8a; text-transform: uppercase;">
                    1. VERIFIKASI &amp; PERSETUJUAN {{ $invoice->skema_tagihan === \App\Models\Sekolah::SKEMA_CSR_REGULER_SOGA ? 'PIC CSR SOGA' : 'PIC SEKOLAH' }}
                </div>
                <div style="font-size: 7pt; margin-top: 1px;">
                    Status:
                    @if($invoice->isApproved())
                        <strong style="color: #166534;">[SUDAH DIKONFIRMASI {{ $invoice->skema_tagihan === \App\Models\Sekolah::SKEMA_CSR_REGULER_SOGA ? 'CSR SOGA' : 'PIC SEKOLAH' }}]</strong>
                    @else
                        <span style="color: #b45309; font-weight: bold;">[DRAFT &mdash; MENUNGGU KONFIRMASI]</span>
                    @endif
                </div>
                <div style="font-size: 6.6pt; color: #475569; margin-top: 1px;">
                    PIC Dihubungi: <strong>{{ $invoice->pic_nama ?: 'Koordinator Sekolah' }}</strong>
                    @if($invoice->pic_jabatan) <span style="color:#64748b;">({{ $invoice->pic_jabatan }})</span> @endif
                </div>
                <div style="font-size: 6.2pt; color: #64748b; margin-top: 1px;">
                    Media: Percakapan WhatsApp @if($invoice->bukti_chat_path) <strong>(Bukti Chat Terlampir)</strong> @endif
                </div>

                {{-- Klausul Komitmen Mutlak --}}
                <div style="font-size: 5.8pt; color: #991b1b; background: #fef2f2; border: 0.5px solid #fecaca; padding: 2px 4px; border-radius: 2px; margin-top: 3px; line-height: 1.2;">
                    * Setelah persetujuan ini, tidak dibenarkan ada perubahan data lagi dan invoice mengikat secara sah.
                </div>

                <div style="height: 18px;"></div>

                <div style="border-top: 1px solid #0f172a; padding-top: 1px; font-weight: bold; font-size: 7.2pt; color: #0f172a;">
                    {{ $invoice->pic_nama ?: '( ........................................ )' }}
                </div>
                @if($invoice->pic_jabatan)
                <div style="font-size: 6.2pt; color: #475569;">
                    {{ $invoice->pic_jabatan }}
                </div>
                @endif
                <div style="font-size: 6.2pt; color: #64748b;">
                    {{ $invoice->pic_konfirmasi_tgl?->translatedFormat('d F Y, H:i') ? 'Dikonfirmasi: ' . $invoice->pic_konfirmasi_tgl->translatedFormat('d F Y, H:i') . ' WIB' : 'Tgl: ........................................' }}
                </div>
            </td>

            <td style="width: 3%;"></td>

            {{-- Kolom Kanan: Operasional Erlass --}}
            <td style="width: 47%; vertical-align: top; border: 1px solid #cbd5e1; border-radius: 4px; padding: 5px 8px; background: #ffffff;">
                <div style="font-size: 6.6pt; font-weight: bold; color: #0f172a; text-transform: uppercase;">
                    2. PEMERIKSA OPERASIONAL ERLASS
                </div>
                <div style="font-size: 7pt; margin-top: 1px;">
                    Status:
                    @if($invoice->isApproved())
                        <strong style="color: #166534;">[VERIFIKASI GATE 1 TUNTAS]</strong>
                    @else
                        <span style="color: #b45309; font-weight: bold;">[SEDANG DIPERIKSA]</span>
                    @endif
                </div>
                <div style="font-size: 6.6pt; color: #475569; margin-top: 1px;">
                    Staf Pemeriksa: <strong>{{ $invoice->operasionalUser?->nama_lengkap ?? $invoice->operasionalUser?->name ?? 'Dinda / Novandi' }}</strong>
                </div>
                <div style="font-size: 6.2pt; color: #64748b; margin-top: 1px;">
                    Divisi: Operasional &amp; Akademik
                </div>

                <div style="height: 30px;"></div>

                <div style="border-top: 1px solid #0f172a; padding-top: 1px; font-weight: bold; font-size: 7.2pt; color: #0f172a;">
                    {{ $invoice->operasionalUser?->nama_lengkap ?? $invoice->operasionalUser?->name ?? 'Tim Operasional Erlass' }}
                </div>
                <div style="font-size: 6.2pt; color: #64748b;">
                    {{ $invoice->operasional_approved_at?->translatedFormat('d F Y, H:i') ? 'Diverifikasi: ' . $invoice->operasional_approved_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Tgl: ........................................' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ─── 5. LEMBAR TANDA TERIMA BERKAS KE AKUNTING ─────────────────── --}}
    <table style="margin-bottom: 5px; border: 1px dashed #64748b; background: #f8fafc; border-radius: 4px; padding: 3px 6px; font-size: 6.5pt;">
        <tr>
            <td colspan="2" style="font-weight: bold; color: #0f172a; text-transform: uppercase; border-bottom: 1px solid #cbd5e1; padding-bottom: 1px;">
                LEMBAR TANDA TERIMA PENYERAHAN BERKAS KE BAGIAN KEUANGAN / AKUNTING:
            </td>
        </tr>
        <tr>
            <td colspan="2" style="color: #475569; padding: 2px 0;">
                Seluruh verifikasi presensi dan nominal siswa billable telah tuntas diverifikasi oleh Operasional. Berkas invoice fisik/digital diserahkan ke Bagian Keuangan untuk proses penagihan resmi.
            </td>
        </tr>
        <tr>
            <td style="width: 50%; padding-top: 3px;">
                <span style="color: #64748b;">Diserahkan Oleh (Operasional):</span><br>
                <strong style="color: #0f172a;">{{ $invoice->operasionalUser?->nama_lengkap ?? $invoice->operasionalUser?->name ?? 'Dinda / Novandi' }}</strong><br>
                <span style="color: #94a3b8; font-size: 5.8pt;">Tgl: {{ $invoice->operasional_approved_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}</span>
            </td>
            <td style="width: 50%; padding-top: 3px; text-align: right;">
                <span style="color: #64748b;">Diterima Oleh (Keuangan / Akunting):</span><br>
                <strong style="color: #0f172a;">{{ $invoice->serah_terima_akunting_penerima ?: ($invoice->akuntingUser?->nama_lengkap ?? 'Rendy') }}</strong><br>
                <span style="color: #94a3b8; font-size: 5.8pt;">Tgl: {{ ($invoice->serah_terima_akunting_at ?? $invoice->akunting_approved_at ?? $invoice->operasional_approved_at ?? now())->translatedFormat('d F Y') }}</span>
            </td>
        </tr>
    </table>

    {{-- ─── 6. FOOTER HALAMAN 1 ────────────────────────────────────────── --}}
    <table style="border-top: 1px solid #cbd5e1; padding-top: 2px; font-size: 6pt; color: #94a3b8;">
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
{{-- ═════════════════════════════════════════════════════════════════════════ --}}
@if(!empty($attendanceData))
@foreach($attendanceData as $attIndex => $att)
<div class="page-break" style="padding-top: 2px;">

    {{-- Header Lampiran Formal --}}
    <div style="text-align: center; margin-bottom: 5px; border-bottom: 1.5px solid #0f172a; padding-bottom: 3px;">
        <div style="font-size: 10pt; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;">
            LAMPIRAN REKAPITULASI PRESENSI &amp; LAPORAN MENGAJAR
        </div>
        <div style="font-size: 7.8pt; font-weight: bold; color: #475569; text-transform: uppercase; margin-top: 1px;">
            {{ $att['school_name'] }} &mdash; {{ $att['program_nama'] }} &middot; T.A. {{ $invoice->tahun_ajaran }}
        </div>
        <div style="font-size: 6.2pt; color: #64748b; margin-top: 1px;">
            Lampiran Faktur Tagihan: <strong>{{ $invoice->nomor_invoice }}</strong> &middot; Rombel: <strong>{{ $att['rombel_nama'] }}</strong>
        </div>
    </div>

    {{-- Meta Grid Rombel & Pengajar --}}
    <table style="margin-bottom: 5px; font-size: 7pt; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 4px; padding: 3px 6px;">
        <tr>
            <td style="width: 15%; color: #64748b; padding: 1px 0;">Nama Sekolah</td>
            <td style="width: 35%; font-weight: bold; padding: 1px 0; color: #0f172a;">: {{ $att['school_name'] }}</td>
            <td style="width: 15%; color: #64748b; padding: 1px 0;">Instruktur</td>
            <td style="width: 35%; font-weight: bold; padding: 1px 0; color: #0f172a;">: {{ $att['instructor_name'] }}</td>
        </tr>
        <tr>
            <td style="color: #64748b; padding: 1px 0;">Program</td>
            <td style="font-weight: bold; padding: 1px 0; color: #0f172a;">: {{ $att['program_nama'] }}</td>
            <td style="color: #64748b; padding: 1px 0;">PIC Sekolah</td>
            <td style="font-weight: bold; padding: 1px 0; color: #0f172a;">: {{ $invoice->pic_nama ?: ($att['pic_name'] ?: '-') }}</td>
        </tr>
        <tr>
            <td style="color: #64748b; padding: 1px 0;">Rombel</td>
            <td style="font-weight: bold; padding: 1px 0; color: #0f172a;">: {{ $att['rombel_nama'] }}</td>
            <td style="color: #64748b; padding: 1px 0;">Periode / Sesi</td>
            <td style="font-weight: bold; padding: 1px 0; color: #0f172a;">: {{ $invoice->periode_label }} @if($invoice->sesi_dari && $invoice->sesi_sampai)(Sesi {{ $invoice->sesi_dari }}&ndash;{{ $invoice->sesi_sampai }})@endif</td>
        </tr>
    </table>

    {{-- TABEL PERSETUJUAN KONFIRMASI SESI KEHADIRAN (FORMAT RESMI CHECKLIST) --}}
    <div style="margin-bottom: 5px; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 4px; padding: 4px 6px;">
        <div style="font-size: 6.8pt; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 3px;">
            Persetujuan Konfirmasi Data Kehadiran per Sesi:
        </div>
        <table style="font-size: 6.5pt; width: 100%;">
            <thead>
                <tr style="background: #f1f5f9; color: #0f172a; font-weight: bold;">
                    <th style="padding: 2px 4px; text-align: left; width: 45%;">Sesi Pembelajaran</th>
                    <th style="padding: 2px 4px; text-align: center; width: 25%;">Jumlah Siswa Hadir</th>
                    <th style="padding: 2px 4px; text-align: center; width: 15%;">Kecocokan</th>
                    <th style="padding: 2px 4px; text-align: center; width: 15%;">Setuju</th>
                </tr>
            </thead>
            <tbody>
                @foreach($att['sessionReports'] as $sIdx => $report)
                <tr>
                    <td style="padding: 2px 4px; border-bottom: 0.5px solid #e2e8f0;">
                        {{ $sIdx + 1 }}. Sesi {{ $report['nomor_pertemuan'] }} &mdash; {{ $report['tanggal'] }}
                    </td>
                    <td style="padding: 2px 4px; text-align: center; font-weight: bold; border-bottom: 0.5px solid #e2e8f0;">
                        {{ $report['total_hadir'] }} orang
                    </td>
                    <td style="padding: 2px 4px; text-align: center; color: #166534; border-bottom: 0.5px solid #e2e8f0;">
                        Sesuai
                    </td>
                    <td style="padding: 2px 4px; text-align: center; font-weight: bold; color: #166534; border-bottom: 0.5px solid #e2e8f0;">
                        [ &#10003; ]
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background: #f8fafc;">
                    <td style="padding: 3px 4px; border-top: 1px solid #cbd5e1;">
                        Daftar Hadir: {{ $invoice->jumlah_siswa_billable }} orang 
                        @if(($invoice->jumlah_siswa_gratis ?? 0) > 0)
                            &middot; Gratis: {{ $invoice->jumlah_siswa_gratis }} orang
                        @endif
                    </td>
                    <td colspan="3" style="padding: 3px 4px; text-align: right; border-top: 1px solid #cbd5e1; color: #1e3a8a; font-size: 7.2pt;">
                        Jumlah siswa yang ditagihkan pada invoice: <strong>{{ $invoice->billable_efektif }} orang</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    @php
        $sessions = $att['sessions'];
        $students = $att['students'];
        $attendanceMap = $att['attendanceMap'];
        $colCount = max(1, $sessions->count());
        $colWidth = round(34 / $colCount, 1);
        $totalStudents = $students->count();

        $gratisIds = collect($invoice->siswa_gratis_list ?? [])->pluck('siswa_id')->toArray();

        // Responsive density
        if ($totalStudents > 35) {
            $rowPadding = '0.5px 2px';
            $rowFontSize = '5.6pt';
            $lineHeight = '1.0';
        } elseif ($totalStudents > 25) {
            $rowPadding = '1px 3px';
            $rowFontSize = '6.4pt';
            $lineHeight = '1.1';
        } else {
            $rowPadding = '2px 4px';
            $rowFontSize = '6.8pt';
            $lineHeight = '1.2';
        }
    @endphp

    {{-- Tabel Presensi Siswa --}}
    <div style="margin-bottom: 5px;">
        <div style="font-size: 6.8pt; font-weight: bold; color: #0f172a; margin-bottom: 2px; text-transform: uppercase;">
            Rincian Presensi Nama Siswa:
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
                        <th style="border: 1px solid #334155; padding: 1px 1px; width: {{ $colWidth }}%;">
                            P.{{ $sess->nomor_pertemuan }}<br>
                            <span style="font-size: 5.2pt; font-weight: normal; color: #93c5fd;">{{ $tglShort }}</span>
                        </th>
                    @endforeach
                    <th style="border: 1px solid #334155; padding: 2px 2px; width: 7%;">Hadir</th>
                    <th style="border: 1px solid #334155; padding: 2px 2px; width: 8%;">Status Tagihan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $stIdx => $student)
                @php
                    $hadirCount = 0;
                    $isStudentGratis = in_array($student->id, $gratisIds);
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
                                <span style="font-weight: bold; color: #166534; font-size: 7pt;">&#10003;</span>
                            @elseif($stHadir === 0)
                                <span style="color: #dc2626; font-weight: bold; font-size: 6.5pt;">&times;</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                    @endforeach
                    <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #0f172a; padding: {{ $rowPadding }};">
                        {{ $hadirCount }}
                    </td>
                    <td style="border: 1px solid #cbd5e1; text-align: center; padding: {{ $rowPadding }}; font-size: 5.5pt;">
                        @php
                            $minHadirLimit = ($sessions->count() >= 4) ? 2 : max(1, (int) ceil($sessions->count() / 2));
                        @endphp
                        @if($isStudentGratis)
                            <span style="color: #ea580c; font-weight: bold;">GRATIS</span>
                        @elseif(isset($student->pivot) && $student->pivot->status === 'keluar')
                            <span style="color: #dc2626; font-weight: bold;">Keluar</span>
                        @elseif($hadirCount < $minHadirLimit)
                            <span style="color: #64748b; font-weight: bold;">Tidak Ditagihkan (&lt;{{ $minHadirLimit }}x)</span>
                        @else
                            <span style="color: #166534; font-weight: bold;">Ditagihkan</span>
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
        <div style="margin-bottom: 4px;">
            <div style="font-size: 6.8pt; font-weight: bold; color: #0f172a; margin-bottom: 2px; text-transform: uppercase;">
                Rincian Materi Pembelajaran per Sesi:
            </div>
            <table style="font-size: 5.8pt; border: 1px solid #cbd5e1;">
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

        {{-- Footer Lampiran --}}
        <table style="border-top: 1px solid #cbd5e1; padding-top: 2px; margin-top: 2px;">
            <tr>
                <td style="font-size: 5.8pt; color: #94a3b8; vertical-align: middle;">
                    Lampiran Faktur Tagihan: {{ $invoice->nomor_invoice }} &middot; Rombel: {{ $att['rombel_nama'] }}
                </td>
                <td style="font-size: 5.8pt; color: #94a3b8; text-align: right; vertical-align: middle;">
                    Halaman {{ $attIndex + 2 }} (Lampiran Presensi &amp; Laporan)
                </td>
            </tr>
        </table>
    </div>

</div>
@endforeach
@endif

{{-- ═════════════════════════════════════════════════════════════════════════ --}}
{{-- HALAMAN 3: LAMPIRAN BUKTI CHAT WHATSAPP DENGAN PIC SEKOLAH               --}}
{{-- ═════════════════════════════════════════════════════════════════════════ --}}
@php
    $buktiChatFile = $invoice->bukti_chat_path ? storage_path('app/public/' . $invoice->bukti_chat_path) : null;
@endphp
@if($buktiChatFile && file_exists($buktiChatFile))
<div class="page-break" style="padding-top: 4px;">
    <div style="text-align: center; margin-bottom: 8px; border-bottom: 1.5px solid #0f172a; padding-bottom: 4px;">
        <div style="font-size: 10.5pt; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;">
            LAMPIRAN BUKTI KONFIRMASI CHAT WHATSAPP DENGAN PIC SEKOLAH
        </div>
        <div style="font-size: 7.5pt; font-weight: bold; color: #475569; text-transform: uppercase; margin-top: 1px;">
            {{ $invoice->sekolah?->namasekolah ?? '-' }} &middot; Invoice: {{ $invoice->nomor_invoice }}
        </div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 1px;">
            PIC Dihubungi: <strong>{{ $invoice->pic_nama ?: 'Koordinator Sekolah' }}</strong> &middot; Tanggal: <strong>{{ $invoice->pic_konfirmasi_tgl?->translatedFormat('d F Y, H:i') ?? $invoice->operasional_approved_at?->translatedFormat('d F Y, H:i') }} WIB</strong>
        </div>
    </div>

    <div style="text-align: center; margin: 15px auto; padding: 10px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; max-width: 90%;">
        <img src="{{ $buktiChatFile }}" 
             style="max-width: 95%; max-height: 220mm; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 4px;">
    </div>

    <div style="font-size: 6.5pt; color: #64748b; text-align: center; margin-top: 6px; font-style: italic;">
        Dokumen tangkapan layar percakapan WhatsApp di atas adalah bukti persetujuan resmi dan komitmen kehadiran siswa oleh PIC Sekolah untuk penerbitan faktur tagihan ini.
    </div>

    <table style="border-top: 1px solid #cbd5e1; padding-top: 2px; margin-top: 15px;">
        <tr>
            <td style="font-size: 5.8pt; color: #94a3b8; vertical-align: middle;">
                Lampiran Bukti Chat &middot; No: {{ $invoice->nomor_invoice }}
            </td>
            <td style="font-size: 5.8pt; color: #94a3b8; text-align: right; vertical-align: middle;">
                Lampiran Otorisasi PIC
            </td>
        </tr>
    </table>
</div>
@endif

</body>
</html>
