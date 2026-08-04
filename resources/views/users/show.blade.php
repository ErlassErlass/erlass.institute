@extends('layouts.app')

@section('title', 'Detail User — ' . $user->nama_lengkap)

@section('content')
<div class="container-fluid py-4">

    {{-- ═══════════ HEADER CARD ═══════════ --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    {{-- Avatar Circle --}}
                    @php
                        $initials = collect(explode(' ', $user->nama_lengkap))->take(2)->map(fn($n) => strtoupper(substr($n,0,1)))->join('');
                        $avatarColors = [
                            'webmaster' => 'bg-danger',
                            'admin_sistem' => 'bg-warning text-dark',
                            'admin' => 'bg-info',
                            'instruktur' => 'bg-success',
                        ];
                        $avatarBg = $avatarColors[$user->role] ?? 'bg-secondary';
                    @endphp
                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $avatarBg }}" style="width: 56px; height: 56px; font-size: 1.25rem; font-weight: 700; color: {{ $user->role === 'admin_sistem' ? '#000' : '#fff' }};">
                        {{ $initials }}
                    </div>
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-0">{{ $user->nama_lengkap }}</h1>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            @php
                                $roleClass = match($user->role) {
                                    'webmaster' => 'bg-danger',
                                    'admin_sistem' => 'bg-warning text-dark',
                                    'admin' => 'bg-info',
                                    'instruktur' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $roleClass }} px-2 py-1">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                            <span class="badge {{ $user->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }} px-2 py-1">{{ $user->status }}</span>
                            @if($user->role === 'instruktur')
                                @php
                                    $verBadge = match($user->verification_status) {
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'pending' => 'bg-warning text-dark',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $verBadge }} px-2 py-1"><i class="bi bi-shield-check me-1"></i>{{ ucfirst($user->verification_status ?: 'Unverified') }}</span>
                            @endif
                            <span class="text-muted small">#{{ $user->id }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @can('update', $user)
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning fw-bold">
                            <i class="bi bi-pencil-square me-1"></i> Edit Profil
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ TAB NAVIGATION ═══════════ --}}
    <ul class="nav nav-tabs mb-0" id="userDetailTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="tab-profile" data-bs-toggle="tab" data-bs-target="#pane-profile" type="button" role="tab">
                <i class="bi bi-person-fill me-1"></i> Profil & Data
            </button>
        </li>
        @if($user->role === 'instruktur')
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="tab-sessions" data-bs-toggle="tab" data-bs-target="#pane-sessions" type="button" role="tab">
                    <i class="bi bi-calendar-check me-1"></i> Riwayat Sesi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="tab-payroll" data-bs-toggle="tab" data-bs-target="#pane-payroll" type="button" role="tab">
                    <i class="bi bi-cash-stack me-1"></i> Riwayat Payroll
                </button>
            </li>
        @endif
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="tab-activity" data-bs-toggle="tab" data-bs-target="#pane-activity" type="button" role="tab">
                <i class="bi bi-clock-history me-1"></i> Log Aktivitas
            </button>
        </li>
    </ul>

    {{-- ═══════════ TAB CONTENT ═══════════ --}}
    <div class="tab-content border border-top-0 rounded-bottom bg-white shadow-sm">

        {{-- ────── TAB 1: PROFIL & DATA ────── --}}
        <div class="tab-pane fade show active p-4" id="pane-profile" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">

                    {{-- Informasi Dasar --}}
                    <div class="card border mb-4">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2"></i>Informasi Dasar</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><td class="fw-bold" style="width:40%">ID User</td><td>: <span class="font-monospace text-muted">#{{ $user->id }}</span></td></tr>
                                        <tr><td class="fw-bold">Nama Lengkap</td><td>: {{ $user->nama_lengkap }}</td></tr>
                                        <tr><td class="fw-bold">Email</td><td>: <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a></td></tr>
                                        <tr><td class="fw-bold">Role</td><td>: <span class="badge {{ $roleClass }}">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span></td></tr>
                                        <tr><td class="fw-bold">Status</td><td>: <span class="badge {{ $user->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">{{ $user->status }}</span></td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><td class="fw-bold" style="width:40%">Tanggal Lahir</td><td>: {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d/m/Y') : '-' }}</td></tr>
                                        <tr><td class="fw-bold">No Telepon</td><td>: {{ $user->no_telephone ?: '-' }}</td></tr>
                                        <tr><td class="fw-bold">Agama</td><td>: {{ $user->agama ?: '-' }}</td></tr>
                                        <tr><td class="fw-bold">Pendidikan</td><td>: {{ $user->pend_terakhir ?: '-' }}</td></tr>
                                        @if($user->tanggal_aktif)
                                            <tr><td class="fw-bold">Tgl Aktif</td><td>: {{ $user->tanggal_aktif->format('d/m/Y') }}</td></tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            @if($user->kompetensi_1 || $user->kompetensi_2)
                                <hr class="my-2">
                                <div class="d-flex gap-2 align-items-center">
                                    <strong class="small text-muted">Kompetensi:</strong>
                                    @if($user->kompetensi_1) <span class="badge bg-primary-subtle text-primary">{{ $user->kompetensi_1 }}</span> @endif
                                    @if($user->kompetensi_2) <span class="badge bg-info-subtle text-info">{{ $user->kompetensi_2 }}</span> @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ══════ INSTRUKTUR-ONLY: Profil Lengkap ══════ --}}
                    @if($user->role === 'instruktur')
                        <div class="card border mb-4">
                            <div class="card-header bg-info text-white py-2 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge-fill me-2"></i>Profil Lengkap Instruktur</h6>
                                @if($user->instructorProfile)
                                    <span class="badge bg-white text-info font-monospace">{{ $user->instructorProfile->level ? strtoupper($user->instructorProfile->level) : 'JUNIOR' }}</span>
                                @endif
                            </div>
                            <div class="card-body">
                                @if(!$user->instructorProfile)
                                    <div class="alert alert-warning mb-0">
                                        <i class="bi bi-exclamation-triangle me-2"></i>Instruktur belum melengkapi data profil.
                                    </div>
                                @else
                                    <div class="row">
                                        {{-- Identitas & Kontak --}}
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-info border-bottom pb-2 mb-2"><i class="bi bi-person-fill me-2"></i>Identitas & Kontak</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr><td class="fw-bold" style="width:40%">Nama Panggilan</td><td>: {{ $user->instructorProfile->nama_panggilan ?: '-' }}</td></tr>
                                                <tr><td class="fw-bold">Gelar Depan</td><td>: {{ $user->instructorProfile->gelar_depan ?: '-' }}</td></tr>
                                                <tr><td class="fw-bold">Gelar Belakang</td><td>: {{ $user->instructorProfile->gelar_belakang ?: '-' }}</td></tr>
                                                <tr><td class="fw-bold">Kontak Darurat</td><td>: {{ $user->instructorProfile->no_hp_2 ?: '-' }} (Keluarga)</td></tr>
                                                <tr><td class="fw-bold">Status Pernikahan</td><td>: {{ $user->instructorProfile->status_pernikahan ?: '-' }}</td></tr>
                                            </table>
                                        </div>
                                        {{-- Domisili --}}
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-info border-bottom pb-2 mb-2"><i class="bi bi-geo-alt-fill me-2"></i>Domisili</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr><td class="fw-bold" style="width:40%">Kota Domisili</td><td>: {{ $user->instructorProfile->kota_domisili ?: '-' }}</td></tr>
                                                <tr><td class="fw-bold">Alamat Domisili</td><td>: {{ $user->instructorProfile->alamat_domisili ?: '-' }}</td></tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row">
                                        {{-- Keuangan & Legal --}}
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-info border-bottom pb-2 mb-2"><i class="bi bi-credit-card-2-front-fill me-2"></i>Keuangan & Legal</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr><td class="fw-bold" style="width:40%">NIK</td><td>: <span class="font-monospace">{{ $user->instructorProfile->nik ?: '-' }}</span></td></tr>
                                                <tr><td class="fw-bold">No NPWP</td><td>: <span class="font-monospace">{{ $user->instructorProfile->no_npwp ?: '-' }}</span></td></tr>
                                                <tr><td class="fw-bold">Bank</td><td>: {{ $user->instructorProfile->nama_bank ?: '-' }}</td></tr>
                                                <tr><td class="fw-bold">No Rekening</td><td>: <span class="font-monospace">{{ $user->instructorProfile->no_rekening ?: '-' }}</span></td></tr>
                                                <tr><td class="fw-bold">Pemilik Rek</td><td>: {{ $user->instructorProfile->nama_pemilik_rekening ?: '-' }}</td></tr>
                                            </table>
                                        </div>
                                        {{-- Pendidikan & Pekerjaan --}}
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-info border-bottom pb-2 mb-2"><i class="bi bi-mortarboard-fill me-2"></i>Pendidikan & Pekerjaan</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr><td class="fw-bold" style="width:40%">Universitas</td><td>: {{ $user->instructorProfile->universitas_jurusan ?: '-' }}</td></tr>
                                                <tr><td class="fw-bold">Pekerjaan Terakhir</td><td>: {{ $user->instructorProfile->pekerjaan_terakhir ?: '-' }}</td></tr>
                                                <tr><td class="fw-bold">Jenjang Mengajar</td><td>: {{ $user->instructorProfile->jenjang_mengajar ?: '-' }}</td></tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row">
                                        {{-- Fisik & Logistik --}}
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-info border-bottom pb-2 mb-2"><i class="bi bi-truck me-2"></i>Fisik & Logistik</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr><td class="fw-bold" style="width:40%">Tinggi/Berat</td><td>: {{ $user->instructorProfile->tinggi_berat_badan ?: '-' }}</td></tr>
                                                <tr><td class="fw-bold">Mata Minus</td><td>: {{ $user->instructorProfile->mata_minus ?: '-' }}</td></tr>
                                                <tr><td class="fw-bold">Alat Mengajar</td><td>: {{ $user->instructorProfile->alat_mengajar ?: '-' }}</td></tr>
                                                <tr><td class="fw-bold">Kendaraan</td><td>: {{ $user->instructorProfile->kendaraan ?: '-' }} ({{ $user->instructorProfile->jenis_kendaraan ?: '-' }})</td></tr>
                                                <tr><td class="fw-bold">Riwayat Penyakit</td><td>: <span class="text-danger">{{ $user->instructorProfile->riwayat_penyakit ?: '-' }}</span></td></tr>
                                            </table>
                                        </div>
                                        {{-- Dokumen Lampiran --}}
                                        <div class="col-md-6 mb-3">
                                            <h6 class="text-info border-bottom pb-2 mb-2"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Dokumen Lampiran</h6>
                                            <div class="d-flex flex-column gap-2 mt-2">
                                                @if($user->instructorProfile->foto_ktp)
                                                    <a href="{{ asset('storage/' . $user->instructorProfile->foto_ktp) }}" target="_blank" class="btn btn-outline-primary btn-sm text-start">
                                                        <i class="bi bi-image me-2"></i>Lihat Foto KTP
                                                    </a>
                                                @else
                                                    <button class="btn btn-outline-secondary btn-sm text-start" disabled><i class="bi bi-image me-2"></i>Foto KTP Tidak Ada</button>
                                                @endif
                                                @if($user->instructorProfile->foto_npwp)
                                                    <a href="{{ asset('storage/' . $user->instructorProfile->foto_npwp) }}" target="_blank" class="btn btn-outline-primary btn-sm text-start">
                                                        <i class="bi bi-image me-2"></i>Lihat Foto NPWP
                                                    </a>
                                                @else
                                                    <button class="btn btn-outline-secondary btn-sm text-start" disabled><i class="bi bi-image me-2"></i>Foto NPWP Tidak Ada</button>
                                                @endif
                                                @if($user->instructorProfile->cv_link)
                                                    <a href="{{ asset('storage/' . $user->instructorProfile->cv_link) }}" target="_blank" class="btn btn-outline-primary btn-sm text-start">
                                                        <i class="bi bi-file-earmark-text me-2"></i>Unduh/Lihat CV
                                                    </a>
                                                @else
                                                    <button class="btn btn-outline-secondary btn-sm text-start" disabled><i class="bi bi-file-earmark-text me-2"></i>CV Tidak Ada</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    {{-- ══════ END INSTRUKTUR-ONLY ══════ --}}

                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- System Information --}}
                    <div class="card border mb-3">
                        <div class="card-header py-2">
                            <h6 class="card-title mb-0 fw-bold"><i class="bi bi-gear me-2"></i>Informasi Sistem</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td class="fw-bold">Terdaftar</td><td>: <small class="text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</small></td></tr>
                                <tr><td class="fw-bold">Terakhir Update</td><td>: <small class="text-muted">{{ $user->updated_at->format('d/m/Y H:i') }}</small></td></tr>
                                @if($user->tanggal_aktif)
                                    <tr><td class="fw-bold">Tanggal Aktif</td><td>: <small class="text-muted">{{ $user->tanggal_aktif->format('d/m/Y') }}</small></td></tr>
                                @endif
                                @if($user->tanggal_nonaktif)
                                    <tr><td class="fw-bold text-danger">Tgl Nonaktif</td><td>: <small class="text-danger">{{ $user->tanggal_nonaktif->format('d/m/Y') }}</small></td></tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    {{-- Instructor: Verification Status --}}
                    @if($user->role === 'instruktur')
                        <div class="card border mb-3">
                            <div class="card-header py-2">
                                <h6 class="card-title mb-0 fw-bold"><i class="bi bi-shield-check me-2"></i>Status Verifikasi</h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $verificationClass = match($user->verification_status) {
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'pending' => 'bg-warning text-dark',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <div class="mb-2">
                                    <span class="badge {{ $verificationClass }} px-3 py-2 fs-6">
                                        {{ ucfirst($user->verification_status ?: 'Belum Diverifikasi') }}
                                    </span>
                                </div>
                                @if($user->verified_at)
                                    <small class="text-muted d-block">Diverifikasi: {{ $user->verified_at->format('d/m/Y H:i') }}</small>
                                @endif
                                @if($user->verifiedBy)
                                    <small class="text-muted d-block">Oleh: {{ $user->verifiedBy->nama_lengkap }}</small>
                                @endif
                                @if($user->rejection_reason)
                                    <div class="alert alert-danger mt-2 mb-0 py-2 small">
                                        <strong>Alasan Penolakan:</strong> {{ $user->rejection_reason }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Instructor: Quick Stats --}}
                    @if($user->role === 'instruktur' && isset($instructorStats))
                        <div class="card border mb-3">
                            <div class="card-header py-2">
                                <h6 class="card-title mb-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Ringkasan Performa</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Total Sesi Terjadwal</span>
                                        <span class="badge bg-primary rounded-pill">{{ $instructorStats['total_sessions'] }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Sesi Selesai</span>
                                        <span class="badge bg-success rounded-pill">{{ $instructorStats['completed_sessions'] }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Total Laporan</span>
                                        <span class="badge bg-info rounded-pill">{{ $instructorStats['total_reports'] }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Laporan Bulan Ini</span>
                                        <span class="badge bg-warning text-dark rounded-pill">{{ $instructorStats['reports_this_month'] }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Sekolah Mitra</span>
                                        <span class="badge bg-secondary rounded-pill">{{ $instructorStats['total_schools'] }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 bg-light">
                                        <span class="small fw-bold">Total Honor Netto</span>
                                        <span class="fw-bold text-success">Rp {{ number_format($instructorStats['total_payroll_net'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Admin: Quick Info --}}
                    @if(in_array($user->role, ['webmaster', 'admin_sistem', 'admin']))
                        <div class="card border mb-3">
                            <div class="card-header py-2 bg-danger text-white">
                                <h6 class="card-title mb-0 fw-bold"><i class="bi bi-shield-lock me-2"></i>Level Otoritas</h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $authorityDesc = match($user->role) {
                                        'webmaster' => 'Akses penuh ke seluruh sistem, termasuk manajemen user, konfigurasi, dan data sensitif.',
                                        'admin_sistem' => 'Akses operasional harian: payroll, verifikasi instruktur, manajemen sekolah & ekskul.',
                                        'admin' => 'Akses terbatas untuk operasional dasar dan laporan.',
                                        default => '-'
                                    };
                                    $authorityLevel = match($user->role) {
                                        'webmaster' => '🔴 LEVEL 1 — SUPERADMIN',
                                        'admin_sistem' => '🟡 LEVEL 2 — ADMIN SISTEM',
                                        'admin' => '🔵 LEVEL 3 — ADMIN',
                                        default => '⚪ UNKNOWN'
                                    };
                                @endphp
                                <div class="mb-2">
                                    <span class="fw-bold font-monospace small">{{ $authorityLevel }}</span>
                                </div>
                                <p class="small text-muted mb-0">{{ $authorityDesc }}</p>
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-header py-2">
                                <h6 class="card-title mb-0 fw-bold"><i class="bi bi-activity me-2"></i>Statistik Aksi Admin</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Total Aksi Tercatat</span>
                                        <span class="badge bg-primary rounded-pill">{{ $activityLogs->count() > 0 ? \App\Models\ActivityLog::where('user_id', $user->id)->count() : 0 }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Aksi Bulan Ini</span>
                                        <span class="badge bg-info rounded-pill">{{ \App\Models\ActivityLog::where('user_id', $user->id)->where('created_at', '>=', now()->startOfMonth())->count() }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Aksi Hari Ini</span>
                                        <span class="badge bg-success rounded-pill">{{ \App\Models\ActivityLog::where('user_id', $user->id)->whereDate('created_at', today())->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Action Panel --}}
                    @can('update', $user)
                        <div class="card border">
                            <div class="card-header py-2">
                                <h6 class="card-title mb-0 fw-bold"><i class="bi bi-lightning me-2"></i>Tindakan Cepat</h6>
                            </div>
                            <div class="card-body d-flex flex-column gap-2">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning w-100 text-start">
                                    <i class="bi bi-pencil-square me-2"></i>Edit Data User
                                </a>
                                @can('delete', $user)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->nama_lengkap }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 text-start">
                                            <i class="bi bi-trash me-2"></i>Hapus User
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        {{-- ────── TAB 2: RIWAYAT SESI MENGAJAR (Instruktur Only) ────── --}}
        @if($user->role === 'instruktur')
            <div class="tab-pane fade p-4" id="pane-sessions" role="tabpanel">
                <h5 class="fw-bold mb-3"><i class="bi bi-calendar-check me-2"></i>10 Sesi Mengajar Terakhir</h5>
                @if(isset($recentSessions) && $recentSessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Sekolah Mitra</th>
                                    <th>Rombel / Program</th>
                                    <th>Status Sesi</th>
                                    <th>Laporan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSessions as $idx => $session)
                                    <tr>
                                        <td class="text-muted">{{ $idx + 1 }}</td>
                                        <td class="fw-semibold">{{ \Carbon\Carbon::parse($session->tanggal_terjadwal)->format('d/m/Y') }}</td>
                                        <td>{{ optional(optional(optional($session->rombel)->ekstrakurikuler)->sekolah)->namasekolah ?? 'Ad-Hoc / Office' }}</td>
                                        <td>
                                            <span class="small">{{ optional(optional($session->rombel)->ekstrakurikuler)->kategori_program ?? '-' }}</span>
                                            <br><small class="text-muted">{{ optional($session->rombel)->nama_rombel ?? '-' }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = match($session->status) {
                                                    'completed' => 'bg-success',
                                                    'scheduled' => 'bg-primary',
                                                    'cancelled' => 'bg-danger',
                                                    'missed' => 'bg-warning text-dark',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusBadge }}">{{ ucfirst($session->status) }}</span>
                                        </td>
                                        <td>
                                            @if($session->laporanMengajar)
                                                <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>Sudah</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x-circle me-1"></i>Belum</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-light border text-center py-4">
                        <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">Belum ada sesi mengajar yang tercatat untuk instruktur ini.</p>
                    </div>
                @endif
            </div>

            {{-- ────── TAB 3: RIWAYAT PAYROLL (Instruktur Only) ────── --}}
            <div class="tab-pane fade p-4" id="pane-payroll" role="tabpanel">
                <h5 class="fw-bold mb-3"><i class="bi bi-cash-stack me-2"></i>Riwayat Slip Honor & Payroll</h5>
                @if(isset($payrollItems) && $payrollItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Kode Batch</th>
                                    <th>Periode</th>
                                    <th class="text-end">Total Sesi</th>
                                    <th class="text-end">Honor Dasar</th>
                                    <th class="text-end">Transport</th>
                                    <th class="text-end">Potongan</th>
                                    <th class="text-end">Honor Netto</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payrollItems as $idx => $pItem)
                                    <tr>
                                        <td class="text-muted">{{ $idx + 1 }}</td>
                                        <td class="fw-semibold font-monospace">{{ optional($pItem->batch)->code ?? '-' }}</td>
                                        <td>{{ optional($pItem->batch)->periode ? $pItem->batch->periode->format('M Y') : '-' }}</td>
                                        <td class="text-end">{{ $pItem->total_sessions }}</td>
                                        <td class="text-end">Rp {{ number_format($pItem->total_base_fee, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($pItem->total_transport_fee, 0, ',', '.') }}</td>
                                        <td class="text-end text-danger">Rp {{ number_format($pItem->total_penalty, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-success">Rp {{ number_format($pItem->net_salary, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $batchStatus = optional($pItem->batch)->status ?? 'draft';
                                                $batchBadge = match($batchStatus) {
                                                    'paid' => 'bg-success',
                                                    'processed' => 'bg-primary',
                                                    'draft' => 'bg-secondary',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $batchBadge }}">{{ ucfirst($batchStatus) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('payroll.slip', $pItem->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Slip Gaji">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="3">TOTAL</td>
                                    <td class="text-end">{{ $payrollItems->sum('total_sessions') }}</td>
                                    <td class="text-end">Rp {{ number_format($payrollItems->sum('total_base_fee'), 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($payrollItems->sum('total_transport_fee'), 0, ',', '.') }}</td>
                                    <td class="text-end text-danger">Rp {{ number_format($payrollItems->sum('total_penalty'), 0, ',', '.') }}</td>
                                    <td class="text-end text-success">Rp {{ number_format($payrollItems->sum('net_salary'), 0, ',', '.') }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="alert alert-light border text-center py-4">
                        <i class="bi bi-wallet2 fs-1 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">Belum ada riwayat payroll / slip honor untuk instruktur ini.</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- ────── TAB 4: LOG AKTIVITAS (Semua Role) ────── --}}
        <div class="tab-pane fade p-4" id="pane-activity" role="tabpanel">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>15 Aktivitas Terbaru</h5>
            @if(isset($activityLogs) && $activityLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px">#</th>
                                <th style="width: 150px">Waktu</th>
                                <th style="width: 170px">Aksi</th>
                                <th>Deskripsi</th>
                                <th style="width: 130px">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityLogs as $idx => $log)
                                <tr>
                                    <td class="text-muted small">{{ $idx + 1 }}</td>
                                    <td class="small">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @php
                                            $actionColor = match(true) {
                                                str_contains($log->action, 'create') || str_contains($log->action, 'store') => 'bg-success-subtle text-success',
                                                str_contains($log->action, 'update') || str_contains($log->action, 'edit') => 'bg-warning-subtle text-warning',
                                                str_contains($log->action, 'delete') || str_contains($log->action, 'destroy') => 'bg-danger-subtle text-danger',
                                                str_contains($log->action, 'approve') => 'bg-success-subtle text-success',
                                                str_contains($log->action, 'reject') => 'bg-danger-subtle text-danger',
                                                str_contains($log->action, 'login') => 'bg-info-subtle text-info',
                                                default => 'bg-secondary-subtle text-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $actionColor }} font-monospace">{{ $log->action }}</span>
                                    </td>
                                    <td class="small">{{ Str::limit($log->description, 120) }}</td>
                                    <td class="font-monospace small text-muted">{{ $log->ip_address ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-light border text-center py-4">
                    <i class="bi bi-journal-x fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-0">Belum ada log aktivitas tercatat untuk user ini.</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection