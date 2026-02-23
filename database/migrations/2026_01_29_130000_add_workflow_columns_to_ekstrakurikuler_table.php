<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            // Approval fields
            $table->text('catatan_approval')->nullable();
            
            // Rejection fields
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('ditolak_oleh')->nullable()->constrained('users');
            $table->timestamp('tanggal_ditolak')->nullable();

            // Activation fields
            $table->foreignId('diaktifkan_oleh')->nullable()->constrained('users');
            $table->timestamp('tanggal_aktivasi')->nullable();

            // Completion fields
            $table->foreignId('diselesaikan_oleh')->nullable()->constrained('users');
            $table->timestamp('tanggal_selesai_aktual')->nullable();

            // Cancellation fields
            $table->text('alasan_pembatalan')->nullable();
            $table->foreignId('dibatalkan_oleh')->nullable()->constrained('users');
            $table->timestamp('tanggal_dibatalkan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->dropForeign(['ditolak_oleh']);
            $table->dropForeign(['diaktifkan_oleh']);
            $table->dropForeign(['diselesaikan_oleh']);
            $table->dropForeign(['dibatalkan_oleh']);

            $table->dropColumn([
                'catatan_approval',
                'alasan_penolakan',
                'ditolak_oleh',
                'tanggal_ditolak',
                'diaktifkan_oleh',
                'tanggal_aktivasi',
                'diselesaikan_oleh',
                'tanggal_selesai_aktual',
                'alasan_pembatalan',
                'dibatalkan_oleh',
                'tanggal_dibatalkan',
            ]);
        });
    }
};
