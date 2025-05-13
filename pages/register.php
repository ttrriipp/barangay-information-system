<?php  $style = 'register.css';
    require("partials/head.php") ?>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Create Account</h2>
            <p>Get started with an account</p>
            <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" placeholder="Username" name="user" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" placeholder="Create Password" name="pass" required>
                
                <label for="retypePassword">Re-type Password:</label>
                <input type="password" id="retypePassword" placeholder="Re-type Password" required>
                
                <button type="submit" id="submit_button" name="submit">Create Account</button>
            </form>
            <div class="back-link">
                <a href="login.php" style="color: #0066ff; text-decoration: none; font-size: 1em; display: inline-block; margin-top: 15px;">Back to Login</a>
            </div>
        </div>
        <div class="logo-container">
            <h2>Cupang West Information Management System</h2>
            <img src="../assets/images/logo-cupangwestt.png" alt="Cupang West Logo">
        </div>
    </div>
    <script src="createacc.js"></script>
<?php require("partials/foot.php") ?>