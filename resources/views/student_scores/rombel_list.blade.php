@extends('layouts.app')

@section('title', 'Daftar Kelas Penilaian')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 fw-bold text-dark mb-1">Penilaian Ekstrakurikuler</h1>
            <p class="text-muted mb-0">Pilih Rombel / Kelas untuk mulai menginput nilai siswa.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-journal-bookmark text-primary me-2"></i>Daftar Rombel Aktif</h5>
        </div>
        <div class="card-body p-0">
            @if($rombels->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 25%;">Sekolah & Rombel</th>
                                <th style="width: 20%;">Program Ekskul</th>
                                <th style="width: 20%;">Instruktur</th>
                                <th style="width: 15%; text-align: center;">Progress</th>
                                <th class="text-end pe-4" style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rombels as $rombel)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $rombel->ekstrakurikuler->sekolah->namasekolah }}</div>
                                        <small class="text-muted">{{ $rombel->nama_rombel }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                                            {{ $rombel->ekstrakurikuler->kategori_program }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 11px;">
                                                <strong>{{ substr($rombel->instruktur->nama_lengkap ?? 'I', 0, 1) }}</strong>
                                            </div>
                                            <span class="small fw-semibold text-dark">{{ $rombel->instruktur->nama_lengkap ?? 'Belum Ditugaskan' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center flex-column">
                                            <span class="small fw-bold mb-1">{{ $rombel->pertemuan_selesai }} / {{ $rombel->total_pertemuan }} Sesi</span>
                                            <div class="progress" style="height: 5px; width: 80px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $rombel->getProgressPersentase() }}%;" aria-valuenow="{{ $rombel->getProgressPersentase() }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('student-scores.index', $rombel->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
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
                    <i class="bi bi-journal-x fs-1 mb-3 d-block text-secondary"></i>
                    <p class="mb-0">Tidak ada rombel aktif yang ditugaskan kepada Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
