-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 03:53 PM
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
  `tour_type` enum('Day Tour','Night Stay') NOT NULL,
  `check_in` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `ref_number`, `user_id`, `cottage_name`, `tour_type`, `check_in`, `total_price`, `status`, `created_at`) VALUES
(1, 'BK-AAB049', 2, 'Cabana Deluxe', 'Night Stay', '2025-12-18', 3400.00, 'Pending', '2025-12-16 14:05:39');

-- --------------------------------------------------------

--
-- Table structure for table `cottages`
--

CREATE TABLE `cottages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price_day` decimal(10,2) NOT NULL,
  `price_night` decimal(10,2) NOT NULL,
  `capacity` varchar(50) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cottages`
--

INSERT INTO `cottages` (`id`, `name`, `price_day`, `price_night`, `capacity`, `image_url`) VALUES
(1, 'Cabana Deluxe', 2700.00, 3400.00, '3 Pax (Max 6)', 'https://images.unsplash.com/photo-1566073771259-6a8506099945'),
(2, 'Cozy Cabana', 2300.00, 3000.00, '2 Pax (Max 4)', 'https://images.unsplash.com/photo-1582719508461-905c673771fd'),
(3, 'Nipa House (Double Deck)', 1700.00, 2000.00, 'Group', 'uploads/1765896163_bg.jpg'),
(4, 'Nipa House (Single Bed)', 1400.00, 1700.00, 'Couple', 'https://images.unsplash.com/photo-1590523278135-25aa9690184e'),
(5, 'Beach Front Cottage', 1200.00, 1500.00, 'Open', 'https://images.unsplash.com/photo-1590523278135-25aa9690184e'),
(6, 'Open Cottage', 1000.00, 1200.00, 'Open', 'https://images.unsplash.com/photo-1590523278135-25aa9690184e'),
(7, 'Canopy Tent', 500.00, 700.00, '4 Chairs', 'https://images.unsplash.com/photo-1590523278135-25aa9690184e'),
(8, 'Camping Tent', 500.00, 500.00, '2 Pax', 'https://images.unsplash.com/photo-1590523278135-25aa9690184e');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','owner') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'Jhumari Job Galos', 'iramuhjsolag@gmail.com', '$2y$10$AGVJazLRhChE/WptPK.zMeSKNH6erpoMIEt9RaM4vsqB3Fc94ALRm', 'user', '2025-12-16 14:04:54'),
(3, 'Cymae Admin', 'cymaeadmin@gmail.com', '$2y$10$TReaiFbEmo.vGY8CTsMlk.5y/EHiag.rRvb9856pVkySaiSRHqIg6', 'owner', '2025-12-16 14:07:39');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cottages`
--
ALTER TABLE `cottages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
