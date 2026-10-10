-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2026 at 09:40 AM
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
-- Database: `gst-manu`
--

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Level1', NULL, NULL),
(2, 'Level2', NULL, NULL),
(3, 'Chart Of Account', NULL, NULL),
(4, 'Cash Payment', NULL, NULL),
(5, 'Cash Receipt', NULL, NULL),
(6, 'Bank Payment', NULL, NULL),
(7, 'Bank Receipt', NULL, NULL),
(8, 'Journal Voucher', NULL, NULL),
(9, 'Opening Balance', NULL, NULL),
(10, 'Ledger', NULL, NULL),
(11, 'Cash Book', NULL, NULL),
(12, 'Payables', NULL, NULL),
(13, 'Recieveable', NULL, NULL),
(14, 'Trial Balance', NULL, NULL),
(15, 'Purchase Invoice', NULL, NULL),
(16, 'Purchase Return', NULL, NULL),
(17, 'Supplier', NULL, NULL),
(18, 'Sale Invoice', NULL, NULL),
(19, 'Sale Return', NULL, NULL),
(20, 'Customer', NULL, NULL),
(21, 'Item Category', NULL, NULL),
(22, 'Item Units', NULL, NULL),
(23, 'Brand Name', NULL, NULL),
(24, 'Item Registration', NULL, NULL),
(25, 'Stock Adjustment', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
