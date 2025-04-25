-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2025 at 06:06 AM
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
-- Database: `pdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `strands`
--

CREATE TABLE `strands` (
  `strand_id` int(11) NOT NULL,
  `name` enum('ABM','STEM') NOT NULL,
  `grade` enum('11','12') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strands`
--

INSERT INTO `strands` (`strand_id`, `name`, `grade`) VALUES
(1, 'ABM', '11'),
(3, 'ABM', '12'),
(2, 'STEM', '11'),
(4, 'STEM', '12');

-- --------------------------------------------------------

--
-- Table structure for table `student_info`
--

CREATE TABLE `student_info` (
  `student_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `strand_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_info`
--

INSERT INTO `student_info` (`student_id`, `first_name`, `last_name`, `birthdate`, `gender`, `address`, `contact_number`, `strand_id`) VALUES
(1, 'Juan', 'Dela Cruz', '2006-05-15', 'Male', '123 Manila St', '09123456789', 1),
(2, 'Maria', 'Santos', '2006-07-21', 'Female', '456 Quezon Ave', '09234567890', 1),
(3, 'Pedro', 'Reyes', '2006-02-10', 'Male', '789 Makati City', '09345678901', 1),
(4, 'Ana', 'Lopez', '2006-11-30', 'Female', '321 Taguig St', '09456789012', 2),
(5, 'Luis', 'Gonzales', '2006-09-25', 'Male', '654 Pasig Blvd', '09567890123', 2),
(6, 'Sofia', 'Martinez', '2006-04-18', 'Female', '987 Mandaluyong Rd', '09678901234', 2),
(7, 'Carlos', 'Torres', '2005-08-12', 'Male', '111 Paranaque Ln', '09789012345', 3),
(8, 'Isabel', 'Rivera', '2005-03-05', 'Female', '222 Las Pinas Ave', '09890123456', 3),
(9, 'Andres', 'Fernandez', '2005-12-19', 'Male', '333 Valenzuela St', '09901234567', 3),
(10, 'Elena', 'Ramirez', '2005-06-22', 'Female', '444 Marikina Way', '09112345678', 4),
(11, 'Javier', 'Ortiz', '2005-01-14', 'Male', '555 San Juan Dr', '09223456789', 4),
(12, 'Carmen', 'Vargas', '2005-10-08', 'Female', '666 Pasay Blvd', '09334567890', 4),
(13, 'Miguel', 'Castillo', '2006-03-17', 'Male', '777 Cainta Rd', '09445678901', 1),
(14, 'Andrea', 'Salazar', '2006-09-28', 'Female', '888 Antipolo St', '09556789012', 2),
(15, 'Ricardo', 'Mendoza', '2005-07-11', 'Male', '999 Taytay Ave', '09667890123', 3),
(16, 'Gabriela', 'Castro', '2005-04-03', 'Female', '101 Binangonan Ln', '09778901234', 4),
(17, 'Felipe', 'Aquino', '2006-12-09', 'Male', '202 Angono Way', '09889012345', 1),
(18, 'Beatriz', 'Bautista', '2006-08-24', 'Female', '303 Rodriguez Dr', '09990123456', 2),
(19, 'Alfredo', 'Villanueva', '2005-11-16', 'Male', '404 San Mateo St', '09101234567', 3),
(20, 'Rosario', 'Estrada', '2005-02-07', 'Female', '505 Montalban Ave', '09212345678', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `strands`
--
ALTER TABLE `strands`
  ADD PRIMARY KEY (`strand_id`),
  ADD UNIQUE KEY `unique_strand` (`name`,`grade`);

--
-- Indexes for table `student_info`
--
ALTER TABLE `student_info`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `strand_id` (`strand_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `strands`
--
ALTER TABLE `strands`
  MODIFY `strand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_info`
--
ALTER TABLE `student_info`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_info`
--
ALTER TABLE `student_info`
  ADD CONSTRAINT `student_info_ibfk_1` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
