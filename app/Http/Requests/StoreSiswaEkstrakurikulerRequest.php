<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSiswaEkstrakurikulerRequest extends FormRequest
{
    /**
     * Tentukan apakah user diotorisasi untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('ekstrakurikuler'));
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
     */
    public function rules(): array
    {
        return [
            'siswa_ids' => [
                'required',
                'array',
                'min:1',
                'max:50', // Batasi max 50 siswa per batch
            ],
            'siswa_ids.*' => [
                'required',
                'integer',
                'exists:siswa,id',
            ],
            'ekstrakurikuler_rombel_id' => [
                'required',
                'integer',
                'exists:ekstrakurikuler_rombel,id',
                // Validasi bahwa rombel adalah milik ekstrakurikuler yang benar
                Rule::exists('ekstrakurikuler_rombel', 'id')->where(function ($query) {
                    $query->where('ekstrakurikuler_id', $this->route('ekstrakurikuler')->id);
                }),
            ],
            'tanggal_daftar' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:'.now()->subYears(2)->format('Y-m-d'), // Max 2 tahun yang lalu
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Pesan error kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'siswa_ids.required' => 'Pilih minimal satu siswa untuk didaftarkan.',
            'siswa_ids.array' => 'Data siswa tidak valid.',
            'siswa_ids.min' => 'Pilih minimal satu siswa.',
            'siswa_ids.max' => 'Maksimal 50 siswa dapat didaftarkan dalam satu kali proses.',
            'siswa_ids.*.exists' => 'Salah satu siswa yang dipilih tidak valid.',

            'ekstrakurikuler_rombel_id.required' => 'Rombel ekstrakurikuler wajib dipilih.',
            'ekstrakurikuler_rombel_id.exists' => 'Rombel yang dipilih tidak valid atau tidak tersedia.',

            'tanggal_daftar.required' => 'Tanggal pendaftaran wajib diisi.',
            'tanggal_daftar.date' => 'Format tanggal pendaftaran tidak valid.',
            'tanggal_daftar.before_or_equal' => 'Tanggal pendaftaran tidak boleh di masa depan.',
            'tanggal_daftar.after_or_equal' => 'Tanggal pendaftaran terlalu lama (maksimal 2 tahun yang lalu).',

            'catatan.max' => 'Catatan tidak boleh lebih dari 1000 karakter.',
        ];
    }

    /**
     * Konfigurasi error bag untuk request ini.
     */
    public function errorBag(): string
    {
        return 'enrollment';
    }

    /**
     * Persiapan data sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        // Pastikan siswa_ids adalah array
        if ($this->has('siswa_ids') && ! is_array($this->siswa_ids)) {
            $this->merge([
                'siswa_ids' => [$this->siswa_ids],
            ]);
        }

        // Normalisasi tanggal jika perlu
        if ($this->has('tanggal_daftar') && is_string($this->tanggal_daftar)) {
            try {
                $normalizedDate = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_daftar)->format('Y-m-d');
                $this->merge(['tanggal_daftar' => $normalizedDate]);
            } catch (\Exception $e) {
                // Biarkan validator yang menangani jika format salah
            }
        }
    }

    /**
     * Dapatkan data yang sudah divalidasi dengan transformasi tambahan.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Pastikan siswa_ids adalah array integer
        if (isset($validated['siswa_ids'])) {
            $validated['siswa_ids'] = array_map('intval', array_unique($validated['siswa_ids']));
        }

        return $validated;
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validasi kapasitas rombel
            if ($this->filled('ekstrakurikuler_rombel_id') && $this->filled('siswa_ids')) {
                $rombel = \App\Models\EkstrakurikulerRombel::find($this->ekstrakurikuler_rombel_id);

                if ($rombel) {
                    $currentEnrollments = $rombel->activeEnrollments()->count();
                    $newEnrollments = count($this->siswa_ids);
                    $maxCapacity = $rombel->jumlah_siswa;

                    if (($currentEnrollments + $newEnrollments) > $maxCapacity) {
                        $available = max(0, $maxCapacity - $currentEnrollments);
                        $validator->errors()->add(
                            'siswa_ids',
                            "Rombel hanya dapat menampung {$available} siswa lagi (kapasitas maksimal: {$maxCapacity}, saat ini: {$currentEnrollments})."
                        );
                    }
                }
            }

            // Validasi siswa dari sekolah yang sama
            if ($this->filled('siswa_ids')) {
                $ekstrakurikuler = $this->route('ekstrakurikuler');
                $invalidSiswa = \App\Models\Siswa::whereIn('id', $this->siswa_ids)
                    ->where('sekolah_kodlan', '!=', $ekstrakurikuler->sekolah_kodlan)
                    ->exists();

                if ($invalidSiswa) {
                    $validator->errors()->add(
                        'siswa_ids',
                        'Semua siswa harus berasal dari sekolah yang sama dengan program ekstrakurikuler.'
                    );
                }
            }

            // Validasi siswa yang sudah terdaftar
            if ($this->filled('siswa_ids')) {
                $ekstrakurikuler = $this->route('ekstrakurikuler');
                $alreadyEnrolled = \App\Models\SiswaEkstrakurikuler::whereIn('siswa_id', $this->siswa_ids)
                    ->where('ekstrakurikuler_id', $ekstrakurikuler->id)
                    ->where('status', '!=', 'keluar')
                    ->with('siswa')
                    ->get();

                if ($alreadyEnrolled->isNotEmpty()) {
                    $names = $alreadyEnrolled->pluck('siswa.nama_lengkap')->implode(', ');
                    $validator->errors()->add(
                        'siswa_ids',
                        "Siswa berikut sudah terdaftar dalam program ini: {$names}"
                    );
                }
            }
        });
    }
}
