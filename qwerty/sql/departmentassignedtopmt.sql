-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2019 at 01:38 PM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hris2`
--

-- --------------------------------------------------------

--
-- Table structure for table `departmentassignedtopmt`
--

CREATE TABLE `departmentassignedtopmt` (
  `departmentAssignedToPMT_id` int(11) NOT NULL,
  `employees_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `departmentassignedtopmt`
--

INSERT INTO `departmentassignedtopmt` (`departmentAssignedToPMT_id`, `employees_id`, `department_id`) VALUES
(1, 1360, 1),
(2, 1708, 2),
(3, 1708, 4),
(4, 7, 3),
(5, 1292, 5),
(6, 1, 23),
(7, 1292, 6),
(8, 1, 7),
(9, 1360, 9),
(10, 1708, 8),
(11, 7, 10),
(12, 1360, 14),
(13, 1292, 12),
(14, 1292, 27),
(15, 1292, 28),
(16, 1292, 20),
(17, 1292, 16);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departmentassignedtopmt`
--
ALTER TABLE `departmentassignedtopmt`
  ADD PRIMARY KEY (`departmentAssignedToPMT_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departmentassignedtopmt`
--
ALTER TABLE `departmentassignedtopmt`
  MODIFY `departmentAssignedToPMT_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
