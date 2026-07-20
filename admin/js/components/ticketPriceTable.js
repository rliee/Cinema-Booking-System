function renderTicketPrices(ticketPrices) {

    const tableBody = document.getElementById("ticketPriceTableBody");

    if (!tableBody) return;

    if (!ticketPrices.length) {

        tableBody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center">
                    No ticket prices available.
                </td>
            </tr>
        `;

        return;
    }

    tableBody.innerHTML = ticketPrices.map(price => `
        <tr>

            <td>${price.title}</td>

            <td>
                ₱${Number(price.price).toFixed(2)}
            </td>

            <td class="text-center">

                <button
                    class="btn btn-warning btn-sm edit-price-btn"
                    data-price-id="${price.price_id}">

                    <i class="fa-solid fa-pen"></i>

                </button>

                <button
                    class="btn btn-danger btn-sm delete-price-btn"
                    data-price-id="${price.price_id}">

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

}