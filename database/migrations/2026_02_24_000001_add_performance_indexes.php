<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Performance indexes for frequently queried columns.
     * These indexes target the most common dashboard and list page queries.
     */
    public function up(): void
    {
        // laporan_mengajar — heavy dashboard queries
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            $table->index('jadwal_mengajar', 'laporan_jadwal_idx');
            $table->index(['user_id_instruktur', 'jadwal_mengajar'], 'laporan_instruktur_jadwal_idx');
        });

        // users — role + verification filters
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'verification_status'], 'users_role_status_idx');
        });

        // siswa — NISN search and temporary student lookup
        Schema::table('siswa', function (Blueprint $table) {
            $table->index('nisn', 'siswa_nisn_idx');
        });

        // ekstrakurikuler_session — status + date filtering
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->index(['status', 'tanggal_terjadwal'], 'session_status_date_idx');
            $table->index(['tanggal_pelaksanaan', 'user_id_instruktur'], 'session_pelaksanaan_instruktur_idx');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            $table->dropIndex('laporan_jadwal_idx');
            $table->dropIndex('laporan_instruktur_jadwal_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_status_idx');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropIndex('siswa_nisn_idx');
        });

        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->dropIndex('session_status_date_idx');
            $table->dropIndex('session_pelaksanaan_instruktur_idx');
        });
    }
};
