@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Edit Data Karyawan</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h5 class="mb-4 text-primary">Informasi Dasar</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $employee->nama_lengkap) }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                     <div class="col-md-6">
                        <label for="no_telephone" class="form-label">No. Telepon</label>
                        <input type="text" class="form-control @error('no_telephone') is-invalid @enderror" id="no_telephone" name="no_telephone" value="{{ old('no_telephone', $employee->no_telephone) }}">
                        @error('no_telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h5 class="mb-4 mt-5 text-primary">Posisi & Divisi</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="sales" {{ old('role', $employee->role) == 'sales' ? 'selected' : '' }}>Sales / Marketing</option>
                            <option value="admin_sistem" {{ old('role', $employee->role) == 'admin_sistem' ? 'selected' : '' }}>Admin Sistem (IT)</option>
                            <option value="admin" {{ old('role', $employee->role) == 'admin' ? 'selected' : '' }}>Admin Operasional</option>
                            <option value="instruktur" {{ old('role', $employee->role) == 'instruktur' ? 'selected' : '' }}>Instruktur</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="division_id" class="form-label">Divisi</label>
                        <select class="form-select @error('division_id') is-invalid @enderror" id="division_id" name="division_id">
                            <option value="">- Tidak ada / General -</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id', $employee->division_id) == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h5 class="mb-4 mt-5 text-primary">Ubah Password (Opsional)</h5>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Kosongkan jika tidak ingin mengubah password.
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
