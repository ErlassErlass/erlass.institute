@extends('layouts.app')

@section('title', 'Distribusi Jadwal Instruktur')

@push('styles')
<style>
    :root {
        --sd-navy: #0f172a;
        --sd-slate: #334155;
        --sd-muted: #64748b;
        --sd-line: #e2e8f0;
        --sd-bg: #f8fafc;
        --sd-blue: #2563eb;
        --sd-blue-light: #eff6ff;
        --sd-amber: #d97706;
        --sd-amber-light: #fffbeb;
        --sd-green: #16a34a;
        --sd-green-light: #f0fdf4;
        --sd-red: #dc2626;
        --sd-red-light: #fef2f2;
        --sd-radius: 14px;
    }

    /* Hero header */
    .sd-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #1d4ed8 100%);
        color: #fff;
        border-radius: var(--sd-radius);
        padding: 2.25rem 2rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.12);
    }
    .sd-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 60% 80% at 100% 0%, rgba(59, 130, 246, 0.25) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Filter card */
    .sd-filter-card {
        background: #fff;
        border: 1px solid var(--sd-line);
        border-radius: var(--sd-radius);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }

    .sd-period-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .9rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 600;
        color: var(--sd-slate);
        background: var(--sd-bg);
        border: 1px solid var(--sd-line);
        text-decoration: none;
        transition: all .15s ease;
    }
    .sd-period-pill:hover {
        background: var(--sd-blue-light);
        color: var(--sd-blue);
        border-color: #bfdbfe;
    }
    .sd-period-pill.active {
        background: var(--sd-blue);
        color: #fff;
        border-color: var(--sd-blue);
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }

    /* KPI Cards */
    .sd-kpi-card {
        background: #fff;
        border: 1px solid var(--sd-line);
        border-radius: var(--sd-radius);
        padding: 1.25rem 1.5rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .sd-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
    }
    .sd-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* Table styles */
    .sd-table-card {
        background: #fff;
        border: 1px solid var(--sd-line);
        border-radius: var(--sd-radius);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .sd-table th {
        background: #f8fafc;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--sd-muted);
        padding: .85rem 1.25rem;
        border-bottom: 1px solid var(--sd-line);
    }
    .sd-table td {
        padding: .95rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: .875rem;
        color: var(--sd-slate);
    }
    .sd-table tr:last-child td {
        border-bottom: none;
    }
    .sd-table tr:hover td {
        background: #f8fafc;
    }

    /* Instructor avatar */
    .sd-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #fff;
        font-weight: 700;
        font-size: .85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Freeze / Sticky Columns for Availability Table */
    .sd-sticky-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .sd-sticky-table th.sd-col-no,
    .sd-sticky-table td.sd-col-no {
        position: sticky;
        left: 0;
        z-index: 10;
        width: 48px;
        min-width: 48px;
        max-width: 48px;
        background-color: #ffffff;
    }
    .sd-sticky-table th.sd-col-name,
    .sd-sticky-table td.sd-col-name {
        position: sticky;
        left: 48px;
        z-index: 10;
        width: 220px;
        min-width: 200px;
        max-width: 250px;
        background-color: #ffffff;
    }
    .sd-sticky-table th.sd-col-domisili,
    .sd-sticky-table td.sd-col-domisili {
        position: sticky;
        left: 268px; /* 48px + 220px */
        z-index: 10;
        width: 140px;
        min-width: 130px;
        background-color: #ffffff;
        border-right: 2px solid #cbd5e1 !important;
        box-shadow: 4px 0 8px -2px rgba(15, 23, 42, 0.08);
    }
    .sd-sticky-table thead th.sd-col-no,
    .sd-sticky-table thead th.sd-col-name,
    .sd-sticky-table thead th.sd-col-domisili {
        z-index: 25;
        background-color: #f8fafc;
    }
    .sd-sticky-table tr.avail-row:hover td.sd-col-no,
    .sd-sticky-table tr.avail-row:hover td.sd-col-name,
    .sd-sticky-table tr.avail-row:hover td.sd-col-domisili {
        background-color: #f8fafc !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" style="max-width: 1240px;">

    {{-- ═══ HERO HEADER ═══ --}}
    <div class="sd-hero">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill small fw-bold mb-3"
                     style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff; letter-spacing: .02em;">
                    <i class="bi bi-bar-chart-steps text-info"></i> Analytics &amp; Penjadwalan
                </div>
                <h1 class="h2 fw-bold text-white mb-2" style="letter-spacing: -.02em;">Distribusi Jadwal Instruktur</h1>
                <p class="mb-0" style="color: rgba(255, 255, 255, 0.85); font-size: .95rem; line-height: 1.6;">
                    Analisis pemerataan beban mengajar &amp; alokasi sesi mengajar seluruh instruktur Erlass Institute.
                </p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-inline-block text-start p-3 rounded-3"
                     style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(8px);">
                    <small class="d-block fw-semibold text-uppercase" style="color: rgba(255, 255, 255, 0.75); font-size: .7rem; letter-spacing: .05em;">Periode Terpilih</small>
                    <div class="fw-bold text-white fs-6 mt-1 me-2">
                        <i class="bi bi-calendar-event me-1 text-info"></i> {{ $period_label }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ FILTER & PERIODE TOOLBAR ═══ --}}
    <div class="sd-filter-card">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            
            {{-- Period Mode Quick Pills --}}
            <div>
                <label class="form-label small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing: .04em;">
                    <i class="bi bi-funnel-fill me-1 text-primary"></i> Pilih Periode Tampilan:
                </label>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.analytics.schedule-distribution', ['period_mode' => 'honor_current']) }}" 
                       class="sd-period-pill {{ $period_mode === 'honor_current' ? 'active' : '' }}">
                       <i class="bi bi-clock-history"></i> Honor Berjalan
                    </a>
                    <a href="{{ route('admin.analytics.schedule-distribution', ['period_mode' => 'honor_prev']) }}" 
                       class="sd-period-pill {{ $period_mode === 'honor_prev' ? 'active' : '' }}">
                       <i class="bi bi-arrow-left-circle"></i> Periode Lalu
                    </a>
                    <a href="{{ route('admin.analytics.schedule-distribution', ['period_mode' => 'honor_prev2']) }}" 
                       class="sd-period-pill {{ $period_mode === 'honor_prev2' ? 'active' : '' }}">
                       <i class="bi bi-rewind"></i> 2 Bulan Lalu
                    </a>
                    <a href="{{ route('admin.analytics.schedule-distribution', ['period_mode' => 'all']) }}" 
                       class="sd-period-pill {{ $period_mode === 'all' ? 'active' : '' }}">
                       <i class="bi bi-infinity"></i> Seluruh Waktu (All Time)
                    </a>
                    <button type="button" class="sd-period-pill {{ in_array($period_mode, ['month', 'custom']) ? 'active' : '' }}" 
                            data-bs-toggle="collapse" data-bs-target="#customFilterCollapse">
                       <i class="bi bi-sliders"></i> Filter Custom...
                    </button>
                </div>
            </div>

            {{-- Actions: Export Excel --}}
            <div class="d-flex align-items-end">
                <a href="{{ route('admin.analytics.schedule-distribution.export', array_merge(request()->query(), ['period_mode' => $period_mode])) }}" 
                   class="btn btn-success fw-bold px-3 py-2 rounded-3 shadow-sm text-nowrap" style="font-size: .875rem;">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </a>
            </div>

        </div>

        {{-- Custom Filter Collapse (Month/Year & Custom Range) --}}
        <div class="collapse {{ in_array($period_mode, ['month', 'custom']) ? 'show' : '' }} mt-3 pt-3 border-top" id="customFilterCollapse">
            <form action="{{ route('admin.analytics.schedule-distribution') }}" method="GET" class="row g-3 align-items-end">
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-dark">Mode Filter</label>
                    <select name="period_mode" class="form-select form-select-sm" id="periodModeSelect" onchange="toggleFilterFields(this.value)">
                        <option value="honor_current" {{ $period_mode === 'honor_current' ? 'selected' : '' }}>Honor Berjalan (Siklus 11-10)</option>
                        <option value="honor_prev" {{ $period_mode === 'honor_prev' ? 'selected' : '' }}>Honor Periode Lalu (Siklus 11-10)</option>
                        <option value="honor_prev2" {{ $period_mode === 'honor_prev2' ? 'selected' : '' }}>Honor 2 Bulan Lalu</option>
                        <option value="month" {{ $period_mode === 'month' ? 'selected' : '' }}>Bulan &amp; Tahun Spesifik</option>
                        <option value="custom" {{ $period_mode === 'custom' ? 'selected' : '' }}>Rentang Tanggal Custom</option>
                        <option value="all" {{ $period_mode === 'all' ? 'selected' : '' }}>Seluruh Waktu (All Time)</option>
                    </select>
                </div>

                <div class="col-md-2 filter-field-month {{ $period_mode === 'month' ? '' : 'd-none' }}">
                    <label class="form-label small fw-bold text-dark">Bulan</label>
                    <select name="month" class="form-select form-select-sm">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int)$selected_month === $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2 filter-field-month {{ $period_mode === 'month' ? '' : 'd-none' }}">
                    <label class="form-label small fw-bold text-dark">Tahun</label>
                    <select name="year" class="form-select form-select-sm">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ (int)$selected_year === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3 filter-field-custom {{ $period_mode === 'custom' ? '' : 'd-none' }}">
                    <label class="form-label small fw-bold text-dark">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $start_date }}">
                </div>

                <div class="col-md-3 filter-field-custom {{ $period_mode === 'custom' ? '' : 'd-none' }}">
                    <label class="form-label small fw-bold text-dark">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $end_date }}">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                        <i class="bi bi-search me-1"></i> Terapkan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ═══ TAB NAVIGATION ═══ --}}
    <ul class="nav nav-tabs mb-4 border-bottom" id="mainTabs" role="tablist" style="gap: .25rem;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold px-4 py-2" id="tab-distribusi" data-bs-toggle="tab"
                    data-bs-target="#pane-distribusi" type="button" role="tab" aria-selected="true">
                <i class="bi bi-bar-chart-steps me-1"></i> Distribusi Sesi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold px-4 py-2" id="tab-ketersediaan" data-bs-toggle="tab"
                    data-bs-target="#pane-ketersediaan" type="button" role="tab" aria-selected="false">
                <i class="bi bi-calendar2-week me-1"></i> Ketersediaan Mingguan
                <span class="badge rounded-pill ms-1" style="background:#2563eb;font-size:.7rem;">
                    {{ $availability_instructors->count() }}
                </span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="mainTabContent">

    {{-- ═══ TAB 1: DISTRIBUSI SESI ═══ --}}
    <div class="tab-pane fade show active" id="pane-distribusi" role="tabpanel">

    {{-- ═══ KPI SUMMARY CARDS ═══ --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-3">
            <div class="sd-kpi-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="sd-kpi-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold text-muted">Total Sesi Ditugaskan</div>
                        <div class="fs-4 fw-bold text-dark" style="letter-spacing: -.02em;">
                            {{ $instructors->sum('ekstrakurikuler_sessions_count') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sd-kpi-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="sd-kpi-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="bi bi-calculator-fill"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold text-muted">Rata-rata Sesi / Instruktur</div>
                        <div class="fs-4 fw-bold text-dark" style="letter-spacing: -.02em;">
                            {{ $average_sessions }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sd-kpi-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="sd-kpi-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold text-muted">Instruktur Aktif Mengajar</div>
                        <div class="fs-4 fw-bold text-dark" style="letter-spacing: -.02em;">
                            {{ $instructors->where('ekstrakurikuler_sessions_count', '>', 0)->count() }}
                            <span class="small fw-normal text-muted" style="font-size: .75rem;">/ {{ $instructors->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sd-kpi-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="sd-kpi-icon" style="background: #fef2f2; color: #dc2626;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold text-muted">Perlu Penambahan Sesi</div>
                        <div class="fs-4 fw-bold text-danger" style="letter-spacing: -.02em;">
                            {{ count($recommended_instructors) }}
                            <span class="small fw-normal text-muted" style="font-size: .75rem;">instruktur</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══ CHART & RECOMMENDATIONS ROW ═══ --}}
    <div class="row g-4 mb-4">
        
        {{-- Chart Section --}}
        <div class="col-lg-8">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: .95rem;">
                        <i class="bi bi-bar-chart-line me-2 text-primary"></i>Grafik Distribusi Sesi Instruktur
                    </h5>
                    <span class="badge bg-light text-muted border px-2.5 py-1" style="font-size: .7rem;">
                        {{ count($chart_data['labels']) }} Instruktur Dihitung
                    </span>
                </div>
                <div class="card-body p-4">
                    @if(count($chart_data['labels']) > 0)
                        <canvas id="distributionChart" style="max-height: 320px;"></canvas>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-muted opacity-50"></i>
                            Tidak ada data sesi untuk periode ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recommendation Sidebar --}}
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-3 px-4">
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: .95rem;">
                        <i class="bi bi-lightbulb-fill me-2 text-amber-500" style="color: #d97706;"></i>Rekomendasi Penambahan Sesi
                    </h5>
                </div>
                <div class="card-body p-3 overflow-auto" style="max-height: 360px;">
                    @forelse($recommended_instructors->take(10) as $rec)
                        <div class="d-flex align-items-center justify-content-between p-2.5 mb-2 rounded-3 bg-light border">
                            <div class="d-flex align-items-center gap-2.5 overflow-hidden me-2">
                                <div class="sd-avatar" style="width: 32px; height: 32px; font-size: .75rem;">
                                    {{ strtoupper(substr($rec->nama_lengkap, 0, 2)) }}
                                </div>
                                <div class="text-truncate">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: .85rem;">{{ $rec->nama_lengkap }}</div>
                                    <small class="text-muted" style="font-size: .725rem;">{{ $rec->instructorProfile->kota_domisili ?? 'Domisili -' }}</small>
                                </div>
                            </div>
                            <span class="badge {{ $rec->ekstrakurikuler_sessions_count == 0 ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill px-2.5 py-1" style="font-size: .75rem;">
                                {{ $rec->ekstrakurikuler_sessions_count }} Sesi
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-1"></i>
                            Beban mengajar seluruh instruktur sudah merata!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- ═══ INSTRUCTORS DATA TABLE ═══ --}}
    <div class="sd-table-card">
        <div class="p-3 px-4 border-bottom bg-white d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark" style="font-size: 1rem;">
                    <i class="bi bi-people me-2 text-primary"></i>Daftar Seluruh Instruktur
                </h5>
                <small class="text-muted">Total {{ $instructors->count() }} instruktur terdaftar dalam sistem.</small>
            </div>
            <div style="max-width: 280px;">
                <input type="text" id="tableSearchInput" class="form-control form-control-sm" placeholder="Cari nama instruktur..." autocomplete="off">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table sd-table mb-0 align-middle" id="instructorsTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Instruktur</th>
                        <th>Kota Domisili</th>
                        <th>Kompetensi</th>
                        <th class="text-center" style="width: 140px;">Jumlah Sesi</th>
                        <th class="text-center" style="width: 150px;">Status Distribusi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($instructors as $index => $inst)
                        <tr class="instructor-row" data-name="{{ strtolower($inst->nama_lengkap) }}">
                            <td class="fw-bold text-muted small">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="sd-avatar">
                                        {{ strtoupper(substr($inst->nama_lengkap, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0.5">{{ $inst->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $inst->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal">
                                    <i class="bi bi-geo-alt me-1 text-secondary"></i>{{ $inst->instructorProfile->kota_domisili ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-secondary d-block">
                                    {{ $inst->instructorProfile->kompetensi_1 ?? 'Umum' }}
                                    @if(!empty($inst->instructorProfile->kompetensi_2))
                                        , {{ $inst->instructorProfile->kompetensi_2 }}
                                    @endif
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $inst->ekstrakurikuler_sessions_count > 0 ? 'bg-primary' : 'bg-secondary' }} px-3 py-1.5 rounded-pill fs-6 fw-bold">
                                    {{ $inst->ekstrakurikuler_sessions_count }} Sesi
                                </span>
                            </td>
                            <td class="text-center">
                                @if($inst->ekstrakurikuler_sessions_count == 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                                        <i class="bi bi-x-circle me-1"></i> Belum ada Sesi
                                    </span>
                                @elseif($inst->ekstrakurikuler_sessions_count < $average_sessions)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">
                                        <i class="bi bi-dash-circle me-1"></i> Dibawah Rata2
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                        <i class="bi bi-check-circle me-1"></i> Optimal
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Tidak ada data instruktur ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>{{-- /sd-table-card --}}

    </div>{{-- /tab-pane #pane-distribusi --}}

    {{-- ═══ TAB 2: KETERSEDIAAN MINGGUAN ═══ --}}
    <div class="tab-pane fade" id="pane-ketersediaan" role="tabpanel">

        {{-- Toolbar baris 1: week picker --}}
        <div class="card border-0 shadow-sm rounded-3 mb-3 p-3" style="background:#f8fafc;">
            <div class="d-flex flex-wrap align-items-end gap-3">

                {{-- Week Picker --}}
                <div>
                    <label class="form-label small fw-bold text-muted mb-1" for="weekPicker">
                        <i class="bi bi-calendar-week me-1 text-primary"></i> Pilih Minggu
                    </label>
                    <input type="week" id="weekPicker" class="form-control form-control-sm"
                           style="min-width:180px;"
                           value="{{ now()->format('Y') }}-W{{ now()->format('W') }}">
                </div>

                {{-- Load Button --}}
                <div>
                    <button id="loadWeekBtn" class="btn btn-primary btn-sm fw-bold px-4" style="height:32px;">
                        <i class="bi bi-search me-1"></i> Cek Ketersediaan
                    </button>
                </div>

                {{-- Week label result --}}
                <div id="weekLabelDisplay" class="d-none align-items-center gap-2 ms-1">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2" style="font-size:.82rem;">
                        <i class="bi bi-calendar-check me-1"></i>
                        <span id="weekLabelText"></span>
                    </span>
                </div>

                <div class="ms-auto d-flex align-items-center gap-3 flex-wrap">
                    {{-- Filter Kota --}}
                    <div>
                        <label class="form-label small fw-bold text-muted mb-1" for="kotaFilter">
                            <i class="bi bi-geo-alt-fill me-1 text-secondary"></i> Kota
                        </label>
                        <select id="kotaFilter" class="form-select form-select-sm" style="min-width:160px;">
                            <option value="">— Semua Kota —</option>
                            @foreach($kota_list as $kota)
                                <option value="{{ strtolower($kota) }}">{{ $kota }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Cari nama --}}
                    <div>
                        <label class="form-label small fw-bold text-muted mb-1">
                            <i class="bi bi-search me-1"></i> Cari
                        </label>
                        <input type="text" id="availSearchInput" class="form-control form-control-sm"
                               placeholder="Nama instruktur..." style="min-width:170px;">
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="d-flex align-items-center gap-3 flex-wrap mt-2 pt-2 border-top">
                <span class="small text-muted fw-semibold">Keterangan:</span>
                <span class="d-inline-flex align-items-center gap-1">
                    <span style="width:13px;height:13px;border-radius:3px;background:#dcfce7;border:1px solid #86efac;display:inline-block;"></span>
                    <span class="small text-muted">🟢 Free (belum ada sesi)</span>
                </span>
                <span class="d-inline-flex align-items-center gap-1">
                    <span style="width:13px;height:13px;border-radius:3px;background:#fef3c7;border:1px solid #fcd34d;display:inline-block;"></span>
                    <span class="small text-muted">🟡 Sebagian Terisi</span>
                </span>
                <span class="d-inline-flex align-items-center gap-1">
                    <span style="width:13px;height:13px;border-radius:3px;background:#fee2e2;border:1px solid #fca5a5;display:inline-block;"></span>
                    <span class="small text-muted">🔴 Penuh / Busy</span>
                </span>
                <span class="d-inline-flex align-items-center gap-1">
                    <span style="width:13px;height:13px;border-radius:3px;background:#f1f5f9;border:1px solid #cbd5e1;display:inline-block;"></span>
                    <span class="small text-muted">⬜ Tidak Tersedia</span>
                </span>
                <span class="d-inline-flex align-items-center gap-1">
                    <span style="width:13px;height:13px;border-radius:3px;background:#fef3c7;border:1px solid #fcd34d;display:inline-block;opacity:.6;"></span>
                    <span class="small text-muted">— Belum Isi Jadwal</span>
                </span>
                <span id="loadingBadge" class="d-none ms-2">
                    <span class="spinner-border spinner-border-sm text-primary me-1" style="width:.85rem;height:.85rem;"></span>
                    <span class="small text-muted">Memuat data minggu ini...</span>
                </span>
            </div>
        </div>

        {{-- Availability Matrix Table --}}
        <div class="sd-table-card">
            <div class="p-3 px-4 border-bottom bg-white d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 fw-bold text-dark" style="font-size:1rem;">
                        <i class="bi bi-calendar2-week me-2 text-primary"></i>Ketersediaan Instruktur per Hari (Mingguan)
                    </h5>
                    <small class="text-muted" id="tableSubtitle">Pilih minggu di atas lalu klik "Cek Ketersediaan" untuk melihat slot aktual vs jadwal terjadwal.</small>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1" style="font-size:.8rem;">
                    {{ $availability_instructors->count() }} Instruktur
                </span>
            </div>
            <div class="table-responsive">
                <table class="table sd-table sd-sticky-table mb-0 align-middle" id="availabilityTable">
                    <thead>
                        <tr>
                            <th class="sd-col-no" style="width:48px; min-width:48px;">No</th>
                            <th class="sd-col-name" style="min-width:200px; width:220px;">Instruktur</th>
                            <th class="sd-col-domisili" style="min-width:130px; width:140px;">Domisili</th>
                            <th class="text-center day-header" data-day="Senin" style="min-width:115px;">Senin</th>
                            <th class="text-center day-header" data-day="Selasa" style="min-width:115px;">Selasa</th>
                            <th class="text-center day-header" data-day="Rabu" style="min-width:115px;">Rabu</th>
                            <th class="text-center day-header" data-day="Kamis" style="min-width:115px;">Kamis</th>
                            <th class="text-center day-header" data-day="Jumat" style="min-width:115px;">Jumat</th>
                            <th class="text-center day-header" data-day="Sabtu" style="min-width:115px;">Sabtu</th>
                            <th class="text-center" style="min-width:90px;">Sesi Bln Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($availability_instructors as $idx => $instr)
                            @php
                                $hasSchedule = !empty($instr->instructorProfile?->waktu_mengajar);
                                $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                            @endphp
                            <tr class="avail-row"
                                data-name="{{ strtolower($instr->nama_lengkap) }}"
                                data-kota="{{ strtolower($instr->kota_domisili) }}"
                                data-instr-id="{{ $instr->id }}">
                                <td class="fw-bold text-muted small sd-col-no">{{ $idx + 1 }}</td>
                                <td class="sd-col-name">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="sd-avatar" style="width:32px;height:32px;font-size:.75rem;flex-shrink:0;">
                                            {{ strtoupper(substr($instr->nama_lengkap, 0, 2)) }}
                                        </div>
                                        <div style="min-width:0;">
                                            <div class="fw-bold text-dark text-truncate" style="font-size:.875rem; max-width:160px;" title="{{ $instr->nama_lengkap }}">
                                                {{ $instr->nama_lengkap }}
                                            </div>
                                            <small class="text-muted text-truncate d-block" style="font-size:.725rem; max-width:160px;" title="{{ $instr->email }}">{{ $instr->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="sd-col-domisili">
                                    @if($instr->kota_domisili)
                                        <span class="badge bg-light text-dark border fw-normal text-truncate d-inline-block" style="max-width:120px;" title="{{ $instr->kota_domisili }}">
                                            <i class="bi bi-geo-alt me-1 text-secondary"></i>{{ $instr->kota_domisili }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                @if(!$hasSchedule)
                                    {{-- No waktu_mengajar: span all 6 day columns --}}
                                    <td colspan="6" class="text-center" style="background:#fffbeb;">
                                        <span class="badge fw-normal" style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;font-size:.75rem;">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Belum mengisi jadwal ketersediaan
                                        </span>
                                    </td>
                                @else
                                    @foreach($days as $day)
                                        @php $range = $instr->availability_by_day[$day] ?? null; @endphp
                                        <td class="text-center p-1" style="{{ $range ? 'background:#f0fdf4;' : 'background:#f8fafc;' }}">
                                            @if($range)
                                                <span class="badge fw-normal d-inline-block"
                                                      style="background:#dcfce7;color:#166534;border:1px solid #86efac;font-size:.72rem;line-height:1.4;white-space:normal;">
                                                    {{ $range }}
                                                </span>
                                            @else
                                                <span class="text-muted" style="font-size:.8rem;">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                                <td class="text-center">
                                    <span class="badge {{ $instr->sesi_aktif_bulan_ini > 0 ? 'bg-primary' : 'bg-secondary' }} rounded-pill px-2 py-1">
                                        {{ $instr->sesi_aktif_bulan_ini }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    Tidak ada data instruktur.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /tab-pane #pane-ketersediaan --}}

    </div>{{-- /tab-content --}}

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dynamic Filter Fields Toggle
    function toggleFilterFields(mode) {
        document.querySelectorAll('.filter-field-month').forEach(el => el.classList.toggle('d-none', mode !== 'month'));
        document.querySelectorAll('.filter-field-custom').forEach(el => el.classList.toggle('d-none', mode !== 'custom'));
    }

    // Live Table Search (Tab 1)
    document.getElementById('tableSearchInput')?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('.instructor-row').forEach(row => {
            const name = row.dataset.name || '';
            row.style.display = name.includes(query) ? '' : 'none';
        });
    });

    // Tab 2: Kota filter
    document.getElementById('kotaFilter')?.addEventListener('change', function() {
        applyAvailabilityFilters();
    });

    // Tab 2: Name search
    document.getElementById('availSearchInput')?.addEventListener('input', function() {
        applyAvailabilityFilters();
    });

    function applyAvailabilityFilters() {
        const kota  = (document.getElementById('kotaFilter')?.value  || '').toLowerCase().trim();
        const query = (document.getElementById('availSearchInput')?.value || '').toLowerCase().trim();
        document.querySelectorAll('.avail-row').forEach(row => {
            const rowKota = row.dataset.kota || '';
            const rowName = row.dataset.name || '';
            const kotaOk  = !kota  || rowKota === kota;
            const nameOk  = !query || rowName.includes(query);
            row.style.display = (kotaOk && nameOk) ? '' : 'none';
        });
    }

    // ─── Week Picker AJAX Load ───────────────────────────────────────────────
    const DAYS = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    // Helper: render a single cell based on day data from AJAX
    function renderDayCell(dayData) {
        if (!dayData) {
            // Static mode (no week loaded yet)
            return null;
        }

        const { status, available, sessions } = dayData;

        const bgColor = {
            free:        '#f0fdf4',
            partial:     '#fffbeb',
            busy:        '#fef2f2',
            unavailable: '#f8fafc',
            no_data:     '#fffbeb',
        }[status] || '#f8fafc';

        const badgeStyle = {
            free:        'background:#dcfce7;color:#166534;border:1px solid #86efac;',
            partial:     'background:#fef3c7;color:#92400e;border:1px solid #fcd34d;',
            busy:        'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;',
            unavailable: 'background:#f1f5f9;color:#94a3b8;border:1px solid #cbd5e1;',
            no_data:     'background:#fef3c7;color:#92400e;border:1px solid #fcd34d;opacity:.7;',
        }[status] || '';

        const icon = {
            free: '🟢', partial: '🟡', busy: '🔴', unavailable: '⬜', no_data: '⚠️',
        }[status] || '';

        const label = {
            free: 'Free',
            partial: 'Sebagian',
            busy: 'Penuh',
            unavailable: 'Libur',
            no_data: 'Blm isi',
        }[status] || status;

        let html = `<div style="background:${bgColor};border-radius:6px;padding:4px;min-height:38px;">`;
        html += `<span class="badge fw-semibold d-block mb-1" style="${badgeStyle}font-size:.7rem;">${icon} ${label}</span>`;

        if (available && status !== 'unavailable' && status !== 'no_data') {
            html += `<div style="font-size:.68rem;color:#64748b;margin-bottom:2px;">${available}</div>`;
        }

        if (sessions && sessions.length > 0) {
            sessions.forEach(s => {
                const schoolInfo = s.school && s.school !== '—' ? ` @ ${s.school}` : '';
                html += `<div title="${s.time} ${s.ekskul}${schoolInfo}" style="font-size:.68rem;color:#1e293b;background:#ffffff;border-radius:4px;padding:3px 5px;margin-top:3px;border-left:2.5px solid #f59e0b;box-shadow:0 1px 2px rgba(0,0,0,0.05);line-height:1.25;text-align:left;">`;
                html += `<div style="font-weight:700;color:#0f172a;">${s.time}</div>`;
                html += `<div style="font-size:.65rem;color:#334155;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${s.ekskul}</div>`;
                if (s.school && s.school !== '—') {
                    html += `<div style="font-size:.62rem;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${s.school}</div>`;
                }
                html += `</div>`;
            });
        }

        html += '</div>';
        return html;
    }

    document.getElementById('loadWeekBtn')?.addEventListener('click', function() {
        const weekVal = document.getElementById('weekPicker')?.value;
        if (!weekVal) { alert('Pilih minggu terlebih dahulu'); return; }

        const btn     = this;
        const loading = document.getElementById('loadingBadge');
        const label   = document.getElementById('weekLabelDisplay');

        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memuat...';
        loading?.classList.remove('d-none');

        fetch(`{{ route('admin.analytics.availability-check') }}?week=${weekVal}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }

            // Update header labels with actual dates
            const monday = new Date(data.monday_date + 'T00:00:00');
            document.querySelectorAll('.day-header').forEach((th, idx) => {
                const d = new Date(monday);
                d.setDate(monday.getDate() + idx);
                const dd = String(d.getDate()).padStart(2,'0');
                const mm = String(d.getMonth()+1).padStart(2,'0');
                th.innerHTML = `${DAYS[idx]}<br><small style="font-weight:400;font-size:.68rem;color:#64748b;">${dd}/${mm}</small>`;
            });

            // Update table subtitle
            const subtitle = document.getElementById('tableSubtitle');
            if (subtitle) subtitle.textContent = `Jadwal aktual minggu ${data.week_label} vs ketersediaan instruktur.`;

            // Update week label badge
            const labelText = document.getElementById('weekLabelText');
            if (labelText) labelText.textContent = data.week_label;
            label?.classList.remove('d-none');
            label?.classList.add('d-flex');

            // Update each avail-row cells
            document.querySelectorAll('.avail-row').forEach(row => {
                const instrId = row.dataset.instrId;
                const instrData = data.availability[instrId];
                if (!instrData) return;

                DAYS.forEach((day, idx) => {
                    // day cell index: 3 = Senin, 4=Selasa, ... (after No, Nama, Domisili cols)
                    const cellIdx = 3 + idx;
                    const cell = row.cells[cellIdx];
                    if (!cell) return;

                    // Handle colspan=6 (no waktu_mengajar)
                    if (row.querySelector('td[colspan="6"]')) return;

                    const rendered = renderDayCell(instrData[day]);
                    if (rendered !== null) {
                        cell.innerHTML = rendered;
                        cell.style.padding = '4px';
                        cell.style.verticalAlign = 'top';
                    }
                });
            });
        })
        .catch(err => {
            console.error(err);
            alert('Gagal memuat data. Cek koneksi atau coba lagi.');
        })
        .finally(() => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-search me-1"></i> Cek Ketersediaan';
            loading?.classList.add('d-none');
        });
    });


    // Chart JS Initialization
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('distributionChart');
        if (ctx) {
            const chartLabels = @json($chart_data['labels'] ?? []);
            const chartValues = @json($chart_data['data'] ?? []);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Jumlah Sesi',
                        data: chartValues,
                        backgroundColor: 'rgba(37, 99, 235, 0.85)',
                        borderColor: '#1d4ed8',
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: '#1d4ed8'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' Total: ' + context.parsed.y + ' Sesi';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, precision: 0 },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
