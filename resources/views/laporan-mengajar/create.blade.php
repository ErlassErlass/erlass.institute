@extends('layouts.app')

@section('title', 'Buat Laporan Mengajar')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 44px !important;
        padding-top: 6px !important;
        border-color: #dee2e6 !important;
        border-radius: 0.375rem !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #495057 !important;
        line-height: 1.5 !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-transparent py-3">
                    <h1 class="h4 mb-0 fw-bold text-gradient-primary"><i class="fas fa-plus-circle me-2"></i>Buat Laporan Mengajar</h1>
                </div>
                
                <div class="alert alert-warning m-3 border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle fa-2x me-3 text-warning mt-1"></i>
                        <div>
                            <h5 class="alert-heading fw-bold mb-1">Laporan Mengajar Khusus (Non-Jadwal)</h5>
                            <p class="mb-1">
                                Halaman ini digunakan khusus untuk mencatat penugasan kegiatan <strong>di luar program ekskul rutin</strong> yang tidak memiliki rombel resmi, seperti:
                            </p>
                            <ul class="mb-1">
                                <li>Kegiatan <strong>Inkul</strong> (Inkul Coding Scratch, Inkul LMS, Inkul LKPD, dll.)</li>
                                <li>Pameran Sekolah, Workshop Kilat, Pendampingan Lomba, atau Sosialisasi bersama Sales</li>
                            </ul>
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>PENTING:</strong> Untuk seluruh program ekskul reguler (<strong>termasuk jika menggantikan instruktur lain/inval atau kelas susulan</strong>), silakan buka menu <strong>Jadwal Sesi &amp; Laporan</strong> agar rekap rombel tidak terpisah.
                            </p>
                        </div>
                    </div>
                </div>

                @if(Auth::user()->role === 'instruktur')
                <div class="px-3 mb-2">
                    <div class="card border-0 bg-light shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <span class="fw-bold text-dark"><i class="bi bi-clock-history me-1 text-warning"></i> Tanggal Mengajar Lewat H+1?</span>
                                    <p class="text-muted small mb-0">Ajukan permohonan buka akses kegiatan khusus tanggal lampau ke Admin (Kuota tersisa: <strong>{{ Auth::user()->monthly_late_report_quota }}x</strong> bulan ini).</p>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-warning fw-bold" data-bs-toggle="modal" data-bs-target="#adhocRequestModal">
                                    <i class="bi bi-send me-1"></i> Minta Akses Khusus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif



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
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom"><i class="fas fa-user-tie me-2"></i>Informasi Instruktur</h5>
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

                            <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom mt-4"><i class="fas fa-school me-2"></i>Lokasi Mengajar</h5>
                            <div class="mb-3">
                                <label for="sekolah-search" class="form-label fw-bold">Cari & Pilih Sekolah <span class="text-danger">*</span></label>
                                <select name="sekolah_kodlan" id="sekolah-search" class="form-select @error('sekolah_kodlan') is-invalid @enderror" required>
                                    <option value=""></option>
                                    @if($selectedSekolah)
                                    <option value="{{ $selectedSekolah->kodlan }}" selected>
                                        {{ $selectedSekolah->namasekolah }} ({{ $selectedSekolah->kodlan }})
                                    </option>
                                    @endif
                                </select>
                                <small class="text-muted">Pilih atau ketik nama sekolah untuk mencari</small>
                                @error('sekolah_kodlan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
                                        <input type="number" name="pertemuan_ke" id="pertemuan_ke" class="form-control @error('pertemuan_ke') is-invalid @enderror" value="{{ old('pertemuan_ke') }}" required min="1">
                                    </div>
                                    @error('pertemuan_ke') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="rombel" class="form-label fw-bold">Nama Rombel <span class="text-danger small fw-semibold">(Bukan Jumlah Siswa)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-light-subtle"><i class="fas fa-layer-group text-primary"></i></span>
                                        <input type="text" name="rombel" id="rombel" class="form-control @error('rombel') is-invalid @enderror" value="{{ old('rombel') }}" placeholder="Contoh: 1A, 2B, Rombel A" required>
                                    </div>
                                    <small class="form-text text-muted"><i class="bi bi-info-circle text-primary me-1"></i>Isi dengan <strong>Nama Rombel</strong> (bukan jumlah siswa).</small>
                                    @error('rombel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="kategori_pengajaran" class="form-label">Kategori Pengajaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        <select name="kategori_pengajaran" id="kategori_pengajaran" class="form-select @error('kategori_pengajaran') is-invalid @enderror" required onchange="toggleFreeTrialFields()">
                                            <option value="">Pilih Kategori</option>
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
                                <div class="col-md-4 mb-3">
                                    <label for="jadwal_mengajar" class="form-label">Tanggal Mengajar <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fas fa-calendar-day"></i></span>
                                        <input type="date" name="jadwal_mengajar" id="jadwal_mengajar" class="form-control border-start-0 ps-0 @error('jadwal_mengajar') is-invalid @enderror" value="{{ old('jadwal_mengajar', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}" />
                                    </div>
                                    <div class="form-text small text-muted"><i class="fas fa-info-circle me-1"></i>Klik kolom di atas untuk memilih tanggal dari kalender</div>
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
                                    <small class="form-text text-muted">Durasi mengajar: 60 – 90 menit.</small>
                                </div>
                            </div>

                            <!-- Dynamic Section: Input Jumlah Siswa Khusus Free Trial Class -->
                            <div class="row bg-success bg-opacity-10 p-3 rounded mb-3 border border-success border-opacity-25" id="freeTrialStudentCountWrapper" style="display: none;">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="jumlah_siswa_hadir" class="form-label fw-bold text-success"><i class="fas fa-user-check me-1"></i>Jumlah Siswa Hadir Free Trial <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white"><i class="fas fa-users"></i></span>
                                        <input type="number" name="jumlah_siswa_hadir" id="jumlah_siswa_hadir" class="form-control @error('jumlah_siswa_hadir') is-invalid @enderror" value="{{ old('jumlah_siswa_hadir', 0) }}" min="0" placeholder="0">
                                    </div>
                                    <small class="form-text text-muted"><i class="bi bi-info-circle text-success me-1"></i>Masukkan total peserta yang HADIR pada <strong>Free Trial Class</strong> ini (digunakan untuk penentuan honor: &gt;6 siswa = Rp 100.000, ≤6 siswa = Rp 75.000).</small>
                                    @error('jumlah_siswa_hadir') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="jumlah_siswa_tidak_hadir" class="form-label fw-bold text-secondary"><i class="fas fa-user-times me-1"></i>Jumlah Siswa Tidak Hadir (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user-minus"></i></span>
                                        <input type="number" name="jumlah_siswa_tidak_hadir" id="jumlah_siswa_tidak_hadir" class="form-control @error('jumlah_siswa_tidak_hadir') is-invalid @enderror" value="{{ old('jumlah_siswa_tidak_hadir', 0) }}" min="0" placeholder="0">
                                    </div>
                                    <small class="form-text text-muted">Jumlah peserta trial yang berhalangan/batal hadir (opsional).</small>
                                    @error('jumlah_siswa_tidak_hadir') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="materi_pengajaran" class="form-label">Materi Pengajaran</label>
                                <textarea name="materi_pengajaran" id="materi_pengajaran" class="form-control @error('materi_pengajaran') is-invalid @enderror" rows="3" required placeholder="Tuliskan materi pengajaran...">{{ old('materi_pengajaran') }}</textarea>
                                @error('materi_pengajaran') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Evaluasi Section Removed -->

                        <!-- Section 3: Documentation -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom"><i class="fas fa-images me-2"></i>Dokumentasi Kegiatan</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Upload foto kegiatan dengan format JPEG/PNG (maksimal 5MB)
                            </div>

                            <div class="mb-3">
                                <label for="foto_kegiatan" class="form-label fw-bold">Foto Kegiatan <span class="text-danger">*</span></label>
                                <input type="file" name="foto_kegiatan" id="foto_kegiatan" class="form-control @error('foto_kegiatan') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" data-max-size="5242880" required>
                                <div class="form-text mt-1">
                                    <i class="bi bi-info-circle me-1"></i> Wajib diisi. Format: JPEG, PNG, JPG, GIF, WEBP | Maksimal: 5MB
                                </div>
                                @error('foto_kegiatan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <!-- Foto Absensi Input Removed -->
                        </div>
                    </div>

                    <div class="card-footer bg-light d-flex justify-content-between flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary" id="saveDraftBtn">
                            <i class="fas fa-save me-1"></i> Simpan Draft
                        </button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-secondary">
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

<script>
async function compressImageFile(file, maxWidth = 1600, maxHeight = 1600, quality = 0.82) {
    if (!file || !file.type.startsWith('image/')) return file;
    if (file.size <= 350 * 1024) return file;

    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
            const img = new Image();
            img.src = e.target.result;
            img.onload = () => {
                let width = img.width;
                let height = img.height;

                if (width > maxWidth || height > maxHeight) {
                    if (width > height) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    } else {
                        width = Math.round((width * maxHeight) / height);
                        height = maxHeight;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob((blob) => {
                    if (!blob || blob.size >= file.size) {
                        resolve(file);
                        return;
                    }
                    const cleanName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                    const compressedFile = new File([blob], cleanName, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });
                    resolve(compressedFile);
                }, 'image/jpeg', quality);
            };
            img.onerror = () => resolve(file);
        };
        reader.onerror = () => resolve(file);
    });
}

function toggleFreeTrialFields() {
    var select = document.getElementById('kategori_pengajaran');
    var wrapper = document.getElementById('freeTrialStudentCountWrapper');
    var inputHadir = document.getElementById('jumlah_siswa_hadir');
    
    if (!select || !wrapper) return;

    var val = (select.value || '').toLowerCase();
    var isTrial = val.indexOf('trial') !== -1 || val.indexOf('free') !== -1;

    if (isTrial) {
        wrapper.style.display = 'flex';
        if (inputHadir) inputHadir.setAttribute('required', 'required');
    } else {
        wrapper.style.display = 'none';
        if (inputHadir) inputHadir.removeAttribute('required');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleFreeTrialFields();

    const fotoInput = document.getElementById('foto_kegiatan');
    if (fotoInput) {
        fotoInput.addEventListener('change', async function() {
            if (this.files && this.files[0] && this.files[0].type.startsWith('image/')) {
                const originalSize = this.files[0].size;
                const compressed = await compressImageFile(this.files[0]);
                try {
                    const dt = new DataTransfer();
                    dt.items.add(compressed);
                    this.files = dt.files;
                } catch(e) {}
            }
        });
    }
});
</script>
@endsection

@push('modals')
<!-- Modal Permohonan Akses Ad-Hoc -->
@if(Auth::user()->role === 'instruktur')
<div class="modal fade" id="adhocRequestModal" tabindex="-1" aria-labelledby="adhocRequestModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('laporan-mengajar.adhoc-late-request.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold" id="adhocRequestModalLabel"><i class="bi bi-hourglass-split me-2"></i>Permohonan Buka Akses Laporan Khusus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">Gunakan form ini untuk mengajukan permohonan ke Admin jika Anda perlu membuat <strong>Laporan Khusus (Non-Jadwal)</strong> untuk tanggal kegiatan yang telah melewati batas <strong>H+1</strong>.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tanggal Kegiatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-calendar-date"></i></span>
                            <input type="text" name="adhoc_date" class="form-control datepicker @error('adhoc_date') is-invalid @enderror" placeholder="dd/mm/yyyy" required value="{{ old('adhoc_date', date('d/m/Y', strtotime('-2 days'))) }}">
                        </div>
                        <div class="form-text">Pilih tanggal kegiatan khusus yang telah lewat H+1.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Alasan Keterlambatan</label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Contoh: Terkendala jaringan saat di lapangan, urusan keluarga mendadak, dsb..."></textarea>
                    </div>

                    @if(Auth::user()->monthly_late_report_quota <= 0)
                        <div class="alert alert-danger mb-0 small">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Kuota bantuan bulanan Anda telah habis. Silakan hubungi Admin secara langsung.
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold" {{ Auth::user()->monthly_late_report_quota <= 0 ? 'disabled' : '' }}>
                        <i class="bi bi-send me-1"></i> Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endpush

<!-- Pending Sessions Modal (Keep existing) -->
<!-- ... -->

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 with retry logic to handle potential race conditions
        function initSekolahSelect2() {
            if (typeof $.fn.select2 === 'undefined') {
                console.warn('Select2 not loaded, retrying in 100ms...');
                setTimeout(initSekolahSelect2, 100);
                return;
            }

            $('#sekolah-search').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: 'Pilih atau ketik nama sekolah / kode...',
                allowClear: true,
                ajax: {
                    url: "{{ route('laporan-mengajar.search') }}",
                    dataType: 'json',
                    delay: 250,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(params) {
                        return {
                            q: (params.term || '').trim()
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    error: function(xhr) {
                        console.error('Search error:', xhr);
                    }
                },
                minimumInputLength: 0,
                language: {
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

            // Automatically focus and search initial school list when dropdown is opened
            $('#sekolah-search').on('select2:open', function() {
                setTimeout(function() {
                    let searchField = document.querySelector('.select2-container--open .select2-search__field') || document.querySelector('.select2-search__field');
                    if (searchField) {
                        searchField.focus();
                    }
                }, 50);
            });
        }

        initSekolahSelect2();

        // Native HTML5 date picker is used (matching registration wizard)

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

        // Image preview functionality is now handled by FormValidator globally

        // Helper: Dynamic Rombel Placeholder based on Category
        $('#kategori_pengajaran').change(function() {
            var kategori = $(this).val();
            var rombelInput = $('#rombel'); 

            // Dynamic Placeholder Logic
            if (kategori === 'Pameran') {
                rombelInput.attr('placeholder', 'Contoh: Booth Utama / Tim Pameran');
            } else if (kategori === 'Pendampingan Lomba') {
                rombelInput.attr('placeholder', 'Contoh: Nama Tim / Nama Event Lomba');
            } else if (kategori === 'Sosialisasi bersama Sales') {
                rombelInput.attr('placeholder', 'Contoh: Calon Orang Tua Siswa / Peserta Sosialisasi');
            } else if (kategori === 'Free Trial Class' || kategori === 'Trial Class') {
                rombelInput.attr('placeholder', 'Contoh: Nama Siswa Trial / Kelompok Trial');
            } else {
                rombelInput.attr('placeholder', 'Contoh: 1A, 2B, TK-A'); // Default
            }
        });

        // Trigger change on load if category is already selected
        if ($('#kategori_pengajaran').val()) {
            $('#kategori_pengajaran').trigger('change');
        }

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
                }
            }
        });

        // Toggle input jumlah siswa khusus untuk Kategori Ad-Hoc / Special Event
        function toggleFreeTrialFields() {
            var kat = ($('#kategori_pengajaran').val() || '').toLowerCase();
            var isAdHoc = kat.indexOf('trial') !== -1 
                || kat.indexOf('free') !== -1 
                || kat.indexOf('sosialisasi') !== -1 
                || kat.indexOf('pameran') !== -1 
                || kat.indexOf('lomba') !== -1 
                || kat.indexOf('pendampingan') !== -1 
                || kat.indexOf('event') !== -1 
                || kat.indexOf('per-pertemuan') !== -1 
                || kat.indexOf('inkul') !== -1 
                || kat.indexOf('mandiri') !== -1;
            
            if (isAdHoc) {
                $('#freeTrialStudentCountWrapper').removeClass('d-none').show();
                $('#jumlah_siswa_hadir').attr('required', 'required');
            } else {
                $('#freeTrialStudentCountWrapper').hide();
                $('#jumlah_siswa_hadir').removeAttr('required');
            }
        }

        $(document).on('change', '#kategori_pengajaran', function() {
            toggleFreeTrialFields();
        });

        // Run on page load
        setTimeout(function() {
            toggleFreeTrialFields();
        }, 100);
    });
</script>
<script>
    // Load Pending Sessions logic
    function loadPendingSessions() {
        const listContainer = document.getElementById('sessionsList');
        const loading = document.getElementById('loadingSessions');
        const empty = document.getElementById('emptySessions');
        
        listContainer.classList.add('d-none');
        empty.classList.add('d-none');
        loading.classList.remove('d-none');
        listContainer.innerHTML = '';

        fetch("{{ route('laporan-mengajar.pending-sessions') }}")
            .then(response => response.json())
            .then(data => {
                loading.classList.add('d-none');
                
                if (data.sessions.length === 0) {
                    empty.classList.remove('d-none');
                } else {
                    listContainer.classList.remove('d-none');
                    data.sessions.forEach(session => {
                        const item = document.createElement('a');
                        item.href = session.url;
                        item.className = 'list-group-item list-group-item-action p-3';
                        item.innerHTML = `
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-primary fw-bold">${session.program} - Pertemuan ${session.pertemuan_ke}</h6>
                                    <p class="mb-1 fw-bold">${session.sekolah}</p>
                                    <small class="text-muted"><i class="fas fa-users me-1"></i> ${session.rombel}</small>
                                </div>
                                <div class="text-end">
                                    <small class="fw-bold d-block">${session.tanggal}</small>
                                    <small class="text-muted">${session.jam}</small>
                                    <span class="badge bg-primary rounded-pill mt-2">Pilih <i class="fas fa-arrow-right ms-1"></i></span>
                                </div>
                            </div>
                        `;
                        listContainer.appendChild(item);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching sessions:', error);
                loading.classList.add('d-none');
                empty.innerHTML = '<p class="text-danger">Gagal memuat data. Silakan coba lagi.</p>';
                empty.classList.remove('d-none');
            });
    }
</script>
@endpush