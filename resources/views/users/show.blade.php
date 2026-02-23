@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Detail User</h1>
                    <p class="text-muted">Informasi lengkap pengguna sistem.</p>
                </div>
                <div>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    @can('update', $user)
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    @endcan
                </div>
            </div>

            <!-- User Details Card -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person-circle me-2"></i>{{ $user->nama_lengkap }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Informasi Dasar</h6>
                                    
                                    <div class="mb-3">
                                        <strong>ID User:</strong><br>
                                        <span class="text-muted">#{{ $user->id }}</span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Nama Lengkap:</strong><br>
                                        {{ $user->nama_lengkap }}
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Email:</strong><br>
                                        <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                            {{ $user->email }}
                                        </a>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Role:</strong><br>
                                        @php
                                            $roleClass = match($user->role) {
                                                'webmaster' => 'bg-danger',
                                                'admin_erlass' => 'bg-warning text-dark',
                                                'instruktur' => 'bg-info',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $roleClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Status:</strong><br>
                                        <span class="badge {{ $user->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $user->status }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Informasi Personal</h6>
                                    
                                    <div class="mb-3">
                                        <strong>Tanggal Lahir:</strong><br>
                                        {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d/m/Y') : '-' }}
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Nomor Telepon:</strong><br>
                                        {{ $user->no_telephone ?: '-' }}
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Agama:</strong><br>
                                        {{ $user->agama ?: '-' }}
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Pendidikan Terakhir:</strong><br>
                                        {{ $user->pend_terakhir ?: '-' }}
                                    </div>
                                </div>
                            </div>

                            @if($user->kompetensi_1 || $user->kompetensi_2)
                                <hr>
                                <h6 class="text-primary mb-3">Kompetensi</h6>
                                
                                @if($user->kompetensi_1)
                                    <div class="mb-3">
                                        <strong>Kompetensi 1:</strong><br>
                                        {{ $user->kompetensi_1 }}
                                    </div>
                                @endif
                                
                                @if($user->kompetensi_2)
                                    <div class="mb-3">
                                        <strong>Kompetensi 2:</strong><br>
                                        {{ $user->kompetensi_2 }}
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- System Information Card -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-gear me-2"></i>Informasi Sistem
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Terdaftar:</strong><br>
                                <small class="text-muted">
                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Terakhir Diperbarui:</strong><br>
                                <small class="text-muted">
                                    {{ $user->updated_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Verification Status Card (for instructors) -->
                    @if($user->role === 'instruktur')
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-shield-check me-2"></i>Status Verifikasi
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Status:</strong><br>
                                    @php
                                        $verificationClass = match($user->verification_status) {
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'pending' => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $verificationClass }}">
                                        {{ ucfirst($user->verification_status ?: 'Belum Diverifikasi') }}
                                    </span>
                                </div>
                                
                                @if($user->verified_at)
                                    <div class="mb-3">
                                        <strong>Diverifikasi:</strong><br>
                                        <small class="text-muted">
                                            {{ $user->verified_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                @endif
                                
                                @if($user->verifiedBy)
                                    <div class="mb-3">
                                        <strong>Diverifikasi Oleh:</strong><br>
                                        <small class="text-muted">
                                            {{ $user->verifiedBy->nama_lengkap }}
                                        </small>
                                    </div>
                                @endif
                                
                                @if($user->rejection_reason)
                                    <div class="mb-3">
                                        <strong>Alasan Penolakan:</strong><br>
                                        <small class="text-danger">
                                            {{ $user->rejection_reason }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Card (for instructors with teaching reports) -->
            @if($user->role === 'instruktur' && $user->laporanMengajar()->exists())
                <div class="card shadow-sm mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-bar-chart me-2"></i>Statistik Pengajaran
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1">{{ $user->laporanMengajar()->count() }}</h4>
                                    <small class="text-muted">Total Laporan</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h4 class="text-success mb-1">{{ $user->laporanMengajar()->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                                    <small class="text-muted">Bulan Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h4 class="text-info mb-1">{{ $user->laporanMengajar()->distinct('sekolah_kodlan')->count() }}</h4>
                                    <small class="text-muted">Total Sekolah</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-warning mb-1">{{ $user->laporanMengajar()->where('created_at', '>=', now()->startOfWeek())->count() }}</h4>
                                <small class="text-muted">Minggu Ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection