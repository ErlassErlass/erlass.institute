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
            $table->json('metadata_json')->nullable()->after('pemahaman_materi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_mengajar', function (Blueprint $table) {
            $table->dropColumn('metadata_json');
        });
    }
};
