@extends('layouts.public')

@section('title', 'Agenda Kegiatan')

@push('styles')
<style>
    /* ── Hero ─────────────────────────────────────── */
    .hero-section {
        background: linear-gradient(135deg, #1E3A5F 0%, #2C5282 60%, #3B6BAE 100%);
        color: #fff;
        padding: 3rem 0 2.5rem;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: .5;
    }
    .hero-title   { font-size: 2rem; font-weight: 700; letter-spacing: -.02em; }
    .hero-subtitle { font-size: 1rem; opacity: .8; margin-top: .25rem; }
    .hero-stat-card {
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 12px;
        padding: .75rem 1.25rem;
        text-align: center;
        backdrop-filter: blur(6px);
    }
    .hero-stat-card .stat-num  { font-size: 1.6rem; font-weight: 700; line-height: 1; }
    .hero-stat-card .stat-lbl  { font-size: .72rem; opacity: .7; letter-spacing: .04em; margin-top: .15rem; }

    /* ── Filter Panel ──────────────────────────────── */
    .filter-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(30,58,95,.08);
        padding: 1.5rem;
        margin-top: -1.5rem;
        position: relative;
        z-index: 10;
    }
    .filter-card .form-label   { font-size: .8rem; font-weight: 600; color: #374151; letter-spacing: .02em; }
    .filter-card .form-select  { border-radius: 10px; border-color: #CBD5E0; font-size: .875rem; }
    .filter-card .form-select:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
    .btn-filter {
        background: linear-gradient(135deg, #1E3A5F, #2C5282);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: .875rem;
        padding: .5rem 1.25rem;
        transition: all .2s;
    }
    .btn-filter:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30,58,95,.3); color: #fff; }
    .btn-reset {
        border-radius: 10px;
        font-size: .875rem;
        font-weight: 500;
        color: #64748B;
        border-color: #CBD5E0;
    }

    /* ── Info bar ──────────────────────────────────── */
    .info-bar {
        background: #EBF4FF;
        border: 1px solid #BFDBFE;
        border-radius: 10px;
        padding: .6rem 1rem;
        font-size: .82rem;
        color: #1E40AF;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    /* ── Table ─────────────────────────────────────── */
    .agenda-table { border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; }
    .agenda-table thead th {
        background: #1E3A5F;
        color: #fff;
        font-size: .78rem;
        font-weight: 600;
        letter-spacing: .04em;
        padding: .85rem 1rem;
        border: none;
    }
    .agenda-table tbody td {
        font-size: .85rem;
        padding: .75rem 1rem;
        vertical-align: middle;
        border-color: #F1F5F9;
    }
    .agenda-table tbody tr:hover td { background: #F0F7FF; }
    .badge-kategori {
        background: #E0F2FE;
        color: #0369A1;
        font-size: .7rem;
        font-weight: 600;
        padding: .2rem .55rem;
        border-radius: 20px;
    }
    .badge-hadir {
        background: #DCFCE7;
        color: #166534;
        font-size: .75rem;
        font-weight: 700;
        padding: .25rem .6rem;
        border-radius: 20px;
        min-width: 2rem;
        display: inline-block;
        text-align: center;
    }
    .btn-foto {
        font-size: .75rem;
        padding: .2rem .55rem;
        border-radius: 8px;
        border-color: #BFDBFE;
        color: #2563EB;
        background: #EFF6FF;
        font-weight: 500;
        transition: all .15s;
    }
    .btn-foto:hover { background: #DBEAFE; color: #1D4ED8; border-color: #93C5FD; }

    /* ── Export Panel ──────────────────────────────── */
    .export-panel {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 12px rgba(30,58,95,.06);
    }
    .export-panel h6 { font-weight: 700; color: #1E3A5F; font-size: .9rem; }
    .btn-export {
        background: linear-gradient(135deg, #F59E0B, #D97706);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: .875rem;
        padding: .55rem 1.25rem;
        transition: all .2s;
    }
    .btn-export:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(245,158,11,.4); color: #fff; }
    .btn-export:disabled { opacity: .5; cursor: not-allowed; }
    .btn-download {
        background: linear-gradient(135deg, #059669, #047857);
        color: #fff; border: none; border-radius: 10px;
        font-weight: 700; font-size: .875rem; padding: .55rem 1.25rem;
        animation: pulse-green .8s ease-in-out infinite alternate;
    }
    @keyframes pulse-green {
        from { box-shadow: 0 0 0 0 rgba(5,150,105,.4); }
        to   { box-shadow: 0 0 0 8px rgba(5,150,105,0); }
    }

    /* ── Empty state ───────────────────────────────── */
    .empty-state { text-align: center; padding: 3.5rem 1rem; color: #94A3B8; }
    .empty-state i { font-size: 3.5rem; opacity: .4; display: block; margin-bottom: 1rem; }
    .empty-state p { font-size: .95rem; margin: 0; }

    /* ── Pagination ────────────────────────────────── */
    .pagination-info { font-size: .8rem; color: #64748B; }
    .pagination .page-link {
        border-radius: 8px !important;
        margin: 0 2px;
        font-size: .8rem;
        color: #1E3A5F;
        border-color: #E2E8F0;
    }
    .pagination .page-item.active .page-link {
        background: #1E3A5F;
        border-color: #1E3A5F;
    }
</style>
@endpush

@section('content')

<!-- ── Hero Section ─────────────────────────────────────────── -->
<section class="hero-section">
    <div class="container-xl">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark fw-semibold" style="font-size:.72rem;">
                        <i class="bi bi-calendar3 me-1"></i>AGENDA KEGIATAN
                    </span>
                </div>
                <h1 class="hero-title">Rekap Sesi Mengajar</h1>
                <p class="hero-subtitle">Data aktivitas ekstrakurikuler Erlass Institute — tersedia untuk umum</p>
            </div>
            <div class="col-md-5">
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

<!-- ── Filter & Table ─────────────────────────────────────────── -->
<div class="container-xl py-4">
    <div class="row g-4">

        <!-- Filter Card -->
        <div class="col-12">
            <div class="filter-card">
                <div class="row g-3 align-items-end">
                    <!-- Wilayah -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-uppercase">
                            <i class="bi bi-geo-alt me-1"></i>Wilayah / Kota
                        </label>
                        <select class="form-select" id="filterKota">
                            <option value="">— Semua Wilayah —</option>
                            @foreach($wilayahList as $kota)
                                <option value="{{ $kota }}">{{ Str::title(strtolower($kota)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Sekolah -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-uppercase">
                            <i class="bi bi-building me-1"></i>Sekolah
                        </label>
                        <select class="form-select" id="filterSekolah">
                            <option value="">— Semua Sekolah —</option>
                        </select>
                    </div>
                    <!-- Rombel -->
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label text-uppercase">
                            <i class="bi bi-people me-1"></i>Rombel
                        </label>
                        <select class="form-select" id="filterRombel">
                            <option value="">— Semua Rombel —</option>
                        </select>
                    </div>

                    <!-- Program / Ekskul (BARU) -->
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label text-uppercase">
                            <i class="bi bi-journal-bookmark me-1"></i>Program Ekskul
                        </label>
                        <select class="form-select" id="filterProgram">
                            <option value="">— Semua Program —</option>
                            @foreach($programList as $prog)
                                <option value="{{ $prog }}">{{ $prog }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Instruktur (BARU) -->
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label text-uppercase">
                            <i class="bi bi-person-badge me-1"></i>Instruktur
                        </label>
                        <select class="form-select" id="filterInstruktur">
                            <option value="">— Semua Instruktur —</option>
                            @foreach($instrukturList as $inst)
                                <option value="{{ $inst->id }}">{{ $inst->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Keyword Search (BARU) -->
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label text-uppercase">
                            <i class="bi bi-search me-1"></i>Cari Kata Kunci (Sekolah / Materi)
                        </label>
                        <input type="text" class="form-control" id="filterKeyword" placeholder="Ketik nama sekolah, materi, atau kata kunci..." style="border-radius:10px; font-size:.875rem;">
                    </div>

                    <!-- Tanggal Dari & Sampai -->
                    <div class="col-lg-3 col-md-3">
                        <label class="form-label text-uppercase">
                            <i class="bi bi-calendar-range me-1"></i>Dari Tanggal
                        </label>
                        <input type="date" class="form-control" id="filterTanggalDari" style="border-radius:10px; font-size:.875rem;">
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <label class="form-label text-uppercase">
                            <i class="bi bi-calendar-check me-1"></i>Sampai Tanggal
                        </label>
                        <input type="date" class="form-control" id="filterTanggalSampai" style="border-radius:10px; font-size:.875rem;">
                    </div>

                    <!-- Buttons -->
                    <div class="col-lg-2 col-md-12 d-flex gap-2">
                        <button class="btn btn-filter w-100" id="btnFilter" type="button" onclick="loadTableData(1)">
                            <i class="bi bi-search me-1"></i>Terapkan
                        </button>
                        <button class="btn btn-reset btn-outline-secondary" id="btnReset" type="button" onclick="resetFilter()" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Area -->
        <div class="col-lg-9">
            <!-- Table Header Controls & Info bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div class="info-bar border-0 bg-transparent p-0 m-0" id="infoBar" style="display:none!important;">
                    <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                    <span id="infoBarText" class="fw-semibold text-dark" style="font-size:.85rem;">Menampilkan data...</span>
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

            <!-- Loading -->
            <div id="tableLoading" class="text-center py-5" style="display:none;">
                <div class="spinner-border text-primary" style="width:2.5rem; height:2.5rem;"></div>
                <p class="mt-3 text-muted" style="font-size:.9rem;">Memuat data sesi...</p>
            </div>

            <!-- Empty state (initial) -->
            <div id="emptyInitial" class="empty-state">
                <i class="bi bi-calendar2-week"></i>
                <p class="fw-semibold mb-1" style="color:#475569;">Pilih filter untuk menampilkan data</p>
                <p style="font-size:.82rem;">Gunakan dropdown di atas untuk memfilter sesi kegiatan yang ingin Anda lihat.</p>
            </div>

            <!-- Empty state (no results) -->
            <div id="emptyResults" class="empty-state" style="display:none;">
                <i class="bi bi-search"></i>
                <p class="fw-semibold mb-1" style="color:#475569;">Tidak ada data ditemukan</p>
                <p style="font-size:.82rem;">Coba ubah kombinasi filter yang Anda gunakan.</p>
            </div>

            <!-- Table -->
            <div id="tableWrapper" style="display:none;">
                <div class="table-responsive agenda-table mb-3">
                    <table class="table table-hover mb-0" id="agendaTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sekolah & Wilayah</th>
                                <th>Program & Rombel</th>
                                <th>Instruktur</th>
                                <th>Materi / Topik</th>
                                <th>Tanggal & Ke-</th>
                                <th style="text-align:center;">Hadir</th>
                                <th style="text-align:center;">Dokumentasi & File</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center">
                    <span class="pagination-info" id="paginationInfo"></span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="paginationList"></ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Sidebar: Export Panel -->
        <div class="col-lg-3">
            <div class="export-panel">
                <h6><i class="bi bi-box-arrow-down me-1"></i>Ekspor Dokumentasi</h6>
                <p class="text-muted mb-3" style="font-size:.78rem;">
                    Ekspor berkas ZIP berisi PDF Laporan Rekap, Foto Presensi Kegiatan, dan Berkas Proyek sesuai filter aktif.
                </p>
                <div class="d-grid mb-2">
                    <button class="btn btn-export" id="btnExport" disabled onclick="startExport()">
                        <i class="bi bi-file-earmark-zip me-1"></i>Ekspor Dokumentasi (ZIP)
                    </button>
                </div>

                <!-- Export Progress -->
                <div id="exportProgress" style="display:none;">
                    <div class="d-flex align-items-center gap-2 text-muted mb-2" style="font-size:.8rem;">
                        <div class="spinner-border spinner-border-sm text-warning"></div>
                        <span>Sedang mengompres berkas...</span>
                    </div>
                    <div class="progress" style="height:4px; border-radius:4px;">
                        <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" style="width:100%"></div>
                    </div>
                </div>

                <!-- Download Button -->
                <div id="exportDone" style="display:none;" class="d-grid">
                    <a id="downloadLink" href="#" class="btn btn-download text-center">
                        <i class="bi bi-cloud-download me-1"></i>Unduh Dokumentasi (ZIP)
                    </a>
                </div>

                <!-- Export Error -->
                <div id="exportError" style="display:none;" class="alert alert-danger py-2 px-3 mt-2" style="font-size:.78rem;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Gagal membuat file. Silakan coba lagi.
                </div>

                <hr class="my-3">
                <p class="text-muted" style="font-size:.73rem; line-height:1.5;">
                    <i class="bi bi-shield-check me-1 text-success"></i>
                    Data yang ditampilkan bersifat <strong>read-only</strong>. Seluruh dokumentasi tersimpan aman di server Erlass Institute.
                </p>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
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

// ── Cascading Dropdowns ──────────────────────────────────
function loadSekolahList(kota = '') {
    const sekolahSel = document.getElementById('filterSekolah');
    const rombelSel  = document.getElementById('filterRombel');

    sekolahSel.innerHTML = '<option value="">Memuat...</option>';
    sekolahSel.disabled  = true;

    const url = kota 
        ? `{{ route('agenda-kegiatan.filter') }}?kota=${encodeURIComponent(kota)}`
        : `{{ route('agenda-kegiatan.filter') }}?type=sekolah`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            sekolahSel.innerHTML = '<option value="">— Semua Sekolah —</option>';
            data.forEach(s => {
                sekolahSel.innerHTML += `<option value="${s.kodlan}">${s.namasekolah}</option>`;
            });
            sekolahSel.disabled = false;
        });
}

function loadRombelList(kodlan = '') {
    const rombelSel = document.getElementById('filterRombel');
    rombelSel.innerHTML = '<option value="">Memuat...</option>';
    rombelSel.disabled  = true;

    const url = kodlan 
        ? `{{ route('agenda-kegiatan.filter') }}?sekolah_kodlan=${encodeURIComponent(kodlan)}`
        : `{{ route('agenda-kegiatan.filter') }}?type=rombel`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            rombelSel.innerHTML = '<option value="">— Semua Rombel —</option>';
            data.forEach(r => {
                rombelSel.innerHTML += `<option value="${r.id}">${r.nama_rombel}</option>`;
            });
            rombelSel.disabled = false;
        });
}

document.getElementById('filterKota').addEventListener('change', function () {
    loadSekolahList(this.value);
    loadRombelList('');
});

document.getElementById('filterSekolah').addEventListener('change', function () {
    loadRombelList(this.value);
});

function resetFilter() {
    document.getElementById('filterKota').value          = '';
    document.getElementById('filterSekolah').value       = '';
    document.getElementById('filterRombel').value        = '';
    document.getElementById('filterProgram').value        = '';
    document.getElementById('filterInstruktur').value     = '';
    document.getElementById('filterKeyword').value        = '';
    document.getElementById('filterTanggalDari').value   = '';
    document.getElementById('filterTanggalSampai').value = '';

    loadSekolahList('');
    loadRombelList('');
    loadTableData(1);
}

// Auto load data and populate dropdowns on DOM ready
document.addEventListener('DOMContentLoaded', function () {
    loadSekolahList('');
    loadRombelList('');
    loadTableData(1);
});

// ── Load table data ──────────────────────────────────────
function loadTableData(page = 1) {
    if (typeof page !== 'number' || isNaN(page) || page < 1) {
        page = 1;
    }
    currentPage = page;

    const kota          = document.getElementById('filterKota').value;
    const sekolahKodlan = document.getElementById('filterSekolah').value;
    const rombelId      = document.getElementById('filterRombel').value;
    const program       = document.getElementById('filterProgram').value;
    const instrukturId  = document.getElementById('filterInstruktur').value;
    const keyword       = document.getElementById('filterKeyword').value;
    const tanggalDari   = document.getElementById('filterTanggalDari').value;
    const tanggalSampai = document.getElementById('filterTanggalSampai').value;
    const perPage       = document.getElementById('filterPerPage') ? document.getElementById('filterPerPage').value : 25;

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
            <td class="text-muted" style="font-size:.75rem;">${offset + i + 1}</td>
            <td>
                <div class="fw-bold text-dark" style="font-size:.85rem;">${escHtml(row.namsek)}</div>
                <div class="text-muted" style="font-size:.72rem;"><i class="bi bi-geo-alt me-1"></i>${escHtml(row.kota)}</div>
            </td>
            <td>
                <span class="badge-kategori">${escHtml(row.kategori_pengajaran)}</span>
                <div class="text-secondary mt-1" style="font-size:.78rem;">${escHtml(row.rombel)}</div>
            </td>
            <td style="font-size:.82rem;">
                <div class="fw-semibold text-primary"><i class="bi bi-person-fill me-1"></i>${escHtml(row.instruktur_nama)}</div>
            </td>
            <td style="font-size:.82rem; max-width: 200px;" title="${escHtml(row.topik_materi)}">
                <div class="text-truncate">${escHtml(row.topik_materi)}</div>
            </td>
            <td style="font-size:.82rem;">
                <div>${escHtml(row.tanggal_mengajar)}</div>
                <span class="badge bg-secondary" style="font-size:.68rem;">Sesi Ke-${escHtml(String(row.pertemuan_ke))}</span>
            </td>
            <td style="text-align:center;"><span class="badge-hadir">${row.jumlah_hadir}</span></td>
            <td style="text-align:center;">
                <div class="d-flex flex-wrap gap-1 justify-content-center">
                    ${row.foto_url
                        ? `<a href="${escHtml(row.foto_url)}" target="_blank" class="btn btn-foto btn-sm me-1" title="Lihat Foto Absensi & Kegiatan"><i class="bi bi-image me-1"></i>Foto</a>`
                        : ``
                    }
                    ${row.project_url
                        ? `<a href="${escHtml(row.project_url)}" download class="btn btn-outline-success btn-sm" title="Download File Project"><i class="bi bi-download me-1"></i>Project</a>`
                        : ``
                    }
                    <a href="${escHtml(row.print_url)}" target="_blank" class="btn btn-outline-primary btn-sm" title="Cetak Laporan PDF"><i class="bi bi-printer me-1"></i>PDF</a>
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

// ── Export ───────────────────────────────────────────────
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
                if (!data) return; // downloaded
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

}

// ── Reset Filter ─────────────────────────────────────────
function resetFilter() {
    document.getElementById('filterKota').value         = '';
    document.getElementById('filterSekolah').value      = '';
    document.getElementById('filterSekolah').disabled   = true;
    document.getElementById('filterSekolah').innerHTML  = '<option value="">— Pilih Wilayah dulu —</option>';
    document.getElementById('filterRombel').value       = '';
    document.getElementById('filterRombel').disabled    = true;
    document.getElementById('filterRombel').innerHTML   = '<option value="">— Semua Rombel —</option>';
    document.getElementById('filterTanggalDari').value  = '';
    document.getElementById('filterTanggalSampai').value = '';

    currentFilters = {};
    document.getElementById('tableWrapper').style.display  = 'none';
    document.getElementById('emptyInitial').style.display  = '';
    document.getElementById('emptyResults').style.display  = 'none';
    document.getElementById('infoBar').style.setProperty('display', 'none', 'important');
    document.getElementById('btnExport').disabled = true;
    resetExportUI();
}

// ── UI helpers ───────────────────────────────────────────
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

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
}
</script>
@endpush
