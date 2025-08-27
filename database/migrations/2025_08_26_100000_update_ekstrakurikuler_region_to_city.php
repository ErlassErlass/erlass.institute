<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            // Tambah kolom city baru
            $table->string('city')->nullable()->after('region');
        });

        // Migrasi data dari region ke city berdasarkan sekolah yang terpilih
        $ekstrakurikulerData = DB::table('ekstrakurikuler')
            ->join('sekolah', 'ekstrakurikuler.sekolah_kodlan', '=', 'sekolah.kodlan')
            ->select('ekstrakurikuler.id', 'sekolah.kotkab')
            ->get();

        foreach ($ekstrakurikulerData as $data) {
            DB::table('ekstrakurikuler')
                ->where('id', $data->id)
                ->update(['city' => $data->kotkab]);
        }

        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            // Tambah index untuk city
            $table->index('city');
        });
        
        // Drop region index secara manual jika ada
        try {
            DB::statement('ALTER TABLE ekstrakurikuler DROP INDEX ekstrakurikuler_region_index');
        } catch (Exception $e) {
            // Index tidak ada, skip
        }
    }

    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            // Hapus index city
            $table->dropIndex(['city']);
            
            // Tambah kembali index region
            $table->index('region');
            
            // Hapus kolom city
            $table->dropColumn('city');
        });
    }
};