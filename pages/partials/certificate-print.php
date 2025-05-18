<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

require("../../database.php");

// Check if ID parameter is set
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $certificate_id = $_GET["id"];
    
    // Connect to database
    $conn = getDatabaseConnection();
    
    if ($conn) {
        // Get certificate information
        $query = "SELECT id, resident_name, certificate_type, purpose, issue_date, issued_by
                  FROM certificates
                  WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $certificate_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $certificate = $row;
            mysqli_close($conn);
        } else {
            // Certificate not found
            mysqli_close($conn);
            echo "Certificate not found";
            exit();
        }
    } else {
        echo "Database connection failed";
        exit();
    }
} else {
    // If ID is not provided, redirect to certificates page
    header("Location: ../certificates.php");
    exit();
}

// Format the date
$issue_date = date("F d, Y", strtotime($certificate['issue_date']));

// Get the certificate details
$certificate_type = $certificate['certificate_type'];
$resident_name = $certificate['resident_name'];
$purpose = $certificate['purpose'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $certificate_type ?> Certificate</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        .certificate-container {
            width: 8.5in;
            height: 11in;
            margin: 0 auto;
            padding: 0.5in;
            border: 2px solid #000;
            position: relative;
        }
        
        .certificate-header {
            text-align: center;
            margin-bottom: 0.5in;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            display: block;
        }
        
        .header-text h1 {
            margin: 5px 0;
            font-size: 18pt;
        }
        
        .header-text h2 {
            margin: 5px 0;
            font-size: 16pt;
        }
        
        .header-text p {
            margin: 5px 0;
            font-size: 12pt;
        }
        
        .certificate-title {
            text-align: center;
            margin: 0.5in 0;
            font-size: 24pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }
        
        .certificate-body {
            margin: 0.5in 0;
            font-size: 12pt;
            text-align: justify;
            line-height: 2;
        }
        
        .certificate-footer {
            margin-top: 1in;
        }
        
        .signature-container {
            float: right;
            width: 3in;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .certificate-container {
                border: none;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin: 20px;">
        <button onclick="window.print()">Print Certificate</button>
        <button onclick="window.close()">Close</button>
    </div>
    
    <div class="certificate-container">
        <div class="certificate-header">
            <div class="logo">
                <!-- Replace with your barangay logo -->
                <img src="../../assets/images/logo-cupangwest.png" alt="Barangay Logo" style="width: 100%; height: auto;">
            </div>
            <div class="header-text">
                <h1>Republic of the Philippines</h1>
                <h2>Barangay Cupang West</h2>
                <p>Balanga City, Bataan</p>
            </div>
        </div>
        
        <div class="certificate-title">
            Certificate of <?= $certificate_type ?>
        </div>
        
        <div class="certificate-body">
            <?php if ($certificate_type == 'Residency'): ?>
                <p>This is to certify that <strong><?= htmlspecialchars($resident_name) ?></strong> is a bonafide resident of this barangay and has been living in this address for a considerable period of time.</p>
                
                <p>This certification is being issued upon the request of the above-named person for <?= htmlspecialchars($purpose) ?> purposes.</p>
                
                <p>Issued this <?= $issue_date ?> at the Barangay Office.</p>
            
            <?php elseif ($certificate_type == 'Indigency'): ?>
                <p>This is to certify that <strong><?= htmlspecialchars($resident_name) ?></strong> is a bonafide resident of this barangay and belongs to the indigent family in this barangay.</p>
                
                <p>This certification is being issued upon the request of the above-named person for <?= htmlspecialchars($purpose) ?> purposes.</p>
                
                <p>Issued this <?= $issue_date ?> at the Barangay Office.</p>
            
            <?php elseif ($certificate_type == 'Clearance'): ?>
                <p>This is to certify that <strong><?= htmlspecialchars($resident_name) ?></strong> is a bonafide resident of this barangay and has no derogatory record filed in this barangay.</p>
                
                <p>This certification is being issued upon the request of the above-named person for <?= htmlspecialchars($purpose) ?> purposes.</p>
                
                <p>Issued this <?= $issue_date ?> at Barangay Cupang West Office.</p>
            
            <?php else: ?>
                <p>This is to certify that <strong><?= htmlspecialchars($resident_name) ?></strong> is a bonafide resident of this barangay.</p>
                
                <p>This certification is being issued upon the request of the above-named person for <?= htmlspecialchars($purpose) ?> purposes.</p>
                
                <p>Issued this <?= $issue_date ?> at the Barangay Office.</p>
            <?php endif; ?>
        </div>
        
        <div class="certificate-footer">
            <div class="signature-container">
                <div class="signature-line"></div>
                <strong>Barangay Captain</strong>
            </div>
        </div>
    </div>
</body>
</html> 