-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2025 at 08:40 PM
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
-- Database: `umat_src_poll`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Unique_No` varchar(15) NOT NULL,
  `Last_Name` text NOT NULL,
  `First_Name` text NOT NULL,
  `Middle_Name` text NOT NULL,
  `Status` text NOT NULL,
  `Email` varchar(225) NOT NULL,
  `Tel` int(10) NOT NULL,
  `Management` text NOT NULL,
  `Image` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Unique_No`, `Last_Name`, `First_Name`, `Middle_Name`, `Status`, `Email`, `Tel`, `Management`, `Image`) VALUES
('ad.srid.5528', 'Kuttor', 'Fred', 'Kofi', 'Dean', 'josephkuttor730@gmail.com', 2147483647, 'SRC', 'images/c4.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `jcr_candidate`
--

CREATE TABLE `jcr_candidate` (
  `Index_No` varchar(17) NOT NULL,
  `Full_Name` varchar(100) NOT NULL,
  `Reference_No` int(10) NOT NULL,
  `Post` text NOT NULL,
  `Image` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jcr_ec_statement`
--

CREATE TABLE `jcr_ec_statement` (
  `Title` text NOT NULL,
  `Statement` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jcr_feedback`
--

CREATE TABLE `jcr_feedback` (
  `Feedback` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jcr_financial_secretary`
--

CREATE TABLE `jcr_financial_secretary` (
  `Candidate` varchar(50) NOT NULL,
  `Votes` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jcr_general_secretary`
--

CREATE TABLE `jcr_general_secretary` (
  `Candidate` varchar(50) NOT NULL,
  `Votes` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jcr_nb`
--

CREATE TABLE `jcr_nb` (
  `SN` int(11) NOT NULL,
  `Message` varchar(500) NOT NULL,
  `File` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jcr_post`
--

CREATE TABLE `jcr_post` (
  `Post_id` int(11) NOT NULL,
  `Post` varchar(100) NOT NULL,
  `Type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jcr_post`
--

INSERT INTO `jcr_post` (`Post_id`, `Post`, `Type`) VALUES
(1, 'jcr_President', 'Multi-Voting'),
(2, 'jcr_General_Secretary', 'Multi-Voting'),
(3, 'jcr_Financial_Secretary', 'Multi-Voting'),
(4, 'jcr_Media_President', 'Referendum'),
(5, 'jcr_Womens_Commissioner', 'Referendum'),
(6, 'jcr_Entertainment_President', 'Multi-Voting');

-- --------------------------------------------------------

--
-- Table structure for table `jcr_president`
--

CREATE TABLE `jcr_president` (
  `Candidate` varchar(50) NOT NULL,
  `Votes` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jcr_sent_links`
--

CREATE TABLE `jcr_sent_links` (
  `Student_Email` text NOT NULL,
  `Link_sent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jcr_session`
--

CREATE TABLE `jcr_session` (
  `session` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jcr_session`
--

INSERT INTO `jcr_session` (`session`) VALUES
('stop');

-- --------------------------------------------------------

--
-- Table structure for table `jcr_votes`
--

CREATE TABLE `jcr_votes` (
  `Student_Email` text NOT NULL,
  `Unique_Code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `src_candidate`
--

CREATE TABLE `src_candidate` (
  `Index_No` varchar(12) NOT NULL,
  `Full_Name` varchar(100) NOT NULL,
  `Reference_No` int(10) NOT NULL,
  `Post` text NOT NULL,
  `Image` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `src_ec_statement`
--

CREATE TABLE `src_ec_statement` (
  `Title` text NOT NULL,
  `Statement` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `src_feedback`
--

CREATE TABLE `src_feedback` (
  `Feedback` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `src_nb`
--

CREATE TABLE `src_nb` (
  `SN` int(11) NOT NULL,
  `Message` varchar(500) NOT NULL,
  `File` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `src_post`
--

CREATE TABLE `src_post` (
  `Post_id` int(11) NOT NULL,
  `Post` varchar(100) NOT NULL,
  `Type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `src_post`
--

INSERT INTO `src_post` (`Post_id`, `Post`, `Type`) VALUES
(1, 'src_President', 'Multi-Voting'),
(2, 'src_General_Secretary', 'Multi-Voting'),
(3, 'src_NUGS_President', 'Multi-Voting'),
(4, 'src_Womens_Commissioner', 'Referendum'),
(8, 'src_Treasurer', 'Multi-Voting'),
(9, 'src_NUGS_Secretary', 'Multi-Voting'),
(10, 'src_NUGS_Treasurer', 'Multi-Voting');

-- --------------------------------------------------------

--
-- Table structure for table `src_sent_links`
--

CREATE TABLE `src_sent_links` (
  `Student_Email` text NOT NULL,
  `Link_sent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `src_session`
--

CREATE TABLE `src_session` (
  `session` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `src_session`
--

INSERT INTO `src_session` (`session`) VALUES
('stop');

-- --------------------------------------------------------

--
-- Table structure for table `src_votes`
--

CREATE TABLE `src_votes` (
  `Student_Email` text NOT NULL,
  `Unique_Code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

CREATE TABLE `voters` (
  `Index_No` varchar(17) NOT NULL,
  `Last_Name` varchar(50) NOT NULL,
  `Other_Name` varchar(50) NOT NULL,
  `Voters_Id` varchar(10) NOT NULL,
  `Student_Email` varchar(100) NOT NULL,
  `Programme` text NOT NULL,
  `Tel` int(14) NOT NULL,
  `Password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`Index_No`, `Last_Name`, `Other_Name`, `Voters_Id`, `Student_Email`, `Programme`, `Tel`, `Password`) VALUES
('.', '.', '.', '.', 'agbovimaxwellfosu@gmail.com', '.', 234, 'aa'),
('..', '.', '.', '..', 'ce-jkkuttor4923@st.umat.edu.gh', '.', 234, 'aa'),
('a', '.', '.', 'a', 'Ishaneeadom@gmail.com', '.', 234, 'aa'),
('b', '.', '.', 'b', 'Kalishanee@gmail.com', '.', 234, 'aa'),
('c', '.', '.', 'c', 'b88849844@gmail.com', '.', 234, 'aa'),
('d', '.', '.', 'd', 'amegbanuveronica21@gmail.com', '.', 234, 'aa'),
('e', '.', '.', 'e', 'afuaacquahgyan@gmail.com', '.', 234, 'aa'),
('f', '.', '.', 'f', 'dennisdacosta770@gmail.com', '.', 234, 'aa'),
('g', '.', '.', 'g', 'eugenedeku18@gmail.com', '.', 234, 'aa'),
('h', '.', '.', 'h', 'okyereenestina81@gmail.com', '.', 234, 'aa'),
('i', '.', '.', 'i', 'Leonardtwumasi777@gmail.com', '.', 234, 'aa'),
('j', '.', '.', 'j', 'ce-vaantepim0523@st.umat.edu.gh', '.', 234, 'aa'),
('jkl', 'Christable', 'Boateng', 'w2', 'boatemaaboatengchristable@gmail.com', 'CE', 234, 'aa'),
('k', '.', '.', 'k', 'Kellyenyonam54@gmail.com', '.', 234, 'aa'),
('kln', '', '', '', 'boatemaaboatengchristabel@gmail.com', '', 0, ''),
('l', '.', '.', 'l', 'koomsonefk1@gmail.com', '.', 234, 'aa'),
('m', '.', '.', 'm', 'ce-gttulasi8523@st.umat.edu.gh', '.', 234, 'aa'),
('n', '.', '.', 'n', 'tiekusamuel270@gmail.com', '.', 234, 'aa'),
('p', '.', '.', 'p', 'ce-lbuabassah2221@st.umat.edu.gh', '.', 234, 'aa'),
('SRI.41.008.113.23', 'KUTTOR', 'KOJO JOSEPH', 'SRID.4575', 'josephkuttor730@gmail.com', 'CE', 531363850, '0b09ae2d0760d3a7d59e7c180cdccd23'),
('SRI.41.008.119.23', 'KUTTOR', 'JOE', 'SRID.4239', 'ezzy2win@gmail.com', 'MC', 578907658, '21ad0bd836b90d08f4cf640b4c298e7c'),
('SRI.41.018.119.23', 'TOR', 'JOE', 'SRID.4444', 'wlord820@mail.com', 'MC', 578907656, '40fe9ad4949331a12f5f19b477133924');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Unique_No`),
  ADD UNIQUE KEY `Status` (`Status`) USING HASH;

--
-- Indexes for table `jcr_candidate`
--
ALTER TABLE `jcr_candidate`
  ADD PRIMARY KEY (`Index_No`),
  ADD UNIQUE KEY `Reference _No` (`Reference_No`),
  ADD UNIQUE KEY `Image` (`Image`);
ALTER TABLE `jcr_candidate` ADD FULLTEXT KEY `Index_No` (`Index_No`);
ALTER TABLE `jcr_candidate` ADD FULLTEXT KEY `Index_No_2` (`Index_No`);

--
-- Indexes for table `jcr_nb`
--
ALTER TABLE `jcr_nb`
  ADD PRIMARY KEY (`SN`);

--
-- Indexes for table `jcr_post`
--
ALTER TABLE `jcr_post`
  ADD UNIQUE KEY `Post_id` (`Post_id`);

--
-- Indexes for table `jcr_votes`
--
ALTER TABLE `jcr_votes`
  ADD UNIQUE KEY `Index_No` (`Unique_Code`) USING HASH;

--
-- Indexes for table `src_candidate`
--
ALTER TABLE `src_candidate`
  ADD PRIMARY KEY (`Index_No`),
  ADD UNIQUE KEY `Reference _No` (`Reference_No`),
  ADD UNIQUE KEY `Image` (`Image`);
ALTER TABLE `src_candidate` ADD FULLTEXT KEY `Index_No` (`Index_No`);
ALTER TABLE `src_candidate` ADD FULLTEXT KEY `Index_No_2` (`Index_No`);

--
-- Indexes for table `src_nb`
--
ALTER TABLE `src_nb`
  ADD PRIMARY KEY (`SN`);

--
-- Indexes for table `src_post`
--
ALTER TABLE `src_post`
  ADD UNIQUE KEY `Post_id` (`Post_id`);

--
-- Indexes for table `src_votes`
--
ALTER TABLE `src_votes`
  ADD UNIQUE KEY `Index_No` (`Unique_Code`) USING HASH;

--
-- Indexes for table `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`Index_No`),
  ADD UNIQUE KEY `Reference No.` (`Voters_Id`),
  ADD UNIQUE KEY `Email` (`Student_Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jcr_nb`
--
ALTER TABLE `jcr_nb`
  MODIFY `SN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jcr_post`
--
ALTER TABLE `jcr_post`
  MODIFY `Post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `src_nb`
--
ALTER TABLE `src_nb`
  MODIFY `SN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `src_post`
--
ALTER TABLE `src_post`
  MODIFY `Post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
