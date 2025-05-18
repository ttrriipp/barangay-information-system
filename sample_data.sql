-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/

-- Create database
CREATE DATABASE IF NOT EXISTS `barangay_infomanage`;
USE `barangay_infomanage`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barangay_infomanage`
--

-- --------------------------------------------------------

--
-- Table structure for table `blotter_records`
--

CREATE TABLE `blotter_records` (
  `id` int(11) NOT NULL,
  `blotter_id` varchar(50) NOT NULL,
  `incident_type` varchar(100) NOT NULL,
  `complainant_name` varchar(100) NOT NULL,
  `complainant_address` text NOT NULL,
  `complainant_contact` varchar(20) DEFAULT NULL,
  `complainant_resident_id` int(11) DEFAULT NULL,
  `respondent_name` varchar(100) NOT NULL,
  `respondent_address` text NOT NULL,
  `respondent_contact` varchar(20) DEFAULT NULL,
  `respondent_resident_id` int(11) DEFAULT NULL,
  `incident_date` date NOT NULL,
  `incident_time` time NOT NULL,
  `incident_location` text NOT NULL,
  `incident_details` text NOT NULL,
  `status` enum('Pending','Ongoing','Resolved','Dismissed') NOT NULL DEFAULT 'Pending',
  `action_taken` text DEFAULT NULL,
  `resolution_details` text DEFAULT NULL,
  `date_reported` datetime NOT NULL DEFAULT current_timestamp(),
  `date_resolved` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blotter_records`
--

INSERT INTO `blotter_records` (`id`, `blotter_id`, `incident_type`, `complainant_name`, `complainant_address`, `complainant_contact`, `complainant_resident_id`, `respondent_name`, `respondent_address`, `respondent_contact`, `respondent_resident_id`, `incident_date`, `incident_time`, `incident_location`, `incident_details`, `status`, `action_taken`, `resolution_details`, `date_reported`, `date_resolved`) VALUES
(1, 'BLT-2025-001', 'Noise Complaint', 'Marites Chismosa', 'Block 5, Lot 12, Barangay Cupang West', '09123456789', 1, 'Bulalord Kamote', 'Block 5, Lot 14, Barangay Cupang West', '09187654321', 2, '2025-05-10', '22:30:00', 'Block 5, Lot 14, Barangay Cupang West', 'Respondent was having a loud party past 10PM disturbing the neighbors in the area.', 'Resolved', 'Both parties were advised on the barangay noise ordinance. Respondent agreed to keep noise at acceptable levels after 10PM.', 'The issue was resolved after a discussion with both parties. Respondent apologized and will comply with the ordinance.', '2025-05-11 09:30:00', '2025-05-13 10:15:00'),
(2, 'BLT-2025-002', 'Property Dispute', 'Brutus Tinola', 'Block 3, Lot 7, Barangay Cupang West', '09223344556', 3, 'Kikay Kalamansi', 'Block 3, Lot 8, Barangay Cupang West', '09889977665', 4, '2025-05-12', '15:00:00', 'Block 3, Boundary of Lots 7 and 8, Barangay Cupang West', 'Dispute over the fence boundary between two adjacent properties. Complainant claims respondent built fence 1 meter into his property.', 'Ongoing', 'Initial mediation conducted. Property documents reviewed. Awaiting surveyor\'s assessment.', NULL, '2025-05-12 16:45:00', NULL),
(3, 'BLT-2025-003', 'Physical Altercation', 'Tiktilaok Buritsawa', 'Block 2, Lot 4, Barangay Cupang West', '09556677889', 5, 'Bulbulito Ulampaya', 'Block 1, Lot 9, Barangay Cupang West', '09112233445', 6, '2025-05-14', '18:15:00', 'Barangay Basketball Court', 'Fight broke out during basketball game after a heated argument about a foul call.', 'Pending', 'Scheduled for mediation on May 20, 2025.', NULL, '2025-05-14 19:30:00', NULL),
(4, 'BLT-2025-004', 'Stray Animals', 'Donya Sinaing', 'Block 7, Lot 3, Barangay Cupang West', '09334455667', 7, 'Wakwakdaks Paksiw', 'Block 7, Lot 6, Barangay Cupang West', '09778899001', 8, '2025-05-15', '07:00:00', 'Block 7, Barangay Cupang West', 'Respondent\'s dogs consistently roam the neighborhood unleashed, damaging properties and frightening children.', 'Dismissed', 'After investigation, it was found that the dogs belonged to another resident who has already taken measures to secure their pets.', 'Case dismissed due to misidentification of pet owner.', '2025-05-15 10:00:00', '2025-05-16 14:30:00'),
(5, 'BLT-2025-005', 'Verbal Harassment', 'Luningning Balimbing', 'Block 9, Lot 10, Barangay Cupang West', '09667788990', 9, 'Kidlat Dinuguan', 'Block 9, Lot 11, Barangay Cupang West', '09001122334', 10, '2025-05-16', '16:45:00', 'Barangay Cupang West Public Market', 'Complainant alleges verbal harassment and threats from respondent over market stall positioning.', 'Ongoing', 'Initial statements taken from both parties and witnesses. Market supervisor also provided statement.', NULL, '2025-05-16 17:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `resident_name` varchar(255) NOT NULL,
  `certificate_type` varchar(50) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `issue_date` datetime NOT NULL,
  `issued_by` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `resident_name`, `certificate_type`, `purpose`, `issue_date`, `issued_by`) VALUES
(1, 'Marites Chismosa', 'Residency', 'Employment requirement', '2025-05-01 10:30:00', 'Kap. Utotnak Balahibo'),
(2, 'Bulalord Kamote', 'Indigency', 'Medical assistance application', '2025-05-02 11:15:00', 'Sec. Charing Tsismis'),
(3, 'Brutus Tinola', 'Clearance', 'Job application', '2025-05-03 09:45:00', 'Kap. Utotnak Balahibo'),
(4, 'Kikay Kalamansi', 'Residency', 'School enrollment', '2025-05-05 14:20:00', 'Sec. Charing Tsismis'),
(5, 'Tiktilaok Buritsawa', 'Clearance', 'Visa application', '2025-05-07 11:30:00', 'Kap. Utotnak Balahibo'),
(6, 'Bulbulito Ulampaya', 'Indigency', 'Educational assistance', '2025-05-08 10:00:00', 'Sec. Charing Tsismis'),
(7, 'Donya Sinaing', 'Clearance', 'Business permit application', '2025-05-10 15:45:00', 'Kap. Utotnak Balahibo'),
(8, 'Wakwakdaks Paksiw', 'Residency', 'Bank account opening', '2025-05-12 13:30:00', 'Sec. Charing Tsismis'),
(9, 'Luningning Balimbing', 'Indigency', 'Hospital requirement', '2025-05-14 09:15:00', 'Kap. Utotnak Balahibo'),
(10, 'Kidlat Dinuguan', 'Clearance', 'Police clearance requirement', '2025-05-15 11:00:00', 'Sec. Charing Tsismis');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `age` bigint(10) NOT NULL,
  `sex` enum('Male','Female','Others','') NOT NULL,
  `address` varchar(200) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `voter_status` varchar(20) DEFAULT NULL,
  `pwd_status` varchar(5) DEFAULT NULL,
  `philhealth_status` varchar(50) DEFAULT NULL,
  `4ps_status` varchar(5) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT 'Filipino',
  `date_of_residency` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `surname`, `firstname`, `middlename`, `birthdate`, `age`, `sex`, `address`, `contact`, `email`, `civil_status`, `occupation`, `education`, `voter_status`, `pwd_status`, `philhealth_status`, `4ps_status`, `emergency_contact_name`, `emergency_contact_number`, `blood_type`, `religion`, `nationality`, `date_of_residency`) VALUES
(1, 'Chismosa', 'Marites', 'Usyoso', '1985-06-15', 39, 'Female', 'Block 5, Lot 12, Barangay Cupang West', '09123456789', 'tsismosa.marites@email.com', 'Married', 'Teacher', 'College', 'Registered', 'No', 'Member', 'No', 'Kupal Chismosa', '09123456780', 'A+', 'Catholic', 'Filipino', '2010-03-15'),
(2, 'Kamote', 'Bulalord', 'Kwek-kwek', '1980-08-24', 44, 'Male', 'Block 5, Lot 14, Barangay Cupang West', '09187654321', 'bulalord.kamote@email.com', 'Married', 'Engineer', 'College', 'Registered', 'No', 'Member', 'No', 'Potpot Kamote', '09187654322', 'O+', 'Catholic', 'Filipino', '2008-07-20'),
(3, 'Tinola', 'Brutus', 'Prito', '1975-02-10', 50, 'Male', 'Block 3, Lot 7, Barangay Cupang West', '09223344556', 'brutus.tinola@email.com', 'Married', 'Businessman', 'College', 'Registered', 'No', 'Member', 'No', 'Kulasisi Tinola', '09223344557', 'B+', 'Catholic', 'Filipino', '2005-05-11'),
(4, 'Kalamansi', 'Kikay', 'Palabok', '1990-11-05', 34, 'Female', 'Block 3, Lot 8, Barangay Cupang West', '09889977665', 'kikay.kalamansi@email.com', 'Single', 'Accountant', 'College', 'Registered', 'No', 'Member', 'No', 'Tutubi Kalamansi', '09889977666', 'A-', 'Christian', 'Filipino', '2012-09-28'),
(5, 'Buritsawa', 'Tiktilaok', 'Ng', '1982-04-17', 43, 'Male', 'Block 2, Lot 4, Barangay Cupang West', '09556677889', 'tiktilaok.buritsawa@email.com', 'Married', 'IT Professional', 'College', 'Registered', 'No', 'Member', 'No', 'Burikak Buritsawa', '09556677880', 'AB+', 'Catholic', 'Filipino', '2011-11-30'),
(6, 'Ulampaya', 'Bulbulito', 'Tambay', '1995-09-22', 29, 'Male', 'Block 1, Lot 9, Barangay Cupang West', '09112233445', 'bulbulito.ulampaya@email.com', 'Single', 'Graphic Designer', 'College', 'Registered', 'No', 'Member', 'No', 'Kulangot Ulampaya', '09112233446', 'O-', 'Catholic', 'Filipino', '2018-04-15'),
(7, 'Sinaing', 'Donya', 'Hilaw', '1978-12-03', 46, 'Female', 'Block 7, Lot 3, Barangay Cupang West', '09334455667', 'donya.sinaing@email.com', 'Widowed', 'Nurse', 'College', 'Registered', 'No', 'Member', 'No', 'Diwata Sinaing', '09334455668', 'B-', 'Christian', 'Filipino', '2009-08-07'),
(8, 'Paksiw', 'Wakwakdaks', 'Pundido', '1987-03-28', 38, 'Male', 'Block 7, Lot 6, Barangay Cupang West', '09778899001', 'wakwakdaks.paksiw@email.com', 'Married', 'Construction Worker', 'High School', 'Registered', 'No', 'Dependent', 'Yes', 'Sampaguita Paksiw', '09778899002', 'O+', 'Catholic', 'Filipino', '2015-10-23'),
(9, 'Balimbing', 'Luningning', 'Balat', '1992-07-14', 32, 'Female', 'Block 9, Lot 10, Barangay Cupang West', '09667788990', 'luningning.balimbing@email.com', 'Single', 'Vendor', 'High School', 'Registered', 'No', 'Member', 'Yes', 'Pagong Balimbing', '09667788991', 'A+', 'Catholic', 'Filipino', '2014-02-18'),
(10, 'Dinuguan', 'Kidlat', 'Tsupitero', '1970-05-30', 54, 'Male', 'Block 9, Lot 11, Barangay Cupang West', '09001122334', 'kidlat.dinuguan@email.com', 'Married', 'Fisherman', 'Elementary', 'Registered', 'No', 'Member', 'Yes', 'Tsismosa Dinuguan', '09001122335', 'O+', 'Catholic', 'Filipino', '2001-12-05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$u8IIpBp3lVYLLG7Xm5G5NeXsgeij5/b/mYnw9y9T9Ru3pRmcaoA8.', 'admin'),
(2, 'user', '$2y$10$u8IIpBp3lVYLLG7Xm5G5NeXsgeij5/b/mYnw9y9T9Ru3pRmcaoA8.', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blotter_records`
--
ALTER TABLE `blotter_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complainant_resident_id` (`complainant_resident_id`),
  ADD KEY `respondent_resident_id` (`respondent_resident_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blotter_records`
--
ALTER TABLE `blotter_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blotter_records`
--
ALTER TABLE `blotter_records`
  ADD CONSTRAINT `blotter_records_ibfk_1` FOREIGN KEY (`complainant_resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `blotter_records_ibfk_2` FOREIGN KEY (`respondent_resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;