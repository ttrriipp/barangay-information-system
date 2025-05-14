<?php $style = 'login.css'; 
    require("partials/head.php"); ?>
    <div class="container">
        <div class="left">
            <h1>Welcome</h1>
            <p>Please enter your details to access the system</p>
            <p class="label">Account type:</p>
            <div class="account-types">
                <label class="account-option">
                    <input type="radio" id="user" name="accountType" value="user" checked>
                    <span class="radio-label">User</span>
                </label>
                <label class="account-option">
                    <input type="radio" id="admin" name="accountType" value="admin">
                    <span class="radio-label">Admin</span>
                </label>
            </div>
            <form id="loginForm" action="../controllers/login.php" method="post">
                <input type="hidden" id="selectedAccountType" name="accountType" value="user">
                <label for="username">Username:</label>
                <input type="text" id="username" name="user" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="pass" required>
                <div class="remember-me">
                    <input type="checkbox" id="rememberMe" name="rememberMe">
                    <label for="rememberMe">Remember me</label>
                </div>
                <button type="submit">Login <i class="fas fa-arrow-right"></i></button>
            </form>
            <p id="p2">Don't have an account yet? <a href="register.php">Create Account</a></p>
        </div>
        <div class="right">
            <h2>Cupang West Information Management System</h2>
            <img src="../assets/images/logo-cupangwest.png" alt="Cupang West Logo">
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set the initial value
            document.getElementById('selectedAccountType').value = 'user';
            
            // Update the hidden input when radio buttons change
            const radioButtons = document.querySelectorAll('input[name="accountType"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.getElementById('selectedAccountType').value = this.value;
                });
            });
        });
    </script>
    <?php require("partials/foot.php"); ?>