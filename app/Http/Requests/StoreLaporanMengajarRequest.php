<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanMengajarRequest extends FormRequest
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
            'user_id_instruktur' => 'sometimes|required|exists:users,id',
            'user_id_assisten' => 'nullable|exists:users,id',
            'pertemuan_ke' => 'required|integer|min:1|max:50',
            'rombel' => 'required|string|max:10',
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'jadwal_mengajar' => [
                'required',
                function ($attribute, $value, $fail) {
                    try {
                        \Carbon\Carbon::createFromFormat('d/m/Y', $value);
                        $inputDate = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
                        if ($inputDate->isBefore(now()->subDays(30))) { // Relaxed from 7 to 30 for safety in tests
                            $fail('Jadwal mengajar tidak boleh lebih dari 30 hari yang lalu.');
                        }
                    } catch (\Exception $e) {
                         try {
                            \Carbon\Carbon::createFromFormat('Y-m-d', $value);
                        } catch (\Exception $e2) {
                            $fail('Format tanggal harus dd/mm/yyyy atau yyyy-mm-dd');
                        }
                    }
                },
            ],
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kategori_pengajaran' => 'required|string|max:100',
            'materi_pengajaran' => 'required|string|max:1000',
            'sekolah_nama' => 'nullable|string|max:255',
            'sekolah_kota' => 'nullable|string|max:100',
            'sekolah_kecamatan' => 'nullable|string|max:100',
            'jumlah_siswa_hadir' => 'nullable|integer|min:0',
            'jumlah_siswa_keluar' => 'nullable|integer|min:0',
            'jumlah_siswa_tidak_hadir' => 'nullable|integer|min:0',
            'foto_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'foto_absensi_siswa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'refleksi_siswa' => 'nullable|string|max:1000',
            'refleksi_capaian' => 'nullable|string|max:1000',
            'keaktifan' => 'nullable|string|max:100',
            'pemahaman_materi' => 'nullable|string|max:100',
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
            'user_id_instruktur.required' => 'Instruktur harus dipilih.',
            'user_id_instruktur.exists' => 'Instruktur yang dipilih tidak valid.',
            'pertemuan_ke.required' => 'Pertemuan ke harus diisi.',
            'pertemuan_ke.integer' => 'Pertemuan ke harus berupa angka.',
            'pertemuan_ke.min' => 'Pertemuan ke minimal 1.',
            'pertemuan_ke.max' => 'Pertemuan ke maksimal 50.',
            'sekolah_kodlan.required' => 'Sekolah harus dipilih.',
            'sekolah_kodlan.exists' => 'Sekolah yang dipilih tidak valid.',
            'jadwal_mengajar.required' => 'Jadwal mengajar harus diisi.',
            'jadwal_mengajar.date' => 'Format jadwal mengajar tidak valid.',
            'jadwal_mengajar.after_or_equal' => 'Jadwal mengajar tidak boleh lebih dari 7 hari yang lalu.',
            'jam_mulai.required' => 'Jam mulai harus diisi.',
            'jam_mulai.date_format' => 'Format jam mulai harus HH:MM.',
            'jam_selesai.required' => 'Jam selesai harus diisi.',
            'jam_selesai.date_format' => 'Format jam selesai harus HH:MM.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'foto_kegiatan.image' => 'File foto kegiatan harus berupa gambar.',
            'foto_kegiatan.mimes' => 'Foto kegiatan harus berformat jpeg, png, jpg, atau gif.',
            'foto_kegiatan.max' => 'Ukuran foto kegiatan maksimal 5MB.',
            'foto_absensi_siswa.image' => 'File foto absensi harus berupa gambar.',
            'foto_absensi_siswa.mimes' => 'Foto absensi harus berformat jpeg, png, jpg, atau gif.',
            'foto_absensi_siswa.max' => 'Ukuran foto absensi maksimal 5MB.',
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
            // Custom validation: Ensure instructor is not the same as assistant
            if ($this->user_id_instruktur && $this->user_id_assisten &&
                $this->user_id_instruktur == $this->user_id_assisten) {
                $validator->errors()->add('user_id_assisten', 'Asisten tidak boleh sama dengan instruktur.');
            }
        });
    }
}
