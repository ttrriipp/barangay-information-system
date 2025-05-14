<?php require("../pages/register.php") ?>

<?php
    include("../database.php");
    $conn = getDatabaseConnection();
    $message = "";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = filter_input(INPUT_POST, "user", FILTER_SANITIZE_SPECIAL_CHARS);
        $pass = filter_input(INPUT_POST, "pass", FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Check if username already exists
        $check_sql = "SELECT * FROM users WHERE username = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $user);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // This is handled by AJAX now, but keeping as fallback
            header("Location: ../pages/register.php?error=username_exists");
            exit();
        } else {
            // Hash the password for security
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            
            // Use prepared statement to prevent SQL injection
            $insert_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "ss", $user, $hashed_password);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                echo "<script>alert('Account created successfully!'); window.location.href='../pages/login.php';</script>";
            } else {
                echo "<script>alert('Error creating account: " . mysqli_error($conn) . "');</script>";
            }
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($check_stmt);
    }

    mysqli_close($conn);
?>