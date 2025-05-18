<div class="sidebar">
        <div class="system-title">
            <h2>Cupang West Management</h2>
        </div>
        <div class="profile">
            <img src="../assets/images/alden-FREAKchards.jpg" alt="Profile Image">
            <h3>𝓪𝓵𝓭𝓮𝓷 𝓯𝓻𝓮𝓪𝓴𝓬𝓱𝓪𝓻𝓭𝓼</h3>
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="material-icons">dashboard</i>Dashboard</a></li>
                <li><a href="residents.php"><i class="material-icons">people</i>Resident Management</a></li>
                <li><a href="certificates.php"><i class="material-icons">description</i>Certificate Issuance</a></li>
                <li><a href="blotter.php"><i class="material-icons">gavel</i>Blotter Records</a></li>
                <li><a href="reports.php"><i class="material-icons">assessment</i>Reports</a></li>
            </ul>
        </nav>
        <div class="user-info">
            <div>
                <img src="../assets/images/alden-FREAKchards.jpg" alt="User Image">
            </div>
            <div>
                <p><?= $_SESSION['username'] ?></p>
            </div>
            <div>
                <a href="../controllers/logout.php"><i class="material-icons">logout</i></a>
            </div>
        </div>
    </div>