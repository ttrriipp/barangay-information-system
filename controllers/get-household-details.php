<?php
header('Content-Type: application/json');
include("../database.php");
$conn = getDatabaseConnection();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No household ID provided']);
    exit;
}

$id = intval($_GET['id']);

try {
    // Get household information
    $householdQuery = $conn->prepare("
        SELECT h.id, h.address, r.surname, r.firstname, r.middlename 
        FROM households h
        JOIN residents r ON h.head_id = r.id
        WHERE h.id = ?
    ");
    $householdQuery->bind_param("i", $id);
    $householdQuery->execute();
    $householdResult = $householdQuery->get_result();
    
    if ($householdResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Household not found']);
        exit;
    }
    
    $household = $householdResult->fetch_assoc();
    $householdQuery->close();
    
    // Format household name
    $household['fullname'] = $household['surname'] . ', ' . $household['firstname'] . ' ' . $household['middlename'];
    
    // Get household members
    $membersQuery = $conn->prepare("
        SELECT r.surname, r.firstname, r.middlename, r.age, r.sex, 
               (CASE WHEN r.id = (SELECT head_id FROM households WHERE id = ?) THEN 1 ELSE 0 END) as is_head
        FROM residents r
        WHERE r.household_id = ?
        ORDER BY is_head DESC, r.surname, r.firstname
    ");
    $membersQuery->bind_param("ii", $id, $id);
    $membersQuery->execute();
    $membersResult = $membersQuery->get_result();
    
    $members = [];
    while ($member = $membersResult->fetch_assoc()) {
        $member['fullname'] = $member['surname'] . ', ' . $member['firstname'] . ' ' . $member['middlename'];
        $member['fullname'] = $member['is_head'] ? $member['fullname'] . ' (Head)' : $member['fullname'];
        $members[] = $member;
    }
    $membersQuery->close();
    
    echo json_encode([
        'success' => true,
        'household' => [
            'id' => $household['id'],
            'name' => $household['fullname'],
            'address' => $household['address']
        ],
        'members' => $members
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?> 