@extends('layouts.app')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .text-gradient-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
    }

    .table-modern thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
    }

    .table-modern tbody td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .table-modern tbody tr:hover {
        background-color: #f8fafc;
    }
    
    .avatar-circle {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('sekolah.distribusi') }}" class="btn btn-link text-decoration-none p-0 mb-2 text-muted fw-bold small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Distribusi
            </a>
            <h1 class="h3 fw-bold text-gray-800 mb-1">
                Data Siswa <span class="text-gradient-primary">{{ $sekolah->namasekolah }}</span>
            </h1>
            <p class="text-muted mb-0">
                <i class="bi bi-geo-alt me-1"></i> {{ $sekolah->kotkab ?? 'Lokasi Sekolah' }} • 
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ $sekolah->siswa->count() }} Siswa</span>
            </p>
        </div>
        <div class="d-none d-md-block">
            <div class="p-3 bg-white rounded-4 shadow-sm border border-light text-center" style="min-width: 120px;">
                <span class="d-block text-muted small text-uppercase fw-bold ls-1">Total Valid</span>
                <span class="h2 fw-bold text-dark mb-0 d-block lh-1">{{ $sekolah->siswa->count() }}</span>
            </div>
        </div>
    </div>

    @if($sekolah->siswa->count() > 0)
        <div class="card glass-card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0 datatable" id="siswa-sekolah-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="35%">Nama Siswa</th>
                                <th width="20%">Rombel</th>
                                <th width="20%">Kelas</th>
                                <th width="20%">Jenis Kelamin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sekolah->siswa as $index => $siswa)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle shadow-sm">
                                            {{ substr($siswa->nama_lengkap, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $siswa->nama_lengkap }}</div>
                                            <div class="small text-muted">ID: {{ $siswa->nisn ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $siswa->rombel }}</span></td>
                                <td class="text-muted">{{ $siswa->kelas }}</td>
                                <td>
                                    @if(strtolower($siswa->jenis_kelamin) == 'l' || strtolower($siswa->jenis_kelamin) == 'laki-laki')
                                        <span class="badge bg-blue-50 text-blue-600 rounded-pill px-3 py-2 border border-blue-100">
                                            <i class="bi bi-gender-male me-1"></i> Laki-laki
                                        </span>
                                    @else
                                        <span class="badge bg-pink-50 text-pink-600 rounded-pill px-3 py-2 border border-pink-100">
                                            <i class="bi bi-gender-female me-1"></i> Perempuan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="row min-vh-50 align-items-center justify-content-center">
            <div class="col-md-6 text-center">
                <div class="mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <i class="bi bi-people text-muted" style="font-size: 3rem; opacity: 0.3"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-gray-800">Belum Ada Siswa</h4>
                <p class="text-muted">Sekolah ini belum memiliki data siswa yang terdaftar.</p>
                <a href="{{ route('sekolah.distribusi') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable for Students by School table
        if (typeof window.DataTableManager !== 'undefined') {
            const dataTableManager = new window.DataTableManager();
            dataTableManager.init('#siswa-sekolah-table', {
                order: [[1, 'asc']], // Sort by Student Name column
                columnDefs: [
                    { orderable: false, targets: [0] }, // Disable sorting for No. column
                    { type: 'string', targets: [1, 2, 3, 4] } // String sorting for all other columns
                ],
                pageLength: 25
            });
        }
    });
</script>
@endpush
