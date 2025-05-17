<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Issuance - Admin Panel</title>
    <link rel="stylesheet" href="certificate_issuance.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h3>Admin Panel</h3>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="certificate_issuance.php">
                            <i class="fas fa-certificate"></i> Certificate Issuance
                        </a>
                    </li>
                    <!-- Add more menu items as needed -->
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="content-header">
                    <h2>Certificate Issuance</h2>
                </div>

                <div class="certificate-form">
                    <form id="certificateForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="residentName" class="form-label">Resident Name</label>
                                <input type="text" class="form-control" id="residentName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="certificateType" class="form-label">Certificate Type</label>
                                <select class="form-select" id="certificateType" required>
                                    <option value="">Select Certificate Type</option>
                                    <option value="indigency">Certificate of Indigency</option>
                                    <option value="residency">Certificate of Residency</option>
                                    <option value="clearance">Barangay Clearance</option>
                                    <option value="good_moral">Certificate of Good Moral Character</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="purpose" class="form-label">Purpose</label>
                                <input type="text" class="form-control" id="purpose" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dateIssued" class="form-label">Date Issued</label>
                                <input type="date" class="form-control" id="dateIssued" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Generate Certificate
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="previewCertificate()">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Certificate List Section -->
                <div class="certificate-list mt-4">
                    <h3>Recent Certificates</h3>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Certificate ID</th>
                                    <th>Resident Name</th>
                                    <th>Type</th>
                                    <th>Date Issued</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data - Replace with dynamic data -->
                                <tr>
                                    <td>CERT-001</td>
                                    <td>John Doe</td>
                                    <td>Indigency</td>
                                    <td>2024-03-20</td>
                                    <td><span class="badge bg-success">Issued</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><i class="fas fa-print"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewCertificate() {
            // Add preview functionality here
            alert('Preview functionality will be implemented');
        }

        document.getElementById('certificateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add form submission logic here
            alert('Certificate generation functionality will be implemented');
        });
    </script>
</body>
</html> 