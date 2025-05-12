<?php $style = 'login.css'; 
    require("partials/head.php"); ?>
    <div class="container">
        <div class="left">
            <h1>Welcome</h1>
            <p>Please enter your details</p>
            <form id="loginForm" action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="user" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="pass" required>
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
    <?php require("partials/foot.php"); ?>