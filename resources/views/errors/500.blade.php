@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="text-center">
        <h1 style="font-size: 8rem; font-weight: 800; color: #d63031; text-shadow: 4px 4px 0px #ff7675;">5<span style="display:inline-block; transform: rotate(-5deg);">0</span><span style="display:inline-block; transform: rotate(10deg);">0</span></h1>
        
        <div class="mb-4 cute-shake">
            <!-- Cute Bot Error SVG -->
            <svg width="180" height="180" viewBox="0 0 24 24" fill="none" stroke="#d63031" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="10" rx="2" ry="2" fill="#ff7675" stroke="#d63031" fill-opacity="0.2"></rect>
                <circle cx="12" cy="5" r="2"></circle>
                <path d="M12 7v4"></path>
                <line x1="8" y1="16" x2="8.01" y2="16" stroke-width="3"></line>
                <line x1="16" y1="16" x2="16.01" y2="16" stroke-width="3"></line>
                <path d="M8 12h8"></path>
            </svg>
        </div>

        <h2 class="h3 font-weight-bold mb-3" style="color: #2d3436;">Waduh, Mesinnya Batuk!</h2>
        <p class="text-muted mb-4 lead" style="max-width: 500px; margin: 0 auto;">Robot peladen kami kelelahan dan tersandung kabel. Teknisi terbaik (mungkin kelinci) sedang menolongnya sekarang. Mohon tunggu sebentar! 🤖💤</p>
        
        <a href="{{ url('/') }}" class="btn btn-lg rounded-pill px-4 shadow-sm text-white mt-3" style="background: linear-gradient(135deg, #d63031, #ff7675); border: none; font-weight: 600;">
            <i class="fas fa-redo-alt me-2"></i> Refresh dari Beranda
        </a>
    </div>
</div>

<style>
    .cute-shake {
        animation: shake 2.5s ease-in-out infinite;
    }
    @keyframes shake {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-3deg); }
        75% { transform: rotate(3deg); }
    }
</style>
@endsection
