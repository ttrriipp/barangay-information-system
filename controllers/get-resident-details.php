<?php
header('Content-Type: application/json');
include("../database.php");
$conn = getDatabaseConnection();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No resident ID provided']);
    exit;
}

$id = intval($_GET['id']);

try {
    // Get resident information
    $query = $conn->prepare("
        SELECT 
            r.id, 
            r.surname, 
            r.firstname, 
            r.middlename,
            CONCAT(r.surname, ', ', r.firstname, ' ', r.middlename) AS fullname,
            r.birthdate,
            TIMESTAMPDIFF(YEAR, r.birthdate, CURDATE()) AS age, 
            r.sex, 
            r.address, 
            r.contact,
            r.email,
            r.civil_status,
            r.occupation,
            r.education,
            r.voter_status,
            r.pwd_status,
            r.philhealth_status,
            r.`4ps_status`,
            r.emergency_contact_name,
            r.emergency_contact_number,
            r.blood_type,
            r.religion,
            r.nationality,
            r.date_of_residency,
            r.household_id,
            CONCAT(h.id, ': ', hr.surname, ', ', hr.firstname) AS household_name
        FROM residents r
        LEFT JOIN households h ON r.household_id = h.id
        LEFT JOIN residents hr ON h.head_id = hr.id
        WHERE r.id = ? AND r.archived = 0
    ");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Resident not found']);
        exit;
    }
    
    $resident = $result->fetch_assoc();
    $query->close();
    
    echo json_encode([
        'success' => true,
        'resident' => $resident
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} 