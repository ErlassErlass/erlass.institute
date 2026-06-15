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
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->enum('warning_type', [
                'no_instructor', 
                'not_confirmed', 
                'missing_report', 
                'low_attendance', 
                'reschedule_limit', 
                'behind_target'
            ]);
            
            // Polymorphic relation fields
            $table->string('sourceable_type');
            $table->unsignedBigInteger('sourceable_id');
            $table->index(['sourceable_type', 'sourceable_id']);
            
            $table->enum('severity', ['yellow', 'red']);
            $table->enum('status', ['active', 'resolved', 'ignored'])->default('active');
            
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('resolved_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warnings');
    }
};
