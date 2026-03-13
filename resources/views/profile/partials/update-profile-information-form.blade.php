<section>
    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-input-label for="nama_lengkap" :value="__('Full Name')" />
                <x-text-input id="nama_lengkap" name="nama_lengkap" type="text" class="form-control" :value="old('nama_lengkap', $user->nama_lengkap)" required autofocus />
                <x-input-error class="text-danger mt-1" :messages="$errors->get('nama_lengkap')" />
            </div>

            <div class="col-md-6 mb-3">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="form-control" :value="old('email', $user->email)" required />
                <x-input-error class="text-danger mt-1" :messages="$errors->get('email')" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-input-label for="tanggal_lahir" :value="__('Birth Date')" />
                <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="text" class="form-control datepicker" :value="old('tanggal_lahir', $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : '')" placeholder="DD-MM-YYYY" />
                <x-input-error class="text-danger mt-1" :messages="$errors->get('tanggal_lahir')" />
            </div>

            <div class="col-md-6 mb-3">
                <x-input-label for="no_telephone" :value="__('Phone Number')" />
                <x-text-input id="no_telephone" name="no_telephone" type="text" class="form-control" :value="old('no_telephone', $user->no_telephone)" />
                <x-input-error class="text-danger mt-1" :messages="$errors->get('no_telephone')" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-input-label for="agama" :value="__('Religion')" />
                <select id="agama" name="agama" class="form-select">
                    <option value="">{{ __('Select Religion') }}</option>
                    <option value="Islam" {{ old('agama', $user->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                    <option value="Kristen" {{ old('agama', $user->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                    <option value="Katolik" {{ old('agama', $user->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                    <option value="Hindu" {{ old('agama', $user->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                    <option value="Buddha" {{ old('agama', $user->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                    <option value="Lainnya" {{ old('agama', $user->agama) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                <x-input-error class="text-danger mt-1" :messages="$errors->get('agama')" />
            </div>

            <div class="col-md-6 mb-3">
                <x-input-label for="pend_terakhir" :value="__('Last Education')" />
                <select id="pend_terakhir" name="pend_terakhir" class="form-select">
                    <option value="">{{ __('Select Education Level') }}</option>
                    <option value="SMA/SMK Sederajat" {{ old('pend_terakhir', $user->pend_terakhir) == 'SMA/SMK Sederajat' ? 'selected' : '' }}>SMA/SMK Sederajat</option>
                    <option value="D3" {{ old('pend_terakhir', $user->pend_terakhir) == 'D3' ? 'selected' : '' }}>D3</option>
                    <option value="D4/S1" {{ old('pend_terakhir', $user->pend_terakhir) == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                    <option value="S2" {{ old('pend_terakhir', $user->pend_terakhir) == 'S2' ? 'selected' : '' }}>S2</option>
                    <option value="S3" {{ old('pend_terakhir', $user->pend_terakhir) == 'S3' ? 'selected' : '' }}>S3</option>
                </select>
                <x-input-error class="text-danger mt-1" :messages="$errors->get('pend_terakhir')" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-input-label for="kompetensi_1" :value="__('Primary Competency')" />
                <select id="kompetensi_1" name="kompetensi_1" class="form-select">
                    <option value="">{{ __('Select Competency') }}</option>
                    <option value="Coding" {{ old('kompetensi_1', $user->kompetensi_1) == 'Coding' ? 'selected' : '' }}>Coding</option>
                    <option value="Bahasa Inggris" {{ old('kompetensi_1', $user->kompetensi_1) == 'Bahasa Inggris' ? 'selected' : '' }}>Bahasa Inggris</option>
                    <option value="Robotik" {{ old('kompetensi_1', $user->kompetensi_1) == 'Robotik' ? 'selected' : '' }}>Robotik</option>
                    <option value="Desain" {{ old('kompetensi_1', $user->kompetensi_1) == 'Desain' ? 'selected' : '' }}>Desain</option>
                    <option value="IoT" {{ old('kompetensi_1', $user->kompetensi_1) == 'IoT' ? 'selected' : '' }}>IoT</option>
                    <option value="Data Science" {{ old('kompetensi_1', $user->kompetensi_1) == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                </select>
                <x-input-error class="text-danger mt-1" :messages="$errors->get('kompetensi_1')" />
            </div>

            <div class="col-md-6 mb-3">
                <x-input-label for="kompetensi_2" :value="__('Secondary Competency')" />
                <select id="kompetensi_2" name="kompetensi_2" class="form-select">
                    <option value="">{{ __('Select Competency (Optional)') }}</option>
                    <option value="Coding" {{ old('kompetensi_2', $user->kompetensi_2) == 'Coding' ? 'selected' : '' }}>Coding</option>
                    <option value="Bahasa Inggris" {{ old('kompetensi_2', $user->kompetensi_2) == 'Bahasa Inggris' ? 'selected' : '' }}>Bahasa Inggris</option>
                    <option value="Robotik" {{ old('kompetensi_2', $user->kompetensi_2) == 'Robotik' ? 'selected' : '' }}>Robotik</option>
                    <option value="Desain" {{ old('kompetensi_2', $user->kompetensi_2) == 'Desain' ? 'selected' : '' }}>Desain</option>
                    <option value="IoT" {{ old('kompetensi_2', $user->kompetensi_2) == 'IoT' ? 'selected' : '' }}>IoT</option>
                    <option value="Data Science" {{ old('kompetensi_2', $user->kompetensi_2) == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                </select>
                <x-input-error class="text-danger mt-1" :messages="$errors->get('kompetensi_2')" />
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <x-primary-button>
                <i class="bi bi-check-circle me-2"></i>{{ __('Update Profile') }}
            </x-primary-button>

            <div class="text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                Role: <strong>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</strong>
            </div>
        </div>
    </form>
</section>