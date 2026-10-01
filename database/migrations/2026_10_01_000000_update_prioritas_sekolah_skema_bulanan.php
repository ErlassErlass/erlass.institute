<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengklasifikasikan 21 sekolah prioritas ke skema tagihan 'bulanan'
     * dan mereset sekolah Denpasar sebelumnya ke 'per_4_pertemuan'.
     */
    public function up(): void
    {
        // 1. Reset sekolah placeholder Denpasar kembali ke per_4_pertemuan
        $denpasar = [
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
            ->whereIn('kodlan', $denpasar)
            ->update(['skema_tagihan' => 'per_4_pertemuan']);

        // 2. Klasifikasikan 21 sekolah resmi ke skema 'bulanan'
        $sekolahBulanan = [
            '10000044', // 1. ERLASS POP
            '20100226', // 2. SMP Strada Mardi Utama 1
            '20102428', // 3. SMP Strada Marga Mulia
            '20103904', // 4. SD SANTO YOSEPH
            '20104733', // 5. SDS Bunda Mulia
            '20105100', // 6. SDS Santo Petrus
            '20106318', // 7. SDS Strada Wiyatasana
            '20107147', // 8. SMP Santo Yoseph
            '20108806', // 9. SMP Strada Pelita II
            '20108864', // 10. SDS Santo Antonius I
            '20109176', // 11. SDS Putra I
            '20109198', // 12. SDS Strada Dipamarga
            '20109264', // 13. SDS Strada Van Lith II
            '20223654', // 14. SD STRADA NAWAR
            '20231628', // 15. SD STRADA CAKUNG
            '20607010', // 16. SD STRADA SLAMET RIYADI 01
            '20607290', // 17. SD STRADA SANTA MARIA
            '20615954', // 18. SMPIT LATANSA CENDEKIA
            '69754486', // 19. SDS AR RIDHO TANGERANG
            '69760682', // 20. SDIT DARUL MAARIF ISLAMIC SCHOOL
            '69786993', // 21. SDIT DAUROH
        ];

        DB::table('sekolah')
            ->whereIn('kodlan', $sekolahBulanan)
            ->update(['skema_tagihan' => 'bulanan']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sekolahBulanan = [
            '10000044', '20100226', '20102428', '20103904', '20104733',
            '20105100', '20106318', '20107147', '20108806', '20108864',
            '20109176', '20109198', '20109264', '20223654', '20231628',
            '20607010', '20607290', '20615954', '69754486', '69760682',
            '69786993',
        ];

        DB::table('sekolah')
            ->whereIn('kodlan', $sekolahBulanan)
            ->update(['skema_tagihan' => 'per_4_pertemuan']);
    }
};
