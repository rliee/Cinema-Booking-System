/* ==========================================================
   Skeleton Card Component
   ----------------------------------------------------------
   PURPOSE:
   Displays placeholder cards while data is loading.

   RESPONSIBILITIES:
   - Create loading cards
   - Render multiple loading cards

   DOES NOT:
   - Fetch data
   - Communicate with PHP
========================================================== */

/* ----------------------------------------------------------
   Creates one skeleton card
---------------------------------------------------------- */

function createSkeletonCard() {

    const column = createColumn();

    const card = createCard();

    card.innerHTML = `

<div class="card-body">

    <div class="row g-4 align-items-center">

        <!-- Poster -->

        <div class="col-lg-2 col-md-3 col-sm-4">

            <div class="placeholder-glow">

                <div
                    class="placeholder rounded w-100"
                    style="height:260px;">

                </div>

            </div>

        </div>

        <!-- Information -->

        <div class="col-lg-10 col-md-9 col-sm-8">

            <div class="placeholder-glow">

                <span
                    class="placeholder col-6 mb-4"
                    style="height:30px;">

                </span>

                <div class="row gy-3 mt-2">

                    <div class="col-lg-4">
                        <span class="placeholder col-9"></span>
                    </div>

                    <div class="col-lg-4">
                        <span class="placeholder col-10"></span>
                    </div>

                    <div class="col-lg-4">
                        <span class="placeholder col-8"></span>
                    </div>

                    <div class="col-lg-4">
                        <span class="placeholder col-9"></span>
                    </div>

                    <div class="col-lg-4">
                        <span class="placeholder col-7"></span>
                    </div>

                    <div class="col-lg-4">
                        <span class="placeholder col-10"></span>
                    </div>

                </div>

                <div class="mt-4">

                    <div
                        class="placeholder rounded w-100"
                        style="height:12px;">

                    </div>

                </div>

                <div
                    class="d-flex justify-content-between mt-4">

                    <span
                        class="placeholder rounded"
                        style="width:120px;height:32px;">

                    </span>

                    <div>

                        <span
                            class="placeholder rounded me-2"
                            style="width:40px;height:36px;display:inline-block;">

                        </span>

                        <span
                            class="placeholder rounded"
                            style="width:40px;height:36px;display:inline-block;">

                        </span>

                    </div>

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
   Renders multiple skeleton cards
---------------------------------------------------------- */

function renderSkeletonCards(count = 3) {

    const loadingState =
        document.getElementById("loadingState");

    clearElement(loadingState);

    const row = document.createElement("div");

    row.className = "row g-4";

    for (let i = 0; i < count; i++) {

        row.appendChild(createSkeletonCard());

    }

    loadingState.appendChild(row);

}