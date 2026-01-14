-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 14, 2026 at 07:38 AM
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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dr_with_price`
--

CREATE TABLE `dr_with_price` (
  `id` int(11) NOT NULL,
  `dr_number` int(11) NOT NULL,
  `machine_model` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` int(11) NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `item_description` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `dr_invoice`
--
ALTER TABLE `dr_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `dr_with_price`
--
ALTER TABLE `dr_with_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `main`
--
ALTER TABLE `main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `pullout_machine`
--
ALTER TABLE `pullout_machine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `replacement_machine`
--
ALTER TABLE `replacement_machine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `used_dr`
--
ALTER TABLE `used_dr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `used_machine`
--
ALTER TABLE `used_machine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
