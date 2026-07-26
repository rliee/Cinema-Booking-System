/* ==========================================================
   Toast Component
   ----------------------------------------------------------
   PURPOSE:
   Displays reusable Bootstrap toast notifications.

   RESPONSIBILITIES:
   - Success messages
   - Error messages
   - Warning messages
   - Information messages

   DOES NOT:
   - Fetch data
   - Communicate with PHP
========================================================== */

/* ----------------------------------------------------------
   Displays a Bootstrap Toast

   Parameters:
   - title
   - message
   - type
---------------------------------------------------------- */

function showToast({

    title = "Notification",

    message = "",

    type = "primary"

} = {}) {

    const toastContainer =
        document.getElementById("toastContainer");

    const toast = document.createElement("div");

    toast.className =
        `toast border-0 text-bg-${type}`;

    toast.role = "alert";

    toast.ariaLive = "assertive";

    toast.ariaAtomic = "true";

    toast.innerHTML = `

        <div class="toast-header">

            <strong class="me-auto">

                ${title}

            </strong>

            <small>

                Just now

            </small>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="toast">

            </button>

        </div>

        <div class="toast-body">

            ${message}

        </div>

    `;

    toastContainer.appendChild(toast);

    const bsToast =
        new bootstrap.Toast(toast, {

            delay: 3500

        });

    bsToast.show();

    toast.addEventListener("hidden.bs.toast", () => {

        toast.remove();

    });

}

/* ----------------------------------------------------------
   Success Toast
---------------------------------------------------------- */

function successToast(message) {

    showToast({

        title: "Success",

        message,

        type: "success"

    });

}

/* ----------------------------------------------------------
   Error Toast
---------------------------------------------------------- */

function errorToast(message) {

    showToast({

        title: "Error",

        message,

        type: "danger"

    });

}

/* ----------------------------------------------------------
   Warning Toast
---------------------------------------------------------- */

function warningToast(message) {

    showToast({

        title: "Warning",

        message,

        type: "warning"

    });

}

/* ----------------------------------------------------------
   Information Toast
---------------------------------------------------------- */

function infoToast(message) {

    showToast({

        title: "Information",

        message,

        type: "primary"

    });

}