@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1>Manajemen Pengguna</h1>
            <p class="text-muted">Kelola akun dan role pengguna sistem.</p>

            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('users.create') }}" class="btn btn-success">
                            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
                        </a>
                        <form method="GET" action="{{ route('users.index') }}" class="w-50">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
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
                                    <th>ID</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->nama_lengkap }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ Str::ucfirst($user->role) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline delete-form">
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
                                        <td colspan="5" class="text-center">Pengguna tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if ($users->hasPages())
                    <div class="card-footer">
                        {{ $users->links() }}
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
                event.preventDefault(); 
                if (confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat diurungkan.')) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush