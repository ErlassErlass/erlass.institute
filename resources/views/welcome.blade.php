@extends('layouts.guest')

@push('styles')
<style>
    body { background-color: #fff !important; display: block !important; padding: 0 !important; }
    .gateway-container { display: flex; height: 100vh; overflow: hidden; }
    .brand-side { 
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%); 
        color: white; flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 3rem; position: relative; overflow: hidden;
    }
    .brand-side::before {
        content: '';
        position: absolute;
        top: -10%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    .brand-side::after {
        content: '';
        position: absolute;
        bottom: -5%;
        left: -5%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
    }
    .backdrop-blur { backdrop-filter: blur(8px); }
    .action-side { flex: 1; background: white; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 4rem; }
    .hover-lift { transition: all 0.3s ease; }
    .hover-lift:hover { transform: translateY(-5px); }
    .btn-lg { border-radius: 16px; }
    @media (max-width: 992px) {
        .gateway-container { flex-direction: column; overflow-y: auto; height: auto; }
        .brand-side { padding: 4rem 2rem; }
        .action-side { padding: 3rem 2rem; }
    }
</style>
@endpush

@section('content')
<div class="gateway-container">
    <div class="brand-side">
        <div class="brand-content text-center">
            <div class="mb-4">
                <img src="{{ asset('images/logo-erlass.png') }}" alt="Erlass Logo" class="img-fluid" style="max-height: 120px; filter: brightness(0) invert(1);">
            </div>
            <h1 class="display-5 fw-bold mb-3 text-white">Transformasi Digital<br>Ekstrakurikuler Sekolah</h1>
            <p class="lead text-white-50 mb-5">Solusi terintegrasi untuk manajemen jadwal, absensi real-time, dan pelaporan kegiatan ekstrakurikuler yang efisien dan transparan.</p>
            
            <div class="stat-badge d-inline-flex align-items-center bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-20 px-4 py-2 rounded-pill text-white">
                <i class="bi bi-people-fill me-2"></i>
                <span>Bergabung dengan +70 Instruktur Berbakat</span>
            </div>
        </div>
    </div>
    <div class="action-side">
        <div class="action-content w-100" style="max-width: 450px;">
            <div class="mb-5 text-center text-lg-start">
                <h2 class="fw-bold text-dark mb-2">Selamat Datang di<br>Erlass Prokreatif Indonesia</h2>
                <p class="text-muted"></p>
            </div>

            <!-- Main Actions -->
            <div class="d-grid gap-3 mb-5">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm hover-lift">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Masuk ke Dashboard
                </a>
                
                <a href="{{ route('instructor.register') }}" class="btn btn-outline-primary btn-lg py-3 fw-bold hover-lift">
                    <i class="bi bi-person-video3 me-2"></i>
                    Mulai Pendaftaran Instruktur
                </a>
            </div>

            <!-- Visual Mockup Card -->
            <div class="mockup-card bg-light p-4 rounded-4 border border-light shadow-sm d-none d-sm-block">
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                    <div class="bg-success bg-opacity-10 p-2 rounded-circle text-success">
                        <i class="bi bi-activity"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-dark fw-bold small">Live Dashboard</h6>
                        <small class="text-muted" style="font-size: 0.7rem;">Monitoring Kelas Berjalan</small>
                    </div>
                </div>
                <div class="vstack gap-2">
                    @forelse($liveSessions as $session)
                        @php
                            $now = now()->toTimeString();
                            $isLive = $now >= $session->jam_mulai_terjadwal && $now <= $session->jam_selesai_terjadwal;
                        @endphp
                        <div class="small bg-white p-2 rounded border border-light d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-dark">{{ $session->ekstrakurikuler->kategori_program }}</span>
                                <span class="text-muted" style="font-size: 0.65rem;">{{ $session->ekstrakurikuler->sekolah->namasekolah }}</span>
                            </div>
                            @if($isLive)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">
                                    <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                                    LIVE
                                </span>
                            @else
                                <span class="text-muted" style="font-size: 0.7rem;">{{ date('H:i', strtotime($session->jam_mulai_terjadwal)) }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="small bg-white p-2 rounded border border-light text-center">
                            <span class="text-muted" style="font-size: 0.7rem;">Tidak ada jadwal hari ini.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
