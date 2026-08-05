@extends('layouts.guest')

@push('styles')
<style>
    body { background-color: #fff !important; display: block !important; padding: 0 !important; }
    .reg-container { display: flex; min-height: 100vh; }
    .brand-panel { 
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%); 
        color: white; flex: 0 0 350px; padding: 3rem; display: flex; flex-direction: column;
        position: sticky; top: 0; height: 100vh;
    }
    .form-panel { flex: 1; padding: 2rem; overflow-y: auto; background: #fff; display: flex; flex-direction: column; }
    .step-section { display: none; }
    .step-section.active { display: block; animation: fadeIn 0.4s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .progress-step { display: flex; align-items: center; margin-bottom: 2.5rem; opacity: 0.6; transition: 0.3s; }
    .progress-step.active { opacity: 1; font-weight: bold; }
    .step-number { width: 30px; height: 30px; border-radius: 50%; border: 2px solid #fff; background: rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: center; margin-right: 1rem; }
    .progress-step.active .step-number { background: #fff; color: #3b82f6; }
    @media (max-width: 992px) { 
        .brand-panel { display: none; } 
        .form-panel { padding: 1.25rem 0.75rem !important; }
        .reg-container { min-height: auto; }
        .form-wrapper { padding: 1rem 0 !important; }
    }
    @media (max-width: 576px) {
        .form-control, .form-select, .btn { font-size: 0.925rem; }
        .btn-check + .btn-outline-primary { padding: 0.4rem 0.2rem; min-height: 38px; }
        .d-flex.justify-content-between { flex-wrap: wrap; gap: 0.5rem; }
        .d-flex.justify-content-between .btn { flex: 1 1 auto; text-align: center; }
    }
    
    /* Custom Styles for Checkbox Table */
    .btn-check + .btn-outline-primary {
        background-color: #f8f9fa;
        border: 1px solid #e2e8f0;
        color: #94a3b8;
        font-weight: 500;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
    }
    .btn-check + .btn-outline-primary .unchecked-icon {
        display: inline-block;
        font-size: 1.1rem;
        color: #cbd5e1;
    }
    .btn-check + .btn-outline-primary .checked-icon {
        display: none;
    }
    .btn-check:checked + .btn-outline-primary {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
        box-shadow: inset 0 0 0 1px #2563eb;
    }
    .btn-check:checked + .btn-outline-primary .unchecked-icon {
        display: none;
    }
    .btn-check:checked + .btn-outline-primary .checked-icon {
        display: inline-block;
        font-size: 1.25rem;
        color: #ffffff;
    }
    .btn-check + .btn-outline-primary:hover {
        background-color: #eff6ff;
        border-color: #3b82f6;
        color: #2563eb;
    }
    .btn-check + .btn-outline-primary:hover .unchecked-icon {
        color: #3b82f6;
    }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.02); }
    .hover-scale:hover { transform: scale(1.02); }
</style>
@endpush

@section('title', 'Registrasi Instruktur')

@section('content')
<div class="reg-container">
    <div class="brand-panel">
        <div class="mb-5">
            <a href="{{ url('/') }}" class="text-white text-decoration-none d-flex align-items-center">
                <i class="bi bi-rocket-takeoff fs-3 me-2"></i>
                <span class="fw-bold fs-4">ERLASS</span>
            </a>
        </div>
        
        <h2 class="fw-bold mb-4">Pendaftaran Instruktur</h2>
        <p class="mb-5" style="color: rgba(255, 255, 255, 0.90);">Bergabunglah bersama kami sebagai pengajar profesional dan bagikan keahlian Anda.</p>

        <div class="registration-progress">
            <div class="progress-step active" id="prog-1" onclick="goToStep(1)" style="cursor: pointer;">
                <div class="step-number">1</div>
                <span>Informasi Akun</span>
            </div>
            <div class="progress-step" id="prog-2" onclick="goToStep(2)" style="cursor: pointer;">
                <div class="step-number">2</div>
                <span>Identitas Pribadi</span>
            </div>
            <div class="progress-step" id="prog-3" onclick="goToStep(3)" style="cursor: pointer;">
                <div class="step-number">3</div>
                <span>Domisili & Pendidikan</span>
            </div>
            <div class="progress-step" id="prog-4" onclick="goToStep(4)" style="cursor: pointer;">
                <div class="step-number">4</div>
                <span>Kesehatan & Logistik</span>
            </div>
            <div class="progress-step" id="prog-5" onclick="goToStep(5)" style="cursor: pointer;">
                <div class="step-number">5</div>
                <span>Bank & Dokumen</span>
            </div>
            <div class="progress-step" id="prog-6" onclick="goToStep(6)" style="cursor: pointer;">
                <div class="step-number">6</div>
                <span>Jadwal Mengajar</span>
            </div>
        </div>

        <div class="mt-auto small" style="color: rgba(255, 255, 255, 0.85);">
            &copy; {{ date('Y') }} Erlass. All rights reserved.
        </div>
    </div>

    <div class="form-panel">
        <div class="max-w-2xl mx-auto form-wrapper" style="max-width: 850px; width: 100%; margin: auto; padding: 3rem 0;">
            <!-- Mobile Step Progress Header (< 992px) -->
            <div class="mb-4 d-lg-none bg-light p-3 rounded-3 border shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <a href="{{ url('/') }}" class="text-primary text-decoration-none fw-bold fs-6">
                        <i class="bi bi-rocket-takeoff me-1"></i> ERLASS
                    </a>
                    <span class="badge bg-primary px-3 py-2" id="mobileStepBadge">Langkah 1 dari 6</span>
                </div>
                <div class="fw-bold text-dark mb-2 small" id="mobileStepTitle">Informasi Akun & Pribadi Dasar</div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" id="mobileProgressBar" role="progressbar" style="width: 16.66%; transition: width 0.3s ease;" aria-valuenow="16.66" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <!-- Global Error Banner for Backend Validation -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert" id="globalErrorAlert">
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                        <strong class="fs-6">Pendaftaran Belum Lengkap / Gagal</strong>
                    </div>
                    <p class="mb-2 small">Mohon periksa kembali isian formulir berikut:</p>
                    <ul class="mb-0 small ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Dynamic JS Error Banner -->
            <div id="jsStepErrorAlert" class="alert alert-danger d-none mb-4 shadow-sm">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <span id="jsStepErrorMessage">Mohon lengkapi seluruh isian wajib sebelum melanjutkan.</span>
            </div>

            <form method="POST" action="{{ route('instructor.register.store') }}" enctype="multipart/form-data" id="instructorRegisterForm" novalidate>
                @csrf
                
                <!-- Step 1: Akun & Kontak Dasar -->
                <div class="step-section active" id="step1">
                    <div class="mb-5">
                        <h5 class="text-dark fw-bold mb-5 d-flex align-items-center">
                            <i class="bi bi-person-badge me-2"></i> Informasi Akun & Pribadi Dasar
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required placeholder="email@contoh.com" />
                                </div>
                                @error('email') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">No HP (WhatsApp Aktif) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-whatsapp"></i></span>
                                    <input class="form-control border-start-0 ps-0 @error('no_hp_1') is-invalid @enderror" type="text" name="no_hp_1" value="{{ old('no_hp_1') }}" required placeholder="0812..." />
                                </div>
                                @error('no_hp_1') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                    <input class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror" type="password" id="password" name="password" required placeholder="Minimal 8 karakter" />
                                    <button type="button" class="btn btn-outline-secondary border-start-0 bg-transparent text-muted px-3" id="togglePasswordBtn" tabindex="-1">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Konfirmasi Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                                    <input class="form-control border-start-0 border-end-0 ps-0" type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password" />
                                    <button type="button" class="btn btn-outline-secondary border-start-0 bg-transparent text-muted px-3" id="togglePasswordConfirmBtn" tabindex="-1">
                                        <i class="bi bi-eye" id="togglePasswordConfirmIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary px-4" onclick="nextStep(event)">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- Step 2: Identitas Lengkap -->
                <div class="step-section" id="step2">
                     <div class="mb-5">
                         <h5 class="text-dark fw-bold mb-5 d-flex align-items-center">
                             <i class="bi bi-card-text me-2"></i> Identitas Lengkap
                         </h5>
                         <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label small text-muted">Gelar Depan</label>
                                <input class="form-control" type="text" name="gelar_depan" value="{{ old('gelar_depan') }}" placeholder="Dr, Ir" />
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small text-muted">Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                                <input class="form-control text-uppercase fw-bold @error('nama_lengkap') is-invalid @enderror" type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required />
                                @error('nama_lengkap') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-muted">Gelar Belakang</label>
                                <input class="form-control" type="text" name="gelar_belakang" value="{{ old('gelar_belakang') }}" placeholder="S.Pd" />
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Nama Panggilan <span class="text-danger">*</span></label>
                                <input class="form-control @error('nama_panggilan') is-invalid @enderror" type="text" name="nama_panggilan" value="{{ old('nama_panggilan') }}" required />
                                @error('nama_panggilan') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small text-muted mb-0">NIK (16 Digit) <span class="text-danger">*</span></label>
                                    <span id="nik_counter" class="badge bg-secondary" style="font-size: 0.75rem;">0 / 16 Digit</span>
                                </div>
                                <input class="form-control font-monospace @error('nik') is-invalid @enderror" type="text" name="nik" id="nik_input" value="{{ old('nik') }}" required minlength="16" maxlength="16" placeholder="16 Digit Angka NIK" />
                                @error('nik') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small text-muted">Tanggal Lahir <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-calendar-event"></i></span>
                                    <input class="form-control border-start-0 ps-0 @error('tanggal_lahir') is-invalid @enderror" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required max="{{ date('Y-m-d') }}" />
                                </div>
                                <div class="form-text small text-muted"><i class="bi bi-info-circle me-1"></i>Klik kolom di atas untuk memilih tanggal dari kalender</div>
                                @error('tanggal_lahir') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Agama <span class="text-danger">*</span></label>
                                <select class="form-select @error('agama') is-invalid @enderror" name="agama" required>
                                    <option value="">Pilih Agama</option>
                                    <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                    <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                    <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                    <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                </select>
                                @error('agama') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Status Pernikahan <span class="text-danger">*</span></label>
                                <select class="form-select @error('status_pernikahan') is-invalid @enderror" name="status_pernikahan" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Lajang" {{ old('status_pernikahan') == 'Lajang' ? 'selected' : '' }}>Lajang</option>
                                    <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                    <option value="Duda/Janda" {{ old('status_pernikahan') == 'Duda/Janda' ? 'selected' : '' }}>Duda/Janda</option>
                                </select>
                                @error('status_pernikahan') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                         </div>
                      </div>
                      <div class="mt-4 d-flex justify-content-between">
                         <button type="button" class="btn btn-secondary px-4" onclick="prevStep(event)"><i class="bi bi-arrow-left me-2"></i> Kembali</button>
                         <button type="button" class="btn btn-primary px-4" onclick="nextStep(event)">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
                     </div>
                </div>
                     
                <!-- Step 3: Domisili & Pendidikan -->
                <div class="step-section" id="step3">
                     <div class="mb-5">
                         <h5 class="text-dark fw-bold mb-5 d-flex align-items-center">
                             <i class="bi bi-geo-alt me-2"></i> Kontak & Domisili
                         </h5>
                         <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small text-muted">Alamat Domisili <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('alamat_domisili') is-invalid @enderror" name="alamat_domisili" rows="2" required placeholder="Alamat lengkap sesuai tempat tinggal saat ini">{{ old('alamat_domisili') }}</textarea>
                                @error('alamat_domisili') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                             </div>
                             <div class="col-md-6">
                                <label class="form-label small text-muted">Kota Domisili <span class="text-danger">*</span></label>
                                <input class="form-control @error('kota_domisili') is-invalid @enderror" type="text" name="kota_domisili" value="{{ old('kota_domisili') }}" required placeholder="Jakarta Selatan" />
                                @error('kota_domisili') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                             </div>
                             <div class="col-md-6">
                                <label class="form-label small text-muted">No HP Darurat (Keluarga) <span class="text-danger">*</span></label>
                                <input class="form-control @error('no_hp_2') is-invalid @enderror" type="text" name="no_hp_2" value="{{ old('no_hp_2') }}" required placeholder="0812..." />
                                @error('no_hp_2') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                             </div>
                         </div>
                      </div>
                      
                      <div class="mb-5">
                          <h5 class="text-dark fw-bold mb-5 d-flex align-items-center">
                              <i class="bi bi-mortarboard me-2"></i> Pendidikan & Pekerjaan
                          </h5>
                          <div class="row g-3">
                             <div class="col-md-6">
                                <label class="form-label small text-muted">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <select class="form-select @error('pend_terakhir') is-invalid @enderror" name="pend_terakhir" required>
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="SMA/SMK Sederajat" {{ old('pend_terakhir') == 'SMA/SMK Sederajat' ? 'selected' : '' }}>SMA/SMK Sederajat</option>
                                    <option value="D3" {{ old('pend_terakhir') == 'D3' ? 'selected' : '' }}>D3</option>
                                    <option value="D4/S1" {{ old('pend_terakhir') == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                                    <option value="S2" {{ old('pend_terakhir') == 'S2' ? 'selected' : '' }}>S2</option>
                                </select>
                                @error('pend_terakhir') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                             </div>
                              <div class="col-md-6">
                                <label class="form-label small text-muted">Univ & Jurusan <span class="text-danger">*</span></label>
                                <input class="form-control @error('universitas_jurusan') is-invalid @enderror" type="text" name="universitas_jurusan" value="{{ old('universitas_jurusan') }}" required placeholder="Contoh: UNJ - Pendidikan Matematika" />
                                @error('universitas_jurusan') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                              </div>
                             <div class="col-md-6">
                                <label class="form-label small text-muted">Pekerjaan Terakhir <span class="text-danger">*</span></label>
                                <input class="form-control @error('pekerjaan_terakhir') is-invalid @enderror" type="text" name="pekerjaan_terakhir" value="{{ old('pekerjaan_terakhir') }}" required />
                                @error('pekerjaan_terakhir') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                             </div>
                              <div class="col-md-6">
                                <label class="form-label small text-muted">Jenjang Mengajar <span class="text-danger">*</span></label>
                                <input class="form-control @error('jenjang_mengajar') is-invalid @enderror" type="text" name="jenjang_mengajar" value="{{ old('jenjang_mengajar') }}" required placeholder="TK, SD, SMP, SMA" />
                                <div class="form-text small text-muted">Bila guru/pengajar, isi strip jika selain guru</div>
                                @error('jenjang_mengajar') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                              </div>
                              <div class="col-md-6">
                                  <label class="form-label small text-muted">Kompetensi Utama <span class="text-danger">*</span></label>
                                  <select class="form-select @error('kompetensi_1') is-invalid @enderror" name="kompetensi_1" required>
                                      <option value="">Pilih Kompetensi</option>
                                      <option value="Coding" {{ old('kompetensi_1') == 'Coding' ? 'selected' : '' }}>Coding</option>
                                      <option value="Robotik" {{ old('kompetensi_1') == 'Robotik' ? 'selected' : '' }}>Robotik</option>
                                      <option value="Desain" {{ old('kompetensi_1') == 'Desain' ? 'selected' : '' }}>Desain</option>
                                      <option value="IoT" {{ old('kompetensi_1') == 'IoT' ? 'selected' : '' }}>IoT</option>
                                      <option value="Data Science" {{ old('kompetensi_1') == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                                  </select>
                                  @error('kompetensi_1') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                              </div>
                              <div class="col-md-6">
                                  <label class="form-label small text-muted">Kompetensi Pendukung</label>
                                  <select class="form-select @error('kompetensi_2') is-invalid @enderror" name="kompetensi_2">
                                      <option value="">Pilih Kompetensi (Opsional)</option>
                                      <option value="Coding" {{ old('kompetensi_2') == 'Coding' ? 'selected' : '' }}>Coding</option>
                                      <option value="Robotik" {{ old('kompetensi_2') == 'Robotik' ? 'selected' : '' }}>Robotik</option>
                                      <option value="Desain" {{ old('kompetensi_2') == 'Desain' ? 'selected' : '' }}>Desain</option>
                                      <option value="IoT" {{ old('kompetensi_2') == 'IoT' ? 'selected' : '' }}>IoT</option>
                                      <option value="Data Science" {{ old('kompetensi_2') == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      <div class="mt-4 d-flex justify-content-between">
                         <button type="button" class="btn btn-secondary px-4" onclick="prevStep(event)"><i class="bi bi-arrow-left me-2"></i> Kembali</button>
                         <button type="button" class="btn btn-primary px-4" onclick="nextStep(event)">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
                     </div>
                </div>

                <!-- Step 4: Kesehatan & Logistik -->
                <div class="step-section" id="step4">
                     <div class="mb-5">
                         <h5 class="text-dark fw-bold mb-5 d-flex align-items-center">
                             <i class="bi bi-heart-pulse me-2"></i> Kesehatan & Logistik
                         </h5>
                         <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Tinggi Badan (cm) <span class="text-danger">*</span></label>
                                <input class="form-control @error('tinggi_badan') is-invalid @enderror" type="number" name="tinggi_badan" value="{{ old('tinggi_badan') }}" required placeholder="165" />
                                @error('tinggi_badan') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Berat Badan (kg) <span class="text-danger">*</span></label>
                                <input class="form-control @error('berat_badan') is-invalid @enderror" type="number" name="berat_badan" value="{{ old('berat_badan') }}" required placeholder="55" />
                                @error('berat_badan') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Mata Minus <span class="text-danger">*</span></label>
                                <input class="form-control @error('mata_minus') is-invalid @enderror" type="text" name="mata_minus" value="{{ old('mata_minus') }}" required placeholder="Normal / -0.5" />
                                @error('mata_minus') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Riwayat Penyakit</label>
                                <input class="form-control" type="text" name="riwayat_penyakit" value="{{ old('riwayat_penyakit') }}" placeholder="Kosongkan jika sehat" />
                            </div>
                            
                            <div class="col-md-12">
                               <label class="form-label small text-muted d-block">Alat Mengajar yang Dimiliki <span class="text-danger">*</span></label>
                               <div class="d-flex flex-wrap gap-3 p-2 rounded border" id="alat_mengajar_container">
                                   <div class="form-check">
                                       <input class="form-check-input" type="checkbox" name="alat_mengajar[]" value="Laptop" id="alat_laptop" {{ (is_array(old('alat_mengajar')) && in_array('Laptop', old('alat_mengajar'))) ? 'checked' : '' }}>
                                       <label class="form-check-label" for="alat_laptop">Laptop</label>
                                   </div>
                                   <div class="form-check">
                                       <input class="form-check-input" type="checkbox" name="alat_mengajar[]" value="Handphone" id="alat_hp" {{ (is_array(old('alat_mengajar')) && in_array('Handphone', old('alat_mengajar'))) ? 'checked' : '' }}>
                                       <label class="form-check-label" for="alat_hp">Handphone</label>
                                   </div>
                                   <div class="form-check">
                                       <input class="form-check-input" type="checkbox" name="alat_mengajar[]" value="Tablet" id="alat_tablet" {{ (is_array(old('alat_mengajar')) && in_array('Tablet', old('alat_mengajar'))) ? 'checked' : '' }}>
                                       <label class="form-check-label" for="alat_tablet">Tablet</label>
                                   </div>
                               </div>
                               @error('alat_mengajar') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label small text-muted">Catatan Alat</label>
                                <input class="form-control" type="text" name="catatan_alat" value="{{ old('catatan_alat') }}" placeholder="Contoh: Laptop baterai bocor" />
                            </div>
                            
                            <div class="col-md-6">
                               <label class="form-label small text-muted">Kendaraan <span class="text-danger">*</span></label>
                               <select class="form-select @error('kendaraan') is-invalid @enderror" name="kendaraan" required>
                                   <option value="Pribadi" {{ old('kendaraan', 'Pribadi') == 'Pribadi' ? 'selected' : '' }}>Pribadi</option>
                                   <option value="Umum" {{ old('kendaraan') == 'Umum' ? 'selected' : '' }}>Umum</option>
                                   <option value="Antar Jemput" {{ old('kendaraan') == 'Antar Jemput' ? 'selected' : '' }}>Antar Jemput</option>
                               </select>
                               @error('kendaraan') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                               <label class="form-label small text-muted">Jenis Kendaraan <span class="text-danger">*</span></label>
                               <input class="form-control @error('jenis_kendaraan') is-invalid @enderror" type="text" name="jenis_kendaraan" value="{{ old('jenis_kendaraan') }}" required placeholder="Motor / Kereta / Busway" />
                               @error('jenis_kendaraan') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                         </div>
                      </div>
                      <div class="mt-4 d-flex justify-content-between">
                         <button type="button" class="btn btn-secondary px-4" onclick="prevStep(event)"><i class="bi bi-arrow-left me-2"></i> Kembali</button>
                         <button type="button" class="btn btn-primary px-4" onclick="nextStep(event)">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
                     </div>
                </div>

                <!-- Step 5: Bank & Dokumen -->
                <div class="step-section" id="step5">
                     <div class="mb-5">
                         <h5 class="text-dark fw-bold mb-5 d-flex align-items-center">
                             <i class="bi bi-wallet2 me-2"></i> Data Bank & Dokumen
                         </h5>
                         <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Nama Bank <span class="text-danger">*</span></label>
                                <input class="form-control @error('nama_bank') is-invalid @enderror" type="text" name="nama_bank" value="{{ old('nama_bank') }}" required placeholder="BCA / Mandiri" />
                                @error('nama_bank') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">No Rekening <span class="text-danger">*</span></label>
                                <input class="form-control font-monospace @error('no_rekening') is-invalid @enderror" type="text" name="no_rekening" value="{{ old('no_rekening') }}" required />
                                @error('no_rekening') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small text-muted mb-0">NPWP (15-16 Digit) <span class="text-danger">*</span></label>
                                    <span id="npwp_counter" class="badge bg-secondary" style="font-size: 0.75rem;">0 / 15-16 Digit</span>
                                </div>
                                <input class="form-control font-monospace @error('no_npwp') is-invalid @enderror" type="text" name="no_npwp" id="npwp_input" value="{{ old('no_npwp') }}" minlength="15" maxlength="16" required placeholder="15-16 Digit Angka NPWP" />
                                @error('no_npwp') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Upload KTP <span class="text-danger">*</span></label>
                                <input class="form-control file-input-with-feedback @error('foto_ktp') is-invalid @enderror" type="file" name="foto_ktp" id="foto_ktp" accept="image/jpeg,image/png,image/jpg,image/webp" required data-max-mb="3" data-allowed-ext="jpg,jpeg,png,webp" />
                                <div class="form-text small"><i class="bi bi-camera me-1"></i>Foto dari Kamera HP atau pilih Galeri (Maks 3MB)</div>
                                <div class="file-feedback-box mt-2" id="feedback_foto_ktp"></div>
                                @error('foto_ktp') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Upload NPWP <span class="text-danger">*</span></label>
                                <input class="form-control file-input-with-feedback @error('foto_npwp') is-invalid @enderror" type="file" name="foto_npwp" id="foto_npwp" accept="image/jpeg,image/png,image/jpg,image/webp" required data-max-mb="3" data-allowed-ext="jpg,jpeg,png,webp" />
                                <div class="form-text small"><i class="bi bi-camera me-1"></i>Foto dari Kamera HP atau pilih Galeri (Maks 3MB)</div>
                                <div class="file-feedback-box mt-2" id="feedback_foto_npwp"></div>
                                @error('foto_npwp') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Upload CV <span class="text-danger">*</span></label>
                                <input class="form-control file-input-with-feedback @error('cv') is-invalid @enderror" type="file" name="cv" id="cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required data-max-mb="10" data-allowed-ext="pdf,doc,docx" />
                                <div class="form-text small"><i class="bi bi-file-earmark-pdf me-1"></i>Upload berkas PDF / DOC / DOCX (Maks 10MB)</div>
                                <div class="file-feedback-box mt-2" id="feedback_cv"></div>
                                @error('cv') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                         </div>
                      </div>
                      <div class="mt-4 d-flex justify-content-between">
                         <button type="button" class="btn btn-secondary px-4" onclick="prevStep(event)"><i class="bi bi-arrow-left me-2"></i> Kembali</button>
                         <button type="button" class="btn btn-primary px-4" onclick="nextStep(event)">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
                     </div>
                </div>

                <!-- Step 6: Jadwal -->
                <div class="step-section" id="step6">
                     <div class="mb-4">
                         <h5 class="text-dark fw-bold mb-5 d-flex align-items-center">
                             <i class="bi bi-calendar-check me-2"></i> Ketersediaan Mengajar
                         </h5>
                         <div class="alert alert-soft-info d-flex align-items-center small">
                             <i class="bi bi-info-circle me-2 fs-5 flex-shrink-0"></i>
                             <div>
                                 Pilih jam-jam di mana Anda <strong>BERSEDIA</strong> mengajar (minimal 1 pilihan jam).
                                 <div class="text-muted mt-1 small"><i class="bi bi-hand-index-thumb me-1"></i> <strong>Tips:</strong> Klik/ketuk nama hari (misal <strong>Senin</strong>) untuk memilih seluruh jam pada hari tersebut.</div>
                             </div>
                         </div>
                         @error('waktu_mengajar')
                             <div class="alert alert-danger mb-3 small">
                                 <i class="bi bi-exclamation-triangle me-1"></i> {{ $message }}
                             </div>
                         @enderror
                         <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 bg-light p-3 rounded border">
                             <div class="small fw-bold text-dark"><i class="bi bi-magic me-1 text-primary"></i> Pilih Cepat Jam Mengajar:</div>
                             <div class="d-flex flex-wrap gap-2">
                                 <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" onclick="quickSelectSchedule('weekdays')">
                                     <i class="bi bi-calendar-range me-1"></i> Senin – Jumat (08:00 – 16:00)
                                 </button>
                                 <button type="button" class="btn btn-sm btn-outline-success fw-semibold" onclick="quickSelectSchedule('all')">
                                     <i class="bi bi-check-all me-1"></i> Pilih Semua
                                 </button>
                                 <button type="button" class="btn btn-sm btn-outline-secondary fw-semibold" onclick="quickSelectSchedule('clear')">
                                     <i class="bi bi-x-circle me-1"></i> Bersihkan
                                 </button>
                             </div>
                         </div>
                         <div class="table-responsive rounded-3 border border-light">
                             <table class="table table-bordered table-modern mb-0 text-center align-middle" style="min-width: 600px;">
                                 <thead class="table-light">
                                     <tr>
                                         <th width="10%">Hari</th>
                                         <th width="10%">08:00</th>
                                         <th width="10%">09:00</th>
                                         <th width="10%">10:00</th>
                                         <th width="10%">11:00</th>
                                         <th width="10%">12:00</th>
                                         <th width="10%">13:00</th>
                                         <th width="10%">14:00</th>
                                         <th width="10%">15:00</th>
                                         <th width="10%">16:00</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                     <tr>
                                         <td class="fw-bold text-dark bg-light align-middle" style="cursor: pointer; user-select: none;" onclick="toggleDaySchedule('{{ $hari }}')" title="Klik untuk pilih / batal semua jam hari {{ $hari }}">
                                             {{ $hari }} <i class="bi bi-check2-all ms-1 small text-primary"></i>
                                         </td>
                                         @foreach(['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'] as $jam)
                                         <td class="p-0 position-relative align-middle text-center" style="min-width: 52px; height: 48px;">
                                             <input type="checkbox" name="waktu_mengajar[{{ $hari }}][]" value="{{ $jam }}" class="btn-check schedule-checkbox" id="check_{{ $hari }}_{{ str_replace(':', '-', $jam) }}" autocomplete="off" {{ (is_array(old('waktu_mengajar.'.$hari)) && in_array($jam, old('waktu_mengajar.'.$hari))) ? 'checked' : '' }}>
                                             <label class="btn btn-outline-primary border-0 w-100 h-100 rounded-0 d-flex align-items-center justify-content-center m-0 py-2" for="check_{{ $hari }}_{{ str_replace(':', '-', $jam) }}" style="cursor: pointer; min-height: 48px; user-select: none;">
                                                 <i class="bi bi-plus-lg unchecked-icon"></i>
                                                 <i class="bi bi-check-lg checked-icon"></i>
                                             </label>
                                         </td>
                                         @endforeach
                                     </tr>
                                     @endforeach
                                 </tbody>
                             </table>
                         </div>
                     </div>

                    <div class="mt-5 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary px-4 py-3 fw-bold rounded-pill shadow-lg hover-scale" onclick="prevStep(event)">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </button>
                        <button type="button" id="submitInstructorBtn" class="btn btn-primary px-5 py-3 fw-bold rounded-pill shadow-lg hover-scale" onclick="submitInstructorForm(event)">
                            <i class="bi bi-send me-2"></i> Daftar Sebagai Instruktur
                        </button>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none text-muted">Sudah punya akun? <strong class="text-primary">Login Disini</strong></a>
                    </div>
                </div>
            </form>

            {{-- CRITICAL: Inline script placed immediately after form so functions are available before @push('scripts') loads --}}
            <script>
                var currentStep = 1;
                var _formSubmitAllowed = false;

                function hideJsAlert() {
                    var el = document.getElementById("jsStepErrorAlert");
                    if (el) el.classList.add("d-none");
                    var gl = document.getElementById("globalErrorAlert");
                    if (gl) gl.classList.add("d-none");
                }

                function showJsAlert(message) {
                    var el = document.getElementById("jsStepErrorAlert");
                    var msg = document.getElementById("jsStepErrorMessage");
                    if (el && msg) {
                        msg.innerText = message;
                        el.classList.remove("d-none");
                    }
                }

                var stepTitles = {
                    1: 'Informasi Akun & Pribadi Dasar',
                    2: 'Identitas Lengkap',
                    3: 'Domisili & Pendidikan',
                    4: 'Kesehatan & Logistik',
                    5: 'Bank & Berkas Dokumen',
                    6: 'Jadwal Mengajar'
                };

                function showStep(n) {
                    currentStep = n;
                    hideJsAlert();
                    var steps = document.querySelectorAll(".step-section");
                    var indicators = document.querySelectorAll(".progress-step");
                    for (var i = 0; i < steps.length; i++) {
                        if (i + 1 === n) { steps[i].classList.add("active"); } else { steps[i].classList.remove("active"); }
                    }
                    for (var j = 0; j < indicators.length; j++) {
                        if (j + 1 === n) { indicators[j].classList.add("active"); } else { indicators[j].classList.remove("active"); }
                    }

                    var mobileBadge = document.getElementById("mobileStepBadge");
                    var mobileTitle = document.getElementById("mobileStepTitle");
                    var mobileBar = document.getElementById("mobileProgressBar");
                    if (mobileBadge) mobileBadge.innerText = "Langkah " + n + " dari 6";
                    if (mobileTitle) mobileTitle.innerText = stepTitles[n] || "";
                    if (mobileBar) mobileBar.style.width = Math.round((n / 6) * 100) + "%";

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                function validateStep(n) {
                    hideJsAlert();
                    var stepSection = document.getElementById("step" + n);
                    if (!stepSection) return true;

                    var valid = true;
                    var errorMessage = "";

                    var requiredInputs = stepSection.querySelectorAll("[required]");
                    for (var i = 0; i < requiredInputs.length; i++) {
                        var input = requiredInputs[i];
                        var isEmpty = false;
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            isEmpty = !input.checked;
                        } else if (input.tagName === 'SELECT') {
                            isEmpty = !input.value || input.value.trim() === '';
                        } else {
                            isEmpty = !input.value || !input.value.trim();
                        }
                        if (isEmpty) { input.classList.add("is-invalid"); valid = false; }
                        else { input.classList.remove("is-invalid"); }
                    }

                    if (n === 1) {
                        var email = stepSection.querySelector('input[name="email"]');
                        if (email && email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                            email.classList.add("is-invalid"); valid = false;
                            errorMessage = "Format alamat email tidak valid.";
                        }
                        var pwd = stepSection.querySelector('input[name="password"]');
                        var pwdConf = stepSection.querySelector('input[name="password_confirmation"]');
                        if (pwd && pwd.value && pwd.value.length < 8) {
                            pwd.classList.add("is-invalid"); valid = false;
                            errorMessage = "Password minimal harus 8 karakter.";
                        }
                        if (pwd && pwdConf && pwd.value !== pwdConf.value) {
                            pwdConf.classList.add("is-invalid"); valid = false;
                            errorMessage = "Konfirmasi password tidak cocok.";
                        }
                        var phone1 = stepSection.querySelector('input[name="no_hp_1"]');
                        if (phone1 && phone1.value.replace(/[^0-9]/g, '').length < 10) {
                            phone1.classList.add("is-invalid"); valid = false;
                            errorMessage = errorMessage || "No HP WhatsApp minimal 10 digit angka.";
                        }
                    }

                    if (n === 2) {
                        var nik = stepSection.querySelector('input[name="nik"]');
                        if (nik && nik.value.replace(/[^0-9]/g, '').length !== 16) {
                            nik.classList.add("is-invalid"); valid = false;
                            errorMessage = "NIK harus tepat 16 digit angka.";
                        }
                    }

                    if (n === 4) {
                        var checkedAlat = stepSection.querySelectorAll('input[name="alat_mengajar[]"]:checked');
                        var container = document.getElementById("alat_mengajar_container");
                        if (checkedAlat.length === 0) {
                            if (container) container.classList.add("border-danger");
                            valid = false; errorMessage = "Pilih minimal 1 alat mengajar.";
                        } else { if (container) container.classList.remove("border-danger"); }
                    }

                    if (n === 5) {
                        var npwp = stepSection.querySelector('input[name="no_npwp"]');
                        var npwpLen = npwp ? npwp.value.replace(/[^0-9]/g, '').length : 0;
                        if (npwp && (npwpLen < 15 || npwpLen > 16)) {
                            npwp.classList.add("is-invalid"); valid = false;
                            errorMessage = "NPWP harus 15-16 digit angka.";
                        }
                        var fileInputs = stepSection.querySelectorAll('input[type="file"][required]');
                        for (var fi = 0; fi < fileInputs.length; fi++) {
                            if (!fileInputs[fi].files || fileInputs[fi].files.length === 0) {
                                fileInputs[fi].classList.add("is-invalid"); valid = false;
                                errorMessage = errorMessage || "Upload file KTP, NPWP, dan CV.";
                            }
                        }
                    }

                    if (n === 6) {
                        var checkedJadwal = stepSection.querySelectorAll('.schedule-checkbox:checked');
                        if (checkedJadwal.length === 0) {
                            valid = false; errorMessage = "Pilih minimal 1 jadwal mengajar.";
                        }
                    }

                    if (!valid) {
                        showJsAlert(errorMessage || "Mohon lengkapi seluruh isian wajib bertanda (*) pada langkah ini.");
                    }
                    return valid;
                }

                function nextStep(e) {
                    if (e) { e.preventDefault(); e.stopPropagation(); }
                    if (validateStep(currentStep)) {
                        if (currentStep < 6) {
                            showStep(currentStep + 1);
                        }
                    } else {
                        var stepEl = document.getElementById("step" + currentStep);
                        var inv = stepEl ? stepEl.querySelector(".is-invalid") : null;
                        if (inv) { try { inv.focus(); inv.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch(err){} }
                    }
                    return false;
                }

                function prevStep(e) {
                    if (e) { e.preventDefault(); e.stopPropagation(); }
                    hideJsAlert();
                    if (currentStep > 1) { showStep(currentStep - 1); }
                    return false;
                }

                function goToStep(target) {
                    if (target < currentStep) { showStep(target); }
                    else if (target > currentStep) {
                        for (var s = currentStep; s < target; s++) {
                            if (!validateStep(s)) { showStep(s); return; }
                        }
                        showStep(target);
                    }
                }

                window.quickSelectSchedule = function(action) {
                    var checkboxes = document.querySelectorAll('.schedule-checkbox');
                    for (var i = 0; i < checkboxes.length; i++) {
                        var cb = checkboxes[i];
                        var id = cb.id || '';
                        if (action === 'all') { cb.checked = true; }
                        else if (action === 'clear') { cb.checked = false; }
                        else if (action === 'weekdays') { cb.checked = !id.includes('Sabtu'); }
                    }
                };

                window.toggleDaySchedule = function(hari) {
                    var checkboxes = document.querySelectorAll('[id^="check_' + hari + '_"]');
                    var allChecked = true;
                    for (var i = 0; i < checkboxes.length; i++) {
                        if (!checkboxes[i].checked) { allChecked = false; break; }
                    }
                    for (var j = 0; j < checkboxes.length; j++) {
                        checkboxes[j].checked = !allChecked;
                    }
                };

                function submitInstructorForm(e) {
                    if (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    var allValid = true;
                    for (var s = 1; s <= 6; s++) {
                        if (!validateStep(s)) {
                            allValid = false;
                            showStep(s);
                            var stepEl = document.getElementById("step" + s);
                            var inv = stepEl ? stepEl.querySelector(".is-invalid") : null;
                            if (inv) {
                                try { inv.focus(); inv.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch(err){}
                            }
                            return false;
                        }
                    }

                    var submitBtn = document.getElementById("submitInstructorBtn");
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Memproses Pendaftaran...';
                    }

                    _formSubmitAllowed = true;
                    var form = document.getElementById("instructorRegisterForm");
                    if (form) {
                        form.submit();
                    }
                    return true;
                }
                window.submitInstructorForm = submitInstructorForm;
            </script>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.getElementById("instructorRegisterForm");

        if (form) {
            // Prevent Enter key from submitting form prematurely on steps 1-5
            form.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && e.target.tagName !== "TEXTAREA") {
                    e.preventDefault();
                    e.stopPropagation();
                    if (currentStep < 6) {
                        nextStep(e);
                    } else if (currentStep === 6) {
                        submitInstructorForm(e);
                    }
                    return false;
                }
            });

            form.addEventListener("submit", function(e) {
                if (_formSubmitAllowed) return true;
                return submitInstructorForm(e);
            });
        }

        // Toggle Password Visibility (Icon Mata)
        var togglePasswordBtn = document.getElementById('togglePasswordBtn');
        var passwordInput = document.getElementById('password');
        var togglePasswordIcon = document.getElementById('togglePasswordIcon');
        if (togglePasswordBtn && passwordInput && togglePasswordIcon) {
            togglePasswordBtn.addEventListener('click', function() {
                var isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                togglePasswordIcon.className = isPassword ? 'bi bi-eye-slash-fill text-primary' : 'bi bi-eye';
            });
        }

        var togglePasswordConfirmBtn = document.getElementById('togglePasswordConfirmBtn');
        var passwordConfirmInput = document.getElementById('password_confirmation');
        var togglePasswordConfirmIcon = document.getElementById('togglePasswordConfirmIcon');
        if (togglePasswordConfirmBtn && passwordConfirmInput && togglePasswordConfirmIcon) {
            togglePasswordConfirmBtn.addEventListener('click', function() {
                var isPassword = passwordConfirmInput.type === 'password';
                passwordConfirmInput.type = isPassword ? 'text' : 'password';
                togglePasswordConfirmIcon.className = isPassword ? 'bi bi-eye-slash-fill text-primary' : 'bi bi-eye';
            });
        }

        // Input Masking for NIK & NPWP
        var nikInputMask = document.querySelector('input[name="nik"]');
        if (nikInputMask) {
            nikInputMask.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').substring(0, 16);
                if (this.value.length === 16) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });
        }

        // Real-time File Upload Validation & Green Checklist Feedback
        document.querySelectorAll('.file-input-with-feedback').forEach(function(input) {
            input.addEventListener('change', function() {
                var file = this.files[0];
                var feedbackEl = document.getElementById('feedback_' + this.id);
                var maxMb = parseFloat(this.getAttribute('data-max-mb') || 5);
                var allowedExts = (this.getAttribute('data-allowed-ext') || '').split(',');

                if (!feedbackEl) return;

                if (!file) {
                    feedbackEl.innerHTML = '';
                    this.classList.remove('is-valid', 'is-invalid');
                    return;
                }

                var ext = file.name.split('.').pop().toLowerCase();
                var sizeMb = (file.size / (1024 * 1024)).toFixed(2);

                if (!allowedExts.includes(ext)) {
                    this.value = '';
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    feedbackEl.innerHTML = '<div class="p-2 rounded bg-danger bg-opacity-10 text-danger border border-danger small"><i class="bi bi-x-circle-fill me-1"></i> Format <strong>.' + ext + '</strong> tidak diizinkan. Gunakan ' + allowedExts.join(', ').toUpperCase() + '.</div>';
                    return;
                }

                if (file.size > maxMb * 1024 * 1024) {
                    this.value = '';
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    feedbackEl.innerHTML = '<div class="p-2 rounded bg-danger bg-opacity-10 text-danger border border-danger small"><i class="bi bi-x-circle-fill me-1"></i> Ukuran file <strong>' + sizeMb + ' MB</strong> melebihi batas maksimal ' + maxMb + ' MB.</div>';
                    return;
                }

                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                feedbackEl.innerHTML = '<div class="p-2 rounded bg-success bg-opacity-10 text-success border border-success small fw-semibold"><i class="bi bi-check-circle-fill me-1 text-success fs-6"></i> ' + file.name + ' (' + sizeMb + ' MB) — Siap di-upload</div>';
            });
        });

        // Quick Select Schedule Helpers (Step 6)
        window.quickSelectSchedule = function(action) {
            var checkboxes = document.querySelectorAll('.schedule-checkbox');
            checkboxes.forEach(function(cb) {
                var id = cb.id || '';
                if (action === 'all') { cb.checked = true; }
                else if (action === 'clear') { cb.checked = false; }
                else if (action === 'weekdays') { cb.checked = !id.includes('Sabtu'); }
            });
        };

        window.toggleDaySchedule = function(hari) {
            var checkboxes = document.querySelectorAll('[id^="check_' + hari + '_"]');
            var allChecked = Array.from(checkboxes).every(function(cb) { return cb.checked; });
            checkboxes.forEach(function(cb) { cb.checked = !allChecked; });
        };

        // Real-Time Digit Counters for NIK & NPWP
        var nikInput2 = document.getElementById('nik_input') || document.querySelector('input[name="nik"]');
        var nikCounter = document.getElementById('nik_counter');
        var updateNikCounter = function() {
            if (!nikInput2 || !nikCounter) return;
            var len = nikInput2.value.replace(/[^0-9]/g, '').length;
            nikCounter.innerText = len + ' / 16 Digit';
            nikCounter.className = len === 16 ? 'badge bg-success' : (len > 0 ? 'badge bg-warning text-dark' : 'badge bg-secondary');
        };
        if (nikInput2) {
            nikInput2.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').substring(0, 16);
                updateNikCounter();
                if (this.value.length === 16) { this.classList.remove('is-invalid'); this.classList.add('is-valid'); }
                else { this.classList.remove('is-valid'); }
            });
            updateNikCounter();
        }

        var npwpInput = document.getElementById('npwp_input') || document.querySelector('input[name="no_npwp"]');
        var npwpCounter = document.getElementById('npwp_counter');
        var updateNpwpCounter = function() {
            if (!npwpInput || !npwpCounter) return;
            var len = npwpInput.value.replace(/[^0-9]/g, '').length;
            npwpCounter.innerText = len + ' / 15-16 Digit';
            npwpCounter.className = (len >= 15 && len <= 16) ? 'badge bg-success' : (len > 0 ? 'badge bg-warning text-dark' : 'badge bg-secondary');
        };
        if (npwpInput) {
            npwpInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').substring(0, 16);
                updateNpwpCounter();
                if (this.value.length >= 15 && this.value.length <= 16) { this.classList.remove('is-invalid'); this.classList.add('is-valid'); }
                else { this.classList.remove('is-valid'); }
            });
            updateNpwpCounter();
        }

        // Input Masking for Phone Numbers
        var phoneInputs = document.querySelectorAll('input[name="no_hp_1"], input[name="no_hp_2"]');
        phoneInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').substring(0, 15);
                if (this.value.length >= 10) { this.classList.remove('is-invalid'); this.classList.add('is-valid'); }
                else { this.classList.remove('is-valid'); }
            });
        });

        // Determine starting step based on backend error state
        @if ($errors->any())
            var errorFields = @json(array_keys($errors->toArray()));
            var targetStep = 1;
            var stepMap = {
                1: ['email', 'no_hp_1', 'password'],
                2: ['nama_lengkap', 'gelar_depan', 'gelar_belakang', 'nama_panggilan', 'nik', 'tanggal_lahir', 'agama', 'status_pernikahan'],
                3: ['alamat_domisili', 'kota_domisili', 'no_hp_2', 'pend_terakhir', 'universitas_jurusan', 'pekerjaan_terakhir', 'jenjang_mengajar', 'kompetensi_1', 'kompetensi_2'],
                4: ['tinggi_badan', 'berat_badan', 'mata_minus', 'riwayat_penyakit', 'alat_mengajar', 'catatan_alat', 'kendaraan', 'jenis_kendaraan'],
                5: ['nama_bank', 'no_rekening', 'no_npwp', 'foto_ktp', 'foto_npwp', 'cv'],
                6: ['waktu_mengajar']
            };
            for (var step = 1; step <= 6; step++) {
                if (stepMap[step].some(function(field) { return errorFields.includes(field); })) {
                    targetStep = step;
                    break;
                }
            }
            showStep(targetStep);
        @else
            showStep(1);
        @endif
    });
</script>
@endpush
