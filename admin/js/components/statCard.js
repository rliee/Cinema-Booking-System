/* ==========================================================
   Statistics Card Component
   ----------------------------------------------------------
   PURPOSE:
   Creates reusable dashboard statistic cards.

   RESPONSIBILITIES:
   - Create statistic cards
   - Update statistic values

   DOES NOT:
   - Fetch data
   - Communicate with PHP
========================================================== */

/* ----------------------------------------------------------
   Creates a statistics card
---------------------------------------------------------- */

function createStatCard({

    id,

    title,

    icon,

    value = "0",

    color = "primary"

}) {

    const column = createColumn(

        "col-12 col-sm-6 col-xl-3"

    );

    const card = document.createElement("div");

    card.className =
        "card border-0 shadow-sm h-100";

    card.innerHTML = `

        <div class="card-body">

            <div
                class="d-flex
                       justify-content-between
                       align-items-center">

                <div>

                    <small
                        class="text-muted">

                        ${title}

                    </small>

                    <h3
                        id="${id}"
                        class="fw-bold mt-2 mb-0">

                        ${value}

                    </h3>

                </div>

                <div
                    class="
                        rounded-circle
                        bg-${color}
                        text-white
                        d-flex
                        justify-content-center
                        align-items-center"

                    style="
                        width:55px;
                        height:55px;">

                    <i class="${icon}"></i>

                </div>

            </div>

        </div>

    `;

    column.appendChild(card);

    return column;

}

/* ----------------------------------------------------------
   Renders all statistic cards
---------------------------------------------------------- */

function renderStatistics() {

    const section =
        document.getElementById(
            "statisticsSection"
        );

    clearElement(section);

    section.appendChild(

        createStatCard({

            id: "totalShows",

            title: "Total Shows",

            icon: "fa-solid fa-film",

            color: "primary"

        })

    );

    section.appendChild(

        createStatCard({

            id: "ticketsSold",

            title: "Tickets Sold",

            icon: "fa-solid fa-ticket",

            color: "success"

        })

    );

    section.appendChild(

        createStatCard({

            id: "seatOccupancy",

            title: "Seat Occupancy",

            icon: "fa-solid fa-chair",

            value: "0%",

            color: "warning"

        })

    );

    section.appendChild(

        createStatCard({

            id: "todayRevenue",

            title: "Today's Revenue",

            icon: "fa-solid fa-peso-sign",

            value: "₱0",

            color: "danger"

        })

    );

}

/* ----------------------------------------------------------
   Updates statistics
---------------------------------------------------------- */

function updateStatistics({

    totalShows = 0,

    ticketsSold = 0,

    occupancy = 0,

    revenue = 0

}) {

    document.getElementById(
        "totalShows"
    ).textContent = totalShows;

    document.getElementById(
        "ticketsSold"
    ).textContent = ticketsSold;

    document.getElementById(
        "seatOccupancy"
    ).textContent = occupancy + "%";

    document.getElementById(
        "todayRevenue"
    ).textContent =

        "₱" +

        Number(revenue).toLocaleString();

}