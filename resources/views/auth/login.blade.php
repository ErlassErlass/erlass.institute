@extends('layouts.guest')

@section('title', 'Login - Erlass Ekskul')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Logo/Brand Section -->
                    <div class="text-center mb-5">
                            <img src="{{ asset('images/logo-erlass.png') }}" alt="Erlass Logo" class="img-fluid" style="max-height: 80px;">
                        <h1 class="h3 fw-bold text-dark mb-1">Selamat Datang</h1>
                        <p class="text-muted mb-0">Masuk ke akun Erlass Ekskul Anda</p>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Terjadi kesalahan!</strong>
                        <ul class="mb-0 mt-2">
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

                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <!-- Email Field -->
                        <div class="mb-4">
                            <x-input-label for="email" value="Alamat Email / ID Instruktur" class="fw-semibold" />
                            <div class="input-group">
                                <span class="input-group-text bg-input border-end-0">
                                    <i class="bi bi-person-badge text-muted"></i>
                                </span>
                                <x-text-input
                                    id="email"
                                    name="email"
                                    type="text"
                                    :value="old('email')"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="email@contoh.com atau ICE20261"
                                    class="border-start-0 ps-0" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password Field -->
                        <div class="mb-4">
                            <x-input-label for="password" value="Kata Sandi" class="fw-semibold" />
                            <div class="input-group">
                                <span class="input-group-text bg-input border-end-0">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <x-text-input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan kata sandi"
                                    class="border-start-0 border-end-0 ps-0" />
                                <button type="button" class="btn btn-outline-secondary border-start-0" id="togglePassword">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            {{-- Grup Checkbox "Ingat Saya" --}}
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" @checked(old('remember'))>
                                <label class="form-check-label text-muted" for="remember">
                                    Ingat saya
                                </label>
                            </div>

                            {{-- Link "Lupa Kata Sandi" --}}
                            @if (Route::has('password.request'))
                            <a class="text-decoration-none text-sm text-primary" href="{{ route('password.request') }}">
                                Lupa kata sandi?
                            </a>
                            @endif
                        </div>

                        <!-- Login Button -->
                        <div class="d-grid">
                            <x-primary-button class="btn-lg py-3 fw-semibold" id="loginBtn">
                                <span class="login-text">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Login
                                </span>
                                <span class="loading-text d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Memproses...
                                </span>
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Footer -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-muted">
                            © {{ date('Y') }} Coding Erlass. Hak cipta dilindungi.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Additional Help Text -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Butuh bantuan? Hubungi administrator sistem
                </small>
            </div>
        </div>
    </div>
</div>
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

                if (type === 'password') {
                    toggleIcon.classList.remove('bi-eye-slash');
                    toggleIcon.classList.add('bi-eye');
                } else {
                    toggleIcon.classList.remove('bi-eye');
                    toggleIcon.classList.add('bi-eye-slash');
                }
            });
        }

        // Login form submission with loading state
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');

        if (loginForm && loginBtn) {
            const loginText = loginBtn.querySelector('.login-text');
            const loadingText = loginBtn.querySelector('.loading-text');

            loginForm.addEventListener('submit', function() {
                loginBtn.disabled = true;
                if (loginText) loginText.classList.add('d-none');
                if (loadingText) loadingText.classList.remove('d-none');
            });
        }

        // Auto focus on email input when page loads
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.focus();
        }
    });
</script>
@endpush