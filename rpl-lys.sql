-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 07, 2025 at 03:04 AM
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
-- Database: `rpl-lys`
--

-- --------------------------------------------------------

--
-- Table structure for table `apoteker`
--

CREATE TABLE `apoteker` (
  `id` int(3) NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apoteker`
--

INSERT INTO `apoteker` (`id`, `nama`) VALUES
(1, 'Andikha');

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id` int(3) NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id`, `nama`) VALUES
(1, 'Rizqi');

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `id` int(3) NOT NULL,
  `nama_obat` varchar(50) NOT NULL,
  `harga` int(12) NOT NULL,
  `deskripsi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id` int(3) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `no_telp` varchar(12) NOT NULL,
  `alamat` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id`, `nama`, `no_telp`, `alamat`) VALUES
(1, 'Andika', '089559900012', 'sebelah rizqi'),
(2, 'Ardi', '083456569459', 'depan halte bus'),
(3, 'Topik', '089999999999', 'deket rumah bu laila');

-- --------------------------------------------------------

--
-- Table structure for table `pelayanan`
--

CREATE TABLE `pelayanan` (
  `id` int(3) NOT NULL,
  `nama_pelayanan` varchar(50) NOT NULL,
  `harga` int(12) NOT NULL,
  `deskripsi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelayanan`
--

INSERT INTO `pelayanan` (`id`, `nama_pelayanan`, `harga`, `deskripsi`) VALUES
(1, 'Checkup', 100000, 'Checkup umum pada mulut dan gigi'),
(2, 'Behel Gigi', 500000, 'Pemasangan behel pada gigi'),
(3, 'Tambal Gigi', 50000, 'Penambalan pada gigi yang berlubang');

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan`
--

CREATE TABLE `pemeriksaan` (
  `id` int(3) NOT NULL,
  `id_pasien` int(3) NOT NULL,
  `keluhan` varchar(50) NOT NULL,
  `no_antrian` varchar(3) NOT NULL,
  `status` enum('Dalam Antrian','Sedang Diperiksa','Selesai') NOT NULL DEFAULT 'Dalam Antrian'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemeriksaan`
--

INSERT INTO `pemeriksaan` (`id`, `id_pasien`, `keluhan`, `no_antrian`, `status`) VALUES
(1, 1, 'radang gusi', '1', 'Selesai'),
(2, 2, 'gigi bolong', '2', 'Selesai'),
(3, 3, 'pusing nugas', '3', 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `rekam_medis`
--

CREATE TABLE `rekam_medis` (
  `id` int(3) NOT NULL,
  `id_dokter` int(3) NOT NULL,
  `id_pemeriksaan` int(3) NOT NULL,
  `diagnosa` varchar(255) NOT NULL,
  `id_pelayanan` int(3) NOT NULL,
  `kode_resep` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rekam_medis`
--

INSERT INTO `rekam_medis` (`id`, `id_dokter`, `id_pemeriksaan`, `diagnosa`, `id_pelayanan`, `kode_resep`) VALUES
(1, 1, 1, 'radang mulut', 1, 'RSP-T4LML7SL'),
(2, 1, 2, 'gigi berlubang', 3, 'RSP-2SQ63STR'),
(3, 1, 3, 'dudududu dudududu choaa', 1, 'RSP-59A2ALLS');

-- --------------------------------------------------------

--
-- Table structure for table `resepsionis`
--

CREATE TABLE `resepsionis` (
  `id` int(3) NOT NULL,
  `nama` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resepsionis`
--

INSERT INTO `resepsionis` (`id`, `nama`) VALUES
(1, 'Rizza');

-- --------------------------------------------------------

--
-- Table structure for table `resep_dokter`
--

CREATE TABLE `resep_dokter` (
  `id` int(3) NOT NULL,
  `kode_resep` varchar(15) NOT NULL,
  `id_dokter` int(3) NOT NULL,
  `id_apoteker` int(3) DEFAULT NULL,
  `resep` varchar(255) DEFAULT NULL,
  `harga` int(12) DEFAULT NULL,
  `status` enum('Proses','Selesai') NOT NULL DEFAULT 'Proses'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resep_dokter`
--

INSERT INTO `resep_dokter` (`id`, `kode_resep`, `id_dokter`, `id_apoteker`, `resep`, `harga`, `status`) VALUES
(1, 'RSP-T4LML7SL', 1, 1, 'anti inflamasi x1,obat radang x1', NULL, 'Selesai'),
(2, 'RSP-2SQ63STR', 1, 1, 'anti nyeri x1', NULL, 'Selesai'),
(3, 'RSP-59A2ALLS', 1, 1, 'matcha', 35000, 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `tercatat_pelayanan`
--

CREATE TABLE `tercatat_pelayanan` (
  `id_pelayanan` int(3) NOT NULL,
  `id_rekam_medis` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(3) NOT NULL,
  `id_pasien` int(3) NOT NULL,
  `id_resepsionis` int(3) NOT NULL,
  `id_rekam_medis` int(3) NOT NULL,
  `no_invoice` varchar(12) NOT NULL,
  `tanggal` date NOT NULL,
  `id_pelayanan` int(3) NOT NULL,
  `kode_resep` varchar(15) NOT NULL,
  `harga_total` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_pasien`, `id_resepsionis`, `id_rekam_medis`, `no_invoice`, `tanggal`, `id_pelayanan`, `kode_resep`, `harga_total`) VALUES
(1, 1, 1, 1, 'INV-00001', '2025-08-04', 1, 'RSP-T4LML7SL', 100000),
(2, 2, 1, 2, 'INV-00002', '2025-08-04', 3, 'RSP-2SQ63STR', 50000);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(3) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipe_user` enum('dokter','apoteker','resepsionis','') NOT NULL,
  `id_user` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `tipe_user`, `id_user`) VALUES
(1, 'apoteker', '$2y$10$vkoh32tuogCGE4Mj1RK62OzoFrFJJBmxkDBZLCFzlqW1uSE9mVOIC', 'apoteker', 1),
(2, 'resepsionis', '$2y$10$uY.TPPgVR1TY4rjof0tlF.db3whNXuXzo4BitASTErECTZt0JtZ9a', 'resepsionis', 1),
(3, 'dokter', '$2y$10$P8Uu9KQ7gN78rIXHSckH8OlO00P0CbNJGjAArA1oYD16/Ajj7hj/u', 'dokter', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apoteker`
--
ALTER TABLE `apoteker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pelayanan`
--
ALTER TABLE `pelayanan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `constraint_pasien` (`id_pasien`);

--
-- Indexes for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `constraint_pemeriksaan` (`id_pemeriksaan`),
  ADD KEY `constraint_dokter_2` (`id_dokter`),
  ADD KEY `constraint_pelayanan_2` (`id_pelayanan`);

--
-- Indexes for table `resepsionis`
--
ALTER TABLE `resepsionis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resep_dokter`
--
ALTER TABLE `resep_dokter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_resep` (`kode_resep`),
  ADD KEY `constraint_apoteker` (`id_apoteker`),
  ADD KEY `constraint_dokter` (`id_dokter`);

--
-- Indexes for table `tercatat_pelayanan`
--
ALTER TABLE `tercatat_pelayanan`
  ADD KEY `constraint_pelayanan` (`id_pelayanan`),
  ADD KEY `constraint_rekam_medis` (`id_rekam_medis`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `constraint_resepsionis` (`id_resepsionis`),
  ADD KEY `constraint_pasien_2` (`id_pasien`),
  ADD KEY `constraint_pelayanan_3` (`id_pelayanan`),
  ADD KEY `constraint_rekam_medis_2` (`id_rekam_medis`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apoteker`
--
ALTER TABLE `apoteker`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `obat`
--
ALTER TABLE `obat`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pelayanan`
--
ALTER TABLE `pelayanan`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `resepsionis`
--
ALTER TABLE `resepsionis`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `resep_dokter`
--
ALTER TABLE `resep_dokter`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD CONSTRAINT `constraint_pasien` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  ADD CONSTRAINT `constraint_dokter_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_pelayanan_2` FOREIGN KEY (`id_pelayanan`) REFERENCES `pelayanan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_pemeriksaan` FOREIGN KEY (`id_pemeriksaan`) REFERENCES `pemeriksaan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resep_dokter`
--
ALTER TABLE `resep_dokter`
  ADD CONSTRAINT `constraint_apoteker` FOREIGN KEY (`id_apoteker`) REFERENCES `apoteker` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_dokter` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tercatat_pelayanan`
--
ALTER TABLE `tercatat_pelayanan`
  ADD CONSTRAINT `constraint_pelayanan` FOREIGN KEY (`id_pelayanan`) REFERENCES `pelayanan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_rekam_medis` FOREIGN KEY (`id_rekam_medis`) REFERENCES `rekam_medis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `constraint_pasien_2` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_pelayanan_3` FOREIGN KEY (`id_pelayanan`) REFERENCES `pelayanan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_rekam_medis_2` FOREIGN KEY (`id_rekam_medis`) REFERENCES `rekam_medis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `constraint_resepsionis` FOREIGN KEY (`id_resepsionis`) REFERENCES `resepsionis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
