/* ==========================================================
   Week Selector Component
   ----------------------------------------------------------
   PURPOSE:
   Displays a week calendar that allows the administrator
   to select a specific day.

   RESPONSIBILITIES:
   - Generate the current week
   - Highlight the selected day
   - Notify the controller when the date changes

   DOES NOT:
   - Fetch schedules
   - Communicate with PHP
========================================================== */

/* ----------------------------------------------------------
   Stores the currently selected date.
---------------------------------------------------------- */

let selectedDate = new Date();

/* ----------------------------------------------------------
   Converts a Date object to YYYY-MM-DD.
---------------------------------------------------------- */

function formatDate(date) {

    const year = date.getFullYear();

    const month = String(
        date.getMonth() + 1
    ).padStart(2, "0");

    const day = String(
        date.getDate()
    ).padStart(2, "0");

    return `${year}-${month}-${day}`;

}

/* ----------------------------------------------------------
   Returns the currently selected date.
---------------------------------------------------------- */

function getSelectedDate() {

    return formatDate(selectedDate);

}

/* ----------------------------------------------------------
   Generates the week selector.
---------------------------------------------------------- */

function renderWeekSelector() {

    const container =
        document.getElementById("weekContainer");

    clearElement(container);

    const monday = new Date(selectedDate);

    const day =
        monday.getDay();

    const difference =
        day === 0 ? -6 : 1 - day;

    monday.setDate(
        monday.getDate() + difference
    );

    for (let i = 0; i < 7; i++) {

        const current = new Date(monday);

        current.setDate(
            monday.getDate() + i
        );

        const column =
            createColumn();

        column.className =
            "col";

        const active =
            formatDate(current) ===
            formatDate(selectedDate);

        column.innerHTML = `

<div
    class="day-card text-center p-3 rounded shadow-sm
    ${active ? "active-day" : ""}"
    data-date="${formatDate(current)}"
    style="cursor:pointer;">

    <small class="text-uppercase">

        ${current.toLocaleDateString(
            "en-US",
            {
                weekday: "short"
            }
        )}

    </small>

    <h4 class="mb-0 mt-2">

        ${current.getDate()}

    </h4>

</div>

`;

        container.appendChild(column);

    }

    bindWeekEvents();

}

/* ----------------------------------------------------------
   Previous Week
---------------------------------------------------------- */

function previousWeek() {

    selectedDate.setDate(

        selectedDate.getDate() - 7

    );

    renderWeekSelector();

    notifyDateChanged();

}

/* ----------------------------------------------------------
   Next Week
---------------------------------------------------------- */

function nextWeek() {

    selectedDate.setDate(

        selectedDate.getDate() + 7

    );

    renderWeekSelector();

    notifyDateChanged();

}

/* ----------------------------------------------------------
   Attaches click events.
---------------------------------------------------------- */

function bindWeekEvents() {

    document
        .querySelectorAll(".day-card")
        .forEach(card => {

            card.onclick = function () {

                selectedDate =
                    new Date(
                        this.dataset.date
                    );

                renderWeekSelector();

                notifyDateChanged();

            };

        });

}

/* ----------------------------------------------------------
   Calls the controller whenever
   the selected date changes.

   show_scheduling.js will define:

       window.onScheduleDateChanged()

---------------------------------------------------------- */

function notifyDateChanged() {

    if (

        typeof window.onScheduleDateChanged ===

        "function"

    ) {

        window.onScheduleDateChanged(

            getSelectedDate()

        );

    }

}

/* ----------------------------------------------------------
   Allows the controller to
   programmatically select a date.
---------------------------------------------------------- */

function setSelectedDate(dateString) {

    selectedDate = new Date(dateString);

    renderWeekSelector();

}