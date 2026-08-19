<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" media="print" onload="this.media='all'" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js" defer></script>
    <style>
        #nprogress .bar { background: #3b82f6 !important; height: 3px !important; }
        @keyframes pageFadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        main, .main-content { animation: pageFadeIn 0.4s ease-out forwards; }
        
        /* Disable Double-Tap Zoom & Tap Highlight */
        html, body, button, select, input, textarea, a, .btn, .card, .modal-content {
            touch-action: manipulation;
        }
        button, .btn, a, [role="button"] {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon-192.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon-32.png') }}">

    <!-- PWA Meta Tags & Manifest -->
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/favicon-192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Erlass Ekskul">

    <title>@yield('title', 'Dashboard') — Erlass Ekskul</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" media="print" onload="this.media='all'">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    
    <!-- Additional plugin styles for complex forms -->
    {{-- Plugins (Select2, Flatpickr) now bundled in app.css --}}
    
    <!-- Plugin styles bundled in app.css -->
    
    <!-- App & Dashboard styles & scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dashboard.js'])
    
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
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
            --shadow-soft: 0 20px 25px -5px rgba(0, 0, 0, 0.06), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        /* Modern Glassmorphism Backdrop & Soft Elevation Modal System */
        .modal-backdrop {
            background-color: rgba(15, 23, 42, 0.35) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            will-change: opacity;
        }

        .modal-content {
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            border-radius: 20px !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
            background: #ffffff !important;
            overflow: hidden;
            will-change: transform, opacity;
            transform: translateZ(0);
        }

        .modal-header {
            background-color: #f8fafc !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 1.25rem 1.5rem !important;
        }

        .modal-header .btn-close {
            opacity: 0.6;
            transition: all 0.2s ease;
            border-radius: 50%;
            padding: 0.5rem;
        }

        .modal-header .btn-close:hover {
            opacity: 1;
            background-color: rgba(148, 163, 184, 0.15);
        }

        .modal-footer {
            background-color: #f8fafc !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 1rem 1.5rem !important;
        }

        .select2-container--bootstrap-5 {
            z-index: 1060 !important;
        }

        .flatpickr-calendar {
            z-index: 1065 !important;
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
            padding-bottom: env(safe-area-inset-bottom);
            overscroll-behavior-y: contain; /* Prevents Chrome Android pull-to-refresh reload in PWA mode */
        }

        /* iOS & Android PWA Touch & tap optimizations */
        button, a, .btn, .sidebar-link, .btn-action, .list-group-item, #sidebar-backdrop {
            touch-action: manipulation;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }

        /* Tactile active feedback for mobile PWA buttons */
        .btn:active, .sidebar-link:active, .btn-action:active, .list-group-item-action:active {
            transform: scale(0.97) !important;
            transition: transform 0.05s ease-out !important;
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
            padding: 0.75rem 0.85rem;
        }
        
        .table tbody td {
            padding: 0.65rem 0.85rem;
            vertical-align: middle;
            color: #334155;
            border-bottom: 1px solid var(--border-color);
        }

        .table-compact thead th {
            padding: 0.65rem 0.75rem !important;
        }

        .table-compact tbody td {
            padding: 0.55rem 0.75rem !important;
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
            background-color: #FFFFFF !important;
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 100;
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
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 1050;
                margin-left: 0 !important;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: none;
            }
            #sidebar.active {
                transform: translateX(0);
                box-shadow: var(--shadow-lg);
            }
            #content {
                width: 100% !important;
                margin-left: 0 !important;
            }
            #content.active {
                width: 100% !important;
                margin-left: 0 !important;
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

                    @if(Auth::user()?->hasAdminAccess())
                        <li class="sidebar-section-title">Inisiasi & Kontrak</li>
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
                            <i class="bi bi-calendar2-check"></i>
                            <span>Jadwal Sesi &amp; Laporan</span>
                        </a>
                    </li>

                    <li class="sidebar-section-title">Aktivitas & Kehadiran</li>
                    @if(Auth::user()?->role === 'instruktur' || Auth::user()?->hasAdminAccess())
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs('laporan-mengajar.create') ? 'active' : '' }}" href="{{ route('laporan-mengajar.create') }}">
                                <i class="bi bi-lightning-charge"></i>
                                <span>Laporan Ad-Hoc / Pengganti</span>
                            </a>
                        </li>
                    @endif
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('laporan-mengajar.index') ? 'active' : '' }}" href="{{ route('laporan-mengajar.index') }}">
                            <i class="bi bi-clock-history"></i>
                            <span>Semua Riwayat Laporan</span>
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
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ request()->routeIs('admin.late-reports.index') ? 'active' : '' }}" href="{{ route('admin.late-reports.index') }}">
                                <i class="bi bi-hourglass-split"></i>
                                <span>Request Laporan</span>
                                @php
                                    $pendingCount = \App\Models\LateReportRequest::where('status', 'pending')->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="badge bg-warning text-dark rounded-pill ms-auto fw-bold" style="font-size: 0.7rem;">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                        @if(Auth::user()?->canManageUsers())
                            <li class="sidebar-item">
                                <a class="sidebar-link {{ request()->routeIs('admin.verification.index') ? 'active' : '' }}" href="{{ route('admin.verification.index') }}">
                                    <i class="bi bi-shield-check"></i>
                                    <span>Verifikasi Instruktur</span>
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
                        @if(in_array(Auth::user()?->role, ['webmaster', 'admin_sistem']))
                            <li class="sidebar-item">
                                <a class="sidebar-link {{ request()->routeIs('admin.activity-logs.index') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}">
                                    <i class="bi bi-shield-lock text-danger"></i>
                                    <span>Log Pergerakan Admin</span>
                                </a>
                            </li>
                        @endif
                    @endif

                    <li class="sidebar-section-title">Bantuan & Support</li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('help.index') ? 'active' : '' }}" href="{{ route('help.index') }}">
                            <i class="bi bi-question-circle-fill text-primary"></i>
                            <span>Panduan & FAQ 101</span>
                        </a>
                    </li>

                    <!-- PWA Install Button inside Sidebar -->
                    <li class="sidebar-item d-none" id="pwa-install-item">
                        <a href="javascript:void(0)" class="sidebar-link text-primary fw-bold" id="btn-pwa-install">
                            <i class="bi bi-download"></i>
                            <span>Install Aplikasi</span>
                        </a>
                    </li>
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
                        @if(Auth::check() && in_array(Auth::user()->role, ['webmaster', 'admin_sistem', 'admin', 'debug_user']))
                        <!-- Notification Bell Dropdown (Opsi A) -->
                        <div class="dropdown me-1" id="notificationBellDropdown">
                            <button class="btn btn-light border p-2 position-relative d-flex align-items-center justify-content-center" 
                                    type="button" id="notifBellBtn" data-bs-toggle="dropdown" aria-expanded="false" 
                                    style="width: 40px; height: 40px; border-radius: 10px;" title="Notifikasi Milestone Laporan">
                                <i class="bi bi-bell fs-5 text-dark"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                      id="notifCountBadge" style="display:none; font-size: 0.65rem; padding: 0.25em 0.5em;">0</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 animate slideIn p-0" 
                                 aria-labelledby="notifBellBtn" 
                                 style="width: 380px; max-width: 92vw; border-radius: 14px; overflow: hidden; z-index: 1055;">
                                <div class="p-3 bg-primary text-white d-flex align-items-center justify-content-between">
                                    <div class="fw-bold" style="font-size: 0.88rem;">
                                        <i class="bi bi-bell-fill me-1"></i> Milestone Laporan (4, 8, 12... 32)
                                    </div>
                                    <button type="button" class="btn btn-sm btn-link text-white text-decoration-none p-0 fw-semibold" 
                                            onclick="markAllNotifsAsRead()" style="font-size: 0.72rem;">
                                        Tandai Semua Dibaca
                                    </button>
                                </div>
                                <div id="notifListContainer" style="max-height: 380px; overflow-y: auto;">
                                    <div class="text-center py-4 text-muted small" id="notifLoading">
                                        <div class="spinner-border spinner-border-sm text-primary me-1"></div> Memuat...
                                    </div>
                                </div>
                                <div class="p-2 bg-light text-center border-top">
                                    <span class="text-muted small" style="font-size: 0.72rem;">
                                        <i class="bi bi-info-circle me-1"></i>Notifikasi otomatis untuk sesi kelipatan 4
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-1 pe-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center border" style="width: 38px; height: 38px;">
                                    <span class="fw-bold">{{ substr(Auth::user()->nama_lengkap, 0, 1) }}</span>
                                </div>
                                <div class="d-none d-md-block line-height-sm text-start">
                                    <span class="d-block fw-bold small text-dark">{{ Str::limit(Auth::user()->nama_lengkap, 15) }}</span>
                                    <span class="d-block x-small text-muted" style="font-size: 0.7rem;">{{ ucfirst(Auth::user()->role) }}</span>
                                    @if(Auth::user()->role === 'instruktur' && Auth::user()->instructor_id)
                                    <span class="d-block badge rounded-pill px-1" style="font-size: 0.62rem; background: #e0e7ff; color: #4338ca; font-weight: 700;">{{ Auth::user()->instructor_id }}</span>
                                    @endif
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end animate slideIn">
                                <li><div class="dropdown-header">Akun Saya</div></li>
                                @if(Auth::user()->role === 'instruktur' && Auth::user()->instructor_id)
                                <li>
                                    <div class="dropdown-item disabled pe-none" style="font-size: 0.8rem;">
                                        <i class="bi bi-person-badge me-2 text-primary"></i>
                                        <span class="text-muted">ID: </span>
                                        <span class="fw-bold text-primary">{{ Auth::user()->instructor_id }}</span>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @endif
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
                    @if (session('status') || session('success') || session('error') || session('warning') || session('info'))
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
            @if (session('status') || session('success') || session('error') || session('warning') || session('info'))
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

    <!-- PWA Install Modal untuk iOS (Safari) -->
    <div class="modal fade" id="iosInstallModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0" style="padding: 1.5rem 1.5rem 0.5rem;">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="bi bi-apple me-2 text-primary fs-4"></i>Install Aplikasi di iPhone / iPad
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <img src="{{ asset('images/logo-erlass.png') }}" width="64" height="64" class="rounded-4 mb-3 shadow-sm" alt="Logo Erlass">
                    <p class="small text-secondary mb-4">Ikuti 2 langkah mudah untuk memasang <strong>Erlass Ekskul</strong> ke layar utama iPhone Anda:</p>
                    
                    <div class="p-3 bg-light rounded-3 text-start mb-3 border">
                        <div class="d-flex align-items-center gap-3 mb-2.5">
                            <span class="badge bg-primary rounded-circle px-2.5 py-1">1</span>
                            <span class="small fw-bold text-dark">Tekan tombol <strong>Share</strong> <i class="bi bi-box-arrow-up text-primary fs-5 ms-1"></i> di bagian bawah browser Safari.</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-primary rounded-circle px-2.5 py-1">2</span>
                            <span class="small fw-bold text-dark">Pilih menu <strong>"Add to Home Screen"</strong> (Tambah ke Layar Utama) <i class="bi bi-plus-square text-primary fs-5 ms-1"></i>.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary w-100 rounded-3 fw-bold py-2" data-bs-dismiss="modal">Saya Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <!-- PWA Update Toast Notification -->
    <div id="pwaUpdateToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090; display: none;">
        <div class="toast show border-0 shadow-lg rounded-4 text-white" style="background: linear-gradient(135deg, #0F172A, #1E40AF);">
            <div class="toast-body d-flex align-items-center justify-content-between gap-3 p-3">
                <div>
                    <div class="fw-bold small"><i class="bi bi-rocket-takeoff-fill text-warning me-2"></i>Versi Baru Tersedia!</div>
                    <div class="text-white-50" style="font-size: 0.78rem;">Pembaruan fitur Erlass Ekskul siap dipasang.</div>
                </div>
                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm" id="btnReloadPwa">
                    Perbarui
                </button>
            </div>
        </div>
    </div>

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
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            
            // Create backdrop element dynamically if it doesn't exist
            let backdrop = document.getElementById('sidebar-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'sidebar-backdrop';
                backdrop.className = 'fade position-fixed top-0 start-0 w-100 h-100 d-none';
                backdrop.style.cssText = 'background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 1040; transition: opacity 0.3s ease; cursor: pointer; -webkit-tap-highlight-color: transparent;';
                document.body.appendChild(backdrop);
            }

            if (sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function () {
                    const isActive = sidebar.classList.toggle('active');
                    if (window.innerWidth <= 991.98) {
                        if (isActive) {
                            backdrop.classList.remove('d-none');
                            setTimeout(() => backdrop.classList.add('show'), 10);
                        } else {
                            backdrop.classList.remove('show');
                            setTimeout(() => backdrop.classList.add('d-none'), 300);
                        }
                    } else {
                        content.classList.toggle('active');
                    }
                });
            }

            backdrop.addEventListener('click', function() {
                sidebar.classList.remove('active');
                backdrop.classList.remove('show');
                setTimeout(() => backdrop.classList.add('d-none'), 300);
            });

            // ═══ PWA Service Worker & Lifecycle Registration ═══
            const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

            // Show PWA Install menu for iOS devices if not already standalone
            if (isIos && !isStandalone) {
                document.getElementById('pwa-install-item')?.classList.remove('d-none');
            }

            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registered with scope:', registration.scope);
                        
                        // Detect Service Worker Update
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // New SW version is available and waiting
                                    showPwaUpdateToast(newWorker);
                                }
                            });
                        });
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });

                // Listen for controllerchange event (SW activated)
                let refreshing = false;
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    if (!refreshing) {
                        refreshing = true;
                        window.location.reload();
                    }
                });
            }

            // Show PWA Update Toast UI
            function showPwaUpdateToast(worker) {
                const toastContainer = document.getElementById('pwaUpdateToast');
                const btnReload = document.getElementById('btnReloadPwa');
                if (toastContainer) {
                    toastContainer.style.display = 'block';
                    if (btnReload) {
                        btnReload.addEventListener('click', () => {
                            worker.postMessage({ type: 'SKIP_WAITING' });
                        });
                    }
                }
            }

            // PWA Installation Prompt Logic (Android / Desktop / iOS)
            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                document.getElementById('pwa-install-item')?.classList.remove('d-none');
            });

            const btnInstall = document.getElementById('btn-pwa-install');
            if (btnInstall) {
                btnInstall.addEventListener('click', async () => {
                    if (isIos) {
                        // Trigger iOS Install Modal
                        const iosModal = new bootstrap.Modal(document.getElementById('iosInstallModal'));
                        iosModal.show();
                        return;
                    }

                    if (!deferredPrompt) {
                        alert('Aplikasi sudah terpasang atau perangkat Anda mendukung instalasi dari menu peramban.');
                        return;
                    }
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log('PWA installation choice:', outcome);
                    deferredPrompt = null;
                    document.getElementById('pwa-install-item')?.classList.add('d-none');
                });
            }

            window.addEventListener('appinstalled', () => {
                console.log('PWA was installed successfully!');
                document.getElementById('pwa-install-item')?.classList.add('d-none');
            });

            // Web Push Notification Permission Request Helper
            window.requestWebPushPermission = function() {
                if ('Notification' in window && Notification.permission !== 'granted') {
                    Notification.requestPermission().then((permission) => {
                        if (permission === 'granted') {
                            showNetworkToast('Izin notifikasi aplikasi berhasil diaktifkan!', 'success');
                        }
                    });
                }
            };

            // PWA Network Status (Offline/Online Toast)
            function updateNetworkStatus(e) {
                if (!navigator.onLine) {
                    showNetworkToast('Koneksi internet terputus. Sistem beralih ke mode offline.', 'danger');
                } else if (e && e.type === 'online') {
                    showNetworkToast('Koneksi internet Anda telah kembali terhubung!', 'success');
                }
            }
            window.addEventListener('offline', updateNetworkStatus);
            window.addEventListener('online', updateNetworkStatus);

            function showNetworkToast(msg, type) {
                let toastEl = document.getElementById('pwa-network-toast');
                if (!toastEl) {
                    toastEl = document.createElement('div');
                    toastEl.id = 'pwa-network-toast';
                    toastEl.className = 'position-fixed bottom-0 end-0 p-3';
                    toastEl.style.zIndex = '9999';
                    document.body.appendChild(toastEl);
                }
                toastEl.innerHTML = `
                    <div class="toast align-items-center text-white bg-${type} border-0 show shadow-lg" role="alert">
                        <div class="d-flex">
                            <div class="toast-body fw-bold">
                                <i class="bi bi-${type === 'danger' ? 'wifi-off' : 'wifi'} me-2 fs-6"></i> ${msg}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
                        </div>
                    </div>
                `;
                setTimeout(() => {
                    const t = toastEl.querySelector('.toast');
                    if (t) t.remove();
                }, 4000);
            }
        });
        window.addEventListener("beforeunload", function() {
            NProgress.start();
        });
    </script>

    @if(Auth::check() && in_array(Auth::user()->role, ['webmaster', 'admin_sistem', 'admin', 'debug_user']))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        fetchUnreadNotifications();
        setInterval(fetchUnreadNotifications, 30000);
    });

    function fetchUnreadNotifications() {
        fetch("{{ route('admin.notifications.unread') }}")
            .then(r => r.json())
            .then(res => {
                const badge = document.getElementById('notifCountBadge');
                const container = document.getElementById('notifListContainer');

                if (!badge || !container) return;

                if (res.unread_count > 0) {
                    badge.textContent = res.unread_count > 99 ? '99+' : res.unread_count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }

                if (!res.notifications || res.notifications.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-bell-slash fs-4 d-block mb-1 opacity-50"></i>
                            Belum ada notifikasi milestone baru
                        </div>
                    `;
                    return;
                }

                container.innerHTML = res.notifications.map(n => {
                    const d = n.data || {};
                    const tgl4Html = (d.tanggal_mengajar_4 || []).map(t => 
                        `<span class="badge bg-white text-dark border me-1 mb-1 shadow-sm" style="font-size:0.68rem; font-weight:600;">P${t.pertemuan_ke}: ${t.tanggal}</span>`
                    ).join('');

                    return `
                        <div class="p-3 border-bottom notif-item" id="notif-item-${n.id}" style="background: #F8FAFC; transition: background 0.15s;">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                <span class="badge bg-primary text-white" style="font-size:0.68rem; font-weight:700;">
                                    Milestone Pertemuan Ke-${d.pertemuan_ke}
                                </span>
                                <small class="text-muted" style="font-size:0.68rem;">${formatTimeAgo(n.created_at)}</small>
                            </div>
                            <div class="fw-bold text-dark" style="font-size:0.85rem;">${escJs(d.sekolah_nama || 'Sekolah')}</div>
                            <div class="text-secondary small mb-2" style="font-size:0.78rem;">
                                <strong>${escJs(d.kategori)}</strong> • ${escJs(d.rombel)} • Ins: <strong>${escJs(d.instruktur_nama)}</strong>
                            </div>
                            <div class="mb-2 p-2 rounded bg-light border">
                                <div class="text-muted mb-1 fw-bold" style="font-size:0.7rem;"><i class="bi bi-calendar4-week me-1 text-primary"></i>4 Tanggal Mengajar:</div>
                                <div class="d-flex flex-wrap">${tgl4Html}</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-1" style="font-size:0.75rem;">
                                <span class="text-success fw-bold"><i class="bi bi-people-fill me-1"></i>${d.jumlah_hadir} Hadir</span>
                                <div class="d-flex gap-1">
                                    ${d.foto_absensi_url ? `<a href="${d.foto_absensi_url}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:0.7rem;"><i class="bi bi-file-earmark-image me-1"></i>Absensi</a>` : ''}
                                    ${d.report_detail_url ? `<a href="${d.report_detail_url}" target="_blank" class="btn btn-primary btn-sm py-0 px-2" style="font-size:0.7rem;"><i class="bi bi-eye me-1"></i>Detail</a>` : ''}
                                    <button class="btn btn-outline-secondary btn-sm py-0 px-1" title="Tandai Dibaca" onclick="markNotifAsRead(${n.id})" style="font-size:0.7rem;"><i class="bi bi-check2"></i></button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            })
            .catch(() => {});
    }

    function markNotifAsRead(id) {
        fetch("{{ url('admin/notifications') }}/" + id + "/read", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(() => {
            fetchUnreadNotifications();
        });
    }

    function markAllNotifsAsRead() {
        fetch("{{ route('admin.notifications.read-all') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(() => {
            fetchUnreadNotifications();
        });
    }

    function formatTimeAgo(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        const diff = Math.floor((new Date() - d) / 1000);
        if (diff < 60) return 'Baru saja';
        if (diff < 3600) return Math.floor(diff / 60) + 'm lalu';
        if (diff < 86400) return Math.floor(diff / 3600) + 'j lalu';
        return Math.floor(diff / 86400) + 'h lalu';
    }

    function escJs(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    </script>
    @endif
</body>
</html>