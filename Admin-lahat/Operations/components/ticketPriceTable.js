function renderTicketPrices(ticketPrices) {

    const tableBody =
        document.getElementById(
            "ticketPriceTableBody"
        );


    if (!tableBody) return;


    if (!ticketPrices.length) {

        tableBody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center py-4">

                    <i class="fa-solid fa-film table-empty-icon"></i>

                    <div class="mt-2">
                        No ticket prices available.
                    </div>

                </td>
            </tr>
        `;

        return;

    }


    tableBody.innerHTML =
        ticketPrices.map(price => `

            <tr>

                <td>

                    <div class="table-item">

                        <div class="movie-icon">

                            <i class="fa-solid fa-film"></i>

                        </div>

                        <span>
                            ${price.title}
                        </span>

                    </div>

                </td>


                <td>

                    <span class="price-badge">

                        ₱${Number(price.price).toFixed(2)}

                    </span>

                </td>


                <td class="text-center">


                    <button
                        class="btn btn-warning btn-sm edit-price-btn"
                        data-price-id="${price.price_id}"
                        title="Edit">

                        <i class="fa-solid fa-pen"></i>

                    </button>


                    <button
                        class="btn btn-danger btn-sm delete-price-btn"
                        data-price-id="${price.price_id}"
                        title="Delete">

                        <i class="fa-solid fa-trash"></i>

                    </button>


                </td>


            </tr>

        `).join("");



    document
        .querySelectorAll(".edit-price-btn")
        .forEach(button => {

            button.addEventListener(
                "click",
                () => openEditTicketPriceModal(
                    Number(button.dataset.priceId)
                )
            );

        });



    document
        .querySelectorAll(".delete-price-btn")
        .forEach(button => {

            button.addEventListener(
                "click",
                () => deleteTicketPrice(
                    button.dataset.priceId
                )
            );

        });


}