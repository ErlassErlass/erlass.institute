@extends('layouts.app')

@section('title', 'Edit Enrollment - ' . $enrollment->siswa->nama_lengkap)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>Edit Enrollment Siswa
                        </h4>
                        <a href="{{ route('ekstrakurikuler.enrollment.show', [$ekstrakurikuler, $enrollment]) }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Info Siswa --}}
                    <div class="alert alert-info border-start border-4 border-info mb-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md bg-primary rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0">
                                <span class="text-white fw-bold fs-5">{{ substr($enrollment->siswa->nama_lengkap, 0, 1) }}</span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold">{{ $enrollment->siswa->nama_lengkap }}</h6>
                                <small class="text-muted">
                                    @if($enrollment->siswa->nisn) NISN: {{ $enrollment->siswa->nisn }} &bull; @endif
                                    Kelas: {{ $enrollment->siswa->rombel ?? '-' }}
                                </small>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <i class="bi bi-trophy me-1"></i>{{ $ekstrakurikuler->kategori_program }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('ekstrakurikuler.enrollment.update', [$ekstrakurikuler, $enrollment]) }}">
                        @csrf
                        @method('PUT')

                        {{-- Rombel --}}
                        <div class="mb-4">
                            <label for="ekstrakurikuler_rombel_id" class="form-label">
                                <i class="bi bi-people me-1"></i>Rombel Ekstrakurikuler
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('ekstrakurikuler_rombel_id') is-invalid @enderror"
                                    id="ekstrakurikuler_rombel_id" name="ekstrakurikuler_rombel_id" required>
                                <option value="">Pilih Rombel...</option>
                                @foreach($rombels as $rombel)
                                    <option value="{{ $rombel->id }}"
                                        {{ old('ekstrakurikuler_rombel_id', $enrollment->ekstrakurikuler_rombel_id) == $rombel->id ? 'selected' : '' }}>
                                        {{ $rombel->nama_rombel }}
                                        ({{ $rombel->getJumlahSiswaAktual() }}/{{ $rombel->jumlah_siswa }} siswa)
                                    </option>
                                @endforeach
                            </select>
                            @error('ekstrakurikuler_rombel_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label for="status" class="form-label">
                                <i class="bi bi-tag me-1"></i>Status
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="aktif"    {{ old('status', $enrollment->status) === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                                <option value="lulus"    {{ old('status', $enrollment->status) === 'lulus'    ? 'selected' : '' }}>Lulus</option>
                                <option value="keluar"   {{ old('status', $enrollment->status) === 'keluar'   ? 'selected' : '' }}>Keluar</option>
                                <option value="pindah"   {{ old('status', $enrollment->status) === 'pindah'   ? 'selected' : '' }}>Pindah Rombel</option>
                                <option value="nonaktif" {{ old('status', $enrollment->status) === 'nonaktif' ? 'selected' : '' }}>Non Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Untuk memindahkan rombel, gunakan fitur <strong>Pindah Rombel</strong> di halaman detail enrollment.
                            </div>
                        </div>

                        {{-- Tanggal Keluar — tampil jika status lulus/keluar --}}
                        <div class="mb-4" id="tanggal_keluar_group"
                             style="{{ in_array(old('status', $enrollment->status), ['lulus', 'keluar']) ? '' : 'display:none;' }}">
                            <label for="tanggal_keluar" class="form-label">
                                <i class="bi bi-calendar-x me-1"></i>Tanggal Keluar
                                <span class="text-danger" id="tanggal_keluar_required">*</span>
                            </label>
                            <input type="date"
                                   class="form-control @error('tanggal_keluar') is-invalid @enderror"
                                   id="tanggal_keluar" name="tanggal_keluar"
                                   value="{{ old('tanggal_keluar', $enrollment->tanggal_keluar?->format('Y-m-d')) }}">
                            @error('tanggal_keluar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alasan Keluar — tampil hanya jika status keluar --}}
                        <div class="mb-4" id="alasan_keluar_group"
                             style="{{ old('status', $enrollment->status) === 'keluar' ? '' : 'display:none;' }}">
                            <label for="alasan_keluar" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>Alasan Keluar
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('alasan_keluar') is-invalid @enderror"
                                      id="alasan_keluar" name="alasan_keluar"
                                      rows="3"
                                      placeholder="Masukkan alasan siswa keluar dari program...">{{ old('alasan_keluar', $enrollment->alasan_keluar) }}</textarea>
                            @error('alasan_keluar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-4">
                            <label for="catatan" class="form-label">
                                <i class="bi bi-journal-text me-1"></i>Catatan
                                <span class="text-muted small">(Opsional)</span>
                            </label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror"
                                      id="catatan" name="catatan"
                                      rows="3"
                                      placeholder="Catatan tambahan mengenai enrollment siswa ini...">{{ old('catatan', $enrollment->catatan) }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maksimal 1000 karakter.</div>
                        </div>

                        {{-- Info tanggal daftar (readonly) --}}
                        <div class="mb-4">
                            <label class="form-label text-muted">
                                <i class="bi bi-calendar-check me-1"></i>Tanggal Daftar
                            </label>
                            <input type="text" class="form-control" readonly
                                   value="{{ $enrollment->tanggal_daftar->format('d/m/Y') }}">
                            <div class="form-text">Tanggal pendaftaran tidak dapat diubah.</div>
                        </div>

                        {{-- Submit --}}
                        <div class="d-flex justify-content-between pt-2">
                            <a href="{{ route('ekstrakurikuler.enrollment.show', [$ekstrakurikuler, $enrollment]) }}"
                               class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-floppy me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect        = document.getElementById('status');
    const tanggalKeluar       = document.getElementById('tanggal_keluar_group');
    const alasanKeluar        = document.getElementById('alasan_keluar_group');
    const tanggalKeluarInput  = document.getElementById('tanggal_keluar');
    const alasanKeluarInput   = document.getElementById('alasan_keluar');

    function toggleFields() {
        const val = statusSelect.value;

        // Tanggal keluar: tampil jika lulus atau keluar
        if (val === 'lulus' || val === 'keluar') {
            tanggalKeluar.style.display = '';
            tanggalKeluarInput.required = true;
        } else {
            tanggalKeluar.style.display = 'none';
            tanggalKeluarInput.required = false;
        }

        // Alasan keluar: tampil hanya jika keluar
        if (val === 'keluar') {
            alasanKeluar.style.display = '';
            alasanKeluarInput.required = true;
        } else {
            alasanKeluar.style.display = 'none';
            alasanKeluarInput.required = false;
        }
    }

    statusSelect.addEventListener('change', toggleFields);

    // Jalankan sekali saat load (handle old() value atau nilai dari DB)
    toggleFields();
});
</script>
@endpush

<style>
.avatar-md {
    width: 48px;
    height: 48px;
}
</style>
@endsection
