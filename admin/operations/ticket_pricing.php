<?php
// admin/operations/ticket_pricing.php

require_once __DIR__ . "/../../includes/db.php";

$page_title = "Ticket Pricing";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $page_title ?></title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../../libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../libraries/fontawesome/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/show_schedule.css">

</head>

<body>


<div class="container-fluid p-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="fw-bold">
                Ticket Pricing
            </h1>

            <p class="text-muted">
                Configure movie ticket prices and customer discounts.
            </p>
        </div>


        <button class="btn btn-outline-warning">
            <i class="fa-solid fa-floppy-disk"></i>
            Save Changes
        </button>

    </div>



    <!-- Ticket Prices Section -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">
                Ticket Prices
            </h5>

            <small class="text-muted">
                Manage movie ticket base prices
            </small>
        </div>


        <div class="card-body">

            <button class="btn btn-primary mb-3">
                <i class="fa-solid fa-plus"></i>
                Add Price
            </button>


            <div class="table-responsive">

                <table class="table table-dark table-hover">

                    <thead>
                        <tr>
                            <th>Movie</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>


                    <tbody>

                        <tr>
                            <td colspan="3" class="text-center">
                                No ticket prices available
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>





    <!-- Discounts Section -->

    <div class="card">

        <div class="card-header">

            <h5 class="mb-0">
                Discount Categories
            </h5>

            <small class="text-muted">
                Manage customer discount percentages
            </small>

        </div>


        <div class="card-body">


            <button class="btn btn-primary mb-3">
                <i class="fa-solid fa-plus"></i>
                Add Discount
            </button>



            <div class="table-responsive">

                <table class="table table-dark table-hover">


                    <thead>

                        <tr>
                            <th>Discount Name</th>
                            <th>Percentage</th>
                            <th>Action</th>
                        </tr>

                    </thead>



                    <tbody>

                        <?php

                        $query = "
                        SELECT 
                            ticket_prices.price_id,
                            movies.title,
                            ticket_prices.price

                        FROM ticket_prices

                        INNER JOIN movies

                        ON ticket_prices.movie_id = movies.movie_id

                        ORDER BY movies.title ASC
                        ";


                        $result = mysqli_query($conn, $query);



                        if(mysqli_num_rows($result) > 0):

                        while($row = mysqli_fetch_assoc($result)):

                        ?>

                        <tr>

                        <td>
                        <?= $row['title']; ?>
                        </td>


                        <td>
                        ₱<?= number_format($row['price'],2); ?>
                        </td>


                        <td>

                        <button class="btn btn-warning btn-sm">
                        <i class="fa-solid fa-pen"></i>
                        Edit
                        </button>


                        <button class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-trash"></i>
                        Delete
                        </button>

                        </td>

                        </tr>


                        <?php

                        endwhile;

                        else:

                        ?>

                        <tr>

                        <td colspan="3" class="text-center">
                        No ticket prices available
                        </td>

                        </tr>


                        <?php endif; ?>


                    </tbody>


                </table>


            </div>


        </div>

    </div>



</div>

<script src="../../libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>