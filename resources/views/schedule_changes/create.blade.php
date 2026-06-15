@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('schedule-changes.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <div>
                    <h1 class="h4 fw-bold text-dark mb-1">Ajukan Perubahan Jadwal</h1>
                    <p class="text-muted mb-0 small">Pertemuan {{ $session->nomor_pertemuan }} - {{ $session->rombel->nama_rombel }}</p>
                </div>
            </div>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Current Schedule Info -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar-event me-2"></i>Jadwal Saat Ini
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <span class="text-muted small fw-semibold">Sekolah</span>
                            <div class="fw-bold text-dark mt-1">{{ $session->ekstrakurikuler->sekolah->namasekolah ?? '-' }}</div>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted small fw-semibold">Tanggal</span>
                            <div class="fw-bold text-dark mt-1">{{ $session->tanggal_terjadwal->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted small fw-semibold">Waktu</span>
                            <div class="fw-bold text-dark mt-1">{{ $session->jadwal_waktu }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil-square me-2"></i>Jadwal Yang Diusulkan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('schedule-changes.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="ekstrakurikuler_session_id" value="{{ $session->id }}">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="proposed_date" class="form-label fw-semibold">Tanggal Baru <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('proposed_date') is-invalid @enderror"
                                       id="proposed_date" name="proposed_date"
                                       value="{{ old('proposed_date') }}" required min="{{ date('Y-m-d') }}">
                                @error('proposed_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="proposed_start_time" class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('proposed_start_time') is-invalid @enderror"
                                       id="proposed_start_time" name="proposed_start_time"
                                       value="{{ old('proposed_start_time') }}" required>
                                @error('proposed_start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="proposed_end_time" class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('proposed_end_time') is-invalid @enderror"
                                       id="proposed_end_time" name="proposed_end_time"
                                       value="{{ old('proposed_end_time') }}" required>
                                @error('proposed_end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="reason" class="form-label fw-semibold">Alasan Perubahan <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('reason') is-invalid @enderror"
                                          id="reason" name="reason" rows="4"
                                          placeholder="Jelaskan alasan mengapa jadwal perlu diubah..."
                                          required>{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mt-4 small">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Info:</strong> Pengajuan ini akan melalui 2 tahap persetujuan:
                            <ol class="mb-0 mt-1">
                                <li>Validasi Akademik oleh Admin Erlass</li>
                                <li>Konfirmasi PIC Sekolah</li>
                            </ol>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('schedule-changes.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Ajukan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
