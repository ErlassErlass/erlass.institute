@extends('layouts.app')

@section('title', 'Input Nilai Massal')

@push('styles')
<style>
    .input-cell {
        width: 60px;
        text-align: center;
        padding: 4px !important;
    }
    .input-cell input {
        text-align: center;
        padding: 5px;
        border-radius: 6px;
        font-size: 0.9rem;
    }
    .text-title-input {
        width: 140px;
        font-size: 0.85rem;
    }
    .catatan-input {
        width: 200px;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <a href="{{ route('student-scores.index', $rombel->id) }}" class="btn btn-sm btn-light border mb-2">
                <i class="bi bi-arrow-left"></i> Batal & Kembali
            </a>
            <h1 class="h3 fw-bold text-dark mb-1">Form Input Nilai Massal</h1>
            <p class="text-muted mb-0">Rombel: {{ $rombel->nama_rombel }} | {{ $rombel->ekstrakurikuler->kategori_program }}</p>
        </div>
    </div>

    <form action="{{ route('student-scores.store-bulk', $rombel->id) }}" method="POST">
        @csrf
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Input Nilai Siswa (Skala 0 - 100)</h5>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-save me-1"></i> Simpan Nilai
                </button>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" style="min-width: 1100px;">
                        <thead class="table-light text-center" style="font-size: 0.8rem;">
                            <tr>
                                <th rowspan="2" class="align-middle ps-3 text-start" style="width: 200px;">Nama Siswa</th>
                                <th colspan="4" class="bg-primary-subtle text-primary-emphasis py-2">Tugas & Kuis (30%)</th>
                                <th colspan="4" class="bg-success-subtle text-success-emphasis py-2">Sikap (20%)</th>
                                <th colspan="4" class="bg-warning-subtle text-warning-emphasis py-2">Proyek Akhir (20%)</th>
                                <th rowspan="2" class="align-middle" style="width: 150px;">Projek Scratch</th>
                                <th rowspan="2" class="align-middle ps-3 text-start" style="width: 220px;">Catatan Guru</th>
                            </tr>
                            <tr>
                                <th class="py-1">T1</th>
                                <th class="py-1">T2</th>
                                <th class="py-1">T3</th>
                                <th class="py-1">T4</th>
                                <th class="py-1">S1</th>
                                <th class="py-1">S2</th>
                                <th class="py-1">S3</th>
                                <th class="py-1">S4</th>
                                <th class="py-1">P1</th>
                                <th class="py-1">P2</th>
                                <th class="py-1">P3</th>
                                <th class="py-1">P4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $siswa)
                                @php
                                    $score = $scores[$siswa->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="ps-3 fw-bold text-dark text-truncate" style="max-width: 200px;">
                                        {{ $siswa->nama_lengkap }}
                                        <div class="text-muted x-small font-monospace" style="font-size: 0.75rem;">NISN: {{ $siswa->nisn }}</div>
                                    </td>
                                    
                                    <!-- Tugas 1-4 -->
                                    @for($i = 1; $i <= 4; $i++)
                                        <td class="input-cell bg-primary-subtle bg-opacity-10">
                                            <input type="number" step="0.01" min="0" max="100" 
                                                   name="scores[{{ $siswa->id }}][nilai_tugas_{{ $i }}]" 
                                                   class="form-control form-control-sm"
                                                   value="{{ $score ? $score->{'nilai_tugas_' . $i} : '' }}"
                                                   placeholder="-">
                                        </td>
                                    @endfor
                                    
                                    <!-- Sikap 1-4 -->
                                    @for($i = 1; $i <= 4; $i++)
                                        <td class="input-cell bg-success-subtle bg-opacity-10">
                                            <input type="number" step="0.01" min="0" max="100" 
                                                   name="scores[{{ $siswa->id }}][nilai_sikap_{{ $i }}]" 
                                                   class="form-control form-control-sm"
                                                   value="{{ $score ? $score->{'nilai_sikap_' . $i} : '' }}"
                                                   placeholder="-">
                                        </td>
                                    @endfor
                                    
                                    <!-- Proyek 1-4 -->
                                    @for($i = 1; $i <= 4; $i++)
                                        <td class="input-cell bg-warning-subtle bg-opacity-10">
                                            <input type="number" step="0.01" min="0" max="100" 
                                                   name="scores[{{ $siswa->id }}][nilai_proyek_{{ $i }}]" 
                                                   class="form-control form-control-sm"
                                                   value="{{ $score ? $score->{'nilai_proyek_' . $i} : '' }}"
                                                   placeholder="-">
                                        </td>
                                    @endfor
                                    
                                    <!-- Projek Scratch -->
                                    <td class="p-2">
                                        <input type="text" 
                                               name="scores[{{ $siswa->id }}][projek_scratch]" 
                                               class="form-control form-control-sm text-title-input"
                                               value="{{ $score ? $score->projek_scratch : '' }}"
                                               placeholder="cth: Pacman Game">
                                    </td>
                                    
                                    <!-- Catatan Guru -->
                                    <td class="p-2 pe-3">
                                        <input type="text" 
                                               name="scores[{{ $siswa->id }}][catatan_guru]" 
                                               class="form-control form-control-sm catatan-input"
                                               value="{{ $score ? $score->catatan_guru : '' }}"
                                               placeholder="cth: Sangat antusias...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white py-3 border-top text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-save me-1"></i> Simpan Nilai
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
