-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2025 at 01:40 PM
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
(3, 'admin4', 'admin4@gmail.com', '13 lakandula Pampanga (Postal Code: 2014)', '12345', 140000, '$2y$10$tqZxwj6ObX4F2ifuaHYnN.pNcHu.3yROJxhKZGwGW7tdMZTD6tASq'),
(4, 'jp', 'jp@gmail.com', 'Mabalacat City Pampanga (Postal Code: 2014)', '09123345', 24500, '$2y$10$pnvHf5ZDb1tDadPJUKj3WOuSz4Zvo8jwudFRzqLADcvIamLqK/QBm'),
(5, 'Administrator', 'admin12@gmail.com', '', '', 0, '$2y$10$w3r6imzNeeGDRyE99E32ZOziYbbjdlb6uToJ/vdHhY/0rLuTCR7Di');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,0) NOT NULL,
  `quantity` int(11) NOT NULL,
  `selected_memory` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `product_id`, `product_name`, `product_price`, `quantity`, `selected_memory`, `payment_method`, `address`, `phone_number`, `status`, `order_date`) VALUES
(10, 3, 2, 'IPHONE 13', 31005, 1, '128', 'cashDelivery', '13 lakandula Pampanga (Postal Code: 2014)', '12345', 'Pending', '2025-11-12 17:38:47'),
(34, 3, 6, 'REALME 15 PRO 5G', 27990, 1, '128', 'point', '13 lakandula Pampanga (Postal Code: 2014)', '12345', 'Pending', '2025-11-12 20:19:58'),
(35, 3, 1, 'IPHONE 15 PRO', 63990, 1, '128', 'point', '13 lakandula Pampanga (Postal Code: 2014)', '12345', 'Pending', '2025-11-12 20:19:58');

-- --------------------------------------------------------

--
-- Table structure for table `phone`
--

CREATE TABLE `phone` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
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
  `battery` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phone`
--

INSERT INTO `phone` (`id`, `product_id`, `brand`, `name`, `price`, `points`, `getpoints`, `img`, `processor`, `os`, `resolution`, `dimention`, `camera`, `battery`, `stock`) VALUES
(1, 1, 'Apple', 'IPHONE 15 PRO+', '₱63,990', '80,000 P', 'GET 35,000 P', 'img/iphone15pro_white.PNG', 'Apple A17 Pro', 'iOS 17', '1179 x 2556 pixels, 19.5:9 ratio', '146.6 x 70.6 x 8.3 mm', '48 MP', 'Li-Ion 3274 mAh', 50),
(2, 2, 'INFINIX', 'INFINIX HOT 50i', '₱4,499', '3,000 P', 'GET 1,500 P', 'img/infinix-hot-50i.png', 'Mediatek Helio G81', 'Android 14, XOS 14.5', '720 x 1600 pixels, 20:9 ratio (~262 ppi density)', '165.7 x 77.1 x 8.1 mm (6.52 x 3.04 x 0.32 in)', '48 MP', '5000 mAh', 600),
(3, 3, 'Apple', 'IPHONE 13', '₱31,005', '50,000 P', 'GET 15,000 P', 'img/iPhone13_Midnight.png', 'Apple A15 Bionic (5 nm)', 'iOS 15', '1170 x 2532 pixels, 19.5:9 ratio (~460 ppi density)', '146.7 x 71.5 x 7.7 mm (5.78 x 2.81 x 0.30 in)', '12 MP', 'Li-Ion 3240 mAh (12.41 Wh) ', 100),
(4, 4, 'Realme', 'REALME 15 PRO 5G', '₱27,990', '40,000 P', 'GET 20,000 P', 'img/realme_15_pro.png', 'Qualcomm SM7750-AB Snapdragon 7 Gen 4 (4 nm)', 'Android 15, up to 3 major Android upgrades, Realme UI 6.0', '1280 x 2800 pixels, 19.5:9 ratio (~453 ppi density)', '162.3 x 76.2 x 7.7 mm (6.39 x 3.00 x 0.30 in)', '50 MP', ' Li-Ion 7000 mAh ', 500),
(5, 5, 'Realme', 'REALME 14 PRO+ 5G', '₱23,990', '29,000 P', 'GET 25,000 P', 'img/realme_14.png', 'Qualcomm SM7635 Snapdragon 7s Gen 3 (4 nm)', 'Android 15, up to 3 major Android upgrades, Realme UI 6.0', '1272 x 2800 pixels (~450 ppi density)', '163.5 x 77.3 x 8 mm or 8.3 mm', '50 MP', 'Si/C Li-Ion 6000 mAh', 1000),
(6, 6, 'Infinix', 'INFINIX GT 30 PRO', '₱14,199', '22,000 P', 'GET 8,000 P', 'img/infinix_gt_30.png', 'Mediatek Dimensity 8350 Ultimate (4 nm)', 'Android 15, up to 2 major Android upgrades, XOS 15', '1224 x 2720 pixels, 20:9 ratio (~440 ppi density)', '163.7 x 75.8 x 8 mm (6.44 x 2.98 x 0.31 in)', '108 MP', '5200 mAh or 5500 mAh', 50),
(7, 7, 'Infinix', 'INFINIX NOTE 50 PRO 4G', '₱10,199', '18,000 P', 'GET 6,000 P', 'img/infinix_note_50.png', 'Mediatek Helio G100 Ultimate (6 nm)', 'Android 15, up to 2 major Android upgrades, XOS 15', '1080 x 2436 pixels (~393 ppi density)', '163.3 x 74.4 x 7.3 mm (6.43 x 2.93 x 0.29 in)', '50 MP', '5200 mAh', 1000),
(8, 8, 'Apple', 'IPAD10', '₱25,000', '30,000 P', 'GET 15,000 P', 'img/ipad10.png', 'iPadOS 16.1 ', '1640 x 2360 pixels', '1640 x 2360 pixels', '248.6 x 179.5 x 7 mm', '12 MP', 'Li-Po 7606 mAh (28.6 Wh)', 10);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
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
  `battery` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_id`, `brand`, `name`, `price`, `points`, `getpoints`, `img`, `processor`, `os`, `resolution`, `dimention`, `camera`, `battery`, `stock`) VALUES
(1, 1, 'Apple', 'IPHONE 15 PRO', '₱63,990', '80,000 P', 'GET 35,000 P', 'img/iphone15pro_white.PNG', 'Apple A17 Pro', 'iOS 17', '1179 x 2556 pixels, 19.5:9 ratio', '146.6 x 70.6 x 8.3 mm', '48 MP', 'Li-Ion 3274 mAh', 50),
(2, 2, 'Apple', 'IPHONE 13', '₱31,005', '50,000 P', 'GET 15,000 P', 'img/iPhone13_Midnight.png', 'Apple A15 Bionic (5 nm)', 'iOS 15', '1170 x 2532 pixels, 19.5:9 ratio (~460 ppi density)', '146.7 x 71.5 x 7.7 mm (5.78 x 2.81 x 0.30 in)', '12 MP', 'Li-Ion 3240 mAh (12.41 Wh) ', 100),
(3, 3, 'Infinix', 'INFINIX NOTE 50 PRO 4G', '₱10,199', '18,000 P', 'GET 6,000 P', 'img/infinix_note_50.png', 'Mediatek Helio G100 Ultimate (6 nm)', 'Android 15, up to 2 major Android upgrades, XOS 15', '1080 x 2436 pixels (~393 ppi density)', '163.3 x 74.4 x 7.3 mm (6.43 x 2.93 x 0.29 in)', '50 MP', '5200 mAh', 1000),
(4, 4, 'Infinix', 'INFINIX GT 30 PRO', '₱14,199', '22,000 P', 'GET 8,000 P', 'img/infinix_gt_30.png', 'Mediatek Dimensity 8350 Ultimate (4 nm)', 'Android 15, up to 2 major Android upgrades, XOS 15', '1224 x 2720 pixels, 20:9 ratio (~440 ppi density)', '163.7 x 75.8 x 8 mm (6.44 x 2.98 x 0.31 in)', '108 MP', '5200 mAh or 5500 mAh', 50),
(5, 5, 'Realme', 'REALME 14 PRO+ 5G', '₱23,990', '89,000 P', 'GET 25,000 P', 'img/realme_14.png', 'Qualcomm SM7635 Snapdragon 7s Gen 3 (4 nm)', 'Android 15, up to 3 major Android upgrades, Realme UI 6.0', '1272 x 2800 pixels (~450 ppi density)', '163.5 x 77.3 x 8 mm or 8.3 mm', '50 MP', 'Si/C Li-Ion 6000 mAh', 1000),
(6, 6, 'Realme', 'REALME 15 PRO 5G', '₱27,990', '40,000 P', 'GET 20,000 P', 'img/realme_15_pro.png', 'Qualcomm SM7750-AB Snapdragon 7 Gen 4 (4 nm)', 'Android 15, up to 3 major Android upgrades, Realme UI 6.0', '1280 x 2800 pixels, 19.5:9 ratio (~453 ppi density)', '162.3 x 76.2 x 7.7 mm (6.39 x 3.00 x 0.30 in)', '50 MP', ' Li-Ion 7000 mAh ', 500),
(7, 7, 'INFINIX', 'INFINIX HOT 50i', '₱4,499', '3,000 P', 'GET 1,500 P', 'img/infinix-hot-50i.png', 'Mediatek Helio G81', 'Android 14, XOS 14.5', '720 x 1600 pixels, 20:9 ratio (~262 ppi density)', '165.7 x 77.1 x 8.1 mm (6.52 x 3.04 x 0.32 in)', '48 MP', '5000 mAh', 600),
(8, 9, 'APPLE', 'APPLE IPAD 10', '₱43,990', '60,000 P', 'GET 25,000 P', 'img/ipad10.png', 'Apple A14 Bionic (5 nm)', 'iPadOS 16.1 ', '1640 x 2360 pixels', '248.6 x 179.5 x 7 mm', '12 MP', 'Li-Po 7606 mAh (28.6 Wh)', 20);

-- --------------------------------------------------------

--
-- Table structure for table `tablet`
--

CREATE TABLE `tablet` (
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
  `battery` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tablet`
--

INSERT INTO `tablet` (`id`, `brand`, `name`, `price`, `points`, `getpoints`, `img`, `processor`, `os`, `resolution`, `dimention`, `camera`, `battery`, `stock`) VALUES
(1, 'APPLE', 'APPLE IPAD 10', '₱43,990', '60,000 P', 'GET 25,000 P', 'img/ipad10.png', 'Apple A14 Bionic (5 nm)', 'iPadOS 16.1 ', '1640 x 2360 pixels', '248.6 x 179.5 x 7 mm', '12 MP', 'Li-Po 7606 mAh (28.6 Wh)', 20),
(2, 'LENOVO', 'LENOVO PAD', '₱14,547', '20,000 P', 'GET 12,000 P', 'img/lenovo_tab.png', 'Mediatek Helio G85 (12 nm)', 'Android 14', '1200 x 1920 pixels, 16:10 ratio', '235.7 x 154.5 x 7.5 mm', '8 MP, AF', 'Li-Ion 5100 mAh', 140);

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
(8, 'jp', 'jp@gmail.com', '$2y$10$pnvHf5ZDb1tDadPJUKj3WOuSz4Zvo8jwudFRzqLADcvIamLqK/QBm'),
(9, 'Administrator', 'admin12@gmail.com', '$2y$10$w3r6imzNeeGDRyE99E32ZOziYbbjdlb6uToJ/vdHhY/0rLuTCR7Di');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `phone`
--
ALTER TABLE `phone`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tablet`
--
ALTER TABLE `tablet`
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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `phone`
--
ALTER TABLE `phone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tablet`
--
ALTER TABLE `tablet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
