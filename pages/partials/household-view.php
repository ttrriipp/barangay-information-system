<?php
include("../../database.php");
$conn = getDatabaseConnection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<p class="error-message">No household ID provided</p>';
    exit;
}

$id = intval($_GET['id']);

// Get household information
$householdQuery = $conn->prepare("
    SELECT h.id, h.address, r.id as head_id, r.surname as head_surname, 
           r.firstname as head_firstname, r.middlename as head_middlename
    FROM households h
    JOIN residents r ON h.head_id = r.id
    WHERE h.id = ?
");
$householdQuery->bind_param("i", $id);
$householdQuery->execute();
$householdResult = $householdQuery->get_result();

if ($householdResult->num_rows === 0) {
    echo '<p class="error-message">Household not found</p>';
    exit;
}

$household = $householdResult->fetch_assoc();
$householdQuery->close();

// Format household name
$headFullname = $household['head_surname'] . ', ' . $household['head_firstname'] . ' ' . $household['head_middlename'];

// Get household members
$membersQuery = $conn->prepare("
    SELECT r.id, r.surname, r.firstname, r.middlename, r.age, r.sex, 
           r.occupation, r.civil_status, r.voter_status, r.contact,
           (CASE WHEN r.id = ? THEN 1 ELSE 0 END) as is_head
    FROM residents r
    WHERE r.household_id = ?
    ORDER BY is_head DESC, r.surname, r.firstname
");
$membersQuery->bind_param("ii", $household['head_id'], $id);
$membersQuery->execute();
$membersResult = $membersQuery->get_result();

$members = [];
while ($member = $membersResult->fetch_assoc()) {
    $members[] = $member;
}
$membersQuery->close();

// Count total household members
$memberCount = count($members);
?>

<div class="household-details-container">
    <h2 class="view-header">Household Information</h2>

    <div class="details-section">
        <h3>Basic Information</h3>
        <div class="details-grid">
            <div class="detail-item">
                <strong>Household ID:</strong>
                <span><?= htmlspecialchars($household['id']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Head of Household:</strong>
                <span><?= htmlspecialchars($headFullname) ?></span>
            </div>
            <div class="detail-item">
                <strong>Address:</strong>
                <span><?= htmlspecialchars($household['address']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Total Members:</strong>
                <span><?= $memberCount ?></span>
            </div>
        </div>
    </div>

    <div class="details-section">
        <h3>Household Members</h3>
        <?php if ($memberCount > 0): ?>
        <div class="members-table-container">
            <table class="members-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Sex</th>
                        <th>Occupation</th>
                        <th>Civil Status</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr <?= $member['is_head'] ? 'class="household-head"' : '' ?>>
                            <td>
                                <?= htmlspecialchars($member['surname'] . ', ' . $member['firstname'] . ' ' . $member['middlename']) ?>
                                <?= $member['is_head'] ? ' <span class="head-label">(Head)</span>' : '' ?>
                            </td>
                            <td><?= htmlspecialchars($member['age']) ?></td>
                            <td><?= htmlspecialchars($member['sex']) ?></td>
                            <td><?= htmlspecialchars($member['occupation'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($member['civil_status'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($member['contact'] ?? 'N/A') ?></td>
                            <td>
                                <button class="icon-button view-btn" onclick="openModal('partials/resident-view.php?id=<?= $member['id'] ?>')" title="View Member Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="no-members">No members found in this household</p>
        <?php endif; ?>
    </div>

    <div class="action-buttons-container">
        <button class="edit-btn" onclick="editHousehold(<?= $id ?>)">Edit Household</button>
    </div>
</div>

<style>
.household-details-container {
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;
    color: white;
}

.view-header {
    text-align: center;
    color: white;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #4CAF50;
}

.details-section {
    margin-bottom: 25px;
}

.details-section h3 {
    color: white;
    font-size: 1.2rem;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.detail-item {
    display: flex;
    flex-direction: column;
}

.detail-item strong {
    font-weight: bold;
    color: #4CAF50; /* Light green for labels */
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.detail-item span {
    padding: 8px;
    background-color: rgba(255, 255, 255, 0.1); /* Semi-transparent white */
    border-radius: 4px;
    font-size: 1rem;
    color: white;
}

.members-table-container {
    overflow-x: auto;
    margin-top: 15px;
}

.members-table {
    width: 100%;
    border-collapse: collapse;
    color: white;
}

.members-table th, 
.members-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.members-table th {
    background-color: rgba(76, 175, 80, 0.3);
    color: white;
    font-weight: bold;
}

.members-table .household-head {
    background-color: rgba(255, 255, 255, 0.1);
}

.head-label {
    color: #4CAF50;
    font-size: 0.85rem;
    font-style: italic;
}

.no-members {
    text-align: center;
    font-style: italic;
    color: rgba(255, 255, 255, 0.7);
    padding: 20px;
}

.action-buttons-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
}

.action-buttons-container button {
    margin: 0 10px;
}

.edit-btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.edit-btn:hover {
    background-color: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.icon-button {
    background-color: #00695c;
    border: none;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    color: white;
    transition: all 0.3s;
}

.icon-button:hover {
    background-color: #00897b;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.icon-button i {
    font-size: 14px;
}

.error-message {
    color: #ff6b6b;
    text-align: center;
    font-weight: bold;
    padding: 20px;
}

@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
}
</style> 