<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambah kolom is_manual_reschedule untuk menandai sesi yang tanggalnya
     * sudah diubah secara manual oleh admin/instruktur, sehingga Sync Sesi
     * tidak akan menghapus/mengacak sesi tersebut.
     */
    public function up(): void
    {
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->boolean('is_manual_reschedule')->default(false)->after('tanggal_pengganti')
                ->comment('TRUE jika tanggal sesi ini sudah diubah secara manual (protected dari auto-sync)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->dropColumn('is_manual_reschedule');
        });
    }
};
