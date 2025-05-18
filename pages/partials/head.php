<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barangay Cupang West Management System</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php 
     if (!isset($hide_sidebar)) {
      echo '<link rel="stylesheet" href="../assets/css/sidebar.css">';
    } ?>
    <link rel="stylesheet" href="../assets/css/<?= $style ?>">
    <?php
    // Load additional styles if specified
    if (isset($additionalStyles) && is_array($additionalStyles)) {
        foreach ($additionalStyles as $additionalStyle) {
            echo '<link rel="stylesheet" href="../assets/css/' . $additionalStyle . '">';
        }
    }
    
    // Load modal CSS on pages that use modals (for backward compatibility)
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page === 'residents.php' || $current_page === 'blotter.php') {
        echo '<link rel="stylesheet" href="../assets/css/modal.css">';
    }
    ?>
  </head>
  <body>
