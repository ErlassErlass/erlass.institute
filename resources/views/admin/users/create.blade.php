@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-person-plus me-2 text-primary"></i>
                Tambah Pengguna Baru
            </h2>
            <p class="text-muted mt-1 mb-0">Buat akun pengguna baru dalam sistem</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Nama Lengkap -->
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
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

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
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

                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>

                            <!-- Role -->
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">
                                    Role <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $value => $label)
                                        <option value="{{ $value }}" 
                                                {{ old('role') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small>
                                        <strong>Webmaster:</strong> Akses penuh semua fitur<br>
                                        <strong>Admin Erlass:</strong> Akses terbatas, tidak bisa mengelola user<br>
                                        <strong>Instruktur:</strong> Perlu verifikasi untuk akses sistem
                                    </small>
                                </div>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_lahir" class="form-label">
                                    Tanggal Lahir <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control datepicker @error('tanggal_lahir') is-invalid @enderror" 
                                       id="tanggal_lahir" 
                                       name="tanggal_lahir" 
                                       value="{{ old('tanggal_lahir') }}" 
                                       placeholder="DD-MM-YYYY"
                                       required>
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- No Telephone -->
                            <div class="col-md-6 mb-3">
                                <label for="no_telephone" class="form-label">
                                    No. Telepon <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('no_telephone') is-invalid @enderror" 
                                       id="no_telephone" 
                                       name="no_telephone" 
                                       value="{{ old('no_telephone') }}" 
                                       required>
                                @error('no_telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Agama -->
                            <div class="col-md-6 mb-3">
                                <label for="agama" class="form-label">
                                    Agama <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('agama') is-invalid @enderror" 
                                        id="agama" 
                                        name="agama" 
                                        required>
                                    <option value="">Pilih Agama</option>
                                    <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                    <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                    <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                    <option value="Lainnya" {{ old('agama') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('agama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Pendidikan Terakhir -->
                            <div class="col-md-6 mb-3">
                                <label for="pend_terakhir" class="form-label">
                                    Pendidikan Terakhir <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('pend_terakhir') is-invalid @enderror" 
                                        id="pend_terakhir" 
                                        name="pend_terakhir" 
                                        required>
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="SMA" {{ old('pend_terakhir') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                    <option value="D3" {{ old('pend_terakhir') == 'D3' ? 'selected' : '' }}>D3</option>
                                    <option value="S1" {{ old('pend_terakhir') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('pend_terakhir') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('pend_terakhir') == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                                @error('pend_terakhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kompetensi 1 -->
                            <div class="col-md-6 mb-3">
                                <label for="kompetensi_1" class="form-label">
                                    Kompetensi Utama <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('kompetensi_1') is-invalid @enderror" 
                                       id="kompetensi_1" 
                                       name="kompetensi_1" 
                                       value="{{ old('kompetensi_1') }}" 
                                       placeholder="Contoh: Coding, Robotik, Desain" 
                                       required>
                                @error('kompetensi_1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kompetensi 2 -->
                            <div class="col-md-6 mb-3">
                                <label for="kompetensi_2" class="form-label">
                                    Kompetensi Tambahan
                                </label>
                                <input type="text" 
                                       class="form-control @error('kompetensi_2') is-invalid @enderror" 
                                       id="kompetensi_2" 
                                       name="kompetensi_2" 
                                       value="{{ old('kompetensi_2') }}" 
                                       placeholder="Opsional">
                                @error('kompetensi_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Simpan Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection