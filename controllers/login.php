<?php
session_start();
    $userlog="";
    $passlog="";
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $userlog = $_POST['user'];
        $passlog = $_POST['pass'];
        $accountType = isset($_POST['accountType']) ? $_POST['accountType'] : "admin"; // Default to admin
        
        if(empty($userlog) || empty($passlog)){
            echo "<script>alert('Please input the correct credentials!')</script>";
        }else {
            include("../database.php");
            $conn = getDatabaseConnection();

            $statement = $conn->prepare(
                "SELECT id, username, password, role FROM users WHERE username = ?"
            );
            $statement->bind_param('s', $userlog);
            $statement->execute();

            $statement->bind_result($id, $username, $password, $role);
            if($statement->fetch()){
                // Only allow admin login
                if($role !== "admin"){
                    echo "<script>alert('Access denied! Admin credentials required.')</script>";
                } 
                else if (password_verify($passlog, $password)){
                    $_SESSION["id"] = $id;
                    $_SESSION["username"] = $username;
                    $_SESSION["role"] = $role;
                    
                    // Redirect to admin dashboard
                    header("Location: ../pages/dashboard.php");
                    exit();
                } 
                else { 
                    echo "<script>alert('Incorrect password!')</script>";
                }
            } else {
                echo "<script>alert('Username not found!')</script>";
            }
            $statement->close();
            mysqli_close($conn);
        }
    }
?>

<?php require("../pages/login.php"); ?>