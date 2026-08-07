@extends('layouts.app')

@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

@section('content')
<div class="container-fluid py-4">
    <!-- Hero Banner (Bright / Light Theme) -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 50%, #E0F2FE 100%); border-radius: 1rem; border: 1px solid #DBEAFE !important;">
        <div class="card-body p-4 p-lg-5 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2.5">
                        <span class="badge bg-primary text-white shadow-sm rounded-pill px-3 py-1.5 small fw-bold">
                            <i class="bi bi-shield-lock-fill me-1 text-white"></i> Mode Otorisasi Terproteksi
                        </span>
                        <span class="badge bg-warning text-dark shadow-sm rounded-pill px-3 py-1.5 small fw-bold">
                            <i class="bi bi-clock-history me-1"></i> Cutoff 11 – 10
                        </span>
                    </div>
                    <h1 class="h2 fw-bold text-dark mb-2">Pencairan Payroll & Kompensasi</h1>
                    <p class="text-muted mb-0 fs-6" style="max-width: 680px;">
                        Kelola batch honorarium sesi mengajar bulanan instruktur secara terpusat. Dilengkapi dengan <strong>6 Aturan Warning Calculator Engine</strong>, validasi presensi H+1, dan ekspor rincian payroll (.xlsx / .csv / .pdf).
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="bg-white rounded-3 p-3 border border-secondary-subtle shadow-sm d-inline-block text-start" style="min-width: 230px;">
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Otorisasi Kelola Payroll:</div>
                        <div class="fw-bold text-dark small mt-1.5">
                            <i class="bi bi-person-check-fill text-success me-1"></i> Adinda Wardania
                        </div>
                        <div class="fw-bold text-dark small mt-1">
                            <i class="bi bi-person-check-fill text-success me-1"></i> Cornelis Banu
                        </div>
                        <div class="fw-bold text-dark small mt-1">
                            <i class="bi bi-shield-lock-fill text-warning me-1"></i> Webmaster IT
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- Stats Card 1 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white hover-shadow transition-all">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-4 bg-primary bg-opacity-10 p-3 me-3 text-primary">
                        <i class="bi bi-wallet2 fs-2"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider mb-0.5">Batch Terverifikasi</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalProcessed) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Card 2 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white hover-shadow transition-all">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-4 bg-success bg-opacity-10 p-3 me-3 text-success">
                        <i class="bi bi-check2-circle fs-2"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider mb-0.5">Batch Lunas Dibayar</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalPaid) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Card 3 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white hover-shadow transition-all">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-4 bg-warning bg-opacity-10 p-3 me-3 text-warning">
                        <i class="bi bi-hourglass-split fs-2"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider mb-0.5">Sesi Layak Bayar (Unpaid)</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($unpaidSessionsCount) }} <span class="fs-6 text-muted fw-normal">Sesi</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- List Batches Table -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-journal-text text-primary me-2"></i>Daftar Batch Payroll
                    </h5>
                    <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill small fw-medium">
                        Total: {{ $batches->total() }} Batch
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Kode Batch</th>
                                    <th>Periode Cutoff</th>
                                    <th>Status</th>
                                    <th>Total Transfer</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($batches as $item)
                                    <tr>
                                        <td class="ps-4 fw-bold font-monospace text-dark">{{ $item->code }}</td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $item->periode->format('F Y') }}</span>
                                        </td>
                                        <td>
                                            @if ($item->status === 'draft')
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis border px-2.5 py-1 rounded-pill small fw-bold">
                                                    <i class="bi bi-pencil me-1 text-secondary"></i> Draft
                                                </span>
                                            @elseif ($item->status === 'processed')
                                                <span class="badge bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-pill small fw-bold">
                                                    <i class="bi bi-check-circle me-1 text-primary"></i> Terverifikasi
                                                </span>
                                            @elseif ($item->status === 'paid')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill small fw-bold">
                                                    <i class="bi bi-check-all me-1"></i> Lunas Dibayar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-dark">
                                            Rp {{ number_format($item->items->sum('net_salary'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center gap-1.5">
                                                <a href="{{ route('admin.payroll.batches.show', $item->id) }}" class="btn btn-sm btn-light border text-primary fw-semibold px-2.5" title="Lihat Detail Rincian Payroll">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>
                                                @if ($item->status === 'draft' && auth()->user()->isPrimaryAdmin())
                                                    <form action="{{ route('admin.payroll.batches.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Draft Batch {{ $item->code }} ini? Sesi mengajar akan dikembalikan ke status unpaid.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light border text-danger px-2.5" title="Hapus Draft Batch">
                                                            <i class="bi bi-trash me-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="mb-3">
                                                <i class="bi bi-journal-x text-muted fs-1 opacity-25"></i>
                                            </div>
                                            <h6 class="text-muted fw-semibold mb-1">Belum Ada Batch Payroll Terbuat</h6>
                                            <p class="text-muted small mb-0">Gunakan formulir di sebelah kanan untuk merekap batch baru.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <x-pagination-wrapper :paginator="$batches" class="bg-white border-top py-3 px-4" />
                </div>
            </div>
        </div>

        <!-- Create Batch Form -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-plus-circle-fill text-primary me-2"></i>Generate Batch Baru
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if (auth()->user()->isPrimaryAdmin())
                        <form method="POST" action="{{ route('admin.payroll.batches.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="month" class="form-label fw-semibold text-dark small">Pilih Bulan & Tahun Cutoff</label>
                                <input 
                                    type="month" 
                                    class="form-control form-control-lg @error('month') is-invalid @enderror" 
                                    id="month" 
                                    name="month" 
                                    value="{{ old('month', date('Y-m')) }}" 
                                    required
                                >
                                <div class="form-text small text-primary mt-2.5 p-3 bg-blue-50 rounded-3 border border-blue-200">
                                    <i class="bi bi-calendar-range me-1 fw-bold"></i> <strong>Aturan Cutoff Payroll:</strong>
                                    <div class="mt-1 text-secondary">Tanggal 11 bulan sebelumnya s.d. Tanggal 10 bulan terpilih.</div>
                                    <div class="mt-1 text-muted small">Contoh: Memilih <strong>Agustus 2026</strong> akan menyaring sesi & laporan mengajar dari <em>11 Juli s/d 10 Agustus 2026</em>.</div>
                                </div>
                                @error('month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label fw-semibold text-dark small">Catatan Batch (Opsional)</label>
                                <textarea 
                                    class="form-control @error('notes') is-invalid @enderror" 
                                    id="notes" 
                                    name="notes" 
                                    rows="3" 
                                    placeholder="Tulis catatan penting terkait batch ini (contoh: Honor Pengajaran Periode Agustus)..."
                                >{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg py-2.5 fw-semibold shadow-sm">
                                    <i class="bi bi-gear-fill me-1"></i> Generate Draft Payroll
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 text-dark small p-3 mb-0">
                            <div class="d-flex gap-2">
                                <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
                                <div>
                                    <strong class="d-block mb-1">Akses Pembuatan Batch Dibatasi</strong>
                                    Pembuatan dan pencairan batch payroll memerlukan verifikasi Admin Utama (<strong>Adinda Wardania</strong> atau <strong>Cornelis Banu</strong>).
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
