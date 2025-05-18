<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
require("../database.php");
$conn = getDatabaseConnection();

// Get current date for default values
$currentYear = date('Y');
$currentMonth = date('m');

// Handle date filter parameters
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'residents';

// Get resident data by age
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

// Get resident data by gender
$genderData = [];
$genderLabels = [];
$genderCounts = [];
$query = "SELECT sex, COUNT(*) as count FROM residents GROUP BY sex";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $genderLabels[] = $row['sex'];
        $genderCounts[] = $row['count'];
    }
}

// Get resident data by civil status
$civilStatusData = [];
$civilStatusLabels = [];
$civilStatusCounts = [];
$query = "SELECT civil_status, COUNT(*) as count FROM residents GROUP BY civil_status";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['civil_status'])) {
            $civilStatusLabels[] = $row['civil_status'];
            $civilStatusCounts[] = $row['count'];
        }
    }
}

// Get resident data by education
$educationData = [];
$educationLabels = [];
$educationCounts = [];
$query = "SELECT education, COUNT(*) as count FROM residents GROUP BY education";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['education'])) {
            $educationLabels[] = $row['education'];
            $educationCounts[] = $row['count'];
        }
    }
}

// Get certificate data by type
$certificateTypeLabels = [];
$certificateTypeCounts = [];
$query = "SELECT certificate_type, COUNT(*) as count FROM certificates GROUP BY certificate_type";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $certificateTypeLabels[] = $row['certificate_type'];
        $certificateTypeCounts[] = $row['count'];
    }
}

// Get certificate data by month (for the current year)
$monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$certificateMonthCounts = array_fill(0, 12, 0);

$query = "SELECT MONTH(issue_date) as month, COUNT(*) as count FROM certificates 
          WHERE YEAR(issue_date) = ? 
          GROUP BY MONTH(issue_date)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $currentYear);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $certificateMonthCounts[$row['month'] - 1] = $row['count'];
}
$stmt->close();

// Get blotter data by status
$blotterStatusLabels = [];
$blotterStatusCounts = [];
$query = "SELECT status, COUNT(*) as count FROM blotter_records GROUP BY status";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $blotterStatusLabels[] = $row['status'];
        $blotterStatusCounts[] = $row['count'];
    }
}

// Get blotter data by incident type
$incidentTypeLabels = [];
$incidentTypeCounts = [];
$query = "SELECT incident_type, COUNT(*) as count FROM blotter_records GROUP BY incident_type";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $incidentTypeLabels[] = $row['incident_type'];
        $incidentTypeCounts[] = $row['count'];
    }
}

// Get blotter data by month (for the current year)
$blotterMonthCounts = array_fill(0, 12, 0);
$query = "SELECT MONTH(incident_date) as month, COUNT(*) as count FROM blotter_records 
          WHERE YEAR(incident_date) = ? 
          GROUP BY MONTH(incident_date)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $currentYear);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $blotterMonthCounts[$row['month'] - 1] = $row['count'];
}
$stmt->close();

// Get total counts
$totalResidents = array_sum($ageCounts);
$totalCertificates = array_sum($certificateTypeCounts);
$totalBlotters = array_sum($blotterStatusCounts);

$conn->close();

$style = 'main.css';
$additionalStyles = ['reports.css'];
require("partials/head.php");
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<?php require("partials/sidebar.php") ?>

<div class="main-content">
    <div class="reports-header">
        <h1>Reports and Analytics</h1>
        <div class="report-actions">
            <button id="printReport" class="btn btn-primary"><i class="fas fa-print"></i> Print</button>
            <button id="exportPdf" class="btn btn-success"><i class="fas fa-file-pdf"></i> Export PDF</button>
        </div>
    </div>
    
    <div class="report-filters">
        <form id="reportForm" method="GET" action="reports.php">
            <div class="filter-group">
                <label for="report_type">Report Type:</label>
                <select id="report_type" name="report_type" class="form-control">
                    <option value="residents" <?php echo $reportType == 'residents' ? 'selected' : ''; ?>>Resident Demographics</option>
                    <option value="certificates" <?php echo $reportType == 'certificates' ? 'selected' : ''; ?>>Certificate Issuance</option>
                    <option value="blotter" <?php echo $reportType == 'blotter' ? 'selected' : ''; ?>>Blotter Records</option>
                    <option value="all" <?php echo $reportType == 'all' ? 'selected' : ''; ?>>All Reports</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="start_date">From:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $startDate; ?>">
            </div>
            <div class="filter-group">
                <label for="end_date">To:</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $endDate; ?>">
            </div>
            <div class="filter-group">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
        </form>
    </div>

    <div id="reportContainer" class="report-container">
        <!-- Summary Statistics -->
        <div class="summary-stats">
            <div class="stat-box">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-details">
                    <h3>Total Residents</h3>
                    <div class="stat-number"><?php echo number_format($totalResidents); ?></div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon"><i class="fas fa-certificate"></i></div>
                <div class="stat-details">
                    <h3>Certificates Issued</h3>
                    <div class="stat-number"><?php echo number_format($totalCertificates); ?></div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-details">
                    <h3>Blotter Cases</h3>
                    <div class="stat-number"><?php echo number_format($totalBlotters); ?></div>
                </div>
            </div>
        </div>

        <?php if ($reportType == 'residents' || $reportType == 'all'): ?>
        <!-- Resident Demographics Section -->
        <div class="report-section" id="residentReports">
            <h2 class="section-title">Resident Demographics</h2>
            
            <div class="chart-row">
                <div class="chart-container">
                    <h3>Age Distribution</h3>
                    <canvas id="ageDistributionChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Gender Distribution</h3>
                    <canvas id="genderDistributionChart"></canvas>
                </div>
            </div>
            
            <div class="chart-row">
                <div class="chart-container">
                    <h3>Civil Status</h3>
                    <canvas id="civilStatusChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Educational Attainment</h3>
                    <canvas id="educationChart"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($reportType == 'certificates' || $reportType == 'all'): ?>
        <!-- Certificate Reports Section -->
        <div class="report-section" id="certificateReports">
            <h2 class="section-title">Certificate Issuance Reports</h2>
            
            <div class="chart-row">
                <div class="chart-container">
                    <h3>Certificates by Type</h3>
                    <canvas id="certificateTypeChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Certificates Issued by Month (<?php echo $currentYear; ?>)</h3>
                    <canvas id="certificateTimeChart"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($reportType == 'blotter' || $reportType == 'all'): ?>
        <!-- Blotter Reports Section -->
        <div class="report-section" id="blotterReports">
            <h2 class="section-title">Blotter Record Analysis</h2>
            
            <div class="chart-row">
                <div class="chart-container">
                    <h3>Cases by Status</h3>
                    <canvas id="blotterStatusChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Cases by Incident Type</h3>
                    <canvas id="incidentTypeChart"></canvas>
                </div>
            </div>
            
            <div class="chart-row">
                <div class="chart-container full-width">
                    <h3>Blotter Cases by Month (<?php echo $currentYear; ?>)</h3>
                    <canvas id="blotterTimeChart"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Chart color palette
const colors = [
    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
    '#5a5c69', '#6f42c1', '#fd7e14', '#20c9a6', '#6610f2'
];

// Utility function for chart options
function getChartOptions(chartType) {
    const baseOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: { size: 12 }
                }
            }
        }
    };
    
    if (chartType === 'bar') {
        return {
            ...baseOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        };
    }
    
    return baseOptions;
}

// Age Distribution Chart
const ageLabels = <?= json_encode(array_keys($ageCounts)) ?>;
const ageData = <?= json_encode(array_values($ageCounts)) ?>;

<?php if ($reportType == 'residents' || $reportType == 'all'): ?>
const ageCtx = document.getElementById('ageDistributionChart').getContext('2d');
new Chart(ageCtx, {
    type: 'bar',
    data: {
        labels: ageLabels,
        datasets: [{
            label: 'Number of Residents',
            data: ageData,
            backgroundColor: colors,
            borderColor: colors.map(color => color),
            borderWidth: 1
        }]
    },
    options: getChartOptions('bar')
});

// Gender Distribution Chart
const genderLabels = <?= json_encode($genderLabels) ?>;
const genderData = <?= json_encode($genderCounts) ?>;

const genderCtx = document.getElementById('genderDistributionChart').getContext('2d');
new Chart(genderCtx, {
    type: 'doughnut',
    data: {
        labels: genderLabels,
        datasets: [{
            data: genderData,
            backgroundColor: colors.slice(0, genderLabels.length),
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        ...getChartOptions('doughnut'),
        plugins: {
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

// Civil Status Chart
const civilStatusLabels = <?= json_encode($civilStatusLabels) ?>;
const civilStatusData = <?= json_encode($civilStatusCounts) ?>;

const civilCtx = document.getElementById('civilStatusChart').getContext('2d');
new Chart(civilCtx, {
    type: 'pie',
    data: {
        labels: civilStatusLabels,
        datasets: [{
            data: civilStatusData,
            backgroundColor: colors.slice(0, civilStatusLabels.length),
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        ...getChartOptions('pie'),
        plugins: {
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

// Education Chart
const educationLabels = <?= json_encode($educationLabels) ?>;
const educationData = <?= json_encode($educationCounts) ?>;

const educationCtx = document.getElementById('educationChart').getContext('2d');
new Chart(educationCtx, {
    type: 'pie',
    data: {
        labels: educationLabels,
        datasets: [{
            data: educationData,
            backgroundColor: colors.slice(0, educationLabels.length),
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        ...getChartOptions('pie'),
        plugins: {
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
<?php endif; ?>

<?php if ($reportType == 'certificates' || $reportType == 'all'): ?>
// Certificate Type Chart
const certTypeLabels = <?= json_encode($certificateTypeLabels) ?>;
const certTypeData = <?= json_encode($certificateTypeCounts) ?>;

const certTypeCtx = document.getElementById('certificateTypeChart').getContext('2d');
new Chart(certTypeCtx, {
    type: 'pie',
    data: {
        labels: certTypeLabels,
        datasets: [{
            data: certTypeData,
            backgroundColor: colors.slice(0, certTypeLabels.length),
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        ...getChartOptions('pie'),
        plugins: {
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

// Certificate Time Chart
const monthLabels = <?= json_encode($monthLabels) ?>;
const certMonthData = <?= json_encode($certificateMonthCounts) ?>;

const certTimeCtx = document.getElementById('certificateTimeChart').getContext('2d');
new Chart(certTimeCtx, {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Certificates Issued',
            data: certMonthData,
            backgroundColor: colors[0],
            borderColor: colors[0],
            tension: 0.3,
            fill: false
        }]
    },
    options: getChartOptions('line')
});
<?php endif; ?>

<?php if ($reportType == 'blotter' || $reportType == 'all'): ?>
// Blotter Status Chart
const blotterStatusLabels = <?= json_encode($blotterStatusLabels) ?>;
const blotterStatusData = <?= json_encode($blotterStatusCounts) ?>;

const blotterStatusCtx = document.getElementById('blotterStatusChart').getContext('2d');
new Chart(blotterStatusCtx, {
    type: 'doughnut',
    data: {
        labels: blotterStatusLabels,
        datasets: [{
            data: blotterStatusData,
            backgroundColor: colors.slice(0, blotterStatusLabels.length),
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        ...getChartOptions('doughnut'),
        plugins: {
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

// Incident Type Chart
const incidentTypeLabels = <?= json_encode($incidentTypeLabels) ?>;
const incidentTypeData = <?= json_encode($incidentTypeCounts) ?>;

const incidentTypeCtx = document.getElementById('incidentTypeChart').getContext('2d');
new Chart(incidentTypeCtx, {
    type: 'bar',
    data: {
        labels: incidentTypeLabels,
        datasets: [{
            label: 'Number of Cases',
            data: incidentTypeData,
            backgroundColor: colors.slice(0, incidentTypeLabels.length),
            borderColor: colors.map(color => color).slice(0, incidentTypeLabels.length),
            borderWidth: 1
        }]
    },
    options: getChartOptions('bar')
});

// Blotter Time Chart
const blotterMonthData = <?= json_encode($blotterMonthCounts) ?>;

const blotterTimeCtx = document.getElementById('blotterTimeChart').getContext('2d');
new Chart(blotterTimeCtx, {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Blotter Cases',
            data: blotterMonthData,
            backgroundColor: colors[1],
            borderColor: colors[1],
            tension: 0.3,
            fill: false
        }]
    },
    options: getChartOptions('line')
});
<?php endif; ?>

// Print report functionality
document.getElementById('printReport').addEventListener('click', function() {
    window.print();
});

// Export to PDF functionality
document.getElementById('exportPdf').addEventListener('click', function() {
    const { jsPDF } = window.jspdf;
    const reportElement = document.getElementById('reportContainer');
    
    // Create new PDF document
    const doc = new jsPDF('p', 'mm', 'a4');
    
    // Add title
    doc.setFontSize(18);
    doc.text('Barangay Information System Report', 105, 15, { align: 'center' });
    doc.setFontSize(12);
    doc.text('Generated on: ' + new Date().toLocaleDateString(), 105, 22, { align: 'center' });
    
    // Use html2canvas to capture the report as an image
    html2canvas(reportElement, { scale: 1 }).then(canvas => {
        // Convert the canvas to an image
        const imgData = canvas.toDataURL('image/png');
        
        // Calculate the width and height to fit on A4
        const imgProps = doc.getImageProperties(imgData);
        const pdfWidth = doc.internal.pageSize.getWidth() - 20;
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        
        // Add the image to the PDF
        doc.addImage(imgData, 'PNG', 10, 30, pdfWidth, pdfHeight);
        
        // Save the PDF
        doc.save('barangay_report.pdf');
    });
});
</script>

<!-- Reports styles are in the reports.css file -->

<?php require("partials/foot.php"); ?>