-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2024 at 08:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `study_sky`
--

-- --------------------------------------------------------

--
-- Table structure for table `pdf_submissions`
--

CREATE TABLE `pdf_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `category` enum('RFIA','Category2','Category3','Category4') DEFAULT NULL,
  `pdf_filename` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','accepted','unaccepted') DEFAULT 'pending',
  `admin_comment` text DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_changed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_comment_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pdf_submissions`
--

INSERT INTO `pdf_submissions` (`id`, `user_id`, `title`, `category`, `pdf_filename`, `comment`, `status`, `admin_comment`, `submission_date`, `status_changed_at`, `admin_comment_updated_at`) VALUES
(1, 2, 'Cyber Security', 'RFIA', '66b9d1c51d906_bank statment.pdf', '123', 'pending', NULL, '2024-08-10 14:52:12', '2024-08-12 09:11:33', '2024-08-12 09:11:33'),
(2, 2, 'Working Industry Experts', 'RFIA', '66b7a9f9b03f8_BCA.pdf', 'DO According to the', 'accepted', 'ok', '2024-08-10 17:57:13', '2024-08-10 14:41:49', '2024-08-10 14:41:49'),
(3, 2, 'Working Industry Experts', 'RFIA', '66b8cf0723dbc_BCA.pdf', '123', 'accepted', 'BA', '2024-08-11 14:47:35', '2024-08-12 03:26:05', '2024-08-12 03:26:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`) VALUES
(1, 'admin', '$2y$10$/O8u/zR1f41YDI7hr1rVqeF.MNvmSCzVm8J1aicoNtx1AgCn0TbPO', 'aryachaturvedi15@gmail.com', 'admin'),
(2, 'Shubham12', '$2y$10$ynvk3JtAcGdi5Z2jK.du9OwDm0T3FTCpUot0ZznxmXXns.omIEVYe', 'shubhamchoubey151999@gmail.com', 'user'),
(4, 'admin123', '$2y$10$SNd3HloU0aDly43RqPj.LuLWxPZeVD35eD8KSKHFkv6.666YZ1wfS', 'support@servix.in', 'user'),
(13, '12345', '$2y$10$TetXRpOvFv/YDlJOyYSU8u6/0Q.6x5HIRCH9oxXAZoCiehYBOfbyW', 'krvishwaroop.915@gmail.com', 'user'),
(18, 'admin123321', '$2y$10$8SNqw7db5SL4lLvImSlPBeSpYj8r4D5lNx6qMAdzjRVGl1maYVApy', '!@gmail.com', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pdf_submissions`
--
ALTER TABLE `pdf_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pdf_filename` (`pdf_filename`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pdf_submissions`
--
ALTER TABLE `pdf_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pdf_submissions`
--
ALTER TABLE `pdf_submissions`
  ADD CONSTRAINT `pdf_submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
