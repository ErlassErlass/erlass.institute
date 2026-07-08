<?php

namespace App\Http\Requests;

use App\Models\Ekstrakurikuler;
use Illuminate\Foundation\Http\FormRequest;

class StoreEkstrakurikulerRequest extends FormRequest
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
            // Basic Information
            'kategori_program' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'user_id_sales' => 'required|exists:salesmen,id',
            'region' => 'nullable|string|in:JAKARTA,DEPOK,BOGOR,TANGERANG,BEKASI',
            'city' => 'nullable|string|max:255',
            'status' => 'required|string|in:'.implode(',', [
                Ekstrakurikuler::STATUS_DRAFT,
                Ekstrakurikuler::STATUS_DIAJUKAN,
            ]),

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
            'keterangan_kabel' => 'nullable|string|max:500',

            // Class Structure
            'total_siswa' => 'required|integer|min:1|max:500',
            'total_ruangan' => 'required|integer|min:1|max:50',
            'total_rombel' => 'required|integer|min:1|max:5',
            'tanggal_mulai' => 'nullable|date|after_or_equal:today',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'total_pertemuan' => 'nullable|integer|min:1|max:200',
            'frekuensi' => 'nullable|string|in:harian,mingguan,dua_minggu,bulanan',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kategori_program' => 'kategori program',
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
            'keterangan_kabel' => 'keterangan kabel',
            'total_siswa' => 'total siswa',
            'total_ruangan' => 'total ruangan',
            'total_rombel' => 'total rombongan belajar',
            'tanggal_mulai' => 'tanggal mulai',
            'tanggal_selesai' => 'tanggal selesai',
            'total_pertemuan' => 'total pertemuan',
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

            'region.in' => 'Region harus salah satu dari: Jakarta, Depok, Bogor, Tangerang, atau Bekasi.',
            'city.max' => 'Nama kota tidak boleh lebih dari 255 karakter.',

            'status.required' => 'Status program wajib dipilih.',
            'status.in' => 'Status harus Draft atau Diajukan.',

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

            'total_siswa.required' => 'Total siswa wajib diisi.',
            'total_siswa.integer' => 'Total siswa harus berupa angka.',
            'total_siswa.min' => 'Total siswa minimal 1 orang.',
            'total_siswa.max' => 'Total siswa maksimal 500 orang.',

            'total_ruangan.required' => 'Total ruangan wajib diisi.',
            'total_ruangan.integer' => 'Total ruangan harus berupa angka.',
            'total_ruangan.min' => 'Total ruangan minimal 1.',
            'total_ruangan.max' => 'Total ruangan maksimal 50.',

            'total_rombel.required' => 'Total rombongan belajar wajib diisi.',
            'total_rombel.integer' => 'Total rombel harus berupa angka.',
            'total_rombel.min' => 'Total rombel minimal 1.',
            'total_rombel.max' => 'Total rombel maksimal 5.',

            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',

            'total_pertemuan.integer' => 'Total pertemuan harus berupa angka.',
            'total_pertemuan.min' => 'Total pertemuan minimal 1.',
            'total_pertemuan.max' => 'Total pertemuan maksimal 200.',

            'frekuensi.in' => 'Frekuensi harus salah satu dari: harian, mingguan, dua minggu, atau bulanan.',
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

        // Ensure jarak_km is properly formatted
        if ($this->has('jarak_km')) {
            $this->merge([
                'jarak_km' => is_numeric($this->jarak_km) ? (float) $this->jarak_km : $this->jarak_km,
            ]);
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
