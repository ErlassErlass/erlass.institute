<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            // Hapus kolom-kolom lama yang tidak lagi kita perlukan
            if (Schema::hasColumn('laporan_mengajar', 'sekolah_nama')) {
                $table->dropColumn(['sekolah_nama', 'sekolah_kota', 'sekolah_kecamatan']);
            }

            // Tambahkan satu kolom foreign key yang benar
            // Pastikan kolom ini ditambahkan setelah kolom yang sudah ada, misal 'user_id_assisten'
            $table->string('sekolah_kodlan')->after('user_id_assisten')->nullable();
            
            // Buat relasi foreign key
            $table->foreign('sekolah_kodlan')->references('kodlan')->on('sekolah')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            // Logika untuk rollback jika migrasi dibatalkan
            $table->dropForeign(['sekolah_kodlan']);
            $table->dropColumn('sekolah_kodlan');
            
            $table->string('sekolah_nama');
            $table->string('sekolah_kota');
            $table->string('sekolah_kecamatan');
        });
    }
};