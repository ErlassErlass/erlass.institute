@extends('layouts.app')

@section('title', 'Profil Pengguna — ' . $user->nama_lengkap)

@push('styles')
<style>
    /* ── Impeccable Design Tokens ──────────────────────────── */
    :root {
        --imp-navy: #0F172A;
        --imp-blue: #3B82F6;
        --imp-blue-dark: #1E40AF;
        --imp-green: #10B981;
        --imp-red: #EF4444;
        --imp-amber: #F59E0B;
        --imp-purple: #8B5CF6;
        --imp-slate: #64748B;
        --imp-surface: #FFFFFF;
        --imp-surface-alt: #F8FAFC;
        --imp-border: #E2E8F0;
        --imp-radius: 16px;
        --imp-shadow: 0 4px 24px rgba(15, 23, 42, 0.08);
        --imp-shadow-lg: 0 12px 40px rgba(15, 23, 42, 0.12);
    }

    /* ── Profile Hero Header ───────────────────────────────── */
    .profile-hero {
        background: linear-gradient(135deg, var(--imp-navy) 0%, #1E3A5F 50%, var(--imp-blue-dark) 100%);
        border-radius: var(--imp-radius);
        padding: 2.25rem 2.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: var(--imp-shadow-lg);
    }
    .profile-hero::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -15%;
        width: 340px;
        height: 340px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.28), transparent 70%);
        pointer-events: none;
    }
    .profile-hero::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -10%;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.16), transparent 70%);
        pointer-events: none;
    }
    .profile-hero-content {
        position: relative;
        z-index: 1;
    }
    .profile-avatar-circle {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        border: 3.5px solid rgba(255, 255, 255, 0.9);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        flex-shrink: 0;
    }
    .hero-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 20px;
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        color: #FFFFFF;
        font-weight: 600;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    /* ── Impeccable Stat Cards ─────────────────────────────── */
    .impeccable-stat-card {
        background: var(--imp-surface);
        border: 1px solid var(--imp-border);
        border-radius: 16px;
        padding: 1.25rem 1.4rem;
        box-shadow: var(--imp-shadow);
        position: relative;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .impeccable-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--imp-shadow-lg);
    }
    .stat-accent-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    .accent-blue { background: linear-gradient(90deg, #3B82F6, #1E40AF); }
    .accent-emerald { background: linear-gradient(90deg, #10B981, #059669); }
    .accent-violet { background: linear-gradient(90deg, #8B5CF6, #7C3AED); }
    .accent-amber { background: linear-gradient(90deg, #F59E0B, #D97706); }
    .accent-rose { background: linear-gradient(90deg, #F43F5E, #E11D48); }

    .stat-icon-circle {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* ── Impeccable Custom Pills Nav ───────────────────────── */
    .nav-pills-impeccable {
        background: var(--imp-surface);
        border: 1px solid var(--imp-border);
        border-radius: 16px;
        padding: 0.5rem;
        box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
    }
    .nav-pills-impeccable .nav-link {
        color: var(--imp-slate);
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.65rem 1.15rem;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .nav-pills-impeccable .nav-link:hover {
        color: var(--imp-navy);
        background: var(--imp-surface-alt);
    }
    .nav-pills-impeccable .nav-link.active {
        color: #FFFFFF;
        background: linear-gradient(135deg, var(--imp-navy) 0%, var(--imp-blue-dark) 100%);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.18);
    }

    /* ── Impeccable Report Card in Tab ─────────────────────── */
    .profile-report-card {
        background: var(--imp-surface);
        border: 1px solid var(--imp-border);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.25s ease;
    }
    .profile-report-card:hover {
        border-color: #CBD5E1;
        box-shadow: var(--imp-shadow);
        transform: translateY(-2px);
    }
    /* ── Tab Pane Visibility ───────────────────────────────── */
    .tab-pane:not(.active) {
        display: none !important;
    }
    .tab-pane.active {
        display: block !important;
    }
    .btn-xs {
        font-size: 0.775rem;
        padding: 0.28rem 0.65rem;
        line-height: 1.35;
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['title' => 'Dashboard', 'url' => route('dashboard')],
        ['title' => 'Profil Saya', 'url' => null]
    ]" />

    {{-- Alerts --}}
    @if (session('success') || session('status') === 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm border-0 rounded-4 d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
        <div>{{ session('success') ?? 'Pembaruan data profil berhasil disimpan!' }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any() && !$errors->updatePassword->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0 rounded-4" role="alert">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Terdapat kesalahan pengisian:</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- ═══════════ IMPECCABLE PROFILE HERO ═══════════ --}}
    <div class="profile-hero">
        <div class="profile-hero-content d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3.5">
                {{-- Avatar --}}
                <div class="profile-avatar-circle">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->nama_lengkap }}" class="w-100 h-100 object-fit-cover">
                    @else
                        <span class="fw-bold text-white fs-3">{{ $user->initials }}</span>
                    @endif
                </div>

                {{-- User Info --}}
                <div>
                    <h1 class="h3 fw-bold mb-1 text-white text-shadow">
                        @if($user->role === 'instruktur' && !empty($profile->gelar_depan))
                            {{ $profile->gelar_depan }}
                        @endif
                        {{ $user->nama_lengkap }}
                        @if($user->role === 'instruktur' && !empty($profile->gelar_belakang))
                            , {{ $profile->gelar_belakang }}
                        @endif
                    </h1>
                    
                    <p class="mb-2 text-white-50 small">
                        @if($user->role === 'instruktur' && !empty($profile->nama_panggilan))
                            Panggilan: <strong class="text-white">"{{ $profile->nama_panggilan }}"</strong> &bull;
                        @endif
                        ID Akun #{{ $user->id }} &bull; Bergabung sejak {{ $user->created_at ? $user->created_at->translatedFormat('F Y') : '-' }}
                    </p>

                    {{-- Meta Chips --}}
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        {{-- Role Chip --}}
                        <span class="hero-meta-chip">
                            <i class="bi bi-person-badge"></i> {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>

                        {{-- WhatsApp Chip --}}
                        @if($user->no_telephone)
                        <span class="hero-meta-chip">
                            <i class="bi bi-whatsapp text-success"></i> {{ $user->no_telephone }}
                        </span>
                        @endif

                        {{-- Email Chip --}}
                        <span class="hero-meta-chip">
                            <i class="bi bi-envelope"></i> {{ $user->email }}
                        </span>

                        {{-- Domicile Chip --}}
                        @if($user->role === 'instruktur' && !empty($profile->kota_domisili))
                        <span class="hero-meta-chip">
                            <i class="bi bi-geo-alt-fill text-warning"></i> {{ $profile->kota_domisili }}
                        </span>
                        @endif

                        {{-- Level / Verification Chip --}}
                        @if($user->role === 'instruktur')
                            <span class="hero-meta-chip">
                                <i class="bi bi-stars text-warning"></i> Level {{ ucfirst($user->level ?? 'Junior') }}
                            </span>
                            @if($user->verification_status === 'approved')
                                <span class="hero-meta-chip bg-success bg-opacity-25 border-success border-opacity-50">
                                    <i class="bi bi-shield-check text-success"></i> Terverifikasi
                                </span>
                            @elseif($user->verification_status === 'pending')
                                <span class="hero-meta-chip bg-warning bg-opacity-25 border-warning border-opacity-50">
                                    <i class="bi bi-hourglass-split text-warning"></i> Menunggu Verifikasi
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ IMPECCABLE STAT CARDS ═══════════ --}}
    <div class="row g-3 mb-4">
        @if($user->role === 'instruktur')
            {{-- Card 1: Total Laporan --}}
            <div class="col-6 col-md-3">
                <div class="impeccable-stat-card h-100">
                    <div class="stat-accent-bar accent-blue"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-secondary">Total Laporan</span>
                        <div class="stat-icon-circle bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-journal-text"></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">{{ $reportStats['total_reports'] ?? 0 }}</div>
                    <small class="text-muted">Sesi pembelajaran selesai</small>
                </div>
            </div>

            {{-- Card 2: Rata-rata Kehadiran --}}
            <div class="col-6 col-md-3">
                <div class="impeccable-stat-card h-100">
                    <div class="stat-accent-bar accent-emerald"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-secondary">Kehadiran Siswa</span>
                        <div class="stat-icon-circle bg-success bg-opacity-10 text-success">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">{{ $reportStats['avg_attendance_percent'] ?? 100 }}%</div>
                    <small class="text-muted">Rata-rata partisipasi kelas</small>
                </div>
            </div>

            {{-- Card 3: Kelengkapan Berkas --}}
            <div class="col-6 col-md-3">
                <div class="impeccable-stat-card h-100">
                    <div class="stat-accent-bar accent-violet"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-secondary">Kelengkapan Data</span>
                        <div class="stat-icon-circle text-purple" style="background: rgba(139, 92, 246, 0.12); color: #8B5CF6;">
                            <i class="bi bi-clipboard2-check-fill"></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">{{ $reportStats['profile_completion'] ?? 0 }}%</div>
                    <small class="text-muted">Berkas & identitas terisi</small>
                </div>
            </div>

            {{-- Card 4: Status Akun --}}
            <div class="col-6 col-md-3">
                <div class="impeccable-stat-card h-100">
                    <div class="stat-accent-bar accent-amber"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-secondary">Status Instruktur</span>
                        <div class="stat-icon-circle bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">{{ ucfirst($user->status) }}</div>
                    <small class="text-muted">{{ ucfirst($user->verification_status ?? 'Aktif') }}</small>
                </div>
            </div>
        @else
            {{-- Stat Cards for Admin / Webmaster --}}
            <div class="col-6 col-md-3">
                <div class="impeccable-stat-card h-100">
                    <div class="stat-accent-bar accent-blue"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-secondary">Hak Akses</span>
                        <div class="stat-icon-circle bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-shield-shaded"></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
                    <small class="text-muted">Tingkat wewenang sistem</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="impeccable-stat-card h-100">
                    <div class="stat-accent-bar accent-emerald"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-secondary">Status Akun</span>
                        <div class="stat-icon-circle bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">{{ ucfirst($user->status) }}</div>
                    <small class="text-muted">Aktif & terautentikasi</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="impeccable-stat-card h-100">
                    <div class="stat-accent-bar accent-violet"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-secondary">Total Pengguna</span>
                        <div class="stat-icon-circle text-purple" style="background: rgba(139, 92, 246, 0.12); color: #8B5CF6;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">{{ $reportStats['total_users'] ?? '-' }}</div>
                    <small class="text-muted">User terdaftar dalam sistem</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="impeccable-stat-card h-100">
                    <div class="stat-accent-bar accent-amber"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-secondary">Total Laporan</span>
                        <div class="stat-icon-circle bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-journal-check"></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">{{ $reportStats['total_reports'] ?? '-' }}</div>
                    <small class="text-muted">Total sesi mengajar</small>
                </div>
            </div>
        @endif
    </div>

    {{-- ═══════════ MAIN CONTENT SECTION ═══════════ --}}
    @if($user->role === 'instruktur')
        <!-- Unified Instructor Tabs (All 6 Tabs self-contained) -->
        @include('profile.partials.instructor-tabs')
    @else
        <!-- Classic Profile Forms for Admin/Webmaster with Impeccable Styling -->
        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-circle text-primary me-2"></i>Informasi Profil Akun</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1.5 fw-bold">Admin Workspace</span>
            </div>
            <div class="card-body p-4 p-md-5">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3.5 px-4 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Perbarui Password</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden border-danger border-opacity-25">
            <div class="card-header bg-danger bg-opacity-10 py-3.5 px-4 border-bottom border-danger border-opacity-25">
                <h5 class="mb-0 text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Zona Berbahaya</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    @endif
</div>
@endsection
