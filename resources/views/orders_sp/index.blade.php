@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Surat Pesanan (SP)</h1>
                    <p class="text-muted mb-0">Kelola dokumen Surat Pesanan kegiatan sekolah mitra.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importSpModal">
                        <i class="bi bi-file-earmark-excel me-1"></i> Impor Excel
                    </button>
                    <a href="{{ route('orders-sp.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Buat SP Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('orders-sp.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">Cari SP</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="No SP, sekolah, atau salesman..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="menunggu_validasi" {{ request('status') === 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="berjalan" {{ request('status') === 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ request('status') === 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">Daftar SP</h5>
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
                            <th width="15%" class="ps-4">No. SP</th>
                            <th width="12%">Tanggal SP</th>
                            <th width="20%">Sekolah Pelanggan</th>
                            <th width="15%">Salesman</th>
                            <th width="10%">Peserta (Est.)</th>
                            <th width="13%">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $item)
                            <tr>
                                <td class="font-monospace fw-bold ps-4 text-dark">{{ $item->nomor_sp }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_sp)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->sekolah->namasekolah }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $item->sekolah->kota }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->salesman->nama_salesman }}</div>
                                    <small class="text-muted font-monospace">{{ $item->salesman->kode_salesman }}</small>
                                </td>
                                <td>{{ $item->jumlah_peserta_estimasi }} anak</td>
                                <td>
                                    @switch($item->status)
                                        @case('draft')
                                            <span class="badge bg-secondary">Draft</span>
                                            @break
                                        @case('menunggu_validasi')
                                            <span class="badge bg-warning text-dark">Menunggu Validasi</span>
                                            @break
                                        @case('disetujui')
                                            <span class="badge bg-success">Disetujui</span>
                                            @break
                                        @case('berjalan')
                                            <span class="badge bg-primary">Berjalan</span>
                                            @break
                                        @case('selesai')
                                            <span class="badge bg-info text-dark">Selesai</span>
                                            @break
                                        @case('batal')
                                            <span class="badge bg-danger">Batal</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('orders-sp.show', $item) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($item->status === 'draft')
                                            <a href="{{ route('orders-sp.edit', $item) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('orders-sp.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SP ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-file-earmark-text text-muted fs-1 opacity-25"></i>
                                    </div>
                                    <h6 class="text-muted">Data Surat Pesanan Tidak Ditemukan</h6>
                                    @if (request('search') || request('status'))
                                        <p class="small text-muted">Tidak ada SP yang cocok dengan pencarian Anda.</p>
                                        <a href="{{ route('orders-sp.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">Reset Filter</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end p-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Import Excel Modal -->
<div class="modal fade" id="importSpModal" tabindex="-1" aria-labelledby="importSpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('orders-sp.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="importSpModalLabel">Impor Data SP dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold text-muted small">Pilih File Excel (.xlsx, .xls, .csv)</label>
                        <input type="file" name="file" class="form-control" id="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text small mt-2">
                            Pastikan header Excel memiliki kolom berikut:<br>
                            <code>nomor_sp</code>, <code>tanggal_sp</code>, <code>kode_pelanggan</code>, <code>kode_salesman</code>, <code>jumlah_peserta_estimasi</code>, <code>jenis_kegiatan</code>, <code>lokasi_pembelajaran</code>, <code>tanggal_mulai_rencana</code>, <code>jumlah_pertemuan</code>, <code>kode_produk</code>, <code>harga_satuan</code>, <code>catatan_khusus</code>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Impor Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
