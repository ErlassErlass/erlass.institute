@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-dark">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                Edit Pengguna
            </h2>
            <p class="text-muted mt-1 mb-0">Perbarui informasi akun pengguna</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card glass-card border-0 shadow-sm rounded-4 position-relative overflow-hidden">
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Section Header -->
                            <div class="col-12 mb-2">
                                <h5 class="fw-bold text-primary border-bottom pb-2">Informasi Pribadi</h5>
                            </div>

                            <!-- Nama Lengkap -->
                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label fw-semibold text-secondary small">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                    <input type="text" 
                                           class="form-control bg-light border-start-0 ps-0 @error('nama_lengkap') is-invalid @enderror" 
                                           id="nama_lengkap" 
                                           name="nama_lengkap" 
                                           value="{{ old('nama_lengkap', $user->nama_lengkap) }}" 
                                           required>
                                </div>
                                @error('nama_lengkap')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tanggal Lahir -->
                            <div class="col-md-6">
                                <label for="tanggal_lahir" class="form-label fw-semibold text-secondary small">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control bg-light @error('tanggal_lahir') is-invalid @enderror" 
                                       id="tanggal_lahir" 
                                       name="tanggal_lahir" 
                                       value="{{ old('tanggal_lahir', $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : '') }}" 
                                       required>
                                @error('tanggal_lahir')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- No Telephone -->
                            <div class="col-md-6">
                                <label for="no_telephone" class="form-label fw-semibold text-secondary small">No. Telepon <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone"></i></span>
                                    <input type="text" 
                                           class="form-control bg-light border-start-0 ps-0 @error('no_telephone') is-invalid @enderror" 
                                           id="no_telephone" 
                                           name="no_telephone" 
                                           value="{{ old('no_telephone', $user->no_telephone) }}" 
                                           required>
                                </div>
                                @error('no_telephone')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                             <!-- Agama -->
                             <div class="col-md-6">
                                <label for="agama" class="form-label fw-semibold text-secondary small">Agama <span class="text-danger">*</span></label>
                                <select class="form-select bg-light @error('agama') is-invalid @enderror" 
                                        id="agama" 
                                        name="agama" 
                                        required>
                                    <option value="">Pilih Agama</option>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Lainnya'] as $agama)
                                        <option value="{{ $agama }}" {{ old('agama', $user->agama) == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                                @error('agama')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Pendidikan & Kompetensi Header -->
                            <div class="col-12 mt-4 mb-2">
                                <h5 class="fw-bold text-primary border-bottom pb-2">Kualifikasi</h5>
                            </div>

                            <!-- Pendidikan Terakhir -->
                            <div class="col-md-6">
                                <label for="pend_terakhir" class="form-label fw-semibold text-secondary small">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <select class="form-select bg-light @error('pend_terakhir') is-invalid @enderror" 
                                        id="pend_terakhir" 
                                        name="pend_terakhir" 
                                        required>
                                    <option value="">Pilih Pendidikan</option>
                                    @foreach(['SMA', 'D3', 'S1', 'S2', 'S3'] as $pend)
                                        <option value="{{ $pend }}" {{ old('pend_terakhir', $user->pend_terakhir) == $pend ? 'selected' : '' }}>{{ $pend }}</option>
                                    @endforeach
                                </select>
                                @error('pend_terakhir')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                             <!-- Kompetensi -->
                             <div class="col-md-6">
                                <label for="kompetensi_1" class="form-label fw-semibold text-secondary small">Kompetensi Utama <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control bg-light @error('kompetensi_1') is-invalid @enderror" 
                                       id="kompetensi_1" 
                                       name="kompetensi_1" 
                                       value="{{ old('kompetensi_1', $user->kompetensi_1) }}" 
                                       required>
                                @error('kompetensi_1')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 offset-md-6">
                                <label for="kompetensi_2" class="form-label fw-semibold text-secondary small">Kompetensi Tambahan (Opsional)</label>
                                <input type="text" 
                                       class="form-control bg-light @error('kompetensi_2') is-invalid @enderror" 
                                       id="kompetensi_2" 
                                       name="kompetensi_2" 
                                       value="{{ old('kompetensi_2', $user->kompetensi_2) }}">
                                @error('kompetensi_2')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Akun Login Header -->
                            <div class="col-12 mt-4 mb-2">
                                <h5 class="fw-bold text-primary border-bottom pb-2">Pengaturan Akun</h5>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold text-secondary small">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                    <input type="email" 
                                           class="form-control bg-light border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                </div>
                                @error('email')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div class="col-md-6">
                                <label for="role" class="form-label fw-semibold text-secondary small">Role <span class="text-danger">*</span></label>
                                <select class="form-select bg-light @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    @foreach($roles as $value => $label)
                                        <option value="{{ $value }}" {{ old('role', $user->role) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text small text-muted">Mengubah role ke 'Instruktur' akan mereset status verifikasi.</div>
                                @error('role')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password Fields -->
                            <div class="col-12 mt-2">
                                <div class="alert alert-soft-info border-info border-opacity-25 p-3 rounded-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-info-circle-fill text-info fs-5"></i>
                                    <small class="mb-0">Kosongkan password jika tidak ingin mengubahnya.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold text-secondary small">Password Baru</label>
                                <input type="password" 
                                       class="form-control bg-light @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password"
                                       autocomplete="new-password">
                                @error('password')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold text-secondary small">Konfirmasi Password Baru</label>
                                <input type="password" 
                                       class="form-control bg-light" 
                                       id="password_confirmation" 
                                       name="password_confirmation"
                                       autocomplete="new-password">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                             @if(auth()->id() !== $user->id)
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                    <i class="bi bi-trash me-1"></i> Hapus User
                                </button>
                            @else
                                <div></div>
                            @endif

                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-light border">Batal</a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">
                                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
@if(auth()->id() !== $user->id)
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="avatar avatar-xl bg-danger bg-opacity-10 text-danger rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2">Hapus Pengguna?</h5>
                <p class="text-muted mb-4">Apakah Anda yakin ingin menghapus user <strong>{{ $user->nama_lengkap }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
