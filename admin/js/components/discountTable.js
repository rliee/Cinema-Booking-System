function renderDiscounts(discounts) {

    const tableBody =
        document.getElementById(
            "discountTableBody"
        );


    if (!tableBody) return;



    if (!discounts.length) {

        tableBody.innerHTML = `

            <tr>

                <td colspan="3" class="text-center py-4">

                    <i class="fa-solid fa-tags table-empty-icon"></i>

                    <div class="mt-2">
                        No discounts available.
                    </div>

                </td>

            </tr>

        `;

        return;

    }



    tableBody.innerHTML =
        discounts.map(discount => `

            <tr>


                <td>


                    <div class="table-item">


                        <div class="discount-icon">

                            <i class="fa-solid fa-tags"></i>

                        </div>


                        <span>

                            ${discount.discount_name}

                        </span>


                    </div>


                </td>



                <td>

                    <span class="discount-badge">

                        ${discount.discount_percentage}%

                    </span>


                </td>



                <td class="text-center">


                    <button
                        class="btn btn-warning btn-sm edit-discount-btn"
                        data-discount-id="${discount.discount_id}"
                        title="Edit">


                        <i class="fa-solid fa-pen"></i>


                    </button>



                    <button
                        class="btn btn-danger btn-sm delete-discount-btn"
                        data-discount-id="${discount.discount_id}"
                        title="Delete">


                        <i class="fa-solid fa-trash"></i>


                    </button>


                </td>


            </tr>


        `).join("");



    document
        .querySelectorAll(".edit-discount-btn")
        .forEach(button => {

            button.addEventListener(
                "click",
                () => openEditDiscountModal(
                    Number(button.dataset.discountId)
                )
            );

        });



    document
        .querySelectorAll(".delete-discount-btn")
        .forEach(button => {

            button.addEventListener(
                "click",
                () => deleteDiscount(
                    Number(button.dataset.discountId)
                )
            );

        });


}