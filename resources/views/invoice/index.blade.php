@extends('layouts.app')

@section('title', 'Invoice Management — Erlass')

@push('styles')
<!-- Select2 CSS & Bootstrap 5 Theme -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* Styling Select2 Filter Bar agar serasi dengan form-select-sm */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.375rem !important;
        border-color: #dee2e6 !important;
        font-size: 0.875rem !important;
        min-height: 31px !important;
        height: 31px !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #212529 !important;
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
        padding-left: 0.5rem !important;
        padding-right: 1.5rem !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18) !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 10px !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12) !important;
        overflow: hidden !important;
        z-index: 1060 !important;
        font-size: 0.875rem !important;
    }
    .select2-container--bootstrap-5 .select2-search__field {
        border-radius: 6px !important;
        font-size: 0.85rem !important;
        padding: 6px 10px !important;
        border-color: #cbd5e1 !important;
    }
    .select2-container--bootstrap-5 .select2-search__field:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb !important;
    }
    .select2-container--bootstrap-5 .select2-selection__clear {
        margin-right: 0.5rem !important;
        font-size: 1.1rem !important;
        color: #94a3b8 !important;
    }
    .select2-container--bootstrap-5 .select2-selection__clear:hover {
        color: #ef4444 !important;
    }
</style>
@endpush

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

    {{-- ─── HEADER MINIMALIS ──────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-receipt text-primary"></i> Manajemen Invoice Tagihan
            </h4>
        </div>
        <div>
            <a href="{{ route('invoice.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Buat Invoice Baru
            </a>
        </div>
    </div>

    {{-- ─── FILTER BAR ─────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: .75rem;">
        <div class="card-body p-3">
            @if($currentTab === 'benchmark')
            <form id="filter-form" method="GET" action="{{ route('invoice.index') }}"
                  class="row g-2 align-items-end">
                <input type="hidden" name="tab" id="filter-tab" value="benchmark">

                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Sekolah</label>
                    <select name="sekolah" id="filter-sekolah" class="form-select form-select-sm select2-sekolah" data-placeholder="🔍 Cari & pilih sekolah...">
                        <option value="">Semua Sekolah</option>
                        @foreach($sekolahs as $s)
                            <option value="{{ $s->kodlan }}" {{ request('sekolah') === $s->kodlan ? 'selected' : '' }}>
                                [{{ $s->kodlan }}] {{ $s->namasekolah }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Wilayah / Kota</label>
                    <select name="wilayah" class="form-select form-select-sm">
                        <option value="">Semua Wilayah</option>
                        @foreach($allWilayah ?? [] as $w)
                            <option value="{{ $w }}" {{ request('wilayah') === $w ? 'selected' : '' }}>{{ $w }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Salesman</label>
                    <select name="salesman" class="form-select form-select-sm">
                        <option value="">Semua Salesman</option>
                        @foreach($allSalesmen ?? [] as $sm)
                            <option value="{{ $sm->id }}" {{ request('salesman') == $sm->id ? 'selected' : '' }}>{{ $sm->nama_salesman }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Skema</label>
                    <select name="skema" class="form-select form-select-sm">
                        <option value="">Semua Skema</option>
                        <option value="per_4_pertemuan"  {{ request('skema') === 'per_4_pertemuan'  ? 'selected' : '' }}>🔄 Per 4 Pertemuan (Min. 8x)</option>
                        <option value="bulanan"          {{ request('skema') === 'bulanan'          ? 'selected' : '' }}>🗓️ Bulanan (≤ 8x)</option>
                        <option value="semester"         {{ request('skema') === 'semester'         ? 'selected' : '' }}>📚 Semesteran (2x)</option>
                        <option value="tahunan"          {{ request('skema') === 'tahunan'          ? 'selected' : '' }}>🎓 Tahunan (1x)</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                    <select name="status_benchmark" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="tercapai"  {{ request('status_benchmark') === 'tercapai'  ? 'selected' : '' }}>Tercapai</option>
                        <option value="on_track"  {{ request('status_benchmark') === 'on_track'  ? 'selected' : '' }}>On-Track</option>
                        <option value="belum_ada" {{ request('status_benchmark') === 'belum_ada' ? 'selected' : '' }}>Belum Ada</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('invoice.index', ['tab' => 'benchmark']) }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
            @else
            <form id="filter-form" method="GET" action="{{ route('invoice.index') }}"
                  class="row g-2 align-items-end">
                <input type="hidden" name="tab" id="filter-tab" value="{{ $currentTab }}">

                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Sekolah</label>
                    <select name="sekolah" id="filter-sekolah" class="form-select form-select-sm select2-sekolah" data-placeholder="🔍 Cari & pilih sekolah...">
                        <option value="">Semua Sekolah</option>
                        @foreach($sekolahs as $s)
                            <option value="{{ $s->kodlan }}" {{ request('sekolah') === $s->kodlan ? 'selected' : '' }}>
                                [{{ $s->kodlan }}] {{ $s->namasekolah }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Wilayah / Kota</label>
                    <select name="wilayah" class="form-select form-select-sm">
                        <option value="">Semua Wilayah</option>
                        @foreach($allWilayah ?? [] as $w)
                            <option value="{{ $w }}" {{ request('wilayah') === $w ? 'selected' : '' }}>{{ $w }}</option>
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
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" value="{{ request('tahun_ajaran', '2026/2027') }}"
                           class="form-control form-control-sm" placeholder="2026/2027">
                </div>

                <div class="col-md-1">
                    <label class="form-label small fw-semibold text-muted mb-1">Tampilkan</label>
                    <select name="per_page" class="form-select form-select-sm" onchange="document.getElementById('filter-form').submit();">
                        <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                        <option value="75" {{ ($perPage ?? 25) == 75 ? 'selected' : '' }}>75</option>
                        <option value="all" {{ request('per_page') === 'all' || ($perPage ?? 25) >= 9999 ? 'selected' : '' }}>Semua</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('invoice.index', ['tab' => $currentTab]) }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
            @endif
        </div>
    </div>

    {{-- ─── 5 WORKFLOW TABS & CONTENT ────────────────────────────────────── --}}
    @php
        $eligibleList = $eligibleSekolahs ?? $eligibleRombels ?? collect();
        $badgeSiapTagih = $eligibleTotalCount ?? $eligibleList->count();
    @endphp
    <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
        <div class="card-header bg-white border-bottom py-2.5 px-3">
            <ul class="nav nav-pills card-header-pills gap-1 flex-wrap">
                {{-- Tab 1: Antrean Siap Tagih --}}
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'siap_tagih' ? 'active bg-success text-white fw-bold shadow-xs' : 'text-dark' }} py-1.5 px-3" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab'), ['tab' => 'siap_tagih'])) }}">
                        <i class="bi bi-lightning-charge-fill me-1 {{ $currentTab === 'siap_tagih' ? 'text-white' : 'text-success' }}"></i>Antrean Siap Tagih
                        <span class="badge {{ $currentTab === 'siap_tagih' ? 'bg-white text-success' : 'bg-success text-white' }} ms-1">
                            {{ $badgeSiapTagih }}
                        </span>
                    </a>
                </li>

                {{-- Tab 2: Menunggu Admin Produksi --}}
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'pending_operasional' ? 'active bg-warning text-dark fw-bold shadow-xs' : 'text-dark' }} py-1.5 px-3 position-relative" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab'), ['tab' => 'pending_operasional'])) }}">
                        <i class="bi bi-hourglass-split me-1 text-warning-emphasis"></i>Menunggu Admin Produksi
                        <span class="badge {{ $currentTab === 'pending_operasional' ? 'bg-dark text-warning' : 'bg-warning-subtle text-warning-emphasis border border-warning' }} ms-1">
                            {{ $operasionalPendingCount }}
                        </span>
                        @if($operasionalPendingCount > 0 && $currentTab !== 'pending_operasional')
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Perlu review produksi</span>
                            </span>
                        @endif
                    </a>
                </li>

                {{-- Tab 3: Menunggu Revisi --}}
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'revisi' ? 'active bg-danger text-white fw-bold shadow-xs' : 'text-dark' }} py-1.5 px-3 position-relative" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab'), ['tab' => 'revisi'])) }}">
                        <i class="bi bi-arrow-return-left me-1 {{ $currentTab === 'revisi' ? 'text-white' : 'text-danger' }}"></i>Menunggu Revisi
                        <span class="badge {{ $currentTab === 'revisi' ? 'bg-white text-danger' : 'bg-danger-subtle text-danger border border-danger' }} ms-1">
                            {{ $revisiCount }}
                        </span>
                        @if($revisiCount > 0 && $currentTab !== 'revisi')
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Perlu revisi invoice</span>
                            </span>
                        @endif
                    </a>
                </li>

                {{-- Tab 4: Menunggu Staff Akunting --}}
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'pending_akunting' ? 'active bg-primary text-white fw-bold shadow-xs' : 'text-dark' }} py-1.5 px-3 position-relative" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab'), ['tab' => 'pending_akunting'])) }}">
                        <i class="bi bi-shield-check me-1"></i>Menunggu Staff Akunting
                        <span class="badge {{ $currentTab === 'pending_akunting' ? 'bg-white text-primary' : 'bg-primary-subtle text-primary border border-primary' }} ms-1">
                            {{ $akuntingPendingCount }}
                        </span>
                        @if($akuntingPendingCount > 0 && $currentTab !== 'pending_akunting')
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-primary border border-light rounded-circle">
                                <span class="visually-hidden">Perlu approval akunting</span>
                            </span>
                        @endif
                    </a>
                </li>

                {{-- Tab 5: Disetujui Resmi --}}
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'approved' ? 'active bg-success text-white fw-bold shadow-xs' : 'text-dark' }} py-1.5 px-3" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab'), ['tab' => 'approved'])) }}">
                        <i class="bi bi-patch-check-fill me-1 {{ $currentTab === 'approved' ? 'text-white' : 'text-success' }}"></i>Disetujui Resmi
                        <span class="badge {{ $currentTab === 'approved' ? 'bg-white text-success' : 'bg-success-subtle text-success border border-success-subtle' }} ms-1">
                            {{ $approvedCount }}
                        </span>
                    </a>
                </li>

                {{-- Tab 6: Monitoring Benchmark --}}
                <li class="nav-item">
                    <a class="nav-link {{ $currentTab === 'benchmark' ? 'active bg-dark text-white fw-bold shadow-xs' : 'text-dark' }} py-1.5 px-3" 
                       href="{{ route('invoice.index', array_merge(request()->except('page', 'tab'), ['tab' => 'benchmark'])) }}">
                        <i class="bi bi-speedometer2 me-1 {{ $currentTab === 'benchmark' ? 'text-warning' : 'text-primary' }}"></i>Monitoring Benchmark
                        <span class="badge {{ $currentTab === 'benchmark' ? 'bg-warning text-dark' : 'bg-secondary-subtle text-dark border' }} ms-1">
                            🎯 Target
                        </span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            @if($currentTab === 'siap_tagih')
                {{-- ─── TAB 1 CONTENT: SEKOLAH SIAP DITAGIHKAN ───────────────────── --}}
                <div id="panel-sekolah-siap-tagih">
                    <div class="p-3 bg-light bg-opacity-50 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
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
                            <select id="filterEligibleSkema" class="form-select form-select-sm bg-white border" style="width: 180px;" onchange="filterEligibleSchools()">
                                <option value="">Semua Skema</option>
                                <option value="bulanan">🗓️ Bulanan</option>
                                <option value="per_4_pertemuan">🔄 Per 4 Pertemuan</option>
                                <option value="semester">📚 Semesteran</option>
                                <option value="tahunan">🎓 Tahunan</option>
                            </select>
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted small"></i></span>
                                <input type="text" id="searchEligibleTable" class="form-control bg-white border-start-0" 
                                       placeholder="Cari sekolah..." onkeyup="filterEligibleSchools()">
                            </div>
                            <a href="{{ route('invoice.index', array_merge(request()->query(), ['tab' => 'siap_tagih', 'refresh_antrean' => 1])) }}" 
                               class="btn btn-sm btn-outline-secondary fw-semibold px-2.5 py-1.5 rounded-pill shadow-xs text-nowrap" 
                               title="Segarkan antrean data sesi dan program terbaru">
                                <i class="bi bi-arrow-clockwise me-1 text-primary"></i> Segarkan Antrean
                            </a>
                            @if(auth()->user()?->hasRole(['admin', 'admin_sistem', 'webmaster']))
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1.5 rounded-pill shadow-xs text-nowrap" data-bs-toggle="modal" data-bs-target="#modalAturSkema">
                                <i class="bi bi-gear-fill me-1"></i> Atur Skema Tagihan
                            </button>
                            @endif
                            @if($eligibleList->isNotEmpty())
                            <form action="{{ route('invoice.bulk-generate') }}" method="POST" onsubmit="return confirm('Generate invoice untuk semua {{ $eligibleList->count() }} sekolah yang siap ditagihkan?');" class="mb-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success fw-bold px-3 py-1.5 rounded-pill shadow-xs text-nowrap">
                                    <i class="bi bi-lightning-charge-fill me-1"></i> Generate Semua (Bulk)
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    @if($eligibleList->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-uppercase text-muted" style="font-size: .75rem; letter-spacing: .5px;">
                                    <th class="ps-4">Sekolah</th>
                                    <th>Wilayah</th>
                                    <th>Rombel & Program (Item)</th>
                                    <th>Skema Tagihan</th>
                                    <th>Periode Tagihan</th>
                                    <th class="text-center">Jumlah Pertemuan</th>
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
                                        @php
                                            $wilayahNama = $item['sekolah_kota'] ?? $item['sekolah']?->kota ?? null;
                                        @endphp
                                        @if($wilayahNama)
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $wilayahNama }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small">
                                            <i class="bi bi-mortarboard-fill me-1 text-primary"></i>{{ $item['kategori_program'] ?? 'Program Ekskul' }}
                                        </div>
                                        <div class="text-muted small mt-0.5" style="font-size: .75rem;">
                                            <i class="bi bi-diagram-3 me-1"></i>{{ $item['total_rombel'] }} Rombel: 
                                            @php
                                                $displayItems = array_slice($item['items'] ?? [], 0, 4);
                                                $remainingCount = count($item['items'] ?? []) - count($displayItems);
                                            @endphp
                                            @foreach($displayItems as $it)
                                                <span class="badge bg-light text-muted border font-monospace me-1" style="font-size: .68rem;">
                                                    {{ $it['rombel_nama'] }} ({{ $it['jumlah_siswa_billable'] }} sw)
                                                </span>
                                            @endforeach
                                            @if($remainingCount > 0)
                                                <span class="badge bg-secondary-subtle text-secondary border font-monospace me-1" style="font-size: .68rem;" title="{{ implode(', ', array_map(fn($r) => $r['rombel_nama'] . ' (' . $r['jumlah_siswa_billable'] . ' sw)', array_slice($item['items'] ?? [], 4))) }}">
                                                    +{{ $remainingCount }} rombel lainnya
                                                </span>
                                            @endif
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
                                            <button type="button" 
                                                    class="btn btn-link p-0 text-muted btn-sm btn-quick-skema" 
                                                    title="Ubah Skema Tagihan Sekolah"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalAturSkema"
                                                    data-kodlan="{{ $item['sekolah_kodlan'] }}"
                                                    data-nama="{{ $item['sekolah_nama'] }}"
                                                    data-skema="{{ $item['skema_tagihan'] }}"
                                                    onclick="quickEditSkema('{{ $item['sekolah_kodlan'] }}', '{{ addslashes($item['sekolah_nama']) }}', '{{ $item['skema_tagihan'] }}')">
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
                                        @php
                                            $totSesi = $item['total_jumlah_sesi'] ?? (!empty($item['items']) ? array_sum(array_column($item['items'], 'jumlah_sesi')) : null);
                                            $sDari = $item['sesi_dari'] ?? null;
                                            $sSampai = $item['sesi_sampai'] ?? null;
                                        @endphp
                                        @if($totSesi)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">
                                                {{ $totSesi }} Sesi
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border">-</span>
                                        @endif
                                        @if($sDari && $sSampai)
                                            <div class="text-muted mt-0.5" style="font-size: .70rem;">
                                                P.{{ $sDari }}–{{ $sSampai }}
                                            </div>
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

                    @if($eligibleSekolahs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <small class="text-muted">
                                Menampilkan {{ $eligibleSekolahs->firstItem() ?? 1 }} - {{ $eligibleSekolahs->lastItem() ?? $eligibleSekolahs->total() }} dari {{ $eligibleSekolahs->total() }} sekolah siap ditagih
                            </small>
                            <div class="d-flex align-items-center gap-1 ms-2">
                                <span class="small text-muted">Per halaman:</span>
                                <select class="form-select form-select-sm py-0 px-2" style="width: auto; font-size: 0.75rem;" onchange="location.href = this.value;">
                                    <option value="{{ route('invoice.index', array_merge(request()->query(), ['tab' => $currentTab, 'per_page' => 25, 'page' => 1])) }}" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="{{ route('invoice.index', array_merge(request()->query(), ['tab' => $currentTab, 'per_page' => 50, 'page' => 1])) }}" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="{{ route('invoice.index', array_merge(request()->query(), ['tab' => $currentTab, 'per_page' => 75, 'page' => 1])) }}" {{ ($perPage ?? 25) == 75 ? 'selected' : '' }}>75</option>
                                    <option value="{{ route('invoice.index', array_merge(request()->query(), ['tab' => $currentTab, 'per_page' => 'all', 'page' => 1])) }}" {{ request('per_page') === 'all' || ($perPage ?? 25) >= 9999 ? 'selected' : '' }}>Semua</option>
                                </select>
                            </div>
                        </div>
                        @if($eligibleSekolahs->hasPages())
                        <div>
                            {{ $eligibleSekolahs->appends(request()->query())->links() }}
                        </div>
                        @endif
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5">
                        <div class="avatar-lg bg-success-subtle text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="bi bi-check2-all fs-2"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tidak Ada Antrean Sekolah Siap Ditagih</h6>
                        <p class="text-muted small mb-0">Semua sekolah dan rombel telah diterbitkan invoice atau belum mencapai target sesi tagihan.</p>
                    </div>
                    @endif
                </div>
            @elseif($currentTab === 'benchmark')
                {{-- ─── TAB 6 CONTENT: MONITORING BENCHMARK PENAGIHAN ───────────────── --}}
                <div id="panel-benchmark-penagihan">
                    <!-- 4 KPI CARDS -->
                    <div class="p-3 bg-light bg-opacity-75 border-bottom">
                        <div class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <div class="card border-0 shadow-xs h-100 bg-white" style="border-radius: 10px; border-left: 4px solid #3b82f6 !important;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-bold text-muted text-uppercase">Per 4 Pertemuan</span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">Target ≥ 8x</span>
                                        </div>
                                        <h4 class="fw-bold mb-0 text-dark">{{ $benchmarkStats['by_skema']['per_4_pertemuan']['count'] ?? 0 }} <span class="fs-6 fw-normal text-muted">Sekolah</span></h4>
                                        <small class="text-muted">Target minimal 8x tagihan (32 sesi)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card border-0 shadow-xs h-100 bg-white" style="border-radius: 10px; border-left: 4px solid #10b981 !important;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-bold text-muted text-uppercase">Bulanan</span>
                                            <span class="badge bg-success bg-opacity-10 text-success">Target ≤ 8x</span>
                                        </div>
                                        <h4 class="fw-bold mb-0 text-dark">{{ $benchmarkStats['by_skema']['bulanan']['count'] ?? 0 }} <span class="fs-6 fw-normal text-muted">Sekolah</span></h4>
                                        <small class="text-muted">Tagihan rutin kalender (maks. 8 bulan)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card border-0 shadow-xs h-100 bg-white" style="border-radius: 10px; border-left: 4px solid #8b5cf6 !important;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-bold text-muted text-uppercase">Semesteran</span>
                                            <span class="badge bg-purple bg-opacity-10 text-purple">Target 2x</span>
                                        </div>
                                        <h4 class="fw-bold mb-0 text-dark">{{ $benchmarkStats['by_skema']['semester']['count'] ?? 0 }} <span class="fs-6 fw-normal text-muted">Sekolah</span></h4>
                                        <small class="text-muted">Semester 1 (Ganjil) & Semester 2 (Genap)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card border-0 shadow-xs h-100 bg-white" style="border-radius: 10px; border-left: 4px solid #f59e0b !important;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-bold text-muted text-uppercase">Tahunan</span>
                                            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis">Target 1x</span>
                                        </div>
                                        <h4 class="fw-bold mb-0 text-dark">{{ $benchmarkStats['by_skema']['tahunan']['count'] ?? 0 }} <span class="fs-6 fw-normal text-muted">Sekolah</span></h4>
                                        <small class="text-muted">1 Invoice Kontrak Penuh 1 Tahun Ajaran</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUMMARY PROGRESS INFO -->
                    <div class="px-4 py-2 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <span class="small text-muted">
                                Total Mitra Terdaftar: <strong class="text-dark">{{ $benchmarkStats['total_sekolah'] }}</strong> Sekolah
                            </span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">
                                <i class="bi bi-patch-check-fill me-1"></i>{{ $benchmarkStats['total_invoices_approved'] }} Invoice Resmi Tercetak
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i>{{ $benchmarkStats['tercapai'] }} Tercapai Penuh</span>
                            <span class="badge bg-primary text-white px-2 py-1"><i class="bi bi-arrow-repeat me-1"></i>{{ $benchmarkStats['on_track'] }} On-Track</span>
                            <span class="badge bg-secondary text-white px-2 py-1"><i class="bi bi-hourglass me-1"></i>{{ $benchmarkStats['belum_ada'] }} Belum Ada Invoice</span>
                        </div>
                    </div>

                    <!-- BENCHMARK TABLE -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="benchmarkTable">
                            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-semibold" width="50">#</th>
                                    <th class="py-3 text-muted small fw-semibold">Nama Sekolah & Kodlan</th>
                                    <th class="py-3 text-muted small fw-semibold">Wilayah</th>
                                    <th class="py-3 text-muted small fw-semibold">Salesman / Penanggung Jawab</th>
                                    <th class="py-3 text-muted small fw-semibold">Skema Tagihan</th>
                                    <th class="py-3 text-muted small fw-semibold text-center">Target Benchmark</th>
                                    <th class="py-3 text-muted small fw-semibold text-center" style="min-width: 180px;">Invoice & Periode Tertagih</th>
                                    <th class="py-3 text-muted small fw-semibold" style="width: 180px;">Progress Capaian</th>
                                    <th class="py-3 text-muted small fw-semibold text-center">Status</th>
                                    <th class="pe-4 py-3 text-muted small fw-semibold text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($benchmarkPaginator && $benchmarkPaginator->count() > 0)
                                    @foreach($benchmarkPaginator as $idx => $b)
                                        <tr>
                                            <td class="ps-4 text-muted small fw-medium">
                                                {{ ($benchmarkPaginator->currentPage() - 1) * $benchmarkPaginator->perPage() + $idx + 1 }}
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $b['namasekolah'] }}</div>
                                                <code class="small text-muted">{{ $b['kodlan'] }}</code>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $b['wilayah'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark small">
                                                    <i class="bi bi-person-badge text-primary me-1"></i>{{ $b['salesman'] }}
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeBg = match($b['skema']) {
                                                        'per_4_pertemuan' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                                        'bulanan' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                                        'semester' => 'bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25',
                                                        'tahunan' => 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25',
                                                        default => 'bg-secondary bg-opacity-10 text-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeBg }} px-2 py-1">
                                                    {{ $b['skema_label'] }}
                                                </span>
                                            </td>
                                            <td class="text-center fw-bold text-muted">
                                                {{ $b['skema'] === 'per_4_pertemuan' ? '≥ ' . $b['target'] . 'x' : ($b['skema'] === 'bulanan' ? '≤ ' . $b['target'] . 'x' : $b['target'] . 'x') }}
                                            </td>
                                            <td class="text-center" style="min-width: 180px;">
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 fw-bold">
                                                    {{ $b['realisasi'] }} Invoice Resmi
                                                </span>
                                                @if(!empty($b['approved_periods']) || !empty($b['pending_periods']))
                                                    <div class="mt-1 d-flex flex-wrap gap-1 justify-content-center">
                                                        @foreach($b['approved_periods'] as $pLabel)
                                                            <span class="badge bg-success text-white py-0.5 px-1.5" style="font-size: .67rem;" title="Invoice resmi disetujui">
                                                                <i class="bi bi-check-circle-fill me-0.5"></i>{{ $pLabel }}
                                                            </span>
                                                        @endforeach
                                                        @foreach($b['pending_periods'] as $pLabel)
                                                            <span class="badge bg-warning text-dark border border-warning py-0.5 px-1.5" style="font-size: .67rem;" title="Sedang dalam antrean approval / draft">
                                                                <i class="bi bi-hourglass-split me-0.5"></i>{{ $pLabel }} (Proses)
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-muted mt-1" style="font-size: .70rem;">(Belum ada invoice)</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 7px; background-color: #e2e8f0; border-radius: 10px;">
                                                        <div class="progress-bar {{ $b['persentase'] >= 100 ? 'bg-success' : ($b['persentase'] > 0 ? 'bg-primary' : 'bg-secondary') }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $b['persentase'] }}%; border-radius: 10px;">
                                                        </div>
                                                    </div>
                                                    <small class="fw-bold text-muted" style="min-width: 40px; font-size: 0.75rem;">{{ $b['persentase'] }}%</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $b['badge_class'] }} px-2.5 py-1 rounded-pill small">
                                                    {{ $b['status_label'] }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('invoice.index', ['tab' => 'approved', 'sekolah' => $b['kodlan']]) }}" 
                                                   class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1"
                                                   title="Lihat riwayat invoice disetujui resmi untuk sekolah ini">
                                                    <i class="bi bi-search me-1"></i>Lihat Invoice
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="bi bi-speedometer2 fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                            Tidak ada data benchmark yang cocok dengan filter yang dipilih.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- BENCHMARK PAGINATION -->
                    @if($benchmarkPaginator && $benchmarkPaginator->hasPages())
                        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">
                                Menampilkan {{ $benchmarkPaginator->firstItem() ?? 1 }} - {{ $benchmarkPaginator->lastItem() ?? $benchmarkPaginator->total() }} dari {{ $benchmarkPaginator->total() }} sekolah mitra
                            </small>
                            <div>
                                {{ $benchmarkPaginator->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            @else
                {{-- ─── TABS 2-5 CONTENT: TABEL DAFTAR INVOICE ───────────────────── --}}
                @if($currentTab === 'pending_operasional')
                <div class="p-2.5 px-3 bg-warning bg-opacity-10 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark rounded-circle p-1.5 d-flex align-items-center justify-content-center" style="width:28px; height:28px;">
                            <i class="bi bi-hourglass-split fs-6"></i>
                        </span>
                        <div>
                            <span class="fw-bold text-dark small">Antrean Menunggu Admin Produksi</span>
                            <span class="badge bg-dark text-warning ms-1" style="font-size: .7rem;"><i class="bi bi-sort-numeric-down me-1"></i>Prioritas: Tertua / Tertinggal Jauh</span>
                            <div class="text-muted" style="font-size: .75rem;">
                                Menampilkan invoice yang paling lama dibuat / tertinggal di baris teratas agar segera diproses dan diselesaikan.
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted small me-1">Urutan:</span>
                        <a href="{{ route('invoice.index', array_merge(request()->except('sort', 'page'), ['tab' => 'pending_operasional', 'sort' => 'asc'])) }}" 
                           class="btn btn-xs btn-sm {{ request('sort', 'asc') === 'asc' ? 'btn-warning fw-bold text-dark shadow-xs' : 'btn-outline-secondary bg-white' }}"
                           title="Urutkan dari yang paling lama dibuat (prioritas)">
                            <i class="bi bi-sort-numeric-down me-1"></i>Paling Lama (Prioritas)
                        </a>
                        <a href="{{ route('invoice.index', array_merge(request()->except('sort', 'page'), ['tab' => 'pending_operasional', 'sort' => 'desc'])) }}" 
                           class="btn btn-xs btn-sm {{ request('sort') === 'desc' ? 'btn-warning fw-bold text-dark shadow-xs' : 'btn-outline-secondary bg-white' }}"
                           title="Urutkan dari yang paling baru dibuat">
                            <i class="bi bi-sort-numeric-up-alt me-1"></i>Paling Baru
                        </a>
                    </div>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="invoiceTable">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-semibold">
                                No. Invoice
                                @if($currentTab === 'pending_operasional')
                                    <span class="text-warning-emphasis small ms-1" title="Urutan prioritas: terlama di atas"><i class="bi bi-sort-up"></i></span>
                                @endif
                            </th>
                            <th class="py-3 text-muted small fw-semibold">Sekolah / Rombel</th>
                            <th class="py-3 text-muted small fw-semibold">Wilayah</th>
                            <th class="py-3 text-muted small fw-semibold">Periode Tagihan</th>
                            <th class="py-3 text-muted small fw-semibold">Skema Tagihan</th>
                            <th class="py-3 text-muted small fw-semibold text-center">Jumlah Pertemuan</th>
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
                                    @if($currentTab === 'pending_operasional' && $inv->created_at)
                                        <span class="badge bg-warning-subtle text-warning-emphasis ms-1" style="font-size:.62rem;" title="{{ $inv->created_at->diffForHumans() }}">
                                            <i class="bi bi-clock-history me-0.5"></i>{{ $inv->created_at->diffForHumans(null, true) }} lalu
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold small text-dark">
                                    {{ $inv->sekolah?->namasekolah ?? $inv->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? '-' }}
                                </div>
                                @php
                                    // Ambil kategori_program dari items atau rombel langsung
                                    $kategoriProgram = $inv->items->isNotEmpty()
                                        ? ($inv->items->first()?->rombel?->ekstrakurikuler?->kategori_program ?? null)
                                        : ($inv->rombel?->ekstrakurikuler?->kategori_program ?? null);
                                    // Warna badge berdasarkan jenis program
                                    $progColor = 'info';
                                    if ($kategoriProgram) {
                                        $kp = strtolower($kategoriProgram);
                                        if (str_contains($kp, 'coding') || str_contains($kp, 'pictoblox')) $progColor = 'indigo';
                                        elseif (str_contains($kp, 'robotik')) $progColor = 'orange';
                                        elseif (str_contains($kp, 'english')) $progColor = 'teal';
                                        elseif (str_contains($kp, 'soga')) $progColor = 'success';
                                        else $progColor = 'info';
                                    }
                                @endphp
                                @if($kategoriProgram)
                                <div class="mt-0.5 mb-0.5">
                                    <span class="badge rounded-pill px-2 py-1"
                                          style="font-size:.65rem; background:rgba(99,102,241,.1); color:#4f46e5; border:1px solid rgba(99,102,241,.25);">
                                        <i class="bi bi-mortarboard-fill me-1"></i>{{ $kategoriProgram }}
                                    </span>
                                </div>
                                @endif
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
                                @php
                                    $kotaInv = $inv->sekolah?->kota ?? $inv->rombel?->ekstrakurikuler?->sekolah?->kota ?? null;
                                @endphp
                                @if($kotaInv)
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $kotaInv }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
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
                                @php
                                    $sesiCount = $inv->jumlah_sesi ?: (($inv->sesi_sampai && $inv->sesi_dari) ? ($inv->sesi_sampai - $inv->sesi_dari + 1) : null);
                                @endphp
                                @if($sesiCount)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">
                                        {{ $sesiCount }} Sesi
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">-</span>
                                @endif
                                @if($inv->sesi_dari && $inv->sesi_sampai)
                                    <div class="text-muted mt-0.5" style="font-size: .70rem;">
                                        P.{{ $inv->sesi_dari }}–{{ $inv->sesi_sampai }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $inv->hasKoreksi() ? 'bg-warning-subtle text-warning border border-warning' : 'bg-light text-dark border' }} fw-bold">
                                    {{ $inv->billable_efektif }} siswa
                                </span>
                                @if($inv->hasKoreksi())
                                    <div class="text-warning-emphasis" style="font-size:.68rem;">(koreksi)</div>
                                @endif
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
                            <td colspan="11" class="text-center py-5 text-muted">
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
            <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $invoices->firstItem() ?? 0 }} - {{ $invoices->lastItem() ?? 0 }} dari {{ $invoices->total() }} invoice
                    </small>
                    <div class="d-flex align-items-center gap-1 ms-2">
                        <span class="small text-muted">Per halaman:</span>
                        <select class="form-select form-select-sm py-0 px-2" style="width: auto; font-size: 0.75rem;" onchange="location.href = this.value;">
                            <option value="{{ route('invoice.index', array_merge(request()->query(), ['tab' => $currentTab, 'per_page' => 25, 'page' => 1])) }}" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="{{ route('invoice.index', array_merge(request()->query(), ['tab' => $currentTab, 'per_page' => 50, 'page' => 1])) }}" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                            <option value="{{ route('invoice.index', array_merge(request()->query(), ['tab' => $currentTab, 'per_page' => 75, 'page' => 1])) }}" {{ ($perPage ?? 25) == 75 ? 'selected' : '' }}>75</option>
                            <option value="{{ route('invoice.index', array_merge(request()->query(), ['tab' => $currentTab, 'per_page' => 'all', 'page' => 1])) }}" {{ request('per_page') === 'all' || ($perPage ?? 25) >= 9999 ? 'selected' : '' }}>Semua</option>
                        </select>
                    </div>
                </div>
                @if($invoices->hasPages())
                <div>
                    {{ $invoices->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
            {{-- End Tabs 2-5 --}}
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
                            <option value="csr_reguler_soga">CSR Reguler SOGA (Solidaritas Erlangga - Bebas Tagihan / Non-Invoice)</option>
                        </select>
                        <small class="text-muted mt-1 d-block">
                            Perubahan skema akan langsung diterapkan. Sekolah dengan skema CSR SOGA tidak akan dimasukkan ke antrean penagihan invoice.
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
<!-- jQuery (Required for Select2) & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function initInvoiceFilterSelect2() {
    function setupSelect2() {
        if (typeof window.jQuery === 'undefined' || typeof window.$.fn.select2 === 'undefined') {
            setTimeout(setupSelect2, 50);
            return;
        }
        var $ = window.jQuery;
        
        $('#filter-sekolah').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '🔍 Cari & pilih sekolah...',
            allowClear: true,
            dropdownParent: $(document.body)
        });

        // Auto focus search field saat dropdown dibuka
        $(document).on('select2:open', function() {
            var searchField = document.querySelector('.select2-container--open .select2-search__field');
            if (searchField) {
                searchField.focus();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupSelect2);
    } else {
        setupSelect2();
    }
})();

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
    const form = document.getElementById('formAturSkema');
    const submitBtn = document.getElementById('btnSimpanSkema');
    const skemaSelect = document.getElementById('modalSkemaSelect');

    if (kodlan) {
        if (form) {
            form.action = `/sekolah/${kodlan}/skema-tagihan`;
        }
        if (submitBtn) {
            submitBtn.disabled = false;
        }

        if (select) {
            // Cek apakah opsi sekolah sudah ada di dropdown, jika belum ada, tambahkan secara dinamis
            let found = false;
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value == kodlan) {
                    found = true;
                    select.selectedIndex = i;
                    break;
                }
            }
            if (!found) {
                const opt = document.createElement('option');
                opt.value = kodlan;
                opt.textContent = `[${kodlan}] ${nama || 'Sekolah'}`;
                opt.setAttribute('data-skema', currentSkema || 'per_4_pertemuan');
                opt.setAttribute('data-nama', nama || '');
                select.appendChild(opt);
                select.value = kodlan;
            }
        }
    }

    if (skemaSelect) {
        skemaSelect.value = currentSkema || 'per_4_pertemuan';
    }

    // Buka modal secara aman (mendukung Bootstrap 5 via window.bootstrap, global bootstrap, atau jQuery fallback)
    const modalEl = document.getElementById('modalAturSkema');
    if (modalEl) {
        const bs = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
        if (bs && bs.Modal) {
            const modal = bs.Modal.getInstance(modalEl) || new bs.Modal(modalEl);
            modal.show();
        } else if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
            $(modalEl).modal('show');
        }
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
        if (selectElem.selectedIndex >= 0 && selectElem.options[selectElem.selectedIndex]) {
            const selectedOption = selectElem.options[selectElem.selectedIndex];
            const skema = selectedOption.getAttribute('data-skema');
            if (skema && skemaSelect) {
                skemaSelect.value = skema;
            }
        }
    } else {
        if (form) form.action = '';
        if (submitBtn) submitBtn.disabled = true;
    }
}

// Inisialisasi event listener agar modal membaca data-attributes tombol pemanggil
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('modalAturSkema');
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (btn && btn.getAttribute('data-kodlan')) {
                const kodlan = btn.getAttribute('data-kodlan');
                const nama = btn.getAttribute('data-nama');
                const skema = btn.getAttribute('data-skema');
                quickEditSkema(kodlan, nama, skema);
            }
        });
    }
});
</script>
@endpush

