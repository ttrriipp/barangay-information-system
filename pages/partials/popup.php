<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barangay Cupang West Form</title>
    <link rel="stylesheet" href="../../assets/css/popup.css" />
  </head>
  <body>
    <div class="container">
      <div class="form-container">
        <form id="residentForm" action="/integ-proj/barangay-information-system/controllers/popupdb.php" method="POST">
          <label for="surname">Surname:</label>
          <input type="text" id="surname" placeholder="Surname" name="surname" required />

          <label for="firstName">First Name:</label>
          <input type="text" id="firstName" placeholder="First Name" name="fname" required />

          <label for="middleName">Middle Name:</label>
          <input type="text" id="middleName" placeholder="Middle Name" name="mname" required />

          <label for="age">Age:</label>
          <input type="text" id="age" placeholder="Age" name="age" required />

          <p class="label">Sex:</p>
          <div class="sex">
            <select name="sex" required>
              <option value="not selected">Select</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <label for="Address">Address:</label>
          <input type="text" id="Address" placeholder="Address" name="address" required />

          <label for="contactNo">Contact No.:</label>
          <input type="text" id="contactNo" placeholder="Contact No." name="contact" required />

          <button type="submit" value="register" name="residentadd">Submit</button>
        </form>
      </div>
      <div class="logo-container">
        <img src="your-logo-url-here.png" alt="Cupang West Logo" />
      </div>
    </div>
    <script src="popup.js"></script>
  </body>
</html>

