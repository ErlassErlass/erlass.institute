<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    <style>
        #nprogress .bar { background: #3b82f6 !important; height: 3px !important; }
        @keyframes pageFadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        main, .main-content { animation: pageFadeIn 0.4s ease-out forwards; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- PWA Meta Tags & Manifest -->
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-erlass.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Erlass Ekskul">

    <title>@yield('title', 'Erlass Ekskul')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Additional plugin styles for complex forms -->
    {{-- Plugins (Select2, Flatpickr) now bundled in app.css --}}
    
    <!-- Plugin styles bundled in app.css -->
    
    <!-- App styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Plugin scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            /* Palette: Modern Elegant */
            --font-primary: 'Outfit', sans-serif;
            --primary-color: #3b82f6; /* Royal Blue */
            --primary-dark: #2563eb;
            --secondary-color: #06b6d4; /* Cyan */
            --success-color: #10b981; /* Emerald */
            --danger-color: #f43f5e; /* Rose */
            --warning-color: #f59e0b; /* Amber */
            
            --bg-body: #f1f5f9;
            --bg-card: rgba(255, 255, 255, 0.95);
            --bg-glass: rgba(255, 255, 255, 0.7);
            
            --border-color: #e2e8f0;
            --card-radius: 16px;
            --btn-radius: 12px;
            
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-soft: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.01);
        }

        /* Custom Placeholder */
        ::placeholder {
            font-weight: 300 !important;
            font-size: 0.95em;
            opacity: 0.55 !important;
            color: #94a3b8 !important;
        }
        ::-moz-placeholder {
            font-weight: 300 !important;
            font-size: 0.95em;
            opacity: 0.55 !important;
            color: #94a3b8 !important;
        }

        body {
            font-family: var(--font-primary);
            background-color: var(--bg-body);
            color: #334155;
            -webkit-font-smoothing: antialiased;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(6, 182, 212, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
        }

        /* Navbar Styling */
        .navbar {
            background-color: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: var(--shadow-sm);
            padding-top: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.3);
        }

        .navbar-brand {
            font-weight: 700;
            color: #0f172a !important;
            letter-spacing: -0.03em;
            font-size: 1.35rem;
        }

        .nav-link {
            font-weight: 500;
            color: #64748b !important;
            padding: 0.5rem 1rem !important;
            border-radius: var(--btn-radius);
            transition: all 0.2s ease;
            font-size: 0.95rem;
            white-space: nowrap;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            background-color: rgba(59, 130, 246, 0.05);
        }

        .nav-link.active {
            color: var(--primary-color) !important;
            background-color: rgba(59, 130, 246, 0.1);
            font-weight: 600;
        }

        /* Asymmetric Card Styling */
        .card {
            border: 1px solid var(--border-color);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-sm);
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s;
            overflow: hidden;
            position: relative;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Asymmetric Accent on Cards */
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .card:hover::before {
            opacity: 1;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 1.75rem;
        }
        
        .card-body {
            padding: 1.75rem;
        }

        /* Custom Action Button Group (Requested Style) */
        .btn-group-custom {
            display: inline-flex;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-radius: 12px;
            /* overflow: hidden; Removed to allow dropdowns to show */
        }

        .btn-action {
            width: 40px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-color);
            background: #fff;
            transition: all 0.2s;
            position: relative;
            margin-right: -1px; /* Overlap borders */
            font-size: 1rem;
        }

        .btn-action:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .btn-action:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            margin-right: 0;
        }
        
        /* View Button (Cyan Hover) */
        .btn-action.view:hover { 
            background: rgba(6, 182, 212, 0.1); 
            color: var(--secondary-color);
            border-color: var(--secondary-color);
            z-index: 1;
        }
        .btn-action.view { color: var(--secondary-color); }

        /* Edit Button (Blue Hover) */
        .btn-action.edit:hover { 
            background: rgba(59, 130, 246, 0.1); 
            color: var(--primary-color);
            border-color: var(--primary-color);
            z-index: 1;
        }
        .btn-action.edit { color: var(--primary-color); }

        /* Delete Button (Red Hover) */
        .btn-action.delete:hover { 
            background: rgba(244, 63, 94, 0.1); 
            color: var(--danger-color);
            border-color: var(--danger-color);
            z-index: 1;
        }
        .btn-action.delete { color: var(--danger-color); }

        /* General Button Styling */
        .btn {
            border-radius: var(--btn-radius);
            padding: 0.6rem 1.4rem;
            font-weight: 500;
            letter-spacing: 0.01em;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
        }

        /* Form Inputs */
        .form-control, .form-select {
            border-radius: var(--btn-radius);
            padding: 0.8rem 1rem;
            border-color: var(--border-color);
            background-color: #f8fafc;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            background-color: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* Table Design */
        .table {
            --bs-table-bg: transparent;
        }
        
        .table thead th {
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1rem;
        }
        
        .table tbody td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            color: #334155;
            border-bottom: 1px solid var(--border-color);
        }
        
        /* Dropdowns */
        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-soft);
            border-radius: 16px;
            padding: 0.75rem;
            animation-duration: 0.2s;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-weight: 500;
            color: #475569;
        }
        
        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 { 
            font-weight: 700; 
            letter-spacing: -0.025em; 
            color: #0f172a;
        }
        
        .text-muted { color: #94a3b8 !important; }

        /* DataTables Adjustments */
        .dataTables_wrapper .dataTables_length select { padding-right: 2rem; }
        .dataTables_wrapper .dataTables_filter input { margin-left: 0.5rem; }
        div.dataTables_wrapper div.dataTables_info { color: #94a3b8; padding-top: 1rem; }
        .page-item .page-link { border-radius: 8px; margin: 0 2px; border: none; color: #64748b; }
        .page-item.active .page-link { background-color: var(--primary-color); color: white;box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3); }

        /* Sidebar layout styling */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #ffffff;
            color: #334155;
            border-right: 1px solid var(--border-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
        }

        #sidebar.active {
            margin-left: -260px;
        }

        #content {
            width: calc(100% - 260px);
            margin-left: 260px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        #content.active {
            width: 100%;
            margin-left: 0;
        }

        /* Top Header Bar */
        .header-bar {
            background-color: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            height: 70px;
        }

        /* Sidebar Nav Styling */
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            height: 70px;
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            list-style: none;
            margin: 0;
        }

        .sidebar-section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            font-weight: 600;
            margin: 1.25rem 0.75rem 0.5rem;
        }

        .sidebar-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            border-radius: var(--btn-radius);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            color: var(--primary-color);
            background-color: rgba(59, 130, 246, 0.05);
        }

        .sidebar-link.active {
            color: var(--primary-color);
            background-color: rgba(59, 130, 246, 0.1);
            font-weight: 600;
        }

        .sidebar-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: -260px;
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                width: 100%;
                margin-left: 0;
            }
            #content.active {
                width: calc(100% - 260px);
                margin-left: 260px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    @auth
        <div class="wrapper">
            <!-- Sidebar -->
            <nav id="sidebar">
                <div class="sidebar-header">
                    <a class="d-flex align-items-center gap-2 text-decoration-none" href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logo-erlass.png') }}" alt="Erlass Logo" style="height: 32px; width: auto;">
                        <span class="fs-5 fw-bold text-dark">Erlass<span class="text-primary">Ekskul</span></span>
                    </a>
                </div>
                
                <ul class="sidebar-nav">
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    @if(Auth::user()?->hasAdminAccess() || in_array(Auth::user()?->role, ['sales']))
                        <li class="sidebar-section-title">Inisiasi & Kontrak</li>
                        @if(Auth::user()?->hasAdminAccess())
                            <li class="sidebar-item">
                                <a class="sidebar-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                    <i class="bi bi-box-seam"></i>
                                    <span>Produk</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link {{ request()->routeIs('salesmen.index') ? 'active' : '' }}" href="{{ route('salesmen.index') }}">
                                    <i class="bi bi-person-badge"></i>
                                    <span>Salesman</span>
                                </a>
                            </li>
                        @endif
                        @if(in_array(Auth::user()?->role, ['webmaster', 'admin_sistem', 'admin', 'sales']))
                            <li class="sidebar-item">
                                <a class="sidebar-link {{ request()->routeIs('orders-sp.index') ? 'active' : '' }}" href="{{ route('orders-sp.index') }}">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <span>Surat Pesanan (SP)</span>
                                </a>
                            </li>
                        @endif
                    @endif

                    @if(Auth::user()?->hasAdminAccess())
                        <li class="sidebar-section-title">Data Master</li>
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs('sekolah.index') ? 'active' : '' }}" href="{{ route('sekolah.index') }}">
                                <i class="bi bi-building"></i>
                                <span>Sekolah</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs('siswa.index') ? 'active' : '' }}" href="{{ route('siswa.index') }}">
                                <i class="bi bi-people"></i>
                                <span>Siswa</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs('sekolah.distribusi') ? 'active' : '' }}" href="{{ route('sekolah.distribusi') }}">
                                <i class="bi bi-pie-chart"></i>
                                <span>Distribusi Sekolah</span>
                            </a>
                        </li>
                    @endif

                    <li class="sidebar-section-title">Akademik & Penjadwalan</li>
                    @if(Auth::user()?->role !== 'instruktur')
                        @can('viewAny', App\Models\Ekstrakurikuler::class)
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs('ekstrakurikuler.index') ? 'active' : '' }}" href="{{ route('ekstrakurikuler.index') }}">
                                <i class="bi bi-journal-bookmark"></i>
                                <span>Program Ekskul</span>
                            </a>
                        </li>
                        @endcan
                    @endif
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs(['ekstrakurikuler.sessions.*', 'ekstrakurikuler.reports.*']) ? 'active' : '' }}" href="{{ route('ekstrakurikuler.sessions.index') }}">
                            <i class="bi bi-calendar-event"></i>
                            <span>Agenda Kegiatan</span>
                        </a>
                    </li>

                    <li class="sidebar-section-title">Aktivitas & Kehadiran</li>
                    @if(Auth::user()?->role === 'instruktur')
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs('laporan-mengajar.create') ? 'active' : '' }}" href="{{ route('laporan-mengajar.create') }}">
                                <i class="bi bi-pencil-square"></i>
                                <span>Buat Laporan</span>
                            </a>
                        </li>
                    @endif
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('laporan-mengajar.index') ? 'active' : '' }}" href="{{ route('laporan-mengajar.index') }}">
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat Laporan</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('absensi.index') ? 'active' : '' }}" href="{{ route('absensi.index') }}">
                            <i class="bi bi-qr-code-scan"></i>
                            <span>Kelola Absensi</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('rekap-absensi') ? 'active' : '' }}" href="{{ route('rekap-absensi') }}">
                            <i class="bi bi-table"></i>
                            <span>Rekap Kehadiran</span>
                        </a>
                    </li>

                    <li class="sidebar-section-title">Penilaian & Kelulusan</li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs(['student-scores.*']) ? 'active' : '' }}" href="{{ route('student-scores.rombel-list') }}">
                            <i class="bi bi-journal-check"></i>
                            <span>Penilaian Siswa</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs(['certificates.index']) ? 'active' : '' }}" href="{{ route('certificates.index') }}">
                            <i class="bi bi-patch-check"></i>
                            <span>Sertifikat & Rapor</span>
                        </a>
                    </li>

                    <li class="sidebar-section-title">Kompensasi & Payroll</li>
                    @if(Auth::user()?->hasAdminAccess())
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs(['admin.salary-rates.*']) ? 'active' : '' }}" href="{{ route('admin.salary-rates.index') }}">
                                <i class="bi bi-cash-coin"></i>
                                <span>Master Tarif</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs(['admin.payroll.batches.*']) ? 'active' : '' }}" href="{{ route('admin.payroll.batches.index') }}">
                                <i class="bi bi-wallet2"></i>
                                <span>Pencairan Payroll</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()?->role === 'instruktur' || Auth::user()?->hasAdminAccess())
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs(['payroll.my-salaries', 'payroll.slip.*']) ? 'active' : '' }}" href="{{ route('payroll.my-salaries') }}">
                                <i class="bi bi-file-earmark-text"></i>
                                <span>Slip Gaji Saya</span>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()?->hasAdminAccess())
                        <li class="sidebar-section-title">Sistem & Pengaturan</li>
                        @if(Auth::user()?->canManageUsers())
                            <li class="sidebar-item">
                                <a class="sidebar-link {{ request()->routeIs('admin.verification.index') ? 'active' : '' }}" href="{{ route('admin.verification.index') }}">
                                    <i class="bi bi-shield-check"></i>
                                    <span>Verifikasi Instruktur</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link {{ request()->routeIs('admin.late-reports.index') ? 'active' : '' }}" href="{{ route('admin.late-reports.index') }}">
                                    <i class="bi bi-hourglass-split"></i>
                                    <span>Request Laporan</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="bi bi-people-fill"></i>
                                    <span>Manajemen User</span>
                                </a>
                            </li>
                        @endif
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs('admin.analytics.index') ? 'active' : '' }}" href="{{ route('admin.analytics.index') }}">
                                <i class="bi bi-graph-up"></i>
                                <span>Dashboard Analitik</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs('admin.analytics.schedule-distribution') ? 'active' : '' }}" href="{{ route('admin.analytics.schedule-distribution') }}">
                                <i class="bi bi-calendar-week"></i>
                                <span>Distribusi Jadwal</span>
                            </a>
                        </li>
                        @if(Auth::user()?->canManageUsers())
                            <li class="sidebar-item">
                                <a class="sidebar-link {{ request()->routeIs('admin.activity-logs.index') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}">
                                    <i class="bi bi-activity"></i>
                                    <span>Log Aktivitas</span>
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </nav>

            <!-- Content Area -->
            <div id="content">
                <!-- Top Header Bar -->
                <header class="header-bar">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" id="sidebarCollapse" class="btn btn-light border shadow-none p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 10px;">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                        <h5 class="mb-0 fw-semibold text-dark d-none d-md-block">Erlass Portal</h5>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-1 pe-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center border" style="width: 38px; height: 38px;">
                                    <span class="fw-bold">{{ substr(Auth::user()->nama_lengkap, 0, 1) }}</span>
                                </div>
                                <div class="d-none d-md-block line-height-sm text-start">
                                    <span class="d-block fw-bold small text-dark">{{ Str::limit(Auth::user()->nama_lengkap, 15) }}</span>
                                    <span class="d-block x-small text-muted" style="font-size: 0.7rem;">{{ ucfirst(Auth::user()->role) }}</span>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end animate slideIn">
                                <li><div class="dropdown-header">Akun Saya</div></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Edit Profil
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Keluar Aplikasi
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>

                <main class="flex-grow-1 py-4 px-3 px-md-4">
                    @if (session('status') || session('success') || session('error'))
                    <div class="container-fluid p-0 mb-4 animate slideInDown">
                        <x-session-status />
                    </div>
                    @endif
                    
                    @yield('content')
                </main>

                <footer class="footer mt-auto py-4 bg-white border-top">
                    <div class="container-fluid text-center">
                        <p class="mb-0 text-muted small">
                            &copy; {{ date('Y') }} <strong>Erlass Ekskul</strong>. Crafted with <i class="bi bi-heart-fill text-danger"></i> for Education.
                        </p>
                    </div>
                </footer>
            </div>
        </div>
    @else
        <!-- Guest Top Navbar Layout -->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo-erlass.png') }}" alt="Erlass Logo" class="me-1" style="height: 32px; width: auto;">
                    <span>Erlass<span class="text-primary">Ekskul</span></span>
                </a>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Spacer for Fixed Navbar -->
        <div style="height: 100px;"></div>

        <main class="flex-grow-1 py-4">
            @if (session('status') || session('success') || session('error'))
            <div class="container mt-4 animate slideInDown">
                <x-session-status />
            </div>
            @endif
            
            @yield('content')
        </main>

        <footer class="footer mt-auto py-4 bg-white border-top">
            <div class="container text-center">
                <p class="mb-0 text-muted small">
                    &copy; {{ date('Y') }} <strong>Erlass Ekskul</strong>. Crafted with <i class="bi bi-heart-fill text-danger"></i> for Education.
                </p>
            </div>
        </footer>
    @endauth

    @stack('modals')
    @stack('scripts')

    <!-- Animation Keyframes -->
    <style>
        .animate { animation-duration: 0.3s; animation-fill-mode: both; }
        .slideIn { animation-name: slideIn; }
        .slideInDown { animation-name: slideInDown; }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            NProgress.configure({ showSpinner: false, trickleSpeed: 200 });
            NProgress.done();

            // Toggle Sidebar event handler
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            if (sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function () {
                    document.getElementById('sidebar').classList.toggle('active');
                    document.getElementById('content').classList.toggle('active');
                });
            }

            // PWA Service Worker Registration
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            }
        });
        window.addEventListener("beforeunload", function() {
            NProgress.start();
        });
    </script>
</body>
</html>