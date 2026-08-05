@extends('layouts.app')

@section('title', 'Distribusi Jadwal Instruktur')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            
    <!-- Hero Header -->
    <div class="card border-0 mb-4 text-white overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #2563EB 100%); border-radius: 16px;">
        <div class="card-body p-4 position-relative">
            <div class="row align-items-center g-3">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge border border-white border-opacity-25 text-white rounded-pill px-3 py-1.5 text-uppercase small fw-semibold" style="background: rgba(255, 255, 255, 0.18);">
                            <i class="bi bi-bar-chart-steps me-1"></i> Analytics & Penjadwalan
                        </span>
                    </div>
                    <h1 class="h2 fw-bold text-white mb-2">Distribusi Jadwal Instruktur</h1>
                    <p class="mb-0" style="color: rgba(255, 255, 255, 0.92); font-size: 0.95rem;">
                        Analisis pemerataan beban mengajar & distribusi jadwal seluruh instruktur Erlass Institute.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="row g-2 justify-content-lg-end">
                        <div class="col-6 col-sm-4">
                            <div class="p-3 text-center rounded-3" style="background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.25);">
                                <div class="small fw-semibold mb-1" style="color: rgba(255, 255, 255, 0.88);">Rata-rata Sesi</div>
                                <div class="fs-4 fw-bold text-white">{{ $average_sessions }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="p-3 text-center rounded-3" style="background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.25);">
                                <div class="small fw-semibold mb-1" style="color: rgba(255, 255, 255, 0.88);">Total Instruktur</div>
                                <div class="fs-4 fw-bold text-white">{{ $instructors->count() }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="p-3 text-center rounded-3" style="background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.25);">
                                <div class="small fw-semibold mb-1" style="color: rgba(255, 255, 255, 0.88);">Perlu Tambahan</div>
                                <div class="fs-4 fw-bold text-warning">{{ count($recommended_instructors) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            
            <div class="row mb-4">
                <!-- Chart Section -->
                <div class="col-md-8 mb-4 mb-md-0">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-bottom px-4 py-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-bar-chart-line me-2 text-info"></i>Grafik Distribusi</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="distributionChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recommendations Section -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-bottom px-4 py-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-lightbulb me-2 text-warning"></i>Rekomendasi Jadwal</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-soft-info border-0 d-flex align-items-center mb-4 p-3 rounded-3" style="background-color: rgba(13, 202, 240, 0.1);">
                                <i class="bi bi-info-circle-fill me-3 fs-4 text-info"></i>
                                <div>
                                    <strong class="d-block text-dark">Rata-rata: {{ $average_sessions }} Sesi</strong>
                                    <small class="text-muted">Instruktur di bawah ini dianjurkan mendapat jadwal tambahan.</small>
                                </div>
                            </div>

                            <div class="list-group list-group-flush overflow-auto custom-scrollbar" style="max-height: 280px; padding-right: 5px;">
                                @forelse($recommended_instructors as $rec)
                                    @php
                                        $isZero = $rec->ekstrakurikuler_sessions_count == 0;
                                    @endphp
                                    <div class="list-group-item px-3 py-2 d-flex justify-content-between align-items-center border-0 mb-2 rounded-3 hover-bg-light transition-all">
                                        <div class="d-flex align-items-center">
                                            <div class="position-relative">
                                                <div class="avatar-circle me-3 {{ $isZero ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning-emphasis' }} d-flex align-items-center justify-content-center fw-bold rounded-circle shadow-sm" style="width: 36px; height: 36px; font-size: 14px;">
                                                    {{ substr($rec->nama_lengkap, 0, 1) }}
                                                </div>
                                                @if($isZero)
                                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                                    <span class="visually-hidden">Critical</span>
                                                </span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 130px; font-size: 0.95rem;">
                                                    {{ $rec->nama_lengkap }}
                                                </div>
                                                @if($isZero)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill" style="font-size: 0.65rem;">Belum ada jadwal</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill" style="font-size: 0.65rem;">Di bawah rata-rata</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                             <span class="d-block fw-bold fs-6 {{ $isZero ? 'text-danger' : 'text-dark' }}">{{ $rec->ekstrakurikuler_sessions_count }}</span>
                                             <small class="text-muted" style="font-size: 0.7rem;">Sesi</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="bi bi-check-circle-fill fs-1 text-success opacity-75"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">Luar Biasa!</h6>
                                        <p class="text-muted small mb-0">Distribusi jadwal sudah merata.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-people me-2 text-primary"></i>Detail Distribusi</h5>
                                <small class="text-muted">Periode: {{ $period_start->format('d M') }} - {{ $period_end->format('d M Y') }}</small>
                            </div>
                            <a href="{{ route('admin.analytics.schedule-distribution.export') }}" class="btn btn-success btn-sm text-white d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Instruktur</th>
                                            <th>Domisili</th>
                                            <th class="text-center pe-4">Total Sesi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Calculate max sessions for progress bar scale
                                            $max_sessions = $instructors->max('ekstrakurikuler_sessions_count') ?: 1;
                                        @endphp
                                        @forelse($instructors as $instr)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center fw-bold rounded-circle" style="width: 32px; height: 32px; font-size: 14px;">
                                                            {{ substr($instr->nama_lengkap, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark">{{ $instr->nama_lengkap }}</div>
                                                            <small class="text-muted">{{ $instr->instructor_id ?? '-' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $instr->instructorProfile->kota_domisili ?? '-' }}
                                                    </small>
                                                </td>
                                                <td class="pe-4">
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <div class="progress flex-grow-1 me-3" style="height: 6px; max-width: 100px;">
                                                            <div class="progress-bar rounded-pill" role="progressbar" 
                                                                 style="width: {{ ($instr->ekstrakurikuler_sessions_count / $max_sessions) * 100 }}%;
                                                                        background-color: #0d6efd;
                                                                        "
                                                                 aria-valuenow="{{ $instr->ekstrakurikuler_sessions_count }}" aria-valuemin="0" aria-valuemax="{{ $max_sessions }}">
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-primary rounded-pill">{{ $instr->ekstrakurikuler_sessions_count }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">
                                                    <i class="bi bi-info-circle fs-4 mb-2 d-block"></i>
                                                    Belum ada data instruktur yang ditemukan.<br>
                                                    <small>Filter: Teaching Staff (ID >= 48), Status: Active/Aktif</small>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('distributionChart').getContext('2d');
            
            // Data from Controller
            const labels = @json($chart_data['labels']);
            const data = @json($chart_data['data']);
            const average = {{ $average_sessions }};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Sesi',
                        data: data,
                        backgroundColor: data.map(val => val < average ? 'rgba(255, 99, 132, 0.7)' : 'rgba(54, 162, 235, 0.7)'),
                        borderColor: data.map(val => val < average ? 'rgb(255, 99, 132)' : 'rgb(54, 162, 235)'),
                        borderWidth: 1,
                        borderRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y + ' Sesi';
                                    }
                                    return label;
                                }
                            }
                        },
                        annotation: {
                            annotations: {
                                line1: {
                                    type: 'line',
                                    yMin: average,
                                    yMax: average,
                                    borderColor: 'rgba(255, 159, 64, 0.8)',
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    label: {
                                        content: 'Rata-rata: ' + average,
                                        enabled: true,
                                        position: 'end'
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: '#f3f4f6' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { autoSkip: false, maxRotation: 90, minRotation: 90 } 
                        }
                    }
                }
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        .hover-bg-light:hover { background-color: #f8f9fa; }
        .transition-all { transition: all 0.2s ease; }
        
        /* Custom Scrollbar for the list */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
    </style>
    @endpush
@endsection
