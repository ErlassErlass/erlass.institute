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
        Schema::table('invoice_approvals', function (Blueprint $table) {
            $table->string('bukti_chat_path')->nullable()->after('operasional_checklist');
            $table->json('siswa_gratis_list')->nullable()->after('bukti_chat_path');
            $table->unsignedInteger('jumlah_siswa_gratis')->default(0)->after('siswa_gratis_list');
            $table->string('pic_konfirmasi_jabatan', 150)->nullable()->after('pic_konfirmasi_nama');
            $table->dateTime('pic_konfirmasi_tgl')->nullable()->after('pic_konfirmasi_jabatan');
            $table->json('sesi_verifikasi_data')->nullable()->after('pic_konfirmasi_tgl');
            $table->dateTime('serah_terima_akunting_at')->nullable()->after('akunting_catatan');
            $table->string('serah_terima_akunting_penerima', 150)->nullable()->after('serah_terima_akunting_at');
            $table->text('serah_terima_akunting_catatan')->nullable()->after('serah_terima_akunting_penerima');
        });

        Schema::table('invoice_approval_items', function (Blueprint $table) {
            $table->json('siswa_gratis_list')->nullable()->after('koreksi_at');
            $table->unsignedInteger('jumlah_siswa_gratis')->default(0)->after('siswa_gratis_list');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_approvals', function (Blueprint $table) {
            $table->dropColumn([
                'bukti_chat_path',
                'siswa_gratis_list',
                'jumlah_siswa_gratis',
                'pic_konfirmasi_jabatan',
                'pic_konfirmasi_tgl',
                'sesi_verifikasi_data',
                'serah_terima_akunting_at',
                'serah_terima_akunting_penerima',
                'serah_terima_akunting_catatan',
            ]);
        });

        Schema::table('invoice_approval_items', function (Blueprint $table) {
            $table->dropColumn([
                'siswa_gratis_list',
                'jumlah_siswa_gratis',
            ]);
        });
    }
};
