@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="text-center">
        <h1 style="font-size: 8rem; font-weight: 800; color: #00b894; text-shadow: 4px 4px 0px #55efc4;">4<span style="display:inline-block; transform: rotate(8deg);">0</span>1</h1>
        
        <div class="mb-4 cute-float">
            <!-- Cute Key / Padlock SVG -->
            <svg width="180" height="180" viewBox="0 0 24 24" fill="none" stroke="#00b894" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="8" cy="15" r="4" fill="#55efc4" fill-opacity="0.3" stroke="#00b894"></circle>
                <path d="M10.85 12.15L19 4"></path>
                <path d="M18 5l2 2"></path>
                <path d="M15 8l2 2"></path>
            </svg>
        </div>

        <h2 class="h3 font-weight-bold mb-3" style="color: #2d3436;">Silakan Login Terlebih Dahulu</h2>
        <p class="text-muted mb-4 lead" style="max-width: 500px; margin: 0 auto;">
            Anda memerlukan akses akun Erlass Institute untuk membuka halaman ini. Silakan masuk terlebih dahulu dengan akun Anda! 🔑🔑
        </p>
        
        <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
            <a href="{{ route('login') }}" class="btn btn-lg rounded-pill px-4 shadow-sm text-white" style="background: linear-gradient(135deg, #00b894, #00cec9); border: none; font-weight: 600;">
                <i class="fas fa-sign-in-alt me-2"></i> Ke Halaman Login
            </a>
            <a href="{{ url('/') }}" class="btn btn-lg rounded-pill px-4 shadow-sm btn-outline-secondary" style="font-weight: 600;">
                <i class="fas fa-home me-2"></i> Beranda
            </a>
        </div>
    </div>
</div>

<style>
    .cute-float {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-12px); }
    }
</style>
@endsection
