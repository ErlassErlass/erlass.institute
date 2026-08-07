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
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            if (!Schema::hasColumn('ekstrakurikuler', 'keterangan_internet')) {
                $table->text('keterangan_internet')->nullable()->after('koneksi_internet');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            if (Schema::hasColumn('ekstrakurikuler', 'keterangan_internet')) {
                $table->dropColumn('keterangan_internet');
            }
        });
    }
};
