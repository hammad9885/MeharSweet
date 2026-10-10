-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 17, 2026 at 02:59 PM
-- Server version: 8.0.43-cll-lve
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `realerp_GST`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_masters`
--

CREATE TABLE `account_masters` (
  `id` int NOT NULL,
  `title` varchar(255)   NOT NULL,
  `opening_date` varchar(255)   NOT NULL,
  `account_code` varchar(255)   NOT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL,
  `level2_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_masters`
--

INSERT INTO `account_masters` (`id`, `title`, `opening_date`, `account_code`, `cid`, `level2_id`, `created_at`, `updated_at`) VALUES
(1, 'Purchase Debit Account', '2025-09-17', '0001', 1, 5, '2025-09-18 00:57:32', '2025-09-18 00:57:32'),
(2, 'Allied Bank LTD', '2025-09-17', '0001', 1, 3, '2025-09-18 00:58:29', '2025-09-18 00:58:29'),
(3, 'Cashier Account', '2025-09-17', '0001', 1, 4, '2025-09-18 00:58:48', '2025-09-18 00:58:56'),
(6, 'Sale debit ACCOUNT', '2025-09-18', '0001', 1, 7, '2025-09-18 12:34:43', '2025-09-18 12:34:43'),
(9, 'Testing Account', '2025-09-21', '0001', 1, 9, '2025-09-22 01:06:20', '2025-09-22 01:06:20'),
(13, 'testuser Supplier', '2025-09-22 18:55:12', '0001', 1, 6, '2025-09-22 18:55:12', '2025-09-22 18:55:12'),
(14, 'testuser Customer', '2025-09-22 19:02:06', '0001', 1, 8, '2025-09-22 19:02:06', '2025-09-22 19:02:06'),
(15, 'testing customer abc Customer', '2025-10-01 14:35:07', '0002', 1, 8, '2025-10-01 14:35:07', '2025-10-01 14:35:07'),
(16, 'Abc employee', '2025-10-01', '0001', 1, 11, '2025-10-01 14:43:57', '2025-10-01 14:43:57'),
(17, 'Stock Account', '2025-10-15', '0001', 7, 16, '2025-10-15 12:12:56', '2025-10-15 12:12:56'),
(18, 'Abc supplier Supplier', '2025-10-17 15:51:45', '0001', 7, 13, '2025-10-17 15:51:45', '2025-10-17 15:51:45'),
(19, 'Office Cash', '2025-10-25', '0001', 7, 15, '2025-10-25 11:37:23', '2025-10-25 11:37:23'),
(20, 'AWAIS METAL RECYCLING Customer', '2025-10-25 11:56:48', '0001', 7, 12, '2025-10-25 11:56:48', '2025-10-25 11:56:48'),
(21, 'SHAFIQUE PLASTIC Customer', '2025-10-25 12:05:19', '0002', 7, 12, '2025-10-25 12:05:19', '2025-10-25 12:05:19'),
(22, 'N-B TRADERS Customer', '2025-10-25 12:11:19', '0003', 7, 12, '2025-10-25 12:11:19', '2025-10-25 12:11:19'),
(23, 'M SIDDIQ EXPENCE', '2025-07-01', '0001', 7, 17, '2025-10-25 12:27:35', '2025-10-25 12:27:35'),
(24, 'MISC ACCOUNT EXPENCE', '2025-07-01', '0002', 7, 17, '2025-10-25 12:32:48', '2025-10-25 12:32:48'),
(25, 'TIME STAR Customer', '2025-10-25 13:22:24', '0004', 7, 12, '2025-10-25 13:22:24', '2025-10-25 13:22:24'),
(26, 'BRIGHT CORPORATION Customer', '2025-10-25 13:37:43', '0005', 7, 12, '2025-10-25 13:37:43', '2025-10-25 13:37:43'),
(27, 'PAK METAL ( RANA MUNAWAR ) Customer', '2025-10-25 13:43:31', '0006', 7, 12, '2025-10-25 13:43:31', '2025-10-25 13:43:31'),
(28, 'PAK METAL WOKS ( RANA MUNAWAR ) Customer', '2025-10-25 13:54:27', '0007', 7, 12, '2025-10-25 13:54:27', '2025-10-25 13:54:27'),
(29, 'UNITED GROUP Customer', '2025-10-25 14:00:58', '0008', 7, 12, '2025-10-25 14:00:58', '2025-10-25 14:00:58'),
(30, 'AMNA ENTERPRICES Customer', '2025-10-25 14:03:35', '0009', 7, 12, '2025-10-25 14:03:35', '2025-10-25 14:03:35'),
(31, 'OFFICE ENTEETAINMENT EXPENCE Customer', '2025-10-25 14:36:43', '0010', 7, 12, '2025-10-25 14:36:43', '2025-10-25 14:36:43'),
(32, 'ATLAS ENGINEERING Customer', '2025-10-25 14:52:45', '0011', 7, 12, '2025-10-25 14:52:45', '2025-10-25 14:52:45'),
(34, 'SALARY EXPENCE', '2025-10-25', '0003', 7, 17, '2025-10-25 15:19:56', '2025-10-25 15:19:56'),
(35, 'United Trading Company Supplier', '2025-11-11 14:44:27', '0002', 1, 6, '2025-11-11 14:44:27', '2025-11-11 14:44:27'),
(36, 'United Trading Company Customer', '2025-11-11 14:54:34', '0003', 1, 8, '2025-11-11 14:54:34', '2025-11-11 14:54:34'),
(37, 'Azgard Nine Ltd.', '2025-12-09', '0001', 11, 19, '2025-12-09 12:50:56', '2025-12-09 12:50:56'),
(38, 'Rahil Sb. Purchase Customer', '2025-12-09 17:29:04', '0002', 11, 19, '2025-12-09 17:29:04', '2025-12-09 17:29:04'),
(39, 'Ulfat Customer', '2025-12-19 14:21:01', '0003', 11, 19, '2025-12-19 14:21:01', '2025-12-19 14:21:01'),
(40, 'Custmer Customer', '2026-01-06 15:38:01', '0004', 1, 8, '2026-01-06 15:38:01', '2026-01-06 15:38:01'),
(41, 'Rahil Sb. Purchase Customer', '2026-01-20 16:20:08', '0004', 11, 19, '2026-01-20 16:20:08', '2026-01-20 16:20:08'),
(42, 'Partner 1 Capital', '2026-02-07', '0001', 13, 39, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(43, 'Partner 2 Capital', '2026-02-07', '0002', 13, 39, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(44, 'Main Cash A/c', '2026-02-07', '0001', 13, 45, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(45, 'Meezan Account # 1234', '2026-02-07', '0001', 13, 46, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(46, 'Stock A/c', '2026-02-07', '0001', 13, 44, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(47, 'Sales Account', '2026-02-07', '0001', 13, 48, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(48, 'Salary Expenses', '2026-02-07', '0001', 13, 50, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(49, 'Misc Expenses', '2026-02-07', '0002', 13, 50, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(50, 'Stationary Expenses', '2026-02-07', '0003', 13, 50, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(51, 'Entertainment Expenses', '2026-02-07', '0004', 13, 50, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(52, 'Electricity Bill', '2026-02-07', '0001', 13, 51, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(53, 'Mobile & Telephone bills', '2026-02-07', '0002', 13, 51, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(54, 'Gas Bills', '2026-02-07', '0003', 13, 51, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(55, 'Internet Bill', '2026-02-07', '0004', 13, 51, '2026-02-07 10:36:53', '2026-02-07 10:36:53'),
(56, 'Partner 1 Capital', '2026-02-14', '0001', 14, 53, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(57, 'Partner 2 Capital', '2026-02-14', '0002', 14, 53, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(58, 'Main Cash A/c', '2026-02-14', '0001', 14, 59, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(59, 'Meezan Account # 1234', '2026-02-14', '0001', 14, 60, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(60, 'Stock A/c', '2026-02-14', '0001', 14, 58, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(61, 'Sales Account', '2026-02-14', '0001', 14, 62, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(62, 'Salary Expenses', '2026-02-14', '0001', 14, 64, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(63, 'Misc Expenses', '2026-02-14', '0002', 14, 64, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(64, 'Stationary Expenses', '2026-02-14', '0003', 14, 64, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(65, 'Entertainment Expenses', '2026-02-14', '0004', 14, 64, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(66, 'Electricity Bill', '2026-02-14', '0001', 14, 65, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(67, 'Mobile & Telephone bills', '2026-02-14', '0002', 14, 65, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(68, 'Gas Bills', '2026-02-14', '0003', 14, 65, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(69, 'Internet Bill', '2026-02-14', '0004', 14, 65, '2026-02-14 08:56:22', '2026-02-14 08:56:22'),
(70, 'test Customer', '2026-02-14 14:44:42', '0005', 11, 19, '2026-02-14 09:44:42', '2026-02-14 09:44:42'),
(71, 'Sethi centary Supplier', '2026-02-16 13:22:16', '0001', 13, 40, '2026-02-16 08:22:16', '2026-02-16 08:22:16'),
(72, 'zillay Customer', '2026-02-16 13:33:43', '0001', 13, 43, '2026-02-16 08:33:43', '2026-02-16 08:33:43');

-- --------------------------------------------------------

--
-- Table structure for table `attendence_forms`
--

CREATE TABLE `attendence_forms` (
  `id` int NOT NULL,
  `employee_id` bigint UNSIGNED DEFAULT NULL,
  `fname` varchar(255)   DEFAULT NULL,
  `cnic` bigint DEFAULT NULL,
  `employee_date` date DEFAULT NULL,
  `employee_time` time DEFAULT NULL,
  `employee_time_out` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `title` varchar(255)   NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumption_details`
--

CREATE TABLE `consumption_details` (
  `id` bigint UNSIGNED NOT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `v_no` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `qty` varchar(255)   NOT NULL DEFAULT '0',
  `rate` varchar(255)   NOT NULL DEFAULT '0',
  `amount` varchar(255)   NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consumption_details`
--

INSERT INTO `consumption_details` (`id`, `cid`, `v_no`, `item_id`, `qty`, `rate`, `amount`, `created_at`, `updated_at`) VALUES
(8, 7, 1, 5, '1', '10', '10.00', '2025-10-16 13:15:51', '2025-10-16 13:15:51'),
(9, 7, 1, 5, '1', '10', '10.00', '2025-10-16 13:15:51', '2025-10-16 13:15:51'),
(10, 7, 1, 5, '1', '10', '10.00', '2025-10-16 13:15:51', '2025-10-16 13:15:51'),
(11, 7, 2, 5, '20', '15', '300.00', '2025-10-17 15:58:38', '2025-10-17 15:58:38'),
(12, 7, 2, 7, '10', '100', '1000.00', '2025-10-17 15:58:38', '2025-10-17 15:58:38'),
(14, 7, 4, 15, '2500', '627', '1567500.00', '2025-10-18 12:56:37', '2025-10-18 12:56:37'),
(15, 7, 4, 16, '800', '196', '156800.00', '2025-10-18 12:56:37', '2025-10-18 12:56:37'),
(16, 7, 4, 17, '700', '248', '173600.00', '2025-10-18 12:56:37', '2025-10-18 12:56:37'),
(17, 7, 4, 18, '1000', '120', '120000.00', '2025-10-18 12:56:37', '2025-10-18 12:56:37'),
(18, 7, 4, 19, '1300', '3', '3900.00', '2025-10-18 12:56:37', '2025-10-18 12:56:37'),
(19, 7, 4, 20, '750', '250', '187500.00', '2025-10-18 12:56:37', '2025-10-18 12:56:37'),
(24, 11, 1, 29, '0.5', '10', '5.00', '2025-12-03 18:00:21', '2025-12-03 18:00:21'),
(25, 11, 1, 29, '.8', '60', '48.00', '2025-12-03 18:00:21', '2025-12-03 18:00:21'),
(254, 1, 1, 33, '1', '322', '322.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(255, 1, 1, 34, '1', '296', '296.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(256, 1, 1, 35, '2', '1091', '2182.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(257, 1, 1, 36, '0.03', '1422', '42.66', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(258, 1, 1, 37, '4', '196', '784.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(259, 1, 1, 38, '1', '24', '24.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(260, 1, 1, 39, '4', '15', '60.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(261, 1, 1, 40, '1', '12', '12.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(262, 1, 1, 41, '4', '20', '80.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(263, 1, 1, 42, '1', '15', '15.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(264, 1, 1, 43, '1', '10', '10.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(265, 1, 1, 44, '3', '5', '15.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(266, 1, 1, 45, '3', '3', '9.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(267, 1, 1, 46, '1', '20', '20.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(268, 1, 1, 47, '1', '40', '40.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(269, 1, 1, 48, '0.03', '320', '9.60', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(270, 1, 1, 49, '0.03', '368', '11.04', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(271, 1, 1, 50, '5', '24', '120.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(272, 1, 1, 52, '20', '173', '3460.00', '2026-02-13 13:27:51', '2026-02-13 13:27:51'),
(273, 1, 2, 32, '1', '3500', '3500.00', '2026-02-14 07:08:10', '2026-02-14 07:08:10');

-- --------------------------------------------------------

--
-- Table structure for table `consumption_masters`
--

CREATE TABLE `consumption_masters` (
  `id` bigint UNSIGNED NOT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `v_no` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `descr` varchar(255)   DEFAULT NULL,
  `amount` varchar(255)   NOT NULL DEFAULT '0',
  `qty` varchar(255)   NOT NULL DEFAULT '0',
  `cost_price` varchar(255)   NOT NULL DEFAULT '0',
  `tamount` varchar(255)   NOT NULL DEFAULT '0',
  `is_expired` varchar(255)   NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consumption_masters`
--

INSERT INTO `consumption_masters` (`id`, `cid`, `item_id`, `v_no`, `date`, `descr`, `amount`, `qty`, `cost_price`, `tamount`, `is_expired`, `created_at`, `updated_at`) VALUES
(2, 7, 6, 1, '2025-10-16', NULL, '30.00', '1', '30.00', '0', '1', '2025-10-16 13:15:51', '2025-10-17 15:58:37'),
(3, 7, 6, 2, '2025-10-17', NULL, '1300.00', '10', '130.00', '0', '0', '2025-10-17 15:58:37', '2025-10-17 15:58:37'),
(5, 7, 21, 4, '2025-10-18', NULL, '2209300.00', '1', '2209300.00', '0', '0', '2025-10-18 12:56:37', '2025-10-18 12:56:37'),
(8, 11, 28, 1, '2025-12-03', NULL, '53.00', '1', '53.00', '0', '0', '2025-12-03 18:00:21', '2025-12-03 18:00:21'),
(9, 1, 51, 1, '2026-02-06', 'Plastic Cooler', '7512.30', '1', '7512.30', '0', '1', '2026-02-13 13:02:49', '2026-02-14 07:08:10'),
(10, 1, 51, 2, '2026-02-14', NULL, '3500.00', '1', '3500.00', '0', '0', '2026-02-14 07:08:10', '2026-02-14 07:08:10');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255)   NOT NULL,
  `city` varchar(255)   NOT NULL,
  `address` varchar(255)   DEFAULT NULL,
  `Remark` varchar(255)   DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255)   NOT NULL,
  `city` varchar(255)   DEFAULT NULL,
  `address` varchar(255)   DEFAULT NULL,
  `cnic` varchar(255)   DEFAULT NULL,
  `mobile` varchar(255)   DEFAULT NULL,
  `email` varchar(255)   DEFAULT NULL,
  `ntn` varchar(255)   DEFAULT NULL,
  `acc_id` bigint UNSIGNED DEFAULT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `b_type` varchar(255)   DEFAULT NULL,
  `registration_tax` varchar(255)   DEFAULT NULL,
  `strn` varchar(255)   DEFAULT NULL,
  `file_no` varchar(255)   DEFAULT NULL,
  `company_title` varchar(225)   DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `city`, `address`, `cnic`, `mobile`, `email`, `ntn`, `acc_id`, `cid`, `created_at`, `updated_at`, `b_type`, `registration_tax`, `strn`, `file_no`, `company_title`) VALUES
(1, 'Faizan Rajput', 'Madiun', 'Jr. Krakatau No. 106', '3410458626759', '03377777092', 'faizanrajput488@gmail.com', '12121212', 7, 1, '2025-09-18 16:31:07', '2025-11-04 03:17:26', 'Individual', 'Sale tax', '1212', '2121', NULL),
(2, 'Checker Custmer', 'Madiun', 'Jr. Krakatau No. 106', '3410458626759', '03377777092', 'faizanrajput488@gmail.com', '1212121233', 8, 1, '2025-09-20 02:57:47', '2025-09-20 02:57:47', NULL, NULL, NULL, NULL, NULL),
(3, 'MUHAMAMD FAIZAN', 'Ghakhar Mandi', 'NEW ABADI H NO 278 ST 13 GHAKHAR TEHSIL WAZIRABAD DISTRICT GUJRANWALA 52250 GUJRANWALA PUNJAB PK', '3410458626759', '03377777093', 'faizanrajput488@gmail.com', '12121212', 10, 1, '2025-09-22 10:37:29', '2025-09-22 10:37:29', NULL, NULL, NULL, NULL, NULL),
(5, 'testing customer abc', 'Grw', 'abc', '123', '123', '123@abc.com', '123', 15, 1, '2025-10-01 14:35:07', '2025-10-01 14:35:07', NULL, NULL, NULL, NULL, NULL),
(6, 'AWAIS METAL RECYCLING', 'GUJRANWALA', 'gujranwala', '111111111111111', '03237460883', 'fastoffice786@gmail.com', '125486', 20, 7, '2025-10-25 11:56:48', '2025-10-25 11:57:03', NULL, NULL, NULL, NULL, NULL),
(7, 'SHAFIQUE PLASTIC', 'GUJRANWALA', 'AASD', 'SSDD', '03007793843', 'SHAFIQUE@GAMIL.COM', '1112225', 21, 7, '2025-10-25 12:05:19', '2025-10-25 12:05:19', NULL, NULL, NULL, NULL, NULL),
(8, 'N-B TRADERS', 'GUJRANWALA', 'FAST OFFICE , TRUST PLAZA T- BLOCK ROOM #15, G.T ROAD.', '112232111', '03236275503', 'NBTRADER@GMAIL.COM', '121212', 22, 7, '2025-10-25 12:11:20', '2025-10-25 12:11:20', NULL, NULL, NULL, NULL, NULL),
(9, 'TIME STAR', 'GUJRANWALA', 'FAST OFFICE , TRUST PLAZA T- BLOCK ROOM #15, G.T ROAD.', '45434542', '0325252882', 'fastoffice786@gmail.com', '524652', 25, 7, '2025-10-25 13:22:24', '2025-10-25 13:22:24', NULL, NULL, NULL, NULL, NULL),
(12, 'PAK METAL WOKS ( RANA MUNAWAR )', 'GUJRANWALA', 'FAST OFFICE , TRUST PLAZA T- BLOCK ROOM #15, G.T ROAD.', '14632465462', '03212426825', 'fastoffice786@gmail.com', '5452555', 28, 7, '2025-10-25 13:54:27', '2025-10-25 13:54:27', NULL, NULL, NULL, NULL, NULL),
(13, 'UNITED GROUP', 'GUJRANWALA', 'FAST OFFICE , TRUST PLAZA T- BLOCK ROOM #15, G.T ROAD.', '22122215213', '03212426924', 'fastoffice786@gmail.com', '26462675', 29, 7, '2025-10-25 14:00:58', '2025-10-25 14:00:58', NULL, NULL, NULL, NULL, NULL),
(14, 'AMNA ENTERPRICES', 'GUJRANWALA', 'FAST OFFICE , TRUST PLAZA T- BLOCK ROOM #15, G.T ROAD.', '25524454646', '03212426825', 'fastoffice786@gmail.com', '125487', 30, 7, '2025-10-25 14:03:35', '2025-10-25 14:03:35', NULL, NULL, NULL, NULL, NULL),
(15, 'OFFICE ENTEETAINMENT EXPENCE', 'GUJRANWALA', 'FAST OFFICE , TRUST PLAZA T- BLOCK ROOM #15, G.T ROAD.', '2165413465', '54544654', 'fastoffice786@gmail.com', '65656454', 31, 7, '2025-10-25 14:36:43', '2025-10-25 14:36:43', NULL, NULL, NULL, NULL, NULL),
(16, 'ATLAS ENGINEERING', 'GUJRANWALA', 'FAST OFFICE , TRUST PLAZA T- BLOCK ROOM #15, G.T ROAD.', '14632465462', '03212426924', 'fastoffice786@gmail.com', '45412465', 32, 7, '2025-10-25 14:52:45', '2025-10-25 14:52:45', NULL, NULL, NULL, NULL, NULL),
(17, 'SALARY EXPENCE', 'GUJRANWALA', 'FAST OFFICE , TRUST PLAZA T- BLOCK ROOM #15, G.T ROAD.', '446543231', '65656552', 'fastoffice786@gmail.com', '9465464', 33, 7, '2025-10-25 15:17:38', '2025-10-25 15:17:38', NULL, NULL, NULL, NULL, NULL),
(18, 'United Trading Company', 'Gujranwala', 'Gujranwala', '000000000000', '00000000000000', '00000000@00000000000', '00000000000', 36, 1, '2025-11-11 14:54:34', '2025-12-10 19:21:16', 'Individual', 'Both', '00000000000', '00000000000000', 'Softix'),
(19, 'Rahil Sb. Purchase', NULL, NULL, NULL, NULL, 'info@Azgard9.com', NULL, 38, 11, '2025-12-09 17:29:04', '2025-12-09 17:29:04', NULL, NULL, NULL, NULL, 'Azgard Nine Ltd.'),
(20, 'Hafiz Umer Sb', NULL, '46-Km Phool Nagar', NULL, '03213526436', NULL, '4005994-4', 39, 11, '2025-12-19 14:21:01', '2025-12-19 14:24:06', 'PVT LTD', NULL, NULL, NULL, 'Ulfat Textile Mills (PVT) LTD.'),
(21, 'Custmer', NULL, NULL, NULL, NULL, NULL, NULL, 40, 1, '2026-01-06 15:38:01', '2026-01-06 15:38:01', NULL, NULL, NULL, NULL, 'TEST'),
(23, 'test', 'Faisalabad', NULL, '0', '0321-1234567', NULL, NULL, 70, 11, '2026-02-14 09:44:42', '2026-02-14 09:46:17', 'Individual', 'Both', NULL, NULL, 'test'),
(24, 'zillay', NULL, NULL, NULL, NULL, 'zillayshah002@gmail.com', NULL, 72, 13, '2026-02-16 08:33:43', '2026-02-16 08:33:43', NULL, NULL, NULL, NULL, 'softix');

-- --------------------------------------------------------

--
-- Table structure for table `erp_params`
--

CREATE TABLE `erp_params` (
  `id` int NOT NULL,
  `cash_level` bigint UNSIGNED DEFAULT NULL,
  `employee_level` bigint DEFAULT NULL,
  `employee_advance` bigint DEFAULT NULL,
  `bank_level` bigint UNSIGNED DEFAULT NULL,
  `supplier_level` bigint UNSIGNED DEFAULT NULL,
  `purchase_account` bigint UNSIGNED DEFAULT NULL,
  `purchase_return_account` int DEFAULT NULL,
  `sale_ac` bigint UNSIGNED DEFAULT NULL,
  `customer_level` bigint UNSIGNED DEFAULT NULL,
  `cash_acc` bigint UNSIGNED DEFAULT NULL,
  `pur_freight` bigint UNSIGNED DEFAULT NULL,
  `pur_freight_exp` int DEFAULT NULL,
  `sale_freight` int DEFAULT NULL,
  `sale_freight_exp` int DEFAULT NULL,
  `salary_level` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL,
  `purchase_debit_account` bigint UNSIGNED DEFAULT NULL,
  `saleCreditAccount` int DEFAULT NULL,
  `capital_account` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `erp_params`
--

INSERT INTO `erp_params` (`id`, `cash_level`, `employee_level`, `employee_advance`, `bank_level`, `supplier_level`, `purchase_account`, `purchase_return_account`, `sale_ac`, `customer_level`, `cash_acc`, `pur_freight`, `pur_freight_exp`, `sale_freight`, `sale_freight_exp`, `salary_level`, `created_at`, `updated_at`, `cid`, `purchase_debit_account`, `saleCreditAccount`, `capital_account`) VALUES
(3, 4, NULL, NULL, 3, 6, 1, NULL, NULL, 8, 2, NULL, NULL, NULL, NULL, NULL, '2025-09-18 00:51:19', '2026-02-13 11:33:35', 1, 1, 2, NULL),
(4, 15, NULL, NULL, 14, 13, 17, NULL, NULL, 12, 19, NULL, NULL, NULL, NULL, NULL, '2025-10-15 12:13:14', '2025-10-25 11:38:06', 7, 17, NULL, NULL),
(5, 22, NULL, NULL, 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-09 14:26:58', '2025-12-09 14:26:58', NULL, NULL, NULL, NULL),
(6, 22, NULL, NULL, 19, NULL, 37, NULL, 37, 19, 37, NULL, NULL, NULL, NULL, NULL, '2025-12-09 17:17:07', '2025-12-09 17:17:07', 11, NULL, NULL, 37),
(7, 45, 41, NULL, 46, 40, 46, NULL, 47, 43, 44, NULL, NULL, NULL, NULL, NULL, '2026-02-07 10:38:47', '2026-02-07 10:38:47', 13, NULL, NULL, 42),
(8, 59, 55, NULL, 60, 54, 60, NULL, 61, 57, 58, NULL, NULL, NULL, NULL, NULL, '2026-02-14 08:57:20', '2026-02-14 08:57:20', 14, NULL, NULL, 57);

-- --------------------------------------------------------

--
-- Table structure for table `expense_detail`
--

CREATE TABLE `expense_detail` (
  `id` bigint UNSIGNED NOT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `v_no` bigint UNSIGNED NOT NULL,
  `expense_type` varchar(255)   DEFAULT NULL,
  `amount` varchar(255)   DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_masters`
--

CREATE TABLE `expense_masters` (
  `id` bigint UNSIGNED NOT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `v_no` bigint UNSIGNED NOT NULL,
  `total_amount` varchar(255)   DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_masters`
--

INSERT INTO `expense_masters` (`id`, `cid`, `v_no`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 4, NULL, '2025-10-24 16:00:50', '2025-10-24 16:00:50'),
(2, 1, 1, NULL, '2025-11-11 17:11:23', '2025-11-11 17:11:23'),
(3, 11, 1, NULL, '2025-12-03 18:00:21', '2025-12-03 18:00:21'),
(4, 1, 1, NULL, '2026-02-13 13:02:49', '2026-02-13 13:02:49'),
(5, 1, 2, NULL, '2026-02-14 07:08:10', '2026-02-14 07:08:10');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` int NOT NULL,
  `uuid` varchar(255)   NOT NULL,
  `connection` text   NOT NULL,
  `queue` text   NOT NULL,
  `payload` longtext   NOT NULL,
  `exception` longtext   NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255)   NOT NULL,
  `group_code` varchar(255)   NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `title`, `group_code`, `created_at`, `updated_at`) VALUES
(1, 'Capital', '01', NULL, NULL),
(2, 'Liabilities', '02', NULL, NULL),
(3, 'Assets', '03', NULL, NULL),
(4, 'Revenue', '04', NULL, NULL),
(5, 'Expenses', '05', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `item_logs`
--

CREATE TABLE `item_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `item_code` varchar(255)   DEFAULT NULL,
  `new_purchase` varchar(255)   DEFAULT NULL,
  `old_purchase` varchar(255)   DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL,
  `type_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_masters`
--

CREATE TABLE `item_masters` (
  `id` int NOT NULL,
  `item_code` varchar(255)   DEFAULT NULL,
  `purchase` varchar(255)   DEFAULT NULL,
  `sale_rate` varchar(255)   DEFAULT NULL,
  `type_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL,
  `sale_in` varchar(255)   DEFAULT NULL,
  `sale_out` varchar(255)   DEFAULT NULL,
  `s_type` bigint UNSIGNED DEFAULT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `cost_price` varchar(255)   DEFAULT NULL,
  `item_type` varchar(255)   DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_masters`
--

INSERT INTO `item_masters` (`id`, `item_code`, `purchase`, `sale_rate`, `type_id`, `created_at`, `updated_at`, `cid`, `sale_in`, `sale_out`, `s_type`, `unit_id`, `cost_price`, `item_type`) VALUES
(5, 'Handle Shoulder Shampo Cover', '20', '30', 6, '2025-10-16 00:12:00', '2025-11-01 20:22:37', 7, '0', '0', 7, 5, '20.00', 'rp'),
(6, 'Final Shampo production', '300', '500', 5, '2025-10-16 00:12:35', '2025-10-17 15:58:37', 7, '10', '20', 7, 5, '130.00', 'fp'),
(15, 'Aluminium Ingot', '627', '0', 16, '2025-10-18 11:30:11', '2025-10-18 12:50:11', 7, '18', '18', 8, 6, '0', 'rp'),
(16, 'bakelight Products', '196', '0', 16, '2025-10-18 11:32:03', '2025-10-18 12:51:02', 7, '18', '18', 7, 6, '0', 'rp'),
(17, 'Phorsphosric Acid', '248', '0', 11, '2025-10-18 11:32:40', '2025-10-18 12:51:44', 7, '18', '18', 7, 6, '0', 'rp'),
(18, 'Packing', '120', '0', 18, '2025-10-18 11:33:59', '2025-10-18 12:51:57', 7, '18', '18', 7, 6, '0', 'rp'),
(19, 'Plastic Bag', '2.96', '0', 18, '2025-10-18 11:34:37', '2025-10-18 12:52:23', 7, '18', '18', 7, 6, '0', 'rp'),
(20, 'Glass Lid', '250.27', '0', 16, '2025-10-18 11:35:17', '2025-10-18 12:52:49', 7, '18', '18', 7, 6, '0', 'rp'),
(21, 'Metal Finish / Anodized', '2500', '3000', 19, '2025-10-18 11:37:03', '2025-10-18 12:56:37', 7, '18', '18', 8, 6, '2209300.00', 'fp'),
(28, 'PW T4.5 H.M', '0', '0', 28, '2025-12-03 17:55:53', '2025-12-09 17:27:33', 11, '0', '0', 10, 8, '53.00', 'fp'),
(29, '58G', '0', '0', 25, '2025-12-03 17:58:24', '2025-12-03 17:58:24', 11, '0', '0', 10, 8, '0', 'rp'),
(30, 'PW', NULL, NULL, 27, '2025-12-09 17:13:58', '2025-12-09 17:13:58', 11, '0', '0', NULL, 8, NULL, 'fp'),
(32, 'Complete Body Set', NULL, NULL, 37, '2026-02-13 12:18:40', '2026-02-14 07:30:48', 1, '18', '18', 6, 11, '11', 'rp'),
(33, 'Iron Front', NULL, NULL, 38, '2026-02-13 12:19:12', '2026-02-13 12:33:04', 1, '18', '18', 6, 1, '322', 'rp'),
(34, 'Housing Iron Sheet', NULL, NULL, 39, '2026-02-13 12:19:40', '2026-02-13 12:33:34', 1, '18', '18', 6, 1, '296', 'rp'),
(35, 'Bloure Motor', NULL, NULL, 40, '2026-02-13 12:20:06', '2026-02-13 12:34:01', 1, '18', '18', 6, 1, '1091', 'rp'),
(36, 'Cooling Pad', NULL, NULL, 41, '2026-02-13 12:22:57', '2026-02-13 12:32:18', 1, '18', '18', 6, 4, '1422', 'rp'),
(37, 'Moving Motor', NULL, NULL, 42, '2026-02-13 12:23:26', '2026-02-13 12:31:36', 1, '18', '18', 6, 1, '196', 'rp'),
(38, 'Capacitor 6U', NULL, NULL, 43, '2026-02-13 12:24:03', '2026-02-13 12:34:33', 1, '18', '18', 6, 1, '24', 'rp'),
(39, 'Water Supply Set', NULL, NULL, 44, '2026-02-13 12:25:22', '2026-02-13 12:35:05', 1, '18', '18', 6, 11, '15', 'rp'),
(40, 'Water Guage Set', NULL, NULL, 45, '2026-02-13 12:25:49', '2026-02-13 12:35:59', 1, '18', '18', 6, 11, '12', 'rp'),
(41, 'Wheel Set', NULL, NULL, 46, '2026-02-13 12:26:22', '2026-02-13 12:37:52', 1, '18', '18', 6, 11, '20', 'rp'),
(42, 'Main Wire', NULL, NULL, 47, '2026-02-13 12:26:52', '2026-02-13 12:38:17', 1, '18', '18', 6, 11, '15', 'rp'),
(43, 'Switch', NULL, NULL, 48, '2026-02-13 12:27:19', '2026-02-13 12:38:42', 1, '18', '18', 6, 1, '10', 'rp'),
(44, 'Light', NULL, NULL, 49, '2026-02-13 12:27:55', '2026-02-13 12:39:05', 1, '18', '18', 6, 1, '5', 'rp'),
(45, 'Nob', NULL, NULL, 50, '2026-02-13 12:28:18', '2026-02-13 12:39:29', 1, '18', '18', 6, 1, '3', 'rp'),
(46, 'Name Plate', NULL, NULL, 51, '2026-02-13 12:28:41', '2026-02-13 12:39:57', 1, '18', '18', 6, 1, '20', 'rp'),
(47, 'Packing Box', NULL, NULL, 52, '2026-02-13 12:29:12', '2026-02-13 12:40:20', 1, '18', '18', 6, 1, '40', 'rp'),
(48, 'Packing Thermopoure', NULL, NULL, 53, '2026-02-13 12:29:45', '2026-02-13 12:40:42', 1, '18', '18', 6, 4, '320', 'rp'),
(49, 'Bags', NULL, NULL, 54, '2026-02-13 12:30:06', '2026-02-13 12:41:25', 1, '18', '18', 6, 4, '368', 'rp'),
(50, 'Screws & Misc', NULL, NULL, 55, '2026-02-13 12:30:32', '2026-02-13 12:41:58', 1, '18', '18', 6, 1, '24', 'rp'),
(51, 'Plastic Cooler Model TCP 1200', NULL, NULL, 56, '2026-02-13 12:57:59', '2026-02-14 07:08:10', 1, '18', '18', 6, 1, '3500.00', 'fp'),
(52, 'Plastic moulding Compound', NULL, NULL, 57, '2026-02-13 13:10:49', '2026-02-13 13:17:08', 1, '18', '18', 6, 4, '173', 'rp'),
(53, 'Electricity Bill', '1', NULL, 58, '2026-02-13 13:43:37', '2026-02-13 13:43:37', 1, '18', '18', 6, 13, NULL, 'rp'),
(54, 'KEYBOARD DELL', '100', NULL, 60, '2026-02-16 08:17:30', '2026-02-16 08:29:07', 13, '0', '0', NULL, 14, '100.00', 'rp');

-- --------------------------------------------------------

--
-- Table structure for table `item_types`
--

CREATE TABLE `item_types` (
  `id` int NOT NULL,
  `item_type` varchar(255)   DEFAULT NULL,
  `type_title` varchar(255)   NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_types`
--

INSERT INTO `item_types` (`id`, `item_type`, `type_title`, `created_at`, `updated_at`, `cid`) VALUES
(1, 'fp', 'Shampoo', '2025-09-17 18:33:21', '2025-09-17 18:33:21', 2),
(5, 'fp', 'Shampoo', '2025-10-16 00:10:31', '2025-10-16 00:10:31', 7),
(6, 'fp', 'Plastic bag', '2025-10-16 00:10:46', '2025-10-18 11:16:58', 7),
(11, 'rp', 'Acid`', '2025-10-18 11:10:42', '2025-10-18 11:10:42', 7),
(13, 'fp', 'HANDLE', '2025-10-18 11:13:01', '2025-10-18 11:13:01', 7),
(14, 'fp', 'Glass lid', '2025-10-18 11:13:53', '2025-10-18 11:13:53', 7),
(15, 'rp', 'Furnice Oil', '2025-10-18 11:15:16', '2025-10-18 11:15:16', 7),
(16, 'rp', 'Aluminium', '2025-10-18 11:15:42', '2025-10-18 11:15:42', 7),
(17, 'fp', 'Carton', '2025-10-18 11:16:32', '2025-10-18 11:16:32', 7),
(18, 'rp', 'Packing', '2025-10-18 11:33:14', '2025-10-18 11:33:14', 7),
(19, 'fp', 'Iozied', '2025-10-18 11:35:56', '2025-10-18 11:35:56', 7),
(24, 'fp', 'PW', '2025-12-03 17:53:33', '2025-12-03 17:53:33', 11),
(25, 'rp', '58G', '2025-12-03 17:57:16', '2025-12-03 17:57:16', 11),
(26, 'fp', '450-PPSI', '2025-12-08 13:18:23', '2025-12-09 17:12:14', 11),
(27, 'fp', 'T4.5 H.M', '2025-12-09 17:13:15', '2025-12-09 17:13:15', 11),
(28, 'fp', 'T4.5 M.M', '2025-12-09 17:13:28', '2025-12-09 17:13:28', 11),
(37, 'rp', 'Complete Body Set', '2026-02-13 11:48:05', '2026-02-13 11:48:05', 1),
(38, 'rp', 'Iron Front', '2026-02-13 11:48:16', '2026-02-13 11:48:16', 1),
(39, 'rp', 'Housing Iron Sheet', '2026-02-13 11:48:28', '2026-02-13 11:48:28', 1),
(40, 'rp', 'Bloure Motor', '2026-02-13 11:48:41', '2026-02-13 11:48:41', 1),
(41, 'rp', 'Cooling Pad', '2026-02-13 11:48:54', '2026-02-13 11:48:54', 1),
(42, 'rp', 'Moving Motor', '2026-02-13 11:49:06', '2026-02-13 11:49:06', 1),
(43, 'rp', 'Capacitor', '2026-02-13 11:49:28', '2026-02-13 11:49:54', 1),
(44, 'rp', 'Water Supply Set', '2026-02-13 11:49:47', '2026-02-13 11:49:47', 1),
(45, 'rp', 'Water Guage Set', '2026-02-13 11:50:09', '2026-02-13 11:50:09', 1),
(46, 'rp', 'Wheel Set', '2026-02-13 11:50:21', '2026-02-13 11:50:21', 1),
(47, 'rp', 'Main Wire', '2026-02-13 11:50:34', '2026-02-13 11:50:34', 1),
(48, 'rp', 'Switch', '2026-02-13 11:50:43', '2026-02-13 11:50:49', 1),
(49, 'rp', 'Light', '2026-02-13 11:51:10', '2026-02-13 11:51:10', 1),
(50, 'rp', 'Nob', '2026-02-13 11:51:22', '2026-02-13 11:51:22', 1),
(51, 'rp', 'Name Plate', '2026-02-13 11:51:34', '2026-02-13 11:51:34', 1),
(52, 'rp', 'Packing Box', '2026-02-13 11:51:48', '2026-02-13 11:51:48', 1),
(53, 'rp', 'Packing Thermopoure', '2026-02-13 11:52:01', '2026-02-13 11:52:01', 1),
(54, 'rp', 'Bags', '2026-02-13 11:52:09', '2026-02-13 11:52:09', 1),
(55, 'rp', 'Screws & Misc', '2026-02-13 11:52:25', '2026-02-13 11:52:25', 1),
(56, 'fp', 'Plastic Cooler', '2026-02-13 12:57:07', '2026-02-13 12:57:07', 1),
(57, 'rp', 'Plastic Moulding Compoung', '2026-02-13 13:09:51', '2026-02-13 13:09:51', 1),
(58, 'rp', 'Factory over head (F.O.H)', '2026-02-13 13:41:53', '2026-02-13 13:41:53', 1),
(59, 'rp', 'Mouse', '2026-02-16 08:08:45', '2026-02-16 08:08:45', 13),
(60, 'rp', 'KEYBOARD', '2026-02-16 08:13:17', '2026-02-16 08:13:17', 13);

-- --------------------------------------------------------

--
-- Table structure for table `journal_vouchers`
--

CREATE TABLE `journal_vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `account_title` varchar(255)   NOT NULL,
  `v_type` varchar(255)   NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `debit_entries` text   NOT NULL,
  `credit_entries` text   NOT NULL,
  `total_debit` varchar(255)   NOT NULL,
  `total_credit` varchar(255)   NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `level1s`
--

CREATE TABLE `level1s` (
  `id` int NOT NULL,
  `title` varchar(255)   NOT NULL,
  `level1_code` varchar(255)   NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `level1s`
--

INSERT INTO `level1s` (`id`, `title`, `level1_code`, `group_id`, `created_at`, `updated_at`, `cid`) VALUES
(2, 'Current Assets', '01', 3, '2025-09-17 18:49:15', '2025-09-17 18:49:15', 2),
(3, 'Current Assets', '01', 3, '2025-09-18 00:40:07', '2025-09-18 00:40:07', 1),
(4, 'Direct Expense', '01', 5, '2025-09-18 00:55:45', '2025-09-18 00:55:45', 1),
(5, 'Current Liabilities', '01', 2, '2025-09-18 01:09:06', '2025-09-18 01:09:06', 1),
(6, 'Current Revenue', '01', 4, '2025-09-18 12:33:46', '2025-09-18 12:33:46', 1),
(7, 'Current Assets', '01', 3, '2025-09-20 14:32:26', '2025-09-20 14:32:26', 7),
(8, 'Current Liabilities', '01', 2, '2025-09-20 14:32:38', '2025-09-20 14:32:38', 7),
(9, 'Testing Level1', '01', 1, '2025-09-22 01:03:53', '2025-09-22 01:03:53', 1),
(11, 'Direct Expense', '01', 5, '2025-10-15 12:12:20', '2025-10-15 12:12:20', 7),
(12, 'Current Assets', '01', 3, '2025-10-25 11:19:24', '2025-10-25 11:19:24', 9),
(13, 'Current Liability', '01', 2, '2025-10-25 11:20:03', '2025-10-25 11:20:03', 9),
(14, 'Direct Expense', '01', 5, '2025-10-25 11:20:22', '2025-10-25 11:20:22', 9),
(15, 'Owner', '01', 1, '2025-12-07 16:57:42', '2025-12-07 17:05:06', 11),
(16, 'Sale Revenue', '01', 4, '2025-12-07 16:58:40', '2025-12-07 16:58:40', 11),
(17, 'Current Liabilities', '01', 2, '2025-12-07 16:59:06', '2025-12-07 16:59:06', 11),
(18, 'Current Assets', '01', 3, '2025-12-07 16:59:22', '2025-12-07 16:59:22', 11),
(19, 'Direct Expenses', '01', 5, '2025-12-07 16:59:41', '2025-12-07 16:59:41', 11),
(28, 'Authorized Capital', '001', 1, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(29, 'Short Term Liabilities', '001', 2, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(30, 'Long Term Liabilities', '002', 2, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(31, 'Current Assets', '001', 3, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(32, 'Fixed Assets', '002', 3, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(33, 'Branch Revenue', '001', 4, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(34, 'Direct Expenses', '001', 5, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(35, 'In-Direct Expenses', '002', 5, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(36, 'Authorized Capital', '001', 1, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(37, 'Short Term Liabilities', '001', 2, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(38, 'Long Term Liabilities', '002', 2, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(39, 'Current Assets', '001', 3, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(40, 'Fixed Assets', '002', 3, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(41, 'Branch Revenue', '001', 4, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(42, 'Direct Expenses', '001', 5, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(43, 'In-Direct Expenses', '002', 5, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14);

-- --------------------------------------------------------

--
-- Table structure for table `level2s`
--

CREATE TABLE `level2s` (
  `id` int NOT NULL,
  `title` varchar(255)   NOT NULL,
  `level2_code` varchar(255)   NOT NULL,
  `level1_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `level2s`
--

INSERT INTO `level2s` (`id`, `title`, `level2_code`, `level1_id`, `created_at`, `updated_at`, `cid`) VALUES
(2, 'Bank Accounts', '001', 2, '2025-09-17 18:50:09', '2025-09-17 18:50:09', 2),
(3, 'Bank Accounts', '001', 3, '2025-09-18 00:53:57', '2025-09-18 00:53:57', 1),
(4, 'Cash Accounts', '002', 3, '2025-09-18 00:54:07', '2025-09-18 00:54:07', 1),
(5, 'Purchase Debit Level', '001', 4, '2025-09-18 00:56:18', '2025-09-18 00:56:18', 1),
(6, 'Suppliers', '001', 5, '2025-09-18 01:09:20', '2025-09-18 01:09:20', 1),
(7, 'Sale Credit Accunts', '001', 6, '2025-09-18 12:34:10', '2025-09-22 10:44:03', 1),
(8, 'Customer', '002', 5, '2025-09-18 16:21:49', '2025-09-18 16:21:49', 1),
(9, 'Testing Level 2', '001', 9, '2025-09-22 01:05:14', '2025-09-22 01:05:14', 1),
(11, 'Employees', '003', 5, '2025-10-01 14:43:39', '2025-10-01 14:43:39', 1),
(12, 'Customers', '001', 7, '2025-10-15 12:10:40', '2025-10-15 12:10:59', 7),
(13, 'Supplier', '002', 8, '2025-10-15 12:10:51', '2025-10-15 12:10:51', 7),
(14, 'Bank Accounts', '002', 7, '2025-10-15 12:11:30', '2025-10-15 12:11:30', 7),
(15, 'Cash Accounts', '003', 7, '2025-10-15 12:11:44', '2025-10-15 12:11:44', 7),
(16, 'Purchase Accounts', '001', 11, '2025-10-15 12:12:39', '2025-10-15 12:12:39', 7),
(17, 'Expense Account', '002', 11, '2025-10-25 11:35:48', '2025-10-25 11:35:48', 7),
(18, 'Capital Account', '001', 15, '2025-12-07 17:02:14', '2025-12-07 17:02:14', 11),
(19, 'Customer Level', '001', 18, '2025-12-07 17:02:48', '2025-12-07 17:02:48', 11),
(20, 'Bank Level', '002', 18, '2025-12-07 17:03:19', '2025-12-07 17:03:19', 11),
(21, 'Sale Level', '001', 16, '2025-12-07 17:03:41', '2025-12-07 17:03:41', 11),
(22, 'Cash level', '003', 18, '2025-12-07 17:03:58', '2025-12-07 17:03:58', 11),
(23, 'Stock Account', '001', 19, '2025-12-07 17:04:25', '2025-12-07 17:04:25', 11),
(24, 'Supplier', '001', 17, '2025-12-07 17:04:41', '2025-12-07 17:04:41', 11),
(39, 'Shared Capital', '001', 28, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(40, 'Suppliers', '001', 29, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(41, 'Employees', '002', 29, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(42, 'Long Term Loans', '001', 30, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(43, 'Customers', '001', 31, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(44, 'Stocks', '002', 31, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(45, 'Cash Accounts', '003', 31, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(46, 'Bank Accounts', '004', 31, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(47, 'Land and Building', '001', 32, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(48, 'Revenue from Sales', '001', 33, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(49, 'Revenue from Other Operations', '002', 33, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(50, 'Office Expenses', '001', 35, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(51, 'Utility Bills', '002', 35, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(52, 'Taxation', '003', 35, '2026-02-07 10:36:53', '2026-02-07 10:36:53', 13),
(53, 'Shared Capital', '001', 36, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(54, 'Suppliers', '001', 37, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(55, 'Employees', '002', 37, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(56, 'Long Term Loans', '001', 38, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(57, 'Customers', '001', 39, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(58, 'Stocks', '002', 39, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(59, 'Cash Accounts', '003', 39, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(60, 'Bank Accounts', '004', 39, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(61, 'Land and Building', '001', 40, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(62, 'Revenue from Sales', '001', 41, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(63, 'Revenue from Other Operations', '002', 41, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(64, 'Office Expenses', '001', 43, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(65, 'Utility Bills', '002', 43, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14),
(66, 'Taxation', '003', 43, '2026-02-14 08:56:22', '2026-02-14 08:56:22', 14);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255)   NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2025_09_18_120600_create_purchase_return_masters_table', 2),
(3, '2025_09_18_120750_create_purchase_return_invoices_table', 2),
(4, '2025_09_18_134602_create_customers_table', 3),
(5, '2025_09_18_135718_create_sale_invoice_table', 4),
(6, '2025_09_18_140058_create_sale_invoice_return_table', 4),
(7, '2025_09_19_172738_create_sale_return_masters_table', 5),
(8, '2025_09_19_184055_create_sale_return_invoices_table', 5),
(9, '2025_09_20_155910_add_cid_to_item_logs_table', 6),
(10, '2025_10_15_133111_add_item_type_to_item_type_table', 7),
(11, '2025_10_15_151806_add_col_to_item_masters_table', 7),
(12, '2025_10_15_152737_create_production_master_table', 7),
(13, '2025_10_15_153012_create_production_detail_table', 7),
(14, '2025_10_15_170406_create_consumption_masters_table', 7),
(15, '2025_10_15_170412_create_consumption_details_table', 7),
(16, '2025_10_16_003713_create_stock_adj_master_table', 8),
(17, '2025_10_16_003734_create_stock_adj_table', 8),
(18, '2025_10_16_005456_add_col_open_qty_to_item_masters_table', 8),
(19, '2025_10_16_123138_create_production_consumed_table', 9),
(20, '2025_10_17_191353_update_col_supplier_table', 10),
(21, '2025_10_18_102053_add_col_to_production_master_table', 11),
(22, '2025_10_18_115508_create_expense_detail_table', 12),
(23, '2025_10_18_115646_create_expense_masters_table', 12),
(24, '2025_10_31_121855_create_opening_stock_table', 13),
(25, '2025_10_31_144758_drop_op_qty_to_item_masters_table', 14),
(26, '2025_10_31_145805_add_rate_to_opening_stock_table', 15),
(27, '2025_10_31_191249_add_rate_to_opening_stock_table', 15),
(28, '2025_11_01_121231_add_col_to_purchase_master', 15),
(29, '2025_11_01_121337_add_col_to_purchase_return_master', 15),
(30, '2025_11_01_121413_add_col_to_sale_master', 15),
(31, '2025_11_01_121431_add_col_to_sale_return_master', 15),
(32, '2025_11_01_125347_add_col_to_production_master', 15),
(33, '2025_11_01_125552_add_col_to_production_master', 15),
(34, '2025_11_04_030652_add_col_to_customers_table', 16);

-- --------------------------------------------------------

--
-- Table structure for table `opening_stock`
--

CREATE TABLE `opening_stock` (
  `id` bigint UNSIGNED NOT NULL,
  `item_id` varchar(50) CHARACTER SET utf8mb4   DEFAULT NULL,
  `qty` varchar(255)   NOT NULL,
  `cid` varchar(50) CHARACTER SET utf8mb4   DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rate` varchar(255)   NOT NULL,
  `v_no` bigint UNSIGNED NOT NULL,
  `v_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parties`
--

CREATE TABLE `parties` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255)   NOT NULL,
  `balance` varchar(255)   DEFAULT NULL,
  `registered` enum('official','unofficial')   NOT NULL DEFAULT 'official',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255)   NOT NULL,
  `token` varchar(255)   NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255)   NOT NULL,
  `token` varchar(255)   NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255)   NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255)   NOT NULL,
  `token` varchar(64)   NOT NULL,
  `abilities` text  ,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_consumed`
--

CREATE TABLE `production_consumed` (
  `id` bigint UNSIGNED NOT NULL,
  `v_no` bigint NOT NULL,
  `fp_item_id` bigint UNSIGNED NOT NULL,
  `rp_item_id` bigint UNSIGNED NOT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `qty` varchar(255)   NOT NULL,
  `rate` varchar(255)   NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_consumed`
--

INSERT INTO `production_consumed` (`id`, `v_no`, `fp_item_id`, `rp_item_id`, `cid`, `qty`, `rate`, `created_at`, `updated_at`) VALUES
(16, 1, 51, 33, 1, '10', '322', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(17, 1, 51, 34, 1, '10', '296', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(18, 1, 51, 35, 1, '20', '1091', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(19, 1, 51, 36, 1, '0.3', '1422', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(20, 1, 51, 37, 1, '40', '196', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(21, 1, 51, 38, 1, '10', '24', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(22, 1, 51, 39, 1, '40', '15', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(23, 1, 51, 40, 1, '10', '12', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(24, 1, 51, 41, 1, '40', '20', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(25, 1, 51, 42, 1, '10', '15', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(26, 1, 51, 43, 1, '10', '10', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(27, 1, 51, 44, 1, '30', '5', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(28, 1, 51, 45, 1, '30', '3', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(29, 1, 51, 46, 1, '10', '20', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(30, 1, 51, 47, 1, '10', '40', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(31, 1, 51, 48, 1, '0.3', '320', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(32, 1, 51, 49, 1, '0.3', '368', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(33, 1, 51, 50, 1, '50', '24', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(34, 1, 51, 52, 1, '200', '173', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(35, 2, 51, 32, 1, '1', '3500', '2026-02-14 07:17:11', '2026-02-14 07:17:11');

-- --------------------------------------------------------

--
-- Table structure for table `production_detail`
--

CREATE TABLE `production_detail` (
  `id` bigint UNSIGNED NOT NULL,
  `v_no` bigint UNSIGNED NOT NULL,
  `item_id` varchar(50) CHARACTER SET utf8mb4   DEFAULT NULL,
  `qty` varchar(255)   NOT NULL,
  `rate` varchar(255)   NOT NULL,
  `amount` varchar(255)   NOT NULL,
  `cid` varchar(255)   NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_detail`
--

INSERT INTO `production_detail` (`id`, `v_no`, `item_id`, `qty`, `rate`, `amount`, `cid`, `created_at`, `updated_at`) VALUES
(7, 1, '6', '100', '30.00', '3000', '7', '2025-11-01 20:25:56', '2025-11-01 20:25:56'),
(8, 2, '6', '10', '130.00', '1300', '7', '2025-11-01 20:26:25', '2025-11-01 20:26:25'),
(15, 1, '51', '10', '7512.30', '75123', '1', '2026-02-13 13:30:53', '2026-02-13 13:30:53'),
(16, 2, '51', '1', '3500.00', '3500', '1', '2026-02-14 07:17:11', '2026-02-14 07:17:11');

-- --------------------------------------------------------

--
-- Table structure for table `production_master`
--

CREATE TABLE `production_master` (
  `id` bigint UNSIGNED NOT NULL,
  `v_no` bigint UNSIGNED NOT NULL,
  `descr` varchar(255)   DEFAULT NULL,
  `amount` varchar(255)   NOT NULL,
  `cid` varchar(50) CHARACTER SET utf8mb4   DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `v_date` varchar(255)   NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_master`
--

INSERT INTO `production_master` (`id`, `v_no`, `descr`, `amount`, `cid`, `created_at`, `updated_at`, `v_date`) VALUES
(1, 1, 'Goods sale', '3000', '7', '2025-10-16 13:16:14', '2025-11-01 20:25:56', '2025-11-01'),
(2, 2, 'asdad', '1300', '7', '2025-10-17 16:05:05', '2025-11-01 20:26:25', '2025-11-01'),
(7, 1, NULL, '75123', '1', '2026-02-13 13:30:53', '2026-02-13 13:30:53', '2026-02-01'),
(8, 2, '1212', '3500', '1', '2026-02-14 07:17:11', '2026-02-14 07:17:11', '2026-02-14');

-- --------------------------------------------------------

--
-- Table structure for table `product_log`
--

CREATE TABLE `product_log` (
  `id` bigint UNSIGNED NOT NULL,
  `prod_id` int DEFAULT NULL,
  `prod_name` varchar(255)   DEFAULT NULL,
  `old_rate` float DEFAULT NULL,
  `new_rate` float DEFAULT NULL,
  `action` varchar(255)   DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_master`
--

CREATE TABLE `product_master` (
  `id` bigint UNSIGNED NOT NULL,
  `aid` bigint DEFAULT NULL,
  `product_type` varchar(255)   DEFAULT NULL,
  `prod_name` varchar(255)   DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `grammage` float DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `width` float DEFAULT NULL,
  `length` float DEFAULT NULL,
  `ups` int DEFAULT NULL,
  `descr` varchar(1000)   DEFAULT NULL,
  `lamination` tinyint(1) NOT NULL DEFAULT '0',
  `corrugation` tinyint(1) NOT NULL DEFAULT '0',
  `uv` tinyint(1) NOT NULL DEFAULT '0',
  `color` tinyint(1) NOT NULL DEFAULT '0',
  `lam_size` float DEFAULT NULL,
  `lam_item` int DEFAULT NULL,
  `curr_size` float DEFAULT NULL,
  `curr_item` int DEFAULT NULL,
  `color_no` int DEFAULT NULL,
  `file_path` varchar(255)   DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `simple` int NOT NULL DEFAULT '0',
  `spot` int NOT NULL DEFAULT '0',
  `limpression` float DEFAULT NULL,
  `clabour` float DEFAULT NULL,
  `breaking_rate` float DEFAULT NULL,
  `manual_pasting_rate` float DEFAULT NULL,
  `auto_pasting_rate` double DEFAULT NULL,
  `breaking` bigint DEFAULT '0',
  `spot_rate` float DEFAULT NULL,
  `simple_rate` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prod_cons`
--

CREATE TABLE `prod_cons` (
  `id` int NOT NULL,
  `stock_report_id` int DEFAULT NULL,
  `item_code` varchar(255)   NOT NULL,
  `cquantity` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prod_masters`
--

CREATE TABLE `prod_masters` (
  `id` int NOT NULL,
  `v_no` int DEFAULT NULL,
  `date` date NOT NULL,
  `prepared_by` varchar(255)   NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_details`
--

CREATE TABLE `purchase_details` (
  `id` bigint UNSIGNED NOT NULL,
  `vorcher_no` int NOT NULL,
  `width` float NOT NULL,
  `lenght` float NOT NULL,
  `grammage` float NOT NULL,
  `qty` float NOT NULL,
  `rate` float NOT NULL,
  `total_wt` float NOT NULL,
  `amount` float NOT NULL,
  `item_code` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `freight` double DEFAULT NULL,
  `freight_type` varchar(255)   DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_invoices`
--

CREATE TABLE `purchase_invoices` (
  `id` int NOT NULL,
  `v_no` bigint DEFAULT NULL,
  `qty` int UNSIGNED NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `exclusive` decimal(10,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL,
  `inclusive` decimal(10,2) NOT NULL,
  `item_id` varchar(50) CHARACTER SET utf8mb4   DEFAULT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_invoices`
--

INSERT INTO `purchase_invoices` (`id`, `v_no`, `qty`, `rate`, `exclusive`, `tax_rate`, `tax_amount`, `inclusive`, `item_id`, `supplier_id`, `cid`, `created_at`, `updated_at`) VALUES
(30, 1, 20, 20.00, 400.00, 0.00, 0.00, 400.00, '5', 4, 7, '2025-11-01 20:22:37', '2025-11-01 20:22:37'),
(37, 1, 100, 3500.00, 350000.00, 18.00, 63000.00, 413000.00, '32', 2, 1, '2026-02-13 12:31:14', '2026-02-13 12:31:14'),
(38, 2, 500, 196.00, 98000.00, 18.00, 17640.00, 115640.00, '37', 1, 1, '2026-02-13 12:31:36', '2026-02-13 12:31:36'),
(39, 3, 500, 1422.00, 711000.00, 18.00, 127980.00, 838980.00, '36', 5, 1, '2026-02-13 12:32:18', '2026-02-13 12:32:18'),
(40, 4, 50, 322.00, 16100.00, 18.00, 2898.00, 18998.00, '33', 5, 1, '2026-02-13 12:33:04', '2026-02-13 12:33:04'),
(41, 5, 150, 296.00, 44400.00, 18.00, 7992.00, 52392.00, '34', 3, 1, '2026-02-13 12:33:34', '2026-02-13 12:33:34'),
(42, 6, 740, 1091.00, 807340.00, 18.00, 145321.20, 952661.20, '35', 1, 1, '2026-02-13 12:34:01', '2026-02-13 12:34:01'),
(43, 7, 150, 24.00, 3600.00, 18.00, 648.00, 4248.00, '38', 2, 1, '2026-02-13 12:34:33', '2026-02-13 12:34:33'),
(44, 8, 135, 15.00, 2025.00, 18.00, 364.50, 2389.50, '39', 5, 1, '2026-02-13 12:35:05', '2026-02-13 12:35:05'),
(45, 9, 920, 12.00, 11040.00, 18.00, 1987.20, 13027.20, '40', 3, 1, '2026-02-13 12:35:59', '2026-02-13 12:35:59'),
(46, 10, 500, 20.00, 10000.00, 18.00, 1800.00, 11800.00, '41', 1, 1, '2026-02-13 12:37:52', '2026-02-13 12:37:52'),
(47, 11, 980, 15.00, 14700.00, 18.00, 2646.00, 17346.00, '42', 3, 1, '2026-02-13 12:38:17', '2026-02-13 12:38:17'),
(48, 12, 1720, 10.00, 17200.00, 18.00, 3096.00, 20296.00, '43', 5, 1, '2026-02-13 12:38:42', '2026-02-13 12:38:42'),
(49, 13, 1560, 5.00, 7800.00, 18.00, 1404.00, 9204.00, '44', 1, 1, '2026-02-13 12:39:05', '2026-02-13 12:39:05'),
(50, 14, 850, 3.00, 2550.00, 18.00, 459.00, 3009.00, '45', 5, 1, '2026-02-13 12:39:29', '2026-02-13 12:39:29'),
(51, 15, 250, 20.00, 5000.00, 18.00, 900.00, 5900.00, '46', 1, 1, '2026-02-13 12:39:57', '2026-02-13 12:39:57'),
(52, 16, 600, 40.00, 24000.00, 18.00, 4320.00, 28320.00, '47', 2, 1, '2026-02-13 12:40:20', '2026-02-13 12:40:20'),
(53, 17, 750, 320.00, 240000.00, 18.00, 43200.00, 283200.00, '48', 5, 1, '2026-02-13 12:40:42', '2026-02-13 12:40:42'),
(54, 18, 630, 368.00, 231840.00, 18.00, 41731.20, 273571.20, '49', 2, 1, '2026-02-13 12:41:25', '2026-02-13 12:41:25'),
(55, 19, 1000, 24.00, 24000.00, 18.00, 4320.00, 28320.00, '50', 5, 1, '2026-02-13 12:41:58', '2026-02-13 12:41:58'),
(57, 20, 6250, 173.00, 1081250.00, 18.00, 194625.00, 1275875.00, '52', 2, 1, '2026-02-13 13:17:08', '2026-02-13 13:17:08'),
(58, 21, 1, 11.00, 11.00, 18.00, 1.98, 12.98, '32', 2, 1, '2026-02-14 07:30:48', '2026-02-14 07:30:48'),
(59, 1, 100, 100.00, 10000.00, 0.00, 0.00, 10000.00, '54', 6, 13, '2026-02-16 08:29:07', '2026-02-16 08:29:07');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_master`
--

CREATE TABLE `purchase_master` (
  `id` int NOT NULL,
  `v_no` bigint NOT NULL,
  `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cid` varchar(50) CHARACTER SET utf8mb4   DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `v_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_master`
--

INSERT INTO `purchase_master` (`id`, `v_no`, `grand_total`, `discount`, `cid`, `created_at`, `updated_at`, `v_date`) VALUES
(6, 1, 300.00, 100.00, '7', '2025-10-17 15:52:39', '2025-11-01 20:22:37', '2025-11-01'),
(14, 1, 413000.00, 0.00, '1', '2026-02-13 12:31:14', '2026-02-13 12:31:14', '2026-01-01'),
(15, 2, 115640.00, 0.00, '1', '2026-02-13 12:31:36', '2026-02-13 12:31:36', '2026-01-01'),
(16, 3, 838980.00, 0.00, '1', '2026-02-13 12:32:18', '2026-02-13 12:32:18', '2026-01-01'),
(17, 4, 18998.00, 0.00, '1', '2026-02-13 12:33:04', '2026-02-13 12:33:04', '2026-01-01'),
(18, 5, 52392.00, 0.00, '1', '2026-02-13 12:33:34', '2026-02-13 12:33:34', '2026-01-01'),
(19, 6, 952661.20, 0.00, '1', '2026-02-13 12:34:01', '2026-02-13 12:34:01', '2026-01-01'),
(20, 7, 4248.00, 0.00, '1', '2026-02-13 12:34:33', '2026-02-13 12:34:33', '2026-01-01'),
(21, 8, 2389.50, 0.00, '1', '2026-02-13 12:35:05', '2026-02-13 12:35:05', '2026-01-01'),
(22, 9, 13027.20, 0.00, '1', '2026-02-13 12:35:59', '2026-02-13 12:35:59', '2026-01-01'),
(23, 10, 11800.00, 0.00, '1', '2026-02-13 12:37:52', '2026-02-13 12:37:52', '2026-01-01'),
(24, 11, 17346.00, 0.00, '1', '2026-02-13 12:38:17', '2026-02-13 12:38:17', '2026-01-01'),
(25, 12, 20296.00, 0.00, '1', '2026-02-13 12:38:42', '2026-02-13 12:38:42', '2026-01-01'),
(26, 13, 9204.00, 0.00, '1', '2026-02-13 12:39:05', '2026-02-13 12:39:05', '2026-01-01'),
(27, 14, 3009.00, 0.00, '1', '2026-02-13 12:39:29', '2026-02-13 12:39:29', '2026-01-01'),
(28, 15, 5900.00, 0.00, '1', '2026-02-13 12:39:57', '2026-02-13 12:39:57', '2026-01-01'),
(29, 16, 28320.00, 0.00, '1', '2026-02-13 12:40:20', '2026-02-13 12:40:20', '2026-01-01'),
(30, 17, 283200.00, 0.00, '1', '2026-02-13 12:40:42', '2026-02-13 12:40:42', '2026-01-01'),
(31, 18, 273571.20, 0.00, '1', '2026-02-13 12:41:25', '2026-02-13 12:41:25', '2026-01-01'),
(32, 19, 28320.00, 0.00, '1', '2026-02-13 12:41:58', '2026-02-13 12:41:58', '2026-01-01'),
(34, 20, 1275875.00, 0.00, '1', '2026-02-13 13:17:08', '2026-02-13 13:17:08', '2026-02-01'),
(35, 21, 12.98, 0.00, '1', '2026-02-14 07:30:48', '2026-02-14 07:30:48', '2026-02-14'),
(36, 1, 10000.00, 0.00, '13', '2026-02-16 08:29:07', '2026-02-16 08:29:07', '2026-02-16');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `vorcher_no` int NOT NULL,
  `width` float NOT NULL,
  `lenght` float NOT NULL,
  `grammage` float NOT NULL,
  `qty` float NOT NULL,
  `rate` float NOT NULL,
  `total_wt` float NOT NULL,
  `amount` float NOT NULL,
  `item_code` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `freight` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_invoice`
--

CREATE TABLE `purchase_return_invoice` (
  `id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL,
  `rate` decimal(15,2) NOT NULL,
  `exclusive` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `inclusive` decimal(15,2) NOT NULL DEFAULT '0.00',
  `item_id` varchar(50) CHARACTER SET utf8mb4   DEFAULT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `v_no` bigint NOT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_return_invoice`
--

INSERT INTO `purchase_return_invoice` (`id`, `qty`, `rate`, `exclusive`, `tax_rate`, `tax_amount`, `inclusive`, `item_id`, `supplier_id`, `v_no`, `cid`, `created_at`, `updated_at`) VALUES
(14, 10, 100.00, 1000.00, 0.00, 0.00, 1000.00, '54', 6, 1, 13, '2026-02-16 08:31:13', '2026-02-16 08:31:13');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_master`
--

CREATE TABLE `purchase_return_master` (
  `id` bigint UNSIGNED NOT NULL,
  `v_no` bigint NOT NULL,
  `grand_total` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cid` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `v_date` varchar(255)   NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_return_master`
--

INSERT INTO `purchase_return_master` (`id`, `v_no`, `grand_total`, `discount`, `cid`, `created_at`, `updated_at`, `v_date`) VALUES
(7, 1, 1000.00, 0.00, 13, '2026-02-16 08:30:59', '2026-02-16 08:31:13', '2026-02-16');

-- --------------------------------------------------------

--
-- Table structure for table `rights`
--

CREATE TABLE `rights` (
  `id` int NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `app_name` varchar(255)   DEFAULT NULL,
  `add` tinyint(1) DEFAULT '0',
  `del` tinyint(1) DEFAULT '0',
  `edit` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_invoices`
--

CREATE TABLE `sale_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `v_no` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL,
  `rate` decimal(15,2) NOT NULL,
  `exclusive` decimal(15,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  `tax_amount` decimal(15,2) NOT NULL,
  `inclusive` decimal(15,2) NOT NULL,
  `item_id` varchar(50) CHARACTER SET utf8mb4   DEFAULT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_invoices`
--

INSERT INTO `sale_invoices` (`id`, `v_no`, `qty`, `rate`, `exclusive`, `tax_rate`, `tax_amount`, `inclusive`, `item_id`, `customer_id`, `cid`, `created_at`, `updated_at`) VALUES
(24, 2, 78, 0.00, 0.00, 0.00, 0.00, 0.00, '29', 19, 11, '2026-01-06 15:34:53', '2026-01-06 15:34:53'),
(26, 3, 10, 53.00, 530.00, 0.00, 0.00, 530.00, '28', 23, 11, '2026-02-14 09:45:48', '2026-02-14 09:45:48'),
(27, 1, 91, 120.00, 10920.00, 0.00, 0.00, 10920.00, '54', 24, 13, '2026-02-16 08:34:26', '2026-02-16 08:34:26'),
(28, 1, 1, 173.00, 173.00, 18.00, 31.14, 204.14, '52', 1, 1, '2026-02-16 10:23:39', '2026-02-16 10:23:39'),
(29, 2, 1, 3500.00, 3500.00, 18.00, 630.00, 4130.00, '51', 1, 1, '2026-02-16 10:28:12', '2026-02-16 10:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `sale_master`
--

CREATE TABLE `sale_master` (
  `id` bigint UNSIGNED NOT NULL,
  `v_no` bigint NOT NULL,
  `grand_total` varchar(255)   NOT NULL,
  `discount` varchar(255)   NOT NULL DEFAULT '0',
  `cid` varchar(50) CHARACTER SET utf8mb4   DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `v_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_master`
--

INSERT INTO `sale_master` (`id`, `v_no`, `grand_total`, `discount`, `cid`, `created_at`, `updated_at`, `v_date`) VALUES
(21, 1, '106', '0', '11', '2025-12-09 17:29:53', '2025-12-09 17:29:53', '2025-12-09'),
(24, 2, '0', '0', '11', '2026-01-06 15:34:53', '2026-01-06 15:34:53', '0026-01-03'),
(26, 3, '530', '0', '11', '2026-02-14 09:45:48', '2026-02-14 09:45:48', '2026-02-14'),
(27, 1, '10920', '0', '13', '2026-02-16 08:34:26', '2026-02-16 08:34:26', '2026-02-16'),
(28, 1, '204.14', '0', '1', '2026-02-16 10:23:39', '2026-02-16 10:23:39', '2026-02-16'),
(29, 2, '4130', '0', '1', '2026-02-16 10:28:12', '2026-02-16 10:28:12', '2026-02-16');

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_invoices`
--

CREATE TABLE `sale_return_invoices` (
  `id` int NOT NULL,
  `qty` int NOT NULL,
  `rate` decimal(15,2) NOT NULL,
  `exclusive` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `inclusive` decimal(15,2) NOT NULL DEFAULT '0.00',
  `item_id` varchar(50) CHARACTER SET utf8mb4   DEFAULT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `v_no` bigint NOT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_masters`
--

CREATE TABLE `sale_return_masters` (
  `id` int NOT NULL,
  `v_no` bigint NOT NULL,
  `grand_total` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cid` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `v_date` varchar(255)   NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_type`
--

CREATE TABLE `sale_type` (
  `id` int NOT NULL,
  `sale_type` varchar(255)   NOT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_type`
--

INSERT INTO `sale_type` (`id`, `sale_type`, `cid`, `created_at`, `updated_at`) VALUES
(1, 'Taxable', 2, '2025-09-17 18:40:38', '2025-09-17 18:40:38'),
(3, 'Exempt', 1, '2025-09-22 10:34:37', '2025-09-22 10:34:37'),
(6, 'Taxable', 1, '2025-10-15 11:55:10', '2025-10-15 11:55:10'),
(7, 'Exempt', 7, '2025-10-16 00:11:02', '2025-10-16 00:11:02'),
(8, 'Taxable', 7, '2025-10-18 11:20:13', '2025-10-18 11:20:13'),
(9, 'Taxable', 1, '2025-11-11 14:50:55', '2025-11-11 14:50:55'),
(10, 'Unregister', 11, '2025-12-03 17:54:26', '2025-12-03 17:54:26'),
(11, 'Register', 11, '2025-12-09 12:53:54', '2025-12-09 12:53:54');

-- --------------------------------------------------------

--
-- Stand-in structure for view `specific_item`
-- (See below for the actual view)
--
CREATE TABLE `specific_item` (
`adj_amt` double
,`adj_qty` double
,`cid` varchar(50)
,`con_amt` double
,`con_qty` double
,`item_id` varchar(50)
,`opn_amt` double
,`opn_qty` double
,`pret_amt` decimal(47,2)
,`pret_qty` decimal(32,0)
,`pur_amt` decimal(42,2)
,`pur_qty` decimal(32,0)
,`raw_amt` double
,`raw_qty` double
,`sal_amt` decimal(47,2)
,`sal_qty` decimal(32,0)
,`sret_amt` decimal(47,2)
,`sret_qty` decimal(32,0)
,`v_date` date
);

-- --------------------------------------------------------

--
-- Table structure for table `stock_adj_details`
--

CREATE TABLE `stock_adj_details` (
  `id` bigint UNSIGNED NOT NULL,
  `v_no` bigint NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `qty` varchar(255)   NOT NULL DEFAULT '0',
  `rate` varchar(255)   NOT NULL DEFAULT '0',
  `amount` varchar(255)   NOT NULL DEFAULT '0',
  `cid` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `v_date` varchar(100)   NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_adj_details`
--

INSERT INTO `stock_adj_details` (`id`, `v_no`, `item_id`, `qty`, `rate`, `amount`, `cid`, `created_at`, `updated_at`, `v_date`) VALUES
(1, 1, 32, '100', '10', '0', 1, '2026-02-14 06:28:35', '2026-02-14 06:28:35', '');

-- --------------------------------------------------------

--
-- Table structure for table `stock_adj_masters`
--

CREATE TABLE `stock_adj_masters` (
  `id` bigint UNSIGNED NOT NULL,
  `v_no` bigint UNSIGNED NOT NULL,
  `v_date` date DEFAULT NULL,
  `prepared_by` varchar(255)   NOT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_adj_masters`
--

INSERT INTO `stock_adj_masters` (`id`, `v_no`, `v_date`, `prepared_by`, `cid`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-02-14', '1', 1, '2026-02-14 06:28:35', '2026-02-14 06:28:35');

-- --------------------------------------------------------

--
-- Stand-in structure for view `stock_qty_view`
-- (See below for the actual view)
--
CREATE TABLE `stock_qty_view` (
`cid` varchar(50)
,`con_amt` double
,`con_qty` double
,`item_id` varchar(50)
,`opn_amt` double
,`opn_qty` double
,`pret_amt` decimal(47,2)
,`pret_qty` decimal(32,0)
,`pur_amt` decimal(42,2)
,`pur_qty` decimal(32,0)
,`sal_amt` decimal(47,2)
,`sal_qty` decimal(32,0)
,`sret_amt` decimal(47,2)
,`sret_qty` decimal(32,0)
,`v_date` date
);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int NOT NULL,
  `city` varchar(255)   DEFAULT NULL,
  `address` varchar(255)   DEFAULT NULL,
  `cnic` varchar(255)   DEFAULT NULL,
  `mobile` varchar(255)   NOT NULL,
  `email` varchar(255)   DEFAULT NULL,
  `ntn` varchar(255)   DEFAULT NULL,
  `acc_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255)   DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `city`, `address`, `cnic`, `mobile`, `email`, `ntn`, `acc_id`, `created_at`, `updated_at`, `cid`, `name`) VALUES
(1, 'Madiun', 'Jr. Krakatau No. 106', '3410458626759', '03377777092', 'faizanrajput488@gmail.com', '12121212', 5, '2025-09-18 01:17:07', '2025-09-18 01:17:07', 1, 'Faizan Rajput'),
(2, 'abc', 'abc', '111111111111', '1111111111', 'fa@gmail.com', '12121212', 11, '2025-09-22 10:40:54', '2025-09-22 10:40:54', 1, 'Abc'),
(3, 'Gujranwala', 'Gujranwala', '30000-00000-0', '03377777000', 'testuser@gmail.com', '12121213', 13, '2025-09-22 18:55:12', '2025-09-22 18:55:26', 1, 'testuser'),
(4, 'grw', 'asdf', '32342', '234', 'asdfadsf@asdf.com', '3234234', 18, '2025-10-17 15:51:45', '2025-10-17 15:51:45', 7, 'Abc supplier'),
(5, 'Gujranwala', 'Gujranwala', '0000000', '000000000', '00000000@000000', '0000000', 35, '2025-11-11 14:44:27', '2025-11-11 14:44:27', 1, 'United Trading Company'),
(6, 'GUJRANWALA', 'RAHWALI STREET #4 MOHALA BILAL COLONY GUJRANWALA', '3410151258546', '03320141535', 'Welcome@Industry.com', '3410151258546', 71, '2026-02-16 08:22:16', '2026-02-16 08:22:16', 13, 'Sethi centary');

-- --------------------------------------------------------

--
-- Table structure for table `trndtls_deletions_log`
--

CREATE TABLE `trndtls_deletions_log` (
  `id` int NOT NULL,
  `deleted_id` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255)   DEFAULT NULL,
  `v_type` varchar(255)   DEFAULT NULL,
  `r_id` bigint DEFAULT NULL,
  `prepared_by` varchar(255)   DEFAULT NULL,
  `original_data` text  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trndtls_deletions_log`
--

INSERT INTO `trndtls_deletions_log` (`id`, `deleted_id`, `deleted_at`, `deleted_by`, `v_type`, `r_id`, `prepared_by`, `original_data`) VALUES
(0, 1, '2025-09-17 19:40:30', 'realerp_erplive', 'CRV', NULL, 'Admin', 'id: 1, v_no: 1, v_type: CRV, date: 2025-09-15, description: NONEs, debit: 0, credit: 112.00, status: unofficial, preparedby: Admin, cash_id: 1, account_id: 1, created_at: 2025-09-15 13:34:05, updated_at: 2025-09-16 07:13:07'),
(0, 2, '2025-09-17 19:40:43', 'realerp_erplive', 'CPV', NULL, 'Admin', 'id: 2, v_no: 1, v_type: CPV, date: 2025-09-15, description: NONE, debit: 22.00, credit: 0, status: unofficial, preparedby: Admin, cash_id: 1, account_id: 1, created_at: 2025-09-15 13:34:25, updated_at: 2025-09-15 13:34:25'),
(0, 3, '2025-09-17 19:40:51', 'realerp_erplive', 'BRV', NULL, 'Admin', 'id: 3, v_no: 1, v_type: BRV, date: 2025-09-15, description: NONE, debit: 0, credit: 33.00, status: unofficial, preparedby: Admin, cash_id: 1, account_id: 1, created_at: 2025-09-15 13:34:48, updated_at: 2025-09-15 13:34:48'),
(0, 4, '2025-09-17 19:40:58', 'realerp_erplive', 'BPV', NULL, 'Admin', 'id: 4, v_no: 1, v_type: BPV, date: 2025-09-15, description: NONE, debit: 44.00, credit: 0, status: unofficial, preparedby: Admin, cash_id: 1, account_id: 1, created_at: 2025-09-15 13:35:12, updated_at: 2025-09-15 13:35:12'),
(0, 5, '2025-09-17 19:41:14', 'realerp_erplive', 'JV', NULL, 'Admin', 'id: 5, v_no: 1, v_type: JV, date: 2025-09-15, description: NONE, debit: 12, credit: 0, status: unofficial, preparedby: Admin, account_id: 1, created_at: 2025-09-15 13:35:58, updated_at: 2025-09-15 13:35:58'),
(0, 6, '2025-09-17 19:41:14', 'realerp_erplive', 'JV', NULL, 'Admin', 'id: 6, v_no: 1, v_type: JV, date: 2025-09-15, description: NONE, debit: 0, credit: 12, status: unofficial, preparedby: Admin, account_id: 1, created_at: 2025-09-15 13:35:58, updated_at: 2025-09-15 13:35:58'),
(0, 7, '2025-09-17 19:41:23', 'realerp_erplive', 'OB', NULL, 'Admin', 'id: 7, v_no: 1, v_type: OB, date: 2025-09-15, description: NONE, debit: 13, credit: 0, status: unofficial, preparedby: Admin, account_id: 1, created_at: 2025-09-15 13:36:29, updated_at: 2025-09-15 13:36:29'),
(0, 8, '2025-09-17 19:41:23', 'realerp_erplive', 'OB', NULL, 'Admin', 'id: 8, v_no: 1, v_type: OB, date: 2025-09-15, description: NONE, debit: 0, credit: 13, status: unofficial, preparedby: Admin, account_id: 1, created_at: 2025-09-15 13:36:29, updated_at: 2025-09-15 13:36:29');

-- --------------------------------------------------------

--
-- Table structure for table `trn_dtl`
--

CREATE TABLE `trn_dtl` (
  `id` bigint UNSIGNED NOT NULL,
  `v_type` varchar(255)   NOT NULL,
  `v_no` varchar(255)   NOT NULL,
  `date` varchar(255)   NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4   DEFAULT NULL,
  `debit` varchar(255)   NOT NULL,
  `credit` varchar(255)   NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4   DEFAULT NULL,
  `preparedby` varchar(255)   NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `cash_id` bigint UNSIGNED DEFAULT NULL,
  `r_id` varchar(255)   DEFAULT NULL,
  `pre_bal` varchar(255)   DEFAULT NULL,
  `file_id` varchar(255)   DEFAULT NULL,
  `cid` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trn_dtl`
--

INSERT INTO `trn_dtl` (`id`, `v_type`, `v_no`, `date`, `description`, `debit`, `credit`, `status`, `preparedby`, `account_id`, `cash_id`, `r_id`, `pre_bal`, `file_id`, `cid`, `created_at`, `updated_at`) VALUES
(47, 'PRN', '1', '2025-09-22 19:26:55', NULL, '0', '45', NULL, 'Admin', 13, 1, '1', NULL, NULL, 1, '2025-09-22 19:26:55', '2025-09-22 19:27:18'),
(51, 'BPV', '1', '2025-09-10', 'adad', '75000', '0', 'N', 'Admin', 9, 2, NULL, NULL, NULL, 1, '2025-09-22 23:21:00', '2025-09-22 23:24:02'),
(59, 'CPV', '1', '2025-09-22', NULL, '1212.00', '0', 'N', 'Admin', 1, 3, NULL, NULL, NULL, 1, '2025-09-23 02:20:57', '2025-09-24 15:38:35'),
(60, 'BPV', '2', '2025-09-22', NULL, '1212.00', '0', 'N', 'Admin', 1, 2, NULL, NULL, NULL, 1, '2025-09-23 02:21:10', '2025-09-24 16:03:56'),
(61, 'JV', '2', '2025-09-22', '', '12000', '0', 'N', 'Admin', 1, NULL, NULL, NULL, NULL, 1, '2025-09-23 02:21:40', '2025-09-23 02:21:40'),
(62, 'JV', '2', '2025-09-22', '', '0', '10000', 'N', 'Admin', 6, NULL, NULL, NULL, NULL, 1, '2025-09-23 02:21:40', '2025-09-23 02:21:40'),
(63, 'JV', '2', '2025-09-22', '', '0', '2000', 'N', 'Admin', 6, NULL, NULL, NULL, NULL, 1, '2025-09-23 02:21:40', '2025-09-23 02:21:40'),
(66, 'JV', '3', '2025-09-22', '', '12', '0', 'N', 'Admin', 2, NULL, NULL, NULL, NULL, 1, '2025-09-23 02:23:19', '2025-09-23 02:23:19'),
(67, 'JV', '3', '2025-09-22', '', '0', '12', 'N', 'Admin', 6, NULL, NULL, NULL, NULL, 1, '2025-09-23 02:23:19', '2025-09-23 02:23:19'),
(68, 'JV', '4', '2025-09-22', '', '12', '0', 'N', 'Admin', 6, NULL, NULL, NULL, NULL, 1, '2025-09-23 02:24:01', '2025-09-23 02:24:01'),
(69, 'JV', '4', '2025-09-22', '', '0', '12', 'N', 'Admin', 9, NULL, NULL, NULL, NULL, 1, '2025-09-23 02:24:01', '2025-09-23 02:24:01'),
(70, 'CRV', '2', '2025-09-22', NULL, '0', '121212.00', 'N', 'Admin', 1, 3, NULL, NULL, NULL, 1, '2025-09-23 02:25:06', '2025-09-23 02:25:06'),
(80, 'JV', '1', '2025-09-01', 'kjhk', '20000', '0', 'N', 'Admin', 2, NULL, NULL, NULL, NULL, 1, '2025-09-24 16:06:34', '2025-09-24 16:06:34'),
(81, 'JV', '1', '2025-09-01', 'kjhk', '0', '20000', 'N', 'Admin', 2, NULL, NULL, NULL, NULL, 1, '2025-09-24 16:06:34', '2025-09-24 16:06:34'),
(82, 'CPV', '2', '2025-09-28', NULL, '5000.00', '0', 'N', 'Admin', 2, 3, NULL, NULL, NULL, 1, '2025-09-29 01:10:50', '2025-09-29 01:10:50'),
(84, 'BRV', '1', '2025-09-28', NULL, '0', '5000', 'N', 'Admin', 6, 2, NULL, NULL, NULL, 1, '2025-09-29 01:14:32', '2025-09-29 01:14:32'),
(91, 'CRV', '3', '2025-10-01', 'avc', '0', '25000.00', 'N', 'Admin', 9, 3, NULL, NULL, NULL, 1, '2025-10-01 14:33:05', '2025-10-01 14:33:05'),
(92, 'BRV', '2', '2025-10-01', 'testing', '0', '50000.00', 'N', 'Admin', 15, 2, NULL, NULL, NULL, 1, '2025-10-01 14:35:42', '2025-10-01 14:35:42'),
(93, 'JV', '2', '2025-10-01', 'testing', '0', '25000', 'N', 'Admin', 3, NULL, NULL, NULL, NULL, 1, '2025-10-01 14:40:50', '2025-10-01 14:40:50'),
(94, 'JV', '2', '2025-10-01', 'asdasd', '20000', '0', 'N', 'Admin', 2, NULL, NULL, NULL, NULL, 1, '2025-10-01 14:40:50', '2025-10-01 14:40:50'),
(95, 'JV', '2', '2025-10-01', 'asds', '5000', '0', 'N', 'Admin', 6, NULL, NULL, NULL, NULL, 1, '2025-10-01 14:40:50', '2025-10-01 14:40:50'),
(96, 'PIN', '1', '2025-11-01', NULL, '300', '0', NULL, 'Admin PremierTax', 18, 17, '1', NULL, NULL, 7, '2025-10-17 15:52:40', '2025-11-01 20:22:37'),
(98, 'CRV', '1', '2025-07-01', 'Sale Tax fees \r\n7-24/6-25\r\nRecord Fees (25)', '0', '64000.00', 'N', 'Admin PremierTax', 20, 19, NULL, NULL, NULL, 7, '2025-10-25 12:02:03', '2025-10-25 12:02:03'),
(99, 'CRV', '2', '2025-07-01', 'SALE TAX FESS\r\n4-25/6-25 \r\nREC FESS ( 25 )', '0', '21500.00', 'N', 'Admin PremierTax', 21, 19, NULL, NULL, NULL, 7, '2025-10-25 12:07:48', '2025-10-25 12:07:48'),
(100, 'CRV', '3', '2025-07-02', 'SALE TAX FESS\r\n4-25 / 6-25\r\nREC FESS ( 25 )', '0', '20000.00', 'N', 'Admin PremierTax', 22, 19, NULL, NULL, NULL, 7, '2025-10-25 12:13:52', '2025-10-25 12:13:52'),
(101, 'CPV', '1', '2025-07-01', 'FOR THE MONTH OF JULY 2025 \r\nVC NO 602', '200000.00', '0', 'N', 'Admin PremierTax', 23, 19, NULL, NULL, NULL, 7, '2025-10-25 12:31:06', '2025-10-25 12:31:06'),
(102, 'CPV', '2', '2025-07-01', 'AC SERVICE 3 X 1800 VC NO 602', '5400.00', '0', 'N', 'Admin PremierTax', 24, 19, NULL, NULL, NULL, 7, '2025-10-25 12:34:32', '2025-10-25 12:35:33'),
(103, 'CPV', '3', '2025-07-01', 'BRIGHT CORPORATION PARTNERSHIP DEED\r\nVC NO 602', '3500.00', '0', 'N', 'Admin PremierTax', 24, 19, NULL, NULL, NULL, 7, '2025-10-25 12:38:22', '2025-10-25 12:38:22'),
(104, 'CRV', '4', '2025-07-03', 'SALE TAX FESS 7-20 / 6-21 REC FESS ( 21 ),7-21 / 6-22 REC FESS ( 22 ),7-22 / 6-23 REC FESS ( 23 ),7-23 / 6-24 REC FESS ( 24 ),7-24 / 6-25 REC FESS ( 25 ), VC NO 884', '0', '100000.00', 'N', 'Admin PremierTax', 25, 19, NULL, NULL, NULL, 7, '2025-10-25 13:35:31', '2025-10-25 15:27:12'),
(105, 'CRV', '5', '2025-07-03', 'PARTNERSHIP DEED 2025 VC NO 884', '0', '30000.00', 'N', 'Admin PremierTax', 26, 19, NULL, NULL, NULL, 7, '2025-10-25 13:40:03', '2025-10-25 15:32:33'),
(106, 'CRV', '6', '2025-07-03', 'SALE TAX FESS 5-25 RECEIVED AMOUNT FROM MEEZANTID : 276050VC NO 122', '0', '5000.00', 'N', 'Admin PremierTax', 28, 19, NULL, NULL, NULL, 7, '2025-10-25 13:59:11', '2025-10-25 15:33:51'),
(107, 'CRV', '7', '2025-07-04', 'SALE TAX FESS 5-25 VC NO 886', '0', '28000.00', 'N', 'Admin PremierTax', 29, 19, NULL, NULL, NULL, 7, '2025-10-25 14:03:02', '2025-10-25 15:34:52'),
(108, 'CRV', '8', '2025-07-04', 'PARTNERSHIP DEED \r\nVC NO 886', '0', '35000.00', 'unofficial', 'Admin PremierTax', 30, 19, NULL, NULL, NULL, 7, '2025-10-25 14:05:05', '2025-11-04 11:34:13'),
(109, 'CPV', '4', '2025-07-01', 'CHAMBER RENEWAL\r\nVC NO 602', '3500.00', '0', 'N', 'Admin PremierTax', 32, 19, NULL, NULL, NULL, 7, '2025-10-25 14:55:16', '2025-10-25 14:55:16'),
(110, 'CPV', '5', '2025-07-01', '1-4 / 7 / 2025\r\nVC NO 704', '7500.00', '0', 'N', 'Admin PremierTax', 31, 19, NULL, NULL, NULL, 7, '2025-10-25 15:03:20', '2025-10-25 15:03:20'),
(111, 'CPV', '6', '2025-07-04', 'KORI LOADER AC SERVICE VC NO 705', '1400.00', '0', 'N', 'Admin PremierTax', 24, 19, NULL, NULL, NULL, 7, '2025-10-25 15:06:30', '2025-10-25 15:37:32'),
(112, 'CPV', '7', '2025-07-04', 'BIKE OIL CHANGE\r\nVC NO 705', '640.00', '0', 'N', 'Admin PremierTax', 24, 19, NULL, NULL, NULL, 7, '2025-10-25 15:10:29', '2025-10-25 15:10:29'),
(113, 'CPV', '8', '2025-07-09', 'HAFIZ USMAN 6/25 VC NO 707', '50000.00', '0', 'N', 'Admin PremierTax', 34, 19, NULL, NULL, NULL, 7, '2025-10-25 15:42:05', '2025-10-25 15:43:19'),
(114, 'CPV', '9', '2025-07-09', 'ZEESHAN IKHLAQ 6/25 \r\nVC NO 708', '60000.00', '0', 'N', 'Admin PremierTax', 34, 19, NULL, NULL, NULL, 7, '2025-10-25 15:44:37', '2025-10-25 15:44:37'),
(115, 'CPV', '10', '2025-07-09', 'IQRA ISLAM 6/25 \r\nVC NO 709', '30000.00', '0', 'N', 'Admin PremierTax', 34, 19, NULL, NULL, NULL, 7, '2025-10-25 15:45:27', '2025-10-25 15:45:27'),
(116, 'CPV', '11', '2025-07-09', 'MUNEEB AHMAD 6/25\r\nVC NO 710', '35000.00', '0', 'N', 'Admin PremierTax', 34, 19, NULL, NULL, NULL, 7, '2025-10-25 15:48:29', '2025-10-25 15:48:29'),
(117, 'CPV', '12', '2025-07-09', 'AHMAD GULZAR 6/25\r\nVC NO 711', '18000.00', '0', 'N', 'Admin PremierTax', 34, 19, NULL, NULL, NULL, 7, '2025-10-25 15:50:19', '2025-10-25 15:50:19'),
(118, 'CPV', '13', '2025-07-09', 'KHURRAM SHAHZAD 6/25\r\nVC NO 712', '20000.00', '0', 'N', 'Admin PremierTax', 34, 19, NULL, NULL, NULL, 7, '2025-10-25 15:51:39', '2025-10-25 15:51:39'),
(119, 'CPV', '14', '2025-07-09', 'MUHAMMAD ALI SIDDIQ 6/25\r\nVC NO 713', '15000.00', '0', 'N', 'Admin PremierTax', 34, 19, NULL, NULL, NULL, 7, '2025-10-25 15:53:54', '2025-10-25 15:53:54'),
(120, 'CPV', '15', '2025-07-09', 'IQBAL SWEEPER 6/25\r\nVC NO 714', '5000.00', '0', 'N', 'Admin PremierTax', 34, 19, NULL, NULL, NULL, 7, '2025-10-25 15:56:42', '2025-10-25 15:56:42'),
(121, 'CPV', '16', '2025-07-09', 'BILAL AMJAD 6/25\r\nVC NO 715', '23000.00', '0', 'N', 'Admin PremierTax', 34, 19, NULL, NULL, NULL, 7, '2025-10-25 15:58:50', '2025-10-25 15:58:50'),
(122, 'CPV', '17', '2025-07-09', 'DONATION 6/25\r\nVC NO 716', '8500.00', '0', 'N', 'Admin PremierTax', 34, 19, NULL, NULL, NULL, 7, '2025-10-25 16:00:13', '2025-10-25 16:00:13'),
(125, 'OB', '1', '2025-09-01', 'afs', '100000', '0', 'N', 'Admin', 2, NULL, NULL, NULL, NULL, 1, '2025-11-02 01:22:13', '2025-11-02 01:22:13'),
(126, 'OB', '1', '2025-09-01', 'afs', '0', '100000', 'N', 'Admin', 2, NULL, NULL, NULL, NULL, 1, '2025-11-02 01:22:13', '2025-11-02 01:22:13'),
(127, 'OB', '1', '2025-09-01', 'afs', '5000', '0', 'N', 'Admin', 2, NULL, NULL, NULL, NULL, 1, '2025-11-02 01:22:13', '2025-11-02 01:22:13'),
(128, 'OB', '1', '2025-09-01', 'afs', '0', '5000', 'N', 'Admin', 2, NULL, NULL, NULL, NULL, 1, '2025-11-02 01:22:13', '2025-11-02 01:22:13'),
(129, 'OB', '1', '2025-09-01', 'asdf', '100', '0', 'N', 'Admin', 9, NULL, NULL, NULL, NULL, 1, '2025-11-02 01:22:13', '2025-11-02 01:22:13'),
(130, 'OB', '1', '2025-09-01', 'asdf', '0', '100', 'N', 'Admin', 9, NULL, NULL, NULL, NULL, 1, '2025-11-02 01:22:13', '2025-11-02 01:22:13'),
(137, 'SIN', '1', '2025-12-09', NULL, '0', '106', NULL, 'MCC State', 38, NULL, '1', NULL, NULL, 11, '2025-12-09 17:29:53', '2025-12-09 17:29:53'),
(140, 'SIN', '2', '0026-01-03', NULL, '0', '0', NULL, 'MCC State', 38, NULL, '2', NULL, NULL, 11, '2026-01-06 15:34:53', '2026-01-06 15:34:53'),
(143, 'PIN', '1', '2026-01-01', NULL, '413000', '0', NULL, 'Admin', 11, 1, '1', NULL, NULL, 1, '2026-02-13 12:31:14', '2026-02-13 12:31:14'),
(144, 'PIN', '2', '2026-01-01', NULL, '115640', '0', NULL, 'Admin', 5, 1, '2', NULL, NULL, 1, '2026-02-13 12:31:36', '2026-02-13 12:31:36'),
(145, 'PIN', '3', '2026-01-01', NULL, '838980', '0', NULL, 'Admin', 35, 1, '3', NULL, NULL, 1, '2026-02-13 12:32:18', '2026-02-13 12:32:18'),
(146, 'PIN', '4', '2026-01-01', NULL, '18998', '0', NULL, 'Admin', 35, 1, '4', NULL, NULL, 1, '2026-02-13 12:33:04', '2026-02-13 12:33:04'),
(147, 'PIN', '5', '2026-01-01', NULL, '52392', '0', NULL, 'Admin', 13, 1, '5', NULL, NULL, 1, '2026-02-13 12:33:34', '2026-02-13 12:33:34'),
(148, 'PIN', '6', '2026-01-01', NULL, '952661.2', '0', NULL, 'Admin', 5, 1, '6', NULL, NULL, 1, '2026-02-13 12:34:01', '2026-02-13 12:34:01'),
(149, 'PIN', '7', '2026-01-01', NULL, '4248', '0', NULL, 'Admin', 11, 1, '7', NULL, NULL, 1, '2026-02-13 12:34:33', '2026-02-13 12:34:33'),
(150, 'PIN', '8', '2026-01-01', NULL, '2389.5', '0', NULL, 'Admin', 35, 1, '8', NULL, NULL, 1, '2026-02-13 12:35:05', '2026-02-13 12:35:05'),
(151, 'PIN', '9', '2026-01-01', NULL, '13027.2', '0', NULL, 'Admin', 13, 1, '9', NULL, NULL, 1, '2026-02-13 12:35:59', '2026-02-13 12:35:59'),
(152, 'PIN', '10', '2026-01-01', NULL, '11800', '0', NULL, 'Admin', 5, 1, '10', NULL, NULL, 1, '2026-02-13 12:37:52', '2026-02-13 12:37:52'),
(153, 'PIN', '10', '2026-01-01', NULL, '17346', '0', NULL, 'Admin', 13, 1, '11', NULL, NULL, 1, '2026-02-13 12:38:17', '2026-02-13 12:38:17'),
(154, 'PIN', '10', '2026-01-01', NULL, '20296', '0', NULL, 'Admin', 35, 1, '12', NULL, NULL, 1, '2026-02-13 12:38:42', '2026-02-13 12:38:42'),
(155, 'PIN', '10', '2026-01-01', NULL, '9204', '0', NULL, 'Admin', 5, 1, '13', NULL, NULL, 1, '2026-02-13 12:39:05', '2026-02-13 12:39:05'),
(156, 'PIN', '10', '2026-01-01', NULL, '3009', '0', NULL, 'Admin', 35, 1, '14', NULL, NULL, 1, '2026-02-13 12:39:29', '2026-02-13 12:39:29'),
(157, 'PIN', '10', '2026-01-01', NULL, '5900', '0', NULL, 'Admin', 5, 1, '15', NULL, NULL, 1, '2026-02-13 12:39:57', '2026-02-13 12:39:57'),
(158, 'PIN', '10', '2026-01-01', NULL, '28320', '0', NULL, 'Admin', 11, 1, '16', NULL, NULL, 1, '2026-02-13 12:40:20', '2026-02-13 12:40:20'),
(159, 'PIN', '10', '2026-01-01', NULL, '283200', '0', NULL, 'Admin', 35, 1, '17', NULL, NULL, 1, '2026-02-13 12:40:42', '2026-02-13 12:40:42'),
(160, 'PIN', '10', '2026-01-01', NULL, '273571.2', '0', NULL, 'Admin', 11, 1, '18', NULL, NULL, 1, '2026-02-13 12:41:25', '2026-02-13 12:41:25'),
(161, 'PIN', '10', '2026-01-01', NULL, '28320', '0', NULL, 'Admin', 35, 1, '19', NULL, NULL, 1, '2026-02-13 12:41:58', '2026-02-13 12:41:58'),
(163, 'PIN', '10', '2026-02-01', NULL, '1275875', '0', NULL, 'Admin', 11, 1, '20', NULL, NULL, 1, '2026-02-13 13:17:08', '2026-02-13 13:17:08'),
(165, 'PIN', '10', '2026-02-14', NULL, '12.98', '0', NULL, 'Admin', 11, 1, '21', NULL, NULL, 1, '2026-02-14 07:30:48', '2026-02-14 07:30:48'),
(167, 'SIN', '3', '2026-02-14', NULL, '0', '530', NULL, 'MCC State', 70, NULL, '3', NULL, NULL, 11, '2026-02-14 09:45:48', '2026-02-14 09:45:48'),
(168, 'PIN', '1', '2026-02-16', NULL, '10000', '0', NULL, 'Welcome Industry Admin', 71, 46, '1', NULL, NULL, 13, '2026-02-16 08:29:07', '2026-02-16 08:29:07'),
(169, 'PRN', '1', '2026-02-16', NULL, '0', '1000', NULL, 'Welcome Industry Admin', 71, 46, '1', NULL, NULL, 13, '2026-02-16 08:30:59', '2026-02-16 08:30:59'),
(170, 'SIN', '1', '2026-02-16', NULL, '0', '10920', NULL, 'Welcome Industry Admin', 72, NULL, '1', NULL, NULL, 13, '2026-02-16 08:34:26', '2026-02-16 08:34:26'),
(171, 'SIN', '1', '2026-02-16', NULL, '0', '204.14', NULL, 'Admin', 7, 2, '1', NULL, NULL, 1, '2026-02-16 10:23:39', '2026-02-16 10:23:39'),
(172, 'SIN', '2', '2026-02-16', NULL, '0', '4130', NULL, 'Admin', 7, 2, '2', NULL, NULL, 1, '2026-02-16 10:28:12', '2026-02-16 10:28:12');

-- --------------------------------------------------------

--
-- Stand-in structure for view `trn_dtl_views`
-- (See below for the actual view)
--
CREATE TABLE `trn_dtl_views` (
`aid` bigint unsigned
,`cid` bigint unsigned
,`credit` varchar(255)
,`debit` varchar(255)
,`descr` varchar(255)
,`entry_type` varchar(7)
,`id` bigint unsigned
,`v_date` varchar(255)
,`v_no` varchar(255)
,`v_type` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `t_r_n_d_t_l_s`
--

CREATE TABLE `t_r_n_d_t_l_s` (
  `id` int NOT NULL,
  `v_no` int DEFAULT NULL,
  `v_type` varchar(255)   DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` varchar(255)   DEFAULT NULL,
  `debit` varchar(255)   DEFAULT NULL,
  `credit` varchar(255)   DEFAULT NULL,
  `status` varchar(255)   DEFAULT NULL,
  `preparedby` varchar(255)   DEFAULT NULL,
  `cash_id` bigint UNSIGNED DEFAULT NULL,
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `r_id` bigint UNSIGNED DEFAULT NULL,
  `pre_bal` double DEFAULT NULL,
  `file_id` varchar(255)   DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `t_r_n_d_t_l_s`
--
DELIMITER $$
CREATE TRIGGER `before_trndtls_delete` BEFORE DELETE ON `t_r_n_d_t_l_s` FOR EACH ROW BEGIN
  INSERT INTO trndtls_deletions_log (
    deleted_id,
    deleted_at,
    deleted_by,
    v_type,
    r_id,
    prepared_by,
    original_data
  )
  VALUES (
    OLD.id,
    NOW(),
    SUBSTRING_INDEX(USER(), '@', 1),
    OLD.v_type,
    OLD.r_id,
    OLD.preparedby,
    CONCAT_WS(', ',
      CONCAT('id: ', OLD.id),
      CONCAT('v_no: ', OLD.v_no),
      CONCAT('v_type: ', OLD.v_type),
      CONCAT('date: ', OLD.date),
      CONCAT('description: ', OLD.description),
      CONCAT('debit: ', OLD.debit),
      CONCAT('credit: ', OLD.credit),
      CONCAT('status: ', OLD.status),
      CONCAT('preparedby: ', OLD.preparedby),
      CONCAT('cash_id: ', OLD.cash_id),
      CONCAT('account_id: ', OLD.account_id),
      CONCAT('r_id: ', OLD.r_id),
      CONCAT('pre_bal: ', OLD.pre_bal),
      CONCAT('file_id: ', OLD.file_id),
      CONCAT('created_at: ', OLD.created_at),
      CONCAT('updated_at: ', OLD.updated_at)
    )
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int NOT NULL,
  `name` varchar(255)   NOT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `cid`, `created_at`, `updated_at`) VALUES
(1, 'pcs', 1, '2025-09-18 01:21:11', '2025-09-18 01:21:11'),
(4, 'KG', 1, '2025-10-15 11:54:53', '2025-10-15 11:54:53'),
(5, 'pcs', 7, '2025-10-16 00:10:54', '2025-10-16 00:10:54'),
(6, 'kg', 7, '2025-10-18 11:10:52', '2025-10-18 11:10:52'),
(7, 'liter', 7, '2025-10-18 11:11:00', '2025-10-18 11:11:00'),
(8, 'kgs', 11, '2025-12-03 17:53:55', '2025-12-03 17:53:55'),
(10, 'QTY', 11, '2025-12-08 13:18:44', '2025-12-08 13:18:44'),
(11, 'Set', 1, '2026-02-13 11:52:57', '2026-02-13 11:52:57'),
(12, 'Bags', 1, '2026-02-13 13:12:01', '2026-02-13 13:12:01'),
(13, 'Rs.', 1, '2026-02-13 13:42:18', '2026-02-13 13:42:18'),
(14, 'Pcs', 13, '2026-02-16 08:09:03', '2026-02-16 08:09:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255)   NOT NULL,
  `email` varchar(255)   NOT NULL,
  `account` tinyint(1) DEFAULT NULL,
  `report` tinyint(1) DEFAULT NULL,
  `billing` tinyint(1) DEFAULT NULL,
  `delivery_challan` tinyint(1) DEFAULT NULL,
  `waste_sale` tinyint(1) DEFAULT NULL,
  `gate_pass` tinyint(1) DEFAULT NULL,
  `gate_ex` tinyint DEFAULT NULL,
  `purchase` tinyint(1) DEFAULT NULL,
  `inventory` tinyint(1) DEFAULT NULL,
  `product_registration` tinyint(1) DEFAULT NULL,
  `job_sheet` tinyint(1) DEFAULT NULL,
  `attendance_system` tinyint(1) DEFAULT NULL,
  `setup` tinyint(1) DEFAULT NULL,
  `setup_department` tinyint(1) DEFAULT NULL,
  `employee_department` tinyint(1) DEFAULT NULL,
  `password` varchar(255)   NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100)   DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee` tinyint(1) DEFAULT NULL,
  `wage_calculator` tinyint(1) DEFAULT NULL,
  `cid` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `is_admin`, `name`, `email`, `account`, `report`, `billing`, `delivery_challan`, `waste_sale`, `gate_pass`, `gate_ex`, `purchase`, `inventory`, `product_registration`, `job_sheet`, `attendance_system`, `setup`, `setup_department`, `employee_department`, `password`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`, `employee`, `wage_calculator`, `cid`) VALUES
(1, 1, 'Admin', 'superadmin@example.com', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '$2y$12$J4hNtyuXj3ULv/5u3Kt5OekqdCaGwy3g5DVDs8oeia/zoBmsrJTLq', NULL, 'UNJy6xhT1DeObRfAFECLmdAVXDEmoK0M6bUXHGmzfwOc7yuh8o0I5P0W0Xor', '2024-11-11 02:00:37', '2025-07-29 01:07:02', NULL, 0, 1),
(99, 1, 'Demo account', 'demo@gmail.com', 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 1, 1, 0, 0, 1, '$2y$12$FVF5Odqw0BiIPEcm1awHo.2h.qgQib60BUHM0ytmI59FP1kD/Ouzq', NULL, 'Nt2zwqK23VxGdKXuGvBdDMu6iM1lysVdpmtusAey2nxyB2vjZJ3yIkoCZSAx', '2025-04-16 01:07:51', '2025-08-02 20:10:32', 0, 0, 2),
(127, 3, 'Super Admin', 'admin@gmail.com', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '$2y$12$PG6LTVuCozPajc7Bs1h0IOin.NW9sa9exEKtwnSqpFjQrUlHVtQ8G', NULL, 'kIPaTPeYsNehpnJTIcVeEEz0FfyiuONe6xR2iakdWp3UJSM7wtActcbWRcKf', '2025-07-29 01:07:55', '2025-08-02 12:43:06', 0, 0, 6),
(128, 0, 'ADMIN', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 6),
(133, 1, 'Admin PremierTax', 'premiertax@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$KkBYf10TJ9Mto6zq/efyQuYuFSldxXC3MgCoipol5zZiEOxJtNjhu', NULL, 'GPnr0dIaLmhcB8JQX3zmBazt7aTZD92uK3c3n7v8zOkW2iSVWq4ZM4bow1fR', '2025-09-20 14:31:38', '2025-09-20 14:31:38', NULL, NULL, 7),
(135, 1, 'AHMAD METAL WORKS', 'ahmedmetalworks4748@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$kLKsOEIVe.QWIkxVV5nJReWvkX0UBF1JPvRp4dn1ztsixdCc2Yv46', NULL, NULL, '2025-10-25 11:17:51', '2025-10-25 11:17:51', NULL, NULL, 9),
(136, 1, 'DEMO Admin', 'demoadmin@softix.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$z2zBDFndFi6r03hFAh0SKeoEZJjtJClF8Sm3U.ersbYDHJCeSXoia', NULL, 'cmjLtfhCiz5miJbD5gnxU9oeEqKuWVaHdqumGHScZTn7WGKNOquSjffoVcva', '2025-11-01 14:56:03', '2025-11-01 14:56:03', NULL, NULL, 10),
(137, 1, 'MCC State', 'Forasiacardclothing@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$.xL6.hmn0a8w9Di0I2jHpuSUaH6C9ezPfXzR3C/pcTebkgqaIncwu', NULL, NULL, '2025-12-03 17:44:02', '2025-12-03 17:44:02', NULL, NULL, 11),
(138, 1, 'Welcome User', 'welcome.admin@gstmanufacturing.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$Uz8DEp8v5EvuMHmLz/wFv.ltgJ9oPsflWr3.Xu847h16urd27HlNi', NULL, NULL, '2026-02-07 09:28:19', '2026-02-07 09:28:19', NULL, NULL, 12),
(139, 1, 'Welcome Industry Admin', 'welcome@industry.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$nfoO1E0A0xYjSzCA8Mv1vuGMLLSCNlE/SfK5WBsbqO6V96I34oAPC', NULL, 'xW9aeFIMIiJhqDRIH1LKIUaSjKUzlUrATy5XzyZzpOskPImBxqIWFMFopd4p', '2026-02-07 10:36:53', '2026-02-07 10:36:53', NULL, NULL, 13),
(140, 1, 'Faisal Stationary Admin', 'faisal@admin.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$VUcEILcUOEnqGz3DQFtFfuqqjEX2JYWQk/5I5o5/4FMGwTykz.7lS', NULL, '8yLWhx9R9hb2ov1C2DB3QYgF2VZplDAvVH6m4d1vxdP3DAfSrIKsrli2693K', '2026-02-14 08:56:22', '2026-02-14 08:56:22', NULL, NULL, 14);

-- --------------------------------------------------------

--
-- Table structure for table `workspace`
--

CREATE TABLE `workspace` (
  `cid` int NOT NULL,
  `name` varchar(255)   NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workspace`
--

INSERT INTO `workspace` (`cid`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Softix', NULL, NULL),
(6, 'ADMIN', NULL, NULL),
(7, 'Premiertax', '2025-09-20 14:29:16', '2025-09-20 14:29:16'),
(9, 'AHMAD METAL WORKS', '2025-10-25 11:16:15', '2025-10-25 11:16:15'),
(10, 'DEMO', '2025-11-01 14:55:14', '2025-11-01 14:55:14'),
(11, 'MCC State', '2025-12-03 17:42:05', '2025-12-03 17:42:05'),
(12, 'Welcome', '2026-02-07 09:27:19', '2026-02-07 09:27:19'),
(13, 'Welcome Industry', '2026-02-07 10:34:05', '2026-02-07 10:34:05'),
(14, 'Faisal Stationary', '2026-02-14 08:55:22', '2026-02-14 08:55:22');

-- --------------------------------------------------------

--
-- Structure for view `specific_item`
--
DROP TABLE IF EXISTS `specific_item`;

CREATE ALGORITHM=UNDEFINED DEFINER=`realerp`@`localhost` SQL SECURITY DEFINER VIEW `specific_item`  AS SELECT `os`.`item_id` AS `item_id`, str_to_date(`os`.`v_date`,'%Y-%m-%d') AS `v_date`, sum(`os`.`qty`) AS `opn_qty`, sum((`os`.`qty` * `os`.`rate`)) AS `opn_amt`, 0 AS `pur_qty`, 0 AS `pur_amt`, 0 AS `pret_qty`, 0 AS `pret_amt`, 0 AS `sal_qty`, 0 AS `sal_amt`, 0 AS `sret_qty`, 0 AS `sret_amt`, 0 AS `con_qty`, 0 AS `con_amt`, 0 AS `adj_qty`, 0 AS `adj_amt`, 0 AS `raw_qty`, 0 AS `raw_amt`, `os`.`cid` AS `cid` FROM `opening_stock` AS `os` GROUP BY `os`.`item_id`, `os`.`v_date`, `os`.`cid`union all select `pi`.`item_id` AS `item_id`,str_to_date(`pm`.`v_date`,'%Y-%m-%d') AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,sum(`pi`.`qty`) AS `pur_qty`,sum((`pi`.`qty` * `pi`.`rate`)) AS `pur_amt`,0 AS `pret_qty`,0 AS `pret_amt`,0 AS `sal_qty`,0 AS `sal_amt`,0 AS `sret_qty`,0 AS `sret_amt`,0 AS `con_qty`,0 AS `con_amt`,0 AS `adj_qty`,0 AS `adj_amt`,0 AS `raw_qty`,0 AS `raw_amt`,`pm`.`cid` AS `cid` from (`purchase_invoices` `pi` join `purchase_master` `pm` on((`pi`.`v_no` = `pm`.`v_no`))) group by `pi`.`item_id`,`pm`.`v_date`,`pm`.`cid` union all select `pri`.`item_id` AS `item_id`,str_to_date(`prm`.`v_date`,'%Y-%m-%d') AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,0 AS `pur_qty`,0 AS `pur_amt`,sum(`pri`.`qty`) AS `pret_qty`,sum((`pri`.`qty` * `pri`.`rate`)) AS `pret_amt`,0 AS `sal_qty`,0 AS `sal_amt`,0 AS `sret_qty`,0 AS `sret_amt`,0 AS `con_qty`,0 AS `con_amt`,0 AS `adj_qty`,0 AS `adj_amt`,0 AS `raw_qty`,0 AS `raw_amt`,`prm`.`cid` AS `cid` from (`purchase_return_invoice` `pri` join `purchase_return_master` `prm` on((`pri`.`v_no` = `prm`.`v_no`))) group by `pri`.`item_id`,`prm`.`v_date`,`prm`.`cid` union all select `si`.`item_id` AS `item_id`,str_to_date(`sm`.`v_date`,'%Y-%m-%d') AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,0 AS `pur_qty`,0 AS `pur_amt`,0 AS `pret_qty`,0 AS `pret_amt`,sum(`si`.`qty`) AS `sal_qty`,sum((`si`.`qty` * `si`.`rate`)) AS `sal_amt`,0 AS `sret_qty`,0 AS `sret_amt`,0 AS `con_qty`,0 AS `con_amt`,0 AS `adj_qty`,0 AS `adj_amt`,0 AS `raw_qty`,0 AS `raw_amt`,`sm`.`cid` AS `cid` from (`sale_invoices` `si` join `sale_master` `sm` on((`si`.`v_no` = `sm`.`v_no`))) group by `si`.`item_id`,`sm`.`v_date`,`sm`.`cid` union all select `sri`.`item_id` AS `item_id`,str_to_date(`srm`.`v_date`,'%Y-%m-%d') AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,0 AS `pur_qty`,0 AS `pur_amt`,0 AS `pret_qty`,0 AS `pret_amt`,0 AS `sal_qty`,0 AS `sal_amt`,sum(`sri`.`qty`) AS `sret_qty`,sum((`sri`.`qty` * `sri`.`rate`)) AS `sret_amt`,0 AS `con_qty`,0 AS `con_amt`,0 AS `adj_qty`,0 AS `adj_amt`,0 AS `raw_qty`,0 AS `raw_amt`,`srm`.`cid` AS `cid` from (`sale_return_invoices` `sri` join `sale_return_masters` `srm` on((`sri`.`v_no` = `srm`.`v_no`))) group by `sri`.`item_id`,`srm`.`v_date`,`srm`.`cid` union all select `pd`.`item_id` AS `item_id`,str_to_date(`pm`.`v_date`,'%Y-%m-%d') AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,0 AS `pur_qty`,0 AS `pur_amt`,0 AS `pret_qty`,0 AS `pret_amt`,0 AS `sal_qty`,0 AS `sal_amt`,0 AS `sret_qty`,0 AS `sret_amt`,sum(`pd`.`qty`) AS `con_qty`,sum((`pd`.`qty` * `pd`.`rate`)) AS `con_amt`,0 AS `adj_qty`,0 AS `adj_amt`,0 AS `raw_qty`,0 AS `raw_amt`,`pm`.`cid` AS `cid` from (`production_detail` `pd` join `production_master` `pm` on((`pd`.`v_no` = `pm`.`v_no`))) group by `pd`.`item_id`,`pm`.`v_date`,`pm`.`cid` union all select `sad`.`item_id` AS `item_id`,str_to_date(`sam`.`v_date`,'%Y-%m-%d') AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,0 AS `pur_qty`,0 AS `pur_amt`,0 AS `pret_qty`,0 AS `pret_amt`,0 AS `sal_qty`,0 AS `sal_amt`,0 AS `sret_qty`,0 AS `sret_amt`,0 AS `con_qty`,0 AS `con_amt`,sum(`sad`.`qty`) AS `adj_qty`,sum((`sad`.`qty` * `sad`.`rate`)) AS `adj_amt`,0 AS `raw_qty`,0 AS `raw_amt`,`sam`.`cid` AS `cid` from (`stock_adj_details` `sad` join `stock_adj_masters` `sam` on((`sad`.`v_no` = `sam`.`v_no`))) group by `sad`.`item_id`,`sam`.`v_date`,`sam`.`cid` union all select `pc`.`rp_item_id` AS `item_id`,str_to_date(`pm`.`v_date`,'%Y-%m-%d') AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,0 AS `pur_qty`,0 AS `pur_amt`,0 AS `pret_qty`,0 AS `pret_amt`,0 AS `sal_qty`,0 AS `sal_amt`,0 AS `sret_qty`,0 AS `sret_amt`,0 AS `con_qty`,0 AS `con_amt`,0 AS `adj_qty`,0 AS `adj_amt`,sum(`pc`.`qty`) AS `raw_qty`,sum((`pc`.`qty` * `pc`.`rate`)) AS `raw_amt`,`pm`.`cid` AS `cid` from (`production_consumed` `pc` join `production_master` `pm` on((`pc`.`v_no` = `pm`.`v_no`))) group by `pc`.`rp_item_id`,`pm`.`v_date`,`pm`.`cid`  ;

-- --------------------------------------------------------

--
-- Structure for view `stock_qty_view`
--
DROP TABLE IF EXISTS `stock_qty_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`realerp`@`localhost` SQL SECURITY DEFINER VIEW `stock_qty_view`  AS SELECT (`os`.`item_id`  ) AS `item_id`, (`os`.`v_date`  ) AS `v_date`, sum(`os`.`qty`) AS `opn_qty`, sum((`os`.`qty` * `os`.`rate`)) AS `opn_amt`, 0 AS `pur_qty`, 0 AS `pur_amt`, 0 AS `pret_qty`, 0 AS `pret_amt`, 0 AS `sal_qty`, 0 AS `sal_amt`, 0 AS `sret_qty`, 0 AS `sret_amt`, 0 AS `con_qty`, 0 AS `con_amt`, `os`.`cid` AS `cid` FROM `opening_stock` AS `os` GROUP BY `os`.`item_id`, `os`.`v_date`, `os`.`cid`union all select (`pi`.`item_id`  ) AS `item_id`,(`pm`.`v_date`  ) AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,sum(`pi`.`qty`) AS `pur_qty`,sum((`pi`.`qty` * `pi`.`rate`)) AS `pur_amt`,0 AS `pret_qty`,0 AS `pret_amt`,0 AS `sal_qty`,0 AS `sal_amt`,0 AS `sret_qty`,0 AS `sret_amt`,0 AS `con_qty`,0 AS `con_amt`,`pm`.`cid` AS `cid` from (`purchase_invoices` `pi` join `purchase_master` `pm` on(((`pi`.`v_no`  ) = (`pm`.`v_no`  )))) group by `pi`.`item_id`,`pm`.`v_date`,`pm`.`cid` union all select (`pri`.`item_id`  ) AS `item_id`,(`prm`.`v_date`  ) AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,0 AS `pur_qty`,0 AS `pur_amt`,sum(`pri`.`qty`) AS `pret_qty`,sum((`pri`.`qty` * `pri`.`rate`)) AS `pret_amt`,0 AS `sal_qty`,0 AS `sal_amt`,0 AS `sret_qty`,0 AS `sret_amt`,0 AS `con_qty`,0 AS `con_amt`,`prm`.`cid` AS `cid` from (`purchase_return_invoice` `pri` join `purchase_return_master` `prm` on(((`pri`.`v_no`  ) = (`prm`.`v_no`  )))) group by `pri`.`item_id`,`prm`.`v_date`,`prm`.`cid` union all select (`si`.`item_id`  ) AS `item_id`,(`sm`.`v_date`  ) AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,0 AS `pur_qty`,0 AS `pur_amt`,0 AS `pret_qty`,0 AS `pret_amt`,sum(`si`.`qty`) AS `sal_qty`,sum((`si`.`qty` * `si`.`rate`)) AS `sal_amt`,0 AS `sret_qty`,0 AS `sret_amt`,0 AS `con_qty`,0 AS `con_amt`,`sm`.`cid` AS `cid` from (`sale_invoices` `si` join `sale_master` `sm` on(((`si`.`v_no`  ) = (`sm`.`v_no`  )))) group by `si`.`item_id`,`sm`.`v_date`,`sm`.`cid` union all select (`sri`.`item_id`  ) AS `item_id`,(`srm`.`v_date`  ) AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,0 AS `pur_qty`,0 AS `pur_amt`,0 AS `pret_qty`,0 AS `pret_amt`,0 AS `sal_qty`,0 AS `sal_amt`,sum(`sri`.`qty`) AS `sret_qty`,sum((`sri`.`qty` * `sri`.`rate`)) AS `sret_amt`,0 AS `con_qty`,0 AS `con_amt`,`srm`.`cid` AS `cid` from (`sale_return_invoices` `sri` join `sale_return_masters` `srm` on(((`sri`.`v_no`  ) = (`srm`.`v_no`  )))) group by `sri`.`item_id`,`srm`.`v_date`,`srm`.`cid` union all select (`pd`.`item_id`  ) AS `item_id`,(`pm`.`v_date`  ) AS `v_date`,0 AS `opn_qty`,0 AS `opn_amt`,0 AS `pur_qty`,0 AS `pur_amt`,0 AS `pret_qty`,0 AS `pret_amt`,0 AS `sal_qty`,0 AS `sal_amt`,0 AS `sret_qty`,0 AS `sret_amt`,sum(`pd`.`qty`) AS `con_qty`,sum((`pd`.`qty` * `pd`.`rate`)) AS `con_amt`,`pm`.`cid` AS `cid` from (`production_detail` `pd` join `production_master` `pm` on(((`pd`.`v_no`  ) = (`pm`.`v_no`  )))) group by `pd`.`item_id`,`pm`.`v_date`,`pm`.`cid`  ;

-- --------------------------------------------------------

--
-- Structure for view `trn_dtl_views`
--
DROP TABLE IF EXISTS `trn_dtl_views`;

CREATE ALGORITHM=UNDEFINED DEFINER=`realerp`@`localhost` SQL SECURITY DEFINER VIEW `trn_dtl_views`  AS SELECT `t`.`id` AS `id`, `t`.`v_no` AS `v_no`, `t`.`v_type` AS `v_type`, `t`.`date` AS `v_date`, `t`.`account_id` AS `aid`, `t`.`description` AS `descr`, `t`.`debit` AS `debit`, `t`.`credit` AS `credit`, `t`.`cid` AS `cid`, 'account' AS `entry_type` FROM `trn_dtl` AS `t`union all select `t`.`id` AS `id`,`t`.`v_no` AS `v_no`,`t`.`v_type` AS `v_type`,`t`.`date` AS `v_date`,`t`.`cash_id` AS `aid`,`t`.`description` AS `descr`,`t`.`credit` AS `debit`,`t`.`debit` AS `credit`,`t`.`cid` AS `cid`,'cash' AS `entry_type` from `trn_dtl` `t`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_masters`
--
ALTER TABLE `account_masters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `consumption_details`
--
ALTER TABLE `consumption_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `consumption_masters`
--
ALTER TABLE `consumption_masters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `erp_params`
--
ALTER TABLE `erp_params`
  ADD PRIMARY KEY (`id`),
  ADD KEY `erp_params_saledebitaccount_foreign` (`saleCreditAccount`);

--
-- Indexes for table `expense_detail`
--
ALTER TABLE `expense_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_masters`
--
ALTER TABLE `expense_masters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_masters`
--
ALTER TABLE `item_masters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_types`
--
ALTER TABLE `item_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `level1s`
--
ALTER TABLE `level1s`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `level2s`
--
ALTER TABLE `level2s`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opening_stock`
--
ALTER TABLE `opening_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `production_consumed`
--
ALTER TABLE `production_consumed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_detail`
--
ALTER TABLE `production_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_master`
--
ALTER TABLE `production_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_invoices`
--
ALTER TABLE `purchase_invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_master`
--
ALTER TABLE `purchase_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_return_invoice`
--
ALTER TABLE `purchase_return_invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_return_master`
--
ALTER TABLE `purchase_return_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_return_master_v_no_unique` (`v_no`);

--
-- Indexes for table `sale_invoices`
--
ALTER TABLE `sale_invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_master`
--
ALTER TABLE `sale_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_return_invoices`
--
ALTER TABLE `sale_return_invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_return_masters`
--
ALTER TABLE `sale_return_masters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_return_masters_v_no_unique` (`v_no`);

--
-- Indexes for table `sale_type`
--
ALTER TABLE `sale_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_adj_details`
--
ALTER TABLE `stock_adj_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_adj_masters`
--
ALTER TABLE `stock_adj_masters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trn_dtl`
--
ALTER TABLE `trn_dtl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workspace`
--
ALTER TABLE `workspace`
  ADD PRIMARY KEY (`cid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_masters`
--
ALTER TABLE `account_masters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `consumption_details`
--
ALTER TABLE `consumption_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=274;

--
-- AUTO_INCREMENT for table `consumption_masters`
--
ALTER TABLE `consumption_masters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `erp_params`
--
ALTER TABLE `erp_params`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `expense_detail`
--
ALTER TABLE `expense_detail`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_masters`
--
ALTER TABLE `expense_masters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `item_masters`
--
ALTER TABLE `item_masters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `item_types`
--
ALTER TABLE `item_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `level1s`
--
ALTER TABLE `level1s`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `level2s`
--
ALTER TABLE `level2s`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `opening_stock`
--
ALTER TABLE `opening_stock`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_consumed`
--
ALTER TABLE `production_consumed`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `production_detail`
--
ALTER TABLE `production_detail`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `production_master`
--
ALTER TABLE `production_master`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `purchase_invoices`
--
ALTER TABLE `purchase_invoices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `purchase_master`
--
ALTER TABLE `purchase_master`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `purchase_return_invoice`
--
ALTER TABLE `purchase_return_invoice`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `purchase_return_master`
--
ALTER TABLE `purchase_return_master`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sale_invoices`
--
ALTER TABLE `sale_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `sale_master`
--
ALTER TABLE `sale_master`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `sale_return_invoices`
--
ALTER TABLE `sale_return_invoices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sale_return_masters`
--
ALTER TABLE `sale_return_masters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sale_type`
--
ALTER TABLE `sale_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stock_adj_details`
--
ALTER TABLE `stock_adj_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_adj_masters`
--
ALTER TABLE `stock_adj_masters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `trn_dtl`
--
ALTER TABLE `trn_dtl`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `workspace`
--
ALTER TABLE `workspace`
  MODIFY `cid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `erp_params`
--
ALTER TABLE `erp_params`
  ADD CONSTRAINT `erp_params_saledebitaccount_foreign` FOREIGN KEY (`saleCreditAccount`) REFERENCES `account_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sale_debit_account` FOREIGN KEY (`saleCreditAccount`) REFERENCES `account_masters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
