@extends('layouts.app')

@section('title', 'Rapor & Sertifikat Siswa')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 fw-bold text-dark mb-1">Rapor & Sertifikat Digital</h1>
            <p class="text-muted mb-0">Manajemen pencetakan hasil belajar dan sertifikat kelulusan siswa.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body py-3">
            <form action="{{ route('certificates.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="rombel_id" class="form-label small fw-semibold text-muted">Filter Rombel / Kelas</label>
                    <select name="rombel_id" id="rombel_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Rombel</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                                {{ $rombel->ekstrakurikuler->sekolah->namasekolah }} - {{ $rombel->nama_rombel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mt-md-4">
                    @if(request()->filled('rombel_id'))
                        <a href="{{ route('certificates.index') }}" class="btn btn-sm btn-light border text-nowrap">
                            <i class="bi bi-x-circle me-1"></i> Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Centralized Document list -->
    <div class="card shadow-sm border-0 bg-white">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-patch-check text-primary me-2"></i>Daftar Berkas Terbit</h5>
        </div>
        <div class="card-body p-0">
            @if($scores->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 25%;">Siswa & Kelas</th>
                                <th style="width: 15%; text-align: center;">Kehadiran</th>
                                <th style="width: 15%; text-align: center;">Nilai Akhir</th>
                                <th style="width: 15%; text-align: center;">Predikat</th>
                                <th class="text-end pe-4" style="width: 30%;">Download Dokumen</th>
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
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $score->siswa->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $score->ekstrakurikulerRombel->nama_rombel }} | {{ $score->ekstrakurikuler->kategori_program }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-dark">{{ number_format($score->nilai_kehadiran, 1) }}%</span>
                                    </td>
                                    <td class="text-center fw-bold text-primary">
                                        {{ number_format($score->nilai_akhir, 1) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3">
                                            {{ $score->getPredikat() }} - {{ $score->getKeterangan() }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Rapor Download -->
                                        @if($reportCard)
                                            <a href="{{ route('report-cards.download', $reportCard->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                                <i class="bi bi-file-earmark-pdf me-1"></i> Rapor PDF
                                            </a>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 me-2">Rapor N/A</span>
                                        @endif

                                        <!-- Certificate Download / Eligibility -->
                                        @if($score->nilai_kehadiran >= 75)
                                            @if($cert)
                                                <a href="{{ route('certificates.download', $cert->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                                    <i class="bi bi-patch-check me-1"></i> Sertifikat PDF
                                                </a>
                                            @else
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3">Generating...</span>
                                            @endif
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3" title="Rasio kehadiran di bawah 75%">
                                                Tidak Eligible
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top p-3">
                    {{ $scores->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-file-earmark-x fs-1 mb-3 d-block text-secondary"></i>
                    <p class="mb-0">Belum ada rapor atau sertifikat yang diterbitkan untuk rombel/filter ini.</p>
                    <small class="text-muted">Finalisasi kelas di modul Penilaian terlebih dahulu.</small>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
