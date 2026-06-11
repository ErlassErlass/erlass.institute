@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="text-center">
        <h1 style="font-size: 8rem; font-weight: 800; color: #ff6b6b; text-shadow: 4px 4px 0px #ffeaa7;">4<span style="display:inline-block; transform: rotate(15deg);">0</span>4</h1>
        
        <div class="mb-4 cute-float">
            <!-- Cute Cat SVG -->
            <svg width="180" height="180" viewBox="0 0 24 24" fill="none" stroke="#6c5ce7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5c.67 0 1.35.09 2 .26 1.78-2 5.03-2.84 6.42-2.26 1.4.58-.42 4.18-.42 4.18C21.31 9 22 10.4 22 12c0 5.52-4.48 10-10 10S2 17.52 2 12c0-1.6.69-3 2-4.82 0 0-1.82-3.6-.42-4.18 1.4-.58 4.64.26 6.42 2.26.65-.17 1.33-.26 2-.26z"></path>
                <circle cx="9" cy="11" r="1.5" fill="#6c5ce7"></circle>
                <circle cx="15" cy="11" r="1.5" fill="#6c5ce7"></circle>
                <path d="M12 14c-1 0-1.5-.5-1.5-.5"></path>
            </svg>
        </div>

        <h2 class="h3 font-weight-bold mb-3" style="color: #2d3436;">Oops! Halaman Nyasar...</h2>
        <p class="text-muted mb-4 lead" style="max-width: 500px; margin: 0 auto;">Kucing virtual kami sudah mencari ke seluruh sudut peladen, tapi halaman yang kamu mau nggak ketemu nih. Meow! 🐱</p>
        
        <a href="{{ url('/') }}" class="btn btn-lg rounded-pill px-4 shadow-sm text-white mt-3" style="background: linear-gradient(135deg, #6c5ce7, #a29bfe); border: none; font-weight: 600;">
            <i class="fas fa-home me-2"></i> Yuk, Pulang ke Beranda
        </a>
    </div>
</div>

<style>
    .cute-float {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
</style>
@endsection
