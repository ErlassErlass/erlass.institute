@extends('layouts.app')

@section('title', 'Edit Laporan Mengajar')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<style>
    .card-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-bottom: none;
    }
    .section-header {
        color: #4e73df;
        font-weight: 600;
        border-left: 4px solid #4e73df;
        padding-left: 10px;
        margin-bottom: 1rem;
    }
    .form-section {
        background-color: #f8f9fc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        border-left: 3px solid #4e73df;
    }
    .img-thumbnail {
        max-width: 200px;
        height: auto;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        background-color: white;
    }
    .input-group-text {
        background-color: #e9ecef;
    }
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
    }
    .time-picker {
        cursor: pointer;
    }
    .character-counter {
        font-size: 0.8rem;
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
            <div class="card shadow-lg">
                <div class="card-header">
                    <h1 class="h4 mb-0"><i class="fas fa-edit me-2"></i>Edit Laporan Mengajar</h1>
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
                        <div class="form-section">
                            <h5 class="section-header"><i class="fas fa-user-tie me-2"></i>Informasi Instruktur</h5>
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

                            <h5 class="section-header mt-4"><i class="fas fa-school me-2"></i>Lokasi Mengajar</h5>
                            <div class="mb-3">
                                <label for="kodlan" class="form-label">Cari & Pilih Sekolah</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <select name="kodlan" id="sekolah-search" class="form-select @error('kodlan') is-invalid @enderror" required>
                                        <option value="{{ $laporanMengajar->kodlan }}" selected>
                                            {{ $laporanMengajar->sekolah_nama }} ({{ $laporanMengajar->kodlan }})
                                        </option>
                                    </select>
                                </div>
                                @error('kodlan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
@php
    try {
        $tanggalParsed = \Carbon\Carbon::createFromFormat('Y-m-d', $laporanMengajar->jadwal_mengajar)->format('d/m/Y');
    } catch (\Exception $e) {
        try {
            $tanggalParsed = \Carbon\Carbon::createFromFormat('d/m/Y', $laporanMengajar->jadwal_mengajar)->format('d/m/Y');
        } catch (\Exception $e2) {
            $tanggalParsed = '';
        }
    }
@endphp

<input type="text" name="jadwal_mengajar" id="jadwal_mengajar"
    class="form-control @error('jadwal_mengajar') is-invalid @enderror"
    value="{{ old('jadwal_mengajar', $tanggalParsed) }}"
    required placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    @error('jadwal_mengajar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        <input type="text" name="jam_mulai" id="jam_mulai"
                                            value="{{ old('jam_mulai', \Carbon\Carbon::createFromFormat('H:i:s', $laporanMengajar->jam_mulai)->format('H:i')) }}"
                                            class="form-control @error('jam_mulai') is-invalid @enderror"
                                            required placeholder="HH:mm" autocomplete="off">
                                    </div>
                                    @error('jam_mulai') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        <input type="text" name="jam_selesai" id="jam_selesai"
                                            value="{{ old('jam_selesai', \Carbon\Carbon::createFromFormat('H:i:s', $laporanMengajar->jam_selesai)->format('H:i')) }}"
                                            class="form-control @error('jam_selesai') is-invalid @enderror"
                                            required placeholder="HH:mm" autocomplete="off">
                                    </div>
                                    @error('jam_selesai') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="materi_pengajaran" class="form-label">Materi Pengajaran</label>
                                <textarea name="materi_pengajaran" id="materi_pengajaran" class="form-control @error('materi_pengajaran') is-invalid @enderror" required rows="3">{{ old('materi_pengajaran', $laporanMengajar->materi_pengajaran) }}</textarea>
                                <div class="character-counter" id="materi-counter">0/500 karakter</div>
                                @error('materi_pengajaran') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                            <h5 class="section-header mt-4"><i class="fas fa-chart-line me-2"></i>Evaluasi Pembelajaran</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="keaktifan" class="form-label">Keaktifan Siswa</label>
                                    <select name="keaktifan" id="keaktifan" class="form-select @error('keaktifan') is-invalid @enderror" required>
                                        <option value="sangat_pasif" {{ old('keaktifan', $laporanMengajar->keaktifan) == 'sangat_pasif' ? 'selected' : '' }}>Sangat Pasif</option>
                                        <option value="pasif" {{ old('keaktifan', $laporanMengajar->keaktifan) == 'pasif' ? 'selected' : '' }}>Pasif</option>
                                        <option value="aktif" {{ old('keaktifan', $laporanMengajar->keaktifan) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="sangat_aktif" {{ old('keaktifan', $laporanMengajar->keaktifan) == 'sangat_aktif' ? 'selected' : '' }}>Sangat Aktif</option>
                                    </select>
                                    @error('keaktifan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pemahaman_materi" class="form-label">Pemahaman Materi Siswa</label>
                                    <select name="pemahaman_materi" id="pemahaman_materi" class="form-select @error('pemahaman_materi') is-invalid @enderror" required>
                                        <option value="belum_paham" {{ old('pemahaman_materi', $laporanMengajar->pemahaman_materi) == 'belum_paham' ? 'selected' : '' }}>Belum Paham</option>
                                        <option value="sedikit_paham" {{ old('pemahaman_materi', $laporanMengajar->pemahaman_materi) == 'sedikit_paham' ? 'selected' : '' }}>Sedikit Paham</option>
                                        <option value="paham" {{ old('pemahaman_materi', $laporanMengajar->pemahaman_materi) == 'paham' ? 'selected' : '' }}>Paham</option>
                                        <option value="sangat_paham" {{ old('pemahaman_materi', $laporanMengajar->pemahaman_materi) == 'sangat_paham' ? 'selected' : '' }}>Sangat Paham</option>
                                    </select>
                                    @error('pemahaman_materi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="refleksi_siswa" class="form-label">Refleksi Siswa</label>
                                    <textarea name="refleksi_siswa" id="refleksi_siswa" class="form-control @error('refleksi_siswa') is-invalid @enderror" required rows="3">{{ old('refleksi_siswa', $laporanMengajar->refleksi_siswa) }}</textarea>
                                    <div class="character-counter" id="refleksi-counter">0/300 karakter</div>
                                    @error('refleksi_siswa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="refleksi_capaian" class="form-label">Refleksi Capaian</label>
                                    <textarea name="refleksi_capaian" id="refleksi_capaian" class="form-control @error('refleksi_capaian') is-invalid @enderror" required rows="3">{{ old('refleksi_capaian', $laporanMengajar->refleksi_capaian) }}</textarea>
                                    <div class="character-counter" id="capaian-counter">0/300 karakter</div>
                                    @error('refleksi_capaian') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
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
                                    <input type="file" name="foto_kegiatan" id="foto_kegiatan" class="form-control @error('foto_kegiatan') is-invalid @enderror" accept="image/*">
                                    @error('foto_kegiatan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Foto Absensi Siswa</label>
                                    @if($laporanMengajar->foto_absensi_siswa)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $laporanMengajar->foto_absensi_siswa) }}" alt="Foto Absensi" class="img-thumbnail">
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="hapus_foto_absensi" id="hapus_foto_absensi" value="1">
                                                <label class="form-check-label" for="hapus_foto_absensi">
                                                    Hapus foto saat ini
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" name="foto_absensi_siswa" id="foto_absensi_siswa" class="form-control @error('foto_absensi_siswa') is-invalid @enderror" accept="image/*">
                                    @error('foto_absensi_siswa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
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