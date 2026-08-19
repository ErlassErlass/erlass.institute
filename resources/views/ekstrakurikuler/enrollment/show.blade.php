@extends('layouts.app')

@section('title', 'Detail Enrollment - ' . $enrollment->siswa->nama_lengkap)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-person-lines-fill me-2"></i>Detail Enrollment Siswa
                    </h1>
                    <p class="mb-0 text-muted">{{ $ekstrakurikuler->kategori_program }}</p>
                </div>
                <div class="d-flex gap-2">
                    @can('update', $ekstrakurikuler)
                        <a href="{{ route('ekstrakurikuler.enrollment.edit', [$ekstrakurikuler, $enrollment]) }}"
                           class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    @endcan
                    <a href="{{ route('ekstrakurikuler.enrollment.index', $ekstrakurikuler) }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Student Info Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-fill me-2"></i>Informasi Siswa</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                            <span class="text-white fs-4 fw-bold">{{ substr($enrollment->siswa->nama_lengkap, 0, 1) }}</span>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $enrollment->siswa->nama_lengkap }}</h5>
                            <p class="mb-0 text-muted">
                                @if($enrollment->siswa->nisn)
                                    NISN: {{ $enrollment->siswa->nisn }} &bull;
                                @endif
                                Kelas: {{ $enrollment->siswa->rombel ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="row g-3">
                        @if($enrollment->siswa->no_hp_orangtua)
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">No. HP Orang Tua</label>
                            <p class="mb-0">{{ $enrollment->siswa->no_hp_orangtua }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Enrollment Detail Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Detail Enrollment</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Program Ekstrakurikuler</label>
                            <p class="mb-0 fw-medium">{{ $ekstrakurikuler->kategori_program }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Rombel</label>
                            <p class="mb-0">
                                <span class="badge bg-light text-dark border">{{ $enrollment->rombel->nama_rombel ?? '-' }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $enrollment->status === 'aktif' ? 'success' : ($enrollment->status === 'lulus' ? 'info' : ($enrollment->status === 'nonaktif' ? 'secondary' : 'danger')) }} fs-6">
                                    {{ $enrollment->status_label }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Durasi</label>
                            <p class="mb-0">{{ $enrollment->durasi_enrollment }} hari</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Tanggal Daftar</label>
                            <p class="mb-0">{{ $enrollment->tanggal_daftar ? $enrollment->tanggal_daftar->isoFormat('D MMMM YYYY') : '-' }}</p>
                        </div>
                        @if($enrollment->tanggal_keluar)
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Tanggal Keluar</label>
                            <p class="mb-0">{{ $enrollment->tanggal_keluar->isoFormat('D MMMM YYYY') }}</p>
                        </div>
                        @endif
                        @if($enrollment->alasan_keluar)
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Alasan Keluar</label>
                            <p class="mb-0">{{ $enrollment->alasan_keluar }}</p>
                        </div>
                        @endif
                        @if($enrollment->catatan)
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Catatan</label>
                            <p class="mb-0">{{ $enrollment->catatan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Audit Info Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Informasi Audit</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Dibuat oleh</label>
                            <p class="mb-0">{{ $enrollment->creator->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Dibuat pada</label>
                            <p class="mb-0">{{ $enrollment->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Diperbarui oleh</label>
                            <p class="mb-0">{{ $enrollment->updater->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Diperbarui pada</label>
                            <p class="mb-0">{{ $enrollment->updated_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Link ke Aksi Bulk --}}
            @can('update', $ekstrakurikuler)
            <div class="alert alert-light border d-flex align-items-center gap-3 mt-2">
                <i class="bi bi-info-circle fs-5 text-primary"></i>
                <div>
                    <strong>Kelola enrollment siswa ini</strong> melalui halaman Manajemen Siswa.<br>
                    <small class="text-muted">Gunakan <em>Aksi Bulk</em> untuk Keluarkan, Pindah Rombel, Aktifkan, dan lainnya.</small>
                </div>
                <a href="{{ route('ekstrakurikuler.enrollment.index', $ekstrakurikuler) }}" class="btn btn-sm btn-primary ms-auto text-nowrap">
                    <i class="bi bi-people-fill me-1"></i> Manajemen Siswa
                </a>
            </div>
            @endcan

        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 56px;
    height: 56px;
}
</style>
@endsection
