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

// Prepare and execute the ARCHIVE query (set archived = 1 instead of deleting)
$stmt = $conn->prepare("UPDATE residents SET archived = 1 WHERE id = ? AND archived = 0");
$stmt->bind_param("i", $id);
$result = $stmt->execute();

// Check if archiving was successful
if ($result && $stmt->affected_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode(["success" => true, "message" => "Resident archived successfully"]);
} else {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Failed to archive resident or resident already archived"]);
}

// Close the connection
$stmt->close();
$conn->close();
?> 