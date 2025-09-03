-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2025 at 09:13 PM
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
-- Database: `chol`
--

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
(3, 1, 'accepted', 3, '2025-08-31 18:36:53'),
(4, 1, 'pending', 4, '2025-08-31 18:54:56');

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
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`user_id`,`friend_id`),
  ADD KEY `idx_friend` (`friend_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `fk_f_friend` FOREIGN KEY (`friend_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_f_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
