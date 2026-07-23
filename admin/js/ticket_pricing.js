document.addEventListener("DOMContentLoaded", initializeTicketPricingPage);

async function initializeTicketPricingPage() {
  initializeTicketPriceForm();
  initializeDiscountForm();

  await Promise.all([loadTicketPricing(), loadMovieOptions()]);
}

async function loadTicketPricing() {
  const response = await request("../api/ticket-pricing/get.php");

  if (!response.success) {
    console.error(response.message);
    return;
  }

  const { ticketPrices, discounts } = response.data;

  renderTicketPrices(ticketPrices);

  renderDiscounts(discounts);

  renderStatistics(ticketPrices, discounts);
}

function renderStatistics(ticketPrices, discounts) {
  const totalMoviesPricing = ticketPrices.length;

  const totalDiscounts = discounts.length;

  const averagePrice = ticketPrices.length
    ? ticketPrices.reduce(
        (total, ticketPrice) => total + Number(ticketPrice.price),
        0,
      ) / ticketPrices.length
    : 0;

  const highestDiscount = discounts.length
    ? Math.max(
        ...discounts.map((discount) => Number(discount.discount_percentage)),
      )
    : 0;

  document.getElementById("totalMoviesPricing").textContent =
    totalMoviesPricing;

  document.getElementById("averageTicketPrice").textContent =
    `₱${averagePrice.toFixed(2)}`;

  document.getElementById("totalDiscounts").textContent = totalDiscounts;

  document.getElementById("highestDiscount").textContent =
    `${highestDiscount}%`;
}

async function loadMovieOptions() {
  const response = await request(
    "../api/ticket-pricing/options.php?type=movies",
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

  form.addEventListener("submit", submitTicketPriceForm);

  modal.addEventListener("hidden.bs.modal", resetTicketPriceForm);
}

function initializeDiscountForm() {
  const form = document.getElementById("discountForm");

  const modal = document.getElementById("discountModal");

  if (!form || !modal) {
    return;
  }

  form.addEventListener("submit", submitDiscountForm);

  modal.addEventListener("hidden.bs.modal", resetDiscountForm);
}

async function submitTicketPriceForm(event) {
  event.preventDefault();

  const form = event.target;

  const formData = new FormData(form);

  const priceId = formData.get("price_id");

  let movieId;

  // Editing existing price
  if (priceId) {
    movieId = document.getElementById("editMovieId").value;

    formData.set("movie_id", movieId);
  }
  // Adding new price
  else {
    movieId = document.getElementById("movieId").value;

    formData.set("movie_id", movieId);
  }

  const endpoint = formData.get("price_id")
    ? "../api/ticket-pricing/update.php"
    : "../api/ticket-pricing/insert.php";

  const response = await request(endpoint, {
    method: "POST",
    body: formData,
  });

  if (!response.success) {
    showMessageModal("Error", response.message);

    return;
  }

  bootstrap.Modal.getInstance(
    document.getElementById("ticketPriceModal"),
  ).hide();

  showMessageModal("Success", response.message);

  await Promise.all([loadTicketPricing(), loadMovieOptions()]);
}

async function deleteTicketPrice(priceId) {
  showConfirmationModal(
    "Delete Ticket Price",

    "Are you sure you want to delete this ticket price?",

    async () => {
      const formData = new FormData();

      formData.append("price_id", Number(priceId));

      const response = await request("../api/ticket-pricing/delete.php", {
        method: "POST",
        body: formData,
      });

      if (!response.success) {
        showMessageModal("Error", response.message);

        return;
      }

      showMessageModal("Success", response.message);

      await Promise.all([loadTicketPricing(), loadMovieOptions()]);
    },
  );
}

async function deleteDiscount(discountId) {
  showConfirmationModal(
    "Delete Discount",

    "Are you sure you want to delete this discount?",

    async () => {
      const formData = new FormData();

      formData.append("discount_id", discountId);

      const response = await request(
        "../api/ticket-pricing/delete_discount.php",
        {
          method: "POST",
          body: formData,
        },
      );

      if (!response.success) {
        showMessageModal("Error", response.message);

        return;
      }

      showMessageModal("Success", response.message);

      await loadTicketPricing();
    },
  );
}

async function openEditTicketPriceModal(priceId) {
  const response = await request(
    `../api/ticket-pricing/get.php?price_id=${priceId}`,
  );

  if (!response.success) {
    showMessageModal("Error", response.message);

    return;
  }

  const ticketPrice = response.data;
  console.log(ticketPrice);

  document.getElementById("priceId").value = ticketPrice.price_id;

  const movieSelect = document.getElementById("movieId");
  movieSelect.value = "";
  movieSelect.style.display = "none";
  movieSelect.disabled = true;

  document.getElementById("editMovieId").value = ticketPrice.movie_id;

  const movieName = document.getElementById("movieName");

  movieName.style.display = "flex";

  movieName.querySelector("span").textContent = ticketPrice.title;

  document.getElementById("ticketPrice").value = ticketPrice.price;

  document
    .getElementById("ticketPriceModalTitle")
    .querySelector("span").textContent = "Edit Ticket Price";

  document.getElementById("ticketPriceSubmitButton").textContent = "Update";

  const modal = new bootstrap.Modal(
    document.getElementById("ticketPriceModal"),
  );

  modal.show();
}

async function openEditDiscountModal(discountId) {
  const response = await request(
    `../api/ticket-pricing/get.php?discount_id=${discountId}`,
  );

  if (!response.success) {
    showMessageModal("Error", response.message);

    return;
  }

  const discount = response.data;

  document.getElementById("discountId").value = discount.discount_id;

  const discountName = document.getElementById("discountName");

  discountName.style.display = "none";
  discountName.disabled = true;

  const discountNameDisplay = document.getElementById("discountNameDisplay");

  discountNameDisplay.style.display = "flex";

  discountNameDisplay.querySelector("span").textContent =
    discount.discount_name;

  document.getElementById("discountPercentage").value =
    discount.discount_percentage;

  document
    .getElementById("discountModalTitle")
    .querySelector("span").textContent = "Edit Discount";

  document.getElementById("discountSubmitButton").textContent = "Update";

  const modal = new bootstrap.Modal(document.getElementById("discountModal"));

  modal.show();
}

function resetTicketPriceForm() {
  const form = document.getElementById("ticketPriceForm");

  form.reset();

  document.getElementById("priceId").value = "";
  document.getElementById("editMovieId").value = "";
  document.getElementById("movieId").value = "";

  const movieSelect = document.getElementById("movieId");

  movieSelect.style.display = "block";
  movieSelect.disabled = false;

  document.getElementById("movieName").style.display = "none";

  document.getElementById("movieName").querySelector("span").textContent = "";

  document
    .getElementById("ticketPriceModalTitle")
    .querySelector("span").textContent = "Add Ticket Price";

  document.getElementById("ticketPriceSubmitButton").textContent = "Save";
}

async function submitDiscountForm(event) {
  event.preventDefault();

  const form = event.target;

  const formData = new FormData(form);

  const discountName = formData.get("discount_name");

  const percentage = Number(formData.get("discount_percentage"));

  if (!formData.get("discount_id")) {
    if (!discountName || !discountName.trim()) {
      showMessageModal("Validation Error", "Please enter a discount name.");

      return;
    }
  }

  if (isNaN(percentage) || percentage <= 0 || percentage > 100) {
    showMessageModal(
      "Validation Error",
      "Discount percentage must be between 1 and 100.",
    );

    return;
  }

  if (formData.get("discount_id")) {
    formData.delete("discount_name");
  }

  const endpoint = formData.get("discount_id")
    ? "../api/ticket-pricing/update_discount.php"
    : "../api/ticket-pricing/insert_discount.php";

  const response = await request(endpoint, {
    method: "POST",
    body: formData,
  });

  if (!response.success) {
    showMessageModal("Error", response.message);

    return;
  }

  bootstrap.Modal.getInstance(document.getElementById("discountModal")).hide();

  showMessageModal("Success", response.message);

  await loadTicketPricing();
}

function resetDiscountForm() {
  const form = document.getElementById("discountForm");

  form.reset();

  document.getElementById("discountId").value = "";

  const discountName = document.getElementById("discountName");

  discountName.style.display = "block";
  discountName.disabled = false;

  const discountNameDisplay = document.getElementById("discountNameDisplay");

  discountNameDisplay.style.display = "none";
  discountNameDisplay.querySelector("span").textContent = "";

  document
    .getElementById("discountModalTitle")
    .querySelector("span").textContent = "Add Discount";

  document.getElementById("discountSubmitButton").textContent = "Save";
}
