<?php
// Ensure we have a proper session
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../pages/login.php");
    exit();
}

require("../database.php");

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get database connection
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        die("Connection failed: Unable to connect to database");
    }
    
    // Get data from the form
    $resident_name = $_POST['resident_name'];
    $certificate_type = $_POST['certificate_type'];
    $purpose = $_POST['purpose'];
    $issued_by = $_POST['issued_by'];
    
    // Insert certificate into database
    $stmt = $conn->prepare("INSERT INTO certificates (resident_name, certificate_type, purpose, issue_date, issued_by) 
                          VALUES (?, ?, ?, NOW(), ?)");
    
    if ($stmt) {
        $stmt->bind_param("ssss", $resident_name, $certificate_type, $purpose, $issued_by);
        
        if ($stmt->execute()) {
            $_SESSION['certificate_success'] = "Certificate issued successfully.";
            header("Location: ../pages/certificates.php");
            exit();
        } else {
            $_SESSION['certificate_error'] = "Error issuing certificate: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $_SESSION['certificate_error'] = "Error preparing statement: " . $conn->error;
    }
    
    mysqli_close($conn);
    
    // Redirect back to certificates page with error
    header("Location: ../pages/certificates.php");
    exit();
} else {
    // If not a POST request, redirect to certificates page
    header("Location: ../pages/certificates.php");
    exit();
}
?> 