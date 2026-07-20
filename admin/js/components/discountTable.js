function renderDiscounts(discounts) {

    const tableBody = document.getElementById("discountTableBody");

    if (!tableBody) return;

    if (!discounts.length) {

        tableBody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center">
                    No discounts available.
                </td>
            </tr>
        `;

        return;
    }

    tableBody.innerHTML = discounts.map(discount => `
        <tr>

            <td>${discount.discount_name}</td>

            <td>
                ${discount.discount_percentage}%
            </td>

            <td class="text-center">

                <button
                    class="btn btn-warning btn-sm edit-discount-btn"
                    data-discount-id="${discount.discount_id}">

                    <i class="fa-solid fa-pen"></i>

                </button>

                <button
                    class="btn btn-danger btn-sm delete-discount-btn"
                    data-discount-id="${discount.discount_id}">

                    <i class="fa-solid fa-trash"></i>

                </button>

            </td>

        </tr>
    `).join("");

}