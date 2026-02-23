@extends('layouts.app')

@section('title', 'Rekap Absensi Harian')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Rekap Absensi per Tanggal</h1>
                    <p class="mb-0 text-muted">
                        Untuk: {{ $laporan_mengajar->sekolah->namasekolah ?? 'N/A' }} - Rombel {{ $laporan_mengajar->rombel }}
                    </p>
                </div>
                <a href="{{ route('laporan-mengajar.show', $laporan_mengajar) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Laporan
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Pilih Tanggal untuk Melihat Detail</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 datatable" id="absensi-dates-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tanggal Absensi</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($absensi_per_tanggal as $data)
                                <tr class="align-middle">
                                    <td class="ps-4">
                                        <i class="bi bi-calendar-check text-primary me-2"></i>
                                        <strong>{{ \Carbon\Carbon::parse($data->tanggal)->isoFormat('dddd, D MMMM Y') }}</strong>
                                    </td>
                                    <td class="text-end pe-4">
                                        {{-- ✅ ROUTE DIPERBAIKI: Menggunakan nama dan parameter yang benar --}}
                                        <a href="{{ route('laporan-mengajar.absensi.showByDate', ['laporan_mengajar' => $laporan_mengajar->id, 'tanggal' => $data->tanggal]) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Lihat Detail Absensi
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-5">
                                        <i class="bi bi-calendar-x fs-3"></i>
                                        <p class="mt-2 mb-0">Belum ada catatan absensi untuk laporan ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable for Attendance Dates table
        if (typeof window.DataTableManager !== 'undefined') {
            const dataTableManager = new window.DataTableManager();
            dataTableManager.init('#absensi-dates-table', {
                order: [[0, 'desc']], // Sort by Date column (newest first)
                columnDefs: [
                    { orderable: false, targets: [1] }, // Disable sorting for Actions column
                    { type: 'date', targets: [0] } // Date sorting for date column
                ],
                pageLength: 15
            });
        }
    });
</script>
@endpush