@extends('layouts.app')

@section('title', 'Laporan & Absensi Sesi')

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

    /* ── Hero Section ──────────────────────────────────────── */
    .report-hero {
        background: linear-gradient(135deg, var(--imp-navy) 0%, #1E3A5F 50%, var(--imp-blue-dark) 100%);
        border-radius: var(--imp-radius);
        padding: 2rem 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
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
    .report-hero h1 {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 0.25rem;
        color: #FFFFFF;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        position: relative;
        z-index: 1;
    }
    .report-hero .hero-subtitle {
        font-size: 0.95rem;
        color: rgba(255,255,255,0.9);
        font-weight: 600;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 1;
    }
    .report-hero .d-flex {
        position: relative;
        z-index: 1;
    }
    .hero-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(255,255,255,0.18);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        padding: 0.35rem 0.9rem;
        font-size: 0.8rem;
        color: #FFFFFF;
        font-weight: 600;
        text-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    /* ── Stepper Progress ──────────────────────────────────── */
    .stepper-container {
        display: flex;
        justify-content: center;
        gap: 0;
        margin-bottom: 2rem;
        padding: 0 1rem;
    }
    .stepper-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
    }
    .stepper-item:not(:last-child)::after {
        content: '';
        width: 40px;
        height: 2px;
        background: var(--imp-border);
        margin: 0 0.5rem;
        transition: background 0.4s ease;
    }
    .stepper-item.completed:not(:last-child)::after {
        background: var(--imp-green);
    }
    .stepper-number {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        border: 2.5px solid var(--imp-border);
        color: var(--imp-slate);
        background: var(--imp-surface);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }
    .stepper-item.active .stepper-number {
        background: var(--imp-blue);
        border-color: var(--imp-blue);
        color: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }
    .stepper-item.completed .stepper-number {
        background: var(--imp-green);
        border-color: var(--imp-green);
        color: white;
    }
    .stepper-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--imp-slate);
        white-space: nowrap;
    }
    .stepper-item.active .stepper-label {
        color: var(--imp-blue);
    }
    .stepper-item.completed .stepper-label {
        color: var(--imp-green);
    }

    /* ── Cards Premium ─────────────────────────────────────── */
    .imp-card {
        background: var(--imp-surface);
        border: 1px solid var(--imp-border);
        border-radius: var(--imp-radius);
        box-shadow: var(--imp-shadow);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
        margin-bottom: 1.5rem;
    }
    .imp-card:hover {
        box-shadow: var(--imp-shadow-lg);
    }
    .imp-card-header {
        background: var(--imp-surface-alt);
        border-bottom: 1px solid var(--imp-border);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .imp-card-header h5 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--imp-navy);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .imp-card-header h5 i {
        color: var(--imp-blue);
    }
    .imp-card-body {
        padding: 1.5rem;
    }

    /* ── Upload Zone ────────────────────────────────────────── */
    .upload-zone {
        border: 2px dashed var(--imp-border);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: var(--imp-surface-alt);
        position: relative;
    }
    .upload-zone:hover,
    .upload-zone.dragover {
        border-color: var(--imp-blue);
        background: rgba(59, 130, 246, 0.04);
    }
    .upload-zone .upload-icon {
        font-size: 2rem;
        color: var(--imp-blue);
        margin-bottom: 0.5rem;
    }
    .upload-zone .upload-text {
        font-weight: 600;
        color: var(--imp-navy);
        font-size: 0.9rem;
    }
    .upload-zone .upload-subtext {
        font-size: 0.78rem;
        color: var(--imp-slate);
        margin-top: 0.25rem;
    }
    .upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .upload-preview {
        display: none;
        margin-top: 0.75rem;
        padding: 0.5rem;
        border-radius: 8px;
        background: white;
        border: 1px solid var(--imp-border);
    }
    .upload-preview img {
        max-height: 120px;
        border-radius: 6px;
        object-fit: cover;
    }
    .upload-preview .file-info {
        font-size: 0.78rem;
        color: var(--imp-slate);
    }

    /* ── Attendance Table Premium ───────────────────────────── */
    .attendance-counter {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .att-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.7rem;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .att-badge.hadir {
        background: rgba(16, 185, 129, 0.12);
        color: var(--imp-green);
    }
    .att-badge.absen {
        background: rgba(239, 68, 68, 0.12);
        color: var(--imp-red);
    }
    .att-badge.total-badge {
        background: rgba(59, 130, 246, 0.12);
        color: var(--imp-blue);
    }
    .student-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        color: white;
        flex-shrink: 0;
    }
    .avatar-male { background: linear-gradient(135deg, #3B82F6, #1E40AF); }
    .avatar-female { background: linear-gradient(135deg, #EC4899, #BE185D); }
    .avatar-default { background: linear-gradient(135deg, #8B5CF6, #6D28D9); }

    /* Premium Toggle Switch for attendance */
    .att-toggle-group {
        display: flex;
        gap: 0;
        border-radius: 10px;
        overflow: hidden;
        border: 1.5px solid var(--imp-border);
        background: var(--imp-surface-alt);
    }
    .att-toggle-group .btn-check:checked + .att-toggle-btn.hadir-btn {
        background: var(--imp-green);
        color: white;
        border-color: var(--imp-green);
    }
    .att-toggle-group .btn-check:checked + .att-toggle-btn.absen-btn {
        background: var(--imp-red);
        color: white;
        border-color: var(--imp-red);
    }
    .att-toggle-btn {
        padding: 0.45rem 0.85rem;
        font-size: 0.82rem;
        font-weight: 600;
        border: none;
        background: transparent;
        color: var(--imp-slate);
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .att-toggle-btn:hover {
        background: rgba(0,0,0,0.04);
    }

    /* Student row styling */
    .student-row {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid var(--imp-border);
        transition: background 0.2s ease;
        gap: 0.75rem;
    }
    .student-row:hover {
        background: var(--imp-surface-alt);
    }
    .student-row:last-child {
        border-bottom: none;
    }
    .student-row .student-num {
        font-size: 0.78rem;
        color: var(--imp-slate);
        font-weight: 600;
        min-width: 28px;
    }
    .student-row .student-name {
        font-weight: 600;
        color: var(--imp-navy);
        font-size: 0.9rem;
        flex: 1;
        min-width: 140px;
    }
    .student-row.new-student {
        background: rgba(59, 130, 246, 0.05);
        border-left: 3px solid var(--imp-blue);
    }
    .student-row.transferred-student {
        background: #f8fafc;
        opacity: 0.72;
        border-left: 3px solid #f59e0b;
    }
    .student-row.transferred-student:hover {
        opacity: 0.95;
        background: #f1f5f9;
    }

    /* Rombel tag in attendance table */
    .student-rombel-col {
        min-width: 120px;
    }
    .student-rombel-tag {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.28rem 0.65rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
    }
    .student-rombel-tag.current {
        background: rgba(37, 99, 235, 0.08);
        color: #2563eb;
        border: 1px solid rgba(37, 99, 235, 0.2);
    }
    .student-rombel-tag.transferred {
        background: rgba(217, 119, 6, 0.12);
        color: #b45309;
        border: 1px solid rgba(217, 119, 6, 0.28);
    }
    .student-action-col {
        min-width: 32px;
        text-align: right;
    }

    /* Search Bar in Attendance */
    .search-attendance {
        position: relative;
        margin-bottom: 0;
    }
    .search-attendance input {
        border: 1px solid var(--imp-border);
        border-radius: 10px;
        padding: 0.5rem 0.75rem 0.5rem 2.5rem;
        font-size: 0.85rem;
        width: 100%;
        background: var(--imp-surface);
    }
    .search-attendance input:focus {
        border-color: var(--imp-blue);
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        outline: none;
    }
    .search-attendance i {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--imp-slate);
    }

    /* ── Confirmation Modal ────────────────────────────────── */
    .confirm-summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 0;
        border-bottom: 1px solid var(--imp-border);
    }
    .confirm-summary-item:last-child { border-bottom: none; }
    .confirm-summary-item .label {
        color: var(--imp-slate);
        font-size: 0.85rem;
    }
    .confirm-summary-item .value {
        font-weight: 700;
        color: var(--imp-navy);
        font-size: 0.9rem;
    }

    /* ── Submit Button Premium ─────────────────────────────── */
    .btn-submit-premium {
        background: linear-gradient(135deg, var(--imp-blue) 0%, var(--imp-blue-dark) 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 0.85rem 2.5rem;
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: -0.01em;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.35);
        transition: all 0.3s ease;
    }
    .btn-submit-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.45);
        color: white;
    }
    .btn-submit-premium:active {
        transform: translateY(0);
    }
    .btn-submit-premium.loading {
        opacity: 0.7;
        pointer-events: none;
    }

    /* ── Previous Report Widget ────────────────────────────── */
    .prev-report-card {
        background: linear-gradient(135deg, rgba(59,130,246,0.06), rgba(139,92,246,0.04));
        border: 1px solid rgba(59,130,246,0.15);
        border-left: 4px solid var(--imp-blue);
        border-radius: var(--imp-radius);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    /* ── Mobile Responsive ─────────────────────────────────── */
    @media (max-width: 768px) {
        .report-hero { padding: 1.25rem; }
        .report-hero h1 { font-size: 1.2rem; }
        .hero-meta-chip { font-size: 0.72rem; padding: 0.2rem 0.6rem; }
        .stepper-container { flex-wrap: wrap; gap: 0.5rem; }
        .stepper-item:not(:last-child)::after { width: 20px; }
        .stepper-label { display: none; }
        .imp-card-body { padding: 1rem; }
        .student-row { flex-wrap: wrap; padding: 0.75rem 1rem; }
        .att-toggle-btn { padding: 0.4rem 0.65rem; font-size: 0.78rem; }
    }

    @media (max-width: 576px) {
        .stepper-item:not(:last-child)::after { width: 12px; margin: 0 0.25rem; }
        .stepper-number { width: 30px; height: 30px; font-size: 0.75rem; }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- ═══ Hero Section ═══ --}}
            <div class="report-hero">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <h1><i class="bi bi-file-earmark-check me-2"></i>Laporan & Absensi Sesi</h1>
                        <p class="hero-subtitle mb-3">
                            {{ $session->rombel->ekstrakurikuler->kategori_program }} — Pertemuan ke-{{ $session->nomor_pertemuan }}
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="hero-meta-chip">
                                <i class="bi bi-building"></i>
                                {{ $session->rombel->ekstrakurikuler->sekolah->nama ?? '-' }}
                            </span>
                            <span class="hero-meta-chip">
                                <i class="bi bi-people-fill"></i>
                                {{ $session->rombel->nama_rombel }}
                            </span>
                            <span class="hero-meta-chip">
                                <i class="bi bi-calendar3"></i>
                                {{ \Carbon\Carbon::parse($session->tanggal_terjadwal)->translatedFormat('d F Y') }}
                            </span>
                            <span class="hero-meta-chip">
                                <i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($session->jam_mulai_terjadwal)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->jam_selesai_terjadwal)->format('H:i') }}
                            </span>
                            @if(isset($isEndOfMonth) && $isEndOfMonth)
                                <span class="hero-meta-chip" style="background: rgba(239, 68, 68, 0.4); border-color: rgba(239, 68, 68, 0.6);">
                                    <i class="bi bi-exclamation-octagon-fill text-warning"></i> Akhir Bulan: Wajib Submit Hari H (23:59)
                                </span>
                            @elseif(isset($deadline))
                                <span class="hero-meta-chip">
                                    <i class="bi bi-hourglass-split"></i> Batas Submit: {{ \Carbon\Carbon::parse($deadline)->translatedFormat('d M, H:i') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('ekstrakurikuler.sessions.show', $session) }}" class="btn btn-sm px-3 py-2 fw-bold" style="background: rgba(255,255,255,0.15); color: white; border-radius: 10px; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            {{-- ═══ Stepper Progress ═══ --}}
            <div class="stepper-container" id="progressStepper">
                <div class="stepper-item active" data-step="1">
                    <span class="stepper-number">1</span>
                    <span class="stepper-label">Detail Kegiatan</span>
                </div>
                <div class="stepper-item" data-step="2">
                    <span class="stepper-number">2</span>
                    <span class="stepper-label">Absensi Siswa</span>
                </div>
                <div class="stepper-item" data-step="3">
                    <span class="stepper-number">3</span>
                    <span class="stepper-label">Evaluasi</span>
                </div>
                <div class="stepper-item" data-step="4">
                    <span class="stepper-number"><i class="bi bi-check-lg"></i></span>
                    <span class="stepper-label">Submit</span>
                </div>
            </div>

            <form action="{{ route('ekstrakurikuler.sessions.report.store', $session) }}" method="POST" enctype="multipart/form-data" id="reportForm">
                @csrf
                
                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert" style="border-left: 4px solid var(--imp-red);">
                        <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan pada input:</h6>
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                {{-- ═══ Previous Report Widget ═══ --}}
                @if(isset($previousReport) && $previousReport)
                <div class="prev-report-card">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: var(--imp-blue); font-size: 0.78rem;">
                                <i class="bi bi-journal-bookmark-fill me-1"></i> Laporan Sebelumnya
                            </span>
                            <span class="small fw-semibold" style="color: var(--imp-navy);">
                                Pertemuan Ke-{{ $previousReport->pertemuan_ke ?? '-' }} &bull; {{ \Carbon\Carbon::parse($previousReport->jadwal_mengajar)->translatedFormat('d F Y') }}
                            </span>
                        </div>
                        @if($previousReport->instruktur)
                            <small class="fw-semibold" style="color: var(--imp-slate);">
                                <i class="bi bi-person-badge me-1"></i> {{ $previousReport->instruktur->nama_lengkap }}
                            </small>
                        @endif
                    </div>
                    
                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <div class="small fw-semibold" style="color: var(--imp-navy);"><i class="bi bi-book me-1" style="color: var(--imp-blue);"></i> Materi Sebelumnya:</div>
                            <div class="small" style="color: var(--imp-slate);">{{ $previousReport->materi_pengajaran ?? $previousReport->topik_materi ?? '-' }}</div>
                        </div>
                        @if(!empty($previousReport->deskripsi_kegiatan))
                        <div class="col-md-6">
                            <div class="small fw-semibold" style="color: var(--imp-navy);"><i class="bi bi-card-text me-1" style="color: var(--imp-blue);"></i> Ringkasan:</div>
                            <div class="small" style="color: var(--imp-slate);">{{ $previousReport->deskripsi_kegiatan }}</div>
                        </div>
                        @endif
                        @if(!empty($previousReport->catatan))
                        <div class="col-12 mt-2 pt-2" style="border-top: 1px solid rgba(59,130,246,0.1);">
                            <div class="small fw-semibold" style="color: var(--imp-navy);"><i class="bi bi-chat-left-text me-1" style="color: var(--imp-blue);"></i> Catatan:</div>
                            <div class="small fst-italic" style="color: var(--imp-slate);">{{ $previousReport->catatan }}</div>
                        </div>
                        @endif
                        @if(!empty($previousReport->file_project))
                        <div class="col-12 mt-2 pt-2 d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-top: 1px solid rgba(59,130,246,0.1);">
                            <div class="small fw-semibold" style="color: var(--imp-navy);"><i class="bi bi-file-earmark-code me-1" style="color: var(--imp-blue);"></i> File Project:</div>
                            <a href="{{ asset('storage/' . $previousReport->file_project) }}" class="btn btn-sm fw-bold px-3 rounded-pill shadow-sm" style="background: var(--imp-blue); color: white;" download target="_blank">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                {{-- ═══ Section 1: Detail Kegiatan ═══ --}}
                <div class="imp-card" id="section-1">
                    <div class="imp-card-header">
                        <h5><i class="bi bi-journal-text"></i> 1. Detail Kegiatan</h5>
                    </div>
                    <div class="imp-card-body">
                        <div class="row g-3">
                            {{-- Topik Materi --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold" style="color: var(--imp-navy);">Topik Materi <span class="text-danger">*</span></label>
                                <select name="topik_materi" class="form-select select2 @error('topik_materi') is-invalid @enderror" required style="border-radius: 10px; border-color: var(--imp-border);">
                                    <option value="">Pilih Topik Materi</option>
                                    @php
                                        $currentMateri = old('topik_materi', $defaults['materi']);
                                        $isInList = $materiList->contains($currentMateri);
                                    @endphp
                                    
                                    @if($currentMateri && !$isInList)
                                        <option value="{{ $currentMateri }}" selected>{{ $currentMateri }}</option>
                                    @endif

                                    @foreach($materiList as $materi)
                                        <option value="{{ $materi }}" {{ $currentMateri == $materi ? 'selected' : '' }}>
                                            {{ $materi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('topik_materi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            {{-- Foto Kegiatan --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: var(--imp-navy);">Foto Kegiatan <span class="text-danger">*</span></label>
                                <div class="upload-zone" id="uploadFotoKegiatan">
                                    <div class="upload-icon"><i class="bi bi-camera"></i></div>
                                    <div class="upload-text">Klik atau seret foto kegiatan</div>
                                    <div class="upload-subtext">Format: JPG, PNG • Max: 5MB</div>
                                    <input type="file" name="foto_kegiatan" class="@error('foto_kegiatan') is-invalid @enderror" 
                                           accept="image/*" data-max-size="5242880" required>
                                    <div class="upload-preview" id="previewFotoKegiatan"></div>
                                </div>
                                @error('foto_kegiatan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- File Project --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: var(--imp-navy);">File Project <span class="text-danger">*</span></label>
                                <div class="upload-zone" id="uploadFileProject">
                                    <div class="upload-icon"><i class="bi bi-file-earmark-code"></i></div>
                                    <div class="upload-text">Klik atau seret file project</div>
                                    <div class="upload-subtext">.hex .sb3 .zip .rar .py .ino .pdf • Max: 10MB</div>
                                    <input type="file" name="file_project" class="@error('file_project') is-invalid @enderror" 
                                           accept=".hex,.sb3,.zip,.rar,.7z,.py,.ino,.cpp,.pdf,.png,.jpg,.jpeg" data-max-size="10485760" required>
                                    <div class="upload-preview" id="previewFileProject"></div>
                                </div>
                                @error('file_project')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="col-12">
                                <label class="form-label fw-bold" style="color: var(--imp-navy);">Deskripsi / Catatan Kegiatan</label>
                                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tuliskan deskripsi atau catatan kegiatan..." style="border-radius: 10px; border-color: var(--imp-border);">{{ old('deskripsi', $defaults['deskripsi']) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══ Section 2: Absensi Siswa ═══ --}}
                <div class="imp-card" id="section-2">
                    <div class="imp-card-header">
                        <h5><i class="bi bi-people-fill"></i> 2. Absensi Siswa</h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="attendance-counter me-md-1">
                                <span class="att-badge total-badge">
                                    <i class="bi bi-people"></i> Total: <span id="totalStudents">{{ $siswaList->count() }}</span>
                                </span>
                                <span class="att-badge hadir">
                                    <i class="bi bi-check-circle-fill"></i> H: <span id="hadirCount">{{ $siswaList->count() }}</span>
                                </span>
                                <span class="att-badge absen">
                                    <i class="bi bi-x-circle-fill"></i> A: <span id="absenCount">0</span>
                                </span>
                            </div>
                            <button type="button" class="btn btn-sm fw-bold px-3 py-1 rounded-pill shadow-sm" style="background: var(--imp-blue); color: white; border: none;" data-bs-toggle="modal" data-bs-target="#transferStudentModal">
                                <i class="bi bi-arrow-left-right me-1"></i> Ambil Siswa dari Rombel Lain
                            </button>
                            <button type="button" class="btn btn-sm fw-bold px-3 py-1 rounded-pill shadow-sm" style="background: var(--imp-green); color: white; border: none;" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa Baru Masuk
                            </button>
                        </div>
                    </div>
                    
                    {{-- Search Bar --}}
                    <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--imp-border);">
                        <div class="search-attendance">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchStudentInput" placeholder="Cari nama siswa...">
                        </div>
                    </div>

                    {{-- Student List --}}
                    <div id="studentListContainer">
                        {{-- 1. Active Students in this Rombel --}}
                        @foreach($siswaList as $index => $siswa)
                        <div class="student-row" id="student_row_{{ $siswa->id }}" data-student-id="{{ $siswa->id }}" data-student-name="{{ strtolower($siswa->nama_lengkap) }}">
                            <span class="student-num">{{ $index + 1 }}</span>
                            <div class="student-avatar {{ $siswa->jenis_kelamin == 'L' ? 'avatar-male' : ($siswa->jenis_kelamin == 'P' ? 'avatar-female' : 'avatar-default') }}">
                                {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                            </div>
                            <div class="student-name">
                                <div class="fw-bold text-dark">{{ $siswa->nama_lengkap }}</div>
                                <small class="text-muted">{{ $siswa->kelas ?? $siswa->rombel ?? 'Siswa' }}</small>
                            </div>
                            <div class="student-rombel-col">
                                <span class="student-rombel-tag current">
                                    <i class="bi bi-people-fill"></i> {{ $session->rombel->nama_rombel }}
                                </span>
                            </div>
                            <div class="att-toggle-group">
                                <input type="radio" class="btn-check att-radio" name="absensi[{{ $siswa->id }}]" id="hadir_{{ $siswa->id }}" value="1" checked required>
                                <label class="att-toggle-btn hadir-btn" for="hadir_{{ $siswa->id }}">
                                    <i class="bi bi-check-circle"></i> Hadir
                                </label>

                                <input type="radio" class="btn-check att-radio" name="absensi[{{ $siswa->id }}]" id="absen_{{ $siswa->id }}" value="0">
                                <label class="att-toggle-btn absen-btn" for="absen_{{ $siswa->id }}">
                                    <i class="bi bi-x-circle"></i> Absen
                                </label>
                            </div>
                            <div class="student-action-col">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill btn-withdraw-action px-2 py-0.5" 
                                        data-student-id="{{ $siswa->id }}" 
                                        data-student-name="{{ $siswa->nama_lengkap }}" 
                                        title="Keluarkan Siswa dari Rombel">
                                    <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline small">Keluar</span>
                                </button>
                            </div>
                        </div>
                        @endforeach
                        
                        {{-- 2. Transferred Students (Baris Abu-Abu) --}}
                        @if(isset($transferredStudents) && count($transferredStudents) > 0)
                            @foreach($transferredStudents as $tSiswa)
                            <div class="student-row transferred-student" id="student_row_{{ $tSiswa['id'] }}" data-student-id="{{ $tSiswa['id'] }}" data-student-name="{{ strtolower($tSiswa['nama_lengkap']) }}">
                                <span class="student-num"><i class="bi bi-arrow-right-short text-warning fs-5"></i></span>
                                <div class="student-avatar avatar-default" style="opacity: 0.6;">
                                    {{ strtoupper(substr($tSiswa['nama_lengkap'], 0, 1)) }}
                                </div>
                                <div class="student-name" style="opacity: 0.75;">
                                    <div class="fw-semibold text-secondary text-decoration-line-through">{{ $tSiswa['nama_lengkap'] }}</div>
                                    <small class="text-muted">{{ $tSiswa['kelas'] }}</small>
                                </div>
                                <div class="student-rombel-col">
                                    <span class="student-rombel-tag transferred" title="Pindah sejak {{ $tSiswa['tanggal_pindah'] ?? '-' }}">
                                        <i class="bi bi-arrow-right-circle-fill"></i> Pindah ke {{ $tSiswa['target_rombel_nama'] }}
                                    </span>
                                </div>
                                <div class="att-toggle-group" style="opacity: 0.7;">
                                    <span class="badge bg-light text-muted border py-1.5 px-2.5 rounded-pill" style="font-size: 0.75rem;">
                                        <i class="bi bi-slash-circle me-1"></i> Non-aktif di Sesi Ini
                                    </span>
                                </div>
                                <div class="student-action-col">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-restore-action px-2 py-0.5"
                                            data-student-id="{{ $tSiswa['id'] }}"
                                            data-student-name="{{ $tSiswa['nama_lengkap'] }}"
                                            data-target-rombel-id="{{ $session->ekstrakurikuler_rombel_id }}"
                                            data-from-rombel="{{ $tSiswa['target_rombel_nama'] }}"
                                            title="Tarik kembali siswa ke rombel ini">
                                        <i class="bi bi-arrow-counterclockwise"></i> <span class="d-none d-md-inline small">Tarik Kembali</span>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        @endif

                        @if($siswaList->isEmpty() && (!isset($transferredStudents) || count($transferredStudents) === 0))
                        <div class="text-center py-5" style="color: var(--imp-slate);" id="emptyStudentPlaceholder">
                            <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                            <span class="fw-semibold">Belum ada siswa di rombel ini.</span><br>
                            <small>Klik "Ambil Siswa dari Rombel Lain" atau "Tambah Siswa Baru Masuk".</small>
                        </div>
                        @endif
                    </div>

                    {{-- Foto Presensi --}}
                    <div style="padding: 1.25rem; border-top: 1px solid var(--imp-border);">
                        <label class="form-label fw-bold" style="color: var(--imp-navy);">
                            Foto Lembar Presensi (Wajib TTD) <span class="text-danger">*</span>
                        </label>
                        <div class="upload-zone" id="uploadPresensi">
                            <div class="upload-icon"><i class="bi bi-card-checklist"></i></div>
                            <div class="upload-text">Klik atau seret foto presensi fisik</div>
                            <div class="upload-subtext" style="color: var(--imp-red);">
                                <i class="bi bi-exclamation-circle me-1"></i>Wajib foto absensi fisik bertanda tangan PIC & Instruktur
                            </div>
                            <input type="file" name="foto_absensi_siswa" class="@error('foto_absensi_siswa') is-invalid @enderror" accept="image/*" data-max-size="5242880" required>
                            <div class="upload-preview" id="previewPresensi"></div>
                        </div>
                        @error('foto_absensi_siswa')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- ═══ Section 3: Evaluasi & Refleksi ═══ --}}
                <div class="imp-card" id="section-3">
                    <div class="imp-card-header">
                        <h5><i class="bi bi-clipboard-check"></i> 3. Evaluasi & Refleksi</h5>
                    </div>
                    <div class="imp-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: var(--imp-navy);">Keaktifan Kelas <span class="text-danger">*</span></label>
                                <select name="keaktifan" class="form-select" required style="border-radius: 10px; border-color: var(--imp-border);">
                                    <option value="aktif">Aktif</option>
                                    <option value="sangat_aktif">Sangat Aktif</option>
                                    <option value="pasif">Pasif</option>
                                    <option value="sangat_pasif">Sangat Pasif</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: var(--imp-navy);">Pemahaman Materi <span class="text-danger">*</span></label>
                                <select name="pemahaman_materi" class="form-select" required style="border-radius: 10px; border-color: var(--imp-border);">
                                    <option value="paham">Paham</option>
                                    <option value="sangat_paham">Sangat Paham</option>
                                    <option value="sedikit_paham">Sedikit Paham</option>
                                    <option value="belum_paham">Belum Paham</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold" style="color: var(--imp-navy);">Refleksi Siswa</label>
                                <textarea name="refleksi_siswa" class="form-control" rows="2" placeholder="Bagaimana respons siswa terhadap materi hari ini?" style="border-radius: 10px; border-color: var(--imp-border);"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold" style="color: var(--imp-navy);">Capaian & Evaluasi</label>
                                <textarea name="refleksi_capaian" class="form-control" rows="2" placeholder="Apa yang sudah dicapai dan perlu diperbaiki?" style="border-radius: 10px; border-color: var(--imp-border);"></textarea>
                            </div>
                            @if(isset($isSevereLate) && $isSevereLate)
                            <div class="col-12 mt-3">
                                <div class="p-3 rounded-3" style="background: #FEF2F2; border: 1.5px solid #FCA5A5;">
                                    <label class="form-label fw-bold text-danger d-flex align-items-center gap-1 mb-1">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Catatan Kendala Keterlambatan <span class="badge bg-danger text-white ms-1">Wajib Diisi</span>
                                    </label>
                                    <p class="small text-muted mb-2">
                                        Laporan sesi ini dibuat melewati batas toleransi (> 3 hari setelah tanggal jadwal atau melewati batas cutoff). Mohon jelaskan alasan/kendala keterlambatan pelaporan secara rinci (minimal 10 karakter).
                                    </p>
                                    <textarea name="alasan_kendala_keterlambatan" required minlength="10" class="form-control @error('alasan_kendala_keterlambatan') is-invalid @enderror" rows="3" placeholder="Contoh: Mengalami kendala koneksi internet saat upload foto dokumentasi dan konfirmasi PIC sekolah baru didapatkan kemarin..." style="border-radius: 10px; border-color: #F87171;">{{ old('alasan_kendala_keterlambatan') }}</textarea>
                                    @error('alasan_kendala_keterlambatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ═══ Submit Buttons ═══ --}}
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                    <button type="button" class="btn btn-light border me-md-2 px-4 py-2 rounded-3 fw-semibold" onclick="history.back()">Batal</button>
                    <button type="button" class="btn-submit-premium" id="btnConfirmSubmit">
                        <i class="bi bi-save me-2"></i> Simpan Laporan & Selesaikan Sesi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('modals')
{{-- ═══ Confirmation Modal ═══ --}}
<div class="modal fade" id="confirmSubmitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--imp-radius); border: none; box-shadow: var(--imp-shadow-lg);">
            <div class="modal-header border-0 pb-0" style="padding: 1.5rem 1.5rem 0.5rem;">
                <h5 class="modal-title fw-bold" style="color: var(--imp-navy);">
                    <i class="bi bi-check-circle-fill me-2" style="color: var(--imp-green);"></i>Konfirmasi Submit Laporan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 1rem 1.5rem;">
                <p class="small" style="color: var(--imp-slate);">Pastikan data berikut sudah benar sebelum menyimpan:</p>
                <div id="confirmSummaryContent">
                    {{-- Filled by JS --}}
                </div>
            </div>
            <div class="modal-footer border-0" style="padding: 0.75rem 1.5rem 1.5rem;">
                <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Periksa Lagi</button>
                <button type="button" class="btn px-4 rounded-3 fw-bold" style="background: var(--imp-green); color: white;" id="btnFinalSubmit">
                    <i class="bi bi-check-lg me-1"></i> Ya, Submit Laporan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Modal 1: Ambil Siswa dari Rombel Lain ═══ --}}
<div class="modal fade" id="transferStudentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: var(--imp-radius); border: none; box-shadow: var(--imp-shadow-lg);">
            <div class="modal-header" style="border-bottom: 1px solid var(--imp-border); padding: 1.25rem 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold" style="color: var(--imp-navy);">
                        <i class="bi bi-arrow-left-right me-2 text-primary"></i>Ambil Siswa dari Rombel Lain
                    </h5>
                    <small class="text-muted">Pindahkan siswa dari rombel paralel di program ekskul yang sama ke <strong>{{ $session->rombel->nama_rombel }}</strong>.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" id="parallelStudentSearchInput" placeholder="Cari nama siswa di rombel lain...">
                    </div>
                </div>

                <div id="parallelStudentsLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="small text-muted mt-2">Memuat daftar siswa rombel lain...</div>
                </div>

                <div id="parallelStudentsContainer" style="max-height: 380px; overflow-y: auto;">
                    {{-- Populated via AJAX --}}
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Modal 2: Tambah Siswa Baru Masuk ═══ --}}
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--imp-radius); border: none; box-shadow: var(--imp-shadow-lg);">
            <div class="modal-header" style="border-bottom: 1px solid var(--imp-border); padding: 1.25rem 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold" style="color: var(--imp-navy);">
                        <i class="bi bi-person-plus-fill me-2 text-success"></i>Tambah Siswa Baru Masuk
                    </h5>
                    <small class="text-muted">Pendaftaran siswa yang baru pertama kali bergabung ke <strong>{{ $session->rombel->nama_rombel }}</strong>.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="quickAddStudentForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="newStudentName" required placeholder="Contoh: Muhammad Rizky" style="border-radius: 10px;">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select" id="newStudentGender" required style="border-radius: 10px;">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Kelas Sekolah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newStudentClass" placeholder="Contoh: 7A / 8B" required style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">No. WhatsApp Orang Tua <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="text" class="form-control" id="newStudentPhone" placeholder="08xxxxxxxxxx" maxlength="20" style="border-radius: 10px;">
                        <div class="form-text" style="font-size: 0.72rem;">Digunakan untuk pengiriman notifikasi kemajuan & rapor belajar siswa.</div>
                    </div>
                    <input type="hidden" id="schoolKodlan" value="{{ $session->rombel->ekstrakurikuler->sekolah_kodlan }}">
                    <input type="hidden" id="rombelId" value="{{ $session->ekstrakurikuler_rombel_id }}">
                    <input type="hidden" id="ekskulId" value="{{ $session->rombel->ekstrakurikuler_id }}">
                    <button type="submit" class="btn w-100 fw-bold py-2.5 rounded-3" style="background: var(--imp-green); color: white;" id="btnSubmitNewStudent">
                        <i class="bi bi-person-check-fill me-1"></i> Simpan & Daftarkan ke Rombel Ini
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Modal 3: Keluarkan Siswa dari Rombel ═══ --}}
<div class="modal fade" id="withdrawStudentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--imp-radius); border: none; box-shadow: var(--imp-shadow-lg);">
            <div class="modal-header border-0 pb-0" style="padding: 1.5rem 1.5rem 0.5rem;">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i>Keluarkan Siswa dari Rombel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 1rem 1.5rem;">
                <p class="small text-muted mb-3">
                    Apakah Anda yakin ingin mengeluarkan siswa <strong id="withdrawStudentName" class="text-dark">Siswa</strong> dari rombel <strong>{{ $session->rombel->nama_rombel }}</strong>? Siswa tidak akan muncul di absensi sesi mendatang.
                </p>
                <form id="withdrawStudentForm">
                    <input type="hidden" id="withdrawStudentId" value="">
                    <input type="hidden" id="withdrawRombelId" value="{{ $session->ekstrakurikuler_rombel_id }}">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Alasan Keluar <span class="text-danger">*</span></label>
                        <select class="form-select mb-2" id="withdrawReasonSelect" required style="border-radius: 10px;">
                            <option value="Berhenti mengikuti ekstrakurikuler">Berhenti mengikuti ekstrakurikuler</option>
                            <option value="Pindah sekolah">Pindah sekolah</option>
                            <option value="Jadwal bentrok permanen">Jadwal bentrok permanen</option>
                            <option value="Lainnya">Lainnya (Tuliskan di bawah)</option>
                        </select>
                        <textarea class="form-control" id="withdrawReasonText" rows="2" placeholder="Catatan tambahan alasan keluar..." style="border-radius: 10px; font-size: 0.85rem;"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4 rounded-3 fw-bold" id="btnConfirmWithdraw">
                            <i class="bi bi-box-arrow-right me-1"></i> Ya, Keluarkan Siswa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const currentRombelId = "{{ $session->ekstrakurikuler_rombel_id }}";
        const currentRombelNama = "{{ $session->rombel->nama_rombel }}";
        const ekskulId = "{{ $session->rombel->ekstrakurikuler_id }}";

        // ═══ Client-Side Image Auto-Compression Engine ═══
        async function compressImageFile(file, maxWidth = 1600, maxHeight = 1600, quality = 0.82) {
            if (!file || !file.type.startsWith('image/')) return file;
            if (file.size <= 350 * 1024) return file; // Skip if already very small

            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (e) => {
                    const img = new Image();
                    img.src = e.target.result;
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        if (width > maxWidth || height > maxHeight) {
                            if (width > height) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            } else {
                                width = Math.round((width * maxHeight) / height);
                                height = maxHeight;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (!blob || blob.size >= file.size) {
                                resolve(file);
                                return;
                            }
                            const cleanName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                            const compressedFile = new File([blob], cleanName, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(compressedFile);
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = () => resolve(file);
                };
                reader.onerror = () => resolve(file);
            });
        }

        // ═══ Upload Zone Logic ═══
        document.querySelectorAll('.upload-zone').forEach(zone => {
            const input = zone.querySelector('input[type="file"]');
            const previewDiv = zone.querySelector('.upload-preview');
            
            zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('dragover'); });
            zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('dragover');
                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    input.dispatchEvent(new Event('change'));
                }
            });
            
            input.addEventListener('change', async function() {
                if (this.files && this.files[0]) {
                    let file = this.files[0];
                    const originalSize = file.size;
                    const maxSize = parseInt(this.dataset.maxSize || 10485760);
                    
                    if (originalSize > maxSize && !file.type.startsWith('image/')) {
                        alert(`File terlalu besar. Maksimal ${Math.round(maxSize/1048576)}MB.`);
                        this.value = '';
                        previewDiv.style.display = 'none';
                        return;
                    }
                    
                    previewDiv.style.display = 'block';
                    if (file.type.startsWith('image/')) {
                        previewDiv.innerHTML = `
                            <div class="d-flex align-items-center gap-2 p-2">
                                <span class="spinner-border spinner-border-sm text-primary"></span>
                                <span class="small text-muted">Mengoptimasi ukuran foto...</span>
                            </div>`;

                        file = await compressImageFile(file);

                        // Attach compressed file to input via DataTransfer if supported
                        try {
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            this.files = dt.files;
                        } catch (err) {
                            console.warn('DataTransfer fallback', err);
                        }

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const savedBadge = originalSize > file.size 
                                ? ` <span class="badge bg-success bg-opacity-10 text-success rounded-pill ms-1 fw-bold">Hemat ${Math.round((1 - file.size/originalSize)*100)}%</span>` 
                                : '';
                            previewDiv.innerHTML = `
                                <div class="d-flex align-items-center gap-3">
                                    <img src="${e.target.result}" alt="Preview" style="max-height: 100px; border-radius: 8px; object-fit: cover;">
                                    <div>
                                        <div class="fw-semibold small" style="color: var(--imp-navy);">${file.name}</div>
                                        <div class="file-info">${(file.size / 1024).toFixed(1)} KB ${savedBadge}</div>
                                    </div>
                                </div>`;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewDiv.innerHTML = `
                            <div class="d-flex align-items-center gap-2 p-2">
                                <i class="bi bi-file-earmark-check fs-4" style="color: var(--imp-green);"></i>
                                <div>
                                    <div class="fw-semibold small" style="color: var(--imp-navy);">${file.name}</div>
                                    <div class="file-info">${(file.size / 1024).toFixed(1)} KB</div>
                                </div>
                            </div>`;
                    }
                }
            });
        });

        // ═══ Attendance Counter Logic ═══
        const hadirCountEl = document.getElementById('hadirCount');
        const absenCountEl = document.getElementById('absenCount');
        const totalCountEl = document.getElementById('totalStudents');
        
        function updateAttendanceCounters() {
            const allRadios = document.querySelectorAll('.student-row:not(.transferred-student) .att-radio:checked');
            let hadir = 0, absen = 0;
            allRadios.forEach(r => {
                if (r.value === '1') hadir++;
                else absen++;
            });
            hadirCountEl.textContent = hadir;
            absenCountEl.textContent = absen;
            totalCountEl.textContent = hadir + absen;
        }
        
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('att-radio')) {
                updateAttendanceCounters();
            }
        });

        // ═══ Search Students in Attendance List ═══
        const searchInput = document.getElementById('searchStudentInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                document.querySelectorAll('.student-row').forEach(row => {
                    const name = row.dataset.studentName || '';
                    row.style.display = name.includes(query) ? '' : 'none';
                });
            });
        }

        // ═══ Stepper Progress Tracking ═══
        function updateStepper() {
            const steps = document.querySelectorAll('.stepper-item');
            const sections = [
                document.getElementById('section-1'),
                document.getElementById('section-2'),
                document.getElementById('section-3')
            ];
            
            let activeIdx = 0;
            sections.forEach((section, i) => {
                if (section) {
                    const rect = section.getBoundingClientRect();
                    if (rect.top < window.innerHeight * 0.5) {
                        activeIdx = i;
                    }
                }
            });
            
            steps.forEach((step, i) => {
                step.classList.remove('active', 'completed');
                if (i < activeIdx) step.classList.add('completed');
                else if (i === activeIdx) step.classList.add('active');
            });
        }
        
        window.addEventListener('scroll', updateStepper);
        updateStepper();

        // ═══ Confirmation Modal Logic ═══
        const btnConfirm = document.getElementById('btnConfirmSubmit');
        const btnFinalSubmit = document.getElementById('btnFinalSubmit');
        
        if (btnConfirm) {
            btnConfirm.addEventListener('click', function() {
                const topik = document.querySelector('[name="topik_materi"]');
                const topikText = topik ? (topik.options ? topik.options[topik.selectedIndex]?.text : topik.value) : '-';
                const keaktifan = document.querySelector('[name="keaktifan"]');
                const keaktifanText = keaktifan ? keaktifan.options[keaktifan.selectedIndex]?.text : '-';
                const pemahaman = document.querySelector('[name="pemahaman_materi"]');
                const pemahamanText = pemahaman ? pemahaman.options[pemahaman.selectedIndex]?.text : '-';
                
                const summaryHtml = `
                    <div class="confirm-summary-item">
                        <span class="label">Topik Materi</span>
                        <span class="value">${topikText}</span>
                    </div>
                    <div class="confirm-summary-item">
                        <span class="label">Siswa Hadir</span>
                        <span class="value" style="color: var(--imp-green);">${hadirCountEl.textContent} siswa</span>
                    </div>
                    <div class="confirm-summary-item">
                        <span class="label">Siswa Absen</span>
                        <span class="value" style="color: var(--imp-red);">${absenCountEl.textContent} siswa</span>
                    </div>
                    <div class="confirm-summary-item">
                        <span class="label">Keaktifan Kelas</span>
                        <span class="value">${keaktifanText}</span>
                    </div>
                    <div class="confirm-summary-item">
                        <span class="label">Pemahaman Materi</span>
                        <span class="value">${pemahamanText}</span>
                    </div>
                `;
                
                document.getElementById('confirmSummaryContent').innerHTML = summaryHtml;
                const modal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
                modal.show();
            });
        }
        
        if (btnFinalSubmit) {
            btnFinalSubmit.addEventListener('click', async function() {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengunggah Laporan...';
                if (btnConfirm) {
                    btnConfirm.classList.add('loading');
                    btnConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengunggah...';
                }

                // Final safety check: ensure all image inputs are compressed before submit
                const fileInputs = document.querySelectorAll('#reportForm input[type="file"]');
                for (const input of fileInputs) {
                    if (input.files && input.files[0] && input.files[0].type.startsWith('image/')) {
                        const compressed = await compressImageFile(input.files[0]);
                        try {
                            const dt = new DataTransfer();
                            dt.items.add(compressed);
                            input.files = dt.files;
                        } catch(e) {}
                    }
                }

                document.getElementById('reportForm').submit();
            });
        }

        // ═══ Quick Add Student (New Student) Logic ═══
        const quickAddForm = document.getElementById('quickAddStudentForm');
        if (quickAddForm) {
            quickAddForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const name = document.getElementById('newStudentName').value;
                const gender = document.getElementById('newStudentGender').value;
                const studentClass = document.getElementById('newStudentClass').value;
                const kodlan = document.getElementById('schoolKodlan').value;
                const btn = document.getElementById('btnSubmitNewStudent');
                
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mendaftarkan...';
                
                fetch('{{ route('api.ekstrakurikuler.store-quick-student') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        nama_lengkap: name,
                        jenis_kelamin: gender,
                        kelas: studentClass,
                        no_hp_orangtua: document.getElementById('newStudentPhone').value,
                        sekolah_kodlan: kodlan,
                        ekstrakurikuler_rombel_id: currentRombelId
                    })
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        renderNewStudentRow({
                            id: res.data.id,
                            nama_lengkap: res.data.nama_lengkap,
                            jenis_kelamin: gender,
                            kelas: studentClass,
                            rombel_nama: currentRombelNama
                        });

                        document.getElementById('newStudentName').value = '';
                        document.getElementById('newStudentClass').value = '';
                        document.getElementById('newStudentPhone').value = '';
                        
                        const modalEl = document.getElementById('addStudentModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    } else {
                        alert('Gagal: ' + (res.message || 'Terjadi kesalahan'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan sistem.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            });
        }

        // ═══ Parallel Students (Ambil dari Rombel Lain) Modal Logic ═══
        const transferModalEl = document.getElementById('transferStudentModal');
        const parallelLoading = document.getElementById('parallelStudentsLoading');
        const parallelContainer = document.getElementById('parallelStudentsContainer');
        const parallelSearchInput = document.getElementById('parallelStudentSearchInput');

        if (transferModalEl) {
            transferModalEl.addEventListener('show.bs.modal', function () {
                loadParallelStudents();
            });
        }

        function loadParallelStudents() {
            parallelLoading.style.display = 'block';
            parallelContainer.innerHTML = '';
            
            fetch(`{{ route('api.ekstrakurikuler.parallel-students') }}?ekstrakurikuler_id=${ekskulId}&current_rombel_id=${currentRombelId}`)
                .then(res => res.json())
                .then(res => {
                    parallelLoading.style.display = 'none';
                    if (!res.success || res.data.length === 0) {
                        parallelContainer.innerHTML = `
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle fs-4 d-block mb-1"></i>
                                Tidak ada rombel paralel lain di program ekstrakurikuler ini.
                            </div>`;
                        return;
                    }

                    let html = '';
                    let totalStudentsCount = 0;

                    res.data.forEach(rombel => {
                        totalStudentsCount += rombel.students.length;
                        html += `
                            <div class="card mb-3 border shadow-none rounded-3 parallel-rombel-block">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                                    <div class="fw-bold text-dark small">
                                        <i class="bi bi-people-fill text-primary me-1"></i> ${rombel.nama_rombel}
                                        <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill ms-1">${rombel.students.length} Siswa</span>
                                    </div>
                                    <small class="text-muted">${rombel.hari} • ${rombel.jam}</small>
                                </div>
                                <div class="list-group list-group-flush">
                        `;

                        if (rombel.students.length === 0) {
                            html += `<div class="p-3 text-center text-muted small">Tidak ada siswa aktif di rombel ini.</div>`;
                        } else {
                            rombel.students.forEach(st => {
                                const isAlreadyInCurrent = document.getElementById(`student_row_${st.siswa_id}`) && !document.getElementById(`student_row_${st.siswa_id}`).classList.contains('transferred-student');
                                const btnDisabled = isAlreadyInCurrent ? 'disabled' : '';
                                const btnText = isAlreadyInCurrent ? '<i class="bi bi-check2"></i> Sudah di Rombel Ini' : `<i class="bi bi-plus-lg me-1"></i> Pindahkan ke ${currentRombelNama}`;
                                const btnClass = isAlreadyInCurrent ? 'btn-light border text-muted' : 'btn-primary';

                                html += `
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 parallel-student-item" data-student-name="${st.nama_lengkap.toLowerCase()}">
                                        <div>
                                            <div class="fw-semibold text-dark">${st.nama_lengkap}</div>
                                            <small class="text-muted">Kelas: ${st.kelas} • Asal: ${st.source_rombel_nama}</small>
                                        </div>
                                        <button type="button" class="btn btn-sm ${btnClass} rounded-pill px-3 py-1 fw-bold btn-transfer-now" 
                                                data-student-id="${st.siswa_id}" 
                                                data-student-name="${st.nama_lengkap}"
                                                data-student-gender="${st.jenis_kelamin}"
                                                data-student-class="${st.kelas}"
                                                data-source-rombel="${st.source_rombel_nama}"
                                                ${btnDisabled}>
                                            ${btnText}
                                        </button>
                                    </div>
                                `;
                            });
                        }

                        html += `</div></div>`;
                    });

                    if (totalStudentsCount === 0) {
                        parallelContainer.innerHTML = `
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-people fs-4 d-block mb-1"></i>
                                Belum ada siswa yang terdaftar di rombel paralel lain.
                            </div>`;
                    } else {
                        parallelContainer.innerHTML = html;
                        attachTransferButtons();
                    }
                })
                .catch(err => {
                    console.error(err);
                    parallelLoading.style.display = 'none';
                    parallelContainer.innerHTML = '<div class="text-center text-danger py-4">Gagal memuat data rombel lain.</div>';
                });
        }

        // Live Search parallel students in modal
        if (parallelSearchInput) {
            parallelSearchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.parallel-student-item').forEach(item => {
                    const name = item.dataset.studentName || '';
                    item.style.display = name.includes(q) ? '' : 'none';
                });
            });
        }

        function attachTransferButtons() {
            document.querySelectorAll('.btn-transfer-now:not(:disabled)').forEach(btn => {
                btn.addEventListener('click', function() {
                    const siswaId = this.dataset.studentId;
                    const studentName = this.dataset.studentName;
                    const gender = this.dataset.studentGender;
                    const studentClass = this.dataset.studentClass;
                    const sourceRombel = this.dataset.sourceRombel;

                    const originalText = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memindahkan...';

                    fetch('{{ route('api.ekstrakurikuler.transfer-student') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            siswa_id: siswaId,
                            target_rombel_id: currentRombelId,
                            ekstrakurikuler_id: ekskulId,
                            alasan: `Pindahan dari ${sourceRombel}`
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            renderNewStudentRow({
                                id: siswaId,
                                nama_lengkap: studentName,
                                jenis_kelamin: gender,
                                kelas: studentClass,
                                rombel_nama: `${currentRombelNama} (Pindahan)`
                            });

                            const modal = bootstrap.Modal.getInstance(transferModalEl);
                            if (modal) modal.hide();
                        } else {
                            alert('Gagal: ' + (res.message || 'Terjadi kesalahan'));
                            this.disabled = false;
                            this.innerHTML = originalText;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Terjadi kesalahan sistem.');
                        this.disabled = false;
                        this.innerHTML = originalText;
                    });
                });
            });
        }

        // ═══ Restore Student Action (Tarik Kembali Siswa dari Baris Abu-Abu) ═══
        document.addEventListener('click', function(e) {
            const restoreBtn = e.target.closest('.btn-restore-action');
            if (restoreBtn) {
                const siswaId = restoreBtn.dataset.studentId;
                const studentName = restoreBtn.dataset.studentName;
                const fromRombel = restoreBtn.dataset.fromRombel;

                if (!confirm(`Tarik kembali ${studentName} dari ${fromRombel} ke ${currentRombelNama}?`)) {
                    return;
                }

                const originalHtml = restoreBtn.innerHTML;
                restoreBtn.disabled = true;
                restoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch('{{ route('api.ekstrakurikuler.transfer-student') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        siswa_id: siswaId,
                        target_rombel_id: currentRombelId,
                        ekstrakurikuler_id: ekskulId,
                        alasan: `Ditarik kembali ke ${currentRombelNama}`
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        const row = document.getElementById(`student_row_${siswaId}`);
                        if (row) {
                            row.classList.remove('transferred-student');
                            
                            // Restore Avatar
                            const avatar = row.querySelector('.student-avatar');
                            if (avatar) avatar.style.opacity = '1';

                            // Restore Name
                            const nameDiv = row.querySelector('.student-name');
                            if (nameDiv) {
                                nameDiv.style.opacity = '1';
                                const boldName = nameDiv.querySelector('.fw-semibold');
                                if (boldName) {
                                    boldName.className = 'fw-bold text-dark';
                                }
                            }

                            // Restore Rombel Tag
                            const rombelCol = row.querySelector('.student-rombel-col');
                            if (rombelCol) {
                                rombelCol.innerHTML = `
                                    <span class="student-rombel-tag current">
                                        <i class="bi bi-people-fill"></i> ${currentRombelNama}
                                    </span>
                                `;
                            }

                            // Restore Toggle Group
                            const toggleCol = row.querySelector('.att-toggle-group');
                            if (toggleCol) {
                                toggleCol.style.opacity = '1';
                                toggleCol.innerHTML = `
                                    <input type="radio" class="btn-check att-radio" name="absensi[${siswaId}]" id="hadir_${siswaId}" value="1" checked required>
                                    <label class="att-toggle-btn hadir-btn" for="hadir_${siswaId}">
                                        <i class="bi bi-check-circle"></i> Hadir
                                    </label>
                                    <input type="radio" class="btn-check att-radio" name="absensi[${siswaId}]" id="absen_${siswaId}" value="0">
                                    <label class="att-toggle-btn absen-btn" for="absen_${siswaId}">
                                        <i class="bi bi-x-circle"></i> Absen
                                    </label>
                                `;
                            }

                            // Restore Action Col
                            const actionCol = row.querySelector('.student-action-col');
                            if (actionCol) {
                                actionCol.innerHTML = `
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill btn-withdraw-action px-2 py-0.5" 
                                            data-student-id="${siswaId}" 
                                            data-student-name="${studentName}" 
                                            title="Keluarkan Siswa dari Rombel">
                                        <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline small">Keluar</span>
                                    </button>
                                `;
                            }

                            updateAttendanceCounters();
                        }
                    } else {
                        alert('Gagal menarik kembali: ' + (res.message || 'Terjadi kesalahan'));
                        restoreBtn.disabled = false;
                        restoreBtn.innerHTML = originalHtml;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan sistem.');
                    restoreBtn.disabled = false;
                    restoreBtn.innerHTML = originalHtml;
                });
            }
        });

        // ═══ Withdraw Student Action ═══
        const withdrawModalEl = document.getElementById('withdrawStudentModal');
        const withdrawStudentForm = document.getElementById('withdrawStudentForm');
        let activeWithdrawRow = null;

        document.addEventListener('click', function(e) {
            const withdrawBtn = e.target.closest('.btn-withdraw-action');
            if (withdrawBtn) {
                const siswaId = withdrawBtn.dataset.studentId;
                const studentName = withdrawBtn.dataset.studentName;

                document.getElementById('withdrawStudentId').value = siswaId;
                document.getElementById('withdrawStudentName').textContent = studentName;
                document.getElementById('withdrawReasonText').value = '';

                activeWithdrawRow = document.getElementById(`student_row_${siswaId}`);

                const modal = new bootstrap.Modal(withdrawModalEl);
                modal.show();
            }
        });

        if (withdrawStudentForm) {
            withdrawStudentForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const siswaId = document.getElementById('withdrawStudentId').value;
                const reasonSelect = document.getElementById('withdrawReasonSelect').value;
                const reasonText = document.getElementById('withdrawReasonText').value.trim();
                const combinedReason = reasonText ? `${reasonSelect}: ${reasonText}` : reasonSelect;

                const btn = document.getElementById('btnConfirmWithdraw');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';

                fetch('{{ route('api.ekstrakurikuler.withdraw-student') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        siswa_id: siswaId,
                        rombel_id: currentRombelId,
                        alasan_keluar: combinedReason
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        if (activeWithdrawRow) {
                            activeWithdrawRow.remove();
                        }
                        updateAttendanceCounters();

                        const modal = bootstrap.Modal.getInstance(withdrawModalEl);
                        if (modal) modal.hide();
                    } else {
                        alert('Gagal: ' + (res.message || 'Terjadi kesalahan'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan sistem.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            });
        }

        // ═══ Helper: Append New Active Student Row ═══
        function renderNewStudentRow(student) {
            const emptyPlaceholder = document.getElementById('emptyStudentPlaceholder');
            if (emptyPlaceholder) emptyPlaceholder.remove();

            // If already exists (e.g. was transferred), remove existing row first
            const existingRow = document.getElementById(`student_row_${student.id}`);
            if (existingRow) existingRow.remove();

            const studentListContainer = document.getElementById('studentListContainer');
            const rowCount = studentListContainer.querySelectorAll('.student-row').length + 1;
            const initial = student.nama_lengkap ? student.nama_lengkap.charAt(0).toUpperCase() : '?';
            const gender = student.jenis_kelamin;
            const avatarClass = gender === 'L' ? 'avatar-male' : (gender === 'P' ? 'avatar-female' : 'avatar-default');
            
            const div = document.createElement('div');
            div.className = 'student-row new-student';
            div.id = `student_row_${student.id}`;
            div.dataset.studentId = student.id;
            div.dataset.studentName = student.nama_lengkap.toLowerCase();
            div.innerHTML = `
                <span class="student-num">${rowCount}</span>
                <div class="student-avatar ${avatarClass}">${initial}</div>
                <div class="student-name">
                    <div class="fw-bold text-dark">${student.nama_lengkap}</div>
                    <small class="text-muted">${student.kelas || 'Siswa'}</small>
                </div>
                <div class="student-rombel-col">
                    <span class="student-rombel-tag current">
                        <i class="bi bi-people-fill"></i> ${student.rombel_nama || currentRombelNama}
                    </span>
                </div>
                <div class="att-toggle-group">
                    <input type="radio" class="btn-check att-radio" name="absensi[${student.id}]" id="hadir_${student.id}" value="1" checked required>
                    <label class="att-toggle-btn hadir-btn" for="hadir_${student.id}">
                        <i class="bi bi-check-circle"></i> Hadir
                    </label>
                    <input type="radio" class="btn-check att-radio" name="absensi[${student.id}]" id="absen_${student.id}" value="0">
                    <label class="att-toggle-btn absen-btn" for="absen_${student.id}">
                        <i class="bi bi-x-circle"></i> Absen
                    </label>
                </div>
                <div class="student-action-col">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill btn-withdraw-action px-2 py-0.5" 
                            data-student-id="${student.id}" 
                            data-student-name="${student.nama_lengkap}" 
                            title="Keluarkan Siswa dari Rombel">
                        <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline small">Keluar</span>
                    </button>
                </div>
            `;

            studentListContainer.prepend(div);
            updateAttendanceCounters();
        }
    });
</script>
@endpush
@endsection
