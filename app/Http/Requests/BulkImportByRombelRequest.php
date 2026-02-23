<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkImportByRombelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rombel' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    // Validate that rombel exists in the school
                    $ekstrakurikuler = $this->route('ekstrakurikuler');
                    $rombelExists = \App\Models\Siswa::where('sekolah_kodlan', $ekstrakurikuler->sekolah_kodlan)
                        ->where('rombel', $value)
                        ->exists();

                    if (! $rombelExists) {
                        $fail('Rombel yang dipilih tidak ditemukan di sekolah ini.');
                    }
                },
            ],
            'ekstrakurikuler_rombel_id' => [
                'required',
                'exists:ekstrakurikuler_rombel,id',
                function ($attribute, $value, $fail) {
                    // Validate that rombel belongs to this ekstrakurikuler
                    $ekstrakurikuler = $this->route('ekstrakurikuler');
                    $rombelBelongs = \App\Models\EkstrakurikulerRombel::where('id', $value)
                        ->where('ekstrakurikuler_id', $ekstrakurikuler->id)
                        ->exists();

                    if (! $rombelBelongs) {
                        $fail('Rombel ekstrakurikuler yang dipilih tidak valid untuk program ini.');
                    }
                },
            ],
            'tanggal_daftar' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:'.now()->subYear()->format('Y-m-d'), // Not older than 1 year
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'rombel.required' => 'Rombel wajib dipilih.',
            'rombel.max' => 'Nama rombel tidak boleh lebih dari 50 karakter.',
            'ekstrakurikuler_rombel_id.required' => 'Rombel ekstrakurikuler wajib dipilih.',
            'ekstrakurikuler_rombel_id.exists' => 'Rombel ekstrakurikuler yang dipilih tidak valid.',
            'tanggal_daftar.required' => 'Tanggal pendaftaran wajib diisi.',
            'tanggal_daftar.date' => 'Format tanggal pendaftaran tidak valid.',
            'tanggal_daftar.before_or_equal' => 'Tanggal pendaftaran tidak boleh lebih dari hari ini.',
            'tanggal_daftar.after_or_equal' => 'Tanggal pendaftaran tidak boleh lebih dari 1 tahun yang lalu.',
            'catatan.max' => 'Catatan tidak boleh lebih dari 1000 karakter.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'rombel' => 'rombel',
            'ekstrakurikuler_rombel_id' => 'rombel ekstrakurikuler',
            'tanggal_daftar' => 'tanggal pendaftaran',
            'catatan' => 'catatan',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(
            redirect()->back()
                ->withInput()
                ->withErrors($errors, 'bulk_import')
                ->with('show_bulk_import_modal', true)
        );
    }
}
