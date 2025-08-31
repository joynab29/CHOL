-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2025 at 08:17 AM
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
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(3, 'Faiza', 'Hossain', 'faifai', 'faifai27', 'faifai27@gmail.com', 2147483647, '2003-12-02');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_fk` (`user_id`),
  ADD KEY `friend_id_fk` (`friend_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`friend_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
