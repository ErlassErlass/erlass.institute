
<div class="row g-3 mb-4">
    <!-- Total Hours -->
    <div class="col-md-6 col-xl-4">
        @include('dashboard.partials.stat-card', [
            'bg' => 'bg-primary bg-gradient',
            'icon' => 'bi bi-clock-history',
            'title' => 'Total Jam Mengajar',
            'value' => $total_jam_mengajar . ' Jam',
            'subtitle' => 'Bulan Ini'
        ])
    </div>
    
    <!-- Reports Submitted -->
    <div class="col-md-6 col-xl-4">
        @include('dashboard.partials.stat-card', [
            'bg' => 'bg-success bg-gradient',
            'icon' => 'bi bi-file-earmark-check',
            'title' => 'Laporan Terkirim',
            'value' => $total_laporan_bulan_ini . ' Laporan',
            'subtitle' => 'Bulan Ini'
        ])
    </div>
    
    <!-- Next Class -->
    <div class="col-md-6 col-xl-4">
        <div class="card shadow-sm border-0 h-100 bg-info bg-gradient text-white">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-white bg-opacity-25 p-2 rounded-3 text-white me-3">
                        <i class="bi bi-calendar-event fs-4"></i>
                    </div>
                    <h6 class="card-subtitle text-white-50 mb-0">Kelas Berikutnya</h6>
                </div>
                @if($next_class)
                    <h5 class="card-title fw-bold mb-0 text-truncate">{{ \Carbon\Carbon::parse($next_class->tanggal_terjadwal)->translatedFormat('d M') }}, {{ \Carbon\Carbon::parse($next_class->jam_mulai_terjadwal)->format('H:i') }}</h5>
                    <small class="text-white-50 text-truncate d-block">{{ $next_class->rombel->ekstrakurikuler->sekolah->namasekolah }}</small>
                @else
                     <h5 class="card-title fw-bold mb-0">-</h5>
                     <small class="text-white-50">Belum ada jadwal</small>
                @endif
            </div>
        </div>
    </div>
</div>
