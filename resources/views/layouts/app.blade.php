<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Erlass Ekskul')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Additional plugin styles for complex forms -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <!-- App styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="bi bi-mortarboard-fill me-2 text-white"></i>
                Erlass Ekskul
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="main-nav">
                <!-- Main Navigation Menu -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs(['sekolah.*', 'siswa.*']) ? 'active' : '' }}" 
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-building me-1"></i>
                            Data Master
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('sekolah.index') }}">
                                <i class="bi bi-building me-2"></i>Sekolah
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('siswa.index') }}">
                                <i class="bi bi-people me-2"></i>Siswa
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('sekolah.distribusi') }}">
                                <i class="bi bi-pie-chart me-2"></i>Distribusi Sekolah
                            </a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('laporan-mengajar.*') ? 'active' : '' }}" 
                           href="{{ route('laporan-mengajar.index') }}">
                            <i class="bi bi-journal-check me-1"></i>
                            Laporan Mengajar
                        </a>
                    </li>
                    
                    @can('viewAny', App\Models\Ekstrakurikuler::class)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ekstrakurikuler.*') ? 'active' : '' }}" 
                           href="{{ route('ekstrakurikuler.index') }}">
                            <i class="bi bi-trophy me-1"></i>
                            Ekstrakurikuler
                        </a>
                    </li>
                    @endcan
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs(['absensi.*', 'rekap-absensi']) ? 'active' : '' }}" 
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-calendar-check me-1"></i>
                            Absensi
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('absensi.index') }}">
                                <i class="bi bi-list-check me-2"></i>Kelola Absensi
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('absensi.rekap') }}">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i>Rekap Absensi
                            </a></li>
                        </ul>
                    </li>
                </ul>

                <!-- User Menu -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> 
                            <span class="d-none d-md-inline">{{ Str::limit(Auth::user()->nama_lengkap, 20) }}</span>
                            <span class="d-md-none">{{ Str::limit(Auth::user()->nama_lengkap, 10) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header">
                                <div class="text-center">
                                    <strong>{{ Auth::user()->nama_lengkap }}</strong>
                                    <br>
                                    <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a></li>
                            
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-gear me-2"></i>Profil Saya
                            </a></li>
                            
                            @if(Auth::user()->hasAdminAccess())
                                <li><hr class="dropdown-divider"></li>
                                @if(Auth::user()->canManageUsers())
                                    <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                        <i class="bi bi-people-fill me-2"></i>Manajemen Pengguna
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.verification.index') }}">
                                        <i class="bi bi-patch-check me-2"></i>Verifikasi Instruktur
                                    </a></li>
                                @else
                                    <li><span class="dropdown-item-text text-muted">
                                        <i class="bi bi-shield-check me-2"></i>Admin Erlass
                                    </span></li>
                                @endif
                            @endif

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        <!-- Session Status Messages -->
        <div class="container mt-3">
            <x-session-status />
        </div>
        
        @yield('content')
    </main>

    <footer class="footer mt-auto py-3 bg-dark text-white-50">
        <div class="container text-center">
            <small>Copyright &copy; {{ date('Y') }} Erlass Ekskul. All Rights Reserved.</small>
        </div>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Plugin scripts that need CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    @stack('scripts')

    <!-- Custom Styles for Navigation -->
    <style>
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #495057;
        }
        
        .dropdown-header {
            background-color: #f8f9fa;
            border-radius: 8px 8px 0 0;
            margin: -0.5rem -1rem 0.5rem;
            padding: 1rem;
        }
        
        @media (max-width: 991.98px) {
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .navbar-nav .nav-link:last-child {
                border-bottom: none;
            }
        }
    </style>
</body>
</html>