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
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Tambah Pengguna
            </a>
        @endcan
    </div>

    <!-- Filter Section -->
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('users.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-search me-1"></i> Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari Nama, Email, atau ID Instruktur..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-shield-lock me-1"></i> Role</label>
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-patch-check me-1"></i> Status / Verifikasi</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-geo-alt me-1"></i> Kota / Domisili</label>
                    <select name="kota" class="form-select">
                        <option value="">Semua Kota Domisili</option>
                        @foreach($kotas as $kota)
                            <option value="{{ $kota }}" {{ request('kota') == $kota ? 'selected' : '' }}>{{ $kota }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-calendar-event me-1"></i> Penugasan Mengajar</label>
                    <select name="penugasan" class="form-select">
                        <option value="">Semua Status Penugasan</option>
                        <option value="assigned" {{ request('penugasan') == 'assigned' ? 'selected' : '' }}>🟢 Punya Jadwal Mengajar</option>
                        <option value="unassigned" {{ request('penugasan') == 'unassigned' ? 'selected' : '' }}>⚪ Belum Ada Jadwal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-sort-down me-1"></i> Urutkan Data</label>
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>🕒 Terbaru Mendaftar</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>⌛ Terlama</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>🔤 Nama (A - Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>🔤 Nama (Z - A)</option>
                    </select>
                </div>
                
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary fw-bold px-4 flex-grow-1">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                    @if(request()->hasAny(['search', 'role', 'status', 'kota', 'penugasan', 'sort']))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                    @endif
                </div>
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
                <table class="table table-hover" id="users-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Domisili</th>
                            <th>Status & Verifikasi</th>
                            <th>Masa Aktif</th>
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
                                    @else
                                        <span class="badge bg-secondary me-2">
                                            <i class="bi bi-person"></i>
                                        </span>
                                    @endif
                                    <div>
                                        <a href="{{ route('users.show', $user) }}" class="fw-medium text-decoration-none">{{ $user->nama_lengkap }}</a>
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
                                @elseif($user->role === 'debug_user')
                                    <span class="badge bg-secondary">Debug User</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ ucfirst($user->role) }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $user->instructorProfile->kota_domisili ?? '-' }}
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1 align-items-start">
                                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                        <i class="bi {{ $user->is_active ? 'bi-check-circle' : 'bi-dash-circle' }} me-1"></i>{{ $user->is_active ? 'Aktif' : $user->status }}
                                    </span>
                                    @if($user->role === 'instruktur')
                                        @if($user->verification_status === 'approved')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                <i class="bi bi-patch-check me-1"></i>Terverifikasi
                                            </span>
                                        @elseif($user->verification_status === 'pending')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                                <i class="bi bi-clock me-1"></i>Pending
                                            </span>
                                        @elseif($user->verification_status === 'rejected')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                <i class="bi bi-x-circle me-1"></i>Ditolak
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                                <i class="bi bi-question-circle me-1"></i>Belum Diverifikasi
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($user->tanggal_aktif || $user->tanggal_nonaktif)
                                    @if($user->tanggal_aktif)
                                        <div class="small"><span class="text-muted">Mulai:</span> {{ $user->tanggal_aktif->format('d M Y') }}</div>
                                    @endif
                                    @if($user->tanggal_nonaktif)
                                        <div class="small"><span class="text-muted">Akhir:</span> {{ $user->tanggal_nonaktif->format('d M Y') }}</div>
                                    @endif
                                @else
                                    <div class="small"><span class="text-muted">Daftar:</span> {{ $user->created_at->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('view', $user)
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endcan
                                    @can('update', $user)
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $user)
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" 
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
                        <tr class="empty-state">
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
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-info flex-grow-1">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                @endcan
                                @can('update', $user)
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @endcan
                                @can('delete', $user)
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" 
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

            <x-pagination-wrapper :paginator="$users->appends(request()->query())" class="bg-white border-top py-3" />
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize DataTable for Users table
        if (typeof window.DataTableManager !== 'undefined') {
            const table = document.getElementById('users-table');
            const isEmpty = table ? table.querySelector('.empty-state') : null;

            if (table && !isEmpty) {
                const dataTableManager = new window.DataTableManager();
                dataTableManager.init('#users-table', {
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
        }
    });
</script>
@endpush