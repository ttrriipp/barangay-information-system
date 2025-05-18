-- Create Blotter Records table
CREATE TABLE `blotter_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `date_resolved` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complainant_resident_id` (`complainant_resident_id`),
  KEY `respondent_resident_id` (`respondent_resident_id`),
  CONSTRAINT `blotter_records_ibfk_1` FOREIGN KEY (`complainant_resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `blotter_records_ibfk_2` FOREIGN KEY (`respondent_resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data
INSERT INTO `blotter_records` 
(`blotter_id`, `incident_type`, `complainant_name`, `complainant_address`, `complainant_contact`, `incident_date`, `incident_time`, `incident_location`, `incident_details`, `respondent_name`, `respondent_address`, `respondent_contact`, `status`, `action_taken`) 
VALUES
('BLT-2023-001', 'Noise Complaint', 'Juan Dela Cruz', 'Purok 1, Barangay Example', '09123456789', '2023-07-15', '22:30:00', 'Purok 2, Barangay Example', 'Loud karaoke singing past quiet hours', 'Pedro Santos', 'Purok 2, Barangay Example', '09187654321', 'Resolved', 'Mediation conducted, respondent agreed to follow quiet hours'),
('BLT-2023-002', 'Property Dispute', 'Maria Garcia', 'Purok 3, Barangay Example', '09567891234', '2023-08-10', '09:15:00', 'Boundary of Purok 3 and 4', 'Dispute over land boundary and fence placement', 'Antonio Reyes', 'Purok 4, Barangay Example', '09891234567', 'Ongoing', 'Scheduled for barangay hearing'),
('BLT-2023-003', 'Physical Altercation', 'Roberto Magtanggol', 'Purok 5, Barangay Example', '09234567891', '2023-09-05', '18:45:00', 'Basketball Court, Barangay Example', 'Fight during basketball game resulting in minor injuries', 'Carlos Bautista', 'Purok 6, Barangay Example', '09765432198', 'Pending', 'Initial statements taken, investigation ongoing'); 