<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ALTER MODIFY COLUMN untuk tambah enum baru
        // SQLite: tidak perlu — SQLite tidak enforce ENUM, kolom TEXT sudah bisa menerima semua value
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `ekstrakurikuler_session`
                MODIFY COLUMN `status` ENUM(
                    'terjadwal',
                    'berlangsung',
                    'selesai',
                    'dibatalkan',
                    'ditunda',
                    'tidak_hadir',
                    'libur',
                    'diganti'
                ) NOT NULL DEFAULT 'terjadwal'
            ");
        }
        // SQLite: skip (tidak perlu ALTER ENUM, semua string diterima)
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `ekstrakurikuler_session`
                MODIFY COLUMN `status` ENUM(
                    'terjadwal',
                    'berlangsung',
                    'selesai',
                    'dibatalkan',
                    'ditunda',
                    'tidak_hadir'
                ) NOT NULL DEFAULT 'terjadwal'
            ");
        }
    }
};

