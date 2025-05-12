<?php
    include("../database.php");
?>

<?php require("../pages/register.php") ?>

<?php
    if( $_SERVER["REQUEST_METHOD"] == "POST")   {
        $user = filter_input(INPUT_POST, "user", FILTER_SANITIZE_SPECIAL_CHARS);
        $pass = filter_input(INPUT_POST, "pass", FILTER_SANITIZE_SPECIAL_CHARS);

            $sql = "INSERT INTO creataccdb (username, password)
                    VALUES ('$user','$pass')";
            mysqli_query($conn, $sql);
    }
   mysqli_close($conn);
?>