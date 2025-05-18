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

// Fetch residents from the database for the dropdown
$conn = getDatabaseConnection();
$residents = [];
$certificates = [];
$total_records = 0;

if ($conn) {
    $query = "SELECT r.id, 
              CONCAT(r.surname, ', ', r.firstname, ' ', r.middlename) AS fullname, 
              r.address, r.sex, r.age, r.civil_status
              FROM residents r
              ORDER BY r.surname, r.firstname";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $residents[] = $row;
        }
    }
    
    // Get total number of certificate records
    $count_query = "SELECT COUNT(*) as total FROM certificates";
    $count_result = mysqli_query($conn, $count_query);
    if ($count_result) {
        $count_row = mysqli_fetch_assoc($count_result);
        $total_records = $count_row['total'];
    }
    
    // Fetch certificate records with pagination
    $query = "SELECT id, resident_name, certificate_type, purpose, issue_date, issued_by
              FROM certificates
              ORDER BY id ASC
              LIMIT $offset, $records_per_page";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $certificates[] = $row;
        }
    }
    
    mysqli_close($conn);
}

$total_pages = ceil($total_records / $records_per_page);
?>

<?php require("partials/sidebar.php") ?>

<div class="main-content">
    <div class="header-container">
        <h1>Certificate Issuance</h1>
    </div>
    
    <div class="search-and-add-container">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Search by No., name, type, purpose...">
        </div>
        <button class="text-button add-btn" onclick="issueCertificate()">Issue New Certificate</button>
    </div>
    
    <div class="table-container">
        <table id="certificateTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Resident Name</th>
                    <th>Certificate Type</th>
                    <th>Purpose</th>
                    <th>Issue Date</th>
                    <th>Issued By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="certificateTableBody">
                <?php foreach ($certificates as $index => $certificate): ?>
                    <tr>
                        <td><?= ($offset + $index + 1) ?></td>
                        <td><?= htmlspecialchars($certificate['resident_name']) ?></td>
                        <td><?= htmlspecialchars($certificate['certificate_type']) ?></td>
                        <td><?= htmlspecialchars($certificate['purpose']) ?></td>
                        <td><?= htmlspecialchars($certificate['issue_date']) ?></td>
                        <td><?= htmlspecialchars($certificate['issued_by']) ?></td>
                        <td class="action-buttons">
                            <button class="icon-button view-btn" onclick="printCertificate(<?= $certificate['id'] ?>)" title="Print Certificate">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="icon-button delete-btn" onclick="deleteCertificate(<?= $certificate['id'] ?>)" title="Delete Certificate">
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

<!-- Certificate Issuance Modal -->
<div id="certificateModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <span class="close-button">&times;</span>
        <h2>Issue New Certificate</h2>
        
        <div style="display: flex; gap: 20px;">
            <form id="certificateForm" action="../controllers/certificate-add.php" method="POST" class="form-container" style="flex: 1;">
                <div class="form-group">
                    <label for="residentName">Resident Name:</label>
                    <input type="text" id="residentName" name="resident_name" required placeholder="Enter resident name">
                </div>
                
                <div class="form-group">
                    <label for="certificateType">Certificate Type:</label>
                    <input type="text" id="certificateType" name="certificate_type" required placeholder="Enter certificate type (e.g., Residency, Indigency, Clearance)">
                </div>
                
                <div class="form-group">
                    <label for="purpose">Purpose:</label>
                    <input type="text" id="purpose" name="purpose" required placeholder="Enter purpose of certificate">
                </div>
                
                <div class="form-group">
                    <label for="issuedBy">Issued By:</label>
                    <input type="text" id="issuedBy" name="issued_by" required placeholder="Enter name of issuing officer">
                </div>
                
                <div class="button-group">
                    <button type="submit" class="text-button">Issue Certificate</button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                </div>
            </form>
            
            <div style="flex: 0.7; display: flex; justify-content: center; align-items: center;">
                <img src="../assets/images/logo-cupangwest.png" alt="Barangay Logo" style="width: 400px; height: auto;">
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close-button" id="closeDeleteModal">&times;</span>
        <div class="delete-confirm-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this certificate record? This action cannot be undone.</p>
            <div class="button-group">
                <button id="confirmDeleteBtn" class="delete-btn">Yes, Delete</button>
                <button id="cancelDeleteBtn" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal elements
    const modal = document.getElementById('certificateModal');
    const closeButton = document.querySelector('.close-button');
    const deleteConfirmModal = document.getElementById('deleteConfirmModal');
    
    // Add debounce search event listener
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                searchCertificates();
            }, 500); // 500ms delay
        });
    }
    
    // Function to open modal for certificate issuance
    function issueCertificate() {
        modal.style.display = 'block';
    }
    
    // Function to close modal
    function closeModal() {
        modal.style.display = 'none';
        deleteConfirmModal.style.display = 'none';
    }
    
    // Search certificates functionality
    function searchCertificates() {
        const searchInput = document.getElementById('searchInput');
        const searchTerm = searchInput.value.trim();
        const tableBody = document.getElementById('certificateTableBody');
        const pagination = document.querySelector('.pagination');
        
        // If search term is empty, reload the page to restore pagination
        if (searchTerm === '') {
            if (window.location.href.includes('?')) {
                window.location.href = window.location.href.split('?')[0];
            } else {
                window.location.reload();
            }
            return;
        }
        
        // Hide pagination while searching
        if (pagination) {
            pagination.style.display = 'none';
        }
        
        // Show loading indicator
        tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Searching...</td></tr>';
        
        // Fetch results from server
        fetch(`../controllers/search-certificates.php?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear the table
                    tableBody.innerHTML = '';
                    
                    if (data.certificates.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No certificates found</td></tr>';
                        return;
                    }
                    
                    // Populate table with search results
                    data.certificates.forEach((certificate, index) => {
                        const row = document.createElement('tr');
                        
                        // No column
                        const noCell = document.createElement('td');
                        noCell.textContent = index + 1;
                        row.appendChild(noCell);
                        
                        // Name column
                        const nameCell = document.createElement('td');
                        nameCell.textContent = certificate.resident_name;
                        row.appendChild(nameCell);
                        
                        // Certificate Type column
                        const typeCell = document.createElement('td');
                        typeCell.textContent = certificate.certificate_type;
                        row.appendChild(typeCell);
                        
                        // Purpose column
                        const purposeCell = document.createElement('td');
                        purposeCell.textContent = certificate.purpose;
                        row.appendChild(purposeCell);
                        
                        // Issue Date column
                        const dateCell = document.createElement('td');
                        dateCell.textContent = certificate.issue_date;
                        row.appendChild(dateCell);
                        
                        // Issued By column
                        const issuedByCell = document.createElement('td');
                        issuedByCell.textContent = certificate.issued_by;
                        row.appendChild(issuedByCell);
                        
                        // Actions column
                        const actionsCell = document.createElement('td');
                        actionsCell.className = 'action-buttons';
                        
                        // Print button
                        const printBtn = document.createElement('button');
                        printBtn.className = 'icon-button view-btn';
                        printBtn.setAttribute('onclick', `printCertificate(${certificate.id})`);
                        printBtn.title = 'Print Certificate';
                        printBtn.innerHTML = '<i class="fas fa-print"></i>';
                        actionsCell.appendChild(printBtn);
                        
                        // Delete button
                        const deleteBtn = document.createElement('button');
                        deleteBtn.className = 'icon-button delete-btn';
                        deleteBtn.setAttribute('onclick', `deleteCertificate(${certificate.id})`);
                        deleteBtn.title = 'Delete Certificate';
                        deleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
                        actionsCell.appendChild(deleteBtn);
                        
                        row.appendChild(actionsCell);
                        tableBody.appendChild(row);
                    });
                } else {
                    tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center;">Error: ${data.message}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Error searching certificates</td></tr>';
            });
    }
    
    // Print certificate
    function printCertificate(id) {
        window.open('partials/certificate-print.php?id=' + id, '_blank');
    }
    
    // Delete certificate
    function deleteCertificate(id) {
        deleteConfirmModal.style.display = 'block';
        
        document.getElementById('confirmDeleteBtn').onclick = function() {
            window.location.href = '../controllers/certificate-delete.php?id=' + id;
        };
        
        document.getElementById('cancelDeleteBtn').onclick = closeModal;
    }
</script>

<?php require("partials/foot.php"); ?> 