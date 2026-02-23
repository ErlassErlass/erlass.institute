<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            // Check if 'kelas' exists and 'rombel' does not, then rename.
            // This undoes the 2026_02_03_150000 rename if it was applied.
            if (Schema::hasColumn('siswa', 'kelas') && !Schema::hasColumn('siswa', 'rombel')) {
                $table->renameColumn('kelas', 'rombel');
            }
            
            // Re-add 'kelas' as a separate column for master data
            if (!Schema::hasColumn('siswa', 'kelas')) {
                $table->string('kelas')->nullable()->after('rombel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            // We usually don't want to lose data on down, but to reverse:
            // If we have both, we might want to drop one or rename.
            // But for safety in this specific fix:
            if (Schema::hasColumn('siswa', 'kelas') && Schema::hasColumn('siswa', 'rombel')) {
                $table->dropColumn('kelas');
            }
        });
    }
};
