@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">🗓️ Rekap Absensi Tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</h1>
            
            @if($laporan_mengajar)
                <p class="text-muted mb-0">
                    Sekolah: <strong class="text-dark">{{ $laporan_mengajar->sekolah_nama }}</strong> | 
                    Rombel: <strong class="text-dark">{{ $laporan_mengajar->rombel }}</strong>
                </p>
            @else
                <p class="text-muted mb-0">
                    Menampilkan absensi dari beberapa laporan.
                </p>
            @endif
        </div>
        
        <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
            <form method="GET" action="{{ route('rekap-absensi') }}" class="d-inline">
                <button type="submit" class="btn btn-light border w-100 w-sm-auto d-flex align-items-center justify-content-center">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </button>
            </form>
            
            <form method="GET" action="{{ route('absensi.rekap-by-date', $tanggal) }}" class="flex-grow-1">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" placeholder="Cari nama siswa..." 
                           value="{{ request('search') }}" 
                           class="form-control border-start-0 ps-0">
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <div class="d-flex align-items-center">
                <span class="fw-bold text-muted small text-uppercase me-2">Filter:</span>
                <div class="btn-group" role="group">
                    <a href="{{ route('absensi.rekap-by-date', ['tanggal' => $tanggal, 'status' => 'all']) }}" 
                       class="btn btn-sm {{ request('status') == 'all' || !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Semua
                    </a>
                    <a href="{{ route('absensi.rekap-by-date', ['tanggal' => $tanggal, 'status' => 'hadir']) }}" 
                       class="btn btn-sm {{ request('status') == 'hadir' ? 'btn-success' : 'btn-outline-secondary' }}">
                        Hadir
                    </a>
                    <a href="{{ route('absensi.rekap-by-date', ['tanggal' => $tanggal, 'status' => 'tidak-hadir']) }}" 
                       class="btn btn-sm {{ request('status') == 'tidak-hadir' ? 'btn-danger' : 'btn-outline-secondary' }}">
                        Tidak Hadir
                    </a>
                </div>
            </div>
            
            <small class="text-muted">
                Total: <span class="fw-bold text-dark">{{ $absensis->total() }}</span> data
            </small>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable" id="absensi-detail-table">
                <thead class="table-light">
                    <tr>
                        <th class="px-3 py-3 text-center" style="width: 50px;">#</th>
                        <th class="px-3 py-3">Nama Siswa</th>
                        <th class="px-3 py-3">Sekolah</th>
                        <th class="px-3 py-3">Rombel</th>
                        <th class="px-3 py-3 text-center">Status</th>
                        <th class="px-3 py-3">Catatan</th>
                        <th class="px-3 py-3 text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensis as $index => $absen)
                    <tr>
                        <td class="px-3 text-center text-muted">{{ ($absensis->currentPage() - 1) * $absensis->perPage() + $loop->iteration }}</td>
                        <td class="px-3 fw-bold">{{ $absen->siswa->nama_lengkap ?? '-' }}</td>
                        <td class="px-3 text-muted small">{{ $absen->laporanMengajar->sekolah_nama ?? '-' }}</td>
                        <td class="px-3 text-muted small">{{ $absen->laporanMengajar->rombel ?? '-' }}</td>
                        <td class="px-3 text-center">
                            @if($absen->hadir == 1)
                                <span class="badge bg-success rounded-pill">Hadir</span>
                            @else
                                <span class="badge bg-danger rounded-pill">Tidak Hadir</span>
                            @endif
                        </td>
                        <td class="px-3 text-muted small">{{ $absen->catatan ?? '-' }}</td>
                        <td class="px-3 text-end">
                            <div class="btn-group btn-group-sm">
                                @can('update', $absen)
                                <a href="{{ route('absensi.edit', $absen->id) }}" class="btn btn-light text-primary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @endcan
                                @can('delete', $absen)
                                <form action="{{ route('absensi.destroy', $absen->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus absensi ini?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-state">
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            <p class="mb-0">Tidak ada data absensi yang ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($absensis->hasPages())
        <div class="card-footer bg-white py-3 border-top-0">
            {{ $absensis->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable for Attendance Detail table
        if (typeof window.DataTableManager !== 'undefined') {
            const table = document.getElementById('absensi-detail-table');
            const isEmpty = table ? table.querySelector('.empty-state') : null;

            if (table && !isEmpty) {
                const dataTableManager = new window.DataTableManager();
                dataTableManager.init('#absensi-detail-table', {
                    order: [[1, 'asc']], // Sort by Student Name column
                    columnDefs: [
                        { orderable: false, targets: [0, 6] }, // Disable sorting for # and Actions columns
                        { type: 'string', targets: [1, 2, 3, 4, 5] } // String sorting for other columns
                    ],
                    pageLength: 25,
                    language: {
                        search: "Cari:",
                        lengthMenu: "_MENU_ item per halaman",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ item",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Prev"
                        }
                    }
                });
            }
        }
    });
</script>
@endpush