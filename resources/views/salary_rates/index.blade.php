@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Master Tarif & Kompensasi</h1>
                    <p class="text-muted mb-0">Kelola tarif honor dasar per sesi mengajar berdasarkan level instruktur dan bonus produk.</p>
                </div>
                <div>
                    <a href="{{ route('admin.salary-rates.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Tarif Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.salary-rates.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Cari Tarif</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Level atau kategori produk..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">Data Tarif Master</h5>
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
                            <th width="25%" class="ps-4">Level Instruktur</th>
                            <th width="20%">Tarif Dasar per Sesi</th>
                            <th width="25%">Kategori Produk (Tambahan)</th>
                            <th width="20%">Bonus Produk per Sesi</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rates as $item)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark text-capitalize">{{ str_replace('_', ' ', $item->level) }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-primary">Rp {{ number_format($item->base_rate, 2, ',', '.') }}</span>
                                </td>
                                <td>
                                    @if ($item->product_category)
                                        <span class="badge bg-info text-dark">{{ $item->product_category }}</span>
                                    @else
                                        <span class="text-muted italic">Umum (Semua Produk)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-success">Rp {{ number_format($item->product_bonus, 2, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.salary-rates.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.salary-rates.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tarif master ini?')">
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
                                <td colspan="5" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-cash-stack text-muted fs-1 opacity-25"></i>
                                    </div>
                                    <h6 class="text-muted">Data Tarif Master Belum Ditentukan</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination-wrapper :paginator="$rates->appends(request()->query())" class="bg-white border-top py-3" />
        </div>
    </div>
</div>
@endsection
