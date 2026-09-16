<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->boolean('is_sekolah_bayar_instruktur')->default(false)->after('lokasi_default')->index();
        });

        // 21 Sekolah Mitra dengan skema "Sekolah Bayar Instruktur"
        $targetKodlans = [
            '10000044', // ERLASS POP
            '20100226', // SMP Strada Mardi Utama 1
            '20102428', // SMP Strada Marga Mulia
            '20103904', // SD SANTO YOSEPH
            '20104733', // SDS Bunda Mulia
            '20105100', // SDS Santo Petrus
            '20106318', // SDS Strada Wiyatasana
            '20107147', // SMP Santo Yoseph
            '20108806', // SMP Strada Pelita II
            '20108864', // SDS Santo Antonius I
            '20109176', // SDS Putra I
            '20109198', // SDS Strada Dipamarga
            '20109264', // SDS Strada Van Lith II
            '20223654', // SD STRADA NAWAR
            '20231628', // SD STRADA CAKUNG
            '20607010', // SD STRADA SLAMET RIYADI 01
            '20607290', // SD STRADA SANTA MARIA
            '20615954', // SMPIT LATANSA CENDEKIA
            '69754486', // SDS AR RIDHO TANGERANG
            '69760682', // SDIT DARUL MAARIF ISLAMIC SCHOOL
            '69786993', // SDIT DAUROH
        ];

        DB::table('sekolah')
            ->whereIn('kodlan', $targetKodlans)
            ->update(['is_sekolah_bayar_instruktur' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn('is_sekolah_bayar_instruktur');
        });
    }
};
