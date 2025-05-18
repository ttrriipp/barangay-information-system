<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

require("../../database.php");

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $resident_name = $_POST["resident_name"];
    $certificate_type = $_POST["certificate_type"];
    $purpose = $_POST["purpose"];
    $issue_date = date("Y-m-d H:i:s"); // Current date and time
    $issued_by = $_POST["issued_by"]; // Use the submitted value instead of session username
    
    // Connect to database
    $conn = getDatabaseConnection();
    
    if ($conn) {
        // Insert the certificate record directly with the text inputs
        $query = "INSERT INTO certificates (resident_name, certificate_type, purpose, issue_date, issued_by) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $resident_name, $certificate_type, $purpose, $issue_date, $issued_by);
        
        if (mysqli_stmt_execute($stmt)) {
            // Success
            mysqli_close($conn);
            header("Location: ../certificates.php");
            exit();
        } else {
            // Error
            $error = "Error: " . mysqli_error($conn);
            mysqli_close($conn);
            echo $error;
            exit();
        }
    } else {
        echo "Database connection failed";
        exit();
    }
} else {
    // If not a POST request, redirect to certificates page
    header("Location: ../certificates.php");
    exit();
}
?> 