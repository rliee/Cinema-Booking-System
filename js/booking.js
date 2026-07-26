let schedules = [];

let selectedDate = null;
let selectedHall = null;
let selectedSchedule = null;
let selectedSeats = [];

const ticketCounts = {
  regular: 0,
  student: 0,
  senior: 0,
  pwd: 0,
};

let ticketPrice = 0;
let discounts = {};

let seatAssignmentQueue = [];
// let discounts = {
//   student: 0,
//   senior: 0,
//   pwd: 0,
// // };

// const regularInput = document.getElementById("regular-count");
// const studentInput = document.getElementById("student-count");
// const seniorInput = document.getElementById("senior-count");
// const pwdInput = document.getElementById("pwd-count");

const discountsElements = document.querySelectorAll("#seat-discount");

function generateSeatAssignmentQueue() {
  seatAssignmentQueue = [];

  discountsElements.forEach((element) => {
    const quantity = parseInt(element.value) || 0;

    if (quantity <= 0) return;

    const discountId = element.dataset.id;
    const name = element.dataset.name;

    for (let i = 0; i < quantity; i++) {
      seatAssignmentQueue.push({
        type: name,

        discount_id: Number(discountId),
      });
    }
  });

  console.log("Seat Assignment Queue:", seatAssignmentQueue);
}

function getNextAvailableTicketType() {
  const assignedDiscounts = selectedSeats.map((seat) => seat.discount_id);

  for (const ticket of seatAssignmentQueue) {
    const alreadyUsed = assignedDiscounts.filter(
      (id) => id === ticket.discount_id,
    ).length;

    const allowed = seatAssignmentQueue.filter(
      (t) => t.discount_id === ticket.discount_id,
    ).length;

    if (alreadyUsed < allowed) {
      return ticket;
    }
  }

  return null;
}

document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);

  const movieId = params.get("movie_id");

  if (!movieId) {
    console.error("No movie ID provided.");
    return;
  }

  initializeTicketInputs();

  loadMovieDetails(movieId);
  loadSchedules(movieId);
  loadTicketPricing(movieId);
});

function showSection(id) {
  const section = document.getElementById(id);

  if (!section) return;

  section.classList.remove("d-none");
}

function hideSection(id) {
  const section = document.getElementById(id);

  if (!section) return;

  section.classList.add("d-none");
}

async function loadMovieDetails(movieId) {
  try {
    const response = await fetch(
      `api/movies/get_by_id.php?movie_id=${movieId}`,
    );

    const data = await response.json();

    if (!data.success) {
      console.error("Movie loading failed:", data.message);

      return;
    }

    renderMovieDetails(data.movie);
  } catch (error) {
    console.error("Error fetching movie:", error);
  }
}

async function loadTicketPricing(movieId) {
  try {
    const response = await fetch(
      `api/booking/get_ticket_pricing.php?movie_id=${movieId}`,
    );

    const data = await response.json();

    console.log("Ticket Pricing:", data);

    if (!data.success) {
      console.error(data.message);
      return;
    }

    ticketPrice = Number(data.price);

    discounts = data.discounts;

    // document.getElementById("regular-price").textContent =
    //   `₱${ticketPrice.toFixed(2)}`;

    // document.getElementById("student-discount").textContent =
    //   `-${discounts.student ?? 0}%`;

    // document.getElementById("senior-discount").textContent =
    //   `-${discounts.senior ?? 0}%`;

    // document.getElementById("pwd-discount").textContent =
    //   `-${discounts.pwd ?? 0}%`;

    console.log("Ticket Price:", ticketPrice);
    console.log("Discounts:", discounts);
  } catch (error) {
    console.error("Ticket pricing loading failed:", error);
  }
}

async function loadSchedules(movieId) {
  try {
    const response = await fetch(
      `api/booking/get_schedules.php?movie_id=${movieId}`,
    );

    const data = await response.json();

    console.log("Schedules:", data);

    if (!data.success) {
      console.error(data.message);
      return;
    }

    // Store the schedules globally
    schedules = data.data;

    // Render the available dates
    renderDates();
  } catch (error) {
    console.error("Schedule loading failed:", error);
  }
}

async function loadSeats(scheduleId) {
  try {
    const response = await fetch(
      `api/booking/get_seats.php?schedule_id=${scheduleId}`,
    );

    const data = await response.json();

    console.log("Seats:", data);

    if (!data.success) {
      console.error(data.message);
      return;
    }

    renderSeatLayout(data.data);
  } catch (error) {
    console.error("Seat loading failed:", error);
  }
}

function renderDates() {
  const dateList = document.getElementById("date-list");

  if (!dateList) return;

  dateList.innerHTML = "";

  // Get unique dates
  const uniqueDates = [
    ...new Set(schedules.map((schedule) => schedule.show_date)),
  ];

  uniqueDates.forEach((date) => {
    const button = document.createElement("button");

    button.className = "date-item";
    button.dataset.date = date;

    const formattedDate = new Date(`${date}T00:00:00`).toLocaleDateString(
      "en-US",
      {
        month: "short",
        day: "numeric",
        year: "numeric",
      },
    );

    button.textContent = formattedDate;

    button.addEventListener("click", () => {
      document.querySelectorAll(".date-item").forEach((item) => {
        item.classList.remove("active");
      });

      button.classList.add("active");

      selectedDate = date;
      selectedHall = null;
      selectedSchedule = null;

      // Hide everything after date selection
      hideSection("time-section");
      // hideSection("ticketSection");
      hideSection("seatSelectionSection");

      // Show hall selection
      showSection("hall-section");

      // Reset the time list
      const timeList = document.getElementById("time-list");

      if (timeList) {
        timeList.classList.add("disabled");
        timeList.innerHTML = `
          <div class="placeholder">
            Select a hall first
          </div>
        `;
      }

      renderHalls();
    });

    dateList.appendChild(button);
  });
}
function renderHalls() {
  const hallList = document.getElementById("hall-list");

  if (!hallList) return;

  hallList.classList.remove("disabled");
  hallList.innerHTML = "";

  const availableHalls = schedules.filter((schedule) => {
    return schedule.show_date === selectedDate;
  });

  const uniqueHalls = [
    ...new Map(
      availableHalls.map((schedule) => [schedule.hall_id, schedule]),
    ).values(),
  ];

  uniqueHalls.forEach((schedule) => {
    const button = document.createElement("button");

    button.className = "hall-item";

    button.dataset.hallId = schedule.hall_id;

    button.textContent = schedule.hall_name;

    button.addEventListener("click", () => {
      document.querySelectorAll(".hall-item").forEach((item) => {
        item.classList.remove("active");
      });

      button.classList.add("active");

      selectedHall = schedule.hall_id;

      console.log("Selected Hall:", selectedHall);

      console.log(
        "Schedules for this hall:",
        schedules.filter((s) => s.hall_id == selectedHall),
      );

      selectedSchedule = null;

      showSection("time-section");

      renderTimes();
    });

    hallList.appendChild(button);
  });
}

function renderTimes() {
  const timeList = document.getElementById("time-list");

  if (!timeList) return;

  timeList.classList.remove("disabled");
  timeList.innerHTML = "";

  const availableSchedules = schedules.filter((schedule) => {
    return (
      schedule.show_date === selectedDate && schedule.hall_id == selectedHall
    );
  });

  availableSchedules.forEach((schedule) => {
    const button = document.createElement("button");

    button.className = "time-item";
    // button.dataset.scheduleId = schedule.schedule_id;
    button.dataset.hallName = schedule.hall_name;

    const formattedTime = new Date(
      `1970-01-01T${schedule.start_time}`,
    ).toLocaleTimeString("en-US", {
      hour: "numeric",
      minute: "2-digit",
    });

    button.textContent = formattedTime;

    button.addEventListener("click", () => {
      document.querySelectorAll(".time-item").forEach((item) => {
        item.classList.remove("active");
      });

      button.classList.add("active");

      selectedSchedule = schedule;

      // Reset previously selected seats
      selectedSeats = [];

      hideSection("seatSelectionSection");

      showSection("ticketSection");

      console.log("Selected Schedule:", selectedSchedule);

      updateCheckoutSummary();
      updateCheckoutButton();

      showSection("ticketSection");
    });

    timeList.appendChild(button);
  });
}

function renderSeatLayout(seats) {
  const seatLayout = document.getElementById("seatLayout");

  seatLayout.innerHTML = "";

  if (!seats.length) {
    seatLayout.innerHTML = `
            <p class="text-center text-white">
                No seats available.
            </p>
        `;

    return;
  }

  let currentRow = "";

  seats.forEach((seat) => {
    if (seat.seat_row !== currentRow) {
      currentRow = seat.seat_row;

      const row = document.createElement("div");

      row.className = "seat-row";
      row.dataset.row = currentRow;

      row.innerHTML = `
                <span class="row-label">
                    ${currentRow}
                </span>
            `;

      seatLayout.appendChild(row);

      /* Horizontal aisle after Row F */
      if (currentRow === "G") {
        const aisle = document.createElement("div");

        aisle.className = "horizontal-aisle";

        seatLayout.appendChild(aisle);
      }
    }

    const currentRowElement = seatLayout.lastElementChild.classList.contains(
      "seat-row",
    )
      ? seatLayout.lastElementChild
      : seatLayout.querySelector(`.seat-row[data-row="${currentRow}"]`);

    const seatButton = document.createElement("button");

    seatButton.className = "seat available";
    seatButton.dataset.id = seat.seat_id;
    seatButton.dataset.number = seat.seat_number;
    seatButton.dataset.label = seat.seat_label;

    seatButton.textContent = seat.seat_number;

    seatButton.addEventListener("click", () => {
      if (
        seatButton.classList.contains("occupied") ||
        seatButton.classList.contains("unavailable")
      ) {
        return;
      }

      toggleSeatSelection(seatButton);
    });

    currentRowElement.appendChild(seatButton);

    /* Vertical aisle after Seat 6 */
    if (seat.seat_number == 6) {
      const aisle = document.createElement("div");

      aisle.className = "vertical-aisle";

      currentRowElement.appendChild(aisle);
    }
  });
}

function toggleSeatSelection(button) {
  const seatId = Number(button.dataset.id);
  const seatLabel = button.dataset.label;

  const existingIndex = selectedSeats.findIndex(
    (seat) => seat.seat_id === seatId,
  );
  // Deselect seat
  if (existingIndex !== -1) {
    selectedSeats.splice(existingIndex, 1);

    button.classList.remove("selected");
    button.classList.add("available");

    updateBookingSummary();

    return;
  }

  console.log("Current selected seats:", selectedSeats);
  console.log("Current selected count:", selectedSeats.length);
  console.log("Allowed tickets:", getTotalTickets());

  if (selectedSeats.length >= getTotalTickets()) {
    alert("You cannot select more seats than your ticket quantity.");
    return;
  }

  // Prevent selecting more seats than tickets
  if (selectedSeats.length >= getTotalTickets()) {
    alert("You cannot select more seats than your ticket quantity.");

    return;
  }

  // Select seat
  const assignedTicket = getNextAvailableTicketType();
  selectedSeats.push({
    seat_id: seatId,

    label: seatLabel,

    type: assignedTicket.type,

    discount_id: assignedTicket.discount_id,
  });
  console.log(selectedSeats);

  button.classList.remove("available");
  button.classList.add("selected");

  updateBookingSummary();
}

function toggleSeat(element, seat) {
  const index = selectedSeats.findIndex((s) => s.seat_id === seat.seat_id);

  if (index >= 0) {
    selectedSeats.splice(index, 1);

    element.classList.remove("selected");
  } else {
    selectedSeats.push(seat);

    element.classList.add("selected");
  }

  updateBookingSummary();
}

function updateBookingSummary() {
  document.getElementById("summarySeats").textContent = selectedSeats.length
    ? selectedSeats.map((s) => s.label).join(", ")
    : "None";

  document.getElementById("summarySeatCount").textContent =
    selectedSeats.length;

  updateTicketBreakdown();

  const totalElement = document.getElementById("summaryTotal");

  if (totalElement) {
    totalElement.textContent = `₱${calculateTotalPrice().toFixed(2)}`;
  }

  const checkoutTotal = document.getElementById("summary-price");

  if (checkoutTotal) {
    checkoutTotal.textContent = `₱${calculateTotalPrice().toFixed(2)}`;
  }

  document.getElementById("continueBookingBtn").disabled =
    selectedSeats.length !== getTotalTickets();
}

function updateCheckoutSummary() {
  if (!selectedSchedule) {
    return;
  }

  const movieElement = document.getElementById("summaryMovie");

  const dateElement = document.getElementById("summaryDate");

  const hallElement = document.getElementById("summaryHall");

  const timeElement = document.getElementById("summaryTime");

  if (movieElement) {
    movieElement.textContent = selectedSchedule.title;
  }

  if (dateElement) {
    dateElement.textContent = new Date(
      `${selectedSchedule.show_date}T00:00:00`,
    ).toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
      year: "numeric",
    });
  }

  if (hallElement) {
    hallElement.textContent = selectedSchedule.hall_name;
  }

  if (timeElement) {
    timeElement.textContent = new Date(
      `1970-01-01T${selectedSchedule.start_time}`,
    ).toLocaleTimeString("en-US", {
      hour: "numeric",
      minute: "2-digit",
    });
  }

  // bottom checkout bar

  const bottomDate = document.getElementById("summary-date");

  const bottomHall = document.getElementById("summary-hall");

  const bottomTime = document.getElementById("summary-time");

  if (bottomDate) bottomDate.textContent = dateElement.textContent;

  if (bottomHall) bottomHall.textContent = hallElement.textContent;

  if (bottomTime) bottomTime.textContent = timeElement.textContent;
}

function renderMovieDetails(movie) {
  // Poster

  const poster = document.getElementById("moviePoster");

  if (poster) {
    poster.src = movie.poster_url;

    poster.alt = movie.title;
  }

  // Hero title

  const title = document.getElementById("movieTitle");

  if (title) {
    title.textContent = movie.title;
  }

  // Booking heading

  const bookingTitle = document.getElementById("booking-movie-title");

  if (bookingTitle) {
    bookingTitle.textContent = `Book tickets for ${movie.title}`;
  }

  // Age Rating

  const age = document.getElementById("heroAgeRating");

  if (age) {
    age.textContent = movie.age_rating;
  }

  // Duration

  const duration = document.getElementById("heroDuration");

  if (duration) {
    duration.textContent = formatDuration(movie.duration);
  }

  // Genre

  const genre = document.getElementById("heroGenreText");

  if (genre) {
    genre.textContent = movie.genre_name;
  }

  // Synopsis

  const heroSynopsis = document.getElementById("heroSynopsis");

  if (heroSynopsis) {
    heroSynopsis.textContent = movie.synopsis;
  }

  const detailSynopsis = document.getElementById("detailSynopsis");

  if (detailSynopsis) {
    detailSynopsis.textContent = movie.synopsis;
  }

  // Summary movie name

  const summaryMovie = document.getElementById("summary-movie");

  if (summaryMovie) {
    summaryMovie.textContent = movie.title;
  }
}

function formatDuration(minutes) {
  const hours = Math.floor(minutes / 60);

  const mins = minutes % 60;

  return `${hours}h ${mins}m`;
}

function initializeTicketInputs() {
  discountsElements.forEach((discountElement) => {
    discountElement.addEventListener("input", updateTickets);
  });
}

function updateTickets() {
  // ticketCounts.regular = Number(regularInput.value);

  // ticketCounts.student = Number(studentInput.value);

  // ticketCounts.senior = Number(seniorInput.value);

  // ticketCounts.pwd = Number(pwdInput.value);

  const seatId = this.id;
  const value = this.value;

  // addtoSeats(ticketCounts);
  generateSeatAssignmentQueue();

  updateBookingSummary();

  updateCheckoutButton();
}
function getTotalTickets() {
  let total = 0;
  discountsElements.forEach((discountElement) => {
    // console.log(discountElement.value);
    total += parseInt(discountElement.value);
  });

  return total;
}

function calculateTotalPrice() {
  let total = 0;

  selectedSeats.forEach((seat) => {
    let price = ticketPrice;

    if (seat.discount_id) {
      const discount = discounts[seat.discount_id];

      if (discount) {
        price = ticketPrice - (ticketPrice * discount.percentage) / 100;
      }
    }

    total += price;
  });

  return total;
}

function updateTicketBreakdown() {
  const breakdown = document.getElementById("ticketBreakdown");

  if (!breakdown) return;

  breakdown.innerHTML = "";

  discountsElements.forEach((item) => {
    if (item.value > 0) {
      const dataSet = item.dataset;
      const discount = parseInt(dataSet.discount);
      let price = ticketPrice;

      console.log(price, discount);
      if (dataSet.name !== "Regular") {
        price = ticketPrice - (ticketPrice * (discount ?? 0)) / 100;
      }
      const subtotal = parseInt(item.value) * price;
      const name = dataSet.name;

      breakdown.innerHTML += `
        <div class="summary-line">

            <span>
                ${name} x ${item.value}
            </span>

            <span>
                ₱${subtotal.toFixed(2)}
            </span>

        </div>`;
    }
  });

  const ticketTypes = [
    {
      name: "Regular",
      key: "regular",
      price: ticketPrice,
    },
    {
      name: "Student",
      key: "student",
      price: ticketPrice - (ticketPrice * (discounts.student ?? 0)) / 100,
    },
    {
      name: "Senior Citizen",
      key: "senior",
      price: ticketPrice - (ticketPrice * (discounts.senior ?? 0)) / 100,
    },
    {
      name: "PWD",
      key: "pwd",
      price: ticketPrice - (ticketPrice * (discounts.pwd ?? 0)) / 100,
    },
  ];

  ticketTypes.forEach((ticket) => {
    const quantity = ticketCounts[ticket.key];

    // Only display tickets that were selected
    if (quantity > 0) {
      const subtotal = quantity * ticket.price;

      breakdown.innerHTML += `
        <div class="summary-line">

            <span>
                ${ticket.name} x ${quantity}
            </span>

            <span>
                ₱${subtotal.toFixed(2)}
            </span>

        </div>
      `;
    }
  });
}

function updateCheckoutButton() {
  const button = document.getElementById("checkout-btn");

  if (!button) return;

  const ready = selectedSchedule && getTotalTickets() > 0;

  button.disabled = !ready;
  selectedSeats = [];

  if (ready) {
    button.classList.remove("d-none");
  } else {
    button.classList.add("d-none");
  }
}

document.getElementById("checkout-btn")?.addEventListener("click", () => {
  if (!selectedSchedule) {
    return;
  }

  // hideSection("ticketSection");

  showSection("seatSelectionSection");

  loadSeats(selectedSchedule.schedule_id);
});

// document.getElementById("continueBookingBtn")?.addEventListener("click", () => {
//   if (selectedSeats.length !== getTotalTickets()) {
//     alert("Please select the correct number of seats.");
//     return;
//   }

//   console.log("Booking Data:");

//   console.log({
//     movie: selectedSchedule.title,
//     date: selectedSchedule.show_date,
//     hall: selectedSchedule.hall_name,
//     time: selectedScheidule.start_time,
//     seats: selectedSeats,
//     tickets: ticketCounts,
//     total: calculateTotalPrice(),
//   });
// });

document
  .getElementById("continueBookingBtn")
  ?.addEventListener("click", async () => {
    if (!selectedSchedule) {
      alert("Please select a schedule.");
      return;
    }

    if (selectedSeats.length !== getTotalTickets()) {
      alert("Please select seats first.");
      return;
    }

    const bookingData = {
      schedule_id: selectedSchedule.schedule_id,

      seat_ids: selectedSeats,

      tickets: ticketCounts,

      ticketPrice: ticketPrice,

      discounts: discounts,
    };

    try {
      const response = await fetch("api/booking/create.php", {
        method: "POST",

        headers: {
          "Content-Type": "application/json",
        },

        body: JSON.stringify(bookingData),
      });

      const result = await response.json();

      console.log(result);
      if (result.success) {
        window.location.href = `payment.php?id=${result.booking_reference}`;
      }
    } catch (error) {
      console.error("Saving booking failed:", error);
    }
  });
