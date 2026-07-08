<div class="card glass-card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom">
        <ul class="nav nav-tabs card-header-tabs" id="instructorProfileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark" id="account-tab" data-bs-toggle="tab" data-bs-target="#account-pane" type="button" role="tab" aria-controls="account-pane" aria-selected="true">
                    <i class="bi bi-person-fill me-1"></i> Data Akun & Domisili
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs-pane" type="button" role="tab" aria-controls="docs-pane" aria-selected="false">
                    <i class="bi bi-card-text me-1"></i> Bank & Berkas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional-pane" type="button" role="tab" aria-controls="professional-pane" aria-selected="false">
                    <i class="bi bi-mortarboard-fill me-1"></i> Karir & Logistik
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule-pane" type="button" role="tab" aria-controls="schedule-pane" aria-selected="false">
                    <i class="bi bi-calendar-week-fill me-1"></i> Jadwal Mengajar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" type="button" role="tab" aria-controls="security-pane" aria-selected="false">
                    <i class="bi bi-shield-lock-fill me-1"></i> Ganti Password
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
                    <h5 class="text-primary fw-bold border-bottom pb-2 mb-4"><i class="bi bi-bank me-2"></i>Data Bank & Berkas</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="nik" class="form-label small text-muted text-uppercase fw-bold">NIK (KTP) <span class="text-danger">*</span></label>
                            <input type="text" id="nik" name="nik" class="form-control font-monospace @error('nik') is-invalid @enderror" value="{{ old('nik', $profile->nik ?? '') }}" required minlength="16" maxlength="16">
                            @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nama_bank" class="form-label small text-muted text-uppercase fw-bold">Nama Bank <span class="text-danger">*</span></label>
                            <input type="text" id="nama_bank" name="nama_bank" class="form-control @error('nama_bank') is-invalid @enderror" value="{{ old('nama_bank', $profile->nama_bank ?? '') }}" required placeholder="BCA / Mandiri / BNI">
                            @error('nama_bank') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="no_rekening" class="form-label small text-muted text-uppercase fw-bold">No. Rekening <span class="text-danger">*</span></label>
                            <input type="text" id="no_rekening" name="no_rekening" class="form-control font-monospace @error('no_rekening') is-invalid @enderror" value="{{ old('no_rekening', $profile->no_rekening ?? '') }}" required>
                            @error('no_rekening') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="no_npwp" class="form-label small text-muted text-uppercase fw-bold">NPWP (16 Digit)</label>
                            <input type="text" id="no_npwp" name="no_npwp" class="form-control font-monospace @error('no_npwp') is-invalid @enderror" value="{{ old('no_npwp', $profile->no_npwp ?? '') }}" maxlength="16">
                            @error('no_npwp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-arrow-up me-2"></i>Unggah Dokumen Verifikasi</h6>
                    
                    <div class="row g-4">
                        <!-- Foto KTP -->
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <label for="foto_ktp" class="form-label fw-bold small text-dark mb-1">Foto KTP <span class="text-danger">*</span></label>
                                    <input type="file" id="foto_ktp" name="foto_ktp" class="form-control @error('foto_ktp') is-invalid @enderror" accept="image/*">
                                    <div class="form-text small">Maks 5MB. Format JPG/PNG.</div>
                                    @error('foto_ktp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @if(isset($profile->foto_ktp) && $profile->foto_ktp)
                                    <div class="mt-3 pt-2 border-top">
                                        <a href="{{ Storage::url($profile->foto_ktp) }}" target="_blank" class="btn btn-xs btn-outline-success w-100 rounded-pill">
                                            <i class="bi bi-image me-1"></i> Lihat KTP Terunggah
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Foto NPWP -->
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <label for="foto_npwp" class="form-label fw-bold small text-dark mb-1">Foto NPWP</label>
                                    <input type="file" id="foto_npwp" name="foto_npwp" class="form-control @error('foto_npwp') is-invalid @enderror" accept="image/*">
                                    <div class="form-text small">Maks 5MB. Format JPG/PNG.</div>
                                    @error('foto_npwp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @if(isset($profile->foto_npwp) && $profile->foto_npwp)
                                    <div class="mt-3 pt-2 border-top">
                                        <a href="{{ Storage::url($profile->foto_npwp) }}" target="_blank" class="btn btn-xs btn-outline-success w-100 rounded-pill">
                                            <i class="bi bi-image me-1"></i> Lihat NPWP Terunggah
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- CV / Resume -->
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <label for="cv" class="form-label fw-bold small text-dark mb-1">CV / Resume <span class="text-danger">*</span></label>
                                    <input type="file" id="cv" name="cv" class="form-control @error('cv') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                    <div class="form-text small">Maks 5MB. Format PDF/DOCX.</div>
                                    @error('cv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @if(isset($profile->cv_link) && $profile->cv_link)
                                    <div class="mt-3 pt-2 border-top">
                                        <a href="{{ Storage::url($profile->cv_link) }}" target="_blank" class="btn btn-xs btn-outline-primary w-100 rounded-pill">
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
            </div>
            
            <div class="mt-5 text-center">
                <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-pill shadow-lg hover-scale">
                    <i class="bi bi-save me-2"></i> Simpan Pembaruan Profil
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tab 5: Security / Password Form (Separate Form) -->
<div class="tab-pane fade d-none" id="security-pane" role="tabpanel" aria-labelledby="security-tab" tabindex="0">
    <!-- Managed via JS tab-switching below to prevent nested form issues -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabEl = document.querySelectorAll('#instructorProfileTabs button');
        const securityPane = document.getElementById('security-pane');
        const securityFormCard = document.getElementById('securityFormCard');
        const instructorFormCard = document.getElementById('instructorProfileForm');

        tabEl.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(event) {
                if (event.target.id === 'security-tab') {
                    // Move security form to the active pane area
                    securityPane.classList.remove('d-none');
                    securityPane.appendChild(securityFormCard);
                    instructorFormCard.classList.add('d-none');
                } else {
                    // Restore main form
                    instructorFormCard.classList.remove('d-none');
                    securityPane.classList.add('d-none');
                    document.body.appendChild(securityFormCard); // move it outside form temporarily
                }
            });
        });
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
