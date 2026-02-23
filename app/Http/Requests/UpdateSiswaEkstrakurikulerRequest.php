<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiswaEkstrakurikulerRequest extends FormRequest
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
        $enrollment = $this->route('enrollment');

        return [
            'ekstrakurikuler_rombel_id' => [
                'required',
                'integer',
                'exists:ekstrakurikuler_rombel,id',
                // Validasi bahwa rombel adalah milik ekstrakurikuler yang benar
                Rule::exists('ekstrakurikuler_rombel', 'id')->where(function ($query) {
                    $query->where('ekstrakurikuler_id', $this->route('ekstrakurikuler')->id);
                }),
            ],
            'status' => [
                'required',
                'string',
                Rule::in(['aktif', 'lulus', 'keluar', 'pindah', 'nonaktif']),
            ],
            'tanggal_keluar' => [
                'nullable',
                'date',
                'required_if:status,lulus,keluar',
                'before_or_equal:today',
                'after_or_equal:'.($enrollment ? $enrollment->tanggal_daftar->format('Y-m-d') : now()->subYears(2)->format('Y-m-d')),
            ],
            'alasan_keluar' => [
                'nullable',
                'string',
                'max:1000',
                'required_if:status,keluar',
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
            'ekstrakurikuler_rombel_id.required' => 'Rombel ekstrakurikuler wajib dipilih.',
            'ekstrakurikuler_rombel_id.exists' => 'Rombel yang dipilih tidak valid atau tidak tersedia.',

            'status.required' => 'Status enrollment wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',

            'tanggal_keluar.date' => 'Format tanggal keluar tidak valid.',
            'tanggal_keluar.required_if' => 'Tanggal keluar wajib diisi untuk status lulus atau keluar.',
            'tanggal_keluar.before_or_equal' => 'Tanggal keluar tidak boleh di masa depan.',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar tidak boleh sebelum tanggal pendaftaran.',

            'alasan_keluar.required_if' => 'Alasan keluar wajib diisi untuk status keluar.',
            'alasan_keluar.max' => 'Alasan keluar tidak boleh lebih dari 1000 karakter.',

            'catatan.max' => 'Catatan tidak boleh lebih dari 1000 karakter.',
        ];
    }

    /**
     * Konfigurasi error bag untuk request ini.
     */
    public function errorBag(): string
    {
        return 'enrollment_update';
    }

    /**
     * Persiapan data sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        // Normalisasi tanggal jika perlu
        if ($this->has('tanggal_keluar') && is_string($this->tanggal_keluar)) {
            try {
                $normalizedDate = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_keluar)->format('Y-m-d');
                $this->merge(['tanggal_keluar' => $normalizedDate]);
            } catch (\Exception $e) {
                // Biarkan validator yang menangani jika format salah
            }
        }

        // Reset tanggal_keluar dan alasan_keluar jika status aktif atau nonaktif
        if (in_array($this->status, ['aktif', 'nonaktif'])) {
            $this->merge([
                'tanggal_keluar' => null,
                'alasan_keluar' => null,
            ]);
        }
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $enrollment = $this->route('enrollment');

            // Validasi perubahan rombel - cek kapasitas rombel baru
            if ($this->filled('ekstrakurikuler_rombel_id') &&
                $enrollment &&
                $this->ekstrakurikuler_rombel_id != $enrollment->ekstrakurikuler_rombel_id) {

                $newRombel = \App\Models\EkstrakurikulerRombel::find($this->ekstrakurikuler_rombel_id);

                if ($newRombel) {
                    $currentEnrollments = $newRombel->activeEnrollments()
                        ->where('id', '!=', $enrollment->id) // Exclude current enrollment
                        ->count();
                    $maxCapacity = $newRombel->jumlah_siswa;

                    if (($currentEnrollments + 1) > $maxCapacity) {
                        $validator->errors()->add(
                            'ekstrakurikuler_rombel_id',
                            "Rombel tujuan sudah penuh (kapasitas maksimal: {$maxCapacity}, saat ini: {$currentEnrollments})."
                        );
                    }
                }
            }

            // Validasi logika status
            if ($enrollment) {
                $currentStatus = $enrollment->status;
                $newStatus = $this->status;

                // Tidak bisa mengubah dari lulus ke status lain
                if ($currentStatus === 'lulus' && $newStatus !== 'lulus') {
                    $validator->errors()->add(
                        'status',
                        'Status siswa yang sudah lulus tidak dapat diubah.'
                    );
                }

                // Tidak bisa mengubah dari keluar ke aktif (harus didaftarkan ulang)
                if ($currentStatus === 'keluar' && $newStatus === 'aktif') {
                    $validator->errors()->add(
                        'status',
                        'Siswa yang sudah keluar tidak dapat diaktifkan kembali. Lakukan pendaftaran ulang jika diperlukan.'
                    );
                }
            }

            // Validasi tanggal keluar harus setelah tanggal daftar
            if ($this->filled('tanggal_keluar') && $enrollment) {
                $tanggalKeluar = \Carbon\Carbon::parse($this->tanggal_keluar);
                $tanggalDaftar = $enrollment->tanggal_daftar;

                if ($tanggalKeluar->lt($tanggalDaftar)) {
                    $validator->errors()->add(
                        'tanggal_keluar',
                        'Tanggal keluar tidak boleh sebelum tanggal pendaftaran ('.$tanggalDaftar->format('d/m/Y').').'
                    );
                }
            }
        });
    }
}
