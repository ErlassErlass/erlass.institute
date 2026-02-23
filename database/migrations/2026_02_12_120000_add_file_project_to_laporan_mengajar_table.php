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
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            $table->string('file_project')->nullable()->after('foto_absensi_siswa')
                  ->comment('Path to project file (e.g., .sb3)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            $table->dropColumn('file_project');
        });
    }
};
