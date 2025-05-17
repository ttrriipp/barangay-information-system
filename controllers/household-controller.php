<?php
include("../database.php");
$conn = getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["householdadd"])) {
        $head_id = $_POST["head_id"];
        $address = $_POST["address"];
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Insert household record
            $stmt = $conn->prepare("INSERT INTO households (head_id, address) VALUES (?, ?)");
            $stmt->bind_param("is", $head_id, $address);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to add household: " . $stmt->error);
            }
            
            $household_id = $conn->insert_id;
            $stmt->close();
            
            // Update the resident with household_id
            $updateStmt = $conn->prepare("UPDATE residents SET household_id = ? WHERE id = ?");
            
            // Update head of household
            $updateStmt->bind_param("ii", $household_id, $head_id);
            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update head of household: " . $updateStmt->error);
            }
            
            $updateStmt->close();
            
            // Commit transaction
            $conn->commit();
            
            header("Location: ../pages/household.php");
            exit;
        } catch (Exception $e) {
            // Roll back transaction on error
            $conn->rollback();
            echo "Error: " . $e->getMessage();
            exit;
        }
    } elseif (isset($_POST["householdedit"])) {
        $id = intval($_POST["id"]);
        $head_id = $_POST["head_id"];
        $address = $_POST["address"];
        $member_ids = isset($_POST["member_ids"]) ? $_POST["member_ids"] : [];
        $member_resident_ids = isset($_POST["member_resident_ids"]) ? $_POST["member_resident_ids"] : [];
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Get current head before update
            $oldHeadStmt = $conn->prepare("SELECT head_id FROM households WHERE id = ?");
            $oldHeadStmt->bind_param("i", $id);
            $oldHeadStmt->execute();
            $oldHeadStmt->bind_result($old_head_id);
            $oldHeadStmt->fetch();
            $oldHeadStmt->close();
            
            // Reset old head if changing
            if ($old_head_id != $head_id) {
                $resetOldHeadStmt = $conn->prepare("UPDATE residents SET household_id = NULL WHERE id = ?");
                $resetOldHeadStmt->bind_param("i", $old_head_id);
                $resetOldHeadStmt->execute();
                $resetOldHeadStmt->close();
            }
            
            // Update household record
            $stmt = $conn->prepare("UPDATE households SET head_id = ?, address = ? WHERE id = ?");
            $stmt->bind_param("isi", $head_id, $address, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update household: " . $stmt->error);
            }
            
            $stmt->close();
            
            // Get all existing member IDs
            $existingMembersStmt = $conn->prepare("SELECT id FROM household_members WHERE household_id = ?");
            $existingMembersStmt->bind_param("i", $id);
            $existingMembersStmt->execute();
            $existingMembersResult = $existingMembersStmt->get_result();
            
            $existingMemberIds = [];
            while ($row = $existingMembersResult->fetch_assoc()) {
                $existingMemberIds[] = $row['id'];
            }
            $existingMembersStmt->close();
            
            // Process remaining members - those in the form submission
            $remainingMemberIds = [];
            if (!empty($member_ids)) {
                $remainingMemberIds = $member_ids;
                
                // Process submitted members
                for ($i = 0; $i < count($member_ids); $i++) {
                    if (empty($member_ids[$i]) || empty($member_resident_ids[$i])) continue;
                    
                    $member_id = $member_ids[$i];
                    $resident_id = $member_resident_ids[$i];
                    
                    // Ensure the resident's household_id is properly set
                    $updateResidentStmt = $conn->prepare("UPDATE residents SET household_id = ? WHERE id = ?");
                    $updateResidentStmt->bind_param("ii", $id, $resident_id);
                    $updateResidentStmt->execute();
                    $updateResidentStmt->close();
                }
            }
            
            // Determine which members were removed
            $removedMemberIds = array_diff($existingMemberIds, $remainingMemberIds);
            
            // Process removed members
            if (!empty($removedMemberIds)) {
                // First, find the resident IDs associated with removed members
                foreach ($removedMemberIds as $removedMemberId) {
                    // Get resident ID for this member
                    $getResidentStmt = $conn->prepare("SELECT resident_id FROM household_members WHERE id = ?");
                    $getResidentStmt->bind_param("i", $removedMemberId);
                    $getResidentStmt->execute();
                    $getResidentStmt->bind_result($resident_id);
                    $getResidentStmt->fetch();
                    $getResidentStmt->close();
                    
                    // Update resident to remove household_id
                    if ($resident_id) {
                        $updateResidentStmt = $conn->prepare("UPDATE residents SET household_id = NULL WHERE id = ?");
                        $updateResidentStmt->bind_param("i", $resident_id);
                        $updateResidentStmt->execute();
                        $updateResidentStmt->close();
                    }
                    
                    // Delete the member record
                    $deleteMemberStmt = $conn->prepare("DELETE FROM household_members WHERE id = ?");
                    $deleteMemberStmt->bind_param("i", $removedMemberId);
                    $deleteMemberStmt->execute();
                    $deleteMemberStmt->close();
                }
            }
            
            // Update head resident's household_id
            $updateHeadStmt = $conn->prepare("UPDATE residents SET household_id = ? WHERE id = ?");
            $updateHeadStmt->bind_param("ii", $id, $head_id);
            $updateHeadStmt->execute();
            $updateHeadStmt->close();
            
            // Commit transaction
            $conn->commit();
            
            header("Location: ../pages/household.php");
            exit;
        } catch (Exception $e) {
            // Roll back transaction on error
            $conn->rollback();
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}

mysqli_close($conn);
?> 