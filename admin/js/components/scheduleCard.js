/* ==========================================================
   Schedule Card Component
   ----------------------------------------------------------
   PURPOSE:
   Creates a single schedule card.

   RESPONSIBILITIES:
   - Display movie information
   - Display occupancy
   - Display action buttons

   DOES NOT:
   - Fetch schedules
   - Delete schedules
   - Edit schedules
========================================================== */

function createScheduleCard(schedule) {

    const column = createColumn();

    const card = createCard();
    card.classList.add("schedule-card");

    let badgeClass = "bg-secondary";

    switch (schedule.status) {

        case "Coming Soon":
            badgeClass = "bg-primary";
            break;

        case "Now Showing":
            badgeClass = "bg-success";
            break;

        case "Completed":
            badgeClass = "bg-dark";
            break;

    }

    const poster = schedule.poster || "../assets/images/no-poster.png";
    const occupancy = Math.round(Number(schedule.percent));

    const startTime = new Date(
        `1970-01-01T${schedule.start_time}`).toLocaleTimeString(
            "en-US",
            {
                hour: "numeric",
                minute: "2-digit",
                hour12: true
            }
        );

    const endTime = new Date(
        `1970-01-01T${schedule.end_time}`).toLocaleTimeString(
            "en-US",
            {
                hour: "numeric",
                minute: "2-digit",
                hour12: true
            }
        );

    card.innerHTML = `

<div class="card-body">

    <div class="row g-4 align-items-center">

        <!-- Movie Poster -->

        <div class="col-lg-2 col-md-3 col-sm-4">

            <img
                src="${poster}"
                class="img-fluid rounded shadow-sm w-100"
                alt="${schedule.title}">

        </div>

        <!-- Movie Information -->

        <div class="col-lg-10 col-md-9 col-sm-8">

            <h4 class="movie-title">

                ${schedule.title}

            </h4>

            <div class="schedule-details mt-4">

    <div class="row g-3">

        <div class="col-xl-4 col-md-6">

            <i class="fa-solid fa-building me-2"></i>

            <strong>Hall</strong>

            <br>

            <span>${schedule.hall_name}</span>

        </div>

        <div class="col-xl-4 col-md-6">
            <i class="fa-regular fa-calendar me-2"></i>
            <strong>Date</strong>
            <br>
            <span>
                ${new Date(schedule.show_date).toLocaleDateString(
                    "en-US",
                    {
                        month: "short",
                        day: "numeric",
                        year: "numeric"
                    }
                )}
            </span>
        </div>

        <div class="col-xl-4 col-md-6">
            <i class="fa-regular fa-clock me-2"></i>
            <strong>Time</strong>
            <br>
            <span>${startTime} - ${endTime}</span>
        </div>

        <div class="col-xl-4 col-md-6">
            <i class="fa-solid fa-hourglass-half me-2"></i>
            <strong>Duration</strong>
            <br>
            <span>${schedule.duration} mins</span>
        </div>

        <div class="col-xl-4 col-md-6">

            <i class="fa-solid fa-chair me-2"></i>

            <strong>Seats</strong>

            <br>

            <span>

                ${schedule.sold}/${schedule.total_seats}

            </span>

        </div>

    </div>

</div>

            <!-- Progress -->

            <div class="mt-4">

                <div class="progress">

                    <div
                        class="progress-bar"
                        style="width:${occupancy}%">

                    </div>

                </div>

                <div
    class="d-flex
           justify-content-between
           mt-2">

    <small>

        Occupancy

    </small>

    <small>

        ${occupancy}%

    </small>

</div>

            </div>

            <!-- Footer -->

            <div class="schedule-footer">

                <span
                    class="badge schedule-status ${badgeClass}">

                    ${schedule.status}

                </span>

                <div class="schedule-actions">

                    <button
                        class = "btn btn-outline-gold editBtn"
                        data-id = "${schedule.schedule_id}"
                        aria-label = "Edit Schedule">

                        <i class = "fa-solid fa-pen-to-square"></i>

                    </button>

                    <button
                        class = "btn btn-outline-danger deleteBtn"
                        data-id = "${schedule.schedule_id}"
                        aria-label = "Delete Schedule">

                        <i class="fa-solid fa-trash"></i>

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>

`;

    column.appendChild(card);

    return column;

}

/* ----------------------------------------------------------
   Renders all schedule cards
---------------------------------------------------------- */

function renderScheduleCards(container, schedules) {

    /*
        Clear existing cards
    */

    clearElement(container);

    /*
        Render each schedule
    */

    schedules.forEach(schedule => {

        container.appendChild(

            createScheduleCard(schedule)

        );

    });

}