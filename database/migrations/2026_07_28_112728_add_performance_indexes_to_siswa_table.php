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
        Schema::table('siswa', function (Blueprint $table) {
            $table->index(['sekolah_kodlan', 'nama_lengkap'], 'siswa_sekolah_nama_idx');
            $table->index(['sekolah_kodlan', 'nisn'], 'siswa_sekolah_nisn_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropIndex('siswa_sekolah_nama_idx');
            $table->dropIndex('siswa_sekolah_nisn_idx');
        });
    }
};
