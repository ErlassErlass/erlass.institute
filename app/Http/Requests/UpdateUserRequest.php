<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller via Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$userId],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'no_telephone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
            'agama' => ['nullable', 'string', 'max:50'],
            'pend_terakhir' => ['nullable', 'string', 'max:10'],
            'kompetensi_1' => ['nullable', 'string', 'in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris'],
            'kompetensi_2' => ['nullable', 'string', 'in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris'],
            'role' => ['required', 'in:webmaster,admin_sistem,admin,instruktur'],

            // Field untuk sistem verifikasi instruktur
            'is_verified' => ['boolean'],
            'verification_status' => ['nullable', 'in:pending,approved,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],

            // Field baru untuk tanggal aktif/nonaktif dan domisili
            'tanggal_aktif' => ['nullable', 'date'],
            'tanggal_nonaktif' => ['nullable', 'date', 'after_or_equal:tanggal_aktif'],
            'alamat_domisili' => ['nullable', 'string'],
            'kota_domisili' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'no_telephone.regex' => 'Format nomor telepon tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status hanya boleh Aktif atau Nonaktif.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'verification_status.in' => 'Status verifikasi tidak valid.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_lengkap' => 'nama lengkap',
            'tanggal_lahir' => 'tanggal lahir',
            'no_telephone' => 'nomor telepon',
            'pend_terakhir' => 'pendidikan terakhir',
            'is_verified' => 'status verifikasi',
            'verification_status' => 'status verifikasi',
            'rejection_reason' => 'alasan penolakan',
        ];
    }
}
