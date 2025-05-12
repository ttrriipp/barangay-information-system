<?php $style = 'main.css';
  require("partials/head.php"); ?>
<?php require("partials/sidebar.php") ?>
<div class="main-content">
  <h1 class="reports-title">Reports</h1>
  <div class="reports-grid">
    <div class="report-card">
      <h2>Program and Initiatives</h2>
      <a href="link-to-program-and-initiatives.html">
        <img
          src="your-image-url-here.png"
          alt="Program and Initiatives Graph"
        />
      </a>
    </div>
    <div class="report-card">
      <h2>Number of Household</h2>
      <a href="link-to-number-of-household.html">
        <img
          src="your-image-url-here.png"
          alt="Number of Household Graph"
        />
      </a>
    </div>
    <div class="report-card">
      <h2>Percentage of Population</h2>
      <a href="link-to-percentage-of-population.html">
        <img
          src="your-image-url-here.png"
          alt="Percentage of Population Graph"
        />
      </a>
    </div>
    <div class="report-card">
      <h2>Social Development</h2>
      <a href="link-to-social-development.html">
        <img
          src="your-image-url-here.png"
          alt="Social Development Pie Chart"
        />
      </a>
    </div>
  </div>
</div>
<?php require("partials/foot.php");