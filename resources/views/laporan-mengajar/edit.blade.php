@extends('layouts.app')

@section('title', 'Edit Laporan Mengajar #' . $laporanMengajar->id)

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
    .edit-hero {
        background: linear-gradient(135deg, var(--imp-navy) 0%, #1E3A5F 50%, var(--imp-blue-dark) 100%);
        border-radius: var(--imp-radius);
        padding: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.75rem;
        box-shadow: var(--imp-shadow);
    }
    .edit-hero::before {
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
    .edit-hero h1 {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 0.35rem;
        color: #FFFFFF;
    }
    .edit-hero .hero-subtitle {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
    }
    .hero-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        color: #FFFFFF;
        font-weight: 600;
    }

    /* ── Section Cards ─────────────────────────────────────── */
    .form-card {
        background: var(--imp-surface);
        border-radius: var(--imp-radius);
        border: 1px solid var(--imp-border);
        box-shadow: var(--imp-shadow);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        transition: box-shadow 0.2s ease;
    }
    .form-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--imp-navy);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--imp-border);
    }
    .form-card-title i {
        color: var(--imp-blue);
        font-size: 1.2rem;
    }

    /* ── Upload Box & Previews ─────────────────────────────── */
    .upload-zone {
        border: 2px dashed var(--imp-border);
        border-radius: 12px;
        padding: 1.5rem 1rem;
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
        font-size: 1.8rem;
        color: var(--imp-blue);
        margin-bottom: 0.4rem;
    }
    .upload-zone .upload-text {
        font-weight: 600;
        color: var(--imp-navy);
        font-size: 0.875rem;
    }
    .upload-zone .upload-subtext {
        font-size: 0.75rem;
        color: var(--imp-slate);
        margin-top: 0.2rem;
    }
    .upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .current-file-preview {
        background: #f8fafc;
        border: 1px solid var(--imp-border);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* ── Attendance Grid ───────────────────────────────────── */
    .attendance-counter {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .att-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .att-badge.hadir { background: rgba(16, 185, 129, 0.12); color: var(--imp-green); }
    .att-badge.absen { background: rgba(239, 68, 68, 0.12); color: var(--imp-red); }
    .att-badge.total-badge { background: rgba(59, 130, 246, 0.12); color: var(--imp-blue); }

    .student-row {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--imp-border);
        transition: background 0.2s ease;
        gap: 0.75rem;
    }
    .student-row:hover { background: var(--imp-surface-alt); }
    .student-row:last-child { border-bottom: none; }
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

    .att-toggle-group {
        display: flex;
        gap: 0;
        border-radius: 8px;
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
        padding: 0.4rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
        background: transparent;
        color: var(--imp-slate);
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* ── Option Chips (Rating) ─────────────────────────────── */
    .chip-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .chip-btn {
        border: 1.5px solid var(--imp-border);
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--imp-slate);
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-check:checked + .chip-btn {
        border-color: var(--imp-blue);
        background: rgba(59, 130, 246, 0.08);
        color: var(--imp-blue-dark);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">

            {{-- Hero Header --}}
            <div class="edit-hero">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="hero-meta-chip">
                                <i class="bi bi-journal-text"></i> Laporan #{{ $laporanMengajar->id }}
                            </span>
                            @if($session)
                            <span class="hero-meta-chip">
                                <i class="bi bi-calendar-event"></i> Sesi Sched #{{ $session->id }}
                            </span>
                            @else
                            <span class="hero-meta-chip">
                                <i class="bi bi-lightning-charge"></i> Sesi Ad-Hoc / Khusus
                            </span>
                            @endif
                        </div>
                        <h1>Edit Laporan Mengajar</h1>
                        <p class="hero-subtitle mb-0">
                            {{ $laporanMengajar->sekolah->namasekolah ?? $laporanMengajar->sekolah_nama ?? 'Sekolah Mitra' }} 
                            • {{ $laporanMengajar->kategori_pengajaran ?? 'Ekstrakurikuler' }}
                        </p>
                    </div>

                    <div class="d-flex flex-column gap-2 text-end">
                        <span class="hero-meta-chip">
                            <i class="bi bi-layers"></i> {{ $laporanMengajar->rombel }} (P.{{ $laporanMengajar->pertemuan_ke }})
                        </span>
                        <span class="hero-meta-chip">
                            <i class="bi bi-clock"></i> {{ $laporanMengajar->jam_mulai ? \Carbon\Carbon::parse($laporanMengajar->jam_mulai)->format('H:i') : '-' }} - {{ $laporanMengajar->jam_selesai ? \Carbon\Carbon::parse($laporanMengajar->jam_selesai)->format('H:i') : '-' }} WIB
                        </span>
                    </div>
                </div>
            </div>

            @if (isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan pada input form:</h6>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('laporan-mengajar.update', $laporanMengajar) }}" enctype="multipart/form-data" id="editLaporanForm">
                @csrf
                @method('PUT')

                {{-- CARD 1: Informasi Sesi & Lokasi --}}
                <div class="form-card">
                    <div class="form-card-title">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>1. Informasi Sesi &amp; Pengajar</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Instruktur Utama</label>
                            <input type="text" class="form-control bg-light" value="{{ $laporanMengajar->instruktur->nama_lengkap ?? 'Instruktur' }}" readonly disabled>
                            <input type="hidden" name="user_id_instruktur" value="{{ $laporanMengajar->user_id_instruktur }}">
                        </div>

                        <div class="col-md-6">
                            <label for="user_id_assisten" class="form-label text-muted small fw-semibold">Asisten Instruktur (Opsional)</label>
                            <select name="user_id_assisten" id="user_id_assisten" class="form-select @error('user_id_assisten') is-invalid @enderror">
                                <option value="">-- Tanpa Asisten --</option>
                                @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ old('user_id_assisten', $laporanMengajar->user_id_assisten) == $instructor->id ? 'selected' : '' }}>
                                    {{ $instructor->nama_lengkap }}
                                </option>
                                @endforeach
                            </select>
                            @error('user_id_assisten') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Sekolah Mitra</label>
                            <input type="text" class="form-control bg-light" value="{{ $laporanMengajar->sekolah->namasekolah ?? $laporanMengajar->sekolah_nama ?? 'Data Sekolah' }}" readonly disabled>
                            <input type="hidden" name="sekolah_kodlan" value="{{ $laporanMengajar->sekolah_kodlan }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Kategori Program</label>
                            <input type="text" name="kategori_pengajaran" class="form-control bg-light" value="{{ old('kategori_pengajaran', $laporanMengajar->kategori_pengajaran) }}" readonly>
                        </div>

                        <div class="col-md-3">
                            <label for="pertemuan_ke" class="form-label text-muted small fw-semibold">Pertemuan Ke- <span class="text-danger">*</span></label>
                            <input type="number" name="pertemuan_ke" id="pertemuan_ke" class="form-control @error('pertemuan_ke') is-invalid @enderror" value="{{ old('pertemuan_ke', $laporanMengajar->pertemuan_ke) }}" required min="0" max="100">
                            @error('pertemuan_ke') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="rombel" class="form-label text-muted small fw-semibold">Nama Rombel <span class="text-danger">*</span></label>
                            <input type="text" name="rombel" id="rombel" class="form-control @error('rombel') is-invalid @enderror" value="{{ old('rombel', $laporanMengajar->rombel) }}" required>
                            @error('rombel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jadwal_mengajar" class="form-label text-muted small fw-semibold">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                            <input type="date" name="jadwal_mengajar" id="jadwal_mengajar" class="form-control @error('jadwal_mengajar') is-invalid @enderror" value="{{ old('jadwal_mengajar', $laporanMengajar->jadwal_mengajar ? \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->format('Y-m-d') : date('Y-m-d')) }}" required>
                            @error('jadwal_mengajar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jam_mulai" class="form-label text-muted small fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" id="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai', $laporanMengajar->jam_mulai ? \Carbon\Carbon::parse($laporanMengajar->jam_mulai)->format('H:i') : '') }}" required>
                            @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jam_selesai" class="form-label text-muted small fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" id="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" value="{{ old('jam_selesai', $laporanMengajar->jam_selesai ? \Carbon\Carbon::parse($laporanMengajar->jam_selesai)->format('H:i') : '') }}" required>
                            @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Materi & Evaluasi Pembelajaran --}}
                <div class="form-card">
                    <div class="form-card-title">
                        <i class="bi bi-book-half"></i>
                        <span>2. Materi &amp; Evaluasi Pembelajaran</span>
                    </div>

                    {{-- Catch-up Previous Topic Badge --}}
                    @if($previousReport)
                    <div class="alert alert-info d-flex align-items-center gap-2 py-2 px-3 mb-3 border-0 bg-info-subtle text-info-emphasis rounded-3">
                        <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                        <div class="small">
                            <strong>Materi Pertemuan Sebelumnya:</strong> "{{ $previousReport->materi_pengajaran }}" 
                            <span class="text-muted">({{ $previousReport->jadwal_mengajar ? \Carbon\Carbon::parse($previousReport->jadwal_mengajar)->format('d/m/Y') : '' }} oleh {{ $previousReport->instruktur->nama_lengkap ?? 'Instruktur' }})</span>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="materi_pengajaran" class="form-label fw-bold text-dark">
                            Topik / Materi Pengajaran <span class="text-danger">*</span>
                        </label>
                        
                        @if($materiList->isNotEmpty())
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-white"><i class="bi bi-journal-check text-primary"></i></span>
                            <select class="form-select @error('materi_pengajaran') is-invalid @enderror" id="materi_select" onchange="handleMateriSelect(this)">
                                <option value="">-- Pilih dari Silabus Kurikulum --</option>
                                @foreach($materiList as $m)
                                <option value="{{ $m }}" {{ old('materi_pengajaran', $laporanMengajar->materi_pengajaran) == $m ? 'selected' : '' }}>
                                    {{ $m }}
                                </option>
                                @endforeach
                                <option value="__CUSTOM__" {{ !in_array(old('materi_pengajaran', $laporanMengajar->materi_pengajaran), $materiList->toArray()) ? 'selected' : '' }}>
                                    ✏️ Tulis Materi Kustom / Lain-lain...
                                </option>
                            </select>
                        </div>
                        @endif

                        <textarea name="materi_pengajaran" id="materi_pengajaran" class="form-control @error('materi_pengajaran') is-invalid @enderror" rows="2" placeholder="Tuliskan topik / judul materi pembelajaran..." required>{{ old('materi_pengajaran', $laporanMengajar->materi_pengajaran) }}</textarea>
                        @error('materi_pengajaran') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="refleksi_siswa" class="form-label fw-semibold text-muted small">Respon &amp; Refleksi Siswa</label>
                            <textarea name="refleksi_siswa" id="refleksi_siswa" class="form-control" rows="2" placeholder="Bagaimana antusiasme dan respon siswa terhadap materi hari ini?">{{ old('refleksi_siswa', $laporanMengajar->refleksi_siswa) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="refleksi_capaian" class="form-label fw-semibold text-muted small">Refleksi Capaian Target Materi</label>
                            <textarea name="refleksi_capaian" id="refleksi_capaian" class="form-control" rows="2" placeholder="Apakah seluruh materi tuntas tersampaikan atau ada kendala?">{{ old('refleksi_capaian', $laporanMengajar->refleksi_capaian) }}</textarea>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small d-block">Tingkat Keaktifan Kelas</label>
                            <div class="chip-group">
                                @foreach(['sangat_aktif' => '⚡ Sangat Aktif', 'aktif' => '👍 Aktif', 'cukup' => '👌 Cukup', 'kurang' => '⚠️ Kurang'] as $val => $label)
                                <div>
                                    <input type="radio" class="btn-check" name="keaktifan" id="keaktifan_{{ $val }}" value="{{ $val }}" {{ old('keaktifan', $laporanMengajar->keaktifan ?? 'aktif') == $val ? 'checked' : '' }}>
                                    <label class="chip-btn" for="keaktifan_{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small d-block">Pemahaman Materi Siswa</label>
                            <div class="chip-group">
                                @foreach(['sangat_paham' => '🌟 Sangat Paham', 'paham' => '✅ Paham', 'sedikit_paham' => '📖 Sedikit Paham', 'belum_paham' => '❌ Belum Paham'] as $val => $label)
                                <div>
                                    <input type="radio" class="btn-check" name="pemahaman_materi" id="paham_{{ $val }}" value="{{ $val }}" {{ old('pemahaman_materi', $laporanMengajar->pemahaman_materi ?? 'paham') == $val ? 'checked' : '' }}>
                                    <label class="chip-btn" for="paham_{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="catatan" class="form-label fw-semibold text-muted small">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="2" placeholder="Catatan khusus untuk sesi ini...">{{ old('catatan', $laporanMengajar->ekstrakurikulerSession->catatan ?? '') }}</textarea>
                    </div>
                </div>

                {{-- CARD 3: Checklist Absensi Siswa --}}
                <div class="form-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="form-card-title mb-0 border-0 pb-0">
                            <i class="bi bi-people-fill"></i>
                            <span>3. Absensi &amp; Kehadiran Siswa</span>
                        </div>

                        <div class="attendance-counter">
                            <span class="att-badge total-badge" id="counterTotal">
                                <i class="bi bi-person-lines-fill"></i> Total: {{ $siswaList->count() }}
                            </span>
                            <span class="att-badge hadir" id="counterHadir">
                                <i class="bi bi-check-circle-fill"></i> Hadir: {{ $laporanMengajar->jumlah_siswa_hadir }}
                            </span>
                            <span class="att-badge absen" id="counterAbsen">
                                <i class="bi bi-x-circle-fill"></i> Tidak Hadir: {{ $laporanMengajar->jumlah_siswa_tidak_hadir }}
                            </span>
                        </div>
                    </div>

                    @if($siswaList->isNotEmpty())
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div class="input-group input-group-sm" style="max-width: 280px;">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control" id="searchSiswa" placeholder="Cari nama siswa..." onkeyup="filterSiswa(this.value)">
                        </div>

                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-success" onclick="setAllAttendance('hadir')">
                                <i class="bi bi-check-all me-1"></i> Semua Hadir
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="setAllAttendance('alpha')">
                                <i class="bi bi-x-lg me-1"></i> Semua Alpha
                            </button>
                        </div>
                    </div>

                    <div class="border rounded-3 overflow-hidden mb-2" style="max-height: 420px; overflow-y: auto;">
                        <div id="studentListContainer">
                            @foreach($siswaList as $idx => $siswa)
                            @php
                                $status = $absensiMap[$siswa->id] ?? 'hadir';
                                $gender = strtolower($siswa->jenis_kelamin ?? '');
                                $avatarClass = ($gender === 'l' || $gender === 'laki-laki') ? 'avatar-male' : (($gender === 'p' || $gender === 'perempuan') ? 'avatar-female' : 'avatar-default');
                                $initials = strtoupper(substr($siswa->nama_lengkap, 0, 2));
                            @endphp
                            <div class="student-row" data-name="{{ strtolower($siswa->nama_lengkap) }}">
                                <span class="student-num">{{ $idx + 1 }}.</span>
                                <div class="student-avatar {{ $avatarClass }}">{{ $initials }}</div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $siswa->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $siswa->nisn ? 'NISN: ' . $siswa->nisn : ($siswa->kelas ? 'Kelas: ' . $siswa->kelas : 'Siswa Terdaftar') }}</small>
                                </div>

                                <div class="att-toggle-group">
                                    <input type="radio" class="btn-check att-radio" name="absensi[{{ $siswa->id }}]" id="att_h_{{ $siswa->id }}" value="hadir" {{ $status === 'hadir' ? 'checked' : '' }} onchange="updateAttendanceCounter()">
                                    <label class="att-toggle-btn hadir-btn" for="att_h_{{ $siswa->id }}">
                                        <i class="bi bi-check-lg"></i> Hadir
                                    </label>

                                    <input type="radio" class="btn-check att-radio" name="absensi[{{ $siswa->id }}]" id="att_a_{{ $siswa->id }}" value="alpha" {{ $status === 'alpha' ? 'checked' : '' }} onchange="updateAttendanceCounter()">
                                    <label class="att-toggle-btn absen-btn" for="att_a_{{ $siswa->id }}">
                                        <i class="bi bi-x-lg"></i> Alpha
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    {{-- Fallback jika sesi tidak memiliki daftar siswa rombel terdaftar (contoh: Ad-Hoc bebas) --}}
                    <div class="alert alert-warning border-0 bg-warning-subtle text-warning-emphasis d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div class="small">Laporan ini tidak terhubung dengan data rombel siswa individual. Silakan isi rekapitulasi jumlah siswa secara manual di bawah ini.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Jumlah Siswa Hadir</label>
                            <input type="number" name="jumlah_siswa_hadir" class="form-control" value="{{ old('jumlah_siswa_hadir', $laporanMengajar->jumlah_siswa_hadir) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Jumlah Siswa Tidak Hadir</label>
                            <input type="number" name="jumlah_siswa_tidak_hadir" class="form-control" value="{{ old('jumlah_siswa_tidak_hadir', $laporanMengajar->jumlah_siswa_tidak_hadir) }}" min="0">
                        </div>
                    </div>
                    @endif
                </div>

                {{-- CARD 4: Berkas Dokumentasi & File Project --}}
                <div class="form-card">
                    <div class="form-card-title">
                        <i class="bi bi-paperclip"></i>
                        <span>4. Berkas Dokumentasi &amp; File Project</span>
                    </div>

                    <div class="row g-4">
                        {{-- Foto Kegiatan --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small mb-2">
                                <i class="bi bi-camera-fill text-primary me-1"></i> Foto Kegiatan Mengajar
                            </label>

                            @if($laporanMengajar->foto_kegiatan)
                            <div class="current-file-preview">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <img src="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" alt="Foto Kegiatan" class="rounded" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div class="text-truncate">
                                        <div class="small fw-semibold text-dark text-truncate">Foto Kegiatan Saat Ini</div>
                                        <a href="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" target="_blank" class="small text-primary text-decoration-none">
                                            <i class="bi bi-box-arrow-up-right"></i> Lihat Foto
                                        </a>
                                    </div>
                                </div>
                                <div class="form-check ms-2">
                                    <input class="form-check-input" type="checkbox" name="hapus_foto_kegiatan" id="hapus_foto_kegiatan" value="1">
                                    <label class="form-check-label text-danger small fw-semibold" for="hapus_foto_kegiatan">Hapus</label>
                                </div>
                            </div>
                            @endif

                            <div class="upload-zone" onclick="document.getElementById('foto_kegiatan').click()">
                                <i class="bi bi-cloud-arrow-up-fill upload-icon"></i>
                                <div class="upload-text">Klik atau Tarik Foto Baru</div>
                                <div class="upload-subtext">JPG, PNG, WebP (Maks. 5MB)</div>
                                <input type="file" name="foto_kegiatan" id="foto_kegiatan" accept="image/*" onchange="previewUpload(this, 'preview_kegiatan')">
                            </div>
                            <div id="preview_kegiatan" class="small text-success mt-1 fw-semibold d-none"></div>
                            @error('foto_kegiatan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- Foto Absensi Siswa (TTD & Stempel) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small mb-2">
                                <i class="bi bi-card-checklist text-success me-1"></i> Foto Absensi Bertanda Tangan / Stempel
                            </label>

                            @if($laporanMengajar->foto_absensi_siswa)
                            <div class="current-file-preview">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <img src="{{ asset('storage/' . $laporanMengajar->foto_absensi_siswa) }}" alt="Foto Absensi" class="rounded" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div class="text-truncate">
                                        <div class="small fw-semibold text-dark text-truncate">Foto Absensi Saat Ini</div>
                                        <a href="{{ asset('storage/' . $laporanMengajar->foto_absensi_siswa) }}" target="_blank" class="small text-primary text-decoration-none">
                                            <i class="bi bi-box-arrow-up-right"></i> Lihat Absensi
                                        </a>
                                    </div>
                                </div>
                                <div class="form-check ms-2">
                                    <input class="form-check-input" type="checkbox" name="hapus_foto_absensi_siswa" id="hapus_foto_absensi_siswa" value="1">
                                    <label class="form-check-label text-danger small fw-semibold" for="hapus_foto_absensi_siswa">Hapus</label>
                                </div>
                            </div>
                            @endif

                            <div class="upload-zone" onclick="document.getElementById('foto_absensi_siswa').click()">
                                <i class="bi bi-file-earmark-image-fill upload-icon text-success"></i>
                                <div class="upload-text">Klik atau Tarik Foto Absensi</div>
                                <div class="upload-subtext">JPG, PNG, WebP (Maks. 5MB)</div>
                                <input type="file" name="foto_absensi_siswa" id="foto_absensi_siswa" accept="image/*" onchange="previewUpload(this, 'preview_absensi')">
                            </div>
                            <div id="preview_absensi" class="small text-success mt-1 fw-semibold d-none"></div>
                            @error('foto_absensi_siswa') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- File Project (.sb3, .hex, .zip) --}}
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small mb-2">
                                <i class="bi bi-file-earmark-code-fill text-purple me-1"></i> File Project (.sb3, .hex, .zip, .rar, .ino, .pdf)
                            </label>

                            @if($laporanMengajar->file_project)
                            <div class="current-file-preview">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <div class="bg-primary text-white rounded p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-file-earmark-zip-fill"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="small fw-semibold text-dark text-truncate">{{ basename($laporanMengajar->file_project) }}</div>
                                        <a href="{{ asset('storage/' . $laporanMengajar->file_project) }}" target="_blank" class="small text-primary text-decoration-none">
                                            <i class="bi bi-download"></i> Unduh File Project
                                        </a>
                                    </div>
                                </div>
                                <div class="form-check ms-2">
                                    <input class="form-check-input" type="checkbox" name="hapus_file_project" id="hapus_file_project" value="1">
                                    <label class="form-check-label text-danger small fw-semibold" for="hapus_file_project">Hapus</label>
                                </div>
                            </div>
                            @endif

                            <div class="upload-zone" onclick="document.getElementById('file_project').click()">
                                <i class="bi bi-folder-fill upload-icon text-warning"></i>
                                <div class="upload-text">Klik atau Tarik File Project Baru</div>
                                <div class="upload-subtext">Ekstensi: .sb3, .hex, .zip, .rar, .7z, .py, .ino, .pdf (Maks. 10MB)</div>
                                <input type="file" name="file_project" id="file_project" onchange="previewUpload(this, 'preview_project')">
                            </div>
                            <div id="preview_project" class="small text-success mt-1 fw-semibold d-none"></div>
                            @error('file_project') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between align-items-center gap-3 mt-4 mb-5">
                    <a href="{{ route('laporan-mengajar.show', $laporanMengajar) }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                        <i class="bi bi-arrow-left me-1"></i> Batal &amp; Kembali
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm" id="btnSubmit">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Laporan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Handle Materi Dropdown Selection
    function handleMateriSelect(selectEl) {
        const val = selectEl.value;
        const textarea = document.getElementById('materi_pengajaran');
        if (val && val !== '__CUSTOM__') {
            textarea.value = val;
        } else if (val === '__CUSTOM__') {
            textarea.value = '';
            textarea.focus();
        }
    }

    // Filter Student List
    function filterSiswa(query) {
        const q = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.student-row');
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            row.style.display = name.includes(q) ? 'flex' : 'none';
        });
    }

    // Set All Attendance
    function setAllAttendance(status) {
        const radios = document.querySelectorAll('.att-radio');
        radios.forEach(radio => {
            if (radio.value === status) {
                radio.checked = true;
            }
        });
        updateAttendanceCounter();
    }

    // Update Live Attendance Counter
    function updateAttendanceCounter() {
        const hadirRadios = document.querySelectorAll('.att-radio[value="hadir"]:checked');
        const absenRadios = document.querySelectorAll('.att-radio[value="alpha"]:checked');
        
        const counterHadir = document.getElementById('counterHadir');
        const counterAbsen = document.getElementById('counterAbsen');
        
        if (counterHadir) {
            counterHadir.innerHTML = `<i class="bi bi-check-circle-fill"></i> Hadir: ${hadirRadios.length}`;
        }
        if (counterAbsen) {
            counterAbsen.innerHTML = `<i class="bi bi-x-circle-fill"></i> Tidak Hadir: ${absenRadios.length}`;
        }
    }

    // Preview File Selection
    function previewUpload(input, targetId) {
        const target = document.getElementById(targetId);
        if (input.files && input.files[0]) {
            const file = input.files[0];
            target.textContent = `✓ File terpilih: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            target.classList.remove('d-none');
        } else {
            target.classList.add('d-none');
        }
    }

    // Submit Guard
    document.getElementById('editLaporanForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
    });

    // Initialize attendance counters on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateAttendanceCounter();
    });
</script>
@endpush