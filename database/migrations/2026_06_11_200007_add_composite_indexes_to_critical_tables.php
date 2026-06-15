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
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->index(['ekstrakurikuler_rombel_id', 'tanggal_terjadwal', 'status'], 'idx_rombel_date_status');
            $table->index(['tanggal_terjadwal', 'status'], 'idx_date_status');
        });

        Schema::table('absensi', function (Blueprint $table) {
            $table->index(['laporan_mengajar_id', 'siswa_id'], 'idx_laporan_siswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->dropIndex('idx_rombel_date_status');
            $table->dropIndex('idx_date_status');
        });

        Schema::table('absensi', function (Blueprint $table) {
            $table->dropIndex('idx_laporan_siswa');
        });
    }
};
