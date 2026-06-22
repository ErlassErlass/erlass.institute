@extends('layouts.app')

@section('title', 'Daftar Nilai Rombel')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <a href="{{ route('student-scores.rombel-list') }}" class="btn btn-sm btn-light border mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Rombel
            </a>
            <h1 class="h3 fw-bold text-dark mb-1">Kelola Nilai Rombel: {{ $rombel->nama_rombel }}</h1>
            <p class="text-muted mb-0">
                {{ $rombel->ekstrakurikuler->sekolah->namasekolah }} | {{ $rombel->ekstrakurikuler->kategori_program }}
            </p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
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
                <a href="{{ route('student-scores.bulk-input', $rombel->id) }}" class="btn btn-primary rounded-pill px-4 me-2">
                    <i class="bi bi-pencil-square me-1"></i> Input/Edit Nilai Massal
                </a>
                
                @if($allComplete && count($scores) > 0)
                    <form action="{{ route('student-scores.finalize', $rombel->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi nilai kelas ini? Setelah difinalisasi, nilai TIDAK DAPAT diubah kembali, dan dokumen Rapor & Sertifikat akan langsung diterbitkan.');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="bi bi-lock-fill me-1"></i> Finalisasi Kelas
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-secondary rounded-pill px-4" disabled title="Lengkapi semua {{ $limit }} input nilai terlebih dahulu untuk memfinalisasi.">
                        <i class="bi bi-lock-fill me-1"></i> Finalisasi Kelas (Belum Lengkap)
                    </button>
                @endif
            @else
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-4 py-2 fs-6">
                    <i class="bi bi-check-circle-fill me-1"></i> Nilai Terkunci (Finalized)
                </span>
            @endif
        </div>
    </div>

    <!-- Stats summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body">
                    <h6 class="text-muted small uppercase mb-1">Jumlah Peserta</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ count($siswaList) }} Siswa</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body">
                    <h6 class="text-muted small uppercase mb-1">Sesi Pelaksanaan</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $rombel->pertemuan_selesai }} / {{ $rombel->total_pertemuan }} Sesi</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body">
                    <h6 class="text-muted small uppercase mb-1">Status Nilai</h6>
                    <h3 class="fw-bold mb-0 {{ $isFinalized ? 'text-success' : 'text-warning' }}">
                        {{ $isFinalized ? 'Finalized' : 'Draft / Inputting' }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body">
                    <h6 class="text-muted small uppercase mb-1">Threshold Sertifikat</h6>
                    <h3 class="fw-bold mb-0 text-primary">&ge; 75% Kehadiran</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Penilaian Siswa</h5>
            @if(!$isFinalized)
                <small class="text-muted">Formula: NA = Kehadiran 30% + Tugas 30% + Sikap 20% + Proyek 20%</small>
            @endif
        </div>
        <div class="card-body p-0">
            @if(count($siswaList) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 25%;">Nama Siswa</th>
                                <th style="width: 15%; text-align: center;">Rasio Kehadiran</th>
                                <th style="width: 10%; text-align: center;">Rata Tugas (30%)</th>
                                <th style="width: 10%; text-align: center;">Rata Sikap (20%)</th>
                                <th style="width: 10%; text-align: center;">Rata Proyek (20%)</th>
                                <th style="width: 15%; text-align: center;">Nilai Akhir (NA)</th>
                                <th style="width: 10%; text-align: center;">Predikat</th>
                                <th class="text-end pe-4" style="width: 5%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $siswa)
                                @php
                                    $score = $scores[$siswa->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $siswa->nama_lengkap }}</div>
                                        <small class="text-muted">NISN: {{ $siswa->nisn }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-dark">{{ $score ? number_format($score->nilai_kehadiran, 1) : 0 }}%</span>
                                        <div class="progress mx-auto mt-1" style="height: 4px; width: 60px;">
                                            <div class="progress-bar bg-{{ ($score && $score->nilai_kehadiran >= 75) ? 'success' : 'danger' }}" role="progressbar" style="width: {{ $score ? $score->nilai_kehadiran : 0 }}%;"></div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-medium text-dark">
                                        {{ $score ? number_format($score->nilai_tugas, 1) : '-' }}
                                    </td>
                                    <td class="text-center fw-medium text-dark">
                                        {{ $score ? number_format($score->nilai_sikap, 1) : '-' }}
                                    </td>
                                    <td class="text-center fw-medium text-dark">
                                        {{ $score ? number_format($score->nilai_proyek, 1) : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="fs-6 fw-bold text-primary">{{ $score ? number_format($score->nilai_akhir, 1) : '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($score && $score->nilai_akhir > 0)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3">
                                                {{ $score->getPredikat() }} - {{ $score->getKeterangan() }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        @if($score && $score->isComplete())
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">Lengkap</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2">Draft</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-people fs-1 mb-3 d-block text-secondary"></i>
                    <p class="mb-0">Tidak ada siswa terdaftar dalam rombel ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
