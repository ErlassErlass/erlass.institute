@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-people-fill me-2 text-primary"></i>
                Manajemen Pengguna
            </h2>
            <p class="text-muted mt-1 mb-0">Kelola semua pengguna sistem</p>
        </div>
        @can('create', App\Models\User::class)
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Tambah Pengguna
            </a>
        @endcan
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">{{ $statistics['total_instructors'] }}</h5>
                            <small class="opacity-75">Total Instruktur</small>
                        </div>
                        <i class="bi bi-person-video2 fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">{{ $statistics['approved_instructors'] }}</h5>
                            <small class="opacity-75">Terverifikasi</small>
                        </div>
                        <i class="bi bi-patch-check fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">{{ $statistics['pending_verification'] }}</h5>
                            <small class="opacity-75">Menunggu Verifikasi</small>
                        </div>
                        <i class="bi bi-clock-history fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">{{ $statistics['rejected_instructors'] }}</h5>
                            <small class="opacity-75">Ditolak</small>
                        </div>
                        <i class="bi bi-x-circle fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable" id="admin-users-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status Verifikasi</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->role === 'webmaster')
                                        <span class="badge bg-danger me-2">
                                            <i class="bi bi-crown-fill"></i>
                                        </span>
                                    @elseif($user->role === 'admin_erlass')
                                        <span class="badge bg-warning me-2">
                                            <i class="bi bi-shield-check"></i>
                                        </span>
                                    @elseif($user->role === 'instruktur')
                                        <span class="badge bg-info me-2">
                                            <i class="bi bi-person-video2"></i>
                                        </span>
                                    @else
                                        <span class="badge bg-secondary me-2">
                                            <i class="bi bi-bug"></i>
                                        </span>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $user->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $user->kompetensi_1 }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'webmaster')
                                    <span class="badge bg-danger">Webmaster</span>
                                @elseif($user->role === 'admin_erlass')
                                    <span class="badge bg-warning">Admin Erlass</span>
                                @elseif($user->role === 'instruktur')
                                    <span class="badge bg-info">Instruktur</span>
                                @elseif($user->role === 'debug_user')
                                    <span class="badge bg-secondary">Debug User</span>
                                @endif
                            </td>
                            <td>
                                @if($user->role === 'instruktur')
                                    @if($user->verification_status === 'approved')
                                        <span class="badge bg-success">
                                            <i class="bi bi-patch-check me-1"></i>Terverifikasi
                                        </span>
                                    @elseif($user->verification_status === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock me-1"></i>Pending
                                        </span>
                                    @elseif($user->verification_status === 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Ditolak
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    </span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('view', $user)
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endcan
                                    @can('update', $user)
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $user)
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                                                  onsubmit="return confirm('Yakin ingin menghapus user ini?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-people fs-2 d-block mb-2"></i>
                                    Belum ada pengguna
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize DataTable for Admin Users table
        if (typeof window.DataTableManager !== 'undefined') {
            const dataTableManager = new window.DataTableManager();
            dataTableManager.init('#admin-users-table', {
                order: [[1, 'asc']], // Sort by Name column
                columnDefs: [
                    { orderable: false, targets: [0, 6] }, // Disable sorting for # and Actions columns
                    { type: 'string', targets: [1, 2, 3, 4] }, // String sorting for name, email, role, status
                    { type: 'date', targets: [5] } // Date sorting for registration date
                ],
                pageLength: 25
            });
        }
    });
</script>
@endpush