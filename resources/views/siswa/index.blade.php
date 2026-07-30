@extends('layouts.app')

@section('title', 'Daftar Siswa')



@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Daftar Siswa</h1>
                    <p class="text-muted mb-0">Kelola data siswa yang terdaftar dalam program.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('siswa.import') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import CSV
                    </a>
                    <a href="{{ route('siswa.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('siswa.index') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Cari Siswa</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nama siswa..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Filter Sekolah</label>
                    <select name="kodlan" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Sekolah</option>
                        @foreach($sekolahs as $sekolah)
                        <option value="{{ $sekolah->kodlan }}" {{ request('kodlan') == $sekolah->kodlan ? 'selected' : '' }}>
                            {{ $sekolah->namasekolah }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-md-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Filter</button>
                    @if(request()->has('search') || request()->has('kodlan'))
                        <a href="{{ route('siswa.index') }}" class="btn btn-light border ms-2">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-4 px-2">
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a class="nav-link {{ !request('temp_nisn') ? 'active' : 'bg-white text-dark border' }}" href="{{ route('siswa.index') }}">
                    Semua Siswa
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('temp_nisn') ? 'active bg-warning text-dark border-start-0' : 'bg-white text-dark border' }}" href="{{ route('siswa.index', ['temp_nisn' => 1]) }}">
                    <i class="bi bi-exclamation-triangle me-1"></i> Perlu Verifikasi NISN
                </a>
            </li>
        </ul>
    </div>

    <!-- Table Section -->
    <form id="bulkDeleteForm" action="{{ route('siswa.bulk-destroy') }}" method="POST">
        @csrf
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark">Data Siswa</h5>
                @if(auth()->user()->role !== 'instruktur')
                <div id="bulkActionContainer" class="d-none">
                    <button type="button" class="btn btn-danger btn-sm shadow-sm px-3" onclick="confirmBulkDelete()">
                        <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show m-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session('import_errors') && count(session('import_errors')) > 0)
                <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                    <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Rincian Error Import Siswa:</h6>
                    <ul class="mb-0 small ps-3" style="max-height: 200px; overflow-y: auto;">
                        @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0" id="siswa-table">
                        <thead class="table-light">
                            <tr>
                                @if(auth()->user()->role !== 'instruktur')
                                <th width="40px" class="text-center ps-3">
                                    <input type="checkbox" id="selectAll" class="form-check-input" title="Pilih Semua">
                                </th>
                                @endif
                                <th width="15%" class="{{ auth()->user()->role === 'instruktur' ? 'ps-4' : '' }}">NIS/NISN</th>
                                <th width="25%">Nama Siswa</th>
                                <th width="15%">Jenis Kelamin</th>
                                <th width="25%">Sekolah</th>
                                <th width="10%">Kelas</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($siswa as $item)
                            <tr class="{{ Str::startsWith($item->nisn, 'TMP') ? 'table-warning' : '' }}">
                                @if(auth()->user()->role !== 'instruktur')
                                <td class="text-center ps-3">
                                    <input type="checkbox" name="siswa_ids[]" value="{{ $item->id }}" class="form-check-input siswa-checkbox">
                                </td>
                                @endif
                                <td class="text-dark fw-bold font-monospace small {{ auth()->user()->role === 'instruktur' ? 'ps-4' : '' }}">{{ $item->nisn ?? '-' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                        {{ substr($item->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $item->nama_lengkap }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(strtolower($item->jenis_kelamin) == 'l' || strtolower($item->jenis_kelamin) == 'laki-laki')
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 small">
                                        <i class="bi bi-gender-male me-1"></i> Laki-laki
                                    </span>
                                @elseif(strtolower($item->jenis_kelamin) == 'p' || strtolower($item->jenis_kelamin) == 'perempuan')
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 small">
                                        <i class="bi bi-gender-female me-1"></i> Perempuan
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-building text-muted small"></i>
                                    <span class="text-dark small">{{ $item->sekolah->namasekolah }}</span>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $item->kelas }}</span></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('siswa.edit', $item) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('siswa.destroy', $item) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-state">
                            <td colspan="7" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-people text-muted fs-1 opacity-25"></i>
                                </div>
                                <h6 class="text-muted">Data Tidak Ditemukan</h6>
                                <p class="small text-muted">Coba ubah filter pencarian Anda atau tambah siswa baru.</p>
                                <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">Reset Filter</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse ($siswa as $item)
                    <div class="card mb-3 shadow-sm border-0 border-start border-4 {{ Str::startsWith($item->nisn, 'TMP') ? 'border-warning' : 'border-primary' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-3">
                                    @if(auth()->user()->role !== 'instruktur')
                                    <div class="form-check me-1">
                                        <input type="checkbox" name="siswa_ids[]" value="{{ $item->id }}" class="form-check-input siswa-checkbox">
                                    </div>
                                    @endif
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 40px; height: 40px;">
                                        {{ substr($item->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ $item->nama_lengkap }}</h6>
                                        <span class="badge bg-light text-dark border font-monospace small">{{ $item->nisn ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-2 opacity-10">

                            <div class="row g-2 small mb-3">
                                <div class="col-12">
                                    <div class="text-muted mb-1"><i class="bi bi-building me-1"></i>Sekolah</div>
                                    <div class="fw-semibold">{{ $item->sekolah->namasekolah }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted mb-1"><i class="bi bi-bookmarks me-1"></i>Kelas</div>
                                    <span class="badge bg-light text-dark border">{{ $item->kelas }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('siswa.edit', $item) }}" class="btn btn-sm btn-outline-warning flex-grow-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('siswa.destroy', $item) }}" method="POST" class="flex-grow-1 delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-people text-muted fs-1 opacity-25"></i>
                        <h6 class="text-muted mt-3">Data Tidak Ditemukan</h6>
                        <p class="small text-muted">Coba ubah filter pencarian Anda.</p>
                    </div>
                @endforelse
            </div>
            
            <x-pagination-wrapper :paginator="$siswa->appends(request()->query())" class="bg-white border-top py-3" />
        </div>
    </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bulk Select Checkboxes Handling
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.siswa-checkbox');
        const bulkContainer = document.getElementById('bulkActionContainer');
        const selectedCount = document.getElementById('selectedCount');

        function updateBulkButton() {
            const checkedCount = document.querySelectorAll('.siswa-checkbox:checked').length;
            if (selectedCount) selectedCount.textContent = checkedCount;
            
            if (bulkContainer) {
                if (checkedCount > 0) {
                    bulkContainer.classList.remove('d-none');
                } else {
                    bulkContainer.classList.add('d-none');
                }
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateBulkButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (selectAll) {
                    selectAll.checked = checkboxes.length === document.querySelectorAll('.siswa-checkbox:checked').length && checkboxes.length > 0;
                }
                updateBulkButton();
            });
        });

        // Initialize DataTable for Siswa table
        if (typeof window.DataTableManager !== 'undefined') {
            const table = document.getElementById('siswa-table');
            const isEmpty = table ? table.querySelector('.empty-state') : null;

            if (table && !isEmpty) {
                const dataTableManager = new window.DataTableManager();
                dataTableManager.init('#siswa-table', {
                    order: [[1, 'asc']], // Sort by NISN by default
                    columnDefs: [
                        { orderable: false, targets: [0, 6] }, // Disable sorting for Checkbox & Actions columns
                        { type: 'string', targets: [1, 2, 3, 4, 5] },
                        { searchable: false, targets: [0, 6] }
                    ],
                    pageLength: 25,
                    paging: false,
                    info: false
                });
            }
        }
        
        // Menambahkan konfirmasi sebelum submit form hapus
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus siswa ini?')) {
                    this.submit();
                }
            });
        });
    });

    function confirmBulkDelete() {
        const checkedCount = document.querySelectorAll('.siswa-checkbox:checked').length;
        if (checkedCount === 0) {
            alert('Pilih minimal satu siswa untuk dihapus.');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus ${checkedCount} data siswa yang dipilih secara bersamaan? Data absensi dan pendaftaran ekstrakurikuler terkait juga akan terhapus.`)) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }
</script>
@endpush