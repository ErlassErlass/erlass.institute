<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_batch_id')->constrained('payroll_batches')->onDelete('cascade');
            $table->foreignId('user_id_instruktur')->constrained('users')->onDelete('cascade');
            $table->integer('total_sessions')->default(0);
            $table->decimal('total_base_fee', 10, 2)->default(0.00);
            $table->decimal('total_product_bonus', 10, 2)->default(0.00);
            $table->decimal('total_penalty', 10, 2)->default(0.00);
            $table->decimal('total_bonus', 10, 2)->default(0.00);
            $table->decimal('net_salary', 10, 2)->default(0.00);
            $table->string('status')->default('pending'); // pending, approved, paid
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
