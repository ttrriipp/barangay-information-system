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

// Fetch blotter records from the database
$conn = getDatabaseConnection();
$blotters = [];
if ($conn) {
    $query = "SELECT id, blotter_id, incident_type, complainant_name, respondent_name, 
              incident_date, status, date_reported 
              FROM blotter_records 
              ORDER BY date_reported DESC";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Format date for display
            $row['incident_date_formatted'] = date('M d, Y', strtotime($row['incident_date']));
            $row['date_reported_formatted'] = date('M d, Y', strtotime($row['date_reported']));
            $blotters[] = $row;
        }
    }
    mysqli_close($conn);
}
?>

<?php require("partials/sidebar.php") ?>

<div class="main-content">
    <div class="header-container">
        <h1>Blotter Records Management</h1>
    </div>
    
    <?php if (isset($_SESSION['blotter_success'])): ?>
    <div class="alert alert-success">
        <p>Blotter record added successfully!</p>
    </div>
    <?php unset($_SESSION['blotter_success']); ?>
    <?php elseif (isset($_SESSION['blotter_error'])): ?>
    <div class="alert alert-error">
        <p><?= htmlspecialchars($_SESSION['blotter_error']) ?></p>
    </div>
    <?php unset($_SESSION['blotter_error']); ?>
    <?php endif; ?>
    
    <div class="search-and-add-container">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Search blotter records..." onkeyup="searchBlotters()">
        </div>
        <button class="text-button add-btn" onclick="addBlotter()">File New Blotter</button>
    </div>
    
    <div class="table-container">
        <table id="blotterTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Blotter ID</th>
                    <th>Incident Type</th>
                    <th>Complainant</th>
                    <th>Respondent</th>
                    <th>Incident Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="blotterTableBody">
                <?php foreach ($blotters as $index => $blotter): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($blotter['blotter_id']) ?></td>
                        <td><?= htmlspecialchars($blotter['incident_type']) ?></td>
                        <td><?= htmlspecialchars($blotter['complainant_name']) ?></td>
                        <td><?= htmlspecialchars($blotter['respondent_name']) ?></td>
                        <td><?= htmlspecialchars($blotter['incident_date_formatted']) ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower($blotter['status']) ?>">
                                <?= htmlspecialchars($blotter['status']) ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <button class="icon-button view-btn" onclick="openModal('partials/blotter-view.php?id=<?= $blotter['id'] ?>')" title="View Blotter">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="icon-button edit-btn" onclick="editBlotter(<?= $blotter['id'] ?>)" title="Edit Blotter">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="icon-button delete-btn" onclick="deleteBlotter(<?= $blotter['id'] ?>)" title="Delete Blotter">
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
<div id="blotterModal" class="modal" style="display: none;">
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
            <p>Are you sure you want to delete this blotter record? This action cannot be undone.</p>
            <div class="button-group">
                <button id="confirmDeleteBtn" class="delete-btn">Yes, Delete</button>
                <button id="cancelDeleteBtn" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Status badge styles */
.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
    text-align: center;
    text-transform: uppercase;
}

.status-pending {
    background-color: #FFC107;
    color: #000;
}

.status-ongoing {
    background-color: #2196F3;
    color: #fff;
}

.status-resolved {
    background-color: #4CAF50;
    color: #fff;
}

.status-dismissed {
    background-color: #9E9E9E;
    color: #fff;
}

/* Alert styles */
.alert {
    padding: 15px;
    margin: 0 20px 20px;
    border-radius: 5px;
    animation: fadeOut 5s forwards;
    animation-delay: 3s;
}

.alert-success {
    background-color: rgba(76, 175, 80, 0.3);
    border: 1px solid #4CAF50;
}

.alert-error {
    background-color: rgba(244, 67, 54, 0.3);
    border: 1px solid #f44336;
}

@keyframes fadeOut {
    from {opacity: 1;}
    to {opacity: 0; display: none;}
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
}

.modal-content {
    background-color: #1e3a8a; /* Dark blue background */
    margin: 5vh auto;
    border-radius: 8px;
    width: 90%;
    max-width: 900px;
    position: relative;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
}

.close-button {
    position: absolute;
    top: 15px;
    right: 20px;
    color: white;
    font-size: 28px;
    font-weight: bold;
    z-index: 10;
}

.close-button:hover,
.close-button:focus {
    color: #4CAF50;
    text-decoration: none;
    cursor: pointer;
}

.modal-body {
    padding: 0;
}

/* Loading spinner */
.loading-spinner {
    display: block;
    width: 50px;
    height: 50px;
    margin: 50px auto;
    border: 5px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #4CAF50;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Delete confirmation modal */
.delete-confirm-content {
    text-align: center;
    padding: 20px;
    color: white;
}

.delete-confirm-content h3 {
    margin-bottom: 15px;
}

.button-group {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 10px;
}

#deleteConfirmModal .modal-content {
    max-width: 500px;
}

#confirmDeleteBtn {
    background-color: #f44336;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

#confirmDeleteBtn:hover {
    background-color: #d32f2f;
    transform: translateY(-2px);
}

#cancelDeleteBtn {
    background-color: #9e9e9e;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

#cancelDeleteBtn:hover {
    background-color: #757575;
    transform: translateY(-2px);
}
</style>

<script src="../assets/js/blotter.js"></script>
<?php require("partials/foot.php"); ?> 