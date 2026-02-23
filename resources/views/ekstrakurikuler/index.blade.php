@extends('layouts.app')

@section('title', 'Manajemen Ekstrakurikuler')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card {
        border-radius: 8px;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        /* Transform animation removed for cleaner interface */
    }

    .stat-icon {
        font-size: 2rem;
        opacity: 0.7;
    }

    .quick-filter-btn {
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 0.85rem;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .badge-custom {
        padding: 5px 10px;
        font-weight: normal;
        font-size: 0.8rem;
    }

    .clear-date {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        display: none;
        padding: 0 8px;
    }

    .input-group {
        position: relative;
    }

    .progress-bar-container {
        width: 100px;
        height: 6px;
        background-color: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 text-gray-800">Manajemen Ekstrakurikuler</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Ekstrakurikuler</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @can('create', App\Models\Ekstrakurikuler::class)
                    <a href="{{ route('ekstrakurikuler.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Program Baru
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Statistics Cards -->

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Program</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300 stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Program Aktif</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['aktif'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-play-circle fa-2x text-gray-300 stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Menunggu Persetujuan</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['diajukan'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300 stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Program Selesai</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['selesai'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300 stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filter & Pencarian</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('ekstrakurikuler.index') }}" id="filterForm">
                        <div class="row">
                            <!-- Search -->
                            <div class="col-md-3 mb-3">
                                <label for="search" class="form-label">Pencarian</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Nama program atau sekolah...">
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-2 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Region Filter -->
                            <div class="col-md-2 mb-3">
                                <label for="region" class="form-label">Region</label>
                                <select class="form-control" id="region" name="region">
                                    <option value="">Semua Region</option>
                                    @foreach($regions as $region)
                                    <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>
                                        {{ $region }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- City Filter -->
                            <div class="col-md-2 mb-3">
                                <label for="kota" class="form-label">Kota</label>
                                <select class="form-control" id="kota" name="kota">
                                    <option value="">Semua Kota</option>
                                    @foreach($kotaOptions as $kota)
                                    <option value="{{ $kota }}" {{ request('kota') == $kota ? 'selected' : '' }}>
                                        {{ $kota }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- School Filter -->
                            <div class="col-md-2 mb-3">
                                <label for="sekolah_kodlan" class="form-label">Sekolah</label>
                                <select class="form-control" id="sekolah_kodlan" name="sekolah_kodlan">
                                    <option value="">Semua Sekolah</option>
                                    @foreach($sekolahs as $sekolah)
                                    <option value="{{ $sekolah->kodlan }}" {{ request('sekolah_kodlan') == $sekolah->kodlan ? 'selected' : '' }}>
                                        {{ $sekolah->namasekolah }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date Range -->
                            <div class="col-md-2 mb-3">
                                <label for="date_range" class="form-label">Rentang Tanggal</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="date_range" name="date_range"
                                        value="{{ request('date_range') }}"
                                        placeholder="Pilih tanggal...">
                                    <button type="button" class="btn btn-outline-secondary clear-date"
                                        onclick="clearDateRange()" style="display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('ekstrakurikuler.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Program Ekstrakurikuler</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Program</th>
                                    <th width="20%">Sekolah</th>
                                    <th width="12%">Kota</th>
                                    <th width="12%">Kecamatan</th>
                                    <th width="8%">Total Siswa</th>
                                    <th width="6%">Rombel</th>
                                    <th width="8%">Status</th>
                                    <th width="6%">Progress</th>
                                    <th width="8%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ekstrakurikulers as $ekstrakurikuler)
                                <tr>
                                    <td>{{ $ekstrakurikulers->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $ekstrakurikuler->kategori_program }}</div>
                                        @if($ekstrakurikuler->deskripsi)
                                        <small class="text-muted">{{ Str::limit($ekstrakurikuler->deskripsi, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $ekstrakurikuler->sekolah?->namasekolah ?? '-' }}</div>
                                        <small class="text-muted">{{ $ekstrakurikuler->sekolah_kodlan }}</small>
                                    </td>
                                    <td>{{ $ekstrakurikuler->sekolah?->kota ?? '-' }}</td>
                                    <td>{{ $ekstrakurikuler->sekolah?->kec ?? '-' }}</td>
                                    <td class="text-center">{{ $ekstrakurikuler->total_siswa ?? 0 }}</td>
                                    <td class="text-center">{{ $ekstrakurikuler->total_rombel ?? 0 }}</td>
                                    <td>
                                        @php
                                        $statusClass = match($ekstrakurikuler->status) {
                                        'draft' => 'bg-secondary',
                                        'diajukan' => 'bg-warning text-dark',
                                        'disetujui' => 'bg-info text-dark',
                                        'ditolak' => 'bg-danger',
                                        'aktif' => 'bg-success',
                                        'selesai' => 'bg-primary',
                                        'dibatalkan' => 'bg-dark',
                                        default => 'bg-secondary'
                                        };
                                        @endphp
                                        <span class="badge {{ $statusClass }} badge-custom">
                                            {{ $ekstrakurikuler->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                        $progress = $ekstrakurikuler->getProgressPertemuan();
                                        $percentage = $progress['persentase'];
                                        @endphp
                                        <div class="progress-bar-container">
                                            <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $percentage }}%</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="btn-group-custom">
                                                @can('view', $ekstrakurikuler)
                                                <a href="{{ route('ekstrakurikuler.show', $ekstrakurikuler) }}"
                                                    class="btn-action view" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @endcan

                                                @can('update', $ekstrakurikuler)
                                                <a href="{{ route('ekstrakurikuler.edit', $ekstrakurikuler) }}"
                                                    class="btn-action edit" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @endcan

                                                @can('delete', $ekstrakurikuler)
                                                @if(!$ekstrakurikuler->isActive())
                                                <form action="{{ route('ekstrakurikuler.destroy', $ekstrakurikuler) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action delete"
                                                        title="Hapus"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus program ini?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                @endcan
                                            </div>

                                            {{-- Special Actions outside the main group --}}
                                            @can('approve', $ekstrakurikuler)
                                            @if($ekstrakurikuler->canBeApproved())
                                            <form action="{{ route('ekstrakurikuler.approve', $ekstrakurikuler) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3"
                                                    title="Setujui"
                                                    onclick="return confirm('Apakah Anda yakin ingin menyetujui program ini?')">
                                                    <i class="bi bi-check-lg me-1"></i> Approve
                                                </button>
                                            </form>
                                            @endif
                                            @endcan

                                            @can('activate', $ekstrakurikuler)
                                            @if($ekstrakurikuler->canBeActivated())
                                            <form action="{{ route('ekstrakurikuler.activate', $ekstrakurikuler) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3"
                                                    title="Aktifkan"
                                                    onclick="return confirm('Apakah Anda yakin ingin mengaktifkan program ini?')">
                                                    <i class="bi bi-play-fill me-1"></i> Aktifkan
                                                </button>
                                            </form>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>Belum ada data program ekstrakurikuler.</p>
                                            @can('create', App\Models\Ekstrakurikuler::class)
                                            <a href="{{ route('ekstrakurikuler.create') }}" class="btn btn-primary">
                                                Tambah Program Pertama
                                            </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-md-none">
                        @forelse($ekstrakurikulers as $ekstrakurikuler)
                        <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-primary">{{ $ekstrakurikuler->kategori_program }}</h6>
                                        <small class="text-muted font-monospace">{{ $ekstrakurikuler->sekolah_kodlan }}</small>
                                    </div>
                                    @php
                                    $statusClass = match($ekstrakurikuler->status) {
                                    'draft' => 'bg-secondary',
                                    'diajukan' => 'bg-warning text-dark',
                                    'disetujui' => 'bg-info text-dark',
                                    'ditolak' => 'bg-danger',
                                    'aktif' => 'bg-success',
                                    'selesai' => 'bg-primary',
                                    'dibatalkan' => 'bg-dark',
                                    default => 'bg-secondary'
                                    };
                                    @endphp
                                    <span class="badge {{ $statusClass }} rounded-pill">
                                        {{ $ekstrakurikuler->status_label }}
                                    </span>
                                </div>

                                <div class="mb-2">
                                    <div class="fw-semibold text-dark">{{ $ekstrakurikuler->sekolah?->namasekolah ?? '-' }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $ekstrakurikuler->sekolah?->kota ?? '-' }}, {{ $ekstrakurikuler->sekolah?->kec ?? '-' }}</small>
                                </div>

                                <div class="row g-2 small mb-3 bg-light p-2 rounded">
                                    <div class="col-4 text-center border-end">
                                        <div class="fw-bold">{{ $ekstrakurikuler->total_siswa ?? 0 }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Siswa</div>
                                    </div>
                                    <div class="col-4 text-center border-end">
                                        <div class="fw-bold">{{ $ekstrakurikuler->total_rombel ?? 0 }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Rombel</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        @php
                                        $progress = $ekstrakurikuler->getProgressPertemuan();
                                        $percentage = $progress['persentase'];
                                        @endphp
                                        <div class="fw-bold text-success">{{ $percentage }}%</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Progress</div>
                                    </div>
                                </div>

                                <div class="btn-group w-100">
                                    @can('view', $ekstrakurikuler)
                                    <a href="{{ route('ekstrakurikuler.show', $ekstrakurikuler) }}" class="btn btn-sm btn-outline-info">Detail</a>
                                    @endcan
                                    
                                    @can('update', $ekstrakurikuler)
                                    <a href="{{ route('ekstrakurikuler.edit', $ekstrakurikuler) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @endcan

                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                        @can('approve', $ekstrakurikuler)
                                        @if($ekstrakurikuler->canBeApproved())
                                        <li>
                                            <form action="{{ route('ekstrakurikuler.approve', $ekstrakurikuler) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item text-success" onclick="return confirm('Approve program?')">
                                                    <i class="bi bi-check-lg me-2"></i> Approve
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        @endcan

                                        @can('activate', $ekstrakurikuler)
                                        @if($ekstrakurikuler->canBeActivated())
                                        <li>
                                            <form action="{{ route('ekstrakurikuler.activate', $ekstrakurikuler) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item text-primary" onclick="return confirm('Aktifkan program?')">
                                                    <i class="bi bi-play-fill me-2"></i> Aktifkan
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        @endcan

                                        @can('delete', $ekstrakurikuler)
                                        @if(!$ekstrakurikuler->isActive())
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('ekstrakurikuler.destroy', $ekstrakurikuler) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Hapus program?')">
                                                    <i class="bi bi-trash me-2"></i> Hapus
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        @endcan
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 bg-white rounded shadow-sm">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="mb-0 fw-bold">Belum ada data</p>
                            @can('create', App\Models\Ekstrakurikuler::class)
                            <a href="{{ route('ekstrakurikuler.create') }}" class="btn btn-sm btn-primary mt-2">Tambah Baru</a>
                            @endcan
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Menampilkan {{ $ekstrakurikulers->firstItem() ?? 0 }} hingga {{ $ekstrakurikulers->lastItem() ?? 0 }}
                            dari {{ $ekstrakurikulers->total() }} data
                        </div>
                        {{ $ekstrakurikulers->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ asset('js/modules/ekstrakurikuler-city-filter.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr for date range
        const dateRangeInput = document.getElementById('date_range');
        const clearButton = document.querySelector('.clear-date');

        if (dateRangeInput) {
            flatpickr(dateRangeInput, {
                mode: 'range',
                dateFormat: 'd/m/Y',
                locale: 'id',
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateStr) {
                        clearButton.style.display = 'block';
                    } else {
                        clearButton.style.display = 'none';
                    }
                }
            });

            // Show clear button if there's already a value
            if (dateRangeInput.value) {
                clearButton.style.display = 'block';
            }
        }

        // Auto-submit form on select change
        const selects = document.querySelectorAll('#status, #region, #sekolah_kodlan');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });

        // Search input with debounce
        const searchInput = document.getElementById('search');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 3 || this.value.length === 0) {
                        document.getElementById('filterForm').submit();
                    }
                }, 500);
            });
        }
    });

    function clearDateRange() {
        const dateRangeInput = document.getElementById('date_range');
        const clearButton = document.querySelector('.clear-date');

        dateRangeInput.value = '';
        clearButton.style.display = 'none';
        document.getElementById('filterForm').submit();
    }

    // Quick status filters
    function filterByStatus(status) {
        const form = document.getElementById('filterForm');
        const statusSelect = document.getElementById('status');
        statusSelect.value = status;
        form.submit();
    }

    // Confirmation for actions
    function confirmAction(message) {
        return confirm(message);
    }

    // Prevent DataTables auto-initialization on this page
    // since we're using manual pagination
    $(document).ready(function() {
        // Disable DataTables auto-initialization for this specific table
        $.fn.dataTable.ext.errMode = 'none'; // Silent error mode
        
        // Initialize city filter for dynamic school loading
        if (typeof EkstrakurikulerCityFilter !== 'undefined') {
            new EkstrakurikulerCityFilter();
        }
    });
</script>
@endpush