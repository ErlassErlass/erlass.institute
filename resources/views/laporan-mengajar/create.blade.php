@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Buat Laporan Mengajar</h1>

    <form method="POST" action="{{ route('laporan-mengajar.store') }}">
        @csrf

        <!-- Instruktur -->
        <div class="mb-3">
            <label for="user_id_instruktur" class="form-label">Instruktur</label>
            <select name="user_id_instruktur" class="form-select" required>
                <option value="">Pilih Instruktur</option>
                @foreach (\App\Models\User::where('role', 'instruktur')->get() as $instruktur)
                <option value="{{ $instruktur->id }}">{{ $instruktur->nama_lengkap }}</option>
                @endforeach
            </select>
        </div>

        <!-- Provinsi -->
        <div class="mb-3">
            <label for="sekolah_provinsi" class="form-label">Provinsi</label>
            <select name="sekolah_provinsi" id="sekolah_provinsi" class="form-select" required>
                <option value="">Pilih Provinsi</option>
                @foreach ($provinsi as $prov)
                <option value="{{ $prov }}">{{ $prov }}</option>
                @endforeach
            </select>
        </div>

        <!-- Kota (City/Municipality) -->
        <div class="mb-3">
            <label for="sekolah_kota" class="form-label">Kota/Kabupaten</label>
            <select name="sekolah_kota" id="sekolah_kota" class="form-select" required>
                <option value="">Pilih Kota/Kabupaten</option>
            </select>
        </div>

        <!-- Kecamatan (District) -->
        <div class="mb-3">
            <label for="sekolah_kecamatan" class="form-label">Kecamatan</label>
            <select name="sekolah_kecamatan" id="sekolah_kecamatan" class="form-select" disabled required>
                <option value="">Pilih Kecamatan</option>
            </select>
        </div>

        <!-- Sekolah (School) -->
        <div class="mb-3">
            <label for="sekolah_nama" class="form-label">Nama Sekolah</label>
            <select name="sekolah_nama" id="sekolah_nama" class="form-select" disabled required>
                <option value="">Pilih Sekolah</option>
            </select>
        </div>

        <!-- Other Fields -->
        <div class="mb-3">
            <label for="pertemuan_ke" class="form-label">Pertemuan Ke-</label>
            <input type="number" name="pertemuan_ke" class="form-control" required>
        </div>

        <!-- Rombel -->
        <div class="mb-3">
            <label for="rombel" class="form-label">Rombel</label>
            <input type="text" name="rombel" class="form-control" required>
        </div>

        <!-- Jadwal Mengajar -->
        <div class="mb-3">
            <label for="jadwal_mengajar" class="form-label">Jadwal Mengajar</label>
            <input type="date" name="jadwal_mengajar" class="form-control" required>
        </div>

        <!-- Jam Mulai -->
        <div class="mb-3">
            <label for="jam_mulai" class="form-label">Jam Mulai</label>
            <input type="time" name="jam_mulai" class="form-control" required>
        </div>

        <!-- Jam Selesai -->
        <div class="mb-3">
            <label for="jam_selesai" class="form-label">Jam Selesai</label>
            <input type="time" name="jam_selesai" class="form-control" required>
        </div>

        <!-- Kategori Pengajaran -->
        <div class="mb-3">
            <label for="kategori_pengajaran" class="form-label">Kategori Pengajaran</label>
            <input type="text" name="kategori_pengajaran" class="form-control" required>
        </div>

        <!-- Materi Pengajaran -->
        <div class="mb-3">
            <label for="materi_pengajaran" class="form-label">Materi Pengajaran</label>
            <textarea name="materi_pengajaran" class="form-control" required></textarea>
        </div>

        <!-- Jumlah Siswa -->
        <div class="mb-3">
            <label for="jumlah_siswa_hadir" class="form-label">Jumlah Siswa Hadir</label>
            <input type="number" name="jumlah_siswa_hadir" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="jumlah_siswa_keluar" class="form-label">Jumlah Siswa Keluar</label>
            <input type="number" name="jumlah_siswa_keluar" class="form-control" required>
        </div>

        <!-- Refleksi -->
        <div class="mb-3">
            <label for="refleksi_siswa" class="form-label">Refleksi Siswa</label>
            <textarea name="refleksi_siswa" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="refleksi_capaian" class="form-label">Refleksi Capaian</label>
            <textarea name="refleksi_capaian" class="form-control" required></textarea>
        </div>

        <!-- Keaktifan -->
        <div class="mb-3">
            <label for="keaktifan" class="form-label">Keaktifan</label>
            <select name="keaktifan" class="form-select" required>
                <option value="sangat_pasif">Sangat Pasif</option>
                <option value="pasif">Pasif</option>
                <option value="aktif">Aktif</option>
                <option value="sangat_aktif">Sangat Aktif</option>
            </select>
        </div>

        <!-- Pemahaman -->
        <div class="mb-3">
            <label for="pemahaman_materi" class="form-label">Pemahaman Materi</label>
            <select name="pemahaman_materi" class="form-select" required>
                <option value="belum_paham">Belum Paham</option>
                <option value="sedikit_paham">Sedikit Paham</option>
                <option value="paham">Paham</option>
                <option value="sangat_paham">Sangat Paham</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>

    <!-- JavaScript for Dependent Dropdowns -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Provinsi -> Kota
            $('#sekolah_provinsi').change(function() {
                const provinsi = $(this).val();
                const kotaSelect = $('#sekolah_kota');
                const kecamatanSelect = $('#sekolah_kecamatan');
                const sekolahSelect = $('#sekolah_nama');

                // Clear previous data
                kotaSelect.empty().append('<option value="">Loading...</option>');
                kecamatanSelect.empty().prop('disabled', true);
                sekolahSelect.empty().prop('disabled', true);

                // Fetch cities for the selected province
                const citiesUrl = "{{ route('api.sekolah.kota', ['provinsi' => ':provinsi']) }}".replace(':provinsi', provinsi);
                $.get(citiesUrl, function(data) {
                    console.log('Cities:', data); // Check data structure here
                    if (Array.isArray(data) && data.length > 0) {
                        kotaSelect.empty();
                        kotaSelect.append('<option value="">Pilih Kota/Kabupaten</option>');
                        data.forEach(kota => {
                            kotaSelect.append(`<option value="${kota}">${kota}</option>`);
                        });
                        kotaSelect.prop('disabled', false);
                    } else {
                        kotaSelect.empty().append('<option value="">Tidak ada kota</option>');
                        kotaSelect.prop('disabled', false);
                    }
                }).fail(function() {
                    console.error('Failed to load cities:', arguments);
                    kotaSelect.empty().append('<option value="">Gagal memuat</option>');
                    kotaSelect.prop('disabled', false);
                });
            });

            // Kota -> Kecamatan
            $('#sekolah_kota').change(function() {
                const kota = $(this).val();
                const kecamatanSelect = $('#sekolah_kecamatan');
                const sekolahSelect = $('#sekolah_nama');

                // Clear previous data
                kecamatanSelect.empty().append('<option value="">Loading...</option>');
                sekolahSelect.empty().prop('disabled', true);

                // Fetch districts for the selected city
                const kecamatanUrl = "{{ route('api.sekolah.kecamatan', ['kota' => ':kota']) }}".replace(':kota', kota);
                $.get(kecamatanUrl, function(data) {
                    console.log('Districts:', data); // Check data structure here
                    if (Array.isArray(data) && data.length > 0) {
                        kecamatanSelect.empty();
                        kecamatanSelect.append('<option value="">Pilih Kecamatan</option>');
                        data.forEach(kecamatan => {
                            kecamatanSelect.append(`<option value="${kecamatan}">${kecamatan}</option>`);
                        });
                        kecamatanSelect.prop('disabled', false);
                    } else {
                        kecamatanSelect.empty().append('<option value="">Tidak ada kecamatan</option>');
                        kecamatanSelect.prop('disabled', false);
                    }
                });
            });

            // Kecamatan -> Sekolah
            $('#sekolah_kecamatan').change(function() {
                const kota = $('#sekolah_kota').val();
                const kecamatan = $(this).val();
                const sekolahSelect = $('#sekolah_nama');

                // Clear previous data
                sekolahSelect.empty().prop('disabled', true);

                // Fetch schools for the selected city and district
                const sekolahUrl = "{{ route('api.sekolah.schools', ['kota' => ':kota', 'kecamatan' => ':kecamatan']) }}"
                    .replace(':kota', kota)
                    .replace(':kecamatan', kecamatan);
                $.get(sekolahUrl, function(data) {
                    console.log('Schools:', data); // Check data structure here
                    if (Array.isArray(data) && data.length > 0) {
                        sekolahSelect.empty();
                        sekolahSelect.append('<option value="">Pilih Sekolah</option>');
                        data.forEach(namasekolah => {
                            sekolahSelect.append(`<option value="${namasekolah}">${namasekolah}</option>`);
                        });
                        sekolahSelect.prop('disabled', false);
                    } else {
                        sekolahSelect.empty().append('<option value="">Tidak ada sekolah</option>');
                        sekolahSelect.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</div>
@endsection