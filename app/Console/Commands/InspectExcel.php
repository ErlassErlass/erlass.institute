<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InspectExcel extends Command
{
    protected $signature = 'inspect:excel';
    protected $description = 'Inspect Excel headers';

    public function handle()
    {
        $path = base_path('Data Instruktur Erlass 2025.xlsx');
        if (!file_exists($path)) {
            $this->error("File not found: $path");
            return;
        }

        $data = Excel::toArray(new class implements ToArray {
            public function array(array $array)
            {
                return $array;
            }
        }, $path);

        if (empty($data) || empty($data[0])) {
            $this->error("Empty file");
            return;
        }

        // Sheet 1, Row 1 (Headers usually)
        $headers = $data[0][0]; // First sheet, first row
        $firstRow = $data[0][1] ?? []; // First sheet, second row (data)

        $this->info("Headers: " . implode(", ", $headers));
        $this->info("First Row Example: " . implode(", ", $firstRow));
    }
}
