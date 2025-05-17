<?php
include("../../database.php");
$conn = getDatabaseConnection();

$resident = [
    'surname' => '',
    'fname' => '',
    'mname' => '',
    'age' => '',
    'sex' => '',
    'address' => '',
    'contact' => '',
    'household_id' => ''
];
$isEdit = false;

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT surname, firstname, middlename, age, sex, address, contact, household_id FROM residents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($resident['surname'], $resident['fname'], $resident['mname'], $resident['age'], $resident['sex'], $resident['address'], $resident['contact'], $resident['household_id']);
    $stmt->fetch();
    $stmt->close();
}

// Get all households for dropdown
$householdsStmt = $conn->prepare("SELECT h.id, CONCAT(r.surname, ', ', r.firstname, ' (', h.address, ')') AS household_name 
                                FROM households h 
                                JOIN residents r ON h.head_id = r.id 
                                ORDER BY r.surname, r.firstname");
$householdsStmt->execute();
$householdsResult = $householdsStmt->get_result();
$households = [];
while ($row = $householdsResult->fetch_assoc()) {
    $households[] = $row;
}
$householdsStmt->close();
?>
<div class="modal-flex-container">
    <div class="form-container">
        <form id="residentForm" action="../controllers/popupdb.php" method="POST">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
            <?php endif; ?>
            <label for="surname">Surname:</label>
            <input type="text" id="surname" placeholder="Surname" name="surname" required value="<?= htmlspecialchars($resident['surname']) ?>" />

            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" placeholder="First Name" name="fname" required value="<?= htmlspecialchars($resident['fname']) ?>" />

            <label for="middleName">Middle Name:</label>
            <input type="text" id="middleName" placeholder="Middle Name" name="mname" required value="<?= htmlspecialchars($resident['mname']) ?>" />

            <label for="age">Age:</label>
            <input type="text" id="age" placeholder="Age" name="age" required value="<?= htmlspecialchars($resident['age']) ?>" />

            <p class="label">Sex:</p>
            <div class="sex">
                <select name="sex" required>
                    <option value="not selected" <?= $resident['sex'] == 'not selected' ? 'selected' : '' ?>>Select</option>
                    <option value="Male" <?= $resident['sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $resident['sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $resident['sex'] == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <label for="Address">Address:</label>
            <input type="text" id="Address" placeholder="Address" name="address" required value="<?= htmlspecialchars($resident['address']) ?>" />

            <label for="contactNo">Contact No.:</label>
            <input type="text" id="contactNo" placeholder="Contact No." name="contact" required value="<?= htmlspecialchars($resident['contact']) ?>" />

            <label for="household">Household:</label>
            <select id="household" name="household_id">
                <option value="">No Household</option>
                <?php foreach ($households as $household): ?>
                    <option value="<?= $household['id'] ?>" <?= $resident['household_id'] == $household['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($household['household_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="<?= $isEdit ? 'residentedit' : 'residentadd' ?>">
                <?= $isEdit ? 'Update' : 'Submit' ?>
            </button>
        </form>
    </div>
    <div class="logo-container">
        <img src="../assets/images/logo-cupangwest.png" alt="Cupang West Logo" />
    </div>
</div> 