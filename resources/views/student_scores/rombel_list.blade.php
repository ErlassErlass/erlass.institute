@extends('layouts.app')

@section('title', 'Daftar Kelas Penilaian')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Hero Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 d-flex align-items-center justify-content-center text-white shadow-sm"
                         style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); width: 56px; height: 56px;">
                        <i class="bi bi-journal-bookmark-fill fs-3"></i>
                    </div>
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1 small">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i class="bi bi-house me-1"></i>Dashboard</a></li>
                                <li class="breadcrumb-item active fw-medium text-primary" aria-current="page">Penilaian Ekstrakurikuler</li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold mb-0 text-dark">Daftar Kelas & Penilaian Siswa</h3>
                        <p class="text-muted small mb-0 mt-1">Pilih rombongan belajar (rombel) untuk mengelola dan memfinalisasi nilai akhir siswa.</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
                        <i class="bi bi-layer-group me-1"></i> Total Rombel: <strong>{{ $rombels->count() }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Rombel Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-journal-check text-primary fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark">Daftar Rombel Aktif</h5>
            </div>
        </div>
        <div class="card-body p-0">
            @if($rombels->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr class="small text-uppercase fw-bold">
                                <th class="ps-4 py-3" style="width: 30%;">Sekolah & Rombel</th>
                                <th class="py-3" style="width: 20%;">Program Ekskul</th>
                                <th class="py-3" style="width: 20%;">Instruktur Penanggung Jawab</th>
                                <th class="text-center py-3" style="width: 15%;">Progress Sesi</th>
                                <th class="text-end pe-4 py-3" style="width: 15%;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rombels as $rombel)
                                @php
                                    $progressPct = $rombel->getProgressPersentase();
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold fs-6 shadow-sm" style="width: 42px; height: 42px;">
                                                <i class="bi bi-building"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark mb-0">{{ $rombel->ekstrakurikuler->sekolah->namasekolah ?? '-' }}</div>
                                                <small class="text-muted"><i class="bi bi-door-open me-1"></i>{{ $rombel->nama_rombel }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-semibold">
                                            {{ $rombel->ekstrakurikuler->kategori_program ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center fw-bold fs-7" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($rombel->instruktur->nama_lengkap ?? 'I', 0, 1)) }}
                                            </div>
                                            <span class="small fw-semibold text-dark">{{ $rombel->instruktur->nama_lengkap ?? 'Belum Ditugaskan' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <div class="d-flex align-items-center justify-content-center flex-column">
                                            <span class="small fw-bold text-dark mb-1">{{ $rombel->pertemuan_selesai }} / {{ $rombel->total_pertemuan }} Sesi</span>
                                            <div class="progress rounded-pill" style="height: 6px; width: 100px;">
                                                <div class="progress-bar bg-{{ $progressPct >= 100 ? 'success' : 'primary' }}" role="progressbar" style="width: {{ $progressPct }}%;" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <a href="{{ route('student-scores.index', $rombel->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-pencil-square me-1"></i> Kelola Nilai
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
                        <i class="bi bi-journal-x fs-1 text-secondary"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Rombel Aktif</h5>
                    <p class="mb-0 text-muted">Tidak ada rombel aktif yang ditugaskan untuk pengelolaan nilai saat ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
