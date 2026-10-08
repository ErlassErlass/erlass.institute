<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah skema default sekolah ke 'bulanan' dan memigrasikan sekolah
     * yang masih menggunakan per_4_pertemuan (kecuali SDS Sang Timur) ke 'bulanan'.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `sekolah` MODIFY COLUMN `skema_tagihan` ENUM('bulanan','semester','tahunan','per_4_pertemuan','csr_reguler_soga') NOT NULL DEFAULT 'bulanan'");
        }

        // Update semua sekolah yang masih per_4_pertemuan kecuali SDS SANG TIMUR [20105755]
        if (Schema::hasColumn('sekolah', 'skema_tagihan')) {
            DB::table('sekolah')
                ->where('skema_tagihan', 'per_4_pertemuan')
                ->where('kodlan', '!=', '20105755')
                ->update(['skema_tagihan' => 'bulanan']);
        }

        // Update ekstrakurikuler yang override per_4_pertemuan (kecuali SDS Sang Timur) agar mewarisi skema bulanan sekolah
        if (Schema::hasColumn('ekstrakurikuler', 'skema_tagihan')) {
            DB::table('ekstrakurikuler')
                ->where('skema_tagihan', 'per_4_pertemuan')
                ->where('sekolah_kodlan', '!=', '20105755')
                ->update(['skema_tagihan' => null]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `sekolah` MODIFY COLUMN `skema_tagihan` ENUM('bulanan','semester','tahunan','per_4_pertemuan','csr_reguler_soga') NOT NULL DEFAULT 'per_4_pertemuan'");
        }
    }
};
