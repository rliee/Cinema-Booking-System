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
    schedules: [],
    editingScheduleId: null
};

/* PAGE INITIALIZATION */
document.addEventListener(
    "DOMContentLoaded",
    initializeSchedulingPage
);

/* INITIALIZE PAGE */

function initializeSchedulingPage() {
    /* render dashboard cards */
    renderStatistics();

    /* generate week selector */
    renderWeekSelector();

    state.selectedDate = getSelectedDate();
    loadSchedules();
}

/* INITIALIZE ADD SCHEDULE FORM: attaches the submit event to the Add Schedule form */
function initializeAddScheduleForm() {
    const form = document.getElementById(
        "addScheduleForm"
    );

    if (!form) {
        return;
    }

    form.addEventListener(
        "submit",
        submitSchedule
    );
}

/* SUBMIT SCHEDULE: sends the Add Schedule form to the server */
async function submitSchedule(event) {
    /* prevent page refresh */
    event.preventDefault();

    /* form reference */
    const form = event.target;

    /* convert form into FormData */
    const formData = new FormData(form);
    console.log(
        "Submitting schedule...",
        Object.fromEntries(formData)
    );
}


/* LOAD SCHEDULES */
async function loadSchedules() {
    const container = document.getElementById(
        "scheduleContainer"
        );

    /* show loading skeletons */
    renderSkeletonCards(
        container,
        6
    );

    /* request schedules */
    const response = await request(
        "../ajax/getSchedule.php?date=${state.selectedDate}"
    );

    /* request failed */
    if (!response.success) {
        renderEmptyState(
            container,
            {
                title: "Unable to load schedules",
                description: response.message
            }
        );
        return;
    }

    /* save schedules */
    state.schedules = response.data;

    /* render page */
    refreshScheduleView();
}

/* REFRESH PAGE: updates every UI component using the current state */
function refreshScheduleView() {
    const container = document.getElementById(
        "scheduleContainer"
        );

    /* empty state */
    if (state.schedules.length === 0) {
        renderEmptyState(container);
        updateStatistics({
            totalShows: 0,
            ticketsSold: 0,
            occupancy: 0,
            revenue: 0
        });
        return;
    }

    /* schedule cards */
    renderScheduleCards(
        container,
        state.schedules
    );

    /* dashboard */
    updateDashboard();
}

/* UPDATE DASHBOARD: calculates dashboard statistics
   from the currently loaded schedules */
function updateDashboard() {
    /* total scheduled shows */
    const totalShows = state.schedules.length;

    /* total tickets sold */
    const ticketsSold = 
        state.schedules.reduce(
            (total, schedule) => 
                total + Number(schedule.sold),
                0
        );

    /* total available seats */
    const totalSeats =
        state.schedules.reduce(
            (total, schedule) =>
                total + Number(schedule.total_seats),
                0
        );

    /* calculate occupancy percentage */
    const occupancy = totalSeats > 0
        ? Math.round(
            (ticketsSold / totalSeats) * 100
        )
        : 0;

    /* calculate today's revenue
        revenue = tickets sold × ticket price */
    const revenue = 
        state.schedules.reduce(
            (total, schedule) =>
                total + (Number(schedule.sold) * Number(schedule.ticket_price)),
                0

        );

    /* update dashboard cards */
    updateStatistics({
        totalShows,
        ticketsSold,
        occupancy,
        revenue
    });

}

/* WEEK SELECTOR CALLBACK: called by weekSelector.js whenever
   the administrator selects another day */
window.onScheduleDateChanged = function (date) {
    state.selectedDate = date;
    loadSchedules();
};