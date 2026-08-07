@extends('layouts.app')

@section('title', 'Daftarkan Siswa - ' . $ekstrakurikuler->kategori_program)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-person-plus me-2"></i>Daftarkan Siswa
                        </h4>
                        <a href="{{ route('ekstrakurikuler.enrollment.index', $ekstrakurikuler) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Program Info -->
                    <div class="alert alert-info border-start border-4 border-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-trophy-fill me-2"></i>
                            <div>
                                <h6 class="mb-1">{{ $ekstrakurikuler->kategori_program }}</h6>
                                <small class="text-muted">{{ $ekstrakurikuler->sekolah->namasekolah ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>

                    @if($errors->enrollment->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->enrollment->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('ekstrakurikuler.enrollment.store', $ekstrakurikuler) }}">
                        @csrf
                        
                        <!-- Rombel Selection -->
                        <div class="mb-4">
                            <label for="ekstrakurikuler_rombel_id" class="form-label">
                                <i class="bi bi-people me-1"></i>Rombel Ekstrakurikuler <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('ekstrakurikuler_rombel_id', 'enrollment') is-invalid @enderror" 
                                    id="ekstrakurikuler_rombel_id" name="ekstrakurikuler_rombel_id" required>
                                <option value="">Pilih Rombel...</option>
                                @foreach($rombels as $rombel)
                                    <option value="{{ $rombel->id }}" 
                                            data-capacity="{{ $rombel->jumlah_siswa }}"
                                            data-current="{{ $rombel->getJumlahSiswaAktual() }}"
                                            {{ old('ekstrakurikuler_rombel_id') == $rombel->id ? 'selected' : '' }}>
                                        {{ $rombel->nama_rombel }} 
                                        ({{ $rombel->getJumlahSiswaAktual() }} siswa)
                                    </option>
                                @endforeach
                            </select>
                            @error('ekstrakurikuler_rombel_id', 'enrollment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Pilih rombel tempat siswa akan ditempatkan.</div>
                        </div>

                        <!-- Student Selection -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-person-check me-1"></i>Pilih Siswa <span class="text-danger">*</span>
                            </label>
                            
                            @if($availableSiswa->isNotEmpty())
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" class="form-control" id="searchSiswa" 
                                               placeholder="Cari nama atau NISN siswa...">
                                    </div>
                                </div>

                                <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAllSiswa">
                                                <label class="form-check-label fw-bold" for="selectAllSiswa">
                                                    Pilih Semua ({{ $availableSiswa->count() }} siswa)
                                                </label>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row" id="siswaList">
                                        @foreach($availableSiswa as $siswa)
                                            <div class="col-md-6 mb-2 siswa-item" 
                                                 data-name="{{ strtolower($siswa->nama_lengkap) }}" 
                                                 data-nisn="{{ $siswa->nisn ?? '' }}">
                                                <div class="form-check">
                                                    <input class="form-check-input siswa-checkbox" type="checkbox" 
                                                           name="siswa_ids[]" value="{{ $siswa->id }}" 
                                                           id="siswa_{{ $siswa->id }}"
                                                           {{ in_array($siswa->id, old('siswa_ids', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="siswa_{{ $siswa->id }}">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                                <span class="text-white small">{{ substr($siswa->nama_lengkap, 0, 1) }}</span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $siswa->nama_lengkap }}</div>
                                                                @if($siswa->nisn)
                                                                    <small class="text-muted">NISN: {{ $siswa->nisn }}</small>
                                                                @endif
                                                                <small class="text-muted d-block">Rombel: {{ $siswa->rombel }}</small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                @error('siswa_ids', 'enrollment')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                                
                                <div class="form-text mt-2">
                                    <span id="selectedCount">0</span> siswa dipilih. 
                                    Hanya siswa dari sekolah yang sama dan belum terdaftar dalam program ini yang ditampilkan.
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Tidak ada siswa yang dapat didaftarkan. Semua siswa dari sekolah ini mungkin sudah terdaftar dalam program ekstrakurikuler ini.
                                </div>
                            @endif
                        </div>

                        <!-- Registration Date -->
                        <div class="mb-4">
                            <label for="tanggal_daftar" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Tanggal Pendaftaran <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control datepicker @error('tanggal_daftar', 'enrollment') is-invalid @enderror" 
                                   id="tanggal_daftar" name="tanggal_daftar" 
                                   value="{{ old('tanggal_daftar', now()->format('Y-m-d')) }}" 
                                   placeholder="DD-MM-YYYY" required>
                            @error('tanggal_daftar', 'enrollment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="catatan" class="form-label">
                                <i class="bi bi-journal-text me-1"></i>Catatan (Opsional)
                            </label>
                            <textarea class="form-control @error('catatan', 'enrollment') is-invalid @enderror" 
                                      id="catatan" name="catatan" rows="3" 
                                      placeholder="Tambahkan catatan khusus untuk pendaftaran ini...">{{ old('catatan') }}</textarea>
                            @error('catatan', 'enrollment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Catatan akan disimpan dalam data enrollment siswa.</div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('ekstrakurikuler.enrollment.index', $ekstrakurikuler) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn" 
                                    {{ $availableSiswa->isEmpty() ? 'disabled' : '' }}>
                                <i class="bi bi-person-plus me-1"></i> Daftarkan Siswa
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
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchSiswa');
    const selectAllCheckbox = document.getElementById('selectAllSiswa');
    const siswaCheckboxes = document.querySelectorAll('.siswa-checkbox');
    const siswaItems = document.querySelectorAll('.siswa-item');
    const selectedCountSpan = document.getElementById('selectedCount');
    const submitBtn = document.getElementById('submitBtn');

    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            siswaItems.forEach(item => {
                const name = item.dataset.name;
                const nisn = item.dataset.nisn;
                const matches = name.includes(searchTerm) || nisn.includes(searchTerm);
                item.style.display = matches ? 'block' : 'none';
            });
            
            updateSelectAllState();
        });
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const visibleCheckboxes = Array.from(siswaCheckboxes).filter(cb => {
                return cb.closest('.siswa-item').style.display !== 'none';
            });
            
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            updateSelectedCount();
            updateSubmitButton();
        });
    }

    // Individual checkbox functionality
    siswaCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateSelectedCount();
            updateSubmitButton();
        });
    });

    function updateSelectAllState() {
        if (!selectAllCheckbox) return;
        
        const visibleCheckboxes = Array.from(siswaCheckboxes).filter(cb => {
            return cb.closest('.siswa-item').style.display !== 'none';
        });
        
        const checkedVisible = visibleCheckboxes.filter(cb => cb.checked);
        
        selectAllCheckbox.checked = visibleCheckboxes.length > 0 && checkedVisible.length === visibleCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedVisible.length > 0 && checkedVisible.length < visibleCheckboxes.length;
    }

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.siswa-checkbox:checked').length;
        if (selectedCountSpan) {
            selectedCountSpan.textContent = checkedCount;
        }
    }

    function updateSubmitButton() {
        const checkedCount = document.querySelectorAll('.siswa-checkbox:checked').length;
        if (submitBtn) {
            submitBtn.disabled = checkedCount === 0;
        }
    }

    // Rombel capacity warning
    const rombelSelect = document.getElementById('ekstrakurikuler_rombel_id');
    if (rombelSelect) {
        rombelSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const capacity = parseInt(selectedOption.dataset.capacity);
                const current = parseInt(selectedOption.dataset.current);
                const available = capacity - current;
                
                if (available <= 0) {
                    alert('Rombel ini sudah penuh. Pilih rombel lain.');
                    this.value = '';
                } else if (available <= 5) {
                    alert(`Perhatian: Rombel ini hanya tersisa ${available} slot.`);
                }
            }
        });
    }

    // Initialize counts
    updateSelectedCount();
    updateSubmitButton();
    updateSelectAllState();
});
</script>
@endpush

<style>
.avatar-sm {
    width: 28px;
    height: 28px;
    font-size: 12px;
}

.siswa-item {
    transition: opacity 0.2s ease;
}

.form-check-label {
    cursor: pointer;
    width: 100%;
}

.form-check {
    border: 1px solid transparent;
    border-radius: 0.375rem;
    padding: 0.5rem;
    transition: all 0.2s ease;
}

.form-check:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.form-check-input:checked + .form-check-label .avatar-sm {
    background-color: #198754 !important;
}
</style>
@endsection