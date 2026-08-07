<?php

namespace App\Http\Requests;

use App\Models\Ekstrakurikuler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEkstrakurikulerRequest extends FormRequest
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
        $ekstrakurikuler = $this->route('ekstrakurikuler');

        return [
            // Basic Information
            'kategori_program' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'user_id_sales' => 'required|exists:salesmen,id',
            'region' => 'nullable|string|in:JAKARTA,DEPOK,BOGOR,TANGERANG,BEKASI',
            'city' => 'nullable|string|max:255',
            'status' => [
                'required',
                'string',
                Rule::in($this->getAllowedStatuses($ekstrakurikuler)),
            ],

            // School Information
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'alamat_lengkap' => 'required|string',
            'google_maps_link' => 'nullable|url|max:500',
            'jarak_km' => 'required|numeric|min:0|max:999.99',
            'kepala_sekolah' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',

            // Technical Requirements
            'koneksi_internet' => 'required|string|in:ada,tidak_ada,tidak_diketahui',
            'keterangan_internet' => 'nullable|string|max:500',
            'proyektor' => 'required|string|in:ada,tidak_ada,tidak_diketahui',
            'keterangan_proyektor' => 'nullable|string|max:500',
            'kabel_hdmi' => 'required|string|in:ada,tidak_ada,tidak_diketahui',
            'kabel_vga' => 'required|string|in:ada,tidak_ada,tidak_diketahui',
            'kabel_roll' => 'required|string|in:ada,tidak_ada,tidak_diketahui',
            'keterangan_kabel' => 'nullable|string|max:500',

            // Rombel data (if updating rombel information)
            'rombel' => 'sometimes|array',
            'rombel.*.jumlah_siswa' => 'required_with:rombel|integer|min:1|max:50',
            'rombel.*.total_pertemuan' => 'required_with:rombel|integer|min:1|max:50',
            'rombel.*.ruangan' => 'nullable|string|max:100',
            'rombel.*.keterangan_ruangan' => 'nullable|string|max:255',
            'rombel.*.hari' => 'required_with:rombel|string|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'rombel.*.jam_mulai' => 'required_with:rombel|date_format:H:i',
            'rombel.*.tanggal_mulai' => 'required_with:rombel|date',
            'rombel.*.tanggal_selesai' => 'required_with:rombel|date|after:rombel.*.tanggal_mulai',
        ];
    }

    /**
     * Get allowed statuses based on current status and user role.
     */
    private function getAllowedStatuses($ekstrakurikuler): array
    {
        $user = auth()->user();
        $currentStatus = $ekstrakurikuler->status;

        // Admin and webmaster can change to any status
        if (in_array($user->role, ['admin', 'webmaster'])) {
            return [
                Ekstrakurikuler::STATUS_DRAFT,
                Ekstrakurikuler::STATUS_DIAJUKAN,
                Ekstrakurikuler::STATUS_DISETUJUI,
                Ekstrakurikuler::STATUS_DITOLAK,
                Ekstrakurikuler::STATUS_AKTIF,
                Ekstrakurikuler::STATUS_SELESAI,
                Ekstrakurikuler::STATUS_DIBATALKAN,
            ];
        }

        // Sales/instructor can only change certain statuses
        switch ($currentStatus) {
            case Ekstrakurikuler::STATUS_DRAFT:
                return [
                    Ekstrakurikuler::STATUS_DRAFT,
                    Ekstrakurikuler::STATUS_DIAJUKAN,
                ];

            case Ekstrakurikuler::STATUS_DITOLAK:
                return [
                    Ekstrakurikuler::STATUS_DRAFT,
                    Ekstrakurikuler::STATUS_DIAJUKAN,
                ];

            case Ekstrakurikuler::STATUS_DIAJUKAN:
            case Ekstrakurikuler::STATUS_DISETUJUI:
            case Ekstrakurikuler::STATUS_AKTIF:
            case Ekstrakurikuler::STATUS_SELESAI:
            case Ekstrakurikuler::STATUS_DIBATALKAN:
            default:
                return [$currentStatus]; // Can't change
        }
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kategori_program' => 'nama program',
            'user_id_sales' => 'sales/koordinator',
            'sekolah_kodlan' => 'sekolah',
            'alamat_lengkap' => 'alamat lengkap',
            'google_maps_link' => 'link Google Maps',
            'jarak_km' => 'jarak dari POP',
            'kepala_sekolah' => 'nama kepala sekolah',
            'penanggung_jawab' => 'penanggung jawab ekstrakurikuler',
            'no_telepon' => 'nomor telepon',
            'koneksi_internet' => 'koneksi internet',
            'keterangan_internet' => 'keterangan internet',
            'keterangan_proyektor' => 'keterangan proyektor',
            'kabel_hdmi' => 'kabel HDMI',
            'kabel_vga' => 'kabel VGA',
            'kabel_roll' => 'kabel roll',
            'keterangan_kabel' => 'keterangan kabel',

            // Rombel attributes
            'rombel.*.jumlah_siswa' => 'jumlah siswa rombel',
            'rombel.*.total_pertemuan' => 'total pertemuan rombel',
            'rombel.*.ruangan' => 'ruangan rombel',
            'rombel.*.keterangan_ruangan' => 'keterangan ruangan rombel',
            'rombel.*.hari' => 'hari kegiatan rombel',
            'rombel.*.jam_mulai' => 'jam mulai rombel',
            'rombel.*.tanggal_mulai' => 'tanggal mulai rombel',
            'rombel.*.tanggal_selesai' => 'tanggal selesai rombel',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'kategori_program.required' => 'Kategori program ekstrakurikuler wajib diisi.',
            'kategori_program.max' => 'Kategori program tidak boleh lebih dari 255 karakter.',

            'user_id_sales.required' => 'Sales/koordinator wajib dipilih.',
            'user_id_sales.exists' => 'Sales/koordinator yang dipilih tidak valid.',

            'region.required' => 'Region wajib dipilih.',
            'region.in' => 'Region harus salah satu dari: Jakarta, Depok, Bogor, Tangerang, atau Bekasi.',

            'status.required' => 'Status program wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid untuk kondisi saat ini.',

            'sekolah_kodlan.required' => 'Sekolah wajib dipilih.',
            'sekolah_kodlan.exists' => 'Sekolah yang dipilih tidak valid.',

            'alamat_lengkap.required' => 'Alamat lengkap sekolah wajib diisi.',

            'google_maps_link.url' => 'Link Google Maps harus berupa URL yang valid.',

            'jarak_km.required' => 'Jarak dari POP wajib diisi.',
            'jarak_km.numeric' => 'Jarak harus berupa angka.',
            'jarak_km.min' => 'Jarak tidak boleh kurang dari 0 km.',
            'jarak_km.max' => 'Jarak tidak boleh lebih dari 999.99 km.',

            'kepala_sekolah.required' => 'Nama kepala sekolah wajib diisi.',
            'penanggung_jawab.required' => 'Nama penanggung jawab ekstrakurikuler wajib diisi.',
            'no_telepon.required' => 'Nomor telepon penanggung jawab wajib diisi.',

            'email.email' => 'Format email tidak valid.',

            'koneksi_internet.required' => 'Status koneksi internet wajib dipilih.',
            'koneksi_internet.in' => 'Status koneksi internet tidak valid.',

            'proyektor.required' => 'Status proyektor wajib dipilih.',
            'proyektor.in' => 'Status proyektor tidak valid.',

            'kabel_hdmi.required' => 'Status kabel HDMI wajib dipilih.',
            'kabel_hdmi.in' => 'Status kabel HDMI tidak valid.',

            'kabel_vga.required' => 'Status kabel VGA wajib dipilih.',
            'kabel_vga.in' => 'Status kabel VGA tidak valid.',

            'kabel_roll.required' => 'Status kabel roll wajib dipilih.',
            'kabel_roll.in' => 'Status kabel roll tidak valid.',

            // Rombel validation messages
            'rombel.*.jumlah_siswa.required_with' => 'Jumlah siswa rombel wajib diisi.',
            'rombel.*.jumlah_siswa.integer' => 'Jumlah siswa harus berupa angka.',
            'rombel.*.jumlah_siswa.min' => 'Jumlah siswa minimal 1 orang.',
            'rombel.*.jumlah_siswa.max' => 'Jumlah siswa maksimal 50 orang.',

            'rombel.*.total_pertemuan.required_with' => 'Total pertemuan rombel wajib diisi.',
            'rombel.*.total_pertemuan.integer' => 'Total pertemuan harus berupa angka.',
            'rombel.*.total_pertemuan.min' => 'Total pertemuan minimal 1.',
            'rombel.*.total_pertemuan.max' => 'Total pertemuan maksimal 50.',

            'rombel.*.hari.required_with' => 'Hari kegiatan rombel wajib dipilih.',
            'rombel.*.hari.in' => 'Hari kegiatan tidak valid.',

            'rombel.*.jam_mulai.required_with' => 'Jam mulai rombel wajib diisi.',
            'rombel.*.jam_mulai.date_format' => 'Format jam mulai tidak valid (HH:MM).',

            'rombel.*.tanggal_mulai.required_with' => 'Tanggal mulai rombel wajib diisi.',
            'rombel.*.tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',

            'rombel.*.tanggal_selesai.required_with' => 'Tanggal selesai rombel wajib diisi.',
            'rombel.*.tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('no_telepon')) {
            $this->merge([
                'no_telepon' => preg_replace('/[^0-9+]/', '', $this->no_telepon),
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
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $ekstrakurikuler = $this->route('ekstrakurikuler');

            // Additional validation for active programs
            if ($ekstrakurikuler && $ekstrakurikuler->isActive()) {
                $this->validateActiveProgram($validator, $ekstrakurikuler);
            }

            // Validate rombel date consistency
            if ($this->has('rombel')) {
                $this->validateRombelDates($validator);
            }
        });
    }

    /**
     * Validate active program restrictions.
     */
    private function validateActiveProgram($validator, $ekstrakurikuler): void
    {
        $user = auth()->user();

        // Admin, admin_sistem, and webmaster are allowed to change school/critical fields for active programs
        if ($user && in_array($user->role, ['admin', 'admin_sistem', 'webmaster'])) {
            return;
        }

        // Check if critical fields are being changed for an active program by regular users
        $criticalFields = ['sekolah_kodlan', 'total_rombel'];

        foreach ($criticalFields as $field) {
            if ($this->has($field) && $this->input($field) != $ekstrakurikuler->$field) {
                $validator->errors()->add($field,
                    'Field ini tidak dapat diubah untuk program yang sedang aktif.'
                );
            }
        }
    }

    /**
     * Validate rombel dates for consistency.
     */
    private function validateRombelDates($validator): void
    {
        $rombels = $this->input('rombel', []);

        foreach ($rombels as $rombelId => $rombelData) {
            if (isset($rombelData['tanggal_mulai']) && isset($rombelData['tanggal_selesai'])) {
                $startDate = \Carbon\Carbon::parse($rombelData['tanggal_mulai']);
                $endDate = \Carbon\Carbon::parse($rombelData['tanggal_selesai']);

                if ($endDate->lte($startDate)) {
                    $validator->errors()->add("rombel.{$rombelId}.tanggal_selesai",
                        'Tanggal selesai harus setelah tanggal mulai.'
                    );
                }

                // Check if date range is reasonable (not more than 2 years)
                if ($endDate->diffInMonths($startDate) > 24) {
                    $validator->errors()->add("rombel.{$rombelId}.tanggal_selesai",
                        'Periode program tidak boleh lebih dari 2 tahun.'
                    );
                }
            }
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->ajax() || $this->wantsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
