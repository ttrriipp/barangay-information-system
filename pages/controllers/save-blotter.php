<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require("../../database.php");

// Log the start of the script
file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Starting save-blotter.php\n", FILE_APPEND);

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Not authenticated\n", FILE_APPEND);
    header("Location: ../login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - POST request received\n", FILE_APPEND);
    
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Database connection failed\n", FILE_APPEND);
        http_response_code(500);
        echo "Database connection failed";
        exit();
    }
    
    file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Connected to database\n", FILE_APPEND);
    
    try {
        // Generate blotter ID (format: BLT-YYYY-XXX)
        $year = date('Y');
        $query = "SELECT MAX(CAST(SUBSTRING_INDEX(blotter_id, '-', -1) AS UNSIGNED)) as last_id 
                FROM blotter_records 
                WHERE blotter_id LIKE 'BLT-$year-%'";
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Failed to query for last ID: " . mysqli_error($conn) . "\n", FILE_APPEND);
            throw new Exception("Failed to generate blotter ID: " . mysqli_error($conn));
        }
        
        $row = mysqli_fetch_assoc($result);
        $last_id = $row['last_id'] ?? 0;
        $new_id = $last_id + 1;
        $blotter_id = "BLT-$year-" . str_pad($new_id, 3, '0', STR_PAD_LEFT);
        
        file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Generated blotter ID: $blotter_id\n", FILE_APPEND);
        
        // Log POST data
        file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);
        
        // Get form data
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
        
        // Insert into database
        $query = "INSERT INTO blotter_records (
            blotter_id, incident_type, complainant_name, complainant_address, complainant_contact, complainant_resident_id,
            respondent_name, respondent_address, respondent_contact, respondent_resident_id,
            incident_date, incident_time, incident_location, incident_details, status, action_taken
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Query: $query\n", FILE_APPEND);
        
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Prepare failed: " . $conn->error . "\n", FILE_APPEND);
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param(
            "sssssisssissssss", 
            $blotter_id, $incident_type, $complainant_name, $complainant_address, $complainant_contact, $complainant_resident_id,
            $respondent_name, $respondent_address, $respondent_contact, $respondent_resident_id,
            $incident_date, $incident_time, $incident_location, $incident_details, $status, $action_taken
        );
        
        file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Bind parameters complete\n", FILE_APPEND);
        
        if (!$stmt->execute()) {
            file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Execute failed: " . $stmt->error . "\n", FILE_APPEND);
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $insert_id = $stmt->insert_id;
        file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Insert successful, ID: $insert_id\n", FILE_APPEND);
        
        $stmt->close();
        mysqli_close($conn);
        
        $_SESSION['blotter_success'] = "Blotter record #" . $blotter_id . " has been added successfully.";
        file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Success, redirecting to blotter.php\n", FILE_APPEND);
        
        header("Location: ../blotter.php");
        exit();
        
    } catch (Exception $e) {
        file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Exception: " . $e->getMessage() . "\n", FILE_APPEND);
        
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
    file_put_contents('../../blotter-debug.log', date('Y-m-d H:i:s') . " - Not a POST request\n", FILE_APPEND);
    header("Location: ../blotter.php");
    exit();
}
?> 