-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2025 at 11:46 PM
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
-- Database: `brs_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `phone` int(11) NOT NULL,
  `password` text NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`, `email`, `phone`, `password`, `address`, `created_at`) VALUES
(7, 'auhtor1', 'author1@gmail.com', 1234567890, '$2y$10$MnCUJ.PQa/g1B8I.Bqpu4OSrkECbIJyxLDaQ.6afuxaRlQZTp.TO2', 'abc', '2025-07-01 21:12:03'),
(8, 'auhtor2', 'author2@gmail.com', 1234567890, '$2y$10$yoCQur3V64f.pd1oi6YAeOzLNZxSjelQ7oxVxchqLdDBs791UhheC', 'abc', '2025-07-01 21:12:24');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `img` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `vol` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `name`, `img`, `author_id`, `vol`, `created_at`, `pdf`) VALUES
(24, 'book2', '68644f6a1ad2c.jpg', 7, '2', '2025-07-01 21:13:14', '68644f6a1ad2e.pdf'),
(25, 'book3', '68644fb612e23.jpg', 7, '1', '2025-07-01 21:14:30', '68644fb612e26.pdf'),
(26, 'book4', '68644fcd135fc.jpg', 7, '2', '2025-07-01 21:14:53', '68644fcd13600.pdf'),
(27, 'book1', '68645041b4fb2.png', 7, '1', '2025-07-01 21:16:49', '68645041b4fb5.pdf'),
(28, 'book5', '686450660ce1b.jpg', 8, '1', '2025-07-01 21:17:26', '686450660ce20.pdf'),
(29, 'book6', '686450780515b.jpg', 8, '2', '2025-07-01 21:17:44', '686450780515e.pdf'),
(30, 'book7', '686450891a85e.jpg', 8, '4', '2025-07-01 21:18:01', '686450891a861.pdf'),
(31, 'book8', '6864509b4b970.jpg', 8, '4', '2025-07-01 21:18:19', '6864509b4b974.pdf'),
(32, 'book9', '686450d0bcffc.png', 8, '5', '2025-07-01 21:19:12', '686450d0bcfff.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `book_id`, `user_name`, `rating`, `comment`, `created_at`) VALUES
(20, 32, 'user1', 5, 'v.good', '2025-07-01 21:20:01'),
(21, 31, 'user1', 4, 'good', '2025-07-01 21:20:19'),
(22, 30, 'My Name is LAKKHAN', 5, 'AA JI OO JI LO JI SUNO JII 12 ka 4', '2025-07-01 21:21:07'),
(30, 29, 'user2', 5, 'good one', '2025-07-01 21:36:08'),
(31, 29, 'RAM', 5, 'Good', '2025-07-01 21:37:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `phone` int(11) NOT NULL,
  `address` text NOT NULL,
  `password` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `password`, `created_at`) VALUES
(13, 'user1', 'user1@example.com', 1234567890, 'xyz', '$2y$10$FGL45k1OP.QlP6vAPWTaZuyJRdUEV0brcpfcqBwg3.kedEmnZdTyC', '2025-07-01 21:11:22'),
(14, 'user2', 'user2@example.com', 1234567890, 'xyz', '$2y$10$sikcmFfjvNcrJpYbCRgti.eGgm3iFH9kcAGwrhuwzprVOjiYMwhTK', '2025-07-01 21:11:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id_fr` (`author_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `author_id_fr` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
