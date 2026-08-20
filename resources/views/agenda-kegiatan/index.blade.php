@extends('layouts.public')

@section('title', 'Agenda & Rekap Pertemuan Ekskul')

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
        padding: 3.25rem 0 3rem;
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
        font-size: 2.25rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.2;
        margin-top: 0.5rem;
    }
    .hero-subtitle {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.82);
        margin-top: 0.4rem;
        font-weight: 400;
    }
    .hero-stat-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 14px;
        padding: 0.85rem 1.25rem;
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
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1.1;
        color: #FFFFFF;
    }
    .hero-stat-card .stat-lbl {
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.75);
        letter-spacing: 0.05em;
        margin-top: 0.2rem;
        font-weight: 600;
    }

    /* ── Filter Card Container ────────────────────────────── */
    .filter-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        padding: 1.75rem;
        margin-top: -2rem;
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
            <div class="col-lg-7">
                <div class="mb-2">
                    <span class="hero-badge">
                        <i class="bi bi-calendar3"></i> AGENDA & REKAP KEGIATAN
                    </span>
                </div>
                <h1 class="hero-title">Rekap Sesi Mengajar Ekskul</h1>
                <p class="hero-subtitle">Dokumentasi & laporan aktivitas ekstrakurikuler Erlass Institute secara terbuka & terverifikasi</p>
            </div>
            <div class="col-lg-5">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="hero-stat-card">
                            <div class="stat-num" id="heroTotalSesi">{{ number_format($totalSesi) }}</div>
                            <div class="stat-lbl">TOTAL SESI SELESAI</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-stat-card">
                            <div class="stat-num">{{ $wilayahList->count() }}</div>
                            <div class="stat-lbl">WILAYAH AKTIF</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Filter & Content ──────────────────────────────────────── -->
<div class="container-xl py-4">
    <div class="row g-4">

        <!-- ── Filter Panel ──────────────────────────────────── -->
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

        <!-- ── Table & Main Display Area ────────────────────── -->
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

        <!-- ── Sidebar Export Panel ─────────────────────────── -->
        <div class="col-lg-3">
            <div class="export-panel">
                <h6><i class="bi bi-box-arrow-down text-amber-500"></i> Ekspor Dokumentasi</h6>
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

@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let currentFilters = {};
let currentPage    = 1;
let exportToken    = null;
let pollInterval   = null;

function escHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ── Select2 Setup & Cascading Dropdowns ─────────────────
$(document).ready(function() {
    // Initialize Select2 on all filter selects with Bootstrap 5 theme
    $('.select2-filter').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $(document.body)
    });

    // Cascading listeners
    $('#filterKota').on('change', function () {
        loadSekolahList($(this).val());
        loadRombelList('');
    });

    $('#filterSekolah').on('change', function () {
        loadRombelList($(this).val());
    });

    // Initial load
    loadSekolahList('');
    loadRombelList('');
    loadTableData(1);
});

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

// ── Load Table Data via AJAX ─────────────────────────────
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

// ── UI Helpers ───────────────────────────────────────────
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
</script>
@endpush
