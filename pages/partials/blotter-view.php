<?php
include("../../database.php");
$conn = getDatabaseConnection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<p class="error-message">No blotter ID provided</p>';
    exit;
}

$id = intval($_GET['id']);

// Get blotter information (excluding archived)
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

// Format dates for display
$incidentDate = date('F d, Y', strtotime($blotter['incident_date']));
$incidentTime = date('h:i A', strtotime($blotter['incident_time']));
$dateReported = date('F d, Y h:i A', strtotime($blotter['date_reported']));
$dateResolved = !empty($blotter['date_resolved']) ? date('F d, Y h:i A', strtotime($blotter['date_resolved'])) : 'N/A';
?>

<div class="blotter-details-container">
    <h2 class="view-header">Blotter Record Details</h2>
    
    <div class="details-section">
        <h3>Basic Information</h3>
        <div class="details-grid">
            <div class="detail-item">
                <strong>Blotter ID:</strong>
                <span><?= htmlspecialchars($blotter['blotter_id']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Incident Type:</strong>
                <span><?= htmlspecialchars($blotter['incident_type']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Date Reported:</strong>
                <span><?= htmlspecialchars($dateReported) ?></span>
            </div>
            <div class="detail-item">
                <strong>Status:</strong>
                <span class="status-badge status-<?= strtolower($blotter['status']) ?>">
                    <?= htmlspecialchars($blotter['status']) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="details-section">
        <h3>Incident Details</h3>
        <div class="details-grid">
            <div class="detail-item">
                <strong>Incident Date:</strong>
                <span><?= htmlspecialchars($incidentDate) ?></span>
            </div>
            <div class="detail-item">
                <strong>Incident Time:</strong>
                <span><?= htmlspecialchars($incidentTime) ?></span>
            </div>
            <div class="detail-item">
                <strong>Incident Location:</strong>
                <span><?= htmlspecialchars($blotter['incident_location']) ?></span>
            </div>
        </div>
        <div class="detail-item full-width">
            <strong>Incident Description:</strong>
            <span class="multiline-text"><?= nl2br(htmlspecialchars($blotter['incident_details'])) ?></span>
        </div>
    </div>

    <div class="details-section">
        <h3>Complainant Information</h3>
        <div class="details-grid">
            <div class="detail-item">
                <strong>Name:</strong>
                <span><?= htmlspecialchars($blotter['complainant_name']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Address:</strong>
                <span><?= htmlspecialchars($blotter['complainant_address']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Contact:</strong>
                <span><?= htmlspecialchars($blotter['complainant_contact'] ?? 'N/A') ?></span>
            </div>
        </div>
    </div>

    <div class="details-section">
        <h3>Respondent Information</h3>
        <div class="details-grid">
            <div class="detail-item">
                <strong>Name:</strong>
                <span><?= htmlspecialchars($blotter['respondent_name']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Address:</strong>
                <span><?= htmlspecialchars($blotter['respondent_address']) ?></span>
            </div>
            <div class="detail-item">
                <strong>Contact:</strong>
                <span><?= htmlspecialchars($blotter['respondent_contact'] ?? 'N/A') ?></span>
            </div>
        </div>
    </div>

    <div class="details-section">
        <h3>Resolution Information</h3>
        <div class="details-grid">
            <div class="detail-item">
                <strong>Date Resolved:</strong>
                <span><?= htmlspecialchars($dateResolved) ?></span>
            </div>
        </div>
        <div class="detail-item full-width">
            <strong>Action Taken:</strong>
            <span class="multiline-text"><?= nl2br(htmlspecialchars($blotter['action_taken'] ?? 'N/A')) ?></span>
        </div>
        <div class="detail-item full-width">
            <strong>Resolution Details:</strong>
            <span class="multiline-text"><?= nl2br(htmlspecialchars($blotter['resolution_details'] ?? 'N/A')) ?></span>
        </div>
    </div>

    <div class="action-buttons-container">
        <button class="edit-btn" onclick="editBlotter(<?= $id ?>)">Edit Blotter</button>
        <?php if ($blotter['status'] != 'Resolved' && $blotter['status'] != 'Dismissed'): ?>
        <button class="resolve-btn" onclick="resolveBlotter(<?= $id ?>)">Mark as Resolved</button>
        <?php endif; ?>
    </div>
</div>

<style>
.blotter-details-container {
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;
    color: white;
    max-height: 80vh;
    overflow-y: auto;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.blotter-details-container::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
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

.detail-item.full-width {
    grid-column: 1 / -1;
    margin-top: 15px;
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

.multiline-text {
    min-height: 60px;
    white-space: pre-wrap;
    line-height: 1.5;
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

.edit-btn, .resolve-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: bold;
}

.edit-btn {
    background-color: #4CAF50;
    color: white;
}

.edit-btn:hover {
    background-color: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.resolve-btn {
    background-color: #2196F3;
    color: white;
}

.resolve-btn:hover {
    background-color: #0b7dda;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.error-message {
    color: #ff6b6b;
    text-align: center;
    font-weight: bold;
    padding: 20px;
}

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

@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
}
</style> 