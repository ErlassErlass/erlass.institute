@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex align-items-center gap-2 mb-4">
        @if (auth()->user()->role === 'instruktur')
            <a href="{{ route('payroll.my-salaries') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        @else
            <a href="{{ route('admin.payroll.batches.show', $item->payroll_batch_id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Batch
            </a>
        @endif
        <h5 class="mb-0 fw-bold text-dark">Rincian Slip Gaji</h5>
    </div>

    <!-- Alert for Actions -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Receipt Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-center pb-4 border-bottom mb-4">
                            <h5 class="fw-bold text-dark mb-0">Erlass Institute</h5>
                            <small class="text-muted">SLIP GAJI INSTRUKTUR</small>
                            <h4 class="fw-bold text-primary mt-3 mb-0">Rp {{ number_format($item->net_salary, 2, ',', '.') }}</h4>
                            <span class="badge bg-light text-dark mt-2 border">{{ $item->batch->code }}</span>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted d-block small uppercase fw-bold mb-2">Informasi Penerima</small>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Nama:</span>
                                <span class="fw-semibold text-dark text-end">{{ $item->instruktur->nama_lengkap }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">ID Instruktur:</span>
                                <span class="fw-semibold text-dark font-monospace">{{ $item->instruktur->instructor_id ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Level:</span>
                                <span class="fw-semibold text-dark text-capitalize">{{ str_replace('_', ' ', $item->instruktur->level) }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted d-block small uppercase fw-bold mb-2">Transfer Bank</small>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Bank:</span>
                                <span class="fw-semibold text-dark">{{ $item->instruktur->instructorProfile->nama_bank ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">No Rekening:</span>
                                <span class="fw-semibold text-dark font-monospace">{{ $item->instruktur->instructorProfile->no_rekening ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted d-block small uppercase fw-bold mb-2">Rincian Kompensasi</small>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Mengajar:</span>
                                <span class="fw-semibold text-dark">{{ $item->total_sessions }} Sesi</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Honor Dasar:</span>
                                <span class="fw-semibold text-dark">Rp {{ number_format($item->total_base_fee, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Bonus Produk:</span>
                                <span class="fw-semibold text-dark">Rp {{ number_format($item->total_product_bonus, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Uang Transport:</span>
                                <span class="fw-semibold text-dark">Rp {{ number_format($item->total_transport_fee, 2, ',', '.') }}</span>
                            </div>
                            @if ($item->total_penalty > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-danger">Denda Terlambat:</span>
                                    <span class="fw-semibold text-danger">-Rp {{ number_format($item->total_penalty, 2, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-top pt-4 text-center">
                        <small class="text-muted d-block mb-1">Status Pembayaran</small>
                        @if ($item->status === 'pending')
                            <span class="badge bg-secondary py-2 px-4 fs-6">DRAFT BATCH</span>
                        @elseif ($item->status === 'approved')
                            <span class="badge bg-primary py-2 px-4 fs-6">TERVERIFIKASI</span>
                        @elseif ($item->status === 'paid')
                            <span class="badge bg-success py-2 px-4 fs-6">LUNAS</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sessions List Table -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">Sesi Mengajar Terhitung</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Pertemuan / Sekolah</th>
                                    <th>Jadwal / Rombel</th>
                                    <th>Waktu Check-In</th>
                                    <th>Honor Sesi</th>
                                    <th>Transport</th>
                                    <th>Denda</th>
                                    @if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']))
                                        <th class="text-center">Koreksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item->sessions as $session)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">Pertemuan Ke-{{ $session->nomor_pertemuan }}</div>
                                            <small class="text-muted">{{ $session->ekstrakurikuler->sekolah->namasekolah ?? 'Sekolah' }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $session->rombel->nama_rombel }}</div>
                                            <small class="text-muted">{{ $session->tanggal_pelaksanaan ? $session->tanggal_pelaksanaan->format('d/m/Y') : $session->tanggal_terjadwal->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            @if ($session->jam_mulai_aktual)
                                                <div class="fw-semibold text-dark">{{ $session->jam_mulai_aktual->format('H:i') }}</div>
                                                @if ($session->actual_checkin_status === 'excellent')
                                                    <span class="badge bg-success bg-opacity-10 text-success">Excellent</span>
                                                @elseif ($session->actual_checkin_status === 'on_time')
                                                    <span class="badge bg-info bg-opacity-10 text-info">On Time</span>
                                                @elseif ($session->actual_checkin_status === 'warning')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning">Warning</span>
                                                @elseif ($session->actual_checkin_status === 'penalty')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger">Penalty</span>
                                                @endif
                                            @else
                                                <span class="text-muted italic small">No Checkin</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($session->override_fee !== null)
                                                <span class="text-decoration-line-through text-muted small">Rp {{ number_format($session->calculated_fee, 0, ',', '.') }}</span>
                                                <div class="fw-bold text-success">Rp {{ number_format($session->override_fee, 0, ',', '.') }}</div>
                                            @else
                                                <span class="fw-semibold text-dark">Rp {{ number_format($session->calculated_fee, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">Rp {{ number_format($session->transport_fee, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            @if ($session->actual_checkin_penalty > 0)
                                                <span class="text-danger fw-semibold">-Rp {{ number_format($session->actual_checkin_penalty, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        @if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']))
                                            <td class="text-center">
                                                @if($item->status === 'pending')
                                                    <form method="POST" action="{{ route('ekstrakurikuler.sessions.override-fee', $session->id) }}" class="d-flex justify-content-center align-items-center gap-1">
                                                        @csrf
                                                        <input 
                                                            type="number" 
                                                            name="override_fee" 
                                                            class="form-control form-control-sm text-center" 
                                                            style="width: 100px;" 
                                                            placeholder="Override" 
                                                            value="{{ $session->override_fee }}"
                                                        >
                                                        <button type="submit" class="btn btn-sm btn-primary" title="Terapkan">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small italic">Locked</span>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
