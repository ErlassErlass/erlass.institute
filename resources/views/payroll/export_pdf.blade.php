<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rincian Payroll Batch {{ $batch->code }} - Erlass Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #1e293b; background-color: #fff; }
        .table-pdf th { background-color: #f1f5f9 !important; color: #0f172a; font-weight: 700; border-bottom: 2px solid #cbd5e1; }
        .table-pdf td, .table-pdf th { padding: 8px 10px; border-color: #e2e8f0; }
        .header-logo { max-height: 50px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
            .container-fluid { width: 100% !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="p-4">

    <!-- Print Action Bar -->
    <div class="no-print mb-4 p-3 bg-light border rounded d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-printer me-2"></i>Cetak Laporan Rincian Payroll</h6>
            <small class="text-muted">Klik tombol di kanan untuk mengunduh PDF atau mencetak langsung.</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary fw-bold">
                <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Header Letterhead -->
    <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom border-2 border-dark">
        <div>
            <h3 class="fw-bold text-dark mb-0">ERLASS INSTITUTE</h3>
            <p class="text-muted small mb-0">Laporan Rekapitulasi & Slip Kompensasi Instruktur Ekstrakurikuler</p>
            <small class="text-secondary">Jl. Pejaten Barat No. 10A, Pasar Minggu, Jakarta Selatan</small>
        </div>
        <div class="text-end">
            <h5 class="fw-bold text-primary mb-1">BATCH PAYROLL</h5>
            <span class="badge bg-dark fs-6 px-3 py-1">{{ $batch->code }}</span>
        </div>
    </div>

    <!-- Metadata Section -->
    <div class="row g-3 mb-4 p-3 bg-light rounded border">
        <div class="col-3">
            <small class="text-muted d-block">Periode Cutoff</small>
            <strong class="text-dark">{{ $batch->periode->format('F Y') }}</strong>
        </div>
        <div class="col-3">
            <small class="text-muted d-block">Total Instruktur</small>
            <strong class="text-dark">{{ $batch->items->count() }} Orang</strong>
        </div>
        <div class="col-3">
            <small class="text-muted d-block">Total Sesi Terbayar</small>
            <strong class="text-dark">{{ $batch->items->sum('total_sessions') }} Sesi</strong>
        </div>
        <div class="col-3">
            <small class="text-muted d-block">Status Batch</small>
            <strong class="text-success text-uppercase">{{ $batch->status }}</strong>
        </div>
    </div>

    <!-- Table 1: Bank Transfer Summary -->
    <div class="mb-5">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. REKAPITULASI TRANSFER BANK INSTRUKTUR</h6>
        <table class="table table-bordered table-pdf align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>ID Instruktur</th>
                    <th>Nama Instruktur</th>
                    <th>Nama Bank</th>
                    <th>Nomor Rekening</th>
                    <th>Nama Pemilik Rekening</th>
                    <th class="text-center">Sesi Utama</th>
                    <th class="text-center">Sesi Asisten</th>
                    <th class="text-center">Total Sesi</th>
                    <th class="text-end">Total Nominal Transfer (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalNet = 0; 
                    $totalSess = 0; 
                    $totalUtama = 0;
                    $totalAsisten = 0;
                @endphp
                @foreach($batch->items as $idx => $item)
                @php
                    $profile = $item->instruktur->instructorProfile ?? null;
                    $sessions = $item->sessions;
                    $sesiUtama = $sessions->filter(fn($s) => (int)$s->user_id_instruktur === (int)$item->user_id_instruktur)->count();
                    $sesiAsisten = $sessions->filter(fn($s) => (int)$s->user_id_asisten === (int)$item->user_id_instruktur && (int)$s->user_id_instruktur !== (int)$item->user_id_instruktur)->count();
                    if ($sesiUtama + $sesiAsisten < $item->total_sessions) {
                        $sesiUtama = $item->total_sessions - $sesiAsisten;
                    }
                    $totalNet += $item->net_salary;
                    $totalSess += $item->total_sessions;
                    $totalUtama += $sesiUtama;
                    $totalAsisten += $sesiAsisten;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $item->instruktur->instructor_id ?? $item->instruktur->id }}</td>
                    <td class="fw-bold">{{ $item->instruktur->nama_lengkap }}</td>
                    <td>{{ $profile->nama_bank ?? '-' }}</td>
                    <td class="font-monospace">{{ $profile->no_rekening ?? '-' }}</td>
                    <td>{{ $profile->nama_pemilik_rekening ?? $item->instruktur->nama_lengkap }}</td>
                    <td class="text-center">{{ $sesiUtama }}</td>
                    <td class="text-center">{{ $sesiAsisten }}</td>
                    <td class="text-center fw-bold">{{ $item->total_sessions }}</td>
                    <td class="text-end fw-bold text-dark">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary fw-bold">
                    <td colspan="6" class="text-end">TOTAL KESELURUHAN DANA PAYROLL:</td>
                    <td class="text-center">{{ $totalUtama }}</td>
                    <td class="text-center">{{ $totalAsisten }}</td>
                    <td class="text-center">{{ $totalSess }} Sesi</td>
                    <td class="text-end text-success fs-6">Rp {{ number_format($totalNet, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Table 2: Accounting Journal Summary -->
    <div class="mb-5">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. REKAPITULASI JURNAL AKUNTANSI (KOMPOSISI DANA & POTONGAN)</h6>
        <table class="table table-bordered table-pdf align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>Nama Instruktur</th>
                    <th class="text-center">Sesi Utama</th>
                    <th class="text-center">Sesi Asisten</th>
                    <th class="text-center">Total Sesi</th>
                    <th class="text-end">Honor Dasar (Rp)</th>
                    <th class="text-end">Bonus Alat (Rp)</th>
                    <th class="text-end">Uang Transport (Rp)</th>
                    <th class="text-end">Potongan Denda (Rp)</th>
                    <th class="text-end">Gaji Netto (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batch->items as $idx => $item)
                @php
                    $sessions = $item->sessions;
                    $sesiUtama = $sessions->filter(fn($s) => (int)$s->user_id_instruktur === (int)$item->user_id_instruktur)->count();
                    $sesiAsisten = $sessions->filter(fn($s) => (int)$s->user_id_asisten === (int)$item->user_id_instruktur && (int)$s->user_id_instruktur !== (int)$item->user_id_instruktur)->count();
                    if ($sesiUtama + $sesiAsisten < $item->total_sessions) {
                        $sesiUtama = $item->total_sessions - $sesiAsisten;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $item->instruktur->nama_lengkap }}</td>
                    <td class="text-center">{{ $sesiUtama }}</td>
                    <td class="text-center">{{ $sesiAsisten }}</td>
                    <td class="text-center fw-bold">{{ $item->total_sessions }}</td>
                    <td class="text-end">Rp {{ number_format($item->total_base_fee, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($item->total_product_bonus, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($item->total_transport_fee, 0, ',', '.') }}</td>
                    <td class="text-end text-danger">{{ $item->total_penalty > 0 ? '-Rp ' . number_format($item->total_penalty, 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary fw-bold">
                    <td colspan="2" class="text-end">TOTAL JUMLAH:</td>
                    <td class="text-center">{{ $totalUtama }}</td>
                    <td class="text-center">{{ $totalAsisten }}</td>
                    <td class="text-center">{{ $batch->items->sum('total_sessions') }}</td>
                    <td class="text-end">Rp {{ number_format($batch->items->sum('total_base_fee'), 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($batch->items->sum('total_product_bonus'), 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($batch->items->sum('total_transport_fee'), 0, ',', '.') }}</td>
                    <td class="text-end text-danger">-Rp {{ number_format($batch->items->sum('total_penalty'), 0, ',', '.') }}</td>
                    <td class="text-end text-success fs-6">Rp {{ number_format($batch->items->sum('net_salary'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Table 3: Session Audit Details -->
    <div class="mb-5">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">3. AUDIT RINCIAN PER SESI MENGAJAR (INSTRUKTUR UTAMA & ASISTEN)</h6>
        <table class="table table-bordered table-pdf align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>ID Sesi</th>
                    <th>Tanggal</th>
                    <th>Sekolah Mitra & Program</th>
                    <th>Penerima Honor</th>
                    <th>Instruktur Utama</th>
                    <th>Asisten Instruktur</th>
                    <th class="text-center">Peran</th>
                    <th class="text-end">Honor Netto (Rp)</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $sessNo = 1; 
                    $calculator = app(\App\Services\PayrollCalculatorService::class);
                    $totalNetSesiAll = 0;
                @endphp
                @foreach($batch->items as $item)
                    @foreach($item->sessions as $session)
                    @php
                        $calc = $calculator->calculateSessionFee($session);
                        $baseFee = $session->override_fee !== null ? (float)$session->override_fee : (float)$calc['calculated_fee'];
                        $netFee = max(0, $baseFee + $calc['transport_fee'] - $calc['actual_checkin_penalty']);
                        $totalNetSesiAll += $netFee;

                        $sekolahName = optional(optional(optional($session->rombel)->ekstrakurikuler)->sekolah)->namasekolah ?? 'Kegiatan Office / Ad-Hoc';
                        $programName = optional(optional($session->rombel)->ekstrakurikuler)->kategori_program ?? 'Ad-Hoc';
                        $rombelName = optional($session->rombel)->nama_rombel;

                        $utamaName = optional($session->instruktur)->nama_lengkap 
                            ?? optional(optional($session->rombel)->instruktur)->nama_lengkap 
                            ?? '-';

                        $asistenName = optional($session->asisten)->nama_lengkap 
                            ?? optional(optional($session->rombel)->asisten)->nama_lengkap 
                            ?? '-';

                        $peran = ((int)$session->user_id_asisten === (int)$item->user_id_instruktur && (int)$session->user_id_instruktur !== (int)$item->user_id_instruktur)
                            ? 'Asisten'
                            : 'Utama';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $sessNo++ }}</td>
                        <td>#{{ $session->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($session->tanggal_pelaksanaan ?? $session->tanggal_terjadwal)->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $sekolahName }}</strong><br>
                            <small class="text-muted">{{ $programName }} @if($rombelName) ({{ $rombelName }}) @endif</small>
                        </td>
                        <td class="fw-bold">{{ $item->instruktur->nama_lengkap }}</td>
                        <td>{{ $utamaName }}</td>
                        <td>{{ $asistenName }}</td>
                        <td class="text-center">
                            <span class="badge {{ $peran === 'Utama' ? 'bg-primary' : 'bg-info text-dark' }}">{{ $peran }}</span>
                        </td>
                        <td class="text-end fw-bold">Rp {{ number_format($netFee, 0, ',', '.') }}</td>
                        <td class="text-center text-uppercase small">{{ $session->status }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary fw-bold">
                    <td colspan="8" class="text-end">TOTAL RINCIAN SESI:</td>
                    <td class="text-end text-success fs-6">Rp {{ number_format($totalNetSesiAll, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Signature Footer -->
    <div class="row mt-5 pt-4">
        <div class="col-4 text-center">
            <p class="mb-5">Dibuat Oleh,<br><strong>Admin Operational Payroll</strong></p>
            <p class="mt-4 border-top pt-2 text-muted">( ........................................ )</p>
        </div>
        <div class="col-4 text-center">
            <p class="mb-5">Diverifikasi Oleh,<br><strong>Finance / Akuntansi Erlass</strong></p>
            <p class="mt-4 border-top pt-2 text-muted">( ........................................ )</p>
        </div>
        <div class="col-4 text-center">
            <p class="mb-5">Disetujui Oleh,<br><strong>Manajemen Erlass Institute</strong></p>
            <p class="mt-4 border-top pt-2 text-muted">( ........................................ )</p>
        </div>
    </div>

</body>
</html>
