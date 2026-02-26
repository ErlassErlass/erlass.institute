@extends('layouts.app')

@section('title', 'Edit Laporan Mengajar')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-transparent py-3">
                    <h1 class="h4 mb-0 fw-bold text-gradient-primary"><i class="fas fa-edit me-2"></i>Edit Laporan Mengajar</h1>
                </div>

                <form method="POST" action="{{ route('laporan-mengajar.update', $laporanMengajar) }}" enctype="multipart/form-data" id="laporanForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Terdapat Kesalahan!</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <!-- Section 1: Instructor Information -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom"><i class="fas fa-user-tie me-2"></i>Informasi Instruktur</h5>
                            <input type="hidden" name="user_id_instruktur" value="{{ $laporanMengajar->user_id_instruktur }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_instruktur" class="form-label">Nama Instruktur</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" id="nama_instruktur" class="form-control" value="{{ $laporanMengajar->instruktur->nama_lengkap }}" disabled readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="user_id_assisten" class="form-label">Asisten Instruktur (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                                        <select name="user_id_assisten" id="user_id_assisten" class="form-select @error('user_id_assisten') is-invalid @enderror">
                                            <option value="">Pilih Asisten</option>
                                            @foreach ($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" {{ old('user_id_assisten', $laporanMengajar->user_id_assisten) == $instructor->id ? 'selected' : '' }}>{{ $instructor->nama_lengkap }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('user_id_assisten') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom mt-4"><i class="fas fa-school me-2"></i>Lokasi Mengajar (Tidak dapat diubah)</h5>
                            <div class="row">
                                {{-- Kita tetap mengirim kodlan sebagai hidden input agar validasi di controller tetap berjalan --}}
                                <input type="hidden" name="sekolah_kodlan" value="{{ $laporanMengajar->sekolah_kodlan }}">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Sekolah</label>
                                    {{-- Mengambil nama sekolah dari relasi Eloquent --}}
                                    <input type="text" class="form-control" value="{{ $laporanMengajar->sekolah->namasekolah ?? 'Data Sekolah Tidak Ditemukan' }}" readonly disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kode Sekolah</label>
                                    {{-- Mengambil kodlan langsung dari laporan mengajar --}}
                                    <input type="text" class="form-control" value="{{ $laporanMengajar->sekolah_kodlan }}" readonly disabled>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Teaching Details -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom"><i class="fas fa-chalkboard-teacher me-2"></i>Detail Pengajaran</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="pertemuan_ke" class="form-label">Pertemuan Ke-</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                        <input type="number" name="pertemuan_ke" id="pertemuan_ke" class="form-control @error('pertemuan_ke') is-invalid @enderror" value="{{ old('pertemuan_ke', $laporanMengajar->pertemuan_ke) }}" required min="1">
                                    </div>
                                    @error('pertemuan_ke') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="rombel" class="form-label">Rombongan Belajar</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                                        <input type="text" name="rombel" id="rombel" class="form-control @error('rombel') is-invalid @enderror" value="{{ old('rombel', $laporanMengajar->rombel) }}" required>
                                    </div>
                                    @error('rombel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="kategori_pengajaran" class="form-label">Kategori Pengajaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        <select name="kategori_pengajaran" id="kategori_pengajaran" class="form-select @error('kategori_pengajaran') is-invalid @enderror" required>
                                            <option value="">Pilih Kategori</option>

                                            {{-- ✅ LOOP MELALUI VARIABEL $kategori DARI CONTROLLER --}}
                                            @foreach ($kategori as $kat)
                                            <option value="{{ $kat }}" {{ old('kategori_pengajaran', $laporanMengajar->kategori_pengajaran ?? '') == $kat ? 'selected' : '' }}>
                                                {{ $kat }}
                                            </option>
                                            @endforeach

                                        </select>
                                    </div>
                                    @error('kategori_pengajaran') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                {{-- Ganti blok kode jadwal mengajar Anda dengan ini --}}
                                <div class="col-md-4 mb-3">
                                    <label for="jadwal_mengajar" class="form-label">Jadwal Mengajar</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>

                                        {{-- ✅ KODE JAUH LEBIH BERSIH DAN SEDERHANA --}}
                                        <input
                                            type="text"
                                            name="jadwal_mengajar"
                                            id="jadwal_mengajar"
                                            class="form-control @error('jadwal_mengajar') is-invalid @enderror"
                                            value="{{ old('jadwal_mengajar', $laporanMengajar->jadwal_mengajar_formatted) }}"
                                            required
                                            placeholder="dd/mm/yyyy"
                                            autocomplete="off"
                                            {{ $laporanMengajar->ekstrakurikulerSession ? 'readonly' : '' }}>
                                    </div>
                                    @error('jadwal_mengajar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        <input type="text" name="jam_mulai" id="jam_mulai"
                                            value="{{ old('jam_mulai', $laporanMengajar->jam_mulai ? \Carbon\Carbon::parse($laporanMengajar->jam_mulai)->format('H:i') : '') }}"
                                            class="form-control @error('jam_mulai') is-invalid @enderror"
                                            required placeholder="HH:mm" autocomplete="off"
                                            {{ $laporanMengajar->ekstrakurikulerSession ? 'readonly' : '' }}>
                                    </div>
                                    @error('jam_mulai') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        <input type="text" name="jam_selesai" id="jam_selesai"
                                            value="{{ old('jam_selesai', $laporanMengajar->jam_selesai ? \Carbon\Carbon::parse($laporanMengajar->jam_selesai)->format('H:i') : '') }}"
                                            class="form-control @error('jam_selesai') is-invalid @enderror"
                                            required placeholder="HH:mm" autocomplete="off"
                                            {{ $laporanMengajar->ekstrakurikulerSession ? 'readonly' : '' }}>
                                    </div>
                                    @error('jam_selesai') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="materi_pengajaran" class="form-label">Materi Pengajaran</label>
                                <textarea name="materi_pengajaran" id="materi_pengajaran" class="form-control @error('materi_pengajaran') is-invalid @enderror" rows="3" required placeholder="Tuliskan materi pengajaran...">{{ old('materi_pengajaran', $laporanMengajar->materi_pengajaran) }}</textarea>
                                @error('materi_pengajaran') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Evaluasi Section Removed -->

                        <!-- Section 4: Documentation -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom"><i class="fas fa-images me-2"></i>Dokumentasi Kegiatan</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Upload foto kegiatan dengan format JPEG/PNG (maksimal 5MB)
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Foto Kegiatan</label>
                                @if($laporanMengajar->foto_kegiatan)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $laporanMengajar->foto_kegiatan) }}" alt="Foto Kegiatan" class="img-thumbnail">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="hapus_foto_kegiatan" id="hapus_foto_kegiatan" value="1">
                                        <label class="form-check-label" for="hapus_foto_kegiatan">
                                            Hapus foto saat ini
                                        </label>
                                    </div>
                                </div>
                                @endif
                                <input type="file" name="foto_kegiatan" id="foto_kegiatan" class="form-control @error('foto_kegiatan') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif" data-max-size="5242880">
                                <small class="text-muted">Format: JPEG, PNG, JPG, GIF. Maks: 5MB</small>
                                @error('foto_kegiatan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <!-- Foto Absensi Section Removed -->
                        </div>
                    </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Date picker with proper format handling
        $('#jadwal_mengajar').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'id',
            weekStart: 1
        });

        // Time picker
        $('.time-picker').timepicker({
            timeFormat: 'HH:mm',
            interval: 15,
            minTime: '06:00',
            maxTime: '21:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });

        // Form validation before submit
        $('#laporanForm').submit(function(e) {
            // Validate time
            var startTime = $('#jam_mulai').val();
            var endTime = $('#jam_selesai').val();

            if (startTime && endTime) {
                var start = new Date('1970-01-01T' + startTime + ':00');
                var end = new Date('1970-01-01T' + endTime + ':00');

                if (start >= end) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jam Tidak Valid',
                        text: 'Jam selesai harus setelah jam mulai'
                    });
                    return false;
                }
            }

            // Validate date format
            var dateInput = $('#jadwal_mengajar').val();
            if (dateInput) {
                var dateParts = dateInput.split('/');
                if (dateParts.length !== 3 || dateParts[0].length !== 2 || dateParts[1].length !== 2 || dateParts[2].length !== 4) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Tanggal Salah',
                        text: 'Format tanggal harus dd/mm/yyyy'
                    });
                    return false;
                }
            }

            return true;
        });
    });
</script>
@endpush