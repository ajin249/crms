-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2024 at 10:57 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `review_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `hotel_restaurant_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_reviews`
--

CREATE TABLE `customer_reviews` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `hotel_restaurant_id` int(11) NOT NULL,
  `review_question_id` int(11) NOT NULL,
  `review` text,
  `rating` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hotels_restaurants`
--

CREATE TABLE `hotels_restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `status` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `website_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hotels_restaurants`
--

INSERT INTO `hotels_restaurants` (`id`, `name`, `address`, `status`, `created_by`, `created_at`, `updated_at`, `phone`, `email`, `logo`, `cover_image`, `website_link`) VALUES
(15, 'VIP HOTEL', 'Kerala', 1, 4, '2024-10-06 14:48:42', '2024-10-07 20:56:24', '9784563210', 'viphotel@gmail.com', 'uploads/vip-hotel-logo-design-template-eb72f8981df652fe0be27a9a517ae471_screen.jpg', 'uploads/atr.royalmansion-bedroom2-mr.webp', 'http://www.viphotel.com');

-- --------------------------------------------------------

--
-- Table structure for table `review_questions`
--

CREATE TABLE `review_questions` (
  `id` int(11) NOT NULL,
  `hotel_restaurant_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `type` varchar(30) NOT NULL,
  `status` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `review_questions`
--

INSERT INTO `review_questions` (`id`, `hotel_restaurant_id`, `question`, `type`, `status`, `created_by`, `created_at`) VALUES
(13, 15, 'Cleaness of rooms', 'rating', 1, 5, '2024-10-07 16:03:22'),
(14, 15, 'Staff Support', 'rating', 1, 5, '2024-10-07 16:03:37'),
(15, 15, 'Suggestions', 'text', 1, 5, '2024-10-07 20:16:37'),
(16, 15, 'Ambience', 'rating', 1, 5, '2024-10-07 20:16:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','useradmin') NOT NULL,
  `hotel_restaurant_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `hotel_restaurant_id`, `created_at`) VALUES
(4, 'superadmin', '$2y$10$bcxkFAO.qM0qFTW.PvsMbe5y/uWlWsler4OWzFmhjci1iFIvDHSci', 'superadmin', NULL, '2024-10-06 14:03:08'),
(5, 'viphotel', '$2y$10$RL4S/eVLnYP3S679jN8Xf.zfElkY3W4ZQBKdqNKlmvEk0Ck5auzdi', 'useradmin', 15, '2024-10-06 14:49:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `hotel_restaurant_id` (`hotel_restaurant_id`);

--
-- Indexes for table `customer_reviews`
--
ALTER TABLE `customer_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `hotel_restaurant_id` (`hotel_restaurant_id`),
  ADD KEY `review_question_id` (`review_question_id`);

--
-- Indexes for table `hotels_restaurants`
--
ALTER TABLE `hotels_restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review_questions`
--
ALTER TABLE `review_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_restaurant_id` (`hotel_restaurant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `customer_reviews`
--
ALTER TABLE `customer_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hotels_restaurants`
--
ALTER TABLE `hotels_restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `review_questions`
--
ALTER TABLE `review_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`hotel_restaurant_id`) REFERENCES `hotels_restaurants` (`id`);

--
-- Constraints for table `customer_reviews`
--
ALTER TABLE `customer_reviews`
  ADD CONSTRAINT `customer_reviews_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `customer_reviews_ibfk_2` FOREIGN KEY (`hotel_restaurant_id`) REFERENCES `hotels_restaurants` (`id`),
  ADD CONSTRAINT `customer_reviews_ibfk_3` FOREIGN KEY (`review_question_id`) REFERENCES `review_questions` (`id`);

--
-- Constraints for table `review_questions`
--
ALTER TABLE `review_questions`
  ADD CONSTRAINT `review_questions_ibfk_1` FOREIGN KEY (`hotel_restaurant_id`) REFERENCES `hotels_restaurants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
