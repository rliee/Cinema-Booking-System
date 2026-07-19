-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2026 at 11:06 AM
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
-- Database: `cinema_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `seats` varchar(100) DEFAULT NULL,
  `ticket_qty` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `booking_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `customer_id`, `movie_id`, `seats`, `ticket_qty`, `total_amount`, `booking_date`) VALUES
(1, 1, 1, 'A1,A2', 2, 500.00, '2026-07-19 09:00:00'),
(2, 2, 2, 'B1,B2,B3', 3, 750.00, '2026-07-19 10:00:00'),
(3, 3, 3, 'C1,C2', 2, 440.00, '2026-07-18 11:00:00'),
(4, 4, 4, 'D1,D2,D3,D4', 4, 1120.00, '2026-07-17 12:30:00'),
(5, 5, 5, 'E1', 1, 200.00, '2026-07-16 13:20:00'),
(6, 6, 6, 'F1,F2', 2, 440.00, '2026-07-15 14:10:00'),
(7, 7, 7, 'G1,G2,G3', 3, 780.00, '2026-07-14 15:00:00'),
(8, 8, 1, 'H1,H2', 2, 500.00, '2026-07-13 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `fullname`, `email`, `contact_number`) VALUES
(1, 'John Cruz', 'john@gmail.com', '09171234567'),
(2, 'Maria Santos', 'maria@gmail.com', '09181234567'),
(3, 'James Reyes', 'james@gmail.com', '09191234567'),
(4, 'Anna Dela Cruz', 'anna@gmail.com', '09201234567'),
(5, 'Michael Garcia', 'michael@gmail.com', '09211234567'),
(6, 'Sophia Flores', 'sophia@gmail.com', '09221234567'),
(7, 'Daniel Mendoza', 'daniel@gmail.com', '09231234567'),
(8, 'Angela Ramos', 'angela@gmail.com', '09241234567');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(11) NOT NULL,
  `movie_name` varchar(100) NOT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `ticket_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `movie_name`, `genre`, `duration`, `ticket_price`) VALUES
(1, 'Avatar', 'Sci-Fi', '192 min', 250.00),
(2, 'Superman', 'Action', '130 min', 250.00),
(3, 'Minecraft', 'Adventure', '115 min', 220.00),
(4, 'Avengers: Endgame', 'Action', '181 min', 280.00),
(5, 'Inside Out 2', 'Animation', '96 min', 200.00),
(6, 'How to Train Your Dragon', 'Animation', '104 min', 220.00),
(7, 'Jurassic World', 'Adventure', '146 min', 260.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `transaction_code` varchar(50) DEFAULT NULL,
  `payment_method` enum('Cash','GCash','Maya','Credit Card') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('Pending','Complete') DEFAULT 'Pending',
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `transaction_code`, `payment_method`, `amount`, `payment_status`, `payment_date`) VALUES
(1, 1, 'TXN0001', 'GCash', 500.00, 'Complete', '2026-07-19 09:05:00'),
(2, 2, 'TXN0002', 'Cash', 750.00, 'Complete', '2026-07-19 10:05:00'),
(3, 3, 'TXN0003', 'Credit Card', 440.00, 'Pending', '2026-07-18 11:05:00'),
(4, 4, 'TXN0004', 'Maya', 1120.00, 'Complete', '2026-07-17 12:35:00'),
(5, 5, 'TXN0005', 'GCash', 200.00, 'Complete', '2026-07-16 13:25:00'),
(6, 6, 'TXN0006', 'Cash', 440.00, 'Pending', '2026-07-15 14:15:00'),
(7, 7, 'TXN0007', 'Credit Card', 780.00, 'Complete', '2026-07-14 15:05:00'),
(8, 8, 'TXN0008', 'GCash', 500.00, 'Complete', '2026-07-13 16:05:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `booking_id` (`booking_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
