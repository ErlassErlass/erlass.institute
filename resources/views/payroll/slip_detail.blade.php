@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex align-items-center gap-2 mb-4">
        @if (auth()->user()->role === 'instruktur')
            <a href="{{ route('payroll.my-salaries') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        @else
            <a href="{{ route('admin.payroll.batches.show', $item->payroll_batch_id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Batch
            </a>
        @endif
        <h5 class="mb-0 fw-bold text-dark">Rincian Slip Gaji</h5>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Receipt Card (Official Erlass Slip Structure) -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Official Letterhead Header -->
                        <div class="text-center pb-3 border-bottom mb-3">
                            <h4 class="fw-bold text-dark mb-0">erlass</h4>
                            <small class="text-muted text-uppercase tracking-wider font-monospace" style="font-size: 0.75rem; letter-spacing: 2px;">PROKREATIF INDONESIA</small>
                            <div class="mt-2">
                                <span class="badge bg-light text-dark border font-monospace">{{ $item->batch->code }}</span>
                            </div>
                        </div>

                        <!-- Recipient & Bank Metadata -->
                        <div class="p-3 bg-light rounded border mb-3" style="font-size: 0.88rem;">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Nama:</span>
                                <span class="fw-bold text-dark text-end">{{ $item->instruktur->nama_lengkap }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">No. Rek:</span>
                                <span class="fw-semibold text-dark text-end font-monospace">
                                    {{ $item->instruktur->instructorProfile->nama_bank ?? '-' }} 
                                    {{ $item->instruktur->instructorProfile->no_rekening ?? '-' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">An. Rekening:</span>
                                <span class="fw-semibold text-dark text-end">{{ $item->instruktur->instructorProfile->nama_pemilik_rekening ?? $item->instruktur->nama_lengkap }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Unit Location:</span>
                                <span class="fw-semibold text-dark">Erlass</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Periode:</span>
                                @php
                                    $period = \Carbon\Carbon::parse($item->batch->periode);
                                    $startDate = $period->copy()->subMonth()->day(11)->format('d F Y');
                                    $endDate = $period->copy()->day(10)->format('d F Y');
                                @endphp
                                <span class="fw-semibold text-dark">{{ $startDate }} - {{ $endDate }}</span>
                            </div>
                        </div>

                        <!-- Penerimaan vs Potongan Table Layout -->
                        @php
                            $grossTotal = $item->total_gross_salary ?: ($item->total_base_fee + $item->total_asisten_fee + $item->total_product_bonus + $item->total_transport_fee);
                            $taxVal = $item->tax_amount ?: round($grossTotal * 0.025);
                            $totalPotongan = $taxVal + $item->total_penalty;
                            $sesiU = $item->total_sessions_utama ?: $item->payrollItemSessions->where('role', 'utama')->count();
                            $sesiA = $item->total_sessions_asisten ?: $item->payrollItemSessions->where('role', 'asisten')->count();
                            if ($sesiU === 0 && $sesiA === 0) $sesiU = $item->total_sessions;
                        @endphp

                        <div class="row g-2 mb-3" style="font-size: 0.88rem;">
                            <!-- Column 1: Penerimaan -->
                            <div class="col-6">
                                <div class="p-2 border rounded bg-white h-100">
                                    <strong class="d-block border-bottom pb-1 mb-2 text-primary">PENERIMAAN</strong>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Honor:</span>
                                        <span class="fw-semibold">Rp {{ number_format($item->total_base_fee, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Transport:</span>
                                        <span class="fw-semibold">Rp {{ number_format($item->total_transport_fee, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Honor Asisten:</span>
                                        <span class="fw-semibold text-info">Rp {{ number_format($item->total_asisten_fee, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Transport Asisten:</span>
                                        <span class="fw-semibold text-muted">Rp 0</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Column 2: Potongan -->
                            <div class="col-6">
                                <div class="p-2 border rounded bg-white h-100">
                                    <strong class="d-block border-bottom pb-1 mb-2 text-danger">POTONGAN</strong>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Pajak (2.5%):</span>
                                        <span class="fw-semibold text-warning">Rp {{ number_format($taxVal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Denda Checkin:</span>
                                        <span class="fw-semibold text-danger">Rp {{ number_format($item->total_penalty, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Kekurangan:</span>
                                        <span class="fw-semibold text-muted">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grand Totals Summary Box -->
                        <div class="p-3 bg-light rounded border mb-3">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 0.9rem;">
                                <span class="text-muted">Total Penerimaan Kotor:</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($grossTotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" style="font-size: 0.9rem;">
                                <span class="text-danger">Total Potongan:</span>
                                <span class="fw-bold text-danger">-Rp {{ number_format($totalPotongan, 0, ',', '.') }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark fs-6">GAJI BERSIH:</span>
                                <span class="fw-bold text-success fs-4">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Note Box (Session Counts) -->
                        <div class="p-2 bg-white rounded border border-dashed text-muted small mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Total Mengajar Instruktur:</span>
                                <strong class="text-dark">{{ $sesiU }} Pertemuan</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Total Mengajar Asisten Instruktur:</span>
                                <strong class="text-dark">{{ $sesiA }} Pertemuan</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status Badge -->
                    <div class="border-top pt-3 text-center">
                        <small class="text-muted d-block mb-1">Status Pembayaran</small>
                        @if ($item->status === 'pending')
                            <span class="badge bg-secondary py-2 px-4 fs-6">DRAFT BATCH</span>
                        @elseif ($item->status === 'approved' || $item->status === 'processed')
                            <span class="badge bg-primary py-2 px-4 fs-6">TERVERIFIKASI</span>
                        @elseif ($item->status === 'paid')
                            <span class="badge bg-success py-2 px-4 fs-6">LUNAS DIBAYARKAN</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sessions List Table -->
        <div class="col-lg-7">
            @php
                $sessionEntries = $item->payrollItemSessions->isNotEmpty()
                    ? $item->payrollItemSessions
                    : $item->sessions;
            @endphp
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">Rincian Sesi Mengajar Terhitung</h5>
                        <small class="text-muted">Total: {{ $sessionEntries->count() }} Sesi Terhitung</small>
                    </div>
                    @if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']) && optional($item->batch)->status !== 'paid')
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSessionModal">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Sesi ke Slip
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Sesi / Sekolah</th>
                                    <th class="text-center">Peran</th>
                                    <th>Jadwal / Rombel</th>
                                    <th class="text-end">Honor</th>
                                    <th class="text-end">Transport</th>
                                    <th class="text-end">Denda</th>
                                    @if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']))
                                        <th class="text-center">Aksi / Koreksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sessionEntries as $entry)
                                    @php
                                        $session = ($entry instanceof \App\Models\PayrollItemSession) ? $entry->session : $entry;
                                        if (!$session) continue;

                                        $role = ($entry instanceof \App\Models\PayrollItemSession) ? $entry->role : 'utama';
                                        $baseFee = ($entry instanceof \App\Models\PayrollItemSession) 
                                            ? $entry->base_fee 
                                            : ($session->override_fee !== null ? $session->override_fee : $session->calculated_fee);
                                        $transFee = ($entry instanceof \App\Models\PayrollItemSession) ? $entry->transport_fee : $session->transport_fee;
                                        $penFee = ($entry instanceof \App\Models\PayrollItemSession) ? $entry->penalty_fee : $session->actual_checkin_penalty;

                                        $catLower = strtolower($session->laporanMengajar->kategori_pengajaran ?? $session->topik_materi ?? '');
                                        $isAdHocSess = ($session->nomor_pertemuan == 0)
                                            || ($session->laporanMengajar && $session->laporanMengajar->ekstrakurikuler_session_id === null)
                                            || str_contains($catLower, 'pameran')
                                            || str_contains($catLower, 'sosialisasi')
                                            || str_contains($catLower, 'trial')
                                            || str_contains($catLower, 'lomba')
                                            || str_contains($catLower, 'pendampingan')
                                            || str_contains($catLower, 'per-pertemuan')
                                            || str_contains($catLower, 'event');

                                        $displayTitle = $isAdHocSess
                                            ? ($session->laporanMengajar->kategori_pengajaran ?? $session->topik_materi ?? 'Kegiatan Khusus / Ad-Hoc')
                                            : 'Pertemuan Ke-' . $session->nomor_pertemuan;
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark">{{ $displayTitle }}</div>
                                            <small class="text-muted d-block">{{ optional(optional($session->ekstrakurikuler)->sekolah)->namasekolah ?? 'Sekolah' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $role === 'asisten' ? 'bg-info bg-opacity-25 text-dark border border-info' : 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' }} px-2 py-1">
                                                {{ $role === 'asisten' ? 'Asisten' : 'Instruktur' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ optional($session->rombel)->nama_rombel ?? '-' }}</div>
                                            <small class="text-muted">{{ $session->tanggal_pelaksanaan ? $session->tanggal_pelaksanaan->format('d/m/Y') : ($session->tanggal_terjadwal ? $session->tanggal_terjadwal->format('d/m/Y') : '-') }}</small>
                                        </td>
                                        <td class="text-end">
                                            @if ($session->override_fee !== null && $role === 'utama')
                                                <span class="text-decoration-line-through text-muted small">Rp {{ number_format($session->calculated_fee, 0, ',', '.') }}</span>
                                                <div class="fw-bold text-success">Rp {{ number_format($session->override_fee, 0, ',', '.') }}</div>
                                            @else
                                                <span class="fw-semibold text-dark">Rp {{ number_format($baseFee, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold text-dark">Rp {{ number_format($transFee, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end">
                                            @if ($penFee > 0)
                                                <span class="text-danger fw-semibold">-Rp {{ number_format($penFee, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        @if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']))
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center gap-1">
                                                    @if($item->status === 'pending' && $role === 'utama')
                                                        <form method="POST" action="{{ route('ekstrakurikuler.sessions.override-fee', $session->id) }}" class="d-inline-flex align-items-center gap-1">
                                                            @csrf
                                                            <input 
                                                                 type="number" 
                                                                 name="override_fee" 
                                                                 class="form-control form-control-sm text-center" 
                                                                 style="width: 80px;" 
                                                                 placeholder="Override" 
                                                                 value="{{ $session->override_fee }}"
                                                            >
                                                            <button type="submit" class="btn btn-sm btn-primary" title="Terapkan Override">
                                                                <i class="bi bi-check-lg"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if(optional($item->batch)->status !== 'paid')
                                                        <form method="POST" action="{{ route('admin.payroll.items.detach-session', [$item->id, $session->id]) }}" onsubmit="return confirm('Apakah Anda yakin ingin melepas Sesi #{{ $session->id }} dari slip gaji ini? Sesi akan kembali berstatus Unpaid.')" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Lepas Sesi dari Slip">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']) && optional($item->batch)->status !== 'paid')
<!-- Modal Tambah Sesi ke Slip -->
<div class="modal fade" id="addSessionModal" tabindex="-1" aria-labelledby="addSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('admin.payroll.items.attach-session', $item->id) }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="addSessionModalLabel">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Sesi Mengajar ke Slip Gaji
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @php
                        $period = \Carbon\Carbon::parse($item->batch->periode);
                        $cutoffStr = $period->copy()->day(10)->format('d F Y');
                    @endphp
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> 
                        Menampilkan sesi mengajar milik <strong>{{ $item->instruktur->nama_lengkap }}</strong> yang berstatus <strong>Selesai</strong>, belum pernah dibayar (<strong>Unpaid</strong>), dan bertanggal sebelum/pada batas cutoff 
                        <strong>({{ $cutoffStr }})</strong>.
                    </div>

                    @if(!isset($eligibleSessions) || $eligibleSessions->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0 fw-semibold">Tidak ada sesi mengajar selesai yang belum terbayar untuk instruktur ini sebelum tanggal cutoff.</p>
                            <small class="text-muted">Pastikan laporan mengajar sesi sudah disubmit oleh instruktur dan tidak melebihi tanggal 10 periode ini.</small>
                        </div>
                    @else
                        <div class="table-responsive" style="max-height: 380px;">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="50" class="text-center">Pilih</th>
                                        <th>Sesi / Sekolah</th>
                                        <th>Jadwal / Rombel</th>
                                        <th class="text-center">Peran</th>
                                        <th class="text-end">Estimasi Honor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($eligibleSessions as $eligSession)
                                        @php
                                            $isUtama = ($eligSession->user_id_instruktur == $item->user_id_instruktur);
                                            $eligRole = $isUtama ? 'utama' : 'asisten';
                                            $calcPreview = app(\App\Services\PayrollCalculatorService::class)->calculateSessionFee($eligSession, $eligRole);
                                            $estHonor = (float) $calcPreview['calculated_fee'] + (float) $calcPreview['transport_fee'] - (float) $calcPreview['actual_checkin_penalty'];
                                            $sessDate = $eligSession->tanggal_pelaksanaan ? $eligSession->tanggal_pelaksanaan->format('d/m/Y') : ($eligSession->tanggal_terjadwal ? $eligSession->tanggal_terjadwal->format('d/m/Y') : '-');
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <input 
                                                    class="form-check-input" 
                                                    type="radio" 
                                                    name="session_id" 
                                                    id="session_radio_{{ $eligSession->id }}" 
                                                    value="{{ $eligSession->id }}" 
                                                    required
                                                >
                                            </td>
                                            <td>
                                                <label class="form-check-label fw-bold text-dark d-block" for="session_radio_{{ $eligSession->id }}" style="cursor: pointer;">
                                                    Pertemuan Ke-{{ $eligSession->nomor_pertemuan }} (ID #{{ $eligSession->id }})
                                                </label>
                                                <small class="text-muted">{{ optional(optional($eligSession->ekstrakurikuler)->sekolah)->namasekolah ?? 'Sekolah' }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ optional($eligSession->rombel)->nama_rombel ?? '-' }}</div>
                                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $sessDate }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $eligRole === 'asisten' ? 'bg-info bg-opacity-25 text-dark border border-info' : 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' }} px-2 py-1">
                                                    {{ $eligRole === 'asisten' ? 'Asisten' : 'Utama' }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="fw-bold text-success">Rp {{ number_format($estHonor, 0, ',', '.') }}</div>
                                                <small class="text-muted">Transport: Rp {{ number_format($calcPreview['transport_fee'], 0, ',', '.') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    @if(isset($eligibleSessions) && $eligibleSessions->isNotEmpty())
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="bi bi-plus-lg me-1"></i> Tambahkan ke Slip & Hitung Ulang
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection


