-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 14, 2025 at 05:13 PM
-- Server version: 8.0.42-0ubuntu0.24.04.2
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laporan_harian`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id` int NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_faktur` date DEFAULT NULL,
  `suplayer` varchar(100) DEFAULT NULL,
  `nomor_faktur` varchar(50) DEFAULT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `harga_modal` decimal(18,2) DEFAULT NULL,
  `jumlah_seluruh_faktur` decimal(18,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pembayaran` varchar(20) DEFAULT NULL,
  `lama_tempo` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang_masuk`
--

INSERT INTO `barang_masuk` (`id`, `tanggal_masuk`, `tanggal_faktur`, `suplayer`, `nomor_faktur`, `nama_barang`, `qty`, `harga_modal`, `jumlah_seluruh_faktur`, `created_at`, `pembayaran`, `lama_tempo`) VALUES
(2, '2025-08-12', '2025-08-10', 'irm', '123test', NULL, NULL, NULL, 11000.00, '2025-08-12 09:38:38', 'cash', NULL),
(3, '2025-08-12', '2025-08-10', 'sentos', 'tl12', NULL, NULL, NULL, 79000.00, '2025-08-12 09:42:48', 'tempo', 30),
(4, '2025-08-12', NULL, 'irm', 'test123', NULL, NULL, NULL, 5000.00, '2025-08-12 11:08:50', 'tempo', 30),
(5, '2025-08-12', NULL, 'irm', '123-test/2', NULL, NULL, NULL, 422.00, '2025-08-12 11:59:34', 'tempo', 30);

-- --------------------------------------------------------

--
-- Table structure for table `bayar_tagihan`
--

CREATE TABLE `bayar_tagihan` (
  `id` int NOT NULL,
  `nama_tagihan` varchar(100) NOT NULL,
  `nominal` decimal(18,2) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bayar_tagihan`
--

INSERT INTO `bayar_tagihan` (`id`, `nama_tagihan`, `nominal`, `tanggal`, `keterangan`, `created_at`) VALUES
(1, 'bayar irm', 1000.00, '2025-08-12', 'bayar ir', '2025-08-12 10:15:29');

-- --------------------------------------------------------

--
-- Table structure for table `detail_barang_masuk`
--

CREATE TABLE `detail_barang_masuk` (
  `id` int NOT NULL,
  `id_barang_masuk` int NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `qty` int NOT NULL,
  `harga_modal` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_barang_masuk`
--

INSERT INTO `detail_barang_masuk` (`id`, `id_barang_masuk`, `nama_barang`, `qty`, `harga_modal`) VALUES
(1, 2, 'draglink canter', 1, 300.00),
(2, 3, 'mrn', 50, 1500.00),
(3, 3, '185 70 R14 EP-150', 5, 600.00),
(4, 3, '175 65 r14 ep-150', 2, 575.00),
(5, 5, 'boot steering avanza ', 2, 136.00),
(6, 5, 'karet boot g. kopling canter', 10, 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `id` int NOT NULL,
  `id_penjualan` int NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `qty` int NOT NULL,
  `harga` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`id`, `id_penjualan`, `nama_barang`, `qty`, `harga`) VALUES
(28, 19, '175 65 r14 champiro ecotec', 1, 500.00),
(29, 19, '13 GT', 1, 115.00),
(30, 20, '185 70 R14 EP-150', 2, 650.00),
(31, 21, 'bd 13 kl', 5, 50.00),
(32, 21, 'bd 13 gt', 5, 100.00),
(33, 22, 'mrn', 2, 1640.00),
(34, 22, 'ban dlm 16 gt panjang', 3, 220.00),
(35, 22, 'flap 16 gt', 2, 50.00),
(36, 23, 'Mrn', 3, 1640.00),
(37, 23, 'Ban dlm 16 gt', 3, 220.00);

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nama`) VALUES
(1, 'AL'),
(2, 'TM'),
(3, 'DD'),
(4, 'FD'),
(5, 'KR'),
(6, 'LA'),
(7, 'SL'),
(8, 'RN'),
(9, 'ED');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id` int NOT NULL,
  `tanggal` date NOT NULL,
  `rincian` varchar(255) NOT NULL,
  `nominal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengeluaran`
--

INSERT INTO `pengeluaran` (`id`, `tanggal`, `rincian`, `nominal`) VALUES
(3, '2025-08-12', 'Pembayaran tagihan: bayar irm', 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id` int NOT NULL,
  `nomor_nota` varchar(50) NOT NULL,
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_karyawan` int NOT NULL,
  `tunai` decimal(10,2) NOT NULL,
  `transfer` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id`, `nomor_nota`, `tanggal`, `id_karyawan`, `tunai`, `transfer`) VALUES
(19, '1', '2025-08-12 11:00:50', 2, 623.00, 0.00),
(20, '5', '2025-08-12 11:01:42', 1, 1300.00, 0.00),
(21, '2', '2025-08-12 17:41:55', 1, 755.00, 0.00),
(22, '3', '2025-08-12 18:08:01', 3, 2040.00, 2000.00),
(23, '12', '2025-08-12 19:45:41', 6, 6000.00, 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bayar_tagihan`
--
ALTER TABLE `bayar_tagihan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detail_barang_masuk`
--
ALTER TABLE `detail_barang_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_barang_masuk` (`id_barang_masuk`);

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_penjualan` (`id_penjualan`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bayar_tagihan`
--
ALTER TABLE `bayar_tagihan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `detail_barang_masuk`
--
ALTER TABLE `detail_barang_masuk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_barang_masuk`
--
ALTER TABLE `detail_barang_masuk`
  ADD CONSTRAINT `detail_barang_masuk_ibfk_1` FOREIGN KEY (`id_barang_masuk`) REFERENCES `barang_masuk` (`id`);

--
-- Constraints for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `detail_penjualan_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`);

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
