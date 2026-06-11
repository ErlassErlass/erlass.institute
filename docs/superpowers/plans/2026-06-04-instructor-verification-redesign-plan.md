# Instructor Verification Page Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the instructor verification detail page to match the modern dashboard style.

**Architecture:** Blade template using Bootstrap 5 and Bootstrap Icons. 2-column layout (8/4) with clean cards and clear action hierarchy.

**Tech Stack:** Blade, Bootstrap 5, Bootstrap Icons.

---

### Task 1: Backup & Preparation

**Files:**
- Backup: `/var/www/webapperlass/resources/views/admin/verification/show.blade.php`

- [ ] **Step 1: Create backup of the existing file**

Run: `cp /var/www/webapperlass/resources/views/admin/verification/show.blade.php /var/www/webapperlass/resources/views/admin/verification/show.blade.php.bak`

---

### Task 2: Implementing Base Layout & Header

**Files:**
- Modify: `/var/www/webapperlass/resources/views/admin/verification/show.blade.php`

- [ ] **Step 1: Replace file content with new base structure**

```html
@extends('layouts.app')

@section('title', 'Verifikasi Instruktur')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 fw-bold text-dark mb-1">Verifikasi Instruktur</h1>
            <p class="text-muted mb-0">Review pendaftaran untuk: {{ $instructor->nama_lengkap }}</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.verification.index') }}" class="btn btn-white border shadow-sm rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(!$instructor || !$instructor->instructorProfile)
        <div class="alert alert-warning border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle me-2"></i> Data profil instruktur tidak lengkap atau belum diisi sepenuhnya.
        </div>
    @else
        <div class="row g-4">
            <!-- Left Column: Main Info -->
            <div class="col-lg-8">
                <!-- Profile Summary Card -->
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-auto text-center">
                                @if($instructor->instructorProfile->foto)
                                    <img src="{{ Storage::url($instructor->instructorProfile->foto) }}" alt="Profile" class="rounded-circle shadow border border-3 border-white object-fit-cover" style="width: 120px; height: 120px;">
                                @else
                                    <div class="bg-primary bg-opacity-10 rounded-circle shadow-sm d-flex align-items-center justify-content-center text-primary border border-3 border-white mx-auto" style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold;">
                                        {{ substr($instructor->nama_lengkap, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h2 class="fw-bold text-dark mb-0">{{ $instructor->nama_lengkap }}</h2>
                                    @if($instructor->instructorProfile->gelar_belakang)
                                        <span class="badge bg-light text-secondary border">{{ $instructor->instructorProfile->gelar_belakang }}</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                                    <span><i class="bi bi-envelope me-1"></i> {{ $instructor->email }}</span>
                                    <span><i class="bi bi-whatsapp me-1"></i> <a href="https://wa.me/{{ $instructor->no_telephone }}" target="_blank" class="text-decoration-none">{{ $instructor->no_telephone }}</a></span>
                                    <span><i class="bi bi-geo-alt me-1"></i> {{ $instructor->instructorProfile->kota_domisili ?? '-' }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3">{{ $instructor->instructorProfile->agama ?? 'Agama' }}</span>
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3">{{ $instructor->instructorProfile->status_pernikahan ?? 'Status' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal & Domicile Details -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Data Pribadi & Domisili</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-6 border-end">
                                <div class="p-3 border-bottom">
                                    <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">NIK (No. KTP)</small>
                                    <span class="fw-bold font-monospace">{{ $instructor->nik ?? '-' }}</span>
                                </div>
                                <div class="p-3">
                                    <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Tanggal Lahir</small>
                                    <span>{{ $instructor->tanggal_lahir ? $instructor->tanggal_lahir->format('d F Y') : '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border-bottom">
                                    <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Alamat Domisili</small>
                                    <span class="small">{{ $instructor->instructorProfile->alamat_domisili ?? '-' }}</span>
                                </div>
                                <div class="p-3">
                                    <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">No. HP Keluarga (Darurat)</small>
                                    <span>{{ $instructor->instructorProfile->no_hp_2 ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Education & Skills -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-mortarboard me-2 text-primary"></i>Pendidikan</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3">
                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Pendidikan Terakhir</small>
                                        <span class="fw-bold">{{ $instructor->pend_terakhir }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Universitas & Jurusan</small>
                                        <span>{{ $instructor->instructorProfile->universitas_jurusan ?? '-' }}</span>
                                    </li>
                                    <li>
                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Keahlian/Kompetensi</small>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $instructor->kompetensi_1 }}</span>
                                            @if($instructor->kompetensi_2)
                                                <span class="badge bg-info bg-opacity-10 text-info">{{ $instructor->kompetensi_2 }}</span>
                                            @endif
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-heart-pulse me-2 text-primary"></i>Fisik & Kesehatan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Tinggi/Berat</small>
                                        <span class="fw-bold">{{ $instructor->instructorProfile->tinggi_berat_badan ?? '-' }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Mata Minus</small>
                                        <span>{{ $instructor->instructorProfile->mata_minus ?? '-' }}</span>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Riwayat Penyakit</small>
                                        <span class="text-muted fst-italic">{{ $instructor->instructorProfile->riwayat_penyakit ?? 'Tidak ada' }}</span>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Kendaraan</small>
                                        <span>{{ $instructor->instructorProfile->kendaraan }} ({{ $instructor->instructorProfile->jenis_kendaraan }})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teaching Schedule -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar-week me-2 text-primary"></i>Jadwal Tersedia</h5>
                    </div>
                    <div class="card-body">
                        @php $waktu = $instructor->instructorProfile->waktu_mengajar; @endphp
                        @if($waktu && count($waktu) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle small mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                                <th class="py-2 text-uppercase" style="width: 16.66%">{{ $hari }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                                <td class="p-2 vertical-top" style="height: 100px;">
                                                    <div class="d-flex flex-column gap-1">
                                                        @if(isset($waktu[$hari]))
                                                            @foreach($waktu[$hari] as $jam)
                                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-normal">{{ $jam }}</span>
                                                            @endforeach
                                                        @else
                                                            <small class="text-muted opacity-25">-</small>
                                                        @endif
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-1 opacity-25 d-block mb-2"></i>
                                Belum ada data jadwal mengajar.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar Actions -->
            <div class="col-lg-4">
                <!-- Action Buttons -->
                @if($instructor->verification_status === 'pending')
                    <div class="card shadow-sm border-0 border-top border-4 border-warning mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Tindakan Verifikasi</h5>
                            <p class="text-muted small mb-4">Review data dengan teliti. Menyetujui pendaftaran akan memberikan akses login ke instruktur.</p>
                            
                            <div class="d-grid gap-2">
                                <form action="{{ route('admin.verification.approve', $instructor) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 py-2 rounded-pill shadow-sm fw-bold mb-2" onclick="return confirm('Setujui instruktur ini?')">
                                        <i class="bi bi-check-circle-fill me-2"></i> Setujui
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-danger py-2 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="bi bi-x-circle me-2"></i> Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm border-0 mb-4 bg-{{ $instructor->verification_status === 'approved' ? 'success' : 'danger' }} bg-opacity-10">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-{{ $instructor->verification_status === 'approved' ? 'patch-check-fill text-success' : 'patch-exclamation-fill text-danger' }} fs-1 mb-2"></i>
                            <h5 class="fw-bold text-dark mb-1">Status: {{ Str::upper($instructor->verification_status) }}</h5>
                            <p class="text-muted small mb-0">Diverifikasi pada: {{ $instructor->verified_at ? $instructor->verified_at->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>
                @endif

                <!-- Financial Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-wallet2 me-2 text-primary"></i>Informasi Keuangan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Nama Bank</small>
                            <span class="fw-bold text-dark">{{ $instructor->instructorProfile->nama_bank }}</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">No. Rekening</small>
                            <span class="fw-bold fs-5 text-primary font-monospace">{{ $instructor->instructorProfile->no_rekening }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">NPWP</small>
                            <span class="font-monospace text-dark">{{ $instructor->instructorProfile->no_npwp ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Documents Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-folder2-open me-2 text-primary"></i>Dokumen</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @if($instructor->instructorProfile->cv_link)
                            <a href="{{ Storage::url($instructor->instructorProfile->cv_link) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                                <div class="bg-danger bg-opacity-10 p-2 rounded me-3 text-danger"><i class="bi bi-file-earmark-pdf fs-5"></i></div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 small fw-bold">Curriculum Vitae</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">PDF Document</small>
                                </div>
                                <i class="bi bi-box-arrow-up-right small text-muted"></i>
                            </a>
                        @endif
                        @if($instructor->instructorProfile->foto_ktp)
                            <a href="{{ Storage::url($instructor->instructorProfile->foto_ktp) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary"><i class="bi bi-person-bounding-box fs-5"></i></div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 small fw-bold">KTP (Identitas)</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Image Document</small>
                                </div>
                                <i class="bi bi-box-arrow-up-right small text-muted"></i>
                            </a>
                        @endif
                        @if($instructor->instructorProfile->foto_npwp)
                            <a href="{{ Storage::url($instructor->instructorProfile->foto_npwp) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                                <div class="bg-success bg-opacity-10 p-2 rounded me-3 text-success"><i class="bi bi-credit-card-2-front fs-5"></i></div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 small fw-bold">Kartu NPWP</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Image Document</small>
                                </div>
                                <i class="bi bi-box-arrow-up-right small text-muted"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('admin.verification.reject', $instructor) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i> Tolak Pendaftaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 small mb-4">
                        <i class="bi bi-info-circle me-1"></i> Berikan alasan penolakan yang jelas agar instruktur dapat memperbaiki datanya.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Contoh: Foto KTP tidak jelas atau CV tidak sesuai kriteria..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

---

### Task 3: Verification & Polish

**Files:**
- N/A

- [ ] **Step 1: Check UI consistency**
Compare the new page with the dashboard. Ensure fonts, colors, and shadows match.

- [ ] **Step 2: Test Form Actions**
Verify that "Setujui" still triggers the confirmation and submits correctly.
Verify that "Tolak" opens the modal and the form inside submits correctly.

- [ ] **Step 3: Clear Cache**
Run: `php /var/www/webapperlass/artisan view:clear`
