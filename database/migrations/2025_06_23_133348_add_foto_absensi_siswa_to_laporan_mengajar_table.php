<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            // Menambahkan kolom baru setelah kolom 'foto_kegiatan'
            // nullable() berarti kolom ini boleh kosong
            $table->string('foto_absensi_siswa')->nullable()->after('foto_kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            // Logika untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('foto_absensi_siswa');
        });
    }
};