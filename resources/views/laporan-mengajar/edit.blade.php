@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Laporan Mengajar</h1>

    <form method="POST" action="{{ route('laporan-mengajar.update', $laporan) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Validation Errors -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Instruktur (Auto-filled, hidden) -->
        <input type="hidden" name="user_id_instruktur" value="{{ $laporan->user_id_instruktur }}">

        <!-- Assisten Instruktur -->
        <div class="mb-3">
            <label for="user_id_assisten" class="form-label">Assisten Instruktur</label>
            <select name="user_id_assisten" class="form-select">
                <option value="">Pilih Assisten</option>
                @foreach ($instructors as $id => $name)
                <option value="{{ $id }}"
                    {{ $laporan->user_id_assisten == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
                @endforeach
            </select>
            @error('user_id_assisten')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Provinsi -->
        <div class="mb-3">
            <label for="sekolah_provinsi" class="form-label">Provinsi</label>
            <select name="sekolah_provinsi" id="sekolah_provinsi" class="form-select" required>
                <option value="">Pilih Provinsi</option>
                @foreach ($provinsi as $prov)
                <option value="{{ $prov }}" {{ $laporan->sekolah_kota == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                @endforeach
            </select>
            @error('sekolah_provinsi')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Kota (City/Municipality) -->
        <div class="mb-3">
            <label for="sekolah_kota" class="form-label">Kota/Kabupaten</label>
            <select name="sekolah_kota" id="sekolah_kota" class="form-select" required>
                <option value="{{ $laporan->sekolah_kota }}" selected>{{ $laporan->sekolah_kota }}</option>
            </select>
            @error('sekolah_kota')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Kecamatan (District) -->
        <div class="mb-3">
            <label for="sekolah_kecamatan" class="form-label">Kecamatan</label>
            <select name="sekolah_kecamatan" id="sekolah_kecamatan" class="form-select" required>
                <option value="{{ $laporan->sekolah_kecamatan }}" selected>{{ $laporan->sekolah_kecamatan }}</option>
            </select>
            @error('sekolah_kecamatan')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Sekolah (School) -->
        <div class="mb-3">
            <label for="sekolah_nama" class="form-label">Nama Sekolah</label>
            <select name="sekolah_nama" id="sekolah_nama" class="form-select" required>
                <option value="{{ $laporan->sekolah_nama }}" selected>{{ $laporan->sekolah_nama }}</option>
            </select>
            @error('sekolah_nama')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Pertemuan Ke- -->
        <div class="mb-3">
            <label for="pertemuan_ke" class="form-label">Pertemuan Ke-</label>
            <input type="number" name="pertemuan_ke" class="form-control"
                value="{{ $laporan->pertemuan_ke }}" required>
            @error('pertemuan_ke')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Rombel -->
        <div class="mb-3">
            <label for="rombel" class="form-label">Rombel</label>
            <input type="text" name="rombel" class="form-control"
                value="{{ $laporan->rombel }}" required>
            @error('rombel')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Jadwal Mengajar -->
        <div class="mb-3">
            <label for="jadwal_mengajar" class="form-label">Jadwal Mengajar</label>
            <input type="date" name="jadwal_mengajar" class="form-control"
                value="{{ $laporan->jadwal_mengajar }}" required>
            @error('jadwal_mengajar')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Jam Mulai -->
        <div class="mb-3">
            <label for="jam_mulai" class="form-label">Jam Mulai</label>
            <input type="time" name="jam_mulai" class="form-control"
                value="{{ $laporan->jam_mulai }}" required>
            @error('jam_mulai')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Jam Selesai -->
        <div class="mb-3">
            <label for="jam_selesai" class="form-label">Jam Selesai</label>
            <input type="time" name="jam_selesai" class="form-control"
                value="{{ $laporan->jam_selesai }}" required>
            @error('jam_selesai')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Kategori Pengajaran -->
        <div class="mb-3">
            <label for="kategori_pengajaran" class="form-label">Kategori Pengajaran</label>
            <input type="text" name="kategori_pengajaran" class="form-control"
                value="{{ $laporan->kategori_pengajaran }}" required>
            @error('kategori_pengajaran')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Materi Pengajaran -->
        <div class="mb-3">
            <label for="materi_pengajaran" class="form-label">Materi Pengajaran</label>
            <textarea name="materi_pengajaran" class="form-control" required>{{ $laporan->materi_pengajaran }}</textarea>
            @error('materi_pengajaran')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Jumlah Siswa -->
        <div class="mb-3">
            <label for="jumlah_siswa_hadir" class="form-label">Jumlah Siswa Hadir</label>
            <input type="number" name="jumlah_siswa_hadir" class="form-control"
                value="{{ $laporan->jumlah_siswa_hadir }}" required>
            @error('jumlah_siswa_hadir')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="jumlah_siswa_keluar" class="form-label">Jumlah Siswa Keluar</label>
            <input type="number" name="jumlah_siswa_keluar" class="form-control"
                value="{{ $laporan->jumlah_siswa_keluar }}" required>
            @error('jumlah_siswa_keluar')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Foto Kegiatan -->
        <div class="mb-3">
            <label for="foto_kegiatan" class="form-label">Foto Kegiatan</label>
            @if ($laporan->foto_kegiatan)
            <img src="{{ asset('storage/' . $laporan->foto_kegiatan) }}"
                alt="Foto"
                class="img-fluid mb-2"
                style="max-width: 200px;">
            @endif
            <input type="file" name="foto_kegiatan" class="form-control" accept="image/*">
            @error('foto_kegiatan')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Refleksi -->
        <div class="mb-3">
            <label for="refleksi_siswa" class="form-label">Refleksi Siswa</label>
            <textarea name="refleksi_siswa" class="form-control" required>{{ $laporan->refleksi_siswa }}</textarea>
            @error('refleksi_siswa')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="refleksi_capaian" class="form-label">Refleksi Capaian</label>
            <textarea name="refleksi_capaian" class="form-control" required>{{ $laporan->refleksi_capaian }}</textarea>
            @error('refleksi_capaian')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Keaktifan -->
        <div class="mb-3">
            <label for="keaktifan" class="form-label">Keaktifan</label>
            <select name="keaktifan" class="form-select" required>
                <option value="{{ $laporan->keaktifan }}" selected>{{ ucwords(str_replace('_', ' ', $laporan->keaktifan)) }}</option>
                <option value="sangat_pasif">Sangat Pasif</option>
                <option value="pasif">Pasif</option>
                <option value="aktif">Aktif</option>
                <option value="sangat_aktif">Sangat Aktif</option>
            </select>
            @error('keaktifan')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Pemahaman -->
        <div class="mb-3">
            <label for="pemahaman_materi" class="form-label">Pemahaman Materi</label>
            <select name="pemahaman_materi" class="form-select" required>
                <option value="{{ $laporan->pemahaman_materi }}" selected>{{ ucwords(str_replace('_', ' ', $laporan->pemahaman_materi)) }}</option>
                <option value="belum_paham">Belum Paham</option>
                <option value="sedikit_paham">Sedikit Paham</option>
                <option value="paham">Paham</option>
                <option value="sangat_paham">Sangat Paham</option>
            </select>
            @error('pemahaman_materi')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Perbarui Laporan</button>
    </form>
</div>
<!-- JavaScript for Dependent Dropdowns -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Pre-select existing Provinsi and trigger Kota loading
        const initialProvinsi = $('#sekolah_provinsi').val();
        $('#sekolah_provinsi').val(initialProvinsi).trigger('change');

        // Pre-select existing Kota
        const initialKota = '{{ $laporan->sekolah_kota }}';
        const initialKecamatan = '{{ $laporan->sekolah_kecamatan }}';
        const initialSekolah = '{{ $laporan->sekolah_nama }}';

        // Populate Kota
        $('#sekolah_kota').val(initialKota);
        // Populate Kecamatan
        $('#sekolah_kecamatan').val(initialKecamatan).prop('disabled', false);
        // Populate Sekolah
        $('#sekolah_nama').val(initialSekolah).prop('disabled', false);

        // Provinsi -> Kota
        $('#sekolah_provinsi').change(function() {
            const provinsi = $(this).val();
            $('#sekolah_kota').empty().append('<option value="">Loading...</option>');
            $('#sekolah_kecamatan').empty().prop('disabled', true);
            $('#sekolah_nama').empty().prop('disabled', true);

            const citiesUrl = "{{ route('api.sekolah.kota', ['provinsi' => ':provinsi']) }}".replace(':provinsi', provinsi);
            $.get(citiesUrl, function(data) {
                const kotaSelect = $('#sekolah_kota');
                kotaSelect.empty();
                kotaSelect.append('<option value="">Pilih Kota/Kabupaten</option>');
                data.forEach(kota => {
                    kotaSelect.append(`<option value="${kota}">${kota}</option>`);
                });
                kotaSelect.val(initialKota).prop('disabled', false);
            });
        });

        // Kota -> Kecamatan
        $('#sekolah_kota').change(function() {
            const kota = $(this).val();
            $('#sekolah_kecamatan').empty().append('<option value="">Loading...</option>');
            $('#sekolah_nama').empty().prop('disabled', true);

            const kecamatanUrl = "{{ route('api.sekolah.kecamatan', ['kota' => ':kota']) }}".replace(':kota', kota);
            $.get(kecamatanUrl, function(data) {
                const kecamatanSelect = $('#sekolah_kecamatan');
                kecamatanSelect.empty();
                kecamatanSelect.append('<option value="">Pilih Kecamatan</option>');
                data.forEach(kecamatan => {
                    kecamatanSelect.append(`<option value="${kecamatan}">${kecamatan}</option>`);
                });
                kecamatanSelect.val(initialKecamatan).prop('disabled', false);
            });
        });

        // Kecamatan -> Sekolah
        $('#sekolah_kecamatan').change(function() {
            const kota = $('#sekolah_kota').val();
            const kecamatan = $(this).val();
            const sekolahUrl = "{{ route('api.sekolah.schools', ['kota' => ':kota', 'kecamatan' => ':kecamatan']) }}"
                .replace(':kota', kota)
                .replace(':kecamatan', kecamatan);
            $.get(sekolahUrl, function(data) {
                const sekolahSelect = $('#sekolah_nama');
                sekolahSelect.empty();
                sekolahSelect.append('<option value="">Pilih Sekolah</option>');
                data.forEach(namasekolah => {
                    sekolahSelect.append(`<option value="${namasekolah}">${namasekolah}</option>`);
                });
                sekolahSelect.val(initialSekolah).prop('disabled', false);
            });
        });

        // Initialize existing selections
        $('#sekolah_provinsi').val(initialProvinsi).trigger('change');
        $('#sekolah_kota').val(initialKota);
        $('#sekolah_kecamatan').val(initialKecamatan);
        $('#sekolah_nama').val(initialSekolah);
    });
</script>
@endsection