<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun - Erlass Ekskul</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="container">
        <div class="row min-vh-100 d-flex align-items-center justify-content-center py-5">
            <div class="col-sm-10 col-md-8 col-lg-7 col-xl-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Logo/Brand Section -->
                        <div class="text-center mb-4">
                                <img src="{{ asset('images/logo-erlass.png') }}" alt="Erlass Logo" class="img-fluid" style="max-height: 80px;">
                            <h1 class="h3 fw-bold text-dark mb-1">Daftar Akun Baru</h1>
                            <p class="text-muted mb-0">Bergabunglah dengan Erlass Ekskul</p>
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

                        <form method="POST" action="{{ route('register') }}" id="registerForm">
                            @csrf

                            <!-- Personal Information Section -->
                            <div class="mb-4">
                                <h5 class="text-dark mb-3 border-bottom pb-2">
                                    <i class="bi bi-person me-2 text-primary"></i>Informasi Personal
                                </h5>
                                
                                <div class="row g-3">
                                    <!-- Nama Lengkap -->
                                    <div class="col-12">
                                        <x-input-label for="nama_lengkap" value="Nama Lengkap" class="fw-semibold" />
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-person text-muted"></i>
                                            </span>
                                            <x-text-input 
                                                id="nama_lengkap" 
                                                name="nama_lengkap" 
                                                type="text" 
                                                :value="old('nama_lengkap')" 
                                                required 
                                                autofocus 
                                                placeholder="Masukkan nama lengkap"
                                                class="border-start-0 ps-0"
                                            />
                                        </div>
                                        <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
                                    </div>

                                    <!-- Email -->
                                    <div class="col-12">
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
                                                placeholder="nama@email.com"
                                                class="border-start-0 ps-0"
                                            />
                                        </div>
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>

                                    <!-- Tanggal Lahir -->
                                    <div class="col-12">
                                        <x-input-label for="tanggal_lahir" value="Tanggal Lahir" class="fw-semibold" />
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-calendar text-muted"></i>
                                            </span>
                                            <x-text-input 
                                                id="tanggal_lahir" 
                                                name="tanggal_lahir" 
                                                type="text" 
                                                :value="old('tanggal_lahir')" 
                                                required
                                                class="border-start-0 ps-0 datepicker"
                                                placeholder="DD-MM-YYYY"
                                            />
                                        </div>
                                        <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                                    </div>

                                    <!-- No. Telephone -->
                                    <div class="col-12">
                                        <x-input-label for="no_telephone" value="Nomor Telepon" class="fw-semibold" />
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-telephone text-muted"></i>
                                            </span>
                                            <x-text-input 
                                                id="no_telephone" 
                                                name="no_telephone" 
                                                type="text" 
                                                :value="old('no_telephone')" 
                                                required
                                                placeholder="08xxx"
                                                class="border-start-0 ps-0"
                                            />
                                        </div>
                                        <x-input-error :messages="$errors->get('no_telephone')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Authentication Section -->
                            <div class="mb-4">
                                <h5 class="text-dark mb-3 border-bottom pb-2">
                                    <i class="bi bi-shield-lock me-2 text-primary"></i>Keamanan Akun
                                </h5>
                                
                                <div class="row g-3">
                                    <!-- Password -->
                                    <div class="col-12">
                                        <x-input-label for="password" value="Kata Sandi" class="fw-semibold" />
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
                                                placeholder="Minimal 8 karakter"
                                                class="border-start-0 border-end-0 ps-0"
                                            />
                                            <button type="button" class="btn btn-outline-secondary border-start-0" id="togglePassword">
                                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                            </button>
                                        </div>
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="col-12">
                                        <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" class="fw-semibold" />
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-lock text-muted"></i>
                                            </span>
                                            <x-text-input 
                                                id="password_confirmation"
                                                name="password_confirmation"
                                                type="password"
                                                required 
                                                autocomplete="new-password"
                                                placeholder="Ulangi kata sandi"
                                                class="border-start-0 ps-0"
                                            />
                                        </div>
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Professional Information Section -->
                            <div class="mb-4">
                                <h5 class="text-dark mb-3 border-bottom pb-2">
                                    <i class="bi bi-briefcase me-2 text-primary"></i>Informasi Profesional
                                </h5>
                                
                                <div class="row g-3">
                                    <!-- Agama -->
                                    <div class="col-md-6">
                                        <x-input-label for="agama" value="Agama" class="fw-semibold" />
                                        <select id="agama" name="agama" class="form-select" required>
                                            <option value="">Pilih Agama</option>
                                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                            <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                            <option value="Lainnya" {{ old('agama') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('agama')" class="mt-2" />
                                    </div>

                                    <!-- Pendidikan Terakhir -->
                                    <div class="col-md-6">
                                        <x-input-label for="pend_terakhir" value="Pendidikan Terakhir" class="fw-semibold" />
                                        <select id="pend_terakhir" name="pend_terakhir" class="form-select" required>
                                            <option value="">Pilih Pendidikan</option>
                                            <option value="SMA/SMK" {{ old('pend_terakhir') == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                            <option value="D3" {{ old('pend_terakhir') == 'D3' ? 'selected' : '' }}>D3</option>
                                            <option value="S1" {{ old('pend_terakhir') == 'S1' ? 'selected' : '' }}>S1</option>
                                            <option value="S2" {{ old('pend_terakhir') == 'S2' ? 'selected' : '' }}>S2</option>
                                            <option value="S3" {{ old('pend_terakhir') == 'S3' ? 'selected' : '' }}>S3</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('pend_terakhir')" class="mt-2" />
                                    </div>

                                    <!-- Kompetensi 1 -->
                                    <div class="col-12">
                                        <x-input-label for="kompetensi_1" value="Kompetensi Utama" class="fw-semibold" />
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-award text-muted"></i>
                                            </span>
                                            <x-text-input 
                                                id="kompetensi_1" 
                                                name="kompetensi_1" 
                                                type="text" 
                                                :value="old('kompetensi_1')" 
                                                required
                                                placeholder="Contoh: Matematika, Bahasa Indonesia, dll"
                                                class="border-start-0 ps-0"
                                            />
                                        </div>
                                        <x-input-error :messages="$errors->get('kompetensi_1')" class="mt-2" />
                                    </div>

                                    <!-- Kompetensi 2 (Optional) -->
                                    <div class="col-12">
                                        <x-input-label for="kompetensi_2" value="Kompetensi Tambahan (Opsional)" class="fw-semibold" />
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-star text-muted"></i>
                                            </span>
                                            <x-text-input 
                                                id="kompetensi_2" 
                                                name="kompetensi_2" 
                                                type="text" 
                                                :value="old('kompetensi_2')"
                                                placeholder="Kompetensi kedua (opsional)"
                                                class="border-start-0 ps-0"
                                            />
                                        </div>
                                        <x-input-error :messages="$errors->get('kompetensi_2')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <x-primary-button class="btn-lg py-3 fw-semibold" id="registerBtn">
                                    <span class="register-text">
                                        <i class="bi bi-person-plus me-2"></i>
                                        Daftar Sekarang
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
                            <p class="text-muted mb-0">
                                Sudah punya akun? 
                                <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
                                    Masuk di sini
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Help Text -->
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Dengan mendaftar, Anda menyetujui syarat dan ketentuan yang berlaku
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (togglePassword && passwordInput) {
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

            // Register form submission with loading state
            const registerForm = document.getElementById('registerForm');
            const registerBtn = document.getElementById('registerBtn');
            
            if (registerForm && registerBtn) {
                const registerText = registerBtn.querySelector('.register-text');
                const loadingText = registerBtn.querySelector('.loading-text');

                registerForm.addEventListener('submit', function() {
                    registerBtn.disabled = true;
                    registerText.classList.add('d-none');
                    loadingText.classList.remove('d-none');
                });
            }

            // Auto focus on nama_lengkap input when page loads
            const namaLengkapInput = document.getElementById('nama_lengkap');
            if (namaLengkapInput) {
                namaLengkapInput.focus();
            }

            // Password strength indicator
            const password = document.getElementById('password');
            if (password) {
                password.addEventListener('input', function() {
                    const value = this.value;
                    let strength = 0;
                    
                    if (value.length >= 8) strength++;
                    if (value.match(/[a-z]/)) strength++;
                    if (value.match(/[A-Z]/)) strength++;
                    if (value.match(/[0-9]/)) strength++;
                    if (value.match(/[^A-Za-z0-9]/)) strength++;
                    
                    // You can add visual feedback here if needed
                });
            }
        });
    </script>
</body>
</html>