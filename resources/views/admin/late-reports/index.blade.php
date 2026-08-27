@extends('layouts.app')

@section('title', 'Audit Laporan Terlambat & Kendala')

@push('styles')
<style>
    :root {
        --imp-navy: #0F172A;
        --imp-blue: #3B82F6;
        --imp-blue-dark: #1E40AF;
        --imp-green: #10B981;
        --imp-red: #EF4444;
        --imp-amber: #F59E0B;
        --imp-purple: #8B5CF6;
        --imp-slate: #64748B;
        --imp-surface: #FFFFFF;
        --imp-surface-alt: #F8FAFC;
        --imp-border: #E2E8F0;
        --imp-radius: 16px;
        --imp-shadow: 0 4px 24px rgba(15, 23, 42, 0.08);
        --imp-shadow-lg: 0 12px 40px rgba(15, 23, 42, 0.12);
    }

    /* ── Hero Section ──────────────────────────────────────── */
    .audit-hero {
        background: linear-gradient(135deg, var(--imp-navy) 0%, #1E3A5F 50%, var(--imp-blue-dark) 100%);
        border-radius: var(--imp-radius);
        padding: 2rem 2.25rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.75rem;
        box-shadow: var(--imp-shadow-lg);
    }
    .audit-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.25), transparent 70%);
        pointer-events: none;
    }
    .audit-hero::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -10%;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(239, 68, 68, 0.18), transparent 70%);
        pointer-events: none;
    }
    .audit-hero h1 {
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 0.35rem;
        color: #FFFFFF;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        position: relative;
        z-index: 1;
    }
    .audit-hero .hero-subtitle {
        font-size: 0.95rem;
        color: rgba(255,255,255,0.9);
        font-weight: 500;
        position: relative;
        z-index: 1;
    }

    /* ── Metric Cards ──────────────────────────────────────── */
    .metric-card {
        background: white;
        border-radius: 14px;
        border: 1px solid var(--imp-border);
        padding: 1.25rem 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--imp-shadow);
    }
    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    /* ── Tab Navigation ────────────────────────────────────── */
    .audit-tabs {
        display: flex;
        gap: 0.5rem;
        background: #F1F5F9;
        padding: 0.35rem;
        border-radius: 12px;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }
    .audit-tab-link {
        padding: 0.55rem 1.15rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--imp-slate);
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .audit-tab-link:hover {
        color: var(--imp-navy);
        background: rgba(255,255,255,0.6);
    }
    .audit-tab-link.active {
        background: white;
        color: var(--imp-blue-dark);
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }

    /* ── Reason Box ────────────────────────────────────────── */
    .reason-box {
        background: #FEF2F2;
        border-left: 3.5px solid var(--imp-red);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
        color: #991B1B;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- ═══ HERO BANNER ═══ --}}
    <div class="audit-hero">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-shield-check me-2 text-warning"></i>Audit Laporan Terlambat & Kendala</h1>
                <p class="hero-subtitle mb-0">
                    Tinjau alasan kendala keterlambatan berat (&gt; 3 hari / lewat cutoff) untuk persetujuan pencairan honor instruktur.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(10px); font-size: 0.85rem;">
                    <i class="bi bi-hourglass-split me-1 text-warning"></i> {{ $pendingCount }} Menunggu Review
                </span>
            </div>
        </div>
    </div>

    {{-- ═══ METRIC STATS ═══ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-icon" style="background: #FEF3C7; color: #D97706;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $pendingCount }}</h3>
                    <small class="text-muted fw-semibold">Menunggu Review</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-icon" style="background: #DCFCE7; color: #15803D;">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $approvedCount }}</h3>
                    <small class="text-muted fw-semibold">Disetujui (ACC)</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-icon" style="background: #FEE2E2; color: #DC2626;">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $rejectedCount }}</h3>
                    <small class="text-muted fw-semibold">Ditolak / On Hold</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-icon" style="background: #EFF6FF; color: #2563EB;">
                    <i class="bi bi-collection"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalCount }}</h3>
                    <small class="text-muted fw-semibold">Total Sesi Terlambat</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ TABS & SEARCH BAR ═══ --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div class="audit-tabs mb-0">
                    <a href="{{ route('admin.late-reports.index', ['status' => 'pending', 'search' => $search]) }}" class="audit-tab-link {{ $statusTab === 'pending' ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Menunggu Review
                        @if($pendingCount > 0)
                            <span class="badge bg-warning text-dark rounded-pill">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.late-reports.index', ['status' => 'approved', 'search' => $search]) }}" class="audit-tab-link {{ $statusTab === 'approved' ? 'active' : '' }}">
                        <i class="bi bi-check-circle-fill text-success"></i> Disetujui (ACC) ({{ $approvedCount }})
                    </a>
                    <a href="{{ route('admin.late-reports.index', ['status' => 'rejected', 'search' => $search]) }}" class="audit-tab-link {{ $statusTab === 'rejected' ? 'active' : '' }}">
                        <i class="bi bi-x-circle-fill text-danger"></i> Ditolak ({{ $rejectedCount }})
                    </a>
                    <a href="{{ route('admin.late-reports.index', ['status' => 'all', 'search' => $search]) }}" class="audit-tab-link {{ $statusTab === 'all' ? 'active' : '' }}">
                        <i class="bi bi-list-ul"></i> Semua ({{ $totalCount }})
                    </a>
                </div>

                {{-- Search Form --}}
                <form action="{{ route('admin.late-reports.index') }}" method="GET" class="d-flex gap-2">
                    <input type="hidden" name="status" value="{{ $statusTab }}">
                    <div class="input-group input-group-sm" style="max-width: 280px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari instruktur / rombel..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.late-reports.index', ['status' => $statusTab]) }}" class="btn btn-outline-secondary">Clear</a>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary px-3 rounded-3">Cari</button>
                </form>
            </div>

            {{-- ═══ AUDIT TABLE ═══ --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 20%;">Instruktur</th>
                            <th style="width: 22%;">Sesi & Sekolah</th>
                            <th style="width: 15%;">Waktu Pelaksanaan</th>
                            <th style="width: 28%;">Catatan Kendala Instruktur</th>
                            <th class="text-center" style="width: 15%;">Status & Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $rep)
                            @php
                                $scheduleDate = $rep->jadwal_mengajar ? \Carbon\Carbon::parse($rep->jadwal_mengajar) : null;
                                $submitDate = $rep->created_at;
                                $diffDays = $scheduleDate ? (int) $scheduleDate->diffInDays($submitDate, false) : 0;
                                $meta = $rep->metadata_json ?? [];
                                $approvalStatus = $meta['status_approval_kendala'] ?? ($diffDays >= 3 ? 'pending_approval' : 'approved');
                                $kendalaText = $meta['alasan_kendala_keterlambatan'] ?? $rep->catatan ?? '-';
                                $adminNotes = $meta['admin_notes'] ?? null;
                            @endphp
                            <tr>
                                {{-- Instruktur --}}
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold border" style="width: 38px; height: 38px; min-width: 38px;">
                                            {{ substr($rep->instruktur->nama_lengkap ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $rep->instruktur->nama_lengkap ?? 'Instruktur' }}</div>
                                            <small class="text-muted d-block">{{ $rep->instruktur->instructor_id ?? $rep->instruktur->email ?? 'Instruktur' }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Sesi & Sekolah --}}
                                <td>
                                    <div class="fw-bold text-dark mb-0.5">
                                        {{ $rep->rombel ?? optional(optional($rep->ekstrakurikulerSession)->rombel)->nama_rombel ?? 'Rombel' }}
                                    </div>
                                    <div class="small text-muted mb-1">
                                        <i class="bi bi-building me-1"></i>{{ $rep->sekolah->namasekolah ?? optional(optional(optional($rep->ekstrakurikulerSession)->rombel)->ekstrakurikuler)->sekolah->namasekolah ?? '-' }}
                                    </div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2 py-0.5" style="font-size: 0.72rem;">
                                        Pertemuan ke-{{ $rep->pertemuan_ke }}
                                    </span>
                                </td>

                                {{-- Waktu --}}
                                <td>
                                    <div class="small text-dark fw-semibold">
                                        <i class="bi bi-calendar3 me-1 text-muted"></i>{{ $scheduleDate ? $scheduleDate->translatedFormat('d M Y') : '-' }}
                                    </div>
                                    <div class="small text-muted">
                                        Submit: {{ $submitDate->translatedFormat('d M Y, H:i') }}
                                    </div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Terlambat {{ max(1, $diffDays) }} Hari
                                    </span>
                                </td>

                                {{-- Catatan Kendala --}}
                                <td>
                                    <div class="reason-box mb-1">
                                        <strong class="d-block mb-1"><i class="bi bi-chat-quote-fill me-1"></i>Alasan Kendala:</strong>
                                        {{ $kendalaText }}
                                    </div>
                                    @if($adminNotes)
                                        <div class="small text-muted fst-italic mt-1">
                                            <i class="bi bi-person-badge me-1 text-primary"></i><strong>Catatan Admin:</strong> {{ $adminNotes }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Status & Aksi --}}
                                <td class="text-center pe-3">
                                    @if($approvalStatus === 'pending_approval')
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 fw-bold mb-2 d-inline-block">
                                            <i class="bi bi-hourglass-split me-1"></i>Menunggu Review
                                        </span>
                                        <div class="d-flex justify-content-center gap-1.5 flex-wrap">
                                            <form action="{{ route('admin.late-reports.approve', $rep->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success text-white fw-bold px-3 rounded-pill shadow-xs" onclick="return confirm('Setujui (ACC) kendala keterlambatan ini untuk dimasukkan ke penggajian?')">
                                                    <i class="bi bi-check-lg me-1"></i>ACC
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $rep->id }}">
                                                <i class="bi bi-x-lg me-1"></i>Tolak
                                            </button>
                                        </div>
                                    @elseif($approvalStatus === 'approved')
                                        <span class="badge bg-success rounded-pill px-3 py-1.5 fw-bold mb-1 d-inline-block">
                                            <i class="bi bi-check-circle-fill me-1"></i>Disetujui (ACC)
                                        </span>
                                        <div class="small text-muted">Masuk Payroll Cutoff</div>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-3 py-1.5 fw-bold mb-1 d-inline-block">
                                            <i class="bi bi-x-circle-fill me-1"></i>Ditolak (On Hold)
                                        </span>
                                        <div class="small text-muted">Honor Ditahan</div>
                                    @endif

                                    <div class="mt-2">
                                        <a href="{{ route('laporan-mengajar.show', $rep->id) }}" class="small text-primary text-decoration-none fw-semibold" target="_blank">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal Tolak Kendala --}}
                            <div class="modal fade" id="rejectModal{{ $rep->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                        <form action="{{ route('admin.late-reports.reject', $rep->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                <h5 class="modal-title fw-bold text-danger">
                                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Tolak Kendala Keterlambatan
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body px-4 py-3">
                                                <p class="small text-muted mb-3">
                                                    Sesi <strong>Pertemuan ke-{{ $rep->pertemuan_ke }} ({{ $rep->rombel }})</strong> oleh <strong>{{ optional($rep->instruktur)->nama_lengkap }}</strong> akan ditandai Ditolak dan honornya ditahan.
                                                </p>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Alasan Penolakan / Catatan Evaluasi <span class="text-danger">*</span></label>
                                                    <textarea name="admin_notes" class="form-control" rows="3" required placeholder="Contoh: Alasan kendala tidak dapat diverifikasi dengan PIC sekolah..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Kirim Penolakan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-shield-check fs-1 text-success d-block mb-2"></i>
                                        <h6 class="fw-bold mb-1">Tidak Ada Laporan yang Perlu Ditinjau</h6>
                                        <p class="small mb-0">Semua laporan mengajar terlambat telah ditinjau atau tidak ada sesi keterlambatan berat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($reports->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
