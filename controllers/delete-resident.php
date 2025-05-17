<?php
// Start session for security
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

// Check if ID was provided
if (!isset($_POST['id']) || empty($_POST['id'])) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "No resident ID provided"]);
    exit();
}

// Include database connection
require_once("../database.php");
$conn = getDatabaseConnection();

if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

// Sanitize the ID
$id = intval($_POST['id']);

// Prepare and execute the DELETE query
$stmt = $conn->prepare("DELETE FROM residents WHERE id = ?");
$stmt->bind_param("i", $id);
$result = $stmt->execute();

// Check if deletion was successful
if ($result && $stmt->affected_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode(["success" => true, "message" => "Resident deleted successfully"]);
} else {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Failed to delete resident: " . $conn->error]);
}

// Close the connection
$stmt->close();
$conn->close();
?> 