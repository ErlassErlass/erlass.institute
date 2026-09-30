<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Menambah kolom skema_tagihan ke tabel sekolah.
     * Default: per_4_pertemuan (rolling batch)
     * 21 sekolah prioritas akan di-seed ke 'bulanan' via seeder.
     */
    public function up(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->enum('skema_tagihan', [
                'bulanan',
                'semester',
                'tahunan',
                'per_4_pertemuan',
            ])->default('per_4_pertemuan')->after('is_sekolah_bayar_instruktur');
        });

        // Seed 21 sekolah prioritas ke skema 'bulanan'
        $prioritasSekolah = [
            '10000044', // ERLASS POP
            '20103904', // SD SANTO YOSEPH
            '10001267', // SMPN 1 DENPASAR
            '10001266', // SMPN 2 DENPASAR
            '10001268', // SMPN 3 DENPASAR
            '20104002', // SMP HARAPAN BUNDA
            '20104065', // SMPK ST. YOSEPH
            '10001369', // SDN 1 PEGUYANGAN
            '10001367', // SDN 1 UBUNG
            '10001370', // SDN 2 PEGUYANGAN
            '10001366', // SDN 12 PADANGSAMBIAN
            '10001368', // SDN 3 PEGUYANGAN
            '10001364', // SDN 4 PADANGSAMBIAN
            '10001365', // SDN 5 PADANGSAMBIAN
            '10001363', // SDN 6 PADANGSAMBIAN
            '10001362', // SDN 7 PADANGSAMBIAN
            '20103905', // SDK ST. YOSEPH
            '20104001', // SDK ST. MARIA IMMACULATA
            '10001361', // SDN 1 PADANGSAMBIAN
            '10001360', // SDN 2 PADANGSAMBIAN
            '10001359', // SDN 3 PADANGSAMBIAN
        ];

        DB::table('sekolah')
            ->whereIn('kodlan', $prioritasSekolah)
            ->update(['skema_tagihan' => 'bulanan']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn('skema_tagihan');
        });
    }
};
