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
    $stmt = $conn->prepare("SELECT COUNT(*) FROM resident WHERE age >= ? AND age <= ?");
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
$conn->close();

$style = 'main.css';
require("partials/head.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php require("partials/sidebar.php") ?>
    <!-- Age Range Bar Graph -->
    <div class="report-card">
      <h2>Residents by Age Range</h2>
      <canvas id="ageRangeChart" width="400" height="200"></canvas>
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
        scales: {
            y: {
                beginAtZero: true,
                precision: 0
            }
        }
    }
});
</script>
<?php require("partials/foot.php"); ?>