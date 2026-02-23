<?php

namespace App\Http\Requests\Ekstrakurikuler;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk validasi API request get sekolah by city
 */
class GetSekolahByCityRequest extends FormRequest
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
            'kota' => 'required|string|min:3|max:100',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'kota' => 'nama kota',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'kota.required' => 'Parameter kota diperlukan untuk mengambil data sekolah.',
            'kota.string' => 'Nama kota harus berupa teks.',
            'kota.min' => 'Nama kota minimal 3 karakter.',
            'kota.max' => 'Nama kota tidak boleh lebih dari 100 karakter.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize kota name
        if ($this->has('kota')) {
            $this->merge([
                'kota' => strtoupper(trim($this->kota)),
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Since this is an API request, always return JSON response
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => [],
            ], 422)
        );
    }
}