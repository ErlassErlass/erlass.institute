@extends('layouts.guest')

@section('title', 'Login - Erlass Ekskul')

@push('styles')
<style>
    body {
        background-color: #fff !important;
        background-image: none !important;
        padding: 0 !important;
        display: block !important;
    }
    
    .login-container {
        height: 100vh; overflow: hidden;
        display: flex;
    }

    .brand-panel {
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
        color: white;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 3rem;
        position: relative;
        overflow: hidden;
    }

    .brand-panel::before {
        content: '';
        position: absolute;
        top: -10%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .brand-panel::after {
        content: '';
        position: absolute;
        bottom: -5%;
        left: -5%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .form-panel {
        overflow-y: auto;
        flex: 1;
        background: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 4rem;
        position: relative;
    }

    .login-card {
        max-width: 450px;
        width: 100%;
        margin: 0 auto;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
    }

    .stat-badge {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 500;
        margin-top: 2rem;
    }

    @media (max-width: 992px) {
        .brand-panel {
            display: none;
        }
        .form-panel {
        overflow-y: auto;
            padding: 2rem;
        }
    }
    
    .form-control { border-color: #ced4da !important; }
    .bg-input {
        color: #64748b !important;
        border: 1px solid #ced4da !important;
        background-color: #f8fafc !important;
    }

    .wa-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #25d366;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        font-size: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .wa-float:hover {
        background-color: #128c7e;
        color: #FFF;
        transform: scale(1.1);
    }
</style>
@endpush

@section('content')
<div class="login-container">
    <!-- Panel Kiri: Branding -->
    <div class="brand-panel text-center">
        <div class="mb-4">
            <img src="{{ asset('images/logo-erlass.png') }}" alt="Erlass Logo" class="img-fluid" style="max-height: 120px; filter: brightness(0) invert(1);">
        </div>
        <h1 class="display-5 fw-bold mb-3">Erlass Ekskul</h1>
        <p class="lead opacity-90">Sistem Manajemen Ekstrakurikuler Modern & Terintegrasi.</p>
        <div class="stat-badge">
            <i class="bi bi-people-fill me-2"></i>
            Memberdayakan +70 Instruktur Berbakat
        </div>
    </div>

    <!-- Panel Kanan: Form -->
    <div class="form-panel">
        <div class="login-card">
            <div class="mb-5">
                <h2 class="fw-bold text-dark mb-2">Selamat Datang</h2>
                <p class="text-muted">Masuk ke akun Erlass Ekskul Anda</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0 mt-2 small">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Session Status -->
            @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- PWA Install Banner -->
            <div id="pwa-install-banner" class="alert alert-info d-flex align-items-center justify-content-between rounded-4 d-none mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-phone-vibrate fs-4 me-3"></i>
                    <div>
                        <strong class="d-block small">Gunakan Aplikasi Lebih Cepat</strong>
                        <span class="x-small text-muted" style="font-size: 0.75rem;">Pasang Erlass Ekskul di layar utama ponsel Anda.</span>
                    </div>
                </div>
                <button class="btn btn-primary btn-sm px-3 py-2 fw-semibold" id="btn-pwa-install-login">Install</button>
            </div>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email Field -->
                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">Alamat Email / ID Instruktur</label>
                    <div class="input-group">
                        <span class="input-group-text bg-input border-end-0">
                            <i class="bi bi-person-badge text-muted"></i>
                        </span>
                        <input
                            id="email"
                            name="email"
                            type="text"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="email@contoh.com atau ICE..."
                            class="form-control border-start-0 ps-0" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-input border-end-0">
                            <i class="bi bi-lock text-muted"></i>
                        </span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan kata sandi"
                            class="form-control border-x-0 ps-0" />
                        <button type="button" class="btn bg-input border border-start-0 text-muted" id="togglePassword">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" @checked(old('remember'))>
                        <label class="form-check-label text-muted" for="remember">Ingat saya</label>
                    </div>
                    @if (Route::has('password.request'))
                    <a class="text-decoration-none small text-primary fw-medium" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                    @endif
                </div>

                <!-- Login Button -->
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm" id="loginBtn">
                        <span class="login-text">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Login
                        </span>
                        <span class="loading-text d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Onboarding Section -->
            <div class="text-center mt-5">
                <div class="p-3 rounded-4 bg-light">
                    <p class="text-muted small mb-2">Baru bergabung sebagai Instruktur?</p>
                    <a href="{{ route('instructor.register') }}" class="btn btn-outline-primary w-100 fw-bold">
                        Daftar Akun Baru
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-5 text-muted small">
                © {{ date('Y') }} Erlass Institute. Hak cipta dilindungi.
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Support Floating Button -->
<a href="whatsapp://send?phone=6281234567890&text=Halo%20Admin,%20saya%20butuh%20bantuan%20login%20Erlass%20Ekskul" 
   target="_blank" rel="noopener"
   class="wa-float" 
   title="Hubungi Admin via WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (togglePassword && passwordInput && toggleIcon) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                toggleIcon.classList.toggle('bi-eye');
                toggleIcon.classList.toggle('bi-eye-slash');
            });
        }

        // Handle loading state
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const loginText = document.querySelector('.login-text');
        const loadingText = document.querySelector('.loading-text');

        if (loginForm && loginBtn) {
            loginForm.addEventListener('submit', function() {
                loginBtn.disabled = true;
                loginText.classList.add('d-none');
                loadingText.classList.remove('d-none');
            });
        }
    });
</script>
@endpush
