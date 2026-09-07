<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white p-3.5 border-bottom">
        <ul class="nav nav-pills-impeccable" id="instructorProfileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="account-tab" data-bs-toggle="tab" data-bs-target="#account-pane" type="button" role="tab" aria-controls="account-pane" aria-selected="true">
                    <i class="bi bi-person-fill"></i> Data Akun & Domisili
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs-pane" type="button" role="tab" aria-controls="docs-pane" aria-selected="false">
                    <i class="bi bi-credit-card-2-front"></i> Bank & Berkas
                    @if(empty($profile->nama_bank) || empty($profile->no_rekening))
                        <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.68rem;">Wajib</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional-pane" type="button" role="tab" aria-controls="professional-pane" aria-selected="false">
                    <i class="bi bi-mortarboard-fill"></i> Karir & Logistik
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule-pane" type="button" role="tab" aria-controls="schedule-pane" aria-selected="false">
                    <i class="bi bi-calendar-week-fill"></i> Jadwal Mengajar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports-pane" type="button" role="tab" aria-controls="reports-pane" aria-selected="false">
                    <i class="bi bi-journal-check"></i> Riwayat Laporan
                    <span class="badge bg-primary bg-opacity-20 text-primary ms-1 rounded-pill">{{ count($recentReports ?? []) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" type="button" role="tab" aria-controls="security-pane" aria-selected="false">
                    <i class="bi bi-shield-lock-fill"></i> Ganti Password
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body p-4 p-md-5">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="instructorProfileForm">
            @csrf
            @method('patch')

            <div class="tab-content" id="instructorProfileTabsContent">
                <!-- Tab 1: Account Info & Domisili -->
                <div class="tab-pane fade show active" id="account-pane" role="tabpanel" aria-labelledby="account-tab" tabindex="0">
                    <h5 class="text-primary fw-bold border-bottom pb-2 mb-4"><i class="bi bi-person-lines-fill me-2"></i>Data Akun & Domisili</h5>
                    
                    <!-- Foto Profil Instruktur Card -->
                    <div class="card bg-light border-0 shadow-sm rounded-4 p-3 mb-4">
                        <div class="d-flex flex-column flex-sm-row align-items-center gap-3">
                            <div class="position-relative">
                                <div id="avatarContainer" class="rounded-circle shadow-sm border border-3 border-white d-flex align-items-center justify-content-center overflow-hidden bg-primary bg-opacity-10" style="width: 96px; height: 96px;">
                                    @if($user->avatar_url)
                                        <img id="avatarImagePreview" src="{{ $user->avatar_url }}" alt="{{ $user->nama_lengkap }}" class="w-100 h-100 object-fit-cover">
                                        <span id="avatarInitialsFallback" class="d-none fw-bold text-primary" style="font-size: 2rem;">{{ $user->initials }}</span>
                                    @else
                                        <img id="avatarImagePreview" src="" alt="Preview" class="w-100 h-100 object-fit-cover d-none">
                                        <span id="avatarInitialsFallback" class="fw-bold text-primary" style="font-size: 2rem;">{{ $user->initials }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-grow-1 text-center text-sm-start">
                                <h6 class="fw-bold text-dark mb-1">Foto Profil Instruktur</h6>
                                <p class="text-muted small mb-2">
                                    Unggah foto formal / semi-formal dengan pakaian rapi.
                                </p>
                                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2">
                                    <label for="foto_profil" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 shadow-sm mb-0 cursor-pointer">
                                        <i class="bi bi-camera-fill me-1"></i> Pilih Foto
                                    </label>
                                    <input type="file" id="foto_profil" name="foto_profil" class="d-none" accept="image/jpeg,image/png,image/jpg,image/webp">
                                    <input type="hidden" id="remove_foto_profil" name="remove_foto_profil" value="0">
                                    
                                    <button type="button" id="btnRemoveAvatar" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 {{ $user->foto_profil ? '' : 'd-none' }}">
                                        <i class="bi bi-trash3 me-1"></i> Hapus Foto
                                    </button>
                                    <span id="selectedFileName" class="small text-muted fst-italic d-none"></span>
                                </div>
                                @error('foto_profil')
                                    <div class="text-danger small mt-1"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama_lengkap" class="form-label small text-muted text-uppercase fw-bold">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                            @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label small text-muted text-uppercase fw-bold">Email</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="gelar_depan" class="form-label small text-muted text-uppercase fw-bold">Gelar Depan</label>
                            <input type="text" id="gelar_depan" name="gelar_depan" class="form-control" value="{{ old('gelar_depan', $profile->gelar_depan ?? '') }}" placeholder="Dr. / Ir.">
                        </div>
                        <div class="col-md-8">
                            <label for="nama_panggilan" class="form-label small text-muted text-uppercase fw-bold">Nama Panggilan <span class="text-danger">*</span></label>
                            <input type="text" id="nama_panggilan" name="nama_panggilan" class="form-control @error('nama_panggilan') is-invalid @enderror" value="{{ old('nama_panggilan', $profile->nama_panggilan ?? '') }}" required>
                            @error('nama_panggilan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2">
                            <label for="gelar_belakang" class="form-label small text-muted text-uppercase fw-bold">Gelar Belakang</label>
                            <input type="text" id="gelar_belakang" name="gelar_belakang" class="form-control" value="{{ old('gelar_belakang', $profile->gelar_belakang ?? '') }}" placeholder="S.Pd / M.T">
                        </div>

                        <div class="col-md-6">
                            <label for="no_telephone" class="form-label small text-muted text-uppercase fw-bold">No. WhatsApp (Utama) <span class="text-danger">*</span></label>
                            <input type="text" id="no_telephone" name="no_telephone" class="form-control @error('no_telephone') is-invalid @enderror" value="{{ old('no_telephone', $user->no_telephone) }}" required>
                            @error('no_telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="no_hp_2" class="form-label small text-muted text-uppercase fw-bold">No. HP Darurat (Keluarga) <span class="text-danger">*</span></label>
                            <input type="text" id="no_hp_2" name="no_hp_2" class="form-control @error('no_hp_2') is-invalid @enderror" value="{{ old('no_hp_2', $profile->no_hp_2 ?? '') }}" required>
                            @error('no_hp_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="tanggal_lahir" class="form-label small text-muted text-uppercase fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="text" id="tanggal_lahir" name="tanggal_lahir" class="form-control datepicker @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : '') }}" required placeholder="YYYY-MM-DD">
                            @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="agama" class="form-label small text-muted text-uppercase fw-bold">Agama</label>
                            <select id="agama" name="agama" class="form-select @error('agama') is-invalid @enderror" required>
                                <option value="Islam" {{ old('agama', $user->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama', $user->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama', $user->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama', $user->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama', $user->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Lainnya" {{ old('agama', $user->agama) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('agama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="status_pernikahan" class="form-label small text-muted text-uppercase fw-bold">Status Pernikahan <span class="text-danger">*</span></label>
                            <select id="status_pernikahan" name="status_pernikahan" class="form-select @error('status_pernikahan') is-invalid @enderror" required>
                                <option value="Lajang" {{ old('status_pernikahan', $profile->status_pernikahan ?? '') == 'Lajang' ? 'selected' : '' }}>Lajang</option>
                                <option value="Menikah" {{ old('status_pernikahan', $profile->status_pernikahan ?? '') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="Duda/Janda" {{ old('status_pernikahan', $profile->status_pernikahan ?? '') == 'Duda/Janda' ? 'selected' : '' }}>Duda/Janda</option>
                            </select>
                            @error('status_pernikahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="alamat_domisili" class="form-label small text-muted text-uppercase fw-bold">Alamat Domisili <span class="text-danger">*</span></label>
                            <textarea id="alamat_domisili" name="alamat_domisili" rows="2" class="form-control @error('alamat_domisili') is-invalid @enderror" placeholder="Alamat lengkap domisili saat ini" required>{{ old('alamat_domisili', $profile->alamat_domisili ?? '') }}</textarea>
                            @error('alamat_domisili') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="kota_domisili" class="form-label small text-muted text-uppercase fw-bold">Kota Domisili <span class="text-danger">*</span></label>
                            <input type="text" id="kota_domisili" name="kota_domisili" class="form-control @error('kota_domisili') is-invalid @enderror" value="{{ old('kota_domisili', $profile->kota_domisili ?? '') }}" required placeholder="Contoh: Jakarta Selatan">
                            @error('kota_domisili') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Bank & Berkas -->
                <div class="tab-pane fade" id="docs-pane" role="tabpanel" aria-labelledby="docs-tab" tabindex="0">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                        <h5 class="text-primary fw-bold mb-0"><i class="bi bi-bank me-2"></i>Data Bank & Berkas</h5>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                            <i class="bi bi-asterisk me-1"></i>Rekening Wajib untuk Pencairan Honor
                        </span>
                    </div>

                    @if(empty($profile->nama_bank) || empty($profile->no_rekening))
                    <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 d-flex align-items-center shadow-xs" style="background: #FEF2F2; border-left: 5px solid #EF4444 !important;">
                        <i class="bi bi-exclamation-octagon-fill text-danger fs-3 me-3"></i>
                        <div>
                            <div class="fw-bold text-danger">Data Rekening Bank Wajib Diisi!</div>
                            <small class="text-dark">Mohon pilih nama bank dan masukkan nomor rekening Anda dengan benar. Data rekening yang valid mutlak diwajibkan oleh bagian keuangan agar Anda dapat membuat laporan kegiatan mengajar dan pencairan honor dapat diproses tanpa kendala.</small>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="nik" class="form-label small text-muted text-uppercase fw-bold">NIK (KTP) <span class="text-danger">*</span></label>
                            <input type="text" id="nik" name="nik" class="form-control font-monospace @error('nik') is-invalid @enderror" value="{{ old('nik', $profile->nik ?? '') }}" required minlength="16" maxlength="16">
                            @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nama_bank" class="form-label small text-muted text-uppercase fw-bold">Nama Bank <span class="text-danger">*</span></label>
                            @php
                                $selectedBank = strtoupper(old('nama_bank', $profile->nama_bank ?? ''));
                                $bankList = \App\Models\InstructorProfile::listNamaBank();
                            @endphp
                            <select id="nama_bank" name="nama_bank" class="form-select @error('nama_bank') is-invalid @enderror" required>
                                <option value="" disabled {{ $selectedBank ? '' : 'selected' }}>-- Pilih Singkatan Bank --</option>
                                @foreach($bankList as $bank)
                                    <option value="{{ $bank }}" {{ $selectedBank === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                                @endforeach
                                @if($selectedBank && !in_array($selectedBank, $bankList))
                                    <option value="{{ $selectedBank }}" selected>{{ $selectedBank }}</option>
                                @endif
                            </select>
                            @error('nama_bank') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="no_rekening" class="form-label small text-muted text-uppercase fw-bold">No. Rekening <span class="text-danger">*</span></label>
                            <input type="text" id="no_rekening" name="no_rekening" 
                                class="form-control font-monospace @error('no_rekening') is-invalid @enderror" 
                                value="{{ old('no_rekening', $profile->no_rekening ?? '') }}" 
                                inputmode="numeric" 
                                pattern="[0-9]*" 
                                minlength="5"
                                maxlength="30"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                placeholder="Contoh: 12341332 (hanya angka)" 
                                required>
                            <div class="form-text text-muted" style="font-size: 0.72rem;">Wajib untuk pencairan honor. Hanya angka, tanpa spasi.</div>
                            @error('no_rekening') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="no_npwp" class="form-label small text-muted text-uppercase fw-bold">NPWP (16 Digit)</label>
                            <input type="text" id="no_npwp" name="no_npwp" class="form-control font-monospace @error('no_npwp') is-invalid @enderror" value="{{ old('no_npwp', $profile->no_npwp ?? '') }}" maxlength="16">
                            @error('no_npwp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-arrow-up me-2 text-primary"></i>Unggah Dokumen Verifikasi</h6>
                    
                    <div class="row g-4">
                        <!-- Foto KTP -->
                        <div class="col-md-4">
                            <div class="card border border-light-subtle rounded-4 p-3.5 bg-white shadow-xs h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="stat-icon-circle bg-primary bg-opacity-10 text-primary" style="width: 32px; height: 32px; font-size: 1rem;">
                                                <i class="bi bi-person-vcard"></i>
                                            </div>
                                            <span class="fw-bold small text-dark">Foto KTP</span>
                                        </div>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Wajib</span>
                                    </div>
                                    <input type="file" id="foto_ktp" name="foto_ktp" class="form-control form-control-sm @error('foto_ktp') is-invalid @enderror" accept="image/*">
                                    <div class="form-text small text-muted mt-1.5" style="font-size: 0.72rem;"><i class="bi bi-info-circle me-1"></i>Maks 5MB. Format JPG/PNG.</div>
                                    @error('foto_ktp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @if(isset($profile->foto_ktp) && $profile->foto_ktp)
                                    <div class="mt-3 pt-2.5 border-top">
                                        <a href="{{ Storage::url($profile->foto_ktp) }}" target="_blank" class="btn btn-sm btn-outline-success w-100 rounded-pill fw-semibold" style="font-size: 0.78rem;">
                                            <i class="bi bi-image me-1"></i> Lihat KTP Terunggah
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Foto NPWP -->
                        <div class="col-md-4">
                            <div class="card border border-light-subtle rounded-4 p-3.5 bg-white shadow-xs h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="stat-icon-circle bg-success bg-opacity-10 text-success" style="width: 32px; height: 32px; font-size: 1rem;">
                                                <i class="bi bi-card-heading"></i>
                                            </div>
                                            <span class="fw-bold small text-dark">Foto NPWP</span>
                                        </div>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Opsional</span>
                                    </div>
                                    <input type="file" id="foto_npwp" name="foto_npwp" class="form-control form-control-sm @error('foto_npwp') is-invalid @enderror" accept="image/*">
                                    <div class="form-text small text-muted mt-1.5" style="font-size: 0.72rem;"><i class="bi bi-info-circle me-1"></i>Maks 5MB. Format JPG/PNG.</div>
                                    @error('foto_npwp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @if(isset($profile->foto_npwp) && $profile->foto_npwp)
                                    <div class="mt-3 pt-2.5 border-top">
                                        <a href="{{ Storage::url($profile->foto_npwp) }}" target="_blank" class="btn btn-sm btn-outline-success w-100 rounded-pill fw-semibold" style="font-size: 0.78rem;">
                                            <i class="bi bi-image me-1"></i> Lihat NPWP Terunggah
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- CV / Resume -->
                        <div class="col-md-4">
                            <div class="card border border-light-subtle rounded-4 p-3.5 bg-white shadow-xs h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="stat-icon-circle text-purple" style="background: rgba(139, 92, 246, 0.12); color: #8B5CF6; width: 32px; height: 32px; font-size: 1rem;">
                                                <i class="bi bi-file-earmark-person"></i>
                                            </div>
                                            <span class="fw-bold small text-dark">CV / Resume</span>
                                        </div>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Wajib</span>
                                    </div>
                                    <input type="file" id="cv" name="cv" class="form-control form-control-sm @error('cv') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                    <div class="form-text small text-muted mt-1.5" style="font-size: 0.72rem;"><i class="bi bi-info-circle me-1"></i>Maks 5MB. Format PDF/DOCX.</div>
                                    @error('cv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @if(isset($profile->cv_link) && $profile->cv_link)
                                    <div class="mt-3 pt-2.5 border-top">
                                        <a href="{{ Storage::url($profile->cv_link) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-semibold" style="font-size: 0.78rem;">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> Lihat CV Terunggah
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Karir & Logistik -->
                <div class="tab-pane fade" id="professional-pane" role="tabpanel" aria-labelledby="professional-tab" tabindex="0">
                    <h5 class="text-primary fw-bold border-bottom pb-2 mb-4"><i class="bi bi-mortarboard me-2"></i>Karir & Kualifikasi</h5>
                    
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <label for="pend_terakhir" class="form-label small text-muted text-uppercase fw-bold">Pendidikan Terakhir <span class="text-danger">*</span></label>
                            <select id="pend_terakhir" name="pend_terakhir" class="form-select" required>
                                <option value="SMA/SMK Sederajat" {{ old('pend_terakhir', $user->pend_terakhir) == 'SMA/SMK Sederajat' ? 'selected' : '' }}>SMA/SMK Sederajat</option>
                                <option value="D3" {{ old('pend_terakhir', $user->pend_terakhir) == 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="D4/S1" {{ old('pend_terakhir', $user->pend_terakhir) == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                                <option value="S2" {{ old('pend_terakhir', $user->pend_terakhir) == 'S2' ? 'selected' : '' }}>S2</option>
                                <option value="S3" {{ old('pend_terakhir', $user->pend_terakhir) == 'S3' ? 'selected' : '' }}>S3</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="universitas_jurusan" class="form-label small text-muted text-uppercase fw-bold">Universitas & Jurusan <span class="text-danger">*</span></label>
                            <input type="text" id="universitas_jurusan" name="universitas_jurusan" class="form-control @error('universitas_jurusan') is-invalid @enderror" value="{{ old('universitas_jurusan', $profile->universitas_jurusan ?? '') }}" required placeholder="Contoh: Universitas Indonesia - Fisika">
                            @error('universitas_jurusan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="pekerjaan_terakhir" class="form-label small text-muted text-uppercase fw-bold">Pekerjaan Terakhir <span class="text-danger">*</span></label>
                            <input type="text" id="pekerjaan_terakhir" name="pekerjaan_terakhir" class="form-control @error('pekerjaan_terakhir') is-invalid @enderror" value="{{ old('pekerjaan_terakhir', $profile->pekerjaan_terakhir ?? '') }}" required>
                            @error('pekerjaan_terakhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="jenjang_mengajar" class="form-label small text-muted text-uppercase fw-bold">Jenjang Mengajar <span class="text-danger">*</span></label>
                            <input type="text" id="jenjang_mengajar" name="jenjang_mengajar" class="form-control @error('jenjang_mengajar') is-invalid @enderror" value="{{ old('jenjang_mengajar', $profile->jenjang_mengajar ?? '') }}" required placeholder="Contoh: SD, SMP, SMA">
                            @error('jenjang_mengajar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="kompetensi_1" class="form-label small text-muted text-uppercase fw-bold">Keahlian Utama (Kompetensi 1) <span class="text-danger">*</span></label>
                            <input type="text" id="kompetensi_1" name="kompetensi_1" class="form-control @error('kompetensi_1') is-invalid @enderror" value="{{ old('kompetensi_1', $user->kompetensi_1) }}" required>
                            @error('kompetensi_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="kompetensi_2" class="form-label small text-muted text-uppercase fw-bold">Keahlian Tambahan (Kompetensi 2)</label>
                            <input type="text" id="kompetensi_2" name="kompetensi_2" class="form-control" value="{{ old('kompetensi_2', $user->kompetensi_2) }}">
                        </div>
                    </div>

                    <h5 class="text-primary fw-bold border-bottom pb-2 mb-4"><i class="bi bi-truck me-2"></i>Kesehatan & Logistik</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="tinggi_badan" class="form-label small text-muted text-uppercase fw-bold">Tinggi Badan (cm) <span class="text-danger">*</span></label>
                            <input type="number" id="tinggi_badan" name="tinggi_badan" class="form-control @error('tinggi_badan') is-invalid @enderror" value="{{ old('tinggi_badan', $profile ? explode('cm', explode('/', $profile->tinggi_berat_badan)[0] ?? '')[0] ?? '' : '') }}" required>
                            @error('tinggi_badan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="berat_badan" class="form-label small text-muted text-uppercase fw-bold">Berat Badan (kg) <span class="text-danger">*</span></label>
                            <input type="number" id="berat_badan" name="berat_badan" class="form-control @error('berat_badan') is-invalid @enderror" value="{{ old('berat_badan', $profile ? explode('kg', explode('/', $profile->tinggi_berat_badan)[1] ?? '')[0] ?? '' : '') }}" required>
                            @error('berat_badan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="mata_minus" class="form-label small text-muted text-uppercase fw-bold">Mata Minus <span class="text-danger">*</span></label>
                            <input type="text" id="mata_minus" name="mata_minus" class="form-control @error('mata_minus') is-invalid @enderror" value="{{ old('mata_minus', $profile->mata_minus ?? '') }}" required placeholder="Normal / -1.5">
                            @error('mata_minus') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="riwayat_penyakit" class="form-label small text-muted text-uppercase fw-bold">Riwayat Penyakit</label>
                            <input type="text" id="riwayat_penyakit" name="riwayat_penyakit" class="form-control" value="{{ old('riwayat_penyakit', $profile->riwayat_penyakit ?? '') }}" placeholder="Kosongkan jika sehat">
                        </div>

                        <div class="col-md-6">
                            <label for="kendaraan" class="form-label small text-muted text-uppercase fw-bold">Kendaraan <span class="text-danger">*</span></label>
                            <select id="kendaraan" name="kendaraan" class="form-select @error('kendaraan') is-invalid @enderror" required>
                                <option value="Pribadi" {{ old('kendaraan', $profile->kendaraan ?? '') == 'Pribadi' ? 'selected' : '' }}>Pribadi</option>
                                <option value="Umum" {{ old('kendaraan', $profile->kendaraan ?? '') == 'Umum' ? 'selected' : '' }}>Umum</option>
                                <option value="Antar Jemput" {{ old('kendaraan', $profile->kendaraan ?? '') == 'Antar Jemput' ? 'selected' : '' }}>Antar Jemput</option>
                            </select>
                            @error('kendaraan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="jenis_kendaraan" class="form-label small text-muted text-uppercase fw-bold">Jenis Kendaraan <span class="text-danger">*</span></label>
                            <input type="text" id="jenis_kendaraan" name="jenis_kendaraan" class="form-control @error('jenis_kendaraan') is-invalid @enderror" value="{{ old('jenis_kendaraan', $profile->jenis_kendaraan ?? '') }}" required placeholder="Motor / Mobil / MRT / Busway">
                            @error('jenis_kendaraan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small text-muted text-uppercase fw-bold d-block">Alat Mengajar yang Dimiliki <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-4 mt-1">
                                @php $ownedTools = isset($profile->alat_mengajar) ? json_decode($profile->alat_mengajar, true) : []; @endphp
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="alat_mengajar[]" value="Laptop" id="tool_laptop" {{ in_array('Laptop', $ownedTools ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tool_laptop">Laptop</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="alat_mengajar[]" value="Handphone" id="tool_hp" {{ in_array('Handphone', $ownedTools ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tool_hp">Handphone</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="alat_mengajar[]" value="Tablet" id="tool_tablet" {{ in_array('Tablet', $ownedTools ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tool_tablet">Tablet (Android/iPad)</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="catatan_alat" class="form-label small text-muted text-uppercase fw-bold">Catatan Alat Mengajar</label>
                            <input type="text" id="catatan_alat" name="catatan_alat" class="form-control" value="{{ old('catatan_alat', $profile->catatan_alat ?? '') }}" placeholder="Contoh: Baterai laptop drop">
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Ketersediaan Jadwal -->
                <div class="tab-pane fade" id="schedule-pane" role="tabpanel" aria-labelledby="schedule-tab" tabindex="0">
                    <h5 class="text-primary fw-bold border-bottom pb-2 mb-4"><i class="bi bi-calendar-check me-2"></i>Ketersediaan Waktu Mengajar</h5>
                    <div class="alert alert-soft-info d-flex align-items-center small mb-4">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <span>Pilih jam-jam di mana Anda <strong>BERSEDIA</strong> menerima tugas mengajar.</span>
                    </div>

                    <div class="table-responsive rounded-3 border border-light">
                        <table class="table table-bordered table-modern mb-0 text-center table-hover" style="min-width: 600px;">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">Hari</th>
                                    @foreach(['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'] as $jam)
                                        <th width="10%">{{ $jam }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php $existingSchedule = isset($profile->waktu_mengajar) ? $profile->waktu_mengajar : []; @endphp
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                    <tr>
                                        <td class="fw-bold text-muted bg-light">{{ $hari }}</td>
                                        @foreach(['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'] as $jam)
                                            <td class="p-0 position-relative">
                                                <input type="checkbox" name="waktu_mengajar[{{ $hari }}][]" value="{{ $jam }}" class="btn-check" id="tabcheck_{{ $hari }}_{{ str_replace(':', '-', $jam) }}" autocomplete="off"
                                                    {{ isset($existingSchedule[$hari]) && in_array($jam, $existingSchedule[$hari]) ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary border-0 w-100 h-100 rounded-0 d-flex align-items-center justify-content-center py-2" for="tabcheck_{{ $hari }}_{{ str_replace(':', '-', $jam) }}" style="min-height: 40px; min-width: 40px;">
                                                    <i class="bi bi-check-lg fs-5"></i>
                                                </label>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="submitProfileBtnContainer" class="mt-5 text-center">
                    <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-pill shadow-lg hover-scale">
                        <i class="bi bi-save me-2"></i> Simpan Pembaruan Profil
                    </button>
                </div>
            </form>

            <!-- Tab 5: Riwayat Laporan Mengajar Sesi -->
            <div class="tab-pane fade" id="reports-pane" role="tabpanel" aria-labelledby="reports-tab" tabindex="0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-bottom pb-3 mb-4">
                    <div>
                        <h5 class="text-primary fw-bold mb-1"><i class="bi bi-journal-check me-2"></i>Riwayat Laporan Mengajar Sesi</h5>
                        <p class="text-muted small mb-0">Daftar sesi pembelajaran yang telah selesai. Anda dapat menyalin format laporan atau mengirimkannya langsung ke nomor WhatsApp Anda via Fonnte.</p>
                    </div>
                    <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold">
                        <i class="bi bi-arrow-right-circle me-1"></i> Buka Semua Laporan
                    </a>
                </div>

                @if(isset($recentReports) && count($recentReports) > 0)
                    <div class="row g-3">
                        @foreach($recentReports as $rep)
                            @php
                                $repMeta = is_array($rep->metadata_json) ? $rep->metadata_json : (json_decode($rep->metadata_json, true) ?? []);
                                $repWaSent = !empty($repMeta['wa_report_sent']);
                                $repWaSentAt = !empty($repMeta['wa_report_sent_at']) ? \Carbon\Carbon::parse($repMeta['wa_report_sent_at'])->locale('id')->translatedFormat('d M Y H:i') : '';
                                $repWaText = \App\Notifications\SessionReportNotification::generateReportMessage($rep);
                                $repSchool = $rep->sekolah->nama_sekolah ?? $rep->sekolah->namasekolah ?? $rep->sekolah_nama ?? 'Sekolah';
                                $repProg = $rep->getEkstrakurikulerName() ?? $rep->kategori_pengajaran ?? 'Ekstrakurikuler';
                                $repSiswaHadir = $rep->jumlah_siswa_hadir ?? $rep->jumlah_hadir ?? 0;
                                $repSiswaTidakHadir = $rep->jumlah_siswa_tidak_hadir ?? $rep->jumlah_tidak_hadir ?? 0;
                            @endphp
                            <div class="col-12">
                                <div class="profile-report-card">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 fw-bold">
                                                Pertemuan Ke-{{ $rep->pertemuan_ke ?? ($rep->ekstrakurikulerSession?->nomor_pertemuan ?: '1') }}
                                            </span>
                                            <h6 class="fw-bold text-dark mb-0">{{ $repSchool }}</h6>
                                            <span class="text-muted small">&bull; {{ $repProg }}</span>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar-event me-1 text-primary"></i>
                                            {{ $rep->jadwal_mengajar ? $rep->jadwal_mengajar->translatedFormat('l, d M Y') : '-' }}
                                            @if($rep->jam_mulai && $rep->jam_selesai)
                                                ({{ \Carbon\Carbon::parse($rep->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($rep->jam_selesai)->format('H:i') }} WIB)
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row g-2 align-items-center mt-2">
                                        <div class="col-md-7">
                                            <div class="p-2.5 rounded-3 bg-light bg-opacity-75 small border border-light-subtle">
                                                <div class="text-muted fw-semibold mb-1"><i class="bi bi-book me-1 text-primary"></i>Materi Pembelajaran:</div>
                                                <div class="text-dark fw-medium text-truncate-2">{{ $rep->materi_pengajaran ?: 'Belum ada catatan materi.' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 d-flex flex-column align-items-md-end gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1">
                                                    <i class="bi bi-check-circle me-1"></i>{{ $repSiswaHadir }} Hadir
                                                </span>
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2.5 py-1">
                                                    <i class="bi bi-x-circle me-1"></i>{{ $repSiswaTidakHadir }} Tdk Hadir
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                @if(!empty(trim($rep->materi_pengajaran ?? '')))
                                                    <button type="button" class="btn btn-xs btn-light border rounded-pill px-2.5 py-1 fw-semibold btn-copy-wa shadow-xs" data-text="{{ e($repWaText) }}" title="Salin Teks Laporan">
                                                        <i class="bi bi-clipboard me-1 text-primary"></i> Salin
                                                    </button>
                                                    @if($repWaSent)
                                                        <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-1 fw-semibold disabled" disabled title="Laporan sudah dikirim pada {{ $repWaSentAt }}">
                                                            <i class="bi bi-check-all me-1"></i> Terkirim ({{ $repWaSentAt }})
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-xs btn-success rounded-pill px-2.5 py-1 fw-bold btn-send-wa shadow-xs" data-url="{{ route('laporan-mengajar.send-wa-report', $rep) }}" data-instructor="{{ $user->nama_lengkap }}" data-phone="{{ $user->no_telephone }}" title="Kirim ke WA Saya">
                                                            <i class="bi bi-whatsapp me-1"></i> Kirim WA
                                                        </button>
                                                    @endif
                                                @endif
                                                <a href="{{ route('laporan-mengajar.show', $rep) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold" title="Lihat Detail Laporan">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <div class="stat-icon-circle mx-auto bg-primary bg-opacity-10 text-primary" style="width: 64px; height: 64px; font-size: 1.75rem;">
                                <i class="bi bi-journal-x"></i>
                            </div>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Belum Ada Riwayat Laporan Mengajar</h6>
                        <p class="text-muted small mb-3">Laporan sesi mengajar yang telah Anda buat dan selesaikan akan tampil di sini.</p>
                        <a href="{{ route('laporan-mengajar.create') }}" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold">
                            <i class="bi bi-plus-circle me-1"></i> Buat Laporan Mengajar
                        </a>
                    </div>
                @endif
            </div>

            <!-- Tab 6: Ganti Password Keamanan -->
            <div class="tab-pane fade" id="security-pane" role="tabpanel" aria-labelledby="security-tab" tabindex="0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-bottom pb-3 mb-4">
                    <div>
                        <h5 class="text-primary fw-bold mb-1"><i class="bi bi-shield-lock-fill me-2"></i>Ganti Password Keamanan</h5>
                        <p class="text-muted small mb-0">Pastikan akun Anda menggunakan password yang kuat dan aman untuk melindungi privasi data Anda.</p>
                    </div>
                </div>
                <div class="col-lg-8 mx-auto py-2">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabEl = document.querySelectorAll('#instructorProfileTabs button');
        const submitContainer = document.getElementById('submitProfileBtnContainer');

        tabEl.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(event) {
                const targetId = event.target.id;
                if (targetId === 'security-tab' || targetId === 'reports-tab') {
                    if (submitContainer) submitContainer.classList.add('d-none');
                } else {
                    if (submitContainer) submitContainer.classList.remove('d-none');
                }
            });
        });

        // Auto-switch to tab on URL parameter (?tab=bank) or hash or validation errors or password update status
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'bank' || urlParams.get('tab') === 'docs' || window.location.hash === '#docs-pane') {
            const docsTabBtn = document.getElementById('docs-tab');
            if (docsTabBtn) {
                bootstrap.Tab.getOrCreateInstance(docsTabBtn).show();
            }
        } else if (@json($errors->updatePassword->any()) || @json(session('status') === 'password-updated')) {
            const secTabBtn = document.getElementById('security-tab');
            if (secTabBtn) {
                bootstrap.Tab.getOrCreateInstance(secTabBtn).show();
            }
        } else {
            const firstInvalidInput = document.querySelector('#instructorProfileForm .is-invalid');
            if (firstInvalidInput) {
                const parentPane = firstInvalidInput.closest('.tab-pane');
                if (parentPane && parentPane.id) {
                    const correspondingTab = document.querySelector(`[data-bs-target="#${parentPane.id}"]`);
                    if (correspondingTab) {
                        bootstrap.Tab.getOrCreateInstance(correspondingTab).show();
                    }
                }
            }
        }

        // 📋 Handle Salin Teks
        document.querySelectorAll('.btn-copy-wa').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const text = this.getAttribute('data-text');
                if (!text) return;

                const showSuccess = () => {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Teks Berhasil Disalin!',
                            text: 'Format laporan yang rapi dan sopan telah disalin ke clipboard.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Teks laporan berhasil disalin!');
                    }
                };

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(showSuccess).catch(() => fallbackCopy(text, showSuccess));
                } else {
                    fallbackCopy(text, showSuccess);
                }
            });
        });

        function fallbackCopy(text, callback) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                if (callback) callback();
            } catch (err) {
                prompt('Salin teks laporan di bawah ini secara manual:', text);
            }
            document.body.removeChild(textArea);
        }

        // 📲 Handle Kirim ke WA Saya
        document.querySelectorAll('.btn-send-wa').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                const instructor = this.getAttribute('data-instructor') || 'Instruktur';
                const phone = this.getAttribute('data-phone') || '';

                if (!phone) {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nomor WA Belum Terdaftar',
                            text: 'Nomor WhatsApp instruktur belum terdaftar pada profil akun.',
                        });
                    } else {
                        alert('Nomor WhatsApp instruktur belum terdaftar pada profil.');
                    }
                    return;
                }

                const executeSend = () => {
                    const originalHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(({ status, body }) => {
                        if (status >= 200 && status < 300 && body.success) {
                            btn.outerHTML = `<button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-1 fw-semibold disabled" disabled title="Laporan sudah dikirim pada ${body.sent_at || 'Baru saja'}"><i class="bi bi-check-all me-1"></i> Terkirim (${body.sent_at || 'Baru saja'})</button>`;
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Terkirim!',
                                    text: body.message || 'Laporan beserta foto kegiatan telah dikirim ke WhatsApp Anda.',
                                });
                            } else {
                                alert(body.message || 'Laporan berhasil dikirim ke WhatsApp!');
                            }
                        } else {
                            btn.disabled = false;
                            btn.innerHTML = originalHtml;
                            const errMsg = (body && body.message) ? body.message : 'Terjadi kesalahan saat mengirim laporan.';
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Pengiriman Gagal',
                                    text: errMsg,
                                });
                            } else {
                                alert(errMsg);
                            }
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gangguan Jaringan',
                                text: 'Gagal menghubungi server. Silakan coba sesaat lagi.',
                            });
                        } else {
                            alert('Gagal menghubungi server.');
                        }
                    });
                };

                if (window.Swal) {
                    Swal.fire({
                        title: 'Kirim Laporan ke WhatsApp?',
                        html: `Laporan akan dikirimkan ke nomor WhatsApp <b>${phone}</b> (${instructor}) beserta foto kegiatan.<br><div class="alert alert-warning py-1 px-2 mt-2 mb-0 small"><i class="bi bi-exclamation-triangle me-1"></i> Pengiriman ini dibatasi <b>1 kali</b> per sesi laporan selesai.</div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-whatsapp me-1"></i> Ya, Kirim Sekarang',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            executeSend();
                        }
                    });
                } else {
                    if (confirm(`Kirim laporan ke nomor WhatsApp ${phone} (${instructor})? (Batas 1x kirim)`)) {
                        executeSend();
                    }
                }
            });
        });

        // Avatar Upload & Live Preview Logic
        const fotoProfilInput = document.getElementById('foto_profil');
        const avatarImagePreview = document.getElementById('avatarImagePreview');
        const avatarInitialsFallback = document.getElementById('avatarInitialsFallback');
        const btnRemoveAvatar = document.getElementById('btnRemoveAvatar');
        const removeFotoProfilInput = document.getElementById('remove_foto_profil');
        const selectedFileName = document.getElementById('selectedFileName');

        if (fotoProfilInput) {
            fotoProfilInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/jpg', 'image/webp'].includes(file.type)) {
                        alert('Format file tidak didukung. Harap pilih file JPG, PNG, atau WEBP.');
                        fotoProfilInput.value = '';
                        return;
                    }
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Ukuran file melebihi 5MB. Silakan pilih foto lain.');
                        fotoProfilInput.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (event) {
                        avatarImagePreview.src = event.target.result;
                        avatarImagePreview.classList.remove('d-none');
                        avatarInitialsFallback.classList.add('d-none');
                        btnRemoveAvatar.classList.remove('d-none');
                        removeFotoProfilInput.value = '0';
                        selectedFileName.textContent = file.name;
                        selectedFileName.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (btnRemoveAvatar) {
            btnRemoveAvatar.addEventListener('click', function () {
                if (confirm('Apakah Anda yakin ingin menghapus foto profil ini?')) {
                    fotoProfilInput.value = '';
                    avatarImagePreview.src = '';
                    avatarImagePreview.classList.add('d-none');
                    avatarInitialsFallback.classList.remove('d-none');
                    btnRemoveAvatar.classList.add('d-none');
                    removeFotoProfilInput.value = '1';
                    selectedFileName.textContent = '';
                    selectedFileName.classList.add('d-none');
                }
            });
        }
    });
</script>

<style>
    /* Custom Styles for Checkbox Table */
    .btn-check + .btn-outline-primary {
        background-color: #f8f9fa; /* Light Gray */
        border: 1px solid #dee2e6;
        color: #adb5bd;
    }
    .btn-check:checked + .btn-outline-primary {
        background-color: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
        opacity: 1;
    }
    .btn-check + .btn-outline-primary:hover {
        background-color: #e9ecef;
        border-color: var(--bs-primary);
    }

    /* Hide checkmark by default (unchecked) */
    .btn-check + .btn-outline-primary i {
        display: none;
    }
    /* Show checkmark when checked */
    .btn-check:checked + .btn-outline-primary i {
        display: inline-block;
    }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.02); }
</style>
