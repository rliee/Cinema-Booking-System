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

    const poster =
        schedule.poster
            ? schedule.poster
            : "../assets/images/no-poster.png";

    const occupancy =
        Number(schedule.percent).toFixed(0);

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

            <h4 class="fw-bold">

                ${schedule.title}

            </h4>

            <div class="row gy-3 mt-2">

                <div class="col-lg-4">

                    <i class="fa-solid fa-building me-2"></i>

                    ${schedule.hall_name}

                </div>

                <div class="col-lg-4">

                    <i class="fa-regular fa-calendar me-2"></i>

                    ${schedule.show_date}

                </div>

                <div class="col-lg-4">

                    <i class="fa-regular fa-clock me-2"></i>

                    ${schedule.start_time}

                    -

                    ${schedule.end_time}

                </div>

                <div class="col-lg-4">

                    <i class="fa-solid fa-hourglass-half me-2"></i>

                    ${schedule.duration} mins

                </div>

                <div class="col-lg-4">

                    <i class="fa-solid fa-peso-sign me-2"></i>

                    ₱${schedule.ticket_price}

                </div>

                <div class="col-lg-4">

                    <i class="fa-solid fa-chair me-2"></i>

                    ${schedule.sold}/${schedule.total_seats} Seats

                </div>

            </div>

            <!-- Progress -->

            <div class="mt-4">

                <div class="progress">

                    <div
                        class="progress-bar bg-warning"
                        style="width:${occupancy}%">

                    </div>

                </div>

                <small class="text-muted">

                    ${occupancy}% Occupancy

                </small>

            </div>

            <!-- Footer -->

            <div
                class="d-flex justify-content-between align-items-center mt-4">

                <span class="badge ${badgeClass}">

                    ${schedule.status}

                </span>

                <div>

                    <button
                        class="btn btn-outline-primary btn-sm editBtn"
                        data-id="${schedule.schedule_id}">

                        <i class="fa-solid fa-pen-to-square"></i>

                    </button>

                    <button
                        class="btn btn-outline-danger btn-sm deleteBtn"
                        data-id="${schedule.schedule_id}">

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