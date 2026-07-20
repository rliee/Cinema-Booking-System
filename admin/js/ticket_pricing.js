document.addEventListener("DOMContentLoaded", initializeTicketPricingPage);

async function initializeTicketPricingPage() {

    initializeTicketPriceForm();

    await Promise.all([
        loadTicketPricing(),
        loadMovieOptions()
    ]);

}

async function loadTicketPricing() {

    const response = await request("../api/ticket-pricing/get.php");

    if (!response.success) {
        console.error(response.message);
        return;
    }

    renderTicketPrices(response.data.ticketPrices);
    renderDiscounts(response.data.discounts);

}

async function loadMovieOptions() {

    const response = await request(
        "../api/ticket-pricing/options.php?type=movies"
    );

    if (!response.success) {
        console.error(response.message);
        return;
    }

    populateMovieDropdown(response.data);

}

function initializeTicketPriceForm() {

    const form = document.getElementById("ticketPriceForm");

    const modal = document.getElementById("ticketPriceModal");

    if (!form || !modal) {
        return;
    }

    form.addEventListener(
        "submit",
        submitTicketPriceForm
    );

    modal.addEventListener(
        "hidden.bs.modal",
        resetTicketPriceForm
    );

}

async function submitTicketPriceForm(event) {

    event.preventDefault();

    const form = event.target;

    const formData = new FormData(form);

    const endpoint = formData.get("price_id")
    ? "../api/ticket-pricing/update.php"
    : "../api/ticket-pricing/insert.php";

    const response = await request(
        endpoint,
        {
            method: "POST",
            body: formData
        }
    );

    if (!response.success) {
        alert(response.message);
        return;
    }

    alert(response.message);

    bootstrap.Modal
        .getInstance(document.getElementById("ticketPriceModal"))
        .hide();

    await Promise.all([
        loadTicketPricing(),
        loadMovieOptions()
    ]);

}

async function openEditTicketPriceModal(priceId) {

    console.log("openEditTicketPriceModal called");

    const response = await request(
        `../api/ticket-pricing/get.php?price_id=${priceId}`
    );

    if (!response.success) {
        alert(response.message);
        return;
    }

    const ticketPrice = response.data;

    document.getElementById("priceId").value =
        ticketPrice.price_id;

    const movieSelect = document.getElementById("movieId");

    console.log("Before disable:", movieSelect.disabled);

    movieSelect.style.display = "none";
    movieSelect.disabled = true;

    console.log("After disable:", movieSelect.disabled);

    const movieName = document.getElementById("movieName");

    movieName.style.display = "block";
    movieName.value = ticketPrice.title;

    document.getElementById("ticketPrice").value =
        ticketPrice.price;

    document.getElementById("ticketPriceModalTitle").textContent =
        "Edit Ticket Price";

    document.getElementById("ticketPriceSubmitButton").textContent =
        "Update";

    const modal = new bootstrap.Modal(
        document.getElementById("ticketPriceModal")
    );

    modal.show();

}

function resetTicketPriceForm() {

    const form = document.getElementById("ticketPriceForm");

    form.reset();

    document.getElementById("priceId").value = "";

    const movieSelect = document.getElementById("movieId");

    movieSelect.style.display = "block";
    movieSelect.disabled = false;

    document.getElementById("movieName").style.display = "none";

    document.getElementById("movieName").value = "";

    document.getElementById("ticketPriceModalTitle").textContent =
        "Add Ticket Price";

    document.getElementById("ticketPriceSubmitButton").textContent =
        "Save";

}



