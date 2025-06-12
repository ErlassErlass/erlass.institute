@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1>Daftar Sekolah</h1>

            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('sekolah.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Sekolah
                        </a>
                        <form method="GET" action="{{ route('sekolah.index') }}" class="w-50">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama sekolah..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
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
                                    <th>Kode Sekolah</th>
                                    <th>Nama Sekolah</th>
                                    <th>Provinsi</th>
                                    <th>Kecamatan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sekolah as $item)
                                    <tr>
                                        <td>{{ $item->kodlan }}</td>
                                        <td>{{ $item->namasekolah }}</td>
                                        <td>{{ $item->provinsi }}</td>
                                        <td>{{ $item->kec }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('sekolah.edit', $item) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <form action="{{ route('sekolah.destroy', $item) }}" method="POST" class="d-inline delete-form">
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
                                        <td colspan="5" class="text-center">
                                            Data tidak ditemukan.
                                            @if (request('search'))
                                                <a href="{{ route('sekolah.index') }}" class="btn btn-sm btn-link">Hapus filter</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($sekolah->hasPages())
                    <div class="card-footer">
                        {{ $sekolah->links() }}
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
    document.addEventListener('DOMContentLoaded', function () {
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