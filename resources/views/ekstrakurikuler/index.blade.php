@extends('layouts.app')

@section('title', 'Manajemen Ekstrakurikuler')

@push('styles')
<style>
    .stat-card { border-radius: 12px; transition: transform 0.3s ease; border: none; }
    .stat-icon { font-size: 2rem; opacity: 0.7; }
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .clear-date { position: absolute; right: 5px; top: 50%; transform: translateY(-50%); z-index: 10; display: none; padding: 0 8px; }
    .input-group { position: relative; }
    .progress-bar-container { width: 100px; height: 6px; background-color: #e9ecef; border-radius: 3px; overflow: hidden; }
    .progress-bar { height: 100%; border-radius: 3px; transition: width 0.3s ease; }
    .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; border: 1px solid #e2e8f0; background: white; color: #64748b; }
    .btn-action:hover { background: #f8fafc; color: var(--primary-color); border-color: var(--primary-color); }
    .btn-action.view:hover { color: var(--secondary-color); border-color: var(--secondary-color); }
    .btn-action.delete:hover { color: var(--danger-color); border-color: var(--danger-color); }
    .btn-action.cancel-trigger:hover { color: var(--danger-color); border-color: var(--danger-color); }

    /* Premium Glassmorphism & Custom elements styling */
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 8px 30px 0 rgba(31, 38, 135, 0.03);
        border-radius: 16px;
        transition: all 0.3s ease;
    }
    .glass-card:hover {
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.06);
    }
    .filter-label {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 0.5rem;
    }
    .form-control-premium {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 0.9rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background-color: #f8fafc;
    }
    .form-control-premium:focus {
        background-color: #fff;
        border-color: var(--primary-color, #4f46e5);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    /* Status Badge styling */
    .pill-badge {
        padding: 6px 12px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
    }
    .pill-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .badge-aktif { background: rgba(34, 197, 94, 0.1); color: #166534; border-color: rgba(34, 197, 94, 0.2); }
    .badge-aktif::before { background: #22c55e; }
    
    .badge-diajukan { background: rgba(234, 179, 8, 0.1); color: #854d0e; border-color: rgba(234, 179, 8, 0.2); }
    .badge-diajukan::before { background: #eab308; }
    
    .badge-selesai { background: rgba(59, 130, 246, 0.1); color: #1e40af; border-color: rgba(59, 130, 246, 0.2); }
    .badge-selesai::before { background: #3b82f6; }
    
    .badge-dibatalkan { background: rgba(100, 116, 139, 0.1); color: #334155; border-color: rgba(100, 116, 139, 0.2); }
    .badge-dibatalkan::before { background: #64748b; }
    
    .badge-draft { background: rgba(148, 163, 184, 0.15); color: #475569; border-color: rgba(148, 163, 184, 0.25); }
    .badge-draft::before { background: #94a3b8; }
    
    .badge-disetujui { background: rgba(16, 185, 129, 0.1); color: #065f46; border-color: rgba(16, 185, 129, 0.2); }
    .badge-disetujui::before { background: #10b981; }

    .badge-ditolak { background: rgba(239, 68, 68, 0.1); color: #991b1b; border-color: rgba(239, 68, 68, 0.2); }
    .badge-ditolak::before { background: #ef4444; }

    /* Table row micro-interactions */
    .table-premium tbody tr {
        transition: all 0.2s ease;
    }
    .table-premium tbody tr:hover {
        background-color: rgba(248, 250, 252, 0.8) !important;
        transform: translateY(-1px);
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
            <div class="card glass-card mb-4 border-0">
                <div class="card-header bg-transparent py-3 border-0"><h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filter & Pencarian</h6></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('ekstrakurikuler.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label for="search" class="filter-label">Pencarian</label>
                                <input type="text" class="form-control form-control-premium" id="search" name="search" value="{{ request('search') }}" placeholder="Nama program atau sekolah...">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="status" class="filter-label">Status</label>
                                <select class="form-control form-control-premium" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label for="kota" class="filter-label">Kota</label>
                                <select class="form-control form-control-premium" id="kota" name="kota">
                                    <option value="">Semua Kota</option>
                                    @foreach($kotaOptions as $kota)
                                    <option value="{{ $kota }}" {{ request('kota') == $kota ? 'selected' : '' }}>{{ $kota }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="date_range" class="filter-label">Rentang Tanggal</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-premium" id="date_range" name="date_range" value="{{ request('date_range') }}" placeholder="Pilih tanggal...">
                                    <button type="button" class="btn btn-outline-secondary clear-date" onclick="clearDateRange()" style="display: none;"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-9 mb-3">
                                <label for="sekolah_kodlan" class="filter-label">Sekolah</label>
                                <select class="form-control form-control-premium" id="sekolah_kodlan" name="sekolah_kodlan">
                                    <option value="">Semua Sekolah</option>
                                    @if(request('sekolah_kodlan'))
                                        @php $reqSekolah = \App\Models\Sekolah::where('kodlan', request('sekolah_kodlan'))->first(); @endphp
                                        @if($reqSekolah)
                                            <option value="{{ request('sekolah_kodlan') }}" selected>{{ $reqSekolah->namasekolah }} ({{ request('sekolah_kodlan') }})</option>
                                        @endif
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-3 d-flex gap-2 justify-content-end">
                                <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px;"><i class="fas fa-search me-1"></i> Filter</button>
                                <a href="{{ route('ekstrakurikuler.index') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 10px;">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card glass-card mb-4 border-0">
                <div class="card-header bg-transparent py-3 border-0"><h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ul me-2"></i>Daftar Program</h6></div>
                <div class="card-body">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover table-premium align-middle" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">No</th>
                                    <th>Program</th>
                                    <th>Sekolah</th>
                                    <th class="text-center">Siswa</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th class="pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ekstrakurikulers as $ekstrakurikuler)
                                <tr>
                                    <td class="ps-3">{{ $ekstrakurikulers->firstItem() + $loop->index }}</td>
                                    <td><div class="fw-bold text-dark">{{ $ekstrakurikuler->kategori_program }}</div></td>
                                    <td><div class="fw-bold text-dark">{{ $ekstrakurikuler->sekolah?->namasekolah ?? '-' }}</div><small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $ekstrakurikuler->sekolah?->kota ?? '-' }}</small></td>
                                    <td class="text-center fw-semibold text-secondary">{{ $ekstrakurikuler->total_siswa ?? 0 }}</td>
                                    <td>
                                        @php
                                        $badgeClass = match($ekstrakurikuler->status) {
                                            'aktif' => 'badge-aktif',
                                            'selesai' => 'badge-selesai',
                                            'dibatalkan' => 'badge-dibatalkan',
                                            'diajukan' => 'badge-diajukan',
                                            'draft' => 'badge-draft',
                                            'disetujui' => 'badge-disetujui',
                                            'ditolak' => 'badge-ditolak',
                                            default => 'badge-draft'
                                        };
                                        @endphp
                                        <span class="pill-badge {{ $badgeClass }}">{{ $ekstrakurikuler->status_label }}</span>
                                    </td>
                                    <td>
                                        @php $progress = $ekstrakurikuler->getProgressPertemuan(); @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress-bar-container"><div class="progress-bar bg-success" style="width: {{ $progress['persentase'] }}%"></div></div>
                                            <small class="text-muted fw-semibold">{{ $progress['persentase'] }}%</small>
                                        </div>
                                    </td>
                                    <td class="pe-3">
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
                                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data program ekstrakurikuler yang sesuai filter.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View -->
                    <div class="d-md-none">
                        @foreach($ekstrakurikulers as $ekstrakurikuler)
                        @php
                        $badgeClass = match($ekstrakurikuler->status) {
                            'aktif' => 'badge-aktif',
                            'selesai' => 'badge-selesai',
                            'dibatalkan' => 'badge-dibatalkan',
                            'diajukan' => 'badge-diajukan',
                            'draft' => 'badge-draft',
                            'disetujui' => 'badge-disetujui',
                            'ditolak' => 'badge-ditolak',
                            default => 'badge-draft'
                        };
                        @endphp
                        <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary" style="border-radius: 8px;">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-1">{{ $ekstrakurikuler->kategori_program }}</h6>
                                <p class="small mb-2 text-dark fw-semibold">{{ $ekstrakurikuler->sekolah?->namasekolah }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="pill-badge {{ $badgeClass }}">{{ $ekstrakurikuler->status_label }}</span>
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

                    <x-pagination-wrapper :paginator="$ekstrakurikulers->appends(request()->query())" class="bg-transparent border-top py-3" />
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
        const selects = document.querySelectorAll('#status, #region, #kota');
        selects.forEach(select => { select.addEventListener('change', () => document.getElementById('filterForm').submit()); });

        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#sekolah_kodlan').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Ketik nama sekolah atau kode...',
                allowClear: true,
                ajax: {
                    url: "{{ route('api.sekolah.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            region: $('#region').val(),
                            kota: $('#kota').val()
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            }).on('change', function() {
                document.getElementById('filterForm').submit();
            });
        }
    });

    function clearDateRange() {
        document.getElementById('date_range').value = '';
        document.getElementById('filterForm').submit();
    }
</script>
@endpush

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
