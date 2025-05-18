<?php
// Ensure we have a proper session
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../pages/login.php");
    exit();
}

require("../database.php");

// Check if certificate ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['certificate_error'] = "No certificate ID provided";
    header("Location: ../pages/certificates.php");
    exit();
}

$id = intval($_GET['id']);

// Get database connection
$conn = getDatabaseConnection();

if (!$conn) {
    $_SESSION['certificate_error'] = "Database connection failed";
    header("Location: ../pages/certificates.php");
    exit();
}

try {
    // Get certificate details for the success message
    $query = "SELECT resident_name, certificate_type FROM certificates WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['certificate_error'] = "Certificate record not found";
        header("Location: ../pages/certificates.php");
        exit();
    }
    
    $certificate = $result->fetch_assoc();
    $resident_name = $certificate['resident_name'];
    $certificate_type = $certificate['certificate_type'];
    $stmt->close();
    
    // Delete the certificate record
    $query = "DELETE FROM certificates WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['certificate_success'] = "Certificate for " . $resident_name . " (" . $certificate_type . ") has been deleted successfully";
    } else {
        $_SESSION['certificate_error'] = "Failed to delete certificate record";
    }
    
    $stmt->close();
    mysqli_close($conn);
    
    header("Location: ../pages/certificates.php");
    exit();
} catch (Exception $e) {
    $_SESSION['certificate_error'] = "Error: " . $e->getMessage();
    header("Location: ../pages/certificates.php");
    exit();
}
?> 