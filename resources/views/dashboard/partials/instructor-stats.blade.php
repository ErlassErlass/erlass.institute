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
