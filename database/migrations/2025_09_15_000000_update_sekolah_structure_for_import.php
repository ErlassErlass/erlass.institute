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
            // Add alamat column
            if (!Schema::hasColumn('sekolah', 'alamat')) {
                $table->text('alamat')->nullable()->after('provinsi');
            }

            // Modify jenjang and status to string to accommodate CSV data
            // Note: DB::statement might be needed for enum conversion depending on DB driver, 
            // but Laravel's change() method often works if doctrine/dbal is installed. 
            // If not, we might need raw SQL. For now, try standard Laravel modification.
            // Using string is safer than enum for messy import data.
            $table->string('jenjang')->change();
            $table->string('status')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            if (Schema::hasColumn('sekolah', 'alamat')) {
                $table->dropColumn('alamat');
            }
            
            // Reverting enum is tricky without exact values, simplified rollback:
            // $table->enum('jenjang', ['SD', 'SMP'])->change();
            // $table->enum('status', ['Swasta', 'Negeri'])->change();
        });
    }
};
