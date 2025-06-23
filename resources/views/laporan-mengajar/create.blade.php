@extends('layouts.app')

@section('title', 'Buat Laporan Mengajar')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<style>
    .card-header {
        background-color: #4e73df;
        color: white;
    }

    .section-header {
        color: #4e73df;
        font-weight: 600;
        border-left: 4px solid #4e73df;
        padding-left: 10px;
    }

    .form-section {
        background-color: #f8f9fc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        border-left: 3px solid #4e73df;
    }

    .file-upload-preview {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        display: none;
        margin-top: 10px;
    }

    .select2-container--bootstrap-5 .select2-selection {
        height: auto;
        min-height: 38px;
    }

    .progress-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .progress-step {
        text-align: center;
        position: relative;
        flex: 1;
    }

    .progress-step:not(:last-child):after {
        content: '';
        position: absolute;
        top: 15px;
        left: 50%;
        right: -50%;
        height: 2px;
        background: #e3e6f0;
        z-index: 1;
    }

    .step-number {
        width: 30px;
        height: 30px;
        background: #e3e6f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 5px;
        position: relative;
        z-index: 2;
        color: #858796;
        font-weight: bold;
    }

    .step-label {
        font-size: 12px;
        color: #858796;
    }

    .active-step .step-number {
        background: #4e73df;
        color: white;
    }

    .active-step .step-label {
        color: #4e73df;
        font-weight: bold;
    }

    .character-counter {
        font-size: 12px;
        color: #6c757d;
        text-align: right;
    }

    .character-counter.warning {
        color: #ffc107;
    }

    .character-counter.danger {
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="progress-indicator">
                <div class="progress-step active-step">
                    <div class="step-number">1</div>
                    <div class="step-label">Informasi Dasar</div>
                </div>
                <div class="progress-step">
                    <div class="step-number">2</div>
                    <div class="step-label">Detail Pengajaran</div>
                </div>
                <div class="progress-step">
                    <div class="step-number">3</div>
                    <div class="step-label">Evaluasi</div>
                </div>
                <div class="progress-step">
                    <div class="step-number">4</div>
                    <div class="step-label">Dokumentasi</div>
                </div>
            </div>

            <div class="card shadow-lg">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h4 mb-0"><i class="fas fa-book-open me-2"></i>Formulir Laporan Mengajar</h1>
                    <span class="badge bg-light text-dark">Draft</span>
                </div>

                <form method="POST" action="{{ route('laporan-mengajar.store') }}" enctype="multipart/form-data" id="laporanForm">
                    @csrf
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

                        <!-- Section 1: Basic Information -->
                        <div class="form-section">
                            <h5 class="section-header"><i class="fas fa-user-tie me-2"></i>Informasi Instruktur</h5>
                            <input type="hidden" name="user_id_instruktur" value="{{ Auth::id() }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_instruktur" class="form-label">Nama Instruktur</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" id="nama_instruktur" class="form-control" value="{{ Auth::user()->nama_lengkap }}" disabled readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="user_id_assisten" class="form-label">Asisten Instruktur (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                                        <select name="user_id_assisten" id="user_id_assisten" class="form-select @error('user_id_assisten') is-invalid @enderror">
                                            <option value="">Pilih Asisten</option>
                                            @foreach ($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" {{ old('user_id_assisten') == $instructor->id ? 'selected' : '' }}>{{ $instructor->nama_lengkap }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('user_id_assisten') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- File: resources/views/laporan-mengajar/create.blade.php --}}

                            <h5 class="section-header mt-4 mb-3"><i class="fas fa-school me-2"></i>Lokasi Mengajar</h5>
                            <div class="mb-3">
                                <label for="sekolah_kodlan" class="form-label">Cari & Pilih Sekolah</label>
                                <select name="sekolah_kodlan" id="sekolah-search" class="form-select @error('sekolah_kodlan') is-invalid @enderror" required>
                                    @if($selectedSekolah)
                                    <option value="{{ $selectedSekolah->kodlan }}" selected>{{ $selectedSekolah->namasekolah }} ({{ $selectedSekolah->kodlan }})</option>
                                    @endif
                                </select>
                                <small class="text-muted">Ketik minimal 3 karakter untuk mencari sekolah</small>
                                @error('sekolah_kodlan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Section 2: Teaching Details -->
                        <div class="form-section">
                            <h5 class="section-header"><i class="fas fa-chalkboard-teacher me-2"></i>Detail Pengajaran</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="pertemuan_ke" class="form-label">Pertemuan Ke-</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                        <input type="number" name="pertemuan_ke" id="pertemuan_ke" class="form-control @error('pertemuan_ke') is-invalid @enderror" value="{{ old('pertemuan_ke') }}" required min="1">
                                    </div>
                                    @error('pertemuan_ke') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="rombel" class="form-label">Rombongan Belajar</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                                        <input type="text" name="rombel" id="rombel" class="form-control @error('rombel') is-invalid @enderror" value="{{ old('rombel') }}" required>
                                    </div>
                                    @error('rombel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="kategori_pengajaran" class="form-label">Kategori Pengajaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        <select name="kategori_pengajaran" id="kategori_pengajaran" class="form-select @error('kategori_pengajaran') is-invalid @enderror" required>
                                            <option value="">Pilih Kategori</option>
                                            {{-- Daftar Opsi yang Sudah Diperbarui --}}
                                            <option value="Coding Scratch" {{ old('kategori_pengajaran') == 'Coding Scratch' ? 'selected' : '' }}>Coding Scratch</option>
                                            <option value="Coding Pictoblox" {{ old('kategori_pengajaran') == 'Coding Pictoblox' ? 'selected' : '' }}>Coding Pictoblox</option>
                                            <option value="English Course" {{ old('kategori_pengajaran') == 'English Course' ? 'selected' : '' }}>English Course</option>
                                            <option value="Microbit:Learning Kit" {{ old('kategori_pengajaran') == 'Microbit:Learning Kit' ? 'selected' : '' }}>Microbit:Learning Kit</option>
                                            <option value="Robotic Explorer" {{ old('kategori_pengajaran') == 'Robotic Explorer' ? 'selected' : '' }}>Robotic Explorer</option>
                                            <option value="Robotik Jimu" {{ old('kategori_pengajaran') == 'Robotik Jimu' ? 'selected' : '' }}>Robotik Jimu</option>

                                        </select>
                                    </div>
                                    @error('kategori_pengajaran') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="jadwal_mengajar" class="form-label">Jadwal Mengajar</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                        <input type="text" name="jadwal_mengajar" id="jadwal_mengajar" class="form-control @error('jadwal_mengajar') is-invalid @enderror" value="{{ old('jadwal_mengajar') }}" required placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    @error('jadwal_mengajar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        <input type="text" name="jam_mulai" id="jam_mulai" class="form-control time-picker @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai') }}" required placeholder="HH:MM" autocomplete="off">
                                    </div>
                                    @error('jam_mulai') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        <input type="text" name="jam_selesai" id="jam_selesai" class="form-control time-picker @error('jam_selesai') is-invalid @enderror" value="{{ old('jam_selesai') }}" required placeholder="HH:MM" autocomplete="off">
                                    </div>
                                    @error('jam_selesai') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="materi_pengajaran" class="form-label">Materi Pengajaran</label>
                                <textarea name="materi_pengajaran" id="materi_pengajaran" class="form-control @error('materi_pengajaran') is-invalid @enderror" required rows="3"></textarea>
                                <div class="character-counter" id="materi-counter">0/500 karakter</div>
                                @error('materi_pengajaran') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h5 class="mt-4 border-bottom pb-2 mb-3">Refleksi & Evaluasi</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_siswa_hadir" class="form-label">Jumlah Siswa Hadir</label>
                                <input type="number" name="jumlah_siswa_hadir" id="jumlah_siswa_hadir" class="form-control @error('jumlah_siswa_hadir') is-invalid @enderror" value="{{ old('jumlah_siswa_hadir') }}" required min="0">
                                @error('jumlah_siswa_hadir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_siswa_keluar" class="form-label">Jumlah Siswa Keluar</label>
                                <input type="number" name="jumlah_siswa_keluar" id="jumlah_siswa_keluar" class="form-control @error('jumlah_siswa_keluar') is-invalid @enderror" value="{{ old('jumlah_siswa_keluar') }}" required min="0">
                                @error('jumlah_siswa_keluar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="keaktifan" class="form-label">Keaktifan Siswa</label>
                                <select name="keaktifan" id="keaktifan" class="form-select @error('keaktifan') is-invalid @enderror" required>
                                    <option value="">Pilih Level</option>
                                    <option value="sangat_pasif" {{ old('keaktifan') == 'sangat_pasif' ? 'selected' : '' }}>Sangat Pasif</option>
                                    <option value="pasif" {{ old('keaktifan') == 'pasif' ? 'selected' : '' }}>Pasif</option>
                                    <option value="aktif" {{ old('keaktifan') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="sangat_aktif" {{ old('keaktifan') == 'sangat_aktif' ? 'selected' : '' }}>Sangat Aktif</option>
                                </select>
                                @error('keaktifan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pemahaman_materi" class="form-label">Pemahaman Materi Siswa</label>
                                <select name="pemahaman_materi" id="pemahaman_materi" class="form-select @error('pemahaman_materi') is-invalid @enderror" required>
                                    <option value="">Pilih Level</option>
                                    <option value="belum_paham" {{ old('pemahaman_materi') == 'belum_paham' ? 'selected' : '' }}>Belum Paham</option>
                                    <option value="sedikit_paham" {{ old('pemahaman_materi') == 'sedikit_paham' ? 'selected' : '' }}>Sedikit Paham</option>
                                    <option value="paham" {{ old('pemahaman_materi') == 'paham' ? 'selected' : '' }}>Paham</option>
                                    <option value="sangat_paham" {{ old('pemahaman_materi') == 'sangat_paham' ? 'selected' : '' }}>Sangat Paham</option>
                                </select>
                                @error('pemahaman_materi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="refleksi_siswa" class="form-label">Refleksi Siswa</label>
                                <textarea name="refleksi_siswa" id="refleksi_siswa" class="form-control @error('refleksi_siswa') is-invalid @enderror" required rows="3">{{ old('refleksi_siswa') }}</textarea>
                                @error('refleksi_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="refleksi_capaian" class="form-label">Refleksi Capaian</label>
                                <textarea name="refleksi_capaian" id="refleksi_capaian" class="form-control @error('refleksi_capaian') is-invalid @enderror" required rows="3">{{ old('refleksi_capaian') }}</textarea>
                                @error('refleksi_capaian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Section 4: Documentation -->
                        <div class="form-section">
                            <h5 class="section-header"><i class="fas fa-images me-2"></i>Dokumentasi Kegiatan</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Upload foto kegiatan dan absensi dengan format JPEG/PNG (maksimal 2MB)
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="foto_kegiatan" class="form-label">Foto Kegiatan</label>
                                    <input type="file" name="foto_kegiatan" id="foto_kegiatan" class="form-control @error('foto_kegiatan') is-invalid @enderror" accept="image/*">
                                    <img id="foto_kegiatan_preview" class="file-upload-preview" src="#" alt="Preview Foto Kegiatan">
                                    @error('foto_kegiatan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="foto_absensi_siswa" class="form-label">Foto Absensi Siswa</label>
                                    <input type="file" name="foto_absensi_siswa" id="foto_absensi_siswa" class="form-control @error('foto_absensi_siswa') is-invalid @enderror" accept="image/*">
                                    <img id="foto_absensi_preview" class="file-upload-preview" src="#" alt="Preview Foto Absensi">
                                    @error('foto_absensi_siswa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="saveDraftBtn">
                            <i class="fas fa-save me-1"></i> Simpan Draft
                        </button>
                        <div>
                            <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-1"></i> Simpan Laporan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for school search
        $('#sekolah-search').select2({
            theme: "bootstrap-5",
            placeholder: 'Ketik nama sekolah atau kode...',
            ajax: {
                url: "{{ url('/laporan-mengajar/search') }}",
                dataType: 'json',
                delay: 300,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(params) {
                    return {
                        q: params.term.trim()
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                error: function(xhr) {
                    console.error('Search error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data sekolah. Silakan coba lagi.'
                    });
                }
            },
            minimumInputLength: 3,
            language: {
                inputTooShort: function() {
                    return 'Ketik minimal 3 karakter';
                },
                errorLoading: function() {
                    return "Gagal memuat hasil. Coba lagi.";
                },
                noResults: function() {
                    return "Tidak ditemukan sekolah dengan kata kunci tersebut";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });

        // Date picker with Indonesian language
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

        // Image preview functionality
        function readURL(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $(previewId).attr('src', e.target.result).show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#foto_kegiatan").change(function() {
            readURL(this, '#foto_kegiatan_preview');
        });

        $("#foto_absensi_siswa").change(function() {
            readURL(this, '#foto_absensi_preview');
        });

        // Character counters for textareas
        function setupCharacterCounter(textareaId, counterId, maxLength) {
            $(textareaId).on('input', function() {
                var length = $(this).val().length;
                var remaining = maxLength - length;
                $(counterId).text(length + '/' + maxLength + ' karakter');

                if (remaining < 50) {
                    $(counterId).removeClass('warning danger').addClass('warning');
                }
                if (remaining < 20) {
                    $(counterId).removeClass('warning').addClass('danger');
                }
                if (remaining >= 50) {
                    $(counterId).removeClass('warning danger');
                }
            }).trigger('input');
        }

        setupCharacterCounter('#materi_pengajaran', '#materi-counter', 500);
        setupCharacterCounter('#refleksi_siswa', '#refleksi-counter', 300);
        setupCharacterCounter('#refleksi_capaian', '#capaian-counter', 300);

        // Save as draft functionality
        $('#saveDraftBtn').click(function() {
            Swal.fire({
                title: 'Simpan sebagai Draft?',
                text: "Anda dapat melanjutkan mengeditnya nanti",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan Draft',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add draft indicator to form
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'draft',
                        value: '1'
                    }).appendTo('#laporanForm');

                    // Submit form
                    $('#laporanForm').submit();
                }
            });
        });

        // Form validation before submit
        $('#laporanForm').submit(function() {
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

            return true;
        });

        // Auto-calculate duration
        $('.time-picker').on('change', function() {
            var startTime = $('#jam_mulai').val();
            var endTime = $('#jam_selesai').val();

            if (startTime && endTime) {
                var start = new Date('1970-01-01T' + startTime + ':00');
                var end = new Date('1970-01-01T' + endTime + ':00');
                var diff = (end - start) / 60000; // difference in minutes

                if (diff > 0) {
                    var hours = Math.floor(diff / 60);
                    var minutes = diff % 60;
                    var durationText = '';

                    if (hours > 0) durationText += hours + ' jam ';
                    if (minutes > 0) durationText += minutes + ' menit';

                    // You can display this somewhere if you add a duration display element
                    console.log('Durasi: ' + durationText.trim());
                }
            }
        });
    });
</script>
@endpush