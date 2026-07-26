document.addEventListener("DOMContentLoaded", () => {
  const slides = document.querySelectorAll(".hero-slide");
  const indicators = document.querySelectorAll(".indicator");

  const prevBtn = document.querySelector(".carousel-prev");
  const nextBtn = document.querySelector(".carousel-next");

  if (!slides.length) return;

  let currentSlide = 0;
  let autoSlideInterval;

  function goToSlide(index) {
    slides.forEach((slide) => slide.classList.remove("active"));

    indicators.forEach((indicator) => indicator.classList.remove("active"));

    slides[index].classList.add("active");

    if (indicators[index]) {
      indicators[index].classList.add("active");
    }

    currentSlide = index;
  }

  function nextSlide() {
    goToSlide((currentSlide + 1) % slides.length);
  }

  function previousSlide() {
    goToSlide((currentSlide - 1 + slides.length) % slides.length);
  }

  function startAutoSlide() {
    stopAutoSlide();

    autoSlideInterval = setInterval(() => {
      nextSlide();
    }, 6000);
  }

  function stopAutoSlide() {
    clearInterval(autoSlideInterval);
  }

  prevBtn?.addEventListener("click", () => {
    previousSlide();
    startAutoSlide();
  });

  nextBtn?.addEventListener("click", () => {
    nextSlide();
    startAutoSlide();
  });

  indicators.forEach((indicator, index) => {
    indicator.addEventListener("click", () => {
      goToSlide(index);
      startAutoSlide();
    });
  });

  goToSlide(0);

  startAutoSlide();

  loadNowShowingMovies();
  loadComingSoonMovies();
});

function loadNowShowingMovies() {
  const movieGrid = document.getElementById("movieGrid");

  if (!movieGrid) return;

  fetch("api/movies/get_now_showing.php")
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        movieGrid.innerHTML = "";
        return;
      }

      movieGrid.innerHTML = "";

      data.movies.forEach((movie) => {
        movieGrid.innerHTML += `

        <div class="movie-card">
          <div class="movie-preview">
            <img 
              src="${movie.poster_url}"
              alt="${movie.title}">
          </div>
            
          <div class="card-body">
            <h3 class="movie-title">
              ${movie.title}
            </h3>
              
            <div class="movie-meta">
              <span>
                ${formatDuration(movie.duration)}
              </span>

              <span>|</span>

              <span>
                ${movie.age_rating}
              </span>

              <span>|</span>
                  <span class="movie-genre">
              ${movie.genre_name}
            </span>
              
            </div>
              
            <button 
              class="btn-book-now"
              data-movie-id="${movie.movie_id}">
              Book Ticket
            </button>

          </div>
        </div>
      `;
      });
    })
    .catch((error) => {
      console.error("Failed to load movies:", error);
    });
}

function loadComingSoonMovies() {
  const movieGrid = document.getElementById("comingSoonGrid");

  if (!movieGrid) return;

  fetch("api/movies/get_coming_soon.php")
    .then((response) => response.json())

    .then((data) => {
      if (!data.success) {
        movieGrid.innerHTML = "";
        return;
      }

      movieGrid.innerHTML = "";

      data.movies.forEach((movie) => {
        movieGrid.innerHTML += `
          <div class="movie-card">
            <div class="movie-preview">
              <img 
                src="${movie.poster_url}"
                alt="${movie.title}">
            </div>

            <div class="card-body">
              <h3 class="movie-title">
                ${movie.title}
              </h3>

              <div class="movie-meta">
                <span>
                  ${formatDuration(movie.duration)}
                </span>

                <span>|</span>

                <span>
                  ${movie.age_rating}
                </span>

                <span>|</span>
                
            <span class="movie-genre">
              ${movie.genre_name}
            </span>

            </div>

            <button 
              class="btn-book-now btn btn-danger"
              disabled>
              Coming Soon
            </button>
          </div>
        </div>
      `;
      });
    })
    .catch((error) => {
      console.error("Failed to load coming soon movies:", error);
    });
}

document.addEventListener("click", (event) => {
  const button = event.target.closest(".btn-book-now");

  if (!button) return;

  const movieId = button.dataset.movieId;

  window.location.href = `booking.php?movie_id=${movieId}`;
});

function formatDuration(minutes) {
  const hours = Math.floor(minutes / 60);

  const mins = minutes % 60;

  return `${hours}h ${mins}m`;
}
