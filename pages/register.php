<?php
    include("../database.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Create Account</h2>
            <p>Get started with an account</p>
            <form>
                <label for="username">Username:</label>
                <input type="text" id="username" placeholder="Username" required>
                
                <label for="password">Create Password:</label>
                <input type="password" id="password" placeholder="Create Password" required>
                
                <div class="show-password-container">
                    <input type="checkbox" id="showPassword">
                    <label for="showPassword">Show password</label>
                </div>
                
                <label for="retypePassword">Re-type Password:</label>
                <input type="password" id="retypePassword" placeholder="Re-type Password" required>
                
                <button type="submit">Create Account</button>
            </form>
        </div>
        <div class="logo-container">
            <img src="your-logo-url-here.png" alt="Cupang West Logo">
        </div>
    </div>
    <script src="createacc.js"></script>
</body>
</html>