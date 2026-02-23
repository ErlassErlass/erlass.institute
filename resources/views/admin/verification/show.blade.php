@extends('layouts.app')

@section('title', 'Detail Verifikasi Instruktur')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-person-badge-fill me-2 text-primary"></i>
                Detail Calon Instruktur
            </h2>
            <p class="text-muted mt-1 mb-0">Review lengkap data instruktur sebelum verifikasi</p>
        </div>
        <a href="{{ route('admin.verification.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(!$instructor || !$instructor->instructorProfile)
        <div class="alert alert-warning">
            Data profil instruktur tidak lengkap atau belum diisi sepenuhnya.
        </div>
    @else

    <div class="row">
        <!-- Main Profile Info -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i> Identitas Pribadi</h5>
            <!-- Profile Card -->
            <div class="card glass-card border-0 mb-4 position-relative overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-auto text-center">
                            @if($instructor->instructorProfile && $instructor->instructorProfile->foto)
                                <img src="{{ Storage::url($instructor->instructorProfile->foto) }}" 
                                     alt="Profile" 
                                     class="rounded-circle shadow-lg border border-3 border-white object-fit-cover"
                                     style="width: 140px; height: 140px;">
                            @else
                                <div class="bg-gradient-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center text-white border border-3 border-white mx-auto"
                                     style="width: 140px; height: 140px; font-size: 3.5rem; font-weight: bold;">
                                    {{ substr($instructor->nama_lengkap, 0, 1) }}
                                </div>
                            @endif
                            <div class="mt-3">
                                <span class="badge bg-light text-primary border border-primary-subtle rounded-pill">
                                    {{ $instructor->instructorProfile->agama ?? '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md">
                            <h2 class="fw-bold text-dark mb-1">{{ Str::upper($instructor->nama_lengkap) }}</h2>
                            <p class="text-muted mb-2"><i class="bi bi-envelope me-1"></i> {{ $instructor->email }}</p>
                            
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-light text-secondary border">
                                    <i class="bi bi-person-vcard me-1"></i> {{ $instructor->nik ?? 'NIK tidak tersedia' }}
                                </span>
                                <span class="badge bg-light text-secondary border">
                                    <i class="bi bi-whatsapp me-1"></i> {{ $instructor->no_telephone }}
                                </span>
                                <span class="badge bg-light text-secondary border">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $instructor->instructorProfile->kota_domisili ?? 'Kota tidak tersedia' }}
                                </span>
                            </div>

                            <div class="p-3 rounded-3 bg-light bg-opacity-50 border border-light text-muted small fst-italic">
                                <i class="bi bi-quote me-1 text-primary"></i>
                                {{ $instructor->instructorProfile->alamat_domisili ?? 'Alamat domisili belum diisi.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Information Tabs/Grid -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card glass-card border-0 h-100">
                        <div class="card-header bg-transparent border-bottom border-light py-3">
                            <h5 class="fw-bold mb-0 text-info"><i class="bi bi-mortarboard me-2"></i> Pendidikan</h5>
                        </div>
                        <div class="card-body p-4">
                             <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Pendidikan Terakhir</small>
                                    <span class="fs-6 fw-bold text-dark">{{ $instructor->pend_terakhir }}</span>
                                </li>
                                <li class="mb-3">
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Universitas & Jurusan</small>
                                    <span class="fs-6 text-dark">{{ $instructor->instructorProfile->universitas_jurusan ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Kompetensi Utama</small>
                                    <div class="d-flex gap-1 mt-1">
                                        <span class="badge bg-soft-primary text-primary">{{ $instructor->kompetensi_1 ?? '-' }}</span>
                                        @if($instructor->kompetensi_2)
                                            <span class="badge bg-soft-info text-info">{{ $instructor->kompetensi_2 }}</span>
                                        @endif
                                    </div>
                                </li>
                             </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card glass-card border-0 h-100">
                        <div class="card-header bg-transparent border-bottom border-light py-3">
                            <h5 class="fw-bold mb-0 text-success"><i class="bi bi-heart-pulse me-2"></i> Fisik & Kesehatan</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-2 border rounded-3 text-center bg-white">
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Tinggi/Berat</small>
                                        <span class="fw-bold text-dark">{{ $instructor->instructorProfile->tinggi_berat_badan ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded-3 text-center bg-white">
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Mata Minus</small>
                                        <span class="fw-bold text-dark">{{ $instructor->instructorProfile->mata_minus ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Riwayat Penyakit</small>
                                <span class="text-dark">{{ $instructor->instructorProfile->riwayat_penyakit ?? 'Tidak ada' }}</span>
                            </div>
                             <div class="mt-3">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Kendaraan</small>
                                <span class="text-dark">{{ $instructor->instructorProfile->kendaraan ?? '-' }} ({{ $instructor->instructorProfile->jenis_kendaraan ?? '-' }})</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                     <div class="card glass-card border-0">
                        <div class="card-header bg-transparent border-bottom border-light py-3">
                            <h5 class="fw-bold mb-0 text-secondary"><i class="bi bi-calendar-week me-2"></i> Jadwal Mengajar ({{ collect($instructor->waktu_mengajar)->flatten()->count() }} Sesi)</h5>
                        </div>
                        <div class="card-body p-4">
                            @if($instructor->waktu_mengajar)
                                <div class="table-responsive rounded-3 border">
                                    <table class="table table-bordered table-sm text-center mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                @foreach(array_keys($instructor->waktu_mengajar) as $hari)
                                                    <th class="text-uppercase small text-secondary py-2">{{ $hari }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                @foreach($instructor->waktu_mengajar as $hari => $jams)
                                                    <td class="align-top py-3">
                                                        <div class="d-flex flex-column gap-1">
                                                        @foreach($jams as $jam)
                                                            <span class="badge bg-soft-primary text-primary border border-primary-subtle fw-normal">{{ $jam }}</span>
                                                        @endforeach
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted fst-italic text-center mb-0">Tidak ada data jadwal tersedia.</p>
                            @endif
                        </div>
                     </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Documents & Actions -->
        <div class="col-lg-4">
            
            <!-- Documents Card -->
             <div class="card glass-card border-0 mb-4 sticky-top" style="top: 2rem; z-index: 10;">
                 <div class="card-header bg-transparent border-bottom border-light py-3">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-folder2-open me-2"></i> Dokumen Pendukung</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded-bottom-3">
                         @if($instructor->instructorProfile->cv)
                            <div class="list-group-item bg-transparent p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center overflow-hidden">
                                     <div class="bg-danger bg-opacity-10 p-2 rounded me-3 text-danger">
                                        <i class="bi bi-file-earmark-pdf fs-5"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <h6 class="mb-0 fw-bold text-dark">Curriculum Vitae</h6>
                                        <small class="text-muted">PDF Document</small>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($instructor->instructorProfile->cv) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle" data-bs-toggle="tooltip" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        @endif

                        @if($instructor->instructorProfile->foto_ktp)
                             <div class="list-group-item bg-transparent p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center overflow-hidden">
                                     <div class="bg-info bg-opacity-10 p-2 rounded me-3 text-info">
                                        <i class="bi bi-person-badge fs-5"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <h6 class="mb-0 fw-bold text-dark">Foto KTP</h6>
                                        <small class="text-muted">Identitas Diri</small>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($instructor->instructorProfile->foto_ktp) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle" data-bs-toggle="tooltip" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        @endif
                        
                        @if($instructor->instructorProfile->foto_npwp)
                             <div class="list-group-item bg-transparent p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center overflow-hidden">
                                     <div class="bg-warning bg-opacity-10 p-2 rounded me-3 text-warning">
                                        <i class="bi bi-card-heading fs-5"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <h6 class="mb-0 fw-bold text-dark">Foto NPWP</h6>
                                        <small class="text-muted">Dokumen Pajak</small>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($instructor->instructorProfile->foto_npwp) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle" data-bs-toggle="tooltip" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    @if(!$instructor->instructorProfile->cv && !$instructor->instructorProfile->foto_ktp)
                        <div class="p-4 text-center">
                            <p class="text-muted fst-italic mb-0">Tidak ada dokumen yang diunggah.</p>
                        </div>
                    @endif
                </div>
             </div>

             <!-- Action Card -->
             @if($instructor->status == 'pending')
             <div class="card glass-card border-0 border-top border-4 border-warning">
                 <div class="card-body p-4">
                     <h5 class="fw-bold mb-3">Tindakan Verifikasi</h5>
                     <p class="text-muted small mb-4">Pastikan semua data sudah valid sebelum menyetujui. Aksi ini tidak dapat dibatalkan dengan mudah.</p>
                     
                     <div class="d-grid gap-2">
                        <form action="{{ route('admin.verification.approve', $instructor) }}" method="POST" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-success py-2 rounded-pill shadow-sm fw-bold hover-scale" onclick="return confirm('Yakin menyetujui instruktur ini?')">
                                <i class="bi bi-check-circle-fill me-2"></i> Setujui Pendaftaran
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-danger py-2 rounded-pill fw-bold hover-scale" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-2"></i> Tolak Pendaftaran
                        </button>
                    @endif

                    @if($instructor->instructorProfile->foto_npwp)
                    <a href="{{ Storage::url($instructor->instructorProfile->foto_npwp) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-card-image text-success me-2"></i> Foto NPWP
                        </div>
                        <i class="bi bi-box-arrow-up-right small"></i>
                    </a>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Info Keuangan</h6>
                </div>
                <div class="card-body font-monospace small">
                    <div class="mb-2">
                        <div class="text-muted">Bank:</div>
                        <strong>{{ $instructor->instructorProfile->nama_bank }}</strong>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted">No Rekening:</div>
                        <strong>{{ $instructor->instructorProfile->no_rekening }}</strong>
                    </div>
                    <div>
                        <div class="text-muted">NPWP:</div>
                        <strong>{{ $instructor->instructorProfile->no_npwp ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Verifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.verification.reject', $instructor) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('rejection_reason') is-invalid @enderror" name="rejection_reason" rows="4" required minlength="10" placeholder="Jelaskan data apa yang kurang atau tidak sesuai (min. 10 karakter)..."></textarea>
                            @error('rejection_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
