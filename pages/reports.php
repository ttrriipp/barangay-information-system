<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
require("../database.php");
$conn = getDatabaseConnection();

$ageRanges = [
    '0-12' => [0, 12],
    '13-19' => [13, 19],
    '20-35' => [20, 35],
    '36-59' => [36, 59],
    '60+' => [60, 200]
];

$ageCounts = [];
foreach ($ageRanges as $label => $range) {
    // FIX: Use the correct table name 'residents'
    $stmt = $conn->prepare("SELECT COUNT(*) FROM residents WHERE age >= ? AND age <= ?");
    $min = $range[0];
    $max = $range[1];
    // For 60+, set max to a high value
    if ($label === '60+') $max = 200;
    $stmt->bind_param("ii", $min, $max);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $ageCounts[$label] = $count;
    $stmt->close();
}

// Get total number of residents
$residentTotal = 0;
$residentStmt = $conn->prepare("SELECT COUNT(*) FROM residents");
$residentStmt->execute();
$residentStmt->bind_result($residentTotal);
$residentStmt->fetch();
$residentStmt->close();

// Get household count per month (last 12 months)
$householdCounts = [];
$householdLabels = [];

$householdStmt = $conn->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) as count
    FROM households
    GROUP BY month
    ORDER BY month ASC
    LIMIT 12
");
$householdStmt->execute();
$householdStmt->bind_result($month, $count);
while ($householdStmt->fetch()) {
    $householdLabels[] = $month;
    $householdCounts[] = $count;
}
$householdStmt->close();

$conn->close();

$style = 'main.css';
require("partials/head.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php require("partials/sidebar.php") ?>
<div class="main-content">
    <div class="header-container">
        <h1>Reports</h1>
    </div>
    <div class="reports-grid">
        <!-- Age Range Bar Graph -->
        <div class="report-card">
          <h2>Residents by Age Range</h2>
          <canvas id="ageRangeChart" width="400" height="400"></canvas>
        </div>
        <!-- Household Line Chart -->
        <div class="report-card">
          <h2>Households Over Time</h2>
          <canvas id="householdLineChart" width="400" height="400"></canvas>
        </div>
        <!-- Total Residents Pie Chart -->
        <div class="report-card">
          <h2>Total Residents</h2>
          <canvas id="residentPieChart" width="400" height="400"></canvas>
        </div>
    </div>
</div>
<script>
const ageLabels = <?= json_encode(array_keys($ageCounts)) ?>;
const ageData = <?= json_encode(array_values($ageCounts)) ?>;

const ctx = document.getElementById('ageRangeChart').getContext('2d');
const ageRangeChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ageLabels,
        datasets: [{
            label: 'Number of Residents',
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
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                precision: 0
            }
        }
    }
});

const householdLabels = <?= json_encode($householdLabels) ?>;
const householdData = <?= json_encode($householdCounts) ?>;

const ctx2 = document.getElementById('householdLineChart').getContext('2d');
const householdLineChart = new Chart(ctx2, {
    type: 'line',
    data: {
        labels: householdLabels,
        datasets: [{
            label: 'Number of Households',
            data: householdData,
            fill: false,
            borderColor: '#3949ab',
            backgroundColor: '#3949ab',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                precision: 0
            }
        }
    }
});

const residentTotal = <?= $residentTotal ?>;

const ctxPie = document.getElementById('residentPieChart').getContext('2d');
const residentPieChart = new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: ['Residents: ' + residentTotal],
        datasets: [{
            data: [residentTotal],
            backgroundColor: ['#5c6bc0'],
            borderColor: ['#1a237e'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    font: {
                        size: 16
                    }
                }
            }
        }
    }
});
</script>
<?php require("partials/foot.php"); ?>