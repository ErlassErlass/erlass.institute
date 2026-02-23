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
            // Drop the city index first if it exists
            if (Schema::hasColumn('ekstrakurikuler', 'city')) {
                try {
                    $table->dropIndex(['city']); // Drop the index properly
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }
                
                // Remove redundant city field since we use sekolah.kotkab directly
                $table->dropColumn('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            // Add city field back
            $table->string('city')->nullable()->after('region');
            // Add index back
            $table->index('city');
        });
    }
};
