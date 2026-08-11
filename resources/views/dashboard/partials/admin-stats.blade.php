
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

@if(isset($corporate_punctuality))
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 bg-white rounded-3">
            <div class="card-body p-3 text-center d-flex flex-column justify-content-center">
                <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-shield-check fs-3"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Corporate Punctuality Rate</h6>
                <h2 class="display-6 fw-bold text-success mb-0">{{ $corporate_punctuality['corporate_rate'] }}%</h2>
                <small class="text-muted mt-1">Rata-rata Disiplin Instruktur (Bulan Ini)</small>
                <div class="mt-2 pt-2 border-top">
                    <span class="badge bg-success bg-opacity-10 text-success me-1">🟢 {{ $corporate_punctuality['on_time_count'] }} On Time</span>
                    <span class="badge bg-warning bg-opacity-10 text-warning">🟡 {{ $corporate_punctuality['late_count'] }} Terlambat</span>
                </div>
            </div>
        </div>
    </div>

    @if(isset($punctuality_leaderboard) && $punctuality_leaderboard->count() > 0)
    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100 bg-white rounded-3">
            <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-award me-1 text-warning"></i> Evaluasi Disiplin Instruktur</h6>
                <small class="text-muted">Status Check-in & Laporan H+1</small>
            </div>
            <div class="card-body p-0" style="max-height: 180px; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th class="ps-3">Instruktur</th>
                                <th class="text-center">Total Sesi</th>
                                <th class="text-center">Score On Time</th>
                                <th class="text-center">Status Disiplin</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach($punctuality_leaderboard->take(10) as $item)
                            <tr>
                                <td class="ps-3 fw-bold text-dark">{{ $item['nama_lengkap'] }}</td>
                                <td class="text-center">{{ $item['kpi']['total_laporan'] }}</td>
                                <td class="text-center fw-bold text-{{ $item['kpi']['badge_color'] }}">{{ $item['kpi']['punctuality_rate'] }}%</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $item['kpi']['badge_color'] }}">{{ $item['kpi']['status_label'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif
