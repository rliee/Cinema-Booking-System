document.addEventListener("DOMContentLoaded", () => {
    let activeCard = null; // Track which card is being modified
    let pendingPosterDataUrl = null; // Holds a newly-selected poster (base64) until Save is clicked

    const editPosterInput = document.getElementById("editMoviePoster");
    const editPosterPreview = document.getElementById("editMoviePosterPreview");

    // ==========================================
    // 0. LIVE PREVIEW WHEN A NEW POSTER IS CHOSEN
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
    // 1. OPEN EDIT MODAL & POPULATE DATA
    // ==========================================
    document.addEventListener("click", (e) => {
        if (e.target.classList.contains("edit-btn")) {
            activeCard = e.target.closest(".movie-card-horizontal");
            if (!activeCard) return;

            // Reset any pending poster selection from a previous edit session
            pendingPosterDataUrl = null;
            if (editPosterInput) editPosterInput.value = "";

            // Extract values
            const title = activeCard.querySelector(".movie-title")?.textContent || "";
            const genre = activeCard.querySelector(".tag.genre")?.textContent || "";
            const duration = activeCard.querySelector(".tag.duration")?.textContent || "";
            const rating = activeCard.querySelector(".tag.age-rating")?.textContent || "";
            const synopsis = activeCard.querySelector(".movie-synopsis")?.textContent.trim() || "";
            const posterSrc = activeCard.querySelector(".movie-poster")?.getAttribute("src") || "";

            // Get status from badge class
            const statusBadge = activeCard.querySelector(".status-badge");
            let statusValue = "now-showing"; // Default fallback
            if (statusBadge) {
                if (statusBadge.classList.contains("coming-soon")) statusValue = "coming-soon";
                else if (statusBadge.classList.contains("ended")) statusValue = "ended";
                else if (statusBadge.classList.contains("now-showing")) statusValue = "now-showing";
            }

            // Populate Modal with the movie's CURRENT data
            document.getElementById("editMovieTitle").value = title;
            document.getElementById("editMovieGenre").value = genre;
            document.getElementById("editMovieDuration").value = duration;
            document.getElementById("editMovieRating").value = rating;
            document.getElementById("editMovieStatus").value = statusValue;
            document.getElementById("editMovieSynopsis").value = synopsis;

            // Show current poster preview
            if (editPosterPreview) {
                editPosterPreview.src = posterSrc;
            }

            new bootstrap.Modal(document.getElementById("editMovieModal")).show();
        }
    });

    // ==========================================
    // 2. SUBMIT EDIT MODAL (SAVE CHANGES)
    // ==========================================
    const editForm = document.getElementById("editMovieForm");
    if (editForm) {
        editForm.addEventListener("submit", (e) => {
            e.preventDefault();
            if (!activeCard) return;

            activeCard.querySelector(".movie-title").textContent = document.getElementById("editMovieTitle").value;
            activeCard.querySelector(".tag.genre").textContent = document.getElementById("editMovieGenre").value;
            activeCard.querySelector(".tag.duration").textContent = document.getElementById("editMovieDuration").value;
            activeCard.querySelector(".tag.age-rating").textContent = document.getElementById("editMovieRating").value;
            activeCard.querySelector(".movie-synopsis").textContent = document.getElementById("editMovieSynopsis").value;

            if (pendingPosterDataUrl) {
                const posterImg = activeCard.querySelector(".movie-poster");
                if (posterImg) posterImg.src = pendingPosterDataUrl;
            }

            const statusBadge = activeCard.querySelector(".status-badge");
            const newStatus = document.getElementById("editMovieStatus").value;

            statusBadge.classList.remove("now-showing", "coming-soon", "ended");
            statusBadge.classList.add(newStatus);

            const statusMap = {
                "now-showing": "Now Showing",
                "coming-soon": "Coming Soon",
                "ended": "Ended"
            };
            statusBadge.textContent = statusMap[newStatus] || "Now Showing";

            bootstrap.Modal.getInstance(document.getElementById("editMovieModal")).hide();

            pendingPosterDataUrl = null;
            if (editPosterInput) editPosterInput.value = "";

            if (typeof window.filterMovies === "function") window.filterMovies();
            if (typeof window.updateGenreDistribution === "function") window.updateGenreDistribution();
        });
    }

    // ==========================================
    // 3. OPEN DELETE MODAL
    // ==========================================
    document.addEventListener("click", (e) => {
        if (e.target.classList.contains("delete-btn")) {
            activeCard = e.target.closest(".movie-card-horizontal");
            if (!activeCard) return;

            const title = activeCard.querySelector(".movie-title")?.textContent || "this movie";
            document.getElementById("deleteMovieTarget").textContent = title;

            new bootstrap.Modal(document.getElementById("deleteMovieModal")).show();
        }
    });

    // ==========================================
    // 4. CONFIRM DELETE ACTION + HOVER STATE
    // ==========================================
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    if (confirmDeleteBtn) {
        // Add dynamic hover state style transformations
        confirmDeleteBtn.style.transition = "background-color 0.2s ease, transform 0.1s ease";
        
        confirmDeleteBtn.addEventListener("mouseenter", () => {
            confirmDeleteBtn.style.backgroundColor = "#bd2130"; // Darker feedback red
        });
        
        confirmDeleteBtn.addEventListener("mouseleave", () => {
            confirmDeleteBtn.style.backgroundColor = ""; // Reset to CSS default
        });

        confirmDeleteBtn.addEventListener("click", () => {
            if (activeCard) {
                activeCard.remove();

                if (typeof window.filterMovies === "function") {
                    window.filterMovies();
                }
                if (typeof window.updateGenreDistribution === "function") {
                    window.updateGenreDistribution();
                }
            }
            bootstrap.Modal.getInstance(document.getElementById("deleteMovieModal")).hide();
        });
    }

    // ==========================================
    // 5. ADD NEW MOVIE DATA & FILE HANDLING
    // ==========================================
    const addMovieForm = document.getElementById("addMovieForm");
    if (addMovieForm) {
        addMovieForm.addEventListener("submit", (e) => {
            e.preventDefault();

            const titleEl = document.getElementById("addMovieTitle");
            const statusEl = document.getElementById("addMovieStatus");
            const genreEl = document.getElementById("addMovieGenre");
            const durationEl = document.getElementById("addMovieDuration");
            const ratingEl = document.getElementById("addMovieRating");
            const synopsisEl = document.getElementById("addMovieSynopsis");
            const posterInput = document.getElementById("addMoviePoster");

            const title = titleEl ? titleEl.value : "Untitled Movie";
            const statusValue = statusEl ? statusEl.value : "now-showing";
            const genre = genreEl ? genreEl.value : "General";
            const duration = durationEl ? durationEl.value : "N/A";
            const rating = ratingEl ? ratingEl.value : "G";
            const synopsis = synopsisEl ? synopsisEl.value : "";

            let posterSrc = "image.png"; 
            let statusText = "Now Showing";
            if (statusValue === "coming-soon") statusText = "Coming Soon";
            if (statusValue === "ended") statusText = "Ended";

            const buildAndAppendCard = (imgUrl) => {
                const newMovieCard = document.createElement("div");
                newMovieCard.className = "movie-card-horizontal mt-3";
                newMovieCard.innerHTML = `
                    <div class="poster-container">
                        <img src="${imgUrl}" alt="${title} Poster" class="movie-poster">
                        <span class="status-badge ${statusValue}">${statusText}</span>
                    </div>
                    <div class="movie-details">
                        <div class="details-header">
                            <h3 class="movie-title">${title}</h3>
                            <div class="meta-tags">
                                <span class="tag genre">${genre}</span>
                                <span class="tag duration">${duration}</span>
                                <span class="tag age-rating">${rating}</span>
                            </div>
                        </div>
                        <p class="movie-synopsis">${synopsis}</p>
                        <div class="card-actions">
                            <a href="#" target="_blank" class="trailer-link">
                                <i class="fas fa-play"></i> Watch Trailer
                            </a>
                            <div class="button-group">
                                <button type="button" class="edit-btn">Edit</button>
                                <button type="button" class="delete-btn">Delete</button>
                            </div>
                        </div>
                    </div>
                `;

                const fallbackMessage = document.getElementById("no-movies-fallback");
                const operationsContainer = document.getElementById('page-operations') ||
                    document.getElementById('page-movies') ||
                    document.querySelector('.main-content');

                if (fallbackMessage) {
                    fallbackMessage.parentNode.insertBefore(newMovieCard, fallbackMessage);
                } else if (operationsContainer) {
                    operationsContainer.appendChild(newMovieCard);
                }

                addMovieForm.reset();

                const addModalEl = document.getElementById("addMovieModal");
                if (addModalEl && typeof bootstrap !== "undefined") {
                    const modalInstance = bootstrap.Modal.getInstance(addModalEl) || new bootstrap.Modal(addModalEl);
                    modalInstance.hide();
                }

                if (typeof window.filterMovies === "function") {
                    window.filterMovies();
                }
                if (typeof window.updateGenreDistribution === "function") {
                    window.updateGenreDistribution();
                }
            };

            if (posterInput && posterInput.files && posterInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    buildAndAppendCard(event.target.result);
                };
                reader.readAsDataURL(posterInput.files[0]);
            } else {
                buildAndAppendCard(posterSrc);
            }
        });
    }
});