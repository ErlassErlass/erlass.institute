<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            // Tambahkan kolom baru setelah 'jumlah_siswa_hadir'
            $table->integer('jumlah_siswa_tidak_hadir')->default(0)->after('jumlah_siswa_hadir');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            $table->dropColumn('jumlah_siswa_tidak_hadir');
        });
    }
};
