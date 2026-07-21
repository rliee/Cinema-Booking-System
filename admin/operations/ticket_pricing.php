<?php
// admin/operations/ticket_pricing.php

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

        <div class="row g-4">
            <!-- Ticket Prices -->
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-0">
                                Ticket Prices
                            </h5>

                            <small class="text-muted">
                                Manage movie ticket base prices
                            </small>
                        </div>

                        <button
                            class="btn btn-primary btn-sm"
                            onclick="resetTicketPriceForm()"
                            data-bs-toggle="modal"
                            data-bs-target="#ticketPriceModal">
                            <i class="fa-solid fa-plus"></i>
                            Add Price
                        </button>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-dark table-hover align-middle">

                                <thead>
                                    <tr>
                                        <th>Movie</th>
                                        <th>Price</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="ticketPriceTableBody">

                                    <tr>
                                        <td colspan="3" class="text-center">
                                            Loading ticket prices...
                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Discount Categories -->
            <div class="col-12 col-lg-6">

                <div class="card h-100">

                    <div class="card-header d-flex justify-content-between align-items-start">

                        <div>
                            <h5 class="mb-0">
                                Discount Categories
                            </h5>

                            <small class="text-muted">
                                Manage customer discount percentages
                            </small>
                        </div>

                        <button class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i>
                            Add Discount
                        </button>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-dark table-hover align-middle">

                                <thead>
                                    <tr>
                                        <th>Discount</th>
                                        <th>%</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="discountTableBody">

                                    <tr>
                                        <td colspan="3" class="text-center">
                                            Loading discounts...
                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Add / Edit Ticket Price Modal -->
        <div
            class="modal fade"
            id="ticketPriceModal"
            tabindex="-1"
            aria-hidden="true">

            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="ticketPriceForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ticketPriceModalTitle">
                                Add Ticket Price
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">
                            <input
                                type="hidden"
                                id="priceId"
                                name="price_id">

                            <!-- Movie -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Movie
                                </label>

                                <select
                                    class="form-select"
                                    id="movieId"
                                    name="movie_id"
                                    required>

                                    <option value="">
                                        Select a movie
                                    </option>
                                </select>
                                
                                <input
                                    type="text"
                                    class="form-control"
                                    id="movieName"
                                    readonly
                                    style="display:none;">
                            </div>

                            <!-- Price -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Ticket Price
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        ₱
                                    </span>

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="ticketPrice"
                                        name="price"
                                        min="0"
                                        step="0.01"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                id="ticketPriceSubmitButton">

                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <script src="../js/request.js"></script>
    <script src="../js/components/ticketPriceTable.js"></script>
    <script src="../js/components/discountTable.js"></script>
    <script src="../js/components/movieDropdown.js"></script>


    <script src="../js/ticket_pricing.js"></script>

</body>

</html>