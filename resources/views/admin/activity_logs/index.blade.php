@extends('layouts.app')

@section('title', 'Log Pergerakan Admin & Webmaster')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <span class="text-gradient-primary"><i class="bi bi-shield-lock-fill me-2 text-danger"></i>Log Audit Pergerakan Admin</span>
            </h1>
            <p class="text-muted mb-0">Pantau seluruh aktivitas, perubahan data, dan pergerakan akun Admin & Webmaster secara real-time.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-eye-fill me-1"></i> Mode Akses: Webmaster Only
            </span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card glass-card border-0 mb-4 shadow-sm">
        <div class="card-body p-4">
            <h6 class="card-title fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                <i class="bi bi-funnel text-primary"></i> Filter Audit Log
            </h6>
            <form action="{{ route('admin.activity-logs.index') }}" method="GET" id="filterLogForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="role_filter" class="form-label small text-muted text-uppercase fw-bold">Filter Pergerakan Role</label>
                        <select name="role_filter" id="role_filter" class="form-select border-light-subtle bg-white fw-semibold" onchange="this.form.submit()">
                            <option value="admin_only" @selected(request('role_filter', 'admin_only') == 'admin_only')>🛡️ Khusus Admin & Webmaster</option>
                            <option value="all" @selected(request('role_filter') == 'all')>🌐 Semua Pengguna (All Roles)</option>
                            <option value="admin" @selected(request('role_filter') == 'admin')>👤 Admin</option>
                            <option value="admin_sistem" @selected(request('role_filter') == 'admin_sistem')>⚡ Admin Sistem</option>
                            <option value="webmaster" @selected(request('role_filter') == 'webmaster')>👑 Webmaster</option>
                            <option value="instruktur" @selected(request('role_filter') == 'instruktur')>🎓 Instruktur</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="user_id" class="form-label small text-muted text-uppercase fw-bold">Pilih Pengguna</label>
                        <select name="user_id" id="user_id" class="form-select border-light-subtle bg-white" onchange="this.form.submit()">
                            <option value="">Semua User</option>
                            @foreach(($roleFilter === 'admin_only' ? $adminUsers : $users) as $usr)
                                <option value="{{ $usr->id }}" @selected(request('user_id') == $usr->id)>
                                    {{ $usr->nama_lengkap }} ({{ strtoupper($usr->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="search" class="form-label small text-muted text-uppercase fw-bold">Kata Kunci</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-light-subtle"><i class="bi bi-search text-primary"></i></span>
                            <input type="text" name="search" id="search" class="form-control border-light-subtle bg-white" placeholder="Deskripsi / Action / IP..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="date" class="form-label small text-muted text-uppercase fw-bold">Tanggal</label>
                        <input type="date" class="form-control border-light-subtle bg-white" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
                    </div>
                </div>

                <div class="row g-3 mt-1 align-items-center">
                    <div class="col-md-2">
                        <label for="per_page" class="form-label small text-muted text-uppercase fw-bold">Tampilkan</label>
                        <select name="per_page" id="per_page" class="form-select border-light-subtle bg-white" onchange="this.form.submit()">
                            <option value="25" @selected(request('per_page', 25) == 25)>25 Baris</option>
                            <option value="50" @selected(request('per_page') == 50)>50 Baris</option>
                            <option value="100" @selected(request('per_page') == 100)>100 Baris</option>
                            <option value="all" @selected(request('per_page') == 'all')>⚡ Semua Data</option>
                        </select>
                    </div>
                    <div class="col-md-10 d-flex justify-content-end align-items-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Terapkan Filter</button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-light text-muted border border-light-subtle" title="Reset Filter"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card glass-card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">Waktu Log</th>
                            <th width="20%">User / Pelaku</th>
                            <th width="15%">Aksi / Event</th>
                            <th width="35%">Deskripsi Pergerakan</th>
                            <th width="15%">IP & Perangkat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.875rem;">
                                    <i class="bi bi-clock-history me-1 text-primary"></i> {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </div>
                                <small class="text-muted" style="font-size: 0.725rem;">
                                    {{ $log->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="fw-bold text-dark">{{ $log->user->nama_lengkap }}</div>
                                    @php
                                        $roleBadge = match($log->user->role) {
                                            'webmaster' => 'bg-danger text-white',
                                            'admin_sistem' => 'bg-warning text-dark',
                                            'admin' => 'bg-primary text-white',
                                            default => 'bg-secondary text-white'
                                        };
                                    @endphp
                                    <span class="badge {{ $roleBadge }} rounded-pill" style="font-size: 0.675rem;">
                                        {{ strtoupper($log->user->role) }}
                                    </span>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.725rem;">{{ $log->user->email }}</small>
                                @else
                                    <span class="badge bg-light text-muted border">System / Guest</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $actionBadge = match(strtolower($log->action)) {
                                        'create', 'store', 'tambah' => 'bg-success bg-opacity-10 text-success border border-success-subtle',
                                        'update', 'edit', 'ubah' => 'bg-info bg-opacity-10 text-info border border-info-subtle',
                                        'delete', 'destroy', 'hapus' => 'bg-danger bg-opacity-10 text-danger border border-danger-subtle',
                                        'login' => 'bg-primary bg-opacity-10 text-primary border border-primary-subtle',
                                        default => 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle'
                                    };
                                @endphp
                                <span class="badge {{ $actionBadge }} px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                    {{ strtoupper($log->action) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-dark fw-medium">{{ $log->description }}</div>
                                @if($log->subject_type)
                                    <small class="text-muted d-block mt-1" style="font-size: 0.725rem;">
                                        <i class="bi bi-link-45deg me-1"></i>Entitas: {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="text-secondary fw-semibold small">
                                    <i class="bi bi-laptop me-1"></i> {{ $log->ip_address ?? '127.0.0.1' }}
                                </div>
                                @if($log->user_agent)
                                    <small class="text-muted text-truncate d-block" style="max-width: 180px; font-size: 0.675rem;" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent }}
                                    </small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Tidak ada log pergerakan admin yang ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination-wrapper :paginator="$logs" class="bg-white border-top py-3" />
        </div>
    </div>
</div>
@endsection
