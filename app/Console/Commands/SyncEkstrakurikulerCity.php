<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ekstrakurikuler;
use App\Models\Sekolah;
use Illuminate\Support\Facades\DB;

class SyncEkstrakurikulerCity extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ekstrakurikuler:sync-city {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Sinkronisasi field city pada ekstrakurikuler dengan data kotkab dari sekolah';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Memulai sinkronisasi data city ekstrakurikuler...');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - Tidak ada data yang akan diubah');
        }

        try {
            DB::beginTransaction();
            
            // Ambil data ekstrakurikuler yang city-nya kosong atau null
            $ekstrakurikulerQuery = Ekstrakurikuler::with('sekolah')
                ->where(function($query) {
                    $query->whereNull('city')
                          ->orWhere('city', '');
                });
                
            $totalToUpdate = $ekstrakurikulerQuery->count();
            $this->info("Ditemukan {$totalToUpdate} record yang perlu diperbarui");
            
            if ($totalToUpdate === 0) {
                $this->info('Semua data sudah tersinkronisasi!');
                return 0;
            }
            
            $updated = 0;
            $errors = 0;
            
            $this->withProgressBar($ekstrakurikulerQuery->get(), function($ekstrakurikuler) use (&$updated, &$errors, $dryRun) {
                if ($ekstrakurikuler->sekolah) {
                    if (!$dryRun) {
                        $ekstrakurikuler->update(['city' => $ekstrakurikuler->sekolah->kotkab]);
                    }
                    $updated++;
                } else {
                    $this->warn("\nRecord ID {$ekstrakurikuler->id} tidak memiliki relasi sekolah yang valid");
                    $errors++;
                }
            });
            
            $this->newLine(2);
            
            if (!$dryRun) {
                DB::commit();
                $this->info("✅ Berhasil memperbarui {$updated} record");
            } else {
                DB::rollBack();
                $this->info("✅ DRY RUN: Akan memperbarui {$updated} record jika dijalankan");
            }
            
            if ($errors > 0) {
                $this->warn("⚠️  {$errors} record memiliki masalah dan tidak dapat diperbarui");
            }
            
            // Tampilkan statistik setelah update
            $this->showStatistics();
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Tampilkan statistik data
     */
    private function showStatistics()
    {
        $this->info("\n📊 Statistik Data Ekstrakurikuler:");
        
        $total = Ekstrakurikuler::count();
        $withCity = Ekstrakurikuler::whereNotNull('city')->where('city', '!=', '')->count();
        $withoutCity = $total - $withCity;
        
        $this->table(['Kategori', 'Jumlah'], [
            ['Total Record', $total],
            ['Dengan City', $withCity],
            ['Tanpa City', $withoutCity],
            ['Persentase Lengkap', $total > 0 ? round(($withCity / $total) * 100, 2) . '%' : '0%']
        ]);
        
        // Tampilkan sample data
        $this->info("\n📋 Sample Data (5 record pertama):");
        
        $samples = Ekstrakurikuler::with('sekolah')
            ->select('id', 'kategori_program', 'region', 'city', 'sekolah_kodlan')
            ->take(5)
            ->get();
            
        $sampleData = $samples->map(function($item) {
            return [
                'ID' => $item->id,
                'Program' => substr($item->kategori_program, 0, 20),
                'Region' => $item->region ?: 'NULL',
                'City' => $item->city ?: 'NULL',  
                'Sekolah City' => $item->sekolah?->kotkab ?: 'NULL'
            ];
        });
        
        $this->table(['ID', 'Program', 'Region', 'City', 'Sekolah City'], $sampleData->toArray());
    }
}