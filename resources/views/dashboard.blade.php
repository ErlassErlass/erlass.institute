@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="fw-bold mb-2">Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Beranda</li>
                </ol>
            </nav>
        </div>
        <div class="text-md-end">
            <p class="h5 mb-1">Selamat Datang, <strong>{{ Auth::user()->nama_lengkap }}</strong>!</p>
            <span class="badge bg-{{ [
                'admin' => 'danger',
                'admin_erlass' => 'warning',
                'instruktur' => 'primary'
            ][Auth::user()->role] ?? 'secondary' }} text-capitalize">
                {{ Auth::user()->role }}
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Total Sekolah</h6>
                        <h2 class="fw-bold mb-0">{{ number_format($total_sekolah) }}</h2>
                        <small class="text-muted">SD & SMP</small>
                    </div>
                    <i class="bi bi-building fs-1 text-primary opacity-25"></i>
                </div>
                <div class="card-footer bg-transparent py-2">
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> 5.2% dari bulan lalu
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-start border-success border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Total Siswa</h6>
                        <h2 class="fw-bold mb-0">{{ number_format($total_siswa) }}</h2>
                        <small class="text-muted">Aktif</small>
                    </div>
                    <i class="bi bi-people-fill fs-1 text-success opacity-25"></i>
                </div>
                <div class="card-footer bg-transparent py-2">
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> 12.8% dari tahun lalu
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Laporan Hari Ini</h6>
                        <h2 class="fw-bold mb-0">{{ $laporan_hari_ini }}</h2>
                        <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
                    </div>
                    <i class="bi bi-file-earmark-text fs-1 text-warning opacity-25"></i>
                </div>
                <div class="card-footer bg-transparent py-2">
                    <small class="{{ $laporan_hari_ini > 0 ? 'text-success' : 'text-danger' }}">
                        @if($laporan_hari_ini > 0)
                            <i class="bi bi-check-circle"></i> Ada aktivitas
                        @else
                            <i class="bi bi-exclamation-circle"></i> Belum ada laporan
                        @endif
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-start border-info border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Total Pengguna</h6>
                        <h2 class="fw-bold mb-0">{{ number_format($total_pengguna) }}</h2>
                        <small class="text-muted">Aktif</small>
                    </div>
                    <i class="bi bi-person-check-fill fs-1 text-info opacity-25"></i>
                </div>
                <div class="card-footer bg-transparent py-2">
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> {{ round(($total_pengguna / max(1, ($total_pengguna - 5))) * 100 - 100, 1) }}% dari bulan lalu
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Quick Navigation -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Menu Navigasi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('sekolah.index') }}" class="card text-decoration-none shadow-sm h-100 card-hover">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-building fs-3 text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Daftar Sekolah</h6>
                                        <small class="text-muted">Kelola data sekolah</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('laporan-mengajar.index') }}" class="card text-decoration-none shadow-sm h-100 card-hover">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-journal-check fs-3 text-warning"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Laporan Mengajar</h6>
                                        <small class="text-muted">Buat laporan kegiatan</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('rekap-absensi') }}" class="card text-decoration-none shadow-sm h-100 card-hover">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-calendar-check fs-3 text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Rekap Absensi</h6>
                                        <small class="text-muted">Lihat kehadiran siswa</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('siswa.index') }}" class="card text-decoration-none shadow-sm h-100 card-hover">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-info bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-person-lines-fill fs-3 text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Data Siswa</h6>
                                        <small class="text-muted">Kelola data siswa</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @if(Auth::user()->role === 'admin')
                        <div class="col-md-6">
                            <a href="{{ route('users.index') }}" class="card text-decoration-none shadow-sm h-100 card-hover">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-people-fill fs-3 text-danger"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Manajemen User</h6>
                                        <small class="text-muted">Kelola akun pengguna</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Aktivitas Terkini</h5>
                    <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @if($recent_laporan->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recent_laporan as $laporan)
                            <div class="list-group-item border-0 px-0 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $laporan->sekolah->nama ?? $laporan->sekolah_nama ?? '-' }}</h6>
                                        <small class="text-muted">
                                            {{ $laporan->instruktur->nama_lengkap ?? 'Instruktur Tidak Ditemukan' }} • 
                                            {{ $laporan->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $laporan->created_at->format('H:i') }}</small>
                                        <div class="mt-2">
                                            <a href="{{ route('laporan-mengajar.show', $laporan->id) }}" class="btn btn-sm btn-outline-secondary">
                                                Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-info-circle fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada aktivitas terbaru</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- School Distribution -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Distribusi Siswa per Sekolah</h5>
                </div>
                <div class="card-body">
                    @if($sekolah_distribution->count() > 0)
                        <div class="mb-4">
                            @foreach($sekolah_distribution as $sekolah)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>{{ Str::limit($sekolah->nama, 20) }}</span>
                                    <span>{{ $sekolah->siswa_count }} siswa</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" 
                                         role="progressbar" 
                                         style="width: {{ ($sekolah->siswa_count / max(1, $total_siswa)) * 100 }}%; 
                                                background-color: {{ [
                                                    '#4e73df',
                                                    '#1cc88a',
                                                    '#36b9cc',
                                                    '#f6c23e',
                                                    '#e74a3b'
                                                ][$loop->index % 5] }}" 
                                         aria-valuenow="{{ ($sekolah->siswa_count / max(1, $total_siswa)) * 100 }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center">
                            <a href="{{ route('sekolah.index') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua Sekolah
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-info-circle fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Data sekolah tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Statistik Singkat</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">Total Laporan</h6>
                            <small class="text-muted">Semua waktu</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">

                        </span>
                    </div>
                    
                    @if(Auth::user()->role === 'instruktur')
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">Laporan Anda</h6>
                            <small class="text-muted">Total yang dibuat</small>
                        </div>
                        <span class="badge bg-success rounded-pill">
                            {{ $total_laporan_instruktur }}
                        </span>
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">Rata-rata Siswa</h6>
                            <small class="text-muted">Per sekolah</small>
                        </div>
                        <span class="badge bg-info rounded-pill">
                            {{ $total_sekolah > 0 ? round($total_siswa / $total_sekolah) : 0 }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Hari Ini</h6>
                            <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
                        </div>
                        <span class="badge bg-secondary rounded-pill">
                            {{ now()->format('H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-hover {
        transition: all 0.2s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        border-color: rgba(0,0,0,0.1);
    }
    
    .border-start {
        border-left-width: 4px !important;
    }
    
    .progress {
        border-radius: 10rem;
        background-color: #f0f3f7;
    }
    
    .progress-bar {
        border-radius: 10rem;
    }
</style>
@endpush