-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql303.iceiy.com
-- Generation Time: May 04, 2025 at 08:31 PM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `icei_38874221_pdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `full_name`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Grace123', '$2y$10$qT3Yd2KpidayacBilJwzjODTxxL10a9R6YiPRMVy5tFqwl/U.CXe.', 'Grace Dayupay, Lipit', NULL, '2025-05-02 01:25:45', '2025-05-02 01:25:45'),
(2, 'Jay123', '$2y$10$0e.YW.EUZ2QKCcMtLY6ivORxfiZu5yXiPFrWx4dJeXRLUCWgLmzHC', 'Jay Layam, Paclauna', '2025-05-04 08:41:44', '2025-05-02 01:26:01', '2025-05-04 08:41:44'),
(3, 'Jhon123', '$2y$10$YGs9x9nYct3YcfR0HK21seVQYAZyBJAg7ilSio4dXX5zbUkdXaO5m', 'Jhon Philip Del Rosario, Par', NULL, '2025-05-02 01:26:34', '2025-05-02 01:26:34'),
(4, 'Jeff123', '$2y$10$5GgTng8HONykNgVlmYwEEuVIIoKnKbdSIx.hVuMKFK0kfsd7Az4wS', 'Jeffrey Romero, Salabao', NULL, '2025-05-02 01:28:17', '2025-05-02 01:28:17'),
(5, 'Aldrin123', '$2y$10$UAkuJswRUV0924OSsyWVn.v5x19vdkzS16Yh3bIbOJL.Kf8lGy4vu', 'Aldrin Miaga, Arabia', '2025-05-04 23:59:45', '2025-05-02 01:28:49', '2025-05-04 23:59:45');

-- --------------------------------------------------------

--
-- Table structure for table `student_info`
--

CREATE TABLE `student_info` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_type VARCHAR(50) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    age INT NOT NULL,
    strand ENUM('ABM'. 'STEM') NOT NULL,
    city VARCHAR(100) NOT NULL,
    psa TINYINT(1) DEFAULT 0,         -- 1 for submitted, 0 for not
    form137 TINYINT(1) DEFAULT 0,
    good_moral TINYINT(1) DEFAULT 0,
    card TINYINT(1) DEFAULT 0,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


--
-- Dumping data for table `student_info`
--

INSERT INTO `student_info` (`id`, `student_type`, `full_name`, `gender`, `age`, `strand`, `city`, `psa`, `form137`, `good_moral`, `card`, `submitted_at`) VALUES
(1, 'transferee', 'Arabia, Aldrin Miaga.', 'Male', 21, 'STEM', 'Valenzuela City', 1, 0, 1, 0, '2025-05-04 02:34:24'),
(2, 'transferee', 'Akemi', 'Female', 20, 'ABM', 'Quezon City', 0, 1, 0, 1, '2025-05-04 02:36:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_info`
--
ALTER TABLE `student_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
