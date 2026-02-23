<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erlass Ekskul - Platform Manajemen Ekstrakurikuler</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary: #3b82f6;
            --secondary: #06b6d4;
            --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa; /* Light background like dashboard */
            color: #334155;
            min-height: 100vh;
        }

        .homepage-wrapper {
            width: 100%;
            overflow-x: hidden;
        }

        /* Hero Styling */
        .hero-section {
            background: #ffffff;
            position: relative;
            overflow: hidden;
        }

        /* Subtle Abstract Shapes for Light Mode */
        .shape-blob {
            position: absolute;
            filter: blur(80px);
            opacity: 0.15; /* Reduced opacity for light mode */
            z-index: 1;
            animation: float 10s infinite ease-in-out;
        }
        .shape-1 {
            top: -10%;
            right: -5%;
            width: 600px;
            height: 600px;
            background: #3b82f6;
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        }
        .shape-2 {
            bottom: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: #06b6d4;
            border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            animation-delay: -5s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(30px, 20px) rotate(10deg); }
        }

        /* Glassmorphism for Light Mode */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .glass-badge {
            background: rgba(59, 130, 246, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #2563eb;
        }

        .text-gradient-primary {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .ls-1 { letter-spacing: 1px; }

        .btn-glass {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            backdrop-filter: blur(5px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .btn-glass:hover {
            background: rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
            color: #1d4ed8;
        }

        .hover-scale { transition: transform 0.3s ease; }
        .hover-scale:hover { transform: scale(1.05); }
        
        .hover-lift { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .hover-lift:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
        }

        .tilt-effect {
            transform: perspective(1000px) rotateY(-5deg) rotateX(2deg);
            transition: transform 0.5s ease;
        }
        .tilt-effect:hover {
            transform: perspective(1000px) rotateY(0) rotateX(0);
        }
        
        /* Navbar Text Dark */
        .navbar-brand { color: #1e293b !important; }
        .nav-link { color: #64748b !important; }
    </style>
</head>
<body class="antialiased">
    <div class="homepage-wrapper">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light absolute-top pt-4">
            <div class="container">
                <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="#">
                    <img src="{{ asset('images/logo-erlass.png') }}" alt="Erlass Logo" style="height: 40px; width: auto;">
                    <span>Erlass<span class="text-primary">Ekskul</span></span>
                </a>
                <div class="d-none d-md-block">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary rounded-pill px-4">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-link text-dark text-decoration-none me-2 fw-semibold">Masuk</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section position-relative d-flex align-items-center min-vh-100">
            <div class="container position-relative z-2">
                <div class="row align-items-center">
                    <div class="col-lg-7 text-dark">
                        <div class="badge glass-badge mb-4 px-3 py-2 rounded-pill">
                            <i class="bi bi-stars me-2"></i>
                            The #1 Extracurricular Management Platform
                        </div>
                        <h1 class="display-3 fw-bold mb-4 lh-tight text-dark">
                            Transformasi Digital <br>
                            <span class="text-gradient-primary">Ekstrakurikuler</span> Sekolah
                        </h1>
                        <p class="lead text-muted mb-5 w-75">
                            Solusi terintegrasi untuk manajemen jadwal, absensi real-time, dan pelaporan kegiatan ekstrakurikuler yang efisien dan transparan.
                        </p>

                        <div class="d-flex flex-column flex-sm-row gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg hover-scale border-0" style="background: var(--primary-gradient);">
                                    <i class="bi bi-speedometer2 me-2"></i> Akses Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg hover-scale fw-bold">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                                </a>
                                
                                <a href="{{ route('instructor.register') }}" class="btn btn-glass btn-lg px-4 py-3 rounded-pill hover-scale">
                                    <i class="bi bi-person-video3 me-2"></i> Daftar Instruktur
                                </a>
                            @endauth
                        </div>

                        <div class="mt-5 d-flex gap-4 text-muted small">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-primary"></i>
                                <span>Real-time Monitoring</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-primary"></i>
                                <span>Digital Reporting</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-primary"></i>
                                <span>Trusted by Schools</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-5 d-none d-lg-block">
                        <!-- Light Glassmorphism Mockup -->
                        <div class="hero-card glass-card p-4 rounded-4 tilt-effect bg-white border border-light shadow-lg">
                            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                                        <i class="bi bi-activity"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark fw-bold">Live Activity</h6>
                                        <small class="text-muted">Monitoring Kelas Berjalan</small>
                                    </div>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                    <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                                    Live
                                </span>
                            </div>

                            <!-- Mockup Items -->
                            <div class="vstack gap-3">
                                <!-- Item 1 -->
                                <div class="bg-light p-3 rounded-3 d-flex align-items-center gap-3 border">
                                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center border border-warning border-opacity-25" style="width: 40px; height: 40px;">
                                        <i class="bi bi-laptop text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 text-dark fs-6 fw-semibold">Coding Scratch</h6>
                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                            <small class="text-muted">Projek: Tikus Mencari Keju</small>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">LIVE</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Item 2 -->
                                <div class="bg-light p-3 rounded-3 d-flex align-items-center gap-3 border">
                                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center border border-info border-opacity-25" style="width: 40px; height: 40px;">
                                        <i class="bi bi-chat-quote-fill text-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 text-dark fs-6 fw-semibold">English Course</h6>
                                         <div class="d-flex align-items-center justify-content-between mt-1">
                                            <small class="text-muted">Speaking Practice</small>
                                            <small class="text-primary fw-bold">4 Siswa Hadir</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Abstract Shapes -->
            <div class="shape-blob shape-1"></div>
            <div class="shape-blob shape-2"></div>
        </section>

        <!-- Features Section -->
        <section class="py-5 position-relative">
            <!-- Unified Background Gradient (Subtle) -->
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%); z-index: -1;"></div>
            
            <div class="container py-5 position-relative z-2">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-6 text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill mb-3">
                            <i class="bi bi-stars me-1"></i> Kenapa Erlass?
                        </span>
                        <h2 class="fw-bold display-6 text-dark mb-3">
                            Platform Modern untuk <br>
                            <span class="text-gradient-primary">Ekosistem Pendidikan</span>
                        </h2>
                        <p class="text-muted w-75 mx-auto">
                            Tinggalkan cara lama. Beralihlah ke platform digital yang menyatukan sekolah, instruktur, dan manajemen dalam satu ekosistem terpadu.
                        </p>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Feature 1: Reporting (Primary) -->
                    <div class="col-md-4">
                        <div class="glass-card h-100 p-4 rounded-4 border-0 hover-lift position-relative overflow-hidden group text-center text-md-start">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle mb-4 d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="bi bi-bar-chart-fill fs-3"></i>
                            </div>
                            
                            <h4 class="fw-bold mb-3 text-dark">Laporan Terintegrasi</h4>
                            <p class="text-muted mb-0">
                                Hasilkan laporan kehadiran, nilai, dan perkembangan siswa secara otomatis dalam format digital yang rapi dan siap cetak.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 2: Instructor Mgmt (Warning/Gold) -->
                    <div class="col-md-4">
                         <div class="glass-card h-100 p-4 rounded-4 border-0 hover-lift position-relative overflow-hidden text-center text-md-start">
                            <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-circle mb-4 d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="bi bi-briefcase-fill fs-3"></i>
                            </div>
                            
                            <h4 class="fw-bold mb-3 text-dark">Manajemen Instruktur</h4>
                            <p class="text-muted mb-0">
                                Permudah koordinator dalam mengatur jadwal, penugasan, dan evaluasi kinerja instruktur di berbagai lokasi sekolah secara real-time.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 3: Validation (Success/Teal) -->
                    <div class="col-md-4">
                        <div class="glass-card h-100 p-4 rounded-4 border-0 hover-lift position-relative overflow-hidden text-center text-md-start">
                            <div class="icon-box bg-success bg-opacity-10 text-success rounded-circle mb-4 d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="bi bi-shield-lock-fill fs-3"></i>
                            </div>
                            
                            <h4 class="fw-bold mb-3 text-dark">Validasi & Keamanan</h4>
                            <p class="text-muted mb-0">
                                Setiap data tervalidasi dengan sistem approval berjenjang (Checker, Signer, Approver) untuk memastikan 100% keakuratan informasi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white py-5 border-top">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-4">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-mortarboard-fill text-primary"></i> ErlassEkskul
                        </h5>
                        <p class="text-muted small">
                            Memberdayakan sekolah dan instruktur dengan teknologi manajemen pendidikan terbaik.
                        </p>
                    </div>
                    <div class="col-lg-2 offset-lg-6">
                        <h6 class="fw-bold text-dark mb-3">Links</h6>
                        <ul class="list-unstyled text-muted small gap-2 d-flex flex-column">
                            <li><a href="#" class="text-muted text-decoration-none hover-primary">Tentang Kami</a></li>
                            <li><a href="#" class="text-muted text-decoration-none hover-primary">Bantuan</a></li>
                            <li><a href="{{ route('login') }}" class="text-muted text-decoration-none hover-primary">Login Staff</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-top mt-5 pt-4 text-center text-muted small">
                    &copy; {{ date('Y') }} Coding Erlass. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>