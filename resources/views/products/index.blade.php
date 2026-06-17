@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Master Produk</h1>
                    <p class="text-muted mb-0">Kelola daftar program/kategori produk pembelajaran Erlass.</p>
                </div>
                <div>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Produk
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Cari Produk</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Kode, nama produk, atau jenis..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">Data Produk</h5>
        </div>
        <div class="card-body p-0">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="15%" class="ps-4">Kode Produk</th>
                            <th width="25%">Nama Produk</th>
                            <th width="15%">Jenis</th>
                            <th width="15%">Harga Standar</th>
                            <th width="10%">Durasi (Bulan)</th>
                            <th width="10%">Jenis Kegiatan</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $item)
                            <tr>
                                <td class="font-monospace text-muted ps-4 fw-bold">{{ $item->kode_produk }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->nama_produk }}</div>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $item->standar_durasi_menit }} menit / sesi</small>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $item->jenis }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">Rp {{ number_format($item->harga, 2, ',', '.') }}</span>
                                </td>
                                <td>{{ $item->durasi_bulan ?? '-' }}</td>
                                <td>
                                    @if($item->jenis_kegiatan === 'eskul')
                                        <span class="badge bg-success">Ekstrakurikuler</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Intrakurikuler</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('products.edit', $item) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
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
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-box-seam text-muted fs-1 opacity-25"></i>
                                    </div>
                                    <h6 class="text-muted">Data Produk Tidak Ditemukan</h6>
                                    @if (request('search'))
                                        <p class="small text-muted">Tidak ada produk dengan kata kunci tersebut.</p>
                                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">Reset Filter</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination-wrapper :paginator="$products->appends(request()->query())" class="bg-white border-top py-3" />
        </div>
    </div>
</div>
@endsection
