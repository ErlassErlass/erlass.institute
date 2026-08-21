@extends('layouts.app')

@section('title', 'Matrix Akses Role — Erlass Institute')

@section('content')
<style>
.mx-header {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
    border-radius: 16px;
    padding: 2rem 2.5rem;
    color: #fff;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.mx-badge-root {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(239,68,68,0.25);
    border: 1.5px solid rgba(239,68,68,0.6);
    color: #fca5a5;
    padding: 5px 16px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    margin-bottom: 1rem;
    text-transform: uppercase;
}
.mx-title { font-size: 1.75rem; font-weight: 800; margin-bottom: 0.4rem; }
.mx-desc  { color: rgba(255,255,255,0.6); font-size: 0.9rem; margin: 0; }
.mx-role-card { border-radius: 12px; padding: 1rem 1.25rem; border: 1.5px solid; margin-bottom: 0; }
.mx-role-card.c-danger  { background:#fff5f5; border-color:#fca5a5; }
.mx-role-card.c-warning { background:#fffbeb; border-color:#fde68a; }
.mx-role-card.c-info    { background:#eff6ff; border-color:#93c5fd; }
.mx-role-card.c-success { background:#f0fdf4; border-color:#86efac; }
.mx-role-icon  { font-size: 1.5rem; margin-bottom: 6px; }
.mx-role-label { font-weight: 700; font-size: 0.9rem; }
.mx-role-desc  { font-size: 0.76rem; color:#6b7280; line-height:1.4; }
.mx-filter-bar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:1.5rem; padding:12px 16px; background:#f8f9fb; border-radius:12px; border:1px solid #e5e7eb; }
.mx-filter-btn { padding:5px 16px; border-radius:8px; border:1px solid #d1d5db; background:#fff; font-size:0.8rem; font-weight:600; cursor:pointer; transition:all .18s; color:#374151; }
.mx-filter-btn.active, .mx-filter-btn:hover { background:#1e3a5f; color:#fff; border-color:#1e3a5f; }
.mx-stat { display:inline-flex; align-items:center; gap:5px; background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:5px 14px; font-size:0.8rem; font-weight:600; color:#374151; }
.mx-section { background:#fff; border-radius:14px; box-shadow:0 1px 6px rgba(0,0,0,.07); margin-bottom:1.25rem; overflow:hidden; border:1px solid #f0f0f0; }
.mx-group-header { padding:0.8rem 1.25rem; display:flex; align-items:center; gap:10px; font-weight:700; font-size:0.92rem; border-bottom:1px solid #f0f0f0; }
.g-danger    .mx-group-header { background:#fff5f5; color:#dc2626; }
.g-primary   .mx-group-header { background:#eff6ff; color:#1d4ed8; }
.g-warning   .mx-group-header { background:#fffbeb; color:#d97706; }
.g-info      .mx-group-header { background:#f0f9ff; color:#0369a1; }
.g-secondary .mx-group-header { background:#f8fafc; color:#475569; }
.g-success   .mx-group-header { background:#f0fdf4; color:#15803d; }
.mx-table { width:100%; border-collapse:collapse; font-size:0.875rem; }
.mx-table thead th { background:#f8f9fb; padding:0.6rem 0.9rem; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:#6b7280; border-bottom:2px solid #e5e7eb; text-align:center; }
.mx-table thead th:first-child { text-align:left; min-width:260px; }
.mx-table tbody tr { border-bottom:1px solid #f3f4f6; transition:background .15s; }
.mx-table tbody tr:last-child { border-bottom:none; }
.mx-table tbody tr:hover { background:#fafafa; }
.mx-table td { padding:0.65rem 0.9rem; color:#374151; vertical-align:middle; }
.mx-table td.cc { text-align:center; }
.chk-yes { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:50%; background:#dcfce7; color:#16a34a; font-size:0.85rem; font-weight:800; }
.chk-no  { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:50%; background:#f3f4f6; color:#d1d5db; font-size:0.8rem; }
.count-badge { margin-left:auto; background:rgba(255,255,255,0.5); border:1px solid currentColor; border-radius:999px; padding:1px 10px; font-size:0.7rem; font-weight:600; opacity:0.7; }
@media print { .mx-filter-bar,nav,.sidebar,header,.btn{display:none!important;} .mx-section{box-shadow:none;border:1px solid #e5e7eb;} }
</style>

<div class="container-fluid py-4" style="max-width:1280px;">

    {{-- HEADER --}}
    <div class="mx-header">
        <div class="mx-badge-root">
            <i class="bi bi-shield-fill-check"></i>
            HANYA WEBMASTER
        </div>
        <h1 class="mx-title">🔐 Matrix Akses Role</h1>
        <p class="mx-desc">
            Dokumentasi resmi siapa yang dapat mengakses setiap modul dan fitur dalam sistem Erlass Institute.
            Halaman ini bersifat <strong style="color:#fff;">read-only</strong> — perubahan hak akses dilakukan melalui kode program.
        </p>
    </div>

    {{-- ROLE CARDS --}}
    @php
        $roles = [
            'webmaster'    => ['label'=>'Webmaster',    'icon'=>'bi-shield-fill-check', 'color'=>'danger',  'hex'=>'#dc2626', 'desc'=>'Super-admin, akses penuh sistem'],
            'admin_sistem' => ['label'=>'Admin Sistem', 'icon'=>'bi-gear-fill',          'color'=>'warning', 'hex'=>'#d97706', 'desc'=>'IT Admin, kelola sistem & user'],
            'admin'        => ['label'=>'Admin',        'icon'=>'bi-person-badge-fill',  'color'=>'info',    'hex'=>'#0369a1', 'desc'=>'Admin operasional harian'],
            'instruktur'   => ['label'=>'Instruktur',   'icon'=>'bi-person-video3',      'color'=>'success', 'hex'=>'#15803d', 'desc'=>'Instruktur pengajar ekskul'],
        ];
    @endphp
    <div class="row g-3 mb-3">
        @foreach($roles as $key => $role)
        <div class="col-6 col-md-3">
            <div class="mx-role-card c-{{ $role['color'] }}">
                <div class="mx-role-icon" style="color:{{ $role['hex'] }}"><i class="bi {{ $role['icon'] }}"></i></div>
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
        <span style="font-size:0.8rem;font-weight:700;color:#374151;">Filter:</span>
        <button class="mx-filter-btn active" onclick="mxFilter('all',this)">Semua Modul</button>
        @foreach($roles as $key => $role)
        <button class="mx-filter-btn" onclick="mxFilter('{{ $key }}',this)">
            <i class="bi {{ $role['icon'] }}"></i> {{ $role['label'] }}
        </button>
        @endforeach
        <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
            <span class="mx-stat"><i class="bi bi-list-check text-primary"></i> {{ $totalItems }} fitur</span>
            <span class="mx-stat"><i class="bi bi-shield-lock text-danger"></i> {{ $wmOnly }} eksklusif WM</span>
            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>

    {{-- MATRIX GROUPS --}}
    @foreach($matrix as $group)
    <div class="mx-section g-{{ $group['color'] }}">
        <div class="mx-group-header">
            <i class="bi {{ $group['icon'] }} fs-5"></i>
            {{ $group['group'] }}
            <span class="count-badge">{{ count($group['items']) }} fitur</span>
        </div>
        <table class="mx-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Fitur / Aksi</th>
                    @foreach($roles as $key => $role)
                    <th>
                        <i class="bi {{ $role['icon'] }}" style="color:{{ $role['hex'] }}"></i><br>
                        {{ $role['label'] }}
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
                    <td>{{ $item['label'] }}</td>
                    @foreach(array_keys($roles) as $key)
                    <td class="cc">
                        @if($item[$key])
                            <span class="chk-yes"><i class="bi bi-check-lg"></i></span>
                        @else
                            <span class="chk-no"><i class="bi bi-dash"></i></span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    <div class="alert alert-light border mt-2" style="border-radius:12px;font-size:0.83rem;">
        <i class="bi bi-info-circle-fill text-primary me-2"></i>
        <strong>Catatan:</strong> Matrix ini adalah dokumentasi hak akses yang <em>hardcoded</em> dalam sistem.
        Untuk mengubah izin akses, lakukan melalui kode program di <code>routes/web.php</code>, middleware, atau Policy.
        <span class="text-muted ms-2">— {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
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
            if (role === 'all') { row.style.display = ''; vis++; }
            else {
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
