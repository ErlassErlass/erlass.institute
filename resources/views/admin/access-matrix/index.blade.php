@extends('layouts.app')

@section('title', 'Matrix Akses Role — Erlass Institute')

@section('content')
<style>
/* Header Styling with maximum specificity */
.mx-header-card {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f3460 100%) !important;
    border-radius: 16px !important;
    padding: 2.25rem 2.5rem !important;
    color: #ffffff !important;
    margin-bottom: 2rem !important;
    position: relative !important;
    overflow: hidden !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}
.mx-header-card::after {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.mx-badge-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: rgba(239, 68, 68, 0.25) !important;
    border: 1px solid rgba(239, 68, 68, 0.6) !important;
    color: #fecaca !important;
    padding: 6px 16px !important;
    border-radius: 999px !important;
    font-size: 0.78rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.08em !important;
    margin-bottom: 1.1rem !important;
    text-transform: uppercase !important;
}
.mx-header-title {
    font-size: 1.95rem !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    margin-bottom: 0.6rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    letter-spacing: -0.02em !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
}
.mx-header-subtitle {
    color: #e2e8f0 !important;
    font-size: 0.95rem !important;
    line-height: 1.6 !important;
    max-width: 920px !important;
    margin-bottom: 0 !important;
}

/* Role Cards */
.mx-role-card {
    border-radius: 12px !important;
    padding: 1.1rem 1.25rem !important;
    border: 1.5px solid !important;
    transition: transform 0.18s ease, box-shadow 0.18s ease !important;
    background: #ffffff !important;
}
.mx-role-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.06);
}
.mx-role-card.c-danger  { background: #fff5f5 !important; border-color: #fecaca !important; }
.mx-role-card.c-warning { background: #fffbeb !important; border-color: #fde68a !important; }
.mx-role-card.c-info    { background: #eff6ff !important; border-color: #bfdbfe !important; }
.mx-role-card.c-success { background: #f0fdf4 !important; border-color: #bbf7d0 !important; }

.mx-role-icon { font-size: 1.5rem !important; margin-bottom: 6px !important; }
.mx-role-label { font-weight: 700 !important; font-size: 0.92rem !important; color: #1e293b !important; }
.mx-role-desc  { font-size: 0.77rem !important; color: #64748b !important; line-height: 1.4 !important; }

/* Filter Bar */
.mx-filter-bar {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    margin-bottom: 1.5rem !important;
    padding: 12px 18px !important;
    background: #f8fafc !important;
    border-radius: 12px !important;
    border: 1px solid #e2e8f0 !important;
}
.mx-filter-btn {
    padding: 6px 16px !important;
    border-radius: 8px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all .15s ease !important;
    color: #334155 !important;
}
.mx-filter-btn.active, .mx-filter-btn:hover {
    background: #0f172a !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
}
.mx-stat-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 6px 14px !important;
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    color: #334155 !important;
}

/* Sections */
.mx-section {
    background: #ffffff !important;
    border-radius: 14px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
    margin-bottom: 1.5rem !important;
    overflow: hidden !important;
    border: 1px solid #e2e8f0 !important;
}
.mx-group-header {
    padding: 0.9rem 1.4rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
    border-bottom: 1px solid #e2e8f0 !important;
}
.g-danger    .mx-group-header { background: #fff5f5 !important; color: #dc2626 !important; }
.g-primary   .mx-group-header { background: #eff6ff !important; color: #1d4ed8 !important; }
.g-warning   .mx-group-header { background: #fffbeb !important; color: #d97706 !important; }
.g-info      .mx-group-header { background: #f0f9ff !important; color: #0284c7 !important; }
.g-secondary .mx-group-header { background: #f8fafc !important; color: #475569 !important; }
.g-success   .mx-group-header { background: #f0fdf4 !important; color: #16a34a !important; }

/* Table */
.mx-table { width: 100% !important; border-collapse: collapse !important; font-size: 0.88rem !important; }
.mx-table thead th {
    background: #f8fafc !important;
    padding: 0.75rem 1rem !important;
    font-weight: 700 !important;
    font-size: 0.75rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    color: #64748b !important;
    border-bottom: 2px solid #e2e8f0 !important;
    text-align: center !important;
}
.mx-table thead th:first-child { text-align: left !important; min-width: 280px !important; }
.mx-table tbody tr { border-bottom: 1px solid #f1f5f9 !important; transition: background .12s !important; }
.mx-table tbody tr:last-child { border-bottom: none !important; }
.mx-table tbody tr:hover { background: #f8fafc !important; }
.mx-table td { padding: 0.75rem 1rem !important; color: #334155 !important; vertical-align: middle !important; }
.mx-table td.cc { text-align: center !important; }

/* Icons */
.chk-yes {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 28px !important;
    height: 28px !important;
    border-radius: 50% !important;
    background: #dcfce7 !important;
    color: #16a34a !important;
    font-size: 0.95rem !important;
    font-weight: 800 !important;
}
.chk-no {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 28px !important;
    height: 28px !important;
    border-radius: 50% !important;
    background: #f1f5f9 !important;
    color: #cbd5e1 !important;
    font-size: 0.85rem !important;
}
.count-badge {
    margin-left: auto !important;
    background: rgba(255,255,255,0.7) !important;
    border: 1px solid currentColor !important;
    border-radius: 999px !important;
    padding: 2px 10px !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
}

@media print {
    .mx-filter-bar, nav, .sidebar, header, .btn, .alert { display: none !important; }
    .mx-section { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
}
</style>

<div class="container-fluid py-4" style="max-width: 1280px;">

    {{-- HEADER BANNER --}}
    <div class="mx-header-card" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f3460 100%) !important; color: #ffffff !important; border-radius: 16px; padding: 2.25rem 2.5rem; margin-bottom: 2rem;">
        <div class="mx-badge-pill" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(239, 68, 68, 0.25); border: 1px solid rgba(239, 68, 68, 0.6); color: #fecaca !important; padding: 6px 16px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; letter-spacing: 0.08em; margin-bottom: 1.1rem; text-transform: uppercase;">
            <i class="bi bi-shield-fill-check" style="color: #ef4444; font-size: 0.9rem;"></i>
            <span style="color: #fecaca !important; font-weight: 700;">HANYA WEBMASTER</span>
        </div>
        
        <h1 class="mx-header-title" style="color: #ffffff !important; font-size: 1.95rem; font-weight: 800; margin-bottom: 0.6rem;">
            <span>🔐</span>
            <span style="color: #ffffff !important;">Matrix Akses Role</span>
        </h1>
        
        <p class="mx-header-subtitle" style="color: #e2e8f0 !important; font-size: 0.95rem; line-height: 1.6; margin: 0;">
            Dokumentasi resmi pemetaan hak akses seluruh modul dan fitur dalam sistem Erlass Institute. 
            Halaman ini bersifat <span style="background: rgba(255,255,255,0.15); padding: 2px 8px; border-radius: 6px; color: #ffffff !important; font-weight: 700;">read-only</span> — izin dikontrol secara aman via middleware & Policy Laravel.
        </p>
    </div>

    {{-- ROLE CARDS --}}
    @php
        $roles = [
            'webmaster'    => ['label'=>'Webmaster',    'icon'=>'bi-shield-fill-check', 'color'=>'danger',  'hex'=>'#dc2626', 'desc'=>'Super-admin, akses penuh sistem'],
            'admin_sistem' => ['label'=>'Admin Sistem', 'icon'=>'bi-gear-fill',          'color'=>'warning', 'hex'=>'#d97706', 'desc'=>'IT Admin, kelola sistem & user'],
            'admin'        => ['label'=>'Admin',        'icon'=>'bi-person-badge-fill',  'color'=>'info',    'hex'=>'#0284c7', 'desc'=>'Admin operasional harian'],
            'instruktur'   => ['label'=>'Instruktur',   'icon'=>'bi-person-video3',      'color'=>'success', 'hex'=>'#16a34a', 'desc'=>'Instruktur pengajar ekskul'],
        ];
    @endphp
    <div class="row g-3 mb-4">
        @foreach($roles as $key => $role)
        <div class="col-6 col-md-3">
            <div class="mx-role-card c-{{ $role['color'] }}">
                <div class="mx-role-icon" style="color:{{ $role['hex'] }} !important;"><i class="bi {{ $role['icon'] }}"></i></div>
                <div class="mx-role-label">{{ $role['label'] }}</div>
                <div class="mx-role-desc">{{ $role['desc'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- FILTER BAR --}}
    @php
        $totalItems = collect($matrix)->sum(fn($g) => count($g['items']));
        $wmOnly = collect($matrix)->sum(fn($g) => collect($g['items'])->filter(fn($i) =>
            $i['webmaster'] && !$i['admin_sistem'] && !$i['admin'] && !$i['instruktur']
        )->count());
    @endphp
    <div class="mx-filter-bar">
        <span style="font-size:0.82rem;font-weight:700;color:#334155;margin-right:4px;">Filter Tampilan:</span>
        <button class="mx-filter-btn active" onclick="mxFilter('all',this)">Semua Modul</button>
        @foreach($roles as $key => $role)
        <button class="mx-filter-btn" onclick="mxFilter('{{ $key }}',this)">
            <i class="bi {{ $role['icon'] }}"></i> {{ $role['label'] }}
        </button>
        @endforeach
        
        <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
            <span class="mx-stat-pill"><i class="bi bi-list-check text-primary"></i> <strong>{{ $totalItems }}</strong> fitur</span>
            <span class="mx-stat-pill"><i class="bi bi-shield-lock text-danger"></i> <strong>{{ $wmOnly }}</strong> eksklusif WM</span>
            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>

    {{-- MATRIX GROUPS --}}
    @foreach($matrix as $group)
    <div class="mx-section g-{{ $group['color'] }}">
        <div class="mx-group-header">
            <i class="bi {{ $group['icon'] }} fs-5"></i>
            <span>{{ $group['group'] }}</span>
            <span class="count-badge">{{ count($group['items']) }} fitur</span>
        </div>
        <table class="mx-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Fitur / Aksi</th>
                    @foreach($roles as $key => $role)
                    <th>
                        <i class="bi {{ $role['icon'] }}" style="color:{{ $role['hex'] }} !important; font-size:1.1rem;"></i><br>
                        <span style="color:#475569;">{{ $role['label'] }}</span>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($group['items'] as $item)
                <tr class="mx-row"
                    data-webmaster="{{ $item['webmaster']    ? '1':'0' }}"
                    data-admin_sistem="{{ $item['admin_sistem'] ? '1':'0' }}"
                    data-admin="{{ $item['admin']       ? '1':'0' }}"
                    data-instruktur="{{ $item['instruktur']  ? '1':'0' }}">
                    <td class="fw-medium">{{ $item['label'] }}</td>
                    @foreach(array_keys($roles) as $key)
                    <td class="cc">
                        @if($item[$key])
                            <span class="chk-yes" title="Diizinkan"><i class="bi bi-check-lg"></i></span>
                        @else
                            <span class="chk-no" title="Tidak diizinkan"><i class="bi bi-dash"></i></span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    {{-- FOOTER NOTE --}}
    <div class="alert alert-light border mt-3 d-flex align-items-center gap-3" style="border-radius:12px;font-size:0.86rem;background:#f8fafc;">
        <i class="bi bi-info-circle-fill text-primary fs-4"></i>
        <div>
            <strong>Informasi Otorisasi:</strong> Hak akses dalam matrix ini ditegakkan di level backend melalui <code>RoleMiddleware</code> dan <code>UserPolicy</code>. 
            Jika Anda membutuhkan fitur switch/toggle role dinamis langsung dari UI (database-driven permissions), fitur ini dapat dikembangkan pada update berikutnya.
            <span class="text-muted d-block mt-1">Terakhir disinkronkan: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>
</div>

<script>
function mxFilter(role, btn) {
    document.querySelectorAll('.mx-filter-btn').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    document.querySelectorAll('.mx-section').forEach(function(section) {
        var rows = section.querySelectorAll('.mx-row');
        var vis = 0;
        rows.forEach(function(row) {
            if (role === 'all') { 
                row.style.display = ''; 
                vis++; 
            } else {
                var ok = row.dataset[role] === '1';
                row.style.display = ok ? '' : 'none';
                if (ok) vis++;
            }
        });
        section.style.display = (role !== 'all' && vis === 0) ? 'none' : '';
    });
}
</script>
@endsection
