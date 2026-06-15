@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.payroll.batches.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-1">Batch Detail: {{ $batch->code }}</h1>
                        <p class="text-muted mb-0">Periode: {{ $batch->periode->format('F Y') }}</p>
                    </div>
                </div>
                <div>
                    @if ($batch->status === 'draft')
                        <span class="badge bg-secondary p-2 px-3 fs-6">Draft</span>
                    @elseif ($batch->status === 'processed')
                        <span class="badge bg-primary p-2 px-3 fs-6">Terverifikasi</span>
                    @elseif ($batch->status === 'paid')
                        <span class="badge bg-success p-2 px-3 fs-6">Lunas Dibayar</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alert and Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-octagon-fill me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Batch Stats Summary -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">Informasi Batch</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block mb-1">Total Instruktur</small>
                            <span class="fw-bold text-dark fs-5">{{ $batch->items->count() }} orang</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block mb-1">Total Sesi Terbayar</small>
                            <span class="fw-bold text-dark fs-5">{{ $batch->items->sum('total_sessions') }} Sesi</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block mb-1">Total Potongan Denda</small>
                            <span class="fw-bold text-danger fs-5">Rp {{ number_format($batch->items->sum('total_penalty'), 2, ',', '.') }}</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block mb-1">Total Transfer Netto</small>
                            <span class="fw-bold text-success fs-5">Rp {{ number_format($batch->items->sum('net_salary'), 2, ',', '.') }}</span>
                        </div>
                    </div>
                    @if ($batch->notes)
                        <hr>
                        <div>
                            <small class="text-muted d-block mb-1">Catatan Batch</small>
                            <p class="mb-0 text-dark">{{ $batch->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Control Panel -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">Panel Tindakan</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    @if ($batch->status === 'draft')
                        <p class="text-muted small">Silakan periksa detail honor per instruktur di bawah. Setelah diverifikasi, Anda dapat memproses batch ini.</p>
                        <form method="POST" action="{{ route('admin.payroll.batches.process', $batch->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin memverifikasi dan memproses batch ini?')">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-shield-check me-1"></i> Verifikasi & Proses Batch
                            </button>
                        </form>
                    @elseif ($batch->status === 'processed')
                        <p class="text-muted small">Batch ini telah diverifikasi. Tandai batch sebagai lunas setelah proses pencairan/transfer dana bank selesai dilakukan.</p>
                        <form method="POST" action="{{ route('admin.payroll.batches.pay', $batch->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menandai batch ini telah lunas dibayar?')">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 py-2">
                                <i class="bi bi-check-lg me-1"></i> Cairkan & Tandai Lunas
                            </button>
                        </form>
                    @elseif ($batch->status === 'paid')
                        <div class="text-center py-2">
                            <div class="text-success fs-1 mb-2"><i class="bi bi-patch-check-fill"></i></div>
                            <h6 class="fw-bold text-success mb-1">Batch Selesai (Lunas)</h6>
                            <p class="text-muted small mb-0">Dicairkan oleh {{ $batch->payer->nama_lengkap ?? 'System' }} pada {{ $batch->paid_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Instructor Breakdown Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">Rincian Kompensasi Instruktur</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Instruktur</th>
                            <th>Total Sesi</th>
                            <th>Total Honor Dasar</th>
                            <th>Bonus Produk</th>
                            <th>Potongan Denda</th>
                            <th>Honor Netto</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batch->items as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $item->instruktur->nama_lengkap }}</div>
                                    <small class="text-muted font-monospace">{{ $item->instruktur->instructor_id ?? '-' }}</small>
                                </td>
                                <td>{{ $item->total_sessions }} Sesi</td>
                                <td>Rp {{ number_format($item->total_base_fee, 2, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->total_product_bonus, 2, ',', '.') }}</td>
                                <td>
                                    @if ($item->total_penalty > 0)
                                        <span class="text-danger fw-semibold">-Rp {{ number_format($item->total_penalty, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success">
                                    Rp {{ number_format($item->net_salary, 2, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('payroll.slip.show', $item->id) }}" class="btn btn-sm btn-outline-secondary px-3">
                                        <i class="bi bi-file-earmark-text me-1"></i> Slip Gaji
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
