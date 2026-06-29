-- phpMyAdmin SQL Dump
-- COCOMO-based Software Effort Estimation
-- Updated: added `eaf` column to estimations table

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cocomo_db`
--

CREATE DATABASE IF NOT EXISTS `cocomo_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cocomo_db`;

-- --------------------------------------------------------

--
-- Table structure for `estimations`
--

DROP TABLE IF EXISTS `estimations`;
CREATE TABLE `estimations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(255) NOT NULL,
  `loc` int(11) NOT NULL,
  `project_type` enum('sederhana','menengah','sulit') NOT NULL,
  `RELY - Required Realibility` varchar(50) DEFAULT NULL,
  `DATA - Database Size` varchar(50) DEFAULT NULL,
  `CPLX - Product Complexity` varchar(50) DEFAULT NULL,
  `TIME - Execution Time Constraint` varchar(50) DEFAULT NULL,
  `STOR - Main Storage Constraint` varchar(50) DEFAULT NULL,
  `VIRT - Vrtual MAchine Volatility` varchar(50) DEFAULT NULL,
  `TURN - Computer Turnaround Time` varchar(50) DEFAULT NULL,
  `ACAP - Analyst Capability` varchar(50) DEFAULT NULL,
  `AEXP - Application Experience` varchar(50) DEFAULT NULL,
  `PCAP - Programmer Capability` varchar(50) DEFAULT NULL,
  `VEXP - Virtual Machine Experience` varchar(50) DEFAULT NULL,
  `LEXP - Programming Language Experience` varchar(50) DEFAULT NULL,
  `MODP - Modern Programming Prectice` varchar(50) DEFAULT NULL,
  `TOOL - Use of Software Tools` varchar(50) DEFAULT NULL,
  `SCED - Required Development Schedule` varchar(50) DEFAULT NULL,
  `eaf` double NOT NULL DEFAULT 1,
  `effort_pm` double NOT NULL,
  `tdev_months` double NOT NULL,
  `team_size` double NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for `kriteria`
--

DROP TABLE IF EXISTS `kriteria`;
CREATE TABLE `kriteria` (
  `id_kriteria` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kriteria` varchar(100) NOT NULL,
  PRIMARY KEY (`id_kriteria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data for table `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `nama_kriteria`) VALUES
(1,  'RELY - Required Realibility'),
(2,  'DATA - Database Size'),
(3,  'CPLX - Product Complexity'),
(4,  'TIME - Execution Time Constraint'),
(5,  'STOR - Main Storage Constraint'),
(6,  'VIRT - Vrtual MAchine Volatility'),
(7,  'TURN - Computer Turnaround Time'),
(8,  'ACAP - Analyst Capability'),
(9,  'AEXP - Application Experience'),
(10, 'PCAP - Programmer Capability'),
(11, 'VEXP - Virtual Machine Experience'),
(12, 'LEXP - Programming Language Experience'),
(13, 'MODP - Modern Programming Prectice'),
(14, 'TOOL - Use of Software Tools'),
(15, 'SCED - Required Development Schedule');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
