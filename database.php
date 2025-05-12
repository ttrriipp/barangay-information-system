<?php
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "barangay_infomanage";

    $conn = "";

    try{
    $conn = mysqli_connect($db_server,
                           $db_user,
                           $db_pass,
                           $db_name);
    }
   catch(mysqli_sql_exception){
    echo "Could not Connect to the database";
   }
    // //Checking and Validation of Login Credentials
    // $user_check = "SELECT * FROM creataccdb WHERE user='$user' OR pass='$pass' LIMIT 1";
    // $result = mysqli_query($conn, $user_check);
    // $user = mysqli_fetch_assoc($result);
    // mysqli_close($conn);
?>