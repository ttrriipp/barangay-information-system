<?php
// This is just a redirector to the proper modal partial
// We are now handling this through AJAX/modal, so this is a fallback
session_start();

$id = isset($_GET['id']) ? $_GET['id'] : '';
if (empty($id)) {
    $_SESSION['blotter_error'] = "No blotter ID provided.";
    header("Location: blotter.php");
    exit();
}

// This page is now handled by the modal, we don't need a separate page
header("Location: blotter.php");
exit();
?> 