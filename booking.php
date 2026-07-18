<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Luxe - Premium Experience</title>
    <link rel="stylesheet" href="css/booking.css">
    <link rel="stylesheet" href="libraries/fontawesome/css/all.min.css">
    <!-- <link rel="stylesheet" href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css"> -->
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <span class="logo-icon">🎬</span>
            <div>
                <h1>Cinema Royale</h1>
                <p class="subtitle">PREMIUM EXPERIENCE</p>
            </div>
        </div>
        <nav class="nav-links">
            <a href="index.php" class="active">Home</a>
            <a href="index.php#now-showing">Now Showing</a>
            <a href="index.php#promotions">Promotions</a>
            <a href="index.php#experience">About</a>
            <a href="index.php#contact">Contact</a>
        </nav>
    </header>
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-container">
            <img id="moviePoster" src="assets/images/poster/image1.jpg" alt="Movie Poster" class="movie-poster">
            <div class="movie-details">
                <span class="badge"><span class="dot"></span> NOW SHOWING</span>
                <h2 class="movie-title" id="movieTitle">New Avengers</h2>
                <div class="meta-tags">
                    <span><i class="fa-solid fa-star gold-text"></i> <span id="heroRating">9.2</span> <small>/10</small></span>
                    <span class="tag" id="heroAgeRating">PG-13</span>
                    <span><i class="fa-regular fa-clock"></i> <span id="heroDuration">2h 48m</span></span>
                    <span id="heroGenreText">Sci-Fi / Adventure</span>
                </div>
                <p class="hero-synopsis" id="heroSynopsis">
                    Paul Atreides leads the Fremen in a galaxy-wide holy war as he struggles with the terrible purpose he has foreseen. The fate of the entire universe hangs in the balance as ancient powers collide.
                </p>
            </div>
        </div>
    </section>
    <main class="content-container">
        <div class="left-column">
            <section class="details-section">
                <h3>Synopsis</h3>
                <p id="detailSynopsis">Paul Atreides leads the Fremen in a galaxy-wide holy war as he struggles with the terrible purpose he has foreseen. The fate of the entire universe hangs in the balance as ancient powers collide.</p>
            </section>
            <section class="details-section">
                <h3>Cast & Crew</h3>
                <div class="cast-grid" id="castGrid"></div>
                <div class="director-row">
                    <i class="fa-solid fa-clapperboard text-muted"></i> <span>Director: <strong id="directorName">Denis Villeneuve</strong></span>
                </div>
            </section>
            <section class="details-section">
                <h3>Official Trailer</h3>
                <video id="trailerLink" class="video-container" controls style="width: 100%;">
                    <source src="" type="video/mp4">
                </video>
            </section>
        </div>
        <aside class="right-column">
            <div class="facts-card">
                <h3>Quick Facts</h3>
                <div class="fact-item">
                    <i class="fa-regular fa-calendar-days icon-box"></i>
                    <div>
                        <p class="fact-label">Release Date</p>
                        <p class="fact-value" id="factReleaseDate">December 18, 2026</p>
                    </div>
                </div>
                <div class="fact-item">
                    <i class="fa-regular fa-clock icon-box"></i>
                    <div>
                        <p class="fact-label">Duration</p>
                        <p class="fact-value" id="factDuration">2h 48m</p>
                    </div>
                </div>
                <div class="fact-item">
                    <i class="fa-solid fa-shield-halved icon-box"></i>
                    <div>
                        <p class="fact-label">Rating</p>
                        <p class="fact-value" id="factRating">PG-13</p>
                    </div>
                </div>
                <div class="fact-item">
                    <i class="fa-solid fa-film icon-box"></i>
                    <div>
                        <p class="fact-label">Genre</p>
                        <p class="fact-value" id="factGenre">Sci-Fi / Adventure</p>
                    </div>
                </div>
                <div class="fact-item">
                    <i class="fa-solid fa-video icon-box"></i>
                    <div>
                        <p class="fact-label">Director</p>
                        <p class="fact-value" id="factDirector">Denis Villeneuve</p>
                    </div>
                </div>
                <div class="price-range-section">
                    <p class="fact-label">Ticket Price Range</p>
                    <div class="price-badges">
                        <div class="price-tier">
                            <span class="tier-name">Standard</span>
                            <option class="tier-cost">₱350</option>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </main>
    <section class="booking-flow content-container">
        <h2 class="booking-page-title" id="booking-movie-title">Book tickets for Dune: Part Three</h2>
        <h3 class="section-title">Select Showtime</h3>
        <p class="section-subtitle">Choose your preferred date, cinema hall, and screening time</p>
        <div class="booking-grid">
            <div class="booking-col">
                <h4 class="step-title"><span class="step-num">1</span> Select Date</h4>
                <div class="selectable-list" id="date-list"></div>
            </div>
            <div class="booking-col">
                <h4 class="step-title"><span class="step-num">2</span> Select Hall</h4>
                <div class="selectable-list disabled" id="hall-list">
                    <div class="hall-placeholder">Select a date first</div>
                    <div class="select-item item-detailed" data-value="Cinema Hall 1" data-price="350">
                        <div>
                            <div class="item-header-row">
                                <span class="hall-title">Cinema Hall 1</span>
                                <span class="mini-badge standard">STANDARD</span>
                            </div>
                        </div>
                    </div>
                    <div class="select-item item-detailed" data-value="Cinema Hall 2" data-price="350">
                        <div>
                            <div class="item-header-row">
                                <span class="hall-title">Cinema Hall 2</span>
                                <span class="mini-badge standard">STANDARD</span>
                            </div>
                        </div>
                    </div>
                    <div class="select-item item-detailed" data-value="Cinema Hall 3" data-price="350">
                        <div>
                            <div class="item-header-row">
                                <span class="hall-title">Cinema Hall 3</span>
                                <span class="mini-badge standard">STANDARD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="booking-col">
                <h4 class="step-title"><span class="step-num">3</span> Select Time</h4>
                <div class="selectable-list disabled" id="time-list">
                    <div class="time-placeholder">Select a hall first</div>
                    <div class="select-item item-horizontal" data-time="11:00 AM" data-price="350">
                        <div>
                            <span class="time-text">11:00 AM</span>
                        </div>
                        <span class="price-tag">₱350</span>
                    </div>
                    <div class="select-item item-horizontal" data-time="3:00 PM" data-price="350">
                        <div>
                            <span class="time-text">3:00 PM</span>
                        </div>
                        <span class="price-tag">₱350</span>
                    </div>
                    <div class="select-item item-horizontal" data-time="6:30 PM" data-price="350">
                        <div>
                            <span class="time-text">6:30 PM</span>
                        </div>
                        <span class="price-tag">₱350</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="booking-type-section">
            <h4 class="step-title"><span class="step-num">4</span> Select Type</h4>
            <p class="step-subtitle">Choose how many Regular, Senior Citizen and PWD tickets you want.</p>
            <div class="type-grid">
                <label class="type-card" data-type="Regular">
                    <div class="type-card-header">
                        <span>Regular</span>
                        <span class="badge type-badge price">₱350</span>
                    </div>
                    <p class="type-description">Fixed price ticket</p>
                    <input id="countRegular" class="ticket-count type-input" type="number" min="0" value="0">
                </label>
                <label class="type-card" data-type="Senior Citizen">
                    <div class="type-card-header">
                        <span>Senior Citizen</span>
                        <span class="badge type-badge discount">20% off</span>
                    </div>
                    <p class="type-description">Discounted senior price</p>
                    <input id="countSenior" class="ticket-count type-input" type="number" min="0" value="0">
                </label>
                <label class="type-card" data-type="PWD">
                    <div class="type-card-header">
                        <span>PWD</span>
                        <span class="badge type-badge discount">20% off</span>
                    </div>
                    <p class="type-description">Discounted PWD ticket</p>
                    <input id="countPWD" class="ticket-count type-input" type="number" min="0" value="0">
                </label>
            </div>
        </div>
        <div class="summary-checkout-bar">
            <div class="summary-details">
                <div class="summary-item">
                    <span class="summary-lbl">Movie</span>
                    <span class="summary-val" id="summary-movie">Dune: Part Three</span>
                </div>
                <div class="summary-item">
                    <span class="summary-lbl">Date</span>
                    <span class="summary-val" id="summary-date">Choose a date</span>
                </div>
                <div class="summary-item">
                    <span class="summary-lbl">Hall</span>
                    <span class="summary-val" id="summary-hall">Choose a hall after selecting a date</span>
                </div>
                <div class="summary-item summary-time-with-type">
                    <span class="summary-lbl">Time</span>
                    <div style="display:flex;align-items:center;gap:12px">
                        <span class="summary-val" id="summary-time">Choose a time after selecting a hall</span>
                    </div>
                </div>
                <div class="summary-item">
                    <span class="summary-lbl">Price</span>
                    <span class="summary-val gold-text" id="summary-price">₱0</span>
                </div>
                <div class="summary-item">
                    <span class="summary-lbl">Tickets (R / S / P)</span>
                    <span class="summary-val"><span id="counts-display">0 / 0 / 0</span></span>
                </div>
                <div class="summary-item">
                    <span class="summary-lbl">Total Tickets</span>
                    <span class="summary-val"><span id="total-tickets-display">0</span></span>
                </div>
            </div>
            <button class="book-ticket-btn" id="checkout-btn">
                <i class="fa-solid fa-ticket"></i> Select Seats
            </button>
        </div>
    </section>
    <footer id="contact">
        <div class="container">
            <div class="footer-section">
                <h5>🎬 Cinema Royale</h5>
                <p>Experience movies the way they were meant to be seen. Premium sound, stunning visuals, and unmatched
                    comfort — only at Cinema Royale.</p>
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
                    <input type="email" placeholder="Your email address">
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
                <p><i class="fa-solid fa-location-dot"></i>  Trece Martires City, Cavite 4109</p>
                <p> +63 (2) 8888-1234</p>
                <p> <a href="mailto:hello@cinemaroyale.com"
                        style="color:#ffc700;text-decoration:none;">hello@cinemaroyale.com</a></p>
            </div>
            <div class="footer-section">
                <h5>TEAM</h5>
                <p>Arliesienne Ansuas<br>Ron Andrei Castro<br>James Arnold Dutosme<br>Kier Bryant Levita<br>Kylle Jonathan Padua<br>Genesis Saliedo<br>Josiah Joshua Torrefiel</p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-watermark">CINEMA ROYALE</div>
            <p class="footer-copyright">© 2026 Cinema Royale. All rights reserved.</p>
        </div>
    </footer>
    <script src="libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const params = new URLSearchParams(window.location.search);
            const movieParam = params.get('movie') || localStorage.getItem('movie');
            const bookingTitle = document.getElementById('booking-movie-title');
            const heroMovieTitle = document.querySelector('.movie-title');
            const summaryMovie = document.getElementById('summary-movie');
            const summaryDate = document.getElementById('summary-date');
            const summaryHall = document.getElementById('summary-hall');
            const summaryTime = document.getElementById('summary-time');
            const summaryPrice = document.getElementById('summary-price');
            const checkoutBtn = document.getElementById('checkout-btn');
            const hallList = document.getElementById('hall-list');
            const timeList = document.getElementById('time-list');
            const moviePoster = document.getElementById('moviePoster');
            const movieTitleEl = document.getElementById('movieTitle');
            const heroRating = document.getElementById('heroRating');
            const heroAgeRating = document.getElementById('heroAgeRating');
            const heroDuration = document.getElementById('heroDuration');
            const heroGenreText = document.getElementById('heroGenreText');
            const heroSynopsis = document.getElementById('heroSynopsis');
            const detailSynopsis = document.getElementById('detailSynopsis');
            const castGrid = document.getElementById('castGrid');
            const directorNameEl = document.getElementById('directorName');
            const trailerLink = document.getElementById('trailerLink');
            const trailerText = document.getElementById('trailerText');
            const factReleaseDate = document.getElementById('factReleaseDate');
            const factDuration = document.getElementById('factDuration');
            const factRating = document.getElementById('factRating');
            const factGenre = document.getElementById('factGenre');
            const factDirector = document.getElementById('factDirector');
            const movieDetails = {
                'Avengers: Infinity War': {
                    poster: 'assets/images/poster/image1.jpg',
                    title: 'Avengers: Infinity War',
                    synopsis: 'Thanos launches his devastating crusade, forcing the Avengers and Guardians to unite across the galaxy. Heroes must risk everything to stop the Mad Titan from claiming all six Infinity Stones.',
                    rating: '9.0',
                    ageRating: 'PG-13',
                    duration: '2h 48m',
                    genre: 'Action / Superhero',
                    releaseDate: 'April 27, 2018',
                    director: 'Anthony Russo & Joe Russo',
                    cast: [{
                            name: 'Robert Downey Jr.',
                            image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150'
                        },
                        {
                            name: 'Chris Evans',
                            image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150'
                        },
                        {
                            name: 'Mark Ruffalo',
                            image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'
                        },
                        {
                            name: 'Chris Hemsworth',
                            image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1?w=150'
                        }
                    ],
                    trailerUrl: 'assets/trailer/avengers.mp4'
                },
                'The Home': {
                    poster: 'assets/images/poster/image2.jpg',
                    synopsis: 'A heartwarming family drama about rediscovering home, community, and the courage to rebuild after loss.',
                    rating: '8.1',
                    ageRating: 'PG',
                    duration: '1h 55m',
                    genre: 'Drama / Family',
                    releaseDate: 'September 3, 2026',
                    director: 'Patty Jenkins',
                    cast: [{
                            name: 'Brie Larson',
                            image: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150'
                        },
                        {
                            name: 'Mahershala Ali',
                            image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=150'
                        },
                        {
                            name: 'Octavia Spencer',
                            image: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150'
                        }
                    ],
                    trailerUrl: 'assets/trailer/thehome.mp4'
                },
                'Love You, Long Time': {
                    poster: 'assets/images/poster/image3.jpg',
                    synopsis: 'A romantic comedy about reconnecting with a lost love across city lights, unexpected detours, and second chances.',
                    rating: '7.8',
                    ageRating: 'PG-13',
                    duration: '2h 5m',
                    genre: 'Romance / Comedy',
                    releaseDate: 'June 18, 2026',
                    director: 'Greta Gerwig',
                    cast: [{
                            name: 'Emma Stone',
                            image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150'
                        },
                        {
                            name: 'Henry Golding',
                            image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150'
                        },
                        {
                            name: 'Awkwafina',
                            image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'
                        }
                    ],
                    trailerUrl: 'assets/trailer/loveyoulongtime.mp4'
                },
                'Sputnik': {
                    poster: 'assets/images/poster/image4.jpg',
                    synopsis: 'In this tense sci-fi thriller, a rescued cosmonaut returns with a dangerous and intelligent alien presence inside him.',
                    rating: '8.3',
                    ageRating: 'R',
                    duration: '2h 8m',
                    genre: 'Sci-Fi / Thriller',
                    releaseDate: 'April 22, 2026',
                    director: 'Aleksey Fedorchenko',
                    cast: [{
                            name: 'Oleg Menshikov',
                            image: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150'
                        },
                        {
                            name: 'Fyodor Bondarchuk',
                            image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150'
                        },
                        {
                            name: 'Yuliya Aug',
                            image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'
                        }
                    ],
                    trailerUrl: 'assets/trailer/sputnik.mp4'
                },
                'Jurassic World Rebirth': {
                    poster: 'assets/images/poster/image5.jpg',
                    synopsis: 'A new chapter in the Jurassic World saga brings ancient creatures back to life and forces humanity to rethink the cost of playing god.',
                    rating: '8.0',
                    ageRating: 'PG-13',
                    duration: '2h 20m',
                    genre: 'Adventure / Sci-Fi',
                    releaseDate: 'May 14, 2026',
                    director: 'Colin Trevorrow',
                    cast: [{
                            name: 'Chris Pratt',
                            image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150'
                        },
                        {
                            name: 'Bryce Dallas Howard',
                            image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150'
                        },
                        {
                            name: 'Jeff Goldblum',
                            image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'
                        }
                    ],
                    trailerUrl: 'assets/trailer/jurassic.mp4'
                },
                'The Sheep Detectives': {
                    poster: 'assets/images/poster/image6.jpg',
                    synopsis: 'A group of amateur investigators follows clues across a sleepy town to uncover a woolly mystery with heart and humor.',
                    rating: '7.5',
                    ageRating: 'PG',
                    duration: '1h 40m',
                    genre: 'Family / Comedy',
                    releaseDate: 'November 5, 2026',
                    director: 'Taika Waititi',
                    cast: [{
                            name: 'Chris Hemsworth',
                            image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150'
                        },
                        {
                            name: 'Tessa Thompson',
                            image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150'
                        },
                        {
                            name: 'Awkwafina',
                            image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'
                        }
                    ],
                    trailerUrl: 'assets/trailer/sheepdetectives.mp4'
                },
                'F1': {
                    poster: 'assets/images/poster/image7.jpg',
                    synopsis: 'A high-speed documentary-style drama that follows the greatest drivers as they race toward the championship in a season full of rivalries and heartbreak.',
                    rating: '8.4',
                    ageRating: 'PG-13',
                    duration: '2h 10m',
                    genre: 'Sports / Drama',
                    releaseDate: 'October 1, 2026',
                    director: 'Joseph Kosinski',
                    cast: [{
                            name: 'Brad Pitt',
                            image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150'
                        },
                        {
                            name: 'Damien Chazelle',
                            image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150'
                        }
                    ],
                    trailerUrl: 'assets/trailer/f1.mp4'
                },
                'The Fantastic Four: First Steps': {
                    poster: 'assets/images/poster/image8.jpg',
                    synopsis: 'The world’s most famous heroes discover their powers for the first time and face a mysterious threat that will define their future.',
                    rating: '8.2',
                    ageRating: 'PG-13',
                    duration: '2h 5m',
                    genre: 'Superhero / Adventure',
                    releaseDate: 'December 2, 2026',
                    director: 'Jon Watts',
                    cast: [{
                            name: 'Simu Liu',
                            image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150'
                        },
                        {
                            name: 'Zoe Saldana',
                            image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150'
                        },
                        {
                            name: 'John Krasinski',
                            image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'
                        }
                    ],
                    trailerUrl: 'assets/trailer/fantastic4.mp4'
                }
            };
            let selectedDate = '';
            let selectedHall = '';
            let selectedTime = '';
            let selectedPriceBase = 0;
            let selectedPrice = 0;
            const dateList = document.getElementById('date-list');
            function formatDateLabel(date) {
                return date.toLocaleDateString('en-US', {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric'
                });
            }
            function buildDateOptions() {
                const today = new Date();
                const options = [];
                for (let i = 0; i < 3; i++) {
                    const date = new Date(today);
                    date.setDate(date.getDate() + i);
                    const formatted = formatDateLabel(date);
                    const option = document.createElement('div');
                    option.className = 'select-item';
                    option.dataset.value = formatted;
                    option.innerHTML = `<span>${formatted}</span><div class="radio-indicator"></div>`;
                    dateList.appendChild(option);
                    options.push(option);
                }
                return options;
            }
            const dates = buildDateOptions();
            const halls = Array.from(document.querySelectorAll('#hall-list .select-item'));
            const times = Array.from(document.querySelectorAll('#time-list .select-item'));
            function setActive(items, clickedItem) {
                items.forEach(item => item.classList.remove('active'));
                clickedItem.classList.add('active');
            }
            const movie = movieParam || Object.keys(movieDetails)[0];
            function renderMovieDetails() {
                const details = movieDetails[movie] || movieDetails[Object.keys(movieDetails)[0]];
                const title = details.title || movie;
                moviePoster.src = details.poster;
                moviePoster.alt = title + ' Poster';
                movieTitleEl.textContent = title;
                bookingTitle.textContent = 'Book tickets for ' + title;
                heroRating.textContent = details.rating;
                heroAgeRating.textContent = details.ageRating;
                heroDuration.textContent = details.duration;
                heroGenreText.textContent = details.genre;
                heroSynopsis.textContent = details.synopsis;
                detailSynopsis.textContent = details.synopsis;
                directorNameEl.textContent = details.director;
                trailerLink.querySelector('source').src = details.trailerUrl;
                trailerLink.load();
                factReleaseDate.textContent = details.releaseDate;
                factDuration.textContent = details.duration;
                factRating.textContent = details.ageRating;
                factGenre.textContent = details.genre;
                factDirector.textContent = details.director;
                castGrid.innerHTML = details.cast.map(member => `
                    <div class="cast-card">
                        <img src="${member.image}" alt="${member.name}">
                        <p class="cast-name">${member.name}</p>
                    </div>
                `).join('');
            }
            function enableList(listElement, enabled) {
                if (enabled) {
                    listElement.classList.remove('disabled');
                    listElement.querySelectorAll('.select-item').forEach(item => item.classList.remove('disabled'));
                } else {
                    listElement.classList.add('disabled');
                    listElement.querySelectorAll('.select-item').forEach(item => item.classList.add('disabled'));
                }
            }
            const convenienceFeePerTicket = 25;
            function getTicketCounts() {
                const counts = JSON.parse(localStorage.getItem('ticketCounts') || '{}');
                return {
                    regular: parseInt(counts.regular) || 0,
                    senior: parseInt(counts.senior) || 0,
                    pwd: parseInt(counts.pwd) || 0
                };
            }
            function getTicketsTotal() {
                const c = getTicketCounts();
                return c.regular + c.senior + c.pwd;
            }
            function computeFinalPrice() {
                const base = parseFloat(selectedPriceBase) || 350;
                const counts = getTicketCounts();
                const totalTickets = getTicketsTotal();
                const subtotal = base * totalTickets;
                const discount = (counts.senior + counts.pwd) * base * 0.2;
                const convenienceTotal = convenienceFeePerTicket * totalTickets;
                return Math.round(subtotal - discount + convenienceTotal);
            }
            function updateSummary() {
                const counts = getTicketCounts();
                summaryMovie.textContent = movie;
                summaryDate.textContent = selectedDate || 'Choose a date';
                summaryHall.textContent = selectedHall || 'Choose a hall after selecting a date';
                summaryTime.textContent = selectedTime || 'Choose a time after selecting a hall';
                const totalTickets = getTicketsTotal() || 1;
                selectedPrice = computeFinalPrice();
                summaryPrice.textContent = selectedPrice ? '₱' + selectedPrice : '₱0';
                document.getElementById('counts-display').textContent = `${counts.regular} / ${counts.senior} / ${counts.pwd}`;
                const totalTicketsDisplay = document.getElementById('total-tickets-display');
                if (totalTicketsDisplay) totalTicketsDisplay.textContent = totalTickets;
                checkoutBtn.disabled = !(selectedDate && selectedHall && selectedTime && totalTickets > 0);
                checkoutBtn.classList.toggle('disabled', checkoutBtn.disabled);
                localStorage.setItem('tickets', String(totalTickets));
                localStorage.setItem('grandTotal', String(selectedPrice));
            }
            function saveBooking() {
                localStorage.setItem('movie', movie);
                localStorage.setItem('date', selectedDate);
                localStorage.setItem('cinema', selectedHall);
                localStorage.setItem('time', selectedTime);
                localStorage.setItem('grandTotal', selectedPrice);
                // persist ticketCounts and tickets total
                try {
                    const regularInput = document.getElementById('countRegular');
                    const seniorInput = document.getElementById('countSenior');
                    const pwdInput = document.getElementById('countPWD');
                    const counts = {
                        regular: parseInt(regularInput && regularInput.value) || 0,
                        senior: parseInt(seniorInput && seniorInput.value) || 0,
                        pwd: parseInt(pwdInput && pwdInput.value) || 0
                    };
                    localStorage.setItem('ticketCounts', JSON.stringify(counts));
                    const totalTickets = counts.regular + counts.senior + counts.pwd;
                    localStorage.setItem('tickets', String(totalTickets));
                } catch (e) {
                    localStorage.setItem('tickets', '0');
                }
                localStorage.setItem('selectedSeats', JSON.stringify([]));

                try {
                    if (moviePoster && moviePoster.src) {
                        localStorage.setItem('moviePoster', moviePoster.src);
                    }
                } catch (e) {

                }
            }
            dates.forEach(dateItem => {
                dateItem.addEventListener('click', () => {
                    selectedDate = dateItem.dataset.value;
                    setActive(dates, dateItem);
                    selectedHall = '';
                    selectedTime = '';
                    selectedPrice = 0;
                    halls.forEach(hall => hall.classList.remove('active'));
                    times.forEach(time => time.classList.remove('active'));
                    enableList(hallList, true);
                    enableList(timeList, false);
                    updateSummary();
                });
            });
            if (dates.length > 0) {
                dates[0].classList.add('active');
                selectedDate = dates[0].dataset.value;
                enableList(hallList, true);
                updateSummary();
            }
            halls.forEach(hallItem => {
                hallItem.addEventListener('click', () => {
                    if (!selectedDate) return;
                    selectedHall = hallItem.dataset.value;
                    selectedPriceBase = parseInt(hallItem.dataset.price) || 350;
                    selectedTime = '';
                    setActive(halls, hallItem);
                    times.forEach(time => time.classList.remove('active'));
                    enableList(timeList, true);
                    updateSummary();
                });
            });
            times.forEach(timeItem => {
                timeItem.addEventListener('click', () => {
                    if (!selectedHall) return;
                    selectedTime = timeItem.dataset.time;
                    selectedPriceBase = parseInt(timeItem.dataset.price) || selectedPriceBase || 350;
                    setActive(times, timeItem);
                    updateSummary();
                });
            });
            const regularInput = document.getElementById('countRegular');
            const seniorInput = document.getElementById('countSenior');
            const pwdInput = document.getElementById('countPWD');

            function readCounts() {
                const counts = JSON.parse(localStorage.getItem('ticketCounts') || '{}');
                return {
                    regular: parseInt(counts.regular) || 0,
                    senior: parseInt(counts.senior) || 0,
                    pwd: parseInt(counts.pwd) || 0
                };
            }
            function saveCounts() {
                const counts = {
                    regular: parseInt(regularInput && regularInput.value) || 0,
                    senior: parseInt(seniorInput && seniorInput.value) || 0,
                    pwd: parseInt(pwdInput && pwdInput.value) || 0,
                };
                localStorage.setItem('ticketCounts', JSON.stringify(counts));
                updateSummary();
            }
            if (regularInput) regularInput.addEventListener('change', saveCounts);
            if (seniorInput) seniorInput.addEventListener('change', saveCounts);
            if (pwdInput) pwdInput.addEventListener('change', saveCounts);
            // initialize inputs from storage
            const initialCounts = readCounts();
            if (regularInput) regularInput.value = initialCounts.regular || 0;
            if (seniorInput) seniorInput.value = initialCounts.senior || 0;
            if (pwdInput) pwdInput.value = initialCounts.pwd || 0;

            checkoutBtn.addEventListener('click', () => {
                if (!selectedDate || !selectedHall || !selectedTime) {
                    alert('Please select a date, cinema hall, and time before booking.');
                    return;
                }
                updateSummary();
                saveBooking();
                window.location.href = 'seats.php';
            });
            renderMovieDetails();
            if (summaryMovie) {
                summaryMovie.textContent = movie;
            }
            if (movie) {
                document.title = movie + ' | Cinema Luxe Booking';
            }
            enableList(hallList, false);
            enableList(timeList, false);
            updateSummary();
        })();
    </script>
</body>
</html>