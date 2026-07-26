document.addEventListener("DOMContentLoaded", () => {
    // ==========================================
    // GLOBAL STATE VARIABLES
    // ==========================================
    let activeCard = null;
    let activeMovieId = null;
    let pendingPosterDataUrl = null;

    const editPosterInput = document.getElementById("editMoviePoster");
    const editPosterPreview = document.getElementById("editMoviePosterPreview");

    // Initialize database fetch on load
    fetchMovies();

    function updateMovieCardUI(card, data) {
        if (!card) return;

        // 1. Update Title
        const titleEl = card.querySelector(".movie-title");
        if (titleEl && data.title) titleEl.textContent = data.title;

        // 2. Update Status Badge & CSS Class
        const statusBadge = card.querySelector(".status-badge");
        if (statusBadge && data.status) {
            const rawStatus = data.status.trim();
            const statusClass = rawStatus.toLowerCase().replace(/\s+/g, '-');
            statusBadge.textContent = rawStatus;
            statusBadge.className = `status-badge ${statusClass}`;
        }

        // 3. Update Genre Tag & Data Attributes
        const genreTag = card.querySelector(".tag.genre, .genre");
        if (genreTag && data.genre_name) {
            genreTag.textContent = data.genre_name;
        }
        if (data.genre_id) {
            card.setAttribute("data-genre-id", data.genre_id);
        }
        if (data.genre_name) {
            card.setAttribute("data-genre-name", data.genre_name);
            card.setAttribute("data-genre", data.genre_name);
        }

        // 4. Update Duration
        const durationTag = card.querySelector(".tag.duration");
        if (durationTag && data.duration) {
            durationTag.textContent = `${data.duration} mins`;
        }

        // 5. Update Rating
        const ratingTag = card.querySelector(".tag.age-rating");
        if (ratingTag && data.rating) {
            ratingTag.textContent = data.rating;
        }

        // 6. Update Synopsis
        const synopsisEl = card.querySelector(".movie-synopsis");
        if (synopsisEl && data.synopsis) {
            synopsisEl.textContent = data.synopsis;
        }

        // 7. Update Poster
        if (data.poster_url) {
            const posterImg = card.querySelector(".movie-poster");
            if (posterImg) posterImg.src = data.poster_url;
        }
    }

    // ==========================================
    // 1. POSTER PREVIEW LISTENER
    // ==========================================
    if (editPosterInput) {
        editPosterInput.addEventListener("change", () => {
            const file = editPosterInput.files && editPosterInput.files[0];
            if (!file) {
                pendingPosterDataUrl = null;
                return;
            }
            const reader = new FileReader();
            reader.onload = (event) => {
                pendingPosterDataUrl = event.target.result;
                if (editPosterPreview) {
                    editPosterPreview.src = pendingPosterDataUrl;
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // ==========================================
    // 2. OPEN EDIT MODAL & POPULATE DATA
    // ==========================================
    document.addEventListener("click", (e) => {
        const editBtn = e.target.closest(".edit-btn");
        if (!editBtn) return;

        activeCard = getCardContainer(editBtn);
        if (!activeCard) return;

        activeMovieId = activeCard.getAttribute("data-id");

        const idInput = document.getElementById("editMovieId");
        if (idInput) idInput.value = activeMovieId;

        const title = activeCard.querySelector(".movie-title")?.textContent.trim() || "";
        const genreText = activeCard.querySelector(".tag.genre, .genre")?.textContent.trim() || "";
        const genreId = activeCard.getAttribute("data-genre-id") || "";
        const duration = activeCard.querySelector(".tag.duration")?.textContent.replace(/\D/g, '') || "";
        const rating = activeCard.querySelector(".tag.age-rating")?.textContent.trim() || "";
        const synopsis = activeCard.querySelector(".movie-synopsis")?.textContent.trim() || "";
        const posterSrc = activeCard.querySelector(".movie-poster")?.getAttribute("src") || "";

        const statusBadge = activeCard.querySelector(".status-badge");
        let statusValue = "Now Showing";
        if (statusBadge) {
            const txt = statusBadge.textContent.toUpperCase();
            if (txt.includes("COMING")) statusValue = "Coming Soon";
            else if (txt.includes("ENDED")) statusValue = "Ended";
            else statusValue = "Now Showing";
        }

        if (document.getElementById("editMovieTitle")) document.getElementById("editMovieTitle").value = title;
        if (document.getElementById("editMovieDuration")) document.getElementById("editMovieDuration").value = duration;
        if (document.getElementById("editMovieRating")) document.getElementById("editMovieRating").value = rating;
        if (document.getElementById("editMovieStatus")) document.getElementById("editMovieStatus").value = statusValue;
        if (document.getElementById("editMovieSynopsis")) document.getElementById("editMovieSynopsis").value = synopsis;

        const genreSelect = document.getElementById("editMovieGenre");
        if (genreSelect) {
            if (genreId) {
                genreSelect.value = genreId;
            } else {
                Array.from(genreSelect.options).forEach(option => {
                    if (option.text.toLowerCase().trim() === genreText.toLowerCase().trim()) {
                        option.selected = true;
                    }
                });
            }
        }

        const preview = document.getElementById("editMoviePosterPreview");
        if (preview) {
            preview.src = posterSrc;
            preview.style.display = posterSrc ? "block" : "none";
        }

        const modalEl = document.getElementById("editMovieModal");
        if (modalEl && typeof bootstrap !== "undefined") {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    });

    // ==========================================
    // 3. SUBMIT EDIT MODAL (SAVE & CONNECT TO DASHBOARD)
    // ==========================================
    const editForm = document.getElementById("editMovieForm");
    if (editForm) {
        editForm.addEventListener("submit", (e) => {
            e.preventDefault();

            const formData = new FormData(editForm);

            if (!formData.get("id") && !formData.get("editMovieId") && activeMovieId) {
                formData.append("id", activeMovieId);
            }

            const genreSelect = document.getElementById("editMovieGenre");
            let selectedGenreId = "";
            let selectedGenreName = "";

            if (genreSelect && genreSelect.selectedIndex !== -1) {
                const selectedOption = genreSelect.options[genreSelect.selectedIndex];
                selectedGenreId = selectedOption.value;
                selectedGenreName = selectedOption.text.trim();

                formData.set("genre_id", selectedGenreId);
                formData.set("genre", selectedGenreId);
            }

            fetch('../api/movies/edit_movie.php', {
                method: 'POST',
                body: formData
            })
                .then(parseJsonResponse)
                .then(data => {
                    if (data.success) {
                        alert("Movie updated successfully!");

                        const updatedData = {
                            title: formData.get("title") || document.getElementById("editMovieTitle")?.value,
                            status: formData.get("status") || document.getElementById("editMovieStatus")?.value,
                            duration: formData.get("duration") || document.getElementById("editMovieDuration")?.value,
                            rating: formData.get("rating") || document.getElementById("editMovieRating")?.value,
                            synopsis: formData.get("synopsis") || document.getElementById("editMovieSynopsis")?.value,
                            genre_id: selectedGenreId,
                            genre_name: selectedGenreName,
                            poster_url: data.poster_url || pendingPosterDataUrl
                        };

                        updateMovieCardUI(activeCard, updatedData);
                        pendingPosterDataUrl = null;

                        if (typeof updateMovieCounters === "function") updateMovieCounters();
                        if (typeof updateMovieCounts === "function") updateMovieCounts();
                        if (typeof updateGenreDistribution === "function") updateGenreDistribution();

                        setTimeout(() => {
                            if (typeof updateGenreDistribution === "function") updateGenreDistribution();
                        }, 50);

                        const editModalEl = document.getElementById("editMovieModal");
                        if (editModalEl && typeof bootstrap !== "undefined") {
                            const modalInstance = bootstrap.Modal.getInstance(editModalEl);
                            if (modalInstance) modalInstance.hide();
                        }
                    } else {
                        alert("Error from server: " + (data.error || "Failed to update movie."));
                    }
                })
                .catch(err => {
                    console.error("Fetch Error:", err);
                    alert("Fetch Error: " + err.message);
                });
        });
    }

    // ==========================================
    // 4. OPEN DELETE MODAL
    // ==========================================
    document.addEventListener("click", (e) => {
        const deleteBtn = e.target.closest(".delete-btn");
        if (!deleteBtn) return;

        activeCard = getCardContainer(deleteBtn);
        if (!activeCard) return;

        activeMovieId = activeCard.getAttribute("data-id");
        const title = activeCard.querySelector(".movie-title")?.textContent || "this movie";

        const targetEl = document.getElementById("deleteMovieTarget");
        if (targetEl) targetEl.textContent = title;

        const deleteModalEl = document.getElementById("deleteMovieModal");
        if (deleteModalEl && typeof bootstrap !== "undefined") {
            bootstrap.Modal.getOrCreateInstance(deleteModalEl).show();
        }
    });

    // ==========================================
    // 5. CONFIRM DELETE ACTION
    // ==========================================
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener("click", () => {
            if (!activeMovieId) return;

            // Option A: If keeping your delete_movie.php file (recommended based on your setup)
            const formData = new FormData();
            formData.append('id', activeMovieId);

            fetch('../api/movies/delete_movie.php', {
                method: 'POST',
                body: formData
            })

                // Option B: If you actually built a REST API route (uncomment below and comment out Option A)
                /*
                fetch(`/api/movies/${activeMovieId}`, { method: 'DELETE' })
                */

                .then(res => {
                    // Check if the response is 2xx; if not, throw an error to skip .json() parsing
                    if (!res.ok) throw new Error(`Server returned ${res.status}: ${res.statusText}`);
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log("Deleted:", data);

                        if (activeCard) {
                            activeCard.remove();
                            activeCard = null;
                            activeMovieId = null;
                        }

                        if (typeof updateMovieCounters === "function") updateMovieCounters();
                        if (typeof updateMovieCounts === "function") updateMovieCounts();
                        if (typeof updateGenreDistribution === "function") updateGenreDistribution();

                        const deleteModalEl = document.getElementById("deleteMovieModal");
                        if (deleteModalEl && typeof bootstrap !== "undefined") {
                            const modalInstance = bootstrap.Modal.getInstance(deleteModalEl);
                            if (modalInstance) modalInstance.hide();
                        }
                    } else {
                        alert("Error deleting movie: " + (data.error || "Failed to delete record."));
                    }
                })
                .catch(err => {
                    console.error("Error deleting movie:", err);
                    alert("Fetch Error: " + err.message);
                });
        });
    }

    // ==========================================
    // 6. ADD NEW MOVIE FORM
    // ==========================================
    const addMovieForm = document.getElementById("addMovieForm");
    if (addMovieForm) {
        addMovieForm.addEventListener("submit", (e) => {
            e.preventDefault();

            const formData = new FormData(addMovieForm);

            fetch('../api/movies/add_movie.php', {
                method: 'POST',
                body: formData
            })
                .then(parseJsonResponse)
                .then(data => {
                    if (data.success) {
                        alert('Movie Added to Database!');

                        const addModalEl = document.getElementById("addMovieModal");
                        if (addModalEl && typeof bootstrap !== "undefined") {
                            const modalInstance = bootstrap.Modal.getInstance(addModalEl);
                            if (modalInstance) modalInstance.hide();
                        }

                        addMovieForm.reset();
                        fetchMovies();
                    } else {
                        alert('Error: ' + (data.error || 'Failed to add movie'));
                    }
                })
                .catch(err => {
                    console.error("Error submitting movie:", err);
                    alert("Fetch Error: " + err.message);
                });
        });
    }
});

// ==========================================
// HELPER FUNCTIONS
// ==========================================
async function parseJsonResponse(response) {
    const text = await response.text();
    try {
        return JSON.parse(text);
    } catch (err) {
        throw new Error(`Invalid JSON output from server (HTTP ${response.status}): ${text.substring(0, 150)}`);
    }
}

function getCardContainer(element) {
    if (!element) return null;
    return element.closest('.movie-card-horizontal') ||
        element.closest('.movie-card') ||
        element.closest('.card') ||
        element.closest('[class*="movie"]');
}

// ==========================================
// 7. FETCH & RENDER FUNCTIONS
// ==========================================
function fetchMovies() {
    const container = document.querySelector('.movie-cards-container');
    if (!container) return;

    container.innerHTML = '<p class="text-white">Loading movies from database...</p>';

    fetch('../api/movies/get_movie.php')
        .then(parseJsonResponse)
        .then(result => {
            if (result.success) {
                renderMovies(result.data, container);
            } else {
                container.innerHTML = `<p class="text-danger">Error: ${result.error || 'Failed to fetch movies'}</p>`;
            }
        })
        .catch(error => {
            console.error("Error fetching movies:", error);
            container.innerHTML = `<p class="text-danger">Failed to connect to database: ${error.message}</p>`;
        });
}

function renderMovies(movies, container) {
    container.innerHTML = '';

    const fallback = document.getElementById('no-movies-fallback');

    if (!movies || movies.length === 0) {
        if (fallback) fallback.style.display = 'block';
        if (typeof updateMovieCounters === "function") updateMovieCounters();
        if (typeof updateMovieCounts === "function") updateMovieCounts();
        if (typeof updateGenreDistribution === "function") updateGenreDistribution();
        return;
    }

    if (fallback) fallback.style.display = 'none';

    movies.forEach(movie => {
        const movieId = movie.id || movie.movie_id;
        const rawStatus = movie.status || 'Now Showing';
        const statusClass = rawStatus.toLowerCase().replace(/\s+/g, '-');
        const statusLabel = movie.status_label || rawStatus;
        const genreId = movie.genre_id || '';


        let posterSrc = movie.poster_url

        const movieHTML = `
            <div class="movie-card-horizontal mt-3" data-id="${movieId}" data-genre-id="${genreId}">
                <div class="poster-container">
                    <img src="../../${posterSrc}" 
                         alt="${movie.title} Poster" 
                         class="movie-poster">
                    <span class="status-badge ${statusClass}">${statusLabel}</span>
                </div>
                <div class="movie-details">
                    <div class="details-header">
                        <h3 class="movie-title">${movie.title}</h3>
                        <div class="meta-tags">
                            <span class="tag genre">${movie.genre_name || movie.genre || 'Unknown'}</span>
                            <span class="tag duration">${movie.duration} mins</span>
                            <span class="tag age-rating">${movie.age_rating || 'PG-13'}</span>
                        </div>
                    </div>
                    <p class="movie-synopsis">${movie.synopsis || 'No synopsis available.'}</p>
                    <div class="card-actions">
                        <a href="${movie.trailer_url || '#'}" target="_blank" class="trailer-link">
                            <i class="fas fa-play"></i> Watch Trailer
                        </a>
                        <div class="button-group">
                            <button class="edit-btn">Edit</button>
                            <button class="delete-btn">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', movieHTML);
    });

    if (typeof updateMovieCounters === "function") updateMovieCounters();
    if (typeof updateMovieCounts === "function") updateMovieCounts();
    if (typeof updateGenreDistribution === "function") updateGenreDistribution();
}

// ==========================================
// 8. DYNAMIC DASHBOARD CALCULATORS
// ==========================================
function updateMovieCounters() {
    const cards = document.querySelectorAll('.movie-card-horizontal');
    if (!cards.length) return;

    let nowShowing = 0;
    let comingSoon = 0;
    let ended = 0;
    let total = cards.length;

    cards.forEach(card => {
        const badge = card.querySelector('.status-badge');
        if (badge) {
            const status = badge.textContent.trim().toLowerCase().replace(/[-_]/g, ' ');

            if (status === 'now showing') {
                nowShowing++;
            } else if (status === 'coming soon') {
                comingSoon++;
            } else if (status === 'ended') {
                ended++;
            }
        }
    });

    const elNow = document.getElementById('count-now-showing');
    const elComing = document.getElementById('count-coming-soon');
    const elEnded = document.getElementById('count-ended-runs');
    const elTotal = document.getElementById('count-total-movies');

    if (elNow) elNow.textContent = nowShowing;
    if (elComing) elComing.textContent = comingSoon;
    if (elEnded) elEnded.textContent = ended;
    if (elTotal) elTotal.textContent = total;
}

// ==========================================
// DYNAMIC GENRE DISTRIBUTION CALCULATOR
// ==========================================
function updateGenreDistribution() {
    const cards = document.querySelectorAll('.movie-card-horizontal');
    const genreCounts = {};

    cards.forEach(card => {
        const genreTag = card.querySelector('.tag.genre, .genre');
        const genreName = genreTag ? genreTag.textContent.trim().toLowerCase().replace(/\s+/g, '-') : null;
        if (genreName) {
            genreCounts[genreName] = (genreCounts[genreName] || 0) + 1;
        }
    });

    const bars = document.querySelectorAll('.distribution-wrapper .bar');
    bars.forEach(bar => {
        const genreKey = bar.getAttribute('data-genre');
        const countSpan = bar.querySelector('.count');

        if (genreKey && countSpan) {
            const count = genreCounts[genreKey] || 0;
            countSpan.textContent = `${count} Films`;
        }
    });

    // Counts are updated above; now re-sort the bars and resize the
    // .number-bar widths to match (handled by metrics.js).
    if (typeof window.renderGenreBarWidths === "function") {
        window.renderGenreBarWidths();
    }
}

// Global scope exposure
window.updateMovieCounters = updateMovieCounters;
window.updateGenreDistribution = updateGenreDistribution;