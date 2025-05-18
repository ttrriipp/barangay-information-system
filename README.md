# Barangay Information System

A comprehensive web-based system for managing barangay (small administrative division) information including resident records, certificates, and blotter reports.

## Description

The Barangay Information System is designed to digitize and streamline the administrative processes in a barangay. It helps in managing resident information, issuing certificates, handling blotter reports, and generating analytics for better decision-making.

## Features

- **Dashboard**: Overview of key metrics and statistics about the barangay
- **Resident Management**: Add, view, edit, and delete resident records
- **Certificate Issuance**: Generate and track various certificates (Residency, Indigency, Clearance)
- **Blotter Records**: Record and manage incidents and disputes within the barangay
- **User Management**: Admin and regular user access levels
- **Reports**: Generate various reports based on resident demographics and other data

## Requirements

- XAMPP (with PHP 7.4+ and MySQL 5.7+)
- Web browser (Chrome, Firefox, Edge recommended)

## Installation

1. Install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start the Apache and MySQL services from the XAMPP Control Panel
3. Clone or download this repository into the `htdocs` folder of your XAMPP installation:
   ```
   C:\xampp\htdocs\barangay-information-system
   ```
4. Create the database and import sample data:
   - Open a web browser and navigate to `http://localhost/phpmyadmin`
   - Create a new database named `barangay_infomanage`
   - Import the `sample_data.sql` file from this repository into the newly created database

## Usage

1. Start Apache and MySQL from the XAMPP Control Panel
2. Open your web browser and navigate to `http://localhost/barangay-information-system`
3. Login using one of the following credentials:
   - Admin: 
     - Username: admin
     - Password: 123
   - Regular User: 
     - Username: user
     - Password: 123

## System Navigation

### Admin Access

- **Dashboard**: View overall statistics and demographics
- **Residents**: Manage resident information
- **Certificates**: Issue and track certificates
- **Blotters**: Record and manage incident reports
- **Reports**: Generate and export various reports

### User Access

- **User Dashboard**: View basic information
- **Request Certificates**: Submit certificate requests

## Database Structure

The system uses the following main tables:
- `residents`: Stores personal information about barangay residents
- `certificates`: Records certificates issued to residents
- `blotter_records`: Documents incident reports and their resolutions
- `users`: Manages system user accounts and access levels

## Customization

You can customize the system by:
- Editing database.php to match your database configuration
- Modifying the pages in the pages directory to change the UI
- Adding new features through the controllers directory

## Troubleshooting

- If you encounter a database connection error, check that:
  - MySQL is running in XAMPP
  - The database name in database.php matches your created database
  - Username and password in database.php match your MySQL credentials (default: username="root", password="")

- If pages don't load correctly, ensure:
  - Apache is running in XAMPP
  - The project is in the correct directory
  - File permissions are set correctly