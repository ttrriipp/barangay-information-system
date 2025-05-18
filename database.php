<?php
    function getDatabaseConnection(){
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "barangay_infomanage";
    $conn = null;

    try {
        $conn = mysqli_connect($db_server,
                                $db_user,
                                $db_pass,
                                $db_name);
        
        if (!$conn) {
            throw new Exception("Connection failed: " . mysqli_connect_error());
        }
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        return null;
    }

    return $conn;
    }
?>