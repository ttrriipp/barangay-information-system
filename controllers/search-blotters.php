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
    // Search blotters in database
    $query = "SELECT id, blotter_id, incident_type, complainant_name, respondent_name, 
              incident_date, status, date_reported 
              FROM blotter_records
              WHERE 
                blotter_id LIKE ? OR
                incident_type LIKE ? OR
                complainant_name LIKE ? OR
                respondent_name LIKE ? OR
                status LIKE ? OR
                CAST(id AS CHAR) LIKE ?
              ORDER BY id ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $blotters = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Format date for display
            $row['incident_date_formatted'] = date('M d, Y', strtotime($row['incident_date']));
            $blotters[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'blotters' => $blotters
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