-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 03:40 AM
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
-- Database: `pdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `student_info`
--

CREATE TABLE `student_info` (
  `id` int(11) NOT NULL,
  `student_type` enum('new','transferee','returnee') NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `age` int(11) NOT NULL,
  `strand` enum('STEM','HUMSS','ABM','GAS','TVL','Arts') NOT NULL,
  `city` varchar(50) NOT NULL,
  `documents_submitted` enum('PSA','Form 137','Good Moral','Card') DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_info`
--

INSERT INTO `student_info` (`id`, `student_type`, `full_name`, `gender`, `age`, `strand`, `city`, `documents_submitted`, `submission_date`) VALUES
(1, 'transferee', 'sfdsfdsfsdfs', 'male', 22, 'STEM', 'xsvvd', 'PSA', '2025-05-02 01:13:12'),
(2, 'transferee', 'sfdsfdsfsdfs', 'male', 22, 'STEM', 'xsvvd', 'PSA', '2025-05-02 01:17:37'),
(3, 'transferee', 'sfdsfdsfsdfs', 'male', 22, 'STEM', 'xsvvd', 'PSA', '2025-05-02 01:19:15'),
(4, 'returnee', 'sfdfs', 'male', 22, 'STEM', 'xsvvd', 'Form 137', '2025-05-02 01:19:54'),
(5, 'transferee', 'wefsdfdfsf', 'male', 22, 'ABM', 'jghjhjghjj', 'PSA', '2025-05-02 01:34:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student_info`
--
ALTER TABLE `student_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `student_info`
--
ALTER TABLE `student_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
