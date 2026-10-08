@extends('layouts.app')

@section('title', 'Invoice Management — Erlass')

@section('content')
<style>
    .bg-purple { background-color: rgba(124, 58, 237, var(--bs-bg-opacity, 1)) !important; }
    .text-purple { color: rgba(124, 58, 237, var(--bs-text-opacity, 1)) !important; }
    .border-purple { border-color: rgba(124, 58, 237, var(--bs-border-opacity, 1)) !important; }
    .bg-purple-subtle { background-color: rgba(124, 58, 237, 0.12) !important; }
    .border-purple-subtle { border-color: rgba(124, 58, 237, 0.25) !important; }
    .text-purple-emphasis { color: #6d28d9 !important; }
</style>
<div class="container-fluid py-4">

    {{-- ─── HERO BANNER ──────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden"
         style="background: linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 50%, #E0F2FE 100%);
                border-radius: 1rem; border: 1px solid #DBEAFE !important;">
        <div class="card-body p-4 p-lg-5 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success rounded-pill px-3 py-1 small fw-bold">
                            <i class="bi bi-receipt me-1"></i> Sistem Invoice
                        </span>
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 small fw-bold">
                            <i class="bi bi-shield-check me-1"></i> Gate 1 Verifikasi PIC
                        </span>
                    </div>
                    <h1 class="h2 fw-bold text-dark mb-2">Manajemen Invoice Tagihan</h1>
                    <p class="text-muted mb-0 fs-6" style="max-width: 720px;">
                        Penerbitan &amp; verifikasi invoice per sekolah/rombel dengan konfirmasi mutlak PIC Sekolah di <strong>Gate 1</strong> (Dinda / Novandi).
                        Faktur resmi langsung terbit dan berkas diserahterimakan ke Bagian Keuangan/Akunting dengan lembar tanda terima.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ route('invoice.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>Buat Invoice Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── SUMMARY CARDS (5 TAHAPAN PROSES) ────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Invoice --}}
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm text-center h-100"
                 style="border-radius: .75rem; cursor:pointer;"
                 onclick="window.location.href='{{ route('invoice.index', array_merge(request()->except('page', 'tab', 'status'), ['tab' => 'all'])) }}';">
                <div class="card-body py-3 px-2">
                    <i class="bi bi-files fs-3 text-secondary"></i>
                    <div class="fw-bold fs-4 mt-1">{{ $totalCount }}</div>
                    <div class="text-muted small">Total Invoice</div>
                </div>
            </div>
        </div>
        {{-- Card 2: Menunggu Admin Produksi --}}
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm text-center h-100"
                 style="border-radius: .75rem; cursor:pointer;"
                 onclick="window.location.href='{{ route('invoice.index', array_merge(request()->except('page', 'tab', 'status'), ['tab' => 'pending_operasional'])) }}';">
                <div class="card-body py-3 px-2">
                    <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                    <div class="fw-bold fs-4 mt-1 text-warning-emphasis">{{ $operasionalPendingCount }}</div>
                    <div class="text-muted small">Menunggu Admin Produksi</div>
                </div>
            </div>
        </div>
        {{-- Card 3: Menunggu Revisi (Dikembalikan Akunting) --}}
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm text-center h-100"
                 style="border-radius: .75rem; cursor:pointer; {{ $revisiCount > 0 ? 'border: 1.5px solid #ef4444 !important; background: #fff5f5;' : '' }}"
                 onclick="window.location.href='{{ route('invoice.index', array_merge(request()->except('page', 'tab', 'status'), ['tab' => 'revisi'])) }}';">
                <div class="card-body py-3 px-2">
                    <i class="bi bi-arrow-return-left fs-3 text-danger"></i>
                    <div class="fw-bold fs-4 mt-1 text-danger">{{ $revisiCount }}</div>
                    <div class="text-danger fw-semibold small">Menunggu Revisi</div>
                </div>
            </div>
        </div>
        {{-- Card 4: Menunggu Staff Akunting --}}
        <div class="col-6 col-md-6 col-lg">
            <div class="card border-0 shadow-sm text-center h-100"
                 style="border-radius: .75rem; cursor:pointer;"
                 onclick="window.location.href='{{ route('invoice.index', array_merge(request()->except('page', 'tab', 'status'), ['tab' => 'pending_akunting'])) }}';">
                <div class="card-body py-3 px-2">
                    <i class="bi bi-bank fs-3 text-primary"></i>
                    <div class="fw-bold fs-4 mt-1 text-primary">{{ $akuntingPendingCount }}</div>
                    <div class="text-muted small">Menunggu Staff Akunting</div>
                </div>
            </div>
        </div>
        {{-- Card 5: Disetujui Resmi --}}
        <div class="col-6 col-md-6 col-lg">
            <div class="card border-0 shadow-sm text-center h-100"
                 style="border-radius: .75rem; cursor:pointer;"
                 onclick="window.location.href='{{ route('invoice.index', array_merge(request()->except('page', 'tab', 'status'), ['tab' => 'approved'])) }}';">
                <div class="card-body py-3 px-2">
                    <i class="bi bi-patch-check-fill fs-3 text-success"></i>
                    <div class="fw-bold fs-4 mt-1 text-success">{{ $approvedCount }}</div>
                    <div class="text-muted small">Disetujui Resmi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── PANEL ROMBEL SIAP DITAGIHKAN (ELIGIBLE FOR BILLING) ────────────── --}}
    {{-- ─── PANEL SEKOLAH SIAP DITAGIHKAN (ELIGIBLE FOR BILLING) ────────────── --}}
    @php
        $eligibleList = $eligibleSekolahs ?? $eligibleRombels ?? collect();
    @endphp
    @if($eligibleList->isNotEmpty() && $currentTab === 'all')
    <div id="panel-sekolah-siap-tagih" class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: .875rem; border-left: 5px solid #10b981 !important;">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                    <i class="bi bi-lightning-charge-fill text-white fs-6"></i>
                </span>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Sekolah Siap Ditagihkan ({{ $eligibleList->count() }})</h6>
                    <small class="text-muted">1 Invoice per Sekolah (rincian item per rombel) — Diurutkan prioritas keterlambatan pembuatan invoice</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="filterEligibleSkema" class="form-select form-select-sm bg-light border" style="width: 195px;" onchange="filterEligibleSchools()">
                    <option value="">Semua Skema</option>
                    <option value="bulanan">🗓️ Bulanan</option>
                    <option value="per_4_pertemuan">🔄 Per 4 Pertemuan</option>
                    <option value="semester">📚 Semesteran</option>
                    <option value="tahunan">🎓 Tahunan</option>
                    <option value="csr_reguler_soga">🤝 CSR Reguler SOGA</option>
                </select>
                <div class="input-group input-group-sm" style="width: 230px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted small"></i></span>
                    <input type="text" id="searchEligibleTable" class="form-control bg-light border-start-0" 
                           placeholder="Cari sekolah..." onkeyup="filterEligibleSchools()">
                </div>
                @if(auth()->user()?->hasRole(['admin', 'admin_sistem', 'webmaster']))
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1.5 rounded-pill shadow-xs text-nowrap" data-bs-toggle="modal" data-bs-target="#modalAturSkema">
                    <i class="bi bi-gear-fill me-1"></i> Atur Skema Tagihan
                </button>
                @endif
                <form action="{{ route('invoice.bulk-generate') }}" method="POST" onsubmit="return confirm('Generate invoice untuk semua {{ $eligibleList->count() }} sekolah yang siap ditagihkan?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success fw-bold px-3 py-1.5 rounded-pill shadow-xs text-nowrap">
                        <i class="bi bi-lightning-charge-fill me-1"></i> Generate Semua (Bulk)
                    </button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-uppercase text-muted" style="font-size: .75rem; letter-spacing: .5px;">
                        <th class="ps-4">Sekolah</th>
                        <th>Rombel & Program (Item)</th>
                        <th>Skema Tagihan</th>
                        <th>Periode Tagihan</th>
                        <th class="text-center">Total Siswa Billable</th>
                        <th>Target Invoice</th>
                        <th>Keterlambatan</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" id="eligibleTableBody">
                    @foreach($eligibleList as $item)
                    <tr data-skema="{{ $item['skema_tagihan'] }}">
                        <td class="ps-4 fw-semibold text-dark">
                            <span class="badge bg-light text-secondary border font-monospace me-1">[{{ $item['sekolah_kodlan'] }}]</span>
                            {{ $item['sekolah_nama'] }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">
                                <i class="bi bi-mortarboard-fill me-1 text-primary"></i>{{ $item['kategori_program'] ?? 'Program Ekskul' }}
                            </div>
                            <div class="text-muted small mt-0.5" style="font-size: .75rem;">
                                <i class="bi bi-diagram-3 me-1"></i>{{ $item['total_rombel'] }} Rombel: 
                                @foreach($item['items'] ?? [] as $it)
                                    <span class="badge bg-light text-muted border font-monospace me-1" style="font-size: .68rem;">
                                        {{ $it['rombel_nama'] }} ({{ $it['jumlah_siswa_billable'] }} sw)
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge {{ match($item['skema_tagihan']) {
                                    'bulanan'          => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                    'semester'         => 'bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25',
                                    'tahunan'          => 'bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25',
                                    'csr_reguler_soga' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                    default            => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                                } }} px-2 py-1"
                                @if($item['skema_tagihan'] === 'semester') style="background-color: rgba(124, 58, 237, 0.1) !important; color: #7c3aed !important; border-color: rgba(124, 58, 237, 0.25) !important;" @endif>
                                    {{ match($item['skema_tagihan']) {
                                        'bulanan'          => 'Bulanan',
                                        'semester'         => 'Semesteran',
                                        'tahunan'          => 'Tahunan',
                                        'csr_reguler_soga' => 'CSR SOGA',
                                        default            => 'Per 4 Pertemuan',
                                    } }}
                                </span>
                                @if(auth()->user()?->hasRole(['admin', 'admin_sistem', 'webmaster']))
                                <button type="button" class="btn btn-link p-0 text-muted btn-sm" title="Ubah Skema Tagihan Sekolah" onclick="quickEditSkema('{{ $item['sekolah_kodlan'] }}', '{{ addslashes($item['sekolah_nama']) }}', '{{ $item['skema_tagihan'] }}')">
                                    <i class="bi bi-pencil-square" style="font-size: 0.8rem;"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($item['skema_tagihan'] === 'bulanan')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">
                                    <i class="bi bi-calendar3 me-1"></i>{{ $item['periode_label'] }}
                                </span>
                            @elseif($item['skema_tagihan'] === 'semester')
                                <span class="badge bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25 px-2.5 py-1"
                                      style="background-color: rgba(124, 58, 237, 0.1) !important; color: #7c3aed !important; border-color: rgba(124, 58, 237, 0.25) !important;">
                                    <i class="bi bi-calendar-range me-1"></i>{{ $item['periode_label'] }}
                                </span>
                            @elseif($item['skema_tagihan'] === 'tahunan')
                                <span class="badge bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25 px-2.5 py-1">
                                    <i class="bi bi-calendar-check me-1"></i>{{ $item['periode_label'] }}
                                </span>
                            @elseif($item['skema_tagihan'] === 'csr_reguler_soga')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1">
                                    <i class="bi bi-heart-fill me-1 text-danger"></i>{{ $item['periode_label'] }}
                                </span>
                                <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-building me-1"></i>Tagihan: <strong class="text-dark">Solidaritas Erlangga</strong>
                                </div>
                            @else
                                {{-- Per 4 Pertemuan: detail waktu / sesi --}}
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">
                                    <i class="bi bi-layers me-1"></i>Sesi {{ $item['sesi_dari'] }}–{{ $item['sesi_sampai'] }}
                                </span>
                                @if(!empty($item['bulan_laporan_terakhir']))
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-calendar-event me-1"></i>Lap: <strong class="text-dark">{{ $item['bulan_laporan_terakhir'] }}</strong>
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border fw-bold px-2.5 py-1.5">
                                {{ $item['total_siswa_billable'] }} siswa
                            </span>
                        </td>
                        <td>
                            <div class="text-nowrap small fw-medium text-dark">
                                <i class="bi bi-calendar-event me-1 text-muted"></i>{{ $item['target_date_formatted'] ?? '-' }}
                            </div>
                        </td>
                        <td>
                            @if(($item['days_overdue'] ?? 0) > 0)
                                <span class="badge {{ $item['keterlambatan_badge'] ?? 'bg-danger text-white' }} px-2.5 py-1.5 rounded-pill shadow-xs text-nowrap">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $item['keterlambatan_label'] }}
                                </span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1.5 rounded-pill text-nowrap">
                                    <i class="bi bi-clock-history me-1"></i>{{ $item['keterlambatan_label'] ?? 'Hari Ini' }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <form action="{{ route('invoice.quick-generate') }}" method="POST" class="d-inline mb-0">
                                    @csrf
                                    <input type="hidden" name="sekolah_kodlan" value="{{ $item['sekolah_kodlan'] }}">
                                    @if(!empty($item['ekstrakurikuler_id']))
                                        <input type="hidden" name="ekstrakurikuler_id" value="{{ $item['ekstrakurikuler_id'] }}">
                                    @endif
                                    @if(!empty($item['ekstrakurikuler_rombel_id']))
                                        <input type="hidden" name="ekstrakurikuler_rombel_id" value="{{ $item['ekstrakurikuler_rombel_id'] }}">
                                    @endif
                                    <input type="hidden" name="skema_tagihan" value="{{ $item['skema_tagihan'] }}">
                                    <input type="hidden" name="periode_label" value="{{ $item['periode_label'] }}">
                                    <input type="hidden" name="periode_nomor" value="{{ $item['periode_nomor'] ?? '' }}">
                                    <input type="hidden" name="sesi_dari" value="{{ $item['sesi_dari'] ?? '' }}">
                                    <input type="hidden" name="sesi_sampai" value="{{ $item['sesi_sampai'] ?? '' }}">
                                    <input type="hidden" name="tahun_ajaran" value="{{ $item['tahun_ajaran'] }}">
                                    <button type="submit" class="btn btn-sm btn-success fw-semibold rounded-pill px-3 shadow-xs">
                                        <i class="bi bi-lightning-charge-fill me-1"></i> 1-Klik Generate
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ─── FILTER BAR ─────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
        <div class="card-body p-3">
            <form id="filter-form" method="GET" action="{{ route('invoice.index') }}"
                  class="row g-2 align-items-end">
                <input type="hidden" name="tab" id="filter-tab" value="{{ $currentTab }}">

                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Sekolah</label>
                    <select name="sekolah" class="form-select form-select-sm">
                        <option value="">Semua Sekolah</option>
                        @foreach($sekolahs as $s)
                            <option value="{{ $s->kodlan }}" {{ request('sekolah') === $s->kodlan ? 'selected' : '' }}>
                                [{{ $s->kodlan }}] {{ $s->namasekolah }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                    <select name="status" id="filter-status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $val => $lbl)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Skema</label>
                    <select name="skema" class="form-select form-select-sm">
                        <option value="">Semua Skema</option>
                        <option value="bulanan"          {{ request('skema') === 'bulanan'          ? 'selected' : '' }}>Bulanan</option>
                        <option value="semester"         {{ request('skema') === 'semester'         ? 'selected' : '' }}>Semester</option>
                        <option value="tahunan"          {{ request('skema') === 'tahunan'          ? 'selected' : '' }}>Tahunan</option>
                        <option value="per_4_pertemuan"  {{ request('skema') === 'per_4_pertemuan'  ? 'selected' : '' }}>Per 4 Pertemuan</option>
                        <option value="csr_reguler_soga" {{ request('skema') === 'csr_reguler_soga' ? 'selected' : '' }}>CSR SOGA</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" value="{{ request('tahun_ajaran', '2026/2027') }}"
                           class="form-control form-control-sm" placeholder="2026/2027">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('invoice.index', ['tab' => $currentTab]) }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── TAB NAVIGATION & INVOICE TABLE ─────────────────────────────────── --}}
    <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <ul class="nav nav-pills card-header-pills gap-1 flex-wrap">
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'all' && !request('status') ? 'active fw-bold' : 'text-dark' }} py-1.5 px-3" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab', 'status'), ['tab' => 'all'])) }}">
                        <i class="bi bi-collection me-1"></i>Semua Invoice
                        <span class="badge {{ $currentTab === 'all' && !request('status') ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $totalCount }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'pending_operasional' || request('status') === 'pending_operasional' ? 'active bg-warning text-dark fw-bold' : 'text-dark' }} py-1.5 px-3 position-relative" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab', 'status'), ['tab' => 'pending_operasional'])) }}">
                        <i class="bi bi-hourglass-split me-1 text-warning-emphasis"></i>Menunggu Admin Produksi
                        <span class="badge {{ $currentTab === 'pending_operasional' || request('status') === 'pending_operasional' ? 'bg-dark text-warning' : 'bg-warning-subtle text-warning-emphasis border border-warning' }} ms-1">
                            {{ $operasionalPendingCount }}
                        </span>
                        @if($operasionalPendingCount > 0 && $currentTab !== 'pending_operasional')
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Perlu review produksi</span>
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'revisi' || request('status') === 'revisi' ? 'active bg-danger text-white fw-bold shadow-xs' : 'text-dark' }} py-1.5 px-3 position-relative" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab', 'status'), ['tab' => 'revisi'])) }}">
                        <i class="bi bi-arrow-return-left me-1 {{ $currentTab === 'revisi' || request('status') === 'revisi' ? 'text-white' : 'text-danger' }}"></i>Menunggu Revisi
                        <span class="badge {{ $currentTab === 'revisi' || request('status') === 'revisi' ? 'bg-white text-danger' : 'bg-danger-subtle text-danger border border-danger' }} ms-1">
                            {{ $revisiCount }}
                        </span>
                        @if($revisiCount > 0 && $currentTab !== 'revisi' && request('status') !== 'revisi')
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Perlu revisi invoice</span>
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'pending_akunting' || request('status') === 'pending_akunting' ? 'active bg-primary text-white fw-bold' : 'text-dark' }} py-1.5 px-3 position-relative" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab', 'status'), ['tab' => 'pending_akunting'])) }}">
                        <i class="bi bi-shield-check me-1"></i>Menunggu Staff Akunting
                        <span class="badge {{ $currentTab === 'pending_akunting' || request('status') === 'pending_akunting' ? 'bg-white text-primary' : 'bg-primary-subtle text-primary border border-primary' }} ms-1">
                            {{ $akuntingPendingCount }}
                        </span>
                        @if($akuntingPendingCount > 0 && $currentTab !== 'pending_akunting')
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-primary border border-light rounded-circle">
                                <span class="visually-hidden">Perlu approval akunting</span>
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'approved' || request('status') === 'approved' ? 'active bg-success fw-bold' : 'text-dark' }} py-1.5 px-3" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab', 'status'), ['tab' => 'approved'])) }}">
                        <i class="bi bi-check2-circle me-1 text-success"></i>Disetujui Resmi
                        <span class="badge {{ $currentTab === 'approved' || request('status') === 'approved' ? 'bg-white text-success' : 'bg-success-subtle text-success border border-success-subtle' }} ms-1">{{ $approvedCount }}</span>
                    </a>
                </li>

                @if($eligibleList->isNotEmpty())
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ $currentTab === 'all' ? 'text-success border border-success border-opacity-25' : 'text-muted' }}" 
                       href="{{ $currentTab === 'all' ? '#panel-sekolah-siap-tagih' : route('invoice.index', array_merge(request()->except('tab'), ['tab' => 'all'])) . '#panel-sekolah-siap-tagih' }}" 
                       @if($currentTab === 'all') onclick="document.getElementById('panel-sekolah-siap-tagih')?.scrollIntoView({behavior: 'smooth'}); return false;" @endif
                       title="{{ $currentTab === 'all' ? 'Scroll ke Antrean Sekolah Siap Ditagih' : 'Buka Antrean Sekolah Siap Ditagih di Tab Semua' }}">
                        <i class="bi bi-lightning-charge-fill me-1 text-success"></i>Antrean Siap Tagih
                        <span class="badge bg-success text-white ms-1">{{ $eligibleList->count() }}</span>
                    </a>
                </li>
                @endif
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('invoice.index', array_merge(request()->query(), ['refresh_antrean' => 1])) }}" 
                   class="btn btn-sm btn-outline-primary rounded-pill px-2.5 shadow-xs" 
                   title="Segarkan / Recalculate Antrean Siap Tagih">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
                <a href="{{ route('admin.google-sheets.export', 'detail_invoice_marketing') }}" 
                   class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-xs" 
                   title="Unduh Detail Invoice Tab 13 (Pivot Ready Marketing) Format CSV">
                    <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i>Export CSV Tab 13 (Pivot)
                </a>
                <a href="{{ route('admin.google-sheets.export', 'invoice') }}" 
                   class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-xs" 
                   title="Unduh Rekap Invoice Tab 12 Format CSV">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV Tab 12
                </a>
                <a href="{{ route('admin.google-sheets.index') }}" 
                   class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-xs" 
                   title="Buka Menu Integrasi Master Google Sheets">
                    <i class="bi bi-google me-1"></i>Google Sheets
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="invoiceTable">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-semibold">No. Invoice</th>
                            <th class="py-3 text-muted small fw-semibold">Sekolah / Rombel</th>
                            <th class="py-3 text-muted small fw-semibold">Periode Tagihan</th>
                            <th class="py-3 text-muted small fw-semibold">Skema Tagihan</th>
                            <th class="py-3 text-muted small fw-semibold text-center">Siswa Billable</th>
                            <th class="py-3 text-muted small fw-semibold text-center">Admin Produksi (Gate 1)</th>
                            <th class="py-3 text-muted small fw-semibold text-center">Staff Akunting (Gate 2)</th>
                            <th class="py-3 text-muted small fw-semibold text-center">Status Invoice</th>
                            <th class="pe-4 py-3 text-muted small fw-semibold text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                        <tr>
                            <td class="ps-4">
                                <code class="small {{ str_starts_with($inv->nomor_invoice ?? '', 'DRAFT') ? 'text-secondary' : 'text-primary' }} fw-bold">{{ $inv->nomor_invoice }}</code>
                                @if(str_starts_with($inv->nomor_invoice ?? '', 'DRAFT'))
                                    <br><span class="badge bg-secondary-subtle text-secondary small" style="font-size:.65rem;">Nomor Draft</span>
                                @elseif($inv->hasPdf())
                                    <br><span class="badge bg-success-subtle text-success small" style="font-size:.65rem;"><i class="bi bi-file-pdf me-0.5"></i>PDF Siap</span>
                                @endif
                                <div class="text-muted" style="font-size:.68rem;">
                                    <i class="bi bi-calendar-plus me-1"></i>{{ $inv->created_at?->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold small text-dark">
                                    {{ $inv->sekolah?->namasekolah ?? $inv->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? '-' }}
                                </div>
                                <div class="text-muted" style="font-size: .75rem;">
                                    @if(($inv->total_rombel ?? 0) > 1)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-diagram-3 me-1"></i>{{ $inv->total_rombel }} Rombel</span>
                                    @elseif($inv->items->isNotEmpty())
                                        {{ $inv->items->first()?->rombel?->nama_rombel ?? '-' }}
                                    @else
                                        {{ $inv->rombel?->nama_rombel ?? '-' }}
                                    @endif
                                </div>
                                @php
                                    $sales = $inv->sales;
                                @endphp
                                @if($sales)
                                <div class="mt-1 d-flex align-items-center gap-1 text-secondary" style="font-size: .72rem;" title="Sales & Group Leader Marketing">
                                    <i class="bi bi-person-badge text-primary" style="font-size: .8rem;"></i>
                                    <span><span class="fw-medium text-dark">{{ $sales->nama_salesman }}</span> @if($sales->group_leader)<span class="text-muted small">({{ $sales->group_leader }})</span>@endif</span>
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-medium small">{{ $inv->periode_label }}</span>
                                <br><span class="text-muted" style="font-size:.72rem;">{{ $inv->tahun_ajaran }}</span>
                            </td>
                            <td>
                                @php
                                    $skemaColor = match($inv->skema_tagihan) {
                                        'bulanan'          => 'primary',
                                        'semester'         => 'purple',
                                        'tahunan'          => 'dark',
                                        'per_4_pertemuan'  => 'secondary',
                                        'csr_reguler_soga' => 'success',
                                        default            => 'secondary',
                                    };
                                    $skemaLabel = match($inv->skema_tagihan) {
                                        'bulanan'          => 'Bulanan',
                                        'semester'         => 'Semesteran',
                                        'tahunan'          => 'Tahunan',
                                        'per_4_pertemuan'  => 'Per 4 Pertemuan',
                                        'csr_reguler_soga' => 'CSR Reguler SOGA',
                                        default            => '-',
                                    };
                                @endphp
                                <span class="badge bg-{{ $skemaColor }}-subtle text-{{ $skemaColor }} border border-{{ $skemaColor }}-subtle rounded-pill px-2.5 py-1 small"
                                      @if($skemaColor === 'purple') style="background-color: rgba(124, 58, 237, 0.12) !important; color: #7c3aed !important; border-color: rgba(124, 58, 237, 0.25) !important;" @endif>
                                    {{ $skemaLabel }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $inv->hasKoreksi() ? 'bg-warning-subtle text-warning border border-warning' : 'bg-light text-dark border' }} fw-bold">
                                    {{ $inv->billable_efektif }} siswa
                                </span>
                                @if($inv->hasKoreksi())
                                    <div class="text-warning-emphasis" style="font-size:.68rem;">(koreksi)</div>
                                @endif
                                <div class="text-muted" style="font-size:.7rem;">{{ $inv->jumlah_sesi }} sesi</div>
                            </td>
                            <td class="text-center">
                                @if($inv->operasional_status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2.5">
                                        <i class="bi bi-check2-circle me-1"></i>{{ $inv->operasionalUser?->nama_lengkap ?? $inv->operasionalUser?->name ?? 'Disetujui' }}
                                    </span>
                                    <div class="text-muted" style="font-size:.68rem;">{{ $inv->operasional_approved_at?->format('d/m/Y H:i') }} WIB</div>
                                @elseif($inv->operasional_status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2.5">
                                        <i class="bi bi-x-circle me-1"></i>Ditolak
                                    </span>
                                    <div class="text-muted" style="font-size:.68rem;">{{ $inv->operasional_approved_at?->format('d/m/Y H:i') }} WIB</div>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-1 px-2.5" title="Menunggu verifikasi Admin Produksi">
                                        <i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi
                                    </span>
                                    <div class="text-muted" style="font-size:.68rem;">Antrean Produksi</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($inv->akunting_status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2.5">
                                        <i class="bi bi-check2-all me-1"></i>{{ $inv->akuntingUser?->nama_lengkap ?? $inv->akuntingUser?->name ?? 'Disetujui' }}
                                    </span>
                                    <div class="text-muted" style="font-size:.68rem;">{{ $inv->akunting_approved_at?->format('d/m/Y H:i') }} WIB</div>
                                @elseif($inv->akunting_status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2.5" title="Dikembalikan Staff Akunting untuk revisi">
                                        <i class="bi bi-arrow-return-left me-1"></i>Minta Revisi
                                    </span>
                                    <div class="text-danger fw-semibold" style="font-size:.68rem;">{{ $inv->akuntingUser?->nama_lengkap ?? $inv->akuntingUser?->name ?? 'Staff Akunting' }}</div>
                                @else
                                    @if($inv->operasional_status === 'approved')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle py-1 px-2.5" title="Menunggu persetujuan Staff Akunting">
                                            <i class="bi bi-hourglass-split me-1"></i>Siap Disetujui
                                        </span>
                                        <div class="text-primary-emphasis fw-semibold" style="font-size:.68rem;">Antrean Akunting</div>
                                    @else
                                        <span class="badge bg-light text-muted border py-1 px-2.5" style="font-size:.72rem;" title="Menunggu Gate 1 Produksi selesai">
                                            <i class="bi bi-pause-circle me-1"></i>Menunggu Produksi
                                        </span>
                                        <div class="text-muted" style="font-size:.68rem;">Antre Gate 1</div>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $inv->statusBadgeClass() }} rounded-pill px-2.5 py-1">
                                    {{ $inv->statusLabel() }}
                                </span>
                                @php
                                    $daysAging = (int) $inv->created_at->diffInDays(now());
                                    $isPending = in_array($inv->status, ['draft', 'pending_operasional', 'pending_akunting']);
                                @endphp
                                @if($isPending)
                                    @if($daysAging >= 3)
                                        <div class="mt-1">
                                            <span class="badge bg-danger text-white border border-danger shadow-xs py-1 px-2" style="font-size:.68rem;" title="Draft menunggu lebih dari 3 hari!">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $daysAging }} hr menggantung
                                            </span>
                                        </div>
                                    @elseif($daysAging >= 1)
                                        <div class="mt-1">
                                            <span class="badge bg-warning text-dark border border-warning-subtle py-1 px-2" style="font-size:.68rem;" title="Draft menunggu">
                                                <i class="bi bi-hourglass-split me-1"></i>{{ $daysAging }} hr menggantung
                                            </span>
                                        </div>
                                    @else
                                        <div class="mt-1">
                                            <span class="badge bg-light text-secondary border py-1 px-2" style="font-size:.68rem;" title="Dibuat dalam 24 jam terakhir">
                                                <i class="bi bi-clock-history me-1"></i>Hari ini (&lt;24 jam)
                                            </span>
                                        </div>
                                    @endif
                                @elseif($inv->isApproved())
                                    <div class="mt-1">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2" style="font-size:.68rem;">
                                            <i class="bi bi-patch-check-fill me-1"></i>Terbit Resmi
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('invoice.show', $inv) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($inv->isApproved())
                                <a href="{{ route('invoice.pdf', $inv) }}" class="btn btn-sm btn-success" target="_blank" title="Unduh PDF Resmi">
                                    <i class="bi bi-download"></i>
                                </a>
                                @else
                                <a href="{{ route('invoice.pdf', $inv) }}" class="btn btn-sm btn-outline-warning text-dark" target="_blank" title="Unduh PDF Draft (Konfirmasi PIC)">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                @if($currentTab === 'revisi' || request('status') === 'revisi')
                                    <span class="fw-semibold text-success"><i class="bi bi-check2-circle me-1"></i>Bagus! Tidak ada draft invoice yang perlu direvisi saat ini.</span>
                                @elseif($currentTab === 'pending_operasional')
                                    <span class="fw-semibold text-success"><i class="bi bi-check2-circle me-1"></i>Tidak ada antrean invoice di Meja Admin Produksi saat ini!</span>
                                @elseif($currentTab === 'pending_akunting')
                                    <span class="fw-semibold text-success"><i class="bi bi-check2-circle me-1"></i>Tidak ada antrean invoice di Meja Staff Akunting saat ini!</span>
                                @elseif(in_array($currentTab, ['pending', 'gantung']))
                                    <span class="fw-semibold text-success"><i class="bi bi-check2-circle me-1"></i>Tidak ada draft invoice yang menunggu approval saat ini!</span>
                                @elseif($currentTab === 'approved')
                                    Belum ada invoice yang berstatus Disetujui Resmi.
                                @else
                                    Belum ada invoice. <a href="{{ route('invoice.create') }}">Buat invoice pertama</a>.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($invoices->hasPages())
            <div class="d-flex justify-content-center py-3 border-top">
                {{ $invoices->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

@endsection

@push('modals')
{{-- Modal Atur Skema Tagihan Sekolah --}}
@if(auth()->user()?->hasRole(['admin', 'admin_sistem', 'webmaster']))
<div class="modal fade" id="modalAturSkema" tabindex="-1" aria-labelledby="modalAturSkemaLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formAturSkema" method="POST" action="">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="modalAturSkemaLabel">
                        <i class="bi bi-calendar-check me-2 text-primary"></i>Atur Skema Tagihan Sekolah
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="modalSekolahSelect" class="form-label fw-semibold">Pilih Sekolah</label>
                        <select id="modalSekolahSelect" class="form-select" onchange="onModalSekolahChange(this)">
                            <option value="">-- Pilih Sekolah --</option>
                            @foreach($sekolahs ?? [] as $s)
                                <option value="{{ $s->kodlan }}" data-skema="{{ $s->skema_tagihan ?? 'per_4_pertemuan' }}" data-nama="{{ $s->namasekolah }}">
                                    [{{ $s->kodlan }}] {{ $s->namasekolah }} ({{ $s->skemaTagihanLabel() ?? 'Per 4 Pertemuan' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modalSkemaSelect" class="form-label fw-semibold">Skema Tagihan Invoice</label>
                        <select name="skema_tagihan" id="modalSkemaSelect" class="form-select" required>
                            <option value="per_4_pertemuan">Per 4 Pertemuan (Default Rolling Batch)</option>
                            <option value="bulanan">Bulanan (Setiap Akhir Bulan)</option>
                            <option value="semester">Per Semester (Jul–Des / Jan–Jun)</option>
                            <option value="tahunan">Tahunan (Per Tahun Ajaran)</option>
                            <option value="csr_reguler_soga">CSR Reguler SOGA (Solidaritas Erlangga)</option>
                        </select>
                        <small class="text-muted mt-1 d-block">
                            Perubahan skema akan langsung memperbarui aturan kemunculan invoice di antrean penagihan.
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSimpanSkema" class="btn btn-primary rounded-pill px-4 fw-bold" disabled>
                        <i class="bi bi-check2-circle me-1"></i> Simpan Skema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endpush

@push('scripts')
<script>
function filterEligibleSchools() {
    const q = (document.getElementById('searchEligibleTable')?.value || '').toLowerCase().trim();
    const skema = (document.getElementById('filterEligibleSkema')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#eligibleTableBody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const rowSkema = (row.getAttribute('data-skema') || '').toLowerCase();
        const matchesQuery = !q || text.includes(q);
        const matchesSkema = !skema || rowSkema === skema;
        row.style.display = (matchesQuery && matchesSkema) ? '' : 'none';
    });
}

function quickEditSkema(kodlan, nama, currentSkema) {
    const select = document.getElementById('modalSekolahSelect');
    if (select) {
        select.value = kodlan;
        onModalSekolahChange(select);
    }
    const skemaSelect = document.getElementById('modalSkemaSelect');
    if (skemaSelect) {
        skemaSelect.value = currentSkema || 'per_4_pertemuan';
    }
    const modalEl = document.getElementById('modalAturSkema');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function onModalSekolahChange(selectElem) {
    const kodlan = selectElem.value;
    const form = document.getElementById('formAturSkema');
    const submitBtn = document.getElementById('btnSimpanSkema');
    const skemaSelect = document.getElementById('modalSkemaSelect');
    
    if (kodlan) {
        form.action = `/sekolah/${kodlan}/skema-tagihan`;
        if (submitBtn) submitBtn.disabled = false;
        const selectedOption = selectElem.options[selectElem.selectedIndex];
        const skema = selectedOption.getAttribute('data-skema');
        if (skema && skemaSelect) {
            skemaSelect.value = skema;
        }
    } else {
        if (form) form.action = '';
        if (submitBtn) submitBtn.disabled = true;
    }
}
</script>
@endpush

