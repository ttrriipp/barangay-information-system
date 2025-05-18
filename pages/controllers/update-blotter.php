<?php
session_start();
require("../../database.php");

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDatabaseConnection();
    
    // Get form data
    $id = intval($_POST['id']);
    $blotter_id = $_POST['blotter_id'];
    $incident_type = $_POST['incident_type'];
    $complainant_name = $_POST['complainant_name'];
    $complainant_address = $_POST['complainant_address'];
    $complainant_contact = $_POST['complainant_contact'];
    $complainant_resident_id = !empty($_POST['complainant_resident_id']) ? $_POST['complainant_resident_id'] : null;
    
    $respondent_name = $_POST['respondent_name'];
    $respondent_address = $_POST['respondent_address'];
    $respondent_contact = $_POST['respondent_contact'];
    $respondent_resident_id = !empty($_POST['respondent_resident_id']) ? $_POST['respondent_resident_id'] : null;
    
    $incident_date = $_POST['incident_date'];
    $incident_time = $_POST['incident_time'];
    $incident_location = $_POST['incident_location'];
    $incident_details = $_POST['incident_details'];
    $status = $_POST['status'];
    $action_taken = $_POST['action_taken'];
    $resolution_details = isset($_POST['resolution_details']) ? $_POST['resolution_details'] : null;
    
    // Handle date_resolved field
    $date_resolved = null;
    if ($status == 'Resolved' || $status == 'Dismissed') {
        if (isset($_POST['date_resolved']) && !empty($_POST['date_resolved'])) {
            $date_resolved = $_POST['date_resolved'];
        } else {
            $date_resolved = date('Y-m-d H:i:s');
        }
    }
    
    // Update the database
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
    
    if ($stmt->execute()) {
        $_SESSION['blotter_success'] = "Blotter record #" . $blotter_id . " has been updated successfully.";
        header("Location: ../blotter.php");
        exit();
    } else {
        // Return a proper error response with HTTP 500 status code
        http_response_code(500);
        echo "Database Error: " . $stmt->error;
        exit();
    }
    
    $stmt->close();
    mysqli_close($conn);
} else {
    // If not a POST request, redirect to blotter page
    header("Location: ../blotter.php");
    exit();
}
?> 