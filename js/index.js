const movieData = [
  {
    title: "Avengers: Infinity War",
    image: "assets/images/poster/image1.jpg",
    trailer: "assets/trailer/avengers.mp4",
    rating: "⭐ 9.2",
    genre: "Sci-Fi / Adventure",
    meta: "2h 48m | Sci‑Fi / Adventure | PG-13",
    description:
      "The Avengers and their allies unite to stop the powerful Thanos from collecting all six Infinity Stones, which would give him the power to wipe out half of all life in the universe.",
    extra: "",
  },
  {
    title: "The Home",
    image: "assets/images/poster/image2.jpg",
    trailer: "assets/trailer/thehome.mp4",
    rating: "⭐ 8.7",
    genre: "Action / Thriller",
    meta: "2h 15m | Action / Thriller | R",
    description:
      "A troubled young man working at a retirement home uncovers terrifying secrets hidden within the facility, leading to a chilling fight for survival.",
    extra: "",
  },
  {
    title: "Love You Long Time",
    image: "assets/images/poster/image3.jpg",
    trailer: "assets/trailer/loveyoulongtime.mp4",
    rating: "⭐ 8.9",
    genre: "Romance / Drama",
    meta: "2h 05m | Romance / Drama | PG-13",
    description:
      "A romantic drama about two people who unexpectedly reconnect and discover that love can endure despite distance, time, and life's challenges.",
    extra: "",
  },
  {
    title: "Sputnik",
    image: "assets/images/poster/image4.jpg",
    trailer: "assets/trailer/sputnik.mp4",
    rating: "⭐ 8.5",
    genre: "Sci-Fi / Action",
    meta: "2h 30m | Sci‑Fi / Action | PG-13",
    description:
      "After a mysterious space mission, a Soviet cosmonaut returns to Earth carrying a dangerous alien organism, forcing scientists to confront a terrifying extraterrestrial threat.",
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
    description:
      "A clever team of sheep detectives uses disguises and teamwork to solve a mysterious farmyard crime.",
    extra: "",
  },
  {
    title: "F1",
    image: "assets/images/poster/image7.jpg",
    trailer: "assets/trailer/f1.mp4",
    rating: "⭐ 8.6",
    genre: "Sports",
    meta: "2h 32m | Documentary / Sports | PG",
    description:
      "High-speed racing, fierce rivalries, and the pursuit of victory.",
    extra: "",
  },
  {
    title: "The Fantastic Four: First Steps",
    image: "assets/images/poster/image8.jpg",
    trailer: "assets/trailer/fantastic4.mp4",
    rating: "⭐ 8.3",
    genre: "Superhero",
    meta: "2h 40m | Superhero / Action | PG-13",
    description:
      "Marvel's first family begins an exciting new adventure together.",
    extra: "",
  },
];
const featureData = [
  {
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
const testimonialData = [
  {
    quote:
      "The best cinema experience I have ever had! The seats are incredibly comfortable, the sound system is immersive, and the staff is always friendly and accommodating. My family and I come here every weekend now.",
    initials: "MS",
    name: "Maria Santos",
    date: "2026-06-28",
  },
  {
    quote:
      "Outstanding service from start to finish. The theater is immaculate, the snacks are delicious, and the booking process online was so seamless. Highly recommend Cinema Royale!",
    initials: "JR",
    name: "John Rivera",
    date: "2026-07-02",
  },
  {
    quote:
      "A truly premium experience. The attention to detail is exceptional — from the comfortable seats to the premium beverage selection. Worth every peso!",
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

document.addEventListener("DOMContentLoaded", function () {
  renderMovieCards();
  renderFeatureCards();
  renderTestimonials();

  document
    .querySelectorAll(".btn-book-ticket, .btn-book-now")
    .forEach((btn) => {
      btn.addEventListener("click", function () {
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

document.querySelectorAll("[data-movie]").forEach((button) => {
  button.addEventListener("click", () => {
    const movie = button.dataset.movie;
    const query = new URLSearchParams({
      movie,
    }).toString();
    window.location.href = "booking.php?" + query;
  });
});

(function () {
  if ("ontouchstart" in window || navigator.maxTouchPoints > 0) return;
  document.querySelectorAll(".movie-card").forEach(function (card) {
    var video = card.querySelector("video.trailer");
    if (!video) return;
    video.muted = true;
    video.preload = "metadata";
    card.addEventListener("mouseenter", function () {
      try {
        video.currentTime = 0;
        video.play();
      } catch (e) {}
    });
    card.addEventListener("mouseleave", function () {
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

//   <!-- Restored Session Handler Script -->

document.addEventListener("DOMContentLoaded", function () {
  const loggedIn = localStorage.getItem("loggedIn");
  if (loggedIn === "true") {
    const authButtons = document.querySelector(".auth-buttons");
    if (authButtons) {
      authButtons.innerHTML = `
                    <span class="welcome-text" style="color: #fff; margin-right: 15px;">Welcome Back!</span>
                    <a href="#" id="logout-btn" class="auth-btn login-btn">Logout</a>
                `;
      document
        .getElementById("logout-btn")
        .addEventListener("click", function (e) {
          e.preventDefault();
          localStorage.removeItem("loggedIn");
          window.location.href = "index.php";
        });
    }
  }
});

let lastScrollTop = 0;
const header = document.querySelector(".navbar");

window.addEventListener("scroll", function () {
  let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
  if (currentScroll > lastScrollTop && currentScroll > 50) {
    header.classList.add("hide-header");
  } else {
    header.classList.remove("hide-header");
  }
  lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
});

const modalElement = document.querySelector("#loginModal");
if (modalElement) {
  const form = modalElement.querySelector("form");
  if (form) {
    modalElement.addEventListener("hidden.bs.modal", () => {
      form.reset();
    });
  }
}
