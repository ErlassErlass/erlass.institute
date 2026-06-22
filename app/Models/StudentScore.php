<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentScore extends Model
{
    use HasFactory;

    protected $table = 'student_scores';

    protected $fillable = [
        'siswa_id',
        'ekstrakurikuler_id',
        'ekstrakurikuler_rombel_id',
        
        'nilai_tugas_1', 'nilai_tugas_2', 'nilai_tugas_3', 'nilai_tugas_4',
        'nilai_tugas_5', 'nilai_tugas_6', 'nilai_tugas_7', 'nilai_tugas_8',
        
        'nilai_sikap_1', 'nilai_sikap_2', 'nilai_sikap_3', 'nilai_sikap_4',
        'nilai_sikap_5', 'nilai_sikap_6', 'nilai_sikap_7', 'nilai_sikap_8',
        
        'nilai_proyek_1', 'nilai_proyek_2', 'nilai_proyek_3', 'nilai_proyek_4',
        'nilai_proyek_5', 'nilai_proyek_6', 'nilai_proyek_7', 'nilai_proyek_8',

        'nilai_kehadiran',
        'nilai_tugas',
        'nilai_proyek',
        'nilai_sikap',
        'nilai_akhir',
        
        'catatan_guru',
        'projek_scratch',
        'periode',
        'finalized_at',
        'finalized_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'nilai_tugas_1' => 'float', 'nilai_tugas_2' => 'float', 'nilai_tugas_3' => 'float', 'nilai_tugas_4' => 'float',
        'nilai_tugas_5' => 'float', 'nilai_tugas_6' => 'float', 'nilai_tugas_7' => 'float', 'nilai_tugas_8' => 'float',
        
        'nilai_sikap_1' => 'float', 'nilai_sikap_2' => 'float', 'nilai_sikap_3' => 'float', 'nilai_sikap_4' => 'float',
        'nilai_sikap_5' => 'float', 'nilai_sikap_6' => 'float', 'nilai_sikap_7' => 'float', 'nilai_sikap_8' => 'float',
        
        'nilai_proyek_1' => 'float', 'nilai_proyek_2' => 'float', 'nilai_proyek_3' => 'float', 'nilai_proyek_4' => 'float',
        'nilai_proyek_5' => 'float', 'nilai_proyek_6' => 'float', 'nilai_proyek_7' => 'float', 'nilai_proyek_8' => 'float',

        'nilai_kehadiran' => 'float',
        'nilai_tugas' => 'float',
        'nilai_proyek' => 'float',
        'nilai_sikap' => 'float',
        'nilai_akhir' => 'float',
        'finalized_at' => 'datetime',
    ];

    protected static function booted()
    {
        // Automatically compute averages and overall weighted nilai_akhir on save
        static::saving(function ($score) {
            $average = function ($values) {
                $filtered = array_filter($values, function ($v) {
                    return !is_null($v);
                });
                if (count($filtered) === 0) {
                    return 0;
                }
                return array_sum($filtered) / count($filtered);
            };

            // Calculate averages of existing input columns
            $score->nilai_tugas = round($average([
                $score->nilai_tugas_1, $score->nilai_tugas_2, $score->nilai_tugas_3, $score->nilai_tugas_4,
                $score->nilai_tugas_5, $score->nilai_tugas_6, $score->nilai_tugas_7, $score->nilai_tugas_8,
            ]), 2);

            $score->nilai_sikap = round($average([
                $score->nilai_sikap_1, $score->nilai_sikap_2, $score->nilai_sikap_3, $score->nilai_sikap_4,
                $score->nilai_sikap_5, $score->nilai_sikap_6, $score->nilai_sikap_7, $score->nilai_sikap_8,
            ]), 2);

            $score->nilai_proyek = round($average([
                $score->nilai_proyek_1, $score->nilai_proyek_2, $score->nilai_proyek_3, $score->nilai_proyek_4,
                $score->nilai_proyek_5, $score->nilai_proyek_6, $score->nilai_proyek_7, $score->nilai_proyek_8,
            ]), 2);

            // Compute overall weighted score
            // Kehadiran 30%, Tugas & Kuis 30%, Sikap 20%, Proyek Akhir 20%
            $score->nilai_akhir = round(
                ($score->nilai_kehadiran * 0.3) +
                ($score->nilai_tugas * 0.3) +
                ($score->nilai_sikap * 0.2) +
                ($score->nilai_proyek * 0.2),
                2
            );
        });
    }

    /**
     * Relasi ke model Siswa.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke model Ekstrakurikuler.
     */
    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }

    /**
     * Relasi ke model EkstrakurikulerRombel.
     */
    public function ekstrakurikulerRombel(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerRombel::class, 'ekstrakurikuler_rombel_id');
    }

    /**
     * Relasi ke User yang memfinalisasi nilai.
     */
    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    /**
     * Relasi ke User yang membuat record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang mengupdate record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Hitung nilai kehadiran siswa berdasarkan riwayat absensi.
     */
    public function hitungNilaiKehadiran(): float
    {
        $rombel = $this->ekstrakurikulerRombel;
        if (!$rombel) {
            return 0;
        }

        // Ambil semua sesi selesai di rombel ini
        $completedSessionIds = $rombel->sessions()->where('status', 'selesai')->pluck('id');
        if ($completedSessionIds->isEmpty()) {
            return 0;
        }

        // Ambil semua laporan mengajar untuk sesi-sesi tersebut
        $reportIds = LaporanMengajar::whereIn('ekstrakurikuler_session_id', $completedSessionIds)->pluck('id');
        if ($reportIds->isEmpty()) {
            return 0;
        }

        $totalReports = $reportIds->count();
        
        // Hitung jumlah kehadiran siswa
        $presentCount = Absensi::whereIn('laporan_mengajar_id', $reportIds)
            ->where('siswa_id', $this->siswa_id)
            ->where('status', 'hadir')
            ->count();

        return $totalReports > 0 ? round(($presentCount / $totalReports) * 100, 2) : 0;
    }

    /**
     * Cek apakah semua input nilai dari masing-masing kategori sudah lengkap.
     * Jumlah input dinilai berdasarkan total pertemuan (kontrak), maksimal 8.
     */
    public function isComplete(): bool
    {
        $limit = min(8, $this->ekstrakurikulerRombel->total_pertemuan ?? 4);

        for ($i = 1; $i <= $limit; $i++) {
            if (is_null($this->{'nilai_tugas_' . $i}) || 
                is_null($this->{'nilai_sikap_' . $i}) || 
                is_null($this->{'nilai_proyek_' . $i})) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tentukan Predikat Nilai berdasarkan skala.
     */
    public function getPredikat(): string
    {
        $score = $this->nilai_akhir;
        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'B+';
        if ($score >= 75) return 'B';
        return 'C';
    }

    /**
     * Tentukan Keterangan/Label Predikat.
     */
    public function getKeterangan(): string
    {
        $pred = $this->getPredikat();
        return [
            'A+' => 'Luar Biasa',
            'A' => 'Sangat Baik',
            'B+' => 'Baik',
            'B' => 'Cukup',
            'C' => 'Perlu Pendampingan',
        ][$pred] ?? '';
    }

    /**
     * Menghasilkan array nilai kompetensi secara otomatis untuk rapor & sertifikat.
     */
    public function generateCompetencies(): array
    {
        $scale = function ($score) {
            if ($score >= 90) return ['pred' => 'A+', 'label' => 'Luar Biasa'];
            if ($score >= 85) return ['pred' => 'A', 'label' => 'Sangat Baik'];
            if ($score >= 80) return ['pred' => 'B+', 'label' => 'Baik'];
            if ($score >= 75) return ['pred' => 'B', 'label' => 'Cukup'];
            return ['pred' => 'C', 'label' => 'Perlu Pendampingan'];
        };

        $coding = $scale($this->nilai_tugas);
        $userInteraction = $scale($this->nilai_tugas);
        $graphicDesign = $scale($this->nilai_proyek);
        $dataHandling = $scale($this->nilai_tugas);

        $codingDesc = [
            'A+' => 'Sangat luar biasa dalam merancang algoritma dan mengimplementasikan struktur logika kode yang kompleks.',
            'A' => 'Sangat baik dalam memahami logika pemrograman dan pembuatan algoritma dasar menggunakan Scratch.',
            'B+' => 'Baik dalam memahami dan merancang logika pemrograman dasar dengan blok kode visual.',
            'B' => 'Cukup mampu menggunakan logika dasar pemrograman Scratch dengan beberapa bantuan.',
            'C' => 'Perlu pendampingan lebih lanjut dalam memahami logika pemrograman dasar menggunakan Scratch.'
        ][$coding['pred']];

        $uiDesc = [
            'A+' => 'Sangat luar biasa dalam merancang event handler, deteksi tombol, dan membuat interaksi pengguna yang responsif.',
            'A' => 'Sangat kreatif dalam merancang event handler, deteksi tombol, dan interaksi pengguna sederhana.',
            'B+' => 'Baik dalam menggunakan block event and sensing untuk merancang interaksi pengguna.',
            'B' => 'Cukup dalam merancang interaksi pengguna sederhana dengan keyboard dan mouse.',
            'C' => 'Perlu latihan tambahan dalam menggunakan block event dan interaksi pengguna sederhana.'
        ][$userInteraction['pred']];

        $graphicDesc = [
            'A+' => 'Desain visual game luar biasa dengan animasi sprite yang halus, backdrop serasi, dan detail grafis yang tinggi.',
            'A' => 'Sangat baik dalam merancang kostum, backdrop, animasi gerakan sprite, dan estetika visual game.',
            'B+' => 'Baik dalam menata sprite, backdrop, dan membuat gerakan animasi dasar secara proporsional.',
            'B' => 'Cukup dalam memilih backdrop dan sprite yang sesuai dengan tema proyek Scratch.',
            'C' => 'Perlu bimbingan dalam aspek estetika visual, animasi, dan penataan sprite.'
        ][$graphicDesign['pred']];

        $dataDesc = [
            'A+' => 'Sangat luar biasa dalam memanipulasi variabel, operator logika, dan penanganan struktur data Scratch.',
            'A' => 'Sangat mahir menggunakan variabel, operator matematika, dan manipulasi data di Scratch.',
            'B+' => 'Baik dalam memahami penggunaan variabel untuk menyimpan skor atau kondisi permainan.',
            'B' => 'Cukup memahami konsep variabel sederhana untuk kebutuhan proyek Scratch dasar.',
            'C' => 'Perlu bimbingan dalam memahami penggunaan variabel dan operator pengolahan data.'
        ][$dataHandling['pred']];

        return [
            [
                'kompetensi' => 'CODING',
                'nilai' => $this->nilai_tugas,
                'pred' => $coding['pred'],
                'pred_label' => $coding['label'],
                'deskripsi' => $codingDesc
            ],
            [
                'kompetensi' => 'USER INTERACTION',
                'nilai' => $this->nilai_tugas,
                'pred' => $userInteraction['pred'],
                'pred_label' => $userInteraction['label'],
                'deskripsi' => $uiDesc
            ],
            [
                'kompetensi' => 'GRAPHIC AND DESIGN',
                'nilai' => $this->nilai_proyek,
                'pred' => $graphicDesign['pred'],
                'pred_label' => $graphicDesign['label'],
                'deskripsi' => $graphicDesc
            ],
            [
                'kompetensi' => 'DATA HANDLING',
                'nilai' => $this->nilai_tugas,
                'pred' => $dataHandling['pred'],
                'pred_label' => $dataHandling['label'],
                'deskripsi' => $dataDesc
            ],
        ];
    }
}
