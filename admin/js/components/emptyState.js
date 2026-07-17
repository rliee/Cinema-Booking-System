/* ==========================================================
   Empty State Component
   ----------------------------------------------------------
   PURPOSE:
   Displays a reusable empty state whenever there is
   no data available.

   RESPONSIBILITIES:
   - Create an empty state
   - Render it inside any container

   DOES NOT:
   - Fetch data
   - Handle AJAX
   - Communicate with PHP
========================================================== */

/* ----------------------------------------------------------
   Creates an empty state component

   Parameters:
   - icon        : Font Awesome icon class
   - title       : Main title
   - message     : Description
   - buttonText  : Button label (optional)
   - buttonId    : Button ID (optional)

---------------------------------------------------------- */

function createEmptyState({
    icon = "fa-solid fa-folder-open",
    title = "No Data Found",
    message = "There is currently no available data.",
    buttonText = "",
    buttonId = ""
} = {}) {

    const container = document.createElement("div");

    container.className =
        "text-center py-5";

    let buttonHTML = "";

    if (buttonText !== "") {

        buttonHTML = `
            <button
                id="${buttonId}"
                class="btn btn-primary mt-3">

                <i class="fa-solid fa-plus me-2"></i>

                ${buttonText}

            </button>
        `;

    }

    container.innerHTML = `

        <div class="empty-state">

            <div class="mb-4">

                <i
                    class="${icon}"
                    style="font-size:72px;color:#adb5bd;">
                </i>

            </div>

            <h3 class="fw-bold">

                ${title}

            </h3>

            <p class="text-muted mb-4">

                ${message}

            </p>

            ${buttonHTML}

        </div>

    `;

    return container;

}

/* ----------------------------------------------------------
   Renders the empty state inside a container

   Parameters:
   - containerId
   - options
---------------------------------------------------------- */

function renderEmptyState(containerId, options = {}) {

    const container =
        document.getElementById(containerId);

    clearElement(container);

    container.appendChild(

        createEmptyState(options)

    );

}