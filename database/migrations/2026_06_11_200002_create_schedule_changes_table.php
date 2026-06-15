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
        Schema::create('schedule_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_session_id')->constrained('ekstrakurikuler_session')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            
            // Original schedule
            $table->date('original_date');
            $table->time('original_start_time');
            $table->time('original_end_time');
            
            // Proposed schedule
            $table->date('proposed_date');
            $table->time('proposed_start_time');
            $table->time('proposed_end_time');
            
            $table->text('reason');
            
            // Academic approval (Erlass)
            $table->foreignId('academic_approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('academic_approved_at')->nullable();
            
            // School PIC approval (Partner School)
            $table->foreignId('school_pic_approver_id')->nullable()->constrained('school_pics')->onDelete('set null');
            $table->dateTime('school_pic_approved_at')->nullable();
            
            $table->enum('status', ['pending', 'approved_academic', 'approved_pic', 'rejected', 'applied'])->default('pending');
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_changes');
    }
};
