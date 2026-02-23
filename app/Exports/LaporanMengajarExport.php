<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanMengajarExport implements FromCollection, WithHeadings, WithMapping
{
    protected $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function collection()
    {
        return $this->laporan;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Instruktur',
            'Asisten',
            'Sekolah',
            'Kecamatan',
            'Kota/Kab',
            'Rombel',
            'Kategori',
            'Jam Mulai',
            'Jam Selesai',
            'Materi',
            'Siswa Hadir',
            'Siswa Keluar',
            'Refleksi Siswa',
            'Refleksi Capaian',
            'Keaktifan',
            'Pemahaman Materi',
        ];
    }

    public function map($laporan): array
    {
        return [
            \Carbon\Carbon::parse($laporan->jadwal_mengajar)->format('d/m/Y'),
            $laporan->instruktur->nama_lengkap ?? 'N/A',
            $laporan->asisten->nama_lengkap ?? 'N/A',
            $laporan->sekolah->namasekolah ?? 'N/A',
            $laporan->sekolah->kec ?? 'N/A',
            $laporan->sekolah->kotkab ?? 'N/A',
            $laporan->rombel,
            $laporan->kategori_pengajaran,
            $laporan->jam_mulai,
            $laporan->jam_selesai,
            $laporan->materi_pengajaran,
            $laporan->jumlah_siswa_hadir,
            $laporan->jumlah_siswa_keluar,
            $laporan->refleksi_siswa,
            $laporan->refleksi_capaian,
            $this->mapKeaktifan($laporan->keaktifan),
            $this->mapPemahaman($laporan->pemahaman_materi),
        ];
    }

    private function mapKeaktifan($value)
    {
        $map = [
            'sangat_pasif' => 'Sangat Pasif',
            'pasif' => 'Pasif',
            'aktif' => 'Aktif',
            'sangat_aktif' => 'Sangat Aktif',
        ];

        return $map[$value] ?? $value;
    }

    private function mapPemahaman($value)
    {
        $map = [
            'belum_paham' => 'Belum Paham',
            'sedikit_paham' => 'Sedikit Paham',
            'paham' => 'Paham',
            'sangat_paham' => 'Sangat Paham',
        ];

        return $map[$value] ?? $value;
    }
}
