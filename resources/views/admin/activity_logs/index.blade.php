@extends('layouts.app')

@section('title', 'Log Pergerakan Admin & Webmaster')

@section('content')
@php
    $todayTotal = \App\Models\ActivityLog::whereDate('created_at', today())->count();
    $todayChanges = \App\Models\ActivityLog::whereDate('created_at', today())
        ->whereIn('action', ['update', 'delete', 'edit', 'destroy', 'ubah', 'hapus', 'store', 'create', 'tambah'])
        ->count();
    $activeAdminCount = \App\Models\User::whereIn('role', ['webmaster', 'admin_sistem', 'admin'])
        ->where('status', 'Aktif')
        ->count();

    $activeFiltersCount = 0;
    if (request('role_filter') && request('role_filter') !== 'admin_only') $activeFiltersCount++;
    if (request('user_id')) $activeFiltersCount++;
    if (request('search')) $activeFiltersCount++;
    if (request('date')) $activeFiltersCount++;
    if (request('per_page') && request('per_page') != 25) $activeFiltersCount++;
@endphp

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-semibold small">
                    <i class="bi bi-shield-lock-fill me-1"></i> Webmaster Audit Trail
                </span>
                @if($activeFiltersCount > 0)
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2.5 py-1.5 rounded-pill fw-semibold small">
                        <i class="bi bi-funnel-fill me-1"></i> {{ $activeFiltersCount }} Filter Aktif
                    </span>
                @endif
            </div>
            <h1 class="h3 fw-bold mb-1 text-dark">
                Log Audit Pergerakan Admin
            </h1>
            <p class="text-muted mb-0 small">Pantau seluruh aktivitas, perubahan data, dan pergerakan akun Admin & Webmaster secara real-time.</p>
        </div>

        <!-- Quick Stat Chips (Impeccable Visual Insight) -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="stat-chip bg-white border border-light-subtle rounded-3 px-3 py-2 shadow-sm d-flex align-items-center gap-2">
                <div class="chip-icon bg-primary bg-opacity-10 text-primary rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-activity fs-6"></i>
                </div>
                <div>
                    <div class="text-muted small lh-1">Log Hari Ini</div>
                    <div class="fw-bold text-dark fs-6 lh-1 mt-1">{{ number_format($todayTotal) }}</div>
                </div>
            </div>

            <div class="stat-chip bg-white border border-light-subtle rounded-3 px-3 py-2 shadow-sm d-flex align-items-center gap-2">
                <div class="chip-icon bg-warning bg-opacity-10 text-warning rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-pencil-square fs-6"></i>
                </div>
                <div>
                    <div class="text-muted small lh-1">Perubahan Data</div>
                    <div class="fw-bold text-dark fs-6 lh-1 mt-1">{{ number_format($todayChanges) }}</div>
                </div>
            </div>

            <div class="stat-chip bg-white border border-light-subtle rounded-3 px-3 py-2 shadow-sm d-flex align-items-center gap-2">
                <div class="chip-icon bg-success bg-opacity-10 text-success rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-shield-check fs-6"></i>
                </div>
                <div>
                    <div class="text-muted small lh-1">Admin Aktif</div>
                    <div class="fw-bold text-dark fs-6 lh-1 mt-1">{{ number_format($activeAdminCount) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card glass-card border-0 mb-4 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="card-title fw-bold mb-0 d-flex align-items-center gap-2 text-dark">
                    <i class="bi bi-funnel text-primary"></i> Filter Audit Log
                </h6>
                @if($activeFiltersCount > 0)
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-link btn-sm text-danger text-decoration-none p-0 fw-semibold">
                        <i class="bi bi-x-circle me-1"></i> Bersihkan Semua Filter
                    </a>
                @endif
            </div>

            <form action="{{ route('admin.activity-logs.index') }}" method="GET" id="filterLogForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="role_filter" class="form-label small text-muted text-uppercase fw-bold">Filter Role</label>
                        <select name="role_filter" id="role_filter" class="form-select border-light-subtle bg-white fw-semibold" onchange="this.form.submit()">
                            <option value="admin_only" @selected(request('role_filter', 'admin_only') == 'admin_only')>Khusus Admin & Webmaster</option>
                            <option value="all" @selected(request('role_filter') == 'all')>Semua Pengguna (All Roles)</option>
                            <option value="admin" @selected(request('role_filter') == 'admin')>Admin</option>
                            <option value="admin_sistem" @selected(request('role_filter') == 'admin_sistem')>Admin Sistem</option>
                            <option value="webmaster" @selected(request('role_filter') == 'webmaster')>Webmaster</option>
                            <option value="instruktur" @selected(request('role_filter') == 'instruktur')>Instruktur</option>
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
                        <input type="date" class="form-control border-light-subtle bg-white" name="date" id="date" value="{{ request('date') }}" onchange="this.form.submit()">
                    </div>
                </div>

                <div class="row g-3 mt-1 align-items-center">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center gap-2">
                            <label for="per_page" class="form-label small text-muted text-uppercase fw-bold mb-0 text-nowrap">Tampilkan:</label>
                            <select name="per_page" id="per_page" class="form-select form-select-sm border-light-subtle bg-white" onchange="this.form.submit()">
                                <option value="25" @selected(request('per_page', 25) == 25)>25 Baris</option>
                                <option value="50" @selected(request('per_page') == 50)>50 Baris</option>
                                <option value="100" @selected(request('per_page') == 100)>100 Baris</option>
                                <option value="all" @selected(request('per_page') == 'all')>Semua Data</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-9 d-flex justify-content-end align-items-center gap-2">
                        <button type="submit" class="btn btn-primary px-3 shadow-sm" style="min-height: 40px;">
                            <i class="bi bi-funnel me-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary px-3" style="min-height: 40px;" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card glass-card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-uppercase small text-muted">
                            <th width="16%" class="ps-4">Waktu Log</th>
                            <th width="22%">User / Pelaku</th>
                            <th width="16%">Aksi / Event</th>
                            <th width="32%">Deskripsi Pergerakan</th>
                            <th width="14%" class="pe-4">IP & Perangkat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark" style="font-size: 0.875rem;">
                                    <i class="bi bi-clock-history me-1 text-primary"></i> {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </div>
                                <small class="text-muted" style="font-size: 0.725rem;">
                                    {{ $log->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                @if($log->user)
                                    @php
                                        $initials = strtoupper(substr($log->user->nama_lengkap, 0, 2));
                                        $gradientClass = match($log->user->role) {
                                            'webmaster' => 'background: linear-gradient(135deg, #d63031, #ff7675); color: white;',
                                            'admin_sistem' => 'background: linear-gradient(135deg, #d35400, #f39c12); color: white;',
                                            'admin' => 'background: linear-gradient(135deg, #0984e3, #74b9ff); color: white;',
                                            default => 'background: linear-gradient(135deg, #636e72, #b2bec3); color: white;'
                                        };
                                        $roleBadge = match($log->user->role) {
                                            'webmaster' => 'bg-danger text-white',
                                            'admin_sistem' => 'bg-warning text-dark',
                                            'admin' => 'bg-primary text-white',
                                            default => 'bg-secondary text-white'
                                        };
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-chip rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.8rem; {{ $gradientClass }}">
                                            {{ $initials }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 180px;" title="{{ $log->user->nama_lengkap }}">{{ $log->user->nama_lengkap }}</div>
                                            <div class="d-flex align-items-center gap-1 mt-0.5">
                                                <span class="badge {{ $roleBadge }} rounded-pill" style="font-size: 0.65rem;">
                                                    {{ strtoupper($log->user->role) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1">
                                        <i class="bi bi-robot me-1"></i> System / Guest
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $actionLower = strtolower($log->action);
                                    $actionConfig = match(true) {
                                        str_contains($actionLower, 'create') || str_contains($actionLower, 'store') || str_contains($actionLower, 'tambah') => [
                                            'badge' => 'bg-success bg-opacity-10 text-success border border-success-subtle',
                                            'icon' => 'bi-plus-circle-fill'
                                        ],
                                        str_contains($actionLower, 'update') || str_contains($actionLower, 'edit') || str_contains($actionLower, 'ubah') => [
                                            'badge' => 'bg-info bg-opacity-10 text-info border border-info-subtle',
                                            'icon' => 'bi-pencil-square'
                                        ],
                                        str_contains($actionLower, 'delete') || str_contains($actionLower, 'destroy') || str_contains($actionLower, 'hapus') => [
                                            'badge' => 'bg-danger bg-opacity-10 text-danger border border-danger-subtle',
                                            'icon' => 'bi-trash-fill'
                                        ],
                                        str_contains($actionLower, 'login') => [
                                            'badge' => 'bg-primary bg-opacity-10 text-primary border border-primary-subtle',
                                            'icon' => 'bi-box-arrow-in-right'
                                        ],
                                        default => [
                                            'badge' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle',
                                            'icon' => 'bi-activity'
                                        ]
                                    };
                                @endphp
                                <span class="badge {{ $actionConfig['badge'] }} px-2.5 py-1.5 fw-bold d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                    <i class="bi {{ $actionConfig['icon'] }}"></i>
                                    {{ strtoupper($log->action) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-dark fw-medium">{{ $log->description }}</div>
                                @if($log->subject_type)
                                    <small class="text-muted d-inline-block mt-1 bg-light px-2 py-0.5 rounded border border-light-subtle" style="font-size: 0.725rem;">
                                        <i class="bi bi-layers me-1 text-primary"></i>Entitas: <strong>{{ class_basename($log->subject_type) }}</strong> #{{ $log->subject_id }}
                                    </small>
                                @endif
                            </td>
                            <td class="pe-4">
                                <div class="text-secondary fw-semibold small">
                                    <i class="bi bi-hdd-network me-1 text-primary"></i> {{ $log->ip_address ?? '127.0.0.1' }}
                                </div>
                                @if($log->user_agent)
                                    @php
                                        $ua = $log->user_agent;
                                        $os = 'Desktop';
                                        $osIcon = 'bi-laptop';
                                        if (stripos($ua, 'Windows') !== false) { $os = 'Windows'; $osIcon = 'bi-windows'; }
                                        elseif (stripos($ua, 'Macintosh') !== false || stripos($ua, 'Mac OS') !== false) { $os = 'macOS'; $osIcon = 'bi-apple'; }
                                        elseif (stripos($ua, 'Android') !== false) { $os = 'Android'; $osIcon = 'bi-android2'; }
                                        elseif (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) { $os = 'iOS'; $osIcon = 'bi-apple'; }
                                        elseif (stripos($ua, 'Linux') !== false) { $os = 'Linux'; $osIcon = 'bi-terminal'; }

                                        $browser = 'Browser';
                                        $browserIcon = 'bi-globe';
                                        if (stripos($ua, 'Chrome') !== false && stripos($ua, 'Edg') === false) { $browser = 'Chrome'; $browserIcon = 'bi-browser-chrome'; }
                                        elseif (stripos($ua, 'Edg') !== false) { $browser = 'Edge'; $browserIcon = 'bi-browser-edge'; }
                                        elseif (stripos($ua, 'Firefox') !== false) { $browser = 'Firefox'; $browserIcon = 'bi-browser-firefox'; }
                                        elseif (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) { $browser = 'Safari'; $browserIcon = 'bi-compass'; }
                                    @endphp
                                    <div class="mt-1" title="{{ $ua }}" data-bs-toggle="tooltip">
                                        <span class="badge bg-light text-secondary border border-light-subtle px-2 py-1" style="font-size: 0.675rem;">
                                            <i class="bi {{ $osIcon }} me-1"></i>{{ $os }} &bull; <i class="bi {{ $browserIcon }} ms-1 me-1"></i>{{ $browser }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="py-3">
                                    <i class="bi bi-clipboard-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    <h6 class="fw-bold text-dark mb-1">Tidak ada log pergerakan admin yang ditemukan</h6>
                                    <p class="small text-muted mb-3">Coba ubah parameter filter atau reset pencarian Anda.</p>
                                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination-wrapper :paginator="$logs" class="bg-white border-top py-3" />
        </div>
    </div>
</div>

<style>
    .stat-chip {
        transition: all 0.25s ease;
    }
    .stat-chip:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
    }
    .table-modern tbody tr {
        transition: background-color 0.2s ease;
    }
    .avatar-chip {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
