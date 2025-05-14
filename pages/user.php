<?php 
    session_start();
    // Check if user is logged in
    if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "user") {
        header("Location: login.php");
        exit();
    }
    $style ='user.css';
    $hide_sidebar = true;
    require('partials/head.php'); ?>
    <header>
      <div class="container">
        <div class="logo">
          <img
            src="../assets/images/logo-cupangwest.png"
            width="70px"
            height="70px"
            alt="Cupang West Logo"
          />
          <h1>Barangay Cupang West</h1>
        </div>
        <nav>
          <ul>
            <li><a href="#home" class="active">Home</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li class="user-menu">
              <a href="javascript:void(0);" onclick="window.location.href='../controllers/logout.php'" class="logout-btn">Logout</a>
            </li>
          </ul>
        </nav>
      </div>
    </header>

    <section id="home" class="hero">
      <div class="container">
        <h1>Welcome to Barangay Cupang West</h1>
        <p>
          A progressive, peaceful, healthy and environment-friendly community
        </p>
      </div>
    </section>

    <section class="mission-vision">
      <div class="container">
        <div class="mission">
          <h2>Mission</h2>
          <p>
            We, the leaders and residents of Barangay Cupang
            West, commit ourselves to work together towards building a vibrant
            and harmonious community. Our mission is to provide excellent public
            service, ensure the well-being of our people, and promote social,
            economic, and environmental development that respects our cultural
            heritage and preserves the environment.
          </p>
        </div>
        <div class="vision">
          <h2>Vision</h2>
          <p>
            To be a united and progressive barangay where every resident enjoys
            a safe, inclusive, and empowered community through responsive
            services and participatory governance.
          </p>
        </div>
      </div>
    </section>

    <section id="services" class="services">
      <div class="container">
        <h2>Our Services</h2>
      </div>
    </section>

    <section class="service-items">
      <div class="container">
        <div class="service-grid">
          <div class="service-image">
            <img src="../assets/images/health.jpg" alt="Health Services" />
          </div>
          <div class="service-text right">
            <h3>Health Services</h3>
            <p>
              We provide free medical check-ups and medicines to our residents.
            </p>
          </div>

          <div class="service-text left">
            <h3>Education Programs</h3>
            <p>
              We have a scholarship program for deserving students in our
              community.
            </p>
          </div>
          <div class="service-image">
            <img
              src="../assets/images/education.jpg"
              alt="Education Programs"
            />
          </div>

          <div class="service-image">
            <img
              src="../assets/images/disaster-preparedness.jpg"
              alt="Disaster Preparedness"
            />
          </div>
          <div class="service-text right">
            <h3>Disaster Preparedness</h3>
            <p>
              We conduct regular drills and seminars on disaster preparedness.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section id="about" class="about">
      <div class="container">
        <h2>About Us</h2>
        <p>
          Barangay Cupang West is one of the barangays in Balanga City, Bataan.
          We serve over 25,000 residents.
        </p>

        <div class="about-images">
          <img src="../assets/images/barangay-hall.png" alt="Barangay Hall" />
          <div class="about-text">
            <p>
              Cupang West, a vibrant barangay in Balanga City, Bataan, traces
              its roots to a quiet farming community that began to flourish in
              the mid-20th century. Originally part of a larger agricultural
              expanse, Cupang West gained recognition as a separate barangay in
              the early 1970s, following the growth of local settlements and
              small industries.
            </p>
            <p>
              As Balanga transitioned into a thriving component city, Cupang
              West developed steadily, balancing urban progress with its deep
              community ties. Over the years, the barangay has become known for
              its peaceful neighborhoods, active civic programs, and
              contributions to the city's economic and cultural development.
            </p>
            <p>
              Today, Cupang West stands as a dynamic and welcoming community,
              proud of its heritage and committed to shaping a better future for
              the next generation of Balangueños.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section id="events" class="events">
      <div class="container">
        <h2>Upcoming Events</h2>

        <div class="event-grid">
          <div class="event-item">
            <div class="event-icon">
              <i class="fa-solid fa-person-running"></i>
            </div>
            <div class="event-details">
              <h3>Hataw Takbo Barangay</h3>
              <p>Join us in a fun and healthy fun run!</p>
            </div>
            <div class="event-date">
              <span>May 19, 2025</span>
            </div>
          </div>

          <div class="event-item">
            <div class="event-icon">
              <i class="fa-solid fa-champagne-glasses"></i>
            </div>
            <div class="event-details">
              <h3>Barangay Fiesta</h3>
              <p>Join us for a day full of fun, excitement, and surprises!</p>
            </div>
            <div class="event-date">
              <span>May 25, 2025</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="testimonials" class="testimonials">
      <div class="container">
        <h2>What Our Residents Say</h2>

        <div class="testimonial-grid">
          <div class="testimonial-card">
            <div class="testimonial-content">
              <p>
                "The residents in this barangay is very kind and hospitable."
              </p>
            </div>
            <div class="testimonial-author">
              <span>- Mara Sampang</span>
            </div>
          </div>

          <div class="testimonial-card">
            <div class="testimonial-content">
              <p>
                "There are establishments nearby which makes it easier to get
                the things you need for your everyday lives."
              </p>
            </div>
            <div class="testimonial-author">
              <span>- Aliah Javier</span>
            </div>
          </div>

          <div class="testimonial-card">
            <div class="testimonial-content">
              <p>
                "There are plenty of programs and services offered by the
                barangay that are beneficial for its residents. "
              </p>
            </div>
            <div class="testimonial-author">
              <span>- Mark Reyes</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="contact" class="contact">
      <div class="container">
        <h2>Contact Us</h2>
        <div class="contact-info">
          <p><strong>Address:</strong> Cupang West, Balanga City</p>
          <p><strong>Phone:</strong> (04) 1234-456</p>
          <p><strong>Email:</strong> barangaycupangwest@gmail.com</p>
          <p>
            <strong>Office Hours:</strong> Monday to Friday, 8:00 AM to 5:00 PM
          </p>
        </div>
      </div>
    </section>

    <footer>
      <div class="container">
        <div class="footer-content">
          <div class="footer-info">
            <p>© 2023 Barangay Cupang West. All rights reserved.</p>
          </div>
          <div class="footer-contact">
            <p>Contact us: (04) 1234-456 • barangaycupangwest@gmail.com</p>
          </div>
        </div>
      </div>
    </footer>

    <script src="../assets/js/user.js"></script>
  </body>
</html>
