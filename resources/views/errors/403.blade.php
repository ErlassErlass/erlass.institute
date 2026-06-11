@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="text-center">
        <h1 style="font-size: 8rem; font-weight: 800; color: #fdcb6e; text-shadow: 4px 4px 0px #ffeaa7;">4<span style="display:inline-block; transform: rotate(-10deg);">0</span>3</h1>
        
        <div class="mb-4 cute-bounce">
            <!-- Cute Security SVG -->
            <svg width="180" height="180" viewBox="0 0 24 24" fill="none" stroke="#e17055" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" fill="#ffeaa7" stroke="#e17055"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                <circle cx="12" cy="16" r="1" fill="#e17055"></circle>
            </svg>
        </div>

        <h2 class="h3 font-weight-bold mb-3" style="color: #2d3436;">Hayo, Mau Ke Mana?</h2>
        <p class="text-muted mb-4 lead" style="max-width: 500px; margin: 0 auto;">Ups! Halaman ini dikunci karena kamu nggak punya kunci rahasia buat masuk ke sini. Jangan mengintip ya! 🕵️‍♂️</p>
        
        <button onclick="window.history.back()" class="btn btn-lg rounded-pill px-4 shadow-sm text-white mt-3" style="background: linear-gradient(135deg, #e17055, #fab1a0); border: none; font-weight: 600;">
            <i class="fas fa-arrow-left me-2"></i> Balik Arah Saja
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
