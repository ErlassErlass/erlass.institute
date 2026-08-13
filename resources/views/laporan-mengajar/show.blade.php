@extends('layouts.app')

@section('title', 'Detail Laporan: Pertemuan Ke-' . $laporanMengajar->pertemuan_ke)

@push('styles')
{{-- Fancybox untuk galeri foto --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
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

    /* ── Hero Section ──────────────────────────────────────── */
    .report-hero {
        background: linear-gradient(135deg, var(--imp-navy) 0%, #1E3A5F 50%, var(--imp-blue-dark) 100%);
        border-radius: var(--imp-radius);
        padding: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: var(--imp-shadow-lg);
    }
    .report-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.25), transparent 70%);
        pointer-events: none;
    }
    .report-hero::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -10%;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.15), transparent 70%);
        pointer-events: none;
    }
    .report-hero .d-flex {
        position: relative;
        z-index: 1;
    }
    .report-hero h1 {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 0.35rem;
        color: #FFFFFF;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
    }
    .report-hero .hero-subtitle {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 600;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
    }
    .hero-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 20px;
        padding: 0.35rem 0.9rem;
        font-size: 0.8rem;
        color: #FFFFFF;
        font-weight: 600;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }
    .btn-hero-action {
        background: rgba(255, 255, 255, 0.18);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 10px;
        backdrop-filter: blur(8px);
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.25s ease;
    }
    .btn-hero-action:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        transform: translateY(-1px);
    }

    /* ── Impeccable Stat Cards ─────────────────────────────── */
    .impeccable-stat-card {
        background: var(--imp-surface);
        border: 1px solid var(--imp-border);
        border-radius: 14px;
        padding: 1.15rem;
        box-shadow: var(--imp-shadow);
        position: relative;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .impeccable-stat-card:hover {
        transform: translateY(-2px);
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
    .accent-green { background: linear-gradient(90deg, #10B981, #059669); }
    .accent-amber { background: linear-gradient(90deg, #F59E0B, #D97706); }
    .accent-purple { background: linear-gradient(90deg, #8B5CF6, #6D28D9); }
    .accent-red { background: linear-gradient(90deg, #EF4444, #DC2626); }

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

    /* ── Cards & Section Wrappers ──────────────────────────── */
    .imp-card {
        background: var(--imp-surface);
        border: 1px solid var(--imp-border);
        border-radius: var(--imp-radius);
        box-shadow: var(--imp-shadow);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .imp-card-header {
        background: var(--imp-surface-alt);
        border-bottom: 1px solid var(--imp-border);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .imp-card-header h6 {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--imp-navy);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .imp-card-header h6 i {
        color: var(--imp-blue);
    }
    .imp-card-body {
        padding: 1.5rem;
    }

    /* ── Info Tile ─────────────────────────────────────────── */
    .info-tile {
        background: var(--imp-surface-alt);
        border: 1px solid var(--imp-border);
        border-radius: 12px;
        padding: 0.85rem 1rem;
        height: 100%;
    }
    .info-tile .tile-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--imp-slate);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.2rem;
    }
    .info-tile .tile-value {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--imp-navy);
    }

    /* ── Refleksi Callout ──────────────────────────────────── */
    .refleksi-callout {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.04), rgba(139, 92, 246, 0.03));
        border-left: 4px solid var(--imp-blue);
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
    }

    /* ── Image Hover Zoom ──────────────────────────────────── */
    .doc-img-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        border: 1px solid var(--imp-border);
        display: block;
    }
    .doc-img-wrapper img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.35s ease;
    }
    .doc-img-wrapper:hover img {
        transform: scale(1.04);
    }
    .doc-img-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .doc-img-wrapper:hover .doc-img-overlay {
        opacity: 1;
    }

    /* ── Progress Bar Presensi ─────────────────────────────── */
    .att-progress-bar {
        height: 10px;
        border-radius: 20px;
        background: var(--imp-border);
        overflow: hidden;
    }
    .att-progress-fill {
        height: 100%;
        border-radius: 20px;
        background: linear-gradient(90deg, #10B981, #059669);
        transition: width 0.6s ease;
    }

    @media (max-width: 768px) {
        .report-hero { padding: 1.25rem; }
        .report-hero h1 { font-size: 1.25rem; }
        .hero-meta-chip { font-size: 0.72rem; padding: 0.25rem 0.65rem; }
        .imp-card-body { padding: 1rem; }
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- ═══ HERO BANNER ═══ --}}
    <div class="report-hero">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1>
                    @if($isEkstrakurikuler ?? false)
                        <i class="bi bi-trophy-fill me-2" style="color: #FBBF24;"></i>Detail Laporan Ekstrakurikuler
                    @else
                        <i class="bi bi-journal-check me-2"></i>Detail Laporan Mengajar
                    @endif
                </h1>
                <p class="hero-subtitle mb-3">
                    {{ $laporanMengajar->sekolah->namasekolah ?? 'Sekolah tidak ditemukan' }}
                </p>
                <div class="d-flex flex-wrap gap-2">
                    @if($isEkstrakurikuler ?? false)
                        <span class="hero-meta-chip">
                            <i class="bi bi-trophy"></i>
                            {{ $ekstrakurikulerData['kategori_program'] ?? $ekstrakurikulerData['nama_program'] ?? 'Ekstrakurikuler' }}
                        </span>
                    @endif
                    <span class="hero-meta-chip">
                        <i class="bi bi-people-fill"></i>
                        {{ $laporanMengajar->rombel }}
                    </span>
                    <span class="hero-meta-chip">
                        <i class="bi bi-calendar3"></i>
                        {{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}
                    </span>
                    <span class="hero-meta-chip">
                        <i class="bi bi-clock"></i>
                        @php
                            $jMulai = \Carbon\Carbon::parse($laporanMengajar->jam_mulai);
                            $jSelesai = \Carbon\Carbon::parse($laporanMengajar->jam_selesai);
                            if ($jSelesai->lessThanOrEqualTo($jMulai)) {
                                $jSelesai = (clone $jMulai)->addMinutes(90);
                            }
                        @endphp
                        {{ $jMulai->format('H:i') }} - {{ $jSelesai->format('H:i') }} WIB
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-sm btn-hero-action px-3 py-2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
                @if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']) && $isEkstrakurikuler)
                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold px-3 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#relocateReportModal">
                    <i class="bi bi-arrow-left-right me-1"></i> Pindahkan Pertemuan
                </button>
                @endif
                @can('update', $laporanMengajar)
                <a href="{{ route('laporan-mengajar.edit', $laporanMengajar) }}" class="btn btn-sm btn-primary fw-bold px-3 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Edit Laporan
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- ═══ KPI STAT CARDS ═══ --}}
    @php
        $totalSiswa = ($laporanMengajar->jumlah_hadir + $laporanMengajar->jumlah_tidak_hadir);
        $percentHadir = $totalSiswa > 0 ? round(($laporanMengajar->jumlah_hadir / $totalSiswa) * 100) : 100;
        
        $tglJadwal = \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->startOfDay();
        $tglSubmit = $laporanMengajar->created_at ? $laporanMengajar->created_at->startOfDay() : now()->startOfDay();
        $selisihHari = (int) $tglJadwal->diffInDays($tglSubmit, false);
    @endphp

    <div class="row g-3 mb-4">
        {{-- Card 1: Pertemuan Ke- --}}
        <div class="col-6 col-md-4 col-xl-2.4 col-lg-3">
            <div class="impeccable-stat-card h-100">
                <div class="stat-accent-bar accent-blue"></div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-semibold text-secondary">Pertemuan Ke</span>
                    <div class="stat-icon-circle bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-list-ol"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-0">{{ $laporanMengajar->pertemuan_ke }}</div>
                <small class="text-muted">Sesi rutin</small>
            </div>
        </div>

        {{-- Card 2: Kehadiran Siswa --}}
        <div class="col-6 col-md-4 col-xl-2.4 col-lg-3">
            <div class="impeccable-stat-card h-100">
                <div class="stat-accent-bar accent-green"></div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-semibold text-secondary">Presensi Siswa</span>
                    <div class="stat-icon-circle bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-0">{{ $percentHadir }}%</div>
                <small class="text-success fw-semibold">{{ $laporanMengajar->jumlah_hadir }} Hadir / {{ $totalSiswa }} Siswa</small>
            </div>
        </div>

        {{-- Card 3: Check-in GPS --}}
        <div class="col-6 col-md-4 col-xl-2.4 col-lg-3">
            <div class="impeccable-stat-card h-100">
                <div class="stat-accent-bar accent-purple"></div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-semibold text-secondary">Check-in GPS</span>
                    <div class="stat-icon-circle bg-purple bg-opacity-10 text-purple" style="background: rgba(139, 92, 246, 0.1); color: var(--imp-purple);">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                </div>
                @php
                    $checkinStatus = $ekstrakurikulerSession->actual_checkin_status ?? 'on_time';
                    $checkinLabelShort = match($checkinStatus) {
                        'excellent' => 'Excellent',
                        'on_time' => 'Valid / On Time',
                        'warning' => 'Warning (<15m)',
                        'penalty' => 'Penalty (>15m)',
                        default => 'N/A',
                    };
                    $badgeColorClass = match($checkinStatus) {
                        'excellent', 'on_time' => 'text-success',
                        'warning' => 'text-warning',
                        'penalty' => 'text-danger',
                        default => 'text-secondary',
                    };
                @endphp
                <div class="fs-6 fw-bold {{ $badgeColorClass }} mb-0 mt-1">{{ $checkinLabelShort }}</div>
                <small class="text-muted">{{ $ekstrakurikulerSession->checkin_status_radius ?? 'Terverifikasi' }}</small>
            </div>
        </div>

        {{-- Card 4: Ketepatan Submit H+1 --}}
        <div class="col-6 col-md-4 col-xl-2.4 col-lg-3">
            <div class="impeccable-stat-card h-100">
                <div class="stat-accent-bar {{ $selisihHari <= 1 ? 'accent-green' : 'accent-red' }}"></div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-semibold text-secondary">Status Submit</span>
                    <div class="stat-icon-circle {{ $selisihHari <= 1 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                        <i class="bi {{ $selisihHari <= 1 ? 'bi-clock-check-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                    </div>
                </div>
                <div class="fs-6 fw-bold {{ $selisihHari <= 1 ? 'text-success' : 'text-danger' }} mb-0 mt-1">
                    {{ $selisihHari <= 1 ? 'Tepat Waktu' : 'Terlambat' }}
                </div>
                <small class="text-muted">Submit (H+{{ max(0, $selisihHari) }})</small>
            </div>
        </div>

        {{-- Card 5: File Project --}}
        <div class="col-6 col-md-4 col-xl-2.4 col-lg-3">
            <div class="impeccable-stat-card h-100">
                <div class="stat-accent-bar {{ $laporanMengajar->file_project ? 'accent-blue' : 'accent-amber' }}"></div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-semibold text-secondary">File Project</span>
                    <div class="stat-icon-circle bg-info bg-opacity-10 text-info">
                        <i class="bi bi-file-earmark-code-fill"></i>
                    </div>
                </div>
                <div class="fs-6 fw-bold text-dark mb-0 mt-1">
                    {{ $laporanMengajar->file_project ? 'Terlampir' : 'Tidak Ada' }}
                </div>
                <small class="text-muted">{{ $laporanMengajar->file_project ? 'Sudah Diunggah' : 'Kategori Non-Coding' }}</small>
            </div>
        </div>
    </div>

    {{-- ═══ MAIN CONTENT GRID ═══ --}}
    <div class="row">
        {{-- Left Column --}}
        <div class="col-lg-8">

            {{-- 1. Detail Laporan Card --}}
            <div class="imp-card">
                <div class="imp-card-header">
                    <h6><i class="bi bi-info-circle-fill"></i> Detail Informasi Laporan</h6>
                    <span class="badge px-3 py-1.5 rounded-pill fw-bold" style="background: var(--imp-blue); color: white;">
                        ID Laporan #{{ $laporanMengajar->id }}
                    </span>
                </div>
                <div class="imp-card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-6">
                            <div class="info-tile">
                                <div class="tile-label"><i class="bi bi-person-badge me-1 text-primary"></i> Instruktur Utama</div>
                                <div class="tile-value">{{ $laporanMengajar->instruktur->nama_lengkap ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6 col-6">
                            <div class="info-tile">
                                <div class="tile-label"><i class="bi bi-person-workspace me-1 text-primary"></i> Asisten Instruktur</div>
                                <div class="tile-value">{{ $laporanMengajar->asisten->nama_lengkap ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6 col-6">
                            <div class="info-tile">
                                <div class="tile-label"><i class="bi bi-building me-1 text-primary"></i> Sekolah</div>
                                <div class="tile-value">{{ $laporanMengajar->sekolah->namasekolah ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6 col-6">
                            <div class="info-tile">
                                <div class="tile-label"><i class="bi bi-people me-1 text-primary"></i> Kelompok Rombel</div>
                                <div class="tile-value">{{ $laporanMengajar->rombel }}</div>
                            </div>
                        </div>

                        <div class="col-md-6 col-6">
                            <div class="info-tile">
                                <div class="tile-label"><i class="bi bi-geo-alt me-1 text-primary"></i> Jarak & Transport</div>
                                <div class="tile-value">
                                    {{ $ekstrakurikulerSession->ekstrakurikuler->jarak_km ?? '0' }} Km
                                    <small class="text-muted fw-normal">(Rp {{ number_format($ekstrakurikulerSession->transport_fee ?? 30000, 0, ',', '.') }})</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-6">
                            <div class="info-tile">
                                <div class="tile-label"><i class="bi bi-cloud-arrow-up me-1 text-primary"></i> Waktu Submit</div>
                                <div class="tile-value text-primary">
                                    {{ $laporanMengajar->created_at ? $laporanMengajar->created_at->isoFormat('D MMM Y, HH:mm') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: var(--imp-border);">

                    {{-- Materi Pengajaran --}}
                    <div>
                        <h6 class="fw-bold mb-2" style="color: var(--imp-navy);">
                            <i class="bi bi-book me-2" style="color: var(--imp-blue);"></i>Materi Pengajaran
                        </h6>
                        <div class="p-3 rounded-3" style="background: var(--imp-surface-alt); border: 1px solid var(--imp-border); color: var(--imp-navy); font-weight: 500;">
                            {!! nl2br(e($laporanMengajar->materi_pengajaran)) !!}
                        </div>
                    </div>

                    @if(($isEkstrakurikuler ?? false) && $ekstrakurikulerSession && $ekstrakurikulerSession->catatan)
                        <div class="mt-3">
                            <h6 class="fw-bold mb-2" style="color: var(--imp-navy);">
                                <i class="bi bi-journal-text me-2" style="color: var(--imp-amber);"></i>Catatan Session
                            </h6>
                            <div class="p-3 rounded-3 fst-italic" style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.2); color: var(--imp-navy);">
                                {!! nl2br(e($ekstrakurikulerSession->catatan)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. Refleksi & Evaluasi Card --}}
            <div class="imp-card">
                <div class="imp-card-header">
                    <h6><i class="bi bi-clipboard-check-fill"></i> Refleksi & Evaluasi Kelas</h6>
                </div>
                <div class="imp-card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: var(--imp-surface-alt); border: 1px solid var(--imp-border);">
                                <small class="text-muted fw-semibold d-block mb-1">Keaktifan Kelas</small>
                                <span class="badge px-3 py-1.5 rounded-pill fw-bold text-capitalize" style="background: rgba(16, 185, 129, 0.12); color: var(--imp-green);">
                                    <i class="bi bi-lightning-charge-fill me-1"></i>{{ str_replace('_', ' ', $laporanMengajar->keaktifan ?? 'aktif') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: var(--imp-surface-alt); border: 1px solid var(--imp-border);">
                                <small class="text-muted fw-semibold d-block mb-1">Pemahaman Materi</small>
                                <span class="badge px-3 py-1.5 rounded-pill fw-bold text-capitalize" style="background: rgba(59, 130, 246, 0.12); color: var(--imp-blue);">
                                    <i class="bi bi-check-circle-fill me-1"></i>{{ str_replace('_', ' ', $laporanMengajar->pemahaman_materi ?? 'paham') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="refleksi-callout">
                        <h6 class="fw-bold mb-1" style="color: var(--imp-navy);"><i class="bi bi-chat-quote-fill me-2" style="color: var(--imp-blue);"></i>Refleksi Siswa</h6>
                        <p class="mb-0 small" style="color: var(--imp-slate);">{!! nl2br(e($laporanMengajar->refleksi_siswa ?? 'Siswa mengikuti kegiatan dengan antusias.')) !!}</p>
                    </div>

                    <div class="refleksi-callout" style="border-left-color: var(--imp-purple); background: linear-gradient(135deg, rgba(139, 92, 246, 0.04), rgba(59, 130, 246, 0.02));">
                        <h6 class="fw-bold mb-1" style="color: var(--imp-navy);"><i class="bi bi-trophy-fill me-2" style="color: var(--imp-purple);"></i>Capaian & Evaluasi</h6>
                        <p class="mb-0 small" style="color: var(--imp-slate);">{!! nl2br(e($laporanMengajar->refleksi_capaian ?? 'Materi tersampaikan dengan baik.')) !!}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">

            {{-- 1. Rekap Absensi Card --}}
            <div class="imp-card">
                <div class="imp-card-header">
                    <h6><i class="bi bi-pie-chart-fill"></i> Rekap Absensi</h6>
                    <div class="d-flex align-items-center gap-1">
                        @if(($isEkstrakurikuler ?? false) && $ekstrakurikulerSession)
                        <a href="{{ route('ekstrakurikuler-session.print-session', $ekstrakurikulerSession) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-2.5">
                            <i class="bi bi-printer"></i>
                        </a>
                        @endif
                        @can('update', $laporanMengajar)
                        <a href="{{ route('laporan-mengajar.absensi.create', $laporanMengajar) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="imp-card-body">
                    {{-- Progress Bar --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold" style="color: var(--imp-navy);">Tingkat Kehadiran</span>
                            <span class="small fw-bold text-success">{{ $percentHadir }}%</span>
                        </div>
                        <div class="att-progress-bar">
                            <div class="att-progress-fill" style="width: {{ $percentHadir }}%;"></div>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush border-0">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom" style="border-color: var(--imp-border);">
                            <span class="fw-semibold small" style="color: var(--imp-navy);"><i class="bi bi-check-circle-fill me-2 text-success"></i>Siswa Hadir</span>
                            <span class="badge px-3 py-1.5 rounded-pill fw-bold" style="background: rgba(16, 185, 129, 0.12); color: var(--imp-green);">
                                {{ $laporanMengajar->jumlah_hadir }} Siswa
                            </span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom" style="border-color: var(--imp-border);">
                            <span class="fw-semibold small" style="color: var(--imp-navy);"><i class="bi bi-x-circle-fill me-2 text-danger"></i>Tidak Hadir</span>
                            <span class="badge px-3 py-1.5 rounded-pill fw-bold" style="background: rgba(239, 68, 68, 0.12); color: var(--imp-red);">
                                {{ $laporanMengajar->jumlah_tidak_hadir }} Siswa
                            </span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold small" style="color: var(--imp-navy);"><i class="bi bi-dash-circle-fill me-2 text-secondary"></i>Siswa Keluar</span>
                            <span class="badge px-3 py-1.5 rounded-pill fw-bold" style="background: rgba(100, 116, 139, 0.12); color: var(--imp-slate);">
                                {{ $laporanMengajar->jumlah_siswa_keluar }} Siswa
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- 2. Dokumentasi & File Project Card --}}
            <div class="imp-card">
                <div class="imp-card-header">
                    <h6><i class="bi bi-camera-fill"></i> Berkas Dokumentasi</h6>
                </div>
                <div class="imp-card-body">
                    {{-- File Project Download --}}
                    @if($laporanMengajar->file_project)
                        <div class="p-3 rounded-3 mb-3 text-center" style="background: linear-gradient(135deg, rgba(59,130,246,0.06), rgba(139,92,246,0.04)); border: 1px solid rgba(59,130,246,0.2);">
                            <i class="bi bi-file-earmark-code-fill fs-2 d-block mb-1" style="color: var(--imp-blue);"></i>
                            <div class="fw-bold small mb-2" style="color: var(--imp-navy);">File Project Terlampir</div>
                            <a href="{{ asset('storage/' . $laporanMengajar->file_project) }}" class="btn btn-sm fw-bold px-4 py-2 rounded-pill shadow-sm" style="background: var(--imp-blue); color: white;" download target="_blank">
                                <i class="bi bi-download me-1"></i> Unduh File Project
                            </a>
                        </div>
                    @endif

                    {{-- Foto Kegiatan --}}
                    @if($laporanMengajar->foto_kegiatan)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small" style="color: var(--imp-navy);"><i class="bi bi-image me-1 text-primary"></i> Foto Kegiatan</span>
                            </div>
                            <a href="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" data-fancybox="gallery" class="doc-img-wrapper">
                                <img src="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" alt="Foto Kegiatan" loading="lazy">
                                <div class="doc-img-overlay">
                                    <i class="bi bi-zoom-in me-1"></i> Perbesar Foto
                                </div>
                            </a>
                        </div>
                    @endif

                    {{-- Foto Absensi Siswa --}}
                    @if($laporanMengajar->foto_absensi_siswa)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small" style="color: var(--imp-navy);"><i class="bi bi-card-checklist me-1 text-success"></i> Absensi Fisik (TTD)</span>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold" style="font-size: 0.68rem;">Wajib TTD PIC</span>
                            </div>
                            <a href="{{ asset('storage/' . $laporanMengajar->foto_absensi_siswa) }}" data-fancybox="gallery" class="doc-img-wrapper">
                                <img src="{{ asset('storage/' . $laporanMengajar->foto_absensi_siswa) }}" alt="Foto Absensi" loading="lazy">
                                <div class="doc-img-overlay">
                                    <i class="bi bi-zoom-in me-1"></i> Perbesar Foto
                                </div>
                            </a>
                        </div>
                    @endif

                    @if(!$laporanMengajar->foto_kegiatan && !$laporanMengajar->foto_absensi_siswa && !$laporanMengajar->file_project)
                        <div class="text-center py-4" style="color: var(--imp-slate);">
                            <i class="bi bi-camera-video-off fs-3 d-block mb-1"></i>
                            <span class="small fw-semibold">Tidak ada berkas dokumentasi.</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']) && $isEkstrakurikuler)
<div class="modal fade" id="relocateReportModal" tabindex="-1" aria-labelledby="relocateReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--imp-radius);">
            <div class="modal-header border-0 pb-0" style="padding: 1.5rem 1.5rem 0.5rem;">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center me-2" id="relocateReportModalLabel">
                    <i class="bi bi-arrow-left-right text-warning fs-4 me-2"></i> Relokasi Laporan Mengajar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('laporan-mengajar.relocate', $laporanMengajar) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 rounded-3 mb-3 text-dark small">
                        <i class="bi bi-info-circle-fill me-1 text-info"></i>
                        Laporan ini saat ini berada di <strong>Pertemuan Ke-{{ $laporanMengajar->pertemuan_ke }}</strong>. Memindahkan laporan akan meng-update status sesi target menjadi <strong>Selesai</strong> dan mengosongkan sesi asal.
                    </div>

                    <div class="mb-3">
                        <label for="target_session_id" class="form-label fw-semibold text-dark small">Pilih Pertemuan Target <span class="text-danger">*</span></label>
                        <select name="target_session_id" id="target_session_id" class="form-select rounded-3" required>
                            <option value="" disabled selected>-- Pilih Pertemuan --</option>
                            @foreach($availableSessions as $sess)
                                <option value="{{ $sess->id }}" {{ $sess->id == $laporanMengajar->ekstrakurikuler_session_id ? 'disabled' : '' }}>
                                    Pertemuan {{ $sess->nomor_pertemuan }} ({{ $sess->tanggal_terjadwal ? $sess->tanggal_terjadwal->format('d/m/Y') : '-' }}) 
                                    - Status: {{ ucfirst($sess->status) }}
                                    {{ $sess->id == $laporanMengajar->ekstrakurikuler_session_id ? ' [Sesi Saat Ini]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label for="alasan_relokasi" class="form-label fw-semibold text-dark small">Alasan Pemindahan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <textarea name="alasan_relokasi" id="alasan_relokasi" rows="2" class="form-control rounded-3" placeholder="Contoh: Instruktur salah mendelegasikan saat input laporan"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2 px-4 border-top">
                    <button type="button" class="btn btn-light rounded-3 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-3 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Konfirmasi Pindahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
{{-- Fancybox untuk galeri foto --}}
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    Fancybox.bind("[data-fancybox]", {
        // Opsi custom jika diperlukan
    });

    document.addEventListener('DOMContentLoaded', function() {
        const relocateModal = document.getElementById('relocateReportModal');
        if (relocateModal && relocateModal.parentNode !== document.body) {
            document.body.appendChild(relocateModal);
        }
    });
</script>
@endpush