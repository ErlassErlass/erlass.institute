@extends('layouts.app')

@section('title', 'Manajemen Siswa - ' . $ekstrakurikuler->kategori_program)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-people-fill me-2"></i>Manajemen Siswa
            </h1>
            <p class="mb-0 text-muted">{{ $ekstrakurikuler->kategori_program }} - {{ $ekstrakurikuler->sekolah->namasekolah ?? 'N/A' }}</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('ekstrakurikuler.show', $ekstrakurikuler) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Program
            </a>
            @can('update', $ekstrakurikuler)
                <div class="btn-group" role="group">
                    <a href="{{ route('ekstrakurikuler.enrollment.create', $ekstrakurikuler) }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i> Daftarkan Siswa
                    </a>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkImportRombelModal">
                        <i class="bi bi-people-fill me-1"></i> Daftarkan dari Kelas Sekolah
                    </button>
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importSiswaProgramModal">
                        <i class="bi bi-file-earmark-excel me-1"></i> Unggah Excel/CSV
                    </button>
                </div>
            @endcan
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Siswa</h6>
                            <h3 class="mb-0">{{ $enrollments->total() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Siswa Aktif</h6>
                            <h3 class="mb-0">{{ $enrollments->where('status', 'aktif')->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Lulus</h6>
                            <h3 class="mb-0">{{ $enrollments->where('status', 'lulus')->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-trophy fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Keluar</h6>
                            <h3 class="mb-0">{{ $enrollments->where('status', 'keluar')->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-x fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->import_errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <h6 class="alert-heading font-weight-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Rincian Error Import Siswa Program:</h6>
            <ul class="mb-0 small ps-3" style="max-height: 180px; overflow-y: auto;">
                @foreach($errors->import_errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <!-- Filter & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ekstrakurikuler.enrollment.index', $ekstrakurikuler) }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="keluar" {{ request('status') === 'keluar' ? 'selected' : '' }}>Keluar</option>
                            <option value="pindah" {{ request('status') === 'pindah' ? 'selected' : '' }}>Pindah</option>
                            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="rombel_id" class="form-label">Rombel</label>
                        <select class="form-select" id="rombel_id" name="rombel_id">
                            <option value="">Semua Rombel</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                                    {{ $rombel->nama_rombel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="search" class="form-label">Cari Siswa</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nama, NISN, atau Kelas">
                    </div>
                    <div class="col-md-2">
                        <label for="sort" class="form-label">Urutkan</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="nisn_asc" {{ request('sort', 'nisn_asc') === 'nisn_asc' ? 'selected' : '' }}>NISN (Kecil - Besar)</option>
                            <option value="nisn_desc" {{ request('sort') === 'nisn_desc' ? 'selected' : '' }}>NISN (Besar - Kecil)</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
                            <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Tgl Daftar Terbaru</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="form-label">Tampilkan</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="25" {{ request('per_page', '25') === '25' ? 'selected' : '' }}>25 data</option>
                            <option value="50" {{ request('per_page') === '50' ? 'selected' : '' }}>50 data</option>
                            <option value="100" {{ request('per_page') === '100' ? 'selected' : '' }}>100 data</option>
                            <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>⚡ Semua data</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-1">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['status', 'rombel_id', 'search', 'sort', 'per_page']))
                        <a href="{{ route('ekstrakurikuler.enrollment.index', $ekstrakurikuler) }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-x-circle"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Enrollment List -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    Daftar Siswa Terdaftar
                    @if(request('per_page') === 'all')
                        <span class="badge bg-warning text-dark ms-2"><i class="bi bi-lightning-fill me-1"></i>Semua Data</span>
                    @endif
                </h6>
                @can('update', $ekstrakurikuler)
                @if($enrollments->isNotEmpty())
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i> Aksi Bulk
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Status</h6></li>
                            <li><a class="dropdown-item" href="#" onclick="showBulkModal('activate')">
                                <i class="bi bi-person-check me-1"></i> Aktifkan
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="showBulkModal('deactivate')">
                                <i class="bi bi-person-dash me-1"></i> Non-aktifkan
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="showBulkModal('graduate')">
                                <i class="bi bi-trophy me-1"></i> Luluskan
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Tindakan</h6></li>
                            <li><a class="dropdown-item text-warning" href="#" onclick="showBulkModal('transfer')">
                                <i class="bi bi-arrow-left-right me-1"></i> Pindah Rombel
                            </a></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="showBulkModal('withdraw')">
                                <i class="bi bi-person-x me-1"></i> Keluarkan Siswa
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="showBulkModal('delete')">
                                <i class="bi bi-trash me-1"></i> Hapus
                            </a></li>
                        </ul>
                    </div>
                @endif
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            @if($enrollments->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Siswa</th>
                                <th>NISN</th>
                                <th>Rombel</th>
                                <th>Status</th>
                                <th>Tgl Daftar</th>
                                <th>Durasi</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input enrollment-checkbox" 
                                               value="{{ $enrollment->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <span class="text-white small">{{ substr($enrollment->siswa->nama_lengkap, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $enrollment->siswa->nama_lengkap }}</h6>
                                                @if($enrollment->catatan)
                                                    <small class="text-muted">{{ Str::limit($enrollment->catatan, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $enrollment->siswa->nisn ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $enrollment->rombel->nama_rombel }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'aktif' ? 'success' : ($enrollment->status === 'lulus' ? 'info' : 'secondary') }}">
                                            {{ $enrollment->status_label }}
                                        </span>
                                    </td>
                                    <td>{{ $enrollment->tanggal_daftar->format('d/m/Y') }}</td>
                                    <td>{{ $enrollment->durasi_enrollment }} hari</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('ekstrakurikuler.enrollment.show', [$ekstrakurikuler, $enrollment]) }}" 
                                               class="btn btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @can('update', $ekstrakurikuler)
                                                <a href="{{ route('ekstrakurikuler.enrollment.edit', [$ekstrakurikuler, $enrollment]) }}" 
                                                   class="btn btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <h5 class="text-muted mt-2">Belum ada siswa terdaftar</h5>
                    <p class="text-muted">Klik tombol "Daftarkan Siswa" untuk menambahkan siswa ke program ini.</p>
                </div>
            @endif
        </div>
        <x-pagination-wrapper :paginator="$enrollments->withQueryString()" />
    </div>
</div>

@push('modals')
<!-- Bulk Import by Rombel Modal -->
<div class="modal fade" id="bulkImportRombelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('ekstrakurikuler.enrollment.bulk-import-rombel', $ekstrakurikuler) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-people-fill me-2"></i>Daftarkan Siswa dari Kelas Sekolah
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->bulk_import->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->bulk_import->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Fitur ini akan mendaftarkan semua siswa dari rombel (kelas) tertentu yang belum terdaftar dalam program ekstrakurikuler ini.</small>
                    </div>

                    <!-- Rombel Selection -->
                    <div class="mb-3">
                        <label for="rombel_select" class="form-label">
                            <i class="bi bi-collection me-1"></i>Pilih Rombel <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('rombel', 'bulk_import') is-invalid @enderror" id="rombel_select" name="rombel" required>
                            <option value="">Memuat daftar rombel...</option>
                        </select>
                        @error('rombel', 'bulk_import')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Pilih rombel/kelas yang siswanya akan didaftarkan.</div>
                    </div>

                    <!-- Ekstrakurikuler Rombel Selection -->
                    <div class="mb-3">
                        <label for="bulk_ekstrakurikuler_rombel_id" class="form-label">
                            <i class="bi bi-people me-1"></i>Rombel Ekstrakurikuler <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('ekstrakurikuler_rombel_id', 'bulk_import') is-invalid @enderror" id="bulk_ekstrakurikuler_rombel_id" name="ekstrakurikuler_rombel_id" required>
                            <option value="">Pilih Rombel Ekstrakurikuler...</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" 
                                        data-capacity="{{ $rombel->jumlah_siswa }}"
                                        data-current="{{ $rombel->getJumlahSiswaAktual() }}">
                                    {{ $rombel->nama_rombel }} 
                                    ({{ $rombel->getJumlahSiswaAktual() }} siswa)
                                </option>
                            @endforeach
                        </select>
                        @error('ekstrakurikuler_rombel_id', 'bulk_import')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Pilih rombel ekstrakurikuler tempat siswa akan ditempatkan.</div>
                    </div>

                    <!-- Registration Date -->
                    <div class="mb-3">
                        <label for="bulk_tanggal_daftar" class="form-label">
                            <i class="bi bi-calendar me-1"></i>Tanggal Pendaftaran <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control datepicker @error('tanggal_daftar', 'bulk_import') is-invalid @enderror" id="bulk_tanggal_daftar" name="tanggal_daftar" 
                               value="{{ old('tanggal_daftar', now()->format('Y-m-d')) }}" placeholder="DD-MM-YYYY" required>
                        @error('tanggal_daftar', 'bulk_import')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="bulk_catatan" class="form-label">
                            <i class="bi bi-journal-text me-1"></i>Catatan (Opsional)
                        </label>
                        <textarea class="form-control @error('catatan', 'bulk_import') is-invalid @enderror" id="bulk_catatan" name="catatan" rows="3" 
                                  placeholder="Catatan untuk import rombel ini...">{{ old('catatan') }}</textarea>
                        @error('catatan', 'bulk_import')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Catatan akan disimpan untuk semua siswa yang didaftarkan.</div>
                    </div>

                    <!-- Preview -->
                    <div id="rombelPreview" class="alert alert-light" style="display: none;">
                        <h6><i class="bi bi-eye me-1"></i>Preview:</h6>
                        <p class="mb-0" id="previewText"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="bulkImportBtn" disabled>
                        <i class="bi bi-people-fill me-1"></i>Import Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bulkActionForm" method="POST" action="{{ route('ekstrakurikuler.enrollment.bulk-action', $ekstrakurikuler) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionTitle">Aksi Bulk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="bulkAction">
                    <input type="hidden" name="enrollment_ids" id="enrollmentIds">
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    
                    <div id="bulkActionContent"></div>
                    
                    {{-- Alasan (untuk deactivate & withdraw) --}}
                    <div class="form-group mt-3" id="bulkReasonGroup" style="display: none;">
                        <label for="bulk_alasan" class="form-label">Alasan <span class="text-danger" id="bulk_alasan_required"></span></label>
                        <textarea class="form-control" id="bulk_alasan" name="bulk_alasan" rows="3"
                                  placeholder="Masukkan alasan..."></textarea>
                    </div>

                    {{-- Pilih Rombel Tujuan (untuk transfer) --}}
                    <div class="form-group mt-3" id="bulkRombelGroup" style="display: none;">
                        <label for="bulk_rombel_tujuan" class="form-label">Rombel Tujuan <span class="text-danger">*</span></label>
                        <select class="form-select" id="bulk_rombel_tujuan" name="bulk_rombel_tujuan">
                            <option value="">Pilih Rombel Tujuan...</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}">
                                    {{ $rombel->nama_rombel }} ({{ $rombel->getJumlahSiswaAktual() }} siswa)
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Siswa yang dipilih akan dipindahkan ke rombel ini. Hanya siswa berstatus <strong>Aktif</strong> yang akan diproses.</div>
                        <div class="mt-2">
                            <label for="bulk_alasan_transfer" class="form-label">Alasan Pindah (Opsional)</label>
                            <textarea class="form-control" id="bulk_alasan_transfer" name="bulk_alasan" rows="2"
                                      placeholder="Alasan pemindahan rombel..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="bulkActionBtn">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Import Siswa Program Modal -->
<div class="modal fade" id="importSiswaProgramModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('ekstrakurikuler.enrollment.import', $ekstrakurikuler) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-file-earmark-excel me-2"></i>Import Siswa ke Program (Bulk Rombel)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file_excel_program" class="form-label">File Excel/CSV (.xlsx, .csv)</label>
                        <input type="file" class="form-control" id="file_excel_program" name="file" required accept=".xlsx,.xls,.csv" data-max-size="5242880">
                        <div class="form-text mt-1">
                            Format: .xlsx, .xls, .csv | Maksimal: 5MB
                        </div>
                        <div class="mt-2">
                            <span class="fw-semibold text-dark small me-2">Unduh Template:</span>
                            <a href="{{ asset('templates/Template_Import_Siswa_Program.csv') }}" class="btn btn-sm btn-outline-info text-decoration-none py-1 px-2" style="font-size: 0.75rem;"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Template CSV</a>
                        </div>
                        <small class="text-muted d-block mt-1">Kolom: nama_lengkap, nisn, kelas_akademik, no_hp_orangtua, target_rombel_ekskul</small>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle me-1"></i> Sistem akan mencocokkan target rombel ekskul (misal: "Rombel 1"). Jika siswa belum ada di database, data siswa akan dibuat baru.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Import ke Program</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const enrollmentCheckboxes = document.querySelectorAll('.enrollment-checkbox');
    
    selectAllCheckbox?.addEventListener('change', function() {
        enrollmentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    enrollmentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.enrollment-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === enrollmentCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < enrollmentCheckboxes.length;
        });
    });
});

function showBulkModal(action) {
    const checkedBoxes = document.querySelectorAll('.enrollment-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('Pilih minimal satu siswa untuk diproses.');
        return;
    }
    
    const enrollmentIds = Array.from(checkedBoxes).map(cb => cb.value);
    const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
    
    document.getElementById('bulkAction').value = action;
    document.getElementById('enrollmentIds').value = enrollmentIds.join(',');
    
    const titles = {
        'activate':   'Aktifkan Siswa',
        'deactivate': 'Non-aktifkan Siswa', 
        'graduate':   'Luluskan Siswa',
        'delete':     'Hapus Enrollment',
        'withdraw':   'Keluarkan Siswa dari Program',
        'transfer':   'Pindah Rombel',
    };
    
    const contents = {
        'activate':   `<p>Yakin ingin mengaktifkan <strong>${checkedBoxes.length}</strong> siswa yang dipilih?</p>`,
        'deactivate': `<p>Yakin ingin menonaktifkan <strong>${checkedBoxes.length}</strong> siswa yang dipilih?</p>`,
        'graduate':   `<p>Yakin ingin meluluskan <strong>${checkedBoxes.length}</strong> siswa yang dipilih?</p>`,
        'delete':     `<p class="text-danger">Yakin ingin menghapus enrollment <strong>${checkedBoxes.length}</strong> siswa yang dipilih? <br><small>Tindakan ini tidak dapat dibatalkan.</small></p>`,
        'withdraw':   `<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Anda akan mengeluarkan <strong>${checkedBoxes.length}</strong> siswa dari program. Hanya siswa berstatus <strong>Aktif</strong> yang akan diproses.</div>`,
        'transfer':   `<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Memindahkan <strong>${checkedBoxes.length}</strong> siswa ke rombel lain. Hanya siswa berstatus <strong>Aktif</strong> yang akan diproses.</div>`,
    };
    
    document.getElementById('bulkActionTitle').textContent = titles[action];
    document.getElementById('bulkActionContent').innerHTML = contents[action];
    
    // Tampilkan/sembunyikan field alasan
    const reasonGroup = document.getElementById('bulkReasonGroup');
    const rombelGroup = document.getElementById('bulkRombelGroup');
    
    reasonGroup.style.display  = ['deactivate', 'withdraw'].includes(action) ? 'block' : 'none';
    rombelGroup.style.display  = (action === 'transfer') ? 'block' : 'none';

    // Wajib isi alasan untuk withdraw
    const requiredMark = document.getElementById('bulk_alasan_required');
    if (requiredMark) requiredMark.textContent = (action === 'withdraw') ? '*' : '';
    document.getElementById('bulk_alasan').required = (action === 'withdraw');

    // Warna tombol
    const btnClass = {
        'delete': 'btn btn-danger',
        'withdraw': 'btn btn-danger',
        'transfer': 'btn btn-warning',
        'graduate': 'btn btn-info',
        'activate': 'btn btn-success',
        'deactivate': 'btn btn-secondary',
    };
    document.getElementById('bulkActionBtn').className = btnClass[action] || 'btn btn-primary';
    
    modal.show();
}

// Bulk Import by Rombel functionality
document.addEventListener('DOMContentLoaded', function() {
    const bulkImportModal = document.getElementById('bulkImportRombelModal');
    const rombelSelect = document.getElementById('rombel_select');
    const ekstrakurikulerRombelSelect = document.getElementById('bulk_ekstrakurikuler_rombel_id');
    const bulkImportBtn = document.getElementById('bulkImportBtn');
    const rombelPreview = document.getElementById('rombelPreview');
    const previewText = document.getElementById('previewText');

    // Load available rombels when modal is opened
    bulkImportModal.addEventListener('show.bs.modal', function () {
        loadAvailableRombels();
    });

    // Load rombels from API
    function loadAvailableRombels() {
        fetch('{{ route('ekstrakurikuler.enrollment.available-rombels', $ekstrakurikuler, false) }}')
            .then(response => response.json())
            .then(rombels => {
                rombelSelect.innerHTML = '<option value="">Pilih Rombel...</option>';
                rombels.forEach(rombel => {
                    const option = document.createElement('option');
                    option.value = rombel;
                    option.textContent = rombel;
                    rombelSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading rombels:', error);
                rombelSelect.innerHTML = '<option value="">Error memuat rombel</option>';
            });
    }

    // Update preview and button state when selections change
    function updateBulkImportPreview() {
        const selectedRombel = rombelSelect.value;
        const selectedEkstrakurikulerRombel = ekstrakurikulerRombelSelect.selectedOptions[0];
        
        if (selectedRombel && selectedEkstrakurikulerRombel && selectedEkstrakurikulerRombel.value) {
            const capacity = parseInt(selectedEkstrakurikulerRombel.dataset.capacity);
            const current = parseInt(selectedEkstrakurikulerRombel.dataset.current);
            const available = capacity - current;
            
            previewText.innerHTML = `
                <strong>Rombel:</strong> ${selectedRombel}<br>
                <strong>Tujuan:</strong> ${selectedEkstrakurikulerRombel.textContent}<br>
                <strong>Slot tersedia:</strong> ${available} dari ${capacity}
            `;
            rombelPreview.style.display = 'block';
            
            if (available <= 0) {
                rombelPreview.className = 'alert alert-danger';
                previewText.innerHTML += '<br><strong class="text-danger">⚠️ Rombel ekstrakurikuler sudah penuh!</strong>';
                bulkImportBtn.disabled = true;
            } else if (available <= 5) {
                rombelPreview.className = 'alert alert-warning';
                previewText.innerHTML += '<br><strong class="text-warning">⚠️ Slot hampir penuh!</strong>';
                bulkImportBtn.disabled = false;
            } else {
                rombelPreview.className = 'alert alert-light';
                bulkImportBtn.disabled = false;
            }
        } else {
            rombelPreview.style.display = 'none';
            bulkImportBtn.disabled = true;
        }
    }

    rombelSelect.addEventListener('change', updateBulkImportPreview);
    ekstrakurikulerRombelSelect.addEventListener('change', updateBulkImportPreview);

    // Reset modal when closed
    bulkImportModal.addEventListener('hidden.bs.modal', function () {
        rombelSelect.innerHTML = '<option value="">Memuat daftar rombel...</option>';
        ekstrakurikulerRombelSelect.value = '';
        document.getElementById('bulk_catatan').value = '';
        rombelPreview.style.display = 'none';
        bulkImportBtn.disabled = true;
    });

    // Show modal if there are validation errors
    @if(session('show_bulk_import_modal') && $errors->bulk_import->any())
        const modal = new bootstrap.Modal(bulkImportModal);
        modal.show();
        // Load rombels and restore form values
        loadAvailableRombels();
        setTimeout(() => {
            if ('{{ old('rombel') }}') {
                rombelSelect.value = '{{ old('rombel') }}';
            }
            if ('{{ old('ekstrakurikuler_rombel_id') }}') {
                ekstrakurikulerRombelSelect.value = '{{ old('ekstrakurikuler_rombel_id') }}';
            }
            updateBulkImportPreview();
        }, 500);
    @endif
});
</script>
@endpush

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
@endsection