@extends('layouts.app')

@section('title', 'Daftar Laporan Mengajar')

@push('styles')
{{-- External CSS for the date picker --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* A more organized CSS block for custom styles */
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
    .badge-custom {
        padding: 5px 10px;
        font-weight: normal;
        font-size: 0.8rem;
    }
    /* Active filter alert styling */
    .alert-info {
        border-left: 4px solid #0dcaf0;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Daftar Laporan Mengajar</h1>
            <p class="text-muted mb-0">Tinjau semua laporan kegiatan mengajar yang telah dibuat.</p>
        </div>
        @can('create', App\Models\LaporanMengajar::class)
        <a href="{{ route('laporan-mengajar.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Buat Laporan
        </a>
        @endcan
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1">Total Laporan</h6>
                    <div class="d-flex justify-content-between align-items-end">
                        <h3 class="mb-0">{{ $totalLaporan }}</h3>
                        <i class="bi bi-journal-text stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card bg-success text-white shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1">Minggu Ini</h6>
                     <div class="d-flex justify-content-between align-items-end">
                        <h3 class="mb-0">{{ $laporanMingguIni }}</h3>
                        <i class="bi bi-calendar-week stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card bg-info text-white shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1">Bulan Ini</h6>
                     <div class="d-flex justify-content-between align-items-end">
                        <h3 class="mb-0">{{ $laporanBulanIni }}</h3>
                        <i class="bi bi-calendar-month stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card bg-warning text-dark shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1">Instruktur Aktif</h6>
                     <div class="d-flex justify-content-between align-items-end">
                        <h3 class="mb-0">{{ $totalInstruktur }}</h3>
                        <i class="bi bi-people stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan-mengajar.index') }}" id="filterForm">
                {{-- Note: @csrf is not needed for GET forms as they don't change server state. --}}
                <div class="row g-3 align-items-end">
                    @if(in_array(Auth::user()->role, ['admin', 'admin_erlass']))
                    <div class="col-md-3">
                        <label for="instruktur_id" class="form-label">Instruktur</label>
                        <select name="instruktur_id" id="instruktur_id" class="form-select">
                            <option value="">Semua Instruktur</option>
                            @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" @selected(request('instruktur_id') == $instructor->id)>
                                {{ $instructor->nama_lengkap }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <label for="date_range" class="form-label">Rentang Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input type="text" name="date_range" id="date_range" class="form-control" placeholder="Pilih rentang" value="{{ request('date_range') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select name="kategori" id="kategori" class="form-select">
                            <option value="">Semua</option>
                            <option value="Reguler" @selected(request('kategori') == 'Reguler')>Reguler</option>
                            <option value="Remedial" @selected(request('kategori') == 'Remedial')>Remedial</option>
                            <option value="Pengayaan" @selected(request('kategori') == 'Pengayaan')>Pengayaan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Filter</button>
                            <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
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

    {{-- Main Data Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="laporan-mengajar-table">
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
                                <div>{{ \Carbon\Carbon::parse($item->jadwal_mengajar)->isoFormat('D MMM YYYY') }}</div>
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
                                $badgeClass = match($item->kategori_pengajaran) {
                                    'Reguler' => 'bg-primary',
                                    'Remedial' => 'bg-warning text-dark',
                                    'Pengayaan' => 'bg-info text-dark',
                                    default => 'bg-secondary',
                                };
                                @endphp
                                <span class="badge {{ $badgeClass }} badge-custom">{{ $item->kategori_pengajaran }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    @can('view', $item)
                                    <a href="{{ route('laporan-mengajar.show', $item) }}" class="btn btn-sm btn-info" title="Lihat Detail"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('laporan-mengajar.absensi.create', $item) }}" class="btn btn-sm btn-success" title="Input Absensi"><i class="bi bi-person-check"></i></a>
                                    @endcan
                                    @can('update', $item)
                                    <a href="{{ route('laporan-mengajar.edit', $item) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    @endcan
                                    @can('delete', $item)
                                    <form action="{{ route('laporan-mengajar.destroy', $item) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        {{-- This row now uses 6 <td>s to match the header, avoiding colspan for robust DataTables compatibility --}}
                        <tr class="empty-state">
                            <td class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-journal-x" style="font-size: 2.5rem;"></i>
                                    <h5 class="mt-2">Tidak ada laporan untuk ditampilkan</h5>
                                    <p class="mb-0">Coba sesuaikan filter atau buat laporan baru.</p>
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($laporan->hasPages())
        <div class="card-footer bg-light">
            {{ $laporan->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- External JS libraries --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('laporan-mengajar-table');
        const isEmpty = table.querySelector('.empty-state');

        // Only initialize DataTables if the table is NOT empty.
        // This preserves the custom empty state message.
        if (table && !isEmpty) {
            try {
                $('#laporan-mengajar-table').DataTable({
                    order: [[0, 'desc']], // Default sort by the first column (Date) descending
                    pageLength: 25,
                    responsive: true,
                    autoWidth: false,
                    columnDefs: [
                        {
                            // This single definition targets the last column ('Aksi')
                            targets: 5,
                            orderable: false,       // Disable sorting
                            className: "text-center", // Center-align content
                            responsivePriority: 2,  // Set its priority for responsive display
                        },
                        {
                            // This targets the first column ('Tanggal')
                            targets: 0,
                            responsivePriority: 1, // Make it the highest priority on small screens
                        }
                    ],
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_-_END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        zeroRecords: "Tidak ditemukan data yang sesuai",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "›",
                            previous: "‹"
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing DataTable:', error);
            }
        }

        // Initialize Flatpickr date range picker
        flatpickr("#date_range", {
            mode: "range",
            locale: "id", // Use Indonesian locale
            dateFormat: "d/m/Y",
            onClose: function(selectedDates) {
                // Auto-submit form when a full date range is selected
                if (selectedDates.length === 2) {
                    document.getElementById('filterForm').submit();
                }
            }
        });

        // Add auto-submit functionality to dropdown filters
        document.querySelectorAll('#instruktur_id, #kategori').forEach(el => {
            el.addEventListener('change', () => document.getElementById('filterForm').submit());
        });

        // Handle Export links, preserving current filters
        document.querySelectorAll('.export-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const format = this.dataset.format;
                const params = new URLSearchParams(window.location.search);
                let exportUrl = "{{ route('laporan-mengajar.export', ['format' => '__FORMAT__']) }}";
                
                exportUrl = exportUrl.replace('__FORMAT__', format) + '?' + params.toString();
                window.location.href = exportUrl;
            });
        });

        // Initialize all Bootstrap tooltips on the page
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush