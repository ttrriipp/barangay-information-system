<?php
header('Content-Type: application/json');
include("../database.php");
$conn = getDatabaseConnection();

// Check if request method is POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if ID is provided
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'No household ID provided']);
    exit;
}

$id = intval($_POST['id']);

// Begin transaction
$conn->begin_transaction();

try {
    // Delete the household
    $deleteHousehold = $conn->prepare("DELETE FROM households WHERE id = ?");
    $deleteHousehold->bind_param("i", $id);
    
    if (!$deleteHousehold->execute()) {
        throw new Exception("Failed to delete household: " . $deleteHousehold->error);
    }
    
    $deleteHousehold->close();
    
    // Commit the transaction
    $conn->commit();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Roll back the transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?> 