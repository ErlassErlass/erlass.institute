@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="text-center">
        <h1 style="font-size: 8rem; font-weight: 800; color: #6c5ce7; text-shadow: 4px 4px 0px #a29bfe;">4<span style="display:inline-block; transform: rotate(-10deg);">2</span>9</h1>
        
        <div class="mb-4 cute-bounce">
            <!-- Cute Speed Limit / Traffic Light SVG -->
            <svg width="180" height="180" viewBox="0 0 24 24" fill="none" stroke="#6c5ce7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="6" y="3" width="12" height="18" rx="4" fill="#a29bfe" fill-opacity="0.2" stroke="#6c5ce7"></rect>
                <circle cx="12" cy="7" r="2" fill="#ff7675"></circle>
                <circle cx="12" cy="12" r="2" fill="#ffeaa7"></circle>
                <circle cx="12" cy="17" r="2" fill="#55efc4"></circle>
            </svg>
        </div>

        <h2 class="h3 font-weight-bold mb-3" style="color: #2d3436;">Pelan-Pelan Ya!</h2>
        <p class="text-muted mb-4 lead" style="max-width: 500px; margin: 0 auto;">
            Sistem mendeteksi terlalu banyak permintaan dalam waktu singkat. Demi keamanan dan stabilitas server, mohon istirahat sejenak lalu coba lagi nanti. 🚦💨
        </p>
        
        <button onclick="window.history.back()" class="btn btn-lg rounded-pill px-4 shadow-sm text-white mt-3" style="background: linear-gradient(135deg, #6c5ce7, #a29bfe); border: none; font-weight: 600;">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Halaman Sebelumnya
        </button>
    </div>
</div>

<style>
    .cute-bounce {
        animation: bounce 2s ease-in-out infinite;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
</style>
@endsection
