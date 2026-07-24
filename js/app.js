/**
 * ==========================================================
 * Customer App
 * ----------------------------------------------------------
 * PURPOSE:
 * Handles client-side interactions shared across pages.
 *
 * RESPONSIBILITIES:
 * - Booking button handling
 * - Session checking
 * - Customer type synchronization
 * ==========================================================
 */

document.addEventListener("DOMContentLoaded", () => {
  /*
    |--------------------------------------------------------------------------
    | Booking Buttons
    |--------------------------------------------------------------------------
    */

  document.body.addEventListener("click", async (event) => {
    const button = event.target.closest(
      ".btn-book-ticket, .btn-book-now, [data-movie]",
    );

    if (!button) {
      return;
    }

    const movie = button.dataset.movie || button.getAttribute("data-movie");

    if (!movie) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    try {
      const response = await fetch("auth/session.php");

      const session = await response.json();

      if (session.loggedIn) {
        window.location.href = "booking.php?movie=" + encodeURIComponent(movie);

        return;
      }

      /*
            |--------------------------------------------------------------------------
            | Not logged in
            |--------------------------------------------------------------------------
            */

      sessionStorage.setItem("pendingMovie", movie);

      const loginModalElement = document.getElementById("loginModal");

      if (loginModalElement) {
        bootstrap.Modal.getOrCreateInstance(loginModalElement).show();

        return;
      }

      alert("Please log in first.");
    } catch (error) {
      console.error(error);

      alert("Unable to verify your login session.");
    }
  });

  /*
    |--------------------------------------------------------------------------
    | Customer Type Synchronization
    |--------------------------------------------------------------------------
    */

  function initializeCustomerType(select) {
    const savedType = localStorage.getItem("customerType") || "Regular";

    select.value = savedType;

    if (select.dataset.initialized) {
      return;
    }

    select.addEventListener("change", () => {
      localStorage.setItem("customerType", select.value);

      window.dispatchEvent(
        new CustomEvent("customerTypeChanged", {
          detail: select.value,
        }),
      );
    });

    select.dataset.initialized = "true";
  }

  document
    .querySelectorAll("#customerTypeSelect")
    .forEach(initializeCustomerType);

  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (!node.querySelectorAll) {
          return;
        }

        node
          .querySelectorAll("#customerTypeSelect")
          .forEach(initializeCustomerType);
      });
    });
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
});
