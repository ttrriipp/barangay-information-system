<?php
// Include database connection file
include("database.php");

// Connect to database
$conn = getDatabaseConnection();

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Read the SQL file
$sql = file_get_contents('create_household_tables.sql');

// Execute multi query
if (mysqli_multi_query($conn, $sql)) {
    echo "Household tables created successfully.<br>";
    
    // Process all result sets
    do {
        // Get result (if any)
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    
    echo "Database setup completed.<br>";
    echo "<a href='index.php'>Go to home page</a>";
} else {
    echo "Error creating household tables: " . mysqli_error($conn);
}

// Close connection
mysqli_close($conn);
?> 