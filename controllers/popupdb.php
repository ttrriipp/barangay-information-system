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
                    $stmt = $conn->prepare("INSERT INTO residents (surname, firstname, middlename, age, sex, address, contact) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssisss", $surname, $firstname, $midname, $age, $sex, $address, $contact);
                } else {
                    $stmt = $conn->prepare("INSERT INTO residents (surname, firstname, middlename, age, sex, address, contact, household_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssisssi", $surname, $firstname, $midname, $age, $sex, $address, $contact, $household_id);
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
            $age = $_POST["age"];
            $sex = $_POST["sex"];
            $address = $_POST["address"];
            $contact = $_POST["contact"];
            
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
                    $stmt = $conn->prepare("UPDATE residents SET surname=?, firstname=?, middlename=?, age=?, sex=?, address=?, contact=?, household_id=NULL WHERE id=?");
                    $stmt->bind_param("sssisssi", $surname, $firstname, $midname, $age, $sex, $address, $contact, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE residents SET surname=?, firstname=?, middlename=?, age=?, sex=?, address=?, contact=?, household_id=? WHERE id=?");
                    $stmt->bind_param("sssisssii", $surname, $firstname, $midname, $age, $sex, $address, $contact, $household_id, $id);
                }

                if (!$stmt->execute()) {
                    throw new Exception("Failed to update resident: " . $stmt->error);
                }
                
                $stmt->close();
                
                // Handle household_members table synchronization
                if ($current_household_id !== $household_id) {
                    // If old household exists, remove the resident from its members
                    if ($current_household_id !== null) {
                        $deleteStmt = $conn->prepare("DELETE FROM household_members WHERE household_id = ? AND resident_id = ?");
                        $deleteStmt->bind_param("ii", $current_household_id, $id);
                        
                        if (!$deleteStmt->execute()) {
                            throw new Exception("Failed to remove resident from previous household: " . $deleteStmt->error);
                        }
                        
                        $deleteStmt->close();
                    }
                    
                    // If new household exists, add the resident to its members
                    if ($household_id !== null) {
                        // Check if entry already exists to prevent duplicates
                        $checkMemberStmt = $conn->prepare("SELECT id FROM household_members WHERE household_id = ? AND resident_id = ?");
                        $checkMemberStmt->bind_param("ii", $household_id, $id);
                        $checkMemberStmt->execute();
                        $checkResult = $checkMemberStmt->get_result();
                        
                        if ($checkResult->num_rows === 0) {
                            $addStmt = $conn->prepare("INSERT INTO household_members (household_id, resident_id) VALUES (?, ?)");
                            $addStmt->bind_param("ii", $household_id, $id);
                            
                            if (!$addStmt->execute()) {
                                throw new Exception("Failed to add resident to new household: " . $addStmt->error);
                            }
                            
                            $addStmt->close();
                        }
                        
                        $checkMemberStmt->close();
                    }
                }
                
                // Commit the transaction
                $conn->commit();
                
                header("Location: ../pages/residents.php");
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