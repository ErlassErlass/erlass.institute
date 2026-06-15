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
        if (Schema::hasTable('absensi') && Schema::hasColumn('absensi', 'hadir')) {
            // 1. Add status enum column
            Schema::table('absensi', function (Blueprint $table) {
                $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('alpha')->after('siswa_id');
            });

            // 2. Migrate existing boolean data to status enum
            DB::table('absensi')->where('hadir', 1)->update(['status' => 'hadir']);
            DB::table('absensi')->where('hadir', 0)->update(['status' => 'alpha']);

            // 3. Drop boolean hadir column
            Schema::table('absensi', function (Blueprint $table) {
                $table->dropColumn('hadir');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('absensi') && Schema::hasColumn('absensi', 'status')) {
            // 1. Add boolean hadir column
            Schema::table('absensi', function (Blueprint $table) {
                $table->boolean('hadir')->default(false)->after('status');
            });

            // 2. Rollback data from status enum to boolean
            DB::table('absensi')->where('status', 'hadir')->update(['hadir' => true]);
            DB::table('absensi')->whereIn('status', ['izin', 'sakit', 'alpha'])->update(['hadir' => false]);

            // 3. Drop status enum column
            Schema::table('absensi', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
