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
        Schema::table('sekolah', function (Blueprint $table) {
            if (!Schema::hasColumn('sekolah', 'jarak_km')) {
                $table->decimal('jarak_km', 8, 2)->nullable()->after('kustom_transport_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            if (Schema::hasColumn('sekolah', 'jarak_km')) {
                $table->dropColumn('jarak_km');
            }
        });
    }
};
