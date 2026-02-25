@extends('layouts.app')

@section('title', 'Buat Laporan Mengajar')

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
                    <h1 class="h4 mb-0 fw-bold text-gradient-primary"><i class="fas fa-plus-circle me-2"></i>Buat Laporan Mengajar</h1>
                </div>
                
                <div class="alert alert-danger m-3 border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x me-3 text-danger"></i>
                        <div>
                            <h5 class="alert-heading fw-bold mb-1">DILARANG MENGISI UNTUK JADWAL RUTIN!</h5>
                            <p class="mb-0">
                                Halaman ini <strong>KHUSUS</strong> untuk membuat Laporan Mengajar <strong>DI LUAR JADWAL / TAMBAHAN</strong> (Ad-Hoc).
                                <br>
                                Jika Anda ingin mengisi laporan untuk kelas terjadwal, silakan buka menu <strong>Jadwal Mengajar</strong> atau <strong>Dashboard</strong>.
                            </p>
                        </div>
                    </div>
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
                                <label for="sekolah_kodlan" class="form-label">Cari & Pilih Sekolah</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <select name="sekolah_kodlan" id="sekolah-search" class="form-select @error('sekolah_kodlan') is-invalid @enderror" required>
                                        @if($selectedSekolah)
                                        <option value="{{ $selectedSekolah->kodlan }}" selected>
                                            {{ $selectedSekolah->namasekolah }} ({{ $selectedSekolah->kodlan }})
                                        </option>
                                        @endif
                                    </select>
                                </div>
                                <small class="text-muted">Ketik minimal 3 karakter untuk mencari sekolah</small>
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
                                <label for="foto_kegiatan" class="form-label">Foto Kegiatan</label>
                                <input type="file" name="foto_kegiatan" id="foto_kegiatan" class="form-control @error('foto_kegiatan') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif" data-max-size="5242880">
                                <div class="form-text mt-1">
                                    <i class="bi bi-info-circle me-1"></i> Format: JPEG, PNG, JPG, GIF | Maksimal: 5MB
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
@endsection

<!-- Pending Sessions Modal (Keep existing) -->
<!-- ... -->

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for school search
        $('#sekolah-search').select2({
            theme: "bootstrap-5",
            width: '100%',
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
            } else if (kategori === 'Trial Class') {
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
                    
                    // console.log('Durasi: ' + durationText.trim());
                }
            }
        });
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