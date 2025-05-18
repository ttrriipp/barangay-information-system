<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
require("../database.php");
$conn = getDatabaseConnection();

// Get counts and statistics
$totalResidents = 0;
$query = "SELECT COUNT(*) as total FROM residents";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalResidents = $row['total'];
}

$totalCertificates = 0;
$query = "SELECT COUNT(*) as total FROM certificates";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalCertificates = $row['total'];
}

$totalBlotters = 0;
$query = "SELECT COUNT(*) as total FROM blotter_records";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalBlotters = $row['total'];
}

// Estimate households (in a real system, this would be from actual data)
$totalHouseholds = round($totalResidents / 3);

// Get gender distribution
$maleCount = 0;
$femaleCount = 0;
$query = "SELECT sex, COUNT(*) as count FROM residents GROUP BY sex";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (strtolower($row['sex']) == 'male') {
            $maleCount = $row['count'];
        } else if (strtolower($row['sex']) == 'female') {
            $femaleCount = $row['count'];
        }
    }
}

// Get age distribution
$ageRanges = [
    '0-12' => [0, 12],
    '13-19' => [13, 19],
    '20-35' => [20, 35],
    '36-59' => [36, 59],
    '60+' => [60, 200]
];

$ageCounts = [];
foreach ($ageRanges as $label => $range) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM residents WHERE age >= ? AND age <= ?");
    $min = $range[0];
    $max = $range[1];
    $stmt->bind_param("ii", $min, $max);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $ageCounts[$label] = $count;
    $stmt->close();
}

// Get civil status distribution
$civilStatusData = [];
$civilStatusLabels = [];
$query = "SELECT civil_status, COUNT(*) as count FROM residents GROUP BY civil_status";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['civil_status'])) {
            $civilStatusLabels[] = $row['civil_status'];
            $civilStatusData[] = $row['count'];
        }
    }
}

// Get blotter status counts
$pendingBlotters = 0;
$resolvedBlotters = 0;
$scheduledBlotters = 0;
$query = "SELECT status, COUNT(*) as count FROM blotter_records GROUP BY status";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (strtolower($row['status']) == 'pending') {
            $pendingBlotters = $row['count'];
        } else if (strtolower($row['status']) == 'resolved') {
            $resolvedBlotters = $row['count'];
        } else if (strtolower($row['status']) == 'scheduled') {
            $scheduledBlotters = $row['count'];
        }
    }
}

mysqli_close($conn);

$style = 'main.css';
$additionalStyles = ['dashboard.css'];
require("partials/head.php");
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php require("partials/sidebar.php") ?>

<div class="main-content">
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <div class="date-display"><?php echo date("l, F j, Y"); ?></div>
    </div>
    
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-info">
                <h2>Total Residents</h2>
                <div class="stat-value"><?php echo number_format($totalResidents); ?></div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-info">
                <h2>Certificates Issued</h2>
                <div class="stat-value"><?php echo number_format($totalCertificates); ?></div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-certificate"></i>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-info">
                <h2>Blotter Cases</h2>
                <div class="stat-value"><?php echo number_format($totalBlotters); ?></div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-info">
                <h2>Households</h2>
                <div class="stat-value"><?php echo number_format($totalHouseholds); ?></div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-home"></i>
            </div>
        </div>
    </div>
    
    <div class="dashboard-content">
        <div class="content-card resident-demographics">
            <h2>Resident Demographics</h2>
            
            <div class="demographic-item">
                <div class="demographic-label">Gender Distribution</div>
                <div class="demographic-bar">
                    <div class="bar-male" style="width: <?php echo ($totalResidents > 0) ? ($maleCount / $totalResidents * 100) : 0; ?>%">
                        <span>Male: <?php echo number_format($maleCount); ?></span>
                    </div>
                    <div class="bar-female" style="width: <?php echo ($totalResidents > 0) ? ($femaleCount / $totalResidents * 100) : 0; ?>%">
                        <span>Female: <?php echo number_format($femaleCount); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="demographic-item">
                <div class="demographic-label">Age Distribution</div>
                <div class="chart-container" style="height: 180px; max-width: 100%;">
                    <canvas id="ageDistributionChart"></canvas>
                </div>
            </div>
            
            <div class="demographic-item">
                <div class="demographic-label">Civil Status</div>
                <div class="chart-container" style="height: 200px; max-width: 100%;">
                    <canvas id="civilStatusChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="content-card status-summary">
            <h2>Status Summary</h2>
            
            <div class="status-card">
                <h3>Blotter Cases</h3>
                <div class="status-badges">
                    <div class="status-badge pending">
                        <span class="badge-count"><?php echo $pendingBlotters; ?></span>
                        <span class="badge-label">Pending</span>
                    </div>
                    <div class="status-badge scheduled">
                        <span class="badge-count"><?php echo $scheduledBlotters; ?></span>
                        <span class="badge-label">Scheduled</span>
                    </div>
                    <div class="status-badge resolved">
                        <span class="badge-count"><?php echo $resolvedBlotters; ?></span>
                        <span class="badge-label">Resolved</span>
                    </div>
                </div>
            </div>
            
            <div class="status-card">
                <h3>Certificate Types Issued</h3>
                <div class="certificate-chart">
                    <div class="cert-bar">
                        <div class="cert-fill" style="width: 65%"></div>
                        <span class="cert-label">Residency</span>
                        <span class="cert-value">65%</span>
                    </div>
                    <div class="cert-bar">
                        <div class="cert-fill" style="width: 20%"></div>
                        <span class="cert-label">Indigency</span>
                        <span class="cert-value">20%</span>
                    </div>
                    <div class="cert-bar">
                        <div class="cert-fill" style="width: 15%"></div>
                        <span class="cert-label">Clearance</span>
                        <span class="cert-value">15%</span>
                    </div>
                </div>
            </div>
            
            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="residents.php" class="action-button">
                        <i class="fas fa-user-plus"></i>
                        <span>Add Resident</span>
                    </a>
                    <a href="certificates.php" class="action-button">
                        <i class="fas fa-file-alt"></i>
                        <span>Issue Certificate</span>
                    </a>
                    <a href="blotter.php" class="action-button">
                        <i class="fas fa-clipboard"></i>
                        <span>File Blotter</span>
                    </a>
                    <a href="reports.php" class="action-button">
                        <i class="fas fa-chart-bar"></i>
                        <span>Generate Report</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Age Distribution Chart
const ageLabels = <?= json_encode(array_keys($ageCounts)) ?>;
const ageData = <?= json_encode(array_values($ageCounts)) ?>;

const ageCtx = document.getElementById('ageDistributionChart').getContext('2d');
const ageDistributionChart = new Chart(ageCtx, {
    type: 'bar',
    data: {
        labels: ageLabels,
        datasets: [{
            label: 'Residents',
            data: ageData,
            backgroundColor: [
                '#1a237e', '#3949ab', '#5c6bc0', '#7986cb', '#9fa8da'
            ],
            borderColor: '#1a237e',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                precision: 0,
                ticks: {
                    maxRotation: 0,
                    autoSkip: true
                }
            },
            x: {
                ticks: {
                    maxRotation: 0,
                    autoSkip: true
                }
            }
        }
    }
});

// Civil Status Chart
const civilStatusLabels = <?= !empty($civilStatusLabels) ? json_encode($civilStatusLabels) : "['Married', 'Single', 'Others']" ?>;
const civilStatusData = <?= !empty($civilStatusData) ? json_encode($civilStatusData) : "[55, 30, 15]" ?>;
const civilStatusColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];

const civilCtx = document.getElementById('civilStatusChart').getContext('2d');
const civilStatusChart = new Chart(civilCtx, {
    type: 'pie',
    data: {
        labels: civilStatusLabels,
        datasets: [{
            data: civilStatusData,
            backgroundColor: civilStatusColors.slice(0, civilStatusLabels.length),
            borderColor: '#ffffff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    boxWidth: 12,
                    font: {
                        size: 11
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
</script>

<style>
/* Dashboard specific styles */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.date-display {
    font-size: 1.1rem;
    color: #6c757d;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.stat-info h2 {
    font-size: 1rem;
    margin: 0 0 10px 0;
    color: #333;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-change {
    font-size: 0.85rem;
    color: #6c757d;
}

.stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #4e73df;
}

.dashboard-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    overflow-x: hidden;
    width: 100%;
}

.content-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    max-width: 100%;
    overflow: hidden;
}

.content-card h2 {
    margin-top: 0;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

/* Resident Demographics */
.demographic-item {
    margin-bottom: 25px;
}

.demographic-label {
    font-weight: bold;
    margin-bottom: 10px;
}

.demographic-bar {
    display: flex;
    height: 30px;
    border-radius: 4px;
    overflow: hidden;
}

.bar-male, .bar-female {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: width 0.5s ease;
}

.bar-male {
    background-color: #4e73df;
}

.bar-female {
    background-color: #e74a3b;
}

.chart-container {
    width: 100%;
    position: relative;
    overflow: hidden;
}

/* Status Summary */
.status-card {
    margin-bottom: 25px;
}

.status-card h3 {
    margin-top: 0;
    margin-bottom: 15px;
}

.status-badges {
    display: flex;
    justify-content: space-between;
}

.status-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    width: 30%;
}

.badge-count {
    font-size: 1.8rem;
    font-weight: bold;
    color: white;
}

.badge-label {
    font-size: 0.8rem;
    color: white;
    margin-top: 5px;
}

.pending {
    background-color: #f6c23e;
}

.scheduled {
    background-color: #4e73df;
}

.resolved {
    background-color: #1cc88a;
}

.certificate-chart {
    margin-top: 15px;
}

.cert-bar {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    height: 30px;
}

.cert-fill {
    height: 100%;
    background-color: #4e73df;
    border-radius: 4px;
    margin-right: 10px;
}

.cert-label {
    flex-grow: 1;
}

.cert-value {
    font-weight: bold;
}

.quick-actions {
    margin-top: 25px;
}

.quick-actions h3 {
    margin-top: 0;
    margin-bottom: 15px;
}

.action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.action-button {
    display: flex;
    align-items: center;
    padding: 12px;
    background-color: #f8f9fc;
    border-radius: 8px;
    color: #5a5c69;
    text-decoration: none;
    transition: all 0.2s;
}

.action-button:hover {
    background-color: #4e73df;
    color: white;
}

.action-button i {
    margin-right: 10px;
    font-size: 1.2rem;
}

@media (max-width: 992px) {
    .dashboard-content {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require("partials/foot.php");
