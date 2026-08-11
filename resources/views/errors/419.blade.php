@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="text-center">
        <h1 style="font-size: 8rem; font-weight: 800; color: #e67e22; text-shadow: 4px 4px 0px #f39c12;">4<span style="display:inline-block; transform: rotate(10deg);">1</span>9</h1>
        
        <div class="mb-4 cute-pulse">
            <!-- Cute Hourglass / Timeout SVG -->
            <svg width="180" height="180" viewBox="0 0 24 24" fill="none" stroke="#d35400" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 22h14"></path>
                <path d="M5 2h14"></path>
                <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"></path>
                <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"></path>
                <circle cx="12" cy="16" r="1" fill="#e67e22"></circle>
            </svg>
        </div>

        <h2 class="h3 font-weight-bold mb-3" style="color: #2d3436;">Waktu Sesi Telah Berakhir</h2>
        <p class="text-muted mb-4 lead" style="max-width: 520px; margin: 0 auto;">
            Halaman ini didiamkan terlalu lama sehingga masa berlaku sesi keamanan (CSRF Token) habis. Jangan khawatir, data Anda aman! ⏳🔒
        </p>
        
        <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
            <button onclick="window.location.reload()" class="btn btn-lg rounded-pill px-4 shadow-sm text-white" style="background: linear-gradient(135deg, #e67e22, #f39c12); border: none; font-weight: 600;">
                <i class="fas fa-sync-alt me-2"></i> Muat Ulang Halaman
            </button>
            <a href="{{ url('/') }}" class="btn btn-lg rounded-pill px-4 shadow-sm btn-outline-secondary" style="font-weight: 600;">
                <i class="fas fa-home me-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<style>
    .cute-pulse {
        animation: pulse 2.5s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
</style>
@endsection
