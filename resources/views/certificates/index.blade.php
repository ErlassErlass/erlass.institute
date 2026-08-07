@extends('layouts.app')

@section('title', 'Rapor & Sertifikat Siswa')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Hero Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center text-white shadow-sm"
                         style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); width: 56px; height: 56px;">
                        <i class="bi bi-patch-check-fill fs-3"></i>
                    </div>
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1 small">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i class="bi bi-house me-1"></i>Dashboard</a></li>
                                <li class="breadcrumb-item active fw-medium text-success" aria-current="page">Rapor & Sertifikat</li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold mb-0 text-dark">Rapor & Sertifikat Digital</h3>
                        <p class="text-muted small mb-0 mt-1">Manajemen pencetakan hasil belajar dan sertifikat kelulusan siswa secara terpusat.</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check"></i> Minimal Kehadiran Sertifikat: <strong>75%</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('certificates.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-6 col-lg-5">
                    <label for="rombel_id" class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-funnel me-1"></i> Filter Rombel / Kelompok Belajar
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-door-open"></i></span>
                        <select name="rombel_id" id="rombel_id" class="form-select bg-light border-start-0" onchange="this.form.submit()">
                            <option value="">Semua Rombel Terdaftar...</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                                    {{ $rombel->ekstrakurikuler->sekolah->namasekolah ?? 'Sekolah' }} — {{ $rombel->nama_rombel }} ({{ $rombel->ekstrakurikuler->kategori_program }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    @if(request()->filled('rombel_id'))
                        <a href="{{ route('certificates.index') }}" class="btn btn-light border w-100 rounded-3 text-secondary">
                            <i class="bi bi-x-circle me-1"></i> Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Centralized Document List Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-check-fill text-success fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark">Daftar Berkas Terbit</h5>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">Total: {{ $scores->total() ?? $scores->count() }} Data</span>
        </div>
        <div class="card-body p-0">
            @if($scores->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr class="small text-uppercase fw-bold">
                                <th class="ps-4 py-3" style="width: 28%;">Siswa & Rombel</th>
                                <th class="text-center py-3" style="width: 15%;">Rasio Kehadiran</th>
                                <th class="text-center py-3" style="width: 12%;">Nilai Akhir</th>
                                <th class="text-center py-3" style="width: 15%;">Predikat</th>
                                <th class="text-end pe-4 py-3" style="width: 30%;">Unduh Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scores as $score)
                                @php
                                    $reportCard = $reportCards->get($score->id);
                                    $studentCerts = $certificates->get($score->siswa_id);
                                    
                                    // Get matching cert for this specific program if exists
                                    $cert = null;
                                    if ($studentCerts) {
                                        $cert = $studentCerts->firstWhere('ekstrakurikuler_id', $score->ekstrakurikuler_id);
                                    }
                                    $isEligible = $score->nilai_kehadiran >= 75;
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold fs-6 shadow-sm" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($score->siswa->nama_lengkap ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark mb-0">{{ $score->siswa->nama_lengkap ?? '-' }}</div>
                                                <small class="text-muted">
                                                    <i class="bi bi-door-open me-1"></i>{{ $score->ekstrakurikulerRombel->nama_rombel ?? '-' }}
                                                    <span class="mx-1">•</span>
                                                    <span class="badge bg-light text-dark border">{{ $score->ekstrakurikuler->kategori_program ?? '-' }}</span>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="fw-bold fs-6 {{ $isEligible ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($score->nilai_kehadiran, 1) }}%
                                        </span>
                                        <div class="progress mx-auto mt-1" style="height: 5px; width: 70px;">
                                            <div class="progress-bar bg-{{ $isEligible ? 'success' : 'danger' }}" role="progressbar" style="width: {{ min(100, $score->nilai_kehadiran) }}%;"></div>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="fs-6 fw-bold text-primary bg-primary-subtle px-3 py-1 rounded-pill">
                                            {{ number_format($score->nilai_akhir, 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
                                            {{ $score->getPredikat() }} - {{ $score->getKeterangan() }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-inline-flex gap-2 align-items-center">
                                            <!-- Rapor Download -->
                                            @if($reportCard)
                                                <a href="{{ route('report-cards.download', $reportCard->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Rapor PDF
                                                </a>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 py-2">Rapor N/A</span>
                                            @endif

                                            <!-- Certificate Download / Eligibility -->
                                            @if($isEligible)
                                                @if($cert)
                                                    <a href="{{ route('certificates.download', $cert->id) }}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                                        <i class="bi bi-patch-check-fill me-1"></i> Sertifikat PDF
                                                    </a>
                                                @else
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2">Generasi...</span>
                                                @endif
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2" title="Rasio kehadiran kurang dari 75%">
                                                    <i class="bi bi-x-circle me-1"></i> Tidak Eligible
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top bg-light">
                    <x-pagination-wrapper :paginator="$scores->appends(request()->query())" />
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
                        <i class="bi bi-file-earmark-x fs-1 text-secondary"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Dokumen Terbit</h5>
                    <p class="mb-0 text-muted">Belum ada rapor atau sertifikat yang diterbitkan untuk filter rombel ini.</p>

                </div>
            @endif
        </div>
    </div>
</div>
@endsection
