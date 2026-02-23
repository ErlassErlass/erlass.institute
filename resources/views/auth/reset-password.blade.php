<x-guest-layout>
    <div class="container">
        <div class="row min-vh-100 d-flex align-items-center justify-content-center py-5">
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Logo/Brand Section -->
                        <div class="text-center mb-5">
                                <img src="{{ asset('images/logo-erlass.png') }}" alt="Erlass Logo" class="img-fluid" style="max-height: 80px;">
                            <h1 class="h3 fw-bold text-dark mb-1">Reset Kata Sandi</h1>
                            <p class="text-muted mb-0">
                                Masukkan kata sandi baru untuk akun Anda
                            </p>
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

                        <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
                            @csrf

                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <!-- Email Field -->
                            <div class="mb-4">
                                <x-input-label for="email" value="Alamat Email" class="fw-semibold" />
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <x-text-input 
                                        id="email" 
                                        name="email" 
                                        type="email" 
                                        :value="old('email', $request->email)" 
                                        required 
                                        autofocus
                                        autocomplete="username"
                                        placeholder="nama@email.com"
                                        class="border-start-0 ps-0"
                                        readonly
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Password Field -->
                            <div class="mb-4">
                                <x-input-label for="password" value="Kata Sandi Baru" class="fw-semibold" />
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <x-text-input 
                                        id="password"
                                        name="password"
                                        type="password"
                                        required 
                                        autocomplete="new-password"
                                        placeholder="Masukkan kata sandi baru"
                                        class="border-start-0 border-end-0 ps-0"
                                    />
                                    <button type="button" class="btn btn-outline-secondary border-start-0" id="togglePassword">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">
                                    Kata sandi minimal 8 karakter dengan kombinasi huruf, angka, dan simbol
                                </small>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="mb-4">
                                <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" class="fw-semibold" />
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock-fill text-muted"></i>
                                    </span>
                                    <x-text-input 
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        required 
                                        autocomplete="new-password"
                                        placeholder="Ulangi kata sandi baru"
                                        class="border-start-0 border-end-0 ps-0"
                                    />
                                    <button type="button" class="btn btn-outline-secondary border-start-0" id="togglePasswordConfirm">
                                        <i class="bi bi-eye" id="togglePasswordConfirmIcon"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-4">
                                <x-primary-button class="btn-lg py-3 fw-semibold" id="resetBtn">
                                    <span class="reset-text">
                                        <i class="bi bi-shield-check me-2"></i>
                                        Reset Kata Sandi
                                    </span>
                                    <span class="loading-text d-none">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Memproses...
                                    </span>
                                </x-primary-button>
                            </div>

                            <!-- Back to Login -->
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none text-primary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Kembali ke Halaman Login
                                </a>
                            </div>
                        </form>

                        <!-- Footer -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <small class="text-muted">
                                © {{ date('Y') }} Erlass Ekskul. Hak cipta dilindungi.
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility for password field
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

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

            // Toggle password visibility for confirmation field
            const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            const toggleConfirmIcon = document.getElementById('togglePasswordConfirmIcon');

            togglePasswordConfirm.addEventListener('click', function() {
                const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmInput.setAttribute('type', type);
                
                if (type === 'password') {
                    toggleConfirmIcon.classList.remove('bi-eye-slash');
                    toggleConfirmIcon.classList.add('bi-eye');
                } else {
                    toggleConfirmIcon.classList.remove('bi-eye');
                    toggleConfirmIcon.classList.add('bi-eye-slash');
                }
            });

            // Form submission with loading state
            const resetPasswordForm = document.getElementById('resetPasswordForm');
            const resetBtn = document.getElementById('resetBtn');
            const resetText = resetBtn.querySelector('.reset-text');
            const loadingText = resetBtn.querySelector('.loading-text');

            resetPasswordForm.addEventListener('submit', function() {
                resetBtn.disabled = true;
                resetText.classList.add('d-none');
                loadingText.classList.remove('d-none');
            });

            // Password strength indicator (optional)
            const passwordField = document.getElementById('password');
            passwordField.addEventListener('input', function() {
                // You can add password strength logic here
                const password = this.value;
                // Basic validation example - you can enhance this
                if (password.length >= 8) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });

            // Password confirmation matching
            const confirmField = document.getElementById('password_confirmation');
            confirmField.addEventListener('input', function() {
                const password = passwordField.value;
                const confirm = this.value;
                
                if (confirm && password === confirm) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else if (confirm) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        });
    </script>
    @endpush
</x-guest-layout>
