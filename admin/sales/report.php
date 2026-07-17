<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sales Report</title>

    <link href="../../libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/sale-report.css">



</head>

<body>

    <div class="container py-5">

        <h2 class="text-center mb-5">
            🎬 Cinema Sales Report
        </h2>



        <div class="row g-4 mb-5">

            <div class="col-md-3">
                <div class="card  summary-card">
                    <div class="card-body">
                        <p>Today's Sales</p>
                        <h3>₱15,250</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card  summary-card">
                    <div class="card-body">
                        <p>Weekly Sales</p>
                        <h3>₱98,750</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card  summary-card">
                    <div class="card-body">
                        <p>Monthly Sales</p>
                        <h3>₱450,300</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card  summary-card">
                    <div class="card-body">
                        <p>Tickets Sold</p>
                        <h3>3,250</h3>
                    </div>
                </div>
            </div>

        </div>



        <div class="card mb-4">

            <div class="card-header bg-primary text-white">
                Daily Sales
            </div>

            <div class="card-body">

                <table class="table table-bordered table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Tickets Sold</th>
                            <th>Total Sales</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>July 15, 2026</td>
                            <td>120</td>
                            <td>₱18,000</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


        <div class="card mb-4">

            <div class="card-header  text-white">
                Weekly Sales
            </div>

            <div class="card-body">

                <table class="table table-striped">

                    <thead class="table-dark">
                        <tr>
                            <th>Day</th>
                            <th>Tickets</th>
                            <th>Sales</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>Monday</td>
                            <td>90</td>
                            <td>₱13,500</td>
                        </tr>
                        <tr>
                            <td>Tuesday</td>
                            <td>85</td>
                            <td>₱12,750</td>
                        </tr>
                        <tr>
                            <td>Wednesday</td>
                            <td>100</td>
                            <td>₱15,000</td>
                        </tr>
                        <tr>
                            <td>Thursday</td>
                            <td>95</td>
                            <td>₱14,250</td>
                        </tr>
                        <tr>
                            <td>Friday</td>
                            <td>130</td>
                            <td>₱19,500</td>
                        </tr>
                        <tr>
                            <td>Saturday</td>
                            <td>180</td>
                            <td>₱27,000</td>
                        </tr>
                        <tr>
                            <td>Sunday</td>
                            <td>170</td>
                            <td>₱25,500</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>



        <div class="card mb-4">

            <div class="card-header bg-warning">
                Monthly Sales
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <thead class="table-dark">

                        <tr>

                            <th>Week</th>

                            <th>Tickets Sold</th>

                            <th>Total Sales</th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr>
                            <td>Week 1</td>
                            <td>650</td>
                            <td>₱97,500</td>
                        </tr>
                        <tr>
                            <td>Week 2</td>
                            <td>700</td>
                            <td>₱105,000</td>
                        </tr>
                        <tr>
                            <td>Week 3</td>
                            <td>720</td>
                            <td>₱108,000</td>
                        </tr>
                        <tr>
                            <td>Week 4</td>
                            <td>680</td>
                            <td>₱102,000</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>



        <div class="card mb-4">

            <div class="card-header bg-info text-white">
                Sales by Movie
            </div>

            <div class="card-body">

                <table class="table table-hover">

                    <thead class="table-dark">

                        <tr>

                            <th>Movie</th>

                            <th>Tickets Sold</th>

                            <th>Total Sales</th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr>
                            <td>Superman</td>
                            <td>950</td>
                            <td>₱142,500</td>
                        </tr>
                        <tr>
                            <td>Fantastic Four</td>
                            <td>700</td>
                            <td>₱105,000</td>
                        </tr>
                        <tr>
                            <td>Jurassic World</td>
                            <td>600</td>
                            <td>₱90,000</td>
                        </tr>
                        <tr>
                            <td>F1</td>
                            <td>500</td>
                            <td>₱75,000</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>



        <div class="card">

            <div class="card-header bg-dark text-white">
                Sales by Screening Schedule
            </div>

            <div class="card-body">

                <table class="table table-bordered table-striped">

                    <thead class="table-dark">

                        <tr>

                            <th>Movie</th>

                            <th>Date</th>

                            <th>Time</th>

                            <th>Tickets</th>

                            <th>Sales</th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr>
                            <td>Superman</td>
                            <td>July 15</td>
                            <td>10:00 AM</td>
                            <td>120</td>
                            <td>₱18,000</td>
                        </tr>

                        <tr>
                            <td>Superman</td>
                            <td>July 15</td>
                            <td>2:00 PM</td>
                            <td>150</td>
                            <td>₱22,500</td>
                        </tr>

                        <tr>
                            <td>Fantastic Four</td>
                            <td>July 15</td>
                            <td>6:00 PM</td>
                            <td>180</td>
                            <td>₱27,000</td>
                        </tr>

                        <tr>
                            <td>Jurassic World</td>
                            <td>July 15</td>
                            <td>8:00 PM</td>
                            <td>200</td>
                            <td>₱30,000</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>