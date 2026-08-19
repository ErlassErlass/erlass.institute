@extends('layouts.app')

@section('title', 'Buat Tiket Bantuan Baru')

@section('content')
<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xl-8">
            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="h4 fw-bold text-dark mb-0">Buat Tiket Bantuan Baru</h1>
                        <p class="text-muted small mb-0">Sampaikan kendala Anda untuk ditindaklanjuti oleh Tim Operasional/QC Erlass.</p>
                    </div>
                </div>
            </div>

            <!-- Ticket Form Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- 1. Kategori Tiket (Interactive Radio Cards) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-2">
                                <span class="badge bg-primary rounded-circle p-1 me-1">1</span> Pilih Kategori Tiket <span class="text-danger">*</span>
                            </label>
                            <div class="row g-3">
                                <!-- Option 1: Jadwal / Honor -->
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="kategori" id="kat_jadwal_honor" value="jadwal_honor" {{ old('kategori') === 'jadwal_honor' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary text-start p-3 w-100 rounded-4 h-100 d-flex flex-column justify-content-between position-relative cursor-pointer shadow-none" for="kat_jadwal_honor">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-calendar2-check-fill fs-5"></i>
                                            </div>
                                            <i class="bi bi-check-circle-fill text-primary d-none checked-icon fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">Jadwal / Honor</h6>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                Penyesuaian tanggal mengajar, sesi bentrok, klaim honor, uang transport, atau slip payroll.
                                            </small>
                                        </div>
                                    </label>
                                </div>

                                <!-- Option 2: Keluhan Lain -->
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="kategori" id="kat_keluhan_lain" value="keluhan_lain" {{ old('kategori', 'keluhan_lain') === 'keluhan_lain' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary text-start p-3 w-100 rounded-4 h-100 d-flex flex-column justify-content-between position-relative cursor-pointer shadow-none" for="kat_keluhan_lain">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="bg-info-subtle text-info-emphasis rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-chat-square-quote-fill fs-5"></i>
                                            </div>
                                            <i class="bi bi-check-circle-fill text-primary d-none checked-icon fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">Keluhan Lain</h6>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                Kendala rombel siswa, fasilitas sekolah mitra, modul materi, perlengkapan, atau saran umum.
                                            </small>
                                        </div>
                                    </label>
                                </div>

                                <!-- Option 3: Teknis / Error -->
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="kategori" id="kat_teknis_error" value="teknis_error" {{ old('kategori') === 'teknis_error' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary text-start p-3 w-100 rounded-4 h-100 d-flex flex-column justify-content-between position-relative cursor-pointer shadow-none" for="kat_teknis_error">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="bg-danger-subtle text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-bug-fill fs-5"></i>
                                            </div>
                                            <i class="bi bi-check-circle-fill text-primary d-none checked-icon fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">Teknis / Error</h6>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                Error aplikasi, bug presensi GPS, kesulitan upload foto kegiatan/laporan, atau akun login.
                                            </small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @error('kategori')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- 2. Detail Kendala -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-2">
                                <span class="badge bg-primary rounded-circle p-1 me-1">2</span> Rincian Masalah
                            </label>

                            <div class="mb-3">
                                <label for="judul" class="form-label small fw-semibold text-muted">Subjek / Judul Tiket <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Penyesuaian Jadwal Pertemuan 3 di SDN Pejaten 01" required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                @if(!empty($recentSessions) && count($recentSessions) > 0)
                                <div class="col-12 col-md-8">
                                    <label for="ekstrakurikuler_session_id" class="form-label small fw-semibold text-muted">
                                        Terkait Sesi Mengajar <span class="badge bg-light text-muted border">Opsional</span>
                                    </label>
                                    <select class="form-select @error('ekstrakurikuler_session_id') is-invalid @enderror" id="ekstrakurikuler_session_id" name="ekstrakurikuler_session_id">
                                        <option value="">-- Tidak Terkait Sesi Tertentu / Umum --</option>
                                        @foreach($recentSessions as $sess)
                                        @php
                                            $sekolahName = optional(optional(optional($sess->rombel)->ekstrakurikuler)->sekolah)->namasekolah ?? 'Ekskul';
                                            $programName = optional(optional($sess->rombel)->ekstrakurikuler)->kategori_program ?? '-';
                                            $rombelName = optional($sess->rombel)->nama_rombel;
                                            $sessDate = \Carbon\Carbon::parse($sess->tanggal_terjadwal)->format('d M Y');
                                        @endphp
                                        <option value="{{ $sess->id }}" {{ old('ekstrakurikuler_session_id') == $sess->id ? 'selected' : '' }}>
                                            Pertemuan {{ $sess->nomor_pertemuan }} ({{ $sessDate }}) — {{ $sekolahName }} [{{ $programName }} @if($rombelName)- {{ $rombelName }}@endif]
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('ekstrakurikuler_session_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif

                                <div class="col-12 col-md-4">
                                    <label for="prioritas" class="form-label small fw-semibold text-muted">Tingkat Urgensi</label>
                                    <select class="form-select @error('prioritas') is-invalid @enderror" id="prioritas" name="prioritas">
                                        <option value="low" {{ old('prioritas') === 'low' ? 'selected' : '' }}>Biasa (Low)</option>
                                        <option value="medium" {{ old('prioritas', 'medium') === 'medium' ? 'selected' : '' }}>Sedang (Medium)</option>
                                        <option value="high" {{ old('prioritas') === 'high' ? 'selected' : '' }}>Penting (High)</option>
                                        <option value="urgent" {{ old('prioritas') === 'urgent' ? 'selected' : '' }}>Sangat Mendesak (Urgent)</option>
                                    </select>
                                    @error('prioritas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label small fw-semibold text-muted">Penjelasan Lengkap Kendala <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" placeholder="Ceritakan secara kronologis dan detail kendala yang dialami..." required>{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- 3. Lampiran Bukti / Screenshot -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-2">
                                <span class="badge bg-primary rounded-circle p-1 me-1">3</span> Lampiran Bukti / Screenshot <span class="badge bg-light text-muted border">Opsional</span>
                            </label>
                            <div class="p-3 bg-light rounded-4 border border-dashed text-center">
                                <input type="file" class="form-control @error('foto_lampiran') is-invalid @enderror" id="foto_lampiran" name="foto_lampiran" accept="image/*,.pdf,.doc,.docx">
                                <small class="text-muted d-block mt-2" style="font-size: 0.78rem;">
                                    <i class="bi bi-info-circle me-1"></i>Format yang didukung: JPG, PNG, PDF, DOCX (Maksimal 5MB).
                                </small>
                            </div>
                            @error('foto_lampiran')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary px-4 py-2">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Kirim Tiket Bantuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-check:checked + label {
    border-color: var(--bs-primary) !important;
    background-color: rgba(59, 130, 246, 0.06) !important;
}
.btn-check:checked + label .checked-icon {
    display: inline-block !important;
}
</style>
@endsection
