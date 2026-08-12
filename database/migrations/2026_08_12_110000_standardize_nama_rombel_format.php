<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("UPDATE ekstrakurikuler_rombel SET nama_rombel = CONCAT('Rombel ', nomor_rombel)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for data cleansing
    }
};
