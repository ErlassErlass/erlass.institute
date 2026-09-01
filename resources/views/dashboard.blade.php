@extends('layouts.app')

@push('styles')
<style>
    /* ── Impeccable Design Tokens for Dashboard ──────────────────── */
    .dashboard-hero {
        background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #2563EB 100%);
        border-radius: 20px;
        color: #FFFFFF !important;
        padding: 2.25rem 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 36px rgba(15, 23, 42, 0.15);
    }
    .dashboard-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 15%, rgba(59, 130, 246, 0.28) 0%, transparent 55%),
                    radial-gradient(circle at 15% 85%, rgba(245, 158, 11, 0.15) 0%, transparent 45%);
        pointer-events: none;
    }
    .hero-greeting {
        font-size: 1.85rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.25;
        margin-bottom: 0.35rem;
        color: #FFFFFF !important;
    }
    .hero-subtext {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 400;
    }
    .hero-date-pill {
        background: rgba(255, 255, 255, 0.15) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        color: #FFFFFF !important;
        padding: 0.59rem 1.2rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* ── Impeccable Stat Cards ────────────────────────────── */
    .impeccable-stat-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 1.25rem 1.4rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
    }
    .impeccable-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3B82F6, #6366F1);
        border-radius: 16px 16px 0 0;
    }
    .impeccable-stat-card.accent-emerald::before { background: linear-gradient(90deg, #10B981, #059669); }
    .impeccable-stat-card.accent-amber::before { background: linear-gradient(90deg, #F59E0B, #D97706); }
    .impeccable-stat-card.accent-violet::before { background: linear-gradient(90deg, #8B5CF6, #7C3AED); }
    .impeccable-stat-card.accent-rose::before { background: linear-gradient(90deg, #F43F5E, #E11D48); }

    .impeccable-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
        border-color: #CBD5E1;
    }
    .stat-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* ── Quick Action Hub Pills ──────────────────────────── */
    .quick-action-pill {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 0.9rem 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        text-decoration: none;
        color: #1E293B;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    .quick-action-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(37, 99, 235, 0.12);
        border-color: #93C5FD;
        color: #2563EB;
    }
    .quick-action-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    /* ── WhatsApp Contact Pill Chip ─────────────────────── */
    .wa-contact-chip {
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        color: #047857;
        font-weight: 700;
        font-size: 0.775rem;
        padding: 0.3rem 0.65rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .wa-contact-chip:hover {
        background: #D1FAE5;
        color: #065F46;
        border-color: #6EE7B7;
        transform: scale(1.03);
    }
    .wa-dot {
        width: 7px;
        height: 7px;
        background-color: #10B981;
        border-radius: 50%;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
    }

    /* ── Card Styling ────────────────────────────────────── */
    .dashboard-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header Hero Banner -->
    <div class="dashboard-hero mb-4" id="tour-welcome-card">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(255, 255, 255, 0.18) !important; color: #FFFFFF !important; border: 1px solid rgba(255, 255, 255, 0.28) !important; font-size: 0.75rem;">
                        <i class="bi bi-shield-check me-1"></i> PORTAL UTAMA ERLASS
                    </span>
                </div>
                <h1 class="hero-greeting mb-2">
                    @php
                        $hour = date('H');
                        $greeting = 'Selamat Pagi';
                        if ($hour >= 11 && $hour < 15) $greeting = 'Selamat Siang';
                        elseif ($hour >= 15 && $hour < 18) $greeting = 'Selamat Sore';
                        elseif ($hour >= 18 || $hour < 5) $greeting = 'Selamat Malam';
                    @endphp
                    {{ $greeting }}, {{ Auth::user()->nama_lengkap }}! 👋
                </h1>
                <p class="hero-subtext mb-0">
                    Sistem Manajemen Operational & Laporan Mengajar Ekstrakurikuler Erlass Prokreatif Indonesia
                </p>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="hero-date-pill shadow-sm">
                    <i class="bi bi-calendar3 text-warning"></i>
                    <span>{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Section (Full Width at Top) -->
    @if(Auth::user()->role === 'instruktur')
        <!-- INSTRUCTOR PERSONAL STATS -->
        @include('dashboard.partials.instructor-stats')
    @else
        <!-- ADMIN STATS -->
        @include('dashboard.partials.admin-stats')
    @endif

    <!-- Quick Actions (Mobile-First Touch Target) -->
    @if(Auth::user()->role === 'instruktur')
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="quick-action-pill">
                <div class="quick-action-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark mb-0 small">Jadwal Sesi</div>
                    <small class="text-muted" style="font-size: 0.725rem;">Isi Laporan Sesi</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('laporan-mengajar.create') }}" class="quick-action-pill">
                <div class="quick-action-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-journal-plus"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark mb-0 small">Laporan Khusus (Non-Jadwal)</div>
                    <small class="text-muted" style="font-size: 0.725rem;">Event Khusus / Pameran</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('rekap-absensi') }}" class="quick-action-pill">
                <div class="quick-action-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-file-earmark-check-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark mb-0 small">Rekap Absensi</div>
                    <small class="text-muted" style="font-size: 0.725rem;">Presensi Siswa</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('laporan-mengajar.index') }}" class="quick-action-pill">
                <div class="quick-action-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark mb-0 small">Riwayat Laporan</div>
                    <small class="text-muted" style="font-size: 0.725rem;">Arsip Selesai</small>
                </div>
            </a>
        </div>
    </div>
    @endif

    <!-- Profile Completion Alert -->
    @if(isset($incomplete_profile) && $incomplete_profile)
    <div class="alert alert-warning border-0 shadow-sm mb-4 rounded-4" role="alert" style="background: #FFFBEB; border-left: 6px solid #F59E0B !important;">
        <div class="d-flex align-items-start p-2">
            <div class="bg-warning text-white rounded-circle p-2 me-3 align-self-start d-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px; min-width: 44px;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            </div>
            <div>
                <h4 class="alert-heading h6 fw-bold mb-1 text-dark">Profil Belum Lengkap!</h4>
                <p class="mb-2 small text-dark opacity-75">
                    Mohon lengkapi data berikut agar akun Anda dapat diverifikasi dan pembayaran honor dapat diproses:
                </p>
                @if(isset($missing_fields) && count($missing_fields) > 0)
                    <ul class="mb-2 small text-danger fw-semibold ps-3">
                        @foreach($missing_fields as $field)
                            <li>{{ $field }}</li>
                        @endforeach
                    </ul>
                @endif
                <a href="{{ route('instructor.profile.complete') }}" class="btn btn-warning text-dark fw-bold btn-sm px-4 py-2 border-0 rounded-pill mt-1 shadow-sm" style="transition: all 0.2s;">
                    Lengkapi Sekarang <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- IMPORTANT: Report Usage Warning -->
    @if(Auth::user()->role === 'instruktur')
    <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-4" role="alert" id="tour-instructor-late-request" style="background: #FEF2F2; border-left: 6px solid #EF4444 !important;">
        <div class="d-flex align-items-start p-2">
            <div class="bg-danger text-white rounded-circle p-2 me-3 align-self-start d-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px; min-width: 44px;">
                <i class="bi bi-megaphone-fill fs-5"></i>
            </div>
            <div>
                <h5 class="alert-heading h6 fw-bold mb-2 text-dark">PENTING: CARA PELAPORAN MENGAJAR</h5>
                <p class="mb-0 small text-dark opacity-75">Mohon perhatikan perbedaan cara pelaporan berikut agar tidak salah jalur:</p>
                <ul class="mb-0 mt-2 small text-dark ps-3">
                    <li class="mb-1">
                        <strong>Kelas Rutin / Terjadwal:</strong> WAJIB melalui menu 
                        <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="fw-bold text-decoration-underline text-danger">Jadwal Sesi &amp; Laporan</a> atau tombol <em>"Buat Laporan"</em> di tabel Jadwal Hari Ini (termasuk jika Anda menggantikan instruktur lain/inval atau kelas susulan/reschedule). Data siswa &amp; rombel terhubung otomatis.
                    </li>
                    <li>
                        <strong>Kegiatan Khusus / Non-Jadwal:</strong> Gunakan menu 
                        <a href="{{ route('laporan-mengajar.create') }}" class="fw-bold text-decoration-underline text-danger">Laporan Khusus (Non-Jadwal)</a> 
                        HANYA untuk kegiatan khusus di luar program ekskul yang tidak memiliki rombel (seperti Workshop Kilat, Juri Lomba, Sosialisasi Sales, atau Pameran).
                    </li>
                </ul>
            </div>
        </div>
    </div>



    <!-- Special Rombel & Student Attendance Check Notification (Instructor Only) -->
    @if(isset($instructor_todo_list) && $instructor_todo_list->count() > 0)
    <div class="card border-0 shadow-sm mb-4 overflow-hidden rounded-4" id="tour-instructor-todo" style="background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); border-left: 6px solid #F59E0B !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3 pb-3 border-bottom border-warning-subtle">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; min-width: 48px;">
                        <i class="bi bi-person-check-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            📢 NOTIFIKASI KHUSUS ROMBEL ANDA ({{ $instructor_todo_list->count() }} Sesi Wajib Dilaporkan)
                        </h5>
                        <p class="text-muted small mb-0">
                            Mohon periksa dan verifikasi <strong class="text-dark">kehadiran seluruh nama siswa</strong> pada Rombel yang Anda ajar sebelum menyelesaikan Laporan Mengajar.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                @foreach($instructor_todo_list->take(2) as $todo)
                <div class="col-md-6">
                    <div class="bg-white rounded-3 p-3 border shadow-xs h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary text-white fw-bold" style="font-size: 0.75rem;">
                                    {{ $todo->rombel?->nama_rombel ?? ($todo->rombel ? 'Rombel ' . $todo->rombel->nomor_rombel : 'Sesi Ekstrakurikuler') }}
                                </span>
                                <span class="badge bg-secondary rounded-pill">Pertemuan ke-{{ $todo->nomor_pertemuan }}</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $todo->rombel?->ekstrakurikuler?->kategori_program ?? $todo->ekstrakurikuler?->kategori_program ?? 'Ekstrakurikuler' }}</h6>
                            <div class="text-muted small mb-2">
                                <i class="bi bi-building me-1"></i> {{ $todo->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? $todo->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah' }}
                            </div>
                            <div class="d-flex align-items-center gap-3 text-secondary small mb-3">
                                <span><i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($todo->tanggal_terjadwal)->format('d M Y') }}</span>
                                <span><i class="bi bi-people-fill text-info me-1"></i><strong>{{ $todo->rombel ? $todo->rombel->getJumlahSiswaAktual() : 0 }} Siswa Terdaftar</strong></span>
                            </div>
                        </div>
                        <a href="{{ route('ekstrakurikuler.sessions.report.create', $todo->id) }}" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill w-100 shadow-sm">
                            <i class="bi bi-check2-square me-1"></i> Cek Nama Siswa & Buat Laporan
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            @if($instructor_todo_list->count() > 2)
            <div class="text-end mt-3">
                <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="text-warning-emphasis fw-bold small text-decoration-none">
                    Lihat Seluruh {{ $instructor_todo_list->count() }} Sesi Rombel Anda <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
    @endif

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Left/Main Column -->
        <div class="col-lg-8 col-12">
            <!-- Today's Schedule (Visible to ALL roles) -->
            <div class="dashboard-card mb-4" id="tour-instructor-today">
                <div class="dashboard-card-header p-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-calendar2-week-fill text-primary"></i>
                        <span>Jadwal Hari Ini</span>
                        <span class="text-muted fs-6 fw-normal">({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }})</span>
                    </h5>
                    @if(isset($todays_schedule))
                        <span class="badge bg-primary rounded-pill px-3 py-2 fw-semibold">{{ $todays_schedule->count() }} Sesi Hari Ini</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if(Auth::user()->role === 'instruktur')
                    <div class="px-3 pt-3 pb-1">
                        <div class="alert alert-primary border-0 rounded-3 p-3 mb-2 d-flex align-items-start gap-3 shadow-xs" style="background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%); border-left: 4px solid #2563EB !important;">
                            <div class="bg-primary text-white rounded-circle p-1.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                <i class="bi bi-geo-alt-fill" style="font-size: 0.9rem;"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.82rem;">
                                    <i class="bi bi-info-circle-fill text-primary me-1"></i> ATURAN CHECK-IN: Lakukan SAAT TIBA di Sekolah (SEBELUM Mengajar)
                                </div>
                                <p class="text-secondary small mb-0" style="font-size: 0.75rem; line-height: 1.4;">
                                    Check-in GPS mencatat <strong>jam kedatangan fisik Anda</strong>. Wajib check-in begitu sampai di lokasi sebelum kelas dimulai, <strong class="text-danger">BUKAN saat kelas selesai / saat buat laporan</strong> agar tidak tercatat terlambat oleh sistem.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(isset($todays_schedule) && $todays_schedule->count() > 0)
                        <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                                <thead class="table-light sticky-top" style="z-index: 5; background-color: #F8FAFC;">
                                    <tr class="text-secondary text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em; border-bottom: 2px solid #E2E8F0;">
                                        <th class="ps-3 py-2.5" style="width: 125px;">JAM</th>
                                        <th class="py-2.5" style="min-width: 210px;">SEKOLAH &amp; PROGRAM</th>
                                        <th class="py-2.5" style="min-width: 150px;">INSTRUKTUR</th>
                                        <th class="text-center py-2.5" style="width: 105px;">STATUS</th>
                                        <th class="text-end pe-3 py-2.5" style="width: 155px;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todays_schedule as $session)
                                        <tr>
                                            <td class="ps-3 py-2 fw-bold text-dark text-nowrap" style="font-size: 0.8rem;">
                                                <i class="bi bi-clock-fill text-primary me-1" style="font-size: 0.75rem;"></i> {{ $session->jam_mulai_terjadwal->format('H:i') }} - {{ $session->jam_selesai_terjadwal->format('H:i') }}
                                            </td>
                                            <td class="py-2">
                                                <div class="fw-bold text-dark lh-sm mb-1" style="font-size: 0.85rem;">
                                                    {{ $session->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? $session->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah' }}
                                                </div>
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                    <span class="text-secondary fw-semibold" style="font-size: 0.75rem;">{{ $session->rombel?->ekstrakurikuler?->kategori_program ?? $session->ekstrakurikuler?->kategori_program ?? 'Ekstrakurikuler' }}</span>
                                                    <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 0.7rem;">{{ $session->rombel?->nama_rombel ?? 'Rombel' }}</span>
                                                    @if($session->rombel?->ekstrakurikuler?->google_maps_link ?? $session->ekstrakurikuler?->google_maps_link)
                                                        <a href="{{ $session->rombel?->ekstrakurikuler?->google_maps_link ?? $session->ekstrakurikuler?->google_maps_link }}" target="_blank" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill text-decoration-none px-1.5 py-0.5" title="Buka Google Maps" style="font-size: 0.68rem;">
                                                            <i class="bi bi-geo-alt-fill"></i> Maps
                                                        </a>
                                                    @endif
                                                    @php
                                                        $ekskulObj = $session->rombel?->ekstrakurikuler ?? $session->ekstrakurikuler;
                                                    @endphp
                                                    @if($ekskulObj?->no_telepon)
                                                        @php
                                                            $cleanPhone = preg_replace('/[^0-9]/', '', $ekskulObj->no_telepon);
                                                            if (str_starts_with($cleanPhone, '0')) {
                                                                $cleanPhone = '62' . substr($cleanPhone, 1);
                                                            }
                                                            $waText = urlencode("Halo " . ($ekskulObj->penanggung_jawab ?? 'Bapak/Ibu') . ", saya instruktur Erlass untuk ekstrakurikuler " . ($ekskulObj->kategori_program ?? '') . ".");
                                                        @endphp
                                                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" rel="noopener" class="wa-contact-chip py-0 px-1.5" title="WhatsApp PJ: {{ $ekskulObj->penanggung_jawab ?? '' }}" style="font-size: 0.68rem;">
                                                            <span class="wa-dot"></span> WA
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-2">
                                                @if($session->instruktur)
                                                    <div class="d-flex align-items-center gap-1.5">
                                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; font-size: 10px; min-width: 26px;">
                                                            {{ substr($session->instruktur->nama_lengkap, 0, 1) }}
                                                        </div>
                                                        <span class="text-dark fw-semibold text-truncate" style="font-size: 0.8rem; max-width: 130px;" title="{{ $session->instruktur->nama_lengkap }}">
                                                            {{ $session->instruktur->nama_lengkap }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5" style="font-size: 0.7rem;">Belum Ditugaskan</span>
                                                @endif
                                            </td>
                                            <td class="text-center py-2">
                                                @php
                                                    $statusClass = match($session->status) {
                                                        'terjadwal' => 'primary',
                                                        'berlangsung' => 'warning',
                                                        'selesai' => 'success',
                                                        'dibatalkan' => 'danger',
                                                        'ditunda' => 'secondary',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }}-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                                                    {{ $session->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-3 py-2 text-nowrap">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    @if($session->laporanMengajar)
                                                        <a href="{{ route('laporan-mengajar.show', $session->laporanMengajar->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 fw-semibold shadow-xs" title="Lihat Laporan Selesai" style="font-size: 0.75rem;">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Laporan Selesai
                                                        </a>
                                                    @elseif(in_array($session->status, ['terjadwal', 'berlangsung']))
                                                        <a href="{{ route('ekstrakurikuler.sessions.report.create', $session->id) }}" class="btn btn-sm btn-primary rounded-pill px-2.5 py-1 fw-bold shadow-xs" title="Buat Laporan & Presensi Sesi Ini" style="font-size: 0.75rem;">
                                                            <i class="bi bi-pencil-square me-1"></i> Buat Laporan
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('ekstrakurikuler.sessions.show', $session->id) }}" class="btn btn-sm btn-light border rounded-pill px-2 py-1" title="Detail Sesi" style="font-size: 0.75rem;">
                                                        <i class="bi bi-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-calendar-check fs-1 mb-3 d-block text-primary opacity-50"></i>
                            <h6 class="fw-bold text-dark mb-1">Tidak ada jadwal kegiatan hari ini</h6>
                            <p class="mb-0 small text-secondary">Semua sesi mengajar berjalan sesuai rencana.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Schedule (Instructors only) -->
            @if(Auth::user()->role === 'instruktur')
            <div class="dashboard-card mb-4">
                <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar-week me-2 text-primary"></i>Agenda Mendatang 
                        <span class="text-muted fs-6 ms-2">(3 Hari ke Depan)</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($upcoming_schedule) && $upcoming_schedule->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <tbody class="border-top-0">
                                    @foreach($upcoming_schedule as $date => $sessions)
                                        <tr class="table-light">
                                            <td colspan="5" class="fw-bold ps-4 text-primary bg-primary-subtle py-2">
                                                <i class="bi bi-calendar-event me-2"></i>{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                            </td>
                                        </tr>
                                        @foreach($sessions as $session)
                                        <tr>
                                            <td class="ps-4 text-nowrap fw-semibold" style="width: 100px;">
                                                {{ $session->jam_mulai_terjadwal->format('H:i') }}
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                    <span>{{ $session->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? $session->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah' }}</span>
                                                    @if($session->rombel?->ekstrakurikuler?->google_maps_link ?? $session->ekstrakurikuler?->google_maps_link)
                                                        <a href="{{ $session->rombel?->ekstrakurikuler?->google_maps_link ?? $session->ekstrakurikuler?->google_maps_link }}" target="_blank" class="text-primary d-inline-flex align-items-center" title="Buka Google Maps">
                                                            <i class="bi bi-geo-alt-fill"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $session->rombel?->ekstrakurikuler?->kategori_program ?? $session->ekstrakurikuler?->kategori_program ?? 'Ekstrakurikuler' }} - {{ $session->rombel?->nama_rombel ?? 'Rombel' }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                 <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 rounded-pill">
                                                    Terjadwal
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('ekstrakurikuler.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-calendar-check fs-1 mb-3 d-block text-secondary"></i>
                            <p class="mb-0">Tidak ada jadwal dalam 3 hari ke depan.</p>
                        </div>
                    @endif
                    <div class="card-footer bg-white border-top p-3 text-center">
                        <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="text-decoration-none fw-semibold">
                            Lihat Jadwal Lengkap <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Verification Center (Admin Only) -->
            @if(Auth::user()->role === 'admin_sistem' || Auth::user()->role === 'webmaster' || Auth::user()->role === 'admin')
                <!-- Admin Helpdesk & Ticket Control Center -->
                @if(isset($ticket_stats) && ($ticket_stats['open'] > 0 || $ticket_stats['unread_admin'] > 0 || (isset($admin_actionable_tickets) && $admin_actionable_tickets->count() > 0)))
                <div class="dashboard-card mb-4" id="tour-admin-ticket-control" style="border-left: 6px solid #4F46E5 !important;">
                    <div class="card-header bg-indigo-subtle text-indigo-emphasis fw-bold d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 p-3" style="background: #EEF2FF; color: #3730A3;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #4F46E5 !important;">
                                <i class="bi bi-ticket-detailed-fill" style="font-size: 0.85rem;"></i>
                            </div>
                            <span class="fw-bold">🎫 PUSAT KENDALI TIKET & KENDALA INSTRUKTUR ({{ $ticket_stats['open'] }} Menunggu Respon)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($ticket_stats['urgent_high'] > 0)
                                <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold">
                                    <i class="bi bi-fire me-1"></i>{{ $ticket_stats['urgent_high'] }} Prioritas Mendesak
                                </span>
                            @endif
                            <a href="{{ route('tickets.index') }}" class="btn btn-sm text-white fw-semibold rounded-pill px-3 py-1 shadow-sm" style="font-size: 0.78rem; background: #4F46E5; border-color: #4F46E5;">
                                Kelola Semua Tiket <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Counter Grid -->
                    <div class="p-3 bg-light border-bottom">
                        <div class="row g-2 text-center">
                            <div class="col-6 col-md-3">
                                <div class="p-2 bg-white rounded-3 border shadow-sm">
                                    <div class="small text-muted" style="font-size: 0.72rem;">Tiket Menunggu Respon</div>
                                    <div class="fs-5 fw-bold text-danger">{{ $ticket_stats['open'] }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-2 bg-white rounded-3 border shadow-sm">
                                    <div class="small text-muted" style="font-size: 0.72rem;">Prioritas Urgent / High</div>
                                    <div class="fs-5 fw-bold text-warning">{{ $ticket_stats['urgent_high'] }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-2 bg-white rounded-3 border shadow-sm">
                                    <div class="small text-muted" style="font-size: 0.72rem;">Sedang Diproses</div>
                                    <div class="fs-5 fw-bold text-primary">{{ $ticket_stats['in_progress'] }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-2 bg-white rounded-3 border shadow-sm">
                                    <div class="small text-muted" style="font-size: 0.72rem;">Selesai Dijawab</div>
                                    <div class="fs-5 fw-bold text-success">{{ $ticket_stats['resolved'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($admin_actionable_tickets) && $admin_actionable_tickets->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($admin_actionable_tickets as $actTicket)
                            @php
                                $isUrgent = $actTicket->prioritas === 'urgent';
                                $isHigh = $actTicket->prioritas === 'high';
                            @endphp
                            <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 p-3" style="{{ $isUrgent ? 'background: #FEF2F2;' : '' }}">
                                <div class="w-100 w-md-auto">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <a href="{{ route('tickets.show', $actTicket->id) }}" class="font-monospace fw-bold text-decoration-none" style="font-size: 0.85rem; color: #4F46E5;">
                                            {{ $actTicket->ticket_number }}
                                        </a>
                                        @if($isUrgent)
                                            <span class="badge bg-danger text-white rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.68rem;">
                                                <i class="bi bi-fire me-1"></i>Urgent
                                            </span>
                                        @elseif($isHigh)
                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.68rem;">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>High
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Normal</span>
                                        @endif

                                        <span class="badge {{ $actTicket->kategori_badge }} rounded-pill" style="font-size: 0.68rem;">
                                            {{ $actTicket->kategori_label }}
                                        </span>

                                        @if($actTicket->has_unread_reply_for_admin)
                                            <span class="badge bg-info text-dark rounded-pill" style="font-size: 0.68rem;">
                                                <i class="bi bi-chat-dots-fill me-1"></i>Balasan Baru
                                            </span>
                                        @endif
                                        <small class="text-muted ms-auto ms-md-0" style="font-size: 0.72rem;">{{ $actTicket->created_at->diffForHumans() }}</small>
                                    </div>
                                    <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.90rem;">
                                        {{ $actTicket->judul }}
                                    </h6>
                                    <div class="text-muted small" style="font-size: 0.78rem;">
                                        <i class="bi bi-person-fill text-primary me-1"></i><strong>{{ $actTicket->user->nama_lengkap ?? 'Instruktur' }}</strong>
                                        @if($actTicket->session?->rombel?->ekstrakurikuler?->sekolah)
                                            <span class="mx-1">•</span>
                                            <i class="bi bi-building me-1"></i>{{ $actTicket->session->rombel->ekstrakurikuler->sekolah->namasekolah }}
                                        @endif
                                        <span class="mx-1">•</span>
                                        <span class="fst-italic text-secondary">"{{ Str::limit($actTicket->deskripsi, 80) }}"</span>
                                    </div>
                                </div>
                                <div class="w-100 w-md-auto text-end text-md-start d-flex gap-2 justify-content-end align-items-center">
                                    <a href="{{ route('tickets.show', $actTicket->id) }}" class="btn btn-sm text-white fw-bold rounded-pill px-3 py-1.5 shadow-sm w-100 w-md-auto" style="font-size: 0.78rem; background: #4F46E5; border-color: #4F46E5;">
                                        <i class="bi bi-chat-left-text-fill me-1"></i> Tanggapi Tiket
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif

                <!-- Admin To-Do List: Pending Reschedule / Sesi Libur -->
                @if(isset($pending_reschedule_sessions) && $pending_reschedule_sessions->count() > 0)
                <div class="dashboard-card mb-4" id="tour-admin-pending-reschedule" style="border-left: 6px solid #F59E0B !important;">
                    <div class="card-header bg-warning-subtle text-warning-emphasis fw-bold d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 p-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-range-fill text-warning fs-5"></i>
                            <span>📌 TO-DO LIST ADMIN: Antrean Reschedule ({{ $total_pending_reschedule ?? $pending_reschedule_sessions->count() }} Sesi Wajib Dijadwalkan Ulang)</span>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold">Wajib Reschedule</span>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($pending_reschedule_sessions as $pSession)
                            <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 p-3">
                                <div class="w-100 w-sm-auto">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h6 class="mb-0 fw-bold text-dark">
                                            {{ $pSession->rombel->ekstrakurikuler->sekolah->namasekolah ?? 'Sekolah' }}
                                        </h6>
                                        <span class="badge bg-secondary rounded-pill" style="font-size: 0.7rem;">P.{{ $pSession->nomor_pertemuan }}</span>
                                        @if($pSession->status === 'libur')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 0.7rem;">Libur Sekolah</span>
                                        @else
                                            <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.7rem;">Ditunda</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        <span class="fw-bold text-primary">{{ $pSession->rombel->ekstrakurikuler->kategori_program ?? 'Ekskul' }}</span>
                                        <span class="mx-1">•</span>
                                        {{ $pSession->rombel->nama_rombel ?? 'Rombel' }}
                                        <span class="mx-1">•</span>
                                        Instruktur: <strong>{{ $pSession->instruktur->nama_lengkap ?? 'Belum Ditugaskan' }}</strong>
                                        <br>
                                        <i class="bi bi-calendar-x me-1 text-danger"></i> Tanggal Asal: {{ $pSession->tanggal_terjadwal ? \Carbon\Carbon::parse($pSession->tanggal_terjadwal)->format('d M Y') : '-' }}
                                        @if($pSession->alasan_pembatalan || $pSession->catatan)
                                            <span class="mx-1">•</span>
                                            <span class="fst-italic text-dark">"{{ $pSession->alasan_pembatalan ?? $pSession->catatan }}"</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="w-100 w-sm-auto text-end text-sm-start d-flex gap-2 justify-content-end">
                                    <button type="button" 
                                            class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm w-100 w-sm-auto"
                                            onclick="openDashboardRescheduleModal({{ $pSession->id }}, '{{ addslashes($pSession->rombel->ekstrakurikuler->sekolah->namasekolah ?? 'Sekolah') }}', '{{ addslashes($pSession->rombel->nama_rombel ?? 'Rombel') }}', {{ $pSession->nomor_pertemuan }}, '{{ $pSession->tanggal_terjadwal ? $pSession->tanggal_terjadwal->format('Y-m-d') : '' }}')">
                                        <i class="bi bi-calendar2-range me-1"></i> Reschedule Sekarang
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Admin Monitoring: Pending Reports -->
                @if(isset($admin_pending_reports) && $admin_pending_reports->count() > 0)
                <div class="dashboard-card mb-4" id="tour-admin-pending-reports" style="border-left: 6px solid #0EA5E9 !important;">
                    <div class="card-header bg-info-subtle text-info-emphasis fw-bold d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 p-3">
                        <span><i class="bi bi-clipboard-data me-2"></i>MONITORING: BELUM LAPOR ({{ $admin_pending_reports->count() }} Teratas)</span>
                        <small class="text-muted fst-italic">Urut Deadline</small>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($admin_pending_reports as $report)
                            <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 p-3">
                                <div class="w-100 w-sm-auto">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h6 class="mb-0 fw-bold text-dark">
                                            {{ $report->instruktur->nama_lengkap ?? 'Tanpa Instruktur' }}
                                        </h6>
                                        <span class="badge bg-secondary rounded-pill" style="font-size: 0.7rem;">P.{{ $report->nomor_pertemuan }}</span>
                                        @if($report->isPast())
                                            @php
                                                $waktuRef = $report->waktu_selesai_full ?? $report->tanggal_terjadwal;
                                            @endphp
                                            <span class="badge bg-danger" style="font-size: 0.7rem;">Terlambat {{ $waktuRef->diffForHumans() }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Hari Ini</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        <span class="fw-bold text-primary">{{ $report->rombel?->ekstrakurikuler?->kategori_program ?? $report->ekstrakurikuler?->kategori_program ?? 'Ekskul' }}</span>
                                        <span class="mx-1">•</span>
                                        {{ $report->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? $report->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah' }}
                                        <br>
                                        <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($report->tanggal_terjadwal)->format('d M Y') }}
                                        @if($report->jam_mulai_terjadwal && $report->jam_selesai_terjadwal)
                                            <span class="mx-1">•</span>
                                            <i class="bi bi-clock me-1"></i> {{ $report->jadwal_waktu }}
                                        @endif
                                    </div>
                                </div>
                                <div class="w-100 w-sm-auto text-end text-sm-start">
                                    @php
                                        $cleanInstrukturPhone = '';
                                        if (!empty($report->instruktur->no_telephone)) {
                                            $cleanInstrukturPhone = preg_replace('/[^0-9]/', '', $report->instruktur->no_telephone);
                                            if (str_starts_with($cleanInstrukturPhone, '0')) {
                                                $cleanInstrukturPhone = '62' . substr($cleanInstrukturPhone, 1);
                                            }
                                        }
                                    @endphp
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm w-100 w-sm-auto"
                                            onclick="openDashboardFonnteModal({{ $report->id }}, '{{ addslashes($report->instruktur->nama_lengkap ?? 'Instruktur') }}', '{{ $cleanInstrukturPhone }}', '{{ addslashes($report->rombel?->ekstrakurikuler?->kategori_program ?? $report->ekstrakurikuler?->kategori_program ?? '') }}', '{{ addslashes($report->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? $report->ekstrakurikuler?->sekolah?->namasekolah ?? '') }}', '{{ $report->tanggal_terjadwal ? $report->tanggal_terjadwal->format('d/m/Y') : '' }}')">
                                        <i class="bi bi-whatsapp me-1"></i> Ingatkan
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="dashboard-card mb-4" id="tour-admin-late-approval">
                    <div class="card-header bg-white py-3 border-bottom px-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-check me-2 text-primary"></i>Pusat Verifikasi</h5>
                            <span class="badge bg-primary rounded-pill px-3 py-1">Admin Area</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="{{ route('siswa.index', ['temp_nisn' => 1]) }}" class="card text-decoration-none border shadow-sm h-100 rounded-3 {{ $pending_students > 0 ? 'bg-white' : 'bg-light' }}">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning position-relative">
                                            <i class="bi bi-person-exclamation fs-4"></i>
                                            @if($pending_students > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-white p-1">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">Siswa Perlu NISN</h6>
                                            <small class="text-muted">Siswa ditambah manual (TMP)</small>
                                        </div>
                                        <div class="ms-auto">
                                            <span class="badge bg-{{ $pending_students > 0 ? 'danger' : 'secondary' }} rounded-pill">{{ $pending_students }} Pending</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('users.index', ['role' => 'instruktur', 'status' => 'pending']) }}" class="card text-decoration-none border shadow-sm h-100 rounded-3 {{ $pending_instruktur > 0 ? 'bg-white' : 'bg-light' }}">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info position-relative">
                                            <i class="bi bi-person-badge fs-4"></i>
                                            @if($pending_instruktur > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-white p-1">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">Instruktur Baru</h6>
                                            <small class="text-muted">Menunggu verifikasi profil</small>
                                        </div>
                                        <div class="ms-auto">
                                            <span class="badge bg-{{ $pending_instruktur > 0 ? 'danger' : 'secondary' }} rounded-pill">{{ $pending_instruktur }} Pending</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning QC Panel -->
                <div class="dashboard-card mb-4" id="tour-admin-urgent-sessions">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-shield-fill-exclamation text-danger me-2"></i>Log Warning Quality Control
                        </h5>
                        <span class="badge bg-danger rounded-pill px-3 py-1">{{ $warning_merah + $warning_kuning }} Aktif</span>
                    </div>
                    <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                        @if(isset($warning_list) && $warning_list->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($warning_list as $warning)
                                    @php
                                        $typeLabel = match($warning->warning_type) {
                                            'no_instructor' => 'Tanpa Instruktur (H-1)',
                                            'not_confirmed' => 'Belum Ada Konfirmasi Sesi',
                                            'missing_report' => 'Laporan Mengajar Belum Diisi (>24h)',
                                            'low_attendance' => 'Kehadiran Siswa Rendah (<70%)',
                                            'reschedule_limit' => 'Frekuensi Reschedule Tinggi',
                                            'behind_target' => 'Tertinggal Target Kurikulum',
                                            default => ucwords(str_replace('_', ' ', $warning->warning_type))
                                        };

                                        $sekolahNama = null;
                                        $rombelNama = null;
                                        $actionUrl = null;
                                        $actionText = null;

                                        if ($warning->sourceable instanceof \App\Models\EkstrakurikulerSession) {
                                            $session = $warning->sourceable;
                                            $sekolahNama = $session->rombel?->ekstrakurikuler?->sekolah?->namasekolah;
                                            $rombelNama = $session->rombel?->nama_rombel;
                                            
                                            if (in_array($warning->warning_type, ['not_confirmed', 'missing_report'])) {
                                                $actionUrl = route('ekstrakurikuler.sessions.report.create', $session->id);
                                                $actionText = 'Isi Laporan Mengajar';
                                            } elseif ($warning->warning_type === 'no_instructor') {
                                                $actionUrl = route('ekstrakurikuler.sessions.show', $session->id);
                                                $actionText = 'Tugaskan Instruktur';
                                            }
                                        } elseif ($warning->sourceable instanceof \App\Models\EkstrakurikulerRombel) {
                                            $rombel = $warning->sourceable;
                                            $sekolahNama = $rombel->ekstrakurikuler?->sekolah?->namasekolah;
                                            $rombelNama = $rombel->nama_rombel;
                                            $actionUrl = route('ekstrakurikuler.sessions.index', ['rombel_id' => $rombel->id]);
                                            $actionText = 'Kelola Jadwal Rombel';
                                        }
                                    @endphp
                                    <div class="list-group-item p-3 border-bottom" style="border-left: 4px solid {{ $warning->severity === 'red' ? '#f43f5e' : '#f59e0b' }} !important; background-color: {{ $warning->severity === 'red' ? 'rgba(244, 63, 94, 0.03)' : 'rgba(245, 158, 11, 0.03)' }};">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                @if($warning->severity === 'red')
                                                    <i class="bi bi-x-circle-fill text-danger fs-6"></i>
                                                @else
                                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-6"></i>
                                                @endif
                                                <span class="badge bg-{{ $warning->severity === 'red' ? 'danger' : 'warning text-dark' }} text-uppercase fw-bold" style="font-size: 0.7rem;">
                                                    {{ $typeLabel }}
                                                </span>
                                                @if($sekolahNama)
                                                    <span class="badge bg-white text-dark border shadow-sm" style="font-size: 0.725rem;">
                                                        <i class="bi bi-building text-primary me-1"></i> {{ $sekolahNama }}
                                                    </span>
                                                @endif
                                                @if($rombelNama)
                                                    <span class="badge bg-white text-dark border shadow-sm" style="font-size: 0.725rem;">
                                                        <i class="bi bi-people text-info me-1"></i> {{ $rombelNama }}
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-muted flex-shrink-0"><i class="bi bi-clock me-1"></i>{{ $warning->created_at->diffForHumans() }}</small>
                                        </div>

                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                            <p class="mb-0 text-dark small fw-medium text-break" style="line-height: 1.5;">{{ $warning->notes }}</p>
                                            <div class="flex-shrink-0 d-flex align-items-center gap-2 flex-wrap justify-content-start justify-content-md-end">
                                                @if($actionUrl)
                                                    <a href="{{ $actionUrl }}" class="btn btn-xs btn-primary py-1 px-3 rounded-pill fw-bold" style="font-size: 0.75rem; whitespace: nowrap;">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i> {{ $actionText }}
                                                    </a>
                                                @endif
                                                <form action="{{ route('admin.warnings.resolve', $warning->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-outline-success py-1 px-3 rounded-pill" style="font-size: 0.75rem; whitespace: nowrap;">
                                                        <i class="bi bi-check2 me-1"></i> Resolve
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-5 text-center text-muted">
                                <i class="bi bi-shield-check text-success fs-1 mb-3 d-block"></i>
                                <h6 class="fw-bold mb-1 text-dark">Semua Sistem Berjalan Normal</h6>
                                <p class="mb-0 small text-secondary">Tidak ada peringatan QC aktif saat ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Live Activities Feed -->
            <div class="dashboard-card mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="spinner-grow text-success spinner-grow-sm" role="status"></div>
                        <h5 class="mb-0 fw-bold text-dark">Live Activity</h5>
                    </div>
                    <small class="text-muted">Real-time update</small>
                </div>
                <div class="card-body p-0">
                    @if(isset($recent_activities) && $recent_activities->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recent_activities as $activity)
                        <div class="list-group-item border-bottom px-4 py-3 hover-bg-light">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle {{ $activity['bg'] }} d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="{{ $activity['icon'] }} {{ $activity['color'] }} fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-1 gap-1">
                                        <h6 class="mb-0 fw-bold text-dark">{{ $activity['title'] }}</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                                        </small>
                                    </div>
                                    <p class="mb-1 text-muted small text-truncate" style="max-width: 100%;">
                                        {{ $activity['desc'] }}
                                    </p>
                                    <a href="{{ $activity['link'] }}" class="text-decoration-none text-primary small fw-semibold">
                                        Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-activity fs-1 text-muted opacity-25"></i>
                        </div>
                        <h6 class="text-muted">Belum ada aktivitas terbaru</h6>
                        <small class="text-secondary">Aktivitas mengajar dan program akan muncul di sini.</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right/Sidebar Column -->
        <div class="col-lg-4 col-12">
            @if(Auth::user()->role === 'instruktur')
                <!-- Instructor To-Do List (Urgent Reports) - Wajib Dilaporkan -->
                @if(isset($instructor_todo_list) && $instructor_todo_list->count() > 0)
                <div class="dashboard-card mb-4" style="border-left: 6px solid #F59E0B !important;">
                    <div class="card-header bg-warning-subtle text-warning-emphasis fw-bold d-flex justify-content-between align-items-center p-3">
                        <span class="d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill text-warning me-2 fs-5"></i>WAJIB DILAPORKAN ({{ $instructor_todo_list->count() }})</span>
                    </div>
                    <div class="card-body p-0 todo-scrollable" style="max-height: 450px; overflow-y: auto;">
                        <div class="list-group list-group-flush">
                            @foreach($instructor_todo_list as $todo)
                                <div class="list-group-item p-3 border-bottom hover-bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 75%;" title="{{ $todo->rombel?->ekstrakurikuler?->kategori_program ?? $todo->ekstrakurikuler?->kategori_program ?? 'Ekstrakurikuler' }}">
                                            {{ $todo->rombel?->ekstrakurikuler?->kategori_program ?? $todo->ekstrakurikuler?->kategori_program ?? 'Ekstrakurikuler' }}
                                        </h6>
                                        <span class="badge bg-secondary rounded-pill" style="font-size: 0.7rem;">P.{{ $todo->nomor_pertemuan }}</span>
                                    </div>
                                    <div class="text-muted small mb-3">
                                        <div class="mb-1 text-truncate" title="{{ $todo->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? $todo->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah' }}">
                                            <i class="bi bi-building me-1"></i> {{ $todo->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? $todo->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah' }}
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                            <span>
                                                <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($todo->tanggal_terjadwal)->format('d M Y') }}
                                                @if($todo->jam_mulai_terjadwal && $todo->jam_selesai_terjadwal)
                                                    <span class="mx-1">•</span>
                                                    <i class="bi bi-clock me-1"></i> {{ $todo->jadwal_waktu }}
                                                @endif
                                            </span>
                                            @if($todo->isPast())
                                                @php
                                                    $waktuRefTodo = $todo->waktu_selesai_full ?? $todo->tanggal_terjadwal;
                                                @endphp
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle" style="font-size: 0.65rem;">
                                                    Terlambat {{ $waktuRefTodo->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle" style="font-size: 0.65rem;">
                                                    Hari Ini
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('ekstrakurikuler.sessions.report.create', $todo->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm w-100 d-flex align-items-center justify-content-center">
                                        Buat Laporan <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Emergency Helpdesk / Bantuan Darurat -->
                <div class="dashboard-card mb-4" style="background: #F0FDF4; border: 1px solid #BBF7D0;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center">
                            <i class="bi bi-chat-dots-fill text-success me-2 fs-5"></i> Bantuan & Kontak Darurat
                        </h6>
                        <p class="text-muted small mb-3">
                            Mengalami kendala saat mengajar atau butuh bantuan admin akademik Erlass? Hubungi kami langsung.
                        </p>
                        @php
                            $waAdminPhone = '6282114631380';
                            $waAdminText = urlencode("Halo Admin Akademik Erlass, saya " . Auth::user()->nama_lengkap . " (Instruktur). Saya butuh bantuan terkait operasional mengajar.");
                        @endphp
                        <a href="https://wa.me/{{ $waAdminPhone }}?text={{ $waAdminText }}" target="_blank" rel="noopener" class="btn btn-success w-100 rounded-pill d-flex align-items-center justify-content-center gap-2 shadow-sm fw-bold">
                            <i class="bi bi-whatsapp"></i> Chat Admin Akademik
                        </a>
                    </div>
                </div>
            @else
                <!-- Charts Partial (Admin Only) -->
                @include('dashboard.partials.charts')

                <!-- School Distribution (Admin/Webmaster only) -->
                @if(auth()->user()?->hasAdminAccess())
                <div class="dashboard-card mb-4">
                    <div class="card-header bg-white border-bottom px-4 py-3">
                        <h5 class="mb-0 fw-bold text-dark">Distribusi Siswa</h5>
                    </div>
                    <div class="card-body p-4">
                        @if(isset($sekolah_distribution) && $sekolah_distribution->count() > 0)
                        <div class="mb-4 d-flex flex-column gap-3">
                            @foreach($sekolah_distribution as $sekolah)
                            <div>
                                <div class="d-flex justify-content-between mb-1 align-items-end">
                                    <span class="fw-medium text-dark small">{{ Str::limit($sekolah->namasekolah, 25) }}</span>
                                    <span class="fw-bold text-primary small">{{ $sekolah->siswa_count }}</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar rounded-pill"
                                        role="progressbar"
                                        style="width: {{ ($sekolah->siswa_count / max(1, $total_siswa)) * 100 }}%; 
                                                    background-color: #2563EB;"
                                        aria-valuenow="{{ ($sekolah->siswa_count / max(1, $total_siswa)) * 100 }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-2">
                            <a href="{{ route('sekolah.distribusi') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Lihat Semua Sekolah <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="bi bi-info-circle fs-1 text-muted opacity-50"></i>
                            <p class="text-muted mt-2">Data sekolah tidak tersedia</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Activity Chart (30 Hari Terakhir)
        @if(isset($chart_labels) && isset($chart_values))
        const elActivity = document.getElementById('activityChart');
        if (elActivity) {
            const ctx = elActivity.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.25)');
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chart_labels) !!},
                    datasets: [{
                        label: 'Laporan Masuk',
                        data: {!! json_encode($chart_values) !!},
                        borderColor: '#2563EB',
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#2563EB'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5],
                                drawBorder: false
                            },
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
        @endif

        // 2. Attendance Chart (6 Bulan Terakhir)
        @if(isset($attendanceLabels) && isset($attendanceValues))
        const elAttendance = document.getElementById('attendanceChart');
        if (elAttendance) {
            const ctxAttendance = elAttendance.getContext('2d');
            new Chart(ctxAttendance, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($attendanceLabels) !!},
                    datasets: [{
                        label: 'Kehadiran (%)',
                        data: {!! json_encode($attendanceValues) !!},
                        backgroundColor: '#10B981',
                        borderRadius: 6,
                        barPercentage: 0.55
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                borderDash: [5, 5],
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
        @endif
    });
</script>

@include('dashboard.partials.modal-reminder')
@include('dashboard.partials.modal-reschedule')

<script>
function openDashboardRescheduleModal(sessionId, sekolahName, rombelName, meetingNo, currentDate) {
    document.getElementById('dashRescheduleSessionId').value = sessionId;
    document.getElementById('dashRescheduleTitle').textContent = sekolahName + ' • ' + rombelName;
    document.getElementById('dashRescheduleSubtitle').textContent = 'Pertemuan Ke-' + meetingNo + ' (Jadwal Asal: ' + (currentDate || '-') + ')';
    document.getElementById('dashRescheduleNewDate').value = '';
    document.getElementById('dashRescheduleReason').value = '';
    document.getElementById('dashRescheduleCascade').checked = false;

    const modal = new bootstrap.Modal(document.getElementById('dashRescheduleModal'));
    modal.show();
}

function submitDashboardReschedule(e) {
    e.preventDefault();
    const sessionId = document.getElementById('dashRescheduleSessionId').value;
    const newDate = document.getElementById('dashRescheduleNewDate').value;
    const reason = document.getElementById('dashRescheduleReason').value;
    const cascade = document.getElementById('dashRescheduleCascade').checked;
    const btn = document.getElementById('btnDashSubmitReschedule');
    const spin = document.getElementById('dashSpinReschedule');

    btn.disabled = true;
    spin.classList.remove('d-none');

    fetch(`/ekstrakurikuler/sessions/${sessionId}/reschedule`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            tanggal_pengganti: newDate,
            alasan: reason,
            cascade_shift: cascade
        })
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        spin.classList.add('d-none');
        if (res.success) {
            alert('✅ ' + res.message);
            location.reload();
        } else {
            alert('⚠️ Gagal: ' + (res.message || 'Terjadi kesalahan'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        spin.classList.add('d-none');
        alert('❌ Error: ' + err.message);
    });
}

function openDashboardFonnteModal(sessionId, instrukturName, phone, programName, sekolahName, tanggalText) {
    document.getElementById('dashSessionId').value = sessionId;
    document.getElementById('dashCleanPhone').value = phone;
    document.getElementById('dashInstrukturName').textContent = 'Instruktur: ' + instrukturName;
    document.getElementById('dashSessionInfo').textContent = programName + ' • ' + sekolahName + ' (' + tanggalText + ')';
    document.getElementById('dashCustomMessage').value = '';

    const sessionLink = `${window.location.origin}/ekstrakurikuler/sessions/${sessionId}`;
    const waMsgText = encodeURIComponent(`Halo ${instrukturName}, mohon segera mengunggah laporan sesi ${programName} di ${sekolahName} tanggal ${tanggalText}.\n\nLink Sesi: ${sessionLink}`);
    const btnManual = document.getElementById('btnDashManualWA');
    if (phone) {
        btnManual.href = `https://wa.me/${phone}?text=${waMsgText}`;
        btnManual.classList.remove('disabled');
    } else {
        btnManual.href = 'javascript:void(0)';
        btnManual.classList.add('disabled');
    }

    const modal = new bootstrap.Modal(document.getElementById('dashboardReminderModal'));
    modal.show();
}

function sendDashboardFonnteReminder(target) {
    const sessionId = document.getElementById('dashSessionId').value;
    const customMessage = document.getElementById('dashCustomMessage').value;
    const btnSend = document.getElementById('btnDashSendFonnte');
    const btnAdmin = document.getElementById('btnDashTestAdmin');
    const spinner = document.getElementById('dashSpinFonnte');

    btnSend.disabled = true;
    btnAdmin.disabled = true;
    spinner.classList.remove('d-none');

    fetch(`/ekstrakurikuler/sessions/${sessionId}/remind`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            target: target,
            custom_message: customMessage
        })
    })
    .then(r => r.json())
    .then(res => {
        btnSend.disabled = false;
        btnAdmin.disabled = false;
        spinner.classList.add('d-none');

        if (res.success) {
            alert('✅ ' + res.message);
            bootstrap.Modal.getInstance(document.getElementById('dashboardReminderModal')).hide();
        } else {
            alert('⚠️ Gagal: ' + (res.message || 'Terjadi kesalahan sistem'));
        }
    })
    .catch(err => {
        btnSend.disabled = false;
        btnAdmin.disabled = false;
        spinner.classList.add('d-none');
        alert('❌ Error: ' + err.message);
    });
}
</script>
@endpush