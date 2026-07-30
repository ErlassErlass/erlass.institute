@extends('layouts.app')

@section('title', 'Daftar Laporan Mengajar')

@push('styles')
@endpush



@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1"><span class="text-gradient-primary">Daftar Laporan Mengajar</span></h1>
            <p class="text-muted mb-0">Tinjau semua laporan kegiatan mengajar yang telah dibuat.</p>
        </div>
        @can('create', App\Models\LaporanMengajar::class)
        <a href="{{ route('laporan-mengajar.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Buat Laporan Baru
        </a>
        @endcan
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card glass-card border-0 h-100 p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-1">Total Laporan</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalLaporan }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card glass-card border-0 h-100 p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                         <p class="text-muted small text-uppercase fw-bold mb-1">Minggu Ini</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $laporanMingguIni }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                        <i class="bi bi-calendar-week fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card glass-card border-0 h-100 p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                         <p class="text-muted small text-uppercase fw-bold mb-1">Bulan Ini</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $laporanBulanIni }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                        <i class="bi bi-calendar-month fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card glass-card border-0 h-100 p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                         <p class="text-muted small text-uppercase fw-bold mb-1">Instruktur Aktif</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalInstruktur }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card glass-card border-0 mb-4">
        <div class="card-body p-4">
            <h6 class="card-title fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-funnel text-primary"></i> Filter Laporan
            </h6>
            <form method="GET" action="{{ route('laporan-mengajar.index') }}" id="filterForm">
                <div class="row g-3">
                    @if(in_array(Auth::user()->role, ['admin', 'admin_sistem', 'webmaster']))
                    <div class="col-md-3">
                        <label for="instruktur_id" class="form-label small text-muted text-uppercase fw-bold">Instruktur</label>
                        <select name="instruktur_id" id="instruktur_id" class="form-select border-light-subtle bg-white">
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
                        <label for="date_range" class="form-label small text-muted text-uppercase fw-bold">Rentang Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-light-subtle"><i class="bi bi-calendar text-primary"></i></span>
                            <input type="text" name="date_range" id="date_range" class="form-control border-light-subtle bg-white" placeholder="Pilih rentang" value="{{ request('date_range') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="kategori" class="form-label small text-muted text-uppercase fw-bold">Kategori</label>
                        <select name="kategori" id="kategori" class="form-select border-light-subtle bg-white">
                            <option value="">Semua</option>
                            @foreach($kategoriList as $cat)
                                <option value="{{ $cat }}" @selected(request('kategori') == $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="d-flex w-100 gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Terapkan</button>
                            <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-light text-muted border border-light-subtle" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-download"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><h6 class="dropdown-header">Export Data</h6></li>
                                    <li><a class="dropdown-item export-link" href="#" data-format="excel"><i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel</a></li>
                                    <li><a class="dropdown-item export-link" href="#" data-format="pdf"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Data Table --}}
    <div class="card glass-card border-0">
        <div class="card-body p-0">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="table-responsive d-none d-md-block">
                <table class="table table-modern table-compact align-middle mb-0" id="laporan-mengajar-table">
                    <thead>
                        <tr>
                            <th width="15%">Tanggal</th>
                            <th width="20%">Instruktur</th>
                            <th width="25%">Sekolah</th>
                            <th width="15%">Rombel</th>
                            <th width="15%">Kategori</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporan as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-event text-primary small"></i>
                                    <span class="fw-medium text-dark">{{ \Carbon\Carbon::parse($item->jadwal_mengajar)->isoFormat('D MMM YYYY') }}</span>
                                </div>
                                <small class="text-muted d-block mt-1"><i class="bi bi-clock me-1"></i>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</small>
                                @if($item->created_at)
                                    <small class="text-primary d-block mt-1 fw-semibold" style="font-size: 0.725rem;" title="Waktu data terinput ke sistem">
                                        <i class="bi bi-cloud-arrow-up me-1"></i>Input: {{ $item->created_at->format('d/m/Y H:i') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle small bg-primary bg-opacity-10 text-primary" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <span class="text-dark fw-medium">{{ $item->instruktur->nama_lengkap ?? 'N/A' }}</span>
                                </div>
                                @if($item->asisten)
                                <small class="text-muted d-block mt-1 ps-4 ms-1">Asisten: {{ $item->asisten->nama_lengkap }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $item->sekolah->namasekolah ?? 'N/A' }}</div>
                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $item->sekolah->kec ?? '' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $item->rombel }}</span></td>
                            <td>
                                @php
                                $badgeClass = match($item->kategori_pengajaran) {
                                    'Reguler' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                    'Remedial' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                                    'Pengayaan' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                    default => 'bg-secondary bg-opacity-10 text-secondary',
                                };
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-2 py-1">{{ $item->kategori_pengajaran }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    @can('view', $item)
                                    <a href="{{ route('laporan-mengajar.show', $item) }}" class="btn btn-icon btn-light text-primary border me-1" title="Lihat Detail"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('laporan-mengajar.absensi.create', $item) }}" class="btn btn-icon btn-light text-success border me-1" title="Input Absensi"><i class="bi bi-person-check"></i></a>
                                    @endcan
                                    @can('update', $item)
                                    <a href="{{ route('laporan-mengajar.edit', $item) }}" class="btn btn-icon btn-light text-warning border me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    @endcan
                                    @can('delete', $item)
                                    <form action="{{ route('laporan-mengajar.destroy', $item) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-light text-danger border" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        {{-- This row now uses 6 <td>s to match the header, avoiding colspan for robust DataTables compatibility --}}
                        <tr class="empty-state">
                            <td colspan="6" class="text-center py-5">
                                <div class="mb-3">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="bi bi-journal-x text-muted" style="font-size: 2.5rem; opacity: 0.3"></i>
                                    </div>
                                </div>
                                <h5 class="fw-bold text-gray-800">Tidak ada laporan untuk ditampilkan</h5>
                                <p class="text-muted">Coba sesuaikan filter atau buat laporan baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse($laporan as $item)
                <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1 text-primary">{{ \Carbon\Carbon::parse($item->jadwal_mengajar)->isoFormat('D MMM YYYY') }}</h6>
                                <small class="text-muted d-block"><i class="bi bi-clock me-1"></i>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</small>
                                @if($item->created_at)
                                    <small class="text-primary d-block mt-1 fw-semibold" style="font-size: 0.725rem;" title="Waktu data terinput ke sistem">
                                        <i class="bi bi-cloud-arrow-up me-1"></i>Input: {{ $item->created_at->format('d/m/Y H:i') }}
                                    </small>
                                @endif
                            </div>
                            @php
                            $badgeClass = match($item->kategori_pengajaran) {
                                'Reguler' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                'Remedial' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                                'Pengayaan' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                default => 'bg-secondary bg-opacity-10 text-secondary',
                            };
                            @endphp
                            <span class="badge {{ $badgeClass }} rounded-pill">{{ $item->kategori_pengajaran }}</span>
                        </div>

                        <div class="mb-3">
                            <div class="fw-bold text-dark">{{ $item->sekolah->namasekolah ?? 'N/A' }}</div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-light text-dark border">{{ $item->rombel }}</span>
                                <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $item->instruktur->nama_lengkap ?? 'N/A' }}</small>
                            </div>
                        </div>

                        <div class="btn-group w-100">
                             @can('view', $item)
                            <a href="{{ route('laporan-mengajar.show', $item) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                             <a href="{{ route('laporan-mengajar.absensi.create', $item) }}" class="btn btn-sm btn-outline-success flex-grow-1">
                                <i class="bi bi-person-check"></i> Absensi
                            </a>
                            @endcan
                            
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                @can('update', $item)
                                <li>
                                    <a class="dropdown-item" href="{{ route('laporan-mengajar.edit', $item) }}">
                                        <i class="bi bi-pencil-square me-2 text-warning"></i> Edit
                                    </a>
                                </li>
                                @endcan
                                @can('delete', $item)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('laporan-mengajar.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Hapus laporan?')">
                                            <i class="bi bi-trash me-2"></i> Hapus
                                        </button>
                                    </form>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <i class="bi bi-journal-x text-muted fs-1 opacity-25 d-block mb-3"></i>
                    <h6 class="text-muted fw-bold">Tidak ada laporan</h6>
                    <p class="small text-muted mb-0">Coba sesuaikan filter atau buat laporan baru.</p>
                </div>
                @endforelse
            </div>
        </div>

        <x-pagination-wrapper :paginator="$laporan->appends(request()->query())" class="bg-transparent border-top border-light py-3" />
    </div>
</div>
@endsection

@push('scripts')
{{-- External JS libraries --}}

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