@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Daftar Sekolah</h1>
                    <p class="text-muted mb-0">Kelola data sekolah mitra.</p>
                </div>
                <div>
                    <a href="{{ route('sekolah.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Sekolah
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
             <form method="GET" action="{{ route('sekolah.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Cari Sekolah</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nama atau Kode Sekolah (NPSN)..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">Data Sekolah</h5>
        </div>
        <div class="card-body p-0">


            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0" id="sekolah-table">
                    <thead class="table-light">
                        <tr>
                            <th width="15%" class="ps-4">Kode Sekolah</th>
                            <th width="30%">Nama Sekolah</th>
                            <th width="20%">Provinsi</th>
                            <th width="20%">Kecamatan</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sekolah as $item)
                            <tr>
                                <td class="font-monospace text-muted ps-4">{{ $item->kodlan }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->namasekolah }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $item->provinsi }}</span></td>
                                <td>{{ $item->kec }}</td>
                                <td class="text-center">
                                     <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('sekolah.edit', $item) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('sekolah.destroy', $item) }}" method="POST" class="d-inline delete-form">
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
                                <td colspan="5" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-building text-muted fs-1 opacity-25"></i>
                                    </div>
                                    <h6 class="text-muted">Data Tidak Ditemukan</h6>
                                    @if (request('search'))
                                        <p class="small text-muted">Tidak ada sekolah dengan nama atau kode tersebut.</p>
                                        <a href="{{ route('sekolah.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">Reset Filter</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse ($sekolah as $item)
                    <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1 text-primary">{{ $item->namasekolah }}</h6>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $item->kodlan }}</span>
                                </div>
                            </div>
                            
                            <div class="row g-2 small mb-3 mt-3">
                                <div class="col-6">
                                    <div class="text-muted mb-1"><i class="bi bi-geo-alt me-1"></i>Provinsi</div>
                                    <div class="fw-semibold">{{ $item->provinsi }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted mb-1"><i class="bi bi-map me-1"></i>Kecamatan</div>
                                    <div class="fw-semibold">{{ $item->kec }}</div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('sekolah.edit', $item) }}" class="btn btn-sm btn-outline-warning flex-grow-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('sekolah.destroy', $item) }}" method="POST" class="flex-grow-1 delete-form">
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
                        <i class="bi bi-building text-muted fs-1 opacity-25"></i>
                        <h6 class="text-muted mt-3">Data Tidak Ditemukan</h6>
                        @if (request('search'))
                            <p class="small text-muted">Tidak ada sekolah dengan nama atau kode tersebut.</p>
                        @endif
                    </div>
                @endforelse
            </div>
            
            <x-pagination-wrapper :paginator="$sekolah" class="bg-white border-top py-3" />
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize DataTable for Sekolah table
        if (typeof window.DataTableManager !== 'undefined') {
            const table = document.getElementById('sekolah-table');
            const isEmpty = table ? table.querySelector('.empty-state') : null;

            if (table && !isEmpty) {
                const dataTableManager = new window.DataTableManager();
                dataTableManager.init('#sekolah-table', {
                    order: [[1, 'asc']], // Sort by School Name column
                    columnDefs: [
                        { orderable: false, targets: [4] }, // Disable sorting for Actions column
                        { type: 'string', targets: [0, 1, 2, 3] }, // String sorting for text columns
                        { searchable: false, targets: [4] } // Actions column not searchable
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
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Mencegah form langsung di-submit
                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    this.submit(); // Jika dikonfirmasi, lanjutkan submit
                }
            });
        });
    });
</script>
@endpush