<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini menyimpan status invoice per rombel per periode tagihan.
     * Mencakup skema tagihan, dual approval, dan history penerbitan.
     */
    public function up(): void
    {
        Schema::create('invoice_approvals', function (Blueprint $table) {
            $table->id();

            // ── Relasi ke Rombel ──────────────────────────────────────────
            $table->unsignedBigInteger('ekstrakurikuler_rombel_id');
            $table->foreign('ekstrakurikuler_rombel_id')
                  ->references('id')
                  ->on('ekstrakurikuler_rombel')
                  ->onDelete('cascade');

            // ── Skema & Periode Tagihan ───────────────────────────────────
            $table->enum('skema_tagihan', [
                'bulanan',        // Tagihan per bulan kalender (21 sekolah prioritas)
                'semester',       // ~16 pertemuan per semester
                'tahunan',        // ~32 pertemuan per tahun ajaran
                'per_4_pertemuan' // Rolling batch setiap 4 pertemuan (default)
            ]);

            // Label periode yang tampil di invoice, e.g. "Agustus 2026", "Semester 1 2026/2027", "Batch 1"
            $table->string('periode_label', 100);

            // Tahun ajaran, e.g. "2026/2027"
            $table->string('tahun_ajaran', 9);

            // Nomor batch (untuk per_4_pertemuan) atau nomor semester (1/2)
            $table->unsignedSmallInteger('periode_nomor')->nullable();

            // Range sesi yang dicakup invoice ini
            $table->unsignedSmallInteger('sesi_dari')->nullable(); // nomor pertemuan awal
            $table->unsignedSmallInteger('sesi_sampai')->nullable(); // nomor pertemuan akhir

            // Jumlah siswa billable yang dihitung sistem
            $table->unsignedSmallInteger('jumlah_siswa_billable')->default(0);

            // Jumlah sesi yang dicakup (verifikasi)
            $table->unsignedSmallInteger('jumlah_sesi')->default(0);

            // ── Nomor Invoice ─────────────────────────────────────────────
            // Format: INV/ERLASS/YYYYMM/KODLAN/NNN
            $table->string('nomor_invoice', 60)->nullable()->unique();

            // ── Status Alur ───────────────────────────────────────────────
            $table->enum('status', [
                'draft',
                'pending_operasional',
                'pending_akunting',
                'approved',
                'rejected',
            ])->default('draft');

            // ── Approval Operasional / Akademik ───────────────────────────
            $table->unsignedBigInteger('operasional_user_id')->nullable();
            $table->foreign('operasional_user_id')->references('id')->on('users')->nullOnDelete();
            $table->enum('operasional_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('operasional_approved_at')->nullable();
            $table->text('operasional_catatan')->nullable();

            // ── Approval Akunting / Finance ───────────────────────────────
            $table->unsignedBigInteger('akunting_user_id')->nullable();
            $table->foreign('akunting_user_id')->references('id')->on('users')->nullOnDelete();
            $table->enum('akunting_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('akunting_approved_at')->nullable();
            $table->text('akunting_catatan')->nullable();

            // ── PDF & Penerbitan ──────────────────────────────────────────
            $table->timestamp('pdf_generated_at')->nullable();
            $table->string('pdf_path', 255)->nullable(); // Path file PDF yang disimpan

            // ── Metadata ──────────────────────────────────────────────────
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // ── Indexes ───────────────────────────────────────────────────
            $table->index(['ekstrakurikuler_rombel_id', 'skema_tagihan', 'periode_label'], 'idx_invoice_rombel_periode');
            $table->index(['status'], 'idx_invoice_status');
            $table->index(['tahun_ajaran'], 'idx_invoice_tahun_ajaran');

            // Satu invoice per rombel per periode (tidak boleh duplikat)
            $table->unique(
                ['ekstrakurikuler_rombel_id', 'periode_label'],
                'uq_invoice_rombel_periode'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_approvals');
    }
};
