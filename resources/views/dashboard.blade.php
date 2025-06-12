@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Dashboard</h1>
        <div class="text-end">
            <p class="h5 mb-0">Selamat Datang, <strong>{{ Auth::user()->nama_lengkap }}</strong>!</p>
            <span class="badge bg-primary">{{ Str::ucfirst(Auth::user()->role) }}</span>
        </div>
    </div>

    {{-- Catatan: Angka di bawah ini hanya contoh. Anda perlu mengambil data ini dari database di controller Anda. --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted">Total Sekolah</h6>
                        <h2 class="fw-bold">12</h2>
                    </div>
                    <i class="bi bi-building fs-1 text-primary opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted">Total Siswa</h6>
                        <h2 class="fw-bold">450</h2>
                    </div>
                    <i class="bi bi-people-fill fs-1 text-success opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted">Laporan Hari Ini</h6>
                        <h2 class="fw-bold">5</h2>
                    </div>
                    <i class="bi bi-file-earmark-text fs-1 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted">Pengguna Aktif</h6>
                        <h2 class="fw-bold">{{ \App\Models\User::count() }}</h2>
                    </div>
                    <i class="bi bi-person-check-fill fs-1 text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mb-3">Menu Navigasi</h3>
    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('sekolah.index') }}" class="card text-decoration-none shadow-sm h-100 card-hover">
                <div class="card-body text-center">
                    <i class="bi bi-list-ul fs-1 mb-3 text-primary"></i>
                    <h5 class="card-title">Daftar Sekolah</h5>
                    <p class="card-text text-muted">Kelola data sekolah SD dan SMP.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('laporan-mengajar.index') }}" class="card text-decoration-none shadow-sm h-100 card-hover">
                <div class="card-body text-center">
                    <i class="bi bi-journal-check fs-1 mb-3 text-primary"></i>
                    <h5 class="card-title">Laporan Mengajar</h5>
                    <p class="card-text text-muted">Buat dan lihat laporan kegiatan mengajar.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('absensi.index') }}" class="card text-decoration-none shadow-sm h-100 card-hover">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check fs-1 mb-3 text-primary"></i>
                    <h5 class="card-title">Absensi</h5>
                    <p class="card-text text-muted">Catat dan monitor kehadiran siswa.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('siswa.index') }}" class="card text-decoration-none shadow-sm h-100 card-hover">
                <div class="card-body text-center">
                    <i class="bi bi-person-lines-fill fs-1 mb-3 text-primary"></i>
                    <h5 class="card-title">Daftar Siswa</h5>
                    <p class="card-text text-muted">Lihat dan kelola data siswa.</p>
                </div>
            </a>
        </div>

        @if(Auth::user()->role === 'admin')
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('users.index') }}" class="card text-decoration-none shadow-sm h-100 card-hover border-danger">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill fs-1 mb-3 text-danger"></i>
                        <h5 class="card-title">Manajemen Pengguna</h5>
                        <p class="card-text text-muted">Kelola akun dan role pengguna.</p>
                    </div>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
{{-- Menambahkan sedikit style untuk efek hover pada kartu navigasi --}}
<style>
    .card-hover {
        transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }
</style>
@endpush