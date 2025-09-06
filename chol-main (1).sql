-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2025 at 09:09 PM
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
-- Database: `chol-main`
--

-- --------------------------------------------------------

--
-- Table structure for table `eventactivities`
--

CREATE TABLE `eventactivities` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `is_winner` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `finalized_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventwinners`
--

CREATE TABLE `eventwinners` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `chosen_by` int(11) NOT NULL,
  `chosen_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `status` enum('pending','accepted') NOT NULL DEFAULT 'pending',
  `requested_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`user_id`, `friend_id`, `status`, `requested_by`, `created_at`) VALUES
(1, 3, 'accepted', 3, '2025-08-31 18:43:22'),
(1, 4, 'accepted', 4, '2025-09-03 17:23:45'),
(3, 1, 'accepted', 3, '2025-08-31 18:36:53'),
(4, 1, 'accepted', 4, '2025-08-31 18:54:56');

-- --------------------------------------------------------

--
-- Table structure for table `groupmembers`
--

CREATE TABLE `groupmembers` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('owner','member') NOT NULL DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groupmembers`
--

INSERT INTO `groupmembers` (`group_id`, `user_id`, `role`, `joined_at`) VALUES
(1, 1, 'owner', '2025-09-04 12:06:30'),
(1, 3, 'member', '2025-09-04 13:34:04'),
(1, 4, 'member', '2025-09-04 13:34:06'),
(2, 1, 'owner', '2025-09-04 13:34:44'),
(2, 4, 'member', '2025-09-04 13:35:55'),
(3, 3, 'owner', '2025-09-06 15:39:20');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`, `created_by`, `created_at`) VALUES
(1, 'janina', NULL, 1, '2025-09-04 12:06:30'),
(2, 'amra', NULL, 1, '2025-09-04 13:34:44'),
(3, 'hello', NULL, 3, '2025-09-06 15:39:20');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(200) NOT NULL,
  `First_name` text NOT NULL,
  `Last_name` text NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `NID` int(100) NOT NULL,
  `Birthdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `First_name`, `Last_name`, `Username`, `Password`, `Email`, `NID`, `Birthdate`) VALUES
(1, 'Zareen', 'Rafa', 'raffa22', 'hello22', 'rafffa@gmail.com', 2147483647, '2003-01-06'),
(2, 'Maria', 'Islam', 'maria45', 'yess22', 'maria@gmail.com', 2147483647, '2003-01-15'),
(3, 'Faiza', 'Hossain', 'faifai', 'faifai27', 'faifai27@gmail.com', 2147483647, '2003-12-02'),
(4, 'Samia', 'Zaman', 'samsam', 'itmam', 'samia21@gmail.com', 2147483647, '2002-04-21');

-- --------------------------------------------------------

--
-- Stand-in structure for view `user table`
-- (See below for the actual view)
--
CREATE TABLE `user table` (
`id` int(200)
,`First_name` text
,`Last_name` text
,`Username` varchar(100)
,`Password` varchar(100)
,`Email` varchar(100)
,`NID` int(100)
,`Birthdate` date
);

-- --------------------------------------------------------

--
-- Structure for view `user table`
--
DROP TABLE IF EXISTS `user table`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user table`  AS SELECT `user`.`id` AS `id`, `user`.`First_name` AS `First_name`, `user`.`Last_name` AS `Last_name`, `user`.`Username` AS `Username`, `user`.`Password` AS `Password`, `user`.`Email` AS `Email`, `user`.`NID` AS `NID`, `user`.`Birthdate` AS `Birthdate` FROM `user` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `eventactivities`
--
ALTER TABLE `eventactivities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `eventwinners`
--
ALTER TABLE `eventwinners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `chosen_by` (`chosen_by`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`user_id`,`friend_id`),
  ADD KEY `idx_friend` (`friend_id`);

--
-- Indexes for table `groupmembers`
--
ALTER TABLE `groupmembers`
  ADD PRIMARY KEY (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eventactivities`
--
ALTER TABLE `eventactivities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventwinners`
--
ALTER TABLE `eventwinners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `eventactivities`
--
ALTER TABLE `eventactivities`
  ADD CONSTRAINT `eventactivities_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `eventwinners`
--
ALTER TABLE `eventwinners`
  ADD CONSTRAINT `eventwinners_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `eventwinners_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `eventactivities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `eventwinners_ibfk_3` FOREIGN KEY (`chosen_by`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `fk_f_friend` FOREIGN KEY (`friend_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_f_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `groupmembers`
--
ALTER TABLE `groupmembers`
  ADD CONSTRAINT `groupmembers_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `groupmembers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
