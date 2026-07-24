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
    <link rel="stylesheet" href="../css/ticket_pricing.css">

</head>

<body>
    <div class="container-fluid py-4 px-4">

        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title text-main">
                    Ticket Pricing
                </h1>
                <p class="page-description text-white">
                    Configure movie ticket prices and customer discounts.
                </p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="statistics-section">
            <div class="row g-4">

                <!-- Movies with Pricing -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card card-circle stat-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-title">
                                    Movies with Pricing
                                </div>

                                <div
                                    class="stat-value"
                                    id="totalMoviesPricing">
                                    0
                                </div>

                                <div class="stat-subtitle">
                                    Active ticket prices
                                </div>
                            </div>

                            <div class="stat-icon">
                                <i class="fa-solid fa-film"></i>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- Average Price -->
                <div class="col-12 col-md-6 col-xl-3">

                    <div class="card card-circle stat-card">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="stat-title">
                                    Average Price
                                </div>

                                <div
                                    class="stat-value"
                                    id="averageTicketPrice">

                                    ₱0

                                </div>

                                <div class="stat-subtitle">
                                    Current base ticket price
                                </div>

                            </div>

                            <div class="stat-icon">
                                <i class="fa-solid fa-peso-sign"></i>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- Discounts -->
                <div class="col-12 col-md-6 col-xl-3">

                    <div class="card card-circle stat-card">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="stat-title">
                                    Discount Categories
                                </div>

                                <div
                                    class="stat-value"
                                    id="totalDiscounts">

                                    0

                                </div>

                                <div class="stat-subtitle">
                                    Available discounts
                                </div>

                            </div>

                            <div class="stat-icon">
                                <i class="fa-solid fa-tags"></i>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- Highest Discount -->
                <div class="col-12 col-md-6 col-xl-3">

                    <div class="card card-circle stat-card">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="stat-title">
                                    Highest Discount
                                </div>

                                <div
                                    class="stat-value"
                                    id="highestDiscount">

                                    0%

                                </div>

                                <div class="stat-subtitle">
                                    Maximum customer discount
                                </div>

                            </div>

                            <div class="stat-icon">
                                <i class="fa-solid fa-percent"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="row g-4">
            <!-- Ticket Prices -->
            <div class="col-12 col-lg-6">
                <div class="pricing-table-card h-100">
                    <div class="pricing-table-header d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-0 text-main">
                                Ticket Prices
                            </h5>

                            <small class="text-white">
                                Manage movie ticket base prices
                            </small>
                        </div>

                        <button
                            class="btn btn-warning btn-sm"
                            onclick="resetTicketPriceForm()"
                            data-bs-toggle="modal"
                            data-bs-target="#ticketPriceModal">
                            <i class="fa-solid fa-plus"></i>
                            Add Price
                        </button>

                    </div>

                    <div class="pricing-table-body">

                        <div class="table-responsive">

                            <table class="pricing-table">

                                <thead>
                                    <tr>
                                        <th>Movie</th>
                                        <th>Ticket Price</th>
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

                <div class="pricing-table-card h-100">
                    <div class="pricing-table-header d-flex justify-content-between align-items-start">

                        <div>
                            <h5 class="text-main mb-0">
                                Discount Categories
                            </h5>

                            <small class="text-white">
                                Manage customer discount percentages
                            </small>
                        </div>

                        <button
                            class="btn btn-warning   btn-sm"
                            onclick="resetDiscountForm()"
                            data-bs-toggle="modal"
                            data-bs-target="#discountModal">

                            <i class="fa-solid fa-plus"></i>
                            Add Discount
                        </button>

                    </div>

                    <div class="pricing-table-body">

                        <div class="table-responsive">

                            <table class="pricing-table">

                                <thead>
                                    <tr>
                                        <th>Discount</th>
                                        <th>Discount Rate %</th>
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
                            <h5 class="modal-title d-flex align-items-center gap-2" id="ticketPriceModalTitle">
                                <i class="fa-solid fa-ticket"></i>
                                <span>
                                    Add Ticket Price
                                </span>
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
                            <input
                                type="hidden"
                                id="editMovieId"
                                name="edit_movie_id">

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

                                <div
                                    id="movieName"
                                    class="selected-display"
                                    style="display:none;">

                                    <i class="fa-solid fa-film"></i>
                                    <span></span>
                                </div>
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
                                        min="1"
                                        step="0.01"
                                        placeholder="Enter ticket price"
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

        <!-- Add / Edit Discount Modal -->
        <div
            class="modal fade"
            id="discountModal"
            tabindex="-1"
            aria-hidden="true">

            <div class="modal-dialog">

                <div class="modal-content">

                    <form id="discountForm">

                        <div class="modal-header">
                            <h5
                                class="modal-title d-flex align-items-center gap-2"
                                id="discountModalTitle">

                                <i class="fa-solid fa-tags"></i>

                                <span>
                                    Add Discount
                                </span>

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
                                id="discountId"
                                name="discount_id">

                            <!-- Discount Name -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Discount Name
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="discountName"
                                    name="discount_name"
                                    required>

                                <div
                                    id="discountNameDisplay"
                                    class="selected-display"
                                    style="display:none;">

                                    <i class="fa-solid fa-tags"></i>

                                    <span></span>

                                </div>
                            </div>

                            <!-- Discount Percentage -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Discount Percentage
                                </label>

                                <div class="input-group">

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="discountPercentage"
                                        name="discount_percentage"
                                        min="1"
                                        max="99"
                                        step="1"
                                        placeholder="Enter discount percentage"
                                        required>

                                    <span class="input-group-text">
                                        %
                                    </span>

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
                                id="discountSubmitButton">

                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- =======================================================
        CONFIRMATION MODAL
    ======================================================== -->

    <div
        class="modal fade"
        id="confirmationModal"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">
                    <h5
                        class="modal-title d-flex align-items-center gap-2"
                        id="confirmationModalTitle">

                        <i id="confirmationModalIcon" class="fa-solid fa-trash"></i>

                        <span>
                            Confirm Action
                        </span>

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <p
                        class="mb-0"
                        id="confirmationModalMessage">

                    </p>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="button"
                        class="btn btn-danger"
                        id="confirmationModalConfirmButton">

                        Delete

                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- =======================================================
        MESSAGE MODAL
    ======================================================== -->

    <div
        class="modal fade"
        id="messageModal"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">
                    <h5
                        class="modal-title d-flex align-items-center gap-2"
                        id="messageModalTitle">

                        <i class="fa-solid fa-circle-info"></i>

                        <span>
                            Message
                        </span>

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <p
                        class="mb-0"
                        id="messageModalBody">

                    </p>

                </div>

                <div class="modal-footer">

                    <button
                        class="btn btn-gold"
                        data-bs-dismiss="modal">

                        OK

                    </button>

                </div>

            </div>

        </div>

    </div>

    <script src="../../libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <script src="../js/request.js"></script>
    <script src="../js/components/modals.js"></script>
    <script src="../js/components/ticketPriceTable.js"></script>
    <script src="../js/components/discountTable.js"></script>
    <script src="../js/components/movieDropdown.js"></script>


    <script src="../js/ticket_pricing.js"></script>

</body>

</html>