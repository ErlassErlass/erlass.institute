@extends('layouts.app')

@section('title', 'Detail Invoice ' . $invoice->nomor_invoice)

@section('content')
<div class="container-fluid py-4">

    {{-- ─── Back & Header ────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="h4 fw-bold text-dark mb-0">Invoice Detail</h1>
                @if(str_starts_with($invoice->nomor_invoice ?? '', 'DRAFT-'))
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded-pill small">
                        <i class="bi bi-file-earmark me-1"></i>Draft Invoice
                    </span>
                @else
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill small">
                        <i class="bi bi-shield-check me-1"></i>Resmi / Final
                    </span>
                @endif
            </div>
            <code class="text-primary small fw-bold">{{ $invoice->nomor_invoice }}</code>
            @if(str_starts_with($invoice->nomor_invoice ?? '', 'DRAFT-'))
                <span class="text-muted" style="font-size: .75rem;">(Nomor resmi INV/ERLASS/... terbit otomatis saat disetujui Akunting)</span>
            @endif
        </div>
        <div class="ms-auto d-flex gap-2">
            @if($invoice->isApproved())
            <a href="{{ route('invoice.pdf', $invoice) }}" class="btn btn-success" target="_blank">
                <i class="bi bi-download me-2"></i>Unduh PDF Resmi
            </a>
            @else
            <a href="{{ route('invoice.pdf', $invoice) }}" class="btn btn-outline-warning text-dark fw-semibold" target="_blank" title="Unduh draft invoice untuk konfirmasi kehadiran & absensi ke PIC Sekolah">
                <i class="bi bi-file-earmark-pdf me-2 text-warning"></i>Unduh PDF Draft (Konfirmasi PIC)
            </a>
            @endif
            <span class="badge fs-6 bg-{{ $invoice->statusBadgeClass() }} d-flex align-items-center px-3">
                {{ $invoice->statusLabel() }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- ─── LEFT: Info Tagihan & Rincian Rombel ────────────────────── --}}
        <div class="col-lg-7">

            {{-- Identitas --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4" style="background: #f8fafc;">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-building me-2 text-primary"></i>Identitas Tagihan Sekolah</h6>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Sekolah</div>
                            <div class="fw-semibold">{{ $invoice->sekolah?->namasekolah ?? $invoice->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Kode Sekolah</div>
                            <code class="small">{{ $invoice->sekolah_kodlan ?? $invoice->rombel?->ekstrakurikuler?->sekolah?->kodlan ?? '-' }}</code>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Total Rombel</div>
                            <div class="fw-semibold">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <i class="bi bi-diagram-3 me-1"></i>{{ $invoice->total_rombel ?: $invoice->items->count() ?: 1 }} Rombel
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Tahun Ajaran</div>
                            <div class="fw-semibold">{{ $invoice->tahun_ajaran }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Periode Tagihan</div>
                            <div class="fw-semibold">{{ $invoice->periode_label }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Skema Tagihan</div>
                            @php
                                $skemaLabelMap = [
                                    'bulanan'         => ['label' => 'Bulanan (Kalender)', 'color' => 'primary'],
                                    'semester'        => ['label' => 'Per Semester (~16 sesi)', 'color' => 'purple'],
                                    'tahunan'         => ['label' => 'Per Tahun (~32 sesi)', 'color' => 'dark'],
                                    'per_4_pertemuan' => ['label' => 'Per 4 Pertemuan', 'color' => 'secondary'],
                                ];
                                $skema = $skemaLabelMap[$invoice->skema_tagihan] ?? ['label' => $invoice->skema_tagihan, 'color' => 'secondary'];
                            @endphp
                            <span class="badge bg-{{ $skema['color'] }}-subtle text-{{ $skema['color'] }} border px-2">
                                {{ $skema['label'] }}
                            </span>
                        </div>
                        @if($invoice->sesi_dari && $invoice->sesi_sampai)
                        <div class="col-md-6">
                            <div class="text-muted small">Range Sesi</div>
                            <div class="fw-semibold">Pertemuan {{ $invoice->sesi_dari }} – {{ $invoice->sesi_sampai }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ─── RINCIAN PER ROMBEL (ITEMIZED BREAKDOWN) ──────────────── --}}
            @if($invoice->items->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background: #f8fafc;">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-diagram-3 me-2 text-primary"></i>Rincian Tagihan per Rombel ({{ $invoice->items->count() }} Rombel)
                        </h6>
                        <small class="text-muted">Itemisasi rincian rombel dalam tagihan sekolah ini</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase" style="font-size: .75rem; letter-spacing: .5px;">
                                    <th class="ps-4">#</th>
                                    <th>Program & Rombel</th>
                                    <th>Sesi</th>
                                    <th class="text-center">Siswa Sistem</th>
                                    <th class="text-center">Koreksi</th>
                                    <th class="text-center">Siswa Efektif</th>
                                    @if($invoice->status !== 'approved')
                                    <th class="text-end pe-4">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $idx => $item)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $item->rombel?->nama_rombel ?? '-' }}</div>
                                        <div class="small text-muted">{{ $item->rombel?->ekstrakurikuler?->kategori_program ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @if($item->sesi_dari && $item->sesi_sampai)
                                            <span class="badge bg-light text-secondary border font-monospace">
                                                Sesi {{ $item->sesi_dari }}–{{ $item->sesi_sampai }}
                                            </span>
                                        @else
                                            <span class="text-muted small">{{ $item->jumlah_sesi }} Sesi</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">
                                            {{ $item->jumlah_siswa_billable }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->hasKoreksi())
                                            <span class="badge bg-warning-subtle text-warning border border-warning" title="{{ $item->koreksi_catatan }}">
                                                {{ $item->koreksi_siswa_billable }}
                                            </span>
                                            <div class="text-muted" style="font-size: .68rem;">{{ Str::limit($item->koreksi_catatan, 15) }}</div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->hasKoreksi() ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success' }} fw-bold px-2 py-1">
                                            {{ $item->billable_efektif }} siswa
                                        </span>
                                    </td>
                                    @if($invoice->status !== 'approved')
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5"
                                                data-bs-toggle="modal" data-bs-target="#koreksiModalItem{{ $item->id }}" title="Koreksi rombel ini">
                                            <i class="bi bi-pencil me-1"></i>Koreksi
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light border-top">
                                <tr>
                                    <th colspan="5" class="ps-4 fw-bold text-dark">Total Siswa Billable Seluruh Rombel:</th>
                                    <th class="text-center">
                                        <span class="badge bg-success text-white fw-bold fs-6 px-3 py-1.5 rounded-pill shadow-xs">
                                            {{ $invoice->billable_efektif }} siswa
                                        </span>
                                    </th>
                                    @if($invoice->status !== 'approved')
                                    <th></th>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Ringkasan Billing --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background: #f0fdf4;">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-calculator me-2 text-success"></i>Ringkasan Billing</h6>
                    @if($invoice->status !== 'approved')
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#koreksiPanel">
                        <i class="bi bi-pencil me-1"></i>Koreksi Billable
                    </button>
                    @endif
                </div>
                <div class="card-body px-4 py-3">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="bg-light rounded-3 p-3">
                                <div class="fs-2 fw-bold text-primary">{{ $invoice->jumlah_sesi }}</div>
                                <div class="text-muted small">Total Sesi</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="{{ $invoice->hasKoreksi() ? 'bg-warning-subtle border border-warning' : 'bg-success-subtle' }} rounded-3 p-3">
                                <div class="fs-2 fw-bold {{ $invoice->hasKoreksi() ? 'text-warning' : 'text-success' }}">
                                    {{ $invoice->billable_efektif }}
                                </div>
                                <div class="text-muted small">Siswa Billable</div>
                                @if($invoice->hasKoreksi())
                                    <div class="text-warning-emphasis" style="font-size:.7rem;">
                                        ⚠️ Dikoreksi (sistem: {{ $invoice->jumlah_siswa_billable }})
                                    </div>
                                @else
                                    <div class="text-muted" style="font-size:.7rem;">(hadir ≥2/4 sesi)</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-light rounded-3 p-3">
                                <div class="fs-5 fw-bold text-dark">{{ $invoice->pdf_generated_at ? $invoice->pdf_generated_at->format('d/m/y') : '-' }}</div>
                                <div class="text-muted small">PDF Dibuat</div>
                            </div>
                        </div>
                    </div>

                    {{-- ─── Panel Koreksi Billable ─────────────────────── --}}
                    @if($invoice->status !== 'approved')
                    <div class="collapse mt-3" id="koreksiPanel">
                        <div class="card border-warning" style="border-radius: .5rem; border-width: 2px !important;">
                            <div class="card-header bg-warning-subtle border-0 py-2 px-3">
                                <strong class="small text-warning-emphasis">
                                    <i class="bi bi-pencil-square me-1"></i>Koreksi Manual Siswa Billable
                                </strong>
                            </div>
                            <div class="card-body p-3">
                                @if($invoice->hasKoreksi())
                                <div class="alert alert-warning py-2 small mb-3">
                                    <strong>Koreksi aktif:</strong> {{ $invoice->jumlah_siswa_billable }} → {{ $invoice->koreksi_siswa_billable }} siswa<br>
                                    <span class="text-muted">Alasan: {{ $invoice->koreksi_catatan }}</span><br>
                                    <span class="text-muted">Oleh: {{ $invoice->koreksiByUser?->name ?? '-' }} pada {{ $invoice->koreksi_at?->format('d/m/Y H:i') }}</span>
                                </div>
                                <form action="{{ route('invoice.koreksi.reset', $invoice) }}" method="POST" class="mb-3">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"
                                            onclick="return confirm('Reset koreksi? Akan kembali ke hitungan sistem.')">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset ke Hitungan Sistem ({{ $invoice->jumlah_siswa_billable }} siswa)
                                    </button>
                                </form>
                                <hr class="my-2">
                                <div class="text-muted small mb-2">Atau ganti koreksi:</div>
                                @endif

                                <form action="{{ route('invoice.koreksi', $invoice) }}" method="POST">
                                    @csrf
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold">
                                                Jumlah Billable Terkoreksi
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" name="koreksi_siswa_billable"
                                                   class="form-control form-control-sm @error('koreksi_siswa_billable') is-invalid @enderror"
                                                   value="{{ old('koreksi_siswa_billable', $invoice->koreksi_siswa_billable ?? $invoice->jumlah_siswa_billable) }}"
                                                   min="0" max="999" required>
                                            @error('koreksi_siswa_billable')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label small fw-semibold">
                                                Alasan Koreksi <span class="text-danger">*</span>
                                                <span class="text-muted fw-normal">(min. 10 karakter)</span>
                                            </label>
                                            <input type="text" name="koreksi_catatan"
                                                   class="form-control form-control-sm @error('koreksi_catatan') is-invalid @enderror"
                                                   value="{{ old('koreksi_catatan', $invoice->koreksi_catatan) }}"
                                                   placeholder="Contoh: 2 siswa keluar dari rombel per tgl 10/09/2026"
                                                   required minlength="10">
                                            @error('koreksi_catatan')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="bi bi-save me-1"></i>Simpan Koreksi
                                            </button>
                                            <span class="text-muted small ms-2">
                                                Koreksi tercatat sebagai audit trail — tidak menghapus data sistem.
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Tampilkan koreksi read-only jika sudah approved --}}
                    @if($invoice->status === 'approved' && $invoice->hasKoreksi())
                    <div class="alert alert-warning py-2 small mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Catatan koreksi:</strong> {{ $invoice->koreksi_catatan }}
                        — oleh {{ $invoice->koreksiByUser?->name ?? '-' }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- ─── CATATAN KONTRAK ───────────────────────────────────── --}}
            <div class="card border-warning shadow-sm mb-4" style="border-radius: .75rem; border-width: 2px !important;">
                <div class="card-body p-4">
                    <div class="d-flex gap-3">
                        <div class="flex-shrink-0">
                            <span class="fs-2">⚠️</span>
                        </div>
                        <div>
                            <div class="fw-bold text-danger mb-2 text-uppercase small tracking-wide">
                                CATATAN KOMITMEN KONTRAK (TIDAK BOLEH ADA PEMBATALAN)
                            </div>
                            <p class="text-dark mb-0 small lh-lg">
                                Seluruh sesi pembelajaran yang telah dijadwalkan mengikat alokasi penugasan instruktur
                                dan sarana belajar Erlass Prokreatif Indonesia. Sesi pembelajaran
                                <strong>TIDAK DAPAT DIBATALKAN</strong> secara sepihak untuk pengurangan biaya tagihan.
                                Apabila terdapat kendala operasional internal sekolah (seperti kegiatan porseni, ujian sekolah,
                                atau libur insidental), pertemuan wajib dialihkan ke tanggal pengganti melalui prosedur
                                <strong>Reschedule resmi</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ─── RIGHT: Approval Panel ──────────────────────────────────── --}}
        <div class="col-lg-5">

            {{-- Checklist Alur ─────────────────────────────────────────── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4" style="background: #f8fafc;">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-check me-2 text-info"></i>Alur Approval</h6>
                </div>
                <div class="card-body px-4 py-3">
                    @php
                        $steps = [
                            ['label' => 'Draft Dibuat', 'done' => true, 'color' => 'success',
                             'detail' => 'Oleh ' . ($invoice->createdByUser?->name ?? 'Sistem') . ' · ' . $invoice->created_at->format('d/m/Y H:i')],
                            ['label' => 'Approval Operasional', 'done' => $invoice->operasional_status === 'approved', 'color' => $invoice->operasional_status === 'rejected' ? 'danger' : 'success',
                             'detail' => $invoice->operasional_status === 'approved'
                                ? '✅ ' . ($invoice->operasionalUser?->name ?? '-') . ' · ' . ($invoice->operasional_approved_at?->format('d/m/Y H:i') ?? '')
                                : ($invoice->operasional_status === 'rejected' ? '❌ Ditolak' : '⏳ Menunggu')],
                            ['label' => 'Approval Akunting', 'done' => $invoice->akunting_status === 'approved', 'color' => $invoice->akunting_status === 'rejected' ? 'danger' : 'success',
                             'detail' => $invoice->akunting_status === 'approved'
                                ? '✅ ' . ($invoice->akuntingUser?->name ?? '-') . ' · ' . ($invoice->akunting_approved_at?->format('d/m/Y H:i') ?? '')
                                : ($invoice->akunting_status === 'rejected' ? '❌ Ditolak' : '⏳ Menunggu')],
                            ['label' => 'PDF Resmi Diterbitkan', 'done' => $invoice->isApproved(), 'color' => 'success',
                             'detail' => $invoice->isApproved() ? '✅ Invoice approved & nomor resmi terbit' : '⏳ Nomor resmi INV/... terbit setelah disetujui Akunting'],
                        ];
                    @endphp
                    <div class="d-flex flex-column gap-3">
                        @foreach($steps as $i => $step)
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 mt-1">
                                @if($step['done'])
                                    <div class="rounded-circle bg-{{ $step['color'] }} d-flex align-items-center justify-content-center text-white"
                                         style="width:28px;height:28px;font-size:.8rem;">
                                        <i class="bi bi-check-lg"></i>
                                    </div>
                                @else
                                    <div class="rounded-circle border-2 border d-flex align-items-center justify-content-center text-muted"
                                         style="width:28px;height:28px;font-size:.8rem;">
                                        {{ $i + 1 }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="fw-semibold small text-dark">{{ $step['label'] }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $step['detail'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Action: Approve Operasional ───────────────────────────── --}}
            @if($invoice->status === 'pending_operasional' && auth()->user()->can('approveOperasional', $invoice))
            <div class="card border-warning shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4" style="background: #fffbeb;">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-warning-emphasis mb-0">
                            <i class="bi bi-person-check-fill me-2"></i>Approval Operasional / Pemeriksaan Produk
                        </h6>
                        <span class="badge bg-warning text-dark small">Pemeriksa: Operasional</span>
                    </div>
                </div>
                <div class="card-body px-4 py-3">
                    {{-- Quick Download PDF Draft untuk Operasional --}}
                    <div class="mb-3 p-2 px-3 bg-warning-subtle border border-warning-subtle rounded-3 d-flex align-items-center justify-content-between">
                        <div class="small text-warning-emphasis">
                            <i class="bi bi-file-earmark-text me-1"></i> <strong>Kirim rekap ke PIC?</strong> Unduh dokumen draft pra-tagihan untuk konfirmasi:
                        </div>
                        <a href="{{ route('invoice.pdf', $invoice) }}" class="btn btn-sm btn-warning text-dark fw-semibold" target="_blank">
                            <i class="bi bi-download me-1"></i>Unduh PDF Draft
                        </a>
                    </div>

                    <form action="{{ route('invoice.approve.operasional', $invoice) }}" method="POST">
                        @csrf
                        
                        {{-- Checklist Konfirmasi PIC Sekolah --}}
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_konfirmasi_pic" id="chk_pic_confirm" value="1" checked required>
                                <label class="form-check-label small fw-bold text-dark" for="chk_pic_confirm">
                                    <i class="bi bi-patch-check-fill text-success me-1"></i>Telah mendapat konfirmasi dari PIC Sekolah
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Nama PIC Sekolah yang Dihubungi <span class="text-danger">*</span></label>
                                <input type="text" name="pic_konfirmasi_nama" class="form-control form-control-sm bg-white" 
                                       placeholder="Contoh: Ibu Maria / Pak Budi (Wakasek / Koordinator Ekskul)" required>
                            </div>
                            <div class="text-muted small mb-2 fw-semibold" style="font-size: .75rem;">
                                <i class="bi bi-clipboard2-check me-1"></i>Item Pemeriksaan Produk:
                            </div>
                            <div class="form-check small mb-1">
                                <input class="form-check-input" type="checkbox" name="operasional_checklist[presensi_diverifikasi]" id="op_chk_1" value="1" checked required>
                                <label class="form-check-label text-secondary" for="op_chk_1">
                                    Presensi & sesi mengajar instruktur telah lengkap diverifikasi
                                </label>
                            </div>
                            <div class="form-check small mb-1">
                                <input class="form-check-input" type="checkbox" name="operasional_checklist[materi_tersampaikan]" id="op_chk_2" value="1" checked required>
                                <label class="form-check-label text-secondary" for="op_chk_2">
                                    Modul / materi dan laporan akhir sesi telah sesuai standar
                                </label>
                            </div>
                            <div class="form-check small">
                                <input class="form-check-input" type="checkbox" name="operasional_checklist[billable_sesuai_pic]" id="op_chk_3" value="1" checked required>
                                <label class="form-check-label text-secondary" for="op_chk_3">
                                    Total siswa billable ({{ $invoice->billable_efektif }} siswa) telah sesuai data PIC sekolah
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Catatan Pemeriksa (opsional)</label>
                            <textarea name="catatan" class="form-control form-control-sm" rows="2"
                                      placeholder="Catatan hasil verifikasi PIC & pemeriksaan produk..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="approved"
                                    class="btn btn-success btn-sm flex-fill fw-bold">
                                <i class="bi bi-check-circle me-1"></i>Setujui & Teruskan ke Akunting
                            </button>
                            <button type="submit" name="action" value="rejected"
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Tolak invoice ini?')">
                                <i class="bi bi-x-lg me-1"></i>Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Action: Approve Akunting ───────────────────────────────── --}}
            @if($invoice->status === 'pending_akunting' && auth()->user()->can('approveAkunting', $invoice))
            <div class="card border-info shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4" style="background: #eff6ff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-primary mb-0">
                            <i class="bi bi-bank2 me-2"></i>Approval Akunting & Distribusi Invoice
                        </h6>
                        <span class="badge bg-primary text-white small">Penerima: Akunting / Finance</span>
                    </div>
                </div>
                <div class="card-body px-4 py-3">
                    <form action="{{ route('invoice.approve.akunting', $invoice) }}" method="POST">
                        @csrf

                        {{-- Checklist Akunting setelah invoice tercetak --}}
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_invoice_tercetak" id="chk_inv_tercetak" value="1" checked required>
                                <label class="form-check-label small fw-bold text-dark" for="chk_inv_tercetak">
                                    <i class="bi bi-printer-fill text-primary me-1"></i>Invoice telah tercetak / dokumen PDF resmi siap diterbitkan
                                </label>
                            </div>
                            <div class="text-muted small mb-2 fw-semibold" style="font-size: .75rem;">
                                <i class="bi bi-clipboard2-check me-1"></i>Item Verifikasi Akunting:
                            </div>
                            <div class="form-check small mb-1">
                                <input class="form-check-input" type="checkbox" name="akunting_checklist[rekening_valid]" id="ak_chk_1" value="1" checked required>
                                <label class="form-check-label text-secondary" for="ak_chk_1">
                                    Nomor invoice, nama sekolah & rekening Erlass terverifikasi
                                </label>
                            </div>
                            <div class="form-check small mb-1">
                                <input class="form-check-input" type="checkbox" name="akunting_checklist[nominal_tarif_sesuai]" id="ak_chk_2" value="1" checked required>
                                <label class="form-check-label text-secondary" for="ak_chk_2">
                                    Tarif per siswa dan total tagihan akurat sesuai kesepakatan
                                </label>
                            </div>
                            <div class="form-check small">
                                <input class="form-check-input" type="checkbox" name="akunting_checklist[berkas_siap_edar]" id="ak_chk_3" value="1" checked required>
                                <label class="form-check-label text-secondary" for="ak_chk_3">
                                    Berkas siap dikirimkan secara resmi ke pihak sekolah
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Catatan Akunting (opsional)</label>
                            <textarea name="catatan" class="form-control form-control-sm" rows="2"
                                      placeholder="Catatan akunting / finance..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="approved"
                                    class="btn btn-success btn-sm flex-fill fw-bold">
                                <i class="bi bi-check-all me-1"></i>Final Approve (Terbitkan Nomor Resmi)
                            </button>
                            <button type="submit" name="action" value="rejected"
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Tolak invoice ini?')">
                                <i class="bi bi-x-lg me-1"></i>Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Catatan Operasional & Akunting ────────────────────────── --}}
            @if($invoice->operasional_catatan || $invoice->akunting_catatan || $invoice->is_konfirmasi_pic || $invoice->is_invoice_tercetak)
            <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4" style="background: #f8fafc;">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clipboard-data me-2"></i>Hasil Verifikasi & Catatan Pemeriksa</h6>
                </div>
                <div class="card-body px-4 py-3">
                    @if($invoice->operasional_status === 'approved' || $invoice->is_konfirmasi_pic)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="fw-semibold small text-primary">
                                <i class="bi bi-person-check-fill me-1"></i>Pemeriksaan Produk (Operasional)
                            </div>
                            <span class="badge bg-success-subtle text-success small"><i class="bi bi-check2-circle me-1"></i>Terverifikasi</span>
                        </div>
                        @if($invoice->pic_konfirmasi_nama)
                            <div class="small text-muted mb-1">
                                <strong>PIC Sekolah:</strong> {{ $invoice->pic_konfirmasi_nama }}
                            </div>
                        @endif
                        @if($invoice->is_konfirmasi_pic)
                            <div class="small text-success mb-1">
                                <i class="bi bi-check-circle-fill me-1"></i>Telah konfirmasi dengan PIC Sekolah
                            </div>
                        @endif
                        @if($invoice->operasional_catatan)
                            <div class="bg-light rounded p-2 small mt-1 text-dark">{{ $invoice->operasional_catatan }}</div>
                        @endif
                    </div>
                    @endif

                    @if($invoice->akunting_status === 'approved' || $invoice->is_invoice_tercetak)
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="fw-semibold small text-primary">
                                <i class="bi bi-bank2 me-1"></i>Verifikasi Akunting & Cetak
                            </div>
                            <span class="badge bg-success-subtle text-success small"><i class="bi bi-check2-circle me-1"></i>Invoice Tercetak</span>
                        </div>
                        @if($invoice->is_invoice_tercetak)
                            <div class="small text-success mb-1">
                                <i class="bi bi-printer-fill me-1"></i>Dokumen invoice fisik/digital siap diedarkan
                            </div>
                        @endif
                        @if($invoice->akunting_catatan)
                            <div class="bg-light rounded p-2 small mt-1 text-dark">{{ $invoice->akunting_catatan }}</div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- ─── MODAL KOREKSI PER ITEM ROMBEL ───────────────────────────────────────── --}}
@if($invoice->status !== 'approved' && $invoice->items->isNotEmpty())
    @foreach($invoice->items as $item)
    <div class="modal fade" id="koreksiModalItem{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: .875rem;">
                <div class="modal-header bg-warning-subtle border-0 py-3 px-4">
                    <h6 class="modal-title fw-bold text-warning-emphasis">
                        <i class="bi bi-pencil-square me-2"></i>Koreksi Billable — {{ $item->rombel?->nama_rombel }}
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('invoice.koreksi', $invoice) }}" method="POST">
                    @csrf
                    <input type="hidden" name="invoice_approval_item_id" value="{{ $item->id }}">
                    <div class="modal-body p-4">
                        <div class="alert alert-light border mb-3 py-2 small">
                            <div><strong>Program:</strong> {{ $item->rombel?->ekstrakurikuler?->kategori_program ?? '-' }}</div>
                            <div><strong>Siswa Terhitung Sistem:</strong> {{ $item->jumlah_siswa_billable }} siswa</div>
                            @if($item->hasKoreksi())
                                <div class="text-warning-emphasis mt-1">
                                    <i class="bi bi-info-circle me-1"></i>Koreksi saat ini: <strong>{{ $item->koreksi_siswa_billable }} siswa</strong> ({{ $item->koreksi_catatan }})
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Jumlah Siswa Billable Baru <span class="text-danger">*</span></label>
                            <input type="number" name="koreksi_siswa_billable" class="form-control"
                                   value="{{ old('koreksi_siswa_billable', $item->koreksi_siswa_billable ?? $item->jumlah_siswa_billable) }}"
                                   min="0" max="999" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Alasan Koreksi <span class="text-danger">*</span> <span class="text-muted fw-normal">(min. 10 karakter)</span></label>
                            <textarea name="koreksi_catatan" class="form-control" rows="3"
                                      placeholder="Jelaskan alasan selisih hadir lapangan..."
                                      minlength="10" required>{{ old('koreksi_catatan', $item->koreksi_catatan) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 px-4 py-3">
                        @if($item->hasKoreksi())
                        <button type="submit" formaction="{{ route('invoice.koreksi.reset', $invoice) }}" name="item_id" value="{{ $item->id }}"
                                class="btn btn-sm btn-outline-secondary me-auto" onclick="return confirm('Reset koreksi rombel ini ke sistem?')">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset ke Sistem
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-warning fw-semibold px-4">
                            <i class="bi bi-save me-1"></i>Simpan Koreksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif
@endsection
