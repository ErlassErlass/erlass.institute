<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="impeccable-stat-card h-100">
            <div class="d-flex align-items-center mb-2">
                <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary me-2">
                    <i class="bi bi-building"></i>
                </div>
                <h6 class="text-muted mb-0 small fw-bold">Total Sekolah</h6>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ number_format($total_sekolah) }}</h3>
            <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Mitra Aktif</small>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-3">
        <div class="impeccable-stat-card accent-violet h-100">
            <div class="d-flex align-items-center mb-2">
                <div class="stat-icon-wrapper bg-indigo bg-opacity-10 text-indigo me-2" style="color: #6366f1;">
                    <i class="bi bi-collection"></i>
                </div>
                <h6 class="text-muted mb-0 small fw-bold">Total Rombel</h6>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ number_format($total_rombel) }}</h3>
            <small class="text-indigo fw-semibold" style="color: #6366f1;"><i class="bi bi-people-fill me-1"></i>Kelompok Belajar</small>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="impeccable-stat-card accent-emerald h-100">
            <div class="d-flex align-items-center mb-2">
                <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success me-2">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h6 class="text-muted mb-0 small fw-bold">Total Siswa</h6>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ number_format($total_siswa) }}</h3>
            <small class="text-success fw-semibold"><i class="bi bi-graph-up-arrow me-1"></i>Terdaftar</small>
        </div>
    </div>

    <div class="col-6 col-md-6 col-xl-2">
        <div class="impeccable-stat-card accent-amber h-100">
            <div class="d-flex align-items-center mb-2">
                <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning me-2">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h6 class="text-muted mb-0 small fw-bold">Laporan Hari Ini</h6>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ $laporan_hari_ini }}</h3>
            <small class="text-muted fw-semibold"><i class="bi bi-clock me-1"></i>{{ now()->format('d M Y') }}</small>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="impeccable-stat-card accent-rose h-100">
            <div class="d-flex align-items-center mb-2">
                <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info me-2">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <h6 class="text-muted mb-0 small fw-bold">Total Instruktur</h6>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ number_format($total_instruktur) }}</h3>
            <small class="text-success fw-semibold"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi</small>
        </div>
    </div>
</div>

@if(isset($corporate_punctuality))
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 bg-white rounded-4 overflow-hidden" style="border-top: 4px solid #10b981 !important;">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center shadow-xs" style="width: 56px; height: 56px;">
                    <i class="bi bi-shield-check fs-2"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Corporate Punctuality Rate</h6>
                <h2 class="display-6 fw-bold text-success mb-0">{{ $corporate_punctuality['corporate_rate'] }}%</h2>
                <small class="text-muted mt-1">Rata-rata Disiplin Instruktur (Bulan Ini)</small>
                <div class="mt-3 pt-3 border-top d-flex justify-content-center gap-2">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">🟢 {{ $corporate_punctuality['on_time_count'] }} On Time</span>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1">🟡 {{ $corporate_punctuality['late_count'] }} Terlambat</span>
                </div>
            </div>
        </div>
    </div>

    @if(isset($punctuality_leaderboard) && $punctuality_leaderboard->count() > 0)
    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100 bg-white rounded-4 overflow-hidden" style="border-top: 4px solid #3b82f6 !important;">
            <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-trophy-fill me-2 text-warning fs-5"></i> Evaluasi Disiplin Instruktur</h6>
                <small class="text-muted fw-semibold">Status Check-in & Laporan H+1</small>
            </div>
            <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th class="ps-4">Rank & Instruktur</th>
                                <th class="text-center">Total Sesi</th>
                                <th class="text-center">Score On Time</th>
                                <th class="text-center pe-4">Status Disiplin</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach($punctuality_leaderboard->take(10) as $idx => $item)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    @if($idx === 0)
                                        <span class="me-1">🥇</span>
                                    @elseif($idx === 1)
                                        <span class="me-1">🥈</span>
                                    @elseif($idx === 2)
                                        <span class="me-1">🥉</span>
                                    @else
                                        <span class="badge bg-light text-muted me-2 border">{{ $idx + 1 }}</span>
                                    @endif
                                    {{ $item['nama_lengkap'] }}
                                </td>
                                <td class="text-center fw-semibold text-secondary">{{ $item['kpi']['total_laporan'] }}</td>
                                <td class="text-center fw-bold text-{{ $item['kpi']['badge_color'] }}">{{ $item['kpi']['punctuality_rate'] }}%</td>
                                <td class="text-center pe-4">
                                    <span class="badge bg-{{ $item['kpi']['badge_color'] }}-subtle text-{{ $item['kpi']['badge_color'] }} border border-{{ $item['kpi']['badge_color'] }} rounded-pill px-3 py-1">
                                        {{ $item['kpi']['status_label'] }}
                                    </span>
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
