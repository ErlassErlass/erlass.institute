<?php

namespace App\Http\Requests\Ekstrakurikuler;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk validasi Step 2: School Selection & Details
 */
class CreateEkstrakurikulerStep2Request extends FormRequest
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
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'alamat_lengkap' => 'required|string|min:10|max:500',
            'google_maps_link' => 'nullable|url|max:500',
            'jarak_km' => 'required|numeric|min:0|max:999.99',
            'kepala_sekolah' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'no_telepon' => 'required|string|min:10|max:20',
            'email' => 'nullable|email|max:255',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'sekolah_kodlan' => 'sekolah',
            'alamat_lengkap' => 'alamat lengkap',
            'google_maps_link' => 'link Google Maps',
            'jarak_km' => 'jarak dari POP (km)',
            'kepala_sekolah' => 'nama kepala sekolah',
            'penanggung_jawab' => 'penanggung jawab ekstrakurikuler',
            'no_telepon' => 'nomor telepon',
            'email' => 'email',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'sekolah_kodlan.required' => 'Sekolah wajib dipilih.',
            'sekolah_kodlan.exists' => 'Sekolah yang dipilih tidak valid.',
            
            'alamat_lengkap.required' => 'Alamat lengkap sekolah wajib diisi.',
            'alamat_lengkap.min' => 'Alamat lengkap minimal 10 karakter.',
            'alamat_lengkap.max' => 'Alamat lengkap tidak boleh lebih dari 500 karakter.',
            
            'google_maps_link.url' => 'Link Google Maps harus berupa URL yang valid.',
            'google_maps_link.max' => 'Link Google Maps tidak boleh lebih dari 500 karakter.',
            
            'jarak_km.required' => 'Jarak dari POP wajib diisi.',
            'jarak_km.numeric' => 'Jarak harus berupa angka.',
            'jarak_km.min' => 'Jarak tidak boleh kurang dari 0 km.',
            'jarak_km.max' => 'Jarak tidak boleh lebih dari 999.99 km.',
            
            'kepala_sekolah.required' => 'Nama kepala sekolah wajib diisi.',
            'kepala_sekolah.max' => 'Nama kepala sekolah tidak boleh lebih dari 255 karakter.',
            
            'penanggung_jawab.required' => 'Nama penanggung jawab ekstrakurikuler wajib diisi.',
            'penanggung_jawab.max' => 'Nama penanggung jawab tidak boleh lebih dari 255 karakter.',
            
            'no_telepon.required' => 'Nomor telepon penanggung jawab wajib diisi.',
            'no_telepon.min' => 'Nomor telepon minimal 10 digit.',
            'no_telepon.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean phone number - keep only numbers and +
        if ($this->has('no_telepon')) {
            $cleanPhone = preg_replace('/[^0-9+]/', '', $this->no_telepon);
            $this->merge([
                'no_telepon' => $cleanPhone,
            ]);
        }

        // Ensure jarak_km is properly normalized (comma to dot, strip units)
        if ($this->has('jarak_km') && $this->jarak_km !== null && $this->jarak_km !== '') {
            $raw = str_replace(',', '.', (string) $this->jarak_km);
            if (preg_match('/[0-9]+(?:\.[0-9]+)?/', $raw, $matches)) {
                $this->merge([
                    'jarak_km' => (float) $matches[0],
                ]);
            }
        }

        // Trim text fields
        $textFields = ['alamat_lengkap', 'kepala_sekolah', 'penanggung_jawab'];
        $trimmedData = [];
        
        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $trimmedData[$field] = trim($this->input($field));
            }
        }
        
        if (!empty($trimmedData)) {
            $this->merge($trimmedData);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate phone number format (Indonesian phone numbers)
            if ($this->has('no_telepon')) {
                $phone = $this->no_telepon;
                if (!preg_match('/^(\+62|62|0)[0-9]{8,15}$/', $phone)) {
                    $validator->errors()->add('no_telepon', 
                        'Format nomor telepon tidak valid. Gunakan format Indonesia (contoh: 081234567890 atau +6281234567890).'
                    );
                }
            }

            // Validate Google Maps URL if provided
            if ($this->filled('google_maps_link')) {
                $url = $this->google_maps_link;
                if (!str_contains($url, 'google.com/maps') && !str_contains($url, 'maps.google.com') && !str_contains($url, 'goo.gl/maps')) {
                    $validator->errors()->add('google_maps_link', 
                        'Link Google Maps tidak valid. Gunakan URL dari Google Maps.'
                    );
                }
            }
        });
    }
}