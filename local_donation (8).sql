-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 07:10 AM
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

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `sp_insert_user`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_user` (IN `p_first_name` VARCHAR(100), IN `p_middle_name` VARCHAR(100), IN `p_last_name` VARCHAR(100), IN `p_date_of_birth` DATE, IN `p_gender` ENUM('Male','Female','Other'), IN `p_zip_code` VARCHAR(10), IN `p_phone_number` VARCHAR(20), IN `p_email` VARCHAR(255), IN `p_password` VARCHAR(255), IN `p_role` ENUM('User','Staff','Admin','Superuser'), IN `p_region_id` INT, IN `p_province_id` INT, IN `p_city_id` INT, IN `p_barangay_id` INT)   BEGIN
    INSERT INTO users (
        first_name, middle_name, last_name, date_of_birth, gender,
        zip_code, phone_number, email, password, role,
        region_id, province_id, city_id, barangay_id
    ) VALUES (
        p_first_name, p_middle_name, p_last_name, p_date_of_birth, p_gender,
        p_zip_code, p_phone_number, p_email, p_password, p_role,
        p_region_id, p_province_id, p_city_id, p_barangay_id
    );
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `display_text` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

DROP TABLE IF EXISTS `barangays`;
CREATE TABLE `barangays` (
  `id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `barangays`
--
DROP TRIGGER IF EXISTS `after_barangay_delete`;
DELIMITER $$
CREATE TRIGGER `after_barangay_delete` AFTER DELETE ON `barangays` FOR EACH ROW BEGIN
    -- Step 1: Set barangay_id to NULL for users in that barangay
    UPDATE users
    SET barangay_id = NULL
    WHERE barangay_id = OLD.id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `province_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `cities`
--
DROP TRIGGER IF EXISTS `after_city_delete`;
DELIMITER $$
CREATE TRIGGER `after_city_delete` AFTER DELETE ON `cities` FOR EACH ROW BEGIN
    -- Step 1: Set city_id to NULL for users in that city
    UPDATE users
    SET city_id = NULL
    WHERE city_id = OLD.id;

    -- Step 2: Set barangay_id to NULL for users in the deleted city
    UPDATE users
    SET barangay_id = NULL
    WHERE barangay_id IN (SELECT id FROM barangays WHERE city_id = OLD.id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `donation_entries`
--

DROP TABLE IF EXISTS `donation_entries`;
CREATE TABLE `donation_entries` (
  `entry_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `entry_type` enum('request','offer') NOT NULL,
  `details` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `target_area` enum('philippines','region','province','city','barangay') NOT NULL DEFAULT 'philippines'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `donation_entries`
--
DROP TRIGGER IF EXISTS `trg_donation_entries_after_delete`;
DELIMITER $$
CREATE TRIGGER `trg_donation_entries_after_delete` AFTER DELETE ON `donation_entries` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        user_id,
        profile_id,
        description
    ) VALUES (
        NULL,
        OLD.profile_id,
        CONCAT('Profile ID: ', OLD.profile_id, ' deleted a donation entry')
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_donation_entries_after_insert`;
DELIMITER $$
CREATE TRIGGER `trg_donation_entries_after_insert` AFTER INSERT ON `donation_entries` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        user_id,
        profile_id,
        description
    ) VALUES (
        NULL,
        NEW.profile_id,
        CONCAT('Profile ID: ', NEW.profile_id, ' added a new donation entry')
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_donation_entries_after_update`;
DELIMITER $$
CREATE TRIGGER `trg_donation_entries_after_update` AFTER UPDATE ON `donation_entries` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        user_id,
        profile_id,
        description
    ) VALUES (
        NULL,
        NEW.profile_id,
        CONCAT('Profile ID: ', NEW.profile_id, ' updated a donation entry')
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `donation_entry_items`
--

DROP TABLE IF EXISTS `donation_entry_items`;
CREATE TABLE `donation_entry_items` (
  `item_entry_id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation_logs`
--

DROP TABLE IF EXISTS `donation_logs`;
CREATE TABLE `donation_logs` (
  `log_id` int(11) NOT NULL,
  `donor_profile_id` int(11) NOT NULL,
  `recipient_profile_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_name` varchar(50) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `feedback` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_units`
--

DROP TABLE IF EXISTS `item_units`;
CREATE TABLE `item_units` (
  `item_unit_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `unit_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_admins`
--

DROP TABLE IF EXISTS `pending_admins`;
CREATE TABLE `pending_admins` (
  `pending_admin_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Staff') NOT NULL DEFAULT 'Staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_type` enum('individual','family','institution','organization') NOT NULL,
  `profile_name` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT 'images/profile_pic_placeholder1.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `profiles`
--
DROP TRIGGER IF EXISTS `after_profile_insert`;
DELIMITER $$
CREATE TRIGGER `after_profile_insert` AFTER INSERT ON `profiles` FOR EACH ROW BEGIN
    INSERT INTO activities (
        user_id,
        profile_id,
        description,
       display_text
    ) VALUES (
        NEW.user_id,
        NEW.profile_id,
        CONCAT(
            'New ', NEW.profile_type, ' profile created: ',
            NEW.profile_name, ' (Profile ID: ', NEW.profile_id, ')'
        ),
        CONCAT(
            'You created a new ', NEW.profile_type,
            ' profile: ', NEW.profile_name
        )
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_add_owner`;
DELIMITER $$
CREATE TRIGGER `trg_add_owner` AFTER INSERT ON `profiles` FOR EACH ROW BEGIN
    INSERT INTO profile_members (user_id, profile_id, role)
    VALUES (NEW.user_id, NEW.profile_id, 'owner');
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_profiles_after_delete`;
DELIMITER $$
CREATE TRIGGER `trg_profiles_after_delete` AFTER DELETE ON `profiles` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        user_id,
        profile_id,
        description
    ) VALUES (
        OLD.user_id,
        OLD.profile_id,
        CONCAT('Profile deleted: ', COALESCE(OLD.profile_id))
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_profiles_after_insert`;
DELIMITER $$
CREATE TRIGGER `trg_profiles_after_insert` AFTER INSERT ON `profiles` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        user_id,
        profile_id,
        description
    ) VALUES (
        NEW.user_id,
        NEW.profile_id,
        CONCAT('New profile created: ', COALESCE(NEW.profile_id))
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `profiles_family`
--

DROP TABLE IF EXISTS `profiles_family`;
CREATE TABLE `profiles_family` (
  `profile_id` int(11) NOT NULL,
  `household_name` varchar(255) NOT NULL,
  `primary_contact_person` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `barangay_id` int(11) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profiles_individual`
--

DROP TABLE IF EXISTS `profiles_individual`;
CREATE TABLE `profiles_individual` (
  `profile_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `barangay_id` int(11) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profiles_institution`
--

DROP TABLE IF EXISTS `profiles_institution`;
CREATE TABLE `profiles_institution` (
  `profile_id` int(11) NOT NULL,
  `institution_type` varchar(100) DEFAULT NULL,
  `institution_name` varchar(255) DEFAULT NULL,
  `official_contact_person` varchar(255) DEFAULT NULL,
  `official_contact_number` varchar(50) DEFAULT NULL,
  `official_email` varchar(255) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `barangay_id` int(11) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profiles_organization`
--

DROP TABLE IF EXISTS `profiles_organization`;
CREATE TABLE `profiles_organization` (
  `profile_id` int(11) NOT NULL,
  `organization_type` varchar(100) DEFAULT NULL,
  `organization_name` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `barangay_id` int(11) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile_members`
--

DROP TABLE IF EXISTS `profile_members`;
CREATE TABLE `profile_members` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `role` enum('owner','admin','manager','member','guest') NOT NULL DEFAULT 'guest',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `profile_members`
--
DROP TRIGGER IF EXISTS `after_profile_member_delete`;
DELIMITER $$
CREATE TRIGGER `after_profile_member_delete` AFTER DELETE ON `profile_members` FOR EACH ROW BEGIN
    INSERT INTO activities (user_id, profile_id, description, display_text)
    VALUES (
        null,
        OLD.profile_id,
        CONCAT(
            'User ID ', OLD.user_id,
            ' was removed from Profile ID ', OLD.profile_id,
            ' (Role was ', OLD.role, ')'
        ),
        'A member was removed from the profile'
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_profile_member_insert`;
DELIMITER $$
CREATE TRIGGER `after_profile_member_insert` AFTER INSERT ON `profile_members` FOR EACH ROW BEGIN
    INSERT INTO activities (user_id, profile_id, description, display_text)
    VALUES (
        null,
        NEW.profile_id,
        CONCAT(
            'User ID ', NEW.user_id,
            ' was added to Profile ID ', NEW.profile_id,
            ' as ', NEW.role
        ),
        CONCAT('A member was added with role: ', NEW.role)
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_profile_members_after_delete`;
DELIMITER $$
CREATE TRIGGER `trg_profile_members_after_delete` AFTER DELETE ON `profile_members` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        user_id,
        profile_id,
        description,
        created_at
    ) VALUES (
        OLD.user.id,
        OLD.profile_id,
        CONCAT('Member removed ', OLD.user_id , 'from profile: Profile ID ', OLD.profile_id)
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_profile_members_after_insert`;
DELIMITER $$
CREATE TRIGGER `trg_profile_members_after_insert` AFTER INSERT ON `profile_members` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        user_id,
        profile_id,
        description
    ) VALUES (
        NEW.user_id,
        NEW.profile_id,
        CONCAT('New member ', NEW.user_id , 'added to profile: Profile ID ', NEW.profile_id)
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

DROP TABLE IF EXISTS `provinces`;
CREATE TABLE `provinces` (
  `id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `provinces`
--
DROP TRIGGER IF EXISTS `after_province_delete`;
DELIMITER $$
CREATE TRIGGER `after_province_delete` AFTER DELETE ON `provinces` FOR EACH ROW BEGIN
    -- Step 1: Set province_id to NULL for users in that province
    UPDATE users
    SET province_id = NULL
    WHERE province_id = OLD.id;

    -- Step 2: Find related cities for the deleted province and set city_id to NULL for users in those cities
    UPDATE users
    SET city_id = NULL
    WHERE city_id IN (SELECT id FROM cities WHERE province_id = OLD.id);

    -- Step 3: Set barangay_id to NULL for users in the deleted cities
    UPDATE users
    SET barangay_id = NULL
    WHERE barangay_id IN (SELECT id FROM barangays WHERE city_id IN (SELECT id FROM cities WHERE province_id = OLD.id));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

DROP TABLE IF EXISTS `regions`;
CREATE TABLE `regions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `regions`
--
DROP TRIGGER IF EXISTS `after_region_delete`;
DELIMITER $$
CREATE TRIGGER `after_region_delete` AFTER DELETE ON `regions` FOR EACH ROW BEGIN
    -- Step 1: Set region_id to NULL for users in that region
    UPDATE users
    SET region_id = NULL
    WHERE region_id = OLD.id;
    
    -- Step 2: Find related provinces for the deleted region and set province_id to NULL for users in those provinces
    UPDATE users
    SET province_id = NULL
    WHERE province_id IN (SELECT id FROM provinces WHERE region_id = OLD.id);
    
    -- Step 3: Find related cities for the deleted provinces and set city_id to NULL for users in those cities
    UPDATE users
    SET city_id = NULL
    WHERE city_id IN (SELECT id FROM cities WHERE province_id IN (SELECT id FROM provinces WHERE region_id = OLD.id));
    
    -- Step 4: Set barangay_id to NULL for users in the deleted cities
    UPDATE users
    SET barangay_id = NULL
    WHERE barangay_id IN (SELECT id FROM barangays WHERE city_id IN (SELECT id FROM cities WHERE province_id IN (SELECT id FROM provinces WHERE region_id = OLD.id)));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('User','Staff','Admin','Superuser') DEFAULT 'User',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `region_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `barangay_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `users`
--
DROP TRIGGER IF EXISTS `after_user_delete`;
DELIMITER $$
CREATE TRIGGER `after_user_delete` AFTER DELETE ON `users` FOR EACH ROW BEGIN
    INSERT INTO activities (user_id, profile_id, description, display_text)
    VALUES (
        OLD.user_id,
        NULL,
        CONCAT('User deleted: ', OLD.first_name, ' ', OLD.last_name, ' (ID: ', OLD.user_id, ')'),
        CONCAT('Your account was deleted: ', OLD.first_name, ' ', OLD.last_name)
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_user_insert`;
DELIMITER $$
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    INSERT INTO activities (user_id, profile_id, description, display_text)
    VALUES (
        NEW.user_id,
        NULL,
        CONCAT('New user created: ', NEW.first_name, ' ', NEW.last_name, ' (ID: ', NEW.user_id, ')'),
        CONCAT('You created a new account: ', NEW.first_name, ' ', NEW.last_name)
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_user_update`;
DELIMITER $$
CREATE TRIGGER `after_user_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    -- Insert a record into the activities table
    INSERT INTO activities (
        user_id,
        profile_id,
        description,
        display_text
    )
    VALUES (
        NEW.user_id,
        NULL,
        CONCAT('User updated: ', NEW.first_name, ' ', NEW.last_name, ' (ID: ', NEW.user_id, ')'),
        CONCAT('Your account was updated: ', NEW.first_name, ' ', NEW.last_name)
    );

    -- Update the profile_name and profile_pic in the profiles table, only if profile_type is 'individual'
    UPDATE profiles
    SET
        profile_name = CONCAT(NEW.first_name, ' ', NEW.last_name),
        profile_pic = NEW.profile_pic
    WHERE user_id = NEW.user_id AND profile_type = 'individual';

    -- Update the profiles_individual table only for the 'individual' profile type and corresponding user_id
    UPDATE profiles_individual pi
    JOIN profiles p ON pi.profile_id = p.profile_id
    SET
        pi.first_name = NEW.first_name,
        pi.middle_name = NEW.middle_name,
        pi.last_name = NEW.last_name,
        pi.date_of_birth = NEW.date_of_birth,
        pi.gender = NEW.gender,
        pi.phone_number = NEW.phone_number,
        pi.email = NEW.email,
        pi.region_id = NEW.region_id,
        pi.province_id = NEW.province_id,
        pi.city_id = NEW.city_id,
        pi.barangay_id = NEW.barangay_id,
        pi.zip_code = NEW.zip_code
    WHERE p.user_id = NEW.user_id AND p.profile_type = 'individual';

END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_users_after_delete`;
DELIMITER $$
CREATE TRIGGER `trg_users_after_delete` AFTER DELETE ON `users` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        user_id,
        profile_id,
        description,
        created_at
    ) VALUES (
        OLD.user_id,
        NULL,
        CONCAT('User account deleted: ', COALESCE(OLD.first_name, ''), ' ', COALESCE(OLD.last_name, '')),
        NOW()
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_users_after_insert`;
DELIMITER $$
CREATE TRIGGER `trg_users_after_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        user_id,
        profile_id,
        description
    ) VALUES (
        NEW.user_id,
        NULL,
        CONCAT(
            'New user account created: ', 
            COALESCE(NEW.first_name, ''),
            ' ',
            COALESCE(NEW.last_name, '')
        )
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_users_after_update`;
DELIMITER $$
CREATE TRIGGER `trg_users_after_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        user_id,
        profile_id,
        description,
        created_at
    ) VALUES (
        NEW.user_id,
        NULL,
        CONCAT('User account updated: ', COALESCE(NEW.first_name, ''), ' ', COALESCE(NEW.last_name, '')),
        NOW()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_donation_entries`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `view_donation_entries`;
CREATE TABLE `view_donation_entries` (
`entry_id` int(11)
,`entry_type` enum('request','offer')
,`details` text
,`created_at` timestamp
,`target_area` enum('philippines','region','province','city','barangay')
,`profile_id` int(11)
,`profile_name` varchar(255)
,`profile_type` enum('individual','family','institution','organization')
,`region_id` int(11)
,`province_id` int(11)
,`city_id` int(11)
,`barangay_id` int(11)
,`region_name` varchar(50)
,`province_name` varchar(50)
,`city_name` varchar(50)
,`barangay_name` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_recent_feedback`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `view_recent_feedback`;
CREATE TABLE `view_recent_feedback` (
`feedback_id` int(11)
,`feedback` text
,`created_at` datetime
,`user_id` int(11)
,`profile_id` int(11)
,`first_name` varchar(100)
,`profile_name` varchar(255)
,`profile_type` enum('individual','family','institution','organization')
);

-- --------------------------------------------------------

--
-- Structure for view `view_donation_entries`
--
DROP TABLE IF EXISTS `view_donation_entries`;

DROP VIEW IF EXISTS `view_donation_entries`;
CREATE OR REPLACE VIEW `view_donation_entries`  AS SELECT `de`.`entry_id` AS `entry_id`, `de`.`entry_type` AS `entry_type`, `de`.`details` AS `details`, `de`.`created_at` AS `created_at`, `de`.`target_area` AS `target_area`, `p`.`profile_id` AS `profile_id`, `p`.`profile_name` AS `profile_name`, `p`.`profile_type` AS `profile_type`, coalesce(`pi`.`region_id`,`pf`.`region_id`,`pin`.`region_id`,`po`.`region_id`) AS `region_id`, coalesce(`pi`.`province_id`,`pf`.`province_id`,`pin`.`province_id`,`po`.`province_id`) AS `province_id`, coalesce(`pi`.`city_id`,`pf`.`city_id`,`pin`.`city_id`,`po`.`city_id`) AS `city_id`, coalesce(`pi`.`barangay_id`,`pf`.`barangay_id`,`pin`.`barangay_id`,`po`.`barangay_id`) AS `barangay_id`, `r`.`name` AS `region_name`, `pr`.`name` AS `province_name`, `c`.`name` AS `city_name`, `b`.`name` AS `barangay_name` FROM (((((((((`donation_entries` `de` join `profiles` `p` on(`de`.`profile_id` = `p`.`profile_id`)) left join `profiles_individual` `pi` on(`p`.`profile_id` = `pi`.`profile_id` and `p`.`profile_type` = 'individual')) left join `profiles_family` `pf` on(`p`.`profile_id` = `pf`.`profile_id` and `p`.`profile_type` = 'family')) left join `profiles_institution` `pin` on(`p`.`profile_id` = `pin`.`profile_id` and `p`.`profile_type` = 'institution')) left join `profiles_organization` `po` on(`p`.`profile_id` = `po`.`profile_id` and `p`.`profile_type` = 'organization')) left join `regions` `r` on(coalesce(`pi`.`region_id`,`pf`.`region_id`,`pin`.`region_id`,`po`.`region_id`) = `r`.`id`)) left join `provinces` `pr` on(coalesce(`pi`.`province_id`,`pf`.`province_id`,`pin`.`province_id`,`po`.`province_id`) = `pr`.`id`)) left join `cities` `c` on(coalesce(`pi`.`city_id`,`pf`.`city_id`,`pin`.`city_id`,`po`.`city_id`) = `c`.`id`)) left join `barangays` `b` on(coalesce(`pi`.`barangay_id`,`pf`.`barangay_id`,`pin`.`barangay_id`,`po`.`barangay_id`) = `b`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_recent_feedback`
--
DROP TABLE IF EXISTS `view_recent_feedback`;

DROP VIEW IF EXISTS `view_recent_feedback`;
CREATE OR REPLACE VIEW `view_recent_feedback`  AS SELECT `f`.`feedback_id` AS `feedback_id`, `f`.`feedback` AS `feedback`, `f`.`created_at` AS `created_at`, `f`.`user_id` AS `user_id`, `f`.`profile_id` AS `profile_id`, `u`.`first_name` AS `first_name`, `p`.`profile_name` AS `profile_name`, `p`.`profile_type` AS `profile_type` FROM ((`feedback` `f` left join `users` `u` on(`f`.`user_id` = `u`.`user_id`)) left join `profiles` `p` on(`f`.`profile_id` = `p`.`profile_id`)) ORDER BY `f`.`created_at` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_profile_id` (`profile_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_profile_id` (`profile_id`);

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_city_id` (`city_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_province_id` (`province_id`);

--
-- Indexes for table `donation_entries`
--
ALTER TABLE `donation_entries`
  ADD PRIMARY KEY (`entry_id`),
  ADD KEY `idx_profile_id` (`profile_id`);

--
-- Indexes for table `donation_entry_items`
--
ALTER TABLE `donation_entry_items`
  ADD PRIMARY KEY (`item_entry_id`),
  ADD KEY `fk_entry_items_item` (`item_id`),
  ADD KEY `idx_entry_id` (`entry_id`);

--
-- Indexes for table `donation_logs`
--
ALTER TABLE `donation_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_donor_profile` (`donor_profile_id`),
  ADD KEY `fk_recipient_profile` (`recipient_profile_id`),
  ADD KEY `fk_item` (`item_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_item_name` (`item_name`);

--
-- Indexes for table `item_units`
--
ALTER TABLE `item_units`
  ADD PRIMARY KEY (`item_unit_id`),
  ADD KEY `idx_item_id` (`item_id`);

--
-- Indexes for table `pending_admins`
--
ALTER TABLE `pending_admins`
  ADD PRIMARY KEY (`pending_admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profiles_family`
--
ALTER TABLE `profiles_family`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `idx_region_id` (`region_id`),
  ADD KEY `idx_province_id` (`province_id`),
  ADD KEY `idx_city_id` (`city_id`),
  ADD KEY `idx_barangay_id` (`barangay_id`);

--
-- Indexes for table `profiles_individual`
--
ALTER TABLE `profiles_individual`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `idx_region_id` (`region_id`),
  ADD KEY `idx_province_id` (`province_id`),
  ADD KEY `idx_city_id` (`city_id`),
  ADD KEY `idx_barangay_id` (`barangay_id`);

--
-- Indexes for table `profiles_institution`
--
ALTER TABLE `profiles_institution`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `idx_region_id` (`region_id`),
  ADD KEY `idx_province_id` (`province_id`),
  ADD KEY `idx_city_id` (`city_id`),
  ADD KEY `idx_barangay_id` (`barangay_id`);

--
-- Indexes for table `profiles_organization`
--
ALTER TABLE `profiles_organization`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `idx_region_id` (`region_id`),
  ADD KEY `idx_province_id` (`province_id`),
  ADD KEY `idx_city_id` (`city_id`),
  ADD KEY `idx_barangay_id` (`barangay_id`);

--
-- Indexes for table `profile_members`
--
ALTER TABLE `profile_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_profile_id` (`profile_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_region_id` (`region_id`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation_entries`
--
ALTER TABLE `donation_entries`
  MODIFY `entry_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation_entry_items`
--
ALTER TABLE `donation_entry_items`
  MODIFY `item_entry_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation_logs`
--
ALTER TABLE `donation_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_units`
--
ALTER TABLE `item_units`
  MODIFY `item_unit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_admins`
--
ALTER TABLE `pending_admins`
  MODIFY `pending_admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile_members`
--
ALTER TABLE `profile_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`) ON DELETE CASCADE;

--
-- Constraints for table `barangays`
--
ALTER TABLE `barangays`
  ADD CONSTRAINT `fk_city_id` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `fk_province_id` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donation_entries`
--
ALTER TABLE `donation_entries`
  ADD CONSTRAINT `fk_donation_entries_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`) ON DELETE CASCADE;

--
-- Constraints for table `donation_entry_items`
--
ALTER TABLE `donation_entry_items`
  ADD CONSTRAINT `fk_entry_items_entry` FOREIGN KEY (`entry_id`) REFERENCES `donation_entries` (`entry_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_entry_items_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `donation_logs`
--
ALTER TABLE `donation_logs`
  ADD CONSTRAINT `fk_donor_profile` FOREIGN KEY (`donor_profile_id`) REFERENCES `profiles` (`profile_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recipient_profile` FOREIGN KEY (`recipient_profile_id`) REFERENCES `profiles` (`profile_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`) ON DELETE CASCADE;

--
-- Constraints for table `item_units`
--
ALTER TABLE `item_units`
  ADD CONSTRAINT `item_units_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles_family`
--
ALTER TABLE `profiles_family`
  ADD CONSTRAINT `profiles_family_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles_individual`
--
ALTER TABLE `profiles_individual`
  ADD CONSTRAINT `fk_profiles_individual_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles_institution`
--
ALTER TABLE `profiles_institution`
  ADD CONSTRAINT `profiles_institution_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles_organization`
--
ALTER TABLE `profiles_organization`
  ADD CONSTRAINT `profiles_organization_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`) ON DELETE CASCADE;

--
-- Constraints for table `profile_members`
--
ALTER TABLE `profile_members`
  ADD CONSTRAINT `fk_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profile_members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `provinces`
--
ALTER TABLE `provinces`
  ADD CONSTRAINT `fk_region_id` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
