@extends('layouts.app')

@section('title', 'Matrix Akses Role — Erlass Institute')

@push('styles')
<style>
    /* ── Page Header ── */
    .matrix-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        border-radius: 16px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .matrix-header::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .matrix-header .badge-root {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(239,68,68,0.2);
        border: 1px solid rgba(239,68,68,0.4);
        color: #fca5a5;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    /* ── Role Legend Cards ── */
    .role-card {
        border-radius: 12px;
        padding: 1rem 1.2rem;
        border: 1px solid;
        transition: transform .2s;
    }
    .role-card:hover { transform: translateY(-2px); }
    .role-card.danger  { background: #fff5f5; border-color: #fecaca; }
    .role-card.warning { background: #fffbeb; border-color: #fde68a; }
    .role-card.info    { background: #eff6ff; border-color: #bfdbfe; }
    .role-card.success { background: #f0fdf4; border-color: #bbf7d0; }
    .role-card .role-icon { font-size: 1.4rem; margin-bottom: 0.4rem; }

    /* ── Matrix Table ── */
    .matrix-section {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 8px rgba(0,0,0,.06);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .matrix-section-header {
        padding: 0.85rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 0.92rem;
        letter-spacing: 0.02em;
        border-bottom: 1px solid #f0f0f0;
    }
    .matrix-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    .matrix-table thead th {
        background: #f8f9fb;
        padding: 0.65rem 1rem;
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        border-bottom: 2px solid #e5e7eb;
        text-align: center;
    }
    .matrix-table thead th:first-child {
        text-align: left;
        min-width: 260px;
    }
    .matrix-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background .15s;
    }
    .matrix-table tbody tr:last-child { border-bottom: none; }
    .matrix-table tbody tr:hover { background: #f9fafb; }
    .matrix-table td {
        padding: 0.7rem 1rem;
        color: #374151;
    }
    .matrix-table td.check-cell {
        text-align: center;
        vertical-align: middle;
    }

    /* ── Check Icons ── */
    .check-yes {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px; height: 26px;
        border-radius: 50%;
        background: #dcfce7;
        color: #16a34a;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .check-no {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px; height: 26px;
        border-radius: 50%;
        background: #f3f4f6;
        color: #d1d5db;
        font-size: 0.8rem;
    }

    /* ── Group color bands ── */
    .group-danger  .matrix-section-header { background: #fff5f5; color: #dc2626; }
    .group-primary .matrix-section-header { background: #eff6ff; color: #1d4ed8; }
    .group-warning .matrix-section-header { background: #fffbeb; color: #d97706; }
    .group-info    .matrix-section-header { background: #f0f9ff; color: #0369a1; }
    .group-secondary .matrix-section-header { background: #f8fafc; color: #475569; }
    .group-success .matrix-section-header { background: #f0fdf4; color: #15803d; }

    /* ── Toolbar ── */
    .matrix-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .filter-btn {
        padding: 6px 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #fff;
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        transition: all .2s;
        color: #374151;
    }
    .filter-btn.active, .filter-btn:hover {
        background: #1e3a5f;
        color: #fff;
        border-color: #1e3a5f;
    }

    /* ── Summary bar ── */
    .summary-stat {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8f9fb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 5px 14px;
        font-size: 0.82rem;
        font-weight: 500;
        color: #374151;
    }

    /* ── Print ── */
    @media print {
        .matrix-toolbar, .btn-print, nav, .sidebar, header { display: none !important; }
        .matrix-section { box-shadow: none; border: 1px solid #e5e7eb; }
        .matrix-header { background: #1a1a2e !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" style="max-width: 1280px;">

    {{-- ── Header ── --}}
    <div class="matrix-header">
        <div class="badge-root">
            <i class="bi bi-shield-fill-check"></i>
            Hanya Webmaster
        </div>
        <h1 class="fw-bold mb-1" style="font-size:1.75rem;">🔐 Matrix Akses Role</h1>
        <p class="mb-0 text-white-50" style="font-size:0.93rem;">
            Dokumentasi resmi siapa yang dapat mengakses setiap modul dan fitur dalam sistem Erlass Institute.
            Halaman ini bersifat <strong>read-only</strong> — perubahan hak akses dilakukan melalui kode program.
        </p>
    </div>

    {{-- ── Role Legend ── --}}
    <div class="row g-3 mb-4">
        @foreach($roles as $key => $role)
        <div class="col-6 col-md-3">
            <div class="role-card {{ $role['color'] }}">
                <div class="role-icon text-{{ $role['color'] }}">
                    <i class="bi {{ $role['icon'] }}"></i>
                </div>
                <div class="fw-bold" style="font-size:0.9rem;">{{ $role['label'] }}</div>
                <div class="text-muted" style="font-size:0.78rem; line-height:1.4;">{{ $role['desc'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Toolbar ── --}}
    <div class="matrix-toolbar">
        <span class="text-muted me-1" style="font-size:0.85rem; font-weight:600;">Filter tampilan:</span>
        <button class="filter-btn active" onclick="filterMatrix('all', this)">Semua Modul</button>
        @foreach($roles as $key => $role)
        <button class="filter-btn" onclick="filterMatrix('{{ $key }}', this)">
            <i class="bi {{ $role['icon'] }}"></i> {{ $role['label'] }}
        </button>
        @endforeach

        <div class="ms-auto d-flex gap-2">
            {{-- Statistik --}}
            @php
                $totalItems = collect($matrix)->sum(fn($g) => count($g['items']));
                $webmasterOnly = collect($matrix)->sum(fn($g) => collect($g['items'])->filter(fn($i) => $i['webmaster'] && !$i['admin_sistem'] && !$i['admin'] && !$i['instruktur'])->count());
            @endphp
            <span class="summary-stat">
                <i class="bi bi-list-check text-primary"></i>
                {{ $totalItems }} fitur terdaftar
            </span>
            <span class="summary-stat">
                <i class="bi bi-shield-lock text-danger"></i>
                {{ $webmasterOnly }} eksklusif webmaster
            </span>

            <button class="btn btn-sm btn-outline-secondary btn-print" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>

    {{-- ── Matrix per Group ── --}}
    @foreach($matrix as $group)
    <div class="matrix-section group-{{ $group['color'] }}" data-group="{{ $loop->index }}">
        <div class="matrix-section-header">
            <i class="bi {{ $group['icon'] }} fs-5"></i>
            {{ $group['group'] }}
            <span class="ms-auto badge bg-light text-secondary fw-normal" style="font-size:0.72rem;">
                {{ count($group['items']) }} fitur
            </span>
        </div>
        <table class="matrix-table">
            <thead>
                <tr>
                    <th>Fitur / Aksi</th>
                    @foreach($roles as $key => $role)
                    <th>
                        <i class="bi {{ $role['icon'] }} text-{{ $role['color'] }}"></i><br>
                        {{ $role['label'] }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($group['items'] as $item)
                <tr class="matrix-row"
                    data-webmaster="{{ $item['webmaster'] ? '1' : '0' }}"
                    data-admin_sistem="{{ $item['admin_sistem'] ? '1' : '0' }}"
                    data-admin="{{ $item['admin'] ? '1' : '0' }}"
                    data-instruktur="{{ $item['instruktur'] ? '1' : '0' }}">
                    <td>{{ $item['label'] }}</td>
                    @foreach(array_keys($roles) as $key)
                    <td class="check-cell">
                        @if($item[$key])
                            <span class="check-yes" title="Diizinkan">
                                <i class="bi bi-check-lg"></i>
                            </span>
                        @else
                            <span class="check-no" title="Tidak diizinkan">
                                <i class="bi bi-dash"></i>
                            </span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    {{-- ── Footer Note ── --}}
    <div class="alert alert-light border" style="border-radius:12px; font-size:0.85rem;">
        <i class="bi bi-info-circle-fill text-primary me-2"></i>
        <strong>Catatan:</strong> Matrix ini merupakan dokumentasi hak akses yang <em>hardcoded</em> dalam sistem.
        Untuk mengubah izin akses, lakukan melalui kode program di <code>routes/web.php</code>, middleware, atau Policy.
        Hubungi <strong>Webmaster</strong> untuk perubahan apapun.
        <span class="text-muted ms-2">— Terakhir diperbarui: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
    </div>

</div>
@endsection

@push('scripts')
<script>
/**
 * Filter matrix rows/sections by role.
 * Hanya tampilkan baris di mana role tersebut memiliki akses (value = 1).
 */
function filterMatrix(role, btn) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const sections = document.querySelectorAll('.matrix-section');
    sections.forEach(section => {
        const rows = section.querySelectorAll('.matrix-row');
        let visibleCount = 0;

        rows.forEach(row => {
            if (role === 'all') {
                row.style.display = '';
                visibleCount++;
            } else {
                const hasAccess = row.dataset[role] === '1';
                row.style.display = hasAccess ? '' : 'none';
                if (hasAccess) visibleCount++;
            }
        });

        // Sembunyikan section jika tidak ada baris yang visible (kecuali mode "all")
        section.style.display = (role !== 'all' && visibleCount === 0) ? 'none' : '';
    });
}
</script>
@endpush
