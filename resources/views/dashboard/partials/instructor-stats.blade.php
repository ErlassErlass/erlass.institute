<div class="row g-3 mb-4" id="tour-instructor-earnings">
    <!-- Total Hours -->
    <div class="col-6 col-md-6 col-xl-3">
        <div class="impeccable-stat-card h-100">
            <div class="d-flex align-items-center mb-2">
                <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary me-2">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h6 class="text-muted mb-0 small fw-bold">Total Jam Mengajar</h6>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ $total_jam_mengajar }} Jam</h3>
            <small class="text-muted fw-semibold">Cutoff: {{ $cutoff_label ?? 'Bulan Ini' }}</small>
        </div>
    </div>
    
    <!-- Reports Submitted -->
    <div class="col-6 col-md-6 col-xl-3">
        <div class="impeccable-stat-card accent-emerald h-100">
            <div class="d-flex align-items-center mb-2">
                <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success me-2">
                    <i class="bi bi-file-earmark-check"></i>
                </div>
                <h6 class="text-muted mb-0 small fw-bold">Laporan Terkirim</h6>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ $total_laporan_bulan_ini }} Laporan</h3>
            <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Cutoff: {{ $cutoff_label ?? 'Bulan Ini' }}</small>
        </div>
    </div>

    <!-- Estimated Earnings (AOQCS Pillar 6) -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="impeccable-stat-card accent-amber h-100">
            <div class="d-flex align-items-center mb-2">
                <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning me-2">
                    <i class="bi bi-wallet2"></i>
                </div>
                <h6 class="text-muted mb-0 small fw-bold">Estimasi Honor</h6>
            </div>
            <h3 class="fw-bold mb-1 text-dark">Rp {{ number_format($estimated_earnings, 0, ',', '.') }}</h3>
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <small class="text-muted">Cutoff: {{ $cutoff_label ?? 'Bulan Ini' }}</small>
                @if($total_penalties > 0)
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle small" data-bs-toggle="tooltip" title="Potongan keterlambatan">
                        -Rp {{ number_format($total_penalties, 0, ',', '.') }}
                    </span>
                @else
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle small">
                        <i class="bi bi-shield-check me-1"></i>Aman
                    </span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Next Class -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="impeccable-stat-card accent-violet h-100">
            <div class="d-flex align-items-center mb-2">
                <div class="stat-icon-wrapper bg-indigo bg-opacity-10 me-2" style="color: #8b5cf6;">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <h6 class="text-muted mb-0 small fw-bold">Kelas Berikutnya</h6>
            </div>
            @if($next_class)
                <h4 class="fw-bold mb-1 text-dark text-truncate">{{ \Carbon\Carbon::parse($next_class->tanggal_terjadwal)->translatedFormat('d M') }}, {{ \Carbon\Carbon::parse($next_class->jam_mulai_terjadwal)->format('H:i') }}</h4>
                <small class="text-muted text-truncate d-block fw-semibold"><i class="bi bi-building me-1"></i>{{ $next_class->rombel->ekstrakurikuler->sekolah->namasekolah }}</small>
            @else
                 <h4 class="fw-bold mb-1 text-muted">-</h4>
                 <small class="text-muted fw-semibold">Belum ada jadwal berikutnya</small>
            @endif
        </div>
    </div>
</div>

@if(isset($punctuality_kpi))
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-white rounded-4 overflow-hidden" style="border-top: 4px solid #3b82f6 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle">
                            <i class="bi bi-clock-history fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Status Check-in & Personal Punctuality KPI</h6>
                            <small class="text-muted">Kombinasi Kehadiran di Sekolah & Input Laporan (H+1)</small>
                        </div>
                    </div>
                    <span class="badge bg-{{ $punctuality_kpi['badge_color'] }}-subtle text-{{ $punctuality_kpi['badge_color'] }} border border-{{ $punctuality_kpi['badge_color'] }} fs-6 px-3 py-2 rounded-pill shadow-xs">
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
                        <div class="p-2 bg-success bg-opacity-10 rounded-3">
                            <span class="d-block fw-bold text-success fs-6">{{ $punctuality_kpi['on_time_count'] }}</span>
                            <span class="small text-muted" style="font-size: 0.75rem;">🟢 Sempurna</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 bg-warning bg-opacity-10 rounded-3">
                            <span class="d-block fw-bold text-warning fs-6">{{ $punctuality_kpi['late_report_count'] }}</span>
                            <span class="small text-muted" style="font-size: 0.75rem;">🟡 Late Report</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 bg-info bg-opacity-10 rounded-3">
                            <span class="d-block fw-bold text-info fs-6">{{ $punctuality_kpi['late_arrival_count'] }}</span>
                            <span class="small text-muted" style="font-size: 0.75rem;">🟠 Late Arrival</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 bg-danger bg-opacity-10 rounded-3">
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
