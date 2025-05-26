<?php $style = 'login.css'; 
    require("partials/head.php"); ?>
    <div class="container">
        <div class="left">
            <h1>Admin Login</h1>
            <p>Please enter your admin credentials to access the system</p>
            <form id="loginForm" action="../controllers/login.php" method="post">
                <input type="hidden" id="selectedAccountType" name="accountType" value="admin">
                <label for="username">Username:</label>
                <input type="text" id="username" name="user" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="pass" required>
                <button type="submit">Login <i class="fas fa-arrow-right"></i></button>
            </form>
            <p id="p2"><a href="../pages/user.php">← Back to Home</a></p>
        </div>
        <div class="right">
            <h2>Cupang West Information Management System</h2>
            <img src="../assets/images/logo-cupangwest.png" alt="Cupang West Logo">
        </div>
    </div>

    <?php require("partials/foot.php"); ?>