@extends('layouts.guest')
@section('title', 'Registrasi Instruktur')
@section('content')
<div class="container py-3 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="mb-3">
                <a href="{{ url('/') }}" class="text-decoration-none text-muted small hover-scale d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left-circle me-2 fs-5"></i> Kembali ke Beranda
                </a>
            </div>
            <div class="card glass-card border-0">
                <div class="text-center py-4 border-bottom border-light">
                    <h3 class="mb-1 fw-bold text-gradient-primary">Pendaftaran Instruktur</h3>
                    <p class="mb-0 text-muted">Bergabunglah bersama kami sebagai pengajar profesional.</p>
                </div>
                <div class="card-body p-3 p-md-5">
                    <form method="POST" action="{{ route('instructor.register.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Akun & Kontak Dasar -->
                        <div class="mb-5">
                            <h5 class="text-info border-bottom border-secondary pb-2 mb-4 d-flex align-items-center">
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
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                        <input class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" type="password" name="password" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                                        <input class="form-control border-start-0 ps-0" type="password" name="password_confirmation" required />
                                    </div>
                                </div>
                            </div>
                        </div>

                         <!-- Detail Identitas -->
                         <div class="mb-5">
                             <h5 class="text-info border-bottom border-secondary pb-2 mb-4 d-flex align-items-center">
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
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small text-muted">Gelar Belakang</label>
                                    <input class="form-control" type="text" name="gelar_belakang" value="{{ old('gelar_belakang') }}" placeholder="S.Pd" />
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Nama Panggilan <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="nama_panggilan" value="{{ old('nama_panggilan') }}" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">NIK (16 Digit) <span class="text-danger">*</span></label>
                                    <input class="form-control font-monospace" type="text" name="nik" value="{{ old('nik') }}" required minlength="16" maxlength="16" />
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input class="form-control" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Agama <span class="text-danger">*</span></label>
                                    <select class="form-select" name="agama" required>
                                        <option value="">Pilih Agama</option>
                                        <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                        <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                        <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                        <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Status Pernikahan <span class="text-danger">*</span></label>
                                    <select class="form-select" name="status_pernikahan" required>
                                        <option value="Lajang" {{ old('status_pernikahan') == 'Lajang' ? 'selected' : '' }}>Lajang</option>
                                        <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                        <option value="Duda/Janda" {{ old('status_pernikahan') == 'Duda/Janda' ? 'selected' : '' }}>Duda/Janda</option>
                                    </select>
                                </div>
                             </div>
                         </div>
                         
                         <!-- Kontak dan Domisili -->
                         <div class="mb-5">
                             <h5 class="text-info border-bottom border-secondary pb-2 mb-4 d-flex align-items-center">
                                 <i class="bi bi-geo-alt me-2"></i> Kontak & Domisili
                             </h5>
                             <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small text-muted">Alamat Domisili <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="alamat_domisili" rows="2" required placeholder="Alamat lengkap sesuai tempat tinggal saat ini">{{ old('alamat_domisili') }}</textarea>
                                 </div>
                                 <div class="col-md-6">
                                    <label class="form-label small text-muted">Kota Domisili <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="kota_domisili" value="{{ old('kota_domisili') }}" required placeholder="Jakarta Selatan" />
                                 </div>
                                 <div class="col-md-6">
                                    <label class="form-label small text-muted">No HP Darurat (Keluarga) <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="no_hp_2" value="{{ old('no_hp_2') }}" required />
                                 </div>
                             </div>
                         </div>
                         
                         <!-- Pendidikan & Profesi -->
                         <div class="mb-5">
                             <h5 class="text-info border-bottom border-secondary pb-2 mb-4 d-flex align-items-center">
                                 <i class="bi bi-mortarboard me-2"></i> Pendidikan & Pekerjaan
                             </h5>
                             <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                    <select class="form-select" name="pend_terakhir" required>
                                        <option value="">Pilih Pendidikan</option>
                                        <option value="SMA/SMK Sederajat">SMA/SMK Sederajat</option>
                                        <option value="D3">D3</option>
                                        <option value="D4/S1">D4/S1</option>
                                        <option value="S2">S2</option>
                                    </select>
                                </div>
                                 <div class="col-md-6">
                                    <label class="form-label small text-muted">Univ & Jurusan <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="universitas_jurusan" value="{{ old('universitas_jurusan') }}" required placeholder="Contoh: UNJ - Pendidikan Matematika" />
                                 </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Pekerjaan Terakhir <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="pekerjaan_terakhir" value="{{ old('pekerjaan_terakhir') }}" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Jenjang Mengajar <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="jenjang_mengajar" value="{{ old('jenjang_mengajar') }}" required placeholder="TK, SD, SMP, SMA" />
                                    <div class="form-text small text-muted">Bila guru/pengajar, isi strip jika selain guru</div>
                                </div>
                             </div>
                         </div>

                         <!-- Kesehatan & Fisik -->
                         <div class="mb-5">
                             <h5 class="text-info border-bottom border-secondary pb-2 mb-4 d-flex align-items-center">
                                 <i class="bi bi-heart-pulse me-2"></i> Kesehatan & Logistik
                             </h5>
                             <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Tinggi Badan (cm) <span class="text-danger">*</span></label>
                                    <input class="form-control" type="number" name="tinggi_badan" value="{{ old('tinggi_badan') }}" required placeholder="165" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Berat Badan (kg) <span class="text-danger">*</span></label>
                                    <input class="form-control" type="number" name="berat_badan" value="{{ old('berat_badan') }}" required placeholder="55" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Mata Minus <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="mata_minus" value="{{ old('mata_minus') }}" required placeholder="Normal / -0.5" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Riwayat Penyakit</label>
                                    <input class="form-control" type="text" name="riwayat_penyakit" value="{{ old('riwayat_penyakit') }}" placeholder="Asma, Alergi, dll (Kosongkan jika sehat)" />
                                </div>
                                
                                <div class="col-md-12">
                                   <label class="form-label small text-muted d-block">Alat Mengajar yang Dimiliki <span class="text-danger">*</span></label>
                                   <div class="d-flex flex-wrap gap-3">
                                       <div class="form-check">
                                           <input class="form-check-input" type="checkbox" name="alat_mengajar[]" value="Laptop" id="alat_laptop">
                                           <label class="form-check-label" for="alat_laptop">Laptop</label>
                                       </div>
                                       <div class="form-check">
                                           <input class="form-check-input" type="checkbox" name="alat_mengajar[]" value="Handphone" id="alat_hp">
                                           <label class="form-check-label" for="alat_hp">Handphone</label>
                                       </div>
                                       <div class="form-check">
                                           <input class="form-check-input" type="checkbox" name="alat_mengajar[]" value="Tablet" id="alat_tablet">
                                           <label class="form-check-label" for="alat_tablet">Tablet</label>
                                       </div>
                                   </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label small text-muted">Catatan Alat</label>
                                    <input class="form-control" type="text" name="catatan_alat" value="{{ old('catatan_alat') }}" placeholder="Contoh: Laptop baterai bocor" />
                                </div>
                                
                                <div class="col-md-6">
                                   <label class="form-label small text-muted">Kendaraan <span class="text-danger">*</span></label>
                                   <select class="form-select" name="kendaraan" required>
                                       <option value="Pribadi">Pribadi</option>
                                       <option value="Umum">Umum</option>
                                       <option value="Antar Jemput">Antar Jemput</option>
                                   </select>
                                </div>
                                <div class="col-md-6">
                                   <label class="form-label small text-muted">Jenis Kendaraan <span class="text-danger">*</span></label>
                                   <input class="form-control" type="text" name="jenis_kendaraan" value="{{ old('jenis_kendaraan') }}" required placeholder="Motor / Kereta / Busway" />
                                </div>
                             </div>
                         </div>

                         <!-- Bank & Dokumen -->
                         <div class="mb-5">
                             <h5 class="text-info border-bottom border-secondary pb-2 mb-4 d-flex align-items-center">
                                 <i class="bi bi-wallet2 me-2"></i> Data Bank & Dokumen
                             </h5>
                             <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Nama Bank <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="nama_bank" value="{{ old('nama_bank') }}" required placeholder="BCA / Mandiri " />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">No Rekening <span class="text-danger">*</span></label>
                                    <input class="form-control font-monospace" type="text" name="no_rekening" value="{{ old('no_rekening') }}" required />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">NPWP (16 Digit)</label>
                                    <input class="form-control font-monospace" type="text" name="no_npwp" value="{{ old('no_npwp') }}" maxlength="16" />
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Upload KTP <span class="text-danger">*</span></label>
                                    <input class="form-control" type="file" name="foto_ktp" accept="image/*" required />
                                    <div class="form-text small">Format Image (JPG/PNG)</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Upload NPWP</label>
                                    <input class="form-control" type="file" name="foto_npwp" accept="image/*" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Upload CV <span class="text-danger">*</span></label>
                                    <input class="form-control" type="file" name="cv" accept=".pdf,.doc,.docx" required />
                                    <div class="form-text small">Format PDF/DOC</div>
                                </div>
                             </div>
                         </div>

                         <!-- Jadwal Mengajar -->
                         <div class="mb-4">
                             <h5 class="text-info border-bottom border-secondary pb-2 mb-4 d-flex align-items-center">
                                 <i class="bi bi-calendar-check me-2"></i> Ketersediaan Mengajar
                             </h5>
                             <div class="alert alert-soft-info d-flex align-items-center small">
                                 <i class="bi bi-info-circle me-2 fs-5"></i>
                                 Pilih jam-jam dimana Anda <strong>BERSEDIA</strong> mengajar.
                             </div>
                             <div class="table-responsive rounded-3 border border-light">
                                 <table class="table table-bordered table-modern mb-0 text-center table-hover" style="min-width: 600px;">
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
                                             <td class="fw-bold text-muted bg-light">{{ $hari }}</td>
                                             @foreach(['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'] as $jam)
                                             <td class="p-0 position-relative">
                                                 <input type="checkbox" name="waktu_mengajar[{{ $hari }}][]" value="{{ $jam }}" class="btn-check" id="check_{{ $hari }}_{{ str_replace(':', '-', $jam) }}" autocomplete="off">
                                                 <label class="btn btn-outline-primary border-0 w-100 h-100 rounded-0 d-flex align-items-center justify-content-center py-2" for="check_{{ $hari }}_{{ str_replace(':', '-', $jam) }}" style="min-height: 40px; min-width: 40px;">
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

                        <div class="mt-5 text-center">
                            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-pill shadow-lg hover-scale">
                                <i class="bi bi-send me-2"></i> Daftar Sebagai Instruktur
                            </button>
                            <div class="mt-4">
                                <a href="{{ route('login') }}" class="text-decoration-none text-muted">Sudah punya akun? <strong class="text-primary">Login Disini</strong></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
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
@endsection
