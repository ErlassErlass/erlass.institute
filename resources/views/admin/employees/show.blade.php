@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Detail Karyawan</h1>
            <p class="text-muted">Informasi lengkap tentang karyawan.</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-primary ms-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Profile Info -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="avatar-initial rounded-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ substr($employee->nama_lengkap, 0, 1) }}
                    </div>
                    <h4 class="fw-bold">{{ $employee->nama_lengkap }}</h4>
                    <p class="text-muted mb-1">{{ $employee->email }}</p>
                    <span class="badge bg-info text-dark mb-3">{{ $employee->division->name ?? 'Belum ada divisi' }}</span>
                    
                    <hr>
                    
                    <div class="row text-start mt-4">
                        <div class="col-sm-4 fw-bold text-muted">Role</div>
                        <div class="col-sm-8"><span class="badge bg-secondary">{{ $employee->role }}</span></div>
                    </div>
                    <div class="row text-start mt-2">
                        <div class="col-sm-4 fw-bold text-muted">Status</div>
                        <div class="col-sm-8">
                            <span class="badge {{ $employee->is_verified ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $employee->is_verified ? 'Verified' : 'Unverified' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Info -->
        <div class="col-xl-8 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Informasi Pribadi & Kontak</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">No. Telepon</div>
                        <div class="col-sm-9">{{ $employee->no_telephone ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Tanggal Lahir</div>
                        <div class="col-sm-9">{{ $employee->tanggal_lahir ? $employee->tanggal_lahir->format('d/m/Y') : '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Agama</div>
                        <div class="col-sm-9">{{ $employee->agama ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Pendidikan Terakhir</div>
                        <div class="col-sm-9">{{ $employee->pend_terakhir ?? '-' }}</div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="fw-bold text-primary mb-3">Kompetensi</h6>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Kompetensi 1</div>
                        <div class="col-sm-9">{{ $employee->kompetensi_1 ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Kompetensi 2</div>
                        <div class="col-sm-9">{{ $employee->kompetensi_2 ?? '-' }}</div>
                    </div>

                    <hr class="my-4">
                    
                    <h6 class="fw-bold text-primary mb-3">Aktifitas Akun</h6>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Bergabung Sejak</div>
                        <div class="col-sm-9">{{ $employee->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Terakhir Diupdate</div>
                        <div class="col-sm-9">{{ $employee->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
