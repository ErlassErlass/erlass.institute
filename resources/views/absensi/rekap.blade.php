@extends('layouts.app')

@section('title', 'Rekap Absensi (Invoice)')

@push('styles')
<style>
    .rekap-hero {
        background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #2563EB 100%);
        border-radius: 20px;
        color: #fff;
        padding: 2rem 2.25rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        position: relative;
        overflow: hidden;
    }
    .rekap-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 15%, rgba(255, 255, 255, 0.15) 0%, transparent 45%);
        pointer-events: none;
    }
    .glass-filter-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        padding: 1.5rem;
    }
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 10px !important;
        border-color: #CBD5E1 !important;
        padding: 0.45rem 0.75rem !important;
        min-height: 42px !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #1E293B !important;
        font-weight: 500 !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        border-color: #E2E8F0 !important;
    }
    .select2-search__field {
        border-radius: 8px !important;
        padding: 0.4rem 0.75rem !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="rekap-hero mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative z-1">
            <div>
                <div class="d-inline-flex align-items-center gap-2 mb-2 px-3 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.18);">
                    <i class="bi bi-file-earmark-spreadsheet-fill text-white"></i>
                    <span class="text-white small fw-bold text-uppercase">Laporan & Billing</span>
                </div>
                <h1 class="h2 fw-bold text-white mb-1">Rekap Absensi (Invoice)</h1>
                <p class="mb-0 text-white-50">Rekap kehadiran siswa per periode (4 pertemuan) untuk keperluan verifikasi invoice/tagihan.</p>
            </div>
            <div>
                <a href="{{ route('absensi.index') }}" class="btn btn-light btn-sm fw-semibold shadow-sm px-3.5 py-2 rounded-3 text-dark">
                    <i class="bi bi-arrow-left me-1.5"></i> Kembali ke Daftar Absensi
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="glass-filter-card mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-funnel-fill text-primary"></i> Filter Rekap Tagihan
            </h6>
            @if($selectedSekolah || $selectedEkskul || $selectedRombel)
                <a href="{{ route('rekap-absensi') }}" class="btn btn-link text-decoration-none text-muted small p-0">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                </a>
            @endif
        </div>

        <form action="{{ route('rekap-absensi') }}" method="GET" class="row g-3 align-items-end">
            <!-- Sekolah Search Input (Interactive Live Search, Not plain dropdown!) -->
            <div class="col-lg-4 col-md-6 position-relative">
                <label class="form-label fw-semibold text-secondary small mb-1">
                    <i class="bi bi-building me-1 text-primary"></i> Cari Sekolah <span class="text-danger">*</span>
                </label>
                
                <input type="hidden" name="sekolah_kodlan" id="sekolah_kodlan" value="{{ $selectedSekolah }}" required>

                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" 
                           id="sekolah_search_input" 
                           class="form-control ps-5 pe-5 rounded-3" 
                           placeholder="🔍 Ketik nama sekolah / KODLAN..." 
                           value="{{ $selectedSchoolName ? $selectedSchoolName . ' (' . $selectedSekolah . ')' : '' }}"
                           autocomplete="off">
                    <button type="button" 
                            id="clearSekolahSearch" 
                            class="btn btn-sm btn-link text-danger position-absolute top-50 end-0 translate-middle-y me-2 p-0 text-decoration-none" 
                            style="display: {{ $selectedSekolah ? 'block' : 'none' }};"
                            title="Hapus pilihan sekolah">
                        <i class="bi bi-x-circle-fill fs-6"></i>
                    </button>
                </div>

                <!-- Live Auto-complete Popover Suggestions -->
                <div id="sekolahSearchResults" class="dropdown-menu shadow-lg w-100 border-0 rounded-3 mt-1 py-1" style="max-height: 280px; overflow-y: auto; display: none; z-index: 1050;">
                </div>
            </div>

            <!-- Program Ekskul Dropdown -->
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold text-secondary small mb-1">
                    <i class="bi bi-journal-bookmark me-1 text-info"></i> Program Ekskul <span class="text-danger">*</span>
                </label>
                <select name="ekstrakurikuler_id" id="ekstrakurikuler_id" class="form-select select2-searchable" required {{ !$selectedSekolah ? 'disabled' : '' }} data-placeholder="🔍 Cari & pilih program...">
                    <option value=""></option>
                    @if($selectedSekolah)
                        @foreach($ekstrakurikulers as $ekskul)
                            <option value="{{ $ekskul->id }}" {{ $selectedEkskul == $ekskul->id ? 'selected' : '' }}>
                                {{ $ekskul->kategori_program }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Rombel Dropdown -->
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold text-secondary small mb-1">
                    <i class="bi bi-people me-1 text-success"></i> Pilih Rombel <span class="text-danger">*</span>
                </label>
                <select name="rombel" id="rombel" class="form-select select2-searchable" required {{ !$selectedSekolah ? 'disabled' : '' }} data-placeholder="🔍 Cari & pilih rombel...">
                    <option value=""></option>
                    @if($selectedSekolah)
                        @foreach($rombels as $r)
                            <option value="{{ $r }}" {{ $selectedRombel == $r ? 'selected' : '' }}>
                                {{ $r }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-lg-2 col-md-6">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-semibold w-100 shadow-sm" style="min-height: 42px;">
                        <i class="bi bi-search me-1.5"></i> Tampilkan
                    </button>
                    @if($selectedSekolah || $selectedEkskul || $selectedRombel)
                        <a href="{{ route('rekap-absensi') }}" class="btn btn-outline-secondary px-3" style="min-height: 42px;" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Data Section -->
    @if($selectedRombel)
        @if(!$rombelExists)
            <div class="alert alert-warning shadow-sm border-0 d-flex align-items-center gap-3 p-4 rounded-4">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-2"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Rombel Tidak Ditemukan</h5>
                    @if($selectedSchoolName)
                        Sekolah <strong>{{ $selectedSchoolName }}</strong> tidak memiliki rombel <strong>{{ $selectedRombel }}</strong>.
                    @else
                        Rombel <strong>{{ $selectedRombel }}</strong> tidak terdaftar di sistem.
                    @endif
                </div>
            </div>
        @else
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-table text-primary"></i> Hasil Rekap: {{ $selectedRombel }}
                            </h5>
                            <p class="small text-muted mb-0 mt-1">
                                <i class="bi bi-info-circle me-1"></i> Rule: Billable jika hadir &ge; 2x per periode (4 pertemuan)
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2.5 flex-wrap ms-auto">
                            <!-- Instant In-Table Search Input -->
                            <div class="position-relative" style="min-width: 260px;">
                                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" id="siswaTableSearch" class="form-control form-control-sm ps-5 rounded-pill border-secondary-subtle" placeholder="Cari nama siswa di tabel...">
                            </div>
                            <a href="{{ route('rekap-absensi.export', ['sekolah_kodlan' => $selectedSekolah, 'ekstrakurikuler_id' => $selectedEkskul, 'rombel' => $selectedRombel]) }}" 
                               class="btn btn-success btn-sm fw-semibold px-3 py-1.5 rounded-3 shadow-xs">
                                <i class="bi bi-file-earmark-excel me-1.5"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0" id="rekapTable">
                            <thead class="table-dark text-center align-middle">
                                <tr>
                                    <th rowspan="2" style="width: 50px;">No</th>
                                    <th rowspan="2" class="text-start ps-3" style="min-width: 220px;">Nama Siswa</th>
                                    @foreach($rekapData as $period)
                                        <th colspan="1">Periode {{ $period['index'] }}</th>
                                    @endforeach
                                </tr>
                                <tr class="table-secondary text-dark">
                                    @foreach($rekapData as $period)
                                        <th class="small text-muted fw-semibold">
                                            {{ $period['dates'] }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="rekapTableBody">
                                @foreach($students as $index => $student)
                                    <tr class="student-row">
                                        <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                                        <td class="fw-bold ps-3 text-dark student-name">{{ $student->nama_lengkap }}</td>
                                        @foreach($rekapData as $period)
                                            @php
                                                $stats = $period['student_stats'][$student->id] ?? ['count' => 0, 'is_billable' => false];
                                                $bgClass = $stats['is_billable'] ? 'bg-success-subtle text-success' : 'bg-light text-muted';
                                                $icon = $stats['is_billable'] ? 'bi-check-circle-fill' : 'bi-dash-circle';
                                            @endphp
                                            <td class="text-center {{ $bgClass }}">
                                                <div class="d-flex flex-column align-items-center py-1">
                                                    <span class="fw-bold fs-5 mb-0.5">{{ $stats['count'] }} / 4</span>
                                                    <span class="badge {{ $stats['is_billable'] ? 'bg-success text-white' : 'bg-secondary text-white border' }} px-2 py-1 rounded-pill extra-small d-flex align-items-center gap-1">
                                                        <i class="bi {{ $icon }}"></i>
                                                        {{ $stats['is_billable'] ? 'Billable' : 'Skip' }}
                                                    </span>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                
                                @if($students->isEmpty())
                                    <tr>
                                        <td colspan="{{ count($rekapData) + 2 }}" class="text-center py-4 text-muted">
                                            Tidak ada data siswa atau laporan ditemukan untuk filter ini.
                                        </td>
                                    </tr>
                                @endif
                                <tr id="noSearchResult" style="display: none;">
                                    <td colspan="{{ count($rekapData) + 2 }}" class="text-center py-4 text-muted">
                                        <i class="bi bi-search me-1"></i> Tidak ditemukan siswa dengan kata kunci tersebut.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @elseif(request()->has('rombel'))
        <div class="alert alert-info shadow-sm rounded-4 d-flex align-items-center gap-3 p-4">
            <i class="bi bi-info-circle-fill text-info fs-3"></i>
            <div>Silakan pilih Sekolah, Program Ekskul, dan Rombel untuk melihat rekap tagihan.</div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // 0. Interactive Live Text Search for Sekolah
        var schoolList = [
            @foreach($sekolahs as $sekolah)
                { kodlan: "{{ $sekolah->kodlan }}", name: @json($sekolah->namasekolah) },
            @endforeach
        ];

        $('#sekolah_search_input').on('focus input keyup', function() {
            var query = $(this).val().toLowerCase().trim();
            var $results = $('#sekolahSearchResults');
            $results.empty();

            var matches = schoolList.filter(function(s) {
                return s.name.toLowerCase().indexOf(query) !== -1 || s.kodlan.indexOf(query) !== -1;
            });

            if (matches.length === 0) {
                $results.html('<div class="dropdown-item text-muted small py-2 text-center"><i class="bi bi-exclamation-circle me-1"></i> Tidak ditemukan sekolah cocok</div>').show();
            } else {
                renderSchoolResults(matches.slice(0, 20));
                $results.show();
            }
        });

        function renderSchoolResults(list) {
            var $results = $('#sekolahSearchResults');
            $.each(list, function(i, s) {
                var $item = $('<a class="dropdown-item py-2 px-3 border-bottom border-light cursor-pointer d-flex justify-content-between align-items-center"></a>');
                $item.html('<div><strong class="text-dark d-block small mb-0.5">' + s.name + '</strong><span class="text-muted extra-small">KODLAN: ' + s.kodlan + '</span></div><i class="bi bi-chevron-right text-muted extra-small"></i>');
                $item.on('mousedown click', function(e) {
                    e.preventDefault();
                    selectSchool(s.kodlan, s.name);
                });
                $results.append($item);
            });
        }

        function selectSchool(kodlan, name) {
            $('#sekolah_kodlan').val(kodlan);
            $('#sekolah_search_input').val(name + ' (' + kodlan + ')');
            $('#clearSekolahSearch').show();
            $('#sekolahSearchResults').hide();
            $('#sekolah_kodlan').trigger('change');
        }

        $('#clearSekolahSearch').on('click', function() {
            $('#sekolah_kodlan').val('');
            $('#sekolah_search_input').val('').focus();
            $(this).hide();
            $('#sekolah_kodlan').trigger('change');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#sekolah_search_input, #sekolahSearchResults').length) {
                $('#sekolahSearchResults').hide();
            }
        });

        // Initialize Searchable Select2 for Program & Rombel
        function initSearchableSelect2() {
            $('.select2-searchable').each(function() {
                var placeholder = $(this).data('placeholder') || 'Pilih opsi...';
                $(this).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: placeholder,
                    allowClear: true
                });
            });
        }
        initSearchableSelect2();

        // 1. Dynamic Program Filter based on Sekolah selection
        $('#sekolah_kodlan').on('change', function() {
            var sekolahKodlan = $(this).val();
            var $ekskulSelect = $('#ekstrakurikuler_id');
            var $rombelSelect = $('#rombel');

            if (!sekolahKodlan) {
                $ekskulSelect.empty().append('<option value=""></option>').prop('disabled', true).trigger('change.select2');
                $rombelSelect.empty().append('<option value=""></option>').prop('disabled', true).trigger('change.select2');
                return;
            }

            $ekskulSelect.prop('disabled', false).empty().append('<option value="">-- Mohon Tunggu... --</option>').trigger('change.select2');
            $rombelSelect.empty().append('<option value=""></option>').prop('disabled', true).trigger('change.select2');

            // Fetch Programs for selected school
            $.ajax({
                url: "{{ route('rekap-absensi.programs') }}",
                type: 'GET',
                data: { sekolah_kodlan: sekolahKodlan },
                dataType: 'json',
                success: function(data) {
                    $ekskulSelect.empty().append('<option value=""></option>');
                    if (data.length === 0) {
                        $ekskulSelect.append('<option value="" disabled>-- Sekolah ini tidak memiliki Program Ekskul --</option>');
                    } else {
                        $.each(data, function(key, val) {
                            $ekskulSelect.append('<option value="' + val.id + '">' + val.kategori_program + '</option>');
                        });
                    }
                    $ekskulSelect.trigger('change.select2');
                }
            });
        });

        // 2. Dynamic Rombel Filter based on Program selection
        $('#ekstrakurikuler_id').on('change', function() {
            var sekolahKodlan = $('#sekolah_kodlan').val();
            var ekskulId = $(this).val();
            var $rombelSelect = $('#rombel');

            if (!ekskulId) {
                $rombelSelect.empty().append('<option value=""></option>').prop('disabled', true).trigger('change.select2');
                return;
            }

            $rombelSelect.prop('disabled', false).empty().append('<option value="">-- Mohon Tunggu... --</option>').trigger('change.select2');

            $.ajax({
                url: "{{ route('rekap-absensi.rombels') }}",
                type: 'GET',
                data: { sekolah_kodlan: sekolahKodlan, ekstrakurikuler_id: ekskulId },
                dataType: 'json',
                success: function(data) {
                    $rombelSelect.empty().append('<option value=""></option>');
                    if (data.length === 0) {
                        $rombelSelect.append('<option value="" disabled>-- Program ini tidak memiliki Rombel --</option>');
                    } else {
                        $.each(data, function(key, val) {
                            $rombelSelect.append('<option value="' + val + '">' + val + '</option>');
                        });
                    }
                    $rombelSelect.trigger('change.select2');
                }
            });
        });

        // 3. Real-Time In-Table Student Search
        $('#siswaTableSearch').on('keyup search input', function() {
            var query = $(this).val().toLowerCase().trim();
            var visibleCount = 0;

            $('.student-row').each(function() {
                var studentName = $(this).find('.student-name').text().toLowerCase();
                if (studentName.indexOf(query) !== -1 || query === '') {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            if (visibleCount === 0 && query !== '') {
                $('#noSearchResult').show();
            } else {
                $('#noSearchResult').hide();
            }
        });
    });
</script>
@endpush
