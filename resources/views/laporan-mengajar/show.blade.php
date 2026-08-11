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
                        <i class="bi bi-trophy me-1"></i>{{ $ekstrakurikulerData['kategori_program'] ?? $ekstrakurikulerData['nama_program'] ?? 'Ekstrakurikuler' }}
                    </span>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            @if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']) && $isEkstrakurikuler)
            <button type="button" class="btn btn-warning fw-bold text-dark shadow-sm ms-1" data-bs-toggle="modal" data-bs-target="#relocateReportModal">
                <i class="bi bi-arrow-left-right me-1"></i> Pindahkan Pertemuan
            </button>
            @endif
            @can('update', $laporanMengajar)
            <a href="{{ route('laporan-mengajar.edit', $laporanMengajar) }}" class="btn btn-primary ms-1">
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
                                            <p class="mb-1"><strong>{{ $ekstrakurikulerData['kategori_program'] ?? $ekstrakurikulerData['nama_program'] ?? 'N/A' }}</strong></p>
                                        </div>
                                        @if($ekstrakurikulerSession)
                                            <div class="col-md-6">
                                                <small class="text-muted">Status Session:</small>
                                                <p class="mb-1">
                                                    <span class="badge bg-{{ $ekstrakurikulerSession->status === 'selesai' ? 'success' : 'warning' }}">
                                                        {{ $ekstrakurikulerSession->status_label }}
                                                    </span>
                                                    <a href="{{ route('ekstrakurikuler.sessions.show', $ekstrakurikulerSession) }}" class="btn btn-sm btn-link p-0 ms-2 text-decoration-none">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Detail
                                                    </a>
                                                </p>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                            <div class="col-md-6 mt-2">
                                                <small class="text-muted">Kehadiran Sekolah (Check-in):</small>
                                                <p class="mb-1">
                                                    @php
                                                        $checkinStatus = $ekstrakurikulerSession->actual_checkin_status ?? 'on_time';
                                                        $badgeCheckin = match($checkinStatus) {
                                                            'excellent' => 'success',
                                                            'on_time' => 'primary',
                                                            'warning' => 'warning',
                                                            'penalty' => 'danger',
                                                            default => 'secondary',
                                                        };
                                                        $checkinLabel = match($checkinStatus) {
                                                            'excellent' => 'Excellent (Datang Cepat)',
                                                            'on_time' => 'On Time (Datang Tepat Waktu)',
                                                            'warning' => 'Warning (Terlambat <15 Mnt)',
                                                            'penalty' => 'Penalty (Terlambat >15 Mnt)',
                                                            default => 'Belum Check-in / N/A',
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $badgeCheckin }}">
                                                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $checkinLabel }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <small class="text-muted">Ketepatan Submit Laporan (H+1):</small>
                                                <p class="mb-1">
                                                    @php
                                                        $tglJadwal = \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->startOfDay();
                                                        $tglSubmit = $laporanMengajar->created_at ? $laporanMengajar->created_at->startOfDay() : now()->startOfDay();
                                                        $selisihHari = (int) $tglJadwal->diffInDays($tglSubmit, false);
                                                    @endphp
                                                    @if($selisihHari <= 1)
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Tepat Waktu (H+{{ max(0, $selisihHari) }})
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Terlambat (H+{{ $selisihHari }})
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <small class="text-muted">Jarak & Transportasi:</small>
                                                <p class="mb-1">
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $ekstrakurikulerSession->ekstrakurikuler->jarak_km ?? '0' }} Km
                                                    </span>
                                                    <small class="text-muted d-block mt-1">Uang Transport: Rp {{ number_format($ekstrakurikulerSession->transport_fee ?? 30000, 0, ',', '.') }}</small>
                                                </p>
                                            </div>
                                            @if($ekstrakurikulerSession->topik_materi)
                                                <div class="col-12 mt-2">
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
                            @php
                                $jMulai = \Carbon\Carbon::parse($laporanMengajar->jam_mulai);
                                $jSelesai = \Carbon\Carbon::parse($laporanMengajar->jam_selesai);
                                if ($jSelesai->lessThanOrEqualTo($jMulai)) {
                                    $jSelesai = (clone $jMulai)->addMinutes(90);
                                }
                            @endphp
                            <p>{{ $jMulai->format('H:i') }} - {{ $jSelesai->format('H:i') }} WIB</p>
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
                        <div class="col-md-6">
                            <strong>Waktu Input Sistem (Timestamp):</strong>
                            <p class="text-primary fw-semibold mb-0">
                                <i class="bi bi-cloud-arrow-up me-1"></i>{{ $laporanMengajar->created_at ? $laporanMengajar->created_at->isoFormat('dddd, D MMMM Y [jam] HH:mm:ss') : '-' }}
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
                    <div class="d-flex">
                        @if(($isEkstrakurikuler ?? false) && $ekstrakurikulerSession)
                        <a href="{{ route('ekstrakurikuler-session.print-session', $ekstrakurikulerSession) }}" target="_blank" class="btn btn-sm btn-outline-success me-2">
                            <i class="bi bi-printer me-1"></i> Print
                        </a>
                        @endif
                        @can('update', $laporanMengajar)
                        <a href="{{ route('laporan-mengajar.absensi.create', $laporanMengajar) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Kelola Absensi
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Siswa Hadir
                            <span class="badge bg-success rounded-pill">{{ $laporanMengajar->jumlah_hadir }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tidak Hadir
                            <span class="badge bg-danger rounded-pill">{{ $laporanMengajar->jumlah_tidak_hadir }}</span>
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
                    @if($laporanMengajar->file_project)
                        <div class="mb-3">
                            <p class="mb-2"><strong>File Project:</strong></p>
                            <a href="{{ asset('storage/' . $laporanMengajar->file_project) }}" class="btn btn-outline-primary btn-sm" download>
                                <i class="bi bi-download me-1"></i> Download Project (.sb3/.zip)
                            </a>
                        </div>
                        <hr>
                    @endif

                    @if($laporanMengajar->foto_kegiatan)
                    <p class="mb-2"><strong>Foto Kegiatan:</strong></p>
                    <a href="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" data-fancybox="gallery">
                        <img src="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" class="img-fluid rounded" alt="Foto Kegiatan" loading="lazy">
                    </a>
                    <hr>
                    @endif

                    @if($laporanMengajar->foto_absensi_siswa)
                    <p class="mb-2">
                        <strong>Foto Absensi Siswa:</strong>
                        <br>
                        <small class="text-danger"><i class="bi bi-info-circle me-1"></i>Pastikan foto memuat TTD PIC Ekskul & Instruktur</small>
                    </p>
                    <a href="{{ asset('storage/' . $laporanMengajar->foto_absensi_siswa) }}" data-fancybox="gallery">
                        <img src="{{ asset('storage/' . $laporanMengajar->foto_absensi_siswa) }}" class="img-fluid rounded" alt="Foto Absensi" loading="lazy">
                    </a>
                    @else
                    <p class="text-muted text-center">Tidak ada dokumentasi foto.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']) && $isEkstrakurikuler)
<div class="modal fade" id="relocateReportModal" tabindex="-1" aria-labelledby="relocateReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning bg-opacity-10 border-bottom border-warning border-opacity-25 rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center me-2" id="relocateReportModalLabel">
                    <i class="bi bi-arrow-left-right text-warning fs-4 me-2"></i> Relokasi Laporan Mengajar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('laporan-mengajar.relocate', $laporanMengajar) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 rounded-3 mb-3 text-dark small">
                        <i class="bi bi-info-circle-fill me-1 text-info"></i>
                        Laporan ini saat ini berada di <strong>Pertemuan Ke-{{ $laporanMengajar->pertemuan_ke }}</strong>. Memindahkan laporan akan meng-update status sesi target menjadi <strong>Selesai</strong> dan mengosongkan sesi asal.
                    </div>

                    <div class="mb-3">
                        <label for="target_session_id" class="form-label fw-semibold text-dark small">Pilih Pertemuan Target <span class="text-danger">*</span></label>
                        <select name="target_session_id" id="target_session_id" class="form-select rounded-3" required>
                            <option value="" disabled selected>-- Pilih Pertemuan --</option>
                            @foreach($availableSessions as $sess)
                                <option value="{{ $sess->id }}" {{ $sess->id == $laporanMengajar->ekstrakurikuler_session_id ? 'disabled' : '' }}>
                                    Pertemuan {{ $sess->nomor_pertemuan }} ({{ $sess->tanggal_terjadwal ? $sess->tanggal_terjadwal->format('d/m/Y') : '-' }}) 
                                    - Status: {{ ucfirst($sess->status) }}
                                    {{ $sess->id == $laporanMengajar->ekstrakurikuler_session_id ? ' [Sesi Saat Ini]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label for="alasan_relokasi" class="form-label fw-semibold text-dark small">Alasan Pemindahan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <textarea name="alasan_relokasi" id="alasan_relokasi" rows="2" class="form-control rounded-3" placeholder="Contoh: Instruktur salah mendelegasikan saat input laporan"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2 px-4 border-top">
                    <button type="button" class="btn btn-light rounded-3 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-3 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Konfirmasi Pindahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
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