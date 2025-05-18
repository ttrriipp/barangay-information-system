<?php
require("../database.php");
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../pages/login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        http_response_code(500);
        echo "Database connection failed";
        exit();
    }
    
    try {
        // Get form data
        $id = intval($_POST['id']);
        $blotter_id = $_POST['blotter_id'];
        $incident_type = $_POST['incident_type'];
        $complainant_name = $_POST['complainant_name'];
        $complainant_address = $_POST['complainant_address'];
        $complainant_contact = $_POST['complainant_contact'] ?? null;
        $complainant_resident_id = !empty($_POST['complainant_resident_id']) ? $_POST['complainant_resident_id'] : null;
        
        $respondent_name = $_POST['respondent_name'];
        $respondent_address = $_POST['respondent_address'];
        $respondent_contact = $_POST['respondent_contact'] ?? null;
        $respondent_resident_id = !empty($_POST['respondent_resident_id']) ? $_POST['respondent_resident_id'] : null;
        
        $incident_date = $_POST['incident_date'];
        $incident_time = $_POST['incident_time'];
        $incident_location = $_POST['incident_location'];
        $incident_details = $_POST['incident_details'];
        $status = $_POST['status'];
        $action_taken = $_POST['action_taken'] ?? null;
        
        // Check if we need to record resolution details
        $resolution_details = null;
        $date_resolved = null;
        
        if ($status === 'Resolved' || $status === 'Dismissed') {
            $resolution_details = isset($_POST['resolution_details']) ? $_POST['resolution_details'] : null;
            $date_resolved = isset($_POST['date_resolved']) && !empty($_POST['date_resolved']) 
                ? $_POST['date_resolved'] 
                : date('Y-m-d H:i:s'); // Default to current time if not provided
        }
        
        // Update database
        $query = "UPDATE blotter_records SET 
            incident_type = ?, 
            complainant_name = ?, 
            complainant_address = ?, 
            complainant_contact = ?, 
            complainant_resident_id = ?,
            respondent_name = ?, 
            respondent_address = ?, 
            respondent_contact = ?, 
            respondent_resident_id = ?,
            incident_date = ?, 
            incident_time = ?, 
            incident_location = ?, 
            incident_details = ?, 
            status = ?, 
            action_taken = ?,
            resolution_details = ?,
            date_resolved = ?
            WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param(
            "ssssissssssssssssi", 
            $incident_type, 
            $complainant_name, 
            $complainant_address, 
            $complainant_contact, 
            $complainant_resident_id,
            $respondent_name, 
            $respondent_address, 
            $respondent_contact, 
            $respondent_resident_id,
            $incident_date, 
            $incident_time, 
            $incident_location, 
            $incident_details, 
            $status, 
            $action_taken,
            $resolution_details,
            $date_resolved,
            $id
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $stmt->close();
        mysqli_close($conn);
        
        $_SESSION['blotter_success'] = "Blotter record #" . $blotter_id . " has been updated successfully.";
        
        header("Location: ../pages/blotter.php");
        exit();
        
    } catch (Exception $e) {
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            mysqli_close($conn);
        }
        
        http_response_code(500);
        echo "Database Error: " . $e->getMessage();
        exit();
    }
} else {
    // If not a POST request, redirect to blotter page
    header("Location: ../pages/blotter.php");
    exit();
}
?> 