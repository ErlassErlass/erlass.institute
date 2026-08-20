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
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->decimal('checkin_accuracy_meters', 8, 2)->nullable()->after('checkin_distance_meters');
            $table->boolean('checkin_mock_suspected')->default(false)->after('checkin_accuracy_meters');
            $table->string('checkin_device_info', 255)->nullable()->after('checkin_mock_suspected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->dropColumn([
                'checkin_accuracy_meters',
                'checkin_mock_suspected',
                'checkin_device_info',
            ]);
        });
    }
};
