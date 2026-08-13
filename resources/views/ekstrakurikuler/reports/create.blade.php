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
    }
    .student-row.new-student {
        background: rgba(59, 130, 246, 0.05);
        border-left: 3px solid var(--imp-blue);
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
                            <div class="attendance-counter">
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
                            <button type="button" class="btn btn-sm fw-bold px-3 py-1 rounded-pill shadow-sm" style="background: var(--imp-green); color: white; border: none;" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa
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
                        @foreach($siswaList as $index => $siswa)
                        <div class="student-row" data-student-name="{{ strtolower($siswa->nama_lengkap) }}">
                            <span class="student-num">{{ $index + 1 }}</span>
                            <div class="student-avatar {{ $siswa->jenis_kelamin == 'L' ? 'avatar-male' : ($siswa->jenis_kelamin == 'P' ? 'avatar-female' : 'avatar-default') }}">
                                {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                            </div>
                            <div class="student-name">{{ $siswa->nama_lengkap }}</div>
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
                        </div>
                        @endforeach
                        
                        @if($siswaList->isEmpty())
                        <div class="text-center py-5" style="color: var(--imp-slate);">
                            <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                            <span class="fw-semibold">Belum ada siswa di rombel ini.</span><br>
                            <small>Klik "Tambah Siswa" untuk menambahkan siswa baru.</small>
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

{{-- ═══ Modal Tambah Siswa ═══ --}}
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: var(--imp-radius); border: none; box-shadow: var(--imp-shadow-lg);">
            <div class="modal-header" style="border-bottom: 1px solid var(--imp-border);">
                <h5 class="modal-title fw-bold" style="color: var(--imp-navy);">
                    <i class="bi bi-person-plus-fill me-2" style="color: var(--imp-green);"></i>Tambah Siswa ke Daftar Hadir
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <ul class="nav nav-tabs mb-3" id="studentTabs" role="tablist">
                         <li class="nav-item">
                            <button class="nav-link active fw-semibold" id="search-tab" data-bs-toggle="tab" data-bs-target="#search-pane" type="button">
                                <i class="bi bi-search me-1"></i> Cari Siswa
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" id="create-tab" data-bs-toggle="tab" data-bs-target="#create-pane" type="button">
                                <i class="bi bi-plus-circle me-1"></i> Buat Baru
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="search-pane" role="tabpanel">
                            <label class="form-label fw-semibold">Cari Nama Siswa</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="studentSearchInput" placeholder="Ketik minimal 3 huruf..." style="border-radius: 10px 0 0 10px;">
                                <button class="btn btn-outline-primary" type="button" id="btnSearchStudent" style="border-radius: 0 10px 10px 0;">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="create-pane" role="tabpanel">
                            <div class="alert alert-warning py-2 small rounded-3">
                                <i class="bi bi-exclamation-circle me-1"></i> Data siswa baru akan dicatat dan diverifikasi oleh admin.
                            </div>
                            <form id="quickAddStudentForm">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="newStudentName" required style="border-radius: 10px;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-select" id="newStudentGender" required style="border-radius: 10px;">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="newStudentClass" placeholder="Contoh: 7A, 8B, X-1" required style="border-radius: 10px;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">No. WA Orang Tua <span class="text-muted small">(Opsional)</span></label>
                                    <input type="text" class="form-control" id="newStudentPhone" placeholder="08xxxx (opsional)" maxlength="20" style="border-radius: 10px;">
                                    <div class="form-text" style="font-size: 0.7rem;">Opsional — Digunakan untuk pengiriman notifikasi.</div>
                                </div>
                                <input type="hidden" id="schoolKodlan" value="{{ $session->rombel->ekstrakurikuler->sekolah_kodlan }}">
                                <input type="hidden" id="rombelId" value="{{ $session->ekstrakurikuler_rombel_id }}">
                                <button type="submit" class="btn w-100 fw-bold rounded-3" style="background: var(--imp-blue); color: white;">
                                    <i class="bi bi-person-plus me-1"></i> Simpan & Tambahkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="list-group" id="studentSearchResults">
                    <div class="text-center py-3" style="color: var(--imp-slate);" id="searchPlaceholder">
                        Silakan cari siswa...
                    </div>
                </div>
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
            
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const maxSize = parseInt(this.dataset.maxSize || 10485760);
                    
                    if (file.size > maxSize) {
                        alert(`File terlalu besar. Maksimal ${Math.round(maxSize/1048576)}MB.`);
                        this.value = '';
                        previewDiv.style.display = 'none';
                        return;
                    }
                    
                    previewDiv.style.display = 'block';
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            previewDiv.innerHTML = `
                                <div class="d-flex align-items-center gap-3">
                                    <img src="${e.target.result}" alt="Preview">
                                    <div>
                                        <div class="fw-semibold small" style="color: var(--imp-navy);">${file.name}</div>
                                        <div class="file-info">${(file.size / 1024).toFixed(1)} KB</div>
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
            const allRadios = document.querySelectorAll('.att-radio:checked');
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
                const rect = section.getBoundingClientRect();
                if (rect.top < window.innerHeight * 0.5) {
                    activeIdx = i;
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
        
        btnConfirm.addEventListener('click', function() {
            // Build summary
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
        
        btnFinalSubmit.addEventListener('click', function() {
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
            btnConfirm.classList.add('loading');
            btnConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
            
            document.getElementById('reportForm').submit();
        });

        // ═══ Add Student Modal Logic ═══
        const studentSearchInput = document.getElementById('studentSearchInput');
        const searchBtn = document.getElementById('btnSearchStudent');
        const resultsContainer = document.getElementById('studentSearchResults');
        const studentListContainer = document.getElementById('studentListContainer');

        function performSearch() {
            const query = studentSearchInput.value.trim();
            if (query.length < 3) {
                resultsContainer.innerHTML = '<div class="text-center p-3 small" style="color: var(--imp-amber);"><i class="bi bi-exclamation-circle me-1"></i> Ketik minimal 3 huruf</div>';
                return;
            }

            resultsContainer.innerHTML = '<div class="text-center p-3"><div class="spinner-border text-primary" role="status"></div></div>';

            fetch(`{{ route('api.ekstrakurikuler.search-student') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(res => {
                    resultsContainer.innerHTML = '';
                    if (!res.success || res.data.length === 0) {
                        resultsContainer.innerHTML = '<div class="text-center p-3 small" style="color: var(--imp-slate);">Tidak ditemukan siswa</div>';
                        return;
                    }

                    res.data.forEach(student => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                        item.innerHTML = `
                            <div>
                                <div class="fw-bold">${student.nama_lengkap}</div>
                                <small style="color: var(--imp-slate);">${student.sekolah_nama || '-'} | ${student.rombel || '-'}</small>
                            </div>
                            <span class="badge rounded-pill px-3 py-1" style="background: var(--imp-green); color: white;"><i class="bi bi-plus"></i> Tambah</span>
                        `;
                        item.onclick = () => addStudentParam(student);
                        resultsContainer.appendChild(item);
                    });
                })
                .catch(err => {
                    console.error(err);
                    resultsContainer.innerHTML = '<div class="text-center p-3 small text-danger">Error fetching data</div>';
                });
        }

        searchBtn.addEventListener('click', performSearch);
        studentSearchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); performSearch(); }
        });

        function addStudentParam(student) {
            // Check if already in list
            if (document.getElementById(`hadir_${student.id}`)) {
                alert('Siswa sudah ada di daftar.');
                return;
            }

            // Remove empty placeholder
            const emptyPlaceholder = studentListContainer.querySelector('.text-center.py-5');
            if (emptyPlaceholder) emptyPlaceholder.remove();

            const rowCount = studentListContainer.querySelectorAll('.student-row').length + 1;
            const initial = student.nama_lengkap ? student.nama_lengkap.charAt(0).toUpperCase() : '?';
            const gender = student.jenis_kelamin;
            const avatarClass = gender === 'L' ? 'avatar-male' : (gender === 'P' ? 'avatar-female' : 'avatar-default');
            
            const div = document.createElement('div');
            div.className = 'student-row new-student';
            div.dataset.studentName = student.nama_lengkap.toLowerCase();
            div.innerHTML = `
                <span class="student-num">${rowCount}</span>
                <div class="student-avatar ${avatarClass}">${initial}</div>
                <div class="student-name">
                    ${student.nama_lengkap}
                    <div class="small" style="color: var(--imp-blue); font-weight: 500; font-size: 0.72rem;">
                        <i class="bi bi-plus-circle me-1"></i>Ditambahkan manual
                    </div>
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
            `;

            studentListContainer.appendChild(div);
            updateAttendanceCounters();
            
            // Close modal
            const modalEl = document.getElementById('addStudentModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            // Clear search
            studentSearchInput.value = '';
            resultsContainer.innerHTML = '';
        }

        // Quick Add Student Logic
        const quickAddForm = document.getElementById('quickAddStudentForm');
        if (quickAddForm) {
            quickAddForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const name = document.getElementById('newStudentName').value;
                const gender = document.getElementById('newStudentGender').value;
                const studentClass = document.getElementById('newStudentClass').value;
                const kodlan = document.getElementById('schoolKodlan').value;
                const btn = quickAddForm.querySelector('button[type="submit"]');
                
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
                
                fetch('{{ route('api.ekstrakurikuler.store-quick-student') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nama_lengkap: name,
                        jenis_kelamin: gender,
                        kelas: studentClass,
                        no_hp_orangtua: document.getElementById('newStudentPhone').value,
                        sekolah_kodlan: kodlan,
                        ekstrakurikuler_rombel_id: document.getElementById('rombelId') ? document.getElementById('rombelId').value : null
                    })
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        addStudentParam(res.data);
                        document.getElementById('newStudentName').value = '';
                        document.getElementById('newStudentClass').value = '';
                        document.getElementById('newStudentPhone').value = '';
                        alert('Siswa berhasil ditambahkan dan langsung terdaftar dalam Rombel & Program Ekstrakurikuler ini.');
                    } else {
                        alert('Gagal: ' + (res.message || 'Unknown error'));
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
    });
</script>
@endpush
@endsection
