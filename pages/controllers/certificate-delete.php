<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

require("../../database.php");

// Check if ID parameter is set
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $certificate_id = $_GET["id"];
    
    // Connect to database
    $conn = getDatabaseConnection();
    
    if ($conn) {
        // Delete the certificate record
        $query = "DELETE FROM certificates WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $certificate_id);
        
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
    // If ID is not provided, redirect to certificates page
    header("Location: ../certificates.php");
    exit();
}
?> 