@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Edit User</h1>
                    <p class="text-muted">Perbarui informasi pengguna sistem.</p>
                </div>
                <div>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye"></i> Lihat Detail
                    </a>
                </div>
            </div>

            <!-- Edit Form -->
            <form method="POST" action="{{ route('users.update', $user) }}" novalidate>
                @csrf
                @method('PUT')
                
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
                                                   value="{{ old('nama_lengkap', $user->nama_lengkap) }}" 
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
                                                   value="{{ old('email', $user->email) }}" 
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
                                            <label for="password" class="form-label">Password Baru</label>
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password">
                                            <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <!-- Konfirmasi Password -->
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation">
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
                                                    required
                                                    {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role }}" 
                                                            {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($user->id === Auth::id())
                                                <div class="form-text text-warning">
                                                    <i class="bi bi-exclamation-triangle"></i> 
                                                    Anda tidak dapat mengubah role Anda sendiri
                                                </div>
                                                <input type="hidden" name="role" value="{{ $user->role }}">
                                            @endif
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
                                                <option value="Aktif" {{ old('status', $user->status) === 'Aktif' ? 'selected' : '' }}>
                                                    Aktif
                                                </option>
                                                <option value="Nonaktif" {{ old('status', $user->status) === 'Nonaktif' ? 'selected' : '' }}>
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
                                    <i class="bi bi-person-badge me-2"></i>Informasi Personal
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Tanggal Lahir -->
                                        <div class="mb-3">
                                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                            <input type="date" 
                                                   class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                                   id="tanggal_lahir" 
                                                   name="tanggal_lahir" 
                                                   value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}">
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
                                                   value="{{ old('no_telephone', $user->no_telephone) }}"
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
                                                   value="{{ old('agama', $user->agama) }}">
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
                                                   value="{{ old('pend_terakhir', $user->pend_terakhir) }}"
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
                                                   value="{{ old('kompetensi_1', $user->kompetensi_1) }}">
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
                                                   value="{{ old('kompetensi_2', $user->kompetensi_2) }}">
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
                        <!-- Verification Settings Card (for instructors) -->
                        @if($user->role === 'instruktur' && auth()->user()->isWebmaster())
                            <div class="card shadow-sm mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="bi bi-shield-check me-2"></i>Pengaturan Verifikasi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Status Verifikasi -->
                                    <div class="mb-3">
                                        <label for="verification_status" class="form-label">Status Verifikasi</label>
                                        <select class="form-select @error('verification_status') is-invalid @enderror" 
                                                id="verification_status" 
                                                name="verification_status">
                                            <option value="">Belum Diverifikasi</option>
                                            <option value="pending" {{ old('verification_status', $user->verification_status) === 'pending' ? 'selected' : '' }}>
                                                Pending
                                            </option>
                                            <option value="approved" {{ old('verification_status', $user->verification_status) === 'approved' ? 'selected' : '' }}>
                                                Approved
                                            </option>
                                            <option value="rejected" {{ old('verification_status', $user->verification_status) === 'rejected' ? 'selected' : '' }}>
                                                Rejected
                                            </option>
                                        </select>
                                        @error('verification_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Verified Checkbox -->
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input @error('is_verified') is-invalid @enderror" 
                                                   id="is_verified" 
                                                   name="is_verified" 
                                                   value="1"
                                                   {{ old('is_verified', $user->is_verified) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_verified">
                                                User Terverifikasi
                                            </label>
                                            @error('is_verified')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Alasan Penolakan -->
                                    <div class="mb-3" id="rejection_reason_group" style="display: none;">
                                        <label for="rejection_reason" class="form-label">Alasan Penolakan</label>
                                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                                  id="rejection_reason" 
                                                  name="rejection_reason" 
                                                  rows="3" 
                                                  placeholder="Masukkan alasan penolakan verifikasi">{{ old('rejection_reason', $user->rejection_reason) }}</textarea>
                                        @error('rejection_reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <button type="submit" class="btn btn-success btn-lg w-100 mb-2">
                                    <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                                </button>
                                
                                <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-x-lg me-2"></i>Batal
                                </a>
                                
                                @if($errors->has('delete'))
                                    <div class="alert alert-danger mt-3 mb-0">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        {{ $errors->first('delete') }}
                                    </div>
                                @endif

                                @if($errors->has('role'))
                                    <div class="alert alert-warning mt-3 mb-0">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        {{ $errors->first('role') }}
                                    </div>
                                @endif
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
    // Show/hide rejection reason based on verification status
    document.addEventListener('DOMContentLoaded', function() {
        const verificationStatus = document.getElementById('verification_status');
        const rejectionReasonGroup = document.getElementById('rejection_reason_group');
        
        if (verificationStatus && rejectionReasonGroup) {
            function toggleRejectionReason() {
                if (verificationStatus.value === 'rejected') {
                    rejectionReasonGroup.style.display = 'block';
                } else {
                    rejectionReasonGroup.style.display = 'none';
                }
            }
            
            // Check on page load
            toggleRejectionReason();
            
            // Check when status changes
            verificationStatus.addEventListener('change', toggleRejectionReason);
        }
        
        // Auto-check is_verified when status is approved
        const isVerifiedCheckbox = document.getElementById('is_verified');
        if (verificationStatus && isVerifiedCheckbox) {
            verificationStatus.addEventListener('change', function() {
                if (this.value === 'approved') {
                    isVerifiedCheckbox.checked = true;
                } else if (this.value === 'rejected' || this.value === '') {
                    isVerifiedCheckbox.checked = false;
                }
            });
        }
    });
</script>
@endpush
@endsection