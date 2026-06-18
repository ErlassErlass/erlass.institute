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
        Schema::table('siswa_ekstrakurikuler', function (Blueprint $table) {
            // Hapus unique constraint yang lama
            $table->dropUnique('unique_siswa_per_program');
            
            // Tambahkan unique constraint baru yang mencakup rombel
            $table->unique(['siswa_id', 'ekstrakurikuler_id', 'ekstrakurikuler_rombel_id'], 'unique_siswa_per_program_rombel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_ekstrakurikuler', function (Blueprint $table) {
            $table->dropUnique('unique_siswa_per_program_rombel');
            $table->unique(['siswa_id', 'ekstrakurikuler_id'], 'unique_siswa_per_program');
        });
    }
};
