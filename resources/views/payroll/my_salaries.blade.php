@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Slip Gaji & Kompensasi Saya</h1>
                    <p class="text-muted mb-0">Rincian pendapatan bulanan Anda berdasarkan sesi mengajar, bonus, dan punctuality.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table List -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">Daftar Pendapatan Bulanan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Periode</th>
                            <th>Kode Batch</th>
                            <th>Total Sesi</th>
                            <th>Honor Dasar</th>
                            <th>Bonus Produk</th>
                            <th>Uang Transport</th>
                            <th>Potongan Denda</th>
                            <th>Honor Bersih (Netto)</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">
                                    {{ $item->batch->periode->format('F Y') }}
                                </td>
                                <td class="font-monospace text-muted">{{ $item->batch->code }}</td>
                                <td>{{ $item->total_sessions }} Sesi</td>
                                <td>Rp {{ number_format($item->total_base_fee, 2, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->total_product_bonus, 2, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->total_transport_fee, 2, ',', '.') }}</td>
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
                                <td>
                                    @if ($item->status === 'pending')
                                        <span class="badge bg-secondary">Draft</span>
                                    @elseif ($item->status === 'approved')
                                        <span class="badge bg-primary">Terverifikasi</span>
                                    @elseif ($item->status === 'paid')
                                        <span class="badge bg-success">Lunas Dibayar</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('payroll.slip.show', $item->id) }}" class="btn btn-sm btn-outline-primary px-3">
                                        <i class="bi bi-file-earmark-text me-1"></i> Rincian Slip
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-file-earmark-x text-muted fs-1 opacity-25"></i>
                                    </div>
                                    <h6 class="text-muted">Belum Ada Slip Gaji yang Diterbitkan</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination-wrapper :paginator="$items" class="bg-white border-top py-3" />
        </div>
    </div>
</div>
@endsection
