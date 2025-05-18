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
    
    <div class="mission-vision">
        <div class="mission">
            <h2>Mission</h2>
            <p>To provide efficient and effective public service that enhances the quality of life for all residents through responsive governance, sustainable development, and community engagement.</p>
        </div>
        <div class="vision">
            <h2>Vision</h2>
            <p>A progressive, peaceful, and prosperous barangay where residents enjoy a high quality of life, economic opportunities, and a clean and safe environment.</p>
        </div>
    </div>
</div>

<?php require("partials/foot.php");
