<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah konsep invoice menjadi per sekolah dengan rincian item per rombel.
     */
    public function up(): void
    {
        // 1. Modifikasi tabel invoice_approvals
        Schema::table('invoice_approvals', function (Blueprint $table) {
            // Relasi langsung ke sekolah (1 invoice = 1 sekolah)
            if (!Schema::hasColumn('invoice_approvals', 'sekolah_kodlan')) {
                $table->string('sekolah_kodlan', 20)->nullable()->after('id')->index();
                $table->foreign('sekolah_kodlan')->references('kodlan')->on('sekolah')->onDelete('cascade');
            }

            // ekstrakurikuler_rombel_id dibuat nullable karena detail per rombel pindah ke invoice_approval_items
            if (Schema::hasColumn('invoice_approvals', 'ekstrakurikuler_rombel_id')) {
                $table->unsignedBigInteger('ekstrakurikuler_rombel_id')->nullable()->change();
            }

            // Total rombel yang dicakup dalam invoice sekolah ini
            if (!Schema::hasColumn('invoice_approvals', 'total_rombel')) {
                $table->unsignedSmallInteger('total_rombel')->default(1)->after('jumlah_sesi');
            }
        });

        // 2. Buat tabel invoice_approval_items untuk rincian per rombel
        if (!Schema::hasTable('invoice_approval_items')) {
            Schema::create('invoice_approval_items', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('invoice_approval_id');
                $table->foreign('invoice_approval_id')
                      ->references('id')
                      ->on('invoice_approvals')
                      ->onDelete('cascade');

                $table->unsignedBigInteger('ekstrakurikuler_rombel_id');
                $table->foreign('ekstrakurikuler_rombel_id')
                      ->references('id')
                      ->on('ekstrakurikuler_rombel')
                      ->onDelete('cascade');

                $table->unsignedSmallInteger('sesi_dari')->nullable();
                $table->unsignedSmallInteger('sesi_sampai')->nullable();
                $table->unsignedSmallInteger('jumlah_sesi')->default(0);

                // Hitungan siswa billable untuk rombel ini
                $table->unsignedSmallInteger('jumlah_siswa_billable')->default(0);

                // Koreksi manual per rombel (opsional jika ada dispensasi per kelas)
                $table->smallInteger('koreksi_siswa_billable')->nullable();
                $table->text('koreksi_catatan')->nullable();
                $table->unsignedBigInteger('koreksi_by')->nullable();
                $table->foreign('koreksi_by')->references('id')->on('users')->nullOnDelete();
                $table->timestamp('koreksi_at')->nullable();

                $table->timestamps();

                $table->unique(
                    ['invoice_approval_id', 'ekstrakurikuler_rombel_id'],
                    'inv_items_unique_inv_rombel'
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_approval_items');

        Schema::table('invoice_approvals', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_approvals', 'total_rombel')) {
                $table->dropColumn('total_rombel');
            }
            if (Schema::hasColumn('invoice_approvals', 'sekolah_kodlan')) {
                $table->dropForeign(['sekolah_kodlan']);
                $table->dropColumn('sekolah_kodlan');
            }
        });
    }
};
