@extends('layouts.app')

@section('title', 'Daftar Siswa')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1>Daftar Siswa</h1>
            <p class="text-muted">Kelola data siswa yang terdaftar dalam program.</p>

            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <a href="{{ route('siswa.create') }}" class="btn btn-success">
                            <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa
                        </a>
                        <form method="GET" action="{{ route('siswa.index') }}" class="d-flex gap-2">
                            <select name="kodlan" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Sekolah</option>
                                @foreach($sekolahs as $sekolah)
                                {{-- Menggunakan 'kodlan' sebagai value dan untuk perbandingan --}}
                                <option value="{{ $sekolah->kodlan }}" {{ request('kodlan') == $sekolah->kodlan ? 'selected' : '' }}>
                                    {{ $sekolah->namasekolah }}
                                </option>
                                @endforeach
                            </select>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama siswa..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Lengkap</th>
                                    <th>NISN</th>
                                    <th>Sekolah</th>
                                    <th>Rombel</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswa as $index => $item)
                                <tr>
                                    <td>{{ $siswa->firstItem() + $index }}</td>
                                    <td>{{ $item->nama_lengkap }}</td>
                                    <td>{{ $item->nisn }}</td>
                                    <td>{{ $item->sekolah->namasekolah }}</td>
                                    <td>{{ $item->rombel }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('siswa.edit', $item) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ route('siswa.destroy', $item) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Data siswa tidak ditemukan.
                                        <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-link">Hapus filter</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($siswa->hasPages())
                <div class="card-footer">
                    {{-- Menambahkan query string filter saat paginasi --}}
                    {{ $siswa->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Menambahkan konfirmasi sebelum submit form hapus
    document.addEventListener('DOMContentLoaded', function() {
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
</script>
@endpush