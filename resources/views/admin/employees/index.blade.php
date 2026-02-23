@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Data Karyawan</h1>
            <p class="text-muted">Kelola data karyawan dan pembagian divisi.</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Karyawan
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.employees.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Nama / Email</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci...">
                </div>
                <div class="col-md-3">
                    <label for="division_id" class="form-label">Filter Divisi</label>
                    <select class="form-select" id="division_id" name="division_id">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2" style="display: {{ request('search') || request('division_id') ? 'block' : 'none' }}">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-danger w-100">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Employee Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nama Lengkap</th>
                            <th>Divisi</th>
                            <th>Role</th>
                            <th>Kontak</th>
                            <th>Tanggal Bergabung</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial rounded-circle bg-primary text-white me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            {{ substr($employee->nama_lengkap, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.employees.show', $employee) }}" class="fw-bold text-decoration-none">{{ $employee->nama_lengkap }}</a>
                                            <div class="small text-muted">{{ $employee->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($employee->division)
                                        <span class="badge bg-info text-dark">{{ $employee->division->name }}</span>
                                    @else
                                        <span class="text-muted fst-italic">- Belum ada divisi -</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $employee->role }}</span>
                                </td>
                                <td>
                                    {{ $employee->no_telephone ?? '-' }}
                                </td>
                                <td>
                                    {{ $employee->created_at->format('d M Y') }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada data karyawan ditemukan</h5>
                                    <p class="text-muted small">Coba ubah kata kunci pencarian atau filter divisi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection
