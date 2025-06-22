@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h1 class="mb-0">
                <i class="fas fa-book-open me-2"></i>Detail Laporan Mengajar
                <span class="badge bg-primary ms-2">Pertemuan Ke-{{ $laporanMengajar->pertemuan_ke }}</span>
            </h1>
            <div>
                <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                @can('update', $laporanMengajar)
                <a href="{{ route('laporan-mengajar.edit', $laporanMengajar->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Instruktur</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-lg bg-light-primary rounded-circle me-3">
                            <i class="fas fa-user-tie fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $laporanMengajar->instruktur->nama_lengkap ?? 'Tidak ada' }}</h6>
                            <small class="text-muted">Instruktur Utama</small>
                        </div>
                    </div>
                    
                    @if ($laporanMengajar->user_id_assisten)
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-light-success rounded-circle me-3">
                            <i class="fas fa-user-graduate fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $laporanMengajar->assisten->nama_lengkap ?? 'Tidak ada' }}</h6>
                            <small class="text-muted">Asisten Instruktur</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-school me-2"></i>Sekolah</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-2">{{ $laporanMengajar->sekolah_nama }}</h6>
                    <div class="text-muted small">
                        <div><i class="fas fa-map-marker-alt me-2"></i> {{ $laporanMengajar->sekolah_kecamatan }}, {{ $laporanMengajar->sekolah_kota }}</div>
                        <div><i class="fas fa-map-marked-alt me-2"></i> {{ $laporanMengajar->sekolah_provinsi }}</div>
                        <div><i class="fas fa-users me-2"></i> Rombel: {{ $laporanMengajar->rombel }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Jadwal</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tanggal:</span>
                        <strong>{{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->translatedFormat('l, d F Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Jam:</span>
                        <strong>{{ $laporanMengajar->jam_mulai }} - {{ $laporanMengajar->jam_selesai }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Durasi:</span>
                        <strong>
                            @php
                                $start = \Carbon\Carbon::parse($laporanMengajar->jam_mulai);
                                $end = \Carbon\Carbon::parse($laporanMengajar->jam_selesai);
                                echo $start->diff($end)->format('%h jam %i menit');
                            @endphp
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Kategori:</span>
                        <span class="badge bg-{{ $laporanMengajar->kategori_pengajaran == 'Reguler' ? 'primary' : ($laporanMengajar->kategori_pengajaran == 'Remedial' ? 'warning' : 'info') }}">
                            {{ $laporanMengajar->kategori_pengajaran }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Materi Pengajaran -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Materi Pengajaran</h5>
                </div>
                <div class="card-body">
                    <div class="content-box p-3 bg-light rounded">
                        {!! nl2br(e($laporanMengajar->materi_pengajaran)) !!}
                    </div>
                </div>
            </div>
            
            <!-- Refleksi -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-purple text-white">
                    <h5 class="mb-0"><i class="fas fa-brain me-2"></i>Refleksi Pembelajaran</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-user-graduate me-2"></i>Refleksi Siswa</h6>
                            <div class="content-box p-3 bg-light rounded">
                                {!! nl2br(e($laporanMengajar->refleksi_siswa)) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-bullseye me-2"></i>Refleksi Capaian</h6>
                            <div class="content-box p-3 bg-light rounded">
                                {!! nl2br(e($laporanMengajar->refleksi_capaian)) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6><i class="fas fa-fire me-2"></i>Tingkat Keaktifan</h6>
                            @php
                                $keaktifanLevels = [
                                    'sangat_pasif' => ['text' => 'Sangat Pasif', 'color' => 'danger', 'width' => '25%'],
                                    'pasif' => ['text' => 'Pasif', 'color' => 'warning', 'width' => '50%'],
                                    'aktif' => ['text' => 'Aktif', 'color' => 'info', 'width' => '75%'],
                                    'sangat_aktif' => ['text' => 'Sangat Aktif', 'color' => 'success', 'width' => '100%']
                                ];
                                $keaktifan = $keaktifanLevels[$laporanMengajar->keaktifan];
                            @endphp
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar bg-{{ $keaktifan['color'] }}" role="progressbar" 
                                    style="width: {{ $keaktifan['width'] }}" 
                                    aria-valuenow="{{ $keaktifan['width'] }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ $keaktifan['text'] }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6><i class="fas fa-lightbulb me-2"></i>Pemahaman Materi</h6>
                            @php
                                $pemahamanLevels = [
                                    'belum_paham' => ['text' => 'Belum Paham', 'color' => 'danger', 'width' => '25%'],
                                    'sedikit_paham' => ['text' => 'Sedikit Paham', 'color' => 'warning', 'width' => '50%'],
                                    'paham' => ['text' => 'Paham', 'color' => 'info', 'width' => '75%'],
                                    'sangat_paham' => ['text' => 'Sangat Paham', 'color' => 'success', 'width' => '100%']
                                ];
                                $pemahaman = $pemahamanLevels[$laporanMengajar->pemahaman_materi];
                            @endphp
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar bg-{{ $pemahaman['color'] }}" role="progressbar" 
                                    style="width: {{ $pemahaman['width'] }}" 
                                    aria-valuenow="{{ $pemahaman['width'] }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ $pemahaman['text'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
<!-- Attendance Stats -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-indigo text-white">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Kehadiran Siswa</h5>
    </div>
    <div class="card-body">
@if($jumlah_hadir + $jumlah_tidak_hadir + $jumlah_keluar > 0)
    <div class="row text-center">
        <div class="col-4">
            <div class="stat-card bg-light-success p-3 rounded">
                <h3 class="text-success">{{ $jumlah_hadir }}</h3>
                <small class="text-muted">Hadir</small>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card bg-light-danger p-3 rounded">
                <h3 class="text-danger">{{ $jumlah_tidak_hadir }}</h3>
                <small class="text-muted">Tidak Hadir</small>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card bg-light-info p-3 rounded">
                <h3 class="text-info">{{ $jumlah_keluar }}</h3>
                <small class="text-muted">Keluar</small>
            </div>
        </div>
    </div>
@else
    <div class="empty-state py-4">
        <i class="fas fa-clipboard-list fa-2x text-muted mb-3"></i>
        <p class="text-muted mb-3">Data absensi belum direkam</p>
        @can('create', [App\Models\Absensi::class, $laporanMengajar])
            <a href="{{ route('laporan-mengajar.absensi.create', $laporanMengajar) }}" 
               class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Rekam Absensi
            </a>
        @endcan
    </div>
@endif
<div class="mt-3 text-center">
    <a href="{{ route('laporan-mengajar.absensi.index', $laporanMengajar) }}" 
       class="btn btn-outline-dark btn-sm">
        <i class="fas fa-list me-1"></i> Lihat Data Absensi
    </a>
</div>
            
            <!-- Photo Evidence -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-teal text-white">
                    <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Dokumentasi Kegiatan</h5>
                </div>
                <div class="card-body text-center">
                    @if ($laporanMengajar->foto_kegiatan)
                        <a href="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" data-fancybox="gallery">
                            <img src="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" 
                                alt="Foto Kegiatan" 
                                class="img-fluid rounded mb-2"
                                style="max-height: 250px; object-fit: cover;">
                        </a>
                        <div class="text-center">
                            <a href="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" 
                               download 
                               class="btn btn-sm btn-outline-primary">
                               <i class="fas fa-download me-1"></i>Unduh Foto
                            </a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada foto kegiatan</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Additional Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Aksi Tambahan</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fas fa-print me-2"></i>Cetak Laporan
                        </button>
                        <button class="btn btn-outline-info btn-sm text-start">
                            <i class="fas fa-file-export me-2"></i>Ekspor ke PDF
                        </button>
                        @can('delete', $laporanMengajar)
                        <form action="{{ route('laporan-mengajar.destroy', $laporanMengajar->id) }}" method="POST" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm text-start" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                <i class="fas fa-trash-alt me-2"></i>Hapus Laporan
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Image Preview -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dokumentasi Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="">
            </div>
            <div class="modal-footer">
                <a href="#" id="downloadImage" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i>Unduh
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-purple {
        background-color: #6f42c1;
    }
    .bg-indigo {
        background-color: #6610f2;
    }
    .bg-teal {
        background-color: #20c997;
    }
    .content-box {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
    }
    .empty-state {
        padding: 2rem;
        text-align: center;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">

<script>
    // Initialize fancybox for image preview
    Fancybox.bind("[data-fancybox]", {
        // Your custom options
    });
    
    // Function to show image in modal
    function showImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('downloadImage').href = src;
        var modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }
</script>
@endpush