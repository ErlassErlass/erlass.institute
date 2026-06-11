@extends('layouts.app')

@section('title', 'Manajemen Ekstrakurikuler')

@push('styles')
<style>
    .stat-card { border-radius: 8px; transition: transform 0.3s ease; }
    .stat-icon { font-size: 2rem; opacity: 0.7; }
    .quick-filter-btn { border-radius: 20px; padding: 5px 15px; font-size: 0.85rem; }
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .badge-custom { padding: 5px 10px; font-weight: normal; font-size: 0.8rem; }
    .clear-date { position: absolute; right: 5px; top: 50%; transform: translateY(-50%); z-index: 10; display: none; padding: 0 8px; }
    .input-group { position: relative; }
    .progress-bar-container { width: 100px; height: 6px; background-color: #e9ecef; border-radius: 3px; overflow: hidden; }
    .progress-bar { height: 100%; border-radius: 3px; transition: width 0.3s ease; }
    .filter-section { background: #f8f9fa; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
    .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; border: 1px solid #e2e8f0; background: white; color: #64748b; }
    .btn-action:hover { background: #f8fafc; color: var(--primary-color); border-color: var(--primary-color); }
    .btn-action.view:hover { color: var(--secondary-color); border-color: var(--secondary-color); }
    .btn-action.delete:hover { color: var(--danger-color); border-color: var(--danger-color); }
    .btn-action.cancel-trigger:hover { color: var(--danger-color); border-color: var(--danger-color); }
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
                <div class="col-xl-4 col-md-4 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Program</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300 stat-icon"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-4 mb-4">
                    <div class="card border-left-success shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Program Aktif</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['aktif'] }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-play-circle fa-2x text-gray-300 stat-icon"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-4 mb-4">
                    <div class="card border-left-info shadow h-100 py-2 stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Program Selesai</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['selesai'] }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300 stat-icon"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Filter & Pencarian</h6></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('ekstrakurikuler.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="search" class="form-label text-muted small">Pencarian</label>
                                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nama program atau sekolah...">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="region" class="form-label text-muted small">Region</label>
                                <select class="form-control" id="region" name="region">
                                    <option value="">Semua Region</option>
                                    @foreach($regions as $region)
                                    <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="kota" class="form-label text-muted small">Kota</label>
                                <select class="form-control" id="kota" name="kota">
                                    <option value="">Semua Kota</option>
                                    @foreach($kotaOptions as $kota)
                                    <option value="{{ $kota }}" {{ request('kota') == $kota ? 'selected' : '' }}>{{ $kota }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="sekolah_kodlan" class="form-label text-muted small">Sekolah</label>
                                <select class="form-control" id="sekolah_kodlan" name="sekolah_kodlan">
                                    <option value="">Semua Sekolah</option>
                                    @foreach($sekolahs as $sekolah)
                                    <option value="{{ $sekolah->kodlan }}" {{ request('sekolah_kodlan') == $sekolah->kodlan ? 'selected' : '' }}>{{ $sekolah->namasekolah }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_range" class="form-label text-muted small">Rentang Tanggal</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="date_range" name="date_range" value="{{ request('date_range') }}" placeholder="Pilih tanggal...">
                                    <button type="button" class="btn btn-outline-secondary clear-date" onclick="clearDateRange()" style="display: none;"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search me-1"></i> Filter</button>
                            <a href="{{ route('ekstrakurikuler.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Daftar Program</h6></div>
                <div class="card-body">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Program</th>
                                    <th>Sekolah</th>
                                    <th>Siswa</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ekstrakurikulers as $ekstrakurikuler)
                                <tr>
                                    <td>{{ $ekstrakurikulers->firstItem() + $loop->index }}</td>
                                    <td><div class="fw-bold">{{ $ekstrakurikuler->kategori_program }}</div></td>
                                    <td><div class="fw-bold">{{ $ekstrakurikuler->sekolah?->namasekolah ?? '-' }}</div><small class="text-muted">{{ $ekstrakurikuler->sekolah?->kota ?? '-' }}</small></td>
                                    <td class="text-center">{{ $ekstrakurikuler->total_siswa ?? 0 }}</td>
                                    <td>
                                        @php
                                        $statusClass = match($ekstrakurikuler->status) {
                                            'aktif' => 'bg-success',
                                            'selesai' => 'bg-primary',
                                            'dibatalkan' => 'bg-dark',
                                            'diajukan' => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $ekstrakurikuler->status_label }}</span>
                                    </td>
                                    <td>
                                        @php $progress = $ekstrakurikuler->getProgressPertemuan(); @endphp
                                        <div class="progress-bar-container"><div class="progress-bar bg-success" style="width: {{ $progress['persentase'] }}%"></div></div>
                                        <small class="text-muted">{{ $progress['persentase'] }}%</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('ekstrakurikuler.show', $ekstrakurikuler) }}" class="btn-action view" title="Detail"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('ekstrakurikuler.edit', $ekstrakurikuler) }}" class="btn-action edit" title="Edit"><i class="bi bi-pencil"></i></a>
                                            
                                            @can('cancel', $ekstrakurikuler)
                                            @if($ekstrakurikuler->status === \App\Models\Ekstrakurikuler::STATUS_AKTIF)
                                            <button type="button" class="btn-action cancel-trigger" title="Batalkan" 
                                                    onclick="prepareCancellation('{{ route('ekstrakurikuler.cancel', $ekstrakurikuler) }}')"
                                                    >
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                            @endif
                                            @endcan

                                            @can('delete', $ekstrakurikuler)
                                            @if(!$ekstrakurikuler->isActive())
                                            <form action="{{ route('ekstrakurikuler.destroy', $ekstrakurikuler) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action cancel-trigger" onclick="return confirm('Hapus program?')"><i class="bi bi-trash"></i></button>
                                            </form>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-4">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View -->
                    <div class="d-md-none">
                        @foreach($ekstrakurikulers as $ekstrakurikuler)
                        <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-1">{{ $ekstrakurikuler->kategori_program }}</h6>
                                <p class="small mb-2">{{ $ekstrakurikuler->sekolah?->namasekolah }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge {{ $statusClass ?? 'bg-secondary' }}">{{ $ekstrakurikuler->status_label }}</span>
                                    <div class="btn-group">
                                        <a href="{{ route('ekstrakurikuler.show', $ekstrakurikuler) }}" class="btn btn-sm btn-outline-info">Detail</a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"></button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('ekstrakurikuler.edit', $ekstrakurikuler) }}">Edit</a></li>
                                            @can('cancel', $ekstrakurikuler)
                                            @if($ekstrakurikuler->status === \App\Models\Ekstrakurikuler::STATUS_AKTIF)
                                            <li><button type="button" class="dropdown-item text-danger" onclick="prepareCancellation('{{ route('ekstrakurikuler.cancel', $ekstrakurikuler) }}')" >Batalkan Program</button></li>
                                            @endif
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-3">{{ $ekstrakurikulers->appends(request()->query())->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    function prepareCancellation(actionUrl) {
        const form = document.getElementById('formBatalkanProgram');
        if (form) { form.action = actionUrl; }
        const modalEl = document.getElementById('modalBatalkanProgram');         if (typeof bootstrap !== 'undefined') {             const modal = new bootstrap.Modal(modalEl);             modal.show();         } else if (typeof $ !== 'undefined') {             $(modalEl).modal('show');         }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr
        const dateRangeInput = document.getElementById('date_range');
        if (dateRangeInput && typeof flatpickr !== 'undefined') {
            flatpickr(dateRangeInput, { mode: 'range', dateFormat: 'd/m/Y', locale: 'id' });
        }
        
        // Auto-submit filter
        const selects = document.querySelectorAll('#region, #kota, #sekolah_kodlan');
        selects.forEach(select => { select.addEventListener('change', () => document.getElementById('filterForm').submit()); });
    });

    function clearDateRange() {
        document.getElementById('date_range').value = '';
        document.getElementById('filterForm').submit();
    }
</script>
@endpush

@push('modals')
@push('modals')
<!-- Modal Pembatalan -->
<div class="modal fade" id="modalBatalkanProgram" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formBatalkanProgram" action="" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Pembatalan Program</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning small"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tindakan ini akan menghentikan semua sesi mendatang dan mengeluarkan semua siswa.</div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required placeholder="Siswa tidak mencukupi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Konfirmasi Pembatalan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush
