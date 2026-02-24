<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add payment type and equipment fields to ekstrakurikuler table.
     * - jenis_pembayaran: payment model for the program
     * - jenis_alat: equipment type (for robotics/microbit programs)
     * - jumlah_siswa_per_alat: students per equipment unit (for group units)
     */
    public function up(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->enum('jenis_pembayaran', [
                'per_siswa_bulan',
                'per_siswa_semester',
                'per_siswa_tahun',
                'per_pertemuan_instruktur',
            ])->nullable()->after('deskripsi');

            $table->enum('jenis_alat', [
                'per_siswa',
                'per_kelompok',
            ])->nullable()->after('jenis_pembayaran');

            $table->tinyInteger('jumlah_siswa_per_alat')
                ->nullable()
                ->after('jenis_alat')
                ->comment('Jumlah siswa per alat (2-5), hanya jika jenis_alat = per_kelompok');
        });
    }

    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->dropColumn(['jenis_pembayaran', 'jenis_alat', 'jumlah_siswa_per_alat']);
        });
    }
};
