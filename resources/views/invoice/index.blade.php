@extends('layouts.app')

@section('title', 'Invoice Management — Erlass')

@section('content')
<div class="container-fluid py-4">

    {{-- ─── HERO BANNER ──────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden"
         style="background: linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 50%, #E0F2FE 100%);
                border-radius: 1rem; border: 1px solid #DBEAFE !important;">
        <div class="card-body p-4 p-lg-5 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success rounded-pill px-3 py-1 small fw-bold">
                            <i class="bi bi-receipt me-1"></i> Sistem Invoice
                        </span>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 small fw-bold">
                            <i class="bi bi-shield-check me-1"></i> Dual Approval
                        </span>
                    </div>
                    <h1 class="h2 fw-bold text-dark mb-2">Manajemen Invoice Tagihan</h1>
                    <p class="text-muted mb-0 fs-6" style="max-width: 680px;">
                        Penerbitan & persetujuan invoice per rombel dengan 4 skema tagihan.
                        PDF Draft dapat diunduh langsung untuk <strong>konfirmasi PIC Sekolah</strong>, dan PDF Resmi terbit otomatis setelah disetujui penuh oleh <strong>Operasional</strong> + <strong>Akunting</strong>.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ route('invoice.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>Buat Invoice Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── SUMMARY CARDS ─────────────────────────────────────────────────── --}}
    @php
        $statusConfig = [
            'draft'               => ['label' => 'Draft',                'icon' => 'bi-file-earmark', 'color' => 'secondary'],
            'pending_operasional' => ['label' => 'Pend. Operasional',    'icon' => 'bi-hourglass',    'color' => 'warning'],
            'pending_akunting'    => ['label' => 'Pend. Akunting',       'icon' => 'bi-clock',        'color' => 'info'],
            'approved'            => ['label' => 'Disetujui',            'icon' => 'bi-check-circle', 'color' => 'success'],
            'rejected'            => ['label' => 'Ditolak',              'icon' => 'bi-x-circle',     'color' => 'danger'],
        ];
    @endphp
    <div class="row g-3 mb-4">
        @foreach($statusConfig as $key => $cfg)
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center h-100"
                 style="border-radius: .75rem; cursor:pointer;"
                 onclick="document.getElementById('filter-status').value='{{ $key }}'; document.getElementById('filter-form').submit();">
                <div class="card-body py-3 px-2">
                    <i class="bi {{ $cfg['icon'] }} fs-3 text-{{ $cfg['color'] }}"></i>
                    <div class="fw-bold fs-4 mt-1">{{ $summary[$key] ?? 0 }}</div>
                    <div class="text-muted small">{{ $cfg['label'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ─── PANEL ROMBEL SIAP DITAGIHKAN (ELIGIBLE FOR BILLING) ────────────── --}}
    {{-- ─── PANEL SEKOLAH SIAP DITAGIHKAN (ELIGIBLE FOR BILLING) ────────────── --}}
    @php
        $eligibleList = $eligibleSekolahs ?? $eligibleRombels ?? collect();
    @endphp
    @if($eligibleList->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: .875rem; border-left: 5px solid #10b981 !important;">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                    <i class="bi bi-lightning-charge-fill text-white fs-6"></i>
                </span>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Sekolah Siap Ditagihkan ({{ $eligibleList->count() }})</h6>
                    <small class="text-muted">1 Invoice per Sekolah (rincian item per rombel) — Diurutkan prioritas keterlambatan pembuatan invoice</small>
                </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="filterEligibleSkema" class="form-select form-select-sm bg-light border" style="width: 175px;" onchange="filterEligibleSchools()">
                    <option value="">Semua Skema</option>
                    <option value="bulanan">🗓️ Bulanan</option>
                    <option value="per_4_pertemuan">🔄 Per 4 Pertemuan</option>
                    <option value="semester">📚 Semesteran</option>
                    <option value="tahunan">🎓 Tahunan</option>
                </select>
                <div class="input-group input-group-sm" style="width: 230px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted small"></i></span>
                    <input type="text" id="searchEligibleTable" class="form-control bg-light border-start-0" 
                           placeholder="Cari sekolah..." onkeyup="filterEligibleSchools()">
                </div>
                <form action="{{ route('invoice.bulk-generate') }}" method="POST" onsubmit="return confirm('Generate invoice untuk semua {{ $eligibleList->count() }} sekolah yang siap ditagihkan?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success fw-bold px-3 py-1.5 rounded-pill shadow-xs text-nowrap">
                        <i class="bi bi-lightning-charge-fill me-1"></i> Generate Semua (Bulk)
                    </button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-uppercase text-muted" style="font-size: .75rem; letter-spacing: .5px;">
                        <th class="ps-4">Sekolah</th>
                        <th>Rombel & Program (Item)</th>
                        <th>Skema Tagihan</th>
                        <th>Periode Tagihan</th>
                        <th class="text-center">Total Siswa Billable</th>
                        <th>Target Invoice</th>
                        <th>Keterlambatan</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" id="eligibleTableBody">
                    @foreach($eligibleList as $item)
                    <tr data-skema="{{ $item['skema_tagihan'] }}">
                        <td class="ps-4 fw-semibold text-dark">
                            <span class="badge bg-light text-secondary border font-monospace me-1">[{{ $item['sekolah_kodlan'] }}]</span>
                            {{ $item['sekolah_nama'] }}
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">
                                <i class="bi bi-diagram-3 me-1 text-primary"></i>{{ $item['total_rombel'] }} Rombel
                            </div>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @foreach($item['items'] ?? [] as $it)
                                    <span class="badge bg-light text-muted border font-monospace" style="font-size: .7rem;" title="{{ $it['kategori_program'] }}">
                                        {{ $it['rombel_nama'] }} ({{ $it['jumlah_siswa_billable'] }} sw)
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ match($item['skema_tagihan']) {
                                'bulanan'         => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                'semester'        => 'bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25',
                                'tahunan'         => 'bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25',
                                default           => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                            } }} px-2 py-1">
                                {{ match($item['skema_tagihan']) {
                                    'bulanan'         => 'Bulanan',
                                    'semester'        => 'Semesteran',
                                    'tahunan'         => 'Tahunan',
                                    default           => 'Per 4 Pertemuan',
                                } }}
                            </span>
                        </td>
                        <td>
                            @if($item['skema_tagihan'] === 'bulanan')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">
                                    <i class="bi bi-calendar3 me-1"></i>{{ $item['periode_label'] }}
                                </span>
                            @elseif($item['skema_tagihan'] === 'semester')
                                <span class="badge bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25 px-2.5 py-1">
                                    <i class="bi bi-calendar-range me-1"></i>{{ $item['periode_label'] }}
                                </span>
                            @elseif($item['skema_tagihan'] === 'tahunan')
                                <span class="badge bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25 px-2.5 py-1">
                                    <i class="bi bi-calendar-check me-1"></i>{{ $item['periode_label'] }}
                                </span>
                            @else
                                {{-- Per 4 Pertemuan: detail waktu / sesi --}}
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">
                                    <i class="bi bi-layers me-1"></i>Sesi {{ $item['sesi_dari'] }}–{{ $item['sesi_sampai'] }}
                                </span>
                                @if(!empty($item['bulan_laporan_terakhir']))
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-calendar-event me-1"></i>Lap: <strong class="text-dark">{{ $item['bulan_laporan_terakhir'] }}</strong>
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border fw-bold px-2.5 py-1.5">
                                {{ $item['total_siswa_billable'] }} siswa
                            </span>
                        </td>
                        <td>
                            <div class="text-nowrap small fw-medium text-dark">
                                <i class="bi bi-calendar-event me-1 text-muted"></i>{{ $item['target_date_formatted'] ?? '-' }}
                            </div>
                        </td>
                        <td>
                            @if(($item['days_overdue'] ?? 0) > 0)
                                <span class="badge {{ $item['keterlambatan_badge'] ?? 'bg-danger text-white' }} px-2.5 py-1.5 rounded-pill shadow-xs text-nowrap">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $item['keterlambatan_label'] }}
                                </span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1.5 rounded-pill text-nowrap">
                                    <i class="bi bi-clock-history me-1"></i>{{ $item['keterlambatan_label'] ?? 'Hari Ini' }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <form action="{{ route('invoice.quick-generate') }}" method="POST" class="d-inline mb-0">
                                    @csrf
                                    <input type="hidden" name="sekolah_kodlan" value="{{ $item['sekolah_kodlan'] }}">
                                    @if(!empty($item['ekstrakurikuler_rombel_id']))
                                        <input type="hidden" name="ekstrakurikuler_rombel_id" value="{{ $item['ekstrakurikuler_rombel_id'] }}">
                                    @endif
                                    <input type="hidden" name="skema_tagihan" value="{{ $item['skema_tagihan'] }}">
                                    <input type="hidden" name="periode_label" value="{{ $item['periode_label'] }}">
                                    <input type="hidden" name="periode_nomor" value="{{ $item['periode_nomor'] ?? '' }}">
                                    <input type="hidden" name="sesi_dari" value="{{ $item['sesi_dari'] ?? '' }}">
                                    <input type="hidden" name="sesi_sampai" value="{{ $item['sesi_sampai'] ?? '' }}">
                                    <input type="hidden" name="tahun_ajaran" value="{{ $item['tahun_ajaran'] }}">
                                    <button type="submit" class="btn btn-sm btn-success fw-semibold rounded-pill px-3 shadow-xs">
                                        <i class="bi bi-lightning-charge-fill me-1"></i> 1-Klik Generate
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ─── FILTER BAR ─────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
        <div class="card-body p-3">
            <form id="filter-form" method="GET" action="{{ route('invoice.index') }}"
                  class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Sekolah</label>
                    <select name="sekolah" class="form-select form-select-sm">
                        <option value="">Semua Sekolah</option>
                        @foreach($sekolahs as $s)
                            <option value="{{ $s->kodlan }}" {{ request('sekolah') === $s->kodlan ? 'selected' : '' }}>
                                [{{ $s->kodlan }}] {{ $s->namasekolah }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                    <select name="status" id="filter-status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $val => $lbl)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Skema</label>
                    <select name="skema" class="form-select form-select-sm">
                        <option value="">Semua Skema</option>
                        <option value="bulanan"         {{ request('skema') === 'bulanan'         ? 'selected' : '' }}>Bulanan</option>
                        <option value="semester"        {{ request('skema') === 'semester'        ? 'selected' : '' }}>Semester</option>
                        <option value="tahunan"         {{ request('skema') === 'tahunan'         ? 'selected' : '' }}>Tahunan</option>
                        <option value="per_4_pertemuan" {{ request('skema') === 'per_4_pertemuan' ? 'selected' : '' }}>Per 4 Pertemuan</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" value="{{ request('tahun_ajaran', '2026/2027') }}"
                           class="form-control form-control-sm" placeholder="2026/2027">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── TABLE ──────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="invoiceTable">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-semibold">No. Invoice</th>
                            <th class="py-3 text-muted small fw-semibold">Sekolah / Rombel</th>
                            <th class="py-3 text-muted small fw-semibold">Periode Tagihan</th>
                            <th class="py-3 text-muted small fw-semibold">Skema Tagihan</th>
                            <th class="py-3 text-muted small fw-semibold text-center">Siswa Billable</th>
                            <th class="py-3 text-muted small fw-semibold text-center">Operasional</th>
                            <th class="py-3 text-muted small fw-semibold text-center">Akunting</th>
                            <th class="py-3 text-muted small fw-semibold text-center">Status</th>
                            <th class="pe-4 py-3 text-muted small fw-semibold text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                        <tr>
                            <td class="ps-4">
                                <code class="small {{ str_starts_with($inv->nomor_invoice ?? '', 'DRAFT-') ? 'text-secondary' : 'text-primary' }}">{{ $inv->nomor_invoice }}</code>
                                @if(str_starts_with($inv->nomor_invoice ?? '', 'DRAFT-'))
                                    <br><span class="badge bg-secondary-subtle text-secondary small" style="font-size:.65rem;">Nomor Draft</span>
                                @elseif($inv->hasPdf())
                                    <br><span class="badge bg-success-subtle text-success small" style="font-size:.65rem;">PDF Dibuat</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold small text-dark">
                                    {{ $inv->sekolah?->namasekolah ?? $inv->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? '-' }}
                                </div>
                                <div class="text-muted" style="font-size: .75rem;">
                                    @if(($inv->total_rombel ?? 0) > 1)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-diagram-3 me-1"></i>{{ $inv->total_rombel }} Rombel</span>
                                    @elseif($inv->items->isNotEmpty())
                                        {{ $inv->items->first()?->rombel?->nama_rombel ?? '-' }}
                                    @else
                                        {{ $inv->rombel?->nama_rombel ?? '-' }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fw-medium small">{{ $inv->periode_label }}</span>
                                <br><span class="text-muted" style="font-size:.72rem;">{{ $inv->tahun_ajaran }}</span>
                            </td>
                            <td>
                                @php
                                    $skemaColor = match($inv->skema_tagihan) {
                                        'bulanan'         => 'primary',
                                        'semester'        => 'purple',
                                        'tahunan'         => 'dark',
                                        'per_4_pertemuan' => 'secondary',
                                        default           => 'secondary',
                                    };
                                    $skemaLabel = match($inv->skema_tagihan) {
                                        'bulanan'         => 'Bulanan',
                                        'semester'        => 'Semesteran',
                                        'tahunan'         => 'Tahunan',
                                        'per_4_pertemuan' => 'Per 4 Pertemuan',
                                        default           => '-',
                                    };
                                @endphp
                                <span class="badge bg-{{ $skemaColor }}-subtle text-{{ $skemaColor }} border border-{{ $skemaColor }}-subtle rounded-pill px-2.5 py-1 small">
                                    {{ $skemaLabel }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $inv->hasKoreksi() ? 'bg-warning-subtle text-warning border border-warning' : 'bg-light text-dark border' }} fw-bold">
                                    {{ $inv->billable_efektif }} siswa
                                </span>
                                @if($inv->hasKoreksi())
                                    <div class="text-warning-emphasis" style="font-size:.68rem;">(koreksi)</div>
                                @endif
                                <div class="text-muted" style="font-size:.7rem;">{{ $inv->jumlah_sesi }} sesi</div>
                            </td>
                            <td class="text-center">
                                @if($inv->operasional_status === 'approved')
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check me-1"></i>{{ $inv->operasionalUser?->name ?? '-' }}</span>
                                    <div class="text-muted" style="font-size:.7rem;">{{ $inv->operasional_approved_at?->format('d/m/y') }}</div>
                                @elseif($inv->operasional_status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x me-1"></i>Ditolak</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">Menunggu</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($inv->akunting_status === 'approved')
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check me-1"></i>{{ $inv->akuntingUser?->name ?? '-' }}</span>
                                    <div class="text-muted" style="font-size:.7rem;">{{ $inv->akunting_approved_at?->format('d/m/y') }}</div>
                                @elseif($inv->akunting_status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x me-1"></i>Ditolak</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Menunggu</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $inv->statusBadgeClass() }} rounded-pill">
                                    {{ $inv->statusLabel() }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('invoice.show', $inv) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($inv->isApproved())
                                <a href="{{ route('invoice.pdf', $inv) }}" class="btn btn-sm btn-success" target="_blank" title="Unduh PDF Resmi">
                                    <i class="bi bi-download"></i>
                                </a>
                                @else
                                <a href="{{ route('invoice.pdf', $inv) }}" class="btn btn-sm btn-outline-warning text-dark" target="_blank" title="Unduh PDF Draft (Konfirmasi PIC)">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                Belum ada invoice. <a href="{{ route('invoice.create') }}">Buat invoice pertama</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($invoices->hasPages())
            <div class="d-flex justify-content-center py-3 border-top">
                {{ $invoices->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function filterEligibleSchools() {
    const q = (document.getElementById('searchEligibleTable')?.value || '').toLowerCase().trim();
    const skema = (document.getElementById('filterEligibleSkema')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#eligibleTableBody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const rowSkema = (row.getAttribute('data-skema') || '').toLowerCase();
        const matchesQuery = !q || text.includes(q);
        const matchesSkema = !skema || rowSkema === skema;
        row.style.display = (matchesQuery && matchesSkema) ? '' : 'none';
    });
}
</script>
@endpush

