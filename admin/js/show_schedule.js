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
    initializeEndTimeCalculator();

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
        `../ajax/get_schedules.php?show_date=${state.selectedDate}`
    );

    /* request failed */
    if (!response.success) {
        renderEmptyState(
            container,
            {
                title: "Unable to load schedules",
                message: response.message
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

/* ==========================================================
   ADD SCHEDULE FORM
========================================================== */

document.addEventListener(

    "DOMContentLoaded",

    function () {

        initializeEndTimeCalculator();

        const form =

            document.getElementById(

                "addScheduleForm"

            );

        if (!form) return;

        form.addEventListener(

            "submit",

            submitScheduleForm

        );

    }

);

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
        ? "../ajax/update_schedule.php"
        : "../ajax/insert_schedule.php";

    const response = await request(

        url,
        {
            method: "POST",
            body: formData
        }
    );

    /* Validation failed */
    if (!response.success) {
        errorToast(response.message);
        return;
    }

    /* Success */
    successToast(
        scheduleId
            ? "Schedule updated successfully."
            : "Schedule created successfully."
    );

    const savedDate = formData.get("show_date");

    /* Close modal */
    bootstrap.Modal
        .getInstance(document.getElementById("scheduleModal"))
        .hide();

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

    const movie =
        document.getElementById("movie");

    const startTime =
        document.getElementById("startTime");

    const endTime =
        document.getElementById("endTime");

    if (!movie || !startTime || !endTime) {
        return;
    }

    function calculateEndTime() {

        if (!movie.value || !startTime.value) {
            endTime.value = "";
            return;
        }

        const duration = Number(
            movie.options[
                movie.selectedIndex
            ].dataset.duration
        );

        if (!duration) {
            endTime.value = "";
            return;
        }

        const [hour, minute] =
            startTime.value.split(":");

        const date = new Date();

        date.setHours(Number(hour));
        date.setMinutes(Number(minute));

        date.setMinutes(
            date.getMinutes() + duration
        );

        endTime.value =
            date.toTimeString().slice(0, 5);

    }

    movie.addEventListener(
        "change",
        calculateEndTime
    );

    startTime.addEventListener(
        "change",
        calculateEndTime
    );

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
}

document.getElementById("btnAddSchedule").addEventListener(
    "click",
    resetScheduleModal
);

document.getElementById("scheduleModal").addEventListener(
    "hidden.bs.modal",
    resetScheduleModal
);


/* ==========================================================
   EDIT SCHEDULE
========================================================== */

document.addEventListener(
    "click",
    function(event) {

        const button = event.target.closest(".editBtn");

        if (!button) {
            return;
        }

        const scheduleId = button.dataset.id;

        openEditSchedule(scheduleId);

    }
);


async function openEditSchedule(scheduleId) {

    state.editingScheduleId = scheduleId;


    const response = await request(
        `../ajax/get_schedules.php?schedule_id=${scheduleId}`
    );


    if (!response.success) {

        errorToast(response.message);

        return;

    }


    const schedule = response.data;


    /*
        Change modal title
    */

    document.getElementById(
        "scheduleModalTitle"
    ).textContent = "Edit Schedule";


    /*
        Store ID
    */

    document.getElementById(
        "scheduleId"
    ).value = schedule.schedule_id;


    /*
        Populate fields
    */

    document.getElementById(
        "movie"
    ).value = schedule.movie_id;


    document.getElementById(
        "hall"
    ).value = schedule.hall_id;


    document.getElementById(
        "showDate"
    ).value = schedule.show_date;


    document.getElementById(
        "startTime"
    ).value = schedule.start_time;

    document.getElementById(
        "endTime"
    ).value = schedule.end_time;


    /*
        Show modal
    */

    const modal = new bootstrap.Modal(
        document.getElementById(
            "scheduleModal"
        )
    );

    modal.show();

}
