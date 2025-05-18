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

// Fetch households from the database
$conn = getDatabaseConnection();
$households = [];
if ($conn) {
    $query = "SELECT h.id, r.surname as head_surname, r.firstname as head_firstname, 
              h.address,
              (SELECT COUNT(*) FROM residents WHERE household_id = h.id) as member_count
              FROM households h 
              LEFT JOIN residents r ON h.head_id = r.id 
              GROUP BY h.id";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['head_fullname'] = $row['head_surname'] . ', ' . $row['head_firstname'];
            $households[] = $row;
        }
    }
    mysqli_close($conn);
}
?>

<?php require("partials/sidebar.php") ?>

<div class="main-content">
    <div class="header-container">
        <h1>Household Management</h1>
    </div>
    
    <div class="search-and-add-container">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Search households..." onkeyup="searchHouseholds()">
        </div>
        <button class="text-button add-btn" onclick="addHousehold()">Add New Household</button>
    </div>
    
    <div class="table-container">
        <table id="householdTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Head of Household</th>
                    <th>Address</th>
                    <th>Total Members</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="householdTableBody">
                <?php foreach ($households as $index => $household): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($household['head_fullname']) ?></td>
                        <td><?= htmlspecialchars($household['address']) ?></td>
                        <td><?= htmlspecialchars($household['member_count']) ?></td>
                        <td class="action-buttons">
                            <button class="icon-button view-btn" onclick="openModal('partials/household-view.php?id=<?= $household['id'] ?>')" title="View Household">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="icon-button edit-btn" onclick="editHousehold(<?= $household['id'] ?>)" title="Edit Household">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="icon-button delete-btn" onclick="deleteHousehold(<?= $household['id'] ?>)" title="Delete Household">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Dialog -->
<div id="householdModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <div class="modal-body">
            <!-- Modal content will be loaded here via AJAX -->
        </div>
    </div>
</div>



<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close-button" id="closeDeleteModal">&times;</span>
        <div class="delete-confirm-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this household? This action cannot be undone.</p>
            <div class="button-group">
                <button id="confirmDeleteBtn" class="delete-btn">Yes, Delete</button>
                <button id="cancelDeleteBtn" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/households.js"></script>
<?php require("partials/foot.php"); ?> 