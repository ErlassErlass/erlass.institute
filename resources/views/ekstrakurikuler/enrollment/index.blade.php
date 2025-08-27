@extends('layouts.app')

@section('title', 'Manajemen Siswa - ' . $ekstrakurikuler->kategori_program)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-people-fill me-2"></i>Manajemen Siswa
            </h1>
            <p class="mb-0 text-muted">{{ $ekstrakurikuler->kategori_program }} - {{ $ekstrakurikuler->sekolah->namasekolah ?? 'N/A' }}</p>
        </div>
        <div>
            <a href="{{ route('ekstrakurikuler.show', $ekstrakurikuler) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Program
            </a>
            @can('update', $ekstrakurikuler)
                <a href="{{ route('ekstrakurikuler.enrollment.create', $ekstrakurikuler) }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Daftarkan Siswa
                </a>
            @endcan
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Siswa</h6>
                            <h3 class="mb-0">{{ $enrollments->total() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Siswa Aktif</h6>
                            <h3 class="mb-0">{{ $enrollments->where('status', 'aktif')->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Lulus</h6>
                            <h3 class="mb-0">{{ $enrollments->where('status', 'lulus')->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-trophy fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Keluar</h6>
                            <h3 class="mb-0">{{ $enrollments->where('status', 'keluar')->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-x fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ekstrakurikuler.enrollment.index', $ekstrakurikuler) }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="keluar" {{ request('status') === 'keluar' ? 'selected' : '' }}>Keluar</option>
                            <option value="pindah" {{ request('status') === 'pindah' ? 'selected' : '' }}>Pindah</option>
                            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="rombel_id" class="form-label">Rombel</label>
                        <select class="form-select" id="rombel_id" name="rombel_id">
                            <option value="">Semua Rombel</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                                    {{ $rombel->nama_rombel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Siswa</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nama atau NISN siswa">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Enrollment List -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Daftar Siswa Terdaftar</h6>
                @if($enrollments->isNotEmpty())
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i> Aksi Bulk
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="showBulkModal('activate')">
                                <i class="bi bi-person-check me-1"></i> Aktifkan
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="showBulkModal('deactivate')">
                                <i class="bi bi-person-dash me-1"></i> Non-aktifkan
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="showBulkModal('graduate')">
                                <i class="bi bi-trophy me-1"></i> Luluskan
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="showBulkModal('delete')">
                                <i class="bi bi-trash me-1"></i> Hapus
                            </a></li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($enrollments->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Siswa</th>
                                <th>NISN</th>
                                <th>Rombel</th>
                                <th>Status</th>
                                <th>Tgl Daftar</th>
                                <th>Durasi</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input enrollment-checkbox" 
                                               value="{{ $enrollment->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <span class="text-white small">{{ substr($enrollment->siswa->nama_lengkap, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $enrollment->siswa->nama_lengkap }}</h6>
                                                @if($enrollment->catatan)
                                                    <small class="text-muted">{{ Str::limit($enrollment->catatan, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $enrollment->siswa->nisn ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $enrollment->rombel->nama_rombel }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'aktif' ? 'success' : ($enrollment->status === 'lulus' ? 'info' : 'secondary') }}">
                                            {{ $enrollment->status_label }}
                                        </span>
                                    </td>
                                    <td>{{ $enrollment->tanggal_daftar->format('d/m/Y') }}</td>
                                    <td>{{ $enrollment->durasi_enrollment }} hari</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('ekstrakurikuler.enrollment.show', [$ekstrakurikuler, $enrollment]) }}" 
                                               class="btn btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @can('update', $ekstrakurikuler)
                                                <a href="{{ route('ekstrakurikuler.enrollment.edit', [$ekstrakurikuler, $enrollment]) }}" 
                                                   class="btn btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <h5 class="text-muted mt-2">Belum ada siswa terdaftar</h5>
                    <p class="text-muted">Klik tombol "Daftarkan Siswa" untuk menambahkan siswa ke program ini.</p>
                </div>
            @endif
        </div>
        @if($enrollments->hasPages())
            <div class="card-footer">
                {{ $enrollments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bulkActionForm" method="POST" action="{{ route('ekstrakurikuler.enrollment.bulk-action', $ekstrakurikuler) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionTitle">Aksi Bulk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="bulkAction">
                    <input type="hidden" name="enrollment_ids" id="enrollmentIds">
                    
                    <div id="bulkActionContent"></div>
                    
                    <div class="form-group mt-3" id="bulkReasonGroup" style="display: none;">
                        <label for="bulk_alasan" class="form-label">Alasan</label>
                        <textarea class="form-control" id="bulk_alasan" name="bulk_alasan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="bulkActionBtn">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const enrollmentCheckboxes = document.querySelectorAll('.enrollment-checkbox');
    
    selectAllCheckbox?.addEventListener('change', function() {
        enrollmentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    enrollmentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.enrollment-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === enrollmentCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < enrollmentCheckboxes.length;
        });
    });
});

function showBulkModal(action) {
    const checkedBoxes = document.querySelectorAll('.enrollment-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('Pilih minimal satu siswa untuk diproses.');
        return;
    }
    
    const enrollmentIds = Array.from(checkedBoxes).map(cb => cb.value);
    const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
    
    document.getElementById('bulkAction').value = action;
    document.getElementById('enrollmentIds').value = enrollmentIds.join(',');
    
    const titles = {
        'activate': 'Aktifkan Siswa',
        'deactivate': 'Non-aktifkan Siswa', 
        'graduate': 'Luluskan Siswa',
        'delete': 'Hapus Enrollment'
    };
    
    const contents = {
        'activate': `<p>Yakin ingin mengaktifkan <strong>${checkedBoxes.length}</strong> siswa yang dipilih?</p>`,
        'deactivate': `<p>Yakin ingin menonaktifkan <strong>${checkedBoxes.length}</strong> siswa yang dipilih?</p>`,
        'graduate': `<p>Yakin ingin meluluskan <strong>${checkedBoxes.length}</strong> siswa yang dipilih?</p>`,
        'delete': `<p class="text-danger">Yakin ingin menghapus enrollment <strong>${checkedBoxes.length}</strong> siswa yang dipilih? <br><small>Tindakan ini tidak dapat dibatalkan.</small></p>`
    };
    
    document.getElementById('bulkActionTitle').textContent = titles[action];
    document.getElementById('bulkActionContent').innerHTML = contents[action];
    document.getElementById('bulkReasonGroup').style.display = action === 'deactivate' ? 'block' : 'none';
    document.getElementById('bulkActionBtn').className = `btn ${action === 'delete' ? 'btn-danger' : 'btn-primary'}`;
    
    modal.show();
}
</script>
@endpush

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
@endsection