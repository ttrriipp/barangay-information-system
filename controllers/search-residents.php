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
    // Search residents in database
    $query = "SELECT r.id, 
              CONCAT(r.surname, ', ', r.firstname, ' ', r.middlename) AS fullname, 
              r.address, r.age, r.sex, r.contact,
              r.civil_status, r.occupation, r.voter_status
          FROM residents r
          WHERE
              r.surname LIKE ? OR
              r.firstname LIKE ? OR
              r.middlename LIKE ? OR
              r.address LIKE ? OR
              r.contact LIKE ? OR
              r.occupation LIKE ? OR
              CONCAT(r.surname, ', ', r.firstname, ' ', r.middlename) LIKE ? OR
              CAST(r.id AS CHAR) LIKE ?
          ORDER BY r.id ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $residents = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $residents[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'residents' => $residents
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