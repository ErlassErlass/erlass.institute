<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InstrukturSeeder extends Seeder
{
    /**
     * Seed real instructor data from Erlass 2025 records.
     *
     * Source: database/data/Data Instruktur Erlass 2025.xlsx
     * Total: 70 instructors
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $instructors = [
            ['nama' => 'LUKY', 'tgl_lahir' => '2000-10-03', 'pendidikan' => 'S1 - Jurusan Pendidikan Matematika - Universitas Indraprasta PGRI', 'no_hp' => '081280268940', 'agama' => 'Islam', 'joined' => '2024-01-15'],
            ['nama' => 'APRILISTIYANI', 'tgl_lahir' => '2000-12-14', 'pendidikan' => 'S1 Jurusan Manajemen Keuangan - Universitas Pamulang', 'no_hp' => '08994466612', 'agama' => 'Islam', 'joined' => '2024-01-15'],
            ['nama' => 'MUHAMAD GILANG ALFAJAR', 'tgl_lahir' => '2001-12-24', 'pendidikan' => 'S1 Sistem Informasi Universitas Nusa Mandiri', 'no_hp' => '08568073791', 'agama' => 'Islam', 'joined' => '2024-01-16'],
            ['nama' => 'ANNASWATUL MARDLIYYAH', 'tgl_lahir' => '2000-09-06', 'pendidikan' => 'S1 Jurusan Pendidikan Bahasa Arab - Universitas Negeri Jakarta', 'no_hp' => '083170058614', 'agama' => 'Islam', 'joined' => '2024-01-17'],
            ['nama' => 'MUHAMMAD RIZALDY HAIKAL', 'tgl_lahir' => '2001-12-14', 'pendidikan' => 'S1 Jurusan Teknik Informatika', 'no_hp' => '087882266818', 'agama' => 'Islam', 'joined' => '2024-01-22'],
            ['nama' => 'ASTRI LESTARI', 'tgl_lahir' => '1996-10-01', 'pendidikan' => 'S1 Jurusan S1 Peternakan - Institut Pertanian Bogor', 'no_hp' => '081292232226', 'agama' => 'Islam', 'joined' => '2024-03-01'],
            ['nama' => 'ADIMAS NUGROHO', 'tgl_lahir' => '2004-10-14', 'pendidikan' => 'SMK PGRI 1 TANGERANG - TEKNIK KOMPUTER & JARINGAN', 'no_hp' => '083877649102', 'agama' => 'Islam', 'joined' => '2024-03-11'],
            ['nama' => 'SITI AMELIA', 'tgl_lahir' => '1999-05-02', 'pendidikan' => 'S1 Manajemen Informatika - STMIK JAKARTA STI&K', 'no_hp' => '083870627543', 'agama' => 'Islam', 'joined' => '2024-04-04'],
            ['nama' => 'HASNA NATASHA KUSUMANINGTYAS', 'tgl_lahir' => '2005-01-24', 'pendidikan' => 'SMK 3 Perguruan "Cikini" Jakarta - Multimedia', 'no_hp' => '085711519485', 'agama' => 'Islam', 'joined' => '2024-04-22'],
            ['nama' => 'MIRSA SETIAWAN', 'tgl_lahir' => '2001-07-02', 'pendidikan' => 'S1 Jurusan Teknik Informatika - Universitas Pamulang', 'no_hp' => '088210803345', 'agama' => 'Islam', 'joined' => '2024-05-01'],
            ['nama' => 'LUTFI ANDI PRATAMA', 'tgl_lahir' => '2002-06-13', 'pendidikan' => 'Jurusan Rekayasa Perangkat Lunak - SMK PUSTEK Serpong', 'no_hp' => '085217299055', 'agama' => 'Islam', 'joined' => '2024-05-07'],
            ['nama' => 'SAIFUL ARIF', 'tgl_lahir' => '1991-04-27', 'pendidikan' => 'S1 Manajemen Informatika (D3) - AMIK BSI Jakarta', 'no_hp' => '082124651459', 'agama' => 'Islam', 'joined' => '2024-05-16'],
            ['nama' => 'DIMAS DWI ANGGARA', 'tgl_lahir' => '2005-07-18', 'pendidikan' => 'Jurusan Rekayasa Perangkat Lunak - SMK PGRI 1 Kota Tangerang', 'no_hp' => '085156651447', 'agama' => 'Islam', 'joined' => '2024-06-01'],
            ['nama' => 'RENDI ARDIAN', 'tgl_lahir' => '2003-05-06', 'pendidikan' => 'SMK Teknik Komputer dan Jaringan', 'no_hp' => '0882011953085', 'agama' => 'Islam', 'joined' => '2024-06-04'],
            ['nama' => 'WIDIA SUKMAWATI', 'tgl_lahir' => '1976-10-26', 'pendidikan' => 'S1 Pendidikan Agama Islam - UIN Syarif Hidayatullah', 'no_hp' => '081585999923', 'agama' => 'Islam', 'joined' => '2024-06-04'],
            ['nama' => 'FAISAL HENDRO NUGROHO', 'tgl_lahir' => '2001-06-11', 'pendidikan' => 'S1 Teknik Elektro - Universitas Pamulang', 'no_hp' => '081297694426', 'agama' => 'Islam', 'joined' => '2024-06-17'],
            ['nama' => 'BAYU INDRA PERMANA', 'tgl_lahir' => '1991-12-25', 'pendidikan' => 'Jurusan Teknik Komputer Jaringan - SMK Wijayakusuma', 'no_hp' => '081213558959', 'agama' => 'Islam', 'joined' => '2024-08-08'],
            ['nama' => 'NURUL IKHSAN JUSTICIA', 'tgl_lahir' => '1992-10-13', 'pendidikan' => 'S1 Jurusan Manajemen Hutan - IPB', 'no_hp' => '082299761266', 'agama' => 'Islam', 'joined' => '2024-08-10'],
            ['nama' => 'NUR ROKHMAD PAMUNGKAS', 'tgl_lahir' => '1995-12-16', 'pendidikan' => 'SMK AL-MUHADJIRIN BEKASI - RPL', 'no_hp' => '0895320585395', 'agama' => 'Islam', 'joined' => '2024-08-13'],
            ['nama' => 'ALFATH BAHYHAQQI AMRULLAH', 'tgl_lahir' => '2006-02-01', 'pendidikan' => 'SMK 3 Perguruan "Cikini" - Multimedia - Jakarta', 'no_hp' => '082120208494', 'agama' => 'Islam', 'joined' => '2024-08-15'],
            ['nama' => 'MAULANA AINUL YAQIN', 'tgl_lahir' => '2000-05-03', 'pendidikan' => 'D4 Jurusan Teknik Industri Otomotif - Politeknik Stmi Jakarta', 'no_hp' => '089509174523', 'agama' => 'Islam', 'joined' => '2024-08-29'],
            ['nama' => 'JURAIDAH', 'tgl_lahir' => '1973-07-20', 'pendidikan' => 'S1 Fakultas Hukum Universitas Sriwijaya Palembang', 'no_hp' => '087887066115', 'agama' => 'Islam', 'joined' => '2024-09-01'],
            ['nama' => 'FARREL ANDANA', 'tgl_lahir' => '2006-05-23', 'pendidikan' => 'SMK 3 Perguruan "Cikini" - Multimedia', 'no_hp' => '087887436515', 'agama' => 'Islam', 'joined' => '2024-09-02'],
            ['nama' => 'AQILLAH GHAITSA BARKAH', 'tgl_lahir' => '2006-01-24', 'pendidikan' => 'Teknik Komputer Jaringan - SMK Negeri 22 Jakarta Timur', 'no_hp' => '082133331891', 'agama' => 'Islam', 'joined' => '2024-09-19'],
            ['nama' => 'SABINA BERLINA GULO', 'tgl_lahir' => '1997-10-28', 'pendidikan' => 'S1 Akuntansi Univ. STEKOM Semarang', 'no_hp' => '081396037060', 'agama' => 'Katolik', 'joined' => '2024-09-24'],
            ['nama' => 'NAUFAL GHIFARI', 'tgl_lahir' => '1992-03-23', 'pendidikan' => 'S1 Jurusan Teknik Informatika', 'no_hp' => '081311256900', 'agama' => 'Islam', 'joined' => '2024-10-25'],
            ['nama' => 'ACHMAD NURIL MUSTHOFA', 'tgl_lahir' => '2000-10-11', 'pendidikan' => 'SMKN 5 KAB TANGERANG', 'no_hp' => '0895433086600', 'agama' => 'Islam', 'joined' => '2024-10-31'],
            ['nama' => 'BUNGA DECIANA AMELIA OLII', 'tgl_lahir' => '1988-09-24', 'pendidikan' => 'SMA', 'no_hp' => '081574926696', 'agama' => 'Islam', 'joined' => '2024-11-05'],
            ['nama' => 'SEFITA HARTATI', 'tgl_lahir' => '1997-07-27', 'pendidikan' => 'S1 Jurusan Akuntansi - Universitas Pamulang', 'no_hp' => '082241928694', 'agama' => 'Islam', 'joined' => '2024-12-07'],
            ['nama' => 'DINDA PUTRI AYU PERTIWI', 'tgl_lahir' => '2004-03-05', 'pendidikan' => 'SMAN 51 Jakarta', 'no_hp' => '081324666613', 'agama' => 'Islam', 'joined' => '2025-01-13'],
            ['nama' => 'LULU SAFITRI', 'tgl_lahir' => '1998-01-30', 'pendidikan' => 'D3 Sistem Informatika', 'no_hp' => '0895321747927', 'agama' => 'Islam', 'joined' => '2025-01-21'],
            ['nama' => 'MANDANG SALE', 'tgl_lahir' => '2004-08-12', 'pendidikan' => 'Mahasiswa Program Studi Sistem Komputer', 'no_hp' => '089524851857', 'agama' => 'Protestan', 'joined' => '2025-01-22'],
            ['nama' => 'NICO', 'tgl_lahir' => '2004-10-02', 'pendidikan' => 'Jurusan Sistem Komputer - STMIK JAKARTA STI&K', 'no_hp' => '081385136617', 'agama' => 'Buddha', 'joined' => '2025-01-22'],
            ['nama' => 'MUHAMMAD RAFI HAFIZH', 'tgl_lahir' => '2003-04-16', 'pendidikan' => 'Jurusan Pendidikan Bahasa Inggris - Universitas Indraprasta PGRI', 'no_hp' => '089610920234', 'agama' => 'Islam', 'joined' => '2025-01-30'],
            ['nama' => 'LIDIA NATALIA AMUL', 'tgl_lahir' => '1996-08-03', 'pendidikan' => 'S1 Jurusan Informatika - Univ. Nusa Mandiri', 'no_hp' => '081212199678', 'agama' => 'Katolik', 'joined' => '2025-03-05'],
            ['nama' => 'AKMAL DARRYA FAWWAZ', 'tgl_lahir' => '2006-09-14', 'pendidikan' => 'SMK Taruna Bhakti Depok - Rekayasa Perangkat Lunak', 'no_hp' => '081319923524', 'agama' => 'Islam', 'joined' => '2025-06-03'],
            ['nama' => 'BAYU TRI PRAKOSA WIBAWA', 'tgl_lahir' => '2002-01-16', 'pendidikan' => 'STMIK JAKARTA STI&K - Sistem Komputer', 'no_hp' => '0895324074407', 'agama' => 'Islam', 'joined' => '2025-07-17'],
            ['nama' => 'FIKHIH ANANTATUR SYADAT', 'tgl_lahir' => '1981-08-20', 'pendidikan' => 'Univ Budi Luhur - System Informasi', 'no_hp' => '087878606707', 'agama' => 'Islam', 'joined' => '2025-07-17'],
            ['nama' => 'DZAKY IZULHAQ', 'tgl_lahir' => '2002-09-17', 'pendidikan' => 'SMK BUDHIWARMAN 1 - MultiMedia', 'no_hp' => '089637445324', 'agama' => 'Islam', 'joined' => '2025-07-18'],
            ['nama' => 'INDRI ESTI YUNIARTI', 'tgl_lahir' => '1979-06-09', 'pendidikan' => 'STIE/Univ.IPWIJA - Manajemen Bisnis', 'no_hp' => '081282235787', 'agama' => 'Islam', 'joined' => '2025-07-18'],
            ['nama' => 'MUHAMAD RENAL', 'tgl_lahir' => '2002-01-11', 'pendidikan' => 'SMKN 1 Tangerang Selatan - Teknik Elektronika', 'no_hp' => '089601182859', 'agama' => 'Islam', 'joined' => '2025-07-19'],
            ['nama' => 'FARHAN ARIF INDIARTO', 'tgl_lahir' => '2003-06-16', 'pendidikan' => 'SMK N 22 Jakarta - Teknik Komputer dan Jaringan', 'no_hp' => '085776455722', 'agama' => 'Islam', 'joined' => '2025-07-20'],
            ['nama' => 'MUSH\'AB ABDULLAH AHMAD', 'tgl_lahir' => '2001-08-27', 'pendidikan' => 'Universitas Pamulang - Teknik Elektro', 'no_hp' => '083898147462', 'agama' => 'Islam', 'joined' => '2025-07-21'],
            ['nama' => 'ILAH WASILAH', 'tgl_lahir' => '2001-08-27', 'pendidikan' => 'STMIK Jakarta STI&K', 'no_hp' => '083841332894', 'agama' => 'Islam', 'joined' => '2025-07-22'],
            ['nama' => 'MUHAMMAD ASY\'ARI ARIF YUSUF', 'tgl_lahir' => '2002-11-03', 'pendidikan' => 'Universitas Indraprasta PGRI', 'no_hp' => '085156771732', 'agama' => 'Islam', 'joined' => '2025-08-06'],
            ['nama' => 'RADEN MUHAMMAD RAIHAN', 'tgl_lahir' => '2004-07-15', 'pendidikan' => 'Universitas Sultan Ageng Tirtayasa - Pendidikan Biologi', 'no_hp' => '081218205033', 'agama' => 'Islam', 'joined' => '2025-08-13'],
            ['nama' => 'ASEP JANUAR FAUZI', 'tgl_lahir' => '2004-01-03', 'pendidikan' => 'SMKN 22 JAKARTA - Teknik Komputer Jaringan', 'no_hp' => '085772612595', 'agama' => 'Islam', 'joined' => '2025-08-14'],
            ['nama' => 'ADDINA HANIFA ZAHRAH', 'tgl_lahir' => '2001-11-10', 'pendidikan' => 'SMA Muhammadiyah 13 Jakarta', 'no_hp' => '085882068331', 'agama' => 'Islam', 'joined' => '2025-08-15'],
            ['nama' => 'MUHAMAD JIDDAN AL-AVIV', 'tgl_lahir' => '2002-04-22', 'pendidikan' => 'Institut Teknologi Indonesia - Teknik Informatika', 'no_hp' => '085891868754', 'agama' => 'Islam', 'joined' => '2025-08-26'],
            ['nama' => 'MUHAMMAD ILHAM FAUZI', 'tgl_lahir' => '1997-11-15', 'pendidikan' => 'IBIK-57 - Teknik Informatika', 'no_hp' => '088290600374', 'agama' => 'Islam', 'joined' => '2025-08-29'],
            ['nama' => 'GIFFARI JAKA WALI', 'tgl_lahir' => '1999-08-11', 'pendidikan' => 'Institut Bisnis dan Informatika Kosgoro 1957 - Teknik Informatika', 'no_hp' => '081213671977', 'agama' => 'Islam', 'joined' => '2025-09-01'],
            ['nama' => 'VELLA ROVIQOH', 'tgl_lahir' => '1998-02-20', 'pendidikan' => 'STMIK Jakarta STI&K - Sistem Komputer', 'no_hp' => '0895365229695', 'agama' => 'Islam', 'joined' => '2025-09-03'],
            ['nama' => 'DELIMA RAMADHANI', 'tgl_lahir' => '2005-10-21', 'pendidikan' => 'SMK Barunawati - Akuntansi', 'no_hp' => '0895353059309', 'agama' => 'Islam', 'joined' => '2025-09-12'],
            ['nama' => 'SESAR CHAIRUDIN SLAMET', 'tgl_lahir' => '2004-06-22', 'pendidikan' => 'Politeknik Negeri Jakarta - Teknik Grafika Penerbitan', 'no_hp' => '083831617602', 'agama' => 'Islam', 'joined' => '2025-09-14'],
            ['nama' => 'RADITYA DWI EFFENDY', 'tgl_lahir' => '2006-08-21', 'pendidikan' => 'SMK Taruna Bhakti - Rekayasa Perangkat Lunak', 'no_hp' => '081290680221', 'agama' => 'Islam', 'joined' => '2025-09-15'],
            ['nama' => 'JUAN PATRICK', 'tgl_lahir' => '2000-02-18', 'pendidikan' => 'UPN Veteran Jakarta - Teknik Elektro', 'no_hp' => '087889184217', 'agama' => 'Protestan', 'joined' => '2025-09-30'],
            ['nama' => 'RIZQI ARDIANSAH', 'tgl_lahir' => '2005-05-29', 'pendidikan' => 'Universitas Nusa Mandiri', 'no_hp' => '085883178652', 'agama' => 'Islam', 'joined' => '2025-10-07'],
            ['nama' => 'SYEKH MAULANA WIJAYA', 'tgl_lahir' => '2003-07-10', 'pendidikan' => 'Telkom University - Rekayasa Perangkat Lunak Aplikasi', 'no_hp' => '085156684787', 'agama' => 'Islam', 'joined' => '2025-10-07'],
            ['nama' => 'FANNY FIRMANSYAH', 'tgl_lahir' => '1983-12-29', 'pendidikan' => 'STMIK Jakarta STI&K - Manajemen Informatika', 'no_hp' => '081318414663', 'agama' => 'Islam', 'joined' => '2025-10-11'],
            ['nama' => 'MUHAMAD RIZAL AKMAL', 'tgl_lahir' => '2001-04-22', 'pendidikan' => 'Universitas Indra Prasta PGRI', 'no_hp' => '08558573143', 'agama' => 'Islam', 'joined' => '2025-10-14'],
            ['nama' => 'ELISABETH PUJA', 'tgl_lahir' => '2002-11-24', 'pendidikan' => 'Universitas Merdeka Malang - Sistem Informasi', 'no_hp' => '082138783118', 'agama' => 'Katolik', 'joined' => '2025-10-15'],
            ['nama' => 'KAMALUDIN AZHARI', 'tgl_lahir' => '2007-07-07', 'pendidikan' => 'Pondok Pesantren Daar El Qolam 3', 'no_hp' => '085172232571', 'agama' => 'Islam', 'joined' => '2025-10-15'],
            ['nama' => 'MUHAMAD SATRIA MAHADHIKA', 'tgl_lahir' => '2002-11-02', 'pendidikan' => 'Univ. Indraprasta - Teknik Informatika', 'no_hp' => '085774142640', 'agama' => 'Islam', 'joined' => '2025-10-16'],
            ['nama' => 'RAYYAN ASSADID', 'tgl_lahir' => '2006-10-27', 'pendidikan' => 'Universitas Raharja - Teknik Informatika', 'no_hp' => '085781352259', 'agama' => 'Islam', 'joined' => '2025-10-21'],
            ['nama' => 'NOVITA', 'tgl_lahir' => '1980-04-19', 'pendidikan' => 'D3 Akuntansi', 'no_hp' => '082312393905', 'agama' => 'Islam', 'joined' => '2025-10-23'],
            ['nama' => 'ZAHRA SADIDA', 'tgl_lahir' => '2004-09-27', 'pendidikan' => 'Universitas Al-Khairiyah - Pendidikan Bahasa Inggris', 'no_hp' => '0895405071777', 'agama' => 'Islam', 'joined' => '2025-10-24'],
            ['nama' => 'DIAN QURROTUL AINI', 'tgl_lahir' => '2000-11-30', 'pendidikan' => 'SMKN 2000 Alfitroh - Akuntansi', 'no_hp' => '081904069774', 'agama' => 'Islam', 'joined' => '2025-10-30'],
            ['nama' => 'SERDINAND SAPUTRA', 'tgl_lahir' => '2000-11-14', 'pendidikan' => 'Universitas Sultan Ageng Tirtayasa - Agribisnis', 'no_hp' => '089519856575', 'agama' => 'Katolik', 'joined' => '2025-11-06'],
            ['nama' => 'DINI INTAN FEBRIANTI', 'tgl_lahir' => '2005-02-28', 'pendidikan' => 'SMA AT-TAQWA - IPA', 'no_hp' => '085211028193', 'agama' => 'Islam', 'joined' => '2025-11-07'],
            ['nama' => 'MUHAMMAD ABDUL AZIS MAKSUM MAHFUZH', 'tgl_lahir' => '2001-03-16', 'pendidikan' => 'Universitas Pamulang - Teknik Informatika', 'no_hp' => '081212810390', 'agama' => 'Islam', 'joined' => '2025-11-10'],
        ];

        $seeded = 0;
        $skipped = 0;

        foreach ($instructors as $data) {
            $nama = $data['nama'];

            // Generate unique email from name
            $emailBase = strtolower(str_replace([' ', "'", '.', ','], ['_', '', '', ''], $nama));
            $emailBase = preg_replace('/[^a-z0-9_]/', '', $emailBase);
            $email = $emailBase . '@erlass.institute';

            // Skip if email already exists (avoid duplicates with UserSeeder accounts)
            if (User::where('email', $email)->exists()) {
                $skipped++;
                continue;
            }

            // Determine education level from full education string
            $pendTerakhir = $this->extractEducationLevel($data['pendidikan']);

            User::create([
                'nama_lengkap' => $nama,
                'email' => $email,
                'password' => $password,
                'tanggal_lahir' => $data['tgl_lahir'],
                'no_telephone' => $data['no_hp'],
                'status' => 'active',
                'agama' => ucfirst(strtolower($data['agama'])),
                'pend_terakhir' => $pendTerakhir,
                'kompetensi_1' => 'IT & Coding',
                'kompetensi_2' => null,
                'role' => 'instruktur',
                'is_verified' => true,
                'verification_status' => 'approved',
                'verified_at' => $data['joined'],
                'verified_by' => 1, // Verified by webmaster
                'application_date' => $data['joined'],
            ]);

            $seeded++;
        }

        $this->command->info("✅ InstrukturSeeder: {$seeded} instruktur berhasil ditambahkan, {$skipped} dilewati (sudah ada).");
    }

    /**
     * Extract education level (SMA/SMK/D3/D4/S1/S2) from full education string.
     */
    private function extractEducationLevel(string $pendidikan): string
    {
        $pendidikan = strtolower($pendidikan);

        if (str_contains($pendidikan, 's2') || str_contains($pendidikan, 'magister')) {
            return 'S2';
        }
        if (str_contains($pendidikan, 's1') || str_contains($pendidikan, 'universitas') || str_contains($pendidikan, 'univ') || str_contains($pendidikan, 'institut') || str_contains($pendidikan, 'stmik') || str_contains($pendidikan, 'stie') || str_contains($pendidikan, 'politeknik') || str_contains($pendidikan, 'mahasiswa')) {
            return 'S1';
        }
        if (str_contains($pendidikan, 'd4')) {
            return 'D4';
        }
        if (str_contains($pendidikan, 'd3')) {
            return 'D3';
        }
        if (str_contains($pendidikan, 'smk')) {
            return 'SMK';
        }
        if (str_contains($pendidikan, 'sma') || str_contains($pendidikan, 'sman') || str_contains($pendidikan, 'pesantren')) {
            return 'SMA';
        }

        return 'SMA'; // Default
    }
}
