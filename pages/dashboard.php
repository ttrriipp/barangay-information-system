<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
$style = 'main.css';
require("partials/head.php"); ?>
<?php require("partials/sidebar.php") ?>

<div class="main-content">
    <div class="header-container">
        <h1>Dashboard</h1>
    </div>
    
    <div class="cards">
        <div class="card">
            <h2>Total Residents</h2>
            <p>127</p>
        </div>
        <div class="card">
            <h2>Total Households</h2>
            <p>42</p>
        </div>
        <div class="card">
            <h2>Senior Citizens</h2>
            <p>23</p>
        </div>
    </div>

<?php require("partials/foot.php");
