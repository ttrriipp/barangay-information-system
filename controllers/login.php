<?php
session_start();
    $userlog="";
    $passlog="";
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $userlog = $_POST['user'];
        $passlog = $_POST['pass'];
        if(empty($userlog) || empty($passlog)){
            echo "<script>alert('Please input the correct credentials!')</script>";
        }else{
            include("../database.php");
            $conn = getDatabaseConnection();

            $statement = $conn->prepare(
                "SELECT id, username, password FROM creataccdb WHERE user = ?"
            );
            $statement->bind_param('s', $userlog);
            $statement->execute();

            $statement->bind_result($id, $username, $password);
            if($statement->fetch()){
                if(password_verify($passlog, $password)){
                $_SESSION["id"] = $id;
                $_SESSION["username"] = $username;
                header("location: pages/dashboard.php");
                exit;
                }
            }
            $statement->close();
        }
    }
?>

<?php require("../pages/login.php"); ?>

<?php
    mysqli_close($conn);
?>