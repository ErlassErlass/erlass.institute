<?php

namespace App\Http\Requests\Ekstrakurikuler;

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
        return [
            'kategori_program' => [
                'required',
                'string',
                'in:Coding Scratch,English Course,Micro:bit Learning Kit,Pictoblox AI,Robotik Explorer,Robotik Jimu'
            ],
            'user_id_sales' => 'required|exists:users,id',
            'region' => 'nullable|string|in:JAKARTA,DEPOK,BOGOR,TANGERANG,BEKASI',
            'city' => 'nullable|string|max:255',
            'status' => 'required|string|in:draft,diajukan',
            'deskripsi' => 'nullable|string|max:1000',
        ];
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
            'status' => 'status',
            'deskripsi' => 'deskripsi program',
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
            'status.required' => 'Status program wajib dipilih.',
            'status.in' => 'Status harus draft atau diajukan.',
            'deskripsi.max' => 'Deskripsi tidak boleh lebih dari 1000 karakter.',
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

        // Set nama_program for compatibility
        if ($this->has('kategori_program')) {
            $this->merge([
                'nama_program' => $this->kategori_program,
            ]);
        }
    }
}