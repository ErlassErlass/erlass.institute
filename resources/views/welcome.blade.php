@extends('layouts.guest')

@section('content')
<div class="container py-5">
    <div class="p-5 text-center bg-light rounded-3 shadow-sm">
        <h1 class="display-4 fw-bold">Selamat Datang di Erlass Ekskul</h1>
        <p class="lead text-muted mt-3">
            Its Coding Time, Be Smart, Be Creative~.
        </p>

        <figure class="mt-5">
            <blockquote class="blockquote">
                <p>"Inspire with knowledge, serve with excellence—transform lives together."</p>
            </blockquote>
            <figcaption class="blockquote-footer">
                Our Mission
            </figcaption>
        </figure>

        <div class="mt-5">
            @guest
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 gap-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">
                        Register
                    </a>
                </div>
            @else
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg px-4">
                        <i class="bi bi-speedometer2 me-1"></i> Go to Dashboard
                    </a>
                </div>
            @endguest
        </div>
    </div>
</div>
@endsection