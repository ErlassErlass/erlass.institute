<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefMateriSeeder extends Seeder
{
    public function run(): void
    {
        $baseData = [
            // Coding Scratch
            ['kategori' => 'Coding Scratch', 'materi' => 'Mengenal Bagian Menu Scratch'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Menyusun dan menjalankan perintah Scratch'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Membuat sprite bergerak (1)'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Membuat Sprite bergerak (2)'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Projek Game Saling Menembak'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Memahami Looks'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Mengendalikan Sprite Menggunakan Keyboard dan Mouse'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Membuat dan Mengubah Sprite di Menu Paint'],
            ['kategori' => 'Coding Scratch', 'materi' => 'PROJEK - Tikus Mencari Keju'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Memahami Variable'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Memahami menu Operator Matematika'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Projek : MIssion Target Pursuit'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Membuat Animasi Hujan'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Belajar Drag and Drop'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Projek - Bus Street'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Membuat Pola Garis Berwarna'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Membuat Garis Melengkung Berwarna-warni'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Membuat Scroll Backdrop Bergerak dari Atas ke Bawah'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Membuat Scroll Backdrop Bergerak dari Bawah ke Atas'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Projek - Pong Game'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa mengingat kembali fungsi dasar menu dan perintah di Scratch Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa membuat sprite mengikuti garis dengan perintah gerakan dan sensing Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa merancang dan membuat permainan labirin dengan rintangan dan logika pergerakan Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa menggunakan broadcasting untuk komunikasi antar-sprite Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa merancang dan membuat permainan perlombaan mobil dengan interaksi antar sprite Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa menggabungkan animasi sprite dengan efek suara interaktif Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa membuat game penangkapan dengan sistem skor Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa membuat game “Catch the Fruit” yang responsif terhadap input pengguna Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa belajar menerapkan penanganan kesalahan dalam proyek Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa merancang dan membuat game ular dengan kontrol berbasis input Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa membuat custom blocks untuk mengoptimalkan kode Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa membuat kalkulator dengan operasi matematika dasar Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa mengontrol sprite menggunakan input eksternal dari Makey Makey Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa merancang game berbasis sensor Makey Makey Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa membuat karakter berjalan di atas platform dengan kontrol pergerakan Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa membuat permainan platformer dengan beberapa level Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa memahami logika dan algoritma dasar game development Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa membuat game pencarian harta karun dengan logika dan rintangan Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa mengintegrasikan Microbit dengan Scratch untuk kontrol interaktif Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Siswa membuat permainan interaktif menggunakan Microbit sebagai pengontrol Level 2'],
            ['kategori' => 'Coding Scratch', 'materi' => 'Lain - Lain'],

            // English Course
            ['kategori' => 'English Course', 'materi' => 'English Course'],

            // Pameran
            ['kategori' => 'Pameran', 'materi' => 'Pameran'],

            // Pendampingan Lomba
            ['kategori' => 'Pendampingan Lomba', 'materi' => 'Coding'],
            ['kategori' => 'Pendampingan Lomba', 'materi' => 'Robotika'],
            ['kategori' => 'Pendampingan Lomba', 'materi' => 'English Course'],

            // Pictoblox AI
            ['kategori' => 'Pictoblox AI', 'materi' => 'Pengenalan Scratch & Interface'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Menjalankan Perintah Dasar'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Gerak Dasar Sprite'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Arah & Kontrol Gerak'],
            ['kategori' => 'Pictoblox AI', 'materi' => '🟩 Mini Project'],
            ['kategori' => 'Pictoblox AI', 'materi' => '🟨 Kuis'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Looks (Tampilan)'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Input Keyboard & Mouse'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Desain Sprite'],
            ['kategori' => 'Pictoblox AI', 'materi' => '🟩 Project Scratch'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'AI: Pengenalan Artificial Intelligence'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'AI: Chatbot Sederhana'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Face Recognition'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Project: Ekspresi Wajah'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Project: Filter Wajah'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Speech Recognition'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Human Body Detection'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Project: Gerakan Hidung'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Project: Gerakan Tangan'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Project: Draw Air'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Computer Vision'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Project: Landmark'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Physics Engine'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Project: Velocity'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Perancangan Ide Proyek'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Presentasi Proyek'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Evaluasi & Refleksi'],
            ['kategori' => 'Pictoblox AI', 'materi' => 'Lain - Lain'],

            // Robotika Jimu
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat robot Lucky Cat sebagai pengantar dasar Jimu.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat dan memprogram robot Vehicle.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Merakit robot Sumo Car untuk kompetisi sederhana.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Merakit dan memprogram robot humanoid sederhana.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat robot Police Bot dengan fungsi sensor dasar.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Merancang sistem smart home sederhana menggunakan Jimu.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat pintu otomatis dengan sensor pada robot.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat robot Snail untuk eksplorasi mekanisme gerak lambat.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Mengembangkan Snail untuk lomba kecepatan kreatif.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Mengadakan tantangan robot Sumo untuk kompetisi antar siswa.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat robot kursi roda sederhana dengan mekanisme gerak.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Merancang robot Sportbot untuk simulasi olahraga.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat proyek jembatan robotik dengan kombinasi Lego.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat robot Snake untuk simulasi pergerakan ular.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Merakit robot Grabberbot untuk fungsi menggenggam objek.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Tantangan Grabberbot untuk mengambil objek tertentu.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Merancang dan membuat robot Transformer dengan fitur transformasi.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat robot Elephant Trunk dengan mekanisme unik.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat penghalang otomatis menggunakan sensor dan servo.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat robot Turtle untuk simulasi gerakan lambat.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Mengembangkan robot Turtle untuk lomba kecepatan kreatif.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat robot Digbot untuk simulasi penggalian.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Merancang humanoid kustom sesuai ide kreatif siswa.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Membuat proyek tempat tidur pintar dengan fungsi otomatis.'],
            ['kategori' => 'Robotika Jimu', 'materi' => 'Lain - Lain'],

            // Robotika micro:bit
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Memahami menu utama dan ruang kerja.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menjalankan perintah dengan blok kode sederhana.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat proyek tombol tersenyum.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Memahami sensor gerak dan membuat ekspresi bergerak.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Melanjutkan proyek ekspresi bergerak.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Mengenal sensor cahaya dan konsep if-else.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat proyek cahaya matahari.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat proyek papan nama menggunakan teks.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Mengenal blok random dengan membuat dadu.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Melanjutkan proyek dadu Micro:bit.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Memahami logika kondisi dengan proyek gunting, batu, kertas.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Melanjutkan proyek gunting, batu, kertas.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat proyek penghitung langkah kaki.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Melanjutkan dan menyempurnakan proyek penghitung langkah kaki.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Mengenal konsep variabel untuk data sederhana.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat proyek dengan memanfaatkan variabel.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Merancang miniatur pohon natal dengan Lego dan LED.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Memasang resistor dan pin I/O pada proyek.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menyempurnakan proyek Lego pohon natal.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Merancang miniatur portal dengan Lego dan servo.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Melanjutkan proyek Lego portal dengan mekanisme servo.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menyempurnakan proyek Lego portal.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat proyek gitar DIY menggunakan barang sekitar.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menyempurnakan dan mendemonstrasikan proyek gitar.'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Mengenal menu utama, workspace, dan cara upload kode Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menjalankan perintah dengan blok kode sederhana Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat alarm getar sederhana Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membaca suhu sekitar dengan Micro:bit Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menampilkan suhu di layar Micro:bit Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Mengenal modul radio dan menghubungkan 2 Micro:bit Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Mengirim pesan antar Micro:bit Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menerima pesan antar Micro:bit Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Mengontrol servo motor berdasarkan input sensor Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Merancang kunci pintu otomatis berbasis magnet Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menyempurnakan proyek kunci pintu otomatis Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Kuis dan diskusi refleksi pemahaman Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Mengenal cara kerja sensor jarak dan membaca data Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat alarm berbunyi saat objek mendekat Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menyempurnakan alarm jarak Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menghasilkan suara dengan Micro:bit Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat nada musik berbasis sensor sentuh Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menggabungkan nada menjadi musik sederhana Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Merancang pintu otomatis berbasis gerakan Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Menyempurnakan proyek pintu otomatis Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat lampu otomatis sesuai intensitas cahaya Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat smartwatch notifikasi getar Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat kipas otomatis berbasis suhu ruangan Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Membuat jam digital interaktif Level 2'],
            ['kategori' => 'Robotika micro:bit', 'materi' => 'Lain - Lain'],

            // Robotika Robotic Explorer
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Pengenalan Micro:bit dan MakeCode, membuat proyek Flashing Heart.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Mengenal Jimu Trackbot beserta komponennya.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Mengenal komponen elektro seperti LED, resistor, dll.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Mengenal blok input seperti on start, forever, show icon, pause, dan sensor gerak.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat proyek Emotion Badge.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat proyek Dice menggunakan sensor akselerometer.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat proyek Touch Emotion.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Mengenal sensor cahaya, membuat proyek Sunlight Sensor.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat proyek Night Light dan menyelesaikan proyek tantangan.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Mengenal sensor suara melalui proyek Clap Heart.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat proyek Pet Calling dan menyelesaikan proyek tantangan.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Memahami konsep repeat, while, if/else, dan comparison operators.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Dasar Scratch, menghubungkan dengan Micro:bit, dan membuat perintah suara.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat proyek Compass Bearing dan Compass North.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat proyek Rock, Paper, Scissors.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat proyek Graphical Dice.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat proyek Step Count.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat portal Jimu, merakit, dan membuat kode.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Merakit proyek Jimu Grabberbot.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Memprogram dan menyelesaikan proyek tantangan Grabberbot.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Mengenal sensor radio melalui proyek SOS dan Teleporting Duck.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Mengenal sensor suhu dengan proyek Thermometer.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat Indoor-Outdoor Thermometer dan proyek tantangan.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Membuat smart trash bin mulai dari perancangan hingga pemrograman.'],
            ['kategori' => 'Robotika Robotic Explorer', 'materi' => 'Lain - Lain'],
        ];

        // Category aliases mapping to ensure matching under any variation
        $categoryAliases = [
            'Coding Scratch' => ['Ekskul Coding Scratch'],
            'English Course' => ['Ekskul English Course'],
            'Pameran' => ['Ekskul Pameran'],
            'Pendampingan Lomba' => ['Ekskul Pendampingan Lomba'],
            'Pictoblox AI' => ['Ekskul Pictoblox AI'],
            'Robotika Jimu' => ['Robotik Jimu', 'Ekskul Robotik Jimu', 'Ekskul Robotika Jimu'],
            'Robotika micro:bit' => ['Micro:bit Learning Kit', 'Robotik Microbit Learning Kit', 'Ekskul Robotik Microbit Learning Kit', 'Robotik micro:bit', 'Ekskul Robotika micro:bit'],
            'Robotika Robotic Explorer' => ['Robotik Explorer', 'Robotik Robotic Explorer', 'Ekskul Robotik Explorer', 'Ekskul Robotika Robotic Explorer'],
        ];

        $finalData = [];
        foreach ($baseData as $item) {
            $finalData[] = $item;
            $cat = $item['kategori'];
            if (isset($categoryAliases[$cat])) {
                foreach ($categoryAliases[$cat] as $alias) {
                    $finalData[] = [
                        'kategori' => $alias,
                        'materi' => $item['materi'],
                    ];
                }
            }
        }

        try {
            DB::table('ref_materi')->truncate();
            foreach (array_chunk($finalData, 200) as $chunk) {
                DB::table('ref_materi')->insert($chunk);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
