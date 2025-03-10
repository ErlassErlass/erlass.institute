-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2025 at 05:10 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `laporan_mengajar_id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `hadir` tinyint(1) NOT NULL DEFAULT 0,
  `e_signature_instruktur` varchar(255) DEFAULT NULL,
  `e_signature_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_mengajar`
--

CREATE TABLE `laporan_mengajar` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id_instruktur` bigint(20) UNSIGNED NOT NULL,
  `user_id_assisten` bigint(20) UNSIGNED DEFAULT NULL,
  `pertemuan_ke` int(11) NOT NULL,
  `rombel` varchar(255) NOT NULL,
  `jadwal_mengajar` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `kategori_pengajaran` varchar(255) NOT NULL,
  `materi_pengajaran` text NOT NULL,
  `sekolah_kota` varchar(255) NOT NULL,
  `sekolah_kecamatan` varchar(255) NOT NULL,
  `sekolah_nama` varchar(255) NOT NULL,
  `jumlah_siswa_hadir` int(11) NOT NULL,
  `jumlah_siswa_keluar` int(11) NOT NULL,
  `foto_kegiatan` varchar(255) DEFAULT NULL,
  `refleksi_siswa` text NOT NULL,
  `refleksi_capaian` text NOT NULL,
  `keaktifan` enum('sangat_pasif','pasif','aktif','sangat_aktif') NOT NULL,
  `pemahaman_materi` enum('belum_paham','sedikit_paham','paham','sangat_paham') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_03_10_0000000_create_users_table', 1),
(2, '2025_03_10_0000001_create_sekolah_table', 1),
(3, '2025_03_10_0000002_create_siswa_table', 1),
(4, '2025_03_10_0000003_create_laporan_mengajar_table', 1),
(5, '2025_03_10_0000004_create_absensi_table', 1),
(6, '2025_03_10_131719_create_sessions_table', 2),
(7, '2025_03_10_134403_create_cache_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `sekolah`
--

CREATE TABLE `sekolah` (
  `kodlan` varchar(255) NOT NULL,
  `namasekolah` varchar(255) NOT NULL,
  `rank` varchar(255) DEFAULT NULL,
  `jenjang` enum('SD','SMP') NOT NULL,
  `sub_jenjang` varchar(255) DEFAULT NULL,
  `status` enum('Swasta','Negeri') NOT NULL,
  `pd` varchar(255) DEFAULT NULL,
  `kec` varchar(255) NOT NULL,
  `kotkab` varchar(255) NOT NULL,
  `kota` varchar(255) NOT NULL,
  `provinsi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah`
--

INSERT INTO `sekolah` (`kodlan`, `namasekolah`, `rank`, `jenjang`, `sub_jenjang`, `status`, `pd`, `kec`, `kotkab`, `kota`, `provinsi`, `created_at`, `updated_at`) VALUES
('asdasdas', '11111', '1', 'SD', '2222', 'Negeri', '333', '444', '55', 'asdasd', 'asdasd', '2025-03-10 08:52:31', '2025-03-10 09:01:59');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('83RfJaMgWSsCJIqvh2oEaQoHgUgToWCzGAzprzmF', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:135.0) Gecko/20100101 Firefox/135.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOFFjVEk0M0dWTldhTkFCbGhUNzV5cTJtTFJFRzh3ejlSamwwZXRrOCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zZWtvbGFoIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1741622689);

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `nisn` varchar(255) NOT NULL,
  `sekolah_kodlan` varchar(255) NOT NULL,
  `rombel` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `no_telephone` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `agama` varchar(255) NOT NULL,
  `pend_terakhir` varchar(255) NOT NULL,
  `kompetensi_1` varchar(255) NOT NULL,
  `kompetensi_2` varchar(255) DEFAULT NULL,
  `role` enum('instruktur','admin','admin_erlass') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `password`, `tanggal_lahir`, `no_telephone`, `status`, `agama`, `pend_terakhir`, `kompetensi_1`, `kompetensi_2`, `role`, `created_at`, `updated_at`) VALUES
(1, 'tre', 'test@mail.com', '$2y$12$Y5kf8XFdFNjwUj2S0LBXV.DjVlpEW.snll3P4vDMLT2Km2HETQlWK', '1996-09-01', '12312123', 'active', 'asdasd', 'asdasd', 'asdasd', 'asdasd', 'instruktur', '2025-03-10 06:37:51', '2025-03-10 06:37:51'),
(2, 'asdasd', 'helo@mail.com', '$2y$12$.5TFuqWniHE7nLbvVawiQOUhXDVqzDwO5ejHDi0Jzb39H4EpzbWcy', '1996-09-01', '2032423', 'active', 'fnndf', 'jnjfksndf', 'sjdfnsjkd', 'skdjfnjsd', 'admin', '2025-03-10 07:16:58', '2025-03-10 07:16:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absensi_laporan_mengajar_id_foreign` (`laporan_mengajar_id`),
  ADD KEY `absensi_siswa_id_foreign` (`siswa_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `laporan_mengajar`
--
ALTER TABLE `laporan_mengajar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laporan_mengajar_user_id_instruktur_foreign` (`user_id_instruktur`),
  ADD KEY `laporan_mengajar_user_id_assisten_foreign` (`user_id_assisten`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`kodlan`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `siswa_nisn_unique` (`nisn`),
  ADD KEY `siswa_sekolah_kodlan_foreign` (`sekolah_kodlan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan_mengajar`
--
ALTER TABLE `laporan_mengajar`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_laporan_mengajar_id_foreign` FOREIGN KEY (`laporan_mengajar_id`) REFERENCES `laporan_mengajar` (`id`),
  ADD CONSTRAINT `absensi_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`);

--
-- Constraints for table `laporan_mengajar`
--
ALTER TABLE `laporan_mengajar`
  ADD CONSTRAINT `laporan_mengajar_user_id_assisten_foreign` FOREIGN KEY (`user_id_assisten`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `laporan_mengajar_user_id_instruktur_foreign` FOREIGN KEY (`user_id_instruktur`) REFERENCES `users` (`id`);

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_sekolah_kodlan_foreign` FOREIGN KEY (`sekolah_kodlan`) REFERENCES `sekolah` (`kodlan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
