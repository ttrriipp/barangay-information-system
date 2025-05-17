<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
$style = 'main.css';
require("partials/head.php");
require("../database.php");

// Fetch residents from the database
$conn = getDatabaseConnection();
$residents = [];
if ($conn) {
    $query = "SELECT id, CONCAT(surname, ', ', firstname, ' ', middlename) AS fullname, address, age, sex FROM resident";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $residents[] = $row;
        }
    }
    mysqli_close($conn);
}
?>
<?php require("partials/sidebar.php") ?>
<div class="main-content">
    <h1>Resident Management</h1>
    <div class="table-container">
        <table id="residentTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Age</th>
                    <th>Sex</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="residentTableBody">
                <?php foreach ($residents as $index => $resident): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($resident['fullname']) ?></td>
                        <td><?= htmlspecialchars($resident['address']) ?></td>
                        <td><?= htmlspecialchars($resident['age']) ?></td>
                        <td><?= htmlspecialchars($resident['sex']) ?></td>
                        <td>
                            <button onclick="editResident(<?= $resident['id'] ?>)">Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="button-container">
        <button onclick="addResident()">Add New Resident</button>
        <button onclick="deleteResident()">Delete Resident</button>
    </div>
</div>

<script src="../assets/js/residents.js"></script>
<?php require("partials/foot.php"); ?>