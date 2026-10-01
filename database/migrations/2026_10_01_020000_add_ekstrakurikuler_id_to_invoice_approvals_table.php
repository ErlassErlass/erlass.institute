<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan keterikatan eksplisit per program (ekstrakurikuler) pada invoice_approvals.
     */
    public function up(): void
    {
        Schema::table('invoice_approvals', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_approvals', 'ekstrakurikuler_id')) {
                $table->unsignedBigInteger('ekstrakurikuler_id')->nullable()->after('sekolah_kodlan')->index();
                $table->foreign('ekstrakurikuler_id')->references('id')->on('ekstrakurikuler')->onDelete('cascade');
            }

            if (!Schema::hasColumn('invoice_approvals', 'kategori_program')) {
                $table->string('kategori_program', 150)->nullable()->after('ekstrakurikuler_id')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_approvals', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_approvals', 'ekstrakurikuler_id')) {
                $table->dropForeign(['ekstrakurikuler_id']);
                $table->dropColumn('ekstrakurikuler_id');
            }

            if (Schema::hasColumn('invoice_approvals', 'kategori_program')) {
                $table->dropColumn('kategori_program');
            }
        });
    }
};
