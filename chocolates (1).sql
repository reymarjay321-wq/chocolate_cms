-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2026 at 05:41 AM
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
-- Database: `chocolate_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `chocolates`
--

CREATE TABLE `chocolates` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `flavor` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `expiration_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chocolates`
--

INSERT INTO `chocolates` (`id`, `name`, `brand`, `category`, `flavor`, `price`, `quantity`, `expiration_date`, `description`, `image`, `date_added`) VALUES
(1, 'Silk Dark Bar', 'Cadbury', 'Dark Chocolate', 'Rich Cocoa', 20.00, 100, '2026-10-20', 'Dark', 'choco_6a139969380b04.98589156.jpeg', '2026-05-25 00:35:53'),
(2, 'KITKAT', 'Cadbury', 'Dark Chocolate', 'Dark', 21.00, 12, '2026-10-23', 'Goods', 'choco_6a13998f821be2.83934002.webp', '2026-05-25 00:36:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chocolates`
--
ALTER TABLE `chocolates`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chocolates`
--
ALTER TABLE `chocolates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
