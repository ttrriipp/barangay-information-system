<?php
include("../../database.php");
$conn = getDatabaseConnection();

$household = [
    'id' => '',
    'head_id' => '',
    'address' => ''
];
$isEdit = false;
$members = [];

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = intval($_GET['id']);
    
    // Fetch household details
    $stmt = $conn->prepare("SELECT id, head_id, address FROM households WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($household['id'], $household['head_id'], $household['address']);
    $stmt->fetch();
    $stmt->close();
}

// Fetch all residents for the dropdown
$residentsStmt = $conn->prepare("SELECT id, CONCAT(surname, ', ', firstname, ' ', middlename) AS fullname FROM residents ORDER BY surname, firstname");
$residentsStmt->execute();
$residentsResult = $residentsStmt->get_result();
$residents = [];
while ($row = $residentsResult->fetch_assoc()) {
    $residents[] = $row;
}
$residentsStmt->close();
?>

<div class="modal-flex-container">
    <div class="form-container">
        <form id="householdForm" action="../controllers/household-controller.php" method="POST">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($household['id']) ?>">
            <?php endif; ?>
            
            <label for="headOfHousehold">Head of Household:</label>
            <select id="headOfHousehold" name="head_id" required>
                <option value="">Select Head of Household</option>
                <?php foreach ($residents as $resident): ?>
                    <option value="<?= $resident['id'] ?>" <?= $household['head_id'] == $resident['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($resident['fullname']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="address">Household Address:</label>
            <input type="text" id="address" placeholder="Address" name="address" required value="<?= htmlspecialchars($household['address']) ?>" />

            <button type="submit" name="<?= $isEdit ? 'householdedit' : 'householdadd' ?>">
                <?= $isEdit ? 'Update' : 'Submit' ?>
            </button>
        </form>
    </div>
    <div class="logo-container">
        <img src="../assets/images/logo-cupangwest.png" alt="Cupang West Logo" />
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const headSelect = document.getElementById('headOfHousehold');
    
    // When head selection changes
    headSelect.addEventListener('change', function() {
        // This would typically reload the member list or perform some validation
        // For this implementation, we're just allowing the form to submit with the new head
    });
});
</script> 