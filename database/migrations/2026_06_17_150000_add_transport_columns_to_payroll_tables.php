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
            $table->decimal('kustom_transport_fee', 10, 2)->nullable()->after('provinsi');
        });

        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->decimal('transport_fee', 10, 2)->default(0.00)->after('override_fee');
        });

        Schema::table('payroll_items', function (Blueprint $table) {
            $table->decimal('total_transport_fee', 10, 2)->default(0.00)->after('total_product_bonus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn('kustom_transport_fee');
        });

        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->dropColumn('transport_fee');
        });

        Schema::table('payroll_items', function (Blueprint $table) {
            $table->dropColumn('total_transport_fee');
        });
    }
};
