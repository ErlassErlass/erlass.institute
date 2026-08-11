<div class="row g-3 mb-4">
    <!-- Total Hours -->
    <div class="col-6 col-md-6 col-xl-3">
        @include('dashboard.partials.stat-card', [
            'bg' => 'bg-primary bg-gradient',
            'icon' => 'bi bi-clock-history',
            'title' => 'Total Jam Mengajar',
            'value' => $total_jam_mengajar . ' Jam',
            'subtitle' => 'Cutoff: ' . ($cutoff_label ?? 'Bulan Ini')
        ])
    </div>
    
    <!-- Reports Submitted -->
    <div class="col-6 col-md-6 col-xl-3">
        @include('dashboard.partials.stat-card', [
            'bg' => 'bg-success bg-gradient',
            'icon' => 'bi bi-file-earmark-check',
            'title' => 'Laporan Terkirim',
            'value' => $total_laporan_bulan_ini . ' Laporan',
            'subtitle' => 'Cutoff: ' . ($cutoff_label ?? 'Bulan Ini')
        ])
    </div>

    <!-- Estimated Earnings (AOQCS Pillar 6) -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100 bg-warning bg-gradient text-dark">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-dark bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-wallet2 fs-4 text-dark"></i>
                        </div>
                        <h6 class="card-subtitle text-dark mb-0 fw-semibold">Estimasi Honor</h6>
                    </div>
                    <h5 class="card-title fw-bold mb-1">Rp {{ number_format($estimated_earnings, 0, ',', '.') }}</h5>
                </div>
                <div class="mt-2 pt-2 border-top border-dark border-opacity-10 d-flex justify-content-between align-items-center">
                    <span class="small text-dark-50">Cutoff: {{ $cutoff_label ?? 'Bulan Ini' }}</span>
                    @if($total_penalties > 0)
                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 small" data-bs-toggle="tooltip" title="Potongan keterlambatan">
                            -Rp {{ number_format($total_penalties, 0, ',', '.') }}
                        </span>
                    @else
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 small">
                            <i class="bi bi-shield-check me-1"></i>Aman
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Next Class -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100 bg-info bg-gradient text-white">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-white bg-opacity-25 p-2 rounded-3 text-white me-3">
                        <i class="bi bi-calendar-event fs-4"></i>
                    </div>
                    <h6 class="card-subtitle mb-0" style="color: rgba(255, 255, 255, 0.88);">Kelas Berikutnya</h6>
                </div>
                @if($next_class)
                    <h5 class="card-title fw-bold mb-0 text-truncate">{{ \Carbon\Carbon::parse($next_class->tanggal_terjadwal)->translatedFormat('d M') }}, {{ \Carbon\Carbon::parse($next_class->jam_mulai_terjadwal)->format('H:i') }}</h5>
                    <small class="text-truncate d-block" style="color: rgba(255, 255, 255, 0.88);">{{ $next_class->rombel->ekstrakurikuler->sekolah->namasekolah }}</small>
                @else
                     <h5 class="card-title fw-bold mb-0">-</h5>
                     <small style="color: rgba(255, 255, 255, 0.88);">Belum ada jadwal</small>
                @endif
            </div>
        </div>
    </div>
</div>

@if(isset($punctuality_kpi))
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-white rounded-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Status Check-in & Personal Punctuality KPI</h6>
                            <small class="text-muted">Kombinasi Kehadiran di Sekolah & Input Laporan (H+1)</small>
                        </div>
                    </div>
                    <span class="badge bg-{{ $punctuality_kpi['badge_color'] }} fs-6 px-3 py-2 rounded-pill shadow-sm">
                        {{ $punctuality_kpi['punctuality_rate'] }}% On Time ({{ $punctuality_kpi['status_label'] }})
                    </span>
                </div>

                <!-- Progress Bar -->
                <div class="progress mb-3" style="height: 10px; border-radius: 5px;">
                    <div class="progress-bar bg-success" style="width: {{ $punctuality_kpi['punctuality_rate'] }}%" title="On Time"></div>
                    <div class="progress-bar bg-warning" style="width: {{ round(($punctuality_kpi['late_report_count'] / max(1, $punctuality_kpi['total_laporan'])) * 100) }}%" title="Late Report"></div>
                    <div class="progress-bar bg-info" style="width: {{ round(($punctuality_kpi['late_arrival_count'] / max(1, $punctuality_kpi['total_laporan'])) * 100) }}%" title="Late Arrival"></div>
                    <div class="progress-bar bg-danger" style="width: {{ round(($punctuality_kpi['late_both_count'] / max(1, $punctuality_kpi['total_laporan'])) * 100) }}%" title="Late Both"></div>
                </div>

                <!-- Breakdown -->
                <div class="row text-center g-2">
                    <div class="col-3">
                        <div class="p-2 bg-success bg-opacity-10 rounded">
                            <span class="d-block fw-bold text-success fs-6">{{ $punctuality_kpi['on_time_count'] }}</span>
                            <span class="small text-muted" style="font-size: 0.75rem;">🟢 Sempurna</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 bg-warning bg-opacity-10 rounded">
                            <span class="d-block fw-bold text-warning fs-6">{{ $punctuality_kpi['late_report_count'] }}</span>
                            <span class="small text-muted" style="font-size: 0.75rem;">🟡 Late Report</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 bg-info bg-opacity-10 rounded">
                            <span class="d-block fw-bold text-info fs-6">{{ $punctuality_kpi['late_arrival_count'] }}</span>
                            <span class="small text-muted" style="font-size: 0.75rem;">🟠 Late Arrival</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 bg-danger bg-opacity-10 rounded">
                            <span class="d-block fw-bold text-danger fs-6">{{ $punctuality_kpi['late_both_count'] }}</span>
                            <span class="small text-muted" style="font-size: 0.75rem;">🔴 Late Both</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
