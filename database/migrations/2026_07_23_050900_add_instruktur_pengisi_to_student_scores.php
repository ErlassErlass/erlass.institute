<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds instruktur_pengisi_id to student_scores to track WHICH instructor
     * last filled in the grades for each student record. This supports the
     * scenario where an instructor is replaced mid-semester.
     */
    public function up(): void
    {
        Schema::table('student_scores', function (Blueprint $table) {
            $table->unsignedBigInteger('instruktur_pengisi_id')
                ->nullable()
                ->after('updated_by')
                ->comment('ID instruktur yang terakhir mengisi nilai (bisa berbeda dari instruktur rombel aktif)');

            $table->foreign('instruktur_pengisi_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_scores', function (Blueprint $table) {
            $table->dropForeign(['instruktur_pengisi_id']);
            $table->dropColumn('instruktur_pengisi_id');
        });
    }
};
