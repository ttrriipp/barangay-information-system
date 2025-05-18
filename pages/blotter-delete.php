<?php
// This is just a redirector to the proper controller
session_start();

// Redirect to the controller with the same parameters
$id = isset($_GET['id']) ? $_GET['id'] : '';
header("Location: controllers/delete-blotter.php?id=" . $id);
exit();
?> 