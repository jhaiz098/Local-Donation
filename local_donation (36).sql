-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 12:14 AM
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
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `user_id`, `profile_id`, `description`, `display_text`, `created_at`) VALUES
(1, 1, NULL, 'User logged in (ID: 1)', 'You logged in successfully.', '2025-12-16 04:10:08'),
(2, 1, 4, 'New individual profile created: James Emmanuel wewew Fernandez (Profile ID: 4)', 'You created a new individual profile: James Emmanuel wewew Fernandez', '2025-12-16 04:10:13'),
(3, NULL, 4, 'User ID 1 was added to Profile ID 4 as owner', 'A member was added with role: owner', '2025-12-16 04:10:13'),
(4, 2, NULL, 'Admin logged in (ID: 2, Role: Staff)', 'You logged in successfully.', '2025-12-16 04:11:25'),
(5, 5, NULL, 'Admin logged in (ID: 5, Role: Superuser)', 'You logged in successfully.', '2025-12-16 04:20:32'),
(9, 7, NULL, 'New user created: Carlos Reyes (ID: 7)', 'You created a new account: Carlos Reyes', '2025-12-16 06:37:18'),
(10, 7, NULL, 'New user registered (ID: 7, Email: carlos@gmail.com)', 'You have successfully registered.', '2025-12-16 06:37:18'),
(11, 8, NULL, 'New user created: Beatriz Santos (ID: 8)', 'You created a new account: Beatriz Santos', '2025-12-16 06:38:00'),
(12, 8, NULL, 'New user registered (ID: 8, Email: beatriz@gmail.com)', 'You have successfully registered.', '2025-12-16 06:38:00'),
(13, 7, NULL, 'User logged in (ID: 7)', 'You logged in successfully.', '2025-12-16 06:38:08'),
(14, 5, NULL, 'Admin logged in (ID: 5, Role: Superuser)', 'You logged in successfully.', '2025-12-16 06:38:30'),
(15, 7, NULL, 'User updated: Carlos Reyes (ID: 7)', 'Your account was updated: Carlos Reyes', '2025-12-16 06:39:05'),
(16, 9, NULL, 'New user created: Marco Villanueva (ID: 9)', 'You created a new account: Marco Villanueva', '2025-12-16 06:40:25'),
(17, 9, NULL, 'New user registered (ID: 9, Email: marco@gmail.com)', 'You have successfully registered.', '2025-12-16 06:40:25'),
(18, 10, NULL, 'New user created: Lucia Gomez (ID: 10)', 'You created a new account: Lucia Gomez', '2025-12-16 06:40:59'),
(19, 10, NULL, 'New user registered (ID: 10, Email: lucia@gmail.com)', 'You have successfully registered.', '2025-12-16 06:40:59'),
(20, 5, NULL, 'Admin logged in (ID: 5, Role: Superuser)', 'You logged in successfully.', '2025-12-16 06:41:19'),
(21, NULL, NULL, 'New staff registration submitted for approval: Andres Lim (andres@gmail.com)', 'Staff registration submitted for approval.', '2025-12-16 06:43:18'),
(22, 5, NULL, 'Admin logged in (ID: 5, Role: Superuser)', 'You logged in successfully.', '2025-12-16 06:43:28'),
(23, 7, NULL, 'User updated: Carlos Reyes (ID: 7)', 'Your account was updated: Carlos Reyes', '2025-12-16 06:44:05'),
(24, 8, NULL, 'User updated: Beatriz Santos (ID: 8)', 'Your account was updated: Beatriz Santos', '2025-12-16 06:44:21'),
(25, 9, NULL, 'User updated: Marco Villanueva (ID: 9)', 'Your account was updated: Marco Villanueva', '2025-12-16 06:44:33'),
(26, 10, NULL, 'User updated: Lucia Gomez (ID: 10)', 'Your account was updated: Lucia Gomez', '2025-12-16 06:44:39'),
(27, NULL, NULL, 'New staff registration submitted for approval: Renato Villanueva (renato@gmail.com)', 'Staff registration submitted for approval.', '2025-12-16 06:45:43'),
(28, 5, NULL, 'Admin logged in (ID: 5, Role: Superuser)', 'You logged in successfully.', '2025-12-16 06:48:30'),
(29, 2, NULL, 'User updated: James Emmanuel Fernandez (ID: 2)', 'Your account was updated: James Emmanuel Fernandez', '2025-12-16 06:54:27'),
(30, 2, NULL, 'Admin logged in (ID: 2, Role: Admin)', 'You logged in successfully.', '2025-12-16 06:54:44'),
(31, 7, NULL, 'User logged in (ID: 7)', 'You logged in successfully.', '2025-12-16 06:57:59'),
(32, 7, NULL, 'User updated: Carlos Reyes (ID: 7)', 'Your account was updated: Carlos Reyes', '2025-12-16 07:00:32'),
(33, 7, 5, 'New individual profile created: Carlos Reyes (Profile ID: 5)', 'You created a new individual profile: Carlos Reyes', '2025-12-16 07:01:17'),
(34, NULL, 5, 'User ID 7 was added to Profile ID 5 as owner', 'A member was added with role: owner', '2025-12-16 07:01:17');

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`activity_id`, `user_id`, `profile_id`, `description`, `created_at`) VALUES
(1, 1, 4, 'New member 1added to profile: Profile ID 4', '2025-12-16 04:10:13'),
(2, 1, 4, 'New profile created: 4', '2025-12-16 04:10:13'),
(3, NULL, 2, 'Profile ID: 2 deleted a donation entry', '2025-12-16 04:10:42'),
(4, NULL, 2, 'Profile ID: 2 deleted a donation entry', '2025-12-16 04:10:42'),
(5, NULL, 3, 'Profile ID: 3 deleted a donation entry', '2025-12-16 04:10:42'),
(6, NULL, 3, 'Profile ID: 3 deleted a donation entry', '2025-12-16 04:10:42'),
(7, 1, 2, 'Profile deleted: 2', '2025-12-16 04:14:07'),
(8, 1, 3, 'Profile deleted: 3', '2025-12-16 04:14:07'),
(12, 7, NULL, 'New user account created: Carlos Reyes', '2025-12-16 06:37:18'),
(13, 8, NULL, 'New user account created: Beatriz Santos', '2025-12-16 06:38:00'),
(14, 7, NULL, 'User account updated: Carlos Reyes', '2025-12-16 06:39:05'),
(15, 9, NULL, 'New user account created: Marco Villanueva', '2025-12-16 06:40:25'),
(16, 10, NULL, 'New user account created: Lucia Gomez', '2025-12-16 06:40:59'),
(17, 7, NULL, 'User account updated: Carlos Reyes', '2025-12-16 06:44:05'),
(18, 8, NULL, 'User account updated: Beatriz Santos', '2025-12-16 06:44:21'),
(19, 9, NULL, 'User account updated: Marco Villanueva', '2025-12-16 06:44:33'),
(20, 10, NULL, 'User account updated: Lucia Gomez', '2025-12-16 06:44:39'),
(21, 2, NULL, 'User account updated: James Emmanuel Fernandez', '2025-12-16 06:54:27'),
(22, 7, NULL, 'User account updated: Carlos Reyes', '2025-12-16 07:00:32'),
(23, 7, 5, 'New member 7added to profile: Profile ID 5', '2025-12-16 07:01:17'),
(24, 7, 5, 'New profile created: 5', '2025-12-16 07:01:17');

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
(60, 93, 'Zone 3'),
(61, 103, 'aa'),
(62, 106, 'San Jose'),
(63, 106, 'Concepcion'),
(64, 105, 'Bagumbayan'),
(65, 105, 'Logos'),
(66, 105, 'Balibago');

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
(102, 49, 'Maripipi'),
(103, 99, 'aaa'),
(105, 100, 'Santa Rosa'),
(106, 100, 'San Pablo'),
(107, 24, 'Tagaytay'),
(108, 24, 'Imus');

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`) VALUES
(21, 'aa asdas'),
(22, 'bae'),
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
-- Dumping data for table `pending_admins`
--

INSERT INTO `pending_admins` (`pending_admin_id`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Andres', 'Daniel', 'Lim', '1979-09-20', 'Male', 'andres@gmail.com', '$2y$10$u.2IbFIaYvSedlGFZjjMe.wiDsCQvLGKnrpGSN6K9/qYxoxdWayNK', 'Staff', '2025-12-15 22:43:18'),
(2, 'Renato', 'Santos', 'Villanueva', '1973-02-07', 'Male', 'renato@gmail.com', '$2y$10$pWKi9c/pCPT1eDKuaKmSPeTjwUjGsCSMvqKXIe7LDKFsBXcsbWd8q', 'Staff', '2025-12-15 22:45:43');

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`profile_id`, `user_id`, `profile_type`, `profile_name`, `profile_pic`, `created_at`, `updated_at`) VALUES
(4, 1, 'individual', 'James Emmanuel wewew Fernandez', 'uploads/1765486686_IMG_20231007_180326.JPG', '2025-12-15 20:10:13', '2025-12-15 20:10:13'),
(5, 7, 'individual', 'Carlos Reyes', 'uploads/profile_pic_placeholder1.png', '2025-12-15 23:01:17', '2025-12-15 23:01:17');

--
-- Dumping data for table `profiles_individual`
--

INSERT INTO `profiles_individual` (`profile_id`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone_number`, `email`, `region_id`, `province_id`, `city_id`, `barangay_id`, `zip_code`) VALUES
(4, 'James Emmanuel wewew', 'Palongpong', 'Fernandez', '2001-08-23', 'Male', '09310241773', 'james.fernandez1230@gmail.com', 10, 51, 24, 26, '6720'),
(5, 'Carlos', 'Miguel', 'Reyes', '1990-03-23', 'Male', '09123456789', 'carlos@gmail.com', 5, 100, 106, 62, '4000');

--
-- Dumping data for table `profile_members`
--

INSERT INTO `profile_members` (`id`, `user_id`, `profile_id`, `role`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'owner', '2025-12-15 20:10:13', '2025-12-15 20:10:13'),
(2, 7, 5, 'owner', '2025-12-15 23:01:17', '2025-12-15 23:01:17');

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
(98, 17, 'Tawi-Tawi'),
(99, 18, 'bbb'),
(100, 5, 'Laguna');

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
(17, 'BARMM - Bangsamoro Autonomous Region in Muslim Min'),
(18, 'aaa');

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `profile_pic`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `zip_code`, `phone_number`, `email`, `password`, `role`, `created_at`, `region_id`, `province_id`, `city_id`, `barangay_id`) VALUES
(1, 'uploads/1765486686_IMG_20231007_180326.JPG', 'James Emmanuel wewew', 'Palongpong', 'Fernandez', '2001-08-23', 'Male', '6720', '09310241773', 'james.fernandez1230@gmail.com', '$2y$10$360P2lsH9hZxXTmyuXpvTOcuCso/Sm9W.fRRj.ooDa7ULhRxKxbAe', 'User', '2025-12-11 04:06:17', 10, 51, 24, 26),
(2, 'uploads/1765497537_FB_IMG_1698376370669.jpg', 'James Emmanuel', 'Palongpong', 'Fernandez', '2001-08-23', 'Male', '6720', '09310241773', 'admin@gmail.com', '$2y$10$1CoCvkIS6Uu4tk3VXirkVuM/KytxCDjoYSptcWU1yGrQyRn1q40rC', 'Admin', '2025-12-11 04:06:36', 10, 53, 1, 1),
(3, NULL, 'James Emmanuel', 'Palongpong', 'Fernandez', '2001-08-23', 'Male', NULL, NULL, 'admin3@gmail.com', '$2y$10$/nPU6amabRJRc0/ep/rCXu.nVW9nCCdXTy9ukjVGSvy5gnnmWUpbe', 'User', '2025-12-11 04:07:57', NULL, NULL, NULL, NULL),
(4, NULL, 'James Emmanuel', 'Palongpong', 'Fernandez', '2003-01-02', 'Male', NULL, NULL, 'james.fernandez1231@gmail.com', '$2y$10$jZ6/I3SNgHP8Iup04orqgO/osAx3AakogieFxIFPtKfGLD5QOEfpi', 'User', '2025-12-11 15:05:43', NULL, NULL, NULL, NULL),
(5, NULL, 'James Emmanuel', 'Palongpong', 'Fernandez', '2001-08-23', 'Male', '6720', '09310241773', 'admin2@gmail.com', '$2y$10$CpfjPuR.Q3Ct.hDhjsnbGe7sl5ii7baGJda8iRDO29ENkaMulRgx6', 'Superuser', '2025-12-12 00:41:52', 10, 51, 24, 29),
(6, NULL, 'James Emmanuel', 'Palongpong', 'Fernandez', '2000-02-22', 'Male', NULL, NULL, 'james.fernandez1233@gmail.com', '$2y$10$5A1t9N/BdSSlNzPTN/wmLu3KhhUM9YcYhQYIk4xWrMTIKsLRhH9Bi', 'User', '2025-12-14 13:58:02', NULL, NULL, NULL, NULL),
(7, NULL, 'Carlos', 'Miguel', 'Reyes', '1990-03-23', 'Male', '4000', '09123456789', 'carlos@gmail.com', '$2y$10$Jv02Jwt81L8IyK/nJvlRc.eXJsSI6KOoDCBKZlbyhkLZA5zXIafy2', 'User', '2025-12-15 22:37:18', 5, 100, 106, 62),
(8, NULL, 'Beatriz', 'Lourdes', 'Santos', '2000-05-23', 'Female', '', '', 'beatriz@gmail.com', '$2y$10$icmeFC0iDChPT4WbRAaRXeutIo9VjRa3PnoCfLkJ24SifRnvazskK', 'User', '2025-12-15 22:38:00', 0, NULL, NULL, NULL),
(9, NULL, 'Marco', 'Renato', 'Villanueva', '1970-02-01', 'Male', '', '', 'marco@gmail.com', '$2y$10$RMbTkdCIEg7gjHGKHvxyv.Rzlj3ZoyqLt0yjPICiPQCVYbB1g0Zie', 'User', '2025-12-15 22:40:25', 0, NULL, NULL, NULL),
(10, NULL, 'Lucia', 'Felicia', 'Gomez', '1995-09-03', 'Female', '', '', 'lucia@gmail.com', '$2y$10$yglYhl4/FY0P7VJ358URIecQaHnzipvbfsUzw9f1zKBKzWNCEvLIa', 'User', '2025-12-15 22:40:59', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure for view `view_recent_feedback`
--
DROP TABLE IF EXISTS `view_recent_feedback`;

DROP VIEW IF EXISTS `view_recent_feedback`;
CREATE OR REPLACE VIEW `view_recent_feedback`  AS SELECT `f`.`feedback_id` AS `feedback_id`, `f`.`feedback` AS `feedback`, `f`.`created_at` AS `created_at`, `f`.`user_id` AS `user_id`, `f`.`profile_id` AS `profile_id`, `u`.`first_name` AS `first_name`, `p`.`profile_name` AS `profile_name`, `p`.`profile_type` AS `profile_type` FROM ((`feedback` `f` left join `users` `u` on(`f`.`user_id` = `u`.`user_id`)) left join `profiles` `p` on(`f`.`profile_id` = `p`.`profile_id`)) ORDER BY `f`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `vw_donation_entries`
--
DROP TABLE IF EXISTS `vw_donation_entries`;

DROP VIEW IF EXISTS `vw_donation_entries`;
CREATE OR REPLACE VIEW `vw_donation_entries`  AS SELECT `de`.`entry_id` AS `entry_id`, `de`.`entry_type` AS `entry_type`, `de`.`details` AS `details`, `de`.`target_area` AS `target_area`, `de`.`created_at` AS `created_at`, `de`.`profile_id` AS `profile_id`, `p`.`profile_name` AS `profile_name`, `p`.`profile_type` AS `profile_type`, `i`.`item_id` AS `item_id`, `i`.`item_name` AS `item_name`, `dei`.`quantity` AS `quantity`, `dei`.`unit_name` AS `unit_name`, coalesce(`ind`.`region_id`,`fam`.`region_id`,`inst`.`region_id`,`org`.`region_id`) AS `region_id`, coalesce(`ind`.`province_id`,`fam`.`province_id`,`inst`.`province_id`,`org`.`province_id`) AS `province_id`, coalesce(`ind`.`city_id`,`fam`.`city_id`,`inst`.`city_id`,`org`.`city_id`) AS `city_id`, coalesce(`ind`.`barangay_id`,`fam`.`barangay_id`,`inst`.`barangay_id`,`org`.`barangay_id`) AS `barangay_id`, `r`.`name` AS `region_name`, `pr`.`name` AS `province_name`, `c`.`name` AS `city_name`, `b`.`name` AS `barangay_name` FROM (((((((((((`donation_entries` `de` join `profiles` `p` on(`de`.`profile_id` = `p`.`profile_id`)) left join `donation_entry_items` `dei` on(`de`.`entry_id` = `dei`.`entry_id`)) left join `items` `i` on(`dei`.`item_id` = `i`.`item_id`)) left join `profiles_individual` `ind` on(`p`.`profile_id` = `ind`.`profile_id` and `p`.`profile_type` = 'individual')) left join `profiles_family` `fam` on(`p`.`profile_id` = `fam`.`profile_id` and `p`.`profile_type` = 'family')) left join `profiles_institution` `inst` on(`p`.`profile_id` = `inst`.`profile_id` and `p`.`profile_type` = 'institution')) left join `profiles_organization` `org` on(`p`.`profile_id` = `org`.`profile_id` and `p`.`profile_type` = 'organization')) left join `regions` `r` on(`r`.`id` = coalesce(`ind`.`region_id`,`fam`.`region_id`,`inst`.`region_id`,`org`.`region_id`))) left join `provinces` `pr` on(`pr`.`id` = coalesce(`ind`.`province_id`,`fam`.`province_id`,`inst`.`province_id`,`org`.`province_id`))) left join `cities` `c` on(`c`.`id` = coalesce(`ind`.`city_id`,`fam`.`city_id`,`inst`.`city_id`,`org`.`city_id`))) left join `barangays` `b` on(`b`.`id` = coalesce(`ind`.`barangay_id`,`fam`.`barangay_id`,`inst`.`barangay_id`,`org`.`barangay_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_profile_my_requests`
--
DROP TABLE IF EXISTS `vw_profile_my_requests`;

DROP VIEW IF EXISTS `vw_profile_my_requests`;
CREATE OR REPLACE VIEW `vw_profile_my_requests`  AS SELECT `de`.`entry_id` AS `entry_id`, `de`.`entry_type` AS `entry_type`, `de`.`details` AS `details`, `de`.`created_at` AS `created_at`, `de`.`target_area` AS `target_area`, `p`.`profile_id` AS `profile_id`, `p`.`profile_name` AS `profile_name`, `p`.`profile_type` AS `profile_type`, coalesce(`r`.`name`,'N/A') AS `region_name`, coalesce(`pr`.`name`,'N/A') AS `province_name`, coalesce(`c`.`name`,'N/A') AS `city_name`, coalesce(`b`.`name`,'N/A') AS `barangay_name` FROM (((((((((`donation_entries` `de` join `profiles` `p` on(`de`.`profile_id` = `p`.`profile_id`)) left join `profiles_individual` `pi` on(`p`.`profile_id` = `pi`.`profile_id`)) left join `profiles_family` `pf` on(`p`.`profile_id` = `pf`.`profile_id`)) left join `profiles_institution` `pin` on(`p`.`profile_id` = `pin`.`profile_id`)) left join `profiles_organization` `po` on(`p`.`profile_id` = `po`.`profile_id`)) left join `regions` `r` on(`r`.`id` = coalesce(`pi`.`region_id`,`pf`.`region_id`,`pin`.`region_id`,`po`.`region_id`))) left join `provinces` `pr` on(`pr`.`id` = coalesce(`pi`.`province_id`,`pf`.`province_id`,`pin`.`province_id`,`po`.`province_id`))) left join `cities` `c` on(`c`.`id` = coalesce(`pi`.`city_id`,`pf`.`city_id`,`pin`.`city_id`,`po`.`city_id`))) left join `barangays` `b` on(`b`.`id` = coalesce(`pi`.`barangay_id`,`pf`.`barangay_id`,`pin`.`barangay_id`,`po`.`barangay_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_users_with_location`
--
DROP TABLE IF EXISTS `vw_users_with_location`;

DROP VIEW IF EXISTS `vw_users_with_location`;
CREATE OR REPLACE VIEW `vw_users_with_location`  AS SELECT `u`.`user_id` AS `user_id`, `u`.`profile_pic` AS `profile_pic`, `u`.`first_name` AS `first_name`, `u`.`middle_name` AS `middle_name`, `u`.`last_name` AS `last_name`, `u`.`date_of_birth` AS `date_of_birth`, `u`.`gender` AS `gender`, `u`.`zip_code` AS `zip_code`, `u`.`phone_number` AS `phone_number`, `u`.`email` AS `email`, `u`.`role` AS `role`, `u`.`created_at` AS `created_at`, `u`.`region_id` AS `region_id`, `u`.`province_id` AS `province_id`, `u`.`city_id` AS `city_id`, `u`.`barangay_id` AS `barangay_id`, `get_region_name`(`u`.`region_id`) AS `region_name`, `get_province_name`(`u`.`province_id`) AS `province_name`, `get_city_name`(`u`.`city_id`) AS `city_name`, `get_barangay_name`(`u`.`barangay_id`) AS `barangay_name`, timestampdiff(YEAR,`u`.`date_of_birth`,curdate()) AS `age` FROM `users` AS `u` ;

-- --------------------------------------------------------

--
-- Structure for view `v_profile_activities`
--
DROP TABLE IF EXISTS `v_profile_activities`;

DROP VIEW IF EXISTS `v_profile_activities`;
CREATE OR REPLACE VIEW `v_profile_activities`  AS SELECT `a`.`activity_id` AS `activity_id`, `a`.`profile_id` AS `profile_id`, `a`.`description` AS `description`, `a`.`display_text` AS `display_text`, `a`.`created_at` AS `created_at`, `p`.`profile_type` AS `profile_type`, `p`.`profile_name` AS `profile_name`, `p`.`profile_pic` AS `profile_pic` FROM (`activities` `a` join `profiles` `p` on(`a`.`profile_id` = `p`.`profile_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_profile_dashboard`
--
DROP TABLE IF EXISTS `v_profile_dashboard`;

DROP VIEW IF EXISTS `v_profile_dashboard`;
CREATE OR REPLACE VIEW `v_profile_dashboard`  AS SELECT `p`.`profile_id` AS `profile_id`, `p`.`profile_type` AS `profile_type`, `p`.`profile_name` AS `profile_name`, `p`.`profile_pic` AS `profile_pic`, `i`.`first_name` AS `first_name`, `i`.`middle_name` AS `middle_name`, `i`.`last_name` AS `last_name`, `i`.`date_of_birth` AS `date_of_birth`, `i`.`gender` AS `gender`, `i`.`phone_number` AS `individual_phone`, `i`.`email` AS `individual_email`, `i`.`region_id` AS `individual_region_id`, `i`.`province_id` AS `individual_province_id`, `i`.`city_id` AS `individual_city_id`, `i`.`barangay_id` AS `individual_barangay_id`, `i`.`zip_code` AS `individual_zip_code`, `f`.`household_name` AS `household_name`, `f`.`primary_contact_person` AS `primary_contact_person`, `f`.`contact_number` AS `family_contact_number`, `f`.`email` AS `family_email`, `f`.`region_id` AS `family_region_id`, `f`.`province_id` AS `family_province_id`, `f`.`city_id` AS `family_city_id`, `f`.`barangay_id` AS `family_barangay_id`, `f`.`zip_code` AS `family_zip_code`, `ins`.`institution_name` AS `institution_name`, `ins`.`official_contact_person` AS `official_contact_person`, `ins`.`official_contact_number` AS `official_contact_number`, `ins`.`official_email` AS `official_email`, `ins`.`region_id` AS `institution_region_id`, `ins`.`province_id` AS `institution_province_id`, `ins`.`city_id` AS `institution_city_id`, `ins`.`barangay_id` AS `institution_barangay_id`, `ins`.`zip_code` AS `institution_zip_code`, `o`.`organization_name` AS `organization_name`, `o`.`contact_person` AS `org_contact_person`, `o`.`contact_number` AS `org_contact_number`, `o`.`email` AS `org_email`, `o`.`registration_number` AS `registration_number`, `o`.`region_id` AS `org_region_id`, `o`.`province_id` AS `org_province_id`, `o`.`city_id` AS `org_city_id`, `o`.`barangay_id` AS `org_barangay_id`, `o`.`zip_code` AS `org_zip_code` FROM ((((`profiles` `p` left join `profiles_individual` `i` on(`p`.`profile_id` = `i`.`profile_id` and `p`.`profile_type` = 'individual')) left join `profiles_family` `f` on(`p`.`profile_id` = `f`.`profile_id` and `p`.`profile_type` = 'family')) left join `profiles_institution` `ins` on(`p`.`profile_id` = `ins`.`profile_id` and `p`.`profile_type` = 'institution')) left join `profiles_organization` `o` on(`p`.`profile_id` = `o`.`profile_id` and `p`.`profile_type` = 'organization')) ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
