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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'tanggal_aktif')) {
                $table->date('tanggal_aktif')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'tanggal_nonaktif')) {
                $table->date('tanggal_nonaktif')->nullable()->after('tanggal_aktif');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tanggal_aktif')) {
                $table->dropColumn('tanggal_aktif');
            }
            if (Schema::hasColumn('users', 'tanggal_nonaktif')) {
                $table->dropColumn('tanggal_nonaktif');
            }
        });
    }
};
