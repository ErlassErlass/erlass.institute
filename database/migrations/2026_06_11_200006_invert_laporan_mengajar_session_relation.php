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
        // 1. Add session FK to laporan_mengajar
        if (Schema::hasTable('laporan_mengajar') && !Schema::hasColumn('laporan_mengajar', 'ekstrakurikuler_session_id')) {
            Schema::table('laporan_mengajar', function (Blueprint $table) {
                $table->foreignId('ekstrakurikuler_session_id')->nullable()->after('user_id_instruktur')
                    ->constrained('ekstrakurikuler_session')->onDelete('set null');
            });
        }

        // 2. Migrate relation data from sessions to reports
        if (Schema::hasTable('ekstrakurikuler_session') && Schema::hasColumn('ekstrakurikuler_session', 'laporan_mengajar_id')) {
            $sessions = DB::table('ekstrakurikuler_session')
                ->whereNotNull('laporan_mengajar_id')
                ->get();

            foreach ($sessions as $session) {
                DB::table('laporan_mengajar')
                    ->where('id', $session->laporan_mengajar_id)
                    ->update(['ekstrakurikuler_session_id' => $session->id]);
            }

            // 3. Drop column from session table
            Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
                $table->dropForeign(['laporan_mengajar_id']);
                $table->dropIndex('ekskul_session_laporan_idx');
                $table->dropColumn('laporan_mengajar_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add column back to session table
        if (Schema::hasTable('ekstrakurikuler_session') && !Schema::hasColumn('ekstrakurikuler_session', 'laporan_mengajar_id')) {
            Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
                $table->foreignId('laporan_mengajar_id')->nullable()->after('jam_selesai_terjadwal')
                    ->constrained('laporan_mengajar')->onDelete('set null');
                $table->index('laporan_mengajar_id', 'ekskul_session_laporan_idx');
            });
        }

        // 2. Rollback data from reports to sessions
        if (Schema::hasTable('laporan_mengajar') && Schema::hasColumn('laporan_mengajar', 'ekstrakurikuler_session_id')) {
            $reports = DB::table('laporan_mengajar')
                ->whereNotNull('ekstrakurikuler_session_id')
                ->get();

            foreach ($reports as $report) {
                DB::table('ekstrakurikuler_session')
                    ->where('id', $report->ekstrakurikuler_session_id)
                    ->update(['laporan_mengajar_id' => $report->id]);
            }

            // 3. Drop column from laporan_mengajar
            Schema::table('laporan_mengajar', function (Blueprint $table) {
                $table->dropForeign(['ekstrakurikuler_session_id']);
                $table->dropColumn('ekstrakurikuler_session_id');
            });
        }
    }
};
