<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cinema Royale - Premium Movie Experience</title>

  <link href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/style.css" />
  <style>
    :root {
      --panel: rgba(16, 16, 16, 0.95);
      --gold: #d4af37;
      --text: #f7f3e8;
      --muted: #b5b0a1;
      --border: rgba(212, 175, 55, 0.3);
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      background:
        radial-gradient(circle at top left, rgba(212, 175, 55, 0.25), transparent 25%),
        linear-gradient(135deg, #000, #111 70%, #050505);
      color: var(--text);
    }

    .modal-overlay {
      position: fixed;
      inset: 0;
      display: grid;
      place-items: center;
      padding: 24px;
      background: rgba(0, 0, 0, 0.78);
      backdrop-filter: blur(6px);
      z-index: 10;
      overflow-y: auto;
    }

    .auth-card {
      width: min(100%, 900px);
      display: flex;
      position: relative;
      border: 1px solid var(--border);
      border-radius: 24px;
      background: var(--panel);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45);
      backdrop-filter: blur(8px);
      overflow: hidden;
      min-height: 560px;
      max-width: 100%;
    }

    .close-btn {
      position: absolute;
      top: 12px;
      right: 12px;
      width: 36px;
      height: 36px;
      border: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.08);
      color: var(--text);
      font-size: 1.1rem;
      cursor: pointer;
      z-index: 2;
    }

    .brand-side {
      flex: 1 1 45%;
      background: linear-gradient(rgba(0, 0, 0, 0.72), rgba(0, 0, 0, 0.72)),
        linear-gradient(135deg, #1a1a1a, #000);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
      text-align: center;
      border-right: 1px solid var(--border);
    }

    .logo-box {
      width: 170px;
      height: 170px;
      display: grid;
      place-items: center;
      margin-bottom: 24px;
      background: rgba(255, 255, 255, 0.03);
      color: var(--gold);
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      font-size: 0.95rem;
    }

    .brand-side h2 {
      margin: 0 0 10px;
      color: var(--gold);
      font-size: 1.8rem;
    }

    .brand-side p {
      margin: 0;
      color: var(--muted);
      font-size: 1rem;
      line-height: 1.7;
      max-width: 280px;
    }

    .form-side {
      flex: 1 1 55%;
      padding: 28px 40px 40px;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

    .form-panel {
      width: 100%;
      max-width: 380px;
      margin: 12px auto 0;
    }

    .text-button {
      background: none;
      border: none;
      padding: 0;
      margin: 0;
      font: inherit;
      cursor: pointer;
      color: #0066cc;
    }

    @media (max-width: 800px) {
      body {
        padding: 12px;
      }

      .modal-overlay {
        padding: 12px;
        align-items: start;
      }

      .auth-card {
        flex-direction: column;
        min-height: auto;
      }

      .brand-side {
        border-right: 0;
        border-bottom: 1px solid var(--border);
        padding: 24px 20px;
      }

      .logo-box {
        width: 120px;
        height: 120px;
      }

      .brand-side h2 {
        font-size: 1.5rem;
      }

      .form-side {
        padding: 24px 20px 28px;
      }

      .actions {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
    }

    @media (max-width: 480px) {
      .modal-overlay {
        padding: 8px;
      }

      .brand-side {
        padding: 20px 16px;
      }

      .form-side {
        padding: 20px 16px 24px;
      }

      h1 {
        font-size: 1.5rem;
      }

      .form-group input,
      .btn {
        padding: 12px 13px;
      }
    }

    h1 {
      margin: 0 0 8px;
      font-size: 1.8rem;
    }

    .subtitle {
      margin: 0 0 24px;
      color: var(--muted);
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-group label {
      display: block;
      font-size: 0.9rem;
      margin-bottom: 8px;
      color: var(--muted);
    }

    .form-group input {
      width: 100%;
      padding: 13px 14px;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, 0.04);
      color: var(--text);
      outline: none;
    }

    .form-group input:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.18);
    }

    .actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 8px 0 18px;
      font-size: 0.9rem;
      color: var(--muted);
    }

    .actions a {
      color: #ffd86b;
      text-decoration: none;
    }

    .btn {
      width: 100%;
      padding: 13px 16px;
      border: 0;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      color: #0b0b0b;
      background: linear-gradient(135deg, var(--gold), #f2d570);
    }

    .alt-text {
      text-align: center;
      color: var(--muted);
      margin-top: 16px;
      font-size: 0.94rem;
    }

    .alt-text a {
      color: #ffd86b;
      text-decoration: none;
    }
  </style>
</head>

<body>

  <div class="modal fade" id="registerModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content bg-transparent border-0 text-white">
        <div class="auth-card">
          <button class="close-btn" type="button" aria-label="Close">×</button>
          <div class="brand-side">
            <img src="logo/Logo w text_.png" alt="Logo" class="logo-box" />
            <h2>CINEMA ROYALE</h2>
          </div>
          <div class="form-side">
            <div class="form-panel">
              <h1>Create Account</h1>

              <form id="register-form" method="POST">
                <div class="form-group">
                  <label for="signup-name">Full Name</label>
                  <input
                    id="signup-name"
                    name="fullname"
                    type="text"
                    placeholder="Enter your full name"
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

              <button class="alt-text text-button" data-bs-toggle="modal" data-bs-target="#loginModal">Already have an account? <span href="login.php">Sign in</span></button>
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
          <button class="auth-btn login-btn" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
          <button class="auth-btn register-btn" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
        </div>
      </div>
    </div>
  </nav>

  <section class="hero" id="hero">
    <div class="hero-carousel" id="heroCarousel">
      <div class="hero-slide active" data-movie="Avengers: Infinity War" data-label="ACTION PACKED" data-bg="https://images.unsplash.com/photo-1489599849228-bed96c3ee601?w=1400&h=600&fit=crop">
        <div class="container">
          <div class="hero-label">ACTION PACKED</div>
          <h1>Avengers: Infinity War</h1>
          <p>Thanos has arrived, and the Avengers must stand together like never before.</p>
          <p>From the streets of New York to far-off galaxies, every hero is drawn into a desperate battle to save the universe.</p>
          <p>Experience the highest-stakes showdown in the MCU, filled with epic action, shocking twists, and unforgettable moments.</p>
          <div class="hero-buttons">
            <button class="btn-book-now" data-movie="Avengers: Infinity War">Book Now</button>
            <button class="btn-explore" onclick="document.getElementById('now-showing').scrollIntoView({ behavior: 'smooth' })">Explore Movies</button>
          </div>
        </div>
      </div>
      <div class="hero-slide" data-movie="Jurassic World Rebirth" data-label="THRILLING ADVENTURE" data-bg="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1400&h=600&fit=crop">
        <div class="container">
          <div class="hero-label">THRILLING ADVENTURE</div>
          <h1>Jurassic World Rebirth</h1>
          <p>Dinosaurs once again roam the Earth in this epic new chapter of the Jurassic saga.</p>
          <p>Ancient creatures, new threats, and a race against time to save humanity from extinction.</p>
          <p>The park is gone. The world is now the cage. Experience the rebirth of an era.</p>
          <div class="hero-buttons">
            <button class="btn-book-now" data-movie="Jurassic World Rebirth">Book Now</button>
            <button class="btn-explore" onclick="document.getElementById('now-showing').scrollIntoView({ behavior: 'smooth' })">Explore Movies</button>
          </div>
        </div>
      </div>
      <div class="hero-slide" data-movie="The Fantastic Four: First Steps" data-label="HEROES RISE" data-bg="https://images.unsplash.com/photo-1535016120720-40c646be5580?w=1400&h=600&fit=crop">
        <div class="container">
          <div class="hero-label">HEROES RISE</div>
          <h1>The Fantastic Four: First Steps</h1>
          <p>Marvel's first family discovers their extraordinary powers in this thrilling origin story.</p>
          <p>Four explorers, one cosmic accident, and a bond stronger than any superpower.</p>
          <p>Witness the beginning of a legendary superhero team that will change the world forever.</p>
          <div class="hero-buttons">
            <button class="btn-book-now" data-movie="The Fantastic Four: First Steps">Book Now</button>
            <button class="btn-explore" onclick="document.getElementById('now-showing').scrollIntoView({ behavior: 'smooth' })">Explore Movies</button>
          </div>
        </div>
      </div>
    </div>
    <div class="hero-carousel-indicators">
      <span class="indicator active" data-slide="0"></span>
      <span class="indicator" data-slide="1"></span>
      <span class="indicator" data-slide="2"></span>
    </div>
    <button class="carousel-btn carousel-prev" aria-label="Previous">&#10094;</button>
    <button class="carousel-btn carousel-next" aria-label="Next">&#10095;</button>
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

  <script>
    document.querySelectorAll("[data-movie]").forEach((button) => {
      button.addEventListener("click", () => {
        const movie = button.dataset.movie;
        const query = new URLSearchParams({ movie }).toString();
        window.location.href = "booking.php?" + query;
      });
    });

    (function() {
      if ("ontouchstart" in window || navigator.maxTouchPoints > 0) return;
      document.querySelectorAll(".movie-card").forEach(function(card) {
        var video = card.querySelector("video.trailer");
        if (!video) return;
        video.muted = true;
        video.preload = "metadata";
        card.addEventListener("mouseenter", function() {
          try {
            video.currentTime = 0;
            video.play();
          } catch (e) {}
        });
        card.addEventListener("mouseleave", function() {
          try {
            video.pause();
            video.currentTime = 0;
          } catch (e) {}
        });
      });
    })();

    document.querySelectorAll(".nav-link").forEach((link) => {
      link.addEventListener("click", () => {
        document.querySelector(".navbar-collapse").classList.remove("show");
      });
    });
  </script>

  <!-- Restored Session Handler Script -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        const loggedIn = localStorage.getItem("loggedIn");
        if (loggedIn === "true") {
            const authButtons = document.querySelector(".auth-buttons");
            if (authButtons) {
                authButtons.innerHTML = `
                    <span class="welcome-text" style="color: #fff; margin-right: 15px;">Welcome Back!</span>
                    <a href="#" id="logout-btn" class="auth-btn login-btn">Logout</a>
                `;
                document.getElementById("logout-btn").addEventListener("click", function (e) {
                    e.preventDefault();
                    localStorage.removeItem("loggedIn");
                    window.location.href = "index.php";
                });
            }
        }
    });
  </script>

  <script>
    // Hero Carousel
    (function() {
      const slides = document.querySelectorAll('.hero-slide');
      const indicators = document.querySelectorAll('.indicator');
      const prevBtn = document.querySelector('.carousel-prev');
      const nextBtn = document.querySelector('.carousel-next');
      let currentSlide = 0;
      let autoSlideInterval;

      function goToSlide(index) {
        slides.forEach(s => s.classList.remove('active'));
        indicators.forEach(i => i.classList.remove('active'));
        slides[index].classList.add('active');
        indicators[index].classList.add('active');
        currentSlide = index;
      }

      function nextSlide() {
        goToSlide((currentSlide + 1) % slides.length);
      }

      function prevSlide() {
        goToSlide((currentSlide - 1 + slides.length) % slides.length);
      }

      function startAutoSlide() {
        stopAutoSlide();
        autoSlideInterval = setInterval(nextSlide, 6000);
      }

      function stopAutoSlide() {
        if (autoSlideInterval) {
          clearInterval(autoSlideInterval);
          autoSlideInterval = null;
        }
      }

      if (prevBtn) prevBtn.addEventListener('click', function() { prevSlide(); startAutoSlide(); });
      if (nextBtn) nextBtn.addEventListener('click', function() { nextSlide(); startAutoSlide(); });
      indicators.forEach(ind => {
        ind.addEventListener('click', function() {
          goToSlide(parseInt(this.dataset.slide));
          startAutoSlide();
        });
      });

      startAutoSlide();
    })();

    let lastScrollTop = 0;
    const header = document.querySelector('.navbar');

    window.addEventListener('scroll', function () {
        let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        if (currentScroll > lastScrollTop && currentScroll > 50) {
            header.classList.add('hide-header');
        } else {
            header.classList.remove('hide-header');
        }
        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    });
  </script>

</body>

</html>
