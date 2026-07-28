<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the rombel_instructor_history table.
     * This table stores a complete audit trail of every instructor assignment
     * change for each rombel, enabling:
     * 1. Former instructors to retain READ-ONLY access to student scores
     * 2. Full traceability of "who taught which sessions"
     */
    public function up(): void
    {
        Schema::create('rombel_instructor_history', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('rombel_id')
                ->comment('ID rombel yang mengalami pergantian instruktur');
            $table->foreign('rombel_id')
                ->references('id')
                ->on('ekstrakurikuler_rombel')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('user_id_instruktur')
                ->comment('ID instruktur yang aktif pada rentang sesi ini');
            $table->foreign('user_id_instruktur')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('user_id_asisten')
                ->nullable()
                ->comment('ID asisten instruktur pada rentang sesi ini (opsional)');
            $table->foreign('user_id_asisten')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->unsignedInteger('berlaku_dari_sesi')
                ->default(1)
                ->comment('Nomor pertemuan pertama instruktur ini aktif di rombel');

            $table->unsignedInteger('berlaku_sampai_sesi')
                ->nullable()
                ->comment('Nomor pertemuan terakhir instruktur ini aktif. NULL = masih aktif saat ini');

            $table->text('alasan')
                ->nullable()
                ->comment('Alasan pergantian instruktur (opsional, diisi oleh admin)');

            $table->unsignedBigInteger('diganti_oleh')
                ->nullable()
                ->comment('ID admin/user yang melakukan pergantian instruktur ini');
            $table->foreign('diganti_oleh')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamps();

            // Index for fast lookup: "was this user ever an instructor of this rombel?"
            $table->index(['rombel_id', 'user_id_instruktur'], 'idx_rombel_instruktur_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombel_instructor_history');
    }
};
