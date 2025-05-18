<?php
include("../../database.php");
$conn = getDatabaseConnection();

$resident = [
    'surname' => '',
    'fname' => '',
    'mname' => '',
    'birthdate' => '',
    'age' => '',
    'sex' => '',
    'address' => '',
    'contact' => '',
    'email' => '',
    'civil_status' => '',
    'occupation' => '',
    'education' => '',
    'voter_status' => '',
    'pwd_status' => '',
    'philhealth_status' => '',
    '4ps_status' => '',
    'emergency_contact_name' => '',
    'emergency_contact_number' => '',
    'blood_type' => '',
    'religion' => '',
    'nationality' => 'Filipino',
    'date_of_residency' => ''
];
$isEdit = false;

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT surname, firstname, middlename, birthdate, age, sex, address, contact, email, 
                          civil_status, occupation, education, voter_status, pwd_status, 
                          philhealth_status, 4ps_status, emergency_contact_name, emergency_contact_number,
                          blood_type, religion, nationality, date_of_residency 
                          FROM residents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result(
        $resident['surname'], $resident['fname'], $resident['mname'], 
        $resident['birthdate'], $resident['age'], $resident['sex'],
        $resident['address'], $resident['contact'], $resident['email'],
        $resident['civil_status'], $resident['occupation'], $resident['education'],
        $resident['voter_status'], $resident['pwd_status'], $resident['philhealth_status'],
        $resident['4ps_status'], $resident['emergency_contact_name'], $resident['emergency_contact_number'],
        $resident['blood_type'], $resident['religion'], $resident['nationality'], 
        $resident['date_of_residency']
    );
    $stmt->fetch();
    $stmt->close();
}
?>
<div class="modal-content-header">
    <img src="../assets/images/logo-cupangwest.png" alt="Cupang West Logo" class="header-logo" />
    <h2><?= $isEdit ? 'Edit Resident' : 'Add New Resident' ?></h2>
</div>

<div class="modal-flex-container">
    <div class="form-container">
        <form id="residentForm" action="../controllers/popupdb.php" method="POST">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
            <?php endif; ?>

            <h2 class="form-section-title">Basic Information</h2>
            
            <label for="surname">Surname:</label>
            <input type="text" id="surname" placeholder="Surname" name="surname" required value="<?= htmlspecialchars($resident['surname']) ?>" />

            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" placeholder="First Name" name="fname" required value="<?= htmlspecialchars($resident['fname']) ?>" />

            <label for="middleName">Middle Name:</label>
            <input type="text" id="middleName" placeholder="Middle Name" name="mname" value="<?= htmlspecialchars($resident['mname']) ?>" />

            <label for="birthdate">Date of Birth:</label>
            <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($resident['birthdate']) ?>" onchange="calculateAge()" />

            <label for="age">Age:</label>
            <input type="number" id="age" placeholder="Age" name="age" required min="0" max="120" value="<?= htmlspecialchars($resident['age']) ?>" />

            <label for="sex">Sex:</label>
            <select id="sex" name="sex" required>
                    <option value="not selected" <?= $resident['sex'] == 'not selected' ? 'selected' : '' ?>>Select</option>
                    <option value="Male" <?= $resident['sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $resident['sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $resident['sex'] == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>

            <label for="civil_status">Civil Status:</label>
            <select id="civil_status" name="civil_status">
                <option value="" <?= $resident['civil_status'] == '' ? 'selected' : '' ?>>Select</option>
                <option value="Single" <?= $resident['civil_status'] == 'Single' ? 'selected' : '' ?>>Single</option>
                <option value="Married" <?= $resident['civil_status'] == 'Married' ? 'selected' : '' ?>>Married</option>
                <option value="Widowed" <?= $resident['civil_status'] == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                <option value="Separated" <?= $resident['civil_status'] == 'Separated' ? 'selected' : '' ?>>Separated</option>
                <option value="Divorced" <?= $resident['civil_status'] == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
            </select>
            
            <h2 class="form-section-title">Contact Information</h2>

            <label for="Address">Address:</label>
            <input type="text" id="Address" placeholder="Address" name="address" required value="<?= htmlspecialchars($resident['address']) ?>" />

            <label for="contactNo">Contact No.:</label>
            <input type="text" id="contactNo" placeholder="Contact No." name="contact" value="<?= htmlspecialchars($resident['contact']) ?>" />
            
            <label for="email">Email Address:</label>
            <input type="email" id="email" placeholder="Email Address" name="email" value="<?= htmlspecialchars($resident['email']) ?>" />
            
            <label for="emergency_contact_name">Emergency Contact Name:</label>
            <input type="text" id="emergency_contact_name" placeholder="Emergency Contact Name" name="emergency_contact_name" value="<?= htmlspecialchars($resident['emergency_contact_name']) ?>" />
            
            <label for="emergency_contact_number">Emergency Contact Number:</label>
            <input type="text" id="emergency_contact_number" placeholder="Emergency Contact Number" name="emergency_contact_number" value="<?= htmlspecialchars($resident['emergency_contact_number']) ?>" />
            
            <h2 class="form-section-title">Additional Information</h2>
            
            <label for="nationality">Nationality:</label>
            <input type="text" id="nationality" placeholder="Nationality" name="nationality" value="<?= htmlspecialchars($resident['nationality'] ? $resident['nationality'] : 'Filipino') ?>" />
            
            <label for="occupation">Occupation:</label>
            <input type="text" id="occupation" placeholder="Occupation" name="occupation" value="<?= htmlspecialchars($resident['occupation']) ?>" />
            
            <label for="education">Educational Attainment:</label>
            <select id="education" name="education">
                <option value="" <?= $resident['education'] == '' ? 'selected' : '' ?>>Select</option>
                <option value="None" <?= $resident['education'] == 'None' ? 'selected' : '' ?>>None</option>
                <option value="Elementary" <?= $resident['education'] == 'Elementary' ? 'selected' : '' ?>>Elementary</option>
                <option value="High School" <?= $resident['education'] == 'High School' ? 'selected' : '' ?>>High School</option>
                <option value="Vocational" <?= $resident['education'] == 'Vocational' ? 'selected' : '' ?>>Vocational</option>
                <option value="College" <?= $resident['education'] == 'College' ? 'selected' : '' ?>>College</option>
                <option value="Post-Graduate" <?= $resident['education'] == 'Post-Graduate' ? 'selected' : '' ?>>Post-Graduate</option>
            </select>
            
            <label for="religion">Religion:</label>
            <input type="text" id="religion" placeholder="Religion" name="religion" value="<?= htmlspecialchars($resident['religion']) ?>" />
            
            <label for="blood_type">Blood Type:</label>
            <select id="blood_type" name="blood_type">
                <option value="" <?= $resident['blood_type'] == '' ? 'selected' : '' ?>>Select</option>
                <option value="A+" <?= $resident['blood_type'] == 'A+' ? 'selected' : '' ?>>A+</option>
                <option value="A-" <?= $resident['blood_type'] == 'A-' ? 'selected' : '' ?>>A-</option>
                <option value="B+" <?= $resident['blood_type'] == 'B+' ? 'selected' : '' ?>>B+</option>
                <option value="B-" <?= $resident['blood_type'] == 'B-' ? 'selected' : '' ?>>B-</option>
                <option value="AB+" <?= $resident['blood_type'] == 'AB+' ? 'selected' : '' ?>>AB+</option>
                <option value="AB-" <?= $resident['blood_type'] == 'AB-' ? 'selected' : '' ?>>AB-</option>
                <option value="O+" <?= $resident['blood_type'] == 'O+' ? 'selected' : '' ?>>O+</option>
                <option value="O-" <?= $resident['blood_type'] == 'O-' ? 'selected' : '' ?>>O-</option>
            </select>
            
            <label for="date_of_residency">Date of Residency:</label>
            <input type="date" id="date_of_residency" name="date_of_residency" value="<?= htmlspecialchars($resident['date_of_residency']) ?>" />
            
            <h2 class="form-section-title">Status Information</h2>
            
            <label for="voter_status">Voter Status:</label>
            <select id="voter_status" name="voter_status">
                <option value="" <?= $resident['voter_status'] == '' ? 'selected' : '' ?>>Select</option>
                <option value="Registered" <?= $resident['voter_status'] == 'Registered' ? 'selected' : '' ?>>Registered</option>
                <option value="Not Registered" <?= $resident['voter_status'] == 'Not Registered' ? 'selected' : '' ?>>Not Registered</option>
            </select>
            
            <label for="pwd_status">PWD Status:</label>
            <select id="pwd_status" name="pwd_status">
                <option value="" <?= $resident['pwd_status'] == '' ? 'selected' : '' ?>>Select</option>
                <option value="Yes" <?= $resident['pwd_status'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= $resident['pwd_status'] == 'No' ? 'selected' : '' ?>>No</option>
            </select>
            
            <label for="philhealth_status">PhilHealth Status:</label>
            <select id="philhealth_status" name="philhealth_status">
                <option value="" <?= $resident['philhealth_status'] == '' ? 'selected' : '' ?>>Select</option>
                <option value="Member" <?= $resident['philhealth_status'] == 'Member' ? 'selected' : '' ?>>Member</option>
                <option value="Dependent" <?= $resident['philhealth_status'] == 'Dependent' ? 'selected' : '' ?>>Dependent</option>
                <option value="Not a Member" <?= $resident['philhealth_status'] == 'Not a Member' ? 'selected' : '' ?>>Not a Member</option>
            </select>
            
            <label for="4ps_status">4Ps Beneficiary:</label>
            <select id="4ps_status" name="4ps_status">
                <option value="" <?= $resident['4ps_status'] == '' ? 'selected' : '' ?>>Select</option>
                <option value="Yes" <?= $resident['4ps_status'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= $resident['4ps_status'] == 'No' ? 'selected' : '' ?>>No</option>
            </select>

            <button type="submit" name="<?= $isEdit ? 'residentedit' : 'residentadd' ?>">
                <?= $isEdit ? 'Update' : 'Submit' ?>
            </button>
        </form>
    </div>
</div> 

<script>
function calculateAge() {
    const birthdateInput = document.getElementById('birthdate');
    const ageInput = document.getElementById('age');
    
    if (birthdateInput.value) {
        const birthdate = new Date(birthdateInput.value);
        const today = new Date();
        let age = today.getFullYear() - birthdate.getFullYear();
        const monthDiff = today.getMonth() - birthdate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdate.getDate())) {
            age--;
        }
        
        ageInput.value = age;
    }
}
</script>

<style>
.modal-content-header {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    flex-direction: column;
    text-align: center;
}

.modal-content-header h2 {
    color: white;
    margin: 10px 0 0 0;
    font-size: 1.8rem;
}

.header-logo {
    width: 120px;
    height: auto;
    margin: 0 auto;
}

.modal-flex-container {
    display: flex;
    justify-content: center;
}

.form-container {
    width: 100% !important;
    max-width: 700px;
}

.form-section-title {
    width: 100%;
    text-align: left;
    color:rgb(199, 200, 209);
    margin-top: 20px;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #e0e0e0;
}

/* Reset and normalize all form elements */
#residentForm input,
#residentForm select,
#residentForm button {
    margin: 0 !important;
    box-sizing: border-box !important;
    max-width: none !important;
    min-width: 0 !important;
}

/* Consistent styling for all input types */
#residentForm input[type="text"],
#residentForm input[type="number"],
#residentForm input[type="email"],
#residentForm input[type="date"],
#residentForm select {
    display: block !important;
    width: 100% !important;
    height: 40px !important;
    padding: 8px 12px !important;
    margin-bottom: 15px !important;
    border: 1px solid #ddd !important;
    border-radius: 4px !important;
    background-color: #fff !important;
    font-size: 16px !important;
    line-height: 1.5 !important;
}

/* Ensure date inputs don't get browser-specific sizing */
#residentForm input[type="date"] {
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
}

/* Force select dropdowns to match other inputs */
#residentForm select {
    background-image: url("data:image/svg+xml;utf8,<svg fill='black' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>") !important;
    background-repeat: no-repeat !important;
    background-position: right 8px center !important;
    padding-right: 30px !important;
    text-overflow: ellipsis !important;
}

/* Ensure each form field container is the same width */
#residentForm label {
    display: block !important;
    margin-bottom: 5px !important;
}

#residentForm {
    padding: 0 15px;
}

#residentForm button[type="submit"] {
    background-color: #4CAF50 !important;
    color: white !important;
    font-weight: bold !important;
    margin-top: 20px !important;
    padding: 12px 25px !important;
    width: auto !important;
    transition: all 0.3s ease !important;
}

#residentForm button[type="submit"]:hover {
    background-color: #45a049 !important;
    transform: translateY(-2px) !important;
}

@media (max-width: 768px) {
    .header-logo {
        width: 80px;
    }
    
    .modal-content-header h2 {
        font-size: 1.5rem;
    }
    
    #residentForm {
        padding: 0 10px;
    }
}
</style> 