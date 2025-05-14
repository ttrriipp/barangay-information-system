<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
$style = 'main.css';
require("partials/head.php") ?>
<?php require("partials/sidebar.php") ?>
<div class="main-content">
        <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/themes/color.css">
    <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/demo/demo.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="https://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>
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
        <table id="dg" title="Users Management" class="easyui-datagrid" url="getData.php" toolbar="#toolbar" pagination="true" rownumbers="true" fitColumns="true" singleSelect="true" style="width:100%;height:400px;">
        <thead>
            <tr>
                <th field ="id" width="50">Id No.</th>
                <th field="fullname" width="50">Full Name</th>
                <th field="address" width="50">Address</th>
                <th field="age" width="50">Age</th>
                <th field="sex" width="50">Sex</th>
            </tr>
        </thead>
    </table>
    <div class="button-container">
        <button onclick="addResident()">Add New Resident</button>
        <button onclick="editResident()">Edit Resident</button>
        <button onclick="deleteResident()">Delete Resident</button>
    </div>
</div>

<script src="resi.js"></script>
<?php require("partials/foot.php"); ?>