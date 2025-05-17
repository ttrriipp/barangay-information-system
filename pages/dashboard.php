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


<?php require("partials/foot.php");
