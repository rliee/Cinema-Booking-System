// AJAX HELPER
// Handles all communication with the PHP backend.

/* ==========================================================
   SHOW SCHEDULING CONTROLLER

   PURPOSE
   Controls the Show Scheduling page.

   RESPONSIBILITIES
   - Initialize the page
   - Load schedules
   - Update dashboard statistics
   - Render schedule cards
   - Show loading state
   - Show empty state

   DOES NOT
   - Generate HTML directly
   - Communicate with the database directly

========================================================== */

/* PAGE STATE: stores values shared across the page */

const state = {
  selectedDate: null,
  currentWeek: null,
  schedules: [],
  editingScheduleId: null,
  search: "",
};

let latestRequest = 0;
let latestEditRequest = 0;
let emptyStateTimer = null;

/* PAGE INITIALIZATION */
document.addEventListener("DOMContentLoaded", initializeSchedulingPage);

/* INITIALIZE PAGE */

function initializeSchedulingPage() {
  /* render dashboard cards */
  renderStatistics();

  /* generate week selector */
  renderWeekSelector();

  /* initialize components */
  initializeEndTimeCalculator();
  initializeScheduleForm();
  setupSearch();

  state.selectedDate = null;

  /* load schedules */
  loadSchedules();
}

function initializeScheduleForm() {
  const form = document.getElementById("addScheduleForm");

  if (!form) {
    return;
  }
  form.addEventListener("submit", submitScheduleForm);
}

function debounce(fn, delay = 300) {
  let timer;

  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), delay);
  };
}

function setupSearch() {
  const searchInput = document.getElementById("scheduleSearch");

  if (!searchInput) {
    return;
  }

  const search = debounce(() => {
    state.search = searchInput.value.trim();

    loadSchedules();
  }, 300);

  searchInput.addEventListener("input", () => {
    renderSkeletonCards(document.getElementById("scheduleContainer"), 6);

    search();
  });
}

/* LOAD SCHEDULES */
async function loadSchedules() {
  const requestId = ++latestRequest;
  clearTimeout(emptyStateTimer);
  const container = document.getElementById("scheduleContainer");
  const titleElement = document.getElementById("scheduleText");
  const descriptionElement = document.getElementById("scheduleDescription");

  /* show loading skeletons */
  const skeletonTimer = setTimeout(() => {
    renderSkeletonCards(container, 6);
  }, 150);
  let url = "../api/schedules/get.php";
  const params = new URLSearchParams();

  // if (state.selectedDate) {
  //   params.append("show_date", state.selectedDate);
  // } else {
  //   params.append("show_week", getSelectedWeek());
  // }

  // if (state.search) {
  //   titleElement.textContent = "Search Results";
  //   descriptionElement.textContent = `Showing schedules matching "${state.search}".`;

  //   params.append("search", state.search);
  // } else if (state.selectedDate) {
  //   titleElement.textContent = "Today's Schedule";
  //   descriptionElement.textContent = "Movie schedules for the selected day.";
  // } else {
  //   titleElement.textContent = "Week's Schedule";
  //   descriptionElement.textContent = "Movie schedules for the week.";
  // }
  if (state.search) {
    params.append("search", state.search);

    if (state.selectedDate) {
      params.append("show_date", state.selectedDate);
    }
  } else {
    if (state.selectedDate) {
      params.append("show_date", state.selectedDate);
    } else {
      params.append("show_week", getSelectedWeek());
    }
  }

  url += `?${params.toString()}`;

  const response = await request(url);
  clearTimeout(skeletonTimer);

  if (requestId !== latestRequest) {
    return;
  }

  /* request failed */
  if (!response.success) {
    clearTimeout(emptyStateTimer);

    renderEmptyState(container, {
      title: "Unable to load schedules",
      message: response.message,
    });
    return;
  }

  /* save schedules */
  state.schedules = response.data;

  /* render page */
  refreshScheduleView();
}

function refreshScheduleView() {
  const container = document.getElementById("scheduleContainer");

  /* empty state */
  if (state.schedules.length === 0) {
    renderSkeletonCards(container, 6);
    clearTimeout(emptyStateTimer);

    emptyStateTimer = setTimeout(() => {
      renderEmptyState(
        container,
        state.search
          ? {
              title: "No schedules found",
              message: `No schedules match "${state.search}".`,
            }
          : undefined,
      );

      updateStatistics({
        totalShows: 0,
        ticketsSold: 0,
        occupancy: 0,
        revenue: 0,
      });
    }, 500);
    return;
  }

  clearTimeout(emptyStateTimer);

  /* schedule cards */
  renderScheduleCards(container, state.schedules);
  setupDeleteCards(container);

  /* dashboard */
  updateDashboard();
}

async function deleteScheduleItem(priceId) {
  showConfirmationModal(
    "Delete Schedule",

    "Are you sure you want to delete this schedule?",

    async () => {
      const formData = new FormData();

      formData.append("schedule_id", Number(priceId));

      const response = await request("../api/schedules/delete.php", {
        method: "POST",
        body: formData,
      });

      if (!response.success) {
        showMessageModal("Error", response.message);

        return;
      }

      showMessageModal("Success", response.message);

      await loadSchedules();
    },
  );
}

function setupDeleteCards(container) {
  container.querySelectorAll(".deleteBtn").forEach((button) => {
    button.addEventListener("click", () =>
      deleteScheduleItem(button.dataset.id),
    );
  });
}

/* UPDATE DASHBOARD: calculates dashboard statistics
   from the currently loaded schedules */
function updateDashboard() {
  /* total scheduled shows */
  const totalShows = state.schedules.length;

  /* total tickets sold */
  const ticketsSold = state.schedules.reduce(
    (total, schedule) => total + Number(schedule.sold),
    0,
  );

  /* total available seats */
  const totalSeats = state.schedules.reduce(
    (total, schedule) => total + Number(schedule.total_seats),
    0,
  );

  /* calculate occupancy percentage */
  const occupancy =
    totalSeats > 0 ? Math.round((ticketsSold / totalSeats) * 100) : 0;

  /* calculate today's revenue
        revenue = tickets sold × ticket price */
  const revenue = state.schedules.reduce(
    (total, schedule) =>
      total + Number(schedule.sold) * Number(schedule.ticket_price),
    0,
  );

  /* update dashboard cards */
  updateStatistics({
    totalShows,
    ticketsSold,
    occupancy,
    revenue,
  });
}

/* WEEK SELECTOR CALLBACK: called by weekSelector.js whenever
   the administrator selects another day */
window.onScheduleDateChanged = function (date) {
  if (state.selectedDate === date) {
    state.selectedDate = null;
  } else {
    state.selectedDate = date;
  }

  loadSchedules();
};

/* ==========================================================
   SUBMIT ADD SCHEDULE
========================================================== */

async function submitScheduleForm(event) {
  event.preventDefault();

  const form = event.target;

  const formData = new FormData(form);

  /* Determine whether we're adding or editing */
  const scheduleId = formData.get("schedule_id");

  const url = scheduleId
    ? "../api/schedules/update.php"
    : "../api/schedules/insert.php";

  const response = await request(url, {
    method: "POST",
    body: formData,
  });

  /* Validation failed */
  if (!response.success) {
    errorToast(response.message);
    return;
  }

  /* Success */
  successToast(
    scheduleId
      ? "Schedule updated successfully."
      : "Schedule created successfully.",
  );

  const savedDate = formData.get("show_date");

  /* Close modal */
  bootstrap.Modal.getInstance(document.getElementById("scheduleModal")).hide();

  /* Reset form */
  form.reset();
  document.getElementById("endTime").value = "";

  /* Return modal to Add mode */
  state.editingScheduleId = null;
  document.getElementById("scheduleId").value = "";
  document.getElementById("scheduleModalTitle").textContent = "Add Schedule";

  setSelectedDate(savedDate);
}

/* ==========================================================
   AUTOMATIC END TIME
========================================================== */

function initializeEndTimeCalculator() {
  const movie = document.getElementById("movie");

  const startTime = document.getElementById("startTime");

  const endTime = document.getElementById("endTime");

  if (!movie || !startTime || !endTime) {
    return;
  }

  function calculateEndTime() {
    if (!movie.value || !startTime.value) {
      endTime.value = "";
      return;
    }

    const duration = Number(
      movie.options[movie.selectedIndex].dataset.duration,
    );

    if (!duration) {
      endTime.value = "";
      return;
    }

    const [hour, minute] = startTime.value.split(":");

    const date = new Date();

    date.setHours(Number(hour));
    date.setMinutes(Number(minute));

    date.setMinutes(date.getMinutes() + duration);

    endTime.value = date.toTimeString().slice(0, 5);
  }

  movie.addEventListener("change", calculateEndTime);

  startTime.addEventListener("change", calculateEndTime);
}

/* ==========================================================
   RESET ADD SCHEDULE MODAL
========================================================== */
function resetScheduleModal() {
  /* Return to Add mode */
  state.editingScheduleId = null;

  /* Reset title */
  document.getElementById("scheduleModalTitle").textContent = "Add Schedule";

  /* Clear hidden schedule ID */
  document.getElementById("scheduleId").value = "";

  /* Reset form */
  const form = document.getElementById("addScheduleForm");

  form.reset();

  /* Clear calculated end time */
  document.getElementById("endTime").value = "";

  document.getElementById("movieSelectWrapper").classList.remove("d-none");
  document.getElementById("movieDisplayWrapper").classList.add("d-none");
  document.getElementById("movieDisplay").value = "";
}

document
  .getElementById("btnAddSchedule")
  .addEventListener("click", resetScheduleModal);

document
  .getElementById("scheduleModal")
  .addEventListener("hidden.bs.modal", resetScheduleModal);

/* ==========================================================
   EDIT SCHEDULE
========================================================== */

document.addEventListener("click", function (event) {
  const button = event.target.closest(".editBtn");

  if (!button) {
    return;
  }

  const scheduleId = button.dataset.id;

  openEditSchedule(scheduleId);
});

async function openEditSchedule(scheduleId) {
  const requestId = ++latestEditRequest;
  state.editingScheduleId = scheduleId;

  const response = await request(
    `../api/schedules/get.php?schedule_id=${scheduleId}`,
  );

  if (requestId !== latestEditRequest) {
    return;
  }

  if (!response.success) {
    errorToast(response.message);

    return;
  }

  const schedule = response.data;

  /* Switch Movie field to read-only */
  document.getElementById("movieSelectWrapper").classList.add("d-none");
  document.getElementById("movieDisplayWrapper").classList.remove("d-none");

  document.getElementById("movieDisplay").value = schedule.title;

  /* Change modal title */
  document.getElementById("scheduleModalTitle").textContent = "Edit Schedule";

  /* Store ID */
  document.getElementById("scheduleId").value = schedule.schedule_id;

  /* Populate fields */
  document.getElementById("movie").value = schedule.movie_id;
  document.getElementById("hall").value = schedule.hall_id;
  document.getElementById("showDate").value = schedule.show_date;
  document.getElementById("startTime").value = schedule.start_time;
  document.getElementById("endTime").value = schedule.end_time;

  /* Show modal */
  const modal = new bootstrap.Modal(document.getElementById("scheduleModal"));
  modal.show();
}
