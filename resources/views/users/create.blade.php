@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Tambah User Baru</h1>
                    <p class="text-muted">Buat akun pengguna baru untuk sistem.</p>
                </div>
                <div>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Create Form -->
            <form method="POST" action="{{ route('users.store') }}" novalidate>
                @csrf
                
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Basic Information Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-person-fill me-2"></i>Informasi Dasar
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Nama Lengkap -->
                                        <div class="mb-3">
                                            <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('nama_lengkap') is-invalid @enderror" 
                                                   id="nama_lengkap" 
                                                   name="nama_lengkap" 
                                                   value="{{ old('nama_lengkap') }}" 
                                                   required>
                                            @error('nama_lengkap')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <!-- Email -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Password -->
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password"
                                                   required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <!-- Konfirmasi Password -->
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation"
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Role -->
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                            <select class="form-select @error('role') is-invalid @enderror" 
                                                    id="role" 
                                                    name="role" 
                                                    required>
                                                <option value="">Pilih Role</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role }}" 
                                                            {{ old('role') === $role ? 'selected' : '' }}>
                                                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <!-- Status -->
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" 
                                                    name="status" 
                                                    required>
                                                <option value="">Pilih Status</option>
                                                <option value="Aktif" {{ old('status') === 'Aktif' ? 'selected' : '' }}>
                                                    Aktif
                                                </option>
                                                <option value="Nonaktif" {{ old('status') === 'Nonaktif' ? 'selected' : '' }}>
                                                    Nonaktif
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-person-badge me-2"></i>Informasi Personal (Opsional)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Tanggal Lahir -->
                                        <div class="mb-3">
                                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                            <input type="text" 
                                                   class="form-control datepicker @error('tanggal_lahir') is-invalid @enderror" 
                                                   id="tanggal_lahir" 
                                                   name="tanggal_lahir" 
                                                   value="{{ old('tanggal_lahir') }}"
                                                   placeholder="DD-MM-YYYY">
                                            @error('tanggal_lahir')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <!-- Nomor Telepon -->
                                        <div class="mb-3">
                                            <label for="no_telephone" class="form-label">Nomor Telepon</label>
                                            <input type="text" 
                                                   class="form-control @error('no_telephone') is-invalid @enderror" 
                                                   id="no_telephone" 
                                                   name="no_telephone" 
                                                   value="{{ old('no_telephone') }}"
                                                   placeholder="08xxxxxxxxxx">
                                            @error('no_telephone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Agama -->
                                        <div class="mb-3">
                                            <label for="agama" class="form-label">Agama</label>
                                            <input type="text" 
                                                   class="form-control @error('agama') is-invalid @enderror" 
                                                   id="agama" 
                                                   name="agama" 
                                                   value="{{ old('agama') }}">
                                            @error('agama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <!-- Pendidikan Terakhir -->
                                        <div class="mb-3">
                                            <label for="pend_terakhir" class="form-label">Pendidikan Terakhir</label>
                                            <input type="text" 
                                                   class="form-control @error('pend_terakhir') is-invalid @enderror" 
                                                   id="pend_terakhir" 
                                                   name="pend_terakhir" 
                                                   value="{{ old('pend_terakhir') }}"
                                                   placeholder="S1, S2, dll">
                                            @error('pend_terakhir')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Kompetensi -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="kompetensi_1" class="form-label">Kompetensi 1</label>
                                            <input type="text" 
                                                   class="form-control @error('kompetensi_1') is-invalid @enderror" 
                                                   id="kompetensi_1" 
                                                   name="kompetensi_1" 
                                                   value="{{ old('kompetensi_1') }}">
                                            @error('kompetensi_1')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="kompetensi_2" class="form-label">Kompetensi 2</label>
                                            <input type="text" 
                                                   class="form-control @error('kompetensi_2') is-invalid @enderror" 
                                                   id="kompetensi_2" 
                                                   name="kompetensi_2" 
                                                   value="{{ old('kompetensi_2') }}">
                                            @error('kompetensi_2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Information Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-info-circle me-2"></i>Informasi
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-3">
                                    <small>
                                        <strong>Catatan Role:</strong><br>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Webmaster:</strong> Akses penuh sistem</li>
                                            <li><strong>Admin Erlass:</strong> Akses administratif terbatas</li>
                                            <li><strong>Instruktur:</strong> Akses untuk pengajaran</li>
                                        </ul>
                                    </small>
                                </div>
                                
                                <div class="alert alert-warning mb-0">
                                    <small>
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <strong>Instruktur baru:</strong><br>
                                        User dengan role instruktur akan memerlukan verifikasi sebelum dapat mengakses fitur lengkap.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <button type="submit" class="btn btn-success btn-lg w-100 mb-2">
                                    <i class="bi bi-person-plus-fill me-2"></i>Buat User
                                </button>
                                
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-x-lg me-2"></i>Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Password strength indicator (optional enhancement)
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        
        // Real-time password confirmation validation
        if (passwordInput && confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                if (passwordInput.value !== this.value) {
                    this.setCustomValidity('Password tidak cocok');
                } else {
                    this.setCustomValidity('');
                }
            });
            
            passwordInput.addEventListener('input', function() {
                if (confirmPasswordInput.value && this.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity('Password tidak cocok');
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
            });
        }
    });
</script>
@endpush
@endsection