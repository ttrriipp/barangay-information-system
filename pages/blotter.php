<?php

session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

$style = 'main.css';
$additionalStyles = ['modal.css'];

require("partials/head.php");
require("../database.php");

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Fetch blotter records from the database with pagination
$conn = getDatabaseConnection();
$blotters = [];
$total_records = 0;

if ($conn) {
    // Get total number of records
    $count_query = "SELECT COUNT(*) as total FROM blotter_records";
    $count_result = mysqli_query($conn, $count_query);
    if ($count_result) {
        $count_row = mysqli_fetch_assoc($count_result);
        $total_records = $count_row['total'];
    }

    // Get blotter records with pagination
    $query = "SELECT id, blotter_id, incident_type, complainant_name, respondent_name, 
              incident_date, status, date_reported 
              FROM blotter_records 
              ORDER BY date_reported DESC
              LIMIT $offset, $records_per_page";
    
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

$total_pages = ceil($total_records / $records_per_page);
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
            <input type="text" id="searchInput" placeholder="Search by No., Blotter ID, type, names..." onkeyup="searchBlotters()">
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
                        <td><?= ($offset + $index + 1) ?></td>
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
                            <button class="icon-button view-btn" onclick="openModal('partials/blotter-view.php?id=<?= $blotter['id'] ?>')" title="View Blotter Details">
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
.header-container {
    display: flex;

}
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
    background-color: #1a2b5d; /* Updated to match other modals */
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
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.modal-body::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
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

<script>
    // Global function to close the modal - can be called from within modals
    function closeModal() {
        document.getElementById('blotterModal').style.display = 'none';
    }
    
    // Global searchBlotters function
    function searchBlotters() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const tbody = document.getElementById('blotterTableBody');
        const rows = tbody.getElementsByTagName('tr');
        
        // If we're searching, hide the pagination as we're filtering the table directly
        const pagination = document.querySelector('.pagination');
        if (pagination) {
            pagination.style.display = filter.length > 0 ? 'none' : 'flex';
        }
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.indexOf(filter.toLowerCase()) > -1) {
                    found = true;
                    break;
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    }

    // Open modal with content from URL
    function openModal(url) {
        const modal = document.getElementById('blotterModal');
        const modalBody = modal.querySelector('.modal-body');
        
        // Show loading state
        modalBody.innerHTML = '<div class="loading-spinner"></div>';
        modal.style.display = 'block';
        
        // Fetch content
        fetch(url)
            .then(response => response.text())
            .then(data => {
                modalBody.innerHTML = data;
            })
            .catch(error => {
                modalBody.innerHTML = `<p class="error-message">Error loading content: ${error.message}</p>`;
            });
    }

    // Add new blotter record
    function addBlotter() {
        openModal('partials/blotter-add-modal.php');
    }

    // Edit blotter record
    function editBlotter(id) {
        openModal(`partials/blotter-edit.php?id=${id}`);
    }

    // Delete blotter record
    function deleteBlotter(id) {
        const deleteModal = document.getElementById('deleteConfirmModal');
        deleteModal.style.display = 'block';
        
        document.getElementById('confirmDeleteBtn').onclick = function() {
            window.location.href = 'controllers/blotter-delete.php?id=' + id;
        };
        
        document.getElementById('cancelDeleteBtn').onclick = function() {
            deleteModal.style.display = 'none';
        };
    }

    // Close modals when clicking the X
    document.querySelectorAll('.close-button').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) modal.style.display = 'none';
        });
    });

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    };
</script>
<?php require("partials/foot.php"); ?> 