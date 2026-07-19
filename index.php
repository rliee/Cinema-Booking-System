<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cinema Royale - Premium Movie Experience</title>

  <link href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/style.css" />
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="assets/Blacklogo.png" alt="Cinema Royale Logo" class="navbar-logo me-2" style="height: 40px; width: auto;"/>
        <div> Cinema Royale
          <div class="navbar-brand-subtitle">PREMIUM EXPERIENCE</div>
        </div>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link" href="#hero">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#now-showing">Now Showing</a></li>
          <li class="nav-item"><a class="nav-link" href="#promotions">Promotions</a></li>
          <li class="nav-item"><a class="nav-link" href="#experience">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
        </ul>
        <div class="auth-buttons ms-auto">
          <a href="/login.php" class="auth-btn login-btn">Login</a>
          <a href="/signin.php" class="auth-btn register-btn">Register</a>
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
  <script>
    const movieData = [{
        title: "Avengers: Infinity War",
        image: "assets/images/poster/image1.jpg",
        trailer: "assets/trailer/avengers.mp4",
        rating: "⭐ 9.2",
        genre: "Sci-Fi / Adventure",
        meta: "2h 48m | Sci‑Fi / Adventure | PG-13",
        description: "The Avengers and their allies unite to stop the powerful Thanos from collecting all six Infinity Stones, which would give him the power to wipe out half of all life in the universe.",
        extra: "",
      },
      {
        title: "The Home",
        image: "assets/images/poster/image2.jpg",
        trailer: "assets/trailer/thehome.mp4",
        rating: "⭐ 8.7",
        genre: "Action / Thriller",
        meta: "2h 15m | Action / Thriller | R",
        description: "A troubled young man working at a retirement home uncovers terrifying secrets hidden within the facility, leading to a chilling fight for survival.",
        extra: "",
      },
      {
        title: "Love You Long Time",
        image: "assets/images/poster/image3.jpg",
        trailer: "assets/trailer/loveyoulongtime.mp4",
        rating: "⭐ 8.9",
        genre: "Romance / Drama",
        meta: "2h 05m | Romance / Drama | PG-13",
        description: "A romantic drama about two people who unexpectedly reconnect and discover that love can endure despite distance, time, and life's challenges.",
        extra: "",
      },
      {
        title: "Sputnik",
        image: "assets/images/poster/image4.jpg",
        trailer: "assets/trailer/sputnik.mp4",
        rating: "⭐ 8.5",
        genre: "Sci-Fi / Action",
        meta: "2h 30m | Sci‑Fi / Action | PG-13",
        description: "After a mysterious space mission, a Soviet cosmonaut returns to Earth carrying a dangerous alien organism, forcing scientists to confront a terrifying extraterrestrial threat.",
        extra: "",
      },
      {
        title: "Jurassic World Rebirth",
        image: "assets/images/poster/image5.jpg",
        trailer: "assets/trailer/jurassic.mp4",
        rating: "⭐ 8.8",
        genre: "Adventure",
        meta: "2h 45m | Adventure / Action | PG-13",
        description: "Dinosaurs once again threaten humanity in an epic adventure.",
        extra: "",
      },
      {
        title: "The Sheep Detectives",
        image: "assets/images/poster/image6.jpg",
        trailer: "assets/trailer/sheepdetectives.mp4",
        rating: "⭐ 9.1",
        genre: "Mystery Comedy",
        meta: "2h 12m | Comedy / Mystery | PG",
        description: "A clever team of sheep detectives uses disguises and teamwork to solve a mysterious farmyard crime.",
        extra: "",
      },
      {
        title: "F1",
        image: "assets/images/poster/image7.jpg",
        trailer: "assets/trailer/f1.mp4",
        rating: "⭐ 8.6",
        genre: "Sports",
        meta: "2h 32m | Documentary / Sports | PG",
        description: "High-speed racing, fierce rivalries, and the pursuit of victory.",
        extra: "",
      },
      {
        title: "The Fantastic Four: First Steps",
        image: "assets/images/poster/image8.jpg",
        trailer: "assets/trailer/fantastic4.mp4",
        rating: "⭐ 8.3",
        genre: "Superhero",
        meta: "2h 40m | Superhero / Action | PG-13",
        description: "Marvel's first family begins an exciting new adventure together.",
        extra: "",
      },
    ];
    const featureData = [{
        icon: "🔊",
        title: "Dolby Atmos Sound",
        text: "360° immersive audio that places you at the center of every scene with breathtaking spatial precision.",
      },
      {
        icon: "📺",
        title: "4K Laser Projection",
        text: "Crystal-clear visuals with vibrant colors, deeper blacks, and stunning brightness on massive screens.",
      },
      {
        icon: "👑",
        title: "VIP Recliner Seats",
        text: "Spacious, fully-reclining leather seats with personal side tables and dedicated butler service.",
      },
      {
        icon: "🍿",
        title: "Gourmet Concessions",
        text: "Chef-crafted snacks, premium beverages, and full meals delivered right to your seat.",
      },
    ];
    const testimonialData = [{
        quote: "The best cinema experience I have ever had! The seats are incredibly comfortable, the sound system is immersive, and the staff is always friendly and accommodating. My family and I come here every weekend now.",
        initials: "MS",
        name: "Maria Santos",
        date: "2026-06-28",
      },
      {
        quote: "Outstanding service from start to finish. The theater is immaculate, the snacks are delicious, and the booking process online was so seamless. Highly recommend Cinema Royale!",
        initials: "JR",
        name: "John Rivera",
        date: "2026-07-02",
      },
      {
        quote: "A truly premium experience. The attention to detail is exceptional — from the comfortable seats to the premium beverage selection. Worth every peso!",
        initials: "AC",
        name: "Andrea Cruz",
        date: "2026-07-05",
      },
    ];

    function renderMovieCards() {
      const container = document.getElementById("movieGrid");
      if (!container) return;
      container.innerHTML = movieData
        .map(
          (movie) => `
                <div class="movie-card">
                    <div class="movie-preview">
                        <img src="${movie.image}" alt="${movie.title}">
                        <div class="movie-rating">${movie.rating}</div>
                        <video class="trailer" muted preload="metadata">
                            <source src="${movie.trailer}" type="video/mp4">
                        </video>
                    </div>
                    <div class="card-body">
                        <span class="genre">${movie.genre}</span>
                        <h4 class="card-title">${movie.title}</h4>
                        <p class="card-meta">${movie.meta}</p>
                        <p>${movie.description}</p>
                        ${movie.extra ? `<p>${movie.extra}</p>` : ""}
                        <button class="btn-book-ticket" data-movie="${movie.title}">Book Ticket</button>
                    </div>
                </div>
            `,
        )
        .join("");
    }

    function renderFeatureCards() {
      const container = document.getElementById("featureGrid");
      if (!container) return;
      container.innerHTML = featureData
        .map(
          (feature) => `
                <div class="feature-card">
                    <div class="feature-icon">${feature.icon}</div>
                    <h3 class="feature-title">${feature.title}</h3>
                    <div class="feature-desc">${feature.text}</div>
                </div>
            `,
        )
        .join("");
    }

    function renderTestimonials() {
      const container = document.getElementById("testimonialGrid");
      if (!container) return;
      container.innerHTML = testimonialData
        .map(
          (testimonial) => `
                <div class="testimonial-card">
                    <div>
                        <div class="stars">★ ★ ★ ★ ★</div>
                        <p class="testimonial-quote">"${testimonial.quote}"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">${testimonial.initials}</div>
                        <div class="author-info">
                            <h4>${testimonial.name}</h4>
                            <p>${testimonial.date}</p>
                        </div>
                    </div>
                </div>
            `,
        )
        .join("");
    }

    document.addEventListener("DOMContentLoaded", function() {
      renderMovieCards();
      renderFeatureCards();
      renderTestimonials();

      document.querySelectorAll(".btn-book-ticket, .btn-book-now").forEach((btn) => {
        btn.addEventListener("click", function() {
          const mv = this.dataset.movie || this.getAttribute("data-movie");
          if (mv) {
            localStorage.setItem("movie", mv);
            window.location.href = "booking.php?movie=" + encodeURIComponent(mv);
          } else {
            window.location.href = "booking.php";
          }
        });
      });
    });
  </script>
  <script src="js/app.js"></script>

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

  <!-- Restored Scroll-to-Hide Header script -->
  <script>
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