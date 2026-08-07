@extends('layouts.app')

@section('title', 'Tambah User Baru')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section with Gradient Avatar & Breadcrumb -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center text-white shadow-sm"
                         style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); width: 56px; height: 56px;">
                        <i class="bi bi-person-plus-fill fs-3"></i>
                    </div>
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1 small">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i class="bi bi-house me-1"></i>Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none text-muted">Manajemen User</a></li>
                                <li class="breadcrumb-item active fw-medium text-primary" aria-current="page">Tambah User</li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold mb-0 text-dark">Tambah User Baru</h3>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Create Form -->
    <form method="POST" action="{{ route('users.store') }}" novalidate id="createUserForm">
        @csrf
        
        <div class="row g-4">
            <!-- Left Column: Main Form Fields -->
            <div class="col-lg-8">
                <!-- Section 1: Akun & Otentikasi -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-shield-lock-fill fs-6"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Akun & Otentikasi</h5>
                                <small class="text-muted">Kredensial login dan akses hak peran user</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Nama Lengkap -->
                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label small fw-semibold text-secondary">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" 
                                           class="form-control bg-light border-start-0 @error('nama_lengkap') is-invalid @enderror" 
                                           id="nama_lengkap" 
                                           name="nama_lengkap" 
                                           value="{{ old('nama_lengkap') }}" 
                                           placeholder="Contoh: Budi Santoso"
                                           required>
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-semibold text-secondary">
                                    Email Sistem <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" 
                                           class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="budi@erlass.institute"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label small fw-semibold text-secondary">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                    <input type="password" 
                                           class="form-control bg-light border-start-0 border-end-0 @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password"
                                           placeholder="Minimal 8 karakter"
                                           required>
                                    <button class="btn btn-light border border-start-0 text-muted toggle-password" type="button" data-target="password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Konfirmasi Password -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label small fw-semibold text-secondary">
                                    Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-check2-circle"></i></span>
                                    <input type="password" 
                                           class="form-control bg-light border-start-0 border-end-0" 
                                           id="password_confirmation" 
                                           name="password_confirmation"
                                           placeholder="Ulangi password"
                                           required>
                                    <button class="btn btn-light border border-start-0 text-muted toggle-password" type="button" data-target="password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div id="pass-match-feedback" class="small mt-1 d-none"></div>
                            </div>

                            <!-- Role -->
                            <div class="col-md-6">
                                <label for="role" class="form-label small fw-semibold text-secondary">
                                    Role Pengguna <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person-badge"></i></span>
                                    <select class="form-select bg-light border-start-0 @error('role') is-invalid @enderror" 
                                            id="role" 
                                            name="role" 
                                            required>
                                        <option value="">Pilih Role Akses...</option>
                                        @foreach($roles as $r)
                                            <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $r)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="status" class="form-label small fw-semibold text-secondary">
                                    Status Akun <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-toggle-on"></i></span>
                                    <select class="form-select bg-light border-start-0 @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="Aktif" {{ old('status', 'Aktif') === 'Aktif' ? 'selected' : '' }}>🟢 Aktif</option>
                                        <option value="Nonaktif" {{ old('status') === 'Nonaktif' ? 'selected' : '' }}>🔴 Nonaktif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Masa Aktif Akun (Optional) -->
                            <div class="col-md-6">
                                <label for="tanggal_aktif" class="form-label small fw-semibold text-secondary">Tanggal Aktif Diberlakukan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" class="form-control bg-light border-start-0 @error('tanggal_aktif') is-invalid @enderror" 
                                           id="tanggal_aktif" name="tanggal_aktif" value="{{ old('tanggal_aktif') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_nonaktif" class="form-label small fw-semibold text-secondary">Tanggal Nonaktif (Opsional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-x"></i></span>
                                    <input type="date" class="form-control bg-light border-start-0 @error('tanggal_nonaktif') is-invalid @enderror" 
                                           id="tanggal_nonaktif" name="tanggal_nonaktif" value="{{ old('tanggal_nonaktif') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Domisili Instruktur (Conditional Card - Smooth Slide) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-info d-none" id="instructor-domicile-card">
                    <div class="card-header bg-info bg-opacity-10 border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-geo-alt-fill fs-6"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0 text-info-emphasis">Khusus Role Instruktur: Lokasi Domisili</h5>
                                <small class="text-muted">Diperlukan untuk pemetaan sekolah & jadwal penugasan mengajar</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="kota_domisili" class="form-label small fw-semibold text-secondary">Kota Domisili</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-building"></i></span>
                                    <select class="form-select bg-light border-start-0 @error('kota_domisili') is-invalid @enderror" id="kota_domisili" name="kota_domisili">
                                        <option value="">-- Pilih Kota Domisili --</option>
                                        @foreach(\App\Models\InstructorProfile::listKotaDomisili() as $city)
                                            <option value="{{ $city }}" {{ old('kota_domisili') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                        @endforeach
                                    </select>
                                    @error('kota_domisili')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="alamat_domisili" class="form-label small fw-semibold text-secondary">Alamat Lengkap Domisili</label>
                                <textarea class="form-control bg-light @error('alamat_domisili') is-invalid @enderror" 
                                          id="alamat_domisili" name="alamat_domisili" rows="1"
                                          placeholder="Jalan, No. Rumah, RT/RW, Kecamatan...">{{ old('alamat_domisili') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Informasi Profil Personal (Opsional) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary-subtle text-secondary rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-card-heading fs-6"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Informasi Personal (Opsional)</h5>
                                <small class="text-muted">Biodata pelengkap profil pengguna</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tanggal_lahir" class="form-label small fw-semibold text-secondary">Tanggal Lahir</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-cake2"></i></span>
                                    <input type="date" class="form-control bg-light border-start-0 @error('tanggal_lahir') is-invalid @enderror" 
                                           id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="no_telephone" class="form-label small fw-semibold text-secondary">Nomor Telepon / WA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 @error('no_telephone') is-invalid @enderror" 
                                           id="no_telephone" name="no_telephone" value="{{ old('no_telephone') }}" placeholder="0812xxxxxxxx">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="agama" class="form-label small fw-semibold text-secondary">Agama</label>
                                <select class="form-select bg-light @error('agama') is-invalid @enderror" id="agama" name="agama">
                                    <option value="">-- Pilih Agama --</option>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $rel)
                                        <option value="{{ $rel }}" {{ old('agama') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="pend_terakhir" class="form-label small fw-semibold text-secondary">Pendidikan Terakhir</label>
                                <select class="form-select bg-light @error('pend_terakhir') is-invalid @enderror" id="pend_terakhir" name="pend_terakhir">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach(['SMA/SMK Sederajat', 'D3', 'D4/S1', 'S2', 'S3'] as $edu)
                                        <option value="{{ $edu }}" {{ old('pend_terakhir') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="kompetensi_1" class="form-label small fw-semibold text-secondary">Kompetensi Utama</label>
                                <select class="form-select bg-light @error('kompetensi_1') is-invalid @enderror" id="kompetensi_1" name="kompetensi_1">
                                    <option value="">-- Pilih Kompetensi 1 --</option>
                                    @foreach(['Coding', 'Robotik', 'Desain', 'IoT', 'Data Science', 'Bahasa Inggris'] as $comp)
                                        <option value="{{ $comp }}" {{ old('kompetensi_1') == $comp ? 'selected' : '' }}>{{ $comp }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="kompetensi_2" class="form-label small fw-semibold text-secondary">Kompetensi Sekunder</label>
                                <select class="form-select bg-light @error('kompetensi_2') is-invalid @enderror" id="kompetensi_2" name="kompetensi_2">
                                    <option value="">-- Pilih Kompetensi 2 --</option>
                                    @foreach(['Coding', 'Robotik', 'Desain', 'IoT', 'Data Science', 'Bahasa Inggris'] as $comp)
                                        <option value="{{ $comp }}" {{ old('kompetensi_2') == $comp ? 'selected' : '' }}>{{ $comp }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar Action & Guidance -->
            <div class="col-lg-4">
                <!-- Action Panel Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 sticky-top" style="top: 80px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Tindakan & Simpan</h6>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 shadow-sm rounded-3 py-3 font-semibold" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                            <i class="bi bi-person-check-fill me-2"></i>Simpan User Baru
                        </button>
                        
                        <a href="{{ route('users.index') }}" class="btn btn-light w-100 rounded-3 py-2 text-secondary fw-medium">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </a>

                        <hr class="my-4">

                        <!-- Information Badge Card -->
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex gap-2">
                                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                                <div>
                                    <strong class="d-block small text-dark mb-1">Pedoman Role Pengguna</strong>
                                    <ul class="ps-3 mb-0 text-muted" style="font-size: 0.8rem;">
                                        <li><strong>Webmaster:</strong> Hak cipta & akses penuh sistem</li>
                                        <li><strong>Admin Sistem:</strong> Manajemen operasional & verifikasi</li>
                                        <li><strong>Admin:</strong> Manajemen akademis dasar</li>
                                        <li><strong>Instruktur:</strong> Portal pengajaran & laporan mengajar</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-3 border border-warning-subtle">
                            <div class="d-flex gap-2">
                                <i class="bi bi-shield-exclamation fs-5 text-warning"></i>
                                <div style="font-size: 0.8rem;">
                                    <strong>Catatan Instruktur:</strong><br>
                                    User dengan role instruktur memerlukan tahap verifikasi profil lokasi domisili agar dapat ditugaskan ke sekolah.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Password Visibility Toggle
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });

        // 2. Real-time Password Confirmation Feedback
        const pass = document.getElementById('password');
        const passConf = document.getElementById('password_confirmation');
        const matchFeedback = document.getElementById('pass-match-feedback');

        function validatePasswordMatch() {
            if (!passConf.value) {
                matchFeedback.classList.add('d-none');
                return;
            }

            matchFeedback.classList.remove('d-none');
            if (pass.value === passConf.value) {
                matchFeedback.className = 'small mt-1 text-success';
                matchFeedback.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Password cocok';
            } else {
                matchFeedback.className = 'small mt-1 text-danger';
                matchFeedback.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i>Password belum cocok';
            }
        }

        if (pass && passConf) {
            pass.addEventListener('input', validatePasswordMatch);
            passConf.addEventListener('input', validatePasswordMatch);
        }

        // 3. Smooth Toggle Instruktur Domicile Card
        const roleSelect = document.getElementById('role');
        const domicileCard = document.getElementById('instructor-domicile-card');
        
        function toggleDomicileCard() {
            if (roleSelect && domicileCard) {
                if (roleSelect.value === 'instruktur') {
                    domicileCard.classList.remove('d-none');
                    domicileCard.classList.add('animate__animated', 'animate__fadeIn');
                } else {
                    domicileCard.classList.add('d-none');
                }
            }
        }
        
        if (roleSelect) {
            roleSelect.addEventListener('change', toggleDomicileCard);
            toggleDomicileCard();
        }
    });
</script>
@endpush
@endsection