-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2026 at 02:20 AM
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
  `quantity` int(11) DEFAULT NULL,
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

INSERT INTO `historyv2` (`id`, `si_number`, `dr_number`, `delivered_to`, `tin`, `address`, `terms`, `particulars`, `si_date`, `type`, `created_at`, `machine_model`, `serial_no`, `quantity`, `under_po_no`, `under_invoice_no`, `note`, `delivery_type`, `unit_type`, `item_description`, `mr_start`, `mr_end`, `color_impression`, `black_impression`, `color_large_impression`, `technician_name`, `pr_number`, `status`) VALUES
(4, '333333', '0123', '333333', '3333333', '3333333', '3333333', '3333333', '2026-02-06', 'bnew', '2026-02-13 09:04:47', 'APV 5576', '666666', NULL, NULL, NULL, NULL, 'complete', 'UNIT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPDATED');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `historyv2`
--
ALTER TABLE `historyv2`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `historyv2`
--
ALTER TABLE `historyv2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
