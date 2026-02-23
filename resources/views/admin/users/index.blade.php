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

    <!-- Filter Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-3">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari Nama, Email, atau ID..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                </div>
                @if(request()->hasAny(['search', 'role']))
                <div class="col-md-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary me-3">
                            <i class="bi bi-person-video2 fs-4"></i>
                        </div>
                        <h6 class="card-subtitle text-muted mb-0">Total Instruktur</h6>
                    </div>
                    <h3 class="card-title fw-bold mb-0 text-dark">{{ $statistics['total_instructors'] }}</h3>
                    <small class="text-success"><i class="bi bi-people"></i> Terdaftar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success me-3">
                            <i class="bi bi-patch-check fs-4"></i>
                        </div>
                        <h6 class="card-subtitle text-muted mb-0">Terverifikasi</h6>
                    </div>
                    <h3 class="card-title fw-bold mb-0 text-dark">{{ $statistics['approved_instructors'] }}</h3>
                    <small class="text-success"><i class="bi bi-check-circle"></i> Disetujui</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning me-3">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <h6 class="card-subtitle text-muted mb-0">Menunggu</h6>
                    </div>
                    <h3 class="card-title fw-bold mb-0 text-dark">{{ $statistics['pending_verification'] }}</h3>
                    <small class="text-warning"><i class="bi bi-hourglass-split"></i> Perlu Tindakan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-3 text-danger me-3">
                            <i class="bi bi-x-circle fs-4"></i>
                        </div>
                        <h6 class="card-subtitle text-muted mb-0">Ditolak</h6>
                    </div>
                    <h3 class="card-title fw-bold mb-0 text-dark">{{ $statistics['rejected_instructors'] }}</h3>
                    <small class="text-danger"><i class="bi bi-x"></i> Tidak Memenuhi</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover" id="admin-users-table">
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
                                    @elseif(in_array($user->role, ['admin', 'admin_sistem', 'admin_erlass']))
                                        <span class="badge bg-warning me-2">
                                            <i class="bi bi-shield-check"></i>
                                        </span>
                                    @elseif($user->role === 'instruktur')
                                        <span class="badge bg-info me-2">
                                            <i class="bi bi-person-video2"></i>
                                        </span>
                                    @elseif($user->role === 'sales')
                                        <span class="badge bg-success me-2">
                                            <i class="bi bi-graph-up-arrow"></i>
                                        </span>
                                    @else
                                        <span class="badge bg-secondary me-2">
                                            <i class="bi bi-person"></i>
                                        </span>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.users.show', $user) }}" class="fw-medium text-decoration-none">{{ $user->nama_lengkap }}</a>
                                        <div class="small text-muted">{{ $user->kompetensi_1 ?? $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'webmaster')
                                    <span class="badge bg-danger">Webmaster</span>
                                @elseif(in_array($user->role, ['admin', 'admin_sistem', 'admin_erlass']))
                                    <span class="badge bg-warning">Administrator</span>
                                @elseif($user->role === 'instruktur')
                                    <span class="badge bg-info">Instruktur</span>
                                @elseif($user->role === 'sales')
                                    <span class="badge bg-success">Sales</span>
                                @elseif($user->role === 'debug_user')
                                    <span class="badge bg-secondary">Debug User</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ ucfirst($user->role) }}</span>
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

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse($users as $user)
                    <div class="card mb-3 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center">
                                    @if($user->role === 'webmaster')
                                        <span class="badge bg-danger me-2"><i class="bi bi-crown-fill"></i></span>
                                    @elseif(in_array($user->role, ['admin', 'admin_sistem', 'admin_erlass']))
                                        <span class="badge bg-warning me-2"><i class="bi bi-shield-check"></i></span>
                                    @elseif($user->role === 'instruktur')
                                        <span class="badge bg-info me-2"><i class="bi bi-person-video2"></i></span>
                                    @elseif($user->role === 'sales')
                                        <span class="badge bg-success me-2"><i class="bi bi-graph-up-arrow"></i></span>
                                    @else
                                        <span class="badge bg-secondary me-2"><i class="bi bi-person"></i></span>
                                    @endif
                                    <div>
                                        <h6 class="fw-bold mb-0">{{ $user->nama_lengkap }}</h6>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                                <div>
                                    @if($user->role === 'instruktur')
                                        @if($user->verification_status === 'approved')
                                            <span class="badge bg-success"><i class="bi bi-check-lg"></i></span>
                                        @elseif($user->verification_status === 'pending')
                                            <span class="badge bg-warning"><i class="bi bi-clock"></i></span>
                                        @elseif($user->verification_status === 'rejected')
                                            <span class="badge bg-danger"><i class="bi bi-x-lg"></i></span>
                                        @endif
                                    @else
                                        <span class="badge bg-success"><i class="bi bi-check-lg"></i></span>
                                    @endif
                                </div>
                            </div>
                            
                            <hr class="my-2 text-muted opacity-25">

                            <div class="row g-2 small mb-3">
                                <div class="col-6">
                                    <div class="text-muted mb-1">Role</div>
                                    <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted mb-1">Bergabung</div>
                                    <div class="fw-semibold">{{ $user->created_at->format('d M Y') }}</div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                @can('view', $user)
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info flex-grow-1">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                @endcan
                                @can('update', $user)
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @endcan
                                @can('delete', $user)
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                                                onsubmit="return confirm('Yakin ingin menghapus user ini?')" class="flex-grow-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded shadow-sm">
                        <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
                        <p class="mb-1 fw-bold">Tidak ada pengguna ditemukan</p>
                    </div>
                @endforelse
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
                paging: false,       // Disable DataTables pagination (using Laravel's instead)
                info: false,         // Disable DataTables info display
                searching: false,    // Disable DataTables search (using custom server-side search)
                lengthChange: false, // Disable page length dropdown
                columnDefs: [
                    { orderable: false, targets: [0, 6] }, // Disable sorting for # and Actions columns
                    { type: 'string', targets: [1, 2, 3, 4] }, // String sorting for name, email, role, status
                    { type: 'date', targets: [5] } // Date sorting for registration date
                ]
            });
        }
    });
</script>
@endpush