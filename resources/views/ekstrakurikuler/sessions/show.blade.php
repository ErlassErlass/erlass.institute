@extends('layouts.app')

@section('title', 'Detail Sesi Ekstrakurikuler')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="text-decoration-none">
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
                
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('ekstrakurikuler-session.print-session', $session) }}" 
                       class="btn btn-outline-dark border" target="_blank">
                        <i class="bi bi-printer me-1"></i> Cetak Presensi
                    </a>

                    @if($session->canComplete())
                        <a href="{{ route('ekstrakurikuler.sessions.report.create', $session) }}" 
                           class="btn btn-primary">
                            <i class="bi bi-file-earmark-check me-1"></i> Buat Laporan & Absensi
                        </a>
                    @endif
                    
                    @can('update', $session)
                        @if(in_array($session->status, ['terjadwal', 'ditunda']))
                            <a href="{{ route('ekstrakurikuler.sessions.edit', $session) }}" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                        @endif

                        @if($session->status === 'selesai' && $session->laporan_mengajar_id)
                            <form action="{{ route('ekstrakurikuler.sessions.progress-remind', $session) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin mengirim ulang Pesan Pengingat Progress ke WhatsApp Orang Tua untuk siswa yang sudah menyelesaikan minimal 2 sesi berjalan?');">
                                    <i class="bi bi-whatsapp me-1"></i> Bagikan Progress Reminder
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    
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
                                    Sesi ini sudah melewati batas H+1. Anda masih memiliki <strong>{{ Auth::user()->monthly_late_report_quota }}</strong> kuota bantuan bulan ini untuk membuka laporan ini.
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
                        <div class="col-md-6">
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
                        
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Pertemuan Ke</label>
                            <p class="fs-5 mb-0 fw-medium">{{ $session->nomor_pertemuan }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Tanggal Terjadwal</label>
                            <p class="mb-0 fw-medium">
                                <i class="bi bi-calendar me-1 text-primary"></i>
                                {{ $session->tanggal_terjadwal->format('d/m/Y') }}
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Waktu Terjadwal</label>
                            <p class="mb-0 fw-medium">
                                <i class="bi bi-clock me-1 text-primary"></i>
                                {{ $session->jadwal_waktu }}
                                <span class="badge text-bg-light border ms-2">{{ $session->durasi_terjadwal }} menit</span>
                            </p>
                        </div>
                        
                        @if($session->tanggal_pelaksanaan)
                            <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold">Tanggal Pelaksanaan</label>
                                <p class="mb-0 fw-medium">
                                    {{ \Carbon\Carbon::parse($session->tanggal_pelaksanaan)->format('d/m/Y') }}
                                </p>
                            </div>
                        @endif
                        
                        @if($session->waktu_aktual)
                            <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold">Waktu Aktual</label>
                                <p class="mb-0 fw-medium">
                                    {{ $session->waktu_aktual }}
                                    @if($session->durasi_aktual)
                                        <span class="badge text-bg-light border ms-2">{{ $session->durasi_aktual }} menit</span>
                                    @endif
                                </p>
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
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Nama Program</label>
                            <p class="mb-0 fw-medium">{{ $session->rombel->ekstrakurikuler->kategori_program }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Rombel</label>
                            <p class="mb-0 fw-medium">{{ $session->rombel->nama_rombel }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Sekolah</label>
                            <p class="mb-0 fw-medium text-primary">{{ $session->rombel->ekstrakurikuler->sekolah->namasekolah }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Ruangan</label>
                            <p class="mb-0 fw-medium">{{ $session->rombel->ruangan ?? '-' }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Jumlah Siswa</label>
                            <p class="mb-0 fw-medium"><i class="bi bi-people me-1"></i> {{ $session->rombel->jumlah_siswa }} siswa</p>
                        </div>

                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Penanggung Jawab</label>
                            <p class="mb-0 fw-medium">{{ $session->rombel->ekstrakurikuler->penanggung_jawab ?? '-' }}</p>
                        </div>
                        
                        @if($session->rombel->ekstrakurikuler->no_telepon)
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">No. HP PJ</label>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <a href="tel:{{ $session->rombel->ekstrakurikuler->no_telepon }}" class="btn btn-sm btn-outline-secondary py-1 px-2">
                                    <i class="bi bi-telephone me-1"></i> {{ $session->rombel->ekstrakurikuler->no_telepon }}
                                </a>
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $session->rombel->ekstrakurikuler->no_telepon);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                    $waText = urlencode("Halo " . $session->rombel->ekstrakurikuler->penanggung_jawab . ", saya instruktur Erlass untuk ekstrakurikuler " . $session->rombel->ekstrakurikuler->kategori_program . ".");
                                @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2">
                                    <i class="bi bi-whatsapp me-1"></i> Chat WA
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($session->rombel->ekstrakurikuler->google_maps_link)
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Google Maps</label>
                            <div class="mt-1">
                                <a href="{{ $session->rombel->ekstrakurikuler->google_maps_link }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2">
                                    <i class="bi bi-geo-alt me-1"></i> Petunjuk Arah
                                </a>
                            </div>
                        </div>
                        @endif
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
                        @can('cancel', $session)
                            @if($session->canCancel())
                                <button type="button" class="btn btn-outline-danger text-start" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="bi bi-x-circle me-2"></i> Batalkan Sesi
                                </button>
                            @endif
                        @endcan
                        
                        @can('reschedule', $session)
                            @if($session->canReschedule())
                                <button type="button" class="btn btn-outline-warning text-dark text-start" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                                    <i class="bi bi-calendar2-range me-2"></i> Reschedule
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
                <div class="card shadow-sm border-0 bg-transparent">
                    <div class="card-body p-0">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3 ms-1">Riwayat Aktivitas</h6>
                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item bg-transparent d-flex align-items-start border-0 ps-0">
                                <i class="bi bi-circle-fill text-primary mt-1 me-2" style="font-size: 8px;"></i>
                                <div>
                                    <p class="mb-0 small fw-bold">Sesi dibuat</p>
                                    <small class="text-muted">{{ $session->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </li>
                            
                            @if($session->updated_at->ne($session->created_at))
                            <li class="list-group-item bg-transparent d-flex align-items-start border-0 ps-0">
                                <i class="bi bi-circle-fill text-warning mt-1 me-2" style="font-size: 8px;"></i>
                                <div>
                                    <p class="mb-0 small fw-bold">Terakhir diupdate</p>
                                    <small class="text-muted">{{ $session->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </li>
                            @endif
                            
                            @if($session->status === 'selesai')
                            <li class="list-group-item bg-transparent d-flex align-items-start border-0 ps-0">
                                <i class="bi bi-circle-fill text-success mt-1 me-2" style="font-size: 8px;"></i>
                                <div>
                                    <p class="mb-0 small fw-bold">Sesi selesai</p>
                                    <small class="text-muted">{{ $session->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </li>
                            @endif
                        </ul>
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
    document.getElementById('cancelForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const reason = document.getElementById('cancel_reason').value;
        
        // Disable button
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Memproses...';

        sendRequest(`/ekstrakurikuler/sessions/${sessionId}/cancel`, 'POST', { alasan_pembatalan: reason })
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
        const btn = document.getElementById('btnSendReminder');
        const spinner = btn.querySelector('.spinner-border');
        
        btn.disabled = true;
        spinner.classList.remove('d-none');
        
        sendRequest(`/ekstrakurikuler/sessions/${sessionId}/remind`, 'POST', { custom_message: message })
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
<div class="modal fade" id="reminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim Reminder Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reminderForm">
                <div class="modal-body">
                    <p>Kirim notifikasi WhatsApp ke instruktur: <strong>{{ $session->instruktur->nama_lengkap ?? 'Instruktur' }}</strong></p>
                    
                    <div class="mb-3">
                        <label for="customMessage" class="form-label">Pesan Tambahan (Opsional)</label>
                        <textarea class="form-control" id="customMessage" rows="3" placeholder="Contoh: Harap datang 15 menit lebih awal."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSendReminder">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Kirim Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@endsection