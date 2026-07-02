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
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders_sp');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'harga')) {
                $table->dropColumn('harga');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('harga', 12, 2)->after('jenis')->nullable();
        });

        Schema::create('orders_sp', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sp')->unique();
            $table->date('tanggal_sp');
            $table->string('sekolah_kodlan');
            $table->foreignId('salesman_id')->nullable();
            $table->integer('jumlah_peserta_estimasi')->nullable();
            $table->string('jenis_kegiatan')->nullable();
            $table->string('lokasi_pembelajaran')->nullable();
            $table->date('tanggal_mulai_rencana')->nullable();
            $table->integer('jumlah_pertemuan')->nullable();
            $table->text('catatan_khusus')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_sp_id')->constrained('orders_sp')->onDelete('cascade');
            $table->foreignId('product_id');
            $table->decimal('harga_satuan', 12, 2);
            $table->timestamps();
        });
    }
};

