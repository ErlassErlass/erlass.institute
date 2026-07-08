@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <x-breadcrumb :items="[
        ['title' => 'Dashboard', 'url' => route('dashboard')],
        ['title' => 'Profile', 'url' => null]
    ]" />
    
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($user->role === 'instruktur')
                <!-- Unified Instructor Tabs -->
                @include('profile.partials.instructor-tabs')

                <!-- Hidden Password Card (Will be appended to Security Pane dynamically) -->
                <div id="securityFormCard" class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-lock-fill me-2"></i>Ganti Password Keamanan</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            @else
                <!-- Classic Profile Forms for Admin/Webmaster -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-circle me-2"></i>Profile Information</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-lock me-2"></i>Update Password</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 text-danger fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Delete Account</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
