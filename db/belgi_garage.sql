-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2024 at 03:41 PM
-- Server version: 8.0.39
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `belgi_garage`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `a_id` int NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`a_id`, `email`, `password`) VALUES
(1, 'admin@gmail.com', '$2y$10$l5Teq7SzVwmYVlJnLtucseDHJF8dB5ocq85e/qQGIIAjDcmO5R6Ua');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `b_id` int NOT NULL,
  `s_id` int DEFAULT NULL,
  `u_id` int DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`b_id`, `s_id`, `u_id`, `duration`, `total`, `payment_method`, `status`, `date`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES
(13, 13, 3, 4, 140.00, 'Cash', 1, '2024-10-17', '09:00:00', '13:00:00', '2024-10-16 13:21:12', '2024-10-16 13:21:12');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `r_id` int NOT NULL,
  `u_id` int DEFAULT NULL,
  `b_id` int NOT NULL,
  `s_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spaces`
--

CREATE TABLE `spaces` (
  `s_id` int NOT NULL,
  `u_id` int DEFAULT NULL,
  `post_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `latitude` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `longitude` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `full_time` tinyint(1) DEFAULT NULL,
  `mon_start` time DEFAULT NULL,
  `mon_end` time DEFAULT NULL,
  `tue_start` time DEFAULT NULL,
  `tue_end` time DEFAULT NULL,
  `wed_start` time DEFAULT NULL,
  `wed_end` time DEFAULT NULL,
  `thu_start` time DEFAULT NULL,
  `thu_end` time DEFAULT NULL,
  `fri_start` time DEFAULT NULL,
  `fri_end` time DEFAULT NULL,
  `sat_start` time DEFAULT NULL,
  `sat_end` time DEFAULT NULL,
  `sun_start` time DEFAULT NULL,
  `sun_end` time DEFAULT NULL,
  `rate` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spaces`
--

INSERT INTO `spaces` (`s_id`, `u_id`, `post_code`, `address`, `type`, `description`, `latitude`, `longitude`, `full_time`, `mon_start`, `mon_end`, `tue_start`, `tue_end`, `wed_start`, `wed_end`, `thu_start`, `thu_end`, `fri_start`, `fri_end`, `sat_start`, `sat_end`, `sun_start`, `sun_end`, `rate`, `status`) VALUES
(9, 2, '1000', 'Brussels Center', 'Driveway', 'Driveway space in central Brussels', '50.8503', '4.3517', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30, 1),
(10, 2, '2000', 'Antwerp Docklands', 'Car Park', 'Secure car park near the docks', '51.2194', '4.4025', 0, '08:00:00', '18:00:00', '08:00:00', '18:00:00', '08:00:00', '18:00:00', '08:00:00', '18:00:00', '08:00:00', '18:00:00', NULL, NULL, NULL, NULL, 40, 1),
(11, 2, '3000', 'Leuven Old Town', 'On Street', 'On-street parking in the heart of Leuven', '50.8798', '4.7005', 0, '07:00:00', '19:00:00', '07:00:00', '19:00:00', '07:00:00', '19:00:00', '07:00:00', '19:00:00', '07:00:00', '19:00:00', '08:00:00', '17:00:00', '08:00:00', '17:00:00', 20, 1),
(12, 2, '9000', 'Ghent Riverside', 'Garage', 'Private garage with river view in Ghent', '51.0543', '3.7174', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 50, 1),
(13, 2, '4000', 'Liege City Center', 'Other', 'Versatile space in busy Liege area', '50.6457', '5.573', 0, '09:00:00', '20:00:00', '09:00:00', '20:00:00', '09:00:00', '20:00:00', '09:00:00', '20:00:00', '09:00:00', '20:00:00', '10:00:00', '16:00:00', '10:00:00', '16:00:00', 35, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `u_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'placeholder.jpg',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`u_id`, `name`, `email`, `password`, `image`, `type`) VALUES
(2, 'ABCD', 'abc@xyz.com', '$2y$10$K2/2VNrU1rUeCD3ccRmcDeox5p73./Ydg7aHWEMoJDcPjdPPQnm4i', 'Screenshot 2024-08-26 064039.png', 'Owner'),
(3, 'Anas', 'abc1@xyz.com', '$2y$10$whX8Sq5hempjTuAO0BKgwuG9FkUHZ36cF75R1Ui2YnH5SFsN8CPuW', 'placeholder.jpg', 'Driver');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`a_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`b_id`),
  ADD KEY `fk_bookings_space` (`s_id`),
  ADD KEY `fk_bookings_user` (`u_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`r_id`),
  ADD KEY `fk_ratings_booking` (`b_id`),
  ADD KEY `fk_ratings_user` (`u_id`);

--
-- Indexes for table `spaces`
--
ALTER TABLE `spaces`
  ADD PRIMARY KEY (`s_id`),
  ADD KEY `fk_spaces_user` (`u_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`u_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `a_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `b_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `r_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `spaces`
--
ALTER TABLE `spaces`
  MODIFY `s_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `u_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_space` FOREIGN KEY (`s_id`) REFERENCES `spaces` (`s_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_user` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE SET NULL;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_ratings_booking` FOREIGN KEY (`b_id`) REFERENCES `bookings` (`b_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ratings_user` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `spaces`
--
ALTER TABLE `spaces`
  ADD CONSTRAINT `fk_spaces_user` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
