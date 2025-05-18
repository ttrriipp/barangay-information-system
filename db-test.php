<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('database.php');

echo "Starting database test...<br>";

$conn = getDatabaseConnection();

if (!$conn) {
    echo "Database connection failed!";
    exit;
}

echo "Database connection successful!<br>";

// Check if the blotter_records table exists and has the right structure
$query = "SHOW TABLES LIKE 'blotter_records'";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Error checking table: " . mysqli_error($conn);
    exit;
}

if (mysqli_num_rows($result) == 0) {
    echo "The blotter_records table does not exist!";
    exit;
}

echo "The blotter_records table exists.<br>";

// Check the structure of the blotter_records table
$query = "DESCRIBE blotter_records";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Error retrieving table structure: " . mysqli_error($conn);
    exit;
}

echo "Table structure:<br>";
echo "<pre>";
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
echo "</pre>";

// Try a simple insert
echo "Trying to insert a test record...<br>";

$blotter_id = "TEST-" . time();
$incident_type = "Test Incident";
$complainant_name = "Test Complainant";
$complainant_address = "Test Address";
$incident_date = date('Y-m-d');
$incident_time = date('H:i:s');
$incident_location = "Test Location";
$incident_details = "Test Details";
$respondent_name = "Test Respondent";
$respondent_address = "Test Address";
$status = "Pending";

$stmt = $conn->prepare("INSERT INTO blotter_records (
    blotter_id, incident_type, complainant_name, complainant_address, 
    respondent_name, respondent_address, incident_date, incident_time, 
    incident_location, incident_details, status
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param(
    "sssssssssss", 
    $blotter_id, $incident_type, $complainant_name, $complainant_address,
    $respondent_name, $respondent_address, $incident_date, $incident_time,
    $incident_location, $incident_details, $status
);

if ($stmt->execute()) {
    echo "Test record inserted successfully! ID: " . $stmt->insert_id;
    
    // Clean up the test record
    $stmt = $conn->prepare("DELETE FROM blotter_records WHERE blotter_id = ?");
    $stmt->bind_param("s", $blotter_id);
    $stmt->execute();
    echo "<br>Test record cleaned up.";
} else {
    echo "Insert failed: " . $stmt->error;
}

$stmt->close();
mysqli_close($conn);
?> 