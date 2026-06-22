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
        Schema::table('student_scores', function (Blueprint $table) {
            $table->decimal('nilai_tugas_5', 5, 2)->nullable()->after('nilai_tugas_4');
            $table->decimal('nilai_tugas_6', 5, 2)->nullable()->after('nilai_tugas_5');
            $table->decimal('nilai_tugas_7', 5, 2)->nullable()->after('nilai_tugas_6');
            $table->decimal('nilai_tugas_8', 5, 2)->nullable()->after('nilai_tugas_7');

            $table->decimal('nilai_sikap_5', 5, 2)->nullable()->after('nilai_sikap_4');
            $table->decimal('nilai_sikap_6', 5, 2)->nullable()->after('nilai_sikap_5');
            $table->decimal('nilai_sikap_7', 5, 2)->nullable()->after('nilai_sikap_6');
            $table->decimal('nilai_sikap_8', 5, 2)->nullable()->after('nilai_sikap_7');

            $table->decimal('nilai_proyek_5', 5, 2)->nullable()->after('nilai_proyek_4');
            $table->decimal('nilai_proyek_6', 5, 2)->nullable()->after('nilai_proyek_5');
            $table->decimal('nilai_proyek_7', 5, 2)->nullable()->after('nilai_proyek_6');
            $table->decimal('nilai_proyek_8', 5, 2)->nullable()->after('nilai_proyek_7');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_scores', function (Blueprint $table) {
            $table->dropColumn([
                'nilai_tugas_5', 'nilai_tugas_6', 'nilai_tugas_7', 'nilai_tugas_8',
                'nilai_sikap_5', 'nilai_sikap_6', 'nilai_sikap_7', 'nilai_sikap_8',
                'nilai_proyek_5', 'nilai_proyek_6', 'nilai_proyek_7', 'nilai_proyek_8'
            ]);
        });
    }
};
