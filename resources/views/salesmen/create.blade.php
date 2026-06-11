@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('salesmen.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h5 class="card-title mb-0 fw-bold text-dark">Tambah Salesman Baru</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('salesmen.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="kode_salesman" class="form-label fw-semibold text-muted small">Kode Salesman</label>
                            <input 
                                type="text" 
                                class="form-control @error('kode_salesman') is-invalid @enderror" 
                                id="kode_salesman" 
                                name="kode_salesman" 
                                value="{{ old('kode_salesman') }}" 
                                placeholder="CONTOH: SLS-001"
                                required
                            >
                            @error('kode_salesman')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nama_salesman" class="form-label fw-semibold text-muted small">Nama Salesman</label>
                            <input 
                                type="text" 
                                class="form-control @error('nama_salesman') is-invalid @enderror" 
                                id="nama_salesman" 
                                name="nama_salesman" 
                                value="{{ old('nama_salesman') }}" 
                                placeholder="Nama Lengkap Salesman"
                                required
                            >
                            @error('nama_salesman')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="group_leader" class="form-label fw-semibold text-muted small">Group Leader (Opsional)</label>
                            <input 
                                type="text" 
                                class="form-control @error('group_leader') is-invalid @enderror" 
                                id="group_leader" 
                                name="group_leader" 
                                value="{{ old('group_leader') }}" 
                                placeholder="Nama Leader"
                            >
                            @error('group_leader')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="area" class="form-label fw-semibold text-muted small">Area / Wilayah Kerja</label>
                            <input 
                                type="text" 
                                class="form-control @error('area') is-invalid @enderror" 
                                id="area" 
                                name="area" 
                                value="{{ old('area') }}" 
                                placeholder="CONTOH: Jakarta Barat, Tangerang, dll"
                            >
                            @error('area')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-semibold text-muted small">Hubungkan ke Akun Pengguna (Opsional)</label>
                            <select 
                                class="form-select @error('user_id') is-invalid @enderror" 
                                id="user_id" 
                                name="user_id"
                            >
                                <option value="">-- Pilih Akun User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small mt-1">Hanya menampilkan pengguna yang memiliki role <code>sales</code>.</div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="bi bi-save me-1"></i> Simpan Salesman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
