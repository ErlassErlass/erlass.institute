@extends('layouts.guest')

@section('title', 'Selamat Datang - Erlass Ekskul')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-primary py-5">
    <div class="container">
        <div class="row align-items-center py-5">
            <!-- Left Content -->
            <div class="col-lg-6 text-center text-lg-start">
                <div class="hero-content">
                    <!-- Brand Logo -->
                    <div class="mb-4">
                        <i class="bi bi-mortarboard-fill text-primary display-1"></i>
                    </div>
                    
                    <h1 class="display-4 fw-bold text-dark mb-4">
                        Selamat Datang di 
                        <span class="text-primary">Erlass Ekskul</span>
                    </h1>
                    
                    <p class="lead text-muted mb-4 fs-5">
                        Sistem manajemen kegiatan ekstrakurikuler by Coding Erlass. 
                        Kelola laporan mengajar, absensi siswa, dan data sekolah dengan mudah dan efisien.
                    </p>
                    
                    <div class="feature-highlight mb-5">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                    <span class="text-muted">Laporan Mengajar Digital</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                    <span class="text-muted">Absensi Siswa Real-time</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                    <span class="text-muted">Dashboard Analytics</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                    <span class="text-muted">Multi-role Management</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @guest
                        <div class="cta-buttons">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-3 px-5 py-3 shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-5 py-3 shadow-sm">
                                <i class="bi bi-person-plus me-2"></i>
                                Daftar
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Butuh bantuan registrasi? Hubungi administrator sistem
                            </small>
                        </div>
                    @else
                        <div class="cta-buttons">
                            <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg px-5 py-3 shadow-sm">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Buka Dashboard
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <p class="text-muted">
                                <i class="bi bi-person-circle me-1"></i>
                                Masuk sebagai: <strong>{{ Auth::user()->nama_lengkap }}</strong>
                            </p>
                        </div>
                    @endguest
                </div>
            </div>
            
            <!-- Right Content -->
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <div class="hero-image">
                    <!-- Educational Illustration -->
                    <div class="education-mockup p-4">
                        <div class="card shadow-lg border-0 mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                        <i class="bi bi-building text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">SDN Example School</h6>
                                        <small class="text-muted">Sekolah Dasar</small>
                                    </div>
                                </div>
                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 85%"></div>
                                </div>
                                <small class="text-muted">85% Kehadiran Minggu Ini</small>
                            </div>
                        </div>
                        
                        <div class="card shadow-lg border-0 mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Laporan Hari Ini</h6>
                                        <small class="text-success"><i class="bi bi-check-circle"></i> 12 Laporan Selesai</small>
                                    </div>
                                    <div class="bg-warning bg-opacity-10 p-2 rounded">
                                        <i class="bi bi-file-earmark-text text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <figure class="mb-0">
                    <blockquote class="blockquote fs-4 text-dark">
                        <p class="mb-4">"Inspire with knowledge, serve with excellence—transform lives together."</p>
                    </blockquote>
                    <figcaption class="blockquote-footer fs-6">
                        <cite title="Source Title">Misi Kami dalam Pendidikan</cite>
                    </figcaption>
                </figure>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-light py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0">&copy; {{ date('Y') }} Coding Erlass. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-muted">
                    Sistem Manajemen Ekstrakurikuler
                </small>
            </div>
        </div>
    </div>
</footer>
@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .hero-content {
        animation: fadeInUp 1s ease-out;
    }
    
    .hero-image {
        animation: fadeInRight 1s ease-out 0.2s both;
    }
    
    .education-mockup .card {
        transition: transform 0.3s ease;
    }
    
    .education-mockup .card:hover {
        transform: translateY(-5px);
    }
    
    .cta-buttons .btn {
        transition: all 0.3s ease;
        border-radius: 50px;
    }
    
    .cta-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .feature-highlight {
        animation: fadeIn 1s ease-out 0.4s both;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endpush