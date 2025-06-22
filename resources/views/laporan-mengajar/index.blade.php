@extends('layouts.app')

@section('title', 'Daftar Laporan Mengajar')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card {
        border-radius: 8px;
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        font-size: 2rem;
        opacity: 0.7;
    }
    .quick-filter-btn {
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 0.85rem;
    }
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .badge-custom {
        padding: 5px 10px;
        font-weight: normal;
        font-size: 0.8rem;
    }
    .export-btn-group .btn {
        border-radius: 5px !important;
    }
    /* Add to your styles section */
.clear-date {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    display: none;
    padding: 0 8px;
}

.input-group {
    position: relative;
}

/* Active filter alert */
.alert-info {
    border-left: 4px solid #0dcaf0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .filter-section .col-md-3, 
    .filter-section .col-md-2 {
        margin-bottom: 1rem;
    }
    
    .quick-filter-btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-1">Daftar Laporan Mengajar</h1>
                    <p class="text-muted mb-0">Tinjau semua laporan kegiatan mengajar yang telah dibuat</p>
                </div>
                @can('create', App\Models\LaporanMengajar::class)
                    <a href="{{ route('laporan-mengajar.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Buat Laporan
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Laporan</h6>
                            <h3 class="mb-0">{{ $totalLaporan }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-journal-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Minggu Ini</h6>
                            <h3 class="mb-0">{{ $laporanMingguIni }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Bulan Ini</h6>
                            <h3 class="mb-0">{{ $laporanBulanIni }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-calendar-month"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Instruktur Aktif</h6>
                            <h3 class="mb-0">{{ $totalInstruktur }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Filter Section -->
<div class="card shadow-sm mb-4" id="filterCard">
    <div class="card-body">
        <form method="GET" action="{{ route('laporan-mengajar.index') }}" id="filterForm">
                @csrf <!-- Tambahkan ini -->
                @if(request('page'))
        <input type="hidden" name="page" value="{{ request('page') }}">
    @endif
            <div class="row g-3">
                <!-- Role-based filters -->
                @if(in_array(Auth::user()->role, ['admin', 'admin_erlass']))
                <div class="col-md-3">
                    <label for="instruktur_id" class="form-label">Instruktur</label>
                    <select name="instruktur_id" id="instruktur_id" class="form-select">
                        <option value="">Semua Instruktur</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" 
                                {{ request('instruktur_id') == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Date Range Picker -->
                <div class="col-md-3">
                    <label for="date_range" class="form-label">Rentang Tanggal</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        <input type="text" name="date_range" id="date_range" class="form-control" 
                            placeholder="Pilih rentang" value="{{ request('date_range') }}">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="col-md-2">
                    <label for="kategori" class="form-label">Kategori</label>
                <select name="kategori" id="kategori" class="form-select">
                    <option value="">Semua</option>
                    <option value="Reguler" {{ request('kategori') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                    <option value="Remedial" {{ request('kategori') == 'Remedial' ? 'selected' : '' }}>Remedial</option>
                    <option value="Pengayaan" {{ request('kategori') == 'Pengayaan' ? 'selected' : '' }}>Pengayaan</option>
                </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-12 mt-2">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                        
                        <!-- Quick Filters -->
                        <div class="btn-group ms-auto">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-lightning-charge"></i> Filter Cepat
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item quick-filter" href="#" data-days="1">Hari Ini</a></li>
                                <li><a class="dropdown-item quick-filter" href="#" data-days="7">7 Hari Terakhir</a></li>
                                <li><a class="dropdown-item quick-filter" href="#" data-days="30">30 Hari Terakhir</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item quick-filter" href="#" data-month="current">Bulan Ini</a></li>
                                <li><a class="dropdown-item quick-filter" href="#" data-month="last">Bulan Lalu</a></li>
                            </ul>
                        </div>
                        
                        <!-- Export Buttons -->
                        <div class="btn-group export-btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item export-link" href="#" data-format="excel">Excel</a></li>
                                <li><a class="dropdown-item export-link" href="#" data-format="pdf">PDF</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

    <!-- Main Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Instruktur</th>
                            <th>Sekolah</th>
                            <th>Rombel</th>
                            <th>Kategori</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporan as $item)
                            <tr>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($item->jadwal_mengajar)->isoFormat('D MMMM Y') }}</div>
                                    <small class="text-muted">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</small>
                                </td>
                                <td>
                                    <div>{{ $item->instruktur->nama_lengkap ?? 'N/A' }}</div>
                                    @if($item->asisten)
                                        <small class="text-muted">Asisten: {{ $item->asisten->nama_lengkap }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $item->sekolah->namasekolah ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $item->sekolah->kec ?? '' }}, {{ $item->sekolah->kotkab ?? '' }}</small>
                                </td>
                                <td>{{ $item->rombel }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'Reguler' => 'bg-primary',
                                            'Remedial' => 'bg-warning text-dark',
                                            'Pengayaan' => 'bg-info text-dark'
                                        ][$item->kategori_pengajaran] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} badge-custom">{{ $item->kategori_pengajaran }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('view', $item)
                                            <a href="{{ route('laporan-mengajar.show', $item) }}" class="btn btn-sm btn-info" title="Lihat Detail" data-bs-toggle="tooltip">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('laporan-mengajar.absensi.create', $item) }}" class="btn btn-sm btn-success" title="Input Absensi" data-bs-toggle="tooltip">
                                                <i class="bi bi-person-check"></i>
                                            </a>
                                        @endcan

                                        @can('update', $item)
                                            <a href="{{ route('laporan-mengajar.edit', $item) }}" class="btn btn-sm btn-warning" title="Edit" data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan

                                        @can('delete', $item)
                                            <form action="{{ route('laporan-mengajar.destroy', $item) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="tooltip">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-journal-x" style="font-size: 2rem;"></i>
                                        <h5 class="mt-2">Belum ada laporan yang dibuat</h5>
                                        @can('create', App\Models\LaporanMengajar::class)
                                            <a href="{{ route('laporan-mengajar.create') }}" class="btn btn-primary mt-2">
                                                Buat Laporan Pertama Anda
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($laporan->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Menampilkan {{ $laporan->firstItem() }} sampai {{ $laporan->lastItem() }} dari {{ $laporan->total() }} entri
                    </div>
                    {{ $laporan->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
// Ganti kode flatpickr initialization dengan ini:
flatpickr("#date_range", {
    mode: "range",
    locale: "id",
    dateFormat: "d/m/Y",
    rangeSeparator: " to ",
    defaultDate: @json(request('date_range') ? explode(' to ', request('date_range')) : null),
    allowInput: true,
    onReady: function(selectedDates, dateStr, instance) {
        // Add clear button
        instance.clearButton = instance._input.parentNode.querySelector('.clear-date');
        if (!instance.clearButton) {
            instance.clearButton = document.createElement('button');
            instance.clearButton.className = 'btn btn-sm btn-outline-secondary clear-date';
            instance.clearButton.innerHTML = '<i class="bi bi-x"></i>';
            instance.clearButton.type = 'button';
            instance.clearButton.addEventListener('click', function() {
                instance.clear();
                document.getElementById('filterForm').submit();
            });
            instance._input.parentNode.appendChild(instance.clearButton);
        }
        
        if (instance.input.value) {
            instance.clearButton.style.display = 'block';
        }
    },
    onChange: function(selectedDates, dateStr, instance) {
        if (dateStr) {
            instance.clearButton.style.display = 'block';
        } else {
            instance.clearButton.style.display = 'none';
        }
    },
    onClose: function(selectedDates, dateStr, instance) {
        // Only submit if we have a complete range
        if (selectedDates.length === 2) {
            document.getElementById('filterForm').submit();
        }
    }
});

    // Quick filter buttons
    document.querySelectorAll('.quick-filter').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const days = this.dataset.days;
            const month = this.dataset.month;
            
            let url = new URL(window.location.href);
            
            // Clear existing date filters
            url.searchParams.delete('date_range');
            
            if (days) {
                const endDate = new Date();
                const startDate = new Date();
                startDate.setDate(endDate.getDate() - parseInt(days));
                
                url.searchParams.set('date_range', 
                    formatDate(startDate) + ' to ' + formatDate(endDate));
            } else if (month === 'current') {
                const startDate = new Date();
                startDate.setDate(1);
                const endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0);
                
                url.searchParams.set('date_range', 
                    formatDate(startDate) + ' to ' + formatDate(endDate));
            } else if (month === 'last') {
                const startDate = new Date();
                startDate.setMonth(startDate.getMonth() - 1);
                startDate.setDate(1);
                const endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0);
                
// Di dalam quick filter event listener
            url.searchParams.set('date_range', 
                formatDate(startDate) + ' to ' + formatDate(endDate));
            }
            
            window.location.href = url.toString();
        });
    });

    // Export links
// Export links
document.querySelectorAll('.export-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const format = this.dataset.format;
        
        // Get current URL with query parameters
        let url = new URL(window.location.href);
        let params = new URLSearchParams(url.search);
        
        // Construct the export URL with all current filters
        let exportUrl = "{{ route('laporan-mengajar.export', ['format' => 'FORMAT_PLACEHOLDER']) }}";
        exportUrl = exportUrl.replace('FORMAT_PLACEHOLDER', format) + '?' + params.toString();
        
        window.location.href = exportUrl;
    });
});

    // Format date helper
    function formatDate(date) {
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    // Show active filters
    function showActiveFilters() {
        const activeFilters = [];
        const params = new URLSearchParams(window.location.search);
        
        if (params.get('instruktur_id')) {
            const instructorName = document.querySelector('#instruktur_id option[value="' + params.get('instruktur_id') + '"]')?.text;
            if (instructorName) {
                activeFilters.push(`Instruktur: ${instructorName}`);
            }
        }
        
        if (params.get('date_range')) {
            activeFilters.push(`Tanggal: ${params.get('date_range')}`);
        }
        
        if (params.get('kategori')) {
            activeFilters.push(`Kategori: ${params.get('kategori')}`);
        }
        
        if (activeFilters.length > 0) {
            const filterAlert = document.createElement('div');
            filterAlert.className = 'alert alert-info alert-dismissible fade show mb-3';
            filterAlert.innerHTML = `
                <strong><i class="bi bi-funnel me-1"></i> Filter Aktif:</strong>
                ${activeFilters.join(' • ')}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.querySelector('#filterCard .card-body').prepend(filterAlert);
        }
    }
    
    // Initialize
    showActiveFilters();
});

document.querySelectorAll('#instruktur_id, #kategori').forEach(el => {
    el.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});

// Handle manual date input
document.querySelector('#date_range').addEventListener('input', function() {
    const value = this.value;
    if (value.includes(' to ')) {
        const dates = value.split(' to ');
        const isValid = dates.every(date => {
            return /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(date.trim()) || 
                   /^\d{4}-\d{1,2}-\d{1,2}$/.test(date.trim());
        });

        if (!isValid) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    }
});
document.querySelector('#filterForm').addEventListener('submit', function(e) {
    const dateInput = document.querySelector('#date_range');
    if (dateInput.classList.contains('is-invalid')) {
        e.preventDefault();
        alert('Format tanggal tidak valid. Gunakan format dd/mm/yyyy atau yyyy-mm-dd');
    }
});
</script>
@endpush