@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .card {
        border: 1px solid #ddd;
        padding: 20px;
        margin: 10px;
        border-radius: 5px;
        background: #f9f9f9;
    }
    .card h2 { color: #333; }
    .card ul { list-style: none; padding: 0; }
    .card li { margin: 5px 0; }
    .card a { color: #007bff; text-decoration: none; }
    .card a:hover { text-decoration: underline; }
</style>
</head>
<body>
    
    <div class="card">
        <h2>Hello, {{ Auth::user()->nama_lengkap }}</h2>
        <p>Role: {{ Auth::user()->role }}</p>
    </div>

    <div class="card">
        <h3>Navigation</h3>
        <ul>
            <li><a href="{{ route('sekolah.index') }}">List Sekolah</a></li>
            <li><a href="{{ route('laporan-mengajar.index') }}">Laporan Mengajar</a></li>
            <li><a href="{{ route('absensi.index') }}">Absensi</a></li>
            <li><a href="{{ route('siswa.index') }}">List Siswa</a></li>

            @if(Auth::user()->role === 'admin')
                <li><a href="{{ route('users.index') }}">User Management</a></li>
            @endif
        </ul>
    </div>
</body>
</html>
@endsection
