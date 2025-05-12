<?php $style = "residents.css";
    require("partials/head.php") ?>
<?php require("partials/sidebar.php") ?>
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
<?php require("partials/foot.php"); ?>