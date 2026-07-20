function populateMovieDropdown(movies) {

    const movieSelect = document.getElementById("movieId");

    if (!movieSelect) {
        return;
    }

    movieSelect.innerHTML = `
        <option value="">
            Select a movie
        </option>
    `;

    movies.forEach(movie => {

        movieSelect.insertAdjacentHTML(
            "beforeend",
            `
                <option value="${movie.movie_id}">
                    ${movie.title}
                </option>
            `
        );

    });

}