@extends('layouts.app')

@section('title', 'Rekap Absensi (Invoice)')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Rekap Absensi (Invoice)</h1>
                    <p class="text-muted mb-0">Rekap kehadiran siswa per periode (4 pertemuan) untuk keperluan invoice/tagihan.</p>
                </div>
                <div>
                    <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <form action="{{ route('rekap-absensi') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Pilih Sekolah <span class="text-danger">*</span></label>
                    <select name="sekolah_kodlan" id="sekolah_kodlan" class="form-select select2" required>
                        <option value="">-- Pilih Sekolah --</option>
                        @foreach($sekolahs as $sekolah)
                            <option value="{{ $sekolah->kodlan }}" {{ $selectedSekolah == $sekolah->kodlan ? 'selected' : '' }}>
                                {{ $sekolah->namasekolah }} ({{ $sekolah->kodlan }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Program Ekskul <span class="text-danger">*</span></label>
                    <select name="ekstrakurikuler_id" id="ekstrakurikuler_id" class="form-select select2" required {{ !$selectedSekolah ? 'disabled' : '' }}>
                        @if(!$selectedSekolah)
                            <option value="">-- Pilih Sekolah Terlebih Dahulu --</option>
                        @else
                            <option value="">-- Pilih Program Ekskul --</option>
                            @foreach($ekstrakurikulers as $ekskul)
                                <option value="{{ $ekskul->id }}" {{ $selectedEkskul == $ekskul->id ? 'selected' : '' }}>
                                    {{ $ekskul->kategori_program }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Pilih Rombel <span class="text-danger">*</span></label>
                    <select name="rombel" id="rombel" class="form-select select2" required {{ !$selectedSekolah ? 'disabled' : '' }}>
                        @if(!$selectedSekolah)
                            <option value="">-- Pilih Sekolah Terlebih Dahulu --</option>
                        @else
                            <option value="">-- Pilih Rombel --</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r }}" {{ $selectedRombel == $r ? 'selected' : '' }}>
                                    {{ $r }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Section -->
    @if($selectedRombel)
        @if(!$rombelExists)
            <div class="alert alert-warning shadow-sm border-0 d-flex align-items-center gap-3 p-4">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Rombel Tidak Ditemukan</h5>
                    @if($selectedSchoolName)
                        Sekolah <strong>{{ $selectedSchoolName }}</strong> tidak memiliki <strong>{{ $selectedRombel }}</strong>.
                    @else
                        Rombel <strong>{{ $selectedRombel }}</strong> tidak terdaftar di sistem.
                    @endif
                </div>
            </div>
        @else
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark">Hasil Rekap: {{ $selectedRombel }}</h5>
                            <p class="small text-muted mb-0 mt-1">
                                <i class="bi bi-info-circle me-1"></i> Rule: Billable jika hadir >= 2x per periode (4 pertemuan)
                            </p>
                        </div>
                        <a href="{{ route('rekap-absensi.export', ['sekolah_kodlan' => $selectedSekolah, 'ekstrakurikuler_id' => $selectedEkskul, 'rombel' => $selectedRombel]) }}" 
                           class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th rowspan="2" style="width: 50px;">No</th>
                                    <th rowspan="2" class="text-start ps-3">Nama Siswa</th>
                                    @foreach($rekapData as $period)
                                        <th colspan="1">Periode {{ $period['index'] }}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($rekapData as $period)
                                        <th class="small text-muted fw-normal">
                                            {{ $period['dates'] }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="fw-medium ps-3">{{ $student->nama_lengkap }}</td>
                                        @foreach($rekapData as $period)
                                            @php
                                                $stats = $period['student_stats'][$student->id] ?? ['count' => 0, 'is_billable' => false];
                                                $bgClass = $stats['is_billable'] ? 'bg-success-subtle text-success' : 'text-muted';
                                                $icon = $stats['is_billable'] ? 'bi-check-circle-fill' : 'bi-dash-circle';
                                            @endphp
                                            <td class="text-center {{ $bgClass }}">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="fw-bold fs-5">{{ $stats['count'] }} / 4</span>
                                                    <small class="d-flex align-items-center gap-1">
                                                        <i class="bi {{ $icon }}"></i>
                                                        {{ $stats['is_billable'] ? 'Billable' : 'Skip' }}
                                                    </small>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @elseif(request()->has('rombel'))
        <div class="alert alert-info shadow-sm">
            <i class="bi bi-info-circle me-2"></i> Silakan pilih Sekolah, Program Ekskul, dan Rombel untuk melihat rekap.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        // 1. Dynamic Program Filter based on Sekolah selection
        $('#sekolah_kodlan').on('change', function() {
            var sekolahKodlan = $(this).val();
            var $ekskulSelect = $('#ekstrakurikuler_id');
            var $rombelSelect = $('#rombel');

            if (!sekolahKodlan) {
                $ekskulSelect.empty().append('<option value="">-- Pilih Sekolah Terlebih Dahulu --</option>').prop('disabled', true).trigger('change');
                $rombelSelect.empty().append('<option value="">-- Pilih Program Ekskul Terlebih Dahulu --</option>').prop('disabled', true).trigger('change');
                return;
            }

            $ekskulSelect.prop('disabled', false).empty().append('<option value="">-- Mohon Tunggu... --</option>').trigger('change');
            $rombelSelect.empty().append('<option value="">-- Pilih Program Ekskul Terlebih Dahulu --</option>').prop('disabled', true).trigger('change');

            // Fetch Programs for selected school
            $.ajax({
                url: "{{ route('rekap-absensi.programs') }}",
                type: 'GET',
                data: { sekolah_kodlan: sekolahKodlan },
                dataType: 'json',
                success: function(data) {
                    $ekskulSelect.empty();
                    if (data.length === 0) {
                        $ekskulSelect.append('<option value="">-- Sekolah ini tidak memiliki Program Ekskul --</option>');
                    } else {
                        $ekskulSelect.append('<option value="">-- Pilih Program Ekskul --</option>');
                        $.each(data, function(key, val) {
                            $ekskulSelect.append('<option value="' + val.id + '">' + val.kategori_program + '</option>');
                        });
                    }
                    $ekskulSelect.trigger('change');
                }
            });
        });

        // 2. Dynamic Rombel Filter based on Program selection
        $('#ekstrakurikuler_id').on('change', function() {
            var sekolahKodlan = $('#sekolah_kodlan').val();
            var ekskulId = $(this).val();
            var $rombelSelect = $('#rombel');

            if (!ekskulId) {
                $rombelSelect.empty().append('<option value="">-- Pilih Program Ekskul Terlebih Dahulu --</option>').prop('disabled', true).trigger('change');
                return;
            }

            $rombelSelect.prop('disabled', false).empty().append('<option value="">-- Mohon Tunggu... --</option>').trigger('change');

            $.ajax({
                url: "{{ route('rekap-absensi.rombels') }}",
                type: 'GET',
                data: { sekolah_kodlan: sekolahKodlan, ekstrakurikuler_id: ekskulId },
                dataType: 'json',
                success: function(data) {
                    $rombelSelect.empty();
                    if (data.length === 0) {
                        $rombelSelect.append('<option value="">-- Program ini tidak memiliki Rombel --</option>');
                    } else {
                        $rombelSelect.append('<option value="">-- Pilih Rombel --</option>');
                        $.each(data, function(key, val) {
                            $rombelSelect.append('<option value="' + val + '">' + val + '</option>');
                        });
                    }
                    $rombelSelect.trigger('change');
                }
            });
        });
    });
</script>
@endpush
