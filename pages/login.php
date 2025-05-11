<?php
    include("../database.php")
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="container">
        <div class="left">
            <h1>Welcome</h1>
            <p>Please enter your details</p>
            <form id="loginForm">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <div class="remember-me">
                    <input type="checkbox" id="rememberMe" name="rememberMe">
                    <label for="rememberMe">Remember me</label>
                </div>
                <button type="submit">Login</button>
            </form>
            <p id="p2">Don't have an account yet? <a href="register.php">Create Account</a></p>
        </div>
        <div class="right">
            <h2>Cupang West Management System</h2>
            <img src="../images/logo-cupang.png" alt="Cupang West Logo">
        </div>
    </div>
    <script src="login.js"></script>
</body>
</html>
<?php
    mysqli_close($conn);
?>