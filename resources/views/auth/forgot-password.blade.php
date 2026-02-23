<x-guest-layout>
    <div class="container">
        <div class="row min-vh-100 d-flex align-items-center justify-content-center py-5">
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Logo/Brand Section -->
                        <div class="text-center mb-5">
                                <img src="{{ asset('images/logo-erlass.png') }}" alt="Erlass Logo" class="img-fluid" style="max-height: 80px;">
                            <h1 class="h3 fw-bold text-dark mb-1">Lupa Kata Sandi?</h1>
                            <p class="text-muted mb-0">
                                Jangan khawatir! Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk reset kata sandi.
                            </p>
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

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

                        <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                            @csrf

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
                                        :value="old('email')" 
                                        required 
                                        autofocus
                                        placeholder="nama@email.com"
                                        class="border-start-0 ps-0"
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-4">
                                <x-primary-button class="btn-lg py-3 fw-semibold" id="resetBtn">
                                    <span class="reset-text">
                                        <i class="bi bi-envelope-arrow-up me-2"></i>
                                        Kirim Tautan Reset
                                    </span>
                                    <span class="loading-text d-none">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Mengirim...
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
            // Form submission with loading state
            const forgotPasswordForm = document.getElementById('forgotPasswordForm');
            const resetBtn = document.getElementById('resetBtn');
            const resetText = resetBtn.querySelector('.reset-text');
            const loadingText = resetBtn.querySelector('.loading-text');

            forgotPasswordForm.addEventListener('submit', function() {
                resetBtn.disabled = true;
                resetText.classList.add('d-none');
                loadingText.classList.remove('d-none');
            });

            // Auto focus on email input when page loads
            document.getElementById('email').focus();
        });
    </script>
    @endpush
</x-guest-layout>
