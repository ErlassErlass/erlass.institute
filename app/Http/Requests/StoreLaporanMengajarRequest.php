<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

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
        $allowedKategori = array_unique(array_merge(
            [
                'Backup Pertemuan',
                'Free Trial Class',
                'Trial Class',
                'Inkul Coding Scratch',
                'Inkul LKPD Informatika SD',
                'Inkul LKPD Informatika SMA',
                'Inkul LKPD Informatika SMP',
                'Inkul LMS Koding KA SD',
                'Pameran',
                'Pendampingan Lomba',
                'Sosialisasi bersama Sales',
                'ekstrakurikuler',
                'Ekstrakurikuler',
                'Reguler',
            ],
            \App\Models\RefMateri::distinct()->pluck('kategori')->toArray()
        ));

        return [
            'user_id_instruktur' => 'sometimes|required|integer|exists:users,id',
            'user_id_assisten' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'instruktur');
                }),
            ],
            'pertemuan_ke' => 'required|integer|min:1|max:100',
            'rombel' => 'required|string|max:50',
            'sekolah_kodlan' => 'required|string|exists:sekolah,kodlan',
            'jadwal_mengajar' => [
                'required',
                function ($attribute, $value, $fail) {
                    try {
                        $inputDate = \Carbon\Carbon::parse($value)->startOfDay();
                        if ($inputDate->isBefore(now()->subDays(30)->startOfDay())) {
                            $fail('Tanggal mengajar tidak boleh lebih dari 30 hari yang lalu.');
                        }
                        if ($inputDate->isAfter(now()->endOfDay())) {
                            $fail('Tanggal mengajar tidak boleh di masa depan.');
                        }
                    } catch (\Exception $e) {
                        $fail('Format tanggal tidak valid.');
                    }
                },
            ],
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kategori_pengajaran' => [
                'required',
                'string',
                Rule::in($allowedKategori),
            ],
            'materi_pengajaran' => [
                'required',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    $kategori = $this->input('kategori_pengajaran');
                    if ($kategori && \App\Models\RefMateri::where('kategori', $kategori)->exists()) {
                        $exists = \App\Models\RefMateri::where('kategori', $kategori)
                            ->where('materi', $value)
                            ->exists();
                        if (! $exists) {
                            $fail('Materi pengajaran yang dipilih tidak valid.');
                        }
                    }
                },
            ],
            'sekolah_nama' => 'nullable|string|max:255',
            'sekolah_kota' => 'nullable|string|max:100',
            'sekolah_kecamatan' => 'nullable|string|max:100',
            'jumlah_siswa_hadir' => 'nullable|integer|min:0',
            'jumlah_siswa_keluar' => 'nullable|integer|min:0',
            'jumlah_siswa_tidak_hadir' => 'nullable|integer|min:0',
            'foto_kegiatan' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'foto_absensi_siswa' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'refleksi_siswa' => 'nullable|string|max:1000',
            'refleksi_capaian' => 'nullable|string|max:1000',
            'keaktifan' => [
                'nullable',
                'string',
                Rule::in(['sangat_pasif', 'pasif', 'aktif', 'sangat_aktif']),
            ],
            'pemahaman_materi' => [
                'nullable',
                'string',
                Rule::in(['belum_paham', 'sedikit_paham', 'paham', 'sangat_paham']),
            ],
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
            'user_id_assisten.exists' => 'Asisten instruktur yang dipilih tidak valid.',
            'pertemuan_ke.required' => 'Pertemuan ke harus diisi.',
            'pertemuan_ke.integer' => 'Pertemuan ke harus berupa angka.',
            'pertemuan_ke.min' => 'Pertemuan ke minimal 1.',
            'pertemuan_ke.max' => 'Pertemuan ke maksimal 100.',
            'sekolah_kodlan.required' => 'Sekolah harus dipilih.',
            'sekolah_kodlan.exists' => 'Sekolah yang dipilih tidak valid.',
            'jadwal_mengajar.required' => 'Jadwal mengajar harus diisi.',
            'jam_mulai.required' => 'Jam mulai harus diisi.',
            'jam_mulai.date_format' => 'Format jam mulai harus HH:MM.',
            'jam_selesai.required' => 'Jam selesai harus diisi.',
            'jam_selesai.date_format' => 'Format jam selesai harus HH:MM.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'kategori_pengajaran.in' => 'Kategori pengajaran yang dipilih tidak valid.',
            'keaktifan.in' => 'Pilihan keaktifan kelas tidak valid.',
            'pemahaman_materi.in' => 'Pilihan pemahaman materi tidak valid.',
            'foto_kegiatan.required' => 'Foto kegiatan wajib diunggah.',
            'foto_kegiatan.image' => 'File foto kegiatan harus berupa gambar.',
            'foto_kegiatan.mimes' => 'Foto kegiatan harus berformat jpeg, png, jpg, gif, atau webp.',
            'foto_kegiatan.max' => 'Ukuran foto kegiatan maksimal 5MB.',
            'foto_absensi_siswa.image' => 'File foto absensi harus berupa gambar.',
            'foto_absensi_siswa.mimes' => 'Foto absensi harus berformat jpeg, png, jpg, gif, atau webp.',
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
            // Validasi kustom: Pastikan instruktur tidak sama dengan asisten
            if ($this->user_id_instruktur && $this->user_id_assisten &&
                $this->user_id_instruktur == $this->user_id_assisten) {
                $validator->errors()->add('user_id_assisten', 'Asisten tidak boleh sama dengan instruktur.');
            }

            // Validasi kustom: Durasi mengajar harus antara 60 s.d. 90 menit
            $jamMulai = $this->input('jam_mulai');
            $jamSelesai = $this->input('jam_selesai');
            if ($jamMulai && $jamSelesai) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('H:i', $jamMulai);
                    $end = \Carbon\Carbon::createFromFormat('H:i', $jamSelesai);
                    if ($end < $start) $end->addDay();
                    $diff = $start->diffInMinutes($end);
                    if ($diff < 60) {
                        $validator->errors()->add('jam_selesai', 'Durasi mengajar minimal 60 menit (1 jam).');
                    } elseif ($diff > 180) {
                        $validator->errors()->add('jam_selesai', 'Durasi mengajar maksimal 180 menit (3 jam).');
                    }
                } catch (\Throwable $e) {}
            }
        });
    }

    /**
     * Sanitize validated inputs by stripping HTML/script tags.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if (is_array($validated)) {
            $stringFields = [
                'rombel',
                'materi_pengajaran',
                'refleksi_siswa',
                'refleksi_capaian',
                'sekolah_nama',
                'sekolah_kota',
                'sekolah_kecamatan',
            ];

            foreach ($stringFields as $field) {
                if (isset($validated[$field]) && is_string($validated[$field])) {
                    $validated[$field] = trim(strip_tags($validated[$field]));
                }
            }
        }

        return $validated;
    }
}
