<?php
include("../../database.php");
$conn = getDatabaseConnection();

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

<div class="blotter-add-container">
    <h2 class="view-header">File New Blotter Record</h2>
    
    <form id="blotterAddForm" method="post" action="/barangay-information-system/controllers/save-blotter.php">
        <div class="form-section">
            <h3>Incident Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="incident_type">Incident Type*</label>
                    <input type="text" id="incident_type" name="incident_type" required>
                </div>
                <div class="form-group">
                    <label for="incident_date">Incident Date*</label>
                    <input type="date" id="incident_date" name="incident_date" required>
                </div>
                <div class="form-group">
                    <label for="incident_time">Incident Time*</label>
                    <input type="time" id="incident_time" name="incident_time" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="incident_location">Incident Location*</label>
                    <input type="text" id="incident_location" name="incident_location" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="incident_details">Incident Details*</label>
                    <textarea id="incident_details" name="incident_details" rows="5" required></textarea>
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
                                    data-contact="<?= htmlspecialchars($resident['contact'] ?? '') ?>">
                                <?= htmlspecialchars($resident['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="complainant_name">Complainant Name (Auto-filled)</label>
                    <input type="text" id="complainant_name" name="complainant_name" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="complainant_address">Complainant Address*</label>
                    <input type="text" id="complainant_address" name="complainant_address" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="complainant_contact">Complainant Contact</label>
                    <input type="text" id="complainant_contact" name="complainant_contact">
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
                                    data-contact="<?= htmlspecialchars($resident['contact'] ?? '') ?>">
                                <?= htmlspecialchars($resident['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="respondent_name">Respondent Name (Auto-filled)</label>
                    <input type="text" id="respondent_name" name="respondent_name" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="respondent_address">Respondent Address*</label>
                    <input type="text" id="respondent_address" name="respondent_address" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="respondent_contact">Respondent Contact</label>
                    <input type="text" id="respondent_contact" name="respondent_contact">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Status and Action</h3>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="status">Status*</label>
                    <select id="status" name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Dismissed">Dismissed</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="action_taken">Action Taken</label>
                    <textarea id="action_taken" name="action_taken" rows="3"></textarea>
                </div>
            </div>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="submit-btn">Save Blotter Record</button>
            <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
        </div>
    </form>
</div>

<style>
.blotter-add-container {
    padding: 20px;
    max-width: 900px;
    margin: 0 auto;
    color: white;
    max-height: 80vh;
    overflow-y: auto;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.blotter-add-container::-webkit-scrollbar {
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
.form-group input[type="time"] {
    width: 100%;
    box-sizing: border-box;
}

/* Fix the first row layout */
.form-section:first-of-type .form-row:first-of-type .form-group:first-of-type {
    flex: 2;
}

.form-section:first-of-type .form-row:first-of-type .form-group:nth-of-type(2),
.form-section:first-of-type .form-row:first-of-type .form-group:nth-of-type(3) {
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

/* We've moved these styles to the main blotter.php file for better consistency */

@media (max-width: 768px) {
    .form-group {
        flex-basis: 100%;
    }
    
    #blotterModal .modal-content {
        width: 95%;
        margin: 2vh auto;
    }

    /* First row element adjustments for mobile */
    .form-section:first-of-type .form-row:first-of-type .form-group:first-of-type,
    .form-section:first-of-type .form-row:first-of-type .form-group:nth-of-type(2),
    .form-section:first-of-type .form-row:first-of-type .form-group:nth-of-type(3) {
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

// Set today's date as default for incident date
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('incident_date').value = today;
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