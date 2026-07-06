@extends('layouts.app')

@section('title', 'Tambah Program Ekstrakurikuler')

@push('styles')
<style>
    .step-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .step-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
    }
    
    .progress-container {
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        height: 8px;
        margin-top: 1rem;
    }
    
    .progress-bar {
        background: white;
        height: 100%;
        border-radius: 10px;
        transition: width 0.3s ease;
    }
    
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        padding: 0 1rem;
    }
    
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        flex: 1;
    }
    
    .step-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 15px;
        left: 50%;
        width: calc(100% - 30px);
        height: 2px;
        background: #e9ecef;
        z-index: 1;
    }
    
    .step-item.active:not(:last-child)::after,
    .step-item.completed:not(:last-child)::after {
        background: #28a745;
    }
    
    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
    }
    
    .step-item.active .step-number {
        background: #007bff;
        color: white;
    }
    
    .step-item.completed .step-number {
        background: #28a745;
        color: white;
    }
    
    .step-label {
        font-size: 0.8rem;
        text-align: center;
        color: #6c757d;
        line-height: 1.2;
    }
    
    .step-item.active .step-label {
        color: #007bff;
        font-weight: 600;
    }
    
    .step-item.completed .step-label {
        color: #28a745;
    }
    
    .form-section {
        padding: 2rem;
    }
    
    .section-title {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        color: #495057;
    }
    
    .navigation-buttons {
        padding: 1.5rem 2rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .btn-step {
        min-width: 120px;
    }
    
    .form-group-inline {
        display: flex;
        gap: 1rem;
        align-items: end;
    }
    
    .form-group-inline .form-group {
        flex: 1;
        margin-bottom: 0;
    }
    
    .rombel-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        background: #f8f9fa;
    }
    
    .rombel-header {
        background: #007bff;
        color: white;
        padding: 0.75rem 1rem;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        border-radius: 7px 7px 0 0;
        font-weight: 600;
    }
    
    .summary-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .summary-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .summary-row:last-child {
        border-bottom: none;
    }
    
    .summary-label {
        font-weight: 500;
        color: #6c757d;
    }
    
    .summary-value {
        color: #495057;
        font-weight: 500;
    }
    
    .alert-info-custom {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 1px solid #2196f3;
        color: #1976d2;
    }
    
    .required-indicator {
        color: #dc3545;
        margin-left: 2px;
    }
    
    @media (max-width: 768px) {
        .step-indicator {
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .step-item {
            flex: 0 0 calc(25% - 0.75rem);
            min-width: 80px;
        }
        
        .step-item::after {
            display: none;
        }
        
        .form-group-inline {
            flex-direction: column;
            gap: 0;
        }
        
        .form-group-inline .form-group {
            margin-bottom: 1rem;
        }
        
        .navigation-buttons {
            flex-direction: column;
            gap: 1rem;
        }
        
        .btn-step {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 text-gray-800">Tambah Program Ekstrakurikuler</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ekstrakurikuler.index') }}">Ekstrakurikuler</a></li>
                        <li class="breadcrumb-item active">Tambah Program</li>
                    </ol>
                </nav>
            </div>

            <!-- Multi-Step Form Container -->
            <div class="step-container">
                <!-- Header with Progress -->
                <div class="step-header">
                    <h4 class="mb-0">
                        @switch($step)
                            @case(1) Informasi Dasar Program @break
                            @case(2) Detail Sekolah @break
                            @case(3) Kebutuhan Teknis @break
                            @case(4) Struktur Kelas @break
                            @case(5) Detail Rombel 1 @break
                            @case(6) Detail Rombel 2 @break
                            @case(7) Detail Rombel 3 @break
                            @case(8) Detail Rombel 4 @break
                            @case(9) Detail Rombel 5 @break
                            @case(10) Ringkasan & Validasi @break
                        @endswitch
                    </h4>
                    <p class="mb-0 opacity-75">Langkah {{ $step }} dari {{ max($step, (isset($formData['total_rombel']) ? $formData['total_rombel'] + 5 : 9)) }}</p>
                    
                    <div class="progress-container">
                        @php
                            $totalSteps = max($step, (isset($formData['total_rombel']) ? $formData['total_rombel'] + 5 : 9));
                            $progressPercentage = ($step / $totalSteps) * 100;
                        @endphp
                        <div class="progress-bar" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>

                <!-- Step Indicator -->
                <div class="p-3">
                    <div class="step-indicator">
                        @for($i = 1; $i <= 10; $i++)
                            @php
                                $isActive = $i == $step;
                                $isCompleted = $i < $step;
                                $shouldShow = $i <= 4 || 
                                             ($i == 10) || 
                                             (isset($formData['total_rombel']) && $i <= (4 + $formData['total_rombel']));
                            @endphp
                            
                            @if($shouldShow)
                            <div class="step-item {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                                <div class="step-number">
                                    @if($isCompleted)
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $i }}
                                    @endif
                                </div>
                                <div class="step-label">
                                    @switch($i)
                                        @case(1) Info Dasar @break
                                        @case(2) Sekolah @break
                                        @case(3) Teknis @break
                                        @case(4) Struktur @break
                                        @case(5) Rombel 1 @break
                                        @case(6) Rombel 2 @break
                                        @case(7) Rombel 3 @break
                                        @case(8) Rombel 4 @break
                                        @case(9) Rombel 5 @break
                                        @case(10) Ringkasan @break
                                    @endswitch
                                </div>
                            </div>
                            @endif
                        @endfor
                    </div>
                </div>

                <!-- Form Content -->
                <form method="POST" action="{{ route('ekstrakurikuler.process-step') }}" id="stepForm">
                    @csrf
                    <input type="hidden" name="current_step" value="{{ $step }}">

                    @if ($errors->any())
                        <div class="alert alert-danger mx-3 mt-3">
                            <h6><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success mx-3 mt-3">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="form-section">
                        @if($step == 1)
                            @include('ekstrakurikuler.steps.step1', ['formData' => $formData, 'salesUsers' => $salesUsers, 'regions' => $regions, 'kotaOptions' => $kotaOptions, 'statuses' => $statuses])
                        @elseif($step == 2)
                            @include('ekstrakurikuler.steps.step2', ['formData' => $formData, 'sekolahs' => $sekolahs])
                        @elseif($step == 3)
                            @include('ekstrakurikuler.steps.step3', ['formData' => $formData])
                        @elseif($step == 4)
                            @include('ekstrakurikuler.steps.step4', ['formData' => $formData])
                        @elseif($step >= 5 && $step <= 9)
                            @include('ekstrakurikuler.steps.step-rombel', [
                                'formData' => $formData, 
                                'rombelNumber' => $step - 4,
                                'currentStep' => $step
                            ])
                        @elseif($step == 10)
                            @include('ekstrakurikuler.steps.step-final', ['formData' => $formData])
                        @endif
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="navigation-buttons">
                        <div>
                            @if($step > 1)
                                <button type="button" class="btn btn-secondary btn-step" onclick="goToPreviousStep()">
                                    <i class="fas fa-arrow-left"></i> Sebelumnya
                                </button>
                            @else
                                <a href="{{ route('ekstrakurikuler.index') }}" class="btn btn-secondary btn-step">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            @endif
                        </div>

                        <div class="text-center">
                            {{-- Simpan Draft button removed per request --}}
                        </div>

                        <div>
                            @if($step == 10)
                                <button type="submit" name="submit_final" value="1" class="btn btn-success btn-step">
                                    <i class="fas fa-check"></i> Selesai & Simpan
                                </button>
                            @else
                                <button type="submit" name="next_step" value="{{ $nextStep }}" class="btn btn-primary btn-step">
                                    Selanjutnya <i class="fas fa-arrow-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-4">
                <div class="alert alert-info-custom">
                    <h6><i class="fas fa-info-circle"></i> Bantuan</h6>
                    <p class="mb-0">
                        @switch($step)
                            @case(1)
                                Masukkan informasi dasar program ekstrakurikuler seperti nama program, sales yang bertanggung jawab, dan region.
                            @break
                            @case(2)
                                Pilih sekolah tujuan dan lengkapi informasi kontak serta lokasi sekolah.
                            @break
                            @case(3)
                                Tentukan kebutuhan teknis seperti koneksi internet, proyektor, dan kabel yang tersedia di sekolah.
                            @break
                            @case(4)
                                Tentukan struktur kelas termasuk jumlah total siswa, ruangan, dan berapa banyak rombel yang akan dibuat.
                            @break
                            @default
                                @if($step >= 5 && $step <= 9)
                                    Atur jadwal dan detail untuk rombel {{ $step - 4 }}. Pastikan tanggal dan waktu tidak bentrok dengan rombel lainnya.
                                @elseif($step == 10)
                                    Periksa kembali semua data yang telah dimasukkan sebelum menyimpan program ekstrakurikuler.
                                @endif
                        @endswitch
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Note: initDatepickers() is called in app.js

    // Auto-calculate end date based on meetings and frequency
    const pertemuanInput = document.querySelector('input[name*="_total_pertemuan"]');
    const startDateInput = document.querySelector('input[name*="_tanggal_mulai"]');
    const endDateInput = document.querySelector('input[name*="_tanggal_selesai"]');
    const hariSelect = document.querySelector('select[name*="_hari"]');
    
    if (pertemuanInput && startDateInput && endDateInput && hariSelect) {
        function calculateEndDate() {
            const meetings = parseInt(pertemuanInput.value);
            const startDate = startDateInput.value;
            const day = hariSelect.value;
            
            if (meetings && startDate && day) {
                // Calculate end date based on weekly frequency
                const start = new Date(startDate);
                const daysToAdd = (meetings - 1) * 7;
                const endDate = new Date(start.getTime() + (daysToAdd * 24 * 60 * 60 * 1000));
                
                endDateInput.value = endDate.toISOString().split('T')[0];
            }
        }
        
        pertemuanInput.addEventListener('change', calculateEndDate);
        startDateInput.addEventListener('change', calculateEndDate);
        hariSelect.addEventListener('change', calculateEndDate);
    }

    // Form validation
    const form = document.getElementById('stepForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateCurrentStep()) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Auto-save form data to session storage
    const formInputs = form.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            saveFormDataToSession();
        });
    });

    // Load form data from session storage if available
    loadFormDataFromSession();
});

function validateCurrentStep() {
    const currentStep = {{ $step }};
    let isValid = true;
    const errors = [];

    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    
    document.querySelectorAll('.invalid-feedback').forEach(el => {
        el.remove();
    });

    // Validate based on current step
    switch(currentStep) {
        case 1:
            isValid = validateStep1();
            break;
        case 2:
            isValid = validateStep2();
            break;
        case 3:
            isValid = validateStep3();
            break;
        case 4:
            isValid = validateStep4();
            break;
        case 5:
        case 6:
        case 7:
        case 8:
        case 9:
            isValid = validateStepRombel();
            break;
    }

    return isValid;
}

function validateStep1() {
    let isValid = true;
    const requiredFields = ['kategori_program', 'user_id_sales', 'city'];
    
    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field && !field.value.trim()) {
            showFieldError(field, 'Field ini wajib diisi.');
            isValid = false;
        }
    });
    
    return isValid;
}

function validateStep2() {
    let isValid = true;
    const requiredFields = [
        'sekolah_kodlan', 'alamat_lengkap', 'google_maps_link', 'jarak_km', 
        'kepala_sekolah', 'penanggung_jawab', 'no_telepon'
    ];
    
    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field && !field.value.trim()) {
            showFieldError(field, 'Field ini wajib diisi.');
            isValid = false;
        }
    });
    
    // Validate distance is numeric
    const jarakField = document.querySelector('[name="jarak_km"]');
    if (jarakField && jarakField.value && isNaN(parseFloat(jarakField.value))) {
        showFieldError(jarakField, 'Jarak harus berupa angka.');
        isValid = false;
    }
    
    return isValid;
}

function validateStep3() {
    let isValid = true;
    const requiredFields = ['koneksi_internet', 'proyektor', 'kabel_hdmi', 'kabel_vga'];
    
    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field && !field.value) {
            showFieldError(field, 'Field ini wajib diisi.');
            isValid = false;
        }
    });
    
    return isValid;
}

function validateStep4() {
    let isValid = true;
    const requiredFields = ['total_siswa', 'total_ruangan', 'total_rombel'];
    
    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field && !field.value) {
            showFieldError(field, 'Field ini wajib diisi.');
            isValid = false;
        } else if (field && field.value && parseInt(field.value) <= 0) {
            showFieldError(field, 'Nilai harus lebih dari 0.');
            isValid = false;
        }
    });
    
    return isValid;
}

function validateStepRombel() {
    let isValid = true;
    const currentStep = {{ $step }};
    const rombelNumber = currentStep - 4;
    const prefix = `rombel_${rombelNumber}_`;
    
    const requiredFields = [
        'total_pertemuan', 'tanggal_mulai', 'tanggal_selesai', 
        'hari', 'jam_mulai', 'jumlah_siswa'
    ];
    
    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${prefix}${fieldName}"]`);
        if (field && !field.value) {
            showFieldError(field, 'Field ini wajib diisi.');
            isValid = false;
        }
    });
    
    // Validate end date is after start date
    const startDate = document.querySelector(`[name="${prefix}tanggal_mulai"]`);
    const endDate = document.querySelector(`[name="${prefix}tanggal_selesai"]`);
    
    if (startDate && endDate && startDate.value && endDate.value) {
        if (new Date(endDate.value) <= new Date(startDate.value)) {
            showFieldError(endDate, 'Tanggal selesai harus setelah tanggal mulai.');
            isValid = false;
        }
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('is-invalid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function goToPreviousStep() {
    const prevStep = {{ $prevStep ?? ($step > 1 ? $step - 1 : 1) }};
    window.location.href = `{{ route('ekstrakurikuler.create.step', ['step' => '__STEP__']) }}`.replace('__STEP__', prevStep);
}

function saveDraft() {
    // Save current form data
    saveFormDataToSession();
}

function saveFormDataToSession() {
    const formData = new FormData(document.getElementById('stepForm'));
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    sessionStorage.setItem('ekstrakurikuler_form_draft', JSON.stringify(data));
}

function loadFormDataFromSession() {
    const savedData = sessionStorage.getItem('ekstrakurikuler_form_draft');
    if (savedData) {
        const data = JSON.parse(savedData);
        
        Object.keys(data).forEach(key => {
            const field = document.querySelector(`[name="${key}"]`);
            if (field && !field.value) {
                field.value = data[key];
            }
        });
    }
}

// Clear session storage when form is successfully submitted
window.addEventListener('beforeunload', function() {
    if (document.querySelector('input[name="submit_final"]')) {
        sessionStorage.removeItem('ekstrakurikuler_form_draft');
    }
});

// Dynamic total rombel change handler
function updateTotalRombel() {
    const totalRombelField = document.querySelector('[name="total_rombel"]');
    if (totalRombelField) {
        const totalRombel = parseInt(totalRombelField.value);
        
        // Update progress indicator
        const progressBar = document.querySelector('.progress-bar');
        const totalSteps = totalRombel + 5;
        const currentStep = {{ $step }};
        const progressPercentage = (currentStep / totalSteps) * 100;
        
        if (progressBar) {
            progressBar.style.width = progressPercentage + '%';
        }
    }
}

// Add event listener for total_rombel change
document.addEventListener('DOMContentLoaded', function() {
    const totalRombelField = document.querySelector('[name="total_rombel"]');
    if (totalRombelField) {
        totalRombelField.addEventListener('change', updateTotalRombel);
    }
    
    // Add city selection handler for dynamic school loading
    const citySelect = document.querySelector('#city');
    if (citySelect) {
        citySelect.addEventListener('change', handleCityChange);
    }
});

// Handle city selection change - store in session for next step
function handleCityChange(event) {
    const selectedCity = event.target.value;
    const regionField = document.querySelector('#region');
    
    // Map city to region for backward compatibility
    const cityToRegionMap = {
        'JAKARTA BARAT': 'JAKARTA',
        'JAKARTA PUSAT': 'JAKARTA',
        'JAKARTA SELATAN': 'JAKARTA',
        'JAKARTA TIMUR': 'JAKARTA',
        'JAKARTA UTARA': 'JAKARTA',
        'KOTA JAKARTA BARAT': 'JAKARTA',
        'KOTA JAKARTA PUSAT': 'JAKARTA',
        'KOTA JAKARTA SELATAN': 'JAKARTA',
        'KOTA JAKARTA TIMUR': 'JAKARTA',
        'KOTA JAKARTA UTARA': 'JAKARTA',
        'KOTA DEPOK': 'DEPOK',
        'DEPOK': 'DEPOK',
        'KOTA BOGOR': 'BOGOR',
        'KAB. BOGOR': 'BOGOR',
        'BOGOR': 'BOGOR',
        'KOTA TANGERANG': 'TANGERANG',
        'KOTA TANGERANG SELATAN': 'TANGERANG',
        'KAB. TANGERANG': 'TANGERANG',
        'TANGERANG': 'TANGERANG',
        'KOTA BEKASI': 'BEKASI',
        'KAB. BEKASI': 'BEKASI',
        'BEKASI': 'BEKASI'
    };
    
    // Set region field based on city selection
    if (regionField && selectedCity && cityToRegionMap[selectedCity]) {
        regionField.value = cityToRegionMap[selectedCity];
    }
    
    // Store city selection for session
    if (selectedCity) {
        console.log('City selected:', selectedCity);
    }
}
</script>
@endpush