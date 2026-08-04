@extends('layouts.app')

@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Pencairan Payroll & Kompensasi</h1>
                    <p class="text-muted mb-0">Kelola dan cairkan batch honor sesi mengajar bulanan instruktur secara terpusat.</p>
                </div>
            </div>
        </div>
    </div>



    <div class="row g-4 mb-4">
        <!-- Stats Card 1 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                        <i class="bi bi-wallet2 fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Batch Terverifikasi</h6>
                        <h3 class="fw-bold mb-0">{{ $totalProcessed }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Card 2 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success">
                        <i class="bi bi-check2-circle fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Batch Lunas Dibayar</h6>
                        <h3 class="fw-bold mb-0">{{ $totalPaid }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Card 3 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning">
                        <i class="bi bi-hourglass-split fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Sesi Layak Bayar (Unpaid)</h6>
                        <h3 class="fw-bold mb-0">{{ $unpaidSessionsCount }} Sesi</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- List Batches Table -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">Daftar Batch Payroll</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Kode Batch</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Total Transfer</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($batches as $item)
                                    <tr>
                                        <td class="ps-4 fw-bold font-monospace">{{ $item->code }}</td>
                                        <td>{{ $item->periode->format('F Y') }}</td>
                                        <td>
                                            @if ($item->status === 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif ($item->status === 'processed')
                                                <span class="badge bg-primary">Terverifikasi</span>
                                            @elseif ($item->status === 'paid')
                                                <span class="badge bg-success">Lunas</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-dark">
                                            Rp {{ number_format($item->items->sum('net_salary'), 2, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('admin.payroll.batches.show', $item->id) }}" class="btn btn-sm btn-outline-primary px-2" title="Lihat Detail">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>
                                                @if ($item->status === 'draft')
                                                    <form action="{{ route('admin.payroll.batches.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Draft Batch {{ $item->code }} ini? Sesi mengajar akan dikembalikan ke status unpaid.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2" title="Hapus Draft Batch">
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
                                            <h6 class="text-muted">Belum Ada Batch Payroll Terbuat</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <x-pagination-wrapper :paginator="$batches" class="bg-white border-top py-3" />
                </div>
            </div>
        </div>

        <!-- Create Batch Form -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">Generate Batch Baru</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.payroll.batches.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="month" class="form-label fw-semibold text-muted small">Pilih Bulan & Tahun</label>
                            <input 
                                type="month" 
                                class="form-control @error('month') is-invalid @enderror" 
                                id="month" 
                                name="month" 
                                value="{{ old('month', date('Y-m')) }}" 
                                required
                            >
                            <div class="form-text small text-primary mt-2 p-2 bg-light rounded border border-primary border-opacity-25">
                                <i class="bi bi-calendar-range me-1"></i> <strong>Rentang Cutoff:</strong> Tanggal 11 bulan sebelumnya s.d. Tanggal 10 bulan terpilih.
                                <br><small class="text-muted">Contoh: Memilih <strong>Agustus 2026</strong> akan menyaring sesi/laporan dari <em>11 Juli s/d 10 Agustus 2026</em>.</small>
                            </div>
                            @error('month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold text-muted small">Catatan Batch (Opsional)</label>
                            <textarea 
                                class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" 
                                name="notes" 
                                rows="3" 
                                placeholder="Tulis catatan penting terkait batch ini..."
                            >{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="bi bi-gear-fill me-1"></i> Generate Draft Payroll
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
