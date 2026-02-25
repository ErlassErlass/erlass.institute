<?php

namespace App\Http\Requests\Ekstrakurikuler;

use App\Models\Ekstrakurikuler;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk validasi Step 1: Basic Program Info
 */
class CreateEkstrakurikulerStep1Request extends FormRequest
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
        $rules = [
            'kategori_program' => [
                'required',
                'string',
                'in:Coding Scratch,English Course,Micro:bit Learning Kit,Pictoblox AI,Robotik Explorer,Robotik Jimu'
            ],
            'user_id_sales' => 'required|exists:users,id',
            'region' => 'nullable|string|in:JAKARTA,DEPOK,BOGOR,TANGERANG,BEKASI',
            'city' => 'nullable|string|max:255',
            'jenis_pembayaran' => 'required|string|in:per_siswa_bulan,per_siswa_semester,per_siswa_tahun,per_pertemuan_instruktur',
            'deskripsi' => 'nullable|string|max:1000',
        ];

        // Conditional equipment validation for robotics/microbit programs
        $kategori = $this->input('kategori_program');
        if (in_array($kategori, Ekstrakurikuler::KATEGORI_BUTUH_ALAT)) {
            $rules['jenis_alat'] = 'required|string|in:per_siswa,per_kelompok';
            
            if ($this->input('jenis_alat') === 'per_kelompok') {
                $rules['jumlah_siswa_per_alat'] = 'required|integer|in:2,3,4,5';
            }
        }

        return $rules;
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'kategori_program' => 'kategori program',
            'user_id_sales' => 'sales/koordinator',
            'region' => 'region',
            'city' => 'kota',
            'jenis_pembayaran' => 'jenis pembayaran',
            'deskripsi' => 'deskripsi program',
            'jenis_alat' => 'jenis alat',
            'jumlah_siswa_per_alat' => 'jumlah siswa per alat',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'kategori_program.required' => 'Kategori program wajib dipilih.',
            'kategori_program.in' => 'Kategori program yang dipilih tidak valid.',
            'user_id_sales.required' => 'Sales/koordinator wajib dipilih.',
            'user_id_sales.exists' => 'Sales/koordinator yang dipilih tidak valid.',
            'region.in' => 'Region harus salah satu dari: Jakarta, Depok, Bogor, Tangerang, atau Bekasi.',
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib dipilih.',
            'jenis_pembayaran.in' => 'Jenis pembayaran yang dipilih tidak valid.',
            'deskripsi.max' => 'Deskripsi tidak boleh lebih dari 1000 karakter.',
            'jenis_alat.required' => 'Jenis alat wajib dipilih untuk program Microbit/Robotik.',
            'jenis_alat.in' => 'Jenis alat yang dipilih tidak valid.',
            'jumlah_siswa_per_alat.required' => 'Jumlah siswa per alat wajib dipilih jika menggunakan alat per kelompok.',
            'jumlah_siswa_per_alat.in' => 'Jumlah siswa per alat harus antara 2-5.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize region case
        if ($this->has('region')) {
            $this->merge([
                'region' => strtoupper($this->region),
            ]);
        }

        // Auto-set status to 'disetujui' (auto-approve)
        $this->merge([
            'status' => Ekstrakurikuler::STATUS_DISETUJUI,
        ]);
    }
}