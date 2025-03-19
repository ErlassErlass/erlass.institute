<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laporan_mengajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_instruktur')->constrained('users');
            $table->foreignId('user_id_assisten')->nullable()->constrained('users');
            $table->integer('pertemuan_ke');
            $table->string('rombel');
            $table->date('jadwal_mengajar');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('kategori_pengajaran');
            $table->text('materi_pengajaran');
            $table->string('sekolah_kota'); // Linked to sekolah.kotkab
            $table->string('sekolah_kecamatan'); // Linked to sekolah.kec
            $table->string('sekolah_nama'); // Linked to sekolah.namasekolah
            $table->integer('jumlah_siswa_hadir');
            $table->integer('jumlah_siswa_keluar');
            $table->string('foto_kegiatan')->nullable();
            $table->text('refleksi_siswa');
            $table->text('refleksi_capaian');
            $table->enum('keaktifan', ['sangat_pasif', 'pasif', 'aktif', 'sangat_aktif']);
            $table->enum('pemahaman_materi', ['belum_paham', 'sedikit_paham', 'paham', 'sangat_paham']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_mengajar');
    }
};
