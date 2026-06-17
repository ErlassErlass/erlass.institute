@extends('layouts.app')

@section('title', 'Detail Program Ekstrakurikuler')

@push('styles')
<style>
    .detail-card {
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .detail-card:hover {
        /* Transform animation removed for cleaner interface */
    }
    
    .status-badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    
    .info-label {
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        color: #495057;
        margin-bottom: 1rem;
    }
    
    .rombel-card {
        border-left: 4px solid #007bff;
        background: #f8f9fa;
    }
    
    .session-card {
        border-radius: 6px;
        border: 1px solid #e9ecef;
        background: white;
        transition: all 0.3s ease;
    }
    
    .session-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .progress-ring {
        width: 60px;
        height: 60px;
        transform: rotate(-90deg);
    }
    
    .progress-ring-circle {
        fill: transparent;
        stroke: #e9ecef;
        stroke-width: 4;
    }
    
    .progress-ring-progress {
        fill: transparent;
        stroke: #28a745;
        stroke-width: 4;
        stroke-linecap: round;
        transition: stroke-dasharray 0.3s ease;
    }
    
    .facility-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
    }
    
    .facility-available {
        background: #d4edda;
        color: #155724;
    }
    
    .facility-unavailable {
        background: #f8d7da;
        color: #721c24;
    }
    
    .facility-unknown {
        background: #fff3cd;
        color: #856404;
    }
    
    .timeline {
        position: relative;
        padding-left: 3rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }
    
    .timeline-marker {
        position: absolute;
        left: -23px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #28a745;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #28a745;
    }
    
    .timeline-marker.pending {
        background: #6c757d;
        box-shadow: 0 0 0 2px #6c757d;
    }
    
    .timeline-marker.current {
        background: #007bff;
        box-shadow: 0 0 0 2px #007bff;
        /* Pulse animation removed for cleaner interface */
    }
    
    /* Pulse keyframe animation removed for cleaner interface */
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 text-gray-800">{{ $ekstrakurikuler->kategori_program }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('ekstrakurikuler.index') }}">Ekstrakurikuler</a></li>
                            <li class="breadcrumb-item active">{{ Str::limit($ekstrakurikuler->kategori_program, 30) }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    @can('update', $ekstrakurikuler)
                    <a href="{{ route('ekstrakurikuler.enrollment.index', $ekstrakurikuler) }}" class="btn btn-info text-white">
                        <i class="fas fa-users"></i> Siswa
                    </a>
                    <a href="{{ route('ekstrakurikuler.edit', $ekstrakurikuler) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endcan
                    
                    @can('approve', $ekstrakurikuler)
                        @if($ekstrakurikuler->canBeApproved())
                        <form action="{{ route('ekstrakurikuler.approve', $ekstrakurikuler) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin menyetujui program ini?')">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                        </form>
                        @endif
                    @endcan
                    
                    @can('activate', $ekstrakurikuler)
                        @if($ekstrakurikuler->canBeActivated())
                        <form action="{{ route('ekstrakurikuler.activate', $ekstrakurikuler) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin mengaktifkan program ini?')">
                                <i class="fas fa-play"></i> Aktifkan
                            </button>
                        </form>
                        @endif
                    @endcan

                    @can('complete', $ekstrakurikuler)
                        @if($ekstrakurikuler->canBeCompleted())
                        <form action="{{ route('ekstrakurikuler.complete', $ekstrakurikuler) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success" onclick="return confirm('Tandai program Ekstrakurikuler ini telah selesai?')">
                                <i class="fas fa-flag-checkered"></i> Selesaikan
                            </button>
                        </form>
                        @endif
                    @endcan
                    
                    <a href="{{ route('ekstrakurikuler.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Status and Progress -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card detail-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">Status Program</h5>
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
                                    <span class="badge {{ $statusClass }} status-badge">
                                        {{ $ekstrakurikuler->status_label }}
                                    </span>
                                    
                                    @if($ekstrakurikuler->tanggal_disetujui)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Disetujui pada: {{ $ekstrakurikuler->tanggal_disetujui->format('d/m/Y H:i') }}
                                            @if($ekstrakurikuler->disetujuiOleh)
                                                oleh {{ $ekstrakurikuler->disetujuiOleh->nama_lengkap }}
                                            @endif
                                        </small>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="text-center">
                                    @php
                                        $progress = $ekstrakurikuler->getProgressPertemuan();
                                        $percentage = $progress['persentase'];
                                        $circumference = 2 * pi() * 26;
                                        $strokeDasharray = ($percentage / 100) * $circumference;
                                    @endphp
                                    <svg class="progress-ring" viewBox="0 0 60 60">
                                        <circle class="progress-ring-circle" cx="30" cy="30" r="26"></circle>
                                        <circle class="progress-ring-progress" cx="30" cy="30" r="26" 
                                                style="stroke-dasharray: {{ $strokeDasharray }} {{ $circumference }}; stroke-dashoffset: 0"></circle>
                                    </svg>
                                    <div class="text-center mt-2">
                                        <strong>{{ $percentage }}%</strong><br>
                                        <small class="text-muted">Progress</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card detail-card">
                        <div class="card-body">
                            <h6><i class="fas fa-chart-bar"></i> Statistik</h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="h4 text-primary">{{ $ekstrakurikuler->total_siswa }}</div>
                                    <small class="text-muted">Siswa</small>
                                </div>
                                <div class="col-4">
                                    <div class="h4 text-success">{{ $ekstrakurikuler->total_rombel }}</div>
                                    <small class="text-muted">Rombel</small>
                                </div>
                                <div class="col-4">
                                    <div class="h4 text-info">{{ $progress['total'] }}</div>
                                    <small class="text-muted">Sesi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Tabs -->
            <div class="card detail-card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="detailTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                <i class="fas fa-info-circle"></i> Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rombel-tab" data-bs-toggle="tab" data-bs-target="#rombel" type="button" role="tab">
                                <i class="fas fa-users"></i> Rombel ({{ $ekstrakurikuler->rombels->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions" type="button" role="tab">
                                <i class="fas fa-calendar-alt"></i> Jadwal ({{ $ekstrakurikuler->sessions->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="facilities-tab" data-bs-toggle="tab" data-bs-target="#facilities" type="button" role="tab">
                                <i class="fas fa-tools"></i> Fasilitas
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="detailTabsContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-info-circle text-primary"></i> Informasi Program</h6>
                                    
                                    <div class="info-label">Nama Program</div>
                                    <div class="info-value">{{ $ekstrakurikuler->kategori_program }}</div>
                                    
                                    @if($ekstrakurikuler->deskripsi)
                                    <div class="info-label">Deskripsi</div>
                                    <div class="info-value">{{ $ekstrakurikuler->deskripsi }}</div>
                                    @endif
                                    
                                    <div class="info-label">Region</div>
                                    <div class="info-value">
                                        <span class="badge badge-secondary">{{ $ekstrakurikuler->region }}</span>
                                    </div>
                                    
                                    <div class="info-label">Sales/Koordinator</div>
                                    <div class="info-value">{{ $ekstrakurikuler->sales?->nama_lengkap ?? '-' }}</div>
                                    
                                </div>
                                
                                <div class="col-md-6">
                                    <h6><i class="fas fa-school text-primary"></i> Detail Sekolah</h6>
                                    
                                    <div class="info-label">Nama Sekolah</div>
                                    <div class="info-value">{{ $ekstrakurikuler->sekolah?->namasekolah ?? '-' }}</div>
                                    
                                    <div class="info-label">Alamat</div>
                                    <div class="info-value">{{ $ekstrakurikuler->alamat_lengkap }}</div>
                                    
                                    <div class="info-label">Jarak dari POP</div>
                                    <div class="info-value">{{ $ekstrakurikuler->jarak_km }} KM</div>
                                    
                                    <div class="info-label">Kepala Sekolah</div>
                                    <div class="info-value">{{ $ekstrakurikuler->kepala_sekolah }}</div>
                                    
                                    <div class="info-label">Penanggung Jawab</div>
                                    <div class="info-value">{{ $ekstrakurikuler->penanggung_jawab }}</div>
                                    
                                    <div class="info-label">No. Telepon</div>
                                    <div class="info-value">
                                        <a href="tel:{{ $ekstrakurikuler->no_telepon }}">{{ $ekstrakurikuler->no_telepon }}</a>
                                    </div>
                                    
                                    @if($ekstrakurikuler->email)
                                    <div class="info-label">Email</div>
                                    <div class="info-value">
                                        <a href="mailto:{{ $ekstrakurikuler->email }}">{{ $ekstrakurikuler->email }}</a>
                                    </div>
                                    @endif
                                    
                                    @if($ekstrakurikuler->google_maps_link)
                                    <div class="info-label">Google Maps</div>
                                    <div class="info-value">
                                        <a href="{{ $ekstrakurikuler->google_maps_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-map-marker-alt"></i> Buka Maps
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6><i class="fas fa-calendar text-primary"></i> Periode Program</h6>
                                    <div class="alert alert-info">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Tanggal Mulai:</strong><br>
                                                {{ $ekstrakurikuler->tanggal_mulai ? $ekstrakurikuler->tanggal_mulai->format('d/m/Y') : '-' }}
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Tanggal Selesai:</strong><br>
                                                {{ $ekstrakurikuler->tanggal_selesai ? $ekstrakurikuler->tanggal_selesai->format('d/m/Y') : '-' }}
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Total Pertemuan:</strong><br>
                                                {{ $ekstrakurikuler->total_pertemuan }} pertemuan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Rombel Tab -->
                        <div class="tab-pane fade" id="rombel" role="tabpanel">
                            <div class="row">
                                @forelse($ekstrakurikuler->rombels as $rombel)
                                <div class="col-md-6 mb-4">
                                    <div class="card rombel-card">
                                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-users"></i> {{ $rombel->nama_rombel }}
                                                <span class="badge badge-light ml-2">{{ $rombel->status_label }}</span>
                                            </h6>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton{{ $rombel->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-cog"></i> Aksi
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $rombel->id }}">
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importSiswaModal{{ $rombel->id }}">
                                                            <i class="fas fa-file-upload text-success me-2"></i> Import Siswa (Excel)
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="info-label">Jumlah Siswa</div>
                                                    <div class="info-value">{{ $rombel->jumlah_siswa }} orang</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="info-label">Total Pertemuan</div>
                                                    <div class="info-value">{{ $rombel->total_pertemuan }}x</div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="info-label">Hari</div>
                                                    <div class="info-value">{{ $rombel->hari_label }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="info-label">Waktu</div>
                                                    <div class="info-value">{{ $rombel->jadwal_waktu }}</div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="info-label">Periode</div>
                                                    <div class="info-value">
                                                        {{ $rombel->tanggal_mulai->format('d/m/Y') }} - 
                                                        {{ $rombel->tanggal_selesai->format('d/m/Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($rombel->ruangan)
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="info-label">Ruangan</div>
                                                    <div class="info-value">{{ $rombel->ruangan }}</div>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            <!-- Progress -->
                                            <div class="mt-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="text-muted">Progress Pertemuan</small>
                                                    <small class="text-muted">{{ $rombel->getProgressPersentase() }}%</small>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success" 
                                                         style="width: {{ $rombel->getProgressPersentase() }}%"></div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $rombel->pertemuan_selesai }} dari {{ $rombel->total_pertemuan }} pertemuan
                                                </small>
                                            </div>
                                            
                                            <!-- Instructors -->
                                            @if($rombel->instruktur || $rombel->asisten)
                                            <div class="mt-3">
                                                <div class="info-label">Tim Pengajar</div>
                                                <div class="info-value">
                                                    @if($rombel->instruktur)
                                                        <span class="badge badge-primary">{{ $rombel->instruktur->nama_lengkap }}</span>
                                                    @endif
                                                    @if($rombel->asisten)
                                                        <span class="badge badge-secondary">{{ $rombel->asisten->nama_lengkap }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif

                                            {{-- Soft warning: Rombel > 20 siswa tanpa asisten --}}
                                            @if($rombel->jumlah_siswa > 20 && !$rombel->user_id_asisten)
                                            <div class="alert alert-warning small mt-3 mb-0 py-2">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                <strong>Perhatian:</strong> Rombel ini memiliki {{ $rombel->jumlah_siswa }} siswa tanpa asisten.
                                                Disarankan menambahkan asisten untuk rombel dengan &gt;20 siswa.
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>Belum ada rombel yang dikonfigurasi.</p>
                                    </div>
                                </div>
                                @endforelse
                                
                                @push('modals')
                                {{-- Modals for Rombel Actions (Placed outside loop if IDs used, or inside if unique) --}}
                                @foreach($ekstrakurikuler->rombels as $rombel)
                                <div class="modal fade" id="importSiswaModal{{ $rombel->id }}" tabindex="-1" aria-labelledby="importSiswaModalLabel{{ $rombel->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('rombel.import-siswa', $rombel->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="importSiswaModalLabel{{ $rombel->id }}">Import Siswa ke {{ $rombel->nama_rombel }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="file_excel" class="form-label">File Excel/CSV (.xlsx, .csv)</label>
                                                        <input type="file" class="form-control" id="file_excel" name="file" required accept=".xlsx,.xls,.csv" data-max-size="2097152">
                                                        <div class="form-text mt-1">
                                                            <i class="fas fa-info-circle"></i> Format: .xlsx, .xls, .csv | Maksimal: 2MB
                                                        </div>
                                                        <small class="text-muted d-block mt-1">Data: No, Nama Lengkap, NISN (optional), Kelas</small>
                                                    </div>
                                                    <div class="alert alert-info small">
                                                        <i class="fas fa-info-circle"></i> Sistem akan mencocokkan siswa berdasarkan NISN atau Nama + Sekolah. Jika tidak ditemukan, siswa baru akan dibuat otomatis.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Import Data</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endpush
                            </div>
                        </div>
                        
                        <!-- Sessions Tab -->
                        <div class="tab-pane fade" id="sessions" role="tabpanel">
                            @if($ekstrakurikuler->sessions->count() > 0)
                                <div class="row">
                                    @foreach($ekstrakurikuler->rombels as $rombel)
                                        @if($rombel->sessions->count() > 0)
                                        <div class="col-12 mb-4">
                                            <h6><i class="fas fa-users"></i> {{ $rombel->nama_rombel }}</h6>
                                            <div class="timeline">
                                                @foreach($rombel->sessions->take(10) as $session)
                                                <div class="timeline-item">
                                                    @php
                                                        $markerClass = match($session->status) {
                                                            'selesai' => 'completed',
                                                            'berlangsung' => 'current',
                                                            default => 'pending'
                                                        };
                                                        
                                                        $statusClass = match($session->status) {
                                                            'terjadwal' => 'badge-secondary',
                                                            'berlangsung' => 'badge-primary',
                                                            'selesai' => 'badge-success',
                                                            'dibatalkan' => 'badge-danger',
                                                            'ditunda' => 'badge-warning',
                                                            'tidak_hadir' => 'badge-dark',
                                                            default => 'badge-secondary'
                                                        };
                                                    @endphp
                                                    <div class="timeline-marker {{ $markerClass }}"></div>
                                                    <div class="session-card p-3">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <h6 class="mb-0 mr-2">Pertemuan {{ $session->nomor_pertemuan }}</h6>
                                                                    @can('update', $session)
                                                                    <a href="{{ route('ekstrakurikuler.sessions.edit', $session) }}" class="btn btn-xs btn-outline-warning ms-2" title="Edit Jadwal">
                                                                        <i class="fas fa-pencil-alt"></i>
                                                                    </a>
                                                                    @endcan
                                                                </div>
                                                                <div class="text-muted mb-2">
                                                                    <i class="fas fa-calendar"></i> 
                                                                    {{ $session->tanggal_terjadwal->format('d/m/Y') }}
                                                                    <span class="mx-2">|</span>
                                                                    <i class="fas fa-clock"></i> 
                                                                    {{ $session->jadwal_waktu }}
                                                                </div>
                                                                
                                                                @if($session->topik_materi)
                                                                <div class="mb-2">
                                                                    <strong>Topik:</strong> {{ $session->topik_materi }}
                                                                </div>
                                                                @endif
                                                                
                                                                @if($session->instruktur)
                                                                <div class="mb-2">
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-user"></i> {{ $session->instruktur->nama_lengkap }}
                                                                        @if($session->asisten)
                                                                            & {{ $session->asisten->nama_lengkap }}
                                                                        @endif
                                                                    </small>
                                                                </div>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="text-right d-flex flex-column align-items-end">
                                                                <span class="badge {{ $statusClass }} mb-1">
                                                                    {{ $session->status_label }}
                                                                </span>
                                                                @if($session->status === 'selesai' && !$session->laporan_mengajar_id)
                                                                <span class="badge badge-danger">
                                                                    <i class="fas fa-exclamation-circle"></i> Belum Dilaporkan
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        
                                                        @if($session->status === 'selesai' && $session->tanggal_pelaksanaan)
                                                        <div class="mt-2 p-2 bg-light rounded">
                                                            <small class="text-muted">
                                                                <i class="fas fa-check"></i> 
                                                                Dilaksanakan: {{ $session->tanggal_pelaksanaan->format('d/m/Y') }}
                                                                @if($session->waktu_aktual)
                                                                    ({{ $session->waktu_aktual }})
                                                                @endif
                                                            </small>
                                                        </div>
                                                        @endif
                                                        
                                                        @if($session->status === 'dibatalkan' && $session->alasan_pembatalan)
                                                        <div class="mt-2 p-2 bg-light rounded">
                                                            <small class="text-danger">
                                                                <i class="fas fa-times"></i> 
                                                                Dibatalkan: {{ $session->alasan_pembatalan }}
                                                            </small>
                                                        </div>
                                                        @endif

                                                        {{-- Instructor Actions --}}
                                                        <div class="mt-3 pt-2 border-top">
                                                            <div class="d-flex gap-2">
                                                                @if($session->status == 'terjadwal')
                                                                    @if(auth()->id() == $session->user_id_instruktur || auth()->user()->hasRole(['admin', 'admin_erlass', 'webmaster']))
                                                                    <form action="{{ route('ekstrakurikuler.sessions.start', $session) }}" method="POST" class="d-inline" onsubmit="return confirm('Mulai sesi ini sekarang?');">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                                            <i class="fas fa-play"></i> Mulai Mengajar
                                                                        </button>
                                                                    </form>
                                                                    @endif
                                                                @elseif($session->status == 'berlangsung')
                                                                    @if(auth()->id() == $session->user_id_instruktur || auth()->user()->hasRole(['admin', 'admin_erlass', 'webmaster']))
                                                                    <form action="{{ route('ekstrakurikuler.sessions.complete', $session) }}" method="POST" class="d-inline" onsubmit="return confirm('Selesaikan sesi ini?');">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-success">
                                                                            <i class="fas fa-check"></i> Selesai Mengajar
                                                                        </button>
                                                                    </form>
                                                                    @endif
                                                                @elseif($session->status == 'selesai')
                                                                    @if(!$session->laporan_mengajar_id)
                                                                         @can('create', App\Models\LaporanMengajar::class)
                                                                         <form action="{{ route('laporan-mengajar.from-ekstrakurikuler', $session) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            <button class="btn btn-sm btn-info text-white">
                                                                                <i class="fas fa-file-alt"></i> Buat Laporan
                                                                            </button>
                                                                        </form>
                                                                        @endcan
                                                                    @else
                                                                        <a href="{{ route('laporan-mengajar.show', $session->laporan_mengajar_id) }}" class="btn btn-sm btn-outline-info">
                                                                            <i class="fas fa-eye"></i> Lihat Laporan
                                                                        </a>
                                                                        
                                                                        <a href="{{ route('ekstrakurikuler-session.absensi.create', $session) }}" class="btn btn-sm btn-outline-success">
                                                                            <i class="fas fa-user-check"></i> Absensi
                                                                        </a>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                                
                                                @if($rombel->sessions->count() > 10)
                                                <div class="text-center mt-3">
                                                    <small class="text-muted">
                                                        ... dan {{ $rombel->sessions->count() - 10 }} sesi lainnya
                                                    </small>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                    <p>Belum ada jadwal yang digenerate.</p>
                                    <small>Jadwal akan dibuat otomatis setelah rombel dikonfigurasi.</small>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Facilities Tab -->
                        <div class="tab-pane fade" id="facilities" role="tabpanel">
                            <div class="row">
                                <div class="col-md-3 text-center mb-4">
                                    @php
                                        $internetClass = match($ekstrakurikuler->koneksi_internet) {
                                            'ada' => 'facility-available',
                                            'tidak_ada' => 'facility-unavailable',
                                            default => 'facility-unknown'
                                        };
                                        $internetIcon = match($ekstrakurikuler->koneksi_internet) {
                                            'ada' => 'fa-wifi',
                                            'tidak_ada' => 'fa-wifi-slash',
                                            default => 'fa-question'
                                        };
                                    @endphp
                                    <div class="facility-icon {{ $internetClass }}">
                                        <i class="fas {{ $internetIcon }}"></i>
                                    </div>
                                    <h6>Internet</h6>
                                    <span class="badge badge-{{ $internetClass === 'facility-available' ? 'success' : ($internetClass === 'facility-unavailable' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $ekstrakurikuler->koneksi_internet)) }}
                                    </span>
                                    @if($ekstrakurikuler->keterangan_internet)
                                    <div class="mt-2">
                                        <small class="text-muted">{{ $ekstrakurikuler->keterangan_internet }}</small>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-3 text-center mb-4">
                                    @php
                                        $proyektorClass = match($ekstrakurikuler->proyektor) {
                                            'ada' => 'facility-available',
                                            'tidak_ada' => 'facility-unavailable',
                                            default => 'facility-unknown'
                                        };
                                        $proyektorIcon = match($ekstrakurikuler->proyektor) {
                                            'ada' => 'fa-video',
                                            'tidak_ada' => 'fa-video-slash',
                                            default => 'fa-question'
                                        };
                                    @endphp
                                    <div class="facility-icon {{ $proyektorClass }}">
                                        <i class="fas {{ $proyektorIcon }}"></i>
                                    </div>
                                    <h6>Proyektor</h6>
                                    <span class="badge badge-{{ $proyektorClass === 'facility-available' ? 'success' : ($proyektorClass === 'facility-unavailable' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $ekstrakurikuler->proyektor)) }}
                                    </span>
                                    @if($ekstrakurikuler->keterangan_proyektor)
                                    <div class="mt-2">
                                        <small class="text-muted">{{ $ekstrakurikuler->keterangan_proyektor }}</small>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-3 text-center mb-4">
                                    @php
                                        $hdmiClass = match($ekstrakurikuler->kabel_hdmi) {
                                            'ada' => 'facility-available',
                                            'tidak_ada' => 'facility-unavailable',
                                            default => 'facility-unknown'
                                        };
                                        $hdmiIcon = match($ekstrakurikuler->kabel_hdmi) {
                                            'ada' => 'fa-plug',
                                            'tidak_ada' => 'fa-ban',
                                            default => 'fa-question'
                                        };
                                    @endphp
                                    <div class="facility-icon {{ $hdmiClass }}">
                                        <i class="fas {{ $hdmiIcon }}"></i>
                                    </div>
                                    <h6>Kabel HDMI</h6>
                                    <span class="badge badge-{{ $hdmiClass === 'facility-available' ? 'success' : ($hdmiClass === 'facility-unavailable' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $ekstrakurikuler->kabel_hdmi)) }}
                                    </span>
                                </div>
                                
                                <div class="col-md-3 text-center mb-4">
                                    @php
                                        $vgaClass = match($ekstrakurikuler->kabel_vga) {
                                            'ada' => 'facility-available',
                                            'tidak_ada' => 'facility-unavailable',
                                            default => 'facility-unknown'
                                        };
                                        $vgaIcon = match($ekstrakurikuler->kabel_vga) {
                                            'ada' => 'fa-plug',
                                            'tidak_ada' => 'fa-ban',
                                            default => 'fa-question'
                                        };
                                    @endphp
                                    <div class="facility-icon {{ $vgaClass }}">
                                        <i class="fas {{ $vgaIcon }}"></i>
                                    </div>
                                    <h6>Kabel VGA</h6>
                                    <span class="badge badge-{{ $vgaClass === 'facility-available' ? 'success' : ($vgaClass === 'facility-unavailable' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $ekstrakurikuler->kabel_vga)) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($ekstrakurikuler->keterangan_kabel)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6><i class="fas fa-info-circle"></i> Keterangan Tambahan</h6>
                                    <div class="alert alert-info">
                                        {{ $ekstrakurikuler->keterangan_kabel }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tabs
    const triggerTabList = [].slice.call(document.querySelectorAll('#detailTabs button'));
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
    
    // Auto-refresh status for active programs
    const programStatus = '{{ $ekstrakurikuler->status }}';
    if (programStatus === 'aktif') {
        // Optional: Add auto-refresh for active programs
        setInterval(function() {
            // Could implement auto-refresh for session status
        }, 30000); // 30 seconds
    }
});

// Function to handle session actions (if needed)
function handleSessionAction(sessionId, action) {
    if (confirm(`Are you sure you want to ${action} this session?`)) {
        // Implementation for session actions
        console.log(`${action} session ${sessionId}`);
    }
}
</script>
@endpush