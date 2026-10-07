<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Kodlan 68 sekolah mitra penerima dana CSR Reguler SOGA (Solidaritas Erlangga).
     */
    protected array $csrSekolahKodlans = [
        '60706196', '60706512', '20103445', '20606729', '20604279', '20604361', '20104842', '20228621',
        '20201314', '20100390', '20223543', '20105172', '20109049', '20607363', '20604198', '20104351',
        '20104155', '20228712', '20228720', '20108569', '20108570', '20108573', '20105887', '20223621',
        '20223609', '20222819', '20222815', '20604341', '20228759', '20105287', '20104586', '20108586',
        '20108587', '20108588', '20108597', '20108599', '20108600', '20228843', '20228845', '20104649',
        '20100477', '20104651', '20101424', '20105485', '20603124', '20104932', '20108782', '20103461',
        '20103460', '20103449', '20103448', '20100624', '69769405', '20105169', '20222824', '20222883',
        '20105455', '20104912', '69881551', '20107096', '20103524', '20103617', '20103615', '20109257',
        '20103611', '20103638', '20222981',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Perbarui enum skema_tagihan di 3 tabel (khusus MySQL)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `sekolah` MODIFY COLUMN `skema_tagihan` ENUM('bulanan','semester','tahunan','per_4_pertemuan','csr_reguler_soga') NOT NULL DEFAULT 'per_4_pertemuan'");
            if (\Illuminate\Support\Facades\Schema::hasColumn('ekstrakurikuler', 'skema_tagihan')) {
                DB::statement("ALTER TABLE `ekstrakurikuler` MODIFY COLUMN `skema_tagihan` ENUM('bulanan','semester','tahunan','per_4_pertemuan','csr_reguler_soga') NULL");
            }
            DB::statement("ALTER TABLE `invoice_approvals` MODIFY COLUMN `skema_tagihan` ENUM('bulanan','semester','tahunan','per_4_pertemuan','csr_reguler_soga') NOT NULL");
        }

        // 2. Set skema_tagihan sekolah penerima CSR SOGA
        if (\Illuminate\Support\Facades\Schema::hasColumn('sekolah', 'skema_tagihan')) {
            DB::table('sekolah')
                ->whereIn('kodlan', $this->csrSekolahKodlans)
                ->update(['skema_tagihan' => 'csr_reguler_soga']);
        }

        // 3. Sinkronkan ke ekstrakurikuler di bawah sekolah tersebut jika kolomnya ada
        if (\Illuminate\Support\Facades\Schema::hasColumn('ekstrakurikuler', 'skema_tagihan')) {
            DB::table('ekstrakurikuler')
                ->whereIn('sekolah_kodlan', $this->csrSekolahKodlans)
                ->update(['skema_tagihan' => 'csr_reguler_soga']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan sekolah yang tadinya csr_reguler_soga ke per_4_pertemuan
        if (\Illuminate\Support\Facades\Schema::hasColumn('sekolah', 'skema_tagihan')) {
            DB::table('sekolah')
                ->where('skema_tagihan', 'csr_reguler_soga')
                ->update(['skema_tagihan' => 'per_4_pertemuan']);
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('ekstrakurikuler', 'skema_tagihan')) {
            DB::table('ekstrakurikuler')
                ->where('skema_tagihan', 'csr_reguler_soga')
                ->update(['skema_tagihan' => 'per_4_pertemuan']);
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('invoice_approvals', 'skema_tagihan')) {
            DB::table('invoice_approvals')
                ->where('skema_tagihan', 'csr_reguler_soga')
                ->update(['skema_tagihan' => 'per_4_pertemuan']);
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `sekolah` MODIFY COLUMN `skema_tagihan` ENUM('bulanan','semester','tahunan','per_4_pertemuan') NOT NULL DEFAULT 'per_4_pertemuan'");
            if (\Illuminate\Support\Facades\Schema::hasColumn('ekstrakurikuler', 'skema_tagihan')) {
                DB::statement("ALTER TABLE `ekstrakurikuler` MODIFY COLUMN `skema_tagihan` ENUM('bulanan','semester','tahunan','per_4_pertemuan') NULL");
            }
            DB::statement("ALTER TABLE `invoice_approvals` MODIFY COLUMN `skema_tagihan` ENUM('bulanan','semester','tahunan','per_4_pertemuan') NOT NULL");
        }
    }
};
