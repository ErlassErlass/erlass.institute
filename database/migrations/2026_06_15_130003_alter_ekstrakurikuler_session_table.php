<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid'); // unpaid, processing, paid
            $table->foreignId('payroll_item_id')->nullable()->constrained('payroll_items')->onDelete('set null');
            $table->string('actual_checkin_status')->nullable(); // excellent, on_time, warning, penalty
            $table->decimal('actual_checkin_penalty', 10, 2)->default(0.00);
            $table->decimal('calculated_fee', 10, 2)->default(0.00); // base_rate + product_bonus
            $table->decimal('override_fee', 10, 2)->nullable(); // override target
        });
    }

    public function down(): void
    {
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->dropForeign(['payroll_item_id']);
            $table->dropColumn([
                'payment_status',
                'payroll_item_id',
                'actual_checkin_status',
                'actual_checkin_penalty',
                'calculated_fee',
                'override_fee',
            ]);
        });
    }
};
