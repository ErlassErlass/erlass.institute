<!DOCTYPE html>
<html>
<head>
    <title>My App</title>
    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>