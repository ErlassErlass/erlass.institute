@extends('layouts.app')

@section('title', 'Detail Laporan: Pertemuan Ke-' . $laporanMengajar->pertemuan_ke)

@push('styles')
{{-- Fancybox untuk galeri foto --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<style>
    .stat-card {
        border-left: 4px solid;
    }

    .border-primary {
        border-left-color: #4e73df !important;
    }

    .border-success {
        border-left-color: #1cc88a !important;
    }

    .border-info {
        border-left-color: #36b9cc !important;
    }

    .border-warning {
        border-left-color: #f6c23e !important;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                @if($isEkstrakurikuler ?? false)
                    <i class="bi bi-trophy-fill text-warning me-2"></i>Detail Laporan Ekstrakurikuler
                @else
                    Detail Laporan Mengajar
                @endif
            </h1>
            <p class="mb-0 text-muted">
                {{ $laporanMengajar->sekolah->namasekolah ?? 'Sekolah tidak ditemukan' }}
                @if($isEkstrakurikuler ?? false)
                    <span class="badge bg-warning text-dark ms-2">
                        <i class="bi bi-trophy me-1"></i>{{ $ekstrakurikulerData['nama_program'] ?? 'Ekstrakurikuler' }}
                    </span>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            @can('update', $laporanMengajar)
            <a href="{{ route('laporan-mengajar.edit', $laporanMengajar) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Edit Laporan
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pertemuan Ke-</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $laporanMengajar->pertemuan_ke }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-list-ol fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- ... (kartu statistik lainnya bisa ditambahkan di sini jika perlu) ... --}}
    </div>


    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Laporan</h6>
                </div>
                <div class="card-body">
                    @if($isEkstrakurikuler ?? false)
                        <!-- Informasi Khusus Ekstrakurikuler -->
                        <div class="alert alert-warning border-start border-4 border-warning mb-4">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-trophy-fill me-2 mt-1"></i>
                                <div>
                                    <h6 class="mb-2">Informasi Program Ekstrakurikuler</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Program:</small>
                                            <p class="mb-1"><strong>{{ $ekstrakurikulerData['nama_program'] ?? 'N/A' }}</strong></p>
                                        </div>
                                        @if($ekstrakurikulerSession)
                                            <div class="col-md-6">
                                                <small class="text-muted">Status Session:</small>
                                                <p class="mb-1">
                                                    <span class="badge bg-{{ $ekstrakurikulerSession->status === 'selesai' ? 'success' : 'warning' }}">
                                                        {{ $ekstrakurikulerSession->status_label }}
                                                    </span>
                                                </p>
                                            </div>
                                            @if($ekstrakurikulerSession->topik_materi)
                                                <div class="col-12">
                                                    <small class="text-muted">Topik Session:</small>
                                                    <p class="mb-1">{{ $ekstrakurikulerSession->topik_materi }}</p>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Instruktur Utama:</strong>
                            <p>{{ $laporanMengajar->instruktur->nama_lengkap ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Asisten Instruktur:</strong>
                            <p>{{ $laporanMengajar->asisten->nama_lengkap ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Jadwal Mengajar:</strong>
                            <p>{{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Jam:</strong>
                            <p>{{ \Carbon\Carbon::parse($laporanMengajar->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($laporanMengajar->jam_selesai)->format('H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Rombel:</strong>
                            <p>{{ $laporanMengajar->rombel }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Kategori:</strong>
                            <p>
                                @if($isEkstrakurikuler ?? false)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-trophy me-1"></i>{{ $laporanMengajar->kategori_pengajaran }}
                                    </span>
                                @else
                                    <span class="badge bg-primary">{{ $laporanMengajar->kategori_pengajaran }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <hr>
                    <strong>Materi Pengajaran:</strong>
                    <p>{!! nl2br(e($laporanMengajar->materi_pengajaran)) !!}</p>

                    @if(($isEkstrakurikuler ?? false) && $ekstrakurikulerSession && $ekstrakurikulerSession->catatan)
                        <hr>
                        <strong>Catatan Session:</strong>
                        <p class="text-muted">{!! nl2br(e($ekstrakurikulerSession->catatan)) !!}</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Refleksi & Evaluasi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Refleksi Siswa:</strong>
                            <p>{!! nl2br(e($laporanMengajar->refleksi_siswa)) !!}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Refleksi Capaian:</strong>
                            <p>{!! nl2br(e($laporanMengajar->refleksi_capaian)) !!}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Keaktifan Siswa:</strong>
                            <p class="text-capitalize">{{ str_replace('_', ' ', $laporanMengajar->keaktifan) }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Pemahaman Materi:</strong>
                            <p class="text-capitalize">{{ str_replace('_', ' ', $laporanMengajar->pemahaman_materi) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Rekap Absensi</h6>
                    @can('update', $laporanMengajar)
                    <a href="{{ route('laporan-mengajar.absensi.create', $laporanMengajar) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Kelola Absensi
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Siswa Hadir
                            <span class="badge bg-success rounded-pill">{{ $laporanMengajar->jumlah_siswa_hadir }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tidak Hadir
                            <span class="badge bg-danger rounded-pill">{{ $laporanMengajar->jumlah_siswa_tidak_hadir ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Siswa Keluar
                            <span class="badge bg-secondary rounded-pill">{{ $laporanMengajar->jumlah_siswa_keluar }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dokumentasi</h6>
                </div>
                <div class="card-body">
                    @if($laporanMengajar->foto_kegiatan)
                    <p class="mb-2"><strong>Foto Kegiatan:</strong></p>
                    <a href="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" data-fancybox="gallery">
                        <img src="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" class="img-fluid rounded" alt="Foto Kegiatan">
                    </a>
                    <hr>
                    @endif

                    @if($laporanMengajar->foto_absensi_siswa)
                    <p class="mb-2"><strong>Foto Absensi Siswa:</strong></p>
                    <a href="{{ asset('storage/' . $laporanMengajar->foto_absensi_siswa) }}" data-fancybox="gallery">
                        <img src="{{ asset('storage/' . $laporanMengajar->foto_absensi_siswa) }}" class="img-fluid rounded" alt="Foto Absensi">
                    </a>
                    @else
                    <p class="text-muted text-center">Tidak ada dokumentasi.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Fancybox untuk galeri foto --}}
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    Fancybox.bind("[data-fancybox]", {
        // Opsi custom jika diperlukan
    });
</script>
@endpush