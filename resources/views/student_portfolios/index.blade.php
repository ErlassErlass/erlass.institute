@extends('layouts.app')

@section('title', 'Portofolio Siswa')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 fw-bold text-dark mb-1">Portofolio Siswa</h1>
            <p class="text-muted mb-0">Pilih Rombel / Kelas untuk mengelola portofolio karya digital siswa.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-folder-fill text-primary me-2"></i>Daftar Rombel Portofolio</h5>
        </div>
        <div class="card-body p-0">
            @if($rombels->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 30%;">Sekolah & Rombel</th>
                                <th style="width: 25%;">Program Ekskul</th>
                                <th style="width: 25%;">Instruktur</th>
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
                                        <span class="small fw-semibold text-dark">{{ $rombel->instruktur->nama_lengkap ?? 'Belum Ditugaskan' }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('student-portfolios.rombel', $rombel->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                            <i class="bi bi-folder-open me-1"></i> Buka Portofolio
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-folder-x fs-1 mb-3 d-block text-secondary"></i>
                    <p class="mb-0">Tidak ada rombel aktif yang tersedia.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
