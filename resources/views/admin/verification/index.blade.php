@extends('layouts.app')

@section('title', 'Verifikasi Instruktur')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-patch-check me-2 text-primary"></i>
                Verifikasi Instruktur
            </h2>
            <p class="text-muted mt-1 mb-0">Kelola verifikasi instruktur yang mendaftar</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">{{ $statistics['pending_verification'] }}</h5>
                            <small class="opacity-75">Menunggu Verifikasi</small>
                        </div>
                        <i class="bi bi-clock-history fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">{{ $statistics['recent_applications'] }}</h5>
                            <small class="opacity-75">Aplikasi Minggu Ini</small>
                        </div>
                        <i class="bi bi-calendar-week fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">{{ $statistics['approved_instructors'] }}</h5>
                            <small class="opacity-75">Total Terverifikasi</small>
                        </div>
                        <i class="bi bi-patch-check fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">{{ $statistics['rejected_instructors'] }}</h5>
                            <small class="opacity-75">Total Ditolak</small>
                        </div>
                        <i class="bi bi-x-circle fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pendingInstructors->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-clipboard-check fs-1 text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada instruktur yang menunggu verifikasi</h5>
                <p class="text-muted">Semua aplikasi verifikasi telah diproses.</p>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($pendingInstructors as $instructor)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning bg-opacity-10">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-warning">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Menunggu Verifikasi
                                </h6>
                                <small class="text-muted">
                                    {{ $instructor->application_date->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 p-2 rounded-circle me-3">
                                    <i class="bi bi-person-video2 text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $instructor->nama_lengkap }}</h6>
                                    <small class="text-muted">{{ $instructor->email }}</small>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Pendidikan:</small>
                                    <span class="fw-medium">{{ $instructor->pend_terakhir }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Telepon:</small>
                                    <span class="fw-medium">{{ $instructor->no_telephone }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block">Kompetensi:</small>
                                <div>
                                    <span class="badge bg-primary me-1">{{ $instructor->kompetensi_1 }}</span>
                                    @if($instructor->kompetensi_2)
                                        <span class="badge bg-secondary">{{ $instructor->kompetensi_2 }}</span>
                                    @endif
                                </div>
                            </div>

                            @if($instructor->verification_documents)
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Dokumen:</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($instructor->verification_documents as $type => $path)
                                            <a href="{{ Storage::url($path) }}" target="_blank" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                                {{ ucwords(str_replace('_', ' ', $type)) }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="d-grid gap-2">
                                <div class="btn-group" role="group">
                                    <form action="{{ route('admin.verification.approve', $instructor) }}" method="POST" class="flex-fill">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100" 
                                                onclick="return confirm('Yakin ingin menyetujui verifikasi instruktur ini?')">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Setujui
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal{{ $instructor->id }}">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Tolak
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal{{ $instructor->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tolak Verifikasi - {{ $instructor->nama_lengkap }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.verification.reject', $instructor) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="rejection_reason{{ $instructor->id }}" class="form-label">
                                            Alasan Penolakan <span class="text-danger">*</span>
                                        </label>
                                        <textarea 
                                            class="form-control" 
                                            id="rejection_reason{{ $instructor->id }}" 
                                            name="rejection_reason" 
                                            rows="4" 
                                            required
                                            placeholder="Jelaskan alasan penolakan verifikasi..."
                                        ></textarea>
                                        <div class="form-text">
                                            Minimal 10 karakter. Instruktur akan menerima alasan ini.
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Tolak Verifikasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection