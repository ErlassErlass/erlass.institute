@extends('layouts.app')

@section('title', 'Edit Sesi Ekstrakurikuler')

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
            <li class="breadcrumb-item">
                <a href="{{ route('ekstrakurikuler.sessions.show', array_merge(['session' => $session->id], request()->query())) }}" class="text-decoration-none">
                    Pertemuan {{ $session->nomor_pertemuan }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 text-primary">Edit Sesi</h4>
                        <p class="text-muted mb-0">
                            {{ $session->rombel->ekstrakurikuler->kategori_program }} - {{ $session->rombel->nama_rombel }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('ekstrakurikuler.sessions.update', array_merge(['session' => $session->id], request()->query())) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_return_query" value="{{ json_encode(request()->query()) }}">

                @if($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 mb-4 alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger me-2"></i>
                            <div>
                                <strong>Gagal Menyimpan Perubahan!</strong>
                                <ul class="mb-0 ps-3 mt-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 card-title">Jadwal Sesi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Tanggal -->
                            <div class="col-md-6">
                                <label for="tanggal_terjadwal" class="form-label">Tanggal Sesi <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="tanggal_terjadwal" 
                                       id="tanggal_terjadwal" 
                                       value="{{ old('tanggal_terjadwal', $session->tanggal_terjadwal->format('Y-m-d')) }}"
                                       class="form-control datepicker @error('tanggal_terjadwal') is-invalid @enderror"
                                       placeholder="DD-MM-YYYY">
                                @error('tanggal_terjadwal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label">Status Sesi</label>
                                <div>
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
                                    <span class="badge bg-{{ $statusClass }} fs-6">
                                        {{ $session->status_label }}
                                    </span>
                                </div>
                            </div>

                            <!-- Jam Mulai -->
                            <div class="col-md-6">
                                <label for="jam_mulai_terjadwal" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" 
                                       name="jam_mulai_terjadwal" 
                                       id="jam_mulai_terjadwal" 
                                       value="{{ old('jam_mulai_terjadwal', $session->jam_mulai_terjadwal->format('H:i')) }}"
                                       class="form-control @error('jam_mulai_terjadwal') is-invalid @enderror">
                                @error('jam_mulai_terjadwal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jam Selesai -->
                            <div class="col-md-6">
                                <label for="jam_selesai_terjadwal" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" 
                                       name="jam_selesai_terjadwal" 
                                       id="jam_selesai_terjadwal" 
                                       value="{{ old('jam_selesai_terjadwal', $session->jam_selesai_terjadwal->format('H:i')) }}"
                                       class="form-control @error('jam_selesai_terjadwal') is-invalid @enderror">
                                @error('jam_selesai_terjadwal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tim Pengajar -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 card-title">Tim Pengajar</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Instruktur -->
                            <div class="col-md-6">
                                <label for="user_id_instruktur" class="form-label">Instruktur</label>
                                <select name="user_id_instruktur" 
                                        id="user_id_instruktur"
                                        class="form-select select2 @error('user_id_instruktur') is-invalid @enderror">
                                    <option value="">Pilih Instruktur</option>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}" 
                                                {{ old('user_id_instruktur', $session->user_id_instruktur ?? $session->rombel?->user_id_instruktur) == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id_instruktur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Asisten -->
                            <div class="col-md-6">
                                <label for="user_id_asisten" class="form-label">Asisten (Opsional)</label>
                                <select name="user_id_asisten" 
                                        id="user_id_asisten"
                                        class="form-select select2 @error('user_id_asisten') is-invalid @enderror">
                                    <option value="">Tidak Ada Asisten</option>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}" 
                                                {{ old('user_id_asisten', $session->user_id_asisten ?? $session->rombel?->user_id_asisten) == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id_asisten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Option B: Apply to All Scheduled Sessions -->
                            <div class="col-12 mt-3">
                                <div class="form-check form-switch p-3 bg-light rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="apply_to_all_sessions" value="1" id="apply_to_all_sessions" {{ old('apply_to_all_sessions') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="apply_to_all_sessions">
                                        Terapkan Instruktur & Asisten ini ke seluruh sesi terjadwal dalam Rombel ini (Pertemuan 1 s/d 32)
                                    </label>
                                    <div class="form-text text-muted small mt-1">
                                        <i class="bi bi-info-circle me-1"></i> Centang opsi ini jika Anda ingin instruktur/asisten yang dipilih bertugas untuk seluruh pertemuan rutin Rombel yang belum dilaksanakan. Sesi yang sudah selesai tidak akan terpengaruh.
                                    </div>
                                </div>
                            </div>

                            <!-- Conflict Check Params -->
                            <div class="col-12 mt-3">
                                <button type="button" onclick="checkConflicts()" class="btn btn-warning text-dark">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Cek Konflik Jadwal
                                </button>
                                <div id="conflictResults" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Materi & Catatan -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 card-title">Materi & Catatan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="topik_materi" class="form-label">Topik Materi</label>
                            <select name="topik_materi" 
                                    id="topik_materi" 
                                    class="form-select select2 @error('topik_materi') is-invalid @enderror">
                                <option value="">Pilih Topik Materi</option>
                                @php
                                    $currentMateri = old('topik_materi', $session->topik_materi);
                                    // Check if current materi is in the list
                                    $isInList = $materiList->contains($currentMateri);
                                @endphp
                                
                                @if($currentMateri && !$isInList)
                                    <option value="{{ $currentMateri }}" selected>{{ $currentMateri }}</option>
                                @endif

                                @foreach($materiList as $materi)
                                    <option value="{{ $materi }}" {{ $currentMateri == $materi ? 'selected' : '' }}>
                                        {{ $materi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('topik_materi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi_kegiatan" class="form-label">Deskripsi Kegiatan</label>
                            <textarea name="deskripsi_kegiatan" 
                                      id="deskripsi_kegiatan" 
                                      rows="4"
                                      class="form-control @error('deskripsi_kegiatan') is-invalid @enderror">{{ old('deskripsi_kegiatan', $session->deskripsi_kegiatan) }}</textarea>
                            @error('deskripsi_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan Tambahan</label>
                            <textarea name="catatan" 
                                      id="catatan" 
                                      rows="3"
                                      class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan', $session->catatan) }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Info Program Readonly -->
                <div class="card shadow-sm mb-4 bg-light">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-3 text-muted">Informasi Program</h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <small class="text-muted d-block">Program</small>
                                <strong>{{ $session->rombel->ekstrakurikuler->kategori_program }}</strong>
                            </div>
                            <div class="col-md-4 mb-2">
                                <small class="text-muted d-block">Rombel</small>
                                <strong>{{ $session->rombel->nama_rombel }}</strong>
                            </div>
                            <div class="col-md-4 mb-2">
                                <small class="text-muted d-block">Sekolah</small>
                                <strong>{{ $session->rombel->ekstrakurikuler->sekolah->namasekolah }}</strong>
                            </div>
                            <div class="col-md-4 mb-2">
                                <small class="text-muted d-block">Pertemuan</small>
                                <strong>{{ $session->nomor_pertemuan }} dari {{ $session->rombel->total_pertemuan }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 mb-5">
                    <a href="{{ route('ekstrakurikuler.sessions.index', request()->query() ?: session('ekstrakurikuler_sessions_filters', [])) }}" class="btn btn-secondary">
                        <i class="bi bi-x me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

            <!-- Danger Zone -->
            @if($session->canCancel())
            <div class="card border-danger shadow-sm mb-5">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0 card-title"><i class="bi bi-exclamation-octagon me-2"></i>Area Berbahaya</h5>
                </div>
                <div class="card-body">
                    <p class="text-danger">Membatalkan sesi akan mengubah status sesi menjadi 'Dibatalkan'. Aksi ini tidak dapat dibatalkan.</p>
                    <form action="{{ route('ekstrakurikuler.sessions.cancel', $session) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan sesi ini?');">
                        @csrf
                        <div class="mb-3">
                            <label for="alasan_pembatalan" class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                            <textarea name="alasan_pembatalan" required class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger">
                            Batalkan Sesi Ini
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Inisialisasi Select2 jika tersedia
    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    });

    function checkConflicts() {
        const instrukturId = document.getElementById('user_id_instruktur').value;
        const asistenId = document.getElementById('user_id_asisten') ? document.getElementById('user_id_asisten').value : null;
        const tanggal = document.getElementById('tanggal_terjadwal').value;
        const jamMulai = document.getElementById('jam_mulai_terjadwal').value;
        const jamSelesai = document.getElementById('jam_selesai_terjadwal').value;
        
        if (!tanggal || !jamMulai || !jamSelesai) {
            alert('Mohon isi tanggal dan waktu terlebih dahulu');
            return;
        }

        if (!instrukturId && !asistenId) {
            alert('Mohon pilih instruktur atau asisten terlebih dahulu');
            return;
        }
        
        const resultsDiv = document.getElementById('conflictResults');
        resultsDiv.innerHTML = '<div class="text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div>Mengecek konflik di database...</div>';

        const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}';

        fetch('{{ route("ekstrakurikuler.sessions.check-conflict", $session) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                user_id_instruktur: instrukturId,
                user_id_asisten: asistenId,
                tanggal_terjadwal: tanggal,
                jam_mulai_terjadwal: jamMulai,
                jam_selesai_terjadwal: jamSelesai
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.has_conflict) {
                    let msgList = (data.messages || []).map(m => `<li>${m}</li>`).join('');
                    resultsDiv.innerHTML = `
                        <div class="alert alert-danger mt-2 mb-0">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                                <strong>Konflik Jadwal Ditemukan:</strong>
                            </div>
                            <ul class="mb-0 ps-3 small">${msgList || 'Pengajar sudah memiliki jadwal lain pada waktu tersebut.'}</ul>
                        </div>
                    `;
                } else {
                    resultsDiv.innerHTML = `
                        <div class="alert alert-success mt-2 mb-0 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div>
                                Tidak ada konflik jadwal ditemukan.
                            </div>
                        </div>
                    `;
                }
            } else {
                resultsDiv.innerHTML = `<div class="alert alert-warning mt-2 mb-0">${data.message || 'Gagal mengecek konflik.'}</div>`;
            }
        })
        .catch(err => {
            resultsDiv.innerHTML = '<div class="alert alert-danger mt-2 mb-0">Terjadi kesalahan koneksi saat mengecek konflik.</div>';
        });
    }
</script>
@endpush
@endsection