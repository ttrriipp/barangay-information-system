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
    'contact' => ''
];
$isEdit = false;

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT surname, firstname, middlename, age, sex, address, contact FROM resident WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($resident['surname'], $resident['fname'], $resident['mname'], $resident['age'], $resident['sex'], $resident['address'], $resident['contact']);
    $stmt->fetch();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barangay Cupang West Form</title>
    <link rel="stylesheet" href="../../assets/css/popup.css" />
  </head>
  <body>
    <div class="container">
      <div class="form-container">
        <form id="residentForm" action="/integ-proj/barangay-information-system/controllers/popupdb.php" method="POST">
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

          <button type="submit" name="<?= $isEdit ? 'residentedit' : 'residentadd' ?>">
            <?= $isEdit ? 'Update' : 'Submit' ?>
          </button>
        </form>
      </div>
      <div class="logo-container">
        <img src="your-logo-url-here.png" alt="Cupang West Logo" />
      </div>
    </div>
    <script src="popup.js"></script>
  </body>
</html>

