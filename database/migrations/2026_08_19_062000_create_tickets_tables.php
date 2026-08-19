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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('kategori', ['jadwal_honor', 'keluhan_lain', 'teknis_error'])->default('keluhan_lain');
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('prioritas', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->unsignedBigInteger('ekstrakurikuler_session_id')->nullable();
            $table->string('foto_lampiran')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->boolean('has_unread_reply_for_user')->default(false);
            $table->boolean('has_unread_reply_for_admin')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ekstrakurikuler_session_id')
                ->references('id')
                ->on('ekstrakurikuler_session')
                ->nullOnDelete();

            $table->index(['user_id', 'status']);
            $table->index(['kategori', 'status']);
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('pesan');
            $table->string('lampiran')->nullable();
            $table->boolean('is_staff_reply')->default(false);
            $table->timestamps();

            $table->index('ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('tickets');
    }
};
