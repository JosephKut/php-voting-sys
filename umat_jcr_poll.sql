-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2025 at 12:07 PM
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
-- Database: `umat_jcr_poll`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidate`
--

CREATE TABLE `candidate` (
  `Index_No` varchar(17) NOT NULL,
  `Full_Name` varchar(100) NOT NULL,
  `Reference_No` int(10) NOT NULL,
  `Post` text NOT NULL,
  `Image` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate`
--

INSERT INTO `candidate` (`Index_No`, `Full_Name`, `Reference_No`, `Post`, `Image`) VALUES
('0111999', 'Red Kuttor', 244, 'President', 'uploads/1706195599028.jpg'),
('05130001', 'Fred Kuttor', 0, 'President', 'uploads/1706219777851.jpg'),
('SRI.41.008.135.23', 'Banabas Saint', 2147483647, 'General_Secretary', 'uploads/1000120921.jpg'),
('SRI.41.008.235.23', 'Solomon Quason', 538837547, 'General_Secretary', 'uploads/1000120925.jpg'),
('SRI.41.008.615.23', 'Micheal Ankomah', 45680, 'Financial_Secretary', 'uploads/1000120922.jpg'),
('SRI.41.008.617.23', 'Micheal Asamoah', 7945, 'Financial_Secretary', 'uploads/1000120923.jpg'),
('SRI.41.008.635.23', 'Mary Andor', 4567893, 'General_Secretary', 'uploads/1000120920.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `ec_statement`
--

CREATE TABLE `ec_statement` (
  `Title` text NOT NULL,
  `Statement` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `Feedback` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nb`
--

CREATE TABLE `nb` (
  `SN` int(11) NOT NULL,
  `Message` varchar(500) NOT NULL,
  `File` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nb`
--

INSERT INTO `nb` (`SN`, `Message`, `File`) VALUES
(1, 'asdfcv', 'uploads/'),
(2, '', 'uploads/FB_IMG_1721258107928.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `Post_id` int(11) NOT NULL,
  `Post` varchar(100) NOT NULL,
  `Type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`Post_id`, `Post`, `Type`) VALUES
(1, 'President', 'Multi-Voting'),
(2, 'General_Secretary', 'Multi-Voting'),
(3, 'Financial_Secretary', 'Multi-Voting'),
(4, 'Media_President', 'Referendum'),
(5, 'Womens_Commissioner', 'Referendum'),
(6, 'Entertainment_President', 'Multi-Voting');

-- --------------------------------------------------------

--
-- Table structure for table `sent_links`
--

CREATE TABLE `sent_links` (
  `Student_Email` text NOT NULL,
  `Link_sent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`session`) VALUES
('start');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `Student_Email` text NOT NULL,
  `Unique_Code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`Index_No`),
  ADD UNIQUE KEY `Reference _No` (`Reference_No`),
  ADD UNIQUE KEY `Image` (`Image`);
ALTER TABLE `candidate` ADD FULLTEXT KEY `Index_No` (`Index_No`);
ALTER TABLE `candidate` ADD FULLTEXT KEY `Index_No_2` (`Index_No`);

--
-- Indexes for table `nb`
--
ALTER TABLE `nb`
  ADD PRIMARY KEY (`SN`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD UNIQUE KEY `Post_id` (`Post_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD UNIQUE KEY `Index_No` (`Unique_Code`) USING HASH;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nb`
--
ALTER TABLE `nb`
  MODIFY `SN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `Post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
