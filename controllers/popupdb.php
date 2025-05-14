<?php require("../partials/popup.php")
?>

<?php
    include("../database.php");
    $conn = getDatabaseConnection();

    if($_SERVER["REQUEST_METHOD"] == "POST"){
            if($_POST["residentadd"]){
        $surname = $_POST["surname"]
        $firstname = $_POST["fname"]
        $midname = $_POST["mname"]
        $age = $_POST["age"]
        $sex =  $_POST["sex"]
        $address = $_POST["address"]
        $contact = $_POST["contact"]

        $insert_que= "INSERT INTO resident VALUE('$surname', '$firstname', '$midname', '$age', '$sex', '$address', '$contact')";
        $que = mysqli_query($conn, $insert_que);
        }
        if($que){
            echo "sumakses";
        }else{
            echo "failed";
        }
    }
    mysqli_close($conn);
?>