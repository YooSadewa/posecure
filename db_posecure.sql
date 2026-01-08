-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2026 at 05:33 AM
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
-- Database: `db_posecure`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id_absensi` char(10) NOT NULL,
  `tanggal` date NOT NULL,
  `foto_absensi` varchar(255) NOT NULL,
  `id_user` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id_absensi`, `tanggal`, `foto_absensi`, `id_user`) VALUES
('ABS0052934', '2025-11-27', 'ABS0052934_1764225885.png', 'W-07'),
('ABS3602801', '2025-12-30', 'ABS3602801_1767068721.png', 'W-10'),
('ABS6192944', '2025-11-24', 'ABS6192944_1763996638.png', 'W-02'),
('ABS6192945', '2025-11-24', 'ABS6192944_1763996638.png', 'W-03');

-- --------------------------------------------------------

--
-- Table structure for table `alamat`
--

CREATE TABLE `alamat` (
  `id_alamat` char(10) NOT NULL,
  `kecamatan` varchar(255) NOT NULL,
  `kelurahan` varchar(255) NOT NULL,
  `no_rw` varchar(11) NOT NULL,
  `no_rt` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alamat`
--

INSERT INTO `alamat` (`id_alamat`, `kecamatan`, `kelurahan`, `no_rw`, `no_rt`) VALUES
('A-01', 'Bengkong', 'Tanjung Buntung', '02', '08'),
('A-02', 'Batam Center               ', 'Batam Center', '04', '03'),
('A-03', 'Sekupang', 'Sungai Harapan', '16', '04'),
('A-04', 'werwer', 'werwer', '23', '23');

-- --------------------------------------------------------

--
-- Table structure for table `insiden_keamanan`
--

CREATE TABLE `insiden_keamanan` (
  `id_insiden` char(10) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `jenis_insiden` enum('kriminalitas','gangguan_ketertiban','ancaman_fisik_sosial','bencana','lainnya') NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `keterangan` varchar(255) NOT NULL,
  `id_user` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `insiden_keamanan`
--

INSERT INTO `insiden_keamanan` (`id_insiden`, `tanggal`, `jam`, `jenis_insiden`, `foto`, `keterangan`, `id_user`) VALUES
('I-01', '2025-11-14', '15:38:50', 'kriminalitas', NULL, '', 'W-01'),
('I-02', '2025-11-24', '21:29:00', 'gangguan_ketertiban', '', 'ABC', 'W-02'),
('I-03', '2025-11-21', '21:30:00', 'lainnya', '', 'asdf', 'W-02'),
('I-04', '2025-11-26', '11:26:00', 'kriminalitas', 'I-04_1764131212.jpg', 'qwerty', 'W-02'),
('I-05', '2024-12-11', '13:46:00', 'kriminalitas', 'I-05_1764225981.jpg', 'test', 'W-07'),
('I-06', '2025-11-20', '14:07:00', 'kriminalitas', 'I-06_1764313668.jpg', 'kebakaran', 'W-10'),
('I-07', '2025-11-28', '14:20:00', 'kriminalitas', '', 'hwjdhsj', 'W-10'),
('I-08', '2025-12-30', '11:26:00', 'bencana', 'I-08_1767068808.png', 'qweqwr', 'W-10');

-- --------------------------------------------------------

--
-- Table structure for table `petugas_keamanan`
--

CREATE TABLE `petugas_keamanan` (
  `id_user` char(10) NOT NULL,
  `status_keaktifan` enum('aktif','cuti') NOT NULL,
  `id_alamat` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas_keamanan`
--

INSERT INTO `petugas_keamanan` (`id_user`, `status_keaktifan`, `id_alamat`) VALUES
('P-02', 'aktif', 'A-02'),
('P-03', 'aktif', 'A-01');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` char(10) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `role` enum('warga','petugas_keamanan','admin') NOT NULL,
  `no_telp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `username`, `password`, `foto`, `role`, `no_telp`) VALUES
('P-01', 'Thio Sadewa', 'admin', '$2y$10$Ci0SHllr1c0pomNNkYHI8.fjHazZFW/IJaIHGSofQ4T.4oxJalV0K', 'admin_P-01_1764219735.jpg', 'admin', '087545456799'),
('P-02', 'Petugas1', 'petugas1', '$2y$10$HHkpqsYh4vK9hpQ8XqY5QOyaOnkG1hw/nKv26vcPqGCL/kt994F9W', 'user_P-02_1764291637_download (2).jpg', 'petugas_keamanan', '-'),
('P-03', 'Thio', 'PetugasTest', '$2y$10$Ve98Yes1cUMXt6rjRT2zoOiyYqPIGMKDEmfehMy72F7fQcNsxsyTW', '', 'petugas_keamanan', '-'),
('W-01', 'Sadewa', 'Blok A4', 'Blok A4', '', 'warga', '081270544130'),
('W-02', 'Atma Fauzila', 'Blok B', '$2y$10$CHxztVoowDnqFLwJ7AyzyuiJ5wQQepBgMKuJw6mTWYeBaDvr68zoa', '', 'warga', '0895629499558'),
('W-03', 'Raisya', 'BlokA', '$2y$10$I.LC4.qJltmS91qxGj.89.T31ivFK1z2yqGh5H7AXOB.uI0hqAdYy', '', 'warga', '081270544130'),
('W-04', 'Fadli', 'Blok F No. 13', '$2y$10$FQ9EZe1mI0avPLLl/IpvgeP5oihTHSNyumYpODQtc7jiSucR9uDUa', '????\0JFIF\0\0H\0H\0\0??\0bExif\0\0MM\0*\0\0\0\0\0\0\0\0\0\0\0\Z\0\0\0\0\0\0\0J\0\0\0\0\0\0\0R(\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0H\0\0\0\0\0\0H\0\0\0??\0C\0\n\n\n		\n\Z%\Z# , #&\')*)-0-(0%()(??\0C\n\n\n\n(\Z\Z(((((((((((((((((((((((((((((((((((((((((((((((', 'warga', '081270544130'),
('W-07', 'Fadli', 'Blok M', '$2y$10$VSx9NN6oBgfoiMcbtt5hMOla4Y5zBHzvBKqxEo3OF4MBgB/m0qqUK', '????\0JFIF\0\0H\0H\0\0??\0bExif\0\0MM\0*\0\0\0\0\0\0\0\0\0\0\0\Z\0\0\0\0\0\0\0J\0\0\0\0\0\0\0R(\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0H\0\0\0\0\0\0H\0\0\0??\0C\0\n\n\n		\n\Z%\Z# , #&\')*)-0-(0%()(??\0C\n\n\n\n(\Z\Z(((((((((((((((((((((((((((((((((((((((((((((((', 'warga', '081270544130'),
('W-08', 'haikal', 'blok x', '$2y$10$hGW47bgs4KH9VmFcCk0uFeC3I9FPlTU295VIPqGfsVvOgIlBqF0Hq', '????\0JFIF\0\0H\0H\0\0??\0bExif\0\0MM\0*\0\0\0\0\0\0\0\0\0\0\0\Z\0\0\0\0\0\0\0J\0\0\0\0\0\0\0R(\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0H\0\0\0\0\0\0H\0\0\0??\0C\0\n\n\n		\n\Z%\Z# , #&\')*)-0-(0%()(??\0C\n\n\n\n(\Z\Z(((((((((((((((((((((((((((((((((((((((((((((((', 'warga', '081270544130'),
('W-09', 'Thoriq', 'Blok abc', '$2y$10$xfpsnqOo5DIF4DwCGKz6JOIuoDcxIpWhF5nHFdhiucbdsCbWNkXU.', 'profile_6927e17545726.jpg', 'warga', '081270544130'),
('W-10', 'Jason', 'test1', '$2y$10$kfeAmAA4ebXzbin3ZUSf6uO76Jd2Y/IRQqNAIPKWDqSiChbL8pr4i', 'user_W-10_1765084706_download (2).jpg', 'warga', '081270544130'),
('W-11', 'valorant', '23', '$2y$10$cW4sVXY6LhjiP6qIGWIV1eWzmlIYp8xR1BFqEKIBpuaKQUxBCrmPy', 'profile_692ef80ddf959.jpg', 'warga', '232'),
('W-12', 'Rendy', 'xxxx', '$2y$10$GSj1TxnmYS1g29pSfwX6VeDwX3c7/f859m/PY7Sx3eoDMCTVaJGuG', 'profile_69327fb459354.jpg', 'warga', '892475834785435');

-- --------------------------------------------------------

--
-- Table structure for table `warga`
--

CREATE TABLE `warga` (
  `id_user` char(10) NOT NULL,
  `no_kk` varchar(20) NOT NULL,
  `blok_rumah` varchar(100) NOT NULL,
  `hari_ronda` enum('senin','selasa','rabu','kamis','jumat','sabtu','minggu') DEFAULT NULL,
  `id_alamat` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warga`
--

INSERT INTO `warga` (`id_user`, `no_kk`, `blok_rumah`, `hari_ronda`, `id_alamat`) VALUES
('W-01', '12345678', 'Blok A4', 'senin', 'A-01'),
('W-02', '12434234532532', 'Blok B', 'rabu', 'A-02'),
('W-03', '12345678', 'Blok A', 'senin', 'A-02'),
('W-04', '09876543234567', 'Blok F No. 13', 'senin', 'A-01'),
('W-07', '87834723748234', 'Blok M', 'kamis', 'A-02'),
('W-08', '11111111111111111111', 'blok x', 'selasa', 'A-02'),
('W-09', '0348732847324', 'Blok abc', 'sabtu', 'A-02'),
('W-10', '888888888', 'test1', 'sabtu', 'A-02'),
('W-11', '21711014040800463', '23', NULL, 'A-04'),
('W-12', '9837472423435', 'xxxx', 'minggu', 'A-02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `absensi_ibfk_1` (`id_user`);

--
-- Indexes for table `alamat`
--
ALTER TABLE `alamat`
  ADD PRIMARY KEY (`id_alamat`);

--
-- Indexes for table `insiden_keamanan`
--
ALTER TABLE `insiden_keamanan`
  ADD PRIMARY KEY (`id_insiden`),
  ADD KEY `insiden_keamanan_ibfk_1` (`id_user`);

--
-- Indexes for table `petugas_keamanan`
--
ALTER TABLE `petugas_keamanan`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_alamat` (`id_alamat`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `warga`
--
ALTER TABLE `warga`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_alamat` (`id_alamat`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `warga` (`id_user`);

--
-- Constraints for table `insiden_keamanan`
--
ALTER TABLE `insiden_keamanan`
  ADD CONSTRAINT `insiden_keamanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `warga` (`id_user`);

--
-- Constraints for table `petugas_keamanan`
--
ALTER TABLE `petugas_keamanan`
  ADD CONSTRAINT `petugas_keamanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `petugas_keamanan_ibfk_2` FOREIGN KEY (`id_alamat`) REFERENCES `alamat` (`id_alamat`);

--
-- Constraints for table `warga`
--
ALTER TABLE `warga`
  ADD CONSTRAINT `warga_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `warga_ibfk_2` FOREIGN KEY (`id_alamat`) REFERENCES `alamat` (`id_alamat`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
