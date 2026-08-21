@extends('layouts.app')

@section('title', 'Detail Program Ekstrakurikuler — ' . $ekstrakurikuler->kategori_program)

@push('styles')
<style>
    /* Modern Theme Tokens & Aesthetics */
    .hero-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #1e293b 100%);
        border-radius: 16px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
    }
    
    .hero-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-banner .breadcrumb-item, 
    .hero-banner .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.875rem;
        text-decoration: none;
    }

    .hero-banner .breadcrumb-item.active {
        color: #ffffff;
        font-weight: 500;
    }

    .hero-banner .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.4);
    }

    .btn-glass {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        transition: all 0.25s ease;
        border-radius: 8px;
    }

    .btn-glass:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .premium-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
    }

    .premium-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .stat-box {
        border-radius: 12px;
        padding: 1.25rem;
        border: 1px solid #f1f5f9;
        background: #f8fafc;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Custom Nav Pills */
    .nav-pills-custom {
        background: #f1f5f9;
        padding: 6px;
        border-radius: 12px;
        gap: 4px;
    }

    .nav-pills-custom .nav-link {
        border-radius: 8px;
        color: #64748b;
        font-weight: 500;
        padding: 8px 18px;
        transition: all 0.2s ease;
    }

    .nav-pills-custom .nav-link.active {
        background: #ffffff;
        color: #2563eb;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .info-group {
        margin-bottom: 1.25rem;
    }

    .info-group .info-label {
        font-size: 0.8125rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-bottom: 0.25rem;
    }

    .info-group .info-value {
        font-size: 0.95rem;
        color: #1e293b;
        font-weight: 500;
    }

    .rombel-card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-bottom: 1px solid #cbd5e1;
        border-radius: 14px 14px 0 0;
    }

    .facility-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 0.75rem;
    }

    /* Timeline Session Improvements */
    .timeline-v2 {
        position: relative;
        padding-left: 2.25rem;
    }

    .timeline-v2::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item-v2 {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .timeline-dot {
        position: absolute;
        left: -2.25rem;
        top: 14px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #94a3b8;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 2px #e2e8f0;
    }

    .timeline-dot.completed {
        background: #10b981;
        box-shadow: 0 0 0 2px #d1fae5;
    }

    .timeline-dot.current {
        background: #3b82f6;
        box-shadow: 0 0 0 3px #dbeafe;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    
    <!-- Hero Banner Header -->
    <div class="hero-banner p-4 p-md-5 mb-4">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-3 mb-lg-0">
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door-fill me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ekstrakurikuler.index') }}">Ekstrakurikuler</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($ekstrakurikuler->kategori_program, 25) }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <h1 class="h2 fw-bold mb-0 text-white me-2">{{ $ekstrakurikuler->kategori_program }}</h1>
                    @php
                        $statusBadgeClass = match($ekstrakurikuler->status) {
                            'draft' => 'bg-secondary text-white',
                            'diajukan' => 'bg-warning text-dark',
                            'disetujui' => 'bg-info text-dark',
                            'ditolak' => 'bg-danger text-white',
                            'aktif' => 'bg-success text-white',
                            'selesai' => 'bg-primary text-white',
                            'dibatalkan' => 'bg-dark text-white',
                            default => 'bg-secondary text-white'
                        };
                    @endphp
                    <span class="badge {{ $statusBadgeClass }} px-3 py-2 rounded-pill fs-7 fw-semibold shadow-sm">
                        <i class="bi bi-circle-fill me-1 small"></i> {{ $ekstrakurikuler->status_label }}
                    </span>
                </div>
                <p class="mb-0 d-flex align-items-center gap-2" style="color: rgba(255, 255, 255, 0.92);">
                    <i class="bi bi-building me-1"></i> {{ $ekstrakurikuler->sekolah?->namasekolah ?? 'Sekolah Belum Ditetapkan' }}
                    <span class="opacity-50">•</span>
                    <i class="bi bi-geo-alt-fill me-1"></i> {{ $ekstrakurikuler->region ?? $ekstrakurikuler->sekolah?->kota ?? 'General' }}
                </p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                    @can('update', $ekstrakurikuler)
                    <a href="{{ route('ekstrakurikuler.enrollment.index', $ekstrakurikuler) }}" class="btn btn-glass">
                        <i class="bi bi-people-fill me-1"></i> Kelola Siswa
                    </a>
                    <a href="{{ route('ekstrakurikuler.edit', $ekstrakurikuler) }}" class="btn btn-glass">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    <form action="{{ route('ekstrakurikuler.regenerate-sessions', $ekstrakurikuler) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-glass" onclick="return confirm('Generate ulang seluruh jadwal sesi berdasarkan rombel saat ini? Sesi lama yang belum ada laporan akan diperbarui.')" title="Sinkronkan jadwal sesi sesuai rombel">
                            <i class="bi bi-arrow-repeat me-1"></i> Sync Sesi
                        </button>
                    </form>
                    @endcan
                    
                    @can('approve', $ekstrakurikuler)
                        @if($ekstrakurikuler->canBeApproved())
                        <form action="{{ route('ekstrakurikuler.approve', $ekstrakurikuler) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menyetujui program ini?')">
                                <i class="bi bi-check-circle-fill me-1"></i> Setujui
                            </button>
                        </form>
                        @endif
                    @endcan
                    
                    @can('activate', $ekstrakurikuler)
                        @if($ekstrakurikuler->canBeActivated())
                        <form action="{{ route('ekstrakurikuler.activate', $ekstrakurikuler) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary shadow-sm" onclick="return confirm('Apakah Anda yakin ingin mengaktifkan program ini?')">
                                <i class="bi bi-play-circle-fill me-1"></i> Aktifkan
                            </button>
                        </form>
                        @endif
                    @endcan

                    @can('complete', $ekstrakurikuler)
                        @if($ekstrakurikuler->canBeCompleted())
                        <form action="{{ route('ekstrakurikuler.complete', $ekstrakurikuler) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success shadow-sm" onclick="return confirm('Tandai program Ekstrakurikuler ini telah selesai?')">
                                <i class="bi bi-flag-fill me-1"></i> Selesaikan
                            </button>
                        </form>
                        @endif
                    @endcan
                    
                    <a href="{{ route('ekstrakurikuler.index') }}" class="btn btn-glass">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="premium-card p-3 d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Siswa Terdaftar</div>
                    <div class="h4 fw-bold mb-0 text-dark">{{ $ekstrakurikuler->siswaAktif()->count() ?: $ekstrakurikuler->total_siswa }} <span class="fs-6 font-normal text-muted">Orang</span></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="premium-card p-3 d-flex align-items-center">
                <div class="stat-icon bg-emerald bg-opacity-10 text-success me-3" style="background-color: rgba(16, 185, 129, 0.1);">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Total Rombel</div>
                    <div class="h4 fw-bold mb-0 text-dark">{{ $ekstrakurikuler->total_rombel }} <span class="fs-6 font-normal text-muted">Kelas</span></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            @php
                $progress = $ekstrakurikuler->getProgressPertemuan();
                $percentage = $progress['persentase'];
            @endphp
            <div class="premium-card p-3 d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Pertemuan Selesai</div>
                    <div class="h4 fw-bold mb-0 text-dark">{{ $progress['selesai'] }} / {{ $progress['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="premium-card p-3 d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-medium">Progress</span>
                        <span class="fw-bold text-dark small">{{ $percentage }}%</span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 4px;">
                        <div class="progress-bar bg-success rounded" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="premium-card p-3 p-md-4 mb-4">
        <ul class="nav nav-pills nav-pills-custom mb-4" id="detailTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                    <i class="bi bi-info-circle-fill me-1.5"></i> Overview Program
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rombel-tab" data-bs-toggle="tab" data-bs-target="#rombel" type="button" role="tab">
                    <i class="bi bi-diagram-3-fill me-1.5"></i> Rombongan Belajar ({{ $ekstrakurikuler->rombels->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions" type="button" role="tab">
                    <i class="bi bi-calendar3 me-1.5"></i> Jadwal Sesi ({{ $ekstrakurikuler->sessions->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="facilities-tab" data-bs-toggle="tab" data-bs-target="#facilities" type="button" role="tab">
                    <i class="bi bi-tools me-1.5"></i> Fasilitas & Peralatan
                </button>
            </li>
        </ul>

        <div class="tab-content" id="detailTabsContent">
            
            <!-- OVERVIEW TAB -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row g-4">
                    <!-- Column 1: Basic Program Info -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100 border border-secondary-subtle">
                            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                                <i class="bi bi-award-fill me-2"></i>Informasi Program
                            </h6>
                            
                            <div class="info-group">
                                <div class="info-label">Nama Kategori Program</div>
                                <div class="info-value fw-bold text-dark fs-6">{{ $ekstrakurikuler->kategori_program }}</div>
                            </div>
                            
                            @if($ekstrakurikuler->deskripsi)
                            <div class="info-group">
                                <div class="info-label">Deskripsi Program</div>
                                <div class="info-value text-secondary">{{ $ekstrakurikuler->deskripsi }}</div>
                            </div>
                            @endif
                            
                            <div class="info-group">
                                <div class="info-label">Region / Wilayah Operasional</div>
                                <div class="info-value">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1.5 rounded-pill fs-7 fw-bold">
                                        <i class="bi bi-geo-alt-fill me-1"></i> {{ $ekstrakurikuler->region ?? $ekstrakurikuler->sekolah?->kota ?? 'JAKARTA' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="info-group">
                                <div class="info-label">Sales / Koordinator Lapangan</div>
                                <div class="info-value text-dark font-medium">
                                    <i class="bi bi-person-badge me-1.5 text-muted"></i>{{ $ekstrakurikuler->sales?->nama_lengkap ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Column 2: School Details -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100 border border-secondary-subtle">
                            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                                <i class="bi bi-building me-2"></i>Detail Sekolah Mitra
                            </h6>
                            
                            <div class="info-group">
                                <div class="info-label">Nama Sekolah</div>
                                <div class="info-value fw-bold text-dark">{{ $ekstrakurikuler->sekolah?->namasekolah ?? '-' }}</div>
                            </div>
                            
                            <div class="info-group">
                                <div class="info-label">Alamat Lengkap</div>
                                <div class="info-value text-secondary">{{ $ekstrakurikuler->alamat_lengkap ?? '-' }}</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-group">
                                        <div class="info-label">Jarak dari POP</div>
                                        <div class="info-value text-dark">{{ $ekstrakurikuler->jarak_km ?? 0 }} KM</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-group">
                                        <div class="info-label">Kepala Sekolah</div>
                                        <div class="info-value text-dark">{{ $ekstrakurikuler->kepala_sekolah ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-group">
                                        <div class="info-label">Penanggung Jawab</div>
                                        <div class="info-value text-dark">{{ $ekstrakurikuler->penanggung_jawab ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-group">
                                        <div class="info-label">No. Telepon Kontak</div>
                                        <div class="info-value">
                                            @if($ekstrakurikuler->no_telepon)
                                            <a href="tel:{{ $ekstrakurikuler->no_telepon }}" class="text-decoration-none text-primary fw-medium">
                                                <i class="bi bi-telephone-fill me-1"></i>{{ $ekstrakurikuler->no_telepon }}
                                            </a>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($ekstrakurikuler->google_maps_link)
                            <div class="info-group mb-0">
                                <div class="info-label">Lokasi Google Maps</div>
                                <div class="info-value">
                                    <a href="{{ $ekstrakurikuler->google_maps_link }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-map-fill me-1"></i> Buka Google Maps
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Period Callout Banner -->
                <div class="mt-4 p-3 rounded-3 border border-info border-opacity-25" style="background-color: #f0f9ff;">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <span class="text-muted small d-block">Tanggal Mulai:</span>
                            <span class="fw-bold text-dark"><i class="bi bi-calendar-event me-1 text-info"></i> {{ $ekstrakurikuler->tanggal_mulai ? $ekstrakurikuler->tanggal_mulai->locale('id')->translatedFormat('l, d F Y') : '-' }}</span>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <span class="text-muted small d-block">Tanggal Selesai:</span>
                            <span class="fw-bold text-dark"><i class="bi bi-calendar-check me-1 text-info"></i> {{ $ekstrakurikuler->tanggal_selesai ? $ekstrakurikuler->tanggal_selesai->locale('id')->translatedFormat('l, d F Y') : '-' }}</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Total Alokasi Sesi:</span>
                            <span class="fw-bold text-dark"><i class="bi bi-journal-check me-1 text-info"></i> {{ $ekstrakurikuler->total_pertemuan }} Pertemuan</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROMBEL TAB -->
            <div class="tab-pane fade" id="rombel" role="tabpanel">
                @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addRombelModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Rombel
                    </button>
                </div>
                @endif
                <div class="row g-4">
                    @forelse($ekstrakurikuler->rombels as $rombel)
                    <div class="col-md-6">
                        <div class="premium-card h-100 overflow-hidden">
                            <div class="p-3 rombel-card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-diagram-3-fill text-primary"></i> {{ $rombel->nama_rombel }}
                                    <span class="badge bg-light text-dark border ms-1">{{ $rombel->status_label }}</span>
                                </h6>
                                @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
                                <div class="d-flex align-items-center gap-1.5">
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill shadow-xs" data-bs-toggle="modal" data-bs-target="#addSessionModal{{ $rombel->id }}">
                                        <i class="bi bi-plus-lg me-1"></i> Sesi
                                    </button>
                                    
                                    @if($rombel->canBeDeleted())
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#deleteRombelModal{{ $rombel->id }}" title="Hapus Rombel Kosong">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill opacity-50" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Tidak dapat dihapus: {{ $rombel->getDeleteRestrictionReason() }}">
                                        <i class="bi bi-lock-fill"></i> Hapus
                                    </button>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Siswa Terdaftar</small>
                                        <span class="fw-semibold text-dark"><i class="bi bi-person me-1"></i>{{ $rombel->getJumlahSiswaAktual() }} Siswa</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Hari & Waktu</small>
                                        <span class="fw-semibold text-dark"><i class="bi bi-clock me-1"></i>{{ $rombel->hari_label }}, {{ $rombel->jadwal_waktu }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Periode Pertemuan</small>
                                    <span class="small text-secondary">
                                        {{ $rombel->tanggal_mulai ? $rombel->tanggal_mulai->format('d/m/Y') : '-' }} s.d. {{ $rombel->tanggal_selesai ? $rombel->tanggal_selesai->format('d/m/Y') : '-' }}
                                    </span>
                                </div>

                                <!-- Progress -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center small mb-1">
                                        <span class="text-muted">Progress Pertemuan</span>
                                        <span class="fw-bold text-dark">{{ $rombel->getProgressPersentase() }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; border-radius: 4px;">
                                        <div class="progress-bar bg-success rounded" style="width: {{ $rombel->getProgressPersentase() }}%"></div>
                                    </div>
                                    <small class="text-muted fs-8">
                                        {{ $rombel->pertemuan_selesai }} dari {{ $rombel->total_pertemuan }} pertemuan selesai
                                    </small>
                                </div>

                                <!-- Instructors -->
                                @if($rombel->instruktur || $rombel->asisten)
                                <div class="pt-2 border-top">
                                    <small class="text-muted d-block mb-1">Tim Pengajar assigned:</small>
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if($rombel->instruktur)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1">
                                            <i class="bi bi-person-badge me-1"></i> {{ $rombel->instruktur->nama_lengkap }} (Utama)
                                        </span>
                                        @endif
                                        @if($rombel->asisten)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1">
                                            <i class="bi bi-person me-1"></i> {{ $rombel->asisten->nama_lengkap }} (Asisten)
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-folder-x text-muted opacity-50 display-4"></i>
                        <p class="text-muted mt-2">Belum ada rombongan belajar (rombel) yang didaftarkan.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- JADWAL SESI TAB -->
            <div class="tab-pane fade" id="sessions" role="tabpanel">
                @forelse($ekstrakurikuler->rombels as $rombel)
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="bi bi-collection-fill text-primary me-2"></i> {{ $rombel->nama_rombel }}
                        <span class="badge bg-light text-muted border ms-2 font-normal fs-8">{{ $rombel->sessions->count() }} Sesi Terjadwal</span>
                    </h6>
                    
                    <div class="timeline-v2">
                        @foreach($rombel->sessions as $session)
                        @php
                            $dotClass = match($session->status) {
                                'selesai' => 'completed',
                                'berlangsung' => 'current',
                                default => 'pending'
                            };
                            
                            $sessionBadge = match($session->status) {
                                'terjadwal' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                                'berlangsung' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                'selesai' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                'dibatalkan' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                'ditunda' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                                default => 'bg-secondary bg-opacity-10 text-secondary'
                            };
                        @endphp
                        <div class="timeline-item-v2">
                            <div class="timeline-dot {{ $dotClass }}"></div>
                            <div class="p-3 border rounded-3 bg-white shadow-xs">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-bold text-dark">Pertemuan {{ $session->nomor_pertemuan }}</span>
                                            <span class="badge {{ $sessionBadge }} rounded-pill px-2.5 py-0.5 small fs-8">
                                                {{ $session->status_label }}
                                            </span>
                                            @can('update', $session)
                                            <a href="{{ route('ekstrakurikuler.sessions.edit', $session) }}" class="text-primary small text-decoration-none" title="Edit Jadwal">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endcan
                                        </div>
                                        <div class="small text-muted mb-2">
                                            <i class="bi bi-calendar-event me-1"></i> {{ $session->tanggal_terjadwal ? $session->tanggal_terjadwal->format('d/m/Y') : '-' }}
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-clock me-1"></i> {{ $session->jadwal_waktu }}
                                        </div>
                                        @if($session->topik_materi)
                                        <div class="small text-dark mb-1">
                                            <strong>Topik:</strong> {{ $session->topik_materi }}
                                        </div>
                                        @endif
                                        @if($session->instruktur)
                                        <div class="small text-muted">
                                            <i class="bi bi-person me-1"></i> Instruktur: {{ $session->instruktur->nama_lengkap }}
                                        </div>
                                        @endif
                                        @if($session->asisten)
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-person-fill-check me-1"></i> Asisten: {{ $session->asisten->nama_lengkap }}
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($session->status === 'selesai' && !$session->laporanMengajar)
                                    <div>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Ada Laporan
                                        </span>
                                    </div>
                                    @endif
                                    @can('update', $session)
                                    @if($session->status === 'terjadwal' || $session->status === 'ditunda')
                                    <div>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-warning rounded-pill"
                                            onclick="openRescheduleModal({{ $session->id }}, '{{ $session->tanggal_terjadwal?->format('d/m/Y') }}', '{{ $session->nomor_pertemuan }}')"
                                            title="Libur / Jadwal Ulang">
                                            <i class="bi bi-calendar-x me-1"></i>Libur / Jadwal Ulang
                                        </button>
                                    </div>
                                    @elseif($session->status === 'berlangsung' && auth()->user()?->hasAdminAccess())
                                    <div>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-pill"
                                            onclick="resetSessionToScheduled({{ $session->id }}, '{{ $session->nomor_pertemuan }}')"
                                            title="Kembalikan status sesi dari Berlangsung ke Terjadwal">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Sesi
                                        </button>
                                    </div>
                                    @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted opacity-50 display-4"></i>
                    <p class="text-muted mt-2">Belum ada jadwal sesi yang dibuat.</p>
                </div>
                @endforelse
            </div>

            <!-- FASILITAS TAB -->
            <div class="tab-pane fade" id="facilities" role="tabpanel">
                <div class="row g-3">
                    <!-- Internet -->
                    @php
                        $internetClass = match($ekstrakurikuler->koneksi_internet) {
                            'ada' => 'bg-success bg-opacity-10 text-success',
                            'tidak_ada' => 'bg-danger bg-opacity-10 text-danger',
                            default => 'bg-warning bg-opacity-10 text-warning'
                        };
                        $internetIcon = match($ekstrakurikuler->koneksi_internet) {
                            'ada' => 'bi-wifi',
                            'tidak_ada' => 'bi-wifi-off',
                            default => 'bi-question-circle'
                        };
                    @endphp
                    <div class="col-6 col-md-3">
                        <div class="p-3 text-center border rounded-3 bg-white h-100">
                            <div class="facility-card-icon {{ $internetClass }}">
                                <i class="bi {{ $internetIcon }}"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Koneksi Internet</h6>
                            <span class="badge {{ $internetClass }} px-3 py-1 rounded-pill">
                                {{ ucfirst(str_replace('_', ' ', $ekstrakurikuler->koneksi_internet ?? 'Belum Diketahui')) }}
                            </span>
                            @if($ekstrakurikuler->keterangan_internet)
                            <small class="text-muted d-block mt-2 fs-8">{{ $ekstrakurikuler->keterangan_internet }}</small>
                            @endif
                        </div>
                    </div>

                    <!-- Proyektor -->
                    @php
                        $proyektorClass = match($ekstrakurikuler->proyektor) {
                            'ada' => 'bg-success bg-opacity-10 text-success',
                            'tidak_ada' => 'bg-danger bg-opacity-10 text-danger',
                            default => 'bg-warning bg-opacity-10 text-warning'
                        };
                        $proyektorIcon = match($ekstrakurikuler->proyektor) {
                            'ada' => 'bi-projector-fill',
                            'tidak_ada' => 'bi-camera-video-off',
                            default => 'bi-question-circle'
                        };
                    @endphp
                    <div class="col-6 col-md-3">
                        <div class="p-3 text-center border rounded-3 bg-white h-100">
                            <div class="facility-card-icon {{ $proyektorClass }}">
                                <i class="bi {{ $proyektorIcon }}"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">LCD Proyektor</h6>
                            <span class="badge {{ $proyektorClass }} px-3 py-1 rounded-pill">
                                {{ ucfirst(str_replace('_', ' ', $ekstrakurikuler->proyektor ?? 'Belum Diketahui')) }}
                            </span>
                            @if($ekstrakurikuler->keterangan_proyektor)
                            <small class="text-muted d-block mt-2 fs-8">{{ $ekstrakurikuler->keterangan_proyektor }}</small>
                            @endif
                        </div>
                    </div>

                    <!-- Kabel HDMI -->
                    @php
                        $hdmiClass = match($ekstrakurikuler->kabel_hdmi) {
                            'ada' => 'bg-success bg-opacity-10 text-success',
                            'tidak_ada' => 'bg-danger bg-opacity-10 text-danger',
                            default => 'bg-warning bg-opacity-10 text-warning'
                        };
                    @endphp
                    <div class="col-6 col-md-3">
                        <div class="p-3 text-center border rounded-3 bg-white h-100">
                            <div class="facility-card-icon {{ $hdmiClass }}">
                                <i class="bi bi-plugin"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Kabel HDMI</h6>
                            <span class="badge {{ $hdmiClass }} px-3 py-1 rounded-pill">
                                {{ ucfirst(str_replace('_', ' ', $ekstrakurikuler->kabel_hdmi ?? 'Belum Diketahui')) }}
                            </span>
                        </div>
                    </div>

                    <!-- Kabel VGA -->
                    @php
                        $vgaClass = match($ekstrakurikuler->kabel_vga) {
                            'ada' => 'bg-success bg-opacity-10 text-success',
                            'tidak_ada' => 'bg-danger bg-opacity-10 text-danger',
                            default => 'bg-warning bg-opacity-10 text-warning'
                        };
                    @endphp
                    <div class="col-6 col-md-3">
                        <div class="p-3 text-center border rounded-3 bg-white h-100">
                            <div class="facility-card-icon {{ $vgaClass }}">
                                <i class="bi bi-display"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Kabel VGA</h6>
                            <span class="badge {{ $vgaClass }} px-3 py-1 rounded-pill">
                                {{ ucfirst(str_replace('_', ' ', $ekstrakurikuler->kabel_vga ?? 'Belum Diketahui')) }}
                            </span>
                        </div>
                    </div>

                    <!-- Kabel Roll -->
                    @php
                        $rollClass = match($ekstrakurikuler->kabel_roll) {
                            'ada' => 'bg-success bg-opacity-10 text-success',
                            'tidak_ada' => 'bg-danger bg-opacity-10 text-danger',
                            default => 'bg-warning bg-opacity-10 text-warning'
                        };
                    @endphp
                    <div class="col-6 col-md-3">
                        <div class="p-3 text-center border rounded-3 bg-white h-100">
                            <div class="facility-card-icon {{ $rollClass }}">
                                <i class="bi bi-lightning-charge"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Kabel Roll</h6>
                            <span class="badge {{ $rollClass }} px-3 py-1 rounded-pill">
                                {{ ucfirst(str_replace('_', ' ', $ekstrakurikuler->kabel_roll ?? 'Belum Diketahui')) }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($ekstrakurikuler->keterangan_kabel)
                <div class="mt-3 p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-info-circle me-1"></i> Catatan Peralatan Tambahan</h6>
                    <p class="text-muted small mb-0">{{ $ekstrakurikuler->keterangan_kabel }}</p>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

@push('modals')
{{-- Modals for Adding Manual Session --}}
@if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
    @foreach($ekstrakurikuler->rombels as $rombel)
        <div class="modal fade text-dark" id="addSessionModal{{ $rombel->id }}" tabindex="-1" aria-labelledby="addSessionModalLabel{{ $rombel->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content text-start rounded-4 border-0 shadow">
                    <form action="{{ route('ekstrakurikuler.rombel.add-session', $rombel) }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold" id="addSessionModalLabel{{ $rombel->id }}">
                                <i class="bi bi-calendar-plus text-primary me-2"></i>Tambah Sesi Manual
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info py-2 small mb-3 border-0 bg-info bg-opacity-10 text-info">
                                <i class="bi bi-info-circle me-1"></i> Sesi baru akan ditambahkan sebagai <strong>Pertemuan {{ $rombel->sessions()->max('nomor_pertemuan') + 1 }}</strong> untuk <strong>{{ $rombel->nama_rombel }}</strong>.
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_terjadwal{{ $rombel->id }}" class="form-label small fw-bold text-muted">Tanggal Sesi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_terjadwal{{ $rombel->id }}" name="tanggal_terjadwal" required min="{{ $rombel->tanggal_mulai ? $rombel->tanggal_mulai->format('Y-m-d') : '' }}">
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="jam_mulai_terjadwal{{ $rombel->id }}" class="form-label small fw-bold text-muted">Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="jam_mulai_terjadwal{{ $rombel->id }}" name="jam_mulai_terjadwal" value="{{ $rombel->jam_mulai ? $rombel->jam_mulai->format('H:i') : '' }}" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="jam_selesai_terjadwal{{ $rombel->id }}" class="form-label small fw-bold text-muted">Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="jam_selesai_terjadwal{{ $rombel->id }}" name="jam_selesai_terjadwal" value="{{ $rombel->jam_selesai ? $rombel->jam_selesai->format('H:i') : '' }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="topik_materi{{ $rombel->id }}" class="form-label small fw-bold text-muted">Topik Materi (Opsional)</label>
                                <input type="text" class="form-control" id="topik_materi{{ $rombel->id }}" name="topik_materi" placeholder="Contoh: Pengenalan Interface Scratch">
                            </div>
                            <div class="mb-3">
                                <label for="catatan{{ $rombel->id }}" class="form-label small fw-bold text-muted">Catatan (Opsional)</label>
                                <textarea class="form-control" id="catatan{{ $rombel->id }}" name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Sesi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal for Deleting Empty Rombel --}}
        @if($rombel->canBeDeleted())
        <div class="modal fade text-dark" id="deleteRombelModal{{ $rombel->id }}" tabindex="-1" aria-labelledby="deleteRombelModalLabel{{ $rombel->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content text-start rounded-4 border-0 shadow">
                    <form action="{{ route('ekstrakurikuler.rombel.destroy', [$ekstrakurikuler, $rombel]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold text-danger" id="deleteRombelModalLabel{{ $rombel->id }}">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus {{ $rombel->nama_rombel }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3 text-secondary">
                                Apakah Anda yakin ingin menghapus <strong>{{ $rombel->nama_rombel }}</strong> dari program <strong>{{ $ekstrakurikuler->nama_ekskul }}</strong>?
                            </p>
                            <div class="alert alert-warning py-2.5 small mb-3 border-0 bg-warning bg-opacity-10 text-dark rounded-3">
                                <div class="fw-bold mb-1"><i class="bi bi-shield-check text-warning me-1"></i> Pemeriksaan Keamanan:</div>
                                <ul class="mb-0 ps-3">
                                    <li>Siswa terdaftar: <strong>0 Siswa</strong> (Memenuhi syarat).</li>
                                    <li>Laporan mengajar: <strong>0 Laporan</strong> (Memenuhi syarat).</li>
                                    <li>Sebanyak <strong>{{ $rombel->sessions()->where('status', 'terjadwal')->count() }} sesi pertemuan kosong</strong> yang masih terjadwal akan otomatis ikut dibersihkan.</li>
                                </ul>
                            </div>
                            <p class="small text-danger mb-0 fw-semibold">
                                <i class="bi bi-info-circle me-1"></i> Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
                            </p>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4">
                                <i class="bi bi-trash me-1"></i>Ya, Hapus Rombel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    {{-- Modal for Adding New Rombel --}}
    <div class="modal fade text-dark" id="addRombelModal" tabindex="-1" aria-labelledby="addRombelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-start rounded-4 border-0 shadow">
                <form action="{{ route('ekstrakurikuler.rombel.store', $ekstrakurikuler) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold" id="addRombelModalLabel">
                            <i class="bi bi-diagram-3-fill text-primary me-2"></i>Tambah Rombel Baru
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2 small mb-3 border-0 bg-info bg-opacity-10 text-info">
                            <i class="bi bi-info-circle me-1"></i> Rombel baru akan ditambahkan sebagai <strong>Rombel {{ ($ekstrakurikuler->rombels->max('nomor_rombel') ?? 0) + 1 }}</strong>. Sesi pertemuan akan di-generate secara otomatis.
                        </div>

                        <div class="row">
                            {{-- Hari --}}
                            <div class="col-md-4 mb-3">
                                <label for="addRombel_hari" class="form-label small fw-bold text-muted">Hari <span class="text-danger">*</span></label>
                                <select class="form-select" id="addRombel_hari" name="hari" required>
                                    <option value="" disabled selected>Pilih hari...</option>
                                    <option value="senin">Senin</option>
                                    <option value="selasa">Selasa</option>
                                    <option value="rabu">Rabu</option>
                                    <option value="kamis">Kamis</option>
                                    <option value="jumat">Jumat</option>
                                    <option value="sabtu">Sabtu</option>
                                    <option value="minggu">Minggu</option>
                                </select>
                            </div>
                            {{-- Jam Mulai --}}
                            <div class="col-md-4 mb-3">
                                <label for="addRombel_jam_mulai" class="form-label small fw-bold text-muted">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="addRombel_jam_mulai" name="jam_mulai" required>
                            </div>
                            {{-- Jam Selesai --}}
                            <div class="col-md-4 mb-3">
                                <label for="addRombel_jam_selesai" class="form-label small fw-bold text-muted">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="addRombel_jam_selesai" name="jam_selesai" required>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Tanggal Mulai --}}
                            <div class="col-md-6 mb-3">
                                <label for="addRombel_tanggal_mulai" class="form-label small fw-bold text-muted">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="addRombel_tanggal_mulai" name="tanggal_mulai" required>
                            </div>
                            {{-- Tanggal Selesai --}}
                            <div class="col-md-6 mb-3">
                                <label for="addRombel_tanggal_selesai" class="form-label small fw-bold text-muted">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="addRombel_tanggal_selesai" name="tanggal_selesai" required>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Total Pertemuan --}}
                            <div class="col-md-6 mb-3">
                                <label for="addRombel_total_pertemuan" class="form-label small fw-bold text-muted">Total Pertemuan <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="addRombel_total_pertemuan" name="total_pertemuan" min="1" max="52" placeholder="Contoh: 16" required>
                            </div>
                            {{-- Jumlah Siswa --}}
                            <div class="col-md-6 mb-3">
                                <label for="addRombel_jumlah_siswa" class="form-label small fw-bold text-muted">Kuota Siswa (Target) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="addRombel_jumlah_siswa" name="jumlah_siswa" min="1" placeholder="Contoh: 25" required>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Ruangan --}}
                            <div class="col-md-6 mb-3">
                                <label for="addRombel_ruangan" class="form-label small fw-bold text-muted">Ruangan</label>
                                <input type="text" class="form-control" id="addRombel_ruangan" name="ruangan" placeholder="Contoh: Lab Komputer 2">
                            </div>
                            {{-- Keterangan Ruangan --}}
                            <div class="col-md-6 mb-3">
                                <label for="addRombel_keterangan_ruangan" class="form-label small fw-bold text-muted">Keterangan Ruangan</label>
                                <input type="text" class="form-control" id="addRombel_keterangan_ruangan" name="keterangan_ruangan" placeholder="Contoh: Lantai 2, gedung utara">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Rombel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- Modal: Libur / Jadwal Ulang Sesi --}}
<div class="modal fade" id="rescheduleSessionModal" tabindex="-1" aria-labelledby="rescheduleSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="rescheduleSessionModalLabel">
                    <i class="bi bi-calendar-x me-2 text-warning"></i>Libur / Jadwal Ulang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle me-1"></i>
                    Sesi <strong id="rescheduleMeetingLabel">Pertemuan</strong> pada <strong id="rescheduleCurrentDate"></strong>.
                    Pilih tindakan yang sesuai.
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alasan</label>
                    <textarea id="rescheduleAlasan" class="form-control" rows="2"
                              placeholder="Mis: Libur nasional, cuaca buruk, dll."></textarea>
                </div>

                <hr>
                <p class="fw-semibold mb-2">Jadwal pengganti (opsional)</p>
                <div class="mb-3">
                    <label class="form-label small text-muted">Pilih tanggal pengganti</label>
                    <input type="date" id="rescheduleNewDate" class="form-control"
                           min="{{ now()->addDay()->format('Y-m-d') }}">
                    <div class="form-text">Kosongkan jika hanya ingin membatalkan sesi tanpa pengganti.</div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning rounded-pill px-4" id="btnConfirmReschedule"
                        onclick="submitReschedule()">
                    <i class="bi bi-arrow-repeat me-1"></i>Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const triggerTabList = [].slice.call(document.querySelectorAll('#detailTabs button'));
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
});

// --- Reschedule / Libur Sesi ---
let currentRescheduleSessionId = null;

function openRescheduleModal(sessionId, currentDate, meetingNumber) {
    currentRescheduleSessionId = sessionId;
    document.getElementById('rescheduleMeetingLabel').textContent = 'Pertemuan ' + meetingNumber;
    document.getElementById('rescheduleCurrentDate').textContent = currentDate;
    document.getElementById('rescheduleAlasan').value = '';
    document.getElementById('rescheduleNewDate').value = '';
    const modal = new bootstrap.Modal(document.getElementById('rescheduleSessionModal'));
    modal.show();
}

function submitReschedule() {
    const alasan = document.getElementById('rescheduleAlasan').value.trim();
    const newDate = document.getElementById('rescheduleNewDate').value;
    const btn = document.getElementById('btnConfirmReschedule');

    if (!alasan) {
        alert('Harap isi alasan terlebih dahulu.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

    if (newDate) {
        // Reschedule ke tanggal pengganti
        fetch(`/ekstrakurikuler/sessions/${currentRescheduleSessionId}/reschedule`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ tanggal_pengganti: newDate, alasan: alasan })
        })
        .then(res => res.json())
        .then(data => {
            bootstrap.Modal.getInstance(document.getElementById('rescheduleSessionModal')).hide();
            if (data.success) {
                showToast('success', 'Sesi berhasil dijadwal ulang ke ' + newDate);
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast('danger', data.message || 'Gagal menjadwal ulang sesi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Konfirmasi';
            }
        })
        .catch(() => {
            showToast('danger', 'Terjadi kesalahan. Coba lagi.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Konfirmasi';
        });
    } else {
        // Hanya tunda (postpone) sesi tanpa tanggal pengganti
        fetch(`/ekstrakurikuler/sessions/${currentRescheduleSessionId}/postpone`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ alasan: alasan })
        })
        .then(res => res.json())
        .then(data => {
            bootstrap.Modal.getInstance(document.getElementById('rescheduleSessionModal')).hide();
            if (data.success) {
                showToast('success', 'Sesi berhasil ditunda. Status diubah ke "Ditunda".');
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast('danger', data.message || 'Gagal menunda sesi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Konfirmasi';
            }
        })
        .catch(() => {
            showToast('danger', 'Terjadi kesalahan. Coba lagi.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Konfirmasi';
        });
    }
}

function resetSessionToScheduled(sessionId, meetingNumber) {
    if (!confirm(`Apakah Anda yakin ingin mereset Sesi Pertemuan ${meetingNumber} kembali ke status "Terjadwal"? Waktu mulai/selesai aktual yang terisi akan dikosongkan.`)) {
        return;
    }

    const alasan = prompt('Alasan reset (opsional):', 'Sesi tidak sengaja dimulai') || 'Reset manual oleh admin';

    fetch(`/ekstrakurikuler/sessions/${sessionId}/reset-to-scheduled`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ alasan: alasan })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message || 'Sesi berhasil direset ke status Terjadwal.');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('danger', data.message || 'Gagal mereset sesi.');
        }
    })
    .catch(() => {
        showToast('danger', 'Terjadi kesalahan saat memproses reset sesi.');
    });
}
</script>
@endpush