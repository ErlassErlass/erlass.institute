@extends('layouts.public')

@section('title', 'Portal Kegiatan & Status Invoice Ekskul')

@push('styles')
<!-- jQuery (Required for Select2) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Select2 CSS & Bootstrap 5 Theme -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    /* ── Hero Section ─────────────────────────────────────── */
    .hero-section {
        background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #2563EB 100%);
        color: #fff;
        padding: 3rem 0 2.5rem;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.25) 0%, transparent 50%),
                    radial-gradient(circle at 20% 80%, rgba(245, 158, 11, 0.15) 0%, transparent 40%);
        pointer-events: none;
    }
    .hero-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #FDE68A;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 0.35rem 0.8rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .hero-title {
        font-size: 2.15rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.2;
        margin-top: 0.5rem;
    }
    .hero-subtitle {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.85);
        margin-top: 0.4rem;
        font-weight: 400;
        max-width: 680px;
    }
    .hero-stat-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 14px;
        padding: 0.75rem 1rem;
        text-align: center;
        backdrop-filter: blur(10px);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hero-stat-card:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.14);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    .hero-stat-card .stat-num {
        font-size: 1.55rem;
        font-weight: 800;
        line-height: 1.1;
        color: #FFFFFF;
    }
    .hero-stat-card .stat-lbl {
        font-size: 0.68rem;
        color: rgba(255, 255, 255, 0.75);
        letter-spacing: 0.05em;
        margin-top: 0.25rem;
        font-weight: 600;
    }

    /* ── Main Tab Navigation Bar ─────────────────────────── */
    .portal-nav-bar {
        background: #0B132B;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 0.75rem;
    }
    .custom-portal-tabs {
        border-bottom: none;
        gap: 0.5rem;
    }
    .custom-portal-tabs .nav-link {
        color: rgba(255, 255, 255, 0.75);
        font-weight: 600;
        font-size: 0.925rem;
        padding: 0.75rem 1.4rem;
        border-radius: 12px 12px 0 0;
        border: none;
        background: transparent;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .custom-portal-tabs .nav-link:hover {
        color: #FFFFFF;
        background: rgba(255, 255, 255, 0.08);
    }
    .custom-portal-tabs .nav-link.active {
        color: #1E3A5F;
        background: #F8FAFC;
        font-weight: 700;
        box-shadow: 0 -4px 14px rgba(0, 0, 0, 0.12);
    }
    .tab-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.2);
        color: #FFFFFF;
    }
    .custom-portal-tabs .nav-link.active .tab-badge {
        background: #2563EB;
        color: #FFFFFF;
    }
    .tab-badge-highlight {
        background: #F59E0B !important;
        color: #0F172A !important;
    }

    /* ── Filter Card Container ────────────────────────────── */
    .filter-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        padding: 1.75rem;
        position: relative;
        z-index: 20;
    }
    .filter-card .form-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #334155;
        letter-spacing: 0.03em;
        margin-bottom: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .filter-card .form-control {
        border-radius: 10px;
        border-color: #CBD5E0;
        font-size: 0.875rem;
        padding: 0.55rem 0.85rem;
        color: #1E293B;
    }
    .filter-card .form-control:focus {
        border-color: #3B82F6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    /* ── Select2 Custom Impeccable Styling ───────────────── */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 10px !important;
        border-color: #CBD5E0 !important;
        font-size: 0.875rem !important;
        min-height: 42px !important;
        display: flex !important;
        align-items: center !important;
        background-color: #FFFFFF !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #1E293B !important;
        font-weight: 500 !important;
        line-height: 1.5 !important;
        padding-left: 0.75rem !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18) !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 12px !important;
        border-color: #E2E8F0 !important;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.15) !important;
        overflow: hidden !important;
        z-index: 1060 !important;
    }
    .select2-container--bootstrap-5 .select2-search__field {
        border-radius: 8px !important;
        font-size: 0.85rem !important;
        padding: 8px 12px !important;
        border-color: #CBD5E0 !important;
    }
    .select2-container--bootstrap-5 .select2-search__field:focus {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #2563EB !important;
    }

    /* ── Action Buttons ──────────────────────────────────── */
    .btn-filter {
        background: linear-gradient(135deg, #1E3A5F 0%, #2563EB 100%);
        color: #FFFFFF;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.625rem 1.25rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }
    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        color: #FFFFFF;
    }
    .btn-reset {
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748B;
        border: 1px solid #CBD5E0;
        background: #F8FAFC;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-reset:hover {
        background: #F1F5F9;
        color: #0F172A;
        border-color: #94A3B8;
    }

    /* ── Info Bar ────────────────────────────────────────── */
    .info-bar-container {
        background: #F0F9FF;
        border: 1px solid #BAE6FD;
        border-radius: 10px;
        padding: 0.65rem 1rem;
        font-size: 0.85rem;
        color: #0369A1;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* ── Table Aesthetics ────────────────────────────────── */
    .agenda-table-card {
        background: #FFFFFF;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
    }
    .agenda-table {
        margin-bottom: 0;
    }
    .agenda-table thead th {
        background: #1E3A5F;
        color: #FFFFFF;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 0.95rem 1rem;
        border: none;
        vertical-align: middle;
    }
    .agenda-table tbody td {
        font-size: 0.85rem;
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-color: #F1F5F9;
        color: #334155;
    }
    .agenda-table tbody tr:hover td {
        background: #F8FAFC;
    }
    .badge-kategori {
        background: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #BFDBFE;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        display: inline-block;
    }
    .badge-hadir {
        background: #DCFCE7;
        color: #15803D;
        border: 1px solid #86EFAC;
        font-size: 0.78rem;
        font-weight: 800;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        min-width: 2.2rem;
        display: inline-block;
        text-align: center;
    }
    .btn-foto {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 8px;
        border: 1px solid #93C5FD;
        color: #1D4ED8;
        background: #EFF6FF;
        font-weight: 600;
        transition: all 0.15s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .btn-foto:hover {
        background: #DBEAFE;
        color: #1E40AF;
        border-color: #60A5FA;
    }
    .btn-absensi {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 8px;
        border: 1px solid #A7F3D0;
        color: #047857;
        background: #ECFDF5;
        font-weight: 600;
        transition: all 0.15s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .btn-absensi:hover {
        background: #D1FAE5;
        color: #065F46;
        border-color: #6EE7B7;
    }
    .btn-project {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 8px;
        border: 1px solid #DDD6FE;
        color: #6D28D9;
        background: #F5F3FF;
        font-weight: 600;
        transition: all 0.15s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .btn-project:hover {
        background: #EDE9FE;
        color: #5B21B6;
        border-color: #C4B5FD;
    }
    .btn-pdf {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 8px;
        border: 1px solid #CBD5E0;
        color: #475569;
        background: #FFFFFF;
        font-weight: 600;
        transition: all 0.15s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .btn-pdf:hover {
        background: #F1F5F9;
        color: #0F172A;
        border-color: #94A3B8;
    }

    /* ── Export Sidebar Panel ────────────────────────────── */
    .export-panel {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
        position: sticky;
        top: 1.5rem;
    }
    .export-panel h6 {
        font-weight: 800;
        color: #0F172A;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-export {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        color: #FFFFFF;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.65rem 1.25rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        width: 100%;
    }
    .btn-export:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(245, 158, 11, 0.4);
        color: #FFFFFF;
    }
    .btn-export:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }
    .btn-download {
        background: linear-gradient(135deg, #10B981 0%, #047857 100%);
        color: #FFFFFF;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.65rem 1.25rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        animation: pulse-emerald 1.2s ease-in-out infinite alternate;
    }
    @keyframes pulse-emerald {
        from { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        to   { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    }

    /* ── Invoice Specific Aesthetics ─────────────────────── */
    .invoice-kpi-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .invoice-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    }
    .invoice-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .invoice-kpi-num {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.15;
        color: #0F172A;
    }
    .invoice-kpi-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-top: 0.15rem;
    }
    .badge-invoice-printed {
        background: #DCFCE7;
        color: #166534;
        border: 1px solid #86EFAC;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 0.3rem 0.65rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .badge-invoice-pending {
        background: #FEF3C7;
        color: #92400E;
        border: 1px solid #FCD34D;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 0.3rem 0.65rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .badge-sales {
        background: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #BFDBFE;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .badge-billable {
        background: #F1F5F9;
        color: #0F172A;
        border: 1px solid #CBD5E0;
        font-size: 0.8rem;
        font-weight: 800;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* ── Empty State ─────────────────────────────────────── */
    .empty-state {
        text-align: center;
        padding: 4rem 1.5rem;
        color: #94A3B8;
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px dashed #CBD5E0;
    }
    .empty-state i {
        font-size: 3.5rem;
        opacity: 0.4;
        display: block;
        margin-bottom: 0.75rem;
        color: #3B82F6;
    }

    /* ── Pagination Custom ───────────────────────────────── */
    .pagination .page-link {
        border-radius: 8px !important;
        margin: 0 2px;
        font-size: 0.825rem;
        font-weight: 600;
        color: #334155;
        border-color: #E2E8F0;
        padding: 0.35rem 0.7rem;
    }
    .pagination .page-item.active .page-link {
        background: #1E3A5F;
        border-color: #1E3A5F;
        color: #FFFFFF;
    }
</style>
@endpush

@section('content')

<!-- ── Hero Section ─────────────────────────────────────────── -->
<section class="hero-section">
    <div class="container-xl">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="mb-2">
                    <span class="hero-badge">
                        <i class="bi bi-shield-check"></i> PORTAL KEGIATAN & STATUS INVOICE EKSKUL
                    </span>
                </div>
                <h1 class="hero-title">Monitoring Kegiatan & Status Invoice</h1>
                <p class="hero-subtitle">Dokumentasi sesi mengajar serta pemantauan status cetak invoice resmi Erlass Institute secara terbuka, transparan, dan terverifikasi.</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-2">
                    <div class="col-4">
                        <div class="hero-stat-card">
                            <div class="stat-num" id="heroTotalSesi">{{ number_format($totalSesi) }}</div>
                            <div class="stat-lbl">SESI SELESAI</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="hero-stat-card">
                            <div class="stat-num">{{ $wilayahList->count() }}</div>
                            <div class="stat-lbl">WILAYAH AKTIF</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="hero-stat-card border-warning">
                            <div class="stat-num text-warning">{{ number_format($totalInvoiceTercetak ?? 0) }}</div>
                            <div class="stat-lbl">INVOICE TERCETAK</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Dual-Tab Switcher ────────────────────────────────────── -->
<div class="portal-nav-bar">
    <div class="container-xl">
        <ul class="nav custom-portal-tabs" id="portalTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-sesi-btn" data-bs-toggle="tab" data-bs-target="#pane-sesi" type="button" role="tab" aria-controls="pane-sesi" aria-selected="true">
                    <i class="bi bi-calendar-check-fill"></i> Rekap Sesi Mengajar
                    <span class="tab-badge" id="badgeTotalSesi">{{ number_format($totalSesi) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-invoice-btn" data-bs-toggle="tab" data-bs-target="#pane-invoice" type="button" role="tab" aria-controls="pane-invoice" aria-selected="false">
                    <i class="bi bi-receipt-cutoff"></i> Status Invoice Tercetak (Sales & Wilayah)
                    <span class="tab-badge tab-badge-highlight">{{ number_format($totalInvoiceTercetak ?? 0) }} Tercetak</span>
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- ── Main Tab Panes Container ─────────────────────────────── -->
<div class="tab-content" id="portalTabContent">

    <!-- ══════════════════════════════════════════════════════════ -->
    <!-- ── TAB 1: REKAP SESI MENGAJAR EKSKUL ──────────────────── -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="tab-pane fade show active" id="pane-sesi" role="tabpanel" aria-labelledby="tab-sesi-btn">
        <div class="container-xl py-4">
            <div class="row g-4">

                <!-- ── Filter Panel Sesi ─────────────────────────────── -->
                <div class="col-12">
                    <div class="filter-card">
                        <div class="row g-3">

                            <!-- Wilayah / Kota -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-geo-alt-fill text-primary"></i> Wilayah / Kota
                                </label>
                                <select class="form-select select2-filter" id="filterKota">
                                    <option value="">— Semua Wilayah —</option>
                                    @foreach($wilayahList as $kota)
                                        <option value="{{ $kota }}">{{ Str::title(strtolower($kota)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sekolah (Pencarian Interaktif) -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-building-fill text-primary"></i> Sekolah (Searchable)
                                </label>
                                <select class="form-select select2-filter" id="filterSekolah">
                                    <option value="">— Semua Sekolah —</option>
                                </select>
                            </div>

                            <!-- Rombel -->
                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">
                                    <i class="bi bi-people-fill text-primary"></i> Rombel
                                </label>
                                <select class="form-select select2-filter" id="filterRombel">
                                    <option value="">— Semua Rombel —</option>
                                </select>
                            </div>

                            <!-- Program Ekskul -->
                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">
                                    <i class="bi bi-journal-bookmark-fill text-primary"></i> Program Ekskul
                                </label>
                                <select class="form-select select2-filter" id="filterProgram">
                                    <option value="">— Semua Program —</option>
                                    @foreach($programList as $prog)
                                        <option value="{{ $prog }}">{{ $prog }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Instruktur -->
                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">
                                    <i class="bi bi-person-badge-fill text-primary"></i> Instruktur
                                </label>
                                <select class="form-select select2-filter" id="filterInstruktur">
                                    <option value="">— Semua Instruktur —</option>
                                    @foreach($instrukturList as $inst)
                                        <option value="{{ $inst->id }}">{{ $inst->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Keyword Search -->
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-search text-primary"></i> Kata Kunci (Sekolah / Materi)
                                </label>
                                <input type="text" class="form-control" id="filterKeyword" placeholder="Ketik nama sekolah, materi, atau kata kunci...">
                            </div>

                            <!-- Tanggal Dari & Sampai -->
                            <div class="col-lg-3 col-md-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar-event text-primary"></i> Dari Tanggal
                                </label>
                                <input type="date" class="form-control" id="filterTanggalDari">
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar-check text-primary"></i> Sampai Tanggal
                                </label>
                                <input type="date" class="form-control" id="filterTanggalSampai">
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-lg-2 col-md-12 d-flex align-items-end gap-2">
                                <button class="btn btn-filter w-100" id="btnFilter" type="button" onclick="loadTableData(1)">
                                    <i class="bi bi-funnel-fill"></i> Terapkan
                                </button>
                                <button class="btn btn-reset px-3" id="btnReset" type="button" onclick="resetFilter()" title="Reset Filter">
                                    <i class="bi bi-arrow-counterclockwise fs-6"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ── Table & Main Display Area Sesi ───────────────── -->
                <div class="col-lg-9">

                    <!-- Controls & Info -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div class="info-bar-container" id="infoBar" style="display:none!important;">
                            <i class="bi bi-info-circle-fill fs-6"></i>
                            <span id="infoBarText" class="fw-semibold">Menampilkan data...</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <label for="filterPerPage" class="form-label mb-0 small text-muted text-nowrap fw-semibold">Tampilkan:</label>
                            <select class="form-select form-select-sm" id="filterPerPage" onchange="loadTableData(1)" style="border-radius:8px; font-size:.8rem; width: auto;">
                                <option value="25" selected>25 Baris</option>
                                <option value="50">50 Baris</option>
                                <option value="100">100 Baris</option>
                                <option value="all">⚡ Tampilkan Semua Data</option>
                            </select>
                        </div>
                    </div>

                    <!-- Loading Spinner State -->
                    <div id="tableLoading" class="text-center py-5" style="display:none;">
                        <div class="spinner-border text-primary" style="width:2.5rem; height:2.5rem;" role="status"></div>
                        <p class="mt-3 text-muted fw-medium" style="font-size:.9rem;">Memuat data sesi kegiatan...</p>
                    </div>

                    <!-- Empty Initial State -->
                    <div id="emptyInitial" class="empty-state">
                        <i class="bi bi-calendar2-week"></i>
                        <p class="fw-bold mb-1 text-dark" style="font-size:1.05rem;">Pilih Filter untuk Menampilkan Data</p>
                        <p class="text-muted mb-0" style="font-size:.85rem;">Gunakan opsi pencarian sekolah atau filter di atas untuk melihat rekap sesi kegiatan.</p>
                    </div>

                    <!-- Empty Results State -->
                    <div id="emptyResults" class="empty-state" style="display:none;">
                        <i class="bi bi-search"></i>
                        <p class="fw-bold mb-1 text-dark" style="font-size:1.05rem;">Tidak Ada Data Ditemukan</p>
                        <p class="text-muted mb-0" style="font-size:.85rem;">Coba ubah kata kunci atau kombinasi filter pencarian Anda.</p>
                    </div>

                    <!-- Table Wrapper -->
                    <div id="tableWrapper" style="display:none;">
                        <div class="agenda-table-card mb-3">
                            <div class="table-responsive">
                                <table class="table agenda-table" id="agendaTable">
                                    <thead>
                                        <tr>
                                            <th width="40">#</th>
                                            <th>Sekolah & Wilayah</th>
                                            <th>Program & Rombel</th>
                                            <th>Instruktur</th>
                                            <th>Materi / Topik</th>
                                            <th>Tanggal & Sesi</th>
                                            <th class="text-center">Hadir</th>
                                            <th class="text-center">Aksi / Berkas</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination & Stats Footer -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <span class="pagination-info text-muted fw-medium" id="paginationInfo" style="font-size:.8rem;"></span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0" id="paginationList"></ul>
                            </nav>
                        </div>
                    </div>

                </div>

                <!-- ── Sidebar Export Panel Sesi ────────────────────── -->
                <div class="col-lg-3">
                    <div class="export-panel">
                        <h6><i class="bi bi-box-arrow-down text-warning"></i> Ekspor Dokumentasi</h6>
                        <p class="text-muted mb-3" style="font-size:0.8rem; line-height: 1.55;">
                            Unduh paket berkas ZIP berisi PDF Rekap Laporan, Foto Kegiatan Kelas, Foto Fisik Absensi, dan Karya Proyek Siswa sesuai filter aktif.
                        </p>
                        <div class="d-grid mb-2">
                            <button class="btn btn-export" id="btnExport" disabled onclick="startExport()">
                                <i class="bi bi-file-earmark-zip-fill"></i> Ekspor Dokumentasi (ZIP)
                            </button>
                        </div>

                        <!-- Export Progress -->
                        <div id="exportProgress" style="display:none;">
                            <div class="d-flex align-items-center gap-2 text-muted mb-2" style="font-size:0.8rem;">
                                <div class="spinner-border spinner-border-sm text-warning"></div>
                                <span>Mengompres & menyiapkan berkas...</span>
                            </div>
                            <div class="progress" style="height:6px; border-radius:6px;">
                                <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" style="width:100%"></div>
                            </div>
                        </div>

                        <!-- Download Link -->
                        <div id="exportDone" style="display:none;" class="d-grid">
                            <a id="downloadLink" href="#" class="btn btn-download text-center">
                                <i class="bi bi-cloud-download-fill"></i> Unduh Berkas ZIP
                            </a>
                        </div>

                        <!-- Export Error -->
                        <div id="exportError" style="display:none;" class="alert alert-danger py-2 px-3 mt-2" style="font-size:0.8rem;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Gagal membuat file. Silakan coba lagi.
                        </div>

                        <hr class="my-3">
                        <div class="text-muted small" style="font-size:0.75rem; line-height:1.5;">
                            <i class="bi bi-shield-check text-success me-1"></i>
                            Data bersifat <strong>read-only</strong>. Seluruh arsip tersimpan secara resmi di server Erlass Institute.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div> <!-- end #pane-sesi -->

    <!-- ══════════════════════════════════════════════════════════ -->
    <!-- ── TAB 2: STATUS INVOICE TERCETAK (SALES & WILAYAH) ───── -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="tab-pane fade" id="pane-invoice" role="tabpanel" aria-labelledby="tab-invoice-btn">
        <div class="container-xl py-4">

            <!-- Filter Panel Invoice -->
            <div class="filter-card mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 pb-2 border-bottom gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-receipt text-primary"></i> Pemantauan Invoice Tercetak (Sales & Wilayah)
                        </h5>
                        <p class="text-muted small mb-0">Lihat status invoice yang telah disetujui resmi dan dicetak untuk penagihan sekolah binaan Anda.</p>
                    </div>
                    @if($currentSalesman)
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                        <i class="bi bi-person-badge-fill me-1"></i> Akun Sales: <strong>{{ $currentSalesman->nama_salesman }}</strong>
                    </span>
                    @endif
                </div>

                <div class="row g-3">
                    <!-- Filter Salesman -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">
                            <i class="bi bi-person-fill text-primary"></i> Salesman / Penanggung Jawab
                        </label>
                        <select class="form-select select2-inv" id="invFilterSalesman">
                            <option value="">— Semua Salesman —</option>
                            @foreach($salesmenList as $sales)
                                <option value="{{ $sales->id }}" {{ ($currentSalesman && $currentSalesman->id == $sales->id) ? 'selected' : '' }}>
                                    {{ $sales->nama_salesman }} {{ $sales->group_leader ? '(' . $sales->group_leader . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Wilayah / Kota -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">
                            <i class="bi bi-geo-alt-fill text-primary"></i> Wilayah / Kota
                        </label>
                        <select class="form-select select2-inv" id="invFilterKota">
                            <option value="">— Semua Wilayah —</option>
                            @foreach($wilayahList as $kota)
                                <option value="{{ $kota }}">{{ Str::title(strtolower($kota)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status Invoice -->
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">
                            <i class="bi bi-check2-circle text-primary"></i> Status Invoice
                        </label>
                        <select class="form-select select2-inv" id="invFilterStatus">
                            <option value="tercetak" selected>🟢 Sudah Tercetak Resmi</option>
                            <option value="all">⚡ Semua Status</option>
                            <option value="pending_akunting">⏳ Menunggu Akunting</option>
                            <option value="pending_operasional">📝 Menunggu Operasional</option>
                        </select>
                    </div>

                    <!-- Filter Periode -->
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">
                            <i class="bi bi-calendar-range-fill text-primary"></i> Periode / Bulan
                        </label>
                        <select class="form-select select2-inv" id="invFilterPeriode">
                            <option value="">— Semua Periode —</option>
                            <option value="Juli">Juli</option>
                            <option value="Agustus">Agustus</option>
                            <option value="September">September</option>
                            <option value="Oktober">Oktober</option>
                            <option value="November">November</option>
                            <option value="Desember">Desember</option>
                            <option value="Bulan ke-1">Bulan ke-1</option>
                            <option value="Bulan ke-2">Bulan ke-2</option>
                            <option value="Bulan ke-3">Bulan ke-3</option>
                            <option value="Bulan ke-4">Bulan ke-4</option>
                            <option value="Bulan ke-5">Bulan ke-5</option>
                            <option value="Bulan ke-6">Bulan ke-6</option>
                            <option value="Semester 1">Semester 1</option>
                            <option value="Semester 2">Semester 2</option>
                            <option value="Tahunan">Tahunan</option>
                        </select>
                    </div>

                    <!-- Keyword Search -->
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">
                            <i class="bi bi-search text-primary"></i> Kata Kunci
                        </label>
                        <input type="text" class="form-control" id="invFilterKeyword" placeholder="Cari No. Inv / Sekolah...">
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
                        <button class="btn btn-reset px-3" type="button" onclick="resetInvoiceFilter()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                        <button class="btn btn-filter px-4" type="button" onclick="loadInvoiceTableData(1)">
                            <i class="bi bi-funnel-fill me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- KPI Metric Cards for Invoices -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-6">
                    <div class="invoice-kpi-card">
                        <div class="invoice-kpi-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <div>
                            <div class="invoice-kpi-num text-success" id="invKpiTotal">0</div>
                            <div class="invoice-kpi-label">Invoice Tercetak</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="invoice-kpi-card">
                        <div class="invoice-kpi-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div>
                            <div class="invoice-kpi-num text-primary" id="invKpiSiswa">0</div>
                            <div class="invoice-kpi-label">Siswa Ditagihkan</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="invoice-kpi-card">
                        <div class="invoice-kpi-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <div class="invoice-kpi-num text-dark" id="invKpiSekolah">0</div>
                            <div class="invoice-kpi-label">Sekolah Tercover</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="invoice-kpi-card">
                        <div class="invoice-kpi-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <div style="min-width: 0;">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 1rem;" id="invKpiSalesman">Semua Salesman</div>
                            <div class="invoice-kpi-label">Penanggung Jawab</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Table Card -->
            <div class="agenda-table-card mb-4">
                <!-- Controls & Info -->
                <div class="p-3 bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-2.5 py-1.5" id="invResultBadge">0 Data Ditemukan</span>
                        <span class="text-muted small" id="invFilterSummaryText">Memuat invoice tercetak...</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label for="invPerPage" class="small text-muted fw-semibold mb-0">Tampilkan:</label>
                        <select class="form-select form-select-sm" id="invPerPage" onchange="loadInvoiceTableData(1)" style="width: auto; border-radius: 8px;">
                            <option value="25" selected>25 Baris</option>
                            <option value="50">50 Baris</option>
                            <option value="100">100 Baris</option>
                            <option value="all">⚡ Tampilkan Semua Data</option>
                        </select>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="invLoading" class="text-center py-5" style="display:none;">
                    <div class="spinner-border text-primary" style="width:2.5rem; height:2.5rem;" role="status"></div>
                    <p class="mt-3 text-muted fw-medium" style="font-size:.9rem;">Memuat daftar invoice tercetak...</p>
                </div>

                <!-- Empty State -->
                <div id="invEmpty" class="empty-state m-3" style="display:none;">
                    <i class="bi bi-file-earmark-x"></i>
                    <p class="fw-bold mb-1 text-dark" style="font-size:1.05rem;">Tidak Ada Invoice Ditemukan</p>
                    <p class="text-muted mb-0" style="font-size:.85rem;">Tidak ada invoice yang cocok dengan kombinasi filter salesman, wilayah, atau status yang dipilih.</p>
                </div>

                <!-- Table Responsive Wrapper -->
                <div class="table-responsive" id="invTableWrapper" style="display:none;">
                    <table class="table agenda-table" id="invTable">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>No. Invoice & Status</th>
                                <th>Sekolah & Wilayah</th>
                                <th>Salesman & Group Leader</th>
                                <th>Program & Skema</th>
                                <th>Periode / Sesi</th>
                                <th class="text-center">Siswa Ditagih</th>
                                <th class="text-center" width="90">Detail</th>
                            </tr>
                        </thead>
                        <tbody id="invTableBody"></tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="p-3 bg-white border-top d-flex flex-wrap justify-content-between align-items-center gap-2" id="invPaginationWrapper" style="display:none;">
                    <span class="text-muted fw-medium small" id="invPaginationInfo"></span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="invPaginationList"></ul>
                    </nav>
                </div>
            </div>

        </div>
    </div> <!-- end #pane-invoice -->

</div> <!-- end .tab-content -->

<!-- ── Modal Detail Invoice ──────────────────────────────────── -->
<div class="modal fade" id="modalDetailInvoice" tabindex="-1" aria-labelledby="modalDetailInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #1E3A5F 0%, #2563EB 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff fs-4"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="modalDetailInvoiceLabel">Detail Invoice Tercetak Resmi</h6>
                        <span class="small text-white-50" id="modalDetailNoInv">—</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3" id="modalDetailContent">
                    <!-- Populated dynamically by JavaScript -->
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 border-top d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="bi bi-shield-check text-success me-1"></i> Terverifikasi Sistem Finansial & Operasional Erlass
                </div>
                <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let currentFilters        = {};
let currentPage           = 1;
let exportToken           = null;
let pollInterval          = null;

let currentInvoiceFilters = {};
let currentInvoicePage    = 1;
let cachedInvoiceData     = [];

function escHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ── Select2 Setup & Initialization ───────────────────────
$(document).ready(function() {
    // Sesi Select2
    $('.select2-filter').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $(document.body)
    });

    // Invoice Select2
    $('.select2-inv').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $(document.body)
    });

    // Cascading listeners untuk Sesi
    $('#filterKota').on('change', function () {
        loadSekolahList($(this).val());
        loadRombelList('');
    });

    $('#filterSekolah').on('change', function () {
        loadRombelList($(this).val());
    });

    // Initial load Sesi
    loadSekolahList('');
    loadRombelList('');
    loadTableData(1);

    // Deep linking tab via URL hash (#invoice atau #sesi)
    if (window.location.hash === '#invoice') {
        const triggerEl = document.querySelector('#tab-invoice-btn');
        if (triggerEl) {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
            loadInvoiceTableData(1);
        }
    }

    // Tab switch listener
    $('#tab-invoice-btn').on('shown.bs.tab', function () {
        window.location.hash = '#invoice';
        if (cachedInvoiceData.length === 0) {
            loadInvoiceTableData(1);
        }
    });

    $('#tab-sesi-btn').on('shown.bs.tab', function () {
        window.location.hash = '#sesi';
    });

    // Live search keyword enter key trigger
    $('#filterKeyword').on('keyup', function(e) {
        if (e.key === 'Enter') loadTableData(1);
    });

    $('#invFilterKeyword').on('keyup', function(e) {
        if (e.key === 'Enter') loadInvoiceTableData(1);
    });
});

// ══════════════════════════════════════════════════════════════
// ── LOGIK TAB 1: SESI MENGAJAR ───────────────────────────────
// ══════════════════════════════════════════════════════════════

function loadSekolahList(kota = '') {
    const $sekolahSel = $('#filterSekolah');
    $sekolahSel.prop('disabled', true).html('<option value="">Memuat daftar sekolah...</option>').trigger('change.select2');

    const url = kota 
        ? `{{ route('agenda-kegiatan.filter') }}?kota=${encodeURIComponent(kota)}`
        : `{{ route('agenda-kegiatan.filter') }}?type=sekolah`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            let html = '<option value="">— Semua Sekolah —</option>';
            data.forEach(s => {
                html += `<option value="${escHtml(s.kodlan)}">${escHtml(s.namasekolah)}</option>`;
            });
            $sekolahSel.html(html).prop('disabled', false).trigger('change.select2');
        })
        .catch(() => {
            $sekolahSel.html('<option value="">— Semua Sekolah —</option>').prop('disabled', false).trigger('change.select2');
        });
}

function loadRombelList(kodlan = '') {
    const $rombelSel = $('#filterRombel');
    $rombelSel.prop('disabled', true).html('<option value="">Memuat rombel...</option>').trigger('change.select2');

    const url = kodlan 
        ? `{{ route('agenda-kegiatan.filter') }}?sekolah_kodlan=${encodeURIComponent(kodlan)}`
        : `{{ route('agenda-kegiatan.filter') }}?type=rombel`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            let html = '<option value="">— Semua Rombel —</option>';
            data.forEach(r => {
                html += `<option value="${escHtml(r.id)}">${escHtml(r.nama_rombel)}</option>`;
            });
            $rombelSel.html(html).prop('disabled', false).trigger('change.select2');
        })
        .catch(() => {
            $rombelSel.html('<option value="">— Semua Rombel —</option>').prop('disabled', false).trigger('change.select2');
        });
}

function resetFilter() {
    $('#filterKota').val('').trigger('change.select2');
    $('#filterSekolah').val('').trigger('change.select2');
    $('#filterRombel').val('').trigger('change.select2');
    $('#filterProgram').val('').trigger('change.select2');
    $('#filterInstruktur').val('').trigger('change.select2');
    $('#filterKeyword').val('');
    $('#filterTanggalDari').val('');
    $('#filterTanggalSampai').val('');

    loadSekolahList('');
    loadRombelList('');
    loadTableData(1);
}

function loadTableData(page = 1) {
    if (typeof page !== 'number' || isNaN(page) || page < 1) {
        page = 1;
    }
    currentPage = page;

    const kota          = $('#filterKota').val() || '';
    const sekolahKodlan = $('#filterSekolah').val() || '';
    const rombelId      = $('#filterRombel').val() || '';
    const program       = $('#filterProgram').val() || '';
    const instrukturId  = $('#filterInstruktur').val() || '';
    const keyword       = $('#filterKeyword').val() || '';
    const tanggalDari   = $('#filterTanggalDari').val() || '';
    const tanggalSampai = $('#filterTanggalSampai').val() || '';
    const perPage       = $('#filterPerPage').val() || 25;

    currentFilters = { 
        kota, 
        sekolah_kodlan: sekolahKodlan, 
        rombel_id: rombelId, 
        program,
        instruktur_id: instrukturId,
        keyword,
        tanggal_dari: tanggalDari, 
        tanggal_sampai: tanggalSampai,
        per_page: perPage
    };

    const params = new URLSearchParams({ ...currentFilters, page });
    for (const [k, v] of [...params.entries()]) { if (!v) params.delete(k); }

    showLoading();

    fetch(`{{ route('agenda-kegiatan.data') }}?${params.toString()}`)
        .then(r => r.json())
        .then(resp => {
            hideLoading();
            if (resp.total === 0) {
                showEmpty();
                return;
            }
            renderTable(resp);
            updateExportButton(resp.total);
        })
        .catch(() => {
            hideLoading();
            showEmpty();
        });
}

function renderTable(resp) {
    const tbody = document.getElementById('tableBody');
    const offset = (resp.current_page - 1) * resp.per_page;

    tbody.innerHTML = resp.data.map((row, i) => `
        <tr>
            <td class="text-muted fw-semibold" style="font-size:.78rem;">${offset + i + 1}</td>
            <td>
                <div class="fw-bold text-dark" style="font-size:.875rem;">${escHtml(row.namsek)}</div>
                <div class="text-muted mt-1" style="font-size:.75rem;"><i class="bi bi-geo-alt-fill text-danger me-1"></i>${escHtml(row.kota)}</div>
            </td>
            <td>
                <span class="badge-kategori">${escHtml(row.kategori_pengajaran)}</span>
                <div class="text-secondary fw-medium mt-1" style="font-size:.78rem;">${escHtml(row.rombel)}</div>
            </td>
            <td style="font-size:.825rem;">
                <div class="fw-bold text-primary"><i class="bi bi-person-fill me-1"></i>${escHtml(row.instruktur_nama)}</div>
            </td>
            <td style="font-size:.825rem; max-width: 220px;" title="${escHtml(row.topik_materi)}">
                <div class="text-truncate fw-medium">${escHtml(row.topik_materi)}</div>
            </td>
            <td style="font-size:.825rem;">
                <div class="fw-semibold text-dark">${escHtml(row.tanggal_mengajar)}</div>
                <span class="badge bg-secondary mt-1" style="font-size:.68rem;">Pertemuan Ke-${escHtml(String(row.pertemuan_ke))}</span>
            </td>
            <td class="text-center"><span class="badge-hadir">${row.jumlah_hadir}</span></td>
            <td class="text-center">
                <div class="d-flex flex-wrap gap-1 justify-content-center">
                    ${row.foto_kegiatan_url
                        ? `<a href="${escHtml(row.foto_kegiatan_url)}" target="_blank" class="btn btn-foto" title="Lihat Foto Kegiatan Belajar Mengajar"><i class="bi bi-camera-fill"></i>Foto Kelas</a>`
                        : ``
                    }
                    ${row.foto_absensi_url
                        ? `<a href="${escHtml(row.foto_absensi_url)}" target="_blank" class="btn btn-absensi" title="Lihat Foto Lembar Absensi Fisik Bertanda Tangan"><i class="bi bi-card-checklist"></i>Fisik Absensi</a>`
                        : ``
                    }
                    ${(!row.foto_kegiatan_url && !row.foto_absensi_url && row.foto_url)
                        ? `<a href="${escHtml(row.foto_url)}" target="_blank" class="btn btn-foto" title="Lihat Foto Bukti"><i class="bi bi-image"></i>Foto</a>`
                        : ``
                    }
                    ${row.project_url
                        ? `<a href="${escHtml(row.project_url)}" target="_blank" class="btn btn-project" title="Unduh File Project Hasil Karya Siswa"><i class="bi bi-file-earmark-code"></i>Project</a>`
                        : ``
                    }
                    <a href="${escHtml(row.print_url)}" target="_blank" class="btn btn-pdf" title="Cetak / Unduh Lembar Presensi PDF"><i class="bi bi-file-earmark-pdf-fill"></i>PDF</a>
                </div>
            </td>
        </tr>
    `).join('');

    // Info bar
    const infoBar = document.getElementById('infoBar');
    document.getElementById('infoBarText').textContent =
        `Menampilkan ${offset + 1}–${Math.min(offset + resp.per_page, resp.total)} dari ${resp.total.toLocaleString('id-ID')} sesi kegiatan`;
    infoBar.style.removeProperty('display');

    // Pagination
    renderPagination(resp.current_page, resp.last_page, resp.total, resp.per_page);

    document.getElementById('emptyInitial').style.display  = 'none';
    document.getElementById('emptyResults').style.display  = 'none';
    document.getElementById('tableWrapper').style.display  = '';
}

function renderPagination(current, last, total, perPage) {
    document.getElementById('paginationInfo').textContent =
        `Halaman ${current} dari ${last}`;

    const ul = document.getElementById('paginationList');
    ul.innerHTML = '';

    if (last <= 1) return;

    const pages = buildPageRange(current, last);

    ul.innerHTML += `<li class="page-item ${current===1?'disabled':''}">
        <a class="page-link" href="#" onclick="loadTableData(${current-1});return false;">‹</a></li>`;

    pages.forEach(p => {
        if (p === '...') {
            ul.innerHTML += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        } else {
            ul.innerHTML += `<li class="page-item ${p===current?'active':''}">
                <a class="page-link" href="#" onclick="loadTableData(${p});return false;">${p}</a></li>`;
        }
    });

    ul.innerHTML += `<li class="page-item ${current===last?'disabled':''}">
        <a class="page-link" href="#" onclick="loadTableData(${current+1});return false;">›</a></li>`;
}

function buildPageRange(current, last) {
    if (last <= 7) return Array.from({length: last}, (_, i) => i + 1);
    const pages = [1];
    if (current > 3) pages.push('...');
    for (let i = Math.max(2, current-1); i <= Math.min(last-1, current+1); i++) pages.push(i);
    if (current < last - 2) pages.push('...');
    pages.push(last);
    return pages;
}

// ── Export ZIP Functionality ─────────────────────────────
function updateExportButton(total) {
    const btn = document.getElementById('btnExport');
    btn.disabled = total === 0;
}

function startExport() {
    resetExportUI();
    document.getElementById('btnExport').disabled = true;
    document.getElementById('exportProgress').style.display = '';

    const params = new URLSearchParams(currentFilters);
    for (const [k, v] of [...params.entries()]) { if (!v) params.delete(k); }

    fetch(`{{ route('agenda-kegiatan.export') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: params.toString(),
    })
    .then(r => r.json())
    .then(resp => {
        exportToken = resp.token;
        pollExportStatus();
    })
    .catch(() => {
        showExportError();
    });
}

function pollExportStatus() {
    clearInterval(pollInterval);
    
    const checkStatus = () => {
        fetch(`{{ url('rekap-pertemuan-ekskul/download') }}/${exportToken}`)
            .then(r => {
                if (r.status === 200 && r.headers.get('Content-Type')?.includes('zip')) {
                    clearInterval(pollInterval);
                    showExportDone();
                    return null;
                }
                return r.json();
            })
            .then(data => {
                if (!data) return;
                if (data.status === 'expired' || data.status === 'error') {
                    clearInterval(pollInterval);
                    showExportError();
                }
            })
            .catch(() => {
                clearInterval(pollInterval);
                showExportError();
            });
    };

    pollInterval = setInterval(checkStatus, 3000);
}

function resetExportUI() {
    clearInterval(pollInterval);
    exportToken = null;
    document.getElementById('exportProgress').style.display = 'none';
    document.getElementById('exportDone').style.display     = 'none';
    document.getElementById('exportError').style.display    = 'none';
}

function showExportDone() {
    document.getElementById('exportProgress').style.display = 'none';
    document.getElementById('exportDone').style.display     = '';
    document.getElementById('downloadLink').href = `{{ url('rekap-pertemuan-ekskul/download') }}/${exportToken}`;
}

function showExportError() {
    document.getElementById('exportProgress').style.display = 'none';
    document.getElementById('exportError').style.display    = '';
    document.getElementById('btnExport').disabled = false;
}

function showLoading() {
    document.getElementById('tableLoading').style.display  = '';
    document.getElementById('tableWrapper').style.display  = 'none';
    document.getElementById('emptyInitial').style.display  = 'none';
    document.getElementById('emptyResults').style.display  = 'none';
}

function hideLoading() {
    document.getElementById('tableLoading').style.display = 'none';
}

function showEmpty() {
    document.getElementById('emptyResults').style.display = '';
    document.getElementById('tableWrapper').style.display = 'none';
    document.getElementById('infoBar').style.setProperty('display', 'none', 'important');
    document.getElementById('btnExport').disabled = true;
}

// ══════════════════════════════════════════════════════════════
// ── LOGIK TAB 2: INVOICE TERCETAK (SALES & WILAYAH) ─────────
// ══════════════════════════════════════════════════════════════

function resetInvoiceFilter() {
    $('#invFilterSalesman').val('').trigger('change.select2');
    $('#invFilterKota').val('').trigger('change.select2');
    $('#invFilterStatus').val('tercetak').trigger('change.select2');
    $('#invFilterPeriode').val('').trigger('change.select2');
    $('#invFilterKeyword').val('');
    loadInvoiceTableData(1);
}

function loadInvoiceTableData(page = 1) {
    if (typeof page !== 'number' || isNaN(page) || page < 1) {
        page = 1;
    }
    currentInvoicePage = page;

    const salesmanId = $('#invFilterSalesman').val() || '';
    const kota       = $('#invFilterKota').val() || '';
    const status     = $('#invFilterStatus').val() || 'tercetak';
    const periode    = $('#invFilterPeriode').val() || '';
    const keyword    = $('#invFilterKeyword').val() || '';
    const perPage    = $('#invPerPage').val() || 25;

    currentInvoiceFilters = {
        salesman_id: salesmanId,
        kota: kota,
        status: status,
        periode_label: periode,
        keyword: keyword,
        per_page: perPage,
        page: page
    };

    const params = new URLSearchParams(currentInvoiceFilters);
    for (const [k, v] of [...params.entries()]) { if (!v) params.delete(k); }

    showInvoiceLoading();

    fetch(`{{ route('agenda-kegiatan.invoice-data') }}?${params.toString()}`)
        .then(r => r.json())
        .then(resp => {
            hideInvoiceLoading();
            cachedInvoiceData = resp.data || [];

            // Update KPI cards
            const summary = resp.summary || {};
            document.getElementById('invKpiTotal').textContent   = (summary.total_invoices || 0).toLocaleString('id-ID');
            document.getElementById('invKpiSiswa').textContent   = (summary.total_siswa || 0).toLocaleString('id-ID');
            document.getElementById('invKpiSekolah').textContent = (summary.total_sekolah || 0).toLocaleString('id-ID');

            // Salesman label in KPI card
            const selectedSalesText = $('#invFilterSalesman option:selected').text();
            document.getElementById('invKpiSalesman').textContent = salesmanId ? selectedSalesText.replace(/—/g, '').trim() : 'Semua Salesman';

            if (!resp.total || resp.total === 0) {
                showInvoiceEmpty();
                document.getElementById('invResultBadge').textContent = '0 Data Ditemukan';
                document.getElementById('invFilterSummaryText').textContent = 'Tidak ada invoice yang memenuhi kriteria filter';
                return;
            }

            renderInvoiceTable(resp);
        })
        .catch(err => {
            console.error('Error loading invoices:', err);
            hideInvoiceLoading();
            showInvoiceEmpty();
        });
}

function renderInvoiceTable(resp) {
    const tbody = document.getElementById('invTableBody');
    const offset = (resp.current_page - 1) * resp.per_page;

    tbody.innerHTML = resp.data.map((row, i) => {
        const isOfficialPrinted = row.is_tercetak;
        const statusBadgeHtml = isOfficialPrinted
            ? `<span class="badge-invoice-printed"><i class="bi bi-check2-all"></i> Tercetak Resmi</span>`
            : `<span class="badge ${escHtml(row.status_badge)}">${escHtml(row.status_label)}</span>`;

        return `
            <tr>
                <td class="text-muted fw-semibold" style="font-size:.78rem;">${offset + i + 1}</td>
                <td>
                    <div class="fw-bold font-monospace text-dark" style="font-size:.875rem;">
                        ${escHtml(row.nomor_invoice)}
                    </div>
                    <div class="mt-1">${statusBadgeHtml}</div>
                    <div class="text-muted mt-1" style="font-size:.72rem;">
                        <i class="bi bi-calendar-event me-1"></i>${escHtml(row.tanggal_tercetak)}
                    </div>
                </td>
                <td>
                    <div class="fw-bold text-dark" style="font-size:.875rem;">${escHtml(row.sekolah_nama)}</div>
                    <div class="text-muted mt-1" style="font-size:.75rem;">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>${escHtml(row.wilayah)}
                        ${row.pd ? `<span class="ms-1 badge bg-light text-secondary border" style="font-size:.65rem;">PD: ${escHtml(row.pd)}</span>` : ''}
                    </div>
                </td>
                <td>
                    <div class="badge-sales"><i class="bi bi-person-fill"></i> ${escHtml(row.sales_nama)}</div>
                    ${row.group_leader ? `<div class="text-muted mt-1" style="font-size:.73rem;"><i class="bi bi-diagram-3 me-1"></i>GL: <strong>${escHtml(row.group_leader)}</strong></div>` : ''}
                    ${row.sales_area ? `<div class="text-muted" style="font-size:.7rem;"><i class="bi bi-compass me-1"></i>Area: ${escHtml(row.sales_area)}</div>` : ''}
                </td>
                <td>
                    <span class="badge-kategori">${escHtml(row.program_nama)}</span>
                    <div class="text-secondary fw-semibold mt-1" style="font-size:.75rem;">
                        <i class="bi bi-tag-fill me-1"></i>${escHtml(row.skema_tagihan || 'Bulanan')}
                    </div>
                </td>
                <td>
                    <div class="fw-bold text-dark" style="font-size:.825rem;">${escHtml(row.periode_label)}</div>
                    <div class="text-muted mt-1" style="font-size:.72rem;">
                        Sesi ${escHtml(String(row.sesi_dari || 1))} s.d ${escHtml(String(row.sesi_sampai || row.jumlah_sesi || 4))} (${escHtml(String(row.jumlah_sesi || 4))} Sesi)
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge-billable">
                        <i class="bi bi-people-fill text-primary"></i> ${escHtml(String(row.jumlah_siswa_billable))} Siswa
                    </span>
                    ${row.total_rombel > 1 ? `<div class="text-muted mt-1" style="font-size:.7rem;">${escHtml(String(row.total_rombel))} Rombel</div>` : ''}
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" onclick="openInvoiceModal(${i})" title="Lihat Rincian Lengkap Invoice">
                        <i class="bi bi-info-circle-fill me-1"></i> Detail
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    // Summary texts
    document.getElementById('invResultBadge').textContent = `${resp.total.toLocaleString('id-ID')} Data Ditemukan`;
    document.getElementById('invFilterSummaryText').textContent =
        `Menampilkan baris ${offset + 1}–${Math.min(offset + resp.per_page, resp.total)} dari total ${resp.total.toLocaleString('id-ID')} invoice`;

    renderInvoicePagination(resp.current_page, resp.last_page, resp.total, resp.per_page);

    document.getElementById('invEmpty').style.display             = 'none';
    document.getElementById('invTableWrapper').style.display       = '';
    document.getElementById('invPaginationWrapper').style.display  = '';
}

function renderInvoicePagination(current, last, total, perPage) {
    document.getElementById('invPaginationInfo').textContent =
        `Halaman ${current} dari ${last} (Total: ${total.toLocaleString('id-ID')})`;

    const ul = document.getElementById('invPaginationList');
    ul.innerHTML = '';

    if (last <= 1) return;

    const pages = buildPageRange(current, last);

    ul.innerHTML += `<li class="page-item ${current===1?'disabled':''}">
        <a class="page-link" href="#" onclick="loadInvoiceTableData(${current-1});return false;">‹</a></li>`;

    pages.forEach(p => {
        if (p === '...') {
            ul.innerHTML += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        } else {
            ul.innerHTML += `<li class="page-item ${p===current?'active':''}">
                <a class="page-link" href="#" onclick="loadInvoiceTableData(${p});return false;">${p}</a></li>`;
        }
    });

    ul.innerHTML += `<li class="page-item ${current===last?'disabled':''}">
        <a class="page-link" href="#" onclick="loadInvoiceTableData(${current+1});return false;">›</a></li>`;
}

function openInvoiceModal(index) {
    const inv = cachedInvoiceData[index];
    if (!inv) return;

    document.getElementById('modalDetailNoInv').textContent = inv.nomor_invoice;

    const contentHtml = `
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100 border">
                <span class="text-muted small fw-semibold text-uppercase">Informasi Sekolah</span>
                <h6 class="fw-bold text-dark mt-1 mb-1">${escHtml(inv.sekolah_nama)}</h6>
                <div class="small text-muted mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Wilayah: <strong>${escHtml(inv.wilayah)}</strong></div>
                ${inv.pd ? `<div class="small text-muted"><i class="bi bi-building me-1"></i>PD: ${escHtml(inv.pd)}</div>` : ''}
                <div class="small text-muted"><i class="bi bi-upc me-1"></i>Kode Sekolah: ${escHtml(inv.sekolah_kodlan || '—')}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100 border">
                <span class="text-muted small fw-semibold text-uppercase">Sales & Distribusi</span>
                <h6 class="fw-bold text-primary mt-1 mb-1"><i class="bi bi-person-badge me-1"></i>${escHtml(inv.sales_nama)}</h6>
                <div class="small text-muted mb-1"><i class="bi bi-diagram-3 me-1"></i>Group Leader: <strong>${escHtml(inv.group_leader || '—')}</strong></div>
                <div class="small text-muted"><i class="bi bi-compass me-1"></i>Area Wilayah: ${escHtml(inv.sales_area || '—')}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100 border">
                <span class="text-muted small fw-semibold text-uppercase">Program & Penagihan</span>
                <h6 class="fw-bold text-dark mt-1 mb-1">${escHtml(inv.program_nama)}</h6>
                <div class="small text-muted mb-1"><i class="bi bi-tag me-1"></i>Skema: <strong>${escHtml(inv.skema_tagihan || 'Bulanan')}</strong></div>
                <div class="small text-muted"><i class="bi bi-mortarboard me-1"></i>Tahun Ajaran: ${escHtml(inv.tahun_ajaran || '2026/2027')}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100 border">
                <span class="text-muted small fw-semibold text-uppercase">Sesi & Siswa Terdaftar</span>
                <h6 class="fw-bold text-success mt-1 mb-1">${escHtml(String(inv.jumlah_siswa_billable))} Siswa Billable</h6>
                <div class="small text-muted mb-1"><i class="bi bi-calendar-range me-1"></i>Periode: <strong>${escHtml(inv.periode_label)}</strong></div>
                <div class="small text-muted"><i class="bi bi-clock-history me-1"></i>Rentang Sesi: Sesi ${escHtml(String(inv.sesi_dari || 1))} s.d ${escHtml(String(inv.sesi_sampai || inv.jumlah_sesi || 4))} (${escHtml(String(inv.jumlah_sesi || 4))} Sesi)</div>
            </div>
        </div>
        <div class="col-12">
            <div class="p-3 bg-light rounded-3 border">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Status Cetak Dokumen</span>
                        <div class="mt-1">
                            ${inv.is_tercetak 
                                ? `<span class="badge-invoice-printed fs-6 px-3 py-1.5"><i class="bi bi-check2-all"></i> Sudah Tercetak Resmi & Disetujui</span>`
                                : `<span class="badge ${escHtml(inv.status_badge)} fs-6 px-3 py-1.5">${escHtml(inv.status_label)}</span>`
                            }
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small">Tanggal Cetak / Disetujui:</span>
                        <div class="fw-bold text-dark">${escHtml(inv.tanggal_tercetak)}</div>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('modalDetailContent').innerHTML = contentHtml;
    const modal = new bootstrap.Modal(document.getElementById('modalDetailInvoice'));
    modal.show();
}

function showInvoiceLoading() {
    document.getElementById('invLoading').style.display            = '';
    document.getElementById('invTableWrapper').style.display      = 'none';
    document.getElementById('invPaginationWrapper').style.display = 'none';
    document.getElementById('invEmpty').style.display             = 'none';
}

function hideInvoiceLoading() {
    document.getElementById('invLoading').style.display = 'none';
}

function showInvoiceEmpty() {
    document.getElementById('invEmpty').style.display             = '';
    document.getElementById('invTableWrapper').style.display      = 'none';
    document.getElementById('invPaginationWrapper').style.display = 'none';
}
</script>
@endpush
