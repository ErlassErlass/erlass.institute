
@if(Auth::user()->role !== 'instruktur')
    <!-- Admin Monthly Activity Trend -->
    @if(isset($chart_labels))
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-graph-up me-2 text-primary"></i>Tren Laporan</h5>
            <small class="text-muted">30 Hari Terakhir</small>
        </div>
        <div class="card-body p-4">
            <canvas id="activityChart" height="200"></canvas>
        </div>
    </div>
    @endif

    <!-- Admin Attendance Trend -->
    @if(isset($attendanceLabels))
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-people-fill me-2 text-success"></i>Tren Kehadiran</h5>
            <small class="text-muted">6 Bulan Terakhir</small>
        </div>
        <div class="card-body p-4" style="min-height: 250px; position: relative;">
            <canvas id="attendanceChart" style="max-height: 250px; width: 100%;"></canvas>
        </div>
    </div>
    @endif
@endif
