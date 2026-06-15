<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbsensiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policies
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'absensi' => 'required|array|min:1',
            'absensi.*' => 'required|in:1,0,hadir,izin,sakit,alpha',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'absensi.required' => 'Data absensi harus diisi.',
            'absensi.array' => 'Format data absensi tidak valid.',
            'absensi.min' => 'Minimal harus ada 1 siswa yang diabsen.',
            'absensi.*.required' => 'Status kehadiran setiap siswa harus diisi.',
            'absensi.*.in' => 'Status kehadiran tidak valid.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that all student IDs exist
            $absensi = $this->input('absensi');
            if (is_array($absensi)) {
                $siswaIds = array_keys($absensi);

                if (! empty($siswaIds)) {
                    $existingSiswaIds = \App\Models\Siswa::whereIn('id', $siswaIds)->pluck('id')->toArray();
                    $invalidIds = array_diff($siswaIds, $existingSiswaIds);

                    if (! empty($invalidIds)) {
                        $validator->errors()->add('absensi', 'Beberapa ID siswa tidak valid: '.implode(', ', $invalidIds));
                    }
                }
            }
        });
    }
}
