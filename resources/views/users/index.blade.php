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
                        <div class="w-50">
                            <div class="input-group">
                                <input type="text" id="search-input" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                            </div>
                        </div>
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
                        <table class="table table-striped table-hover datatable" id="users-table">
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
                                            <div class="btn-group" role="group">
                                                @can('view', $user)
                                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('update', $user)
                                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('delete', $user)
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-state">
                                        <td colspan="5" class="text-center">Pengguna tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize DataTable for Users table
        let usersTable = null;
        if (typeof window.DataTableManager !== 'undefined') {
            const table = document.getElementById('users-table');
            const isEmpty = table ? table.querySelector('.empty-state') : null;

            if (table && !isEmpty) {
                const dataTableManager = new window.DataTableManager();
                usersTable = dataTableManager.init('#users-table', {
                    order: [[1, 'asc']], // Sort by Name column
                    columnDefs: [
                        { orderable: false, targets: [4] }, // Disable sorting for Actions column
                        { type: 'string', targets: [1, 2, 3] }, // String sorting for name, email, role
                        { type: 'num', targets: [0] } // Numeric sorting for ID
                    ],
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                    },
                    searching: true, // Enable built-in search
                    search: {
                        search: '{{ request('search') }}' // Apply initial search if exists
                    }
                });
            }
        }
        
        // Connect custom search input to DataTables search
        const searchInput = document.getElementById('search-input');
        if (searchInput && usersTable) {
            searchInput.addEventListener('keyup', function() {
                usersTable.search(this.value).draw();
            });
            
            // Clear search when input is cleared
            searchInput.addEventListener('input', function() {
                if (this.value === '') {
                    usersTable.search('').draw();
                }
            });
        }
        
        // Menambahkan konfirmasi sebelum submit form hapus
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