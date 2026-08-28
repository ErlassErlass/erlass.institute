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
        if (!Schema::hasColumn('payroll_items', 'total_sessions_utama')) {
            Schema::table('payroll_items', function (Blueprint $table) {
                $table->integer('total_sessions_utama')->default(0)->after('total_sessions');
                $table->integer('total_sessions_asisten')->default(0)->after('total_sessions_utama');
                $table->decimal('total_asisten_fee', 10, 2)->default(0.00)->after('total_base_fee');
                $table->decimal('total_gross_salary', 10, 2)->default(0.00)->after('total_transport_fee');
                $table->decimal('tax_rate', 5, 2)->default(2.50)->after('total_gross_salary');
                $table->decimal('tax_amount', 10, 2)->default(0.00)->after('tax_rate');
            });
        }

        if (!Schema::hasTable('payroll_item_session')) {
            Schema::create('payroll_item_session', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_item_id')->constrained('payroll_items')->onDelete('cascade');
                $table->foreignId('ekstrakurikuler_session_id')->constrained('ekstrakurikuler_session')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('role')->default('utama'); // utama, asisten
                $table->decimal('base_fee', 10, 2)->default(0.00);
                $table->decimal('transport_fee', 10, 2)->default(0.00);
                $table->decimal('penalty_fee', 10, 2)->default(0.00);
                $table->decimal('bonus_fee', 10, 2)->default(0.00);
                $table->decimal('net_fee', 10, 2)->default(0.00);
                $table->decimal('override_fee', 10, 2)->nullable();
                $table->timestamps();

                $table->index(['payroll_item_id', 'ekstrakurikuler_session_id'], 'pis_item_session_idx');
                $table->index(['user_id', 'role'], 'pis_user_role_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_item_session');

        if (Schema::hasColumn('payroll_items', 'total_sessions_utama')) {
            Schema::table('payroll_items', function (Blueprint $table) {
                $table->dropColumn([
                    'total_sessions_utama',
                    'total_sessions_asisten',
                    'total_asisten_fee',
                    'total_gross_salary',
                    'tax_rate',
                    'tax_amount',
                ]);
            });
        }
    }
};

