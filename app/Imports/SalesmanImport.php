<?php

namespace App\Imports;

use App\Models\Salesman;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SalesmanImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * Import salesmen data.
     * 
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $userId = null;
            if (!empty($row['user_email'])) {
                $user = User::where('email', trim($row['user_email']))->first();
                if ($user) {
                    $userId = $user->id;
                }
            }

            Salesman::updateOrCreate(
                ['kode_salesman' => trim($row['kode_salesman'])],
                [
                    'user_id' => $userId,
                    'nama_salesman' => trim($row['nama_salesman']),
                    'group_leader' => !empty($row['group_leader']) ? trim($row['group_leader']) : null,
                    'area' => !empty($row['area']) ? trim($row['area']) : null,
                ]
            );
        }
    }

    /**
     * Validation rules.
     * 
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.kode_salesman' => 'required|string',
            '*.nama_salesman' => 'required|string',
            '*.group_leader' => 'nullable|string',
            '*.area' => 'nullable|string',
            '*.user_email' => 'nullable|email',
        ];
    }
}
