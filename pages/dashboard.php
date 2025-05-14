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
    <h1>Barangay Cupang West Management System</h1>
    <div class="cards">
        <div class="card">
            <h2>Percent Population</h2>
            <button>More Info</button>
        </div>
        <div class="card">
            <h2>Number of household</h2>
            <button>More Info</button>
        </div>
        <div class="card">
            <h2>Social Development</h2>
            <button>More Info</button>
        </div>
        <div class="card">
            <h2>Program and initiatives</h2>
            <button>More Info</button>
        </div>
    </div>
</div>

<?php require("partials/foot.php");
