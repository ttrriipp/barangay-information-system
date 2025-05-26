<?php
    include("../database.php");
    $conn = getDatabaseConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["residentadd"])) {
            $surname = $_POST["surname"];
            $firstname = $_POST["fname"];
            $midname = $_POST["mname"];
            $birthdate = !empty($_POST["birthdate"]) ? $_POST["birthdate"] : null;
            $sex = $_POST["sex"];
            $address = $_POST["address"];
            $contact = !empty($_POST["contact"]) ? $_POST["contact"] : null;
            $email = !empty($_POST["email"]) ? $_POST["email"] : null;
            $civil_status = !empty($_POST["civil_status"]) ? $_POST["civil_status"] : null;
            $occupation = !empty($_POST["occupation"]) ? $_POST["occupation"] : null;
            $education = !empty($_POST["education"]) ? $_POST["education"] : null;
            $voter_status = !empty($_POST["voter_status"]) ? $_POST["voter_status"] : null;
            $pwd_status = !empty($_POST["pwd_status"]) ? $_POST["pwd_status"] : null;
            $philhealth_status = !empty($_POST["philhealth_status"]) ? $_POST["philhealth_status"] : null;
            $fourps_status = !empty($_POST["4ps_status"]) ? $_POST["4ps_status"] : null;
            $emergency_contact_name = !empty($_POST["emergency_contact_name"]) ? $_POST["emergency_contact_name"] : null;
            $emergency_contact_number = !empty($_POST["emergency_contact_number"]) ? $_POST["emergency_contact_number"] : null;
            $blood_type = !empty($_POST["blood_type"]) ? $_POST["blood_type"] : null;
            $religion = !empty($_POST["religion"]) ? $_POST["religion"] : null;
            $nationality = !empty($_POST["nationality"]) ? $_POST["nationality"] : "Filipino";
            $date_of_residency = !empty($_POST["date_of_residency"]) ? $_POST["date_of_residency"] : null;

            if ($sex === "not selected") {
                echo "Please select a valid sex.";
                exit;
            }

            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // Use prepared statements to prevent SQL injection
                $stmt = $conn->prepare("INSERT INTO residents (
                    surname, firstname, middlename, birthdate, sex, 
                    address, contact, email, civil_status, occupation, 
                    education, voter_status, pwd_status, philhealth_status, 
                    `4ps_status`, emergency_contact_name, emergency_contact_number, 
                    blood_type, religion, nationality, date_of_residency
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssssssssssssssss", 
                    $surname, $firstname, $midname, $birthdate, $sex, 
                    $address, $contact, $email, $civil_status, $occupation, 
                    $education, $voter_status, $pwd_status, $philhealth_status, 
                    $fourps_status, $emergency_contact_name, $emergency_contact_number, 
                    $blood_type, $religion, $nationality, $date_of_residency
                );

                if (!$stmt->execute()) {
                    throw new Exception("Failed to add resident: " . $stmt->error);
                }
                
                $stmt->close();
                
                // Commit the transaction
                $conn->commit();
                
                header("Location: ../pages/residents.php"); // Redirect to residents.php
                exit;
            } catch (Exception $e) {
                // Roll back on error
                $conn->rollback();
                echo "Error: " . $e->getMessage();
                exit;
            }
        } elseif (isset($_POST["residentedit"])) {
            $id = intval($_POST["id"]);
            $surname = $_POST["surname"];
            $firstname = $_POST["fname"];
            $midname = $_POST["mname"];
            $birthdate = !empty($_POST["birthdate"]) ? $_POST["birthdate"] : null;
            $sex = $_POST["sex"];
            $address = $_POST["address"];
            $contact = !empty($_POST["contact"]) ? $_POST["contact"] : null;
            $email = !empty($_POST["email"]) ? $_POST["email"] : null;
            $civil_status = !empty($_POST["civil_status"]) ? $_POST["civil_status"] : null;
            $occupation = !empty($_POST["occupation"]) ? $_POST["occupation"] : null;
            $education = !empty($_POST["education"]) ? $_POST["education"] : null;
            $voter_status = !empty($_POST["voter_status"]) ? $_POST["voter_status"] : null;
            $pwd_status = !empty($_POST["pwd_status"]) ? $_POST["pwd_status"] : null;
            $philhealth_status = !empty($_POST["philhealth_status"]) ? $_POST["philhealth_status"] : null;
            $fourps_status = !empty($_POST["4ps_status"]) ? $_POST["4ps_status"] : null;
            $emergency_contact_name = !empty($_POST["emergency_contact_name"]) ? $_POST["emergency_contact_name"] : null;
            $emergency_contact_number = !empty($_POST["emergency_contact_number"]) ? $_POST["emergency_contact_number"] : null;
            $blood_type = !empty($_POST["blood_type"]) ? $_POST["blood_type"] : null;
            $religion = !empty($_POST["religion"]) ? $_POST["religion"] : null;
            $nationality = !empty($_POST["nationality"]) ? $_POST["nationality"] : "Filipino";
            $date_of_residency = !empty($_POST["date_of_residency"]) ? $_POST["date_of_residency"] : null;

            if ($sex === "not selected") {
                echo "Please select a valid sex.";
                exit;
            }
            
            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // Update resident record
                $stmt = $conn->prepare("UPDATE residents SET 
                    surname=?, firstname=?, middlename=?, birthdate=?, sex=?, 
                    address=?, contact=?, email=?, civil_status=?, occupation=?, 
                    education=?, voter_status=?, pwd_status=?, philhealth_status=?, 
                    `4ps_status`=?, emergency_contact_name=?, emergency_contact_number=?, 
                    blood_type=?, religion=?, nationality=?, date_of_residency=?
                    WHERE id=?");
                $stmt->bind_param("sssssssssssssssssssssi",
                    $surname, $firstname, $midname, $birthdate, $sex, 
                    $address, $contact, $email, $civil_status, $occupation, 
                    $education, $voter_status, $pwd_status, $philhealth_status, 
                    $fourps_status, $emergency_contact_name, $emergency_contact_number, 
                    $blood_type, $religion, $nationality, $date_of_residency, $id);

                if (!$stmt->execute()) {
                    throw new Exception("Failed to update resident: " . $stmt->error);
                }
                
                $stmt->close();
                
                // Commit transaction
                $conn->commit();
                
                header("Location: ../pages/residents.php"); // Redirect to residents.php
                exit;
            } catch (Exception $e) {
                // Roll back on error
                $conn->rollback();
                echo "Error: " . $e->getMessage();
                exit;
            }
        }
    }

    mysqli_close($conn);
?>