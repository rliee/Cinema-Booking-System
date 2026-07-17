<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Dashboard</title>

    <link rel="stylesheet" href="style.css">
    <link href="./css/dashboardv2.css" rel="stylesheet">

    <link rel="stylesheet" href="./css/header-footer.css">
    <script src="./js/header-footer.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="dashboard-shell">
        <div class="main">
            <div class="header">
                <h1 style="color:#ffd54a;">Dashboard</h1>
                <div class="profile">
                    Admin
                </div>
            </div>

            <div class="cards">
                <div class="card">
                    <i class="fas fa-coins icon"></i>
                    <h3>Today's Revenue</h3>
                    <h1 id="revenue">₱3,150</h1>
                </div>
                <div class="card">
                    <i class="fas fa-ticket-alt icon"></i>
                    <h3>Tickets Sold</h3>
                    <h1 id="tickets">11</h1>
                </div>

                <div class="card">
                    <i class="fas fa-film icon"></i>
                    <h3>Showing Movies</h3>
                    <h1 id="movies">5</h1>
                </div>

                <div class="card">
                    <i class="fas fa-video icon"></i>
                    <h3>Today's Screening</h3>
                    <h1 id="screenings">5</h1>
                </div>
            </div>

            <div class="bottom">

                <div class="transactions">
                    <h2>Recent Transactions</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Movie</th>
                                <th>Tickets</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="transactionTable">
                            <tr>
                                <td>John Doe</td>
                                <td>Avengers</td>
                                <td>3</td>
                                <td>₱750</td>
                            </tr>
                            <tr>
                                <td>Maria Cruz</td>
                                <td>Batman</td>
                                <td>2</td>
                                <td>₱500</td>
                            </tr>
                            <tr>
                                <td>James Lee</td>
                                <td>Deadpool</td>
                                <td>4</td>
                                <td>₱1,000</td>
                            </tr>
                            <tr>
                                <td>Claire Santos</td>
                                <td>Superman</td>
                                <td>2</td>
                                <td>₱900</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="schedule">
                    <h2>Today's Screening</h2>
                    <ul>
                        <li>🎬 Avengers - 10:00 AM</li>
                        <li>🎬 Batman - 1:00 PM</li>
                        <li>🎬 Inside Out 2 - 4:00 PM</li>
                        <li>🎬 Deadpool - 7:00 PM</li>
                        <li>🎬 Superman - 9:30 PM</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>

</html>