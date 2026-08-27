@extends('layouts.app')

@section('title', 'Integrasi Google Spreadsheet')

@push('styles')
<style>
    .sheets-hero-card {
        background: linear-gradient(135deg, #065f46 0%, #047857 50%, #059669 100%);
        color: white;
        border-radius: 1.25rem;
        padding: 2.25rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.3), 0 8px 10px -6px rgba(5, 150, 105, 0.2);
    }
    .sheets-hero-card::after {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .tab-card {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        transition: all 0.25s ease;
        background: #ffffff;
    }
    .tab-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .badge-live-pulse {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 700;
        backdrop-filter: blur(4px);
    }
    .pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #34d399;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7);
        animation: pulse-green 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(52, 211, 153, 0); }
        100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
    }
    .stat-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Breadcrumb -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Integrasi Google Spreadsheet</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-dark">
                <i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Integrasi Google Spreadsheet
            </h3>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ $spreadsheetUrl }}" target="_blank" class="btn btn-outline-success rounded-pill px-3 shadow-sm fw-bold">
                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Google Sheets
            </a>
            <button id="btnSyncAll" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-arrow-repeat me-1" id="syncIcon"></i> Sinkronkan Semua Data
            </button>
        </div>
    </div>

    <!-- Alert / Toast Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div id="ajaxAlertContainer"></div>

    <!-- Hero Card: Connection Status -->
    <div class="sheets-hero-card mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge-live-pulse">
                        <span class="pulse-dot"></span> REAL-TIME ENGINE ACTIVE
                    </span>
                    <span class="badge bg-white bg-opacity-25 rounded-pill px-3 py-1 text-white small">
                        Google Sheets API v4
                    </span>
                </div>
                <h2 class="fw-bold mb-2">Sinkronisasi Data Otomatis & Real-time</h2>
                <p class="mb-3 text-white text-opacity-90 lead fs-6">
                    Seluruh data <strong>Laporan Mengajar, Jadwal Ekskul, Absensi Siswa, Rekap Honor, Rekap Pertemuan Ekskul, dan KPI</strong> terhubung langsung ke dokumen Google Spreadsheet target untuk kemudahan analisis dan pivot table manajemen.
                </p>
                <div class="d-flex flex-wrap align-items-center gap-3 pt-2">
                    <div class="bg-white bg-opacity-10 rounded-pill px-3 py-1 text-white small d-flex align-items-center gap-2">
                        <i class="bi bi-link-45deg"></i>
                        <span>Spreadsheet ID:</span>
                        <code class="text-warning fw-bold">{{ $spreadsheetId }}</code>
                        <button class="btn btn-link btn-sm text-white p-0" onclick="copySpreadsheetId('{{ $spreadsheetId }}')" title="Salin ID">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur text-start">
                    <div class="text-white text-opacity-75 small mb-1">Terakhir Disinkronkan:</div>
                    <div class="text-white fw-bold fs-6 mb-3" id="lastSyncText">
                        <i class="bi bi-clock-history me-1"></i> {{ $lastSync ? \Carbon\Carbon::parse($lastSync)->translatedFormat('d F Y, H:i:s') : 'Belum pernah disinkronkan' }}
                    </div>
                    <div class="text-white text-opacity-75 small mb-1">Service Account Target:</div>
                    <div class="text-white small text-truncate" title="{{ $serviceAccountEmail }}">
                        <i class="bi bi-shield-check text-info me-1"></i> {{ $serviceAccountEmail }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6 Standard Sheets Tabs Grid -->
    <h5 class="fw-bold mb-3 text-dark">
        <i class="bi bi-folder2-open text-primary me-2"></i>Struktur 6 Tab Google Spreadsheet
    </h5>

    <div class="row g-3 mb-4">
        @foreach($tabs as $tab)
        <div class="col-md-6 col-xl-4">
            <div class="tab-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi {{ $tab['icon'] }} fs-4"></i>
                            <h6 class="fw-bold mb-0 text-dark">{{ $tab['name'] }}</h6>
                        </div>
                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small">Tab #{{ $loop->iteration }}</span>
                    </div>
                    <p class="text-muted small mb-3">
                        {{ $tab['description'] }}
                    </p>
                </div>
                <div>
                    <div class="stat-pill d-flex justify-content-between align-items-center mb-3">
                        <span class="small text-muted">Data Baris:</span>
                        <span class="fw-bold text-dark fs-6" id="rows-{{ $tab['key'] }}">
                            {{ $tab['cached_rows'] > 0 ? number_format($tab['cached_rows']) . ' baris' : 'Siap Sinkron' }}
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.google-sheets.export', $tab['key']) }}" class="btn btn-sm btn-outline-secondary w-100 rounded-pill fw-semibold">
                            <i class="bi bi-download me-1"></i> Unduh CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Pivot Table Ready Card -->
        <div class="col-md-6 col-xl-4">
            <div class="tab-card p-4 h-100 d-flex flex-column justify-content-between" style="border-style: dashed; background: #fdfbf7; border-color: #f59e0b;">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-pie-chart-fill text-warning fs-4"></i>
                            <h6 class="fw-bold mb-0 text-dark">Pivot Table & Analitik</h6>
                        </div>
                        <span class="badge bg-warning bg-opacity-25 text-dark border border-warning rounded-pill px-2 py-1 small">Siap Olah</span>
                    </div>
                    <p class="text-muted small mb-3">
                        Gunakan data bersih dari ke-5 tab di Google Spreadsheet untuk langsung membuat Pivot Table laporan bulanan, performa per sekolah, dan perbandingan honor.
                    </p>
                </div>
                <div>
                    <a href="{{ $spreadsheetUrl }}" target="_blank" class="btn btn-sm btn-warning w-100 rounded-pill fw-bold text-dark">
                        <i class="bi bi-magic me-1"></i> Buka & Buat Pivot Table
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Settings Accordion -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom-0 p-4 pb-0">
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-gear-fill text-secondary me-2"></i>Pengaturan Koneksi Google
            </h5>
            <p class="text-muted small mb-0">Ubah target Spreadsheet ID atau unggah file kunci Service Account jika diperlukan.</p>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.google-sheets.config') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="spreadsheet_id" class="form-label small fw-bold text-muted">Google Spreadsheet ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" id="spreadsheet_id" name="spreadsheet_id" value="{{ $spreadsheetId }}" required>
                        <div class="form-text small">
                            Dapat disalin dari URL Google Sheet di antara <code>/d/</code> dan <code>/edit</code>.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="service_account_json" class="form-label small fw-bold text-muted">Unggah File Service Account (JSON)</label>
                        <input type="file" class="form-control rounded-3" id="service_account_json" name="service_account_json" accept=".json">
                        <div class="form-text small">
                            Opsional. Biarkan kosong jika ingin menggunakan kredensial bawaan sistem.
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Google Apps Script Quick Setup Guide -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #f0fdf4; border: 1px solid #bbf7d0 !important;">
        <div class="card-header bg-transparent border-bottom-0 p-4 pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-success rounded-pill px-3 py-1 mb-2 fw-bold">Paling Mudah & Instan</span>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-code-square text-success me-2"></i>Pasang Menu Sync Otomatis di Google Sheets
                    </h5>
                    <p class="text-muted small mb-0">Cukup salin script berikut ke <strong>Ekstensi (Extensions) &gt; Apps Script</strong> di Google Spreadsheet Anda untuk mengisi seluruh 5 tab secara otomatis dan membuat tombol menu sinkronisasi.</p>
                </div>
                <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="copyAppsScript()">
                    <i class="bi bi-clipboard-check me-1"></i> Salin Script
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="position-relative">
                <pre class="bg-dark text-light p-3 rounded-4 small mb-0" style="max-height: 250px; overflow-y: auto;" id="appsScriptCode"><code>/**
 * ERLASS INSTITUTE - GOOGLE SPREADSHEETS AUTO-SYNC
 * Mengisi & menyinkronkan seluruh 5 Tab Data secara otomatis.
 */
function onOpen() {
  var ui = SpreadsheetApp.getUi();
  ui.createMenu('🚀 Erlass Sync')
    .addItem('🔄 Sinkronkan Seluruh Data Sekarang', 'SINKRONKAN_SEMUA_DATA')
    .addToUi();
}

function SINKRONKAN_SEMUA_DATA() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var url = 'https://erlass.institute/api/google-sheets/feed?token=erlass_sheets_sync_2026';
  
  SpreadsheetApp.getActiveSpreadsheet().toast('Sedang mengambil data dari sistem Erlass...', 'Sinkronisasi', 10);
  
  var response = UrlFetchApp.fetch(url, { muteHttpExceptions: true });
  if (response.getResponseCode() !== 200) {
    SpreadsheetApp.getUi().alert('Gagal mengambil data: ' + response.getContentText());
    return;
  }
  
  var json = JSON.parse(response.getContentText());
  if (!json.success || !json.tabs) {
    SpreadsheetApp.getUi().alert('Format data tidak valid.');
    return;
  }
  
  var tabs = json.tabs;
  var tabColors = {
    'Ringkasan_KPI': '#0284c7',
    'Laporan_Mengajar': '#059669',
    'Jadwal_Sesi_Ekskul': '#d97706',
    'Absensi_Siswa': '#0891b2',
    'Rekap_Honor': '#dc2626',
    'Rekap_Pertemuan_Ekskul': '#7c3aed'
  };

  for (var tabName in tabs) {
    var rows = tabs[tabName];
    if (!rows || rows.length === 0) continue;
    
    var sheet = ss.getSheetByName(tabName);
    if (!sheet) {
      sheet = ss.insertSheet(tabName);
    }
    
    sheet.clear();
    
    // Write data
    var numRows = rows.length;
    var numCols = rows[0].length;
    sheet.getRange(1, 1, numRows, numCols).setValues(rows);
    
    // Style Header
    var headerRange = sheet.getRange(1, 1, 1, numCols);
    headerRange.setFontWeight('bold');
    headerRange.setBackground(tabColors[tabName] || '#0f766e');
    headerRange.setFontColor('#ffffff');
    sheet.setFrozenRows(1);
  }
  
  // Hapus Sheet1 default jika kosong
  var sheet1 = ss.getSheetByName('Sheet1');
  if (sheet1 && ss.getSheets().length > 1) {
    try { ss.deleteSheet(sheet1); } catch(e) {}
  }
  
  SpreadsheetApp.getActiveSpreadsheet().toast('✅ Sukses! Seluruh 5 Tab telah terisi lengkap.', 'Selesai', 5);
}</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copySpreadsheetId(id) {
    navigator.clipboard.writeText(id).then(function() {
        alert('Spreadsheet ID berhasil disalin ke clipboard!');
    });
}

function copyAppsScript() {
    const text = document.getElementById('appsScriptCode').innerText;
    navigator.clipboard.writeText(text).then(function() {
        alert('Apps Script berhasil disalin! Silakan tempel di Google Spreadsheet (Ekstensi > Apps Script).');
    });
}

document.getElementById('btnSyncAll').addEventListener('click', function() {
    const btn = this;
    const icon = document.getElementById('syncIcon');
    const alertBox = document.getElementById('ajaxAlertContainer');

    btn.disabled = true;
    icon.classList.add('spinner-border', 'spinner-border-sm');
    icon.classList.remove('bi-arrow-repeat');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyinkronkan 5 Tab...';

    fetch("{{ route('admin.google-sheets.sync') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-repeat me-1" id="syncIcon"></i> Sinkronkan Semua Data';

        if (data.success) {
            alertBox.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <strong>Sukses!</strong> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            if (data.data && data.data.timestamp) {
                document.getElementById('lastSyncText').innerHTML = `<i class="bi bi-clock-history me-1"></i> ${data.data.timestamp}`;
            }
            // Update row counts if available
            if (data.data && data.data.results) {
                for (const [key, res] of Object.entries(data.data.results)) {
                    const rowElem = document.getElementById(`rows-${key}`);
                    if (rowElem && res.rows) {
                        rowElem.innerText = `${res.rows.toLocaleString()} baris`;
                    }
                }
            }
        } else {
            alertBox.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Perhatian:</strong> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-repeat me-1" id="syncIcon"></i> Sinkronkan Semua Data';
        alertBox.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Terjadi Kesalahan:</strong> ${err.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    });
});
</script>
@endpush
