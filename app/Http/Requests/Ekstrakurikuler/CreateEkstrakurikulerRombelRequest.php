<?php

namespace App\Http\Requests\Ekstrakurikuler;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk validasi data Rombel (Steps 5-9)
 */
class CreateEkstrakurikulerRombelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rombelNumber = $this->getRombelNumber();
        $prefix = "rombel_{$rombelNumber}_";

        return [
            $prefix.'total_pertemuan' => 'required|integer|min:1|max:50',
            $prefix.'tanggal_mulai' => 'required|date|after_or_equal:today',
            $prefix.'tanggal_selesai' => 'required|date|after:'.$prefix.'tanggal_mulai',
            $prefix.'hari' => 'required|string|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            $prefix.'jam_mulai' => 'required|date_format:H:i',
            $prefix.'jumlah_siswa' => 'required|integer|min:1|max:50',
            $prefix.'ruangan' => 'nullable|string|max:100',
            $prefix.'keterangan_ruangan' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        $rombelNumber = $this->getRombelNumber();
        $prefix = "rombel_{$rombelNumber}_";

        return [
            $prefix.'total_pertemuan' => 'total pertemuan',
            $prefix.'tanggal_mulai' => 'tanggal mulai',
            $prefix.'tanggal_selesai' => 'tanggal selesai',
            $prefix.'hari' => 'hari kegiatan',
            $prefix.'jam_mulai' => 'jam mulai',
            $prefix.'jumlah_siswa' => 'jumlah siswa',
            $prefix.'ruangan' => 'ruangan',
            $prefix.'keterangan_ruangan' => 'keterangan ruangan',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        $rombelNumber = $this->getRombelNumber();
        $prefix = "rombel_{$rombelNumber}_";

        return [
            $prefix.'total_pertemuan.required' => 'Total pertemuan rombel wajib diisi.',
            $prefix.'total_pertemuan.integer' => 'Total pertemuan harus berupa angka.',
            $prefix.'total_pertemuan.min' => 'Total pertemuan minimal 1.',
            $prefix.'total_pertemuan.max' => 'Total pertemuan maksimal 50.',

            $prefix.'tanggal_mulai.required' => 'Tanggal mulai rombel wajib diisi.',
            $prefix.'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            $prefix.'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',

            $prefix.'tanggal_selesai.required' => 'Tanggal selesai rombel wajib diisi.',
            $prefix.'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            $prefix.'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',

            $prefix.'hari.required' => 'Hari kegiatan rombel wajib dipilih.',
            $prefix.'hari.in' => 'Hari kegiatan tidak valid.',

            $prefix.'jam_mulai.required' => 'Jam mulai rombel wajib diisi.',
            $prefix.'jam_mulai.date_format' => 'Format jam mulai tidak valid (HH:MM).',

            $prefix.'jumlah_siswa.required' => 'Jumlah siswa rombel wajib diisi.',
            $prefix.'jumlah_siswa.integer' => 'Jumlah siswa harus berupa angka.',
            $prefix.'jumlah_siswa.min' => 'Jumlah siswa minimal 1 orang.',
            $prefix.'jumlah_siswa.max' => 'Jumlah siswa maksimal 50 orang.',

            $prefix.'ruangan.max' => 'Nama ruangan tidak boleh lebih dari 100 karakter.',
            $prefix.'keterangan_ruangan.max' => 'Keterangan ruangan tidak boleh lebih dari 255 karakter.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $rombelNumber = $this->getRombelNumber();
            $prefix = "rombel_{$rombelNumber}_";

            // Validate date range (tidak lebih dari 2 tahun)
            if ($this->filled($prefix.'tanggal_mulai') && $this->filled($prefix.'tanggal_selesai')) {
                try {
                    $startDate = Carbon::parse($this->input($prefix.'tanggal_mulai'));
                    $endDate = Carbon::parse($this->input($prefix.'tanggal_selesai'));

                    if ($endDate->diffInMonths($startDate) > 24) {
                        $validator->errors()->add($prefix.'tanggal_selesai',
                            'Periode program tidak boleh lebih dari 2 tahun.'
                        );
                    }

                    // Check if date range is too short (minimal 1 minggu)
                    if ($endDate->diffInDays($startDate) < 7) {
                        $validator->errors()->add($prefix.'tanggal_selesai',
                            'Periode program minimal 1 minggu.'
                        );
                    }

                } catch (\Exception $e) {
                    // Date parsing error will be caught by basic date validation
                }
            }

            // Validate working hours (jam mulai harus dalam jam kerja)
            if ($this->filled($prefix.'jam_mulai')) {
                try {
                    $jamMulai = Carbon::createFromFormat('H:i', $this->input($prefix.'jam_mulai'));
                    $jamKerjaStart = Carbon::createFromFormat('H:i', '07:00');
                    $jamKerjaEnd = Carbon::createFromFormat('H:i', '18:00');

                    if ($jamMulai->lt($jamKerjaStart) || $jamMulai->gt($jamKerjaEnd)) {
                        $validator->errors()->add($prefix.'jam_mulai',
                            'Jam mulai harus dalam rentang jam kerja (07:00 - 18:00).'
                        );
                    }
                } catch (\Exception $e) {
                    // Time parsing error will be caught by basic format validation
                }
            }

            // Validate total pertemuan vs date range consistency
            if ($this->filled($prefix.'total_pertemuan') && 
                $this->filled($prefix.'tanggal_mulai') && 
                $this->filled($prefix.'tanggal_selesai')) {
                
                try {
                    $totalPertemuan = (int) $this->input($prefix.'total_pertemuan');
                    $startDate = Carbon::parse($this->input($prefix.'tanggal_mulai'));
                    $endDate = Carbon::parse($this->input($prefix.'tanggal_selesai'));
                    
                    $totalWeeks = $endDate->diffInWeeks($startDate) + 1;
                    
                    if ($totalPertemuan > $totalWeeks) {
                        $validator->errors()->add($prefix.'total_pertemuan',
                            "Total pertemuan ({$totalPertemuan}) tidak bisa lebih dari jumlah minggu dalam periode ({$totalWeeks} minggu)."
                        );
                    }
                } catch (\Exception $e) {
                    // Calculation error - let it pass, other validations will catch data issues
                }
            }
        });
    }

    /**
     * Get rombel number from current step
     */
    protected function getRombelNumber(): int
    {
        $currentStep = (int) $this->input('current_step', 5);
        return $currentStep - 4; // Step 5 = Rombel 1, Step 6 = Rombel 2, etc.
    }

    /**
     * Get the hari options with proper labels
     */
    public static function getHariOptions(): array
    {
        return [
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            'sabtu' => 'Sabtu',
            'minggu' => 'Minggu',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $rombelNumber = $this->getRombelNumber();
        $prefix = "rombel_{$rombelNumber}_";

        // Ensure integer fields are properly cast
        $intFields = [$prefix.'total_pertemuan', $prefix.'jumlah_siswa'];
        $intData = [];
        
        foreach ($intFields as $field) {
            if ($this->has($field)) {
                $intData[$field] = is_numeric($this->input($field)) ? (int) $this->input($field) : $this->input($field);
            }
        }
        
        if (!empty($intData)) {
            $this->merge($intData);
        }

        // Trim text fields
        $textFields = [$prefix.'ruangan', $prefix.'keterangan_ruangan'];
        $trimmedData = [];
        
        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $trimmedData[$field] = trim($this->input($field));
            }
        }
        
        if (!empty($trimmedData)) {
            $this->merge($trimmedData);
        }

        // Normalize hari to lowercase
        if ($this->has($prefix.'hari')) {
            $this->merge([
                $prefix.'hari' => strtolower($this->input($prefix.'hari')),
            ]);
        }
    }
}