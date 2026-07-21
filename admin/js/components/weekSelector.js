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

// let selectedDate = new Date();
// The week currently being displayed.
let currentWeek = new Date();

// The selected day in that week.
// null = weekly view.
let selectedDate = null;

/* ----------------------------------------------------------
   Converts a Date object to YYYY-MM-DD.
---------------------------------------------------------- */

function formatDate(date) {
  const year = date.getFullYear();

  const month = String(date.getMonth() + 1).padStart(2, "0");

  const day = String(date.getDate()).padStart(2, "0");

  return `${year}-${month}-${day}`;
}

/* ----------------------------------------------------------
   Returns the currently selected date.
---------------------------------------------------------- */
function getSelectedDate() {
  return selectedDate ? formatDate(selectedDate) : null;
}

/* ----------------------------------------------------------
   Generates the week selector.
---------------------------------------------------------- */

function renderWeekSelector() {
  const container = document.getElementById("weekContainer");

  clearElement(container);

  const monday = new Date(currentWeek);

  document.getElementById("weekTitle").textContent = monday.toLocaleDateString(
    "en-US",
    {
      month: "long",
      year: "numeric",
    },
  );

  const day = monday.getDay();

  const difference = day === 0 ? -6 : 1 - day;

  monday.setDate(monday.getDate() + difference);

  /* ----------------------------------------------------------
   Update Week Title
    ---------------------------------------------------------- */

  const sunday = new Date(monday);
  sunday.setDate(monday.getDate() + 6);

  const sameMonth = monday.getMonth() === sunday.getMonth();

  let title = "";
  if (sameMonth) {
    title = `${monday.toLocaleDateString("en-US", {
      month: "long",
    })} ${monday.getDate()} - ${sunday.getDate()}, ${sunday.getFullYear()}`;
  } else {
    title = `${monday.toLocaleDateString("en-US", {
      month: "short",
    })} ${monday.getDate()} - ${sunday.toLocaleDateString("en-US", {
      month: "short",
    })} ${sunday.getDate()}, ${sunday.getFullYear()}`;
  }

  document.getElementById("weekTitle").textContent = title;

  for (let i = 0; i < 7; i++) {
    const current = new Date(monday);
    current.setDate(monday.getDate() + i);

    const column = createColumn();
    column.className = "col";

    const active =
      selectedDate && formatDate(current) === formatDate(selectedDate);
    const today = formatDate(current) === formatDate(new Date());
    column.innerHTML = `

<div
    class="day-card
    ${active ? "active-day" : ""}
    ${today ? "today" : ""}"
    data-date="${formatDate(current)}"
    style="cursor:pointer;">

    <small class="text-uppercase">

        ${current.toLocaleDateString("en-US", {
          weekday: "short",
        })}

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
  currentWeek.setDate(currentWeek.getDate() - 7);
  selectedDate = null;

  renderWeekSelector();

  notifyDateChanged();
}

/* ----------------------------------------------------------
   Next Week
---------------------------------------------------------- */

function nextWeek() {
  currentWeek.setDate(currentWeek.getDate() + 7);

  selectedDate = null;

  renderWeekSelector();

  notifyDateChanged();
}

/* ----------------------------------------------------------
   Attaches click events.
---------------------------------------------------------- */

function bindWeekEvents() {
  document.querySelectorAll(".day-card").forEach((card) => {
    card.onclick = function () {
      const clicked = new Date(this.dataset.date);

      if (selectedDate && formatDate(clicked) === formatDate(selectedDate)) {
        // Clicked the selected day -> deselect it.
        selectedDate = null;
      } else {
        // Select a new day.
        selectedDate = clicked;
      }

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
  if (typeof window.onScheduleDateChanged === "function") {
    window.onScheduleDateChanged(
      selectedDate ? formatDate(selectedDate) : null,
    );
  }
}

/* ----------------------------------------------------------
   Allows the controller to
   programmatically select a date.
---------------------------------------------------------- */

function setSelectedDate(dateString) {
  if (!dateString) {
    selectedDate = null;
  } else {
    selectedDate = new Date(dateString);
    currentWeek = new Date(dateString);
  }

  renderWeekSelector();
  notifyDateChanged();
}   
