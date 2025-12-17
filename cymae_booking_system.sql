-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2025 at 04:20 PM
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
-- Database: `cymae_booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `ref_number` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cottage_name` varchar(100) NOT NULL,
  `tour_type` enum('Day Tour','Night Stay','24 Hours') NOT NULL,
  `check_in` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `ref_number`, `user_id`, `cottage_name`, `tour_type`, `check_in`, `total_price`, `status`, `created_at`) VALUES
(1, 'BK-AAB049', 2, 'Cabana Deluxe', 'Night Stay', '2025-12-18', 3400.00, 'Cancelled', '2025-12-16 14:05:39'),
(2, 'BK-10EB77', 4, 'Cabana Deluxe', 'Night Stay', '2025-12-19', 3400.00, 'Confirmed', '2025-12-17 00:55:56');

-- --------------------------------------------------------

--
-- Table structure for table `cottages`
--

CREATE TABLE `cottages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price_day` decimal(10,2) NOT NULL,
  `price_night` decimal(10,2) NOT NULL,
  `price_24h` decimal(10,2) NOT NULL DEFAULT 0.00,
  `capacity` varchar(50) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cottages`
--

INSERT INTO `cottages` (`id`, `name`, `price_day`, `price_night`, `price_24h`, `capacity`, `image_url`) VALUES
(1, 'Cabana Deluxe', 2700.00, 3400.00, 6000.00, '3 Pax (Max 6)', 'https://images.unsplash.com/photo-1566073771259-6a8506099945'),
(2, 'Cozy Cabana', 2300.00, 3000.00, 5000.00, '2 Pax (Max 4)', 'https://images.unsplash.com/photo-1582719508461-905c673771fd'),
(3, 'Nipa House (Double Deck)', 1700.00, 2000.00, 5000.00, 'Group', 'uploads/1765902431_nipa_doubledeck.png'),
(4, 'Nipa House (Single Bed)', 1400.00, 1700.00, 0.00, 'Couple', 'uploads/1765902450_nipa_singlebed.png'),
(5, 'Beach Front Cottage', 1200.00, 1500.00, 0.00, 'Open', 'uploads/1765902467_beachfrontcottage.png'),
(6, 'Big Open Cottage', 1000.00, 1200.00, 0.00, '20 Pax', 'uploads/1765902484_bigcottage.png'),
(7, 'Medium Open Cottage', 500.00, 700.00, 0.00, '15 Pax', 'uploads/1765902518_mediumopen.png'),
(8, 'Small Open Cottage', 500.00, 500.00, 0.00, '10 Pax', 'uploads/1765902626_opencottage.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_number` varchar(12) DEFAULT NULL,
  `role` enum('user','owner') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `contact_number`, `role`, `created_at`) VALUES
(2, 'Jhumari Job Galos', 'iramuhjsolag@gmail.com', '$2y$10$AGVJazLRhChE/WptPK.zMeSKNH6erpoMIEt9RaM4vsqB3Fc94ALRm', '09104171409', 'user', '2025-12-16 14:04:54'),
(3, 'Cymae Admin', 'cymaeadmin@gmail.com', '$2y$10$TReaiFbEmo.vGY8CTsMlk.5y/EHiag.rRvb9856pVkySaiSRHqIg6', '0', 'owner', '2025-12-16 14:07:39'),
(4, 'ruth', 'ruthadoptante698@gmail.com', '$2y$10$DAX3sottdpc.vgj5Hh/N6.DVjypyLGj.0/bN1ho/WYRUKyOWCZP8m', '0', 'user', '2025-12-17 00:54:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cottages`
--
ALTER TABLE `cottages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cottages`
--
ALTER TABLE `cottages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
