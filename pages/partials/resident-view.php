<?php
include("../../database.php");
$conn = getDatabaseConnection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<p class="error-message">No resident ID provided</p>';
    exit;
}

$id = intval($_GET['id']);

// Get resident information
$query = $conn->prepare("
    SELECT 
        r.id, 
        r.surname, 
        r.firstname, 
        r.middlename,
        CONCAT(r.surname, ', ', r.firstname, ' ', r.middlename) AS fullname,
        r.birthdate,
        TIMESTAMPDIFF(YEAR, r.birthdate, CURDATE()) AS age, 
        r.sex, 
        r.address, 
        r.contact,
        r.email,
        r.civil_status,
        r.occupation,
        r.education,
        r.voter_status,
        r.pwd_status,
        r.philhealth_status,
        r.`4ps_status`,
        r.emergency_contact_name,
        r.emergency_contact_number,
        r.blood_type,
        r.religion,
        r.nationality,
        r.date_of_residency
    FROM residents r
    WHERE r.id = ? AND r.archived = 0
");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo '<p class="error-message">Resident not found</p>';
    exit;
}

$resident = $result->fetch_assoc();
$query->close();

// Format dates for display
$birthdate = !empty($resident['birthdate']) ? date('F d, Y', strtotime($resident['birthdate'])) : 'N/A';
$date_of_residency = !empty($resident['date_of_residency']) ? date('F d, Y', strtotime($resident['date_of_residency'])) : 'N/A';
?>

<div class="resident-details-container">
    <h2 class="view-header"><?= htmlspecialchars($resident['fullname']) ?></h2>

    <div class="details-section">
        <h3>Basic Information</h3>
        <div class="details-grid">
            <div class="detail-item">
                <strong>Surname:</strong>
                <span><?= htmlspecialchars($resident['surname']) ?></span>
            </div>
            <div class="detail-item">
                <strong>First Name:</strong>
                <span><?= htmlspecialchars($resident['firstname']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Middle Name:</strong>
                <span><?= htmlspecialchars($resident['middlename'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>Age:</strong>
                <span><?= htmlspecialchars($resident['age']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Date of Birth:</strong>
                <span><?= $birthdate ?></span>
            </div>
            <div class="detail-item">
                <strong>Sex:</strong>
                <span><?= htmlspecialchars($resident['sex']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Civil Status:</strong>
                <span><?= htmlspecialchars($resident['civil_status'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>Nationality:</strong>
                <span><?= htmlspecialchars($resident['nationality'] ?? 'Filipino') ?></span>
            </div>
        </div>
    </div>

    <div class="details-section">
        <h3>Contact Information</h3>
        <div class="details-grid">
            <div class="detail-item">
                <strong>Address:</strong>
                <span><?= htmlspecialchars($resident['address']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Contact Number:</strong>
                <span><?= htmlspecialchars($resident['contact'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>Email:</strong>
                <span><?= htmlspecialchars($resident['email'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>Emergency Contact Name:</strong>
                <span><?= htmlspecialchars($resident['emergency_contact_name'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>Emergency Contact Number:</strong>
                <span><?= htmlspecialchars($resident['emergency_contact_number'] ?? 'N/A') ?></span>
            </div>
        </div>
    </div>

    <div class="details-section">
        <h3>Additional Information</h3>
        <div class="details-grid">
            <div class="detail-item">
                <strong>Occupation:</strong>
                <span><?= htmlspecialchars($resident['occupation'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>Education:</strong>
                <span><?= htmlspecialchars($resident['education'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>Religion:</strong>
                <span><?= htmlspecialchars($resident['religion'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>Blood Type:</strong>
                <span><?= htmlspecialchars($resident['blood_type'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>Date of Residency:</strong>
                <span><?= $date_of_residency ?></span>
            </div>
        </div>
    </div>

    <div class="details-section">
        <h3>Status Information</h3>
        <div class="details-grid">
            <div class="detail-item">
                <strong>Voter Status:</strong>
                <span><?= htmlspecialchars($resident['voter_status'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>PWD Status:</strong>
                <span><?= htmlspecialchars($resident['pwd_status'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>PhilHealth Status:</strong>
                <span><?= htmlspecialchars($resident['philhealth_status'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <strong>4Ps Beneficiary:</strong>
                <span><?= htmlspecialchars($resident['4ps_status'] ?? 'N/A') ?></span>
            </div>
        </div>
    </div>

    <div class="action-buttons-container">
        <button class="edit-btn" onclick="editResident(<?= $id ?>)">Edit Resident</button>
    </div>
</div>

<style>
.resident-details-container {
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;
    color: white;
}

.view-header {
    text-align: center;
    color: white;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #4CAF50;
}

.details-section {
    margin-bottom: 25px;
}

.details-section h3 {
    color: white;
    font-size: 1.2rem;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.detail-item {
    display: flex;
    flex-direction: column;
}

.detail-item strong {
    font-weight: bold;
    color: #4CAF50; /* Light green for labels */
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.detail-item span {
    padding: 8px;
    background-color: rgba(255, 255, 255, 0.1); /* Semi-transparent white */
    border-radius: 4px;
    font-size: 1rem;
    color: white;
}

.action-buttons-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
}

.action-buttons-container button {
    margin: 0 10px;
}

.edit-btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.edit-btn:hover {
    background-color: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.error-message {
    color: #ff6b6b;
    text-align: center;
    font-weight: bold;
    padding: 20px;
}

@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
}
</style> 