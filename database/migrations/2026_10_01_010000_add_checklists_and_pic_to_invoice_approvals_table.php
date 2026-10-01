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
        Schema::table('invoice_approvals', function (Blueprint $table) {
            $table->boolean('is_konfirmasi_pic')->default(false)->after('operasional_status');
            $table->string('pic_konfirmasi_nama', 150)->nullable()->after('is_konfirmasi_pic');
            $table->text('pic_konfirmasi_catatan')->nullable()->after('pic_konfirmasi_nama');
            $table->json('operasional_checklist')->nullable()->after('pic_konfirmasi_catatan');

            $table->boolean('is_invoice_tercetak')->default(false)->after('akunting_status');
            $table->json('akunting_checklist')->nullable()->after('is_invoice_tercetak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_approvals', function (Blueprint $table) {
            $table->dropColumn([
                'is_konfirmasi_pic',
                'pic_konfirmasi_nama',
                'pic_konfirmasi_catatan',
                'operasional_checklist',
                'is_invoice_tercetak',
                'akunting_checklist',
            ]);
        });
    }
};
