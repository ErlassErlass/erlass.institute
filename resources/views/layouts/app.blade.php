<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Erlass Ekskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .navbar .nav-link {
        padding: 0.5rem 1rem !important; /* Match other links */
    }

    .navbar .nav-item + .nav-item {
        margin-left: 1rem;
    }
</style>
<body>
<!-- resources/views/layouts/app.blade.php -->
<nav class="navbar navbar-expand-lg bg-dark">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" width="50" height="50" class="d-inline-block align-text-top me-2">
            Erlass Ekskul
        </a>

        <!-- Navigation Links -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link text-white">{{ __('Dashboard') }}</a>
            </li>
            @if(Auth::user()->role === 'admin')
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link text-white">{{ __('Users') }}</a>
                </li>
            @endif

            <!-- Logout Item -->
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button 
                        type="submit" 
                        class="nav-link text-white btn btn-link p-0"
                        onclick="return confirm('Are you sure you want to log out?')"
                    >
                        {{ __('Logout') }}
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>

    <div class="container mt-4">
        @yield('content')
    </div>
</body>

</html>