@extends('layouts.app')

@section('title', 'Buat Laporan Mengajar')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h1 class="h4 mb-0">Formulir Laporan Mengajar</h1>
                </div>

                <form method="POST" action="{{ route('laporan-mengajar.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">Terdapat Kesalahan Validasi!</h5>
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <h5 class="mt-2 border-bottom pb-2 mb-3">Informasi Instruktur</h5>
                        <input type="hidden" name="user_id_instruktur" value="{{ Auth::id() }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_instruktur" class="form-label">Nama Instruktur</label>
                                <input type="text" id="nama_instruktur" class="form-control" value="{{ Auth::user()->nama_lengkap }}" disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="user_id_assisten" class="form-label">Asisten Instruktur (Opsional)</label>
                                <select name="user_id_assisten" id="user_id_assisten" class="form-select @error('user_id_assisten') is-invalid @enderror">
                                    <option value="">Pilih Asisten</option>
                                    @foreach ($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ old('user_id_assisten') == $instructor->id ? 'selected' : '' }}>{{ $instructor->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                                @error('user_id_assisten') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <h5 class="mt-4 border-bottom pb-2 mb-3">Lokasi Mengajar</h5>
                        <div class="mb-3">
                            <label for="sekolah_kodlan" class="form-label">Cari & Pilih Sekolah</label>
                            {{-- ✅ DIBUAT KOSONG, akan diisi oleh AJAX --}}
                            <select name="sekolah_kodlan" id="sekolah-search" class="form-select @error('sekolah_kodlan') is-invalid @enderror" required>
                                {{-- Jika ada data lama (karena validation error), kita tampilkan di sini --}}
                                @if(old('sekolah_kodlan'))
                                <option value="{{ old('sekolah_kodlan') }}" selected="selected">{{ old('sekolah_nama_display') }}</option>
                                @endif
                            </select>
                            @error('sekolah_kodlan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            {{-- Kita butuh input hidden untuk menyimpan nama sekolah agar bisa ditampilkan lagi jika ada error validasi --}}
                            <input type="hidden" name="sekolah_nama_display" id="sekolah_nama_display" value="{{ old('sekolah_nama_display') }}">
                        </div>

                        <h5 class="mt-4 border-bottom pb-2 mb-3">Detail Pengajaran</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pertemuan_ke" class="form-label">Pertemuan Ke-</label>
                                <input type="number" name="pertemuan_ke" id="pertemuan_ke" class="form-control @error('pertemuan_ke') is-invalid @enderror" value="{{ old('pertemuan_ke') }}" required>
                                @error('pertemuan_ke') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rombel" class="form-label">Rombongan Belajar (Rombel)</label>
                                <input type="text" name="rombel" id="rombel" class="form-control @error('rombel') is-invalid @enderror" value="{{ old('rombel') }}" required>
                                @error('rombel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jadwal_mengajar" class="form-label">Jadwal Mengajar</label>
                                <input type="text" name="jadwal_mengajar" id="jadwal_mengajar" class="form-control @error('jadwal_mengajar') is-invalid @enderror" value="{{ old('jadwal_mengajar') }}" required placeholder="dd/mm/yyyy">
                                @error('jadwal_mengajar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                <input type="text" name="jam_mulai" id="jam_mulai" class="form-control time-picker @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai') }}" required placeholder="HH:MM">
                                @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                <input type="text" name="jam_selesai" id="jam_selesai" class="form-control time-picker @error('jam_selesai') is-invalid @enderror" value="{{ old('jam_selesai') }}" required placeholder="HH:MM">
                                @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="materi_pengajaran" class="form-label">Materi Pengajaran</label>
                                <textarea name="materi_pengajaran" id="materi_pengajaran" class="form-control @error('materi_pengajaran') is-invalid @enderror" required>{{ old('materi_pengajaran') }}</textarea>
                                @error('materi_pengajaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h5 class="mt-4 border-bottom pb-2 mb-3">Refleksi & Evaluasi</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="keaktifan" class="form-label">Keaktifan Siswa</label>
                                <select name="keaktifan" id="keaktifan" class="form-select @error('keaktifan') is-invalid @enderror" required>
                                    <option value="sangat_pasif" {{ old('keaktifan') == 'sangat_pasif' ? 'selected' : '' }}>Sangat Pasif</option>
                                    <option value="pasif" {{ old('keaktifan') == 'pasif' ? 'selected' : '' }}>Pasif</option>
                                    <option value="aktif" {{ old('keaktifan') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="sangat_aktif" {{ old('keaktifan') == 'sangat_aktif' ? 'selected' : '' }}>Sangat Aktif</option>
                                </select>
                                @error('keaktifan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pemahaman_materi" class="form-label">Pemahaman Materi Siswa</label>
                                <select name="pemahaman_materi" id="pemahaman_materi" class="form-select @error('pemahaman_materi') is-invalid @enderror" required>
                                    <option value="belum_paham" {{ old('pemahaman_materi') == 'belum_paham' ? 'selected' : '' }}>Belum Paham</option>
                                    <option value="sedikit_paham" {{ old('pemahaman_materi') == 'sedikit_paham' ? 'selected' : '' }}>Sedikit Paham</option>
                                    <option value="paham" {{ old('pemahaman_materi') == 'paham' ? 'selected' : '' }}>Paham</option>
                                    <option value="sangat_paham" {{ old('pemahaman_materi') == 'sangat_paham' ? 'selected' : '' }}>Sangat Paham</option>
                                </select>
                                @error('pemahaman_materi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="refleksi_siswa" class="form-label">Refleksi Siswa</label>
                                <textarea name="refleksi_siswa" id="refleksi_siswa" class="form-control @error('refleksi_siswa') is-invalid @enderror" required>{{ old('refleksi_siswa') }}</textarea>
                                @error('refleksi_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="refleksi_capaian" class="form-label">Refleksi Capaian</label>
                                <textarea name="refleksi_capaian" id="refleksi_capaian" class="form-control @error('refleksi_capaian') is-invalid @enderror" required>{{ old('refleksi_capaian') }}</textarea>
                                @error('refleksi_capaian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h5 class="mt-4 border-bottom pb-2 mb-3">Dokumentasi</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="foto_kegiatan" class="form-label">Foto Kegiatan</label>
                                <input type="file" name="foto_kegiatan" id="foto_kegiatan" class="form-control @error('foto_kegiatan') is-invalid @enderror" accept="image/*">
                                @error('foto_kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- ✅ FIELD BARU DITAMBAHKAN DI SINI --}}
                            <div class="col-md-6 mb-3">
                                <label for="foto_absensi_siswa" class="form-label">Foto Absensi Siswa</label>
                                <input type="file" name="foto_absensi_siswa" id="foto_absensi_siswa" class="form-control @error('foto_absensi_siswa') is-invalid @enderror" accept="image/*">
                                @error('foto_absensi_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Laporan
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const sekolahSearch = $('#sekolah-search');

        sekolahSearch.select2({
            theme: "bootstrap-5",
            placeholder: 'Ketik nama atau kode sekolah...',
            // Konfigurasi AJAX
            ajax: {
                url: "{{ url('api/sekolah/search') }}",
                dataType: 'json',
                delay: 250, // Tunggu 250ms setelah user berhenti mengetik
                data: function(params) {
                    return {
                        q: params.term // Kirim teks pencarian sebagai parameter 'q'
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results // Data harus dalam format { results: [ {id: '', text: ''} ] }
                    };
                },
                cache: true
            }
        });

        // Simpan teks dari sekolah yang dipilih ke input hidden
        sekolahSearch.on('select2:select', function(e) {
            var data = e.params.data;
            $('#sekolah_nama_display').val(data.text);
        });

        // Inisialisasi Flatpickr (tetap sama)
        flatpickr("#jadwal_mengajar", {
            dateFormat: "d/m/Y",
            allowInput: true
        });
        flatpickr(".time-picker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            allowInput: true
        });
    });
</script>
@endpush