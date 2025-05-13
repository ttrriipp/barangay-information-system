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
                
                <label for="password">Create Password:</label>
                <input type="password" id="password" placeholder="Create Password" name="pass" required>
                
                <div class="show-password-container">
                    <input type="checkbox" id="showPassword">
                    <label for="showPassword">Show password</label>
                </div>
                
                <label for="retypePassword">Re-type Password:</label>
                <input type="password" id="retypePassword" placeholder="Re-type Password" required>
                
                <button type="submit" id="submit_button" name="submit" >Create Account</button>
            </form>
        </div>
        <div class="logo-container">
            <h2>Cupang West Information Management System</h2>
            <img src="../assets/images/logo-cupangwestt.png" alt="Cupang West Logo">
        </div>
    </div>
    <script src="createacc.js"></script>
<?php require("partials/foot.php") ?>