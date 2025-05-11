<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Management</title>
    <link rel="stylesheet" href="../assets/css/residents.css">
</head>
<body>
    <div class="sidebar">
        <div class="profile">
            <img src="your-profile-image-url-here.png" alt="Profile Image">
            <h3>Admin</h3>
        </div>
        <nav>
            <ul>
                <li><a href="#"><i class="icon-dashboard"></i>Dashboard</a></li>
                <li><a href="#"><i class="icon-resident-management"></i>Resident Management</a></li>
                <li><a href="#"><i class="icon-reports"></i>Reports</a></li>
                <li><a href="#"><i class="icon-services"></i>Services</a></li>
            </ul>
        </nav>
        <div class="user-info">
            <img src="your-user-image-url-here.png" alt="User Image">
            <p>Name</p>
            <p>username</p>
        </div>
    </div>
    <div class="main-content">
        <h1>Resident Management</h1>
        <div class="table-container">
            <table id="residentTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Age</th>
                        <th>Sex</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="button-container">
            <button onclick="addResident()">Add New Resident</button>
            <button onclick="editResident()">Edit Resident</button>
            <button onclick="deleteResident()">Delete Resident</button>
        </div>
    </div>

    <script src="resi.js"></script>
</body>
</html>