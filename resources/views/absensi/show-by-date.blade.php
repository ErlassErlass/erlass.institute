@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="display-6 fw-bold text-dark">
            🗓️ Absensi Tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
        </h1>
        <p class="lead text-muted">
            {{ $laporan_mengajar->sekolah_nama }} - {{ $laporan_mengajar->rombel }}
        </p>
    </div>

    <!-- Table -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable" id="absensi-show-table">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama Siswa</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensis as $index => $absen)
                        <tr>
                            <td class="px-4 py-3 text-muted">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 fw-bold">{{ $absen->siswa->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($absen->hadir == 1)
                                    <span class="badge bg-success rounded-pill">
                                        Hadir
                                    </span>
                                @elseif($absen->hadir == 0)
                                    <span class="badge bg-danger rounded-pill">
                                        Tidak Hadir
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill">
                                        Keluar
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $absen->catatan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Tidak ada data absensi untuk tanggal ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ route('laporan-mengajar.absensi.index', $laporan_mengajar->id) }}"
           class="btn btn-outline-primary shadow-sm hover-shadow">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Rekap Absensi
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable for Attendance Show table
        if (typeof window.DataTableManager !== 'undefined') {
            const dataTableManager = new window.DataTableManager();
            dataTableManager.init('#absensi-show-table', {
                order: [[1, 'asc']], // Sort by Student Name column
                columnDefs: [
                    { orderable: false, targets: [0] }, // Disable sorting for No. column
                    { type: 'string', targets: [1, 2, 3] } // String sorting for other columns
                ],
                pageLength: 25,
                language: {
                    search: "Cari Siswa:",
                    lengthMenu: "Tampilkan _MENU_ siswa",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }
    });
</script>
<style>
/* Custom style to make table header primary color */
#absensi-show-table thead th {
    background-color: var(--bs-primary) !important;
    color: white !important;
    border-bottom: 0;
}
.hover-shadow:hover {
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    transform: translateY(-1px);
    transition: all .2s;
}
</style>
@endpush
