@extends('layouts.app')

@section('title', 'Kelola Nilai Rombel')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Hero Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('student-scores.rombel-list') }}" class="btn btn-light border rounded-circle p-2 d-flex align-items-center justify-content-center text-secondary shadow-sm" style="width: 44px; height: 44px;">
                        <i class="bi bi-arrow-left fs-5"></i>
                    </a>
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1 small">
                                <li class="breadcrumb-item"><a href="{{ route('student-scores.rombel-list') }}" class="text-decoration-none text-muted">Penilaian Rombel</a></li>
                                <li class="breadcrumb-item active fw-medium text-primary" aria-current="page">{{ $rombel->nama_rombel }}</li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold mb-0 text-dark">Kelola Nilai: {{ $rombel->nama_rombel }}</h3>
                        <p class="text-muted small mb-0 mt-1">
                            <i class="bi bi-building me-1"></i>{{ $rombel->ekstrakurikuler->sekolah->namasekolah ?? 'Sekolah' }} 
                            <span class="mx-1">•</span> 
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $rombel->ekstrakurikuler->kategori_program ?? '-' }}</span>
                        </p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @php
                        $limit = min(8, $rombel->total_pertemuan ?? 4);
                        $firstScore = collect($scores)->first();
                        $isFinalized = $firstScore && $firstScore->finalized_at;
                        $allComplete = true;
                        foreach($scores as $score) {
                            if(!$score->isComplete()) {
                                $allComplete = false;
                                break;
                            }
                        }
                    @endphp

                    @if(!$isFinalized)
                        <a href="{{ route('student-scores.bulk-input', $rombel->id) }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm font-semibold">
                            <i class="bi bi-pencil-square me-1"></i> Input/Edit Nilai Massal
                        </a>
                        
                        @if($allComplete && count($scores) > 0)
                            <form action="{{ route('student-scores.finalize', $rombel->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi nilai kelas ini? Setelah difinalisasi, nilai TIDAK DAPAT diubah kembali, dan dokumen Rapor & Sertifikat akan langsung diterbitkan.');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success rounded-pill px-4 py-2 shadow-sm font-semibold">
                                    <i class="bi bi-lock-fill me-1"></i> Finalisasi Kelas
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-secondary rounded-pill px-4 py-2 opacity-75" disabled title="Lengkapi semua {{ $limit }} input nilai terlebih dahulu untuk memfinalisasi.">
                                <i class="bi bi-lock-fill me-1"></i> Finalisasi Kelas (Belum Lengkap)
                            </button>
                        @endif
                    @else
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-4 py-3 fs-6 font-semibold">
                            <i class="bi bi-check-circle-fill me-1"></i> Nilai Terkunci (Finalized)
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Jumlah Peserta</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ count($siswaList) }} Siswa</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-calendar-check-fill fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Sesi Pelaksanaan</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $rombel->pertemuan_selesai }} / {{ $rombel->total_pertemuan }} Sesi</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-{{ $isFinalized ? 'success' : 'warning' }} bg-opacity-10 text-{{ $isFinalized ? 'success' : 'warning' }}">
                        <i class="bi bi-{{ $isFinalized ? 'lock-fill' : 'pencil-fill' }} fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Status Nilai</span>
                        <h4 class="fw-bold mb-0 text-{{ $isFinalized ? 'success' : 'warning' }}">
                            {{ $isFinalized ? 'Finalized' : 'Draft' }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-patch-check-fill fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Syarat Sertifikat</span>
                        <h4 class="fw-bold mb-0 text-success">&ge; 75% Kehadiran</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Scores Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-people-fill text-primary fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark">Daftar Penilaian Siswa</h5>
            </div>
            @if(!$isFinalized)
                <span class="badge bg-light text-muted border px-3 py-2 rounded-pill font-normal">
                    <i class="bi bi-calculator me-1"></i> Formula NA = Kehadiran 30% + Tugas 30% + Sikap 20% + Proyek 20%
                </span>
            @endif
        </div>
        <div class="card-body p-0">
            @if(count($siswaList) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr class="small text-uppercase fw-bold">
                                <th class="ps-4 py-3" style="width: 25%;">Nama Siswa</th>
                                <th class="text-center py-3" style="width: 15%;">Kehadiran</th>
                                <th class="text-center py-3" style="width: 10%;">Tugas (30%)</th>
                                <th class="text-center py-3" style="width: 10%;">Sikap (20%)</th>
                                <th class="text-center py-3" style="width: 10%;">Proyek (20%)</th>
                                <th class="text-center py-3" style="width: 12%;">Nilai Akhir (NA)</th>
                                <th class="text-center py-3" style="width: 12%;">Predikat</th>
                                <th class="text-end pe-4 py-3" style="width: 6%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $siswa)
                                @php
                                    $score = $scores[$siswa->id] ?? null;
                                    $attRate = $score ? $score->nilai_kehadiran : 0;
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold fs-6 shadow-sm" style="width: 38px; height: 38px;">
                                                {{ strtoupper(substr($siswa->nama_lengkap ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark mb-0">{{ $siswa->nama_lengkap }}</div>
                                                <small class="text-muted">NISN: {{ $siswa->nisn ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="fw-bold text-dark">{{ number_format($attRate, 1) }}%</span>
                                        <div class="progress mx-auto mt-1 rounded-pill" style="height: 5px; width: 65px;">
                                            <div class="progress-bar bg-{{ $attRate >= 75 ? 'success' : 'danger' }}" role="progressbar" style="width: {{ min(100, $attRate) }}%;"></div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-medium text-dark py-3">
                                        {{ $score && $score->nilai_tugas !== null ? number_format($score->nilai_tugas, 1) : '-' }}
                                    </td>
                                    <td class="text-center fw-medium text-dark py-3">
                                        {{ $score && $score->nilai_sikap !== null ? number_format($score->nilai_sikap, 1) : '-' }}
                                    </td>
                                    <td class="text-center fw-medium text-dark py-3">
                                        {{ $score && $score->nilai_proyek !== null ? number_format($score->nilai_proyek, 1) : '-' }}
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="fs-6 fw-bold text-primary bg-primary-subtle px-3 py-1 rounded-pill">
                                            {{ $score && $score->nilai_akhir !== null ? number_format($score->nilai_akhir, 1) : '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center py-3">
                                        @if($score && $score->nilai_akhir > 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 font-semibold">
                                                {{ $score->getPredikat() }} - {{ $score->getKeterangan() }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 py-3 text-nowrap">
                                        @if($score && $score->isComplete())
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i>Lengkap</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1"><i class="bi bi-clock me-1"></i>Draft</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
                        <i class="bi bi-people fs-1 text-secondary"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Siswa</h5>
                    <p class="mb-0 text-muted">Tidak ada siswa yang terdaftar dalam rombel ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
