<?php
    include("../database.php");
    $conn = getDatabaseConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["residentadd"])) {
            $surname = $_POST["surname"];
            $firstname = $_POST["fname"];
            $midname = $_POST["mname"];
            $age = $_POST["age"];
            $sex = $_POST["sex"];
            $address = $_POST["address"];
            $contact = $_POST["contact"];

            if ($sex === "not selected") {
                echo "Please select a valid sex.";
                exit;
            }

            // Use prepared statements to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO resident (surname, firstname, middlename, age, sex, address, contact) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssisss", $surname, $firstname, $midname, $age, $sex, $address, $contact);

            if ($stmt->execute()) {
                header("Location: ../pages/residents.php"); // Redirect to residents.php
                exit;
            } else {
                echo "Failed to add resident.";
            }

            $stmt->close();
        }
    }

    mysqli_close($conn);
?>