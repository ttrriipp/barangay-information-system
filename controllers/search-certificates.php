<?php
header('Content-Type: application/json');
include("../database.php");
$conn = getDatabaseConnection();

// Check if search term is provided
if (!isset($_GET['term']) || empty($_GET['term'])) {
    echo json_encode(['success' => false, 'message' => 'No search term provided']);
    exit;
}

$searchTerm = "%" . mysqli_real_escape_string($conn, $_GET['term']) . "%";

try {
    // Search certificates in database
    $query = "SELECT id, resident_name, certificate_type, purpose, issue_date, issued_by
              FROM certificates
              WHERE 
                resident_name LIKE ? OR
                certificate_type LIKE ? OR
                purpose LIKE ? OR
                issued_by LIKE ? OR
                CAST(id AS CHAR) LIKE ?
              ORDER BY id ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $certificates = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $certificates[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'certificates' => $certificates
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
} 