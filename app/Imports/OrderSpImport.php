<?php

namespace App\Imports;

use App\Models\OrderSp;
use App\Models\OrderItem;
use App\Models\Salesman;
use App\Models\Sekolah;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
use Exception;

class OrderSpImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $processedSpNumbers = [];

    /**
     * Import SP and items data.
     * 
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $nomorSp = trim($row['nomor_sp']);
                
                // Find school
                $sekolahKodlan = trim($row['kode_pelanggan']);
                $sekolah = Sekolah::where('kodlan', $sekolahKodlan)->first();
                if (!$sekolah) {
                    throw new Exception("Sekolah dengan kode pelanggan (kodlan) '{$sekolahKodlan}' tidak ditemukan.");
                }

                // Find salesman
                $kodeSalesman = trim($row['kode_salesman']);
                $salesman = Salesman::where('kode_salesman', $kodeSalesman)->first();
                if (!$salesman) {
                    throw new Exception("Salesman dengan kode '{$kodeSalesman}' tidak ditemukan.");
                }

                // Parse dates
                $tanggalSp = Carbon::parse($row['tanggal_sp'])->format('Y-m-d');
                $tanggalMulai = Carbon::parse($row['tanggal_mulai_rencana'])->format('Y-m-d');

                // If this is the first time we see this SP number in this batch, update or create
                if (!isset($this->processedSpNumbers[$nomorSp])) {
                    $orderSp = OrderSp::updateOrCreate(
                        ['nomor_sp' => $nomorSp],
                        [
                            'tanggal_sp' => $tanggalSp,
                            'sekolah_kodlan' => $sekolah->kodlan,
                            'salesman_id' => $salesman->id,
                            'jumlah_peserta_estimasi' => intval($row['jumlah_peserta_estimasi'] ?? 0),
                            'jenis_kegiatan' => trim($row['jenis_kegiatan'] ?? 'eskul'),
                            'lokasi_pembelajaran' => trim($row['lokasi_pembelajaran'] ?? 'Sekolah'),
                            'tanggal_mulai_rencana' => $tanggalMulai,
                            'jumlah_pertemuan' => intval($row['jumlah_pertemuan'] ?? 12),
                            'catatan_khusus' => $row['catatan_khusus'] ?? null,
                            'status' => 'draft', // default to draft
                            'created_by' => Auth::id() ?? 1, // Fallback to 1 if CLI/anonymous
                            'updated_by' => Auth::id() ?? 1,
                        ]
                    );

                    // Delete existing items for a fresh overwrite if updating
                    $orderSp->items()->delete();
                    $this->processedSpNumbers[$nomorSp] = $orderSp;
                } else {
                    $orderSp = $this->processedSpNumbers[$nomorSp];
                }

                // Process item
                $kodeProduk = trim($row['kode_produk']);
                $product = Product::where('kode_produk', $kodeProduk)->first();
                if (!$product) {
                    throw new Exception("Produk dengan kode '{$kodeProduk}' tidak ditemukan.");
                }

                $hargaSatuan = floatval($row['harga_satuan'] ?? $product->harga);

                OrderItem::create([
                    'order_sp_id' => $orderSp->id,
                    'product_id' => $product->id,
                    'harga_satuan' => $hargaSatuan,
                ]);
            }
        });
    }

    /**
     * Validation rules.
     * 
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.nomor_sp' => 'required|string',
            '*.tanggal_sp' => 'required',
            '*.kode_pelanggan' => 'required|string',
            '*.kode_salesman' => 'required|string',
            '*.jumlah_peserta_estimasi' => 'nullable|integer',
            '*.jenis_kegiatan' => 'nullable|in:eskul,inkul',
            '*.lokasi_pembelajaran' => 'nullable|string',
            '*.tanggal_mulai_rencana' => 'required',
            '*.jumlah_pertemuan' => 'required|integer',
            '*.catatan_khusus' => 'nullable|string',
            '*.kode_produk' => 'required|string',
            '*.harga_satuan' => 'nullable|numeric',
        ];
    }
}
