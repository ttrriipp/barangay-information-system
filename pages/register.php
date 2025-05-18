<?php  $style = 'register.css';
    require("partials/head.php") ?>
<div class="container">
    <div class="form-container">
        <h2>Create Account</h2>
        <p>Get started with an account</p>
        <form action="../controllers/register.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" placeholder="Username" name="user" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" placeholder="Create Password" name="pass" required>
            
            <label for="retypePassword">Re-type Password:</label>
            <input type="password" id="retypePassword" placeholder="Re-type Password" required>
            
            <div class="button-row">
                <button type="submit" id="submit_button" name="submit">Create Account</button>
                <a href="login.php" class="back-button">Back to Login</a>
            </div>
        </form>
    </div>
    <div class="logo-container">
        <h2>Cupang West Information Management System</h2>
        <img src="../assets/images/logo-cupangwest.png" alt="Cupang West Logo">
    </div>
</div>
<script src="../assets/js/register.js"></script>
<?php require("partials/foot.php") ?>