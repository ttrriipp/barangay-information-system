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

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Fetch residents from the database with pagination
$conn = getDatabaseConnection();
$residents = [];
$total_records = 0;

if ($conn) {
    // Get total number of records
    $count_query = "SELECT COUNT(*) as total FROM residents";
    $count_result = mysqli_query($conn, $count_query);
    if ($count_result) {
        $count_row = mysqli_fetch_assoc($count_result);
        $total_records = $count_row['total'];
    }

    // Get residents with pagination
    $query = "SELECT r.id, 
              CONCAT(r.surname, ', ', r.firstname, ' ', r.middlename) AS fullname, 
              r.address, r.age, r.sex, r.contact,
              r.civil_status, r.occupation, r.voter_status
          FROM residents r
          ORDER BY r.id ASC
          LIMIT $offset, $records_per_page";

    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $residents[] = $row;
        }
    }

    mysqli_close($conn);
}

$total_pages = ceil($total_records / $records_per_page);
?>

<?php require("partials/sidebar.php") ?>

<div class="main-content">

    <div class="header-container">

        <h1>Resident Management</h1>

    </div>

    

    <div class="search-and-add-container">

        <div class="search-box">

            <i class="fas fa-search search-icon"></i>

            <input type="text" id="searchInput" placeholder="Search by No., name, address...">

        </div>

        <button class="text-button add-btn" onclick="addResident()">Add New Resident</button>

    </div>

    

    <div class="table-container">

        <table id="residentTable">

            <thead>

                <tr>

                    <th>No.</th>

                    <th>Full Name</th>

                    <th>Address</th>

                    <th>Age</th>

                    <th>Sex</th>

                    <th>Contact</th>

                    <th>Civil Status</th>

                    <th>Occupation</th>

                    <th>Voter</th>

                    <th>Actions</th>

                </tr>

            </thead>

            <tbody id="residentTableBody">

                <?php foreach ($residents as $index => $resident): ?>

                    <tr>

                        <td><?= ($offset + $index + 1) ?></td>

                        <td><?= htmlspecialchars($resident['fullname']) ?></td>

                        <td><?= htmlspecialchars($resident['address']) ?></td>

                        <td><?= htmlspecialchars($resident['age']) ?></td>

                        <td><?= htmlspecialchars($resident['sex']) ?></td>

                        <td><?= htmlspecialchars($resident['contact']) ?></td>

                        <td><?= htmlspecialchars($resident['civil_status'] ?? 'N/A') ?></td>

                        <td><?= htmlspecialchars($resident['occupation'] ?? 'N/A') ?></td>

                        <td><?= htmlspecialchars($resident['voter_status'] ?? 'N/A') ?></td>

                        <td class="action-buttons">

                            <button class="icon-button view-btn" onclick="openModal('partials/resident-view.php?id=<?= $resident['id'] ?>')" title="View Resident Details">

                                <i class="fas fa-eye"></i>

                            </button>

                            <button class="icon-button edit-btn" onclick="editResident(<?= $resident['id'] ?>)" title="Edit Resident">

                                <i class="fas fa-edit"></i>

                            </button>

                            <button class="icon-button delete-btn" onclick="deleteResident(<?= $resident['id'] ?>)" title="Delete Resident">

                                <i class="fas fa-trash-alt"></i>

                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

        

        <!-- Pagination Controls -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1" title="First Page">&laquo;&laquo;</a>
                <a href="?page=<?= ($page - 1) ?>" title="Previous Page">&laquo;</a>
            <?php endif; ?>
            
            <?php
            // Display page links
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): 
            ?>
                <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= ($page + 1) ?>" title="Next Page">&raquo;</a>
                <a href="?page=<?= $total_pages ?>" title="Last Page">&raquo;&raquo;</a>
            <?php endif; ?>
            
            <span class="page-info">Page <?= $page ?> of <?= $total_pages ?></span>
        </div>
        <?php endif; ?>
    </div>

</div>



<!-- Modal Dialog -->
<div id="residentModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <div class="modal-body">
            <!-- Modal content will be loaded here via AJAX -->
        </div>
    </div>
</div>



<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close-button" id="closeDeleteModal">&times;</span>
        <div class="delete-confirm-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this resident? This action cannot be undone.</p>
            <div class="button-group">
                <button id="confirmDeleteBtn" class="delete-btn">Yes, Delete</button>
                <button id="cancelDeleteBtn" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>
</div>



<script src="../assets/js/residents.js"></script>

<?php require("partials/foot.php"); ?>