document.addEventListener('DOMContentLoaded', function () {
    const menuItems = document.querySelectorAll('.sidebar a, .sidebar .menu-group');
    const dropdowns = document.querySelectorAll('.sidebar-dropdown');
    const notifToggle = document.getElementById('notifToggle');
    const notifPanel = document.getElementById('notifPanel');
    const sidebarCheckbox = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const openBtn = document.querySelector('label[for="menu-toggle"].open-btn');

    function updateParentStates() {
        dropdowns.forEach(dropdown => {
            const summary = dropdown.querySelector('.menu-group');
            const childLinks = dropdown.querySelectorAll('.menu-link');
            const hasActiveChild = Array.from(childLinks).some(link => link.classList.contains('active'));

            if (summary) {
                if (hasActiveChild && !dropdown.open) {
                    summary.classList.add('active');
                } else {
                    summary.classList.remove('active');
                }
            }
        });
    }

    menuItems.forEach(item => {
        item.addEventListener('click', function (e) {
            if (this.classList.contains('menu-group')) {
                return;
            }

            const href = this.getAttribute('href');
            if (href === '#') {
                e.preventDefault();
                menuItems.forEach(link => link.classList.remove('active'));
                this.classList.add('active');
                updateParentStates();
            }
        });
    });

    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('toggle', updateParentStates);
    });

    if (notifToggle && notifPanel) {
        notifToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            notifPanel.classList.toggle('open');
        });

        notifPanel.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    if (openBtn && sidebarCheckbox) {
        openBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            sidebarCheckbox.checked = !sidebarCheckbox.checked;
        });
    }

    if (sidebar) {
        sidebar.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    document.addEventListener('click', function (e) {
        const clickedNotif = notifToggle && notifToggle.contains(e.target);
        const clickedNotifPanel = notifPanel && notifPanel.contains(e.target);
        if (sidebarCheckbox && sidebarCheckbox.checked && sidebar && !sidebar.contains(e.target) && (!openBtn || !openBtn.contains(e.target))) {
            sidebarCheckbox.checked = false;
        }

        if (notifPanel && notifPanel.classList.contains('open') && !clickedNotif && !clickedNotifPanel) {
            notifPanel.classList.remove('open');
        }
    });

    updateParentStates();
});

document.addEventListener("DOMContentLoaded", () => {
    const menuLinks = document.querySelectorAll(".sidebar-content a");
    const pageSections = document.querySelectorAll(".page-section");
    const headerTitle = document.querySelector(".page-header h3");

    menuLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            // Find the target page ID from data-page attribute
            const targetPage = link.getAttribute("data-page");

            if (targetPage) {
                e.preventDefault(); // Stop page reload for dynamic links

                // 1. Switch Active Class on Sidebar Links
                menuLinks.forEach(item => item.classList.remove("active"));
                link.classList.add("active");

                // 2. Dynamic Title updates in the Top Header
                headerTitle.textContent = link.textContent.trim();

                // 3. Hide all page sections, show only the target section
                pageSections.forEach(section => {
                    if (section.id === `page-${targetPage}`) {
                        section.classList.remove("d-none"); // Show section
                    } else {
                        section.classList.add("d-none");    // Hide section
                    }
                });
            }
        });
    });
});
// genre distribution
// arrangement
document.addEventListener("DOMContentLoaded", () => {
    const wrapper = document.querySelector('.distribution-wrapper');
    const bars = Array.from(wrapper.querySelectorAll('.bar'));

    // Sort bars based on the numeric value inside the .count span
    bars.sort((a, b) => {
        const countA = parseInt(a.querySelector('.count').textContent) || 0;
        const countB = parseInt(b.querySelector('.count').textContent) || 0;
        return countB - countA; // High to low ranking
    });

    // Re-append to the wrapper in the correct arranged order
    bars.forEach(bar => wrapper.appendChild(bar));
});
// auto progress
document.addEventListener("DOMContentLoaded", () => {
    const wrapper = document.querySelector('.distribution-wrapper');
    const bars = Array.from(wrapper.querySelectorAll('.bar'));

    // 1. Parse and extract the count numbers for each genre
    const barData = bars.map(bar => {
        const countText = bar.querySelector('.count').textContent;
        // Extracts digits from "12 Films" -> 12
        const countValue = parseInt(countText.replace(/\D/g, '')) || 0;
        return { element: bar, count: countValue };
    });

    // 2. Find the maximum count dynamically to act as the 100% cap
    const maxCount = Math.max(...barData.map(d => d.count));

    // 3. Automatically sort the data from highest to lowest count
    barData.sort((a, b) => b.count - a.count);

    // 4. Update the visual widths and rearrange elements in the DOM
    barData.forEach(item => {
        // Calculate the percentage relative to the maximum count
        const percentage = maxCount > 0 ? (item.count / maxCount) * 100 : 0;

        // Find the inner filled bar and assign the calculated width
        const numberBar = item.element.querySelector('.number-bar');
        if (numberBar) {
            numberBar.style.width = `${percentage}%`;
        }

        // Re-append the sorted item into the container layout
        wrapper.appendChild(item.element);
    });
});
// search, genre and status function
document.addEventListener("DOMContentLoaded", () => {
    // ==========================================
    // LAYER A: SEARCH & SELECT FILTER ENGINE
    // ==========================================
    const searchInput = document.querySelector('.search-container input');
    const genreSelect = document.querySelector('.genre-container select');
    const statusSelect = document.querySelector('.status-container select');
    const movieCards = document.querySelectorAll('.movie-card-horizontal');
    const fallbackMessage = document.getElementById('no-movies-fallback');

    function filterMovies() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : "";
        const selectedGenre = genreSelect ? genreSelect.value.toLowerCase() : "all";
        const selectedStatus = statusSelect ? statusSelect.value.toLowerCase() : "all";

        let visibleCount = 0;

        movieCards.forEach(card => {
            const title = card.querySelector('.movie-title')?.textContent.toLowerCase() || "";
            const synopsis = card.querySelector('.movie-synopsis')?.textContent.toLowerCase() || "";
            const genresText = card.querySelector('.tag.genre')?.textContent.toLowerCase() || "";

            // Map badge classes or element classes to evaluate active viewing status
            const statusBadge = card.querySelector('.status-badge');
            let itemStatus = "";
            if (statusBadge) {
                if (statusBadge.classList.contains('now-showing')) itemStatus = "now_showing";
                else if (statusBadge.classList.contains('coming-soon')) itemStatus = "coming_soon";
                else if (statusBadge.classList.contains('ended')) itemStatus = "ended";
            }

            // Evaluation Criteria
            const matchesSearch = title.includes(searchTerm) || synopsis.includes(searchTerm);
            const matchesGenre = selectedGenre === "all" || selectedGenre === "" || genresText.includes(selectedGenre);
            const matchesStatus = selectedStatus === "all" || selectedStatus === "" || itemStatus === selectedStatus;

            if (matchesSearch && matchesGenre && matchesStatus) {
                card.style.setProperty('display', 'flex', 'important'); // Matches flex layout structure
                visibleCount++;
            } else {
                card.style.setProperty('display', 'none', 'important');
            }
        });

        // Toggle visibility fallback if matching movies do not exist
        if (visibleCount === 0) {
            if (fallbackMessage) fallbackMessage.style.display = "block";
        } else {
            if (fallbackMessage) fallbackMessage.style.display = "none";
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterMovies);
    if (genreSelect) genreSelect.addEventListener('change', filterMovies);
    if (statusSelect) statusSelect.addEventListener('change', filterMovies);
    window.filterMovies = filterMovies;


    // ==========================================
    // LAYER B: AUTOMATIC GENRE METRIC ENGINE
    // ==========================================
    const distributionWrapper = document.querySelector('.distribution-wrapper');
    function updateGenreDistribution() {
        if (!distributionWrapper) return;
        const bars = Array.from(distributionWrapper.querySelectorAll('.bar'));

        // 1. Process and evaluate structural numbers inside rows
        const barData = bars.map(bar => {
            const countText = bar.querySelector('.count').textContent;
            const countValue = parseInt(countText.replace(/\D/g, '')) || 0;
            return { element: bar, count: countValue };
        });

        // 2. Identify maximum reference value for percentage distribution
        const maxCount = Math.max(...barData.map(d => d.count));

        // 3. Arrange from highest top metric down to lowest metric
        barData.sort((a, b) => b.count - a.count);

        // 4. Update width properties and place them arranged into the DOM wrapper
        barData.forEach(item => {
            const percentage = maxCount > 0 ? (item.count / maxCount) * 100 : 0;
            const filledTrack = item.element.querySelector('.number-bar');
            if (filledTrack) {
                filledTrack.style.width = `${percentage}%`;
            }
            distributionWrapper.appendChild(item.element);
        });
    }
    updateGenreDistribution();
    window.updateGenreDistribution = updateGenreDistribution;
});

// Edit and delete
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

            // Get status from badge class (case-insensitive, resilient to extra classes)
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
            document.getElementById("editMovieStatus").value = statusValue; // Set correct option
            document.getElementById("editMovieSynopsis").value = synopsis;

            // Show the current poster in the preview
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

            // Update Text
            activeCard.querySelector(".movie-title").textContent = document.getElementById("editMovieTitle").value;
            activeCard.querySelector(".tag.genre").textContent = document.getElementById("editMovieGenre").value;
            activeCard.querySelector(".tag.duration").textContent = document.getElementById("editMovieDuration").value;
            activeCard.querySelector(".tag.age-rating").textContent = document.getElementById("editMovieRating").value;
            activeCard.querySelector(".movie-synopsis").textContent = document.getElementById("editMovieSynopsis").value;

            // Update Poster (only if a new file was actually chosen; otherwise keep existing image)
            if (pendingPosterDataUrl) {
                const posterImg = activeCard.querySelector(".movie-poster");
                if (posterImg) {
                    posterImg.src = pendingPosterDataUrl;
                }
            }

            // Update Status Badge
            const statusBadge = activeCard.querySelector(".status-badge");
            const newStatus = document.getElementById("editMovieStatus").value;

            // Remove old status classes and add new one
            statusBadge.classList.remove("now-showing", "coming-soon", "ended");
            statusBadge.classList.add(newStatus);

            // Update display text
            const statusMap = {
                "now-showing": "Now Showing",
                "coming-soon": "Coming Soon",
                "ended": "Ended"
            };
            statusBadge.textContent = statusMap[newStatus] || "Now Showing";

            bootstrap.Modal.getInstance(document.getElementById("editMovieModal")).hide();

            // Clear pending poster state now that it's been committed
            pendingPosterDataUrl = null;
            if (editPosterInput) editPosterInput.value = "";

            // Refresh filters and genre metrics since genre/status may have changed
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

            // Open delete modal
            const deleteModal = new bootstrap.Modal(document.getElementById("deleteMovieModal"));
            deleteModal.show();
        }
    });

    // ==========================================
    // 4. CONFIRM DELETE ACTION
    // ==========================================
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener("click", () => {
            if (activeCard) {
                // Instantly remove from page layout
                activeCard.remove();

                // Trigger your search filter engine and genre metrics to update
                if (typeof window.filterMovies === "function") {
                    window.filterMovies();
                }
                if (typeof window.updateGenreDistribution === "function") {
                    window.updateGenreDistribution();
                }
            }
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById("deleteMovieModal")).hide();
        });
    }
});
// ==========================================
// 4. ADD NEW MOVIE DATA & FILE HANDLING
// ==========================================
const addMovieForm = document.getElementById("addMovieForm");

if (addMovieForm) {
    addMovieForm.addEventListener("submit", (e) => {
        e.preventDefault();

        // 1. Safe form value extraction (protects against missing elements)
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

        let posterSrc = "image.png"; // Fallback placeholder image
        let statusText = "Now Showing";
        if (statusValue === "coming-soon") statusText = "Coming Soon";
        if (statusValue === "ended") statusText = "Ended";

        // 2. Separate logic to construct the card safely
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

            // Detect target container dynamically
            const fallbackMessage = document.getElementById("no-movies-fallback");
            const operationsContainer = document.getElementById('page-operations') ||
                document.getElementById('page-movies') ||
                document.querySelector('.main-content');

            if (fallbackMessage) {
                fallbackMessage.parentNode.insertBefore(newMovieCard, fallbackMessage);
            } else if (operationsContainer) {
                operationsContainer.appendChild(newMovieCard);
            }

            // Clean up form values
            addMovieForm.reset();

            // Safe Modal Dismissal
            const addModalEl = document.getElementById("addMovieModal");
            if (addModalEl && typeof bootstrap !== "undefined") {
                const modalInstance = bootstrap.Modal.getInstance(addModalEl) || new bootstrap.Modal(addModalEl);
                modalInstance.hide();
            }

            // Safe function checks to prevent errors if these aren't defined in your script yet
            if (typeof window.filterMovies === "function") {
                window.filterMovies();
            }
            if (typeof window.updateGenreDistribution === "function") {
                window.updateGenreDistribution();
            }
        };

        // 3. Handle processing locally (FileReader) or fallback smoothly
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