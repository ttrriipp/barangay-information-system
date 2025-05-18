-- Update residents table with new fields
ALTER TABLE residents
    ADD COLUMN birthdate DATE NULL AFTER middlename,
    ADD COLUMN email VARCHAR(100) NULL AFTER contact,
    ADD COLUMN civil_status VARCHAR(20) NULL AFTER email,
    ADD COLUMN occupation VARCHAR(100) NULL AFTER civil_status,
    ADD COLUMN education VARCHAR(100) NULL AFTER occupation,
    ADD COLUMN voter_status VARCHAR(20) NULL AFTER education,
    ADD COLUMN pwd_status VARCHAR(5) NULL AFTER voter_status,
    ADD COLUMN philhealth_status VARCHAR(50) NULL AFTER pwd_status,
    ADD COLUMN `4ps_status` VARCHAR(5) NULL AFTER philhealth_status,
    ADD COLUMN emergency_contact_name VARCHAR(255) NULL AFTER `4ps_status`,
    ADD COLUMN emergency_contact_number VARCHAR(20) NULL AFTER emergency_contact_name,
    ADD COLUMN blood_type VARCHAR(5) NULL AFTER emergency_contact_number,
    ADD COLUMN religion VARCHAR(100) NULL AFTER blood_type,
    ADD COLUMN nationality VARCHAR(50) DEFAULT 'Filipino' AFTER religion,
    ADD COLUMN date_of_residency DATE NULL AFTER nationality,
    MODIFY COLUMN contact VARCHAR(20) NULL,
    MODIFY COLUMN middlename VARCHAR(100) NULL; 