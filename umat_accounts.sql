-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2025 at 03:12 PM
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
-- Database: `umat_accounts`
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
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `SN` int(11) NOT NULL,
  `Name` varchar(225) NOT NULL,
  `Abbreviation` varchar(15) NOT NULL,
  `Course` text NOT NULL,
  `Logo` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`SN`, `Name`, `Abbreviation`, `Course`, `Logo`) VALUES
(6, 'bdnmla', 'MESA', 'MC', 'images/u4.jpeg'),
(7, 'ACSES', 'ACSES', 'CE', 'images/u4.jpeg');

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
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`SN`);

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
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `SN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
