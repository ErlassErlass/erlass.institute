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

    <!-- Policy Information Banner (Non-Confidential TAB 2025/2026 Rules) -->
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark">Informasi Ketentuan Kompensasi & Transportasi Instruktur</h5>
            </div>
            
            <div class="row g-3 text-dark mb-3" style="font-size: 0.88rem;">
                <div class="col-12">
                    <div class="p-3 bg-white rounded border border-primary border-opacity-25" style="background-color: #f0f7ff !important;">
                        <div class="d-flex align-items-center gap-2 text-primary fw-bold mb-1">
                            <i class="bi bi-calendar2-range-fill fs-5"></i>
                            <span class="fs-6">Ketentuan Periode Cutoff Penggajian Bulanan</span>
                        </div>
                        <p class="mb-0 text-secondary">
                            Perhitungan honorarium bulanan dihitung dari <strong>Tanggal 11 Bulan Sebelumnya s.d. Tanggal 10 Bulan Berjalan</strong> (Contoh: Gaji Periode Juli menghitung seluruh sesi &amp; laporan mengajar dari <em>11 Juni s/d 10 Juli</em>). Laporan mengajar yang baru diselesaikan setelah tanggal 10 akan otomatis ditarik pada periode bulan berikutnya (tidak hangus).
                        </p>
                    </div>
                </div>
            </div>

            <div class="row g-3 text-dark" style="font-size: 0.88rem;">
                <div class="col-md-6">
                    <div class="p-3 bg-white rounded border h-100">
                        <div class="fw-bold text-primary mb-2">
                            <i class="bi bi-people-fill me-1"></i> Skala Rombel & Honor Mengajar Utama
                        </div>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            <li><span class="badge bg-success me-1">≥ 15 Siswa</span> <strong>Rp 150.000</strong> / sesi <span class="text-muted">(Berjalan)</span></li>
                            <li><span class="badge bg-success me-1">12 - 14 Siswa</span> <strong>Rp 115.000</strong> / sesi <span class="text-muted">(Berjalan)</span></li>
                            <li><span class="badge bg-success me-1">10 - 11 Siswa</span> <strong>Rp 100.000</strong> / sesi <span class="text-muted">(Berjalan)</span></li>
                            <li><span class="badge bg-warning text-dark me-1">8 - 9 Siswa</span> <strong>Rp 75.000</strong> / sesi <span class="text-muted">(Minimum)</span></li>
                            <li><span class="badge bg-danger me-1">&lt; 8 Siswa</span> <strong>Rp 0</strong> <span class="text-danger fw-bold">(Hold / Ditunda)</span></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="p-3 bg-white rounded border h-100">
                        <div class="fw-bold text-primary mb-2">
                            <i class="bi bi-truck me-1"></i> Transportasi & Ketentuan Operasional
                        </div>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-muted">
                            <li><i class="bi bi-check-circle-fill text-success me-1"></i> <strong>Transport &amp; Sewa Kendaraan</strong>: Jarak ≥ 10 KM (Bensin 2x PP + Sewa Rp 7.500), Jarak &lt; 10 KM (Flat Sewa Kendaraan Rp 7.500).</li>
                            <li><i class="bi bi-check-circle-fill text-success me-1"></i> <strong>Guru Internal &amp; Sesi Erlass</strong>: Biaya transport Rp 0 (Hanya honor mengajar).</li>
                            <li><i class="bi bi-check-circle-fill text-success me-1"></i> <strong>Asisten Instruktur</strong>: Honor Rp 100.000 berlaku untuk Rombel &gt; 24 siswa.</li>
                            <li><i class="bi bi-check-circle-fill text-success me-1"></i> <strong>Kedisiplinan Check-in</strong>: Keterlambatan ≥ 15 menit dikenakan penyesuaian Rp 25.000.</li>
                        </ul>
                    </div>
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
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Periode</th>
                            <th>Kode Batch</th>
                            <th class="text-center">Sesi (U/A)</th>
                            <th class="text-end">Honor Mengajar</th>
                            <th class="text-end">Honor Asisten</th>
                            <th class="text-end">Uang Transport</th>
                            <th class="text-end">Pajak (2.5%)</th>
                            <th class="text-end">Potongan Denda</th>
                            <th class="text-end">Gaji Bersih (Netto)</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            @php
                                $sesiU = $item->total_sessions_utama ?: $item->payrollItemSessions->where('role', 'utama')->count();
                                $sesiA = $item->total_sessions_asisten ?: $item->payrollItemSessions->where('role', 'asisten')->count();
                                if ($sesiU === 0 && $sesiA === 0) $sesiU = $item->total_sessions;

                                $gross = $item->total_gross_salary ?: ($item->total_base_fee + $item->total_asisten_fee + $item->total_product_bonus + $item->total_transport_fee);
                                $tax = $item->tax_amount ?: round($gross * 0.025);
                            @endphp
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">
                                    {{ $item->batch->periode->format('F Y') }}
                                </td>
                                <td class="font-monospace text-muted">{{ $item->batch->code }}</td>
                                <td class="text-center">
                                    <strong>{{ $item->total_sessions }}</strong> Sesi
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        ({{ $sesiU }} U / {{ $sesiA }} A)
                                    </div>
                                </td>
                                <td class="text-end">Rp {{ number_format($item->total_base_fee, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    @if ($item->total_asisten_fee > 0)
                                        <span class="text-info fw-semibold">Rp {{ number_format($item->total_asisten_fee, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($item->total_transport_fee, 0, ',', '.') }}</td>
                                <td class="text-end text-warning fw-semibold">
                                    @if ($tax > 0)
                                        -Rp {{ number_format($tax, 0, ',', '.') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($item->total_penalty > 0)
                                        <span class="text-danger fw-semibold">-Rp {{ number_format($item->total_penalty, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($item->net_salary, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if ($item->status === 'pending')
                                        <span class="badge bg-secondary">Draft</span>
                                    @elseif ($item->status === 'approved' || $item->status === 'processed')
                                        <span class="badge bg-primary">Terverifikasi</span>
                                    @elseif ($item->status === 'paid')
                                        <span class="badge bg-success">Lunas</span>
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
                                <td colspan="11" class="text-center py-5">
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
