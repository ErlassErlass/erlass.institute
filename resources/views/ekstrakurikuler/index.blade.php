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
        transform: translateY(-5px);
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

                            <!-- School Filter -->
                            <div class="col-md-3 mb-3">
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
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Program</th>
                                    <th width="15%">Sekolah</th>
                                    <th width="10%">Region</th>
                                    <th width="10%">Sales</th>
                                    <th width="8%">Total Siswa</th>
                                    <th width="8%">Rombel</th>
                                    <th width="10%">Jadwal</th>
                                    <th width="8%">Status</th>
                                    <th width="6%">Progress</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ekstrakurikulers as $ekstrakurikuler)
                                <tr>
                                    <td>{{ ($ekstrakurikulers->currentPage() - 1) * $ekstrakurikulers->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $ekstrakurikuler->nama_program }}</div>
                                        @if($ekstrakurikuler->deskripsi)
                                            <small class="text-muted">{{ Str::limit($ekstrakurikuler->deskripsi, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $ekstrakurikuler->sekolah?->namasekolah ?? '-' }}</div>
                                        <small class="text-muted">{{ $ekstrakurikuler->sekolah?->kotkab }}, {{ $ekstrakurikuler->sekolah?->kec }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $ekstrakurikuler->region }}</span>
                                    </td>
                                    <td>{{ $ekstrakurikuler->sales?->name ?? '-' }}</td>
                                    <td class="text-center">{{ $ekstrakurikuler->total_siswa }}</td>
                                    <td class="text-center">{{ $ekstrakurikuler->total_rombel }}</td>
                                    <td>
                                        <div>
                                            <small>{{ $ekstrakurikuler->tanggal_mulai ? $ekstrakurikuler->tanggal_mulai->format('d/m/Y') : '-' }}</small>
                                        </div>
                                        <div>
                                            <small>{{ $ekstrakurikuler->tanggal_selesai ? $ekstrakurikuler->tanggal_selesai->format('d/m/Y') : '-' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($ekstrakurikuler->status) {
                                                'draft' => 'badge-secondary',
                                                'diajukan' => 'badge-warning',
                                                'disetujui' => 'badge-info',
                                                'ditolak' => 'badge-danger',
                                                'aktif' => 'badge-success',
                                                'selesai' => 'badge-primary',
                                                'dibatalkan' => 'badge-dark',
                                                default => 'badge-secondary'
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
                                        <div class="btn-group" role="group">
                                            @can('view', $ekstrakurikuler)
                                            <a href="{{ route('ekstrakurikuler.show', $ekstrakurikuler) }}" 
                                               class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan

                                            @can('update', $ekstrakurikuler)
                                            <a href="{{ route('ekstrakurikuler.edit', $ekstrakurikuler) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan

                                            @can('approve', $ekstrakurikuler)
                                                @if($ekstrakurikuler->canBeApproved())
                                                <form action="{{ route('ekstrakurikuler.approve', $ekstrakurikuler) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            title="Setujui" 
                                                            onclick="return confirm('Apakah Anda yakin ingin menyetujui program ini?')">
                                                        <i class="fas fa-check"></i>
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
                                                    <button type="submit" class="btn btn-sm btn-primary" 
                                                            title="Aktifkan" 
                                                            onclick="return confirm('Apakah Anda yakin ingin mengaktifkan program ini?')">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            @endcan

                                            @can('delete', $ekstrakurikuler)
                                                @if(!$ekstrakurikuler->isActive())
                                                <form action="{{ route('ekstrakurikuler.destroy', $ekstrakurikuler) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            title="Hapus" 
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus program ini?')">
                                                        <i class="fas fa-trash"></i>
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
</script>
@endpush