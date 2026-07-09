<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Agenda Kegiatan Ekstrakurikuler Erlass Institute — rekap sesi mengajar yang sedang dan telah berlangsung.">
    <title>@yield('title', 'Agenda Kegiatan') — Erlass Institute</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --erlass-blue:       #1E3A5F;
            --erlass-blue-mid:   #2C5282;
            --erlass-accent:     #3B82F6;
            --erlass-accent-light: #EBF4FF;
            --erlass-gold:       #F59E0B;
            --erlass-surface:    #F8FAFC;
            --erlass-border:     #E2E8F0;
            --erlass-text:       #1A202C;
            --erlass-muted:      #64748B;
        }

        * { box-sizing: border-box; }
        html, body { height: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--erlass-surface);
            color: var(--erlass-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar ────────────────────────────────────────── */
        .public-navbar {
            background: linear-gradient(135deg, var(--erlass-blue) 0%, var(--erlass-blue-mid) 100%);
            box-shadow: 0 2px 20px rgba(30,58,95,.35);
            padding: 0.75rem 0;
        }

        .public-navbar .navbar-brand {
            color: #fff !important;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: -.01em;
        }

        .public-navbar .navbar-brand img {
            filter: brightness(0) invert(1);
            height: 34px;
        }

        .badge-publik {
            background: rgba(255,255,255,.15);
            color: #fff;
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .06em;
            border: 1px solid rgba(255,255,255,.3);
            padding: .25rem .55rem;
            border-radius: 20px;
        }

        /* ── Main content ──────────────────────────────────── */
        main { flex: 1; }

        /* ── Footer ────────────────────────────────────────── */
        .public-footer {
            background: var(--erlass-blue);
            color: rgba(255,255,255,.65);
            font-size: .8rem;
            padding: 1rem 0;
            text-align: center;
        }

        .public-footer a { color: rgba(255,255,255,.8); text-decoration: none; }
        .public-footer a:hover { color: #fff; }

        @yield('extra-styles')
    </style>
    @stack('styles')
</head>
<body>

<!-- ── Navbar ── -->
<nav class="public-navbar navbar">
    <div class="container-xl d-flex align-items-center justify-content-between">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="{{ asset('images/logo-erlass.png') }}" alt="Erlass Logo">
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="badge-publik"><i class="bi bi-globe2 me-1"></i>Data Publik</span>
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light ms-2">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </a>
        </div>
    </div>
</nav>

<!-- ── Main ── -->
<main>
    @yield('content')
</main>

<!-- ── Footer ── -->
<footer class="public-footer">
    <div class="container-xl">
        <span>&copy; {{ date('Y') }} Erlass Institute &mdash; Sistem Akademik Ekstrakurikuler</span>
        <span class="mx-2">|</span>
        <a href="{{ route('home') }}">Beranda</a>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
