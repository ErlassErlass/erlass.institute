<?php

namespace Database\Seeders;

use App\Models\OrderSp;
use App\Models\OrderItem;
use App\Models\Salesman;
use App\Models\Sekolah;
use App\Models\Product;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\User;
use App\Models\SchoolPic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSpDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'timestamp' => '18/07/2025 14:10:27',
                'ekstrakurikuler' => 'Coding Scratch',
                'kode_salesman' => 'P2996',
                'sekolah_kodlan' => '60706176',
                'alamat' => 'Jl. KH. ABD. RAHIM No.1, RT.1/RW.3, Kuningan Bar., Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12710',
                'google_maps_link' => 'https://maps.app.goo.gl/H6FTkxUzQ536ZDhe9',
                'kepala_sekolah' => '-',
                'penanggung_jawab' => 'Irma Amalia',
                'no_telepon' => '085828798821',
                'proyektor' => '1',
                'kabel_hdmi' => '1',
                'kabel_vga' => '1',
                'total_siswa' => 15,
                'ruang_kelas' => 'Lab Komputer',
                'rombels' => [
                    [
                        'nama_rombel' => 'Rombel 1',
                        'nomor_rombel' => 1,
                        'jumlah_siswa' => 15,
                        'total_pertemuan' => 32,
                        'tanggal_mulai' => '2025-07-22',
                        'tanggal_selesai' => '2026-07-30',
                        'hari' => 'selasa',
                        'jam_mulai' => '12:30',
                    ]
                ]
            ],
            [
                'timestamp' => '25/07/2025 15:38:05',
                'ekstrakurikuler' => 'Coding Scratch',
                'kode_salesman' => 'P3076',
                'sekolah_kodlan' => '20109176',
                'alamat' => 'Jl. Inspeksi Saluran Kalimalang Jl. Curug Raya No.98, RT.4/RW.8, Pd. Klp., Kec. Duren Sawit, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13450',
                'google_maps_link' => 'https://maps.app.goo.gl/y4NLKwJtMmRm8vw16',
                'kepala_sekolah' => 'Nurma Irnawaty',
                'penanggung_jawab' => 'Ibu Elsa',
                'no_telepon' => '081383928846',
                'proyektor' => '2',
                'kabel_hdmi' => '2',
                'kabel_vga' => '2',
                'total_siswa' => 13,
                'ruang_kelas' => 'Lab Komputer',
                'rombels' => [
                    [
                        'nama_rombel' => 'Rombel 1',
                        'nomor_rombel' => 1,
                        'jumlah_siswa' => 13,
                        'total_pertemuan' => 32,
                        'tanggal_mulai' => '2025-08-04',
                        'tanggal_selesai' => '2026-05-04',
                        'hari' => 'senin',
                        'jam_mulai' => '14:00',
                    ]
                ]
            ],
            [
                'timestamp' => '25/07/2025 16:22:17',
                'ekstrakurikuler' => 'Coding Scratch',
                'kode_salesman' => 'P3076',
                'sekolah_kodlan' => '20103904',
                'alamat' => 'Jl. Menteng Prima No.2, RW.2, Ujung Menteng, Kec. Cakung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13960',
                'google_maps_link' => 'https://share.google/LS4s0dZbcxhJxWySE',
                'kepala_sekolah' => 'Ibu Rini',
                'penanggung_jawab' => 'Pak Yatno',
                'no_telepon' => '081280005231',
                'proyektor' => '2',
                'kabel_hdmi' => '2',
                'kabel_vga' => '2',
                'total_siswa' => 12,
                'ruang_kelas' => 'Lab Komputer',
                'rombels' => [
                    [
                        'nama_rombel' => 'Rombel 1',
                        'nomor_rombel' => 1,
                        'jumlah_siswa' => 12,
                        'total_pertemuan' => 32,
                        'tanggal_mulai' => '2025-07-30',
                        'tanggal_selesai' => '2026-05-27',
                        'hari' => 'rabu',
                        'jam_mulai' => '13:05',
                    ]
                ]
            ],
            [
                'timestamp' => '28/07/2025 14:41:11',
                'ekstrakurikuler' => 'Robotik Explorer',
                'kode_salesman' => 'P3075',
                'sekolah_kodlan' => '69886369',
                'alamat' => 'Jl. Masjid fatahillah IX, RT.002/RW.007, Sudimara Tim., Kec. Ciledug, Kota Tangerang, Banten 15151',
                'google_maps_link' => 'https://share.google/GJJUTC3uom7cbCcEF',
                'kepala_sekolah' => 'Ibu Risah',
                'penanggung_jawab' => 'Ibu Risa',
                'no_telepon' => '085716977078',
                'proyektor' => '2',
                'kabel_hdmi' => '2',
                'kabel_vga' => '2',
                'total_siswa' => 23,
                'ruang_kelas' => 'Lab Komputer',
                'rombels' => [
                    [
                        'nama_rombel' => 'Rombel 1',
                        'nomor_rombel' => 1,
                        'jumlah_siswa' => 12,
                        'total_pertemuan' => 32,
                        'tanggal_mulai' => '2025-07-26',
                        'tanggal_selesai' => '2026-05-26',
                        'hari' => 'sabtu',
                        'jam_mulai' => '08:30',
                    ],
                    [
                        'nama_rombel' => 'Rombel 2',
                        'nomor_rombel' => 2,
                        'jumlah_siswa' => 11,
                        'total_pertemuan' => 32,
                        'tanggal_mulai' => '2025-07-26',
                        'tanggal_selesai' => '2026-05-26',
                        'hari' => 'sabtu',
                        'jam_mulai' => '08:30',
                    ]
                ]
            ],
            [
                'timestamp' => '28/07/2025 15:09:06',
                'ekstrakurikuler' => 'Coding Scratch',
                'kode_salesman' => 'P3075',
                'sekolah_kodlan' => '70051968',
                'alamat' => 'Jl. Kh Hayim Ashari NO.7 Cipondoh Kota Tangerang',
                'google_maps_link' => 'https://share.google/aCuIcE2IXQvCPl3KQ',
                'kepala_sekolah' => 'Ibu Mardiah',
                'penanggung_jawab' => 'Ibu Melati',
                'no_telepon' => '0895398109595',
                'proyektor' => '1',
                'kabel_hdmi' => '1',
                'kabel_vga' => '1',
                'total_siswa' => 32,
                'ruang_kelas' => 'Lab Komputer',
                'rombels' => [
                    [
                        'nama_rombel' => 'Rombel 1',
                        'nomor_rombel' => 1,
                        'jumlah_siswa' => 32,
                        'total_pertemuan' => 32,
                        'tanggal_mulai' => '2025-08-04',
                        'tanggal_selesai' => '2026-05-04',
                        'hari' => 'senin',
                        'jam_mulai' => '14:30',
                    ]
                ]
            ],
            [
                'timestamp' => '28/07/2025 15:20:03',
                'ekstrakurikuler' => 'Robotik Explorer',
                'kode_salesman' => 'P2996',
                'sekolah_kodlan' => '20106295',
                'alamat' => 'Jl. Madrasah No.12 8, RT.8/RW.1, Gandaria Sel., Kec. Cilandak, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12420',
                'google_maps_link' => 'https://share.google/EyDE5U2YhSNCOyL7O',
                'kepala_sekolah' => 'Ammar Rulloh',
                'penanggung_jawab' => 'Ibu Sholihah',
                'no_telepon' => '081398292032',
                'proyektor' => '1',
                'kabel_hdmi' => '1',
                'kabel_vga' => '1',
                'total_siswa' => 32,
                'ruang_kelas' => 'Lab Komputer',
                'rombels' => [
                    [
                        'nama_rombel' => 'Rombel 1',
                        'nomor_rombel' => 1,
                        'jumlah_siswa' => 32,
                        'total_pertemuan' => 32,
                        'tanggal_mulai' => '2025-08-04',
                        'tanggal_selesai' => '2026-07-30',
                        'hari' => 'senin',
                        'jam_mulai' => '13:00',
                    ]
                ]
            ],
            [
                'timestamp' => '28/07/2025 15:26:16',
                'ekstrakurikuler' => 'Coding Scratch',
                'kode_salesman' => 'P2996',
                'sekolah_kodlan' => '20103074',
                'alamat' => 'No Jl. Mesjid No.16 12, RT.12/RW.1, Pejaten Bar., Ps. Minggu, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12510',
                'google_maps_link' => 'https://maps.app.goo.gl/wkhD56onDBVC78sB6',
                'kepala_sekolah' => 'Mohammad Nizom Chotib',
                'penanggung_jawab' => 'Ibu Nisa',
                'no_telepon' => '081384188585',
                'proyektor' => '1',
                'kabel_hdmi' => '1',
                'kabel_vga' => '1',
                'total_siswa' => 32,
                'ruang_kelas' => 'Lab Komputer',
                'rombels' => [
                    [
                        'nama_rombel' => 'Rombel 1',
                        'nomor_rombel' => 1,
                        'jumlah_siswa' => 10,
                        'total_pertemuan' => 32,
                        'tanggal_mulai' => '2025-08-04',
                        'tanggal_selesai' => '2026-07-30',
                        'hari' => 'senin',
                        'jam_mulai' => '12:00',
                    ],
                    [
                        'nama_rombel' => 'Rombel 2',
                        'nomor_rombel' => 2,
                        'jumlah_siswa' => 11,
                        'total_pertemuan' => 32,
                        'tanggal_mulai' => '2025-08-04',
                        'tanggal_selesai' => '2026-07-30',
                        'hari' => 'senin',
                        'jam_mulai' => '13:00',
                    ],
                    [
                        'nama_rombel' => 'Rombel 3',
                        'nomor_rombel' => 3,
                        'jumlah_siswa' => 11,
                        'total_pertemuan' => 32,
                        'tanggal_mulai' => '2025-08-04',
                        'tanggal_selesai' => '2026-07-30',
                        'hari' => 'senin',
                        'jam_mulai' => '14:00',
                    ]
                ]
            ],
        ];

        // Dapatkan user admin/webmaster pertama untuk approval/creation audit
        $adminUser = User::whereIn('role', ['webmaster', 'admin_sistem', 'admin'])->first();
        $adminId = $adminUser ? $adminUser->id : 1;

        // Dapatkan instruktur untuk ditugaskan ke rombel
        $instructors = User::where('role', 'instruktur')->get();
        $instructorCount = $instructors->count();

        // Seed default products if table is empty
        if (Product::count() === 0) {
            Product::create([
                'id' => 1,
                'kode_produk' => 'P-SCRATCH',
                'nama_produk' => 'Coding Scratch',
                'jenis' => 'Coding',
                'harga' => 150000.00,
                'durasi_bulan' => 12,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 90,
            ]);
            Product::create([
                'id' => 2,
                'kode_produk' => 'P-MICROBIT',
                'nama_produk' => 'Micro:bit Learning Kit',
                'jenis' => 'Coding',
                'harga' => 175000.00,
                'durasi_bulan' => 12,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 90,
            ]);
            Product::create([
                'id' => 3,
                'kode_produk' => 'P-ROBOTIK',
                'nama_produk' => 'Robotik Explorer',
                'jenis' => 'Robotik',
                'harga' => 200000.00,
                'durasi_bulan' => 12,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 90,
            ]);
            Product::create([
                'id' => 4,
                'kode_produk' => 'P-PYTHON',
                'nama_produk' => 'Python Programming',
                'jenis' => 'Coding',
                'harga' => 225000.00,
                'durasi_bulan' => 12,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 90,
            ]);
        }

        DB::beginTransaction();

        try {
            // Clean up old dummy SPs for idempotency
            $oldSpIds = OrderSp::where('nomor_sp', 'LIKE', 'SP/ERLASS/%')->pluck('id');
            if ($oldSpIds->isNotEmpty()) {
                // Delete associated Ekstrakurikuler and Rombels to avoid orphan data
                foreach ($oldSpIds as $spId) {
                    $orderSp = OrderSp::find($spId);
                    if ($orderSp) {
                        $ekstrakurikulers = Ekstrakurikuler::where('sekolah_kodlan', $orderSp->sekolah_kodlan)->get();
                        foreach ($ekstrakurikulers as $eks) {
                            $eks->rombels()->forceDelete();
                            $eks->forceDelete();
                        }
                    }
                }
                OrderSp::whereIn('id', $oldSpIds)->delete();
            }

            $spIndex = 1;
            foreach ($data as $index => $itemData) {
                // 1. Cari atau buat Salesman dari User jika belum ada di tabel salesmen
                $salesman = Salesman::where('kode_salesman', $itemData['kode_salesman'])->first();
                if (!$salesman) {
                    $userSales = User::where('kompetensi_2', 'LIKE', '%' . $itemData['kode_salesman'] . '%')->first();
                    if ($userSales) {
                        $salesman = Salesman::create([
                            'user_id' => $userSales->id,
                            'kode_salesman' => $itemData['kode_salesman'],
                            'nama_salesman' => $userSales->nama_lengkap,
                            'group_leader' => 'Team Leader',
                            'area' => 'Jabodetabek'
                        ]);
                    } else {
                        $this->command->warn("Salesman with code {$itemData['kode_salesman']} not found in users, skipping row.");
                        continue;
                    }
                }

                // 2. Cari Sekolah
                $sekolah = Sekolah::where('kodlan', $itemData['sekolah_kodlan'])->first();
                if (!$sekolah) {
                    $this->command->warn("School with kodlan {$itemData['sekolah_kodlan']} not found, skipping row.");
                    continue;
                }

                // Update info detail sekolah dari spreadsheet
                $sekolah->update([
                    'alamat' => $itemData['alamat'],
                    'lokasi_default' => 'sekolah',
                ]);

                // Update/Create school PIC record
                SchoolPic::updateOrCreate(
                    [
                        'sekolah_kodlan' => $sekolah->kodlan,
                        'nama' => $itemData['penanggung_jawab'],
                    ],
                    [
                        'kontak' => $itemData['no_telepon'],
                        'jabatan' => 'Koordinator Ekskul',
                    ]
                );

                // 3. Cari Produk
                $product = Product::where('nama_produk', $itemData['ekstrakurikuler'])->first();
                if (!$product) {
                    $this->command->warn("Product {$itemData['ekstrakurikuler']} not found, skipping row.");
                    continue;
                }

                // 4. Generate Nomor SP & Tanggal SP
                $nomorSp = 'SP/ERLASS/' . date('Ymd', strtotime(str_replace('/', '-', $itemData['timestamp']))) . '/' . str_pad($spIndex++, 3, '0', STR_PAD_LEFT);
                $tanggalSp = Carbon::createFromFormat('d/m/Y H:i:s', $itemData['timestamp'])->format('Y-m-d');
                $tanggalMulai = $itemData['rombels'][0]['tanggal_mulai'];

                // 5. Buat OrderSp
                $orderSp = OrderSp::create([
                    'nomor_sp' => $nomorSp,
                    'tanggal_sp' => $tanggalSp,
                    'sekolah_kodlan' => $sekolah->kodlan,
                    'salesman_id' => $salesman->id,
                    'jumlah_peserta_estimasi' => $itemData['total_siswa'],
                    'jenis_kegiatan' => 'eskul',
                    'lokasi_pembelajaran' => $itemData['ruang_kelas'],
                    'tanggal_mulai_rencana' => $tanggalMulai,
                    'jumlah_pertemuan' => $itemData['rombels'][0]['total_pertemuan'],
                    'catatan_khusus' => 'Generated from dummy data seeder.',
                    'status' => 'disetujui',
                    'approved_by' => $adminId,
                    'approved_at' => Carbon::now(),
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]);

                // 6. Buat OrderItem
                OrderItem::create([
                    'order_sp_id' => $orderSp->id,
                    'product_id' => $product->id,
                    'harga_satuan' => $product->harga,
                ]);

                // 7. Buat Ekstrakurikuler (Program)
                $eks = Ekstrakurikuler::create([
                    'kategori_program' => $product->nama_produk,
                    'deskripsi' => $product->deskripsi,
                    'jenis_pembayaran' => 'per_siswa_bulan',
                    'user_id_sales' => $salesman->user_id,
                    'sekolah_kodlan' => $sekolah->kodlan,
                    'alamat_lengkap' => $itemData['alamat'],
                    'google_maps_link' => $itemData['google_maps_link'],
                    'kepala_sekolah' => $itemData['kepala_sekolah'],
                    'penanggung_jawab' => $itemData['penanggung_jawab'],
                    'no_telepon' => $itemData['no_telepon'],
                    'koneksi_internet' => 'ada',
                    'proyektor' => intval($itemData['proyektor']) > 0 ? 'ada' : 'tidak_ada',
                    'keterangan_proyektor' => $itemData['proyektor'] . ' unit',
                    'kabel_hdmi' => intval($itemData['kabel_hdmi']) > 0 ? 'ada' : 'tidak_ada',
                    'kabel_vga' => intval($itemData['kabel_vga']) > 0 ? 'ada' : 'tidak_ada',
                    'keterangan_kabel' => 'HDMI: ' . $itemData['kabel_hdmi'] . ', VGA: ' . $itemData['kabel_vga'],
                    'total_siswa' => $itemData['total_siswa'],
                    'total_ruangan' => 1,
                    'total_rombel' => count($itemData['rombels']),
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $itemData['rombels'][0]['tanggal_selesai'],
                    'total_pertemuan' => $itemData['rombels'][0]['total_pertemuan'],
                    'frekuensi' => 'mingguan',
                    'status' => 'aktif',
                    'tanggal_disetujui' => Carbon::now(),
                    'disetujui_oleh' => $adminId,
                    'tanggal_aktivasi' => Carbon::now(),
                    'diaktifkan_oleh' => $adminId,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]);

                // 8. Buat Rombels (dan auto generate sessions lewat model event)
                foreach ($itemData['rombels'] as $rombelData) {
                    // Tentukan instruktur secara bergantian
                    $instructorId = null;
                    if ($instructorCount > 0) {
                        $instructorId = $instructors[($index + $rombelData['nomor_rombel']) % $instructorCount]->id;
                    }

                    // Tentukan jam selesai (+ 90 menit dari jam mulai)
                    $jamMulai = Carbon::createFromFormat('H:i', $rombelData['jam_mulai']);
                    $jamSelesai = (clone $jamMulai)->addMinutes(90);

                    // Buat rombel (akan men-trigger pembuatan sesi otomatis)
                    EkstrakurikulerRombel::create([
                        'ekstrakurikuler_id' => $eks->id,
                        'nama_rombel' => $rombelData['nama_rombel'],
                        'nomor_rombel' => $rombelData['nomor_rombel'],
                        'jumlah_siswa' => $rombelData['jumlah_siswa'],
                        'ruangan' => $itemData['ruang_kelas'],
                        'tanggal_mulai' => $rombelData['tanggal_mulai'],
                        'tanggal_selesai' => $rombelData['tanggal_selesai'],
                        'hari' => $rombelData['hari'],
                        'jam_mulai' => $jamMulai->format('H:i'),
                        'jam_selesai' => $jamSelesai->format('H:i'),
                        'total_pertemuan' => $rombelData['total_pertemuan'],
                        'frekuensi' => 'mingguan',
                        'pertemuan_selesai' => 0,
                        'user_id_instruktur' => $instructorId,
                        'status' => 'berlangsung',
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                    ]);
                }
            }

            DB::commit();
            $this->command->info('Successfully seeded dummy SP data and generated classes/sessions!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding dummy SP data: ' . $e->getMessage());
        }
    }
}
