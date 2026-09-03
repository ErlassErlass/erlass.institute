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

                    @php
                        $userRole = Auth::user()->role ?? '';
                        $canCheckin = in_array($session->status, ['terjadwal', 'berlangsung', 'selesai']);
                        $isInstruktur = $userRole === 'instruktur';
                        $isAdmin = in_array($userRole, ['webmaster', 'admin_sistem', 'admin']);
                        $alreadyCheckedIn = !empty($session->checkin_lat);
                        $isWindowOpen = $session->isCheckinWindowOpen(Auth::user());
                    @endphp

                    @if(($isInstruktur || $isAdmin) && $canCheckin)
                        @if($isWindowOpen || $alreadyCheckedIn || $isAdmin)
                            <button type="button"
                                class="btn {{ $alreadyCheckedIn ? 'btn-outline-success' : 'btn-success' }} w-100 w-sm-auto shadow-sm fw-bold"
                                data-bs-toggle="modal" data-bs-target="#gpsCheckinModal">
                                <i class="bi bi-geo-alt-fill me-1"></i>
                                @if($alreadyCheckedIn)
                                    🔄 Update Check-in (GPS & Camera)
                                @elseif($isAdmin)
                                    📌 Check-in Verifikasi (GPS & Camera)
                                @else
                                    📌 Check-in Hadir (GPS & Camera)
                                @endif
                            </button>
                        @else
                            <button type="button"
                                class="btn btn-outline-secondary w-100 w-sm-auto shadow-xs text-start text-sm-center"
                                disabled
                                title="Check-in dibuka 30 menit sebelum jadwal sesi">
                                <i class="bi bi-clock me-1"></i>
                                Check-in dibuka {{ $session->waktu_buka_checkin ? $session->waktu_buka_checkin->format('H:i') : '-' }} WIB
                            </button>
                        @endif
                    @endif

                    @if($session->canComplete())
                        @php
                            $blockingPrior = $session->getBlockingPriorSession(Auth::user());
                        @endphp
                        @if($blockingPrior)
                            <a href="{{ route('ekstrakurikuler.sessions.report.create', $blockingPrior->id) }}" 
                               class="btn btn-warning text-dark fw-bold w-100 w-sm-auto shadow-xs"
                               title="Selesaikan laporan Pertemuan ke-{{ $blockingPrior->nomor_pertemuan }} terlebih dahulu">
                                <i class="bi bi-lock-fill me-1"></i> Isi Laporan Sesi P.{{ $blockingPrior->nomor_pertemuan }} Dulu
                            </a>
                        @else
                            <a href="{{ route('ekstrakurikuler.sessions.report.create', $session) }}" 
                               class="btn btn-primary w-100 w-sm-auto">
                                <i class="bi bi-file-earmark-check me-1"></i> Buat Laporan & Absensi
                            </a>
                        @endif
                    @endif
                    
                    @can('update', $session)
                        @if(in_array($session->status, ['terjadwal', 'ditunda']) || ($session->status === 'selesai' && Auth::user()->hasRole(['webmaster', 'admin_sistem', 'admin'])))
                            <a href="{{ route('ekstrakurikuler.sessions.edit', array_merge(['session' => $session->id], request()->query())) }}" 
                               class="btn btn-outline-primary w-100 w-sm-auto">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                        @endif

                        @if($session->status === 'selesai' && $session->laporanMengajar)
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

    @if(isset($blockingPrior) && $blockingPrior)
    <div class="alert alert-warning border-0 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
            <div>
                <strong class="d-block text-dark">Laporan Sesi Terdahulu Belum Selesai</strong>
                <span class="small text-muted">
                    Sesuai aturan sistem, Anda tidak dapat membuat laporan di sesi baru sebelum laporan <strong>Pertemuan ke-{{ $blockingPrior->nomor_pertemuan }}</strong> 
                    ({{ $blockingPrior->rombel?->nama_rombel ?? 'Rombel' }} &bull; {{ $blockingPrior->tanggal_terjadwal ? \Carbon\Carbon::parse($blockingPrior->tanggal_terjadwal)->locale('id')->translatedFormat('d F Y') : '-' }}) 
                    selesai dibuat.
                </span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('ekstrakurikuler.sessions.report.create', $blockingPrior->id) }}" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-3 shadow-xs">
                <i class="bi bi-pencil-square me-1"></i> Buat Laporan Sesi P.{{ $blockingPrior->nomor_pertemuan }}
            </a>
            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold bg-white" data-bs-toggle="modal" data-bs-target="#markHolidayModal">
                <i class="bi bi-calendar-x me-1"></i> Sesi P.{{ $blockingPrior->nomor_pertemuan }} Libur / Ditunda?
            </button>
        </div>
    </div>
    @endif

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
                                    @elseif($session->checkin_status_radius === 'out_of_bounds')
                                        <span class="badge bg-warning text-dark fs-7 px-3 py-1.5 rounded-pill">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Diluar Radius (Jarak: {{ $session->checkin_distance_meters }}m dari Sekolah)
                                        </span>
                                    @else
                                        <span class="badge bg-secondary fs-7 px-3 py-1.5 rounded-pill">
                                            <i class="bi bi-geo-alt me-1"></i>Lokasi Tercatat (Koordinat Sekolah Belum Disetel)
                                        </span>
                                    @endif

                                    @if($session->checkin_accuracy_meters)
                                        <span class="badge bg-light text-dark border fs-7 px-2.5 py-1.5 rounded-pill" title="Akurasi Sinyal GPS">
                                            <i class="bi bi-broadcast me-1 text-primary"></i>Akurasi: &plusmn;{{ round($session->checkin_accuracy_meters) }}m
                                        </span>
                                    @endif

                                    @if($session->checkin_mock_suspected)
                                        <span class="badge bg-danger text-white fs-7 px-3 py-1.5 rounded-pill" title="Terdeteksi anomali pada sensor GPS / Kecepatan perpindahan tidak wajar">
                                            <i class="bi bi-shield-slash-fill me-1"></i>Indikasi Fake GPS
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
            @elseif($session->rombel)
                @php
                    $enrolledStudents = $session->rombel->siswaAktif()->orderBy('nama_lengkap', 'asc')->get();
                @endphp
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-secondary">
                                <i class="bi bi-people me-2"></i>Daftar Siswa Terdaftar ({{ $session->rombel->nama_rombel }})
                            </h5>
                            <small class="text-muted">Daftar siswa aktif di rombel ini untuk sesi pertemuan mendatang.</small>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-bold">
                            {{ $enrolledStudents->count() }} Siswa Terdaftar
                        </span>
                    </div>
                    <div class="card-body p-0">
                        @if($enrolledStudents->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4" style="width: 50px;">#</th>
                                            <th>Nama Siswa</th>
                                            <th>NISN / ID</th>
                                            <th>Kelas Akademik</th>
                                            <th class="text-center" style="width: 130px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($enrolledStudents as $index => $siswa)
                                        <tr>
                                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                            <td class="fw-medium text-dark">{{ $siswa->nama_lengkap }}</td>
                                            <td class="text-muted small">{{ $siswa->nisn ?? '-' }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $siswa->kelas ?? '-' }}</span></td>
                                            <td class="text-center">
                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                    <i class="bi bi-check2 me-1"></i> Terdaftar
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="small text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Presensi kehadiran akan dicatat oleh instruktur saat/setelah kegiatan belajar mengajar berlangsung.
                                </span>
                                <a href="{{ route('ekstrakurikuler-session.print-session', $session) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-printer me-1"></i> Cetak Lembar Presensi
                                </a>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-person-x fs-2 d-block mb-1 text-secondary"></i>
                                Belum ada siswa aktif yang terdaftar di {{ $session->rombel->nama_rombel }}.
                            </div>
                        @endif
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

                        @if($session->canResetToScheduled() && auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
                            <button type="button" class="btn btn-outline-danger text-start" onclick="resetSessionToScheduled()">
                                <i class="bi bi-arrow-counterclockwise me-2"></i> Reset ke Terjadwal
                            </button>
                        @endif
                        
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
        e.preventDefault();
        const newDate = document.getElementById('new_date').value;
        const reason = document.getElementById('reschedule_reason').value;
        const cascade = document.getElementById('reschedule_cascade')?.checked || false;
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Memproses...';

        sendRequest(`/ekstrakurikuler/sessions/${sessionId}/reschedule`, 'POST', { 
            tanggal_pengganti: newDate,
            alasan: reason,
            cascade_shift: cascade
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
                    alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
                }
                btn.disabled = false;
                spinner.classList.add('d-none');
            })
            .catch(error => {
                btn.disabled = false;
                spinner.classList.add('d-none');
                alert('Terjadi kesalahan koneksi.');
            });
    });
</script>
@endpush

@push('modals')
@include('ekstrakurikuler.sessions.partials.modals.cancel')
@include('ekstrakurikuler.sessions.partials.modals.reschedule')
@include('ekstrakurikuler.sessions.partials.modals.postpone')
@include('ekstrakurikuler.sessions.partials.modals.holiday')
@include('ekstrakurikuler.sessions.partials.modals.reminder')
@include('ekstrakurikuler.sessions.partials.modals.gps-checkin')

<script>
function sendReminderTarget(target) {
    document.getElementById('reminderTarget').value = target;
    document.getElementById('reminderForm').requestSubmit();
}

function formatBytes(bytes, decimals = 1) {
    if (!+bytes) return '0 B';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
}

function applyCanvasWatermark(ctx, width, height, watermarkData) {
    if (!watermarkData) return;

    const barHeight = Math.max(70, Math.round(height * 0.13));
    const gradient = ctx.createLinearGradient(0, height - barHeight, 0, height);
    gradient.addColorStop(0, 'rgba(0, 0, 0, 0)');
    gradient.addColorStop(0.25, 'rgba(0, 0, 0, 0.72)');
    gradient.addColorStop(1, 'rgba(0, 0, 0, 0.90)');
    ctx.fillStyle = gradient;
    ctx.fillRect(0, height - barHeight, width, barHeight);

    const baseFontSize = Math.max(13, Math.round(width * 0.024));
    ctx.shadowColor = 'rgba(0, 0, 0, 0.9)';
    ctx.shadowBlur = 4;
    ctx.shadowOffsetX = 1;
    ctx.shadowOffsetY = 1;

    // Line 1: School & Meeting
    ctx.font = `bold ${baseFontSize}px sans-serif`;
    ctx.fillStyle = '#ffffff';
    const line1 = `📍 ${watermarkData.school || 'Erlass Institute'} • Pertemuan ${watermarkData.meeting || '?'}`;
    ctx.fillText(line1, 16, height - (barHeight * 0.55));

    // Line 2: Timestamp & GPS Coordinates
    ctx.font = `normal ${Math.max(11, Math.round(baseFontSize * 0.85))}px sans-serif`;
    ctx.fillStyle = '#f8f9fa';
    const line2 = `🕒 ${watermarkData.time || ''} • GPS: ${watermarkData.coords || 'Aktif'}`;
    ctx.fillText(line2, 16, height - (barHeight * 0.20));

    ctx.shadowColor = 'transparent';
    ctx.shadowBlur = 0;
}

function compressImageFile(file, maxWidth = 1280, maxHeight = 1280, quality = 0.75, watermarkData = null) {
    return new Promise((resolve) => {
        if (!file || !file.type.startsWith('image/')) {
            resolve(null);
            return;
        }

        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                let width = img.width;
                let height = img.height;

                if (width > height) {
                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width = Math.round((width * maxHeight) / height);
                        height = maxHeight;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // ─── Geotag Canvas Watermark ───
                applyCanvasWatermark(ctx, width, height, watermarkData);

                canvas.toBlob((blob) => {
                    if (!blob) {
                        resolve(null);
                        return;
                    }
                    const compressedFile = new File([blob], (file.name || 'checkin_photo').replace(/\.[^/.]+$/, "") + ".jpg", {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });
                    resolve({
                        file: compressedFile,
                        previewUrl: canvas.toDataURL('image/jpeg', quality),
                        originalSize: file.size,
                        compressedSize: blob.size
                    });
                }, 'image/jpeg', quality);
            };
            img.onerror = () => resolve(null);
        };
        reader.onerror = () => resolve(null);
    });
}

let mediaStream = null;
let currentFacingMode = 'environment';

window.stopLiveCameraStream = function() {
    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop());
        mediaStream = null;
    }
    const videoEl = document.getElementById('liveCameraVideo');
    if (videoEl) videoEl.srcObject = null;
};

window.startLiveCameraOrFallback = async function() {
    const videoEl = document.getElementById('liveCameraVideo');
    const cameraContainer = document.getElementById('liveCameraContainer');
    const triggerBox = document.getElementById('cameraTriggerBox');
    const previewContainer = document.getElementById('photoPreviewContainer');

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        try {
            window.stopLiveCameraStream();
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: currentFacingMode },
                    width: { ideal: 1280 },
                    height: { ideal: 960 }
                },
                audio: false
            });

            if (videoEl && cameraContainer) {
                videoEl.srcObject = mediaStream;
                await videoEl.play();
                cameraContainer.classList.remove('d-none');
                if (triggerBox) triggerBox.classList.add('d-none');
                if (previewContainer) previewContainer.classList.add('d-none');
                return;
            }
        } catch (err) {
            console.warn('getUserMedia camera stream not available, fallback to native camera capture:', err);
        }
    }

    // Fallback: trigger native camera directly with capture="environment" locked
    const photoInput = document.getElementById('checkin_photo');
    if (photoInput) {
        photoInput.setAttribute('capture', currentFacingMode);
        photoInput.click();
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('checkin_photo');
    const cameraTriggerBox = document.getElementById('cameraTriggerBox');
    const previewContainer = document.getElementById('photoPreviewContainer');
    const photoPreview = document.getElementById('photoPreview');
    const compressionBadge = document.getElementById('photoCompressionBadge');
    const compressionText = document.getElementById('photoCompressionText');
    const form = document.getElementById('gpsCheckinForm');
    const btnSubmit = document.getElementById('btnSubmitCheckin');
    const cameraContainer = document.getElementById('liveCameraContainer');
    const videoEl = document.getElementById('liveCameraVideo');

    const schoolName = @json($session->ekstrakurikuler?->sekolah?->namasekolah ?? ($session->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? 'Erlass Institute'));
    const meetingNumber = @json($session->nomor_pertemuan);

    function getWatermarkPayload() {
        const latVal = document.getElementById('checkin_lat')?.value;
        const lngVal = document.getElementById('checkin_lng')?.value;
        const accVal = document.getElementById('checkin_accuracy')?.value;
        const coordsText = (latVal && lngVal) ? `${parseFloat(latVal).toFixed(5)}, ${parseFloat(lngVal).toFixed(5)} (±${accVal ? Math.round(accVal) + 'm' : '?'})` : 'GPS Aktif';

        const now = new Date();
        const timeString = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + 
                           now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        return {
            school: schoolName,
            meeting: meetingNumber,
            time: timeString,
            coords: coordsText
        };
    }

    // ─── Live Camera Shutter Button ───
    document.getElementById('btnCaptureLive')?.addEventListener('click', async function () {
        if (!videoEl || !videoEl.videoWidth) return;

        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses Foto...';
        }

        const watermarkData = getWatermarkPayload();

        // Capture frame from live video
        const canvas = document.createElement('canvas');
        canvas.width = videoEl.videoWidth;
        canvas.height = videoEl.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);

        // Apply Geotag Watermark
        applyCanvasWatermark(ctx, canvas.width, canvas.height, watermarkData);

        // Stop stream
        window.stopLiveCameraStream();
        if (cameraContainer) cameraContainer.classList.add('d-none');

        canvas.toBlob((blob) => {
            if (!blob) return;
            const liveFile = new File([blob], "live_checkin_" + Date.now() + ".jpg", { type: "image/jpeg" });
            try {
                const dt = new DataTransfer();
                dt.items.add(liveFile);
                if (photoInput) photoInput.files = dt.files;
            } catch (e) {
                console.warn('DataTransfer fallback', e);
            }

            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            const base64Input = document.getElementById('checkin_photo_base64');
            if (base64Input) base64Input.value = dataUrl;

            if (photoPreview && previewContainer) {
                photoPreview.src = dataUrl;
                previewContainer.classList.remove('d-none');
            }

            if (compressionBadge && compressionText) {
                compressionBadge.classList.remove('d-none');
                compressionText.innerHTML = '<i class="bi bi-patch-check-fill text-success fs-6 me-1"></i>Foto Kamera Live Siap (' + formatBytes(blob.size) + ')';
            }

            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="bi bi-check-circle me-1"></i> Kirim Check-in';
            }
        }, 'image/jpeg', 0.8);
    });

    // ─── Switch Camera (Front / Back) ───
    document.getElementById('btnSwitchCam')?.addEventListener('click', async function () {
        currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
        await window.startLiveCameraOrFallback();
    });

    // ─── Close Camera Viewfinder ───
    document.getElementById('btnCloseCam')?.addEventListener('click', function () {
        window.stopLiveCameraStream();
        if (cameraContainer) cameraContainer.classList.add('d-none');
        if (triggerBox) triggerBox.classList.remove('d-none');
    });

    // ─── Close Camera on Modal Dismiss ───
    const gpsModal = document.getElementById('gpsCheckinModal');
    if (gpsModal) {
        gpsModal.addEventListener('hidden.bs.modal', function () {
            window.stopLiveCameraStream();
        });
    }

    // ─── Fallback Native Input Change Handler ───
    if (photoInput) {
        photoInput.addEventListener('change', async function () {
            if (this.files && this.files[0]) {
                const originalFile = this.files[0];
                if (compressionBadge) {
                    compressionBadge.classList.remove('d-none');
                    compressionText.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses stempel geotag...';
                }

                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses Foto...';
                }

                const watermarkData = getWatermarkPayload();

                try {
                    const result = await compressImageFile(originalFile, 1280, 1280, 0.75, watermarkData);
                    if (result && result.file) {
                        try {
                            const dt = new DataTransfer();
                            dt.items.add(result.file);
                            photoInput.files = dt.files;
                        } catch (e) {
                            console.warn('DataTransfer not supported', e);
                        }

                        const base64Input = document.getElementById('checkin_photo_base64');
                        if (base64Input && result.previewUrl) {
                            base64Input.value = result.previewUrl;
                        }

                        if (cameraTriggerBox) cameraTriggerBox.classList.add('d-none');
                        if (photoPreview && previewContainer) {
                            photoPreview.src = result.previewUrl;
                            previewContainer.classList.remove('d-none');
                        }

                        if (compressionBadge && compressionText) {
                            const reduction = Math.round((1 - (result.compressedSize / result.originalSize)) * 100);
                            compressionText.innerHTML = `Foto & Geotag siap! Ukuran: ${formatBytes(result.originalSize)} ➔ <strong>${formatBytes(result.compressedSize)}</strong> (${reduction > 0 ? reduction + '% lebih hemat' : 'Optimal'})`;
                        }
                    }
                } finally {
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="bi bi-check-circle me-1"></i> Kirim Check-in';
                    }
                }
            }
        });
    }

    if (form && btnSubmit) {
        form.addEventListener('submit', function (e) {
            if (!photoInput || !photoInput.files || photoInput.files.length === 0) {
                e.preventDefault();
                alert('Silakan ambil foto bukti kehadiran terlebih dahulu dengan menekan tombol kamera.');
                return;
            }
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Mengirim Presensi...';
        });
    }

    // ─── GPS Distance & Check-in Modal ───
    function calculateHaversineDistanceMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Radius bumi dalam meter
        const toRad = deg => (deg * Math.PI) / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return Math.round(R * c);
    }

    window.triggerGpsDetection = function() {
        const modalEl = document.getElementById('gpsCheckinModal');
        if (!modalEl) return;

        const statusAlert = document.getElementById('gpsStatusAlert');
        const statusText = document.getElementById('gpsStatusText');
        const spinner = document.getElementById('gpsSpinner');
        const btnSubmit = document.getElementById('btnSubmitCheckin');

        const schoolLatRaw = modalEl.dataset.schoolLat;
        const schoolLngRaw = modalEl.dataset.schoolLng;
        const schoolName = modalEl.dataset.schoolName || 'Sekolah';
        const hasSchoolCoords = schoolLatRaw !== '' && schoolLngRaw !== '';
        const schoolLat = hasSchoolCoords ? parseFloat(schoolLatRaw) : null;
        const schoolLng = hasSchoolCoords ? parseFloat(schoolLngRaw) : null;

        statusAlert.className = 'alert alert-info d-flex align-items-center gap-2 mb-3';
        if (spinner) spinner.style.display = 'inline-block';
        statusText.innerHTML = '<span class="small fw-semibold">Mendeteksi titik lokasi GPS HP Anda...</span>';
        if (btnSubmit) btnSubmit.disabled = true;

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    document.getElementById('checkin_lat').value = lat;
                    document.getElementById('checkin_lng').value = lng;
                    document.getElementById('checkin_accuracy').value = accuracy ? accuracy.toFixed(2) : '';
                    document.getElementById('checkin_device_info').value = navigator.userAgent;

                    // Heuristik Deteksi Fake GPS
                    let isMockSuspected = false;
                    let mockReason = '';

                    if (accuracy === 0) {
                        isMockSuspected = true;
                        mockReason = 'Akurasi GPS 0m (anomali)';
                    }

                    if (isMockSuspected) {
                        document.getElementById('checkin_mock_suspected').value = '1';
                    }

                    const acc = accuracy ? Math.round(accuracy) + 'm' : '?';
                    const accBadge = `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size:.7rem;"><i class="bi bi-broadcast me-1"></i>Akurasi: ±${acc}</span>`;

                    if (spinner) spinner.style.display = 'none';

                    let distance = null;
                    let distanceFormatted = '';
                    let isWithinRadius = true;

                    if (schoolLat !== null && schoolLng !== null && !isNaN(schoolLat) && !isNaN(schoolLng)) {
                        distance = calculateHaversineDistanceMeters(lat, lng, schoolLat, schoolLng);
                        if (distance < 1000) {
                            distanceFormatted = `${distance} meter`;
                        } else {
                            distanceFormatted = `${(distance / 1000).toFixed(2)} km (${distance.toLocaleString('id-ID')} m)`;
                        }
                        isWithinRadius = distance <= 500;
                    }

                    let statusHtml = '';

                    if (schoolLat !== null && schoolLng !== null && !isNaN(schoolLat) && !isNaN(schoolLng)) {
                        if (isWithinRadius) {
                            statusAlert.className = 'alert alert-success border border-success border-opacity-25 rounded-3 p-3 mb-3 shadow-sm';
                            statusHtml = `
                                <div class="w-100">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                        <div class="fw-bold text-success d-flex align-items-center gap-1.5">
                                            <i class="bi bi-geo-alt-fill text-success fs-5"></i>
                                            <span>Dalam Radius Sekolah (🟢 Terverifikasi)</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="badge bg-success text-white px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                                <i class="bi bi-check2-circle me-1"></i>Radius Aman (&le; 500m)
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 rounded-pill" onclick="triggerGpsDetection()" title="Refresh Lokasi GPS" style="font-size: 0.72rem;">
                                                <i class="bi bi-arrow-clockwise"></i> Refresh
                                            </button>
                                        </div>
                                    </div>
                                    <div class="p-2.5 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 mt-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-secondary small"><i class="bi bi-building me-1"></i>Tujuan: <strong>${schoolName}</strong></span>
                                            <span class="fw-bold text-success fs-6"><i class="bi bi-signpost-2 me-1"></i>${distanceFormatted}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center text-muted mt-1.5 pt-1.5 border-top border-success border-opacity-25" style="font-size: 0.74rem;">
                                            <span><i class="bi bi-pin-map me-1"></i>Titik Anda: ${lat.toFixed(5)}, ${lng.toFixed(5)}</span>
                                            ${accBadge}
                                        </div>
                                    </div>
                                </div>
                            `;
                        } else {
                            statusAlert.className = 'alert alert-warning border border-warning border-opacity-50 rounded-3 p-3 mb-3 shadow-sm';
                            statusHtml = `
                                <div class="w-100">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                        <div class="fw-bold text-warning-emphasis d-flex align-items-center gap-1.5">
                                            <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                                            <span>Di Luar Radius Sekolah (⚠️ ${distanceFormatted})</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                                > 500m dari Titik
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 rounded-pill" onclick="triggerGpsDetection()" title="Refresh Lokasi GPS" style="font-size: 0.72rem;">
                                                <i class="bi bi-arrow-clockwise"></i> Refresh
                                            </button>
                                        </div>
                                    </div>
                                    <div class="p-2.5 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 mt-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-secondary small"><i class="bi bi-building me-1"></i>Tujuan: <strong>${schoolName}</strong></span>
                                            <span class="fw-bold text-danger fs-6"><i class="bi bi-signpost-2 me-1"></i>${distanceFormatted}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center text-muted mt-1.5 pt-1.5 border-top border-warning border-opacity-25" style="font-size: 0.74rem;">
                                            <span><i class="bi bi-pin-map me-1"></i>Titik Anda: ${lat.toFixed(5)}, ${lng.toFixed(5)}</span>
                                            ${accBadge}
                                        </div>
                                        <div class="small text-muted mt-1.5 pt-1 border-top border-warning border-opacity-25" style="font-size: 0.72rem; line-height: 1.35;">
                                            <i class="bi bi-info-circle me-1 text-warning"></i>Jika Anda sudah berada di lokasi sekolah, titik sekolah mungkin bergeser dan akan dikalibrasi Admin. Presensi tetap dapat dikirim.
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    } else {
                        statusAlert.className = 'alert alert-info border border-info border-opacity-25 rounded-3 p-3 mb-3 shadow-sm';
                        statusHtml = `
                            <div class="w-100">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                    <div class="fw-bold text-info-emphasis d-flex align-items-center gap-1.5">
                                        <i class="bi bi-check-circle-fill text-info fs-5"></i>
                                        <span>Lokasi GPS Anda Terdeteksi!</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 rounded-pill" onclick="triggerGpsDetection()" title="Refresh Lokasi GPS" style="font-size: 0.72rem;">
                                        <i class="bi bi-arrow-clockwise"></i> Refresh
                                    </button>
                                </div>
                                <div class="small text-muted mt-1">Titik Anda: Lat ${lat.toFixed(5)}, Lng ${lng.toFixed(5)} ${accBadge}</div>
                                <div class="small text-secondary mt-1" style="font-size: 0.74rem;"><i class="bi bi-info-circle me-1"></i>Titik koordinat sekolah belum diset oleh Admin. Presensi Anda tetap akan tercatat.</div>
                            </div>
                        `;
                    }

                    if (isMockSuspected) {
                        statusHtml += `<div class="small text-danger mt-2 fw-semibold p-2 bg-danger bg-opacity-10 rounded-2 border border-danger border-opacity-25"><i class="bi bi-shield-slash me-1"></i>Perhatian: Terdeteksi sinyal anomali (${mockReason}).</div>`;
                    }

                    statusText.innerHTML = statusHtml;
                    if (btnSubmit) btnSubmit.disabled = false;
                },
                function (error) {
                    if (spinner) spinner.style.display = 'none';
                    statusAlert.className = 'alert alert-warning border border-warning border-opacity-50 rounded-3 p-3 mb-3 shadow-sm';
                    statusText.innerHTML = `
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                            <div class="small text-dark fw-semibold">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Gagal Membaca GPS: ${error.message}. Pastikan izin lokasi aktif.
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 rounded-pill" onclick="triggerGpsDetection()" style="font-size: 0.72rem;">
                                <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                            </button>
                        </div>
                    `;
                    if (btnSubmit) btnSubmit.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        } else {
            if (spinner) spinner.style.display = 'none';
            statusAlert.className = 'alert alert-danger border border-danger border-opacity-50 rounded-3 p-3 mb-3 shadow-sm';
            statusText.innerHTML = 'Browser Anda tidak mendukung Geolocation GPS.';
        }
    };

    const modalEl = document.getElementById('gpsCheckinModal');
    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function () {
            window.triggerGpsDetection();
        });
    }
});

function resetSessionToScheduled() {
    if (!confirm('Apakah Anda yakin ingin mereset sesi ini kembali ke status "Terjadwal"? Waktu pelaksanaan aktual akan dikosongkan.')) {
        return;
    }

    const alasan = prompt('Alasan reset (opsional):', 'Sesi tidak sengaja dimulai') || 'Reset manual oleh admin';

    fetch('{{ route("ekstrakurikuler.sessions.reset-to-scheduled", $session) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ alasan: alasan })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Sesi berhasil direset ke status Terjadwal.');
            location.reload();
        } else {
            alert(data.message || 'Gagal mereset sesi.');
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan jaringan saat memproses reset sesi.');
    });
}
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