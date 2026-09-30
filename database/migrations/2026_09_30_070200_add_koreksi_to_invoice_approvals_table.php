<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom koreksi_siswa_billable dan koreksi_catatan ke invoice_approvals.
     * Memungkinkan admin mengoreksi hitungan siswa billable sistem jika ada selisih data.
     */
    public function up(): void
    {
        Schema::table('invoice_approvals', function (Blueprint $table) {
            // Nilai koreksi manual (jika diisi, ini yang dipakai di invoice PDF)
            // NULL = pakai nilai sistem (jumlah_siswa_billable)
            $table->unsignedSmallInteger('koreksi_siswa_billable')
                  ->nullable()
                  ->after('jumlah_siswa_billable')
                  ->comment('Override manual admin. NULL = pakai hitung sistem');

            // Alasan koreksi wajib jika koreksi_siswa_billable diisi
            $table->text('koreksi_catatan')
                  ->nullable()
                  ->after('koreksi_siswa_billable')
                  ->comment('Wajib diisi jika ada koreksi billable');

            // User yang melakukan koreksi
            $table->unsignedBigInteger('koreksi_by')
                  ->nullable()
                  ->after('koreksi_catatan');

            $table->timestamp('koreksi_at')
                  ->nullable()
                  ->after('koreksi_by');

            $table->foreign('koreksi_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_approvals', function (Blueprint $table) {
            $table->dropForeign(['koreksi_by']);
            $table->dropColumn([
                'koreksi_siswa_billable',
                'koreksi_catatan',
                'koreksi_by',
                'koreksi_at',
            ]);
        });
    }
};
