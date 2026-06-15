<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_rates', function (Blueprint $table) {
            $table->id();
            $table->string('level'); // junior, madya, senior, expert, master_trainer
            $table->decimal('base_rate', 10, 2);
            $table->string('product_category')->nullable(); // Scratch, Microbit, Robotik, Python, dll
            $table->decimal('product_bonus', 10, 2)->default(0.00);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_rates');
    }
};
