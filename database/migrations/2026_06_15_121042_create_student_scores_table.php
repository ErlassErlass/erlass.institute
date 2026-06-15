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
        Schema::create('student_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikuler')->onDelete('cascade');
            $table->foreignId('ekstrakurikuler_rombel_id')->constrained('ekstrakurikuler_rombel')->onDelete('cascade');
            
            // Sub-scores for 4x inputs
            $table->decimal('nilai_tugas_1', 5, 2)->nullable();
            $table->decimal('nilai_tugas_2', 5, 2)->nullable();
            $table->decimal('nilai_tugas_3', 5, 2)->nullable();
            $table->decimal('nilai_tugas_4', 5, 2)->nullable();

            $table->decimal('nilai_sikap_1', 5, 2)->nullable();
            $table->decimal('nilai_sikap_2', 5, 2)->nullable();
            $table->decimal('nilai_sikap_3', 5, 2)->nullable();
            $table->decimal('nilai_sikap_4', 5, 2)->nullable();

            $table->decimal('nilai_proyek_1', 5, 2)->nullable();
            $table->decimal('nilai_proyek_2', 5, 2)->nullable();
            $table->decimal('nilai_proyek_3', 5, 2)->nullable();
            $table->decimal('nilai_proyek_4', 5, 2)->nullable();

            // Averages and overall scores
            $table->decimal('nilai_kehadiran', 5, 2)->default(0);
            $table->decimal('nilai_tugas', 5, 2)->default(0);
            $table->decimal('nilai_proyek', 5, 2)->default(0);
            $table->decimal('nilai_sikap', 5, 2)->default(0);
            $table->decimal('nilai_akhir', 5, 2)->default(0);

            $table->text('catatan_guru')->nullable();
            $table->string('projek_scratch')->nullable();
            $table->string('periode')->default('Semester 1 2026');
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['siswa_id', 'ekstrakurikuler_rombel_id', 'periode'], 'scores_siswa_rombel_periode_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_scores');
    }
};
