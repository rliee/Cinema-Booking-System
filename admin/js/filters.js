document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector('.search-container input');
    const genreSelect = document.querySelector('.genre-container select');
    const statusSelect = document.querySelector('.status-container select');
    const fallbackMessage = document.getElementById('no-movies-fallback');

    function filterMovies() {
        const movieCards = document.querySelectorAll('.movie-card-horizontal');
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
    
    // Bind to window to share with movie lifecycle handlers
    window.filterMovies = filterMovies;
});