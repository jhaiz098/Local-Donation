-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 09:46 AM
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
-- Database: `local_donation`
--
CREATE DATABASE IF NOT EXISTS `local_donation` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `local_donation`;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `city_id`, `name`) VALUES
(1, 1, 'Palaypay'),
(2, 1, 'Can-abay'),
(3, 1, 'Baloog'),
(4, 1, 'Inuntan'),
(5, 1, 'Paghirang'),
(6, 3, 'Bagacay'),
(7, 3, 'Mercedes'),
(8, 3, 'Guindapunan'),
(9, 3, 'Silanga'),
(10, 3, 'San Andres'),
(11, 4, 'Rawis'),
(12, 4, 'Dagum'),
(13, 4, 'Hamorawon'),
(14, 4, 'Matobato'),
(15, 4, 'Gadgaran'),
(16, 22, 'Barangay 1'),
(17, 22, 'Barangay 9'),
(18, 22, 'Barangay 43-B'),
(19, 22, 'Barangay 74'),
(20, 22, 'San Jose'),
(21, 23, 'Linao'),
(22, 23, 'Cogon'),
(23, 23, 'Tambulilid'),
(24, 23, 'Dahilayan'),
(25, 23, 'Bagong Buhay'),
(26, 24, 'Arado'),
(27, 24, 'San Joaquin'),
(28, 24, 'San Isidro'),
(29, 24, 'Guindapunan'),
(30, 24, 'Salvacion'),
(31, 32, 'Balud'),
(32, 32, 'Maypangdan'),
(33, 32, 'Sabang'),
(34, 32, 'Siha'),
(35, 32, 'Locsoon'),
(36, 38, 'Abiera'),
(37, 38, 'Banahao'),
(38, 38, 'Barangay 1 Poblacion'),
(39, 38, 'Barangay 7 Poblacion'),
(40, 38, 'Cagusipan'),
(41, 57, 'Acacia'),
(42, 57, 'Bangkerohan'),
(43, 57, 'Dalakit'),
(44, 57, 'Molave'),
(45, 57, 'Baybay'),
(46, 60, 'Rawis'),
(47, 60, 'Bingco'),
(48, 60, 'Cabagngan'),
(49, 60, 'Calomotan'),
(50, 60, 'San Vicente'),
(51, 77, 'Abgao'),
(52, 77, 'Asuncion'),
(53, 77, 'Libertad'),
(54, 77, 'Mambajao'),
(55, 77, 'Tagnipa'),
(56, 93, 'Daha'),
(57, 93, 'San Roque'),
(58, 93, 'Santa Cruz'),
(59, 93, 'Zone 1'),
(60, 93, 'Zone 3');

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `province_id`, `name`) VALUES
(1, 53, 'Basey'),
(2, 53, 'Marabut'),
(3, 53, 'Catbalogan City'),
(4, 53, 'Calbayog City'),
(5, 53, 'Daram'),
(6, 53, 'Gandara'),
(7, 53, 'Hinabangan'),
(8, 53, 'Jiabong'),
(9, 53, 'Matuguinao'),
(10, 53, 'Motiong'),
(11, 53, 'Pinabacdao'),
(12, 53, 'San Jose de Buan'),
(13, 53, 'San Sebastian'),
(14, 53, 'Santa Margarita'),
(15, 53, 'Santa Rita'),
(16, 53, 'Santo Ni?o'),
(17, 53, 'Tagapul-an'),
(18, 53, 'Talalora'),
(19, 53, 'Tarangnan'),
(20, 53, 'Villareal'),
(21, 53, 'Zumarraga'),
(22, 51, 'Tacloban City'),
(23, 51, 'Ormoc City'),
(24, 51, 'Palo'),
(25, 51, 'Tanauan'),
(26, 51, 'Tolosa'),
(27, 51, 'Dulag'),
(28, 51, 'Burauen'),
(29, 51, 'Abuyog'),
(30, 51, 'Jaro'),
(31, 51, 'Carigara'),
(32, 50, 'Borongan City'),
(33, 50, 'Balangkayan'),
(34, 50, 'Can-avid'),
(35, 50, 'Dolores'),
(36, 50, 'General MacArthur'),
(37, 50, 'Giporlos'),
(38, 50, 'Guiuan'),
(39, 50, 'Hernani'),
(40, 50, 'Jipapad'),
(41, 50, 'Lawaan'),
(42, 50, 'Llorente'),
(43, 50, 'Maslog'),
(44, 50, 'Maydolong'),
(45, 50, 'Mercedes'),
(46, 50, 'Oras'),
(47, 50, 'Quinapondan'),
(48, 50, 'Salcedo'),
(49, 50, 'San Julian'),
(50, 50, 'San Policarpo'),
(51, 50, 'Sulat'),
(52, 50, 'Taft'),
(53, 52, 'Allen'),
(54, 52, 'Biri'),
(55, 52, 'Bobon'),
(56, 52, 'Capul'),
(57, 52, 'Catarman'),
(58, 52, 'Catubig'),
(59, 52, 'Gamay'),
(60, 52, 'Laoang'),
(61, 52, 'Lapinig'),
(62, 52, 'Las Navas'),
(63, 52, 'Lavezares'),
(64, 52, 'Lope de Vega'),
(65, 52, 'Mapanas'),
(66, 52, 'Mondragon'),
(67, 52, 'Palapag'),
(68, 52, 'Pambujan'),
(69, 52, 'Rosario'),
(70, 52, 'San Antonio'),
(71, 52, 'San Isidro'),
(72, 52, 'San Jose'),
(73, 52, 'San Roque'),
(74, 52, 'San Vicente'),
(75, 52, 'Silvino Lobos'),
(76, 52, 'Victoria'),
(77, 54, 'Maasin City'),
(78, 54, 'Anahawan'),
(79, 54, 'Bontoc'),
(80, 54, 'Hinunangan'),
(81, 54, 'Hinundayan'),
(82, 54, 'Libagon'),
(83, 54, 'Liloan'),
(84, 54, 'Macrohon'),
(85, 54, 'Malitbog'),
(86, 54, 'Padre Burgos'),
(87, 54, 'Pintuyan'),
(88, 54, 'Saint Bernard'),
(89, 54, 'San Francisco'),
(90, 54, 'San Juan'),
(91, 54, 'San Ricardo'),
(92, 54, 'Silago'),
(93, 54, 'Sogod'),
(94, 54, 'Tomas Oppus'),
(95, 49, 'Naval'),
(96, 49, 'Almeria'),
(97, 49, 'Biliran'),
(98, 49, 'Cabucgayan'),
(99, 49, 'Caibiran'),
(100, 49, 'Culaba'),
(101, 49, 'Kawayan'),
(102, 49, 'Maripipi');

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`) VALUES
(17, 'Batteries'),
(6, 'Blankets'),
(9, 'Bottled Water'),
(10, 'Canned Food'),
(7, 'Clothes'),
(13, 'Face Masks'),
(12, 'First Aid Kits'),
(16, 'Flashlights'),
(20, 'Fuel'),
(18, 'Generators'),
(11, 'Medical Supplies'),
(19, 'Radio'),
(14, 'Sanitizers'),
(8, 'Shoes'),
(15, 'Tents');

--
-- Dumping data for table `item_units`
--

INSERT INTO `item_units` (`item_unit_id`, `item_id`, `unit_name`) VALUES
(7, 6, 'pcs'),
(8, 6, 'pack'),
(9, 7, 'pcs'),
(10, 7, 'bundle'),
(11, 8, 'pairs'),
(12, 8, 'box'),
(13, 9, 'bottles'),
(14, 9, 'case'),
(15, 10, 'cans'),
(16, 10, 'box'),
(17, 11, 'boxes'),
(18, 11, 'pcs'),
(19, 12, 'kits'),
(20, 13, 'pcs'),
(21, 13, 'box'),
(22, 14, 'bottles'),
(23, 14, 'pack'),
(24, 15, 'pcs'),
(25, 16, 'pcs'),
(26, 17, 'pcs'),
(27, 17, 'pack'),
(28, 18, 'pcs'),
(29, 19, 'pcs'),
(30, 20, 'liters'),
(31, 20, 'gallons');

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `region_id`, `name`) VALUES
(1, 1, 'Abra'),
(2, 1, 'Apayao'),
(3, 1, 'Benguet'),
(4, 1, 'Ifugao'),
(5, 1, 'Kalinga'),
(6, 1, 'Mountain Province'),
(7, 2, 'Ilocos Norte'),
(8, 2, 'Ilocos Sur'),
(9, 2, 'La Union'),
(10, 2, 'Pangasinan'),
(11, 3, 'Batanes'),
(12, 3, 'Cagayan'),
(13, 3, 'Isabela'),
(14, 3, 'Nueva Vizcaya'),
(15, 3, 'Quirino'),
(16, 4, 'Aurora'),
(17, 4, 'Bataan'),
(18, 4, 'Bulacan'),
(19, 4, 'Nueva Ecija'),
(20, 4, 'Pampanga'),
(21, 4, 'Tarlac'),
(22, 4, 'Zambales'),
(23, 5, 'Batangas'),
(24, 5, 'Cavite'),
(25, 5, 'Laguna'),
(26, 5, 'Quezon'),
(27, 5, 'Rizal'),
(28, 6, 'Marinduque'),
(29, 6, 'Occidental Mindoro'),
(30, 6, 'Oriental Mindoro'),
(31, 6, 'Palawan'),
(32, 6, 'Romblon'),
(33, 7, 'Albay'),
(34, 7, 'Camarines Norte'),
(35, 7, 'Camarines Sur'),
(36, 7, 'Catanduanes'),
(37, 7, 'Masbate'),
(38, 7, 'Sorsogon'),
(39, 8, 'Aklan'),
(40, 8, 'Antique'),
(41, 8, 'Capiz'),
(42, 8, 'Guimaras'),
(43, 8, 'Iloilo'),
(44, 8, 'Negros Occidental'),
(45, 9, 'Bohol'),
(46, 9, 'Cebu'),
(47, 9, 'Negros Oriental'),
(48, 9, 'Siquijor'),
(49, 10, 'Biliran'),
(50, 10, 'Eastern Samar'),
(51, 10, 'Leyte'),
(52, 10, 'Northern Samar'),
(53, 10, 'Samar'),
(54, 10, 'Southern Leyte'),
(55, 11, 'Manila'),
(56, 11, 'Quezon City'),
(57, 11, 'Makati'),
(58, 11, 'Pasay'),
(59, 11, 'Pasig'),
(60, 11, 'Taguig'),
(61, 11, 'Caloocan'),
(62, 11, 'Las Pi?as'),
(63, 11, 'Muntinlupa'),
(64, 11, 'Navotas'),
(65, 11, 'Malabon'),
(66, 11, 'Mandaluyong'),
(67, 11, 'Marikina'),
(68, 11, 'Paranaque'),
(69, 11, 'San Juan'),
(70, 11, 'Valenzuela'),
(71, 11, 'Pateros'),
(72, 12, 'Zamboanga del Norte'),
(73, 12, 'Zamboanga del Sur'),
(74, 12, 'Zamboanga Sibugay'),
(75, 13, 'Bukidnon'),
(76, 13, 'Camiguin'),
(77, 13, 'Lanao del Norte'),
(78, 13, 'Misamis Occidental'),
(79, 13, 'Misamis Oriental'),
(80, 14, 'Davao de Oro'),
(81, 14, 'Davao del Norte'),
(82, 14, 'Davao del Sur'),
(83, 14, 'Davao Occidental'),
(84, 14, 'Davao Oriental'),
(85, 15, 'Cotabato'),
(86, 15, 'Sarangani'),
(87, 15, 'South Cotabato'),
(88, 15, 'Sultan Kudarat'),
(89, 16, 'Agusan del Norte'),
(90, 16, 'Agusan del Sur'),
(91, 16, 'Dinagat Islands'),
(92, 16, 'Surigao del Norte'),
(93, 16, 'Surigao del Sur'),
(94, 17, 'Basilan'),
(95, 17, 'Lanao del Sur'),
(96, 17, 'Maguindanao'),
(97, 17, 'Sulu'),
(98, 17, 'Tawi-Tawi');

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `name`) VALUES
(1, 'CAR - Cordillera Administrative Region'),
(2, 'Region I - Ilocos Region'),
(3, 'Region II - Cagayan Valley'),
(4, 'Region III - Central Luzon'),
(5, 'Region IV-A - CALABARZON'),
(6, 'Region IV-B - MIMAROPA'),
(7, 'Region V - Bicol Region'),
(8, 'Region VI - Western Visayas'),
(9, 'Region VII - Central Visayas'),
(10, 'Region VIII - Eastern Visayas'),
(11, 'NCR - National Capital Region'),
(12, 'Region IX - Zamboanga Peninsula'),
(13, 'Region X - Northern Mindanao'),
(14, 'Region XI - Davao Region'),
(15, 'Region XII - SOCCSKSARGEN'),
(16, 'Region XIII - Caraga'),
(17, 'BARMM - Bangsamoro Autonomous Region in Muslim Min');

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `profile_pic`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `zip_code`, `phone_number`, `email`, `password`, `role`, `created_at`, `region_id`, `province_id`, `city_id`, `barangay_id`) VALUES
(1, 'uploads/1765486686_IMG_20231007_180326.JPG', 'James Emmanuel wewew', 'Palongpong', 'Fernandez', '2001-08-23', 'Male', '6720', '09310241773', 'james.fernandez1230@gmail.com', '$2y$10$360P2lsH9hZxXTmyuXpvTOcuCso/Sm9W.fRRj.ooDa7ULhRxKxbAe', 'User', '2025-12-11 12:06:17', 10, 51, 24, 26),
(2, 'uploads/1765497537_FB_IMG_1698376370669.jpg', 'James Emmanuel', 'Palongpong', 'Fernandez', '2001-08-23', 'Male', '6720', '09310241773', 'admin@gmail.com', '$2y$10$GsorrmO4rp3rhgRQMvH68OGZKLs4mKfxxdbJAHrDBDJbX8/K1MZDm', 'Superuser', '2025-12-11 12:06:36', NULL, NULL, NULL, NULL),
(3, NULL, 'James Emmanuel', 'Palongpong', 'Fernandez', '2001-08-23', 'Male', NULL, NULL, 'admin3@gmail.com', '$2y$10$/nPU6amabRJRc0/ep/rCXu.nVW9nCCdXTy9ukjVGSvy5gnnmWUpbe', 'User', '2025-12-11 12:07:57', NULL, NULL, NULL, NULL),
(4, NULL, 'James Emmanuel', 'Palongpong', 'Fernandez', '2003-01-02', 'Male', NULL, NULL, 'james.fernandez1231@gmail.com', '$2y$10$jZ6/I3SNgHP8Iup04orqgO/osAx3AakogieFxIFPtKfGLD5QOEfpi', 'User', '2025-12-11 23:05:43', NULL, NULL, NULL, NULL),
(5, NULL, 'James Emmanuel', 'Palongpong', 'Fernandez', '2001-08-23', 'Male', NULL, NULL, 'admin2@gmail.com', '$2y$10$ZM4uC30Djbz09gINdCBWd.f8vtttgETyjNv8Q0s7V1WJuSFttAIYe', 'Staff', '2025-12-12 08:41:52', NULL, NULL, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
