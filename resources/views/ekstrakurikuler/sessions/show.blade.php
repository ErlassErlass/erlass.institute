@extends('layouts.app')

@section('title', 'Detail Sesi Ekstrakurikuler')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('ekstrakurikuler.sessions.index', request()->query() ?: session('ekstrakurikuler_sessions_filters', [])) }}" class="text-decoration-none">
                    <i class="bi bi-calendar-event me-1"></i>Sessions
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Pertemuan {{ $session->nomor_pertemuan }}</li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h2 class="h3 fw-bold text-primary mb-1">
                        {{ $session->rombel->ekstrakurikuler->kategori_program }} - Pertemuan {{ $session->nomor_pertemuan }}
                    </h2>
                    <p class="text-muted mb-0 fs-5">{{ $session->rombel->nama_rombel }}</p>
                </div>
                
                <div class="d-grid gap-2 d-sm-flex flex-sm-wrap">
                    <a href="{{ route('ekstrakurikuler-session.print-session', $session) }}" 
                       class="btn btn-outline-dark border w-100 w-sm-auto" target="_blank">
                        <i class="bi bi-printer me-1"></i> Cetak Presensi
                    </a>

                    @if(Auth::user()->role === 'instruktur' && in_array($session->status, ['terjadwal', 'berlangsung']))
                        <button type="button" class="btn btn-success w-100 w-sm-auto shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#gpsCheckinModal">
                            <i class="bi bi-geo-alt-fill me-1"></i> 📌 Check-in Hadir (GPS & Camera)
                        </button>
                    @endif

                    @if($session->canComplete())
                        <a href="{{ route('ekstrakurikuler.sessions.report.create', $session) }}" 
                           class="btn btn-primary w-100 w-sm-auto">
                            <i class="bi bi-file-earmark-check me-1"></i> Buat Laporan & Absensi
                        </a>
                    @endif
                    
                    @can('update', $session)
                        @if(in_array($session->status, ['terjadwal', 'ditunda']))
                            <a href="{{ route('ekstrakurikuler.sessions.edit', array_merge(['session' => $session->id], request()->query())) }}" 
                               class="btn btn-outline-primary w-100 w-sm-auto">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                        @endif

                        @if($session->status === 'selesai' && $session->laporan_mengajar_id)
                            <form action="{{ route('ekstrakurikuler.sessions.progress-remind', $session) }}" method="POST" class="d-grid d-sm-inline">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 w-sm-auto" onclick="return confirm('Apakah Anda yakin ingin mengirim ulang Pesan Pengingat Progress ke WhatsApp Orang Tua untuk siswa yang sudah menyelesaikan minimal 2 sesi berjalan?');">
                                    <i class="bi bi-whatsapp me-1"></i> Bagikan Progress Reminder
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Widget Laporan Sebelumnya (Catch-Up Materi Instruktur Pengganti) -->
    @if(isset($previousReport) && $previousReport)
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-primary px-3 py-2 rounded-pill fs-7 fw-bold">
                        <i class="bi bi-journal-bookmark-fill me-1"></i> Laporan Sebelumnya
                    </span>
                    <span class="small text-dark fw-semibold">
                        Pertemuan Ke-{{ $previousReport->pertemuan_ke ?? '-' }} &bull; {{ \Carbon\Carbon::parse($previousReport->jadwal_mengajar)->translatedFormat('d F Y') }}
                    </span>
                </div>
                @if($previousReport->instruktur)
                    <small class="text-secondary fw-semibold">
                        <i class="bi bi-person-badge me-1"></i> {{ $previousReport->instruktur->nama_lengkap }}
                    </small>
                @endif
            </div>
            
            <div class="row g-2 mt-1">
                <div class="col-md-6">
                    <div class="small fw-semibold text-dark"><i class="bi bi-book me-1 text-primary"></i> Materi / Topik Sebelumnya:</div>
                    <div class="small text-secondary fw-medium">{{ $previousReport->materi_pengajaran ?? $previousReport->topik_materi ?? '-' }}</div>
                </div>
                @if(!empty($previousReport->deskripsi_kegiatan))
                <div class="col-md-6">
                    <div class="small fw-semibold text-dark"><i class="bi bi-card-text me-1 text-primary"></i> Ringkasan Kegiatan:</div>
                    <div class="small text-secondary">{{ $previousReport->deskripsi_kegiatan }}</div>
                </div>
                @endif
                @if(!empty($previousReport->catatan))
                <div class="col-12 mt-2 pt-2 border-top border-primary border-opacity-10">
                    <div class="small fw-semibold text-dark"><i class="bi bi-chat-left-text me-1 text-primary"></i> Catatan Instruktur Sebelumnya:</div>
                    <div class="small text-dark-50 fst-italic">{{ $previousReport->catatan }}</div>
                </div>
                @endif
                @if(!empty($previousReport->file_project))
                <div class="col-12 mt-2 pt-2 border-top border-primary border-opacity-10 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="small fw-semibold text-dark"><i class="bi bi-file-earmark-code me-1 text-primary"></i> File Project Sebelumnya:</div>
                    <a href="{{ asset('storage/' . $previousReport->file_project) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-bold" download target="_blank">
                        <i class="bi bi-download me-1"></i> Download File Project
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    
    @if(Auth::user()->role === "instruktur" && $session->canComplete())
        @php
            $scheduleDate = $session->tanggal_terjadwal; 
            $deadline = $scheduleDate->copy()->addDay()->endOfDay();
            $isLocked = now()->greaterThan($deadline);
            $hasApprovedRequest = $session->lateReportRequests()
                ->where("user_id", Auth::id())
                ->where("status", "approved")
                ->exists();
            $pendingRequest = $session->lateReportRequests()
                ->where("user_id", Auth::id())
                ->where("status", "pending")
                ->first();
        @endphp

        @if($isLocked && !$hasApprovedRequest)
            <div class="card shadow-sm mb-4 border-start border-4 border-warning">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-warning bg-opacity-10 p-2 rounded text-warning">
                            <i class="bi bi-clock-history fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold text-dark mb-2">Batas Waktu Pelaporan Habis</h5>
                            @if($pendingRequest)
                                <div class="alert alert-info border-0 shadow-sm mb-0">
                                    <i class="bi bi-hourglass-split me-2"></i> Permohonan buka akses sedang menunggu persetujuan Admin.
                                </div>
                            @elseif(Auth::user()->monthly_late_report_quota > 0)
                                <p class="text-muted small mb-3">
                                    Sesi ini sudah melewati batas H+1. Silakan tuliskan alasan keterlambatan di bawah ini untuk mengajukan permohonan buka akses laporan ke Admin.
                                </p>
                                <form action="{{ route('sessions.late-report-request.store', $session) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Alasan Keterlambatan</label>
                                        <textarea name="reason" class="form-control form-control-sm" rows="2" required placeholder="Contoh: Terkendala sinyal, sakit, atau alasan lainnya..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning btn-sm fw-bold">
                                        <i class="bi bi-send me-1"></i> Kirim Permintaan Buka Akses
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-danger border-0 shadow-sm mb-0">
                                    <i class="bi bi-exclamation-octagon-fill me-2"></i> Kuota bantuan bulanan Anda sudah habis. Silakan hubungi Admin secara manual untuk bantuan.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Session Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-secondary"><i class="bi bi-info-circle me-2"></i>Informasi Sesi</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Status</label>
                            <div class="mt-1">
                                @php
                                    $statusClass = match($session->status) {
                                        'terjadwal' => 'primary',
                                        'berlangsung' => 'warning',
                                        'selesai' => 'success',
                                        'dibatalkan' => 'danger',
                                        'ditunda' => 'secondary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }} fs-6 px-3 py-2 rounded-pill">
                                    {{ $session->status_label }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Pertemuan Ke</label>
                            <p class="fs-5 mb-0 fw-medium">{{ $session->nomor_pertemuan }}</p>
                        </div>
                        
                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Tanggal Terjadwal</label>
                            <p class="mb-0 fw-medium">
                                <i class="bi bi-calendar me-1 text-primary"></i>
                                {{ $session->tanggal_terjadwal->format('d/m/Y') }}
                            </p>
                        </div>
                        
                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Waktu Terjadwal</label>
                            <p class="mb-0 fw-medium">
                                <i class="bi bi-clock me-1 text-primary"></i>
                                {{ $session->jadwal_waktu }}
                                <span class="badge text-bg-light border ms-2">{{ $session->durasi_terjadwal }} menit</span>
                            </p>
                        </div>
                        
                        @if($session->tanggal_pelaksanaan)
                            <div class="col-6 col-md-6">
                                <label class="small text-muted text-uppercase fw-bold">Tanggal Pelaksanaan</label>
                                <p class="mb-0 fw-medium">
                                    {{ \Carbon\Carbon::parse($session->tanggal_pelaksanaan)->format('d/m/Y') }}
                                </p>
                            </div>
                        @endif
                        
                        @if($session->waktu_aktual)
                            <div class="col-6 col-md-6">
                                <label class="small text-muted text-uppercase fw-bold">Waktu Aktual</label>
                                <p class="mb-0 fw-medium">
                                    {{ $session->waktu_aktual }}
                                    @if($session->durasi_aktual)
                                        <span class="badge text-bg-light border ms-2">{{ $session->durasi_aktual }} menit</span>
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if($session->checkin_lat)
                            <div class="col-12 mt-3 pt-3 border-top">
                                <label class="small text-muted text-uppercase fw-bold d-block mb-1">Verifikasi GPS Check-in (Scenario A)</label>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if($session->checkin_status_radius === 'valid')
                                        <span class="badge bg-success fs-7 px-3 py-1.5 rounded-pill">
                                            <i class="bi bi-shield-check me-1"></i>Terverifikasi di Sekolah (Jarak: {{ $session->checkin_distance_meters }}m)
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark fs-7 px-3 py-1.5 rounded-pill">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Diluar Radius (Jarak: {{ $session->checkin_distance_meters }}m dari Sekolah)
                                        </span>
                                    @endif

                                    <a href="https://maps.google.com/?q={{ $session->checkin_lat }},{{ $session->checkin_lng }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-map me-1"></i> Peta Google Maps
                                    </a>

                                    @if($session->checkin_photo_path)
                                        <a href="{{ asset('storage/' . $session->checkin_photo_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                            <i class="bi bi-camera me-1"></i> Lihat Foto Live
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Program & Rombel Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-secondary"><i class="bi bi-building me-2"></i>Detail Kelas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Nama Program</label>
                            <p class="mb-0 fw-medium">{{ $session->rombel->ekstrakurikuler->kategori_program }}</p>
                        </div>
                        
                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Rombel</label>
                            <p class="mb-0 fw-medium">{{ $session->rombel->nama_rombel }}</p>
                        </div>
                        
                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Sekolah</label>
                            <p class="mb-1 fw-bold text-primary">{{ $session->rombel->ekstrakurikuler->sekolah->namasekolah }}</p>
                            @if($session->rombel->ekstrakurikuler->google_maps_link)
                                <div class="mt-1">
                                    <a href="{{ $session->rombel->ekstrakurikuler->google_maps_link }}" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2.5 rounded-pill" style="font-size: 0.75rem;">
                                        <i class="bi bi-geo-alt me-1"></i> Buka Peta Lokasi
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Ruangan</label>
                            <p class="mb-0 fw-medium">{{ $session->rombel->ruangan ?? '-' }}</p>
                        </div>
                        
                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Penanggung Jawab</label>
                            <p class="mb-1 fw-medium text-dark">{{ $session->rombel->ekstrakurikuler->penanggung_jawab ?? '-' }}</p>
                            @if($session->rombel->ekstrakurikuler->no_telepon)
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $session->rombel->ekstrakurikuler->no_telepon);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                    $waText = urlencode("Halo " . $session->rombel->ekstrakurikuler->penanggung_jawab . ", saya instruktur Erlass untuk ekstrakurikuler " . $session->rombel->ekstrakurikuler->kategori_program . ".");
                                @endphp
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <a href="tel:{{ $session->rombel->ekstrakurikuler->no_telepon }}" class="btn btn-xs btn-outline-secondary py-1 px-2.5 rounded-pill" style="font-size: 0.75rem;">
                                        <i class="bi bi-telephone me-1"></i> Hubungi
                                    </a>
                                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" rel="noopener" class="btn btn-xs btn-outline-success py-1 px-2.5 rounded-pill" style="font-size: 0.75rem;">
                                        <i class="bi bi-whatsapp me-1"></i> WhatsApp
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">PIC Sekolah</label>
                            @php
                                $sekolah = $session->rombel->ekstrakurikuler->sekolah;
                                $picName = $sekolah->pic_nama ?? '-';
                                $picKontak = $sekolah->pic_kontak ?? '';
                            @endphp
                            <p class="mb-1 fw-medium text-dark">{{ $picName }}</p>
                            @if($picKontak)
                                @php
                                    $cleanPicPhone = preg_replace('/[^0-9]/', '', $picKontak);
                                    if (str_starts_with($cleanPicPhone, '0')) {
                                        $cleanPicPhone = '62' . substr($cleanPicPhone, 1);
                                    }
                                    $waPicText = urlencode("Halo Bapak/Ibu " . $picName . " (PIC Sekolah " . $sekolah->namasekolah . "), saya instruktur Erlass untuk ekstrakurikuler " . $session->rombel->ekstrakurikuler->kategori_program . ".");
                                @endphp
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <a href="tel:{{ $picKontak }}" class="btn btn-xs btn-outline-secondary py-1 px-2.5 rounded-pill" style="font-size: 0.75rem;">
                                        <i class="bi bi-telephone me-1"></i> Hubungi
                                    </a>
                                    <a href="https://wa.me/{{ $cleanPicPhone }}?text={{ $waPicText }}" target="_blank" rel="noopener" class="btn btn-xs btn-outline-success py-1 px-2.5 rounded-pill" style="font-size: 0.75rem;">
                                        <i class="bi bi-whatsapp me-1"></i> WhatsApp
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="col-6 col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Jumlah Siswa</label>
                            <p class="mb-0 fw-medium"><i class="bi bi-people me-1 text-primary"></i> {{ $session->rombel->jumlah_siswa }} siswa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content & Notes -->
            @if($session->topik_materi || $session->deskripsi_kegiatan || $session->catatan)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold text-secondary"><i class="bi bi-journal-text me-2"></i>Materi & Catatan</h5>
                    </div>
                    <div class="card-body">
                        @if($session->topik_materi)
                            <div class="mb-4">
                                <label class="small text-muted text-uppercase fw-bold mb-2">Topik Materi</label>
                                <div class="p-3 bg-light rounded border-start border-4 border-primary">
                                    {{ $session->topik_materi }}
                                </div>
                            </div>
                        @endif
                        
                        @if($session->deskripsi_kegiatan)
                            <div class="mb-4">
                                <label class="small text-muted text-uppercase fw-bold mb-2">Deskripsi Kegiatan</label>
                                <div class="p-3 bg-light rounded">
                                    {{ $session->deskripsi_kegiatan }}
                                </div>
                            </div>
                        @endif
                        
                        @if($session->catatan)
                            <div>
                                <label class="small text-muted text-uppercase fw-bold mb-2">Catatan</label>
                                <div class="p-3 bg-light rounded text-muted fst-italic">
                                    {{ $session->catatan }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Cancellation/Reschedule Info -->
            @if($session->alasan_pembatalan || $session->tanggal_pengganti)
                <div class="card shadow-sm mb-4 border-{{ $session->status === 'dibatalkan' ? 'danger' : 'warning' }}">
                    <div class="card-header text-white bg-{{ $session->status === 'dibatalkan' ? 'danger' : 'warning' }} py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            @if($session->status === 'dibatalkan')
                                <i class="bi bi-exclamation-triangle me-2"></i>Informasi Pembatalan
                            @else
                                <i class="bi bi-clock-history me-2"></i>Informasi Reschedule
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($session->alasan_pembatalan)
                            <div class="mb-3">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Alasan</label>
                                <p class="mb-0">{{ $session->alasan_pembatalan }}</p>
                            </div>
                        @endif
                        
                        @if($session->tanggal_pengganti)
                            <div>
                                <label class="small text-muted text-uppercase fw-bold mb-1">Tanggal Pengganti</label>
                                <p class="mb-0 fw-bold">
                                    {{ \Carbon\Carbon::parse($session->tanggal_pengganti)->format('d/m/Y') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Laporan Mengajar -->
            @if($session->laporanMengajar)
                <div class="card shadow-sm mb-4 border-success">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-success mb-1"><i class="bi bi-check-circle-fill me-2"></i>Laporan Tersedia</h5>
                            <small class="text-muted">Dibuat pada {{ $session->laporanMengajar->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <a href="{{ route('laporan-mengajar.show', $session->laporanMengajar) }}" 
                           class="btn btn-success">
                            Lihat Laporan
                        </a>
                    </div>
                </div>
            @endif

            
            <!-- Daftar Kehadiran (New Section) -->
            @if($session->laporanMengajar && $session->laporanMengajar->absensi->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-secondary">
                            <i class="bi bi-person-check me-2"></i>Daftar Kehadiran Siswa
                        </h5>
                        <span class="badge bg-primary rounded-pill">
                            {{ $session->laporanMengajar->jumlah_siswa_hadir }} / {{ $session->laporanMengajar->absensi->count() }} Hadir
                        </span>
                    </div>
                    <div class="card-body p-0">
                         <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 50px;">#</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center" style="width: 120px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($session->laporanMengajar->absensi as $index => $absen)
                                    <tr>
                                        <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                        <td class="fw-medium">{{ $absen->siswa->nama_lengkap ?? 'Siswa Terhapus' }}</td>
                                        <td class="text-center">
                                            @if($absen->hadir)
                                                <span class="badge bg-success-subtle text-success border border-success px-3">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Hadir
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger px-3">
                                                    <i class="bi bi-x-circle-fill me-1"></i> Absen
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 2rem; z-index: 1;">
                <!-- Tim Pengajar -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold text-secondary"><i class="bi bi-people-fill me-2"></i>Tim Pengajar</h5>
                    </div>
                    <div class="card-body">
                        @if($session->instruktur)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="bi bi-person-video3 fs-4"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 fw-bold text-dark">{{ $session->instruktur->nama_lengkap }}</h6>
                                    <small class="text-muted">Instruktur Utama</small>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mb-3">
                                <i class="bi bi-exclamation-circle me-1"></i> Belum ada instruktur
                            </div>
                        @endif
                        
                        @if($session->asisten)
                            <hr>
                            <div class="d-flex align-items-center mt-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar bg-success-subtle text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="bi bi-person-badge fs-4"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 fw-bold text-dark">{{ $session->asisten->nama_lengkap }}</h6>
                                    <small class="text-muted">Asisten</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold text-secondary"><i class="bi bi-gear-fill me-2"></i>Aksi Cepat</h5>
                    </div>
                    <div class="card-body d-grid gap-2">

                        
                        @can('reschedule', $session)
                            @if($session->canReschedule())
                                <button type="button" class="btn btn-outline-warning text-dark text-start" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                                    <i class="bi bi-calendar2-range me-2"></i> Reschedule
                                </button>
                            @endif
                        @endcan

                        @can('postpone', $session)
                            @if($session->canPostpone())
                                <button type="button" class="btn btn-outline-secondary text-start" data-bs-toggle="modal" data-bs-target="#postponeModal">
                                    <i class="bi bi-pause-circle me-2"></i> Tunda Sesi
                                </button>
                            @endif
                        @endcan
                        
                        @if($session->status === 'selesai' && !$session->laporanMengajar)
                            <button onclick="createLaporan()" class="btn btn-outline-success text-start">
                                <i class="bi bi-file-earmark-plus me-2"></i> Buat Laporan
                            </button>
                        @endif

                        <!-- Manual Reminder Button -->
                        @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']) && $session->instruktur)
                            <button type="button" class="btn btn-outline-info text-dark text-start" data-bs-toggle="modal" data-bs-target="#reminderModal">
                                <i class="bi bi-whatsapp me-2"></i> Kirim Reminder
                            </button>
                        @endif

                        @cannot('cancel', $session)
                             @if(!in_array($session->status, ['selesai']))
                                <div class="text-center text-muted small fst-italic">
                                    Hanya Admin yang dapat mengubah jadwal
                                </div>
                             @endif
                        @endcannot
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold text-secondary"><i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline-container">
                            <div class="timeline-item">
                                <span class="timeline-badge bg-primary"></span>
                                <div class="timeline-content">
                                    <p class="mb-0 small fw-bold text-dark">Sesi dibuat</p>
                                    <small class="text-muted">{{ $session->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            
                            @if($session->updated_at->ne($session->created_at))
                            <div class="timeline-item">
                                <span class="timeline-badge bg-warning"></span>
                                <div class="timeline-content">
                                    <p class="mb-0 small fw-bold text-dark">Terakhir diupdate</p>
                                    <small class="text-muted">{{ $session->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            @endif
                            
                            @if($session->status === 'selesai')
                            <div class="timeline-item">
                                <span class="timeline-badge bg-success"></span>
                                <div class="timeline-content">
                                    <p class="mb-0 small fw-bold text-dark">Sesi selesai</p>
                                    <small class="text-muted">{{ $session->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->


<!-- Reschedule Modal -->


<!-- Manual Reminder Modal -->


@push('scripts')
<script>
    const sessionId = {{ $session->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Helper untuk handle request
    async function sendRequest(url, method, body = null) {
        try {
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            };
            
            if (body) {
                options.body = JSON.stringify(body);
            }
            
            const response = await fetch(url, options);
            const data = await response.json();
            
            if (response.ok && data.success) {
                return { success: true, data: data };
            } else {
                return { success: false, message: data.message || 'Terjadi kesalahan' };
            }
        } catch (error) {
            console.error('Error:', error);
            return { success: false, message: 'Terjadi kesalahan koneksi' };
        }
    }

    function confirmCompleteSession() {
        if (confirm('Apakah Anda yakin ingin menyelesaikan sesi ini?')) {
            sendRequest(`/ekstrakurikuler/sessions/${sessionId}/complete`, 'POST')
                .then(result => {
                    if (result.success) {
                        location.reload();
                    } else {
                        alert(result.message);
                    }
                });
        }
    }

    function createLaporan() {
        window.location.href = `/laporan-mengajar/create?session_id=${sessionId}`;
    }

    // Modal Form Formatting to JSON for consistent API handling
    if (document.getElementById('postponeForm')) {
        document.getElementById('postponeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const reason = document.getElementById('postpone_reason').value;
            
            // Disable button
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Memproses...';

            sendRequest(`/ekstrakurikuler/sessions/${sessionId}/postpone`, 'POST', { alasan: reason })
                .then(result => {
                    if (result.success) {
                        location.reload();
                    } else {
                        alert(result.message);
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                });
        });
    }

    document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
        // ... (existing helper logic) ...
        e.preventDefault();
        const newDate = document.getElementById('new_date').value;
        const reason = document.getElementById('reschedule_reason').value;
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Memproses...';

        sendRequest(`/ekstrakurikuler/sessions/${sessionId}/reschedule`, 'POST', { 
            tanggal_pengganti: newDate,
            alasan: reason 
        }).then(result => {
            if (result.success) {
                location.reload();
            } else {
                alert(result.message);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    });

    // Manual Reminder Logic for Show View
    document.getElementById('reminderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const message = document.getElementById('customMessage').value;
        const target = document.getElementById('reminderTarget').value || 'instructor';
        const btn = document.getElementById('btnSendReminder');
        const spinner = btn.querySelector('.spinner-border');
        
        btn.disabled = true;
        spinner.classList.remove('d-none');
        
        sendRequest(`/ekstrakurikuler/sessions/${sessionId}/remind`, 'POST', { custom_message: message, target: target })
            .then(result => {
                if (result.success) {
                    alert('Sukses: ' + result.data.message);
                    bootstrap.Modal.getInstance(document.getElementById('reminderModal')).hide();
                } else {
                    alert('Gagal: ' + result.message);
                }
            })
            .finally(() => {
                btn.disabled = false;
                spinner.classList.add('d-none');
            });
    });
</script>
@endpush

@push('modals')
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">Batalkan Sesi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelForm">
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                        <div>
                            Tindakan ini tidak dapat dibatalkan.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea name="alasan_pembatalan" id="cancel_reason" rows="3" required class="form-control" placeholder="Jelaskan alasan pembatalan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Batalkan Sesi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark" id="rescheduleModalLabel">Reschedule Sesi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rescheduleForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_date" class="form-label">Tanggal Baru <span class="text-danger">*</span></label>
                        <input type="text" name="tanggal_pengganti" id="new_date" required class="form-control datepicker" placeholder="DD-MM-YYYY">
                    </div>
                    <div class="mb-3">
                        <label for="reschedule_reason" class="form-label">Alasan (Opsional)</label>
                        <textarea name="alasan" id="reschedule_reason" rows="3" class="form-control" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-warning text-dark">Simpan Jadwal Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="postponeModal" tabindex="-1" aria-labelledby="postponeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="postponeModalLabel">Tunda Sesi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="postponeForm">
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                        <div>
                            Sesi akan ditunda tanpa tanggal pelaksanaan baru. Anda dapat mengatur ulang jadwal kembali melalui fitur Reschedule.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="postpone_reason" class="form-label">Alasan Penundaan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="postpone_reason" rows="3" required class="form-control" placeholder="Jelaskan alasan penundaan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-secondary text-white">Tunda Sesi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="reminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-whatsapp text-success me-2"></i>Kirim Reminder Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reminderForm">
                <input type="hidden" id="reminderTarget" value="instructor">
                <div class="modal-body">
                    <p class="mb-2">Kirim notifikasi WhatsApp ke instruktur: <strong>{{ $session->instruktur->nama_lengkap ?? 'Instruktur' }}</strong></p>
                    
                    <div class="mb-3">
                        <label for="customMessage" class="form-label small fw-bold">Pesan Tambahan (Opsional)</label>
                        <textarea class="form-control" id="customMessage" rows="3" placeholder="Contoh: Harap datang 15 menit lebih awal."></textarea>
                    </div>

                    <div class="alert alert-info border-0 p-2 small mb-0">
                        <i class="bi bi-info-circle-fill me-1"></i> Gunakan tombol <strong>"Tes WA Admin"</strong> untuk menguji apakah koneksi Fonnte Gateway berfungsi ke HP Admin.
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-success btn-sm fw-bold" id="btnTestAdminReminder" onclick="sendReminderTarget('admin')">
                        <i class="bi bi-whatsapp me-1"></i> 🧪 Tes WA Admin (+62 821-1830-2927)
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold" id="btnSendReminder" onclick="document.getElementById('reminderTarget').value='instructor'">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <i class="bi bi-send me-1"></i> Kirim ke Instruktur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal GPS Check-in (Live Camera & GPS Location) -->
<div class="modal fade" id="gpsCheckinModal" tabindex="-1" aria-labelledby="gpsCheckinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="gpsCheckinModalLabel">
                    <i class="bi bi-geo-alt-fill me-1"></i> Check-in Real-Time (GPS & Camera)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ekstrakurikuler.sessions.checkin', $session) }}" method="POST" enctype="multipart/form-data" id="gpsCheckinForm">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="latitude" id="checkin_lat">
                    <input type="hidden" name="longitude" id="checkin_lng">

                    <div id="gpsStatusAlert" class="alert alert-info d-flex align-items-center gap-2 mb-3">
                        <div class="spinner-border spinner-border-sm text-primary" id="gpsSpinner" role="status"></div>
                        <div id="gpsStatusText" class="small fw-semibold">Mendeteksi titik lokasi GPS HP Anda...</div>
                    </div>

                    <div class="mb-3">
                        <label for="checkin_photo" class="form-label fw-bold text-dark">Foto Live Kamera (Wajib Selfie / Suasana Sekolah)</label>
                        <input type="file" name="photo" id="checkin_photo" capture="camera" accept="image/*" class="form-control" required>
                        <small class="text-muted d-block mt-1">Gunakan Kamera HP secara langsung untuk mengambil foto terbaru di sekolah.</small>
                    </div>

                    <div class="bg-light p-3 rounded-3 border">
                        <small class="text-muted fw-bold d-block"><i class="bi bi-shield-check text-success me-1"></i>Aturan Verifikasi GPS Erlass:</small>
                        <small class="text-secondary d-block" style="font-size: 0.75rem;">
                            Sistem akan secara otomatis menghitung jarak presisi titik HP Anda ke Sekolah (Radius Toleransi: &le; 500 meter).
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold px-4" id="btnSubmitCheckin" disabled>
                        <i class="bi bi-check-circle me-1"></i> Kirim Check-in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function sendReminderTarget(target) {
    document.getElementById('reminderTarget').value = target;
    document.getElementById('reminderForm').requestSubmit();
}

document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('gpsCheckinModal');
    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function () {
            const statusAlert = document.getElementById('gpsStatusAlert');
            const statusText = document.getElementById('gpsStatusText');
            const spinner = document.getElementById('gpsSpinner');
            const btnSubmit = document.getElementById('btnSubmitCheckin');

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        document.getElementById('checkin_lat').value = position.coords.latitude;
                        document.getElementById('checkin_lng').value = position.coords.longitude;

                        statusAlert.className = 'alert alert-success d-flex align-items-center gap-2 mb-3';
                        spinner.style.display = 'none';
                        statusText.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Lokasi GPS Terdeteksi! (Lat: ' + position.coords.latitude.toFixed(5) + ', Lng: ' + position.coords.longitude.toFixed(5) + ')';
                        btnSubmit.disabled = false;
                    },
                    function (error) {
                        spinner.style.display = 'none';
                        statusAlert.className = 'alert alert-warning d-flex align-items-center gap-2 mb-3';
                        statusText.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Gagal mengambil GPS: ' + error.message + '. Menggunakan koordinat default.';
                        document.getElementById('checkin_lat').value = -6.200000;
                        document.getElementById('checkin_lng').value = 106.816666;
                        btnSubmit.disabled = false;
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                spinner.style.display = 'none';
                statusAlert.className = 'alert alert-danger d-flex align-items-center gap-2 mb-3';
                statusText.innerHTML = 'Browser Anda tidak mendukung Geolocation GPS.';
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    .timeline-container {
        position: relative;
        padding-left: 20px;
    }
    .timeline-container::before {
        content: '';
        position: absolute;
        top: 5px;
        bottom: 5px;
        left: 3px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-badge {
        position: absolute;
        left: -22px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #cbd5e1;
        z-index: 1;
    }
    .timeline-badge.bg-primary { border-color: #3b82f6 !important; background-color: #3b82f6 !important; }
    .timeline-badge.bg-warning { border-color: #f59e0b !important; background-color: #f59e0b !important; }
    .timeline-badge.bg-success { border-color: #10b981 !important; background-color: #10b981 !important; }
    .timeline-badge.bg-danger { border-color: #f43f5e !important; background-color: #f43f5e !important; }
</style>
@endpush

@endsection