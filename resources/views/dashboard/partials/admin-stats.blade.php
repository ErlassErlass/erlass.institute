
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary me-3">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <h6 class="card-subtitle text-muted mb-0">Total Sekolah</h6>
                </div>
                <h3 class="card-title fw-bold mb-0 text-dark">{{ number_format($total_sekolah) }}</h3>
                <small class="text-success"><i class="bi bi-arrow-up-short"></i> Mitra Aktif</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-indigo bg-opacity-10 p-2 rounded-3 text-indigo me-3" style="color: #6610f2;">
                        <i class="bi bi-collection fs-4"></i>
                    </div>
                    <h6 class="card-subtitle text-muted mb-0">Total Rombel</h6>
                </div>
                <h3 class="card-title fw-bold mb-0 text-dark">{{ number_format($total_rombel) }}</h3>
                <small class="text-success"><i class="bi bi-arrow-up-short"></i> Kelompok Belajar</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success me-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <h6 class="card-subtitle text-muted mb-0">Total Siswa</h6>
                </div>
                <h3 class="card-title fw-bold mb-0 text-dark">{{ number_format($total_siswa) }}</h3>
                <small class="text-success"><i class="bi bi-arrow-up-short"></i> Terdaftar</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-6 col-xl-2">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning me-3">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                    <h6 class="card-subtitle text-muted mb-0">Laporan Hari Ini</h6>
                </div>
                <h3 class="card-title fw-bold mb-0 text-dark">{{ $laporan_hari_ini }}</h3>
                <small class="text-muted">{{ now()->format('d M Y') }}</small>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info me-3">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                    <h6 class="card-subtitle text-muted mb-0">Total Instruktur</h6>
                </div>
                <h3 class="card-title fw-bold mb-0 text-dark">{{ number_format($total_instruktur) }}</h3>
                <small class="text-success"><i class="bi bi-check-circle"></i> Terverifikasi</small>
            </div>
        </div>
    </div>
</div>
