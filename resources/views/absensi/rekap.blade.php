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
                    <p class="text-muted mb-0">Rekap kehadiran siswa untuk keperluan invoice/tagihan.</p>
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
    @if($selectedRombel && $rombelExists)
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                Rekap Absensi: {{ $selectedSchoolName }} — Rombel {{ $selectedRombel }}
            </h5>
            <a href="{{ route('rekap-absensi.export', ['sekolah_kodlan' => $selectedSekolah, 'ekstrakurikuler_id' => $selectedEkskul, 'rombel' => $selectedRombel]) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
        </div>

        @if(empty($rekapData))
            <div class="alert alert-warning shadow-sm">
                <i class="bi bi-exclamation-triangle me-2"></i> Belum ada data laporan mengajar yang ditemukan untuk rombel ini.
            </div>
        @else
            <!-- Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Nama Siswa</th>
                                    @foreach($rekapData as $period)
                                        <th class="text-center" style="min-width: 140px;">
                                            <div>Periode #{{ $period['index'] }}</div>
                                            <small class="text-muted fw-normal" style="font-size: 0.75rem;">
                                                {{ $period['dates'] }}
                                            </small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $student->nama_lengkap }}</td>
                                        @foreach($rekapData as $period)
                                            @php
                                                $stat = $period['student_stats'][$student->id] ?? ['count' => 0, 'is_billable' => false];
                                            @endphp
                                            <td class="text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="badge {{ $stat['is_billable'] ? 'bg-success' : 'bg-danger' }} fs-6 mb-1">
                                                        {{ $stat['count'] }} / 4 Sesi
                                                    </span>
                                                    <small class="{{ $stat['is_billable'] ? 'text-success fw-bold' : 'text-danger' }}" style="font-size: 0.75rem;">
                                                        {{ $stat['is_billable'] ? '✓ Masuk Invoice' : '✗ Tidak Masuk' }}
                                                    </small>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
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

        // Dynamic Program & Rombel Filter based on Sekolah selection
        $('#sekolah_kodlan').on('change', function() {
            var sekolahKodlan = $(this).val();
            var $ekskulSelect = $('#ekstrakurikuler_id');
            var $rombelSelect = $('#rombel');

            if (!sekolahKodlan) {
                $ekskulSelect.empty().append('<option value="">-- Pilih Sekolah Terlebih Dahulu --</option>').prop('disabled', true).trigger('change');
                $rombelSelect.empty().append('<option value="">-- Pilih Sekolah Terlebih Dahulu --</option>').prop('disabled', true).trigger('change');
                return;
            }

            $ekskulSelect.prop('disabled', false).empty().append('<option value="">-- Mohon Tunggu... --</option>').trigger('change');
            $rombelSelect.prop('disabled', false).empty().append('<option value="">-- Mohon Tunggu... --</option>').trigger('change');

            // Fetch Programs
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

            // Fetch Rombels
            fetchRombels(sekolahKodlan, null);
        });

        $('#ekstrakurikuler_id').on('change', function() {
            var sekolahKodlan = $('#sekolah_kodlan').val();
            var ekskulId = $(this).val();
            if (sekolahKodlan) {
                fetchRombels(sekolahKodlan, ekskulId);
            }
        });

        function fetchRombels(sekolahKodlan, ekskulId) {
            var $rombelSelect = $('#rombel');
            $.ajax({
                url: "{{ route('rekap-absensi.rombels') }}",
                type: 'GET',
                data: { sekolah_kodlan: sekolahKodlan, ekstrakurikuler_id: ekskulId },
                dataType: 'json',
                success: function(data) {
                    $rombelSelect.empty();
                    if (data.length === 0) {
                        $rombelSelect.append('<option value="">-- Tidak ada Rombel --</option>');
                    } else {
                        $rombelSelect.append('<option value="">-- Pilih Rombel --</option>');
                        $.each(data, function(key, val) {
                            $rombelSelect.append('<option value="' + val + '">' + val + '</option>');
                        });
                    }
                    $rombelSelect.trigger('change');
                }
            });
        }
    });
</script>
@endpush
