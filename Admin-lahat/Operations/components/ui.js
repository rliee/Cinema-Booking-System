/* ==========================================================
   UI Helper Component
   ----------------------------------------------------------
   PURPOSE:
   Contains reusable helper functions shared by all UI
   components.

   RESPONSIBILITIES:
   - Create Bootstrap grid columns
   - Create Bootstrap cards
   - Future reusable UI helpers

   This file DOES NOT:
   - Fetch data
   - Communicate with PHP
   - Handle events
========================================================== */

/* ----------------------------------------------------------
   Creates a responsive Bootstrap column
---------------------------------------------------------- */
function createColumn(className = "col-12") {

    const column = document.createElement("div");

    column.className = className;

    return column;

}

/* ----------------------------------------------------------
   Creates a Bootstrap card
---------------------------------------------------------- */
function createCard(extraClasses = "") {

    const card = document.createElement("div");

    card.className =
        `card schedule-card shadow-sm border-0 h-100 ${extraClasses}`;

    return card;

}

/* ----------------------------------------------------------
   Clears an element safely
---------------------------------------------------------- */
function clearElement(element) {

    while (element.firstChild) {

        element.removeChild(element.firstChild);

    }

}

/* ----------------------------------------------------------
   Toggle visibility
---------------------------------------------------------- */
function showElement(element) {

    element.classList.remove("d-none");

}

function hideElement(element) {

    element.classList.add("d-none");

}