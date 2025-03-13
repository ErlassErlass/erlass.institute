<!-- resources/views/welcome.blade.php -->
@extends('layouts.guest')

@section('content')
<div class="container mt-5 text-center">
    <h1 class="display-4">Selamat Datang di Erlass Ekskul</h1>
    <p class="lead">Its Coding Time, Be Smart , Be Creative~.</p>
    <h1 class="display-6">
        <strong><em>Inspire with knowledge, serve with excellence—transform lives together."</h1>
    @if (Auth::guest())
    <div class="mt-4">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Log In</a>
        <a href="{{ route('register') }}" class="btn btn-secondary btn-lg">Register</a>
    </div>
    @else
    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg">Go to Dashboard</a>
    </div>
    @endif
</div>
@endsection