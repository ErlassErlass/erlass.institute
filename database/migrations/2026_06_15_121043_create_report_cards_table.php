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
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikuler')->onDelete('cascade');
            $table->foreignId('ekstrakurikuler_rombel_id')->constrained('ekstrakurikuler_rombel')->onDelete('cascade');
            $table->foreignId('student_score_id')->constrained('student_scores')->onDelete('cascade');
            $table->string('periode');
            $table->string('file_path')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['siswa_id', 'ekstrakurikuler_rombel_id', 'periode'], 'report_siswa_rombel_periode_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};
