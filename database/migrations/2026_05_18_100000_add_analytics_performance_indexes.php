<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa_ekstrakurikuler', function (Blueprint $table) {
            $table->index(['status', 'tanggal_daftar', 'tanggal_keluar'], 'se_analytics_composite_idx');
        });
    }

    public function down(): void
    {
        Schema::table('siswa_ekstrakurikuler', function (Blueprint $table) {
            $table->dropIndex('se_analytics_composite_idx');
        });
    }
};
