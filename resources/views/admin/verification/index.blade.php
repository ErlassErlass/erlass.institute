@extends('layouts.app')

@section('title', 'Verifikasi Instruktur')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1"><span class="text-gradient-primary">Verifikasi Instruktur</span></h1>
            <p class="text-muted mb-0">Kelola dan validasi pendaftaran instruktur baru.</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-white text-muted border px-3 py-2 rounded-pill">
                <i class="bi bi-clock me-1"></i> Update Terakhir: {{ now()->format('H:i') }}
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card glass-card border-0 h-100 position-relative overflow-hidden">
                <div class="card-body position-relative z-1 p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small text-uppercase fw-bold mb-1">Menunggu Verifikasi</p>
                            <h2 class="display-5 fw-bold text-warning mb-0">{{ $statistics['pending_verification'] }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                            <i class="bi bi-hourglass-split text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glass-card border-0 h-100 position-relative overflow-hidden">
                <div class="card-body position-relative z-1 p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small text-uppercase fw-bold mb-1">Aplikasi Minggu Ini</p>
                            <h2 class="display-5 fw-bold text-info mb-0">{{ $statistics['recent_applications'] }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded-circle">
                            <i class="bi bi-calendar-week text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glass-card border-0 h-100 position-relative overflow-hidden">
                <div class="card-body position-relative z-1 p-4">
                     <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small text-uppercase fw-bold mb-1">Terverifikasi</p>
                            <h2 class="display-5 fw-bold text-success mb-0">{{ $statistics['approved_instructors'] }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glass-card border-0 h-100 position-relative overflow-hidden">
                <div class="card-body position-relative z-1 p-4">
                     <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small text-uppercase fw-bold mb-1">Ditolak</p>
                            <h2 class="display-5 fw-bold text-danger mb-0">{{ $statistics['rejected_instructors'] }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-2 rounded-circle">
                            <i class="bi bi-x-circle text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pendingInstructors->isEmpty())
        <div class="card glass-card border-0">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-check-all text-muted" style="font-size: 2.5rem; opacity: 0.5"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-gray-800">Semua Beres!</h5>
                <p class="text-muted mb-0">Tidak ada instruktur yang menunggu verifikasi saat ini.</p>
            </div>
        </div>
    @else
        <h5 class="mb-3 fw-bold text-muted ps-2 border-start border-4 border-warning">Daftar Menunggu Approval</h5>
        <div class="row g-4">
            @foreach($pendingInstructors as $instructor)
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 h-100 hover-scale transition-all">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 48px; height: 48px; font-size: 1.2rem">
                                    {{ substr($instructor->nama_lengkap, 0, 1) }}
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 fw-bold text-dark">{{ Str::title($instructor->nama_lengkap) }}</h6>
                                    <small class="text-muted">{{ $instructor->email }}</small>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-light text-warning border border-warning rounded-pill">
                                        {{ $instructor->application_date ? $instructor->application_date->diffForHumans() : 'Baru saja' }}
                                    </span>
                                </div>
                            </div>
                            
                            <hr class="opacity-10 my-3">

                            <div class="row g-2 mb-3 small">
                                <div class="col-6">
                                    <span class="text-muted d-block mb-1">Pendidikan</span>
                                    <span class="fw-medium text-dark"><i class="bi bi-mortarboard me-1 text-primary"></i> {{ $instructor->pend_terakhir }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block mb-1">Telepon</span>
                                    <span class="fw-medium text-dark"><i class="bi bi-whatsapp me-1 text-success"></i> {{ $instructor->no_telephone }}</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <span class="text-muted small d-block mb-2">Kompetensi Utama</span>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-soft-primary text-primary border border-primary-subtle">{{ $instructor->kompetensi_1 ?? '-' }}</span>
                                    @if($instructor->kompetensi_2)
                                        <span class="badge bg-soft-info text-info border border-info-subtle">{{ $instructor->kompetensi_2 }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.verification.show', $instructor) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">
                                    <i class="bi bi-eye me-1"></i> Review Detail
                                </a>
                                <div class="d-flex gap-2">
                                     <button type="button" class="btn btn-danger btn-sm flex-fill rounded-pill" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal{{ $instructor->id }}">
                                        <i class="bi bi-x-circle me-1"></i> Tolak
                                    </button>
                                    <form action="{{ route('admin.verification.approve', $instructor) }}" method="POST" class="flex-fill d-grid">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill" 
                                                onclick="return confirm('Yakin ingin menyetujui?')">
                                            <i class="bi bi-check-circle me-1"></i> Setujui
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @push('modals')
                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal{{ $instructor->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-danger text-white border-0">
                                <h5 class="modal-title fs-6 fw-bold">Tolak Verifikasi - {{ $instructor->nama_lengkap }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.verification.reject', $instructor) }}" method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label for="rejection_reason{{ $instructor->id }}" class="form-label fw-bold small text-muted">Alesan Penolakan</label>
                                        <textarea class="form-control bg-light" id="rejection_reason{{ $instructor->id }}" name="rejection_reason" rows="4" required placeholder="Tuliskan alasan penolakan secara jelas..."></textarea>
                                        <div class="form-text small">Instruktur akan menerima notifikasi berisi alasan ini.</div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm">
                                        <i class="bi bi-x-circle me-1"></i> Konfirmasi Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endpush
            @endforeach
        </div>
    @endif
</div>

<style>
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .hover-scale { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-scale:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endsection