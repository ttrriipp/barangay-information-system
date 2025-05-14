<?php
// Make sure there's no output before this point
session_start();

// Destroy all session data
$_SESSION = array();

// If a session cookie is used, destroy it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Use JavaScript to ensure redirection works
echo '<script>window.location.href = "../pages/login.php";</script>';
exit();
?> 