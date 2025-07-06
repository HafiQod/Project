-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Jul 2025 pada 12.25
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
-- Database: `foodrescue`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateDailyStats` ()   BEGIN
    DECLARE today DATE DEFAULT CURDATE();
    
    INSERT INTO daily_stats (tanggal, total_donasi, total_porsi_diselamatkan, total_penyumbang_aktif, total_penerima_aktif)
    VALUES (
        today,
        (SELECT COUNT(*) FROM donasi WHERE DATE(created_at) = today),
        (SELECT COALESCE(SUM(porsi_diambil), 0) FROM donasi WHERE DATE(tanggal_diambil) = today),
        (SELECT COUNT(DISTINCT penyumbang_id) FROM donasi WHERE DATE(created_at) = today),
        (SELECT COUNT(DISTINCT penerima_id_ambil) FROM donasi WHERE DATE(tanggal_diambil) = today AND penerima_id_ambil IS NOT NULL)
    )
    ON DUPLICATE KEY UPDATE
        total_donasi = VALUES(total_donasi),
        total_porsi_diselamatkan = VALUES(total_porsi_diselamatkan),
        total_penyumbang_aktif = VALUES(total_penyumbang_aktif),
        total_penerima_aktif = VALUES(total_penerima_aktif);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama`, `email`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@foodsharing.com', '2025-06-16 16:11:52', '2025-06-16 16:11:52'),
(2, 'hapi', '$2y$10$nPHtuzyYMjNUVlNKrDcf5.QDfz2a3NubYxMxvtoJrgMepba0mtu96', 'hapi', 'hapi@g', '2025-06-16 16:17:51', '2025-07-06 09:40:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `daily_stats`
--

CREATE TABLE `daily_stats` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `total_donasi` int(11) DEFAULT 0,
  `total_porsi_diselamatkan` int(11) DEFAULT 0,
  `total_penyumbang_aktif` int(11) DEFAULT 0,
  `total_penerima_aktif` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `daily_stats`
--

INSERT INTO `daily_stats` (`id`, `tanggal`, `total_donasi`, `total_porsi_diselamatkan`, `total_penyumbang_aktif`, `total_penerima_aktif`, `created_at`, `updated_at`) VALUES
(1, '2024-06-01', 12, 45, 8, 15, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(2, '2024-06-02', 15, 52, 10, 18, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(3, '2024-06-03', 8, 28, 6, 12, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(4, '2024-06-04', 18, 64, 12, 22, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(5, '2024-06-05', 22, 78, 15, 28, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(6, '2024-06-06', 19, 71, 13, 25, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(7, '2024-06-07', 25, 89, 17, 32, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(8, '2024-06-08', 16, 58, 11, 20, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(9, '2024-06-09', 21, 76, 14, 27, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(10, '2024-06-10', 28, 95, 19, 35, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(11, '2024-06-11', 24, 86, 16, 30, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(12, '2024-06-12', 30, 108, 22, 38, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(13, '2024-06-13', 27, 92, 18, 33, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(14, '2024-06-14', 33, 115, 24, 42, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(15, '2024-06-15', 29, 102, 20, 36, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(16, '2024-06-16', 35, 125, 26, 45, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(17, '2024-06-17', 31, 112, 23, 40, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(18, '2024-06-18', 38, 135, 28, 48, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(19, '2024-06-19', 34, 120, 25, 43, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(20, '2024-06-20', 40, 142, 30, 52, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(21, '2024-06-21', 37, 131, 27, 47, '2025-07-06 09:24:23', '2025-07-06 09:24:23'),
(64, '2025-07-06', 0, 0, 0, 0, '2025-07-06 10:25:01', '2025-07-06 10:25:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `donasi`
--

CREATE TABLE `donasi` (
  `id` int(11) NOT NULL,
  `penyumbang_id` int(11) NOT NULL,
  `nama_makanan` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `jumlah` varchar(100) DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `lokasi_pickup` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('tersedia','diambil','expired') DEFAULT 'tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `porsi_diambil` int(11) DEFAULT 0,
  `tanggal_diambil` datetime DEFAULT NULL,
  `penerima_id_ambil` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penerima`
--

CREATE TABLE `penerima` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penerima`
--

INSERT INTO `penerima` (`id`, `nama`, `email`, `no_hp`, `password`, `alamat`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mamat', 'admin@uii.ac.id', '6212321', '$2y$10$Nzu5K0hfqO3HoeaOdrJk/enthD4NmgkpjjC0Yz0TuM4cdoMuBWS5y', 'dsafsf', 'active', '2025-06-17 10:36:54', '2025-06-17 10:36:54'),
(2, 'Mamat1', 'hapi1@gmail.com', '62123211', '$2y$10$CcapkNsKoJ2h2uRwF1evUudXdMTBOAgnubpSjm/wuqHbLh.lU3Aq.', 'Aammaaa1', 'active', '2025-06-23 01:20:04', '2025-06-23 01:20:04'),
(3, 'Maya Sari', 'maya@email.com', '628444555666', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jl. Parangtritis No. 321, Bantul', 'active', '2025-07-06 09:38:05', '2025-07-06 09:38:05'),
(4, 'Rizki Pratama', 'rizki@email.com', '628777888999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jl. Solo No. 654, Yogyakarta', 'active', '2025-07-06 09:38:05', '2025-07-06 09:38:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penyumbang`
--

CREATE TABLE `penyumbang` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penyumbang`
--

INSERT INTO `penyumbang` (`id`, `nama`, `email`, `no_hp`, `password`, `alamat`, `status`, `created_at`, `updated_at`) VALUES
(1, 'hapi', 'hapi@gmail.com', '6212321', '$2y$10$QE6xN82DOPLM9bXmGt4aVel/i7Tcooi8hbWogwTPjp1JAy2K/DD.m', 'Aammaaa', 'active', '2025-06-21 14:12:52', '2025-06-21 14:12:52'),
(2, 'Ahmad Susanto', 'ahmad@email.com', '628123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jl. Malioboro No. 123, Yogyakarta', 'active', '2025-07-06 09:38:05', '2025-07-06 09:38:05'),
(3, 'Siti Nurhaliza', 'siti@email.com', '628987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jl. Sudirman No. 456, Bantul', 'active', '2025-07-06 09:38:05', '2025-07-06 09:38:05'),
(4, 'Budi Santoso', 'budi@email.com', '628111222333', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jl. Kaliurang No. 789, Sleman', 'active', '2025-07-06 09:38:05', '2025-07-06 09:38:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `penerima_id` int(11) NOT NULL,
  `donasi_id` int(11) NOT NULL,
  `pesan` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `saran`
--

CREATE TABLE `saran` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal_kirim` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('baru','dibaca','dibalas') DEFAULT 'baru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `kategori` enum('saran','bug','komplain','fitur') DEFAULT 'saran',
  `prioritas` enum('rendah','sedang','tinggi','urgent') DEFAULT 'rendah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `saran`
--

INSERT INTO `saran` (`id`, `nama`, `email`, `pesan`, `tanggal_kirim`, `status`, `created_at`, `updated_at`, `kategori`, `prioritas`) VALUES
(6, 'Bam', 'hapi@gmail.com', 'Keren Banget', '2025-07-06 15:40:30', 'baru', '2025-07-06 08:40:30', '2025-07-06 08:40:30', 'saran', 'rendah'),
(7, 'Ma', 'hapi@gmail.com', 'TEstes', '2025-07-06 16:56:20', 'baru', '2025-07-06 09:56:20', '2025-07-06 09:56:20', 'saran', 'rendah');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `daily_stats`
--
ALTER TABLE `daily_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tanggal` (`tanggal`),
  ADD KEY `idx_daily_stats_tanggal` (`tanggal`);

--
-- Indeks untuk tabel `donasi`
--
ALTER TABLE `donasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_donasi_created_at` (`created_at`),
  ADD KEY `idx_donasi_status` (`status`),
  ADD KEY `idx_donasi_penyumbang` (`penyumbang_id`);

--
-- Indeks untuk tabel `penerima`
--
ALTER TABLE `penerima`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `penyumbang`
--
ALTER TABLE `penyumbang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penerima_id` (`penerima_id`),
  ADD KEY `donasi_id` (`donasi_id`);

--
-- Indeks untuk tabel `saran`
--
ALTER TABLE `saran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_saran_tanggal` (`tanggal_kirim`),
  ADD KEY `idx_saran_status` (`status`),
  ADD KEY `idx_saran_email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `daily_stats`
--
ALTER TABLE `daily_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT untuk tabel `donasi`
--
ALTER TABLE `donasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penerima`
--
ALTER TABLE `penerima`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `penyumbang`
--
ALTER TABLE `penyumbang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `saran`
--
ALTER TABLE `saran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `donasi`
--
ALTER TABLE `donasi`
  ADD CONSTRAINT `donasi_ibfk_1` FOREIGN KEY (`penyumbang_id`) REFERENCES `penyumbang` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`penerima_id`) REFERENCES `penerima` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`donasi_id`) REFERENCES `donasi` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
