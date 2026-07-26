<?php

require_once __DIR__ . "/../../includes/db.php";


/*
|--------------------------------------------------------------------------
| MOVIE COUNTS
|--------------------------------------------------------------------------
*/


$movieCounts = [

    "now_showing" => 0,
    "coming_soon" => 0,
    "ended" => 0,
    "total" => 0

];


$countQuery = "

SELECT

status,
COUNT(*) AS total

FROM movies

GROUP BY status

";


$countResult = $conn->query($countQuery);



while ($row = $countResult->fetch_assoc()) {


    $status = strtolower(
        str_replace(
            " ",
            "_",
            $row["status"]
        )
    );


    if (isset($movieCounts[$status])) {

        $movieCounts[$status] =
            $row["total"];
    }
}



$totalMovieQuery = "

SELECT COUNT(*) AS total

FROM movies

";


$movieCounts["total"] =
    $conn->query($totalMovieQuery)
        ->fetch_assoc()["total"];





/*
|--------------------------------------------------------------------------
| GENRE DISTRIBUTION
|--------------------------------------------------------------------------
*/


$genreCounts = [

    "action" => 0,
    "sci_fi" => 0,
    "animation" => 0,
    "comedy" => 0,
    "horror" => 0,
    "drama" => 0

];



$genreQuery = "

SELECT

g.genre_name,

COUNT(m.movie_id) AS total


FROM genres g


LEFT JOIN movies m

ON g.genre_id = m.genre_id


GROUP BY

g.genre_name


";



$genreResult =
    $conn->query($genreQuery);



while ($row = $genreResult->fetch_assoc()) {


    $genre =
        strtolower(
            $row["genre_name"]
        );


    $genre =
        str_replace(
            "-",
            "_",
            $genre
        );


    if (isset($genreCounts[$genre])) {

        $genreCounts[$genre] =
            $row["total"];
    }
}




/*
|--------------------------------------------------------------------------
| MOVIE LIST
|--------------------------------------------------------------------------
*/


$sql = "

SELECT

m.movie_id,

m.title,

m.genre_id,

g.genre_name,

m.duration,

m.age_rating,

m.synopsis,

m.poster_url,

m.trailer_url,

m.status


FROM movies m


LEFT JOIN genres g

ON m.genre_id = g.genre_id


ORDER BY

m.movie_id DESC


";



$result =
    $conn->query($sql);



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/component.css">
    <link rel="stylesheet" href="../css/modals.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/dashboardv2.css">
    <link rel="stylesheet" href="../css/movie-management.css">
</head>

<body>
    <div class="modal fade" id="editMovieModal" tabindex="-1" aria-labelledby="editMovieModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMovieModalLabel">Edit Movie Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editMovieForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="editMovieId" name="id">

                        <div class="row g-3">
                            <div class="col-md-6 d-flex flex-column gap-3">
                                <div>
                                    <label for="editMovieTitle" class="form-label">Movie Title</label>
                                    <input type="text" class="form-control" id="editMovieTitle" name="title" required placeholder="Enter movie title">
                                </div>
                                <div>
                                    <label for="editMoviePoster" class="form-label">Poster Image</label>
                                    <input type="file" class="form-control" id="editMoviePoster" name="poster" accept="image/*">
                                    <img id="editMoviePosterPreview" src="" alt="Current poster preview" class="mt-2 rounded" style="max-height: 120px; display: none;">
                                </div>
                                <div>
                                    <label for="editMovieGenre" class="form-label">Genre</label>
                                    <select class="form-select" id="editMovieGenre" name="genre_id" required>
                                        <option value="">Select Genre</option>
                                        <option value="1">Action</option>
                                        <option value="2">Sci-Fi</option>
                                        <option value="3">Comedy</option>
                                        <option value="4">Drama</option>
                                        <option value="5">Horror</option>
                                        <option value="6">Animation</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex flex-column gap-3">
                                <div>
                                    <label for="editMovieStatus" class="form-label">Status</label>
                                    <select class="form-select" id="editMovieStatus" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="Now Showing">Now Showing</option>
                                        <option value="Coming Soon">Coming Soon</option>
                                        <option value="Ended">Ended</option>
                                    </select>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="editMovieDuration" class="form-label">Duration (mins)</label>
                                        <input type="number" class="form-control" id="editMovieDuration" name="duration" required placeholder="e.g., 120">
                                    </div>
                                    <div class="col-6">
                                        <label for="editMovieRating" class="form-label">Age Rating</label>
                                        <select class="form-select" id="editMovieRating" name="rating" required>
                                            <option value="" disabled selected>Select Rating</option>
                                            <option value="G">G</option>
                                            <option value="PG">PG</option>
                                            <option value="PG-13">PG-13</option>
                                            <option value="R">R</option>
                                            <option value="NC-17">NC-17</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label for="editMovieSynopsis" class="form-label">Synopsis</label>
                                    <textarea class="form-control" id="editMovieSynopsis" name="synopsis" rows="3" required placeholder="Write a short summary..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary text-white border-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-black font-weight-bold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteMovieModal" tabindex="-1" aria-labelledby="deleteMovieModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-danger">
                <div class="modal-header border-danger">
                    <h5 class="modal-title text-danger" id="deleteMovieModalLabel">Delete Movie Confirmation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteMovieTarget" class="text-warning"></strong>?</p>
                    <p class="small text-secondary mb-0">This action cannot be undone and will permanently remove this record.</p>
                </div>
                <div class="modal-footer border-danger">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger" style="font-weight: lighter;">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ADD MOVIE MODAL -->
    <div class="modal fade" id="addMovieModal" tabindex="-1" aria-labelledby="addMovieModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMovieModalLabel">Add New Movie</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="addMovieForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6 d-flex flex-column gap-3">
                                <div>
                                    <label for="addMovieTitle" class="form-label">Movie Title</label>
                                    <input type="text" class="form-control" id="addMovieTitle" name="title" required placeholder="Enter movie title">
                                </div>
                                <div>
                                    <label for="addMoviePoster" class="form-label">Poster Image</label>
                                    <input type="file" class="form-control" id="addMoviePoster" name="poster" accept="image/*">
                                </div>
                                <div>
                                    <label for="addMovieGenre" class="form-label">Genre</label>
                                    <select class="form-select" id="addMovieGenre" name="genre_id" required>
                                        <option value="">Select Genre</option>
                                        <option value="1">Action</option>
                                        <option value="2">Sci-Fi</option>
                                        <option value="3">Comedy</option>
                                        <option value="4">Drama</option>
                                        <option value="5">Horror</option>
                                        <option value="6">Animation</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex flex-column gap-3">
                                <div>
                                    <label for="addMovieStatus" class="form-label">Showing Status</label>
                                    <select class="form-select" id="addMovieStatus" name="status" required>
                                        <option value="" disabled selected>Select status</option>
                                        <option value="Now Showing">Now Showing</option>
                                        <option value="Coming Soon">Coming Soon</option>
                                        <option value="Ended">Ended</option>
                                    </select>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="addMovieDuration" class="form-label">Duration</label>
                                        <input type="text" class="form-control" id="addMovieDuration" name="duration" placeholder="e.g., 120" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="addMovieRating" class="form-label">Age Rating</label>
                                        <select class="form-select" id="addMovieRating" name="rating" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="G">G</option>
                                            <option value="PG">PG</option>
                                            <option value="PG-13">PG-13</option>
                                            <option value="R">R</option>
                                            <option value="NC-17">NC-17</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label for="addMovieTrailer" class="form-label">Trailer URL</label>
                                    <input type="url" class="form-control" id="addMovieTrailer" name="trailer_url" placeholder="https://www.youtube.com/watch?v=...">
                                </div>
                                <div>
                                    <label for="addMovieSynopsis" class="form-label">Synopsis</label>
                                    <textarea class="form-control" id="addMovieSynopsis" name="synopsis" rows="3" required placeholder="Write a short summary..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary text-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Add Movie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="page-operations" class="page-section">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3 mb-4">
            <div>
                <h2 class="text-main fw-bold mb-2" style="color: #ffc107;">
                    <i class="fa-solid fa-clapperboard page-header-icon"></i>
                    Movie Management
                </h2>
            </div>
            <button type="button" class="add-btn text-black" data-bs-toggle="modal" data-bs-target="#addMovieModal">
                + Add Movie
            </button>
        </div>

        <!-- Dynamic Movie Counters -->
        <div class="row g-3">
            <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                <div class="bg-cinema-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                    <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 30px; background-color: rgba(255, 193, 7, 0.15); border-radius: 8px; padding-left: 8px;">
                        <i class="fa-solid fa-film" style="font-size: 12px; "></i>
                    </div>
                    <div id="count-now-showing" class="fs-5 text-white"><?= $movieCounts['now_showing'] ?? 0 ?></div>
                    <div class="text-secondary small">Now Showing</div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                <div class="bg-cinema-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                    <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 30px; background-color: rgba(255, 193, 7, 0.15); border-radius: 8px; padding-left: 8px;">
                        <i class="fa-solid fa-calendar" style="font-size: 12px; "></i>
                    </div>
                    <div id="count-coming-soon" class="fs-5 fw-bold text-white"><?= $movieCounts['coming_soon'] ?? 0 ?></div>
                    <div class="text-secondary small">Coming Soon</div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                <div class="bg-cinema-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                    <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 31px; height: 30px; background-color: #ff676727; border-radius: 8px; padding-left: 7px;">
                        <i class="fa-solid fa-xmark" style="font-size: 12px; color: #df1414;"></i>
                    </div>
                    <div id="count-ended-runs" class="fs-5 text-white"><?= $movieCounts['ended'] ?? 0 ?></div>
                    <div class="text-secondary small">Ended Runs</div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                <div class="bg-cinema-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                    <div class="mb-1 d-flex align-items-center justify-content-center" style="width: 34px; height: 30px; background-color: #ff676727; border-radius: 8px; padding-left: 8px;">
                        <i class="fa-solid fa-video" style="font-size: 12px; color: #df1414;"></i>
                    </div>
                    <div id="count-total-movies" class="fs-5 text-white"><?= $movieCounts['total'] ?? 0 ?></div>
                    <div class="text-secondary small">Total Movies</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="controls-row">
            <div class="search-container">
                <i class="fa fa-search search-icon"></i>
                <input class="control-element" id="movieSearchInput" placeholder="Search movies or directors..." type="text" value="">
            </div>
            <div class="genre-container">
                <select class="control-element cursor-pointer" id="genreFilterSelect">
                    <option value="All">All Genres</option>
                    <option value="Action">Action</option>
                    <option value="Sci-Fi">Sci-Fi</option>
                    <option value="Animation">Animation</option>
                    <option value="Comedy">Comedy</option>
                    <option value="Horror">Horror</option>
                    <option value="Drama">Drama</option>
                </select>
            </div>
            <div class="status-container">
                <select class="control-element cursor-pointer" id="statusFilterSelect">
                    <option value="all">All Status</option>
                    <option value="now_showing">Now Showing</option>
                    <option value="coming_soon">Coming Soon</option>
                    <option value="ended">Ended</option>
                </select>
            </div>
        </div>

        <p id="no-movies-fallback" class="text-white mt-3" style="display: none;">
            No movies found matching your search.
        </p>

        <div class="dashboard-layout">
            <div class="movie-cards-container" id="movieCardsContainer">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($movie = $result->fetch_assoc()):
                        // Extract movie data
                        $movieId   = $movie['movie_id'] ?? $movie['id'];
                        $genreId   = $movie['genre_id'] ?? '';
                        $genreName = $movie['genre_name'] ?? 'Uncategorized';

                        // Check status dynamically across schema variations
                        $rawStatus = !empty($movie['status'])
                            ? $movie['status']
                            : (!empty($movie['movie_status']) ? $movie['movie_status'] : 'Now Showing');

                        // Format class and text label
                        $statusClean = trim($rawStatus);
                        $statusClass = strtolower(str_replace(' ', '-', $statusClean));
                        $statusLabel = ucwords(strtolower(str_replace('-', ' ', $statusClean)));

                        $rawPoster = trim($movie['poster_url'] ?? '');
                        $posterUrl = htmlspecialchars($rawPoster, ENT_QUOTES);
                        $hasPoster = $rawPoster !== '';
                    ?>
                        <div class="movie-card-horizontal mt-2"
                            data-id="<?php echo $movieId; ?>"
                            data-genre-id="<?php echo $genreId; ?>">
                            <!-- Movie Poster -->
                            <div class="poster-container">
                                <?php if ($hasPoster): ?>
                                    <img src="../<?php echo "../" . $posterUrl; ?>"
                                        alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                        class="movie-poster"
                                        onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="movie-poster-placeholder" style="display:none; align-items:center; justify-content:center; height:100%; background:#1a1a1a; color:#666;">
                                        <i class="fas fa-film"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="movie-poster-placeholder" style="display:flex; align-items:center; justify-content:center; height:100%; background:#1a1a1a; color:#666;">
                                        <i class="fas fa-film"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                            </div>

                            <!-- Movie Details -->
                            <div class="movie-details">
                                <div class="details-header">
                                    <h3 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                                    <div class="meta-tags">
                                        <span class="tag genre"><?php echo htmlspecialchars($genreName); ?></span>
                                        <span class="tag duration"><?php echo htmlspecialchars($movie['duration']); ?> mins</span>
                                        <span class="tag age-rating"><?php echo htmlspecialchars($movie['age_rating'] ?? 'PG-13'); ?></span>
                                    </div>
                                </div>

                                <p class="movie-synopsis">
                                    <?php echo htmlspecialchars($movie['synopsis'] ?? 'No synopsis available.'); ?>
                                </p>

                                <div class="card-actions">
                                    <a href="<?php echo htmlspecialchars($movie['trailer_url'] ?? '#'); ?>" target="_blank" class="trailer-link">
                                        <i class="fas fa-play"></i> Watch Trailer
                                    </a>
                                    <div class="button-group">
                                        <button type="button"
                                            class="edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editMovieModal"
                                            data-id="<?php echo $movieId; ?>"
                                            data-title="<?php echo htmlspecialchars($movie['title'], ENT_QUOTES); ?>"
                                            data-genre-id="<?php echo $genreId; ?>"
                                            data-status="<?php echo htmlspecialchars($statusLabel, ENT_QUOTES); ?>"
                                            data-duration="<?php echo $movie['duration']; ?>"
                                            data-rating="<?php echo htmlspecialchars($movie['age_rating'] ?? '', ENT_QUOTES); ?>"
                                            data-synopsis="<?php echo htmlspecialchars($movie['synopsis'] ?? '', ENT_QUOTES); ?>"
                                            data-poster="<?php echo $hasPoster ? '../' . $posterUrl : ''; ?>">
                                            Edit
                                        </button>
                                        <button type="button" class="delete-btn" data-id="<?php echo $movieId; ?>">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-white mt-3">No movies found in database.</p>
                <?php endif; ?>
            </div>

            <!-- Genre Distribution Aside Column -->
            <aside class="genre-distribution-aside">
                <div class="movie-number">
                    <h1 class="header">Genre Distribution</h1>
                    <div class="distribution-wrapper">
                        <div class="bar" data-genre="sci-fi">
                            <div class="mov-name">
                                <span class="genre">Sci-Fi</span>
                                <span class="count"><?= $genreCounts['sci_fi'] ?? 0 ?> Films</span>
                            </div>
                            <div class="progress-bar"><span class="number-bar"></span></div>
                        </div>
                        <div class="bar" data-genre="drama">
                            <div class="mov-name">
                                <span class="genre">Drama</span>
                                <span class="count"><?= $genreCounts['drama'] ?? 0 ?> Films</span>
                            </div>
                            <div class="progress-bar"><span class="number-bar"></span></div>
                        </div>
                        <div class="bar" data-genre="action">
                            <div class="mov-name">
                                <span class="genre">Action</span>
                                <span class="count"><?= $genreCounts['action'] ?? 0 ?> Films</span>
                            </div>
                            <div class="progress-bar"><span class="number-bar"></span></div>
                        </div>
                        <div class="bar" data-genre="animation">
                            <div class="mov-name">
                                <span class="genre">Animation</span>
                                <span class="count"><?= $genreCounts['animation'] ?? 0 ?> Films</span>
                            </div>
                            <div class="progress-bar"><span class="number-bar"></span></div>
                        </div>
                        <div class="bar" data-genre="comedy">
                            <div class="mov-name">
                                <span class="genre">Comedy</span>
                                <span class="count"><?= $genreCounts['comedy'] ?? 0 ?> Films</span>
                            </div>
                            <div class="progress-bar"><span class="number-bar"></span></div>
                        </div>
                        <div class="bar" data-genre="horror">
                            <div class="mov-name">
                                <span class="genre">Horror</span>
                                <span class="count"><?= $genreCounts['horror'] ?? 0 ?> Films</span>
                            </div>
                            <div class="progress-bar"><span class="number-bar"></span></div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
    <script src="../../libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.js"></script>
    <!-- <script src="../js/movie-manager.js"></script> -->
    <!-- <script src="../js/dashboard.js"></script> -->
    <script src="../js/filters.js"></script>
    <script src="../js/metrics.js"></script>
    <script src="../js/navigation.js"></script>
    <script src="../js/page-router.js"></script>
    <!-- <script src="../js/dashboard.js"></script> -->
    <script src="../js/movie-manager.js"></script>
    <script>
        // const addMovieForm = document.getElementById("addMovieForm");
        // addMovieForm.addEventListener("submit", async (e) => {
        //     e.preventDefault();

        //     const formData = new FormData()


        //     const genreSelect = document.getElementById("editMovieGenre");
        //     let selectedGenreId = "";
        //     let selectedGenreName = "";

        //     if (genreSelect && genreSelect.selectedIndex !== -1) {
        //         const selectedOption = genreSelect.options[genreSelect.selectedIndex];
        //         selectedGenreId = selectedOption.value;
        //         selectedGenreName = selectedOption.text.trim();

        //         formData.set("genre_id", selectedGenreId);
        //         formData.set("genre", selectedGenreId);
        //     }

        //     const updatedData = {
        //         title: formData.get("title") || document.getElementById("editMovieTitle")?.value,
        //         status: formData.get("status") || document.getElementById("editMovieStatus")?.value,
        //         duration: formData.get("duration") || document.getElementById("editMovieDuration")?.value,
        //         rating: formData.get("rating") || document.getElementById("editMovieRating")?.value,
        //         synopsis: formData.get("synopsis") || document.getElementById("editMovieSynopsis")?.value,
        //         genre_id: selectedGenreId,
        //         genre_name: selectedGenreName,
        //         poster_url: data.poster_url || pendingPosterDataUrl
        //     };

        //     const response = await fetch(
        //         "api/movies/create.php", {
        //             method: "POST",
        //             headers: {
        //                 "Content-Type": "application/json"
        //             },
        //             body: JSON.stringify(updatedData)
        //         }
        //     )

        //     if (!response.okay || response.json()["success"]) {

        //     }
        // })
    </script>
</body>

</html>