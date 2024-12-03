-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Okt 2024 pada 05.11
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ibuliluk`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `indekos`
--

CREATE TABLE `indekos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jumlah_kamar` int(11) NOT NULL,
  `jumlah_penghuni` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `indekos`
--

INSERT INTO `indekos` (`id`, `nama`, `jumlah_kamar`, `jumlah_penghuni`, `created_at`, `updated_at`) VALUES
(1, 'ibu liluk', 16, 14, NULL, NULL),
(2, 'domasta kost', 16, 15, '2024-10-26 05:59:30', '2024-10-26 05:59:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kamars`
--

CREATE TABLE `kamars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_kamar` int(11) DEFAULT NULL,
  `status` enum('Terisi','Tidak Terisi') NOT NULL DEFAULT 'Tidak Terisi',
  `harga` int(11) DEFAULT NULL,
  `indekos_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kamars`
--

INSERT INTO `kamars` (`id`, `no_kamar`, `status`, `harga`, `indekos_id`, `created_at`, `updated_at`) VALUES
(1, 4, 'Terisi', 500000, 1, NULL, NULL),
(2, 6, 'Terisi', 500000, 1, '2024-10-26 05:59:46', '2024-10-26 05:59:46'),
(3, 14, 'Tidak Terisi', 500000, 2, '2024-10-26 06:00:12', '2024-10-26 06:00:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_08_16_033458_create_indekos_table', 1),
(2, '2024_10_23_091929_create_kamars_table', 2),
(3, '2014_10_12_000000_create_users_table', 3),
(4, '2023_10_01_000000_create_payments_table', 4),
(5, '2024_10_25_093353_add_user_id_to_payments_table', 5),
(6, '2014_10_12_100000_create_password_reset_tokens_table', 6),
(7, '2019_08_19_000000_create_failed_jobs_table', 6),
(8, '2019_12_14_000001_create_personal_access_tokens_table', 6),
(9, '2024_08_17_072229_add_indekos_id_to_users_table', 6),
(10, '2024_08_17_075134_add_indekos_column_to_users_table', 6),
(11, '2024_10_28_075122_create_pengaduan_table', 7),
(12, '2024_10_28_081703_remove_kamar_id_from_pengaduan_table', 8);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `batas_pembayaran` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `tanggal_bayar`, `batas_pembayaran`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, '2024-10-26', '2024-10-27', 'Belum Dibayar', '2024-10-26 06:01:57', '2024-10-26 06:01:57'),
(2, 5, '2024-10-26', '2024-10-27', 'Belum Dibayar', '2024-10-26 06:01:57', '2024-10-26 06:01:57'),
(3, 6, '2024-10-26', '2024-10-27', 'Belum Dibayar', '2024-10-26 06:01:57', '2024-10-26 06:01:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaduan`
--

CREATE TABLE `pengaduan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_pelaporan` date NOT NULL,
  `masalah` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pengaduan`
--

INSERT INTO `pengaduan` (`id`, `user_id`, `tanggal_pelaporan`, `masalah`, `status`, `foto`, `created_at`, `updated_at`) VALUES
(1, 6, '2024-10-28', 'atap bocor', 'Pending', NULL, NULL, NULL),
(2, 6, '2024-10-27', 'pintu rusak', 'Selesai', NULL, NULL, '2024-10-28 03:06:00'),
(3, 9, '2024-10-28', 'kontol rusak', 'Selesai', NULL, NULL, '2024-10-28 03:06:07'),
(4, 6, '2024-10-29', 'kontol', 'Pending', 'pengaduan/TnxD9G6ywO6rxxHJveRLMFsZ7V1Uu1KQO2hVm5ne.jpg', '2024-10-28 02:12:17', '2024-10-28 02:12:17'),
(5, 6, '2024-10-29', 'kontol ngaceng', 'Pending', 'pengaduan/taofWKqYZgkj4IhDwpZa5rkXorl7VR2NNNbrAp1U.jpg', '2024-10-28 02:31:50', '2024-10-28 02:31:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `nama_indekos` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `kamar_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `nama_indekos`, `email_verified_at`, `password`, `kamar_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(5, 'Admin User', 'admin@example.com', 'admin', 'Indekos Admin', NULL, '$2y$10$xETq1n8/9DjPJKlmcWUEDuQ/kOQfDqZxq6SRYXrHQqmbDBOG2.ZTO', 1, NULL, '2024-10-26 05:58:46', '2024-10-26 05:58:46'),
(6, 'joko', 'joko@gmail.com', 'user', 'ibu liluk', NULL, '$2y$10$gkoguSSSNRZrXaAweX/uQ.jiY.1wywKhlSTgZwBHwIEIlFVidwm6u', 2, NULL, '2024-10-26 06:01:23', '2024-10-26 06:01:23'),
(7, 'bagas', 'bagas@gmail.com', 'admin', 'ibu liluk', NULL, '$2y$10$NwyB.njPOb9AiwZhyqaiQevlvrZKb0IMztCJvJLUSBkPvzUM3W5jG', 1, NULL, '2024-10-27 23:22:38', '2024-10-27 23:22:38'),
(8, 'kajik', 'kajik@gmail.com', 'admin', 'domasta kost', NULL, '$2y$10$7Ig1NEg5s/KVuctaLILyt.iTPu6.Cefj2Jr5wTxamKWUiK2vekk8u', 3, NULL, '2024-10-27 23:45:32', '2024-10-27 23:45:32'),
(9, 'toto', 'toto@gmail.com', 'user', 'ibu liluk', NULL, '$2y$10$C/.Rv.FboHf34G2TNx7CBe8hd6oHqADK1Nm3pPpov21REXhZKsvBC', 1, NULL, '2024-10-28 00:56:26', '2024-10-28 00:56:26');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `indekos`
--
ALTER TABLE `indekos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kamars`
--
ALTER TABLE `kamars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kamars_indekos_id_foreign` (`indekos_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengaduan_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_kamar_id_foreign` (`kamar_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `indekos`
--
ALTER TABLE `indekos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kamars`
--
ALTER TABLE `kamars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pengaduan`
--
ALTER TABLE `pengaduan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kamars`
--
ALTER TABLE `kamars`
  ADD CONSTRAINT `kamars_indekos_id_foreign` FOREIGN KEY (`indekos_id`) REFERENCES `indekos` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD CONSTRAINT `pengaduan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_kamar_id_foreign` FOREIGN KEY (`kamar_id`) REFERENCES `kamars` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
