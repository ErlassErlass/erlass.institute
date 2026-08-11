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
            $table->decimal('checkin_lat', 10, 8)->nullable()->after('jam_selesai_aktual');
            $table->decimal('checkin_lng', 11, 8)->nullable()->after('checkin_lat');
            $table->integer('checkin_distance_meters')->nullable()->after('checkin_lng');
            $table->string('checkin_status_radius', 30)->nullable()->default('unverified')->after('checkin_distance_meters');
            $table->string('checkin_photo_path', 255)->nullable()->after('checkin_status_radius');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->dropColumn([
                'checkin_lat',
                'checkin_lng',
                'checkin_distance_meters',
                'checkin_status_radius',
                'checkin_photo_path'
            ]);
        });
    }
};
