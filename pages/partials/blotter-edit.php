<?php
include("../../database.php");
$conn = getDatabaseConnection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<p class="error-message">No blotter ID provided</p>';
    exit;
}

$id = intval($_GET['id']);

// Get blotter information
$blotterQuery = $conn->prepare("
    SELECT * FROM blotter_records WHERE id = ? AND archived = 0
");
$blotterQuery->bind_param("i", $id);
$blotterQuery->execute();
$blotterResult = $blotterQuery->get_result();

if ($blotterResult->num_rows === 0) {
    echo '<p class="error-message">Blotter record not found</p>';
    exit;
}

$blotter = $blotterResult->fetch_assoc();
$blotterQuery->close();

// Get residents for dropdown
$residents = [];
$residentQuery = "SELECT id, surname, firstname, middlename, address, contact FROM residents WHERE archived = 0 ORDER BY surname, firstname";
$residentResult = mysqli_query($conn, $residentQuery);
if ($residentResult) {
    while ($row = mysqli_fetch_assoc($residentResult)) {
        $row['full_name'] = $row['surname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'];
        $residents[] = $row;
    }
}
?>

<div class="blotter-edit-container">
    <h2 class="view-header">Edit Blotter Record</h2>
    
    <form id="blotterEditForm" method="post" action="/barangay-information-system/controllers/update-blotter.php">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="form-section">
            <h3>Incident Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="blotter_id">Blotter ID</label>
                    <input type="text" id="blotter_id" name="blotter_id" value="<?= htmlspecialchars($blotter['blotter_id']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="incident_type">Incident Type*</label>
                    <input type="text" id="incident_type" name="incident_type" value="<?= htmlspecialchars($blotter['incident_type']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="incident_date">Incident Date*</label>
                    <input type="date" id="incident_date" name="incident_date" value="<?= htmlspecialchars($blotter['incident_date']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="incident_time">Incident Time*</label>
                    <input type="time" id="incident_time" name="incident_time" value="<?= htmlspecialchars($blotter['incident_time']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="incident_location">Incident Location*</label>
                    <input type="text" id="incident_location" name="incident_location" value="<?= htmlspecialchars($blotter['incident_location']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="incident_details">Incident Details*</label>
                    <textarea id="incident_details" name="incident_details" rows="5" required><?= htmlspecialchars($blotter['incident_details']) ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Complainant Information</h3>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="complainant_resident_id">Select Complainant from Residents*</label>
                    <select id="complainant_resident_id" name="complainant_resident_id" onchange="fillComplainantInfo()" required>
                        <option value="">-- Select Resident --</option>
                        <?php foreach ($residents as $resident): ?>
                            <option value="<?= $resident['id'] ?>" 
                                    data-name="<?= htmlspecialchars($resident['full_name']) ?>"
                                    data-address="<?= htmlspecialchars($resident['address'] ?? '') ?>"
                                    data-contact="<?= htmlspecialchars($resident['contact'] ?? '') ?>"
                                    <?= ($blotter['complainant_resident_id'] == $resident['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($resident['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="complainant_name">Complainant Name (Auto-filled)</label>
                    <input type="text" id="complainant_name" name="complainant_name" value="<?= htmlspecialchars($blotter['complainant_name']) ?>" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="complainant_address">Complainant Address*</label>
                    <input type="text" id="complainant_address" name="complainant_address" value="<?= htmlspecialchars($blotter['complainant_address']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="complainant_contact">Complainant Contact</label>
                    <input type="text" id="complainant_contact" name="complainant_contact" value="<?= htmlspecialchars($blotter['complainant_contact'] ?? '') ?>">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Respondent Information</h3>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="respondent_resident_id">Select Respondent from Residents*</label>
                    <select id="respondent_resident_id" name="respondent_resident_id" onchange="fillRespondentInfo()" required>
                        <option value="">-- Select Resident --</option>
                        <?php foreach ($residents as $resident): ?>
                            <option value="<?= $resident['id'] ?>" 
                                    data-name="<?= htmlspecialchars($resident['full_name']) ?>"
                                    data-address="<?= htmlspecialchars($resident['address'] ?? '') ?>"
                                    data-contact="<?= htmlspecialchars($resident['contact'] ?? '') ?>"
                                    <?= ($blotter['respondent_resident_id'] == $resident['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($resident['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="respondent_name">Respondent Name (Auto-filled)</label>
                    <input type="text" id="respondent_name" name="respondent_name" value="<?= htmlspecialchars($blotter['respondent_name']) ?>" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="respondent_address">Respondent Address*</label>
                    <input type="text" id="respondent_address" name="respondent_address" value="<?= htmlspecialchars($blotter['respondent_address']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="respondent_contact">Respondent Contact</label>
                    <input type="text" id="respondent_contact" name="respondent_contact" value="<?= htmlspecialchars($blotter['respondent_contact'] ?? '') ?>">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Status and Action</h3>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="status">Status*</label>
                    <select id="status" name="status" required>
                        <option value="Pending" <?= ($blotter['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="Ongoing" <?= ($blotter['status'] == 'Ongoing') ? 'selected' : '' ?>>Ongoing</option>
                        <option value="Resolved" <?= ($blotter['status'] == 'Resolved') ? 'selected' : '' ?>>Resolved</option>
                        <option value="Dismissed" <?= ($blotter['status'] == 'Dismissed') ? 'selected' : '' ?>>Dismissed</option>
                    </select>
                </div>
                <?php if ($blotter['status'] == 'Resolved' || $blotter['status'] == 'Dismissed'): ?>
                <div class="form-group">
                    <label for="date_resolved">Date Resolved</label>
                    <input type="datetime-local" id="date_resolved" name="date_resolved" 
                        value="<?= !empty($blotter['date_resolved']) ? date('Y-m-d\TH:i', strtotime($blotter['date_resolved'])) : '' ?>">
                </div>
                <?php endif; ?>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="action_taken">Action Taken</label>
                    <textarea id="action_taken" name="action_taken" rows="3"><?= htmlspecialchars($blotter['action_taken'] ?? '') ?></textarea>
                </div>
            </div>
            <?php if ($blotter['status'] == 'Resolved' || $blotter['status'] == 'Dismissed'): ?>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="resolution_details">Resolution Details</label>
                    <textarea id="resolution_details" name="resolution_details" rows="3"><?= htmlspecialchars($blotter['resolution_details'] ?? '') ?></textarea>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="submit-btn">Update Blotter Record</button>
            <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
        </div>
    </form>
</div>

<style>
.blotter-edit-container {
    padding: 20px;
    max-width: 900px;
    margin: 0 auto;
    color: white;
    max-height: 80vh;
    overflow-y: auto;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.blotter-edit-container::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

.view-header {
    text-align: center;
    color: white;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #4CAF50;
}

.form-section {
    margin-bottom: 25px;
    background-color: rgba(255, 255, 255, 0.1);
    padding: 15px;
    border-radius: 5px;
}

.form-section h3 {
    color: white;
    font-size: 1.1rem;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
    align-items: flex-start;
}

.form-group {
    flex: 1;
    min-width: 180px;
}

/* Make the date and time fields have a fixed width */
.form-group input[type="date"],
.form-group input[type="time"],
.form-group input[type="datetime-local"] {
    width: 100%;
    box-sizing: border-box;
}

/* Fix the first row layout for blotter_id and incident_type */
.form-section:first-of-type .form-row:first-of-type .form-group:first-of-type,
.form-section:first-of-type .form-row:first-of-type .form-group:nth-of-type(2) {
    flex: 1;
}

/* Fix the second row layout for date and time */
.form-section:first-of-type .form-row:nth-of-type(2) .form-group:first-of-type,
.form-section:first-of-type .form-row:nth-of-type(2) .form-group:nth-of-type(2) {
    flex: 1;
}

.form-group.full-width {
    flex-basis: 100%;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #4CAF50;
    font-weight: bold;
    font-size: 0.9rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    box-sizing: border-box;
    height: 40px;
    font-size: 14px;
}

/* Add styling for dropdown options */
.form-group select option {
    background-color: #3a4468;
    color: white;
}

.form-group input[readonly] {
    background-color: rgba(255, 255, 255, 0.05);
    color: #aaa;
}

.form-group textarea {
    resize: vertical;
    height: auto;
    min-height: 80px;
}

.form-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 20px;
}

.submit-btn, .cancel-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s;
}

.submit-btn {
    background-color: #4CAF50;
    color: white;
}

.submit-btn:hover {
    background-color: #45a049;
    transform: translateY(-2px);
}

.cancel-btn {
    background-color: #f44336;
    color: white;
}

.cancel-btn:hover {
    background-color: #d32f2f;
    transform: translateY(-2px);
}

.error-message {
    color: #ff6b6b;
    text-align: center;
    font-weight: bold;
    padding: 20px;
}

@media (max-width: 768px) {
    .form-group {
        flex-basis: 100%;
    }

    /* First row element adjustments for mobile */
    .form-section:first-of-type .form-row:first-of-type .form-group:first-of-type,
    .form-section:first-of-type .form-row:first-of-type .form-group:nth-of-type(2),
    .form-section:first-of-type .form-row:nth-of-type(2) .form-group:first-of-type,
    .form-section:first-of-type .form-row:nth-of-type(2) .form-group:nth-of-type(2) {
        flex: 1 0 100%;
    }
    
    /* Make sure date_resolved is full width on mobile */
    .form-section:last-child .form-row:first-child .form-group {
        flex: 1 0 100%;
    }
}
</style>

<script>
function closeModal() {
    // Use window.parent to access the parent window's modal
    if (window.parent && window.parent.document) {
        const parentModal = window.parent.document.getElementById('blotterModal');
        if (parentModal) parentModal.style.display = 'none';
    } else {
        // Fallback to looking for the modal in the current document
        const modal = document.getElementById('blotterModal');
        if (modal) modal.style.display = 'none';
        
        // Alternative approach - tell the parent window to close the modal
        if (window.parent) {
            window.parent.postMessage('closeBlotterModal', '*');
        }
    }
}

// Show resolution fields when status changes to Resolved or Dismissed
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    
    statusSelect.addEventListener('change', function() {
        const status = this.value;
        const resolutionSection = document.querySelector('.form-section:last-child');
        
        if (status === 'Resolved' || status === 'Dismissed') {
            // Add date resolved field if it doesn't exist
            if (!document.getElementById('date_resolved')) {
                const dateField = document.createElement('div');
                dateField.className = 'form-group full-width';
                dateField.innerHTML = `
                    <label for="date_resolved">Date Resolved</label>
                    <input type="datetime-local" id="date_resolved" name="date_resolved" 
                        style="width: 100%; box-sizing: border-box; height: 40px;"
                        value="${new Date().toISOString().slice(0, 16)}">
                `;
                document.querySelector('.form-section:last-child .form-row:first-child').appendChild(dateField);
            }
            
            // Add resolution details field if it doesn't exist
            if (!document.getElementById('resolution_details')) {
                const detailsRow = document.createElement('div');
                detailsRow.className = 'form-row';
                detailsRow.innerHTML = `
                    <div class="form-group full-width">
                        <label for="resolution_details">Resolution Details</label>
                        <textarea id="resolution_details" name="resolution_details" rows="3"></textarea>
                    </div>
                `;
                resolutionSection.insertBefore(detailsRow, document.querySelector('.form-buttons').parentNode);
            }
        } else {
            // Remove date resolved field
            const dateField = document.getElementById('date_resolved');
            if (dateField) {
                dateField.parentNode.remove();
            }
            
            // Remove resolution details field
            const detailsField = document.getElementById('resolution_details');
            if (detailsField) {
                detailsField.parentNode.parentNode.remove();
            }
        }
    });
});

// Function to fill complainant information when resident is selected
function fillComplainantInfo() {
    const select = document.getElementById('complainant_resident_id');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        document.getElementById('complainant_name').value = selectedOption.getAttribute('data-name') || '';
        document.getElementById('complainant_address').value = selectedOption.getAttribute('data-address') || '';
        document.getElementById('complainant_contact').value = selectedOption.getAttribute('data-contact') || '';
    } else {
        // Clear fields if no resident selected
        document.getElementById('complainant_name').value = '';
        document.getElementById('complainant_address').value = '';
        document.getElementById('complainant_contact').value = '';
    }
}

// Function to fill respondent information when resident is selected
function fillRespondentInfo() {
    const select = document.getElementById('respondent_resident_id');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        document.getElementById('respondent_name').value = selectedOption.getAttribute('data-name') || '';
        document.getElementById('respondent_address').value = selectedOption.getAttribute('data-address') || '';
        document.getElementById('respondent_contact').value = selectedOption.getAttribute('data-contact') || '';
    } else {
        // Clear fields if no resident selected
        document.getElementById('respondent_name').value = '';
        document.getElementById('respondent_address').value = '';
        document.getElementById('respondent_contact').value = '';
    }
}
</script> 