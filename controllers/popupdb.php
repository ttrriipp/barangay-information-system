<?php
    include("../database.php");
    $conn = getDatabaseConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["residentadd"])) {
            $surname = $_POST["surname"];
            $firstname = $_POST["fname"];
            $midname = $_POST["mname"];
            $birthdate = !empty($_POST["birthdate"]) ? $_POST["birthdate"] : null;
            $age = $_POST["age"];
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
            $household_id = !empty($_POST["household_id"]) ? $_POST["household_id"] : null;

            if ($sex === "not selected") {
                echo "Please select a valid sex.";
                exit;
            }

            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // Use prepared statements to prevent SQL injection
                if ($household_id === null) {
                    $stmt = $conn->prepare("INSERT INTO residents (
                        surname, firstname, middlename, birthdate, age, sex, 
                        address, contact, email, civil_status, occupation, 
                        education, voter_status, pwd_status, philhealth_status, 
                        `4ps_status`, emergency_contact_name, emergency_contact_number, 
                        blood_type, religion, nationality, date_of_residency
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssississssssssssssss", 
                        $surname, $firstname, $midname, $birthdate, $age, $sex, 
                        $address, $contact, $email, $civil_status, $occupation, 
                        $education, $voter_status, $pwd_status, $philhealth_status, 
                        $fourps_status, $emergency_contact_name, $emergency_contact_number, 
                        $blood_type, $religion, $nationality, $date_of_residency
                    );
                } else {
                    $stmt = $conn->prepare("INSERT INTO residents (
                        surname, firstname, middlename, birthdate, age, sex, 
                        address, contact, email, civil_status, occupation, 
                        education, voter_status, pwd_status, philhealth_status, 
                        `4ps_status`, emergency_contact_name, emergency_contact_number, 
                        blood_type, religion, nationality, date_of_residency, household_id
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssississssssssssssssi", 
                        $surname, $firstname, $midname, $birthdate, $age, $sex, 
                        $address, $contact, $email, $civil_status, $occupation, 
                        $education, $voter_status, $pwd_status, $philhealth_status, 
                        $fourps_status, $emergency_contact_name, $emergency_contact_number, 
                        $blood_type, $religion, $nationality, $date_of_residency, $household_id
                    );
                }

                if (!$stmt->execute()) {
                    throw new Exception("Failed to add resident: " . $stmt->error);
                }
                
                // Get the newly inserted resident ID
                $resident_id = $conn->insert_id;
                $stmt->close();
                
                // If household_id is provided, add to household_members table
                if ($household_id !== null) {
                    // Check if entry already exists to prevent duplicates
                    $checkStmt = $conn->prepare("SELECT id FROM household_members WHERE household_id = ? AND resident_id = ?");
                    $checkStmt->bind_param("ii", $household_id, $resident_id);
                    $checkStmt->execute();
                    $checkResult = $checkStmt->get_result();
                    
                    if ($checkResult->num_rows === 0) {
                        $memberStmt = $conn->prepare("INSERT INTO household_members (household_id, resident_id) VALUES (?, ?)");
                        $memberStmt->bind_param("ii", $household_id, $resident_id);
                        
                        if (!$memberStmt->execute()) {
                            throw new Exception("Failed to add resident to household members: " . $memberStmt->error);
                        }
                        
                        $memberStmt->close();
                    }
                    
                    $checkStmt->close();
                }
                
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
            $age = $_POST["age"];
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
            
            // Get the current household_id before updating
            $checkStmt = $conn->prepare("SELECT household_id FROM residents WHERE id = ?");
            $checkStmt->bind_param("i", $id);
            $checkStmt->execute();
            $checkStmt->bind_result($current_household_id);
            $checkStmt->fetch();
            $checkStmt->close();
            
            $household_id = !empty($_POST["household_id"]) ? $_POST["household_id"] : null;

            if ($sex === "not selected") {
                echo "Please select a valid sex.";
                exit;
            }
            
            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // Update resident record
                if ($household_id === null) {
                    $stmt = $conn->prepare("UPDATE residents SET 
                        surname=?, firstname=?, middlename=?, birthdate=?, age=?, sex=?, 
                        address=?, contact=?, email=?, civil_status=?, occupation=?, 
                        education=?, voter_status=?, pwd_status=?, philhealth_status=?, 
                        `4ps_status`=?, emergency_contact_name=?, emergency_contact_number=?, 
                        blood_type=?, religion=?, nationality=?, date_of_residency=?, 
                        household_id=NULL 
                        WHERE id=?");
                    $stmt->bind_param("ssssississssssssssssssi",
                        $surname, $firstname, $midname, $birthdate, $age, $sex, 
                        $address, $contact, $email, $civil_status, $occupation, 
                        $education, $voter_status, $pwd_status, $philhealth_status, 
                        $fourps_status, $emergency_contact_name, $emergency_contact_number, 
                        $blood_type, $religion, $nationality, $date_of_residency, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE residents SET 
                        surname=?, firstname=?, middlename=?, birthdate=?, age=?, sex=?, 
                        address=?, contact=?, email=?, civil_status=?, occupation=?, 
                        education=?, voter_status=?, pwd_status=?, philhealth_status=?, 
                        `4ps_status`=?, emergency_contact_name=?, emergency_contact_number=?, 
                        blood_type=?, religion=?, nationality=?, date_of_residency=?, 
                        household_id=? 
                        WHERE id=?");
                    $stmt->bind_param("ssssississssssssssssssii",
                        $surname, $firstname, $midname, $birthdate, $age, $sex, 
                        $address, $contact, $email, $civil_status, $occupation, 
                        $education, $voter_status, $pwd_status, $philhealth_status, 
                        $fourps_status, $emergency_contact_name, $emergency_contact_number, 
                        $blood_type, $religion, $nationality, $date_of_residency, $household_id, $id);
                }

                if (!$stmt->execute()) {
                    throw new Exception("Failed to update resident: " . $stmt->error);
                }
                
                $stmt->close();
                
                // Handle household_members table synchronization
                if ($current_household_id !== $household_id) {
                    // If there was a previous household, remove from it
                    if ($current_household_id !== null) {
                        $removeStmt = $conn->prepare("DELETE FROM household_members WHERE household_id = ? AND resident_id = ?");
                        $removeStmt->bind_param("ii", $current_household_id, $id);
                        $removeStmt->execute();
                        $removeStmt->close();
                    }
                    
                    // If there's a new household, add to it
                    if ($household_id !== null) {
                        $checkMemStmt = $conn->prepare("SELECT id FROM household_members WHERE household_id = ? AND resident_id = ?");
                        $checkMemStmt->bind_param("ii", $household_id, $id);
                        $checkMemStmt->execute();
                        $checkMemResult = $checkMemStmt->get_result();
                        
                        if ($checkMemResult->num_rows === 0) {
                            $addStmt = $conn->prepare("INSERT INTO household_members (household_id, resident_id) VALUES (?, ?)");
                            $addStmt->bind_param("ii", $household_id, $id);
                            $addStmt->execute();
                            $addStmt->close();
                        }
                        
                        $checkMemStmt->close();
                    }
                }
                
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