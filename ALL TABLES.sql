-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 07:48 AM
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
-- Database: `barcode`
--
CREATE DATABASE IF NOT EXISTS `barcode` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `barcode`;

-- --------------------------------------------------------

--
-- Table structure for table `client_names`
--
-- Error reading structure for table barcode.client_names: #1932 - Table &#039;barcode.client_names&#039; doesn&#039;t exist in engine
-- Error reading data for table barcode.client_names: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `barcode`.`client_names`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `delivery_in`
--
-- Error reading structure for table barcode.delivery_in: #1932 - Table &#039;barcode.delivery_in&#039; doesn&#039;t exist in engine
-- Error reading data for table barcode.delivery_in: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `barcode`.`delivery_in`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `delivery_out`
--
-- Error reading structure for table barcode.delivery_out: #1932 - Table &#039;barcode.delivery_out&#039; doesn&#039;t exist in engine
-- Error reading data for table barcode.delivery_out: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `barcode`.`delivery_out`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `drum`
--
-- Error reading structure for table barcode.drum: #1932 - Table &#039;barcode.drum&#039; doesn&#039;t exist in engine
-- Error reading data for table barcode.drum: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `barcode`.`drum`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `toner`
--
-- Error reading structure for table barcode.toner: #1932 - Table &#039;barcode.toner&#039; doesn&#039;t exist in engine
-- Error reading data for table barcode.toner: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `barcode`.`toner`&#039; at line 1
--
-- Database: `dr`
--
CREATE DATABASE IF NOT EXISTS `dr` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `dr`;

-- --------------------------------------------------------

--
-- Table structure for table `bnew_dr`
--

CREATE TABLE `bnew_dr` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `machine_model` varchar(100) NOT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dr_with_invoice`
--

CREATE TABLE `dr_with_invoice` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `machine_model` varchar(100) DEFAULT NULL,
  `po_number` varchar(100) DEFAULT NULL,
  `invoice_number` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `delivery_type` enum('partial','complete') DEFAULT 'complete',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dr_with_invoice_items`
--

CREATE TABLE `dr_with_invoice_items` (
  `id` int(11) NOT NULL,
  `dr_with_invoice_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `item_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dr_with_price`
--

CREATE TABLE `dr_with_price` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `machine_model` varchar(100) DEFAULT NULL,
  `grand_total` decimal(15,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dr_with_price_items`
--

CREATE TABLE `dr_with_price_items` (
  `id` int(11) NOT NULL,
  `dr_with_price_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `item_description` text NOT NULL,
  `total_price` decimal(15,2) GENERATED ALWAYS AS (`quantity` * `unit_price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pullout_machines`
--

CREATE TABLE `pullout_machines` (
  `id` int(11) NOT NULL,
  `pullout_dr_id` int(11) NOT NULL,
  `machine_model` varchar(100) NOT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `mr_end` varchar(50) DEFAULT NULL,
  `color_impression` int(11) DEFAULT 0,
  `black_impression` int(11) DEFAULT 0,
  `color_large_impression` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pullout_replacement_dr`
--

CREATE TABLE `pullout_replacement_dr` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `pullout_type` enum('replacementOnly','pulloutOnly','both') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `replacement_machines`
--

CREATE TABLE `replacement_machines` (
  `id` int(11) NOT NULL,
  `pullout_dr_id` int(11) NOT NULL,
  `machine_model` varchar(100) NOT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `mr_start` varchar(50) DEFAULT NULL,
  `color_impression` int(11) DEFAULT 0,
  `black_impression` int(11) DEFAULT 0,
  `color_large_impression` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `used_dr`
--

CREATE TABLE `used_dr` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `machine_model` varchar(100) NOT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `mr_start` varchar(50) DEFAULT NULL,
  `color_impression` int(11) DEFAULT 0,
  `black_impression` int(11) DEFAULT 0,
  `color_large_impression` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `used_dr_full`
--

CREATE TABLE `used_dr_full` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `machine_model` varchar(100) DEFAULT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `mr_start` varchar(50) DEFAULT NULL,
  `technician_name` varchar(100) DEFAULT NULL,
  `pr_number` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `used_dr_items`
--

CREATE TABLE `used_dr_items` (
  `id` int(11) NOT NULL,
  `used_dr_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `item_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bnew_dr`
--
ALTER TABLE `bnew_dr`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `si_number` (`si_number`);

--
-- Indexes for table `dr_with_invoice`
--
ALTER TABLE `dr_with_invoice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `si_number` (`si_number`);

--
-- Indexes for table `dr_with_invoice_items`
--
ALTER TABLE `dr_with_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dr_with_invoice_id` (`dr_with_invoice_id`);

--
-- Indexes for table `dr_with_price`
--
ALTER TABLE `dr_with_price`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `si_number` (`si_number`);

--
-- Indexes for table `dr_with_price_items`
--
ALTER TABLE `dr_with_price_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dr_with_price_id` (`dr_with_price_id`);

--
-- Indexes for table `pullout_machines`
--
ALTER TABLE `pullout_machines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pullout_dr_id` (`pullout_dr_id`);

--
-- Indexes for table `pullout_replacement_dr`
--
ALTER TABLE `pullout_replacement_dr`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `si_number` (`si_number`);

--
-- Indexes for table `replacement_machines`
--
ALTER TABLE `replacement_machines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pullout_dr_id` (`pullout_dr_id`);

--
-- Indexes for table `used_dr`
--
ALTER TABLE `used_dr`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `si_number` (`si_number`);

--
-- Indexes for table `used_dr_full`
--
ALTER TABLE `used_dr_full`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `si_number` (`si_number`);

--
-- Indexes for table `used_dr_items`
--
ALTER TABLE `used_dr_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `used_dr_id` (`used_dr_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bnew_dr`
--
ALTER TABLE `bnew_dr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dr_with_invoice`
--
ALTER TABLE `dr_with_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dr_with_invoice_items`
--
ALTER TABLE `dr_with_invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dr_with_price`
--
ALTER TABLE `dr_with_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dr_with_price_items`
--
ALTER TABLE `dr_with_price_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pullout_machines`
--
ALTER TABLE `pullout_machines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pullout_replacement_dr`
--
ALTER TABLE `pullout_replacement_dr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `replacement_machines`
--
ALTER TABLE `replacement_machines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `used_dr`
--
ALTER TABLE `used_dr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `used_dr_full`
--
ALTER TABLE `used_dr_full`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `used_dr_items`
--
ALTER TABLE `used_dr_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dr_with_invoice_items`
--
ALTER TABLE `dr_with_invoice_items`
  ADD CONSTRAINT `dr_with_invoice_items_ibfk_1` FOREIGN KEY (`dr_with_invoice_id`) REFERENCES `dr_with_invoice` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dr_with_price_items`
--
ALTER TABLE `dr_with_price_items`
  ADD CONSTRAINT `dr_with_price_items_ibfk_1` FOREIGN KEY (`dr_with_price_id`) REFERENCES `dr_with_price` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pullout_machines`
--
ALTER TABLE `pullout_machines`
  ADD CONSTRAINT `pullout_machines_ibfk_1` FOREIGN KEY (`pullout_dr_id`) REFERENCES `pullout_replacement_dr` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `replacement_machines`
--
ALTER TABLE `replacement_machines`
  ADD CONSTRAINT `replacement_machines_ibfk_1` FOREIGN KEY (`pullout_dr_id`) REFERENCES `pullout_replacement_dr` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `used_dr_items`
--
ALTER TABLE `used_dr_items`
  ADD CONSTRAINT `used_dr_items_ibfk_1` FOREIGN KEY (`used_dr_id`) REFERENCES `used_dr_full` (`id`) ON DELETE CASCADE;
--
-- Database: `drv2`
--
CREATE DATABASE IF NOT EXISTS `drv2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `drv2`;

-- --------------------------------------------------------

--
-- Table structure for table `bnew_machine_dr`
--

CREATE TABLE `bnew_machine_dr` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `dr_number` varchar(20) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `machine_model` varchar(100) NOT NULL,
  `serial_no` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bnew_machine_dr`
--

INSERT INTO `bnew_machine_dr` (`id`, `si_number`, `dr_number`, `delivered_to`, `tin`, `address`, `terms`, `particulars`, `si_date`, `unit_type`, `machine_model`, `serial_no`, `created_at`) VALUES
(1, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '2025-12-03 14:37:38'),
(2, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '2025-12-03 14:37:38'),
(3, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123131', '2025-12-03 14:37:38'),
(4, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '231231', '2025-12-03 14:37:38'),
(5, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '312312', '2025-12-03 14:37:38'),
(6, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '123123', '2025-12-03 14:37:38'),
(7, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '123123', '2025-12-03 14:37:38'),
(8, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '123123', '2025-12-03 14:37:38'),
(9, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '123123', '2025-12-03 14:37:38'),
(10, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '123123', '2025-12-03 14:37:38'),
(11, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '123123', '2025-12-03 14:37:38'),
(12, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '123123', '2025-12-03 14:37:38'),
(13, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '123123', '2025-12-03 14:37:38'),
(14, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '123123', '2025-12-03 14:37:38'),
(15, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '131231', '2025-12-03 14:37:38');

-- --------------------------------------------------------

--
-- Table structure for table `dr_with_invoice`
--

CREATE TABLE `dr_with_invoice` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `dr_number` varchar(20) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `machine_model` varchar(100) DEFAULT NULL,
  `under_po_no` varchar(100) DEFAULT NULL,
  `under_invoice_no` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `delivery_type` enum('partial','complete') DEFAULT 'complete',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dr_with_invoice_items`
--

CREATE TABLE `dr_with_invoice_items` (
  `id` int(11) NOT NULL,
  `dr_with_invoice_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_type` varchar(50) DEFAULT NULL,
  `item_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dr_with_price`
--

CREATE TABLE `dr_with_price` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `dr_number` varchar(20) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `machine_model` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dr_with_price`
--

INSERT INTO `dr_with_price` (`id`, `si_number`, `dr_number`, `delivered_to`, `tin`, `address`, `terms`, `particulars`, `si_date`, `machine_model`, `created_at`) VALUES
(1, '000000', '', '111111', '111111', '111111', '111111', '111111', '2025-12-26', 'APV 5576', '2025-12-26 10:21:40'),
(2, '11111', '', '222222', '22222', '222222', '222222', '222222', '2025-12-27', 'APV 5576', '2025-12-26 10:31:43');

-- --------------------------------------------------------

--
-- Table structure for table `dr_with_price_items`
--

CREATE TABLE `dr_with_price_items` (
  `id` int(11) NOT NULL,
  `dr_with_price_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_type` varchar(50) DEFAULT NULL,
  `item_description` text NOT NULL,
  `total_amount` decimal(15,2) GENERATED ALWAYS AS (`quantity` * `price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dr_with_price_items`
--

INSERT INTO `dr_with_price_items` (`id`, `dr_with_price_id`, `quantity`, `price`, `unit_type`, `item_description`) VALUES
(1, 1, 5, 500.00, 'PCS', 'Toner'),
(2, 1, 10, 1500.00, 'PCS', 'TONER BLACK'),
(3, 1, 20, 900.00, 'PCS', 'ITEMS'),
(4, 2, 5, 500.00, 'PCS', 'Toner'),
(5, 2, 10, 1500.00, 'PCS', 'TONER BLACK');

-- --------------------------------------------------------

--
-- Table structure for table `pullout_machine_dr`
--

CREATE TABLE `pullout_machine_dr` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `dr_number` varchar(20) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `machine_model` varchar(100) NOT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `mr_end` varchar(50) DEFAULT NULL,
  `color_impression` int(11) DEFAULT 0,
  `black_impression` int(11) DEFAULT 0,
  `color_large_impression` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pullout_machine_dr`
--

INSERT INTO `pullout_machine_dr` (`id`, `si_number`, `dr_number`, `delivered_to`, `tin`, `address`, `terms`, `particulars`, `si_date`, `unit_type`, `machine_model`, `serial_no`, `mr_end`, `color_impression`, `black_impression`, `color_large_impression`, `created_at`) VALUES
(1, '321321', '', '321321', '321321', '321321', '3232', '321321', '2025-11-25', 'PCS', '312', '321321', '', 321321, 321321, 321321, '2025-11-28 08:27:08'),
(2, '321321', '', '321321', '321321', '321321', '3232', '321321', '2025-11-25', 'PCS', '312', '321321', '', 321321, 321321, 321321, '2025-11-28 08:29:19'),
(3, '321321', '', '321321', '321321', '321321', '3232', '321321', '2025-11-25', 'PCS', '312', '321321', '321', 3321, 321321, 321321, '2025-11-28 08:29:19'),
(4, '2222222', '', '321321', '321321', '321321', '3232', '321321', '2025-11-25', 'PCS', 'Apv 5575', '222222', '2,222,222', 222222, 222222, 2222222, '2025-11-28 08:43:38'),
(5, '2222222', '', '321321', '321321', '321321', '3232', '321321', '2025-11-25', 'PCS', 'Apv 5571', '111111', '2222222', 111111, 111111, 111111, '2025-11-28 08:55:12'),
(6, '98989898', '', '321321', '321321', '321321', '3232', '321321', '2025-11-25', 'PCS', 'APIVC 5576', '987987987', '', 0, 0, 0, '2025-12-01 10:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `replacement_machine_dr`
--

CREATE TABLE `replacement_machine_dr` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `dr_number` varchar(20) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
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
-- Dumping data for table `replacement_machine_dr`
--

INSERT INTO `replacement_machine_dr` (`id`, `si_number`, `dr_number`, `delivered_to`, `tin`, `address`, `terms`, `particulars`, `si_date`, `unit_type`, `machine_model`, `serial_no`, `mr_start`, `color_impression`, `black_impression`, `color_large_impression`, `created_at`) VALUES
(28, '111111', '', '111111', '111111', '111111', '111111', '111111', '1111-11-11', 'UNIT', 'APV 5576', '111111', '111,111', '111111', '111111', '111111', '2025-12-03 09:05:43'),
(29, '111111', '', '111111', '111111', '111111', '111111', '111111', '1111-11-11', 'UNIT', 'APIVC 5575', '123123', '123,132', '123321', '0', '0', '2025-12-03 09:05:43'),
(30, '111111', '', '111111', '111111', '111111', '111111', '111111', '1111-11-11', 'UNIT', 'APIVC 5575', '66666', '66,666', '0', '0', '0', '2025-12-03 09:05:43'),
(31, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 10:05:41'),
(32, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 10:05:41'),
(33, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '123,132', '123321', '0', '0', '2025-12-03 10:05:41'),
(34, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '222222', '', '0', '0', '0', '2025-12-03 10:05:41'),
(35, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'apv 5575', '222222', '123', '0', '0', '0', '2025-12-03 10:05:41'),
(36, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'apv 5575', '66666', '66,666', '0', '0', '0', '2025-12-03 10:05:41'),
(37, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'apv 5575', '123123', '213,123,123', '0', '0', '0', '2025-12-03 10:05:41'),
(38, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 10:09:27'),
(39, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 10:09:27'),
(40, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '11111', '1,111,111', '1111111', '111111', '111111', '2025-12-03 10:09:27'),
(41, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '222222', '123', '222222', '222222', '2222222', '2025-12-03 10:09:27'),
(42, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '66666', '66,666', '66666', '0', '0', '2025-12-03 10:09:27'),
(43, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 10:11:03'),
(44, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 10:11:03'),
(45, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '11111', '1,111,111', '1111111', '111111', '111111', '2025-12-03 10:11:03'),
(46, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 10:58:34'),
(47, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 10:58:34'),
(48, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '11111', '1,111,111', '1111111', '111111', '111111', '2025-12-03 10:58:34'),
(49, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APV 5576', '222222', '123', '222222', '222222', '2222222', '2025-12-03 10:58:34'),
(50, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '66666', '66,666', '66666', '0', '0', '2025-12-03 10:58:34'),
(51, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-03', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 13:05:11'),
(52, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-03', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 13:05:11'),
(53, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-03', 'UNIT', 'apv 5575', '123123', '123,132', '123321', '0', '0', '2025-12-03 13:05:11'),
(54, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-03', 'UNIT', 'APVC55776', '00000', '00,000', '0', '0', '0', '2025-12-03 13:05:11'),
(55, '000000', '', '9999999', '999999', '999999', '999999', '999999', '2025-12-03', 'UNIT', 'APV 5576', '123123', '123,123', '123123', '123123', '123123', '2025-12-03 13:05:29'),
(56, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '4444444', '4,444,444', '444444', '444444', '444444', '2025-12-04 09:04:40'),
(57, '99999', '', '999999', '999999', '999999', '999999', '999999', '2025-12-01', 'UNIT', 'APIVC 5575', '5555555', '5,555,555', '555555', '555555', '555555', '2025-12-04 09:04:40'),
(58, '98989898', '', '321321', '11111111', '111111111', '1111111111', '1111111111', '2025-12-04', '1', 'APIVC 5575', '2222222', '222,222', '222222', '222222', '222222', '2025-12-04 09:53:06'),
(59, '98989898', '', '321321', '11111111', '111111111', '1111111111', '1111111111', '2025-12-04', '1', 'APIVC 5575', '333333', '333,333', '333333', '333333', '333333', '2025-12-04 09:53:06'),
(60, '98989898', '', '321321', '11111111', '111111111', '1111111111', '1111111111', '2025-12-04', '1', 'APIVC 5575', '4444444', '4,444,444', '444444', '444444', '444444', '2025-12-04 09:53:06'),
(61, '98989898', '', '321321', '11111111', '111111111', '1111111111', '1111111111', '2025-12-04', '1', 'APIVC 5575', '1111111', '111,111', '111111111', '11111111', '11111', '2025-12-04 09:53:06');

-- --------------------------------------------------------

--
-- Table structure for table `used_dr_full`
--

CREATE TABLE `used_dr_full` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `dr_number` varchar(20) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
  `machine_model` varchar(100) DEFAULT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `mr_start` varchar(50) DEFAULT NULL,
  `technician_name` varchar(100) DEFAULT NULL,
  `pr_number` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `used_dr_full`
--

INSERT INTO `used_dr_full` (`id`, `si_number`, `dr_number`, `delivered_to`, `tin`, `address`, `terms`, `particulars`, `si_date`, `machine_model`, `serial_no`, `mr_start`, `technician_name`, `pr_number`, `created_at`) VALUES
(1, '11111', '', '222222', '22222', '222222', '222222', '222222', '2025-12-27', 'APV 5576', '123123, 123123, 123131, 231231, 312312', '', 'jd', '98765', '2025-12-26 10:49:07'),
(2, '11111', '', '222222', '22222', '222222', '222222', '222222', '2025-12-27', 'APV 5576', '123123, 123123, 123131, 231231, 312312', '', 'jd', '98765', '2025-12-26 11:03:52'),
(3, '11111', '', '222222', '22222', '222222', '222222', '222222', '2025-12-27', 'APV 5576', '123123123123', '123,123', 'jd', '123123123', '2025-12-26 13:16:20'),
(4, '000000', '', '111111', '111111', '111111', '111111', '111111', '2025-12-29', 'APV 5576', '1111111111', '1,111,111', 'JD', '11111111', '2025-12-29 08:34:37'),
(5, '000000', '', '111111', '111111', '111111', '111111', '111111', '2025-12-29', 'APV 5576', '1111111111', '1,111,111', 'JD', '11111111', '2025-12-29 08:37:34'),
(6, '000000', '', '111111', '111111', '111111', '111111', '111111', '2025-12-29', 'APV 5576', '1111111111', '1,111,111', 'JD', '11111111', '2025-12-29 08:55:30'),
(7, '000000', '', '111111', '111111', '111111', '111111', '111111', '2025-12-29', 'APV 5576', '333333333333', '333,333,333', 'JD', '11111111', '2025-12-29 09:10:25');

-- --------------------------------------------------------

--
-- Table structure for table `used_dr_full_items`
--

CREATE TABLE `used_dr_full_items` (
  `id` int(11) NOT NULL,
  `used_dr_full_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_type` varchar(50) DEFAULT NULL,
  `item_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `used_dr_full_items`
--

INSERT INTO `used_dr_full_items` (`id`, `used_dr_full_id`, `quantity`, `unit_type`, `item_description`) VALUES
(1, 1, 1000, 'UNIT', '123'),
(2, 1, 1000, 'PCS', '123'),
(3, 1, 1000, 'PCS', '123'),
(4, 1, 99, 'PCS', '123123'),
(5, 1, 99, 'PCS', 'TONER'),
(6, 2, 1000, 'UNIT', '123'),
(7, 2, 1000, 'PCS', '123'),
(8, 2, 1000, 'PCS', '123'),
(9, 2, 99, 'PCS', '123123'),
(10, 2, 99, 'PCS', 'TONER'),
(11, 3, 5, 'PCS', 'Toner'),
(12, 4, 5, 'PCS', 'Toner'),
(13, 5, 5, 'PCS', 'Toner'),
(14, 6, 5, 'PCS', 'Toner'),
(15, 6, 10, 'PCS', 'TONER BLACK'),
(16, 6, 1000, 'PCS', '123'),
(17, 6, 99, 'PCS', '123123'),
(18, 6, 99, 'PCS', 'TONER'),
(19, 6, 1000, '1123', 'asdasd'),
(20, 7, 5, 'PCS', 'Toner'),
(21, 7, 10, 'PCS', 'TONER BLACK'),
(22, 7, 1000, 'PCS', '123'),
(23, 7, 99, 'PCS', '123123');

-- --------------------------------------------------------

--
-- Table structure for table `used_machine_dr`
--

CREATE TABLE `used_machine_dr` (
  `id` int(11) NOT NULL,
  `si_number` varchar(50) NOT NULL,
  `dr_number` varchar(20) NOT NULL,
  `delivered_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `terms` varchar(100) DEFAULT NULL,
  `particulars` text DEFAULT NULL,
  `si_date` date NOT NULL,
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
-- Dumping data for table `used_machine_dr`
--

INSERT INTO `used_machine_dr` (`id`, `si_number`, `dr_number`, `delivered_to`, `tin`, `address`, `terms`, `particulars`, `si_date`, `unit_type`, `machine_model`, `serial_no`, `mr_start`, `color_impression`, `black_impression`, `color_large_impression`, `created_at`) VALUES
(1, '000000', '', '111111', '111111', '111111', '111111', '111111', '2025-12-29', 'PCS', 'APV 5576', '333333333333', '333,333,333', '3333333', '333333', '33333333', '2025-12-29 09:27:13'),
(2, '000000', '', '111111', '111111', '111111', '111111', '111111', '2025-12-29', 'PCS', 'APV 5576', '111111', '222,222', '989898', '2222222', '123123', '2025-12-29 09:27:13'),
(3, '000000', '', '111111', '111111', '111111', '111111', '111111', '2025-12-29', 'PCS', 'APV 5576', '111111', '222,222', '989898', '2222222', '0', '2025-12-29 09:27:13'),
(4, '123', '99999', '321321', 'asdasdasd', 'asdasdas', 'asdasda', 'asdasdads', '2025-12-29', 'PCS', 'APV 5576', '444444', '444,444', '444444', '444444', '444444', '2025-12-29 11:18:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bnew_machine_dr`
--
ALTER TABLE `bnew_machine_dr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dr_with_invoice`
--
ALTER TABLE `dr_with_invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dr_with_invoice_items`
--
ALTER TABLE `dr_with_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dr_with_invoice_id` (`dr_with_invoice_id`);

--
-- Indexes for table `dr_with_price`
--
ALTER TABLE `dr_with_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dr_with_price_items`
--
ALTER TABLE `dr_with_price_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dr_with_price_id` (`dr_with_price_id`);

--
-- Indexes for table `pullout_machine_dr`
--
ALTER TABLE `pullout_machine_dr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `replacement_machine_dr`
--
ALTER TABLE `replacement_machine_dr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `used_dr_full`
--
ALTER TABLE `used_dr_full`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `used_dr_full_items`
--
ALTER TABLE `used_dr_full_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `used_dr_full_id` (`used_dr_full_id`);

--
-- Indexes for table `used_machine_dr`
--
ALTER TABLE `used_machine_dr`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bnew_machine_dr`
--
ALTER TABLE `bnew_machine_dr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `dr_with_invoice`
--
ALTER TABLE `dr_with_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dr_with_invoice_items`
--
ALTER TABLE `dr_with_invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dr_with_price`
--
ALTER TABLE `dr_with_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dr_with_price_items`
--
ALTER TABLE `dr_with_price_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pullout_machine_dr`
--
ALTER TABLE `pullout_machine_dr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `replacement_machine_dr`
--
ALTER TABLE `replacement_machine_dr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `used_dr_full`
--
ALTER TABLE `used_dr_full`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `used_dr_full_items`
--
ALTER TABLE `used_dr_full_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `used_machine_dr`
--
ALTER TABLE `used_machine_dr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dr_with_invoice_items`
--
ALTER TABLE `dr_with_invoice_items`
  ADD CONSTRAINT `dr_with_invoice_items_ibfk_1` FOREIGN KEY (`dr_with_invoice_id`) REFERENCES `dr_with_invoice` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dr_with_price_items`
--
ALTER TABLE `dr_with_price_items`
  ADD CONSTRAINT `dr_with_price_items_ibfk_1` FOREIGN KEY (`dr_with_price_id`) REFERENCES `dr_with_price` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `used_dr_full_items`
--
ALTER TABLE `used_dr_full_items`
  ADD CONSTRAINT `used_dr_full_items_ibfk_1` FOREIGN KEY (`used_dr_full_id`) REFERENCES `used_dr_full` (`id`) ON DELETE CASCADE;
--
-- Database: `final_dr`
--
CREATE DATABASE IF NOT EXISTS `final_dr` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `final_dr`;

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
--
-- Database: `fsr`
--
CREATE DATABASE IF NOT EXISTS `fsr` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `fsr`;
--
-- Database: `office`
--
CREATE DATABASE IF NOT EXISTS `office` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `office`;

-- --------------------------------------------------------

--
-- Table structure for table `client_names`
--
-- Error reading structure for table office.client_names: #1932 - Table &#039;office.client_names&#039; doesn&#039;t exist in engine
-- Error reading data for table office.client_names: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `office`.`client_names`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `delivery_in`
--
-- Error reading structure for table office.delivery_in: #1932 - Table &#039;office.delivery_in&#039; doesn&#039;t exist in engine
-- Error reading data for table office.delivery_in: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `office`.`delivery_in`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `delivery_out`
--
-- Error reading structure for table office.delivery_out: #1932 - Table &#039;office.delivery_out&#039; doesn&#039;t exist in engine
-- Error reading data for table office.delivery_out: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `office`.`delivery_out`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `drum`
--
-- Error reading structure for table office.drum: #1932 - Table &#039;office.drum&#039; doesn&#039;t exist in engine
-- Error reading data for table office.drum: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `office`.`drum`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `epson`
--
-- Error reading structure for table office.epson: #1932 - Table &#039;office.epson&#039; doesn&#039;t exist in engine
-- Error reading data for table office.epson: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `office`.`epson`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--
-- Error reading structure for table office.maintenance: #1932 - Table &#039;office.maintenance&#039; doesn&#039;t exist in engine
-- Error reading data for table office.maintenance: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `office`.`maintenance`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `parts`
--
-- Error reading structure for table office.parts: #1932 - Table &#039;office.parts&#039; doesn&#039;t exist in engine
-- Error reading data for table office.parts: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `office`.`parts`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `record`
--
-- Error reading structure for table office.record: #1932 - Table &#039;office.record&#039; doesn&#039;t exist in engine
-- Error reading data for table office.record: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `office`.`record`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `toner`
--
-- Error reading structure for table office.toner: #1932 - Table &#039;office.toner&#039; doesn&#039;t exist in engine
-- Error reading data for table office.toner: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `office`.`toner`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `waste`
--
-- Error reading structure for table office.waste: #1932 - Table &#039;office.waste&#039; doesn&#039;t exist in engine
-- Error reading data for table office.waste: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `office`.`waste`&#039; at line 1
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2019-10-21 13:37:09', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `push`
--
CREATE DATABASE IF NOT EXISTS `push` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `push`;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--
-- Error reading structure for table push.notifications: #1932 - Table &#039;push.notifications&#039; doesn&#039;t exist in engine
-- Error reading data for table push.notifications: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `push`.`notifications`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Error reading structure for table push.users: #1932 - Table &#039;push.users&#039; doesn&#039;t exist in engine
-- Error reading data for table push.users: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `push`.`users`&#039; at line 1
--
-- Database: `sale invoice`
--
CREATE DATABASE IF NOT EXISTS `sale invoice` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sale invoice`;
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
--
-- Database: `warehouse`
--
CREATE DATABASE IF NOT EXISTS `warehouse` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `warehouse`;

-- --------------------------------------------------------

--
-- Table structure for table `client_names`
--
-- Error reading structure for table warehouse.client_names: #1932 - Table &#039;warehouse.client_names&#039; doesn&#039;t exist in engine
-- Error reading data for table warehouse.client_names: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `warehouse`.`client_names`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `delivery_in`
--
-- Error reading structure for table warehouse.delivery_in: #1932 - Table &#039;warehouse.delivery_in&#039; doesn&#039;t exist in engine
-- Error reading data for table warehouse.delivery_in: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `warehouse`.`delivery_in`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `delivery_out`
--
-- Error reading structure for table warehouse.delivery_out: #1932 - Table &#039;warehouse.delivery_out&#039; doesn&#039;t exist in engine
-- Error reading data for table warehouse.delivery_out: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `warehouse`.`delivery_out`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `drum`
--
-- Error reading structure for table warehouse.drum: #1932 - Table &#039;warehouse.drum&#039; doesn&#039;t exist in engine
-- Error reading data for table warehouse.drum: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `warehouse`.`drum`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--
-- Error reading structure for table warehouse.maintenance: #1932 - Table &#039;warehouse.maintenance&#039; doesn&#039;t exist in engine
-- Error reading data for table warehouse.maintenance: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `warehouse`.`maintenance`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `record`
--
-- Error reading structure for table warehouse.record: #1932 - Table &#039;warehouse.record&#039; doesn&#039;t exist in engine
-- Error reading data for table warehouse.record: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `warehouse`.`record`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `toner`
--
-- Error reading structure for table warehouse.toner: #1932 - Table &#039;warehouse.toner&#039; doesn&#039;t exist in engine
-- Error reading data for table warehouse.toner: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `warehouse`.`toner`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `waste`
--
-- Error reading structure for table warehouse.waste: #1932 - Table &#039;warehouse.waste&#039; doesn&#039;t exist in engine
-- Error reading data for table warehouse.waste: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `warehouse`.`waste`&#039; at line 1
--
-- Database: `waste_record`
--
CREATE DATABASE IF NOT EXISTS `waste_record` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `waste_record`;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_out`
--
-- Error reading structure for table waste_record.delivery_out: #1932 - Table &#039;waste_record.delivery_out&#039; doesn&#039;t exist in engine
-- Error reading data for table waste_record.delivery_out: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `waste_record`.`delivery_out`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `delivery_out_history`
--
-- Error reading structure for table waste_record.delivery_out_history: #1932 - Table &#039;waste_record.delivery_out_history&#039; doesn&#039;t exist in engine
-- Error reading data for table waste_record.delivery_out_history: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `waste_record`.`delivery_out_history`&#039; at line 1
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
