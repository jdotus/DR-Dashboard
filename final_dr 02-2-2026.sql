-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 07:49 AM
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
-- Database: `final_dr`
--

-- --------------------------------------------------------

--
-- Table structure for table `bnew_machine`
--

CREATE TABLE `bnew_machine` (
  `id` int(11) NOT NULL,
  `dr_number` varchar(250) NOT NULL,
  `unit_type` varchar(50) NOT NULL,
  `machine_model` varchar(150) NOT NULL,
  `serial_no` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'CREATED',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dr_invoice`
--

CREATE TABLE `dr_invoice` (
  `id` int(11) NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `dr_number` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_description` text NOT NULL,
  `machine_model` varchar(100) DEFAULT NULL,
  `under_po_no` varchar(100) DEFAULT NULL,
  `under_invoice_no` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `delivery_type` enum('partial','complete') DEFAULT 'complete',
  `status` varchar(50) NOT NULL DEFAULT 'CREATED',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dr_invoice`
--

INSERT INTO `dr_invoice` (`id`, `unit_type`, `dr_number`, `quantity`, `item_description`, `machine_model`, `under_po_no`, `under_invoice_no`, `note`, `delivery_type`, `status`, `created_at`) VALUES
(1, 'PCS', 15, 5, 'TONER', 'APV 5576', '656565', '', 'To be Completed', 'partial', 'CREATED', '2026-02-20 14:35:38'),
(2, 'PCS', 15, 10, 'TONER', 'APV 5576', '656565', '', 'To be Completed', 'partial', 'CREATED', '2026-02-20 14:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `dr_with_price`
--

CREATE TABLE `dr_with_price` (
  `id` int(11) NOT NULL,
  `dr_number` varchar(50) NOT NULL,
  `machine_model` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` int(11) NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `item_description` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'CREATED',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dr_with_price`
--

INSERT INTO `dr_with_price` (`id`, `dr_number`, `machine_model`, `quantity`, `price`, `total`, `unit_type`, `item_description`, `status`, `created_at`) VALUES
(1, '015', 'APV 5576', 50, 1000.00, 50000, 'PCS', 'TONER', 'CREATED', '2026-02-20 14:34:53'),
(2, '015', 'APV 5576', 10, 900.00, 9000, 'PCS', 'TONER', 'CREATED', '2026-02-20 14:34:53');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `dr_number` varchar(50) NOT NULL,
  `delivered_to` varchar(200) NOT NULL,
  `tin` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `terms` varchar(200) NOT NULL,
  `particulars` text NOT NULL,
  `si_date` date NOT NULL,
  `type` varchar(100) NOT NULL,
  `status` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `si_number`, `dr_number`, `delivered_to`, `tin`, `address`, `terms`, `particulars`, `si_date`, `type`, `status`, `created_at`) VALUES
(1, '111111', '696969', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'usedmachine', '', '2026-02-04 08:57:21'),
(2, '111111', '696969', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'usedmachine', '', '2026-02-04 09:48:31'),
(3, '111111', '989898', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'bnew', '', '2026-02-04 09:49:08'),
(4, '111111', '111111', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'pulloutandreplacement', '', '2026-02-04 09:51:08'),
(5, '111111', '88888', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'replacementmachine', '', '2026-02-04 11:12:39'),
(6, '111111', '222222', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'replacementmachine', 'Created', '2026-02-04 11:52:34'),
(7, '99999', '999999', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-02-04', 'replacementmachine', 'Created', '2026-02-04 14:52:02'),
(8, '99999', '999999', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-02-04', 'pulloutmachine', 'Created', '2026-02-04 15:00:47'),
(9, '99999', '777777', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-02-04', 'replacementmachine', 'Created', '2026-02-04 15:32:51'),
(10, '99999', '12333', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-02-04', 'pulloutmachine', 'Created', '2026-02-04 15:40:46'),
(11, '111111', '8787878', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'pulloutmachine', 'Created', '2026-02-04 16:30:47'),
(12, '565665', '656565', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'replacementmachine', 'Created', '2026-02-06 08:24:12'),
(13, '565665', '6565656', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'pulloutmachine', 'Created', '2026-02-06 08:25:18'),
(14, '565665', '987', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'pulloutmachine', 'Created', '2026-02-06 08:27:46'),
(15, '565665', '9878', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'pulloutmachine', 'Created', '2026-02-06 08:29:28'),
(16, '565665', '98789', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'pulloutmachine', 'Created', '2026-02-06 08:32:44'),
(17, '565665', '987897', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'replacementmachine', 'Created', '2026-02-06 08:40:26'),
(18, '565665', '9878971', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'pulloutmachine', 'Created', '2026-02-06 08:53:10'),
(19, '565665', '978978', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'pulloutmachine', 'Created', '2026-02-06 08:56:11'),
(20, '565665', '978978', 'TEST', 'TEST', 'TEST', 'TEST', 'TEST', '2026-01-31', 'pulloutmachine', 'Created', '2026-02-06 09:13:49'),
(21, '000000', '0000000', 'JOHN DAVID', 'JOHN DAVID', 'JOHN DAVID', 'JOHN DAVID', 'JOHN DAVID', '2026-02-06', 'usedmachine', 'Created', '2026-02-06 09:55:09'),
(22, '333333', '333333', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'Created', '2026-02-06 10:07:58'),
(23, '444444', '444444', '444444', '444444', '444444', '4444441', '444444', '2026-02-06', 'usedmachine', 'Created', '2026-02-06 10:10:41'),
(24, '555555', '555555', '555555', '555555', '555555', '555555', '555555', '2026-02-06', 'usedmachine', 'Created', '2026-02-06 10:16:30'),
(25, '66666', '66666', '66666', '66666', '66666', '66666', '66666', '2026-02-06', 'usedmachine', 'Created', '2026-02-06 10:24:37'),
(26, '777777', '7777771', '777777', '777777', '777777', '777777', '777777', '2026-02-06', 'usedmachine', 'Created', '2026-02-06 10:35:17'),
(27, '777777', '7777772', '777777', '777777', '777777', '777777', '777777', '2026-02-06', 'usedmachine', 'Created', '2026-02-06 10:57:11'),
(28, '777777', '7777773', '777777', '777777', '777777', '777777', '777777', '2026-02-06', 'usedmachine', 'Created', '2026-02-06 11:00:01'),
(29, '777777', '7777773', '777777', '777777', '777777', '777777', '777777', '2026-02-06', 'usedmachine', 'updated', '2026-02-09 09:01:53'),
(30, '777777', '7777773', '777777', '777777', '777777', '777777', '777777', '2026-02-06', 'usedmachine', 'updated', '2026-02-09 10:08:15'),
(32, '333333', '9111111', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:05:20'),
(33, '333333', '9111112', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:28:42'),
(34, '333333', '9111113', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:29:52'),
(35, '333333', '9111115', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:31:58'),
(36, '333333', '9111116', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:33:22'),
(37, '333333', '9111117', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:41:55'),
(38, '333333', '9111118', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:42:52'),
(39, '333333', '9111119', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:43:06'),
(40, '333333', '910', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:44:44'),
(41, '333333', '1', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:49:14'),
(42, '333333', '2', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:50:16'),
(43, '333333', '3', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-12 15:50:35'),
(44, '333333', '0123', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'bnew', 'CREATED', '2026-02-13 08:59:24'),
(45, '333333', '014', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'usedmachine', 'CREATED', '2026-02-13 14:27:23'),
(46, '333333', '030', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drwithprice', 'CREATED', '2026-02-16 10:00:20'),
(47, '333333', '037', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 11:33:39'),
(48, '333333', '037', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 13:23:18'),
(49, '333333', '038', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 13:38:17'),
(50, '333333', '039', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 13:52:51'),
(51, '333333', '040', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 14:00:37'),
(52, '333333', '041', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 14:06:45'),
(53, '333333', '042', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'useddr', 'CREATED', '2026-02-16 14:13:21'),
(54, '333333', '042', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 14:16:57'),
(55, '333333', '045', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 14:26:27'),
(56, '333333', '046', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 14:29:26'),
(57, '333333', '046', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'useddr', 'CREATED', '2026-02-16 14:35:58'),
(58, '333333', '057', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:15:24'),
(59, '333333', '058', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:19:14'),
(60, '333333', '059', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:25:14'),
(61, '333333', '060', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:25:53'),
(62, '333333', '061', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:29:27'),
(63, '333333', '062', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:29:44'),
(64, '333333', '063', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:29:59'),
(65, '333333', '064', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:34:06'),
(66, '333333', '065', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:35:33'),
(67, '333333', '066', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:35:45'),
(68, '333333', '067', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:36:34'),
(69, '333333', '068', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:38:12'),
(70, '333333', '069', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:39:03'),
(71, '333333', '070', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:40:15'),
(72, '333333', '071', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:41:04'),
(73, '333333', '072', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:41:27'),
(74, '333333', '073', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:43:30'),
(75, '333333', '074', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:45:36'),
(76, '333333', '075', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:45:56'),
(77, '333333', '076', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:47:53'),
(78, '333333', '078', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:48:23'),
(79, '333333', '079', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:48:35'),
(80, '333333', '080', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'drinvoice', 'CREATED', '2026-02-16 16:49:04');

-- --------------------------------------------------------

--
-- Table structure for table `historyv2`
--

CREATE TABLE `historyv2` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `dr_number` varchar(50) NOT NULL,
  `delivered_to` varchar(200) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `type` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `machine_model` varchar(150) DEFAULT NULL,
  `serial_no` text DEFAULT NULL,
  `quantity` varchar(100) DEFAULT NULL,
  `price` varchar(100) NOT NULL,
  `total` varchar(150) NOT NULL,
  `under_po_no` varchar(100) DEFAULT NULL,
  `under_invoice_no` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `delivery_type` enum('partial','complete') DEFAULT 'complete',
  `unit_type` varchar(50) DEFAULT NULL,
  `item_description` text DEFAULT NULL,
  `mr_start` varchar(50) DEFAULT NULL,
  `mr_end` varchar(50) DEFAULT NULL,
  `color_impression` varchar(150) DEFAULT NULL,
  `black_impression` varchar(150) DEFAULT NULL,
  `color_large_impression` varchar(150) DEFAULT NULL,
  `technician_name` varchar(100) DEFAULT NULL,
  `pr_number` varchar(100) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'CREATED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historyv2`
--

INSERT INTO `historyv2` (`id`, `si_number`, `dr_number`, `delivered_to`, `tin`, `address`, `terms`, `particulars`, `si_date`, `type`, `created_at`, `machine_model`, `serial_no`, `quantity`, `price`, `total`, `under_po_no`, `under_invoice_no`, `note`, `delivery_type`, `unit_type`, `item_description`, `mr_start`, `mr_end`, `color_impression`, `black_impression`, `color_large_impression`, `technician_name`, `pr_number`, `status`) VALUES
(1, '01', '01', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 10:35:16', 'APV 5571', '02', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, NULL, '2', '2', '2', '2', NULL, NULL, 'CREATED'),
(2, '01', '02', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 10:41:27', 'APV 5571', '02', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, NULL, '2', '2', '2', '2', NULL, NULL, 'CREATED'),
(3, '01', '03', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 10:46:28', 'APV 5571', '02', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, NULL, '2', '2', '2', '2', NULL, NULL, 'CREATED'),
(4, '01', '04', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 10:55:24', 'APV 5571', '02', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, NULL, '2', '2', '2', '2', NULL, NULL, 'CREATED'),
(5, '02', '05', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 11:23:30', 'APV 5576', '044', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, NULL, '44', '44', '44', '44', NULL, NULL, 'CREATED'),
(6, '02', '05', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 11:23:30', 'APV 5571', '055', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, NULL, '55', '55', '55', '55', NULL, NULL, 'CREATED'),
(7, '02', '06', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 11:26:59', 'APV 5571', '055', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, NULL, '55', '55', '55', '55', NULL, NULL, 'CREATED'),
(8, '02', '07', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 11:33:25', 'APV 5576', '044', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, NULL, '44', '44', '44', '44', NULL, NULL, 'CREATED'),
(9, '02', '07', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 11:33:25', 'APV 5571', '055', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, NULL, '55', '55', '55', '55', NULL, NULL, 'CREATED'),
(10, '02', '08', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 11:35:57', 'APV 5576, APV 5571', '044, 055', NULL, '', '', NULL, NULL, NULL, 'complete', 'test, test', NULL, NULL, '44, 55', '44, 55', '44, 55', '44, 55', NULL, NULL, 'CREATED'),
(11, '02', '09', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 11:44:28', 'APV 5576', '044', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, '0', NULL, '44', '44', '44', NULL, NULL, 'CREATED'),
(12, '02', '09', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 11:44:28', 'APV 5571', '044', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, '44', NULL, '44', '44', '44', NULL, NULL, 'CREATED'),
(13, '02', '010', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 11:47:35', 'APV 5576, APV 5571', '044, 044', NULL, '', '', NULL, NULL, NULL, 'complete', 'test, test', NULL, '0, 44', NULL, '44, 44', '44, 44', '44, 44', NULL, NULL, 'CREATED'),
(14, '02', '011', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutandreplacement', '2026-02-20 14:15:00', 'APV 5576, APV 5576, APV 5571, APV 5571', '1010, 3030, 2020, 4040', NULL, '', '', NULL, NULL, NULL, 'complete', 'test, test, test, test', NULL, '1010, 3030', '2020, 4040', '1010, 3030, 2020, 4040', '1010, 3030, 2020, 4040', '1010, 3030, 2020, 4040', NULL, NULL, 'CREATED'),
(15, '02', '012', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutandreplacement', '2026-02-20 14:18:33', 'APV 5576, APV 5571, APV 5571', '1010, 2020, 4040', NULL, '', '', NULL, NULL, NULL, 'complete', 'test, test, test', NULL, '1010', '2020, 4040', '1010, 2020, 4040', '1010, 2020, 4040', '1010, 2020, 4040', NULL, NULL, 'CREATED'),
(16, '02', '013', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 14:22:48', 'APV 5576', '044', NULL, '', '', NULL, NULL, NULL, 'complete', 'test', NULL, '3', NULL, '44', '44', '44', NULL, NULL, 'CREATED'),
(17, '02', '013', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 14:24:23', 'APV 5576, APV 5571', '044, 0555', NULL, '', '', NULL, NULL, NULL, 'complete', 'test, test', NULL, '4444, 555', NULL, '44, 5555', '44, 5555', '44, 555', NULL, NULL, 'CREATED'),
(18, '02', '014', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 14:30:00', 'APV 5576, APV 5571', '044, 0555', NULL, '', '', NULL, NULL, NULL, 'complete', 'test, test', NULL, '4444, 555', NULL, '44, 5555', '44, 5555', '44, 555', NULL, NULL, 'CREATED'),
(19, '02', '015', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'drwithprice', '2026-02-20 14:34:53', 'APV 5576', NULL, '50,10', '1000,900', '50000,9000', NULL, NULL, NULL, 'complete', 'PCS', 'TONER,TONER', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CREATED'),
(20, '02', '015', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'drinvoice', '2026-02-20 14:35:38', 'APV 5576, ', NULL, '5, 10', '', '', '656565, ', ', ', 'To be Completed, ', 'partial', 'PCS, PCS', 'TONER, TONER', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CREATED'),
(21, '02', '016', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'useddr', '2026-02-20 14:36:53', 'APV 5576,', '111111,', '5,50', '', '', NULL, NULL, NULL, 'complete', 'Array', 'TONER,TONER', '5,000,', NULL, NULL, NULL, NULL, 'RON,', '1231231,', 'CREATED');

-- --------------------------------------------------------

--
-- Table structure for table `main`
--

CREATE TABLE `main` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `dr_number` varchar(50) NOT NULL,
  `delivered_to` varchar(200) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `type` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `main`
--

INSERT INTO `main` (`id`, `si_number`, `dr_number`, `delivered_to`, `tin`, `address`, `terms`, `particulars`, `si_date`, `type`, `created_at`) VALUES
(1, '01', '01', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 10:35:16'),
(2, '01', '02', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 10:41:27'),
(3, '01', '03', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 10:46:28'),
(4, '01', '04', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 10:55:24'),
(5, '02', '05', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 11:23:30'),
(6, '02', '06', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 11:26:59'),
(7, '02', '07', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 11:33:25'),
(8, '02', '08', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutmachine', '2026-02-20 11:35:57'),
(9, '02', '09', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 11:44:28'),
(10, '02', '010', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 11:47:35'),
(11, '02', '011', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutandreplacement', '2026-02-20 14:15:00'),
(12, '02', '012', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'pulloutandreplacement', '2026-02-20 14:18:33'),
(13, '02', '013', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 14:22:48'),
(14, '02', '013', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 14:24:23'),
(15, '02', '014', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'replacementmachine', '2026-02-20 14:30:00'),
(16, '02', '015', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'drwithprice', '2026-02-20 14:34:53'),
(17, '02', '015', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'drinvoice', '2026-02-20 14:35:38'),
(18, '02', '016', 'test', 'test', 'test', 'test', 'test', '2026-02-20', 'useddr', '2026-02-20 14:36:53');

-- --------------------------------------------------------

--
-- Table structure for table `pullout_machine`
--

CREATE TABLE `pullout_machine` (
  `id` int(11) NOT NULL,
  `dr_number` int(11) NOT NULL,
  `unit_type` varchar(50) DEFAULT 'UNITS',
  `machine_model` varchar(120) NOT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `mr_end` varchar(50) DEFAULT '0',
  `color_impression` int(11) DEFAULT 0,
  `black_impression` int(11) DEFAULT 0,
  `color_large_impression` int(11) DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT 'CREATED',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pullout_machine`
--

INSERT INTO `pullout_machine` (`id`, `dr_number`, `unit_type`, `machine_model`, `serial_no`, `mr_end`, `color_impression`, `black_impression`, `color_large_impression`, `status`, `created_at`) VALUES
(1, 1, 'test', 'APV 5571', '02', '2', 2, 2, 2, 'CREATED', '2026-02-20 10:35:16'),
(2, 2, 'test', 'APV 5571', '02', '2', 2, 2, 2, 'CREATED', '2026-02-20 10:41:27'),
(3, 3, 'test', 'APV 5571', '02', '2', 2, 2, 2, 'CREATED', '2026-02-20 10:46:28'),
(4, 4, 'test', 'APV 5571', '02', '2', 2, 2, 2, 'CREATED', '2026-02-20 10:55:24'),
(5, 5, 'test', 'APV 5576', '044', '44', 44, 44, 44, 'CREATED', '2026-02-20 11:23:30'),
(6, 5, 'test', 'APV 5571', '055', '55', 55, 55, 55, 'CREATED', '2026-02-20 11:23:30'),
(7, 6, 'test', 'APV 5576', '044', '44', 44, 44, 44, 'CREATED', '2026-02-20 11:26:59'),
(8, 6, 'test', 'APV 5571', '055', '55', 55, 55, 55, 'CREATED', '2026-02-20 11:26:59'),
(9, 7, 'test', 'APV 5576', '044', '44', 44, 44, 44, 'CREATED', '2026-02-20 11:33:25'),
(10, 7, 'test', 'APV 5571', '055', '55', 55, 55, 55, 'CREATED', '2026-02-20 11:33:25'),
(11, 8, 'test', 'APV 5576', '044', '44', 44, 44, 44, 'CREATED', '2026-02-20 11:35:57'),
(12, 8, 'test', 'APV 5571', '055', '55', 55, 55, 55, 'CREATED', '2026-02-20 11:35:57'),
(13, 11, 'test', 'APV 5571', '2020', '2,020', 2020, 2020, 2020, 'CREATED', '2026-02-20 14:15:00'),
(14, 11, 'test', 'APV 5571', '4040', '4,040', 4040, 4040, 4040, 'CREATED', '2026-02-20 14:15:00'),
(15, 12, 'test', 'APV 5571', '2020', '2,020', 2020, 2020, 2020, 'CREATED', '2026-02-20 14:18:33'),
(16, 12, 'test', 'APV 5571', '4040', '4,040', 4040, 4040, 4040, 'CREATED', '2026-02-20 14:18:33');

-- --------------------------------------------------------

--
-- Table structure for table `replacement_machine`
--

CREATE TABLE `replacement_machine` (
  `id` int(11) NOT NULL,
  `dr_number` int(11) NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `machine_model` varchar(100) NOT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `mr_start` varchar(50) DEFAULT NULL,
  `color_impression` varchar(150) DEFAULT NULL,
  `black_impression` varchar(150) DEFAULT NULL,
  `color_large_impression` varchar(150) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'CREATED',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `replacement_machine`
--

INSERT INTO `replacement_machine` (`id`, `dr_number`, `unit_type`, `machine_model`, `serial_no`, `mr_start`, `color_impression`, `black_impression`, `color_large_impression`, `status`, `created_at`) VALUES
(1, 9, 'test', 'APV 5576', '044', '', '44', '44', '44', 'CREATED', '2026-02-20 11:44:28'),
(2, 9, 'test', 'APV 5571', '044', '044', '44', '44', '44', 'CREATED', '2026-02-20 11:44:28'),
(3, 10, 'test', 'APV 5576', '044', '0', '44', '44', '44', 'CREATED', '2026-02-20 11:47:35'),
(4, 10, 'test', 'APV 5571', '044', '44', '44', '44', '44', 'CREATED', '2026-02-20 11:47:35'),
(5, 11, 'test', 'APV 5576', '1010', '1,010', '1010', '1010', '1010', 'CREATED', '2026-02-20 14:15:00'),
(6, 11, 'test', 'APV 5576', '3030', '3,030', '3030', '3030', '3030', 'CREATED', '2026-02-20 14:15:00'),
(7, 12, 'test', 'APV 5576', '1010', '1,010', '1010', '1010', '1010', 'CREATED', '2026-02-20 14:18:33'),
(8, 13, 'test', 'APV 5576', '044', '3', '44', '44', '44', 'CREATED', '2026-02-20 14:22:48'),
(9, 13, 'test', 'APV 5576', '044', '4444', '44', '44', '44', 'CREATED', '2026-02-20 14:24:23'),
(10, 13, 'test', 'APV 5571', '0555', '555', '5555', '5555', '555', 'CREATED', '2026-02-20 14:24:23'),
(11, 14, 'test', 'APV 5576', '044', '4444', '44', '44', '44', 'CREATED', '2026-02-20 14:30:00'),
(12, 14, 'test', 'APV 5571', '0555', '555', '5555', '5555', '555', 'CREATED', '2026-02-20 14:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `used_dr`
--

CREATE TABLE `used_dr` (
  `id` int(11) NOT NULL,
  `dr_number` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit_type` varchar(50) DEFAULT 'UNITS',
  `item_description` text NOT NULL,
  `machine_model` varchar(100) DEFAULT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `mr_start` varchar(50) DEFAULT NULL,
  `technician_name` varchar(100) DEFAULT NULL,
  `pr_number` varchar(100) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'CREATED',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `used_dr`
--

INSERT INTO `used_dr` (`id`, `dr_number`, `quantity`, `unit_type`, `item_description`, `machine_model`, `serial_no`, `mr_start`, `technician_name`, `pr_number`, `status`, `created_at`) VALUES
(1, 16, 5, 'PCS', 'TONER', 'APV 5576', '111111', '5,000', 'RON', '1231231', 'CREATED', '2026-02-20 14:36:53'),
(2, 16, 50, 'PCS', 'TONER', 'APV 5576', '111111', '5,000', 'RON', '1231231', 'CREATED', '2026-02-20 14:36:53');

-- --------------------------------------------------------

--
-- Table structure for table `used_machine`
--

CREATE TABLE `used_machine` (
  `id` int(11) NOT NULL,
  `dr_number` int(11) NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `machine_model` varchar(100) NOT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `mr_start` varchar(50) DEFAULT NULL,
  `color_impression` varchar(150) DEFAULT NULL,
  `black_impression` varchar(150) DEFAULT NULL,
  `color_large_impression` varchar(150) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'CREATED',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bnew_machine`
--
ALTER TABLE `bnew_machine`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dr_invoice`
--
ALTER TABLE `dr_invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dr_with_price`
--
ALTER TABLE `dr_with_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `historyv2`
--
ALTER TABLE `historyv2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `main`
--
ALTER TABLE `main`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pullout_machine`
--
ALTER TABLE `pullout_machine`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `replacement_machine`
--
ALTER TABLE `replacement_machine`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `used_dr`
--
ALTER TABLE `used_dr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `used_machine`
--
ALTER TABLE `used_machine`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bnew_machine`
--
ALTER TABLE `bnew_machine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dr_invoice`
--
ALTER TABLE `dr_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dr_with_price`
--
ALTER TABLE `dr_with_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `historyv2`
--
ALTER TABLE `historyv2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `main`
--
ALTER TABLE `main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pullout_machine`
--
ALTER TABLE `pullout_machine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `replacement_machine`
--
ALTER TABLE `replacement_machine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `used_dr`
--
ALTER TABLE `used_dr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `used_machine`
--
ALTER TABLE `used_machine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
