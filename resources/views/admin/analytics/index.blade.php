@extends('layouts.app')

@section('title', 'Dashboard Analitik')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Dashboard Analitik</h2>
                    <p class="text-muted mb-0">Rekapitulasi data siswa aktif per sekolah dan rombel.</p>
                </div>
                <!-- Filter Form -->
                <div class="d-flex gap-2">
                    <form id="filterForm" class="d-flex gap-2 align-items-center">
                        <select name="month" id="filterMonth" class="form-select" style="min-width: 150px;">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" id="filterYear" class="form-select">
                            <option value="" disabled>Pilih Tahun</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel-fill me-1"></i> Filter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase mb-2">Total Siswa Aktif</h6>
                    <h2 class="fw-bold text-primary mb-0 display-6" id="summaryTotalSiswa">-</h2>
                    <small class="text-muted">Periode Terpilih</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase mb-2">Total Sekolah</h6>
                    <h2 class="fw-bold text-success mb-0 display-6" id="summaryTotalSekolah">-</h2>
                    <small class="text-muted">Mitra Aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase mb-2">Total Rombel</h6>
                    <h2 class="fw-bold text-info mb-0 display-6" id="summaryTotalRombel">-</h2>
                    <small class="text-muted">Kelompok Belajar</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold">Top 10 Sekolah (Jumlah Siswa)</h5>
        </div>
        <div class="card-body">
            <canvas id="topSchoolsChart" style="max-height: 400px;"></canvas>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="card-title mb-0 fw-bold">Detail Data</h5>
            <button class="btn btn-sm btn-outline-success" onclick="exportTableToExcel('analyticsTable')">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="analyticsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Sekolah</th>
                            <th class="py-3">Program Ekskul</th>
                            <th class="py-3">Rombel</th>
                            <th class="text-center py-3 pe-4">Siswa Aktif</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <span class="spinner-border spinner-border-sm me-2"></span> Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const tableBody = document.getElementById('tableBody');
    let chartInstance = null;

    // Initial Load
    fetchData();

    // Filter Submit
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchData();
    });

    function fetchData() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);

        // Show loading
        tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="mt-2 text-muted small">Mengambil data terbaru...</div></td></tr>`;

        fetch(`{{ route('admin.analytics.data') }}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                updateSummary(data.summary);
                updateTable(data.table_data);
                updateChart(data.chart_data);
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-5"><i class="bi bi-exclamation-triangle display-6 d-block mb-3"></i>Gagal memuat data. Silakan coba lagi.</td></tr>`;
            });
    }

    function updateSummary(summary) {
        animateValue(document.getElementById('summaryTotalSiswa'), 0, summary.total_siswa, 1000);
        animateValue(document.getElementById('summaryTotalSekolah'), 0, summary.total_sekolah, 1000);
        animateValue(document.getElementById('summaryTotalRombel'), 0, summary.total_rombel, 1000);
    }

    function updateTable(data) {
        if (data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-5 text-muted"><i class="bi bi-inbox display-6 d-block mb-3 opacity-50"></i>Tidak ada data untuk periode ini.</td></tr>`;
            return;
        }

        let html = '';
        data.forEach(row => {
            html += `
                <tr>
                    <td class="fw-medium ps-4 text-dark">${row.sekolah_nama}</td>
                    <td class="text-secondary">${row.nama_program}</td>
                    <td><span class="badge bg-light text-dark border rounded-pill px-3">${row.nama_rombel}</span></td>
                    <td class="text-center fw-bold text-primary pe-4 fs-6">${row.total_siswa}</td>
                </tr>
            `;
        });
        tableBody.innerHTML = html;
    }

    function updateChart(data) {
        const ctx = document.getElementById('topSchoolsChart').getContext('2d');
        
        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah Siswa Aktif',
                    data: data.values,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)', // Bootstrap Primary
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverBackgroundColor: 'rgba(13, 110, 253, 0.9)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(33, 37, 41, 0.9)', // Dark tooltip
                        padding: 10,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e9ecef' // Light grid lines
                        },
                        ticks: {
                            color: '#6c757d',
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    }
                }
            }
        });
    }

    // Number Animation Helper
    function animateValue(obj, start, end, duration) {
        if (!obj) return;
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
});

// Excel Export (Keep existing logic)
window.exportTableToExcel = function(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    filename = filename?filename+'.xls':'analytics_data.xls';
    
    downloadLink = document.createElement("a");
    
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}
</script>
@endpush
@endsection
