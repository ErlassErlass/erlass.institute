<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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
                    'diganti',
                    'overdue'
                ) NOT NULL DEFAULT 'terjadwal'
            ");
        }
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
                    'tidak_hadir',
                    'libur',
                    'diganti'
                ) NOT NULL DEFAULT 'terjadwal'
            ");
        }
    }
};
