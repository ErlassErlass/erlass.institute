<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Erlass Ekskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark">
        <div class="container">
            <a href="{{ route('dashboard') }}" class="navbar-brand text-white">Dashboard</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="{{ route('sekolah.index') }}" class="nav-link text-white">List Sekolah</a>
                </li>
                @if(Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link text-white">List Users</a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>
</body>
</html>