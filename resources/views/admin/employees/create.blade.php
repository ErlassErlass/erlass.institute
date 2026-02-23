@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Tambah Karyawan Baru</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.employees.store') }}" method="POST">
                @csrf
                
                <h5 class="mb-4 text-primary">Informasi Dasar</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                     <div class="col-md-6">
                        <label for="no_telephone" class="form-label">No. Telepon</label>
                        <input type="text" class="form-control @error('no_telephone') is-invalid @enderror" id="no_telephone" name="no_telephone" value="{{ old('no_telephone') }}">
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
                            <option value="sales" {{ old('role') == 'sales' ? 'selected' : '' }}>Sales / Marketing</option>
                            <option value="admin_sistem" {{ old('role') == 'admin_sistem' ? 'selected' : '' }}>Admin Sistem (IT)</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin Operasional</option>
                            <option value="instruktur" {{ old('role') == 'instruktur' ? 'selected' : '' }}>Instruktur</option>
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
                                <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Pilih divisi tempat karyawan ini bertugas.</div>
                    </div>
                </div>

                <h5 class="mb-4 mt-5 text-primary">Keamanan</h5>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-light border">Reset</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Karyawan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
