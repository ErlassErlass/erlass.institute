@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 fw-bold text-dark mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Selamat datang kembali, {{ Auth::user()->nama_lengkap }}</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex align-items-center gap-2 bg-white px-4 py-2 rounded-pill shadow-sm border">
                <i class="bi bi-calendar3 text-primary"></i>
                <span class="fw-medium text-dark">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>
    </div>
    
    <!-- Stats Section (Full Width at Top) -->
    @if(Auth::user()->role === 'instruktur')
        <!-- INSTRUCTOR PERSONAL STATS -->
        @include('dashboard.partials.instructor-stats')
    @else
        <!-- ADMIN STATS -->
        @include('dashboard.partials.admin-stats')
    @endif

    <!-- Quick Actions (Mobile Oriented) -->
    @if(Auth::user()->role === 'instruktur')
    <div class="row g-2 mb-4">
        <div class="col-4 col-md-3">
            <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="quick-action-btn btn-action-blue shadow-sm">
                <div class="btn-icon-wrapper">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <span class="small fw-bold text-dark">Jadwal</span>
            </a>
        </div>
        <div class="col-4 col-md-3">
            <a href="{{ route('laporan-mengajar.create') }}" class="quick-action-btn btn-action-green shadow-sm">
                <div class="btn-icon-wrapper">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <span class="small fw-bold text-dark">Laporan</span>
            </a>
        </div>
        <div class="col-4 col-md-3">
            <a href="{{ route('rekap-absensi') }}" class="quick-action-btn btn-action-cyan shadow-sm">
                <div class="btn-icon-wrapper">
                    <i class="bi bi-file-earmark-check"></i>
                </div>
                <span class="small fw-bold text-dark">Absen</span>
            </a>
        </div>
        <div class="col-12 col-md-3 d-none d-md-block">
            <a href="{{ route('laporan-mengajar.index') }}" class="quick-action-btn btn-action-warning shadow-sm">
                <div class="btn-icon-wrapper">
                    <i class="bi bi-clock-history"></i>
                </div>
                <span class="small fw-bold text-dark">Riwayat</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Profile Completion Alert -->
    @if(isset($incomplete_profile) && $incomplete_profile)
    <div class="alert premium-alert-warning border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-start">
            <div class="premium-icon-warning rounded-circle p-2 me-3 align-self-start d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            </div>
            <div>
                <h4 class="alert-heading h6 fw-bold mb-1 text-dark">Profil Belum Lengkap!</h4>
                <p class="mb-2 small text-dark opacity-75">
                    Mohon lengkapi data berikut agar akun Anda dapat diverifikasi dan pembayaran honor dapat diproses:
                </p>
                @if(isset($missing_fields) && count($missing_fields) > 0)
                    <ul class="mb-2 small text-danger fw-semibold ps-3">
                        @foreach($missing_fields as $field)
                            <li>{{ $field }}</li>
                        @endforeach
                    </ul>
                @endif
                <a href="{{ route('instructor.profile.complete') }}" class="btn btn-sm px-4 py-2 text-white fw-bold premium-icon-warning border-0 rounded-pill mt-1" style="transition: all 0.2s;">
                    Lengkapi Sekarang <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- IMPORTANT: Report Usage Warning -->
    @if(Auth::user()->role === 'instruktur')
    <div class="alert premium-alert-danger border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-start">
            <div class="premium-icon-danger rounded-circle p-2 me-3 align-self-start d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                <i class="bi bi-megaphone-fill fs-5"></i>
            </div>
            <div>
                <h5 class="alert-heading h6 fw-bold mb-2 text-dark">PENTING: CARA PELAPORAN MENGAJAR</h5>
                <p class="mb-0 small text-dark opacity-75">Mohon perhatikan perbedaan cara pelaporan berikut:</p>
                <ul class="mb-0 mt-2 small text-dark ps-3">
                    <li class="mb-1">
                        <strong>Kelas Rutin / Terjadwal:</strong> WAJIB melalui menu 
                        <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="fw-bold text-decoration-underline text-danger">Jadwal Mengajar</a>. 
                        (Data siswa & rombel terisi otomatis).
                    </li>
                    <li>
                        <strong>Kelas Tambahan / Ad-Hoc:</strong> Gunakan menu 
                        <a href="{{ route('laporan-mengajar.create') }}" class="fw-bold text-decoration-underline text-danger">Buat Laporan Baru</a>. 
                        (KHUSUS untuk Pameran, Lomba, Sosialisasi atau Pendampingan).
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Left/Main Column -->
        <div class="col-lg-8 col-12">
            <!-- Today's Schedule (Visible to ALL roles) -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar-day me-2 text-primary"></i>Jadwal Hari Ini 
                        <span class="text-muted fs-6 ms-2">({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }})</span>
                    </h5>
                    @if(isset($todays_schedule))
                        <span class="badge bg-primary rounded-pill">{{ $todays_schedule->count() }} Sesi</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if(isset($todays_schedule) && $todays_schedule->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Jam</th>
                                        <th>Sekolah & Program</th>
                                        <th>Instruktur</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todays_schedule as $session)
                                        <tr>
                                            <td class="ps-4 fw-bold text-nowrap" style="width: 120px;">
                                                {{ $session->jam_mulai_terjadwal->format('H:i') }} - {{ $session->jam_selesai_terjadwal->format('H:i') }}
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                    <span>{{ $session->rombel->ekstrakurikuler->sekolah->namasekolah }}</span>
                                                    @if($session->rombel->ekstrakurikuler->google_maps_link)
                                                        <a href="{{ $session->rombel->ekstrakurikuler->google_maps_link }}" target="_blank" class="text-primary d-inline-flex align-items-center" title="Buka Google Maps">
                                                            <i class="bi bi-geo-alt-fill"></i>
                                                        </a>
                                                    @endif
                                                    @if($session->rombel->ekstrakurikuler->no_telepon)
                                                        @php
                                                            $cleanPhone = preg_replace('/[^0-9]/', '', $session->rombel->ekstrakurikuler->no_telepon);
                                                            if (str_starts_with($cleanPhone, '0')) {
                                                                $cleanPhone = '62' . substr($cleanPhone, 1);
                                                            }
                                                            $waText = urlencode("Halo " . $session->rombel->ekstrakurikuler->penanggung_jawab . ", saya instruktur Erlass untuk ekstrakurikuler " . $session->rombel->ekstrakurikuler->kategori_program . ".");
                                                        @endphp
                                                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" rel="noopener" class="text-success d-inline-flex align-items-center" title="WhatsApp PJ: {{ $session->rombel->ekstrakurikuler->penanggung_jawab }}">
                                                            <i class="bi bi-whatsapp"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $session->rombel->ekstrakurikuler->kategori_program }} - {{ $session->rombel->nama_rombel }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($session->instruktur)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                            <span style="font-size: 10px;">{{ substr($session->instruktur->nama_lengkap, 0, 1) }}</span>
                                                        </div>
                                                        <span class="text-dark">{{ $session->instruktur->nama_lengkap }}</span>
                                                    </div>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger">Belum Ada Instruktur</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
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
                                                <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }} px-3 rounded-pill">
                                                    {{ $session->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('ekstrakurikuler.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-calendar-x fs-1 mb-3 d-block text-secondary"></i>
                            <p class="mb-0">Tidak ada jadwal kegiatan hari ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Schedule (Instructors only) -->
            @if(Auth::user()->role === 'instruktur')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar-week me-2 text-primary"></i>Agenda Mendatang 
                        <span class="text-muted fs-6 ms-2">(3 Hari ke Depan)</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($upcoming_schedule) && $upcoming_schedule->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <tbody class="border-top-0">
                                    @foreach($upcoming_schedule as $date => $sessions)
                                        <tr class="table-light">
                                            <td colspan="5" class="fw-bold ps-4 text-primary bg-primary-subtle">
                                                {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                            </td>
                                        </tr>
                                        @foreach($sessions as $session)
                                        <tr>
                                            <td class="ps-4 text-nowrap" style="width: 100px;">
                                                {{ $session->jam_mulai_terjadwal->format('H:i') }}
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                    <span>{{ $session->rombel->ekstrakurikuler->sekolah->namasekolah }}</span>
                                                    @if($session->rombel->ekstrakurikuler->google_maps_link)
                                                        <a href="{{ $session->rombel->ekstrakurikuler->google_maps_link }}" target="_blank" class="text-primary d-inline-flex align-items-center" title="Buka Google Maps">
                                                            <i class="bi bi-geo-alt-fill"></i>
                                                        </a>
                                                    @endif
                                                    @if($session->rombel->ekstrakurikuler->no_telepon)
                                                        @php
                                                            $cleanPhone = preg_replace('/[^0-9]/', '', $session->rombel->ekstrakurikuler->no_telepon);
                                                            if (str_starts_with($cleanPhone, '0')) {
                                                                $cleanPhone = '62' . substr($cleanPhone, 1);
                                                            }
                                                            $waText = urlencode("Halo " . $session->rombel->ekstrakurikuler->penanggung_jawab . ", saya instruktur Erlass untuk ekstrakurikuler " . $session->rombel->ekstrakurikuler->kategori_program . ".");
                                                        @endphp
                                                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" rel="noopener" class="text-success d-inline-flex align-items-center" title="WhatsApp PJ: {{ $session->rombel->ekstrakurikuler->penanggung_jawab }}">
                                                            <i class="bi bi-whatsapp"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $session->rombel->ekstrakurikuler->kategori_program }} - {{ $session->rombel->nama_rombel }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                 <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 rounded-pill">
                                                    Terjadwal
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('ekstrakurikuler.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-calendar-check fs-1 mb-3 d-block text-secondary"></i>
                            <p class="mb-0">Tidak ada jadwal dalam 3 hari ke depan.</p>
                        </div>
                    @endif
                    <div class="card-footer bg-white border-top p-3 text-center">
                        <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="text-decoration-none fw-semibold">
                            Lihat Jadwal Lengkap <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Verification Center (Admin Only) -->
            @if(Auth::user()->role === 'admin_sistem' || Auth::user()->role === 'webmaster' || Auth::user()->role === 'admin')
                <!-- Admin Monitoring: Pending Reports -->
                @if(isset($admin_pending_reports) && $admin_pending_reports->count() > 0)
                <div class="card border-info border-start border-4 shadow-sm mb-4">
                    <div class="card-header bg-info-subtle text-info-emphasis fw-bold d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <span><i class="bi bi-clipboard-data me-2"></i>MONITORING: BELUM LAPOR ({{ $admin_pending_reports->count() }} Teratas)</span>
                        <small class="text-muted fst-italic">Urut Deadline</small>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($admin_pending_reports as $report)
                            <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 p-3">
                                <div class="w-100 w-sm-auto">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h6 class="mb-0 fw-bold text-dark">
                                            {{ $report->instruktur->nama_lengkap ?? 'Tanpa Instruktur' }}
                                        </h6>
                                        <span class="badge bg-secondary rounded-pill" style="font-size: 0.7rem;">P.{{ $report->nomor_pertemuan }}</span>
                                        @if($report->isPast())
                                            @php
                                                $waktuRef = $report->waktu_selesai_full ?? $report->tanggal_terjadwal;
                                            @endphp
                                            <span class="badge bg-danger" style="font-size: 0.7rem;">Terlambat {{ $waktuRef->diffForHumans() }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Hari Ini</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        <span class="fw-bold text-primary">{{ $report->rombel->ekstrakurikuler->kategori_program }}</span>
                                        <span class="mx-1">•</span>
                                        {{ $report->rombel->ekstrakurikuler->sekolah->namasekolah }}
                                        <br>
                                        <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($report->tanggal_terjadwal)->format('d M Y') }}
                                        @if($report->jam_mulai_terjadwal && $report->jam_selesai_terjadwal)
                                            <span class="mx-1">•</span>
                                            <i class="bi bi-clock me-1"></i> {{ $report->jadwal_waktu }}
                                        @endif
                                    </div>
                                </div>
                                <div class="w-100 w-sm-auto text-end text-sm-start">
                                    @php
                                        $cleanInstrukturPhone = '';
                                        if (!empty($report->instruktur->no_telephone)) {
                                            $cleanInstrukturPhone = preg_replace('/[^0-9]/', '', $report->instruktur->no_telephone);
                                            if (str_starts_with($cleanInstrukturPhone, '0')) {
                                                $cleanInstrukturPhone = '62' . substr($cleanInstrukturPhone, 1);
                                            }
                                        }
                                        $waMsgText = urlencode("Halo " . ($report->instruktur->nama_lengkap ?? '') . ", mohon segera laporan sesi " . ($report->rombel->ekstrakurikuler->kategori_program ?? '') . " di " . ($report->rombel->ekstrakurikuler->sekolah->namasekolah ?? '') . " tanggal " . ($report->tanggal_terjadwal ? $report->tanggal_terjadwal->format('d/m') : '') . ".");
                                    @endphp
                                    <a @if(!empty($cleanInstrukturPhone)) href="https://wa.me/{{ $cleanInstrukturPhone }}?text={{ $waMsgText }}" target="_blank" rel="noopener" @else href="javascript:void(0)" style="pointer-events: none; opacity: 0.65;" @endif 
                                       class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm w-100 w-sm-auto {{ empty($cleanInstrukturPhone) ? 'disabled' : '' }}">
                                        <i class="bi bi-whatsapp me-1"></i> Ingatkan
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-check me-2 text-primary"></i>Pusat Verifikasi</h5>
                            <span class="badge bg-primary rounded-pill">Admin Area</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="{{ route('siswa.index', ['temp_nisn' => 1]) }}" class="card text-decoration-none border shadow-sm h-100 {{ $pending_students > 0 ? 'bg-white' : 'bg-light' }}">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning position-relative">
                                            <i class="bi bi-person-exclamation fs-4"></i>
                                            @if($pending_students > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-white p-1">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">Siswa Perlu NISN</h6>
                                            <small class="text-muted">Siswa ditambah manual (TMP)</small>
                                        </div>
                                        <div class="ms-auto">
                                            <span class="badge bg-{{ $pending_students > 0 ? 'danger' : 'secondary' }} rounded-pill">{{ $pending_students }} Pending</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('users.index', ['role' => 'instruktur', 'status' => 'pending']) }}" class="card text-decoration-none border shadow-sm h-100 {{ $pending_instruktur > 0 ? 'bg-white' : 'bg-light' }}">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info position-relative">
                                            <i class="bi bi-person-badge fs-4"></i>
                                            @if($pending_instruktur > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-white p-1">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">Instruktur Baru</h6>
                                            <small class="text-muted">Menunggu verifikasi profil</small>
                                        </div>
                                        <div class="ms-auto">
                                            <span class="badge bg-{{ $pending_instruktur > 0 ? 'danger' : 'secondary' }} rounded-pill">{{ $pending_instruktur }} Pending</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="card text-decoration-none border shadow-sm {{ $pending_sessions_no_instructor > 0 ? 'bg-danger bg-opacity-10 border-danger' : 'bg-light' }}">
                                    <div class="card-body d-flex flex-column gap-3">
                                        <div class="d-flex align-items-center gap-3 w-100">
                                            <div class="bg-white p-3 rounded-circle text-danger position-relative border border-danger shadow-sm flex-shrink-0">
                                                <i class="bi bi-calendar-x fs-4"></i>
                                                @if($pending_sessions_no_instructor > 0)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-white p-1">
                                                    <span class="visually-hidden">Urgent</span>
                                                </span>
                                                @endif
                                            </div>
                                            <div>
                                                <h5 class="h6 mb-1 fw-bold text-dark">Jadwal Tanpa Instruktur</h5>
                                                @if($pending_sessions_no_instructor > 0)
                                                    <small class="text-danger fw-semibold">
                                                        {{ $pending_sessions_no_instructor }} Sesi Belum Ada Pengajar!
                                                    </small>
                                                @else
                                                    <small class="text-muted">Semua jadwal aman.</small>
                                                @endif
                                            </div>
                                        </div>
                                        @if($pending_sessions_no_instructor > 0)
                                            <div class="w-100">
                                                <div class="mt-1">
                                                    @foreach($urgent_sessions_list as $session)
                                                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center bg-white p-3 rounded border border-danger border-opacity-25 mb-2 gap-2" style="font-size: 0.875rem;">
                                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                <span class="badge bg-danger">
                                                                    {{ $session->tanggal_terjadwal->format('d M') }}
                                                                </span>
                                                                <span class="text-dark fw-semibold">
                                                                    {{ $session->rombel->ekstrakurikuler->sekolah->namasekolah ?? 'Sekolah' }} - 
                                                                    {{ $session->rombel->ekstrakurikuler->kategori_program ?? 'Program' }} 
                                                                    ({{ $session->rombel->nama_rombel ?? 'Rombel' }})
                                                                </span>
                                                            </div>
                                                            <div class="text-end text-sm-start w-100 w-sm-auto">
                                                                <a href="{{ route('ekstrakurikuler.sessions.edit', $session->id) }}" class="btn btn-xs btn-outline-danger py-1 px-3 w-100 w-sm-auto text-center" style="font-size: 0.75rem;">
                                                                    Assign <i class="bi bi-arrow-right"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="text-end text-sm-start mt-3">
                                                    <a href="{{ route('ekstrakurikuler.sessions.index', ['filter_no_instructor' => 1]) }}" class="btn btn-sm btn-danger w-100 w-sm-auto">
                                                        Lihat Semua
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning QC Panel -->
                <div class="row g-4 mb-4">
                    <div class="col-md-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold text-dark">
                                    <i class="bi bi-shield-fill-exclamation text-danger me-2"></i>Log Warning Quality Control
                                </h5>
                                <span class="badge bg-danger rounded-pill">{{ $warning_merah + $warning_kuning }} Aktif</span>
                            </div>
                            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                                @if(isset($warning_list) && $warning_list->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($warning_list as $warning)
                                            <div class="list-group-item p-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3" style="border-left: 4px solid {{ $warning->severity === 'red' ? '#f43f5e' : '#f59e0b' }} !important; background-color: {{ $warning->severity === 'red' ? 'rgba(244, 63, 94, 0.05)' : 'rgba(245, 158, 11, 0.05)' }};">
                                                <div class="d-flex gap-3">
                                                    <div class="flex-shrink-0 mt-1">
                                                        @if($warning->severity === 'red')
                                                            <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                                        @else
                                                            <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                            <span class="badge bg-{{ $warning->severity === 'red' ? 'danger' : 'warning text-dark' }} text-uppercase font-monospace" style="font-size: 0.65rem;">
                                                                {{ str_replace('_', ' ', $warning->warning_type) }}
                                                            </span>
                                                            <small class="text-muted">{{ $warning->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="mb-0 text-dark small fw-medium">{{ $warning->notes }}</p>
                                                    </div>
                                                </div>
                                                <div class="w-100 w-sm-auto text-end text-sm-start flex-shrink-0">
                                                    <form action="{{ route('admin.warnings.resolve', $warning->id) }}" method="POST" class="d-grid d-sm-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-outline-success py-1 px-2 rounded w-100 w-sm-auto" style="font-size: 0.75rem;">
                                                            <i class="bi bi-check2"></i> Resolve
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-5 text-center text-muted">
                                        <i class="bi bi-shield-check text-success fs-1 mb-3 d-block"></i>
                                        <h6 class="fw-bold mb-1 text-dark">Semua Sistem Berjalan Normal</h6>
                                        <p class="mb-0 small text-secondary">Tidak ada peringatan QC aktif saat ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Navigation -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-grid-fill me-2 text-primary"></i> Menu Navigasi</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        @if(Auth::user()->role !== 'instruktur')
                        <div class="col-md-6">
                            <a href="{{ route('sekolah.index') }}" class="card text-decoration-none shadow-sm h-100 border card-hover bg-white">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                                        <i class="bi bi-building fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Database Sekolah</h6>
                                        <small class="text-muted">Kelola data sekolah & siswa</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <a href="{{ route('laporan-mengajar.index') }}" class="card text-decoration-none shadow-sm h-100 border card-hover bg-white">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                                        <i class="bi bi-journal-check fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Riwayat Laporan</h6>
                                        <small class="text-muted">Arsip & histori laporan</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('rekap-absensi') }}" class="card text-decoration-none shadow-sm h-100 border card-hover bg-white">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                                        <i class="bi bi-calendar-check fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Rekap Kehadiran</h6>
                                        <small class="text-muted">Data absensi siswa</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @if(Auth::user()->role !== 'instruktur')
                        <div class="col-md-6">
                            <a href="{{ route('siswa.index') }}" class="card text-decoration-none shadow-sm h-100 border card-hover bg-white">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                                        <i class="bi bi-person-lines-fill fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Database Siswa</h6>
                                        <small class="text-muted">Database seluruh siswa</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @else
                        <!-- Ad-Hoc Report for Instructors -->
                        <div class="col-md-6">
                            <a href="{{ route('laporan-mengajar.create') }}" class="card text-decoration-none shadow-sm h-100 border card-hover bg-white">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger">
                                        <i class="bi bi-journal-plus fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Laporan Ad-Hoc</h6>
                                        <small class="text-muted">Pameran, Lomba, Sosialisasi</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="card text-decoration-none shadow-sm h-100 border card-hover bg-white">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                                        <i class="bi bi-calendar-week fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Jadwal & Laporan</h6>
                                        <small class="text-muted">Isi laporan & absen di sini</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
                        <!-- Admin Tools -->
                        <div class="col-md-6">
                            <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="card text-decoration-none shadow-sm h-100 border card-hover bg-white">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                                        <i class="bi bi-calendar-check fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Manajemen Jadwal</h6>
                                        <small class="text-muted">Atur sesi & ploting instruktur</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.broadcast.create') }}" class="card text-decoration-none shadow-sm h-100 border card-hover bg-white">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger">
                                        <i class="bi bi-megaphone-fill fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Broadcast</h6>
                                        <small class="text-muted">Kirim pengumuman massal</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @if(auth()->user()->hasRole(['webmaster', 'admin_sistem']))
                        <div class="col-md-6">
                            <a href="{{ route('admin.users.index') }}" class="card text-decoration-none shadow-sm h-100 border card-hover bg-white">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                                        <i class="bi bi-people-fill fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Users & Staff</h6>
                                        <small class="text-muted">Kelola pengguna & staf</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Live Activities Feed -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="spinner-grow text-success spinner-grow-sm" role="status"></div>
                        <h5 class="mb-0 fw-bold text-dark">Live Activity</h5>
                    </div>
                    <small class="text-muted">Real-time update</small>
                </div>
                <div class="card-body p-0">
                    @if(isset($recent_activities) && $recent_activities->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recent_activities as $activity)
                        <div class="list-group-item border-bottom px-4 py-3 hover-bg-light">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle {{ $activity['bg'] }} d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="{{ $activity['icon'] }} {{ $activity['color'] }} fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-1 gap-1">
                                        <h6 class="mb-0 fw-bold text-dark">{{ $activity['title'] }}</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                                        </small>
                                    </div>
                                    <p class="mb-1 text-muted small text-truncate" style="max-width: 100%;">
                                        {{ $activity['desc'] }}
                                    </p>
                                    <a href="{{ $activity['link'] }}" class="text-decoration-none text-primary small fw-semibold">
                                        Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-activity fs-1 text-muted opacity-25"></i>
                        </div>
                        <h6 class="text-muted">Belum ada aktivitas terbaru</h6>
                        <small class="text-secondary">Aktivitas mengajar dan program akan muncul di sini.</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right/Sidebar Column -->
        <div class="col-lg-4 col-12">
            @if(Auth::user()->role === 'instruktur')
                <!-- Instructor To-Do List (Urgent Reports) - Wajib Dilaporkan -->
                @if(isset($instructor_todo_list) && $instructor_todo_list->count() > 0)
                <div class="card border-warning border-start border-4 shadow-sm mb-4">
                    <div class="card-header bg-warning-subtle text-warning-emphasis fw-bold d-flex justify-content-between align-items-center py-3">
                        <span class="d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill text-warning me-2 fs-5"></i>WAJIB DILAPORKAN ({{ $instructor_todo_list->count() }})</span>
                    </div>
                    <div class="card-body p-0 todo-scrollable" style="max-height: 450px; overflow-y: auto;">
                        <div class="list-group list-group-flush">
                            @foreach($instructor_todo_list as $todo)
                                <div class="list-group-item p-3 border-bottom hover-bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 75%;" title="{{ $todo->rombel->ekstrakurikuler->kategori_program }}">
                                            {{ $todo->rombel->ekstrakurikuler->kategori_program }}
                                        </h6>
                                        <span class="badge bg-secondary rounded-pill" style="font-size: 0.7rem;">P.{{ $todo->nomor_pertemuan }}</span>
                                    </div>
                                    <div class="text-muted small mb-3">
                                        <div class="mb-1 text-truncate" title="{{ $todo->rombel->ekstrakurikuler->sekolah->namasekolah }}">
                                            <i class="bi bi-building me-1"></i> {{ $todo->rombel->ekstrakurikuler->sekolah->namasekolah }}
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                            <span>
                                                <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($todo->tanggal_terjadwal)->format('d M Y') }}
                                                @if($todo->jam_mulai_terjadwal && $todo->jam_selesai_terjadwal)
                                                    <span class="mx-1">•</span>
                                                    <i class="bi bi-clock me-1"></i> {{ $todo->jadwal_waktu }}
                                                @endif
                                            </span>
                                            @if($todo->isPast())
                                                @php
                                                    $waktuRefTodo = $todo->waktu_selesai_full ?? $todo->tanggal_terjadwal;
                                                @endphp
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle" style="font-size: 0.65rem;">
                                                    Terlambat {{ $waktuRefTodo->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle" style="font-size: 0.65rem;">
                                                    Hari Ini
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('ekstrakurikuler.sessions.report.create', $todo->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm w-100 d-flex align-items-center justify-content-center">
                                        Buat Laporan <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Emergency Helpdesk / Bantuan Darurat (AOQCS Pillar 1) -->
                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center">
                            <i class="bi bi-chat-dots-fill text-success me-2"></i> Bantuan & Kontak Darurat
                        </h6>
                        <p class="text-muted small mb-3">
                            Mengalami kendala saat mengajar atau butuh bantuan admin akademik Erlass? Hubungi kami langsung.
                        </p>
                        @php
                            $waAdminPhone = '6282114631380'; // Nomor helpdesk Erlass
                            $waAdminText = urlencode("Halo Admin Akademik Erlass, saya " . Auth::user()->nama_lengkap . " (Instruktur). Saya butuh bantuan terkait operasional mengajar.");
                        @endphp
                        <a href="https://wa.me/{{ $waAdminPhone }}?text={{ $waAdminText }}" target="_blank" rel="noopener" class="btn btn-success w-100 rounded-pill d-flex align-items-center justify-content-center gap-2 shadow-sm fw-bold">
                            <i class="bi bi-whatsapp"></i> Chat Admin Akademik
                        </a>
                    </div>
                </div>
            @else
                <!-- Charts Partial (Admin Only) -->
                @include('dashboard.partials.charts')

                <!-- School Distribution (Admin/Webmaster only) -->
                @if(auth()->user()?->hasAdminAccess())
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom px-4 py-3">
                        <h5 class="mb-0 fw-bold text-dark">Distribusi Siswa</h5>
                    </div>
                    <div class="card-body p-4">
                        @if(isset($sekolah_distribution) && $sekolah_distribution->count() > 0)
                        <div class="mb-4 d-flex flex-column gap-3">
                            @foreach($sekolah_distribution as $sekolah)
                            <div>
                                <div class="d-flex justify-content-between mb-1 align-items-end">
                                    <span class="fw-medium text-dark small">{{ Str::limit($sekolah->namasekolah, 25) }}</span>
                                    <span class="fw-bold text-primary small">{{ $sekolah->siswa_count }}</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar rounded-pill"
                                        role="progressbar"
                                        style="width: {{ ($sekolah->siswa_count / max(1, $total_siswa)) * 100 }}%; 
                                                    background-color: #0d6efd;"
                                        aria-valuenow="{{ ($sekolah->siswa_count / max(1, $total_siswa)) * 100 }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-2">
                            <a href="{{ route('sekolah.distribusi') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Lihat Semua Sekolah <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="bi bi-info-circle fs-1 text-muted opacity-50"></i>
                            <p class="text-muted mt-2">Data sekolah tidak tersedia</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Certificate & Rapor Widget (Admin only) -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-patch-check-fill text-primary me-2"></i>Log Rapor & Sertifikat
                        </h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success bg-opacity-10 p-2 rounded text-success">
                                        <i class="bi bi-patch-check fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold small text-dark">Sertifikat Terbit</h6>
                                        <small class="text-muted">Issued</small>
                                    </div>
                                </div>
                                <span class="fs-5 fw-bold text-success">{{ $sertifikat_issued }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-warning bg-opacity-10 p-2 rounded text-warning">
                                        <i class="bi bi-hourglass-split fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold small text-dark">Sertifikat Pending</h6>
                                        <small class="text-muted">Siswa tidak eligible / belum final</small>
                                    </div>
                                </div>
                                <span class="fs-5 fw-bold text-warning">{{ $sertifikat_pending }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-info bg-opacity-10 p-2 rounded text-info">
                                        <i class="bi bi-file-earmark-pdf fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold small text-dark">Rapor Tergenerasi</h6>
                                        <small class="text-muted">PDF di storage</small>
                                    </div>
                                </div>
                                <span class="fs-5 fw-bold text-info">{{ $rapor_generated }}</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('certificates.index') }}" class="btn btn-primary w-100">
                                Kelola Rapor & Sertifikat <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Stats & Server Time -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="mb-0 fw-bold text-dark">Statistik Singkat</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0 fw-semibold text-dark">Total Laporan</h6>
                            <small class="text-muted">Semua waktu</small>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">
                            {{ $total_laporan ?? 0 }}
                        </span>
                    </div>

                    @if(Auth::user()->role === 'instruktur')
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0 fw-semibold text-dark">Laporan Anda</h6>
                            <small class="text-muted">Total yang dibuat</small>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                            {{ $total_laporan_instruktur }}
                        </span>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0 fw-semibold text-dark">Rata-rata Siswa</h6>
                            <small class="text-muted">Per sekolah</small>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">
                            {{ $total_sekolah > 0 ? round($total_siswa / $total_sekolah) : 0 }}
                        </span>
                    </div>
                    
                    <div class="p-3 bg-light rounded-3 mt-4 text-center border">
                        <small class="text-muted d-block mb-1">Waktu Server</small>
                        <h5 class="fw-bold text-dark mb-0 animate-time">{{ now()->format('H:i:s') }}</h5>
                        <small class="text-primary">{{ now()->translatedFormat('d/m/Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom Scrollbar for To-Do List */
    .todo-scrollable {
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(217, 119, 6, 0.3) transparent;
    }
    .todo-scrollable::-webkit-scrollbar {
        width: 6px;
    }
    .todo-scrollable::-webkit-scrollbar-track {
        background: transparent;
    }
    .todo-scrollable::-webkit-scrollbar-thumb {
        background-color: rgba(217, 119, 6, 0.3);
        border-radius: 10px;
    }
    .todo-scrollable::-webkit-scrollbar-thumb:hover {
        background-color: rgba(217, 119, 6, 0.5);
    }

    .card-hover {
        transition: all 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .card-hover:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        border-color: rgba(0, 0, 0, 0.1);
    }
    .border-start {
        border-left-width: 4px !important;
    }
    .progress {
        border-radius: 10rem;
        background-color: #f0f3f7;
    }
    .progress-bar {
        border-radius: 10rem;
    }

    /* Premium Alert Styles */
    .premium-alert-warning {
        background: linear-gradient(135deg, #fffbeb 0%, #fffbeb 100%) !important;
        border: 1px solid rgba(245, 158, 11, 0.2) !important;
        border-radius: 16px !important;
        position: relative;
        overflow: hidden;
    }
    .premium-alert-warning::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #f59e0b, #d97706);
    }
    .premium-icon-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.25);
    }
    
    .premium-alert-danger {
        background: linear-gradient(135deg, #fff1f2 0%, #fff1f2 100%) !important;
        border: 1px solid rgba(244, 63, 94, 0.18) !important;
        border-radius: 16px !important;
        position: relative;
        overflow: hidden;
    }
    .premium-alert-danger::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #f43f5e, #e11d48);
    }
    .premium-icon-danger {
        background: linear-gradient(135deg, #f43f5e, #e11d48);
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(244, 63, 94, 0.25);
    }

    /* Premium Quick Action Buttons */
    .quick-action-btn {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.25rem 0.75rem;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        height: 100%;
        position: relative;
    }
    .quick-action-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.12);
        background: #ffffff;
    }
    .quick-action-btn .btn-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }
    .quick-action-btn:hover .btn-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }
    
    .btn-action-blue {
        color: #2563eb;
    }
    .btn-action-blue .btn-icon-wrapper {
        background-color: rgba(37, 99, 235, 0.08);
        color: #2563eb;
    }
    .btn-action-blue:hover {
        border-color: rgba(37, 99, 235, 0.3);
    }
    
    .btn-action-green {
        color: #10b981;
    }
    .btn-action-green .btn-icon-wrapper {
        background-color: rgba(16, 185, 129, 0.08);
        color: #10b981;
    }
    .btn-action-green:hover {
        border-color: rgba(16, 185, 129, 0.3);
    }
    
    .btn-action-cyan {
        color: #06b6d4;
    }
    .btn-action-cyan .btn-icon-wrapper {
        background-color: rgba(6, 182, 212, 0.08);
        color: #06b6d4;
    }
    .btn-action-cyan:hover {
        border-color: rgba(6, 182, 212, 0.3);
    }
    
    .btn-action-warning {
        color: #d97706;
    }
    .btn-action-warning .btn-icon-wrapper {
        background-color: rgba(217, 119, 6, 0.08);
        color: #d97706;
    }
    .btn-action-warning:hover {
        border-color: rgba(217, 119, 6, 0.3);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Activity Chart (Existing)
        @if(isset($chart_labels) && isset($chart_values))
        const ctx = document.getElementById('activityChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.2)');
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chart_labels) !!},
                datasets: [{
                    label: 'Laporan Masuk',
                    data: {!! json_encode($chart_values) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5],
                            drawBorder: false
                        },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
        @endif

        // 2. Attendance Chart (New)
        @if(isset($attendanceLabels) && isset($attendanceValues))
        const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctxAttendance, {
            type: 'bar',
            data: {
                labels: {!! json_encode($attendanceLabels) !!},
                datasets: [{
                    label: 'Kehadiran (%)',
                    data: {!! json_encode($attendanceValues) !!},
                    backgroundColor: '#198754', // Success color
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            borderDash: [5, 5],
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
        @endif
        
        // 3. Time Animation
        function updateTime() {
            const timeElement = document.querySelector('.animate-time');
            if (timeElement) {
                const now = new Date();
                timeElement.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
            }
        }
        setInterval(updateTime, 1000);
    });
</script>
@endpush