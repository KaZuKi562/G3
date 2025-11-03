-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2025 at 11:13 AM
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
-- Database: `swastecha_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user_address` varchar(255) NOT NULL,
  `user_number` varchar(255) NOT NULL,
  `getpoints` int(11) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`user_id`, `username`, `email`, `user_address`, `user_number`, `getpoints`, `password`) VALUES
(1, 'admin2', 'admin123@gmail.com', '123 Mabalacat City Pampanga (Postal Code: 2014)', '1234567', 0, ''),
(2, 'admin3', 'admin3@gmail.com', '', '', 0, ''),
(3, 'admin4', 'admin4@gmail.com', '13 lakandula Pampanga (Postal Code: 2014)', '12345', 0, '$2y$10$tqZxwj6ObX4F2ifuaHYnN.pNcHu.3yROJxhKZGwGW7tdMZTD6tASq'),
(4, 'jp', 'jp@gmail.com', 'Mabalacat City Pampanga (Postal Code: 2014)', '09123345', 24500, '$2y$10$pnvHf5ZDb1tDadPJUKj3WOuSz4Zvo8jwudFRzqLADcvIamLqK/QBm');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `points` varchar(255) NOT NULL,
  `getpoints` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `processor` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `resolution` varchar(255) NOT NULL,
  `dimention` varchar(255) NOT NULL,
  `camera` varchar(255) NOT NULL,
  `battery` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `brand`, `name`, `price`, `points`, `getpoints`, `img`, `processor`, `os`, `resolution`, `dimention`, `camera`, `battery`) VALUES
(1, 'Apple', 'IPHONE 15 PRO 256GB', '₱63,990', '80,000 P', 'GET 35,000 P', 'img/iphone15pro_white.PNG', 'Apple A17 Pro', 'iOS 17', '1179 x 2556 pixels, 19.5:9 ratio', '146.6 x 70.6 x 8.3 mm', '48 MP', 'Li-Ion 3274 mAh'),
(2, 'Apple', 'IPHONE 13 128GB', '₱31,005', '50,000 P', 'GET 15,000 P', 'img/iPhone13_Midnight.png', '0', '0', '0', '0', '0', '0'),
(3, 'Infinix', 'INFINIX NOTE 50 PRO 4G', '₱10,199', '18,000 P', 'GET 6,000 P', 'img/infinix_note_50.png', '0', '0', '0', '0', '0', '0'),
(4, 'Infinix', 'INFINIX GT 30 PRO', '₱14,199', '22,000 P', 'GET 8,000 P', 'img/infinix_gt_30.png', '0', '0', '0', '0', '0', '0'),
(5, 'Realme', 'REALME 14 PRO+ 5G (12GB + 512GB)', '₱23,990', '89,000 P', 'GET 25,000 P', 'img/realme_14.png', '0', '0', '0', '0', '0', '0'),
(6, 'Realme', 'REALME 15 PRO 5G (12GB + 256GB)', '₱27,990', '40,000 P', 'GET 20,000 P', 'img/realme_15_pro.png', '0', '0', '0', '0', '0', '0'),
(7, 'INFINIX', 'INFINIX HOT 50i', '₱4,499', '3,000 P', 'GET 1,500 P', 'img/infinix-hot-50i.png', '0', '0', '0', '0', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`) VALUES
(1, 'John_Art', 'johnpaulonunez829@gmail.com', '$2y$10$ipvjKm9q/fRYDrr4CR0vcupH1i/tsnVJ6k8oFS1911xBQayKSFBWi'),
(2, 'ewan', 'ewwan@gmail.com', '$2y$10$rwPqLhOXA5R0xGi1f7/XGeJBTeqF7pS4FBmPliwnfc5zAUFZWn/mG'),
(3, 'asads', 'benz198814@gmail.com', '$2y$10$38M7j6Q9R7znyz1dcjZdue4vLEa3swG8d4a/ujpENRp0yArHNtdoS'),
(4, 'admin', 'admin@gmail.com', '$2y$10$ALIOd.4bqZOSXleet5S.fez/LzA1z14TI6pgrQj5gv9OerwFlMUYy'),
(5, 'admin2', 'admin123@gmail.com', '$2y$10$m3tU2eAg.tw3PXDy/CK93ex6BMpQJkT0bIIVCC.D9hm5lor44eoSa'),
(6, 'admin3', 'admin3@gmail.com', '$2y$10$YIdFjFTfsjFhJ3jBv1w3wumDTj2NLsSc8ncr2TVEbyvjNxhXxjXRe'),
(7, 'admin4', 'admin4@gmail.com', '$2y$10$tqZxwj6ObX4F2ifuaHYnN.pNcHu.3yROJxhKZGwGW7tdMZTD6tASq'),
(8, 'jp', 'jp@gmail.com', '$2y$10$pnvHf5ZDb1tDadPJUKj3WOuSz4Zvo8jwudFRzqLADcvIamLqK/QBm');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
