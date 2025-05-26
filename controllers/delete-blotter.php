<?php
require("../database.php");
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../pages/login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['blotter_error'] = "No blotter ID provided.";
    header("Location: ../pages/blotter.php");
    exit();
}

$id = intval($_GET['id']);

// Get database connection
$conn = getDatabaseConnection();
if (!$conn) {
    $_SESSION['blotter_error'] = "Database connection failed.";
    header("Location: ../pages/blotter.php");
    exit();
}

try {
    // Get blotter ID for the success message
    $query = "SELECT blotter_id FROM blotter_records WHERE id = ? AND archived = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['blotter_error'] = "Blotter record not found or already archived.";
        header("Location: ../pages/blotter.php");
        exit();
    }
    
    $blotter = $result->fetch_assoc();
    $blotter_id = $blotter['blotter_id'];
    $stmt->close();
    
    // Archive the blotter record (set archived = 1 instead of deleting)
    $query = "UPDATE blotter_records SET archived = 1 WHERE id = ? AND archived = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['blotter_success'] = "Blotter record #" . $blotter_id . " has been archived successfully.";
    } else {
        $_SESSION['blotter_error'] = "Failed to archive blotter record or record already archived.";
    }
    
    $stmt->close();
    mysqli_close($conn);
    
    header("Location: ../pages/blotter.php");
    exit();
} catch (Exception $e) {
    $_SESSION['blotter_error'] = "Error: " . $e->getMessage();
    header("Location: ../pages/blotter.php");
    exit();
}
?> 