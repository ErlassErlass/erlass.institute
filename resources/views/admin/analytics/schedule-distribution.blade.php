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

    /* Scroll container for frozen sticky table headers */
    .sd-table-scroll-container {
        max-height: 72vh;
        overflow-y: auto;
        overflow-x: auto;
        position: relative;
        border-radius: 0 0 var(--sd-radius) var(--sd-radius);
        contain: paint;
        will-change: scroll-position;
        -webkit-overflow-scrolling: touch;
    }

    /* Rendering performance optimization for long table rows */
    .instructor-row,
    .avail-row {
        content-visibility: auto;
        contain-intrinsic-size: 0 45px;
    }

    /* Freeze / Sticky Columns and Header for Availability Table */
    .sd-sticky-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .sd-sticky-table thead th {
        position: sticky;
        top: 0;
        z-index: 20;
        background-color: #f8fafc;
        border-bottom: 2px solid #cbd5e1 !important;
        box-shadow: 0 2px 4px rgba(15, 23, 42, 0.05);
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
    }
    /* Fixed Intersection (top-left cells fixed in both axes) */
    .sd-sticky-table thead th.sd-col-no {
        position: sticky;
        top: 0;
        left: 0;
        z-index: 35;
        background-color: #f8fafc;
    }
    .sd-sticky-table thead th.sd-col-name {
        position: sticky;
        top: 0;
        left: 48px;
        z-index: 35;
        background-color: #f8fafc;
    }
    .sd-sticky-table thead th.sd-col-domisili {
        position: sticky;
        top: 0;
        left: 268px;
        z-index: 35;
        background-color: #f8fafc;
        border-right: 2px solid #cbd5e1 !important;
        box-shadow: 4px 2px 8px -2px rgba(15, 23, 42, 0.1);
    }
    .sd-sticky-table tr.avail-row:hover td.sd-col-no,
    .sd-sticky-table tr.avail-row:hover td.sd-col-name,
    .sd-sticky-table tr.avail-row:hover td.sd-col-domisili {
        background-color: #f8fafc !important;
    }

    /* Instructors Table (Tab 1) Sticky Header */
    #instructorsTable thead th {
        position: sticky;
        top: 0;
        z-index: 20;
        background-color: #f8fafc;
        border-bottom: 2px solid #cbd5e1 !important;
        box-shadow: 0 2px 4px rgba(15, 23, 42, 0.05);
    }

    /* Availability Matrix Cell Styles (optimized lightweight classes) */
    .day-cell {
        padding: 4px !important;
        vertical-align: top;
    }
    .day-cell-content {
        border-radius: 6px;
        padding: 4px;
        min-height: 38px;
        background: #f8fafc;
        transition: background-color .15s ease;
    }
    .cell-free, .cell-free .day-cell-content { background-color: #f0fdf4 !important; }
    .cell-unavailable, .cell-unavailable .day-cell-content { background-color: #f8fafc !important; }
    .cell-partial, .cell-partial .day-cell-content { background-color: #fffbeb !important; }
    .cell-busy, .cell-busy .day-cell-content { background-color: #fef2f2 !important; }
    .cell-no_data, .cell-no_data .day-cell-content { background-color: #fffbeb !important; }

    .status-badge {
        display: block;
        margin-bottom: 2px;
        font-size: .7rem;
        font-weight: 600;
        padding: 2px 4px;
        border-radius: 4px;
    }
    .status-badge.status-free { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .status-badge.status-unavailable { background-color: #f1f5f9; color: #94a3b8; border: 1px solid #cbd5e1; }
    .status-badge.status-partial { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
    .status-badge.status-busy { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    .status-badge.status-no_data { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; opacity: .7; }

    .avail-range-text {
        font-size: .68rem;
        color: #64748b;
        margin-bottom: 2px;
        line-height: 1.25;
    }

    .session-item-card {
        font-size: .68rem;
        color: #1e293b;
        background: #ffffff;
        border-radius: 4px;
        padding: 3px 5px;
        margin-top: 3px;
        border-left: 2.5px solid #f59e0b;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        line-height: 1.25;
        text-align: left;
    }
    .session-item-card .session-time { font-weight: 700; color: #0f172a; }
    .session-item-card .session-ekskul { font-size: .65rem; color: #334155; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    /* Hide session cards when toggle is active & show clean green availability */
    .hide-session-cards .session-item-card {
        display: none !important;
    }
    .hide-session-cards .day-cell-content {
        min-height: auto !important;
    }
    .hide-session-cards .day-cell.cell-partial,
    .hide-session-cards .day-cell.cell-busy {
        background-color: #f0fdf4 !important;
    }
    .hide-session-cards .day-cell.cell-partial .day-cell-content,
    .hide-session-cards .day-cell.cell-busy .day-cell-content {
        background-color: #f0fdf4 !important;
    }
    .hide-session-cards .day-cell.cell-partial .status-badge,
    .hide-session-cards .day-cell.cell-busy .status-badge {
        background-color: #dcfce7 !important;
        color: #166534 !important;
        border-color: #86efac !important;
    }
    .hide-session-cards .day-cell.cell-partial .status-badge span,
    .hide-session-cards .day-cell.cell-busy .status-badge span {
        display: none !important;
    }
    .hide-session-cards .day-cell.cell-partial .status-badge::after,
    .hide-session-cards .day-cell.cell-busy .status-badge::after {
        content: "🟢 Free";
    }

    /* Highlight column for selected day */
    .day-col-highlight {
        background-color: #f0f7ff !important;
    }
    th.day-col-highlight {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        border-bottom: 2px solid #0284c7 !important;
    }
    th.day-col-expanded {
        min-width: 260px;
        font-size: .95rem;
    }
    .single-day-mode .day-cell-content {
        max-width: 500px;
        margin: 0 auto;
        padding: 4px;
    }
    .single-day-mode .status-badge {
        font-size: .78rem;
        padding: 3px 8px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .single-day-mode .avail-range-text {
        font-size: .75rem;
        margin-top: 2px;
    }
    .single-day-mode .session-item-card {
        padding: 6px 10px;
        font-size: .78rem;
        margin-top: 5px;
        border-radius: 6px;
        border-left: 3px solid #f59e0b;
    }
    .single-day-mode .session-item-card .session-time {
        font-size: .8rem;
    }
    .single-day-mode .session-item-card .session-ekskul {
        font-size: .76rem;
        white-space: normal;
    }
    .single-day-mode .session-item-card .session-school {
        font-size: .74rem;
        color: #64748b;
        margin-top: 2px;
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
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold px-4 py-2" id="tab-wilayah-sekolah" data-bs-toggle="tab"
                    data-bs-target="#pane-wilayah-sekolah" type="button" role="tab" aria-selected="false">
                <i class="bi bi-geo-alt-fill me-1"></i> Distribusi Wilayah & Sekolah
                <span class="badge rounded-pill ms-1" style="background:#16a34a;font-size:.7rem;">
                    {{ $school_distribution_data->count() }}
                </span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="mainTabContent">

    {{-- ═══ TAB 1: DISTRIBUSI SESI ═══ --}}
    <div class="tab-pane fade show active" id="pane-distribusi" role="tabpanel">

    {{-- ═══ FILTER & PERIODE TOOLBAR (Tab 1 Only) ═══ --}}
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

            {{-- Actions: Export Excel (Tab 1) --}}
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

        <div class="table-responsive sd-table-scroll-container">
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
                                    <div class="sd-avatar">{{ strtoupper(substr($inst->nama_lengkap, 0, 2)) }}</div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0.5">{{ $inst->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $inst->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal"><i class="bi bi-geo-alt me-1 text-secondary"></i>{{ $inst->instructorProfile->kota_domisili ?? '-' }}</span>
                            </td>
                            <td>
                                <small class="text-secondary d-block">{{ $inst->instructorProfile->kompetensi_1 ?? 'Umum' }}@if(!empty($inst->instructorProfile->kompetensi_2)), {{ $inst->instructorProfile->kompetensi_2 }}@endif</small>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $inst->ekstrakurikuler_sessions_count > 0 ? 'bg-primary' : 'bg-secondary' }} px-3 py-1.5 rounded-pill fs-6 fw-bold">{{ $inst->ekstrakurikuler_sessions_count }} Sesi</span>
                            </td>
                            <td class="text-center">
                                @if($inst->ekstrakurikuler_sessions_count == 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1"><i class="bi bi-x-circle me-1"></i> Belum ada Sesi</span>
                                @elseif($inst->ekstrakurikuler_sessions_count < $average_sessions)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1"><i class="bi bi-dash-circle me-1"></i> Dibawah Rata2</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1"><i class="bi bi-check-circle me-1"></i> Optimal</span>
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

            {{-- Filter Baris 2: Filter Hari, Status & Tampilan --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3 pt-3 border-top">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    {{-- Filter Hari --}}
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label small fw-bold text-muted mb-0 text-nowrap" for="dayFilter">
                            <i class="bi bi-calendar3 me-1 text-primary"></i> Filter Hari:
                        </label>
                        <select id="dayFilter" class="form-select form-select-sm" style="min-width:140px;">
                            <option value="">— Semua Hari —</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>

                    {{-- Filter Status Ketersediaan --}}
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label small fw-bold text-muted mb-0 text-nowrap" for="statusFilter">
                            <i class="bi bi-funnel me-1 text-primary"></i> Filter Status:
                        </label>
                        <select id="statusFilter" class="form-select form-select-sm" style="min-width:210px;">
                            <option value="">— Semua Status —</option>
                            <option value="partial">🟡 Hanya Ada Sesi (Kuning)</option>
                            <option value="free">🟢 Hanya Free / Tanpa Sesi (Hijau)</option>
                        </select>
                    </div>

                    {{-- Switch: Sembunyikan Detail Sesi (Kuning) --}}
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="hideJadwalSwitch" style="cursor:pointer;">
                        <label class="form-check-label small fw-bold text-dark text-nowrap" for="hideJadwalSwitch" style="cursor:pointer;" title="Sembunyikan detail sesi kuning dan tampilkan slot hijau saja">
                            🟡 Sembunyikan Detail Sesi (Tampilan Hijau Saja)
                        </label>
                    </div>
                </div>

                {{-- Counter & Reset --}}
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <span class="badge bg-light text-muted border px-2.5 py-1.5" style="font-size:.78rem;">
                        Menampilkan: <strong id="visibleInstructorsCount" class="text-primary">{{ $availability_instructors->count() }}</strong> / {{ $availability_instructors->count() }} instruktur
                    </span>
                    <button type="button" id="resetAvailFiltersBtn" class="btn btn-outline-secondary btn-sm py-1 px-2.5" style="font-size:.78rem;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </button>
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
            <div class="table-responsive sd-table-scroll-container">
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
                            <tr class="avail-row" data-name="{{ strtolower($instr->nama_lengkap) }}" data-kota="{{ strtolower($instr->kota_domisili) }}" data-instr-id="{{ $instr->id }}" @foreach($days as $day) data-status-{{ strtolower($day) }}="{{ !empty($instr->availability_by_day[$day]) ? 'free' : ($hasSchedule ? 'unavailable' : 'no_data') }}"@endforeach>
                                <td class="fw-bold text-muted small sd-col-no">{{ $idx + 1 }}</td>
                                <td class="sd-col-name">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="sd-avatar" style="width:32px;height:32px;font-size:.75rem;flex-shrink:0;">{{ strtoupper(substr($instr->nama_lengkap, 0, 2)) }}</div>
                                        <div style="min-width:0;">
                                            <div class="fw-bold text-dark text-truncate" style="font-size:.875rem;max-width:160px;" title="{{ $instr->nama_lengkap }}">{{ $instr->nama_lengkap }}</div>
                                            <small class="text-muted text-truncate d-block" style="font-size:.725rem;max-width:160px;" title="{{ $instr->email }}">{{ $instr->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="sd-col-domisili">
                                    @if($instr->kota_domisili)
                                        <span class="badge bg-light text-dark border fw-normal text-truncate d-inline-block" style="max-width:120px;" title="{{ $instr->kota_domisili }}"><i class="bi bi-geo-alt me-1 text-secondary"></i>{{ $instr->kota_domisili }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                @if(!$hasSchedule)
                                    <td colspan="6" class="text-center cell-no_data">
                                        <span class="badge status-badge status-no_data d-inline-block px-3 py-1"><i class="bi bi-exclamation-triangle me-1"></i> Belum mengisi jadwal ketersediaan</span>
                                    </td>
                                @else
                                    @foreach($days as $day)
                                        @php $range = $instr->availability_by_day[$day] ?? null; @endphp
                                        <td class="text-center day-cell {{ $range ? 'cell-free' : 'cell-unavailable' }}" data-day="{{ $day }}">
                                            <div class="day-cell-content">
                                                @if($range)<span class="badge status-badge status-free">🟢 Free</span><div class="avail-range-text">{{ $range }}</div>@else<span class="badge status-badge status-unavailable">⬜ Libur</span>@endif
                                            </div>
                                        </td>
                                    @endforeach
                                @endif
                                <td class="text-center"><span class="badge {{ $instr->sesi_aktif_bulan_ini > 0 ? 'bg-primary' : 'bg-secondary' }} rounded-pill px-2 py-1">{{ $instr->sesi_aktif_bulan_ini }}</span></td>
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

    {{-- ═══ TAB 3: DISTRIBUSI WILAYAH & SEKOLAH ═══ --}}
    <div class="tab-pane fade" id="pane-wilayah-sekolah" role="tabpanel">

        {{-- Header & Export Button --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--sd-navy);">
                    <i class="bi bi-geo-alt-fill text-primary me-2"></i>Distribusi Wilayah, Sekolah & Sales
                </h4>
                <p class="text-muted small mb-0">
                    Daftar penugasan instruktur utama (>2x sesi jadwal) dan asisten per rombel sekolah, beserta rekapitulasi beban sales.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.analytics.schedule-distribution.export-schools') }}" 
                   id="btnExportSchoolsExcel" 
                   class="btn btn-success d-inline-flex align-items-center gap-2 px-3 py-2 fw-semibold shadow-sm text-white"
                   style="border-radius: 10px; background-color: #107c41; border-color: #107c41;">
                    <i class="bi bi-file-earmark-excel-fill fs-5"></i>
                    <span>Export Excel (.xlsx)</span>
                </a>
            </div>
        </div>

        {{-- Top KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="sd-kpi-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="sd-kpi-icon" style="background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-collection-fill"></i>
                        </div>
                        <div>
                            <div class="small fw-semibold text-muted">Total Rombel Aktif</div>
                            <div class="fs-4 fw-bold text-dark">{{ $school_distribution_data->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="sd-kpi-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="sd-kpi-icon" style="background: #f0fdf4; color: #16a34a;">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <div class="small fw-semibold text-muted">Total Sekolah</div>
                            <div class="fs-4 fw-bold text-dark">{{ $school_distribution_data->pluck('kodlan')->unique()->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="sd-kpi-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="sd-kpi-icon" style="background: #faf5ff; color: #9333ea;">
                            <i class="bi bi-person-video3"></i>
                        </div>
                        <div>
                            <div class="small fw-semibold text-muted">Instruktur Utama Terlibat</div>
                            <div class="fs-4 fw-bold text-dark">{{ $school_distribution_data->pluck('user_id_instruktur')->filter()->unique()->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="sd-kpi-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="sd-kpi-icon" style="background: #fff7ed; color: #ea580c;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="small fw-semibold text-muted">Salesman Terdaftar</div>
                            <div class="fs-4 fw-bold text-dark">{{ count($sales_summary) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 1: Ringkasan Beban & Total Instruktur beserta Rombel per Sales --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--sd-radius); border: 1px solid var(--sd-line) !important;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-person-badge-fill text-primary me-2"></i>Ringkasan & Total Instruktur beserta Rombel per Sales
                    </h6>
                    <small class="text-muted">Rekapitulasi jumlah sekolah, program, rombel aktif (>2x jadwal), dan instruktur yang bertugas per salesman</small>
                </div>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSalesSummary" aria-expanded="true">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            <div class="collapse show" id="collapseSalesSummary">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 sd-table" style="font-size: .85rem;">
                            <thead style="position: sticky; top: 0; z-index: 2; background: #f8fafc;">
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Nama Salesman</th>
                                    <th>Group Leader</th>
                                    <th class="text-center">Total Sekolah</th>
                                    <th class="text-center">Total Program</th>
                                    <th class="text-center">Total Rombel Aktif</th>
                                    <th class="text-center">Total Instruktur Bertugas</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales_summary as $idx => $sm)
                                    <tr @if($sm->nama_salesman === 'Belum Ditentukan') class="table-warning" @endif>
                                        <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                <div class="sd-avatar" style="width: 28px; height: 28px; font-size: .75rem; background: {{ $sm->nama_salesman === 'Belum Ditentukan' ? '#f59e0b' : '#3b82f6' }};">
                                                    {{ strtoupper(substr($sm->nama_salesman, 0, 1)) }}
                                                </div>
                                                <span>{{ $sm->nama_salesman }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border">
                                                <i class="bi bi-diagram-3-fill me-1"></i>{{ $sm->group_leader ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center fw-semibold">{{ $sm->total_sekolah }}</td>
                                        <td class="text-center fw-semibold">{{ $sm->total_program }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1">
                                                {{ $sm->total_rombel }} Rombel
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success fw-bold px-2 py-1">
                                                {{ $sm->total_instruktur }} Instruktur
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary py-0 px-2" 
                                                    style="font-size: .75rem;" 
                                                    onclick="filterBySalesman('{{ addslashes($sm->nama_salesman) }}')"
                                                    title="Filter tabel rombel di bawah berdasarkan sales ini">
                                                <i class="bi bi-funnel-fill me-1"></i>Filter
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-3">Tidak ada data sales.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        {{-- Section 3: Main Table - Distribusi Jadwal (Per Baris Per Rombel) --}}
        <div class="card border-0 shadow-sm" style="border-radius: var(--sd-radius); border: 1px solid var(--sd-line) !important;">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-table text-primary me-2"></i>Tabel Distribusi Jadwal Sekolah & Instruktur
                        </h6>
                        <small class="text-muted">Format 1 baris per rombel dengan kriteria instruktur utama jadwal &gt; 2x</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="schoolRowCountBadge" class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 fw-semibold">
                            Menampilkan <span id="schoolVisibleCount">{{ $school_distribution_data->count() }}</span> dari {{ $school_distribution_data->count() }} Rombel
                        </span>
                    </div>
                </div>

                {{-- Filter toolbar --}}
                <div class="row g-2 mt-2 pt-2 border-top">
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="schoolSearchInput" class="form-control bg-light border-start-0" placeholder="Cari sekolah, rombel, instruktur, kodlan...">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <select id="schoolCityFilter" class="form-select form-select-sm">
                            <option value="">Semua Kota</option>
                            @foreach($school_cities as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select id="schoolProgramFilter" class="form-select form-select-sm">
                            <option value="">Semua Jenis Program</option>
                            @foreach($school_programs as $prog)
                                <option value="{{ $prog }}">{{ $prog }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-8 col-md-2">
                        <select id="schoolSalesFilter" class="form-select form-select-sm">
                            <option value="">Semua Sales</option>
                            @foreach($school_salesmen as $sls)
                                <option value="{{ $sls }}">{{ $sls }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4 col-md-1 d-grid">
                        <button type="button" id="btnResetSchoolFilters" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 sd-table" id="tableSchoolDistribution" style="font-size: .85rem;">
                        <thead style="position: sticky; top: 0; z-index: 3; background: #0f172a; color: #fff;">
                            <tr>
                                <th class="text-center" style="background: #0f172a; color: #fff; width: 45px;">No</th>
                                <th style="background: #0f172a; color: #fff;">Wilayah Kota</th>
                                <th style="background: #0f172a; color: #fff;">Kecamatan</th>
                                <th style="background: #0f172a; color: #fff;">Nama Sales</th>
                                <th style="background: #0f172a; color: #fff;">Group Leader</th>
                                <th style="background: #0f172a; color: #fff;">Jenis Program</th>
                                <th style="background: #0f172a; color: #fff;">KodLan / NPSN</th>
                                <th style="background: #0f172a; color: #fff;">Nama Sekolah</th>
                                <th style="background: #0f172a; color: #fff;">Rombel</th>
                                <th style="background: #0f172a; color: #fff;">Instruktur Utama (>2x)</th>
                                <th class="text-center" style="background: #0f172a; color: #fff;">Total Sesi</th>
                                <th style="background: #0f172a; color: #fff;">Asisten Instruktur</th>
                            </tr>
                        </thead>
                        <tbody id="schoolTableBody">
                            @forelse($school_distribution_data as $idx => $row)
                                <tr class="school-row"
                                    data-kota="{{ strtolower($row->kota ?? '') }}"
                                    data-program="{{ strtolower($row->kategori_program ?? '') }}"
                                    data-sales="{{ strtolower($row->nama_salesman ?? '') }}"
                                    data-search="{{ strtolower(($row->namasekolah ?? '') . ' ' . ($row->kodlan ?? '') . ' ' . ($row->nama_rombel ?? '') . ' ' . ($row->instruktur_utama ?? '') . ' ' . ($row->asisten_instruktur ?? '') . ' ' . ($row->nama_salesman ?? '') . ' ' . ($row->kota ?? '') . ' ' . ($row->kec ?? '')) }}">
                                    <td class="text-center text-muted fw-semibold school-index">{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $row->kota ?? '-' }}</span>
                                    </td>
                                    <td class="text-muted">{{ $row->kec ?? '-' }}</td>
                                    <td>
                                        @if($row->nama_salesman === 'Belum Ditentukan')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>Belum Ditentukan</span>
                                        @else
                                            <span class="fw-bold text-dark">{{ $row->nama_salesman }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">{{ $row->group_leader ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold border border-primary-subtle">
                                            {{ $row->kategori_program ?? '-' }}
                                        </span>
                                    </td>
                                    <td><code>{{ $row->kodlan ?? '-' }}</code></td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $row->namasekolah ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                            {{ $row->nama_rombel ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-person-check-fill text-success"></i>
                                            <span class="fw-bold text-dark">{{ $row->instruktur_utama ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-success text-white px-2 py-1 fw-bold">
                                            {{ $row->total_sesi }} Sesi
                                        </span>
                                    </td>
                                    <td>
                                        @if(!empty($row->asisten_instruktur))
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-person-badge text-info"></i>
                                                <span>{{ $row->asisten_instruktur }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptySchoolRow">
                                    <td colspan="12" class="text-center text-muted py-4">Tidak ada data jadwal rombel dengan sesi &gt; 2.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- /tab-pane #pane-wilayah-sekolah --}}

    </div>{{-- /tab-content --}}

</div>

@push('scripts')
<script>
    // Debounce helper
    function debounce(fn, wait = 150) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    // Dynamic Filter Fields Toggle
    function toggleFilterFields(mode) {
        document.querySelectorAll('.filter-field-month').forEach(el => el.classList.toggle('d-none', mode !== 'month'));
        document.querySelectorAll('.filter-field-custom').forEach(el => el.classList.toggle('d-none', mode !== 'custom'));
    }

    // Live Table Search (Tab 1) with debounce
    const debouncedTableSearch = debounce(function(val) {
        const query = (val || '').toLowerCase().trim();
        document.querySelectorAll('.instructor-row').forEach(row => {
            const name = row.dataset.name || '';
            row.style.display = name.includes(query) ? '' : 'none';
        });
    }, 150);

    document.getElementById('tableSearchInput')?.addEventListener('input', function() {
        debouncedTableSearch(this.value);
    });

    // Tab 2 Filter Elements
    const kotaFilter = document.getElementById('kotaFilter');
    const availSearchInput = document.getElementById('availSearchInput');
    const dayFilter = document.getElementById('dayFilter');
    const statusFilter = document.getElementById('statusFilter');
    const hideJadwalSwitch = document.getElementById('hideJadwalSwitch');
    const resetAvailFiltersBtn = document.getElementById('resetAvailFiltersBtn');

    // Debounced search for Tab 2
    const debouncedAvailSearch = debounce(applyAvailabilityFilters, 150);

    // Event listeners
    kotaFilter?.addEventListener('change', applyAvailabilityFilters);
    availSearchInput?.addEventListener('input', debouncedAvailSearch);
    dayFilter?.addEventListener('change', function() {
        updateDayColumnVisibility(this.value);
        applyAvailabilityFilters();
    });
    statusFilter?.addEventListener('change', applyAvailabilityFilters);
    hideJadwalSwitch?.addEventListener('change', function() {
        const table = document.getElementById('availabilityTable');
        if (this.checked) {
            table?.classList.add('hide-session-cards');
        } else {
            table?.classList.remove('hide-session-cards');
        }
        applyAvailabilityFilters();
    });

    resetAvailFiltersBtn?.addEventListener('click', function() {
        if (kotaFilter) kotaFilter.value = '';
        if (availSearchInput) availSearchInput.value = '';
        if (dayFilter) dayFilter.value = '';
        if (statusFilter) statusFilter.value = '';
        if (hideJadwalSwitch) {
            hideJadwalSwitch.checked = false;
            document.getElementById('availabilityTable')?.classList.remove('hide-session-cards');
        }
        updateDayColumnVisibility('');
        applyAvailabilityFilters();
    });

    // ── 1 Opsi A: Sesuaikan kolom hari saat filter per hari dipilih ───────────
    function updateDayColumnVisibility(selectedDay) {
        const table = document.getElementById('availabilityTable');
        if (selectedDay) {
            table?.classList.add('single-day-mode');
        } else {
            table?.classList.remove('single-day-mode');
        }

        // Header kolom hari: sembunyikan hari lain, lebarkan hari terpilih
        document.querySelectorAll('.day-header').forEach(th => {
            const isMatch = !selectedDay || th.dataset.day === selectedDay;
            th.style.display = isMatch ? '' : 'none';
            if (selectedDay && th.dataset.day === selectedDay) {
                th.classList.add('day-col-highlight', 'day-col-expanded');
                th.style.minWidth = '260px';
                th.style.width = '';
            } else {
                th.classList.remove('day-col-highlight', 'day-col-expanded');
                th.style.minWidth = '115px';
                th.style.width = '';
            }
        });

        // Cell kolom hari: sembunyikan hari lain
        document.querySelectorAll('.day-cell').forEach(td => {
            const isMatch = !selectedDay || td.dataset.day === selectedDay;
            td.style.display = isMatch ? '' : 'none';
            if (selectedDay && td.dataset.day === selectedDay) {
                td.classList.add('day-col-highlight');
            } else {
                td.classList.remove('day-col-highlight');
            }
        });

        // Sesuaikan colspan baris instruktur yang belum mengisi jadwal
        document.querySelectorAll('#availabilityTable td.cell-no_data').forEach(td => {
            td.colSpan = selectedDay ? 1 : 6;
        });

        // Sesuaikan baris kosong jika ada
        const emptyRowTd = document.querySelector('#availabilityTable tbody td[colspan="10"], #availabilityTable tbody td[colspan="5"]');
        if (emptyRowTd) {
            emptyRowTd.colSpan = selectedDay ? 5 : 10;
        }
    }

    const updateDayColumnHighlight = updateDayColumnVisibility;

    function getRowDayStatus(row, dayName) {
        if (!row || !dayName) return 'no_data';
        const dLower = dayName.toLowerCase();
        const dCapital = dayName.charAt(0).toUpperCase() + dLower.slice(1);
        return row.dataset['status' + dCapital] ||
               row.dataset['status' + dLower] ||
               row.getAttribute('data-status-' + dLower) ||
               'no_data';
    }

    // Prioritas pengurutan status ketersediaan (2 Opsi 2)
    const STATUS_PRIORITY = {
        'free': 1,        // 🟢 Free / Tersedia (paling atas)
        'partial': 2,     // 🟡 Sebagian terisi
        'busy': 3,        // 🔴 Penuh
        'unavailable': 4, // ⬜ Libur
        'no_data': 5      // ⚠️ Belum input jadwal (paling bawah)
    };

    function applyAvailabilityFilters() {
        const kota = (kotaFilter?.value || '').toLowerCase().trim();
        const query = (availSearchInput?.value || '').toLowerCase().trim();
        const selectedDay = dayFilter?.value || '';
        const statusVal = statusFilter?.value || '';

        const tbody = document.querySelector('#availabilityTable tbody');
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('.avail-row'));
        if (rows.length === 0) return;

        // ── 2 Opsi 2: Pengurutan Baris Otomatis ──────────────────────────────
        // Saat filter hari aktif: Urutkan Free -> Partial -> Busy -> Libur -> Belum Isi
        // Jika status sama: urutkan alfabetis A-Z nama instruktur
        // Saat "Semua Hari": kembalikan ke urutan default alfabetis A-Z
        rows.sort((a, b) => {
            if (selectedDay) {
                const stA = getRowDayStatus(a, selectedDay);
                const stB = getRowDayStatus(b, selectedDay);
                const rankA = STATUS_PRIORITY[stA] ?? 99;
                const rankB = STATUS_PRIORITY[stB] ?? 99;
                if (rankA !== rankB) {
                    return rankA - rankB;
                }
            }
            const nameA = a.dataset.name || '';
            const nameB = b.dataset.name || '';
            return nameA.localeCompare(nameB, 'id');
        });

        // Re-append baris ke tbody sesuai urutan baru
        const frag = document.createDocumentFragment();
        rows.forEach(r => frag.appendChild(r));
        tbody.appendChild(frag);

        // ── Filter Visibility & Penomoran Ulang Berurutan ────────────────────
        let visibleCount = 0;
        let visibleNo = 1;

        rows.forEach(row => {
            const rowKota = row.dataset.kota || '';
            const rowName = row.dataset.name || '';
            const kotaOk  = !kota  || rowKota === kota;
            const nameOk  = !query || rowName.includes(query);

            let statusOk = true;

            if (selectedDay) {
                const st = getRowDayStatus(row, selectedDay);
                if (statusVal === 'free' || statusVal === 'hide_partial') {
                    statusOk = (st === 'free');
                } else if (statusVal === 'partial') {
                    statusOk = (st === 'partial' || st === 'busy');
                }
            } else {
                if (statusVal === 'free' || statusVal === 'hide_partial') {
                    // Instruktur yang belum ada jadwal sesi sama sekali (full Free/Libur) di minggu ini
                    statusOk = !DAYS.some(d => {
                        const st = getRowDayStatus(row, d);
                        return st === 'partial' || st === 'busy';
                    });
                } else if (statusVal === 'partial') {
                    // Minimal 1 hari yang ada jadwal sesi (kuning / merah) di minggu ini
                    statusOk = DAYS.some(d => {
                        const st = getRowDayStatus(row, d);
                        return st === 'partial' || st === 'busy';
                    });
                }
            }

            const isVisible = kotaOk && nameOk && statusOk;
            row.style.display = isVisible ? '' : 'none';

            if (isVisible) {
                visibleCount++;
                const noCell = row.querySelector('.sd-col-no');
                if (noCell) {
                    noCell.textContent = visibleNo++;
                }
            }
        });

        const counterEl = document.getElementById('visibleInstructorsCount');
        if (counterEl) {
            counterEl.textContent = visibleCount;
        }
    }

    // ─── Week Picker AJAX Load ───────────────────────────────────────────────
    const DAYS = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    // Helper: render a single cell based on day data from AJAX
    function renderDayCell(dayData) {
        if (!dayData) return null;

        const { status, available, sessions } = dayData;

        const icon = {
            free: '🟢', partial: '🟡', busy: '🔴', unavailable: '⬜', no_data: '⚠️',
        }[status] || '';

        const label = {
            free: 'Free', partial: 'Sebagian', busy: 'Penuh', unavailable: 'Libur', no_data: 'Blm isi',
        }[status] || status;

        let html = `<div class="day-cell-content">`;
        html += `<span class="badge status-badge status-${status}"><span>${icon} ${label}</span></span>`;

        if (available && status !== 'unavailable' && status !== 'no_data') {
            html += `<div class="avail-range-text">${available}</div>`;
        }

        if (sessions && sessions.length > 0) {
            sessions.forEach(s => {
                const schoolInfo = s.school && s.school !== '—' ? `<div class="session-school">${s.school}</div>` : '';
                html += `<div class="session-item-card" title="${s.time} ${s.ekskul}">` +
                        `<div class="session-time">${s.time}</div>` +
                        `<div class="session-ekskul">${s.ekskul}</div>` +
                        schoolInfo +
                        `</div>`;
            });
        }

        html += '</div>';
        return html;
    }

    document.getElementById('weekPicker')?.addEventListener('change', function() {
        document.getElementById('loadWeekBtn')?.click();
    });

    document.getElementById('loadWeekBtn')?.addEventListener('click', function() {
        const weekVal = document.getElementById('weekPicker')?.value;
        if (!weekVal) { alert('Pilih minggu terlebih dahulu'); return; }

        const btn     = this;
        const loading = document.getElementById('loadingBadge');
        const label   = document.getElementById('weekLabelDisplay');
        const table   = document.getElementById('availabilityTable');

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

            // Batch DOM update: hide table momentarily to suppress layout reflows
            if (table) table.style.visibility = 'hidden';

            try {
                document.querySelectorAll('.avail-row').forEach(row => {
                    const instrId = row.dataset.instrId;
                    const instrData = data.availability[instrId];
                    if (!instrData) return;

                    DAYS.forEach((day, idx) => {
                        const dayData = instrData[day];
                        const dayStatus = dayData ? dayData.status : 'no_data';
                        const dayLower = day.toLowerCase();
                        const dayCapital = day.charAt(0).toUpperCase() + dayLower.slice(1);
                        row.setAttribute('data-status-' + dayLower, dayStatus);
                        row.dataset['status' + dayCapital] = dayStatus;
                        row.dataset['status' + dayLower] = dayStatus;

                        const cell = row.querySelector(`.day-cell[data-day="${day}"]`);
                        if (!cell || row.querySelector('td.cell-no_data')) return;

                        const rendered = renderDayCell(dayData);
                        if (rendered !== null) {
                            cell.innerHTML = rendered;
                            cell.className = `text-center day-cell cell-${dayStatus}`;
                            cell.dataset.day = day;
                            cell.style.verticalAlign = 'top';
                        }
                    });
                });

                // Re-apply column visibility & filters/sorting with fresh weekly data
                updateDayColumnVisibility(dayFilter?.value || '');
                applyAvailabilityFilters();
            } finally {
                if (table) table.style.visibility = '';
            }
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

    // Auto-load current week when Tab 2 is opened or page finishes loading
    let weekDataLoaded = false;
    function triggerLoadWeekOnce() {
        if (!weekDataLoaded) {
            weekDataLoaded = true;
            document.getElementById('loadWeekBtn')?.click();
        }
    }

    document.querySelector('button[data-bs-target="#pane-ketersediaan"]')?.addEventListener('shown.bs.tab', function() {
        triggerLoadWeekOnce();
    });

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(triggerLoadWeekOnce, 250);
    });

    // Chart JS Initialization
    let distributionChartInstance = null;
    function initDistributionChart() {
        const ctx = document.getElementById('distributionChart');
        if (ctx && typeof Chart !== 'undefined') {
            if (distributionChartInstance) {
                distributionChartInstance.destroy();
            }
            const chartLabels = @json($chart_data['labels'] ?? []);
            const chartValues = @json($chart_data['data'] ?? []);

            distributionChartInstance = new Chart(ctx, {
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
            window.distributionChartInstance = distributionChartInstance;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDistributionChart();
    });

    document.querySelector('button[data-bs-target="#pane-distribusi"]')?.addEventListener('shown.bs.tab', function() {
        if (!distributionChartInstance) {
            initDistributionChart();
        } else {
            distributionChartInstance.resize();
        }
    });

    // ── Tab 3: School Schedule Distribution Filtering & Export URL sync ────
    function filterSchoolRows() {
        const searchTerm = (document.getElementById('schoolSearchInput')?.value || '').toLowerCase().trim();
        const cityFilter = (document.getElementById('schoolCityFilter')?.value || '').toLowerCase().trim();
        const progFilter = (document.getElementById('schoolProgramFilter')?.value || '').toLowerCase().trim();
        const salesFilter = (document.getElementById('schoolSalesFilter')?.value || '').toLowerCase().trim();

        const rows = document.querySelectorAll('#schoolTableBody .school-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowKota = row.getAttribute('data-kota') || '';
            const rowProg = row.getAttribute('data-program') || '';
            const rowSales = row.getAttribute('data-sales') || '';
            const rowSearch = row.getAttribute('data-search') || '';

            const matchSearch = !searchTerm || rowSearch.includes(searchTerm);
            const matchCity = !cityFilter || rowKota === cityFilter;
            const matchProg = !progFilter || rowProg === progFilter;
            const matchSales = !salesFilter || rowSales === salesFilter;

            if (matchSearch && matchCity && matchProg && matchSales) {
                row.style.display = '';
                visibleCount++;
                const indexCell = row.querySelector('.school-index');
                if (indexCell) indexCell.textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });

        const countBadge = document.getElementById('schoolVisibleCount');
        if (countBadge) countBadge.textContent = visibleCount;

        // Sync parameters with Excel export button
        const baseExportUrl = "{{ route('admin.analytics.schedule-distribution.export-schools') }}";
        const params = new URLSearchParams();
        if (document.getElementById('schoolCityFilter')?.value) {
            params.append('kota', document.getElementById('schoolCityFilter').value);
        }
        if (document.getElementById('schoolProgramFilter')?.value) {
            params.append('program', document.getElementById('schoolProgramFilter').value);
        }
        if (document.getElementById('schoolSalesFilter')?.value) {
            params.append('sales', document.getElementById('schoolSalesFilter').value);
        }

        const exportBtn = document.getElementById('btnExportSchoolsExcel');
        if (exportBtn) {
            exportBtn.href = params.toString() ? `${baseExportUrl}?${params.toString()}` : baseExportUrl;
        }
    }

    function filterBySalesman(salesName) {
        const salesSelect = document.getElementById('schoolSalesFilter');
        if (salesSelect) {
            salesSelect.value = salesName;
            filterSchoolRows();
            document.getElementById('tableSchoolDistribution')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    document.getElementById('schoolSearchInput')?.addEventListener('input', debounce(filterSchoolRows, 150));
    document.getElementById('schoolCityFilter')?.addEventListener('change', filterSchoolRows);
    document.getElementById('schoolProgramFilter')?.addEventListener('change', filterSchoolRows);
    document.getElementById('schoolSalesFilter')?.addEventListener('change', filterSchoolRows);

    document.getElementById('btnResetSchoolFilters')?.addEventListener('click', function() {
        if (document.getElementById('schoolSearchInput')) document.getElementById('schoolSearchInput').value = '';
        if (document.getElementById('schoolCityFilter')) document.getElementById('schoolCityFilter').value = '';
        if (document.getElementById('schoolProgramFilter')) document.getElementById('schoolProgramFilter').value = '';
        if (document.getElementById('schoolSalesFilter')) document.getElementById('schoolSalesFilter').value = '';
        filterSchoolRows();
    });

    // Tab hash state synchronization
    if (window.location.hash) {
        const activeTabBtn = document.querySelector(`button[data-bs-target="${window.location.hash}"]`);
        if (activeTabBtn) {
            bootstrap.Tab.getOrCreateInstance(activeTabBtn).show();
        }
    }
    document.querySelectorAll('#mainTabs button[data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            history.replaceState(null, null, e.target.getAttribute('data-bs-target'));
        });
    });
</script>
@endpush
@endsection
