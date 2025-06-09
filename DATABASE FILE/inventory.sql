-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 07:58 AM
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
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `abstractofbids`
--

CREATE TABLE `abstractofbids` (
  `id` int(11) NOT NULL,
  `abstract_no` varchar(50) NOT NULL,
  `abstract_date` date NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `lot` varchar(50) DEFAULT NULL,
  `pr_number` varchar(50) DEFAULT NULL,
  `abc` decimal(18,2) DEFAULT NULL,
  `rfq_number` varchar(50) DEFAULT NULL,
  `bid_opening` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `abstractofbids`
--

INSERT INTO `abstractofbids` (`id`, `abstract_no`, `abstract_date`, `project_name`, `lot`, `pr_number`, `abc`, `rfq_number`, `bid_opening`, `status`) VALUES
(1, '213', '2025-06-09', '312', '321', '3123', 2131.00, '2312', '312', 'Done');

-- --------------------------------------------------------

--
-- Table structure for table `abstractofbids_items`
--

CREATE TABLE `abstractofbids_items` (
  `id` int(11) NOT NULL,
  `abstract_id` int(11) NOT NULL,
  `item_no` int(11) DEFAULT NULL,
  `particulars` varchar(255) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_cost` decimal(18,2) DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `bidder1_unit_cost` decimal(18,2) DEFAULT NULL,
  `bidder1_total` decimal(18,2) DEFAULT NULL,
  `bidder2_unit_cost` decimal(18,2) DEFAULT NULL,
  `bidder2_total` decimal(18,2) DEFAULT NULL,
  `bidder3_unit_cost` decimal(18,2) DEFAULT NULL,
  `bidder3_total` decimal(18,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `abstractofbids_items`
--

INSERT INTO `abstractofbids_items` (`id`, `abstract_id`, `item_no`, `particulars`, `unit`, `quantity`, `unit_cost`, `total`, `bidder1_unit_cost`, `bidder1_total`, `bidder2_unit_cost`, `bidder2_total`, `bidder3_unit_cost`, `bidder3_total`) VALUES
(1, 1, 312, '3123', '123', 12312, 3123.00, 12312.00, 3.00, 3.00, 312321.00, 321.00, 312.00, 321.00);

-- --------------------------------------------------------

--
-- Table structure for table `accounts_payable`
--

CREATE TABLE `accounts_payable` (
  `ap_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `po_id` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts_payable`
--

INSERT INTO `accounts_payable` (`ap_id`, `supplier_id`, `invoice_id`, `amount`, `due_date`, `status`, `created_at`, `po_id`, `balance`) VALUES
(15, 3, 9, 5000.00, '2025-06-09', 'Paid', '2025-06-09 05:54:31', 14, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `borrower_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(16, 'Equipments'),
(15, 'Supplies');

-- --------------------------------------------------------

--
-- Table structure for table `inspection`
--

CREATE TABLE `inspection` (
  `id` int(11) NOT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `iar_no` varchar(50) DEFAULT NULL,
  `po_no` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `date_inspected` date DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `complete` tinyint(1) DEFAULT 0,
  `quantity` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_items`
--

CREATE TABLE `inspection_items` (
  `id` int(11) NOT NULL,
  `inspection_id` int(11) NOT NULL,
  `stock_no` varchar(50) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `po_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `invoice_number`, `po_id`, `supplier_id`, `amount`, `due_date`, `status`) VALUES
(9, '304', 14, 3, 5000.00, '2025-06-09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `invoice_item_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `stock_property_no` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ledger`
--

CREATE TABLE `ledger` (
  `ledger_id` int(11) NOT NULL,
  `ap_id` int(11) NOT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `transaction_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ledger`
--

INSERT INTO `ledger` (`ledger_id`, `ap_id`, `transaction_type`, `amount`, `balance`, `transaction_date`) VALUES
(5, 15, 'Initial Entry', 5000.00, 5000.00, '2025-06-09 13:54:31'),
(6, 15, 'Payment', 3000.00, 2000.00, '2025-06-09 13:54:46'),
(7, 15, 'Payment', 2000.00, 0.00, '2025-06-09 13:54:59');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`id`, `name`, `contact_number`, `email`, `created_at`) VALUES
(0, '213', '23', '434@gmail.com', '2025-04-07 04:16:07');

-- --------------------------------------------------------

--
-- Table structure for table `principals`
--

CREATE TABLE `principals` (
  `principal_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `principals`
--

INSERT INTO `principals` (`principal_id`, `name`, `address`, `contact_number`, `email`, `created_at`) VALUES
(1, '32', '434', '5454', '657@gmail.com', '2025-05-06 06:44:59');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `po_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `tin` varchar(50) NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `mode_of_procurement` varchar(255) NOT NULL,
  `place_of_delivery` varchar(255) NOT NULL,
  `delivery_term` varchar(255) NOT NULL,
  `payment_term` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `total_amount_words` text NOT NULL,
  `confirm_supplier` varchar(255) NOT NULL,
  `confirm_date` date NOT NULL,
  `head_of_procurement` varchar(255) NOT NULL,
  `fund_cluster` varchar(255) NOT NULL,
  `funds_available` decimal(10,2) NOT NULL,
  `ors_burs_no` varchar(50) NOT NULL,
  `date_of_ors_burs` date NOT NULL,
  `purpose` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`po_id`, `supplier_name`, `address`, `tin`, `po_number`, `date`, `mode_of_procurement`, `place_of_delivery`, `delivery_term`, `payment_term`, `total_amount`, `total_amount_words`, `confirm_supplier`, `confirm_date`, `head_of_procurement`, `fund_cluster`, `funds_available`, `ors_burs_no`, `date_of_ors_burs`, `purpose`) VALUES
(14, 'ron', 'Baranggay sinawal', '30', 'PO-01', '2025-05-03', 'None', 'New Society national high school', 'within 30 days', 'Auto Debit Account', 1250.00, 'One Thousand, Two Hundred and Fifty', 'Ron', '2025-05-04', 'Principal', '23', 23.00, '23', '2025-05-03', 'Black Board'),
(15, 'ron', 'Baranggay sinawal', '0902', 'PO-02', '2025-05-03', '43', 'New Society national high school', 'within 30 days', 'Auto Debit Account', 25000.00, 'Twenty-Five Thousand', 'Ron', '2025-05-04', 'Principal', '3213', 232321.00, '32131', '2025-05-04', 'Chair'),
(16, 'ron', '23', '34', '56', '2025-05-12', '321', '435', '34', '12', 19092.00, 'Nineteen Thousand and Ninety-Two', '21', '2025-05-19', '43', '45', 12.00, '324', '2025-05-05', '324');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `order_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `stock_property_no` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`order_id`, `po_id`, `stock_property_no`, `unit`, `description`, `quantity`, `unit_cost`, `amount`) VALUES
(22, 14, '14001', '1', 'Ply wood', 1, 500.00, 500.00),
(23, 14, '14002', '5', 'Paint', 5, 150.00, 750.00),
(24, 15, '15001', '500', 'Chair', 500, 50.00, 25000.00),
(25, 16, '16001', '43', '3', 123, 43.00, 5289.00),
(26, 16, '16002', '321', '213', 321, 43.00, 13803.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requests`
--

CREATE TABLE `purchase_requests` (
  `request_id` int(11) NOT NULL,
  `entity_name` varchar(250) NOT NULL,
  `fund_cluster` varchar(250) NOT NULL,
  `office_section` varchar(250) NOT NULL,
  `pr_no` varchar(250) NOT NULL,
  `date_requested` date NOT NULL,
  `responsibility_center_code` int(250) NOT NULL,
  `purpose` text NOT NULL,
  `requestor` varchar(255) DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `status` enum('Approve','Rejected') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_requests`
--

INSERT INTO `purchase_requests` (`request_id`, `entity_name`, `fund_cluster`, `office_section`, `pr_no`, `date_requested`, `responsibility_center_code`, `purpose`, `requestor`, `approved_by`, `status`) VALUES
(37, '3213', '312', '312', '321', '2025-05-28', 312, '3123', '1312', '3123', '');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_items`
--

CREATE TABLE `purchase_request_items` (
  `id` int(11) NOT NULL,
  `purchase_request_id` int(11) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_request_items`
--

INSERT INTO `purchase_request_items` (`id`, `purchase_request_id`, `item_description`, `unit`, `quantity`, `unit_cost`, `total_cost`) VALUES
(25, 37, '321', '321', 321, 321.00, 103041.00);

-- --------------------------------------------------------

--
-- Table structure for table `rfq`
--

CREATE TABLE `rfq` (
  `id` int(11) NOT NULL,
  `rfq_no` varchar(50) NOT NULL,
  `rfq_date` date NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_address` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rfq`
--

INSERT INTO `rfq` (`id`, `rfq_no`, `rfq_date`, `company_name`, `company_address`, `status`) VALUES
(2, 'RFQ - 23', '2025-06-09', 'Crown', 'Malapatan', 'Received');

-- --------------------------------------------------------

--
-- Table structure for table `rfq_items`
--

CREATE TABLE `rfq_items` (
  `id` int(11) NOT NULL,
  `rfq_id` int(11) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rfq_items`
--

INSERT INTO `rfq_items` (`id`, `rfq_id`, `item_description`, `unit`, `quantity`) VALUES
(3, 2, 'Bugas', '3', 2);

-- --------------------------------------------------------

--
-- Table structure for table `rfq_summary`
--

CREATE TABLE `rfq_summary` (
  `id` int(11) NOT NULL,
  `rfq_id` int(11) NOT NULL,
  `establishment_name` varchar(255) NOT NULL,
  `representative` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `stocks_id` int(10) UNSIGNED NOT NULL,
  `stock_property_no` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `categorie_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`stocks_id`, `stock_property_no`, `quantity`, `unit_cost`, `description`, `amount`, `categorie_id`) VALUES
(6, '14002', 5, 150.00, 'Paint', 750.00, 15),
(7, '16001', 123, 43.00, '3', 5289.00, 16),
(8, '16002', 321, 43.00, '213', 13803.00, 15),
(9, '321', 23, 32.00, '343', 0.00, 16);

-- --------------------------------------------------------

--
-- Table structure for table `stock_report`
--

CREATE TABLE `stock_report` (
  `report_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `balance_per_card` int(11) NOT NULL,
  `on_hand_per_count` int(11) NOT NULL,
  `discrepancy` int(11) GENERATED ALWAYS AS (`on_hand_per_count` - `balance_per_card`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `name`, `address`, `contact_number`, `email`, `created_at`) VALUES
(3, 'ron', '32', '43', '50@gmail.com', '2025-04-07 05:01:53');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `office_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `address`, `contact_number`, `email`, `created_at`, `office_id`) VALUES
(0, '32123', '32', '32', '434@gmail.com', '2025-04-07 04:16:21', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `image` varchar(255) DEFAULT 'no_image.jpg',
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `status`, `image`, `last_login`) VALUES
(6, 'Ron', 'Admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', 1, 'hixaky46.JPG', '2025-06-09 07:05:52'),
(7, 'Ron Oliver', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', 1, 'no_image.jpg', NULL),
(8, 'User', 'user', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'user', 1, 'hbej2sd08.jpg', '2025-06-09 07:05:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abstractofbids`
--
ALTER TABLE `abstractofbids`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `abstractofbids_items`
--
ALTER TABLE `abstractofbids_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abstract_id` (`abstract_id`);

--
-- Indexes for table `accounts_payable`
--
ALTER TABLE `accounts_payable`
  ADD PRIMARY KEY (`ap_id`),
  ADD KEY `order_id` (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`borrower_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);
ALTER TABLE `categories` ADD FULLTEXT KEY `name_2` (`name`);

--
-- Indexes for table `inspection`
--
ALTER TABLE `inspection`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inspection_items`
--
ALTER TABLE `inspection_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspection_id` (`inspection_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`invoice_item_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `ledger`
--
ALTER TABLE `ledger`
  ADD PRIMARY KEY (`ledger_id`),
  ADD KEY `ap_id` (`ap_id`);

--
-- Indexes for table `principals`
--
ALTER TABLE `principals`
  ADD PRIMARY KEY (`principal_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`po_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `pr_no` (`pr_no`);

--
-- Indexes for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_request_id` (`purchase_request_id`);

--
-- Indexes for table `rfq`
--
ALTER TABLE `rfq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rfq_items`
--
ALTER TABLE `rfq_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rfq_id` (`rfq_id`);

--
-- Indexes for table `rfq_summary`
--
ALTER TABLE `rfq_summary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rfq_id` (`rfq_id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`stocks_id`),
  ADD UNIQUE KEY `stock_property_no` (`stock_property_no`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Indexes for table `stock_report`
--
ALTER TABLE `stock_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `order_id` (`po_id`),
  ADD KEY `order_id_2` (`order_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abstractofbids`
--
ALTER TABLE `abstractofbids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `abstractofbids_items`
--
ALTER TABLE `abstractofbids_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `accounts_payable`
--
ALTER TABLE `accounts_payable`
  MODIFY `ap_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `borrower_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `inspection`
--
ALTER TABLE `inspection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inspection_items`
--
ALTER TABLE `inspection_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `invoice_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ledger`
--
ALTER TABLE `ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `principals`
--
ALTER TABLE `principals`
  MODIFY `principal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `rfq`
--
ALTER TABLE `rfq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rfq_items`
--
ALTER TABLE `rfq_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rfq_summary`
--
ALTER TABLE `rfq_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `stocks_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stock_report`
--
ALTER TABLE `stock_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `abstractofbids_items`
--
ALTER TABLE `abstractofbids_items`
  ADD CONSTRAINT `abstractofbids_items_ibfk_1` FOREIGN KEY (`abstract_id`) REFERENCES `abstractofbids` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `accounts_payable`
--
ALTER TABLE `accounts_payable`
  ADD CONSTRAINT `accounts_payable_ibfk_2` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`),
  ADD CONSTRAINT `accounts_payable_ibfk_3` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `accounts_payable_ibfk_4` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`);

--
-- Constraints for table `inspection_items`
--
ALTER TABLE `inspection_items`
  ADD CONSTRAINT `inspection_items_ibfk_1` FOREIGN KEY (`inspection_id`) REFERENCES `inspection` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_items_ibfk_2` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE;

--
-- Constraints for table `ledger`
--
ALTER TABLE `ledger`
  ADD CONSTRAINT `ledger_ibfk_1` FOREIGN KEY (`ap_id`) REFERENCES `accounts_payable` (`ap_id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`);

--
-- Constraints for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD CONSTRAINT `purchase_request_items_ibfk_1` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`request_id`);

--
-- Constraints for table `rfq_items`
--
ALTER TABLE `rfq_items`
  ADD CONSTRAINT `rfq_items_ibfk_1` FOREIGN KEY (`rfq_id`) REFERENCES `rfq` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rfq_summary`
--
ALTER TABLE `rfq_summary`
  ADD CONSTRAINT `rfq_summary_ibfk_1` FOREIGN KEY (`rfq_id`) REFERENCES `rfq` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_report`
--
ALTER TABLE `stock_report`
  ADD CONSTRAINT `stock_report_ibfk_2` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`),
  ADD CONSTRAINT `stock_report_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `purchase_order_items` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
