<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cinema Royale - Premium Movie Experience</title>

  <link href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/index.css" />
</head>

<body>

  <div class="modal fade" id="registerModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content bg-transparent border-0 text-white">
        <div class="auth-card">
          <button class="close-btn" type="button" data-bs-dismiss="modal" aria-label="Close">×</button>
          <div class="brand-side">
            <img src="logo/Logo w text_.png" alt="Logo" class="logo-box" />
            <h2>CINEMA ROYALE</h2>
          </div>
          <div class="form-side">
            <div class="form-panel">
              <h1>Create Account</h1>

              <form id="register-form" method="POST">
                <div class="form-group">
                  <label for="signup-name">Firstname</label>
                  <input
                    id="signup-name"
                    name="fullname"
                    type="text"
                    placeholder="Enter your firstname"
                    required>
                </div>

                <div class="form-group">
                  <label for="signup-name">Last Name</label>
                  <input
                    id="signup-name"
                    name="fullname"
                    type="text"
                    placeholder="Enter your last name"
                    required>
                </div>

                <div class="form-group">
                  <label for="signup-email">Email</label>
                  <input
                    id="signup-email"
                    name="email"
                    type="email"
                    placeholder="Enter your email"
                    required>
                </div>

                <div class="form-group">
                  <label for="signup-password">Password</label>
                  <input
                    id="signup-password"
                    name="password"
                    type="password"
                    placeholder="Create a password"
                    required>
                </div>
                <button class="btn" type="submit">Create Account</button>
              </form>

              <button class="alt-text text-button" data-bs-toggle="modal" data-bs-target="#loginModal">Already have an account? <span href="login.php">Sign in</span></buttotn>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="loginModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content bg-transparent border-0 text-white">
        <div class="auth-card">
          <button class="close-btn" type="button" data-bs-dismiss="modal" aria-label="Close">×</button>
          <div class="brand-side">
            <img src="logo/Logo w text_.png" alt="Logo" class="logo-box" style="background-color: transparent; border:none;" />
            <h2>CINEMA ROYALE</h2>
          </div>
          <div class="form-side">
            <div class="form-panel">
              <h1>Login</h1>
              <p class="subtitle">Sign in to continue</p>

              <form id="login-form" method="POST">
                <div class="form-group">
                  <label for="login-email">Email</label>
                  <input
                    id="login-email"
                    name="email"
                    type="email"
                    placeholder="Enter your email"
                    required>
                </div>
                <div class="form-group">
                  <label for="login-password">Password</label>
                  <input
                    id="login-password"
                    name="password"
                    type="password"
                    placeholder="Enter your password"
                    required>
                </div>
                <div class="actions">
                  <label><input type="checkbox" /> Remember me</label>
                  <a href="#">Forgot password?</a>
                </div>
                <button class="btn" type="submit">Sign In</button>
              </form>

              <button class="alt-text text-button" data-bs-toggle="modal" data-bs-target="#registerModal">Don't have an account? <span href="signup.html">Create one</span></button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>


  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="logo\Logo.png" alt="Cinema Royale Logo" class="navbar-logo me-2" style="height: 5rem; width: auto;" />
        <div>
          <span class="fs-2 p-0 m-0">Cinema Royale</span>
          <div class="navbar-brand-subtitle ms-1" style="font-size: 0.75rem">PREMIUM EXPERIENCE</div>
        </div>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav mx-auto">
          <div class="d-flex w-100 justify-content-center text-center">
            <li class="nav-item"><a class="nav-link" href="#hero">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#now-showing">Now Showing</a></li>
          </div>
          <div class="d-flex w-100 justify-content-center tex-center">
            <li class="nav-item"><a class="nav-link" href="#promotions">Promotions</a></li>
            <li class="nav-item"><a class="nav-link" href="#experience">About</a></li>

            <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
          </div>
        </ul>
        <div class="auth-buttons ms-auto d-flex flex-lg-row justify-content-center my-2">
          <!-- <a href="login.php" class="auth-btn login-btn">Login</a> -->
          <button class="auth-btn login-btn" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
          <button class="auth-btn register-btn" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
          <!-- <a href="signup.php" class="auth-btn register-btn">Register</a> -->
        </div>
      </div>
    </div>
  </nav>

  <section class="hero" id="hero">
    <div class="container">
      <div class="hero-label">ACTION PACKED</div>
      <h1>Avengers: Infinity War</h1>
      <p>Thanos has arrived, and the Avengers must stand together like never before.</p>
      <p>From the streets of New York to far-off galaxies, every hero is drawn into a desperate battle to save the universe.</p>
      <p>Experience the highest-stakes showdown in the MCU, filled with epic action, shocking twists, and unforgettable moments.</p>
      <div class="hero-buttons">
        <button class="btn-book-now" data-movie="Avengers: Infinity War">Book Now</button>
        <button class="btn-explore" onclick="document.getElementById('now-showing').scrollIntoView({ behavior: 'smooth' })">
          Explore Movies
        </button>
      </div>
    </div>
  </section>

  <section class="now-showing" id="now-showing">
    <div class="container">
      <div class="section-label">NOW SHOWING</div>
      <h2 class="section-title">Now Showing</h2>
      <p class="section-subtitle">
        Catch the biggest blockbusters and award-winning films on the big screen. New experiences every week.
      </p>
      <div class="movie-grid" id="movieGrid"></div>
    </div>
  </section>

  <section class="promotions" id="promotions">
    <div class="container">
      <div class="section-label">SPECIAL OFFERS</div>
      <h2 class="section-title">Exclusive Promotions</h2>
      <p class="section-subtitle">Unlock amazing deals and make every movie night extraordinary with our handpicked promotions.</p>
      <div class="promotion-card">
        <img src="assets/images/promotions/promotion1.jpg" alt="Summer Blockbuster" class="promotion-image" />
        <div class="promotion-content">
          <div class="promotion-label">LIMITED OFFER</div>
          <h3>Summer Blockbuster Pass</h3>
          <p>Watch any 5 movies this summer for only ₱1,499. Save up to 40% on regular ticket prices.</p>
          <button class="btn-claim">Claim Offer</button>
        </div>
      </div>
      <div class="promotion-card" style="direction: rtl">
        <img src="assets/images/promotions/promotion2.png" alt="Family Night" class="promotion-image" />
        <div class="promotion-content" style="direction: ltr">
          <div class="promotion-label">FAMILY FUN</div>
          <h3>Family Night Special</h3>
          <p>Bring your family and enjoy 4 tickets + popcorn combo for just ₱999. Perfect for weekend outings!</p>
          <button class="btn-claim">Claim Offer</button>
        </div>
      </div>
    </div>
  </section>

  <section class="experience" id="experience">
    <div class="container">
      <div class="experience-intro">
        <h2>
          We don't just show movies. We create <span class="highlight">unforgettable moments</span> that stay with you long after the credits roll.
        </h2>
        <p class="about-paragraph">
          Cinema Royale blends cutting-edge technology, luxurious comfort, and friendly service to make every screening feel like an exclusive event for movie lovers.
        </p>
      </div>
      <div class="section-label">/ THE EXPERIENCE</div>
      <div class="experience-features" id="featureGrid"></div>
    </div>
  </section>

  <section class="testimonials">
    <div class="container">
      <div class="section-label">/ TESTIMONIALS /</div>
      <div class="testimonial-header">
        <h2 class="section-title">What Our Guests Say</h2>
      </div>
      <div class="testimonials-container" id="testimonialGrid"></div>
    </div>
  </section>

  <footer id="contact">
    <div class="container">
      <div class="footer-section">
        <h5>🎬 Cinema Royale</h5>
        <p>
          Experience movies the way they were meant to be seen. Premium sound, stunning visuals, and unmatched comfort — only at Cinema Royale.
        </p>
        <div class="footer-socials">
          <a href="#" title="Facebook">f</a>
          <a href="#" title="Instagram">📷</a>
          <a href="#" title="Twitter">𝕏</a>
          <a href="#" title="YouTube">▶</a>
        </div>
      </div>
      <div class="footer-section">
        <h5>NEWSLETTER</h5>
        <p>Get the latest movies, exclusive offers, and event invites straight to your inbox.</p>
        <div class="newsletter-input">
          <input type="email" placeholder="Your email address" />
          <button>→</button>
        </div>
      </div>
      <div class="footer-section">
        <h5>QUICK LINKS</h5>
        <div class="footer-links">
          <a href="#now-showing">Now Showing</a>
          <a href="#promotions">Promotions</a>
          <a href="#experience">About Us</a>
          <a href="#contact">Contact</a>
        </div>
      </div>
      <div class="footer-section">
        <h5>CONTACT</h5>
        <p><i class="fa-solid fa-location-dot"></i> 📍 Trece Martires City, Cavite 4109</p>
        <p>📞 +63 949 141 3401</p>
        <p>📧 <a href="mailto:hello@cinemaroyale.com" style="color: #ffc700; text-decoration: none">hello@cinemaroyale.com</a></p>
      </div>
      <div class="footer-section">
        <h5>TEAM</h5>
        <p>
          Arliesienne Ansuas<br />Genesis Saliedo<br />James Arnold Dutosme<br />Josiah Joshua Torrefiel
          <br />Kier Bryant Levita<br />Kylle Jonathan Padua<br />Ron Andrei Castro
        </p>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="footer-watermark">CINEMA ROYALE</div>
      <p class="footer-copyright">© 2026 Cinema Royale. All rights reserved.</p>
    </div>
  </footer>

  <script src="libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/app.js"></script>
  <script src="js/index.js"></script>

</body>

</html>