<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    include("../database.php");
    $conn = getDatabaseConnection();
    
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Check if username exists in database
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    $response = array(
        'available' => (mysqli_num_rows($result) === 0)
    );
    
    mysqli_stmt_close($check_stmt);
    mysqli_close($conn);
    
    echo json_encode($response);
} else {
    // Invalid request
    echo json_encode(array('error' => 'Invalid request'));
}
?> 