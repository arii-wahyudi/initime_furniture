-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2026 at 05:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `initime_x8y2`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_produk`
--

CREATE TABLE `kategori_produk` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kontak_toko`
--

CREATE TABLE `kontak_toko` (
  `id` int(11) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `maps_embed` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontak_toko`
--

INSERT INTO `kontak_toko` (`id`, `alamat`, `telepon`, `email`, `maps_embed`, `created_at`) VALUES
(1, 'Jl. Merdeka No.123, Jakarta, Indonesia', '+62 812 3456 7890', 'company@example.com', 'https://www.google.com/maps/embed?...', '2026-02-10 14:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `nama_produk` varchar(150) DEFAULT NULL,
  `slug` varchar(160) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `nama_setting` varchar(50) DEFAULT NULL,
  `isi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `nama_setting`, `isi`) VALUES
(1, 'site_name', 'INTIME FURNITURE'),
(2, 'logo', 'assets/img/logo.png'),
(3, 'carousel_1_image', 'assets/img/cr1.png'),
(4, 'carousel_1_title', 'Furniture Berkualitas'),
(5, 'carousel_1_desc', 'Solusi furniture rumah dan kantor modern'),
(6, 'carousel_2_image', 'assets/img/cr2.jpg'),
(7, 'carousel_2_title', 'Desain Modern'),
(8, 'carousel_2_desc', 'Nyaman, estetik, dan tahan lama'),
(9, 'about_title', 'INTIME FURNITURE'),
(10, 'about_desc', 'Kami bergerak di bidang penyediaan furniture rumah dan kantor dengan kualitas terbaik.'),
(11, 'about_exp_title', '4+ Tahun Pengalaman'),
(12, 'about_exp_desc', 'Berpengalaman dalam produksi dan instalasi furniture'),
(13, 'about_team_title', 'Tim Profesional'),
(14, 'about_team_desc', 'Didukung oleh tenaga ahli berpengalaman'),
(15, 'about_fast_title', 'Pengerjaan Cepat'),
(16, 'about_fast_desc', 'Proses produksi dan instalasi tepat waktu'),
(17, 'about_image', 'assets/img/furniture-img.png'),
(18, 'testimonial_1_text', 'Produk berkualitas tinggi dan pelayanan memuaskan'),
(19, 'testimonial_1_name', 'Budi Santoso'),
(20, 'testimonial_2_text', 'Desain modern dan nyaman'),
(21, 'testimonial_2_name', 'Dewi Putri'),
(22, 'testimonial_3_text', 'Pengiriman cepat dan rapi'),
(23, 'testimonial_3_name', 'Andi Prasetyo'),
(24, 'footer_text', 'Solusi Kebutuhan Furniture Anda'),
(25, 'footer_credit', 'Developed by Desadroid'),
(26, 'instagram', 'https://instagram.com/intimefurniture'),
(27, 'whatsapp', '628123456789');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kontak_toko`
--
ALTER TABLE `kontak_toko`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kontak_toko`
--
ALTER TABLE `kontak_toko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_produk` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
