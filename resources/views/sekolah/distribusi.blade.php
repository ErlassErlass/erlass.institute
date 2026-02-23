@extends('layouts.app')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #3b82f6; 
    }
    
    .icon-box {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.25rem;
    }

    .text-gradient-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800 mb-1">
                <span class="text-gradient-primary">Distribusi Sekolah</span>
            </h1>
            <p class="text-muted mb-0">Statistik persebaran siswa di jejaring sekolah mitra.</p>
        </div>
        <div class="d-none d-md-block">
            <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill border border-primary border-opacity-25">
                <i class="bi bi-building-check me-2"></i> {{ $sekolah_list->count() }} Sekolah Terdaftar
            </div>
        </div>
    </div>

    @if($sekolah_list->count() > 0)
        <div class="row g-4">
            @php $max_siswa = max(1, $sekolah_list->max('siswa_count')); @endphp
            @foreach($sekolah_list as $sekolah)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <a href="{{ route('sekolah.siswa', $sekolah->kodlan) }}" class="text-decoration-none">
                    <div class="card glass-card h-100 border-0">
                        <div class="card-body p-4 position-relative overflow-hidden">
                            <!-- Background Decoration (Subtler) -->
                            <div class="position-absolute bottom-0 end-0 mb-n2 me-n3 opacity-5 pe-none">
                                <i class="bi bi-building" style="font-size: 6rem; color: #3b82f6; opacity: 0.1;"></i>
                            </div>

                            <div class="d-flex align-items-start gap-3 mb-3 position-relative z-1">
                                <div class="icon-box bg-primary bg-opacity-10 text-primary shadow-sm flex-shrink-0">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1" style="min-height: 2.5em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;" title="{{ $sekolah->namasekolah }}">
                                        {{ $sekolah->namasekolah }}
                                    </h6>
                                    <small class="text-muted d-block">{{ $sekolah->kotkab ?? 'Kota/Kab' }}</small>
                                </div>
                            </div>

                            <div class="mt-3 position-relative z-1">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <span class="text-muted small">Total Siswa</span>
                                    <span class="h3 fw-bold text-primary mb-0">{{ $sekolah->siswa_count }}</span>
                                </div>
                                <div class="progress rounded-pill bg-light" style="height: 6px;">
                                    <div class="progress-bar rounded-pill bg-gradient-primary" 
                                         role="progressbar" 
                                         style="width: {{ ($sekolah->siswa_count / $max_siswa) * 100 }}%; background: linear-gradient(90deg, #3b82f6, #06b6d4);">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top border-light py-3 position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-primary fw-medium">Lihat Detail</span>
                                <i class="bi bi-arrow-right text-primary"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    @else
        <div class="row min-vh-50 align-items-center justify-content-center">
            <div class="col-md-6 text-center">
                <div class="mb-4">
                    <i class="bi bi-folder-x text-muted" style="font-size: 5rem; opacity: 0.2"></i>
                </div>
                <h4 class="fw-bold text-gray-800">Belum Ada Data</h4>
                <p class="text-muted">Belum ada sekolah yang memiliki data siswa saat ini.</p>
            </div>
        </div>
    @endif
</div>
@endsection
