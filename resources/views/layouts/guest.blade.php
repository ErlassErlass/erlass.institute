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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
    <style>
        :root {
            /* Palette: Modern Elegant (Shared with App) */
            --font-primary: 'Outfit', sans-serif;
            --primary-color: #3b82f6; 
            --primary-dark: #2563eb;
            --bg-body: #f1f5f9;
        }

        body {
            font-family: var(--font-primary);
            background-color: var(--bg-body);
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(6, 182, 212, 0.1) 0px, transparent 50%);
            background-attachment: fixed; 
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        
        .card {
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            transition: all 0.3s ease;
        }
        
        .form-control {
            border-radius: 12px;
            padding: 0.8rem 1rem;
            border: 1px solid #e2e8f0;
            background-color: rgba(255,255,255,0.8);
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        
        .btn {
            border-radius: 12px;
            padding: 0.8rem 1.5rem;
            font-weight: 500;
            letter-spacing: 0.01em;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center py-4">
    <main class="w-100">
        @yield('content')
        {{ $slot ?? '' }}
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>