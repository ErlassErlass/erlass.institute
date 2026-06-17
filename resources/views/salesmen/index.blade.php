@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Master Salesman</h1>
                    <p class="text-muted mb-0">Kelola data agen sales, wilayah kerja, dan group leader.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-file-earmark-excel me-1"></i> Impor Excel
                    </button>
                    <a href="{{ route('salesmen.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Salesman
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('salesmen.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Cari Salesman</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Kode, nama, atau area..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">Data Salesman</h5>
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
                            <th width="15%" class="ps-4">Kode Sales</th>
                            <th width="25%">Nama Salesman</th>
                            <th width="20%">Akun Pengguna</th>
                            <th width="15%">Group Leader</th>
                            <th width="15%">Area / Wilayah</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salesmen as $item)
                            <tr>
                                <td class="font-monospace text-muted ps-4 fw-bold">{{ $item->kode_salesman }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->nama_salesman }}</div>
                                </td>
                                <td>
                                    @if ($item->user)
                                        <div class="fw-semibold text-primary"><i class="bi bi-person-circle me-1"></i>{{ $item->user->name }}</div>
                                        <small class="text-muted">{{ $item->user->email }}</small>
                                    @else
                                        <span class="text-muted small"><em>Belum terhubung</em></span>
                                    @endif
                                </td>
                                <td>{{ $item->group_leader ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $item->area ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('salesmen.edit', $item) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('salesmen.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus salesman ini?')">
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
                                <td colspan="6" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-people text-muted fs-1 opacity-25"></i>
                                    </div>
                                    <h6 class="text-muted">Data Salesman Tidak Ditemukan</h6>
                                    @if (request('search'))
                                        <p class="small text-muted">Tidak ada salesman dengan kata kunci tersebut.</p>
                                        <a href="{{ route('salesmen.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">Reset Filter</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end p-4">
                {{ $salesmen->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- Import Excel Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('salesmen.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="importModalLabel">Impor Data Salesman dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold text-muted small">Pilih File Excel (.xlsx, .xls, .csv)</label>
                        <input type="file" name="file" class="form-control" id="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text small mt-3">
                            <span class="fw-semibold text-dark d-block mb-1">Ketentuan kolom Excel:</span>
                            <ul class="ps-3 mb-0 text-muted" style="list-style-type: decimal;">
                                <li class="mb-1"><strong>kode_salesman</strong> <span class="text-danger">*</span>: Kode unik sales (Wajib diisi, harus unik).</li>
                                <li class="mb-1"><strong>nama_salesman</strong> <span class="text-danger">*</span>: Nama lengkap salesman (Wajib diisi).</li>
                                <li class="mb-1"><strong>group_leader</strong>: Nama / kode group leader (Opsional).</li>
                                <li class="mb-1"><strong>area</strong>: Wilayah kerja / cakupan sales (Opsional).</li>
                                <li class="mb-1"><strong>user_email</strong>: Email akun terdaftar (Opsional). Harus berupa email valid & terdaftar dengan role 'sales'. Jika diisi, sales otomatis terhubung ke user.</li>
                            </ul>
                            <div class="mt-2 text-danger" style="font-size: 0.75rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Kolom bertanda <span class="text-danger">*</span> wajib ada di file Excel Anda.
                            </div>
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
@endpush
@endsection
