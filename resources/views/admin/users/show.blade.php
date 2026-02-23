@extends('layouts.app')

@section('content')
<div class="container-fluid pt-5 mt-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Detail User</h1>
            <p class="text-muted">Informasi lengkap pengguna sistem.</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <!-- Hanya webmaster yang bisa edit dari sini, sesuai UserManagementController policies -->
            @can('update', $user)
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary ms-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <!-- Main Profile Info -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="avatar-initial rounded-circle bg-dark text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ substr($user->nama_lengkap, 0, 1) }}
                    </div>
                    <h4 class="fw-bold">{{ $user->nama_lengkap }}</h4>
                    <p class="text-muted mb-1">{{ $user->email }}</p>
                    
                    @if($user->instructor_id)
                        <h5 class="text-primary fw-bold mb-3">{{ $user->instructor_id }}</h5>
                    @endif

                    @if($user->division)
                        <span class="badge bg-info text-dark mb-3">{{ $user->division->name }}</span>
                    @endif
                    
                    <hr>
                    
                    <div class="row text-start mt-4">
                        <div class="col-sm-4 fw-bold text-muted">Role</div>
                        <div class="col-sm-8"><span class="badge bg-primary">{{ $user->role }}</span></div>
                    </div>
                    <div class="row text-start mt-2">
                        <div class="col-sm-4 fw-bold text-muted">Status</div>
                        <div class="col-sm-8">
                            <span class="badge {{ $user->is_verified ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $user->is_verified ? 'Verified' : 'Unverified' }}
                            </span>
                        </div>
                    </div>
                    @if($user->role === 'instruktur')
                    <div class="row text-start mt-2">
                        <div class="col-sm-4 fw-bold text-muted">Verifikasi</div>
                        <div class="col-sm-8">
                            <span class="badge {{ $user->verification_status === 'approved' ? 'bg-success' : ($user->verification_status === 'rejected' ? 'bg-danger' : 'bg-secondary') }}">
                                {{ ucfirst($user->verification_status ?? 'Pending') }}
                            </span>
                        </div>
                    </div>
                    @endif
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
                        <div class="col-sm-9">{{ $user->no_telephone ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Tanggal Lahir</div>
                        <div class="col-sm-9">{{ $user->tanggal_lahir ? (\Carbon\Carbon::parse($user->tanggal_lahir)->format('d/m/Y')) : '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Agama</div>
                        <div class="col-sm-9">{{ $user->agama ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Pendidikan Terakhir</div>
                        <div class="col-sm-9">{{ $user->pend_terakhir ?? '-' }}</div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="fw-bold text-primary mb-3">Kompetensi</h6>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Kompetensi 1</div>
                        <div class="col-sm-9">{{ $user->kompetensi_1 ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Kompetensi 2</div>
                        <div class="col-sm-9">{{ $user->kompetensi_2 ?? '-' }}</div>
                    </div>

                    @if($user->verification_documents)
                    <hr class="my-4">
                    <h6 class="fw-bold text-primary mb-3">Dokumen Verifikasi</h6>
                    <div class="alert alert-light border">
                        @if(is_array($user->verification_documents))
                            <h6 class="alert-heading h6 mb-2"><i class="fas fa-folder-open me-1"></i> Berkas Lampiran:</h6>
                            <ul class="list-unstyled mb-0 ps-3">
                                @foreach($user->verification_documents as $key => $path)
                                    <li class="mb-1">
                                        <a href="{{ Storage::url($path) }}" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-file-alt me-2"></i> {{ ucwords(str_replace(['_', '-'], ' ', $key)) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <i class="fas fa-file-alt me-2"></i> {{ $user->verification_documents }}
                        @endif
                    </div>
                    @endif

                    <hr class="my-4">
                    
                    <h6 class="fw-bold text-primary mb-3">Aktifitas Akun</h6>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Bergabung Sejak</div>
                        <div class="col-sm-9">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-bold text-muted">Terakhir Diupdate</div>
                        <div class="col-sm-9">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
