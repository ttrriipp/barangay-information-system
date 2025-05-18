<?php
session_start();
require("../../database.php");

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

// Check if ID parameter exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['blotter_error'] = "Invalid blotter ID provided.";
    header("Location: ../blotter.php");
    exit();
}

$id = intval($_GET['id']);
$conn = getDatabaseConnection();

// First, retrieve the blotter info to keep a record of what was deleted
$query = $conn->prepare("SELECT * FROM blotter_records WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    $_SESSION['blotter_error'] = "Blotter record not found.";
    header("Location: ../blotter.php");
    exit();
}

// Get the blotter details before deleting
$blotter = $result->fetch_assoc();
$blotter_id = $blotter['blotter_id'];
$query->close();

// Delete the blotter record
$delete = $conn->prepare("DELETE FROM blotter_records WHERE id = ?");
$delete->bind_param("i", $id);

if ($delete->execute()) {
    $_SESSION['blotter_success'] = "Blotter record #" . $blotter_id . " has been successfully deleted.";
} else {
    $_SESSION['blotter_error'] = "Error deleting blotter record: " . $delete->error;
}

$delete->close();
mysqli_close($conn);

// Redirect back to blotter page
header("Location: ../blotter.php");
exit();
?> 