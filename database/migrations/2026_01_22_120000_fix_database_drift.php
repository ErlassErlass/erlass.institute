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
        // Fix Users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
        });

        // Fix Laporan Mengajar table
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_mengajar', 'sekolah_nama')) {
                $table->string('sekolah_nama')->nullable()->after('materi_pengajaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });

        Schema::table('laporan_mengajar', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_mengajar', 'sekolah_nama')) {
                $table->dropColumn('sekolah_nama');
            }
        });
    }
};
